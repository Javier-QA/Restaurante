<?php

namespace App\Services\AI;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class AiProviderConfigService
{
    public const PROVIDERS = [
        'gemini' => [
            'label' => 'Google Gemini (gratis)',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai',
            'model' => 'gemini-3.5-flash-lite',
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
            'model' => 'meta-llama/llama-3.3-70b-instruct:free',
            'requires_key' => true,
        ],
        'ollama' => [
            'label' => 'Ollama (local, sin internet)',
            'base_url' => 'http://127.0.0.1:11434/v1',
            'model' => 'qwen2.5:7b',
            'requires_key' => false,
        ],
        'openai' => [
            'label' => 'Otro (compatible con OpenAI)',
            'base_url' => 'https://api.openai.com/v1',
            'model' => 'gpt-4o-mini',
            'requires_key' => true,
        ],
    ];

    public function get(): array
    {
        $provider=(string)Setting::where('key','ia_proveedor')->value('value');
        $provider=array_key_exists($provider,self::PROVIDERS)?$provider:'gemini';
        $d=self::PROVIDERS[$provider];
        $url=(string)(Setting::where('key','ia_url')->value('value') ?: $d['base_url']);
        $model=(string)(Setting::where('key','ia_modelo')->value('value') ?: $d['model']);
        $stored=(string)Setting::where('key','ia_clave')->value('value');
        return ['provider'=>$provider,'provider_label'=>$d['label'],'base_url'=>rtrim($url,'/'),'model'=>$model,'api_key'=>$this->decryptKey($stored),'requires_key'=>(bool)$d['requires_key'],'summary'=>Setting::where('key','ia_resumen')->value('value')!=='0'];
    }

    public function save(string $provider,string $baseUrl,string $model,?string $apiKey=null,?bool $summary=null): void
    {
        if(!array_key_exists($provider,self::PROVIDERS)) throw new RuntimeException('El proveedor de IA seleccionado no es válido.');
        $baseUrl=rtrim(trim($baseUrl),'/'); $model=trim($model); $d=self::PROVIDERS[$provider];
        if($baseUrl===''||$model==='') throw new RuntimeException('La URL y el modelo son obligatorios.');
        if($d['requires_key'] && trim((string)$apiKey)===''){
            $existing=Setting::where('key','ia_clave')->value('value');
            if(!$existing) throw new RuntimeException('Este proveedor requiere una API Key.');
        }
        Setting::updateOrCreate(['key'=>'ia_proveedor'],['value'=>$provider]);
        Setting::updateOrCreate(['key'=>'ia_url'],['value'=>$baseUrl]);
        Setting::updateOrCreate(['key'=>'ia_modelo'],['value'=>$model]);
        if($apiKey!==null && trim($apiKey)!=='') Setting::updateOrCreate(['key'=>'ia_clave'],['value'=>Crypt::encryptString(trim($apiKey))]);
        if($summary!==null) Setting::updateOrCreate(['key'=>'ia_resumen'],['value'=>$summary?'1':'0']);
        Setting::updateOrCreate(['key'=>'ia_vistas_ver'],['value'=>'1']);
    }

    public function publicConfig(): array
    {
        $c=$this->get();
        return ['provider'=>$c['provider'],'provider_label'=>$c['provider_label'],'base_url'=>$c['base_url'],'model'=>$c['model'],'configured'=>$c['api_key']!==''||!$c['requires_key'],'key_masked'=>$c['api_key']!==''?'••••••••••••••••':'','summary'=>$c['summary']];
    }

    public function providers(): array { return self::PROVIDERS; }

    private function decryptKey(string $value): string
    {
        if($value==='') return '';
        try{return Crypt::decryptString($value);}catch(\Throwable){return $value;}
    }
}
