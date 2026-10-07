<?php

namespace App\Http\Controllers;

use App\Services\AI\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AiChatController extends Controller
{
    public function __construct(protected AiChatService $chatService){}

    public function index(): View{return view('ai.chat');}

    public function ask(Request $request): JsonResponse
    {
        $v=$request->validate(['message'=>['required','string','max:400']]);
        try{
            $history=session('ai_chat_history',[]);
            $result=$this->chatService->ask($v['message']);
            $history[]= ['q'=>$v['message'],'a'=>$result['answer']];
            session(['ai_chat_history'=>array_slice($history,-12)]);
            $rows=array_map(static fn($r)=>(array)$r,$result['data']??[]);
            return response()->json(['success'=>true,'answer'=>$result['answer'],'sql'=>$result['sql']??null,'data'=>$rows,'columns'=>$rows?array_keys($rows[0]):[]]);
        }catch(Throwable $e){
            report($e);return response()->json(['success'=>false,'message'=>$this->publicError($e)],503);
        }
    }

    public function clear(): JsonResponse{session()->forget('ai_chat_history');return response()->json(['success'=>true]);}

    public function state(): JsonResponse{return response()->json(['success'=>true,'history'=>session('ai_chat_history',[]),'configured'=>app(\App\Services\AI\AiService::class)->isConfigured()]);}

    private function publicError(Throwable $e): string{$m=$e->getMessage();if(str_contains($m,'429')||str_contains($m,'límite'))return 'El servicio de IA alcanzó temporalmente su límite de uso.';return $m;}
}
