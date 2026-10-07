<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class AiSqlService
{
    private const MAX_ROWS = 500;

    /**
     * Únicas tablas que la IA puede consultar.
     *
     * Nunca incluir aquí tablas sensibles como:
     * users, sessions, settings, password_reset_tokens o migrations.
     */
    private const ALLOWED_TABLES = [
        'v_ia_ventas',
        'v_ia_detalle_ventas',
        'v_ia_productos',
        'v_ia_inventario',
        'v_ia_gastos',
        'v_ia_cajas',
        'v_ia_reservas',
        'v_ia_delivery',
    ];

    /**
     * Limpia bloques Markdown que pudiera devolver la IA.
     */
    public function cleanSql(string $sql): string
    {
        $sql = trim($sql);

        $sql = preg_replace('/^```(?:sql)?\s*/i', '', $sql);
        $sql = preg_replace('/\s*```$/', '', $sql);

        return trim($sql);
    }

    /**
     * Valida que una consulta generada por IA sea exclusivamente de lectura.
     */
    public function validate(string $sql): bool
    {
        $sql = $this->cleanSql($sql);

        if ($sql === '') {
            throw new RuntimeException('La consulta SQL está vacía.');
        }

        // Solo permitimos consultas SELECT.
        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            throw new RuntimeException(
                'La IA intentó generar una operación SQL no permitida.'
            );
        }

        // No permitimos varias sentencias.
        $withoutFinalSemicolon = rtrim($sql, " \t\n\r\0\x0B;");

        if (str_contains($withoutFinalSemicolon, ';')) {
            throw new RuntimeException(
                'No se permiten múltiples sentencias SQL.'
            );
        }

        // Palabras que jamás deben ejecutarse desde el asistente.
        $forbidden = [
            'INSERT',
            'UPDATE',
            'DELETE',
            'DROP',
            'ALTER',
            'TRUNCATE',
            'CREATE',
            'REPLACE',
            'RENAME',
            'GRANT',
            'REVOKE',
            'CALL',
            'EXEC',
            'EXECUTE',
            'HANDLER',
            'LOAD',
            'LOCK',
            'UNLOCK',
            'SET',
            'INTO OUTFILE',
            'INTO DUMPFILE',
        ];

        $normalized = strtoupper(
            preg_replace('/\s+/', ' ', $sql)
        );

        /*
         * Restricciones adicionales para evitar consultas complejas
         * o accesos que no son necesarios para el asistente.
         */
        $dangerousPatterns = [
            '/\bUNION\b/i',
            '/\bINFORMATION_SCHEMA\b/i',
            '/\bPERFORMANCE_SCHEMA\b/i',
            '/\bLOAD_FILE\s*\(/i',
            '/\bSLEEP\s*\(/i',
            '/\bBENCHMARK\s*\(/i',
            '/\bGET_LOCK\s*\(/i',
            '/\bRELEASE_LOCK\s*\(/i',
            '/\bFOR\s+UPDATE\b/i',
            '/\bLOCK\s+IN\s+SHARE\s+MODE\b/i',
            '/:=/',
            '/@[a-zA-Z0-9_]+/',
            '/\(\s*SELECT\b/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $sql)) {
                throw new RuntimeException(
                    'La consulta contiene una construcción SQL no permitida.'
                );
            }
        }

        foreach ($forbidden as $keyword) {
            $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';

            if (preg_match($pattern, $normalized)) {
                throw new RuntimeException(
                    "Operación SQL bloqueada: {$keyword}."
                );
            }
        }

        /*
         * Validamos las tablas utilizadas por la consulta.
         *
         * Buscamos tablas después de FROM y JOIN.
         * Cada tabla encontrada debe pertenecer a la lista blanca.
         */
        preg_match_all(
            '/\b(?:FROM|JOIN)\s+`?([a-zA-Z0-9_]+)`?/i',
            $sql,
            $matches
        );

        $tables = array_unique(
            array_map(
                'strtolower',
                $matches[1] ?? []
            )
        );

        if (empty($tables)) {
            throw new RuntimeException(
                'No se pudo identificar una tabla autorizada en la consulta.'
            );
        }

        foreach ($tables as $table) {
            if (!in_array($table, self::ALLOWED_TABLES, true)) {
                throw new RuntimeException(
                    "La IA intentó consultar una tabla no autorizada: {$table}."
                );
            }
        }

        // Evitamos comentarios SQL para reducir técnicas de evasión.
        if (
            str_contains($sql, '--') ||
            str_contains($sql, '#') ||
            str_contains($sql, '/*') ||
            str_contains($sql, '*/')
        ) {
            throw new RuntimeException(
                'No se permiten comentarios dentro del SQL generado.'
            );
        }

        return true;
    }

    /**
     * Ejecuta una consulta validada en modo de solo lectura.
     */
    public function execute(string $sql): array
    {
        $sql = $this->cleanSql($sql);

        $this->validate($sql);
        /*
         * Limitamos la cantidad de filas directamente en MySQL.
         *
         * - Sin LIMIT: agrega LIMIT 500.
         * - LIMIT mayor de 500: lo reduce a 500.
         * - LIMIT menor o igual a 500: lo conserva.
         */
        $sqlForExecution = rtrim($sql, " \t\n\r\0\x0B;");

        if (preg_match('/\bLIMIT\s+(\d+)\b/i', $sqlForExecution, $limitMatch)) {
            $requestedLimit = (int) $limitMatch[1];

            if ($requestedLimit > self::MAX_ROWS) {
                $sqlForExecution = preg_replace(
                    '/\bLIMIT\s+\d+\b/i',
                    'LIMIT ' . self::MAX_ROWS,
                    $sqlForExecution,
                    1
                );
            }
        } else {
            $sqlForExecution .= ' LIMIT ' . self::MAX_ROWS;
        }
        try {
            /*
             * Todas las consultas generadas por IA utilizan una conexión
             * MySQL independiente cuyo usuario solo posee permiso SELECT.
             *
             * Esto evita que la IA utilice las credenciales de escritura
             * de la conexión principal del restaurante.
             */
            $connection = DB::connection('ai_readonly');

            $connection->beginTransaction();

            try {
                $rows = $connection->select($sqlForExecution);

                $rows = array_slice($rows, 0, self::MAX_ROWS);

                $connection->rollBack();

                return array_map(
                    static fn ($row) => (array) $row,
                    $rows
                );
            } catch (Throwable $e) {
                if ($connection->transactionLevel() > 0) {
                    $connection->rollBack();
                }

                throw $e;
            }
        } catch (Throwable $e) {
            throw new RuntimeException(
                'No se pudo ejecutar la consulta de IA: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function getMaxRows(): int
    {
        return self::MAX_ROWS;
    }
}