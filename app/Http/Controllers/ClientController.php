<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ClientController extends Controller
{
    public function index()
    {
        // Listamos clientes con conteo de órdenes
        $clients = Client::withCount('orders')->orderBy('name')->get();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'document_number' => 'nullable|unique:clients']);
        Client::create($request->all());
        return redirect()->route('clients.index')->with('success', 'Cliente registrado.');
    }

    // --- NUEVA FUNCIÓN: PERFIL 360 ---
    public function show(Client $client)
    {
        // 1. Historial de Órdenes (Completadas)
        $orders = $client->orders()
                         ->where('status', 'completed')
                         ->orderBy('created_at', 'desc')
                         ->get();

        // 2. Estadísticas Financieras
        $totalSpent = $orders->sum('total');
        $visitCount = $orders->count();
        $lastVisit = $orders->first() ? $orders->first()->created_at : null;

        // 3. Calcular Nivel VIP
        $rank = 'Nuevo';
        $badgeColor = 'secondary';
        if ($totalSpent > 1000) { $rank = 'Oro (VIP)'; $badgeColor = 'warning'; }
        elseif ($totalSpent > 500) { $rank = 'Plata'; $badgeColor = 'secondary'; }
        elseif ($totalSpent > 100) { $rank = 'Bronce'; $badgeColor = 'danger'; }

        // 4. Plato Favorito (Query avanzada)
        $favoriteDish = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as total_qty'))
            ->where('orders.client_id', $client->id)
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->first();

        $favoriteProduct = $favoriteDish ? $favoriteDish->name . ' (' . $favoriteDish->total_qty . ' veces)' : 'Aún sin datos';

        return view('clients.show', compact('client', 'orders', 'totalSpent', 'visitCount', 'lastVisit', 'rank', 'badgeColor', 'favoriteProduct'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate(['name' => 'required', 'document_number' => 'nullable|unique:clients,document_number,'.$client->id]);
        $client->update($request->all());
        return redirect()->route('clients.index')->with('success', 'Datos actualizados.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado.');
    }
    public function findByDocument(string $document)
    {
        $document = preg_replace('/\D/', '', $document);

        if (!in_array(strlen($document), [8, 11], true)) {
            return response()->json([
                'found' => false,
                'message' => 'Documento inválido.',
            ], 422);
        }

        // 1. Primero buscar en la base de datos local
        $client = Client::where('document_number', $document)
            ->first(['id', 'name', 'document_number']);

        if ($client) {
            return response()->json([
                'found' => true,
                'source' => 'local',
                'client' => $client,
            ]);
        }

        // 2. Si no existe, consultar Factiliza
        $token = config('services.factiliza.token');
        $baseUrl = rtrim(
            config('services.factiliza.base_url', 'https://api.factiliza.com'),
            '/'
        );

        if (!$token) {
            return response()->json([
                'found' => false,
                'message' => 'Factiliza no está configurado.',
            ], 503);
        }

        $endpoint = strlen($document) === 8
            ? "{$baseUrl}/v1/dni/info/{$document}"
            : "{$baseUrl}/v1/ruc/info/{$document}";

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get($endpoint);

            if (!$response->successful()) {
                return response()->json([
                    'found' => false,
                    'message' => 'No se pudo consultar el documento.',
                ], 502);
            }

            $json = $response->json();

            if (!($json['success'] ?? false) || empty($json['data'])) {
                return response()->json([
                    'found' => false,
                    'message' => $json['message'] ?? 'Documento no encontrado.',
                ], 404);
            }

            $data = $json['data'];

            $name = strlen($document) === 8
                ? ($data['nombre_completo'] ?? null)
                : ($data['nombre_o_razon_social'] ?? null);

            if (!$name) {
                return response()->json([
                    'found' => false,
                    'message' => 'La consulta no devolvió nombre.',
                ], 404);
            }

            // 3. Guardar para no volver a gastar consultas
            $client = Client::firstOrCreate(
                ['document_number' => $document],
                ['name' => $name]
            );

            return response()->json([
                'found' => true,
                'source' => 'factiliza',
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'document_number' => $client->document_number,
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'found' => false,
                'message' => 'Error al consultar Factiliza.',
            ], 502);
        }
    }
}