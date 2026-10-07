<?php

namespace App\Http\Controllers;

use App\Services\AI\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AiChatController extends Controller
{
    public function __construct(
        protected AiChatService $chatService
    ) {
    }

    /**
     * Muestra la interfaz del Chat IA.
     */
    public function index(): View
    {
        return view('ai.chat');
    }

    /**
     * Procesa una pregunta enviada al Chat IA.
     */
    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        try {
            $result = $this->chatService->ask(
                $validated['message']
            );

            return response()->json([
                'success' => true,
                'answer' => $result['answer'],
            ]);

        } catch (Throwable $e) {

            report($e);

            $message = $this->getPublicErrorMessage($e);

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 503);
        }
    }

    /**
     * Convierte errores técnicos de la IA en mensajes seguros
     * para mostrar al usuario.
     */
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