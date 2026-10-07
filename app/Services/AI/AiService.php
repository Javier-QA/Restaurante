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
    protected string $provider;
    protected string $providerLabel;

    public function __construct(
        protected AiProviderConfigService $providerConfig
    ) {
        $config = $this->providerConfig->get();

        $this->apiKey = $config['api_key'];
        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->model = $config['model'];
        $this->provider = $config['provider'];
        $this->providerLabel = $config['provider_label'];
    }

    public function chat(
        array $messages,
        float $temperature = 0.2,
        int $maxTokens = 1500
    ): string {
        if ($this->baseUrl === '' || $this->model === '') {
            throw new RuntimeException(
                'La configuración del proveedor de IA está incompleta.'
            );
        }

        $definition = AiProviderConfigService::PROVIDERS[$this->provider]
            ?? null;

        if (($definition['requires_key'] ?? true) && $this->apiKey === '') {
            throw new RuntimeException(
                "El proveedor {$this->providerLabel} no tiene una API Key configurada."
            );
        }

        $request = Http::withHeaders([
            'Content-Type' => 'application/json',
        ]);

        if ($this->apiKey !== '') {
            $request = $request->withToken($this->apiKey);
        }

        $response = $request
            ->timeout(60)
            ->retry(
                2,
                2000,
                function (
                    Throwable $exception,
                    PendingRequest $request
                ): bool {
                    if ($exception instanceof ConnectionException) {
                        return true;
                    }

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
                    "El proveedor {$this->providerLabel} alcanzó temporalmente su límite de uso."
                );
            }

            if (in_array($status, [500, 502, 503, 504], true)) {
                throw new RuntimeException(
                    "El proveedor {$this->providerLabel} no está disponible temporalmente ({$status})."
                );
            }

            throw new RuntimeException(
                "Error al comunicarse con {$this->providerLabel}: {$status}."
            );
        }

        $content = $response->json('choices.0.message.content');

        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException(
                "{$this->providerLabel} devolvió una respuesta vacía o inválida."
            );
        }

        return trim($content);
    }

    public function isConfigured(): bool
    {
        $definition = AiProviderConfigService::PROVIDERS[$this->provider]
            ?? null;

        return $this->baseUrl !== ''
            && $this->model !== ''
            && (
                !($definition['requires_key'] ?? true)
                || $this->apiKey !== ''
            );
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getProviderLabel(): string
    {
        return $this->providerLabel;
    }
}
