<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class AiSqlService
{
    private const MAX_ROWS = 500;

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

        // Solo permitimos SELECT o CTE mediante WITH.
        if (!preg_match('/^\s*(SELECT|WITH)\b/i', $sql)) {
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

        foreach ($forbidden as $keyword) {
            $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';

            if (preg_match($pattern, $normalized)) {
                throw new RuntimeException(
                    "Operación SQL bloqueada: {$keyword}."
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

        try {
            $pdo = DB::connection()->getPdo();

            // Defensa adicional a nivel de sesión MySQL.
            $pdo->exec('SET SESSION TRANSACTION READ ONLY');

            DB::beginTransaction();

            try {
                $rows = DB::select($sql);

                $rows = array_slice($rows, 0, self::MAX_ROWS);

                DB::rollBack();

                return array_map(
                    static fn ($row) => (array) $row,
                    $rows
                );
            } catch (Throwable $e) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                throw $e;
            } finally {
                // Restauramos la sesión utilizada por Laravel.
                try {
                    $pdo->exec('SET SESSION TRANSACTION READ WRITE');
                } catch (Throwable) {
                    // La validación de SQL sigue siendo la barrera principal.
                }
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