<?php

namespace App\Services\AI;

use RuntimeException;

class AiChatService
{
    public function __construct(
        protected AiQueryService $queryService,
        protected AiSqlService $sqlService
    ) {
    }

    /**
     * Responde una pregunta utilizando información real del restaurante.
     *
     * Gemini se utiliza únicamente para transformar la pregunta en SQL.
     * Los resultados se presentan localmente para evitar una segunda
     * llamada al proveedor de IA.
     */
    public function ask(string $question): array
    {
        $question = trim($question);

        if ($question === '') {
            throw new RuntimeException(
                'La pregunta no puede estar vacía.'
            );
        }

        if (mb_strlen($question) > 500) {
            throw new RuntimeException(
                'La pregunta supera el límite de 500 caracteres.'
            );
        }

        // 1. Gemini transforma la pregunta en SQL.
        $sql = $this->queryService->generateSql($question);

        // 2. El SQL se ejecuta mediante la conexión segura ai_readonly.
        $rows = $this->sqlService->execute($sql);

        // 3. PHP presenta los resultados sin realizar otra llamada a Gemini.
        $answer = $this->buildAnswer($rows);

        return [
            'question' => $question,
            'answer' => $answer,
            'sql' => $sql,
            'data' => $rows,
            'total_rows' => count($rows),
        ];
    }

    /**
     * Convierte resultados SQL en una respuesta legible.
     */
    private function buildAnswer(array $rows): string
    {
        if (empty($rows)) {
            return 'No se encontraron resultados para la consulta realizada.';
        }

        if (count($rows) === 1) {
            return $this->formatSingleRow($rows[0]);
        }

        return $this->formatMultipleRows($rows);
    }

    /**
     * Presenta una única fila de resultados.
     */
    private function formatSingleRow(array $row): string
    {
        if (count($row) === 1) {
            $column = (string) array_key_first($row);
            $value = $row[$column];

            return $this->humanize($column)
                . ': '
                . $this->formatValue($column, $value)
                . '.';
        }

        $parts = [];

        foreach ($row as $column => $value) {
            $parts[] = $this->humanize((string) $column)
                . ': '
                . $this->formatValue((string) $column, $value);
        }

        return implode(' | ', $parts);
    }

    /**
     * Presenta varias filas de forma compacta.
     */
    private function formatMultipleRows(array $rows): string
    {
        $lines = [];
        $limit = min(count($rows), 20);

        for ($i = 0; $i < $limit; $i++) {
            $parts = [];

            foreach ($rows[$i] as $column => $value) {
                $parts[] = $this->humanize((string) $column)
                    . ': '
                    . $this->formatValue((string) $column, $value);
            }

            $lines[] = ($i + 1) . '. ' . implode(' | ', $parts);
        }

        $answer = implode(PHP_EOL, $lines);

        if (count($rows) > $limit) {
            $answer .= PHP_EOL
                . 'Se encontraron '
                . count($rows)
                . ' resultados en total. Se muestran los primeros '
                . $limit
                . '.';
        }

        return $answer;
    }

    /**
     * Convierte nombres SQL en etiquetas más legibles.
     */
    private function humanize(string $column): string
    {
        $column = str_replace('_', ' ', trim($column));

        return ucfirst($column);
    }

    /**
     * Aplica formato básico a valores monetarios y nulos.
     */
    private function formatValue(string $column, mixed $value): string
    {
        if ($value === null) {
            return 'Sin dato';
        }

        $column = strtolower($column);

        $moneyTerms = [
            'total',
            'subtotal',
            'igv',
            'precio',
            'importe',
            'monto',
            'costo',
            'descuento',
            'propina',
            'ingreso',
            'venta',
        ];

        foreach ($moneyTerms as $term) {
            if (
                str_contains($column, $term) &&
                is_numeric($value)
            ) {
                return 'S/ ' . number_format(
                    (float) $value,
                    2,
                    '.',
                    ','
                );
            }
        }

        if (is_bool($value)) {
            return $value ? 'Sí' : 'No';
        }

        return (string) $value;
    }
}
