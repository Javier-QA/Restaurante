<?php

namespace App\Services\SysIa;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProviderClient
{
    /** Retry one transient server error, within the original 25-second budget. */
    public function send(string $endpoint, array $config, array $payload): Response
    {
        $deadline = microtime(true) + 25;
        $cafile = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
        if (! $cafile && is_file(storage_path('app/cacert.pem'))) {
            $cafile = storage_path('app/cacert.pem');
        }
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $remaining = max(.1, $deadline - microtime(true));
            $request = Http::acceptJson()->asJson()->timeout($remaining)->connectTimeout(min(5, $remaining));
            if (($config['clave'] ?? '') !== '') {
                $request = $request->withToken($config['clave']);
            }
            if ($cafile) {
                $request = $request->withOptions(['verify' => $cafile]);
            }
            $response = $request->post($endpoint, $payload);
            if ($response->failed()) {
                // Never record the key, prompts, response body, or custom URL.
                Log::warning('SYS IA: provider request failed', [
                    'provider' => $config['proveedor'] ?? 'otro',
                    'status' => $response->status(),
                    'attempt' => $attempt,
                ]);
            }
            if (! in_array($response->status(), [500, 502, 503, 504], true)
                || $attempt === 2 || $deadline - microtime(true) < 1) {
                return $response;
            }
        }

        return $response;
    }
}
