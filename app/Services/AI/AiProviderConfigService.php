<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class AiProviderConfigService
{
    /**
     * Proveedores compatibles con el formato OpenAI Chat Completions.
     *
     * Los valores de modelo son ejemplos editables desde Configuración IA.
     */
    public const PROVIDERS = [
        'gemini' => [
            'label' => 'Google Gemini',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai',
            'model' => 'gemini-3.8-flash',
            'requires_key' => true,
        ],
        'openai' => [
            'label' => 'OpenAI',
            'base_url' => 'https://api.openai.com/v1',
            'model' => 'gpt-5.6-mini',
            'requires_key' => true,
        ],
        'groq' => [
            'label' => 'Groq',
            'base_url' => 'https://api.groq.com/openai/v1',
            'model' => 'llama-3.3-70b-versatile',
            'requires_key' => true,
        ],
        'openrouter' => [
            'label' => 'OpenRouter',
            'base_url' => 'https://openrouter.ai/api/v1',
            'model' => 'google/gemini-3.8-flash',
            'requires_key' => true,
        ],
        'ollama' => [
            'label' => 'Ollama local',
            'base_url' => 'http://127.0.0.1:11434/v1',
            'model' => 'llama3.2',
            'requires_key' => false,
        ],
    ];

    public function get(): array
    {
        $provider = (string) Setting::where('key', 'ia_proveedor')->value('value');
        $provider = array_key_exists($provider, self::PROVIDERS)
            ? $provider
            : 'gemini';

        $defaults = self::PROVIDERS[$provider];

        $baseUrl = (string) (
            Setting::where('key', 'ia_url')->value('value')
            ?: $defaults['base_url']
        );

        $model = (string) (
            Setting::where('key', 'ia_modelo')->value('value')
            ?: $defaults['model']
        );

        $storedKey = (string) Setting::where('key', 'ia_clave')->value('value');
        $apiKey = $this->decryptKey($storedKey);

        return [
            'provider' => $provider,
            'provider_label' => $defaults['label'],
            'base_url' => rtrim($baseUrl, '/'),
            'model' => $model,
            'api_key' => $apiKey,
            'requires_key' => (bool) $defaults['requires_key'],
        ];
    }

    public function save(
        string $provider,
        string $baseUrl,
        string $model,
        ?string $apiKey = null
    ): void {
        if (!array_key_exists($provider, self::PROVIDERS)) {
            throw new RuntimeException('El proveedor de IA seleccionado no es válido.');
        }

        $baseUrl = rtrim(trim($baseUrl), '/');
        $model = trim($model);

        if ($baseUrl === '' || $model === '') {
            throw new RuntimeException('La URL y el modelo son obligatorios.');
        }

        $definition = self::PROVIDERS[$provider];

        if ($definition['requires_key'] && trim((string) $apiKey) === '') {
            $existing = Setting::where('key', 'ia_clave')->value('value');

            if (!$existing) {
                throw new RuntimeException(
                    'Este proveedor requiere una API Key.'
                );
            }
        }

        Setting::updateOrCreate(
            ['key' => 'ia_proveedor'],
            ['value' => $provider]
        );

        Setting::updateOrCreate(
            ['key' => 'ia_url'],
            ['value' => $baseUrl]
        );

        Setting::updateOrCreate(
            ['key' => 'ia_modelo'],
            ['value' => $model]
        );

        if ($apiKey !== null && trim($apiKey) !== '') {
            Setting::updateOrCreate(
                ['key' => 'ia_clave'],
                ['value' => Crypt::encryptString(trim($apiKey))]
            );
        }

        Setting::updateOrCreate(
            ['key' => 'ia_vistas_ver'],
            ['value' => '1']
        );
    }

    public function publicConfig(): array
    {
        $config = $this->get();

        return [
            'provider' => $config['provider'],
            'provider_label' => $config['provider_label'],
            'base_url' => $config['base_url'],
            'model' => $config['model'],
            'configured' => $config['api_key'] !== '' || !$config['requires_key'],
            'key_masked' => $config['api_key'] !== ''
                ? '••••••••••••••••'
                : '',
        ];
    }

    public function providers(): array
    {
        return self::PROVIDERS;
    }

    private function decryptKey(string $value): string
    {
        if ($value === '') {
            return '';
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            // Compatibilidad con instalaciones antiguas que guardaban
            // la clave sin cifrar. Al guardar nuevamente se cifra.
            return $value;
        }
    }
}
