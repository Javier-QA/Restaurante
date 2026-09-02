<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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
        | NORMALIZAR MENSAJE
        |--------------------------------------------------------------------------
        | Permite reconocer mayúsculas, minúsculas y tildes.
        */

        $mensaje = strtolower($message);

        $mensaje = strtr($mensaje, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1. SALUDOS
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($mensaje, 'hola') ||
            str_contains($mensaje, 'buenos dias') ||
            str_contains($mensaje, 'buenas tardes') ||
            str_contains($mensaje, 'buenas noches')
        ) {
            return response()->json([
                'response' => 'Hola, administrador. ¿En qué puedo ayudarte?'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. VENTAS DE HOY
        |--------------------------------------------------------------------------
        | El dato viene directamente de MySQL.
        */

        if (
            str_contains($mensaje, 'vendimos hoy') ||
            str_contains($mensaje, 'ventas de hoy') ||
            str_contains($mensaje, 'venta de hoy') ||
            str_contains($mensaje, 'ingresos de hoy') ||
            str_contains($mensaje, 'ingreso de hoy') ||
            str_contains($mensaje, 'dinero de hoy') ||
            str_contains($mensaje, 'ventas del dia') ||
            str_contains($mensaje, 'cuanto vendimos') ||
            str_contains($mensaje, 'cuanto hemos vendido')
        ) {
            $totalVentas = DB::table('orders')
                ->whereDate('created_at', Carbon::today())
                ->sum('total');

            return response()->json([
                'response' =>
                    'Hoy el restaurante ha vendido S/ ' .
                    number_format($totalVentas, 2)
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. VENTAS DE ESTA SEMANA
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($mensaje, 'vendimos esta semana') ||
            str_contains($mensaje, 'ventas de esta semana') ||
            str_contains($mensaje, 'ventas esta semana') ||
            str_contains($mensaje, 'ingresos de esta semana') ||
            str_contains($mensaje, 'venta semanal') ||
            str_contains($mensaje, 'cuanto vendimos esta semana')
        ) {
            $inicioSemana = Carbon::now()->startOfWeek();
            $finSemana = Carbon::now()->endOfWeek();

            $totalSemana = DB::table('orders')
                ->whereBetween('created_at', [
                    $inicioSemana,
                    $finSemana
                ])
                ->sum('total');

            return response()->json([
                'response' =>
                    'Esta semana el restaurante ha vendido S/ ' .
                    number_format($totalSemana, 2)
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. PEDIDOS DE HOY
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($mensaje, 'cuantos pedidos') ||
            str_contains($mensaje, 'pedidos tenemos') ||
            str_contains($mensaje, 'pedidos de hoy') ||
            str_contains($mensaje, 'pedidos hoy')
        ) {
            $cantidadPedidos = DB::table('orders')
                ->whereDate('created_at', Carbon::today())
                ->count();

            return response()->json([
                'response' =>
                    'Hoy tenemos ' .
                    $cantidadPedidos .
                    ' pedido(s) registrado(s).'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. PRODUCTOS SIN STOCK
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($mensaje, 'sin stock') ||
            str_contains($mensaje, 'sin existencias') ||
            str_contains($mensaje, 'agotados') ||
            str_contains($mensaje, 'agotado')
        ) {
            $productos = DB::table('products')
                ->where('is_active', 1)
                ->whereNotNull('stock')
                ->where('stock', 0)
                ->orderBy('name', 'asc')
                ->get([
                    'name',
                    'price',
                    'stock'
                ]);

            if ($productos->isEmpty()) {
                return response()->json([
                    'response' =>
                        'Actualmente no hay productos registrados sin stock.'
                ]);
            }

            return response()->json([
                'response' =>
                    'Estos productos están actualmente sin stock:',
                'products' => $productos,
                'product_table_type' => 'sin_stock'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. PRODUCTOS CON POCO STOCK
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($mensaje, 'poco stock') ||
            str_contains($mensaje, 'stock bajo') ||
            str_contains($mensaje, 'se estan acabando') ||
            str_contains($mensaje, 'se acaban') ||
            str_contains($mensaje, 'poco inventario')
        ) {
            $productos = DB::table('products')
                ->where('is_active', 1)
                ->whereNotNull('stock')
                ->where('stock', '>', 0)
                ->where('stock', '<=', 5)
                ->orderBy('stock', 'asc')
                ->orderBy('name', 'asc')
                ->get([
                    'name',
                    'price',
                    'stock'
                ]);

            if ($productos->isEmpty()) {
                return response()->json([
                    'response' =>
                        'Actualmente no hay productos con stock bajo.'
                ]);
            }

            return response()->json([
                'response' =>
                    'Estos productos tienen poco stock:',
                'products' => $productos,
                'product_table_type' => 'poco_stock'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. PRODUCTO MÁS VENDIDO
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($mensaje, 'mas vendido') ||
            str_contains($mensaje, 'producto estrella') ||
            str_contains($mensaje, 'plato estrella') ||
            str_contains($mensaje, 'que se vende mas') ||
            str_contains($mensaje, 'que producto se vende mas')
        ) {
            $producto = DB::table('order_details')
                ->join(
                    'products',
                    'products.id',
                    '=',
                    'order_details.product_id'
                )
                ->select(
                    'products.name',
                    DB::raw(
                        'SUM(order_details.quantity) as total_vendido'
                    )
                )
                ->groupBy(
                    'products.id',
                    'products.name'
                )
                ->orderByDesc('total_vendido')
                ->first();

            if (!$producto) {
                return response()->json([
                    'response' =>
                        'Todavía no hay ventas registradas para determinar el producto más vendido.'
                ]);
            }

            return response()->json([
                'response' =>
                    "El producto más vendido es {$producto->name}, " .
                    "con {$producto->total_vendido} unidad(es) vendida(s)."
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 8. LISTA DE PRODUCTOS
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($mensaje, 'muestrame los productos') ||
            str_contains($mensaje, 'lista de productos') ||
            str_contains($mensaje, 'que productos tenemos') ||
            str_contains($mensaje, 'mostrar productos') ||
            str_contains($mensaje, 'ver productos') ||
            str_contains($mensaje, 'catalogo de productos') ||
            str_contains($mensaje, 'menu de productos')
        ) {
            $productos = DB::table('products')
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get([
                    'name',
                    'price',
                    'stock'
                ]);

            if ($productos->isEmpty()) {
                return response()->json([
                    'response' =>
                        'No hay productos registrados.'
                ]);
            }

            return response()->json([
                'response' =>
                    'Aquí tienes la lista de productos:',
                'products' => $productos,
                'product_table_type' => 'productos'
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

Si el administrador pregunta por cifras, ventas, pedidos,
stock o productos específicos y no se te proporcionó ese dato,
indica claramente que ese dato no está disponible.

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