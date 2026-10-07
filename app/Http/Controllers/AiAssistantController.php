<?php

namespace App\Http\Controllers;

use App\Models\AiQuery;
use App\Services\AI\AiAssistantService;
use App\Services\AI\AiContextService;
use App\Services\AI\AiSqlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AiAssistantController extends Controller
{
    public function __construct(protected AiAssistantService $assistantService,protected AiSqlService $sqlService,protected AiContextService $context){}

    public function index(): View{return view('ai.assistant');}

    public function ask(Request $request): JsonResponse
    {
        $v=$request->validate(['question'=>['required','string','max:500'],'chart_type'=>['nullable','in:bar,line,pie']]);
        try{return response()->json(['success'=>true]+$this->assistantService->ask(Auth::user(),$v['question'],$v['chart_type']??null));}
        catch(Throwable $e){report($e);return response()->json(['success'=>false,'message'=>$this->getPublicErrorMessage($e)],503);}
    }

    public function run(AiQuery $query): JsonResponse
    {
        try{return response()->json(['success'=>true]+$this->assistantService->run(Auth::user(),$query));}
        catch(Throwable $e){return response()->json(['success'=>false,'message'=>$e->getMessage()],403);}
    }

    public function schema(): \Illuminate\Http\Response
    {
        return response($this->context->getDatabaseContext(),200,['Content-Type'=>'text/plain; charset=UTF-8']);
    }

    public function history(): JsonResponse{return response()->json(['success'=>true,'data'=>$this->assistantService->history(Auth::user())->map(fn(AiQuery $q)=>$this->formatQuery($q))->values()]);}
    public function favorites(): JsonResponse{return response()->json(['success'=>true,'data'=>$this->assistantService->favorites(Auth::user())->map(fn(AiQuery $q)=>$this->formatQuery($q))->values()]);}
    public function toggleFavorite(AiQuery $query): JsonResponse{try{$q=$this->assistantService->toggleFavorite(Auth::user(),$query);return response()->json(['success'=>true,'data'=>$this->formatQuery($q)]);}catch(Throwable){return response()->json(['success'=>false,'message'=>'No fue posible modificar esta consulta.'],403);}}
    public function destroy(AiQuery $query): JsonResponse{try{$this->assistantService->delete(Auth::user(),$query);return response()->json(['success'=>true]);}catch(Throwable){return response()->json(['success'=>false,'message'=>'No fue posible eliminar esta consulta.'],403);}}

    public function exportCsv(Request $request): StreamedResponse
    {
        $v=$request->validate(['query_id'=>['required','integer','exists:ai_queries,id']]);
        $q=AiQuery::whereKey($v['query_id'])->where('user_id',Auth::id())->firstOrFail();
        $this->sqlService->validate($q->sql_query);$rows=array_map(static fn($r)=>(array)$r,$this->sqlService->execute($q->sql_query));
        $columns=$rows?array_keys($rows[0]):[];$filename='asistente_ia_'.$q->id.'_'.now()->format('Ymd_His').'.csv';
        return response()->streamDownload(function()use($columns,$rows){$h=fopen('php://output','w');fwrite($h,"\xEF\xBB\xBF");if($columns){fputcsv($h,$columns,';');foreach($rows as $row){fputcsv($h,array_map(fn($c)=>$this->sanitizeCsvValue($row[$c]??''),$columns),';');}}fclose($h);},$filename,['Content-Type'=>'text/csv; charset=UTF-8']);
    }

    private function sanitizeCsvValue(mixed $v): string{if($v===null)return '';if(is_bool($v))return $v?'1':'0';$v=(string)$v;return $v!==''&&in_array($v[0],['=','+','-','@'],true)?"'".$v:$v;}
    private function formatQuery(AiQuery $q): array{return ['id'=>$q->id,'question'=>$q->question,'result_count'=>$q->result_count,'chart_type'=>$q->chart_type,'is_favorite'=>(bool)$q->is_favorite,'created_at'=>$q->created_at?->format('d/m/Y H:i')];}
    private function getPublicErrorMessage(Throwable $e): string{ $m=$e->getMessage();if(str_contains($m,'429')||str_contains($m,'límite'))return 'El servicio de inteligencia artificial alcanzó temporalmente su límite de uso.';if(str_contains($m,'No pude'))return $m;return 'No fue posible procesar la consulta en este momento.';}
}
