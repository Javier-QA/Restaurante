<?php

namespace App\Services\AI;

use App\Models\AiQuery;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class AiAssistantService
{
    public function __construct(
        protected AiQueryService $queryService,
        protected AiSqlService $sqlService
    ) {
    }

    /**
     * Procesa una consulta analítica del administrador.
     */
    public function ask(
        User $user,
        string $question,
        ?string $chartType = null
    ): array {
        $question = trim($question);

        if ($question === '') {
            throw new RuntimeException(
                'La pregunta no puede estar vacía.'
            );
        }

        if (mb_strlen($question) > 500) {
            throw new RuntimeException(
                'La pregunta supera el límite de 500 caracteres.'
            );
        }

        $allowedCharts = [
            null,
            'bar',
            'line',
            'pie',
        ];

        if (!in_array($chartType, $allowedCharts, true)) {
            throw new RuntimeException(
                'El tipo de gráfico seleccionado no es válido.'
            );
        }

        // Gemini transforma la pregunta en SQL.
        $sql = $this->queryService->generateSql($question);

        // La consulta vuelve a pasar por la capa de seguridad.
        $this->sqlService->validate($sql);

        // Ejecutamos únicamente SQL autorizado.
        $rows = $this->sqlService->execute($sql);

        // Registramos la consulta en el historial.
        $query = AiQuery::create([
            'user_id' => $user->id,
            'question' => $question,
            'sql_query' => $sql,
            'result_count' => count($rows),
            'chart_type' => $chartType,
            'is_favorite' => false,
        ]);

        $this->trimHistory($user);

        return [
            'query_id' => $query->id,
            'question' => $question,
            'data' => $rows,
            'total_rows' => count($rows),
            'chart_type' => $chartType,
        ];
    }

    /**
     * Devuelve las últimas consultas del usuario.
     */
    public function history(User $user, int $limit = 20): Collection
    {
        $limit = max(1, min($limit, 60));

        return AiQuery::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Devuelve únicamente consultas favoritas.
     */
    public function favorites(User $user): Collection
    {
        return AiQuery::query()
            ->where('user_id', $user->id)
            ->where('is_favorite', true)
            ->latest()
            ->get();
    }

    /**
     * Cambia el estado favorito de una consulta.
     */
    public function toggleFavorite(
        User $user,
        AiQuery $query
    ): AiQuery {
        $this->ensureOwnership($user, $query);

        $query->update([
            'is_favorite' => !$query->is_favorite,
        ]);

        return $query->fresh();
    }

    /**
     * Elimina una consulta del historial.
     */
    public function delete(
        User $user,
        AiQuery $query
    ): void {
        $this->ensureOwnership($user, $query);

        $query->delete();
    }

    /**
     * Conserva un máximo de 60 consultas normales.
     *
     * Las consultas favoritas nunca se eliminan
     * automáticamente.
     */
    private function trimHistory(User $user): void
    {
        $idsToDelete = AiQuery::query()
            ->where('user_id', $user->id)
            ->where('is_favorite', false)
            ->latest()
            ->skip(60)
            ->take(1000)
            ->pluck('id');

        if ($idsToDelete->isNotEmpty()) {
            AiQuery::query()
                ->whereIn('id', $idsToDelete)
                ->delete();
        }
    }

    /**
     * Evita que un usuario manipule consultas
     * pertenecientes a otro usuario.
     */
    private function ensureOwnership(
        User $user,
        AiQuery $query
    ): void {
        if ((int) $query->user_id !== (int) $user->id) {
            throw new RuntimeException(
                'No tienes permiso para acceder a esta consulta.'
            );
        }
    }
}