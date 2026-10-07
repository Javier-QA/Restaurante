<?php

namespace App\Services\AI;

use App\Models\AiQuery;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class AiAssistantService
{
    public function __construct(protected AiQueryService $queryService,protected AiSqlService $sqlService,protected AiService $ai){}

    public function ask(User $user,string $question,?string $chartType=null): array
    {
        $question=trim($question);
        if($question==='') throw new RuntimeException('La pregunta no puede estar vacía.');
        if(mb_strlen($question)>500) throw new RuntimeException('La pregunta supera el límite de 500 caracteres.');

        $sql=$this->queryService->generateSql($question);
        $this->sqlService->validate($sql);
        $rows=$this->sqlService->execute($sql);
        $rows=array_map(static fn($r)=>(array)$r,$rows);
        $type=$this->autoChart($question,$rows,$chartType);
        $query=AiQuery::create(['user_id'=>$user->id,'question'=>$question,'sql_query'=>$sql,'result_count'=>count($rows),'chart_type'=>$type,'is_favorite'=>false]);
        $this->trimHistory($user);

        $summary=null;
        $cfg=app(AiProviderConfigService::class)->get();
        if($cfg['summary'] && $rows){
            try{
                $data=json_encode(['columns'=>array_keys($rows[0]),'rows'=>array_slice($rows,0,25)],JSON_UNESCAPED_UNICODE);
                $summary=$this->ai->chat([
                    ['role'=>'system','content'=>'Eres analista de un restaurante. Resume en 1 o 2 frases en español usando únicamente los datos recibidos. Usa cifras exactas y soles (S/). No inventes ni uses markdown.'],
                    ['role'=>'user','content'=>"Pregunta: {$question}\nDatos: {$data}"],
                ],0.2,300);
            }catch(\Throwable){$summary=null;}
        }

        return $this->formatResult($query,$rows,$type,$summary,$sql);
    }

    public function run(User $user,AiQuery $query): array
    {
        $this->ensureOwnership($user,$query);
        $this->sqlService->validate($query->sql_query);
        $rows=array_map(static fn($r)=>(array)$r,$this->sqlService->execute($query->sql_query));
        $query->increment('result_count',0);
        return $this->formatResult($query,$rows,$query->chart_type,null,$query->sql_query);
    }

    public function history(User $user,int $limit=20): Collection{return AiQuery::query()->where('user_id',$user->id)->latest()->limit(max(1,min($limit,60)))->get();}
    public function favorites(User $user): Collection{return AiQuery::query()->where('user_id',$user->id)->where('is_favorite',true)->latest()->get();}
    public function toggleFavorite(User $user,AiQuery $query): AiQuery{$this->ensureOwnership($user,$query);$query->update(['is_favorite'=>!$query->is_favorite]);return $query->fresh();}
    public function delete(User $user,AiQuery $query): void{$this->ensureOwnership($user,$query);$query->delete();}

    private function formatResult(AiQuery $query,array $rows,?string $type,?string $summary, string $sql): array
    {
        return ['query_id'=>$query->id,'question'=>$query->question,'title'=>$this->title($query->question),'data'=>$rows,'total_rows'=>count($rows),'chart_type'=>$type,'summary'=>$summary,'sql'=>$sql,'is_favorite'=>(bool)$query->is_favorite];
    }

    private function title(string $q): string
    {
        $q=trim($q); return mb_strlen($q)>100?mb_substr($q,0,97).'...':$q;
    }

    private function autoChart(string $q,array $rows,?string $requested): ?string
    {
        if(count($rows)<2) return null;
        if(in_array($requested,['bar','line','pie'],true)) return $requested;
        if(preg_match('/día|diaria|diario|semana|mes|fecha|hora|evolución|últimos/i',$q)) return 'line';
        if(preg_match('/participación|porcentaje|distribución|método de pago/i',$q) && count($rows)<=8) return 'pie';
        return 'bar';
    }

    private function trimHistory(User $user): void
    {
        $ids=AiQuery::query()->where('user_id',$user->id)->where('is_favorite',false)->latest()->skip(60)->take(1000)->pluck('id');
        if($ids->isNotEmpty()) AiQuery::whereIn('id',$ids)->delete();
    }
    private function ensureOwnership(User $user,AiQuery $query): void{if((int)$query->user_id!==(int)$user->id)throw new RuntimeException('No tienes permiso para acceder a esta consulta.');}
}
