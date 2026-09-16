<?php

namespace App\Http\Controllers;

use App\Services\RestaurantAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $message = trim($request->input('message', ''));

        if ($message === '') {
            return response()->json([
                'response' => 'Escribe una pregunta para que pueda ayudarte.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ASISTENTE INTELIGENTE DEL RESTAURANTE
        |--------------------------------------------------------------------------
        | Primero intenta responder utilizando los datos reales del sistema.
        | Si no reconoce la consulta, continúa con las reglas anteriores
        | y finalmente utiliza Ollama como respaldo.
        */

        try {
            $assistant = app(RestaurantAssistantService::class);
            $assistantResult = $assistant->answer($message);

            if ($assistantResult !== null) {
                return response()->json($assistantResult);
            }
        } catch (\Throwable $e) {
            \Log::error('Error en RestaurantAssistantService', [
                'message' => $e->getMessage(),
                'question' => $message,
            ]);
        }
        /*
        |--------------------------------------------------------------------------
        | 9. PREGUNTAS GENERALES → OLLAMA
        |--------------------------------------------------------------------------
        */

        try {
            $ollamaUrl = env(
                'OLLAMA_URL',
                'http://localhost:11434'
            );

            $ollamaModel = env(
                'OLLAMA_MODEL',
                'llama3.2'
            );

            $systemPrompt = <<<PROMPT
Eres el asistente inteligente interno de un restaurante.

Ayudas al administrador del restaurante.

Responde siempre en español.

Puedes responder preguntas generales relacionadas con:

- restaurantes
- ventas
- marketing
- promociones
- atención al cliente
- administración
- inventario
- estrategias comerciales
- fidelización de clientes
- redes sociales
- publicidad
- mejora del servicio

Da respuestas claras, naturales, profesionales y prácticas.

No inventes datos específicos del restaurante.

Si el administrador pregunta por cifras, ventas, pedidos, stock o productos específicos y no se te proporcionó ese dato, indica claramente que ese dato no está disponible.

No inventes precios ni cantidades.

No menciones Ollama.

No menciones que eres una inteligencia artificial.

Habla como un asistente interno del restaurante.
PROMPT;

            $ollamaResponse = Http::timeout(120)
                ->post($ollamaUrl . '/api/chat', [
                    'model' => $ollamaModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'stream' => false
                ]);

            if (!$ollamaResponse->successful()) {
                return response()->json([
                    'response' =>
                        'No pude conectarme con la inteligencia artificial local.'
                ], 500);
            }

            $respuestaIA =
                $ollamaResponse->json('message.content');

            if (!$respuestaIA) {
                return response()->json([
                    'response' =>
                        'La inteligencia artificial no devolvió una respuesta.'
                ], 500);
            }

            return response()->json([
                'response' => trim($respuestaIA)
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'response' =>
                    'Ocurrió un error al procesar la consulta. Verifica que Ollama esté ejecutándose.'
            ], 500);
        }
    }
}