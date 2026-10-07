<?php

namespace App\Http\Controllers;

use App\Services\AI\AiProviderConfigService;
use App\Services\AI\AiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class AiSettingsController extends Controller
{
    public function __construct(protected AiProviderConfigService $config,protected AiService $ai){}

    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if($request->expectsJson()||$request->ajax()) return response()->json(['config'=>$this->config->publicConfig(),'providers'=>$this->config->providers()]);
        return view('ai.settings',['config'=>$this->config->publicConfig(),'providers'=>$this->config->providers()]);
    }

    public function update(Request $request)
    {
        $validated=$request->validate([
            'provider'=>['required','string','in:'.implode(',',array_keys(AiProviderConfigService::PROVIDERS))],
            'base_url'=>['required','url:http,https','max:500'],
            'model'=>['required','string','max:150'],
            'api_key'=>['nullable','string','max:2000'],
            'summary'=>['nullable','boolean'],
        ]);
        try{$this->config->save($validated['provider'],$validated['base_url'],$validated['model'],$validated['api_key']??null,array_key_exists('summary',$validated)?(bool)$validated['summary']:null);}
        catch(RuntimeException $e){if($request->expectsJson()||$request->ajax()) return response()->json(['success'=>false,'message'=>$e->getMessage()],422);return back()->withInput($request->except('api_key'))->withErrors(['api_key'=>$e->getMessage()]);}
        if($request->expectsJson()||$request->ajax()) return response()->json(['success'=>true,'config'=>$this->config->publicConfig()]);
        return redirect()->route('ai.settings')->with('success','Configuración de IA actualizada correctamente.');
    }

    public function test(): \Illuminate\Http\JsonResponse
    {
        try{$this->ai->chat([['role'=>'user','content'=>'Responde solo con la palabra OK.']],0,30);return response()->json(['success'=>true,'respuesta'=>'OK']);}
        catch(Throwable $e){return response()->json(['success'=>false,'message'=>$e->getMessage()],422);}
    }
}
