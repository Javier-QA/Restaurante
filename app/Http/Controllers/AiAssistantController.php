<?php

namespace App\Http\Controllers;

use App\Models\AiQuery;
use App\Services\AI\AiAssistantService;
use App\Services\AI\AiSqlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AiAssistantController extends Controller
{
    public function __construct(
        protected AiAssistantService $assistantService,
        protected AiSqlService $sqlService
    ) {
    }

    /**
     * Muestra la interfaz principal del Asistente IA.
     */
    public function index(): View
    {
        return view('ai.assistant');
    }

    /**
     * Procesa una consulta analítica.
     */
    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => [
                'required',
                'string',
                'max:500',
            ],
            'chart_type' => [
                'nullable',
                'in:bar,line,pie',
            ],
        ]);

        try {
            $result = $this->assistantService->ask(
                Auth::user(),
                $validated['question'],
                $validated['chart_type'] ?? null
            );

            return response()->json([
                'success' => true,
                'query_id' => $result['query_id'],
                'question' => $result['question'],
                'data' => $result['data'],
                'total_rows' => $result['total_rows'],
                'chart_type' => $result['chart_type'],
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $this->getPublicErrorMessage($e),
            ], 503);
        }
    }

    /**
     * Obtiene el historial del administrador autenticado.
     */
    public function history(): JsonResponse
    {
        $queries = $this->assistantService
            ->history(Auth::user())
            ->map(fn (AiQuery $query) => $this->formatQuery($query))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $queries,
        ]);
    }

    /**
     * Obtiene las consultas favoritas.
     */
    public function favorites(): JsonResponse
    {
        $queries = $this->assistantService
            ->favorites(Auth::user())
            ->map(fn (AiQuery $query) => $this->formatQuery($query))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $queries,
        ]);
    }

    /**
     * Marca o desmarca una consulta como favorita.
     */
    public function toggleFavorite(AiQuery $query): JsonResponse
    {
        try {
            $query = $this->assistantService->toggleFavorite(
                Auth::user(),
                $query
            );

            return response()->json([
                'success' => true,
                'data' => $this->formatQuery($query),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'No fue posible modificar esta consulta.',
            ], 403);
        }
    }

    /**
     * Elimina una consulta del historial.
     */
    public function destroy(AiQuery $query): JsonResponse
    {
        try {
            $this->assistantService->delete(
                Auth::user(),
                $query
            );

            return response()->json([
                'success' => true,
                'message' => 'Consulta eliminada correctamente.',
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'No fue posible eliminar esta consulta.',
            ], 403);
        }
    }

    /**
     * Exporta a CSV los resultados actuales enviados
     * desde la interfaz.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'query_id' => [
                'required',
                'integer',
                'exists:ai_queries,id',
            ],
        ]);

        $query = AiQuery::query()
            ->whereKey($validated['query_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (empty($query->sql_query)) {
            abort(422, 'La consulta no contiene información exportable.');
        }

        /*
         * El SQL almacenado vuelve a pasar por AiSqlService.
         * No confiamos en datos enviados desde el navegador.
         */
        $this->sqlService->validate($query->sql_query);
        $rows = $this->sqlService->execute($query->sql_query);

        $rows = array_map(
            static fn ($row) => (array) $row,
            $rows
        );

        $columns = !empty($rows)
            ? array_keys($rows[0])
            : [];

        $filename =
            'asistente_ia_' .
            $query->id .
            '_' .
            now()->format('Ymd_His') .
            '.csv';

        return response()->streamDownload(
            function () use ($columns, $rows) {

                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    return;
                }

                /*
                 * BOM UTF-8 para que Excel reconozca
                 * correctamente tildes y caracteres especiales.
                 */
                fwrite($handle, "\xEF\xBB\xBF");

                if (!empty($columns)) {

                    fputcsv(
                        $handle,
                        array_map(
                            fn ($value) => $this->sanitizeCsvValue($value),
                            $columns
                        ),
                        ';'
                    );

                    foreach ($rows as $row) {

                        $values = [];

                        foreach ($columns as $column) {

                            $values[] =
                                $this->sanitizeCsvValue(
                                    $row[$column] ?? ''
                                );
                        }

                        fputcsv($handle, $values, ';');
                    }
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'X-Content-Type-Options' =>
                    'nosniff',
            ]
        );
    }

    private function sanitizeCsvValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        $value = (string) $value;

        /*
         * Evita CSV/Formula Injection al abrir el archivo
         * en Excel, LibreOffice u otra hoja de cálculo.
         */
        if (
            $value !== '' &&
            in_array($value[0], ['=', '+', '-', '@'], true)
        ) {
            return "'" . $value;
        }

        return $value;
    }
    private function formatQuery(AiQuery $query): array
    {
        return [
            'id' => $query->id,
            'question' => $query->question,
            'result_count' => $query->result_count,
            'chart_type' => $query->chart_type,
            'is_favorite' => $query->is_favorite,
            'created_at' => $query->created_at?->format('d/m/Y H:i'),
        ];
    }

    private function getPublicErrorMessage(Throwable $e): string
    {
        $message = $e->getMessage();

        if (
            str_contains($message, '429') ||
            str_contains($message, 'quota') ||
            str_contains($message, 'RESOURCE_EXHAUSTED')
        ) {
            return 'El servicio de inteligencia artificial alcanzó temporalmente su límite de uso. Inténtalo nuevamente más tarde.';
        }

        if (
            str_contains($message, '503') ||
            str_contains($message, 'UNAVAILABLE') ||
            str_contains($message, 'high demand')
        ) {
            return 'El servicio de inteligencia artificial se encuentra temporalmente ocupado. Inténtalo nuevamente en unos minutos.';
        }

        return 'No fue posible procesar la consulta en este momento.';
    }
}