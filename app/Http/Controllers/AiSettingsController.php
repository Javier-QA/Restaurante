<?php

namespace App\Http\Controllers;

use App\Services\AI\AiProviderConfigService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AiSettingsController extends Controller
{
    public function __construct(
        protected AiProviderConfigService $config
    ) {
    }

    public function index(): View
    {
        return view('ai.settings', [
            'config' => $this->config->publicConfig(),
            'providers' => $this->config->providers(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'in:' . implode(',', array_keys(AiProviderConfigService::PROVIDERS))],
            'base_url' => ['required', 'url:http,https', 'max:500'],
            'model' => ['required', 'string', 'max:150'],
            'api_key' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->config->save(
                $validated['provider'],
                $validated['base_url'],
                $validated['model'],
                $validated['api_key'] ?? null
            );
        } catch (RuntimeException $e) {
            return back()
                ->withInput($request->except('api_key'))
                ->withErrors(['api_key' => $e->getMessage()]);
        }

        return redirect()
            ->route('ai.settings')
            ->with('success', 'Configuración de IA actualizada correctamente.');
    }
}
