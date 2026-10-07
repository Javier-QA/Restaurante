<?php

namespace App\Services\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class AiService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');

        $this->baseUrl = rtrim(
            (string) config('services.gemini.base_url'),
            '/'
        );

        $this->model = (string) config('services.gemini.model');
    }

    /**
     * Envía mensajes a Gemini mediante su endpoint
     * compatible con OpenAI.
     */
    public function chat(
        array $messages,
        float $temperature = 0.2,
        int $maxTokens = 1500
    ): string {
        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'La API Key de Gemini no está configurada.'
            );
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout(60)
            ->retry(
                2,
                2000,
                function (
                    Throwable $exception,
                    PendingRequest $request
                ): bool {
                    /*
                     * Los errores de conexión pueden ser temporales.
                     */
                    if ($exception instanceof ConnectionException) {
                        return true;
                    }

                    /*
                     * Si existe una respuesta HTTP, únicamente
                     * reintentamos errores temporales del servidor.
                     */
                    if (
                        method_exists($exception, 'response') &&
                        $exception->response instanceof Response
                    ) {
                        return in_array(
                            $exception->response->status(),
                            [500, 502, 503, 504],
                            true
                        );
                    }

                    return false;
                },
                throw: false
            )
            ->post(
                $this->baseUrl . '/chat/completions',
                [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]
            );

        if ($response->failed()) {
            $status = $response->status();

            if ($status === 429) {
                throw new RuntimeException(
                    'Error al comunicarse con Gemini: 429 - límite de uso alcanzado.'
                );
            }

            if (in_array($status, [500, 502, 503, 504], true)) {
                throw new RuntimeException(
                    "Error al comunicarse con Gemini: {$status} - servicio temporalmente no disponible."
                );
            }

            throw new RuntimeException(
                "Error al comunicarse con Gemini: {$status}."
            );
        }

        $content = $response->json(
            'choices.0.message.content'
        );

        if (
            !is_string($content) ||
            trim($content) === ''
        ) {
            throw new RuntimeException(
                'Gemini devolvió una respuesta vacía o inválida.'
            );
        }

        return trim($content);
    }

    /**
     * Comprueba si Gemini está configurado.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey)
            && !empty($this->baseUrl)
            && !empty($this->model);
    }

    /**
     * Devuelve el modelo configurado sin exponer la API Key.
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
