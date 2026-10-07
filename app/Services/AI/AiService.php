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
    public function __construct(protected AiProviderConfigService $config) {}

    public function chat(array $messages,float $temperature=0.2,int $maxTokens=1500): string
    {
        $c=$this->config->get();
        if($c['requires_key'] && $c['api_key']==='') throw new RuntimeException('La IA no está configurada. Pulsa «Configurar IA» e ingresa tu clave.');
        $headers=['Content-Type'=>'application/json','Accept'=>'application/json'];
        if($c['api_key']!=='') $headers['Authorization']='Bearer '.$c['api_key'];

        $response=Http::withHeaders($headers)->timeout(60)->retry(2,1000,function(Throwable $e,PendingRequest $r){
            if($e instanceof ConnectionException) return true;
            if(method_exists($e,'response') && $e->response instanceof Response) return in_array($e->response->status(),[500,502,503,504],true);
            return false;
        })->post(rtrim($c['base_url'],'/').'/chat/completions',[
            'model'=>$c['model'],'messages'=>$messages,'temperature'=>$temperature,'max_tokens'=>$maxTokens,
        ]);

        if($response->failed()){
            $s=$response->status();
            if(in_array($s,[401,403],true)) throw new RuntimeException('La clave de la IA no es válida o no tiene permiso.');
            if($s===429) throw new RuntimeException('Se alcanzó el límite de uso del proveedor de IA. Inténtalo nuevamente más tarde.');
            if($s===404) throw new RuntimeException('No se encontró el modelo o la URL configurada.');
            throw new RuntimeException('El proveedor de IA rechazó la petición ('.$s.').');
        }
        $content=$response->json('choices.0.message.content');
        if(!is_string($content)||trim($content)==='') throw new RuntimeException('La IA devolvió una respuesta vacía o inválida.');
        return trim($content);
    }

    public function isConfigured(): bool
    {
        $c=$this->config->get();
        return $c['base_url']!=='' && $c['model']!=='' && (!$c['requires_key']||$c['api_key']!=='');
    }

    public function getModel(): string { return $this->config->get()['model']; }
}
