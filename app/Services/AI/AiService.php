<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use RuntimeException;

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
                4,
                function (int $attempt) {
                    return $attempt * 2000;
                },
                throw: false
            )
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Error al comunicarse con Gemini: '
                . $response->status()
                . ' - '
                . $response->body()
            );
        }

        $content = $response->json('choices.0.message.content');

        if (!is_string($content) || trim($content) === '') {
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
