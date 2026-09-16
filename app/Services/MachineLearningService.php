<?php

namespace App\Services;

class MachineLearningService
{
    private string $script;
    private string $python;

    public function __construct()
    {
        $this->script = base_path(
            'machine_learning/scripts/predecir_api.py'
        );

        $this->python = 'C:\Users\JAVIER\AppData\Local\Programs\Python\Python314\python.exe';
    }

    public function predictSales(?string $date = null): array
    {
        if (!file_exists($this->script)) {
            return [
                'success' => false,
                'error' => 'No se encontró el script de Machine Learning.'
            ];
        }

        if (!file_exists($this->python)) {
            return [
                'success' => false,
                'error' => 'No se encontró el ejecutable de Python.'
            ];
        }

        if ($date !== null) {
            $parsedDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
            $dateErrors = \DateTimeImmutable::getLastErrors();

            if (
                $parsedDate === false ||
                ($dateErrors !== false &&
                    ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) ||
                $parsedDate->format('Y-m-d') !== $date
            ) {
                return [
                    'success' => false,
                    'error' => 'La fecha proporcionada no es válida.'
                ];
            }
        }

        $command = escapeshellarg($this->python)
            . ' '
            . escapeshellarg($this->script);

        if ($date !== null) {
            $command .= ' ' . escapeshellarg($date);
        }

        $command .= ' 2>&1';

        $output = [];
        $exitCode = 0;

        exec($command, $output, $exitCode);

        $rawOutput = trim(implode(PHP_EOL, $output));

        if ($exitCode !== 0) {
            \Log::error('Error ejecutando Machine Learning', [
                'exit_code' => $exitCode,
                'output' => $rawOutput,
            ]);

            return [
                'success' => false,
                'error' => 'No se pudo ejecutar el modelo de Machine Learning.'
            ];
        }

        $result = json_decode($rawOutput, true);

        if (!is_array($result)) {
            \Log::error('Respuesta inválida de Machine Learning', [
                'output' => $rawOutput,
            ]);

            return [
                'success' => false,
                'error' => 'El modelo devolvió una respuesta inválida.'
            ];
        }

        return $result;
    }
}