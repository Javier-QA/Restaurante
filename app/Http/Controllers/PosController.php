<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Table;
use App\Services\Sunat\SunatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    public function index()
    {
        $areas = Area::with(['tables' => function ($q) {
            $q->with(['orders' => function ($q) {
                $q->where('status', 'pending');
            }, 'reservations' => function ($q) {
                $q->where('status', 'confirmed')
                    ->whereDate('reservation_time', Carbon::today())
                    ->where('reservation_time', '>=', Carbon::now()->subHours(2))
                    ->orderBy('reservation_time', 'asc');
            }]);
        }])->get();

        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';

        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');

        return view('pos.index', compact('areas', 'currency'));
    }

    public function order(Table $table)
    {
        // Filtro: Solo productos activos y vendibles
        $categories = Category::with(['products' => function ($q) {
            $q->where('is_active', true)
                ->where('is_saleable', true);
        }])->where('is_active', true)->get();

        $order = Order::where('table_id', $table->id)->where('status', 'pending')->with('details.product')->first();
        $occupiedTableIds = Order::where('status', 'pending')->whereNotNull('table_id')->pluck('table_id');
        $freeTables = Table::whereNotIn('id', $occupiedTableIds)->where('id', '!=', $table->id)->with('area')->get();
        $clients = Client::select('id', 'name', 'document_number')->orderBy('name')->get();
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';

        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');

        return view('pos.order', compact('table', 'categories', 'order', 'freeTables', 'clients', 'currency', 'yapeQr', 'plinQr'));
    }

    // --- AGREGAR POR CLIC (Normal) ---
    public function addToOrder(Request $request, Table $table)
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);
        $product = Product::where('is_active', true)->where('is_saleable', true)->findOrFail($request->product_id);
        $this->addItemToTable($table, $product);

        return $this->getCartHtml($table);
    }

    // --- AGREGAR POR CÓDIGO DE BARRAS (Nuevo) ---
    public function addByBarcode(Request $request, Table $table)
    {
        $request->validate(['barcode' => 'required']);

        $product = Product::where('barcode', $request->barcode)
            ->where('is_active', true)
            ->where('is_saleable', true)
            ->first();

        if (! $product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $this->addItemToTable($table, $product);

        // Devolvemos el HTML actualizado
        return $this->getCartHtml($table);
    }

    // Lógica auxiliar para no repetir código al agregar
    private function addItemToTable(Table $table, Product $product)
    {
        DB::transaction(function () use ($table, $product) {
            $register = CashRegister::where('status', 'open')->orderBy('id')->lockForUpdate()->first();
            if (! $register) {
                throw \Illuminate\Validation\ValidationException::withMessages(['cash_register' => 'Debe abrir una caja para registrar pedidos.']);
            }
            Table::whereKey($table->id)->lockForUpdate()->firstOrFail();
            $order = Order::firstOrCreate(
                ['table_id' => $table->id, 'status' => 'pending'],
                ['user_id' => auth()->id(), 'total' => 0, 'cash_register_id' => $register->id]
            );

            $detail = $order->details()
                ->where('product_id', $product->id)
                ->where('status', 'draft')
                ->first();

            if ($detail) {
                $detail->increment('quantity');
            } else {
                $order->details()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price,
                    'status' => 'draft',
                ]);
            }
            $this->recalculateTotal($order);
        });
    }

    // --- ACTUALIZAR CANTIDAD (Corregido para devolver HTML) ---
    public function updateQuantity(Request $request, OrderDetail $detail)
    {
        $order = $detail->order;
        $table = $order->table;

        if ($detail->status !== 'draft') {
            return response('El plato ya fue enviado a Cocina y no puede modificarse.', 422);
        }

        $request->validate(['quantity' => 'required|integer|min:0|max:9999']);
        $newQty = (int) $request->quantity;

        if ($newQty < 1) {
            $detail->delete();

            if (! $order->details()->exists()) {
                $order->delete();

                return $this->getCartHtml($table);
            }
        } else {
            $detail->update(['quantity' => $newQty]);
        }

        $this->recalculateTotal($order);

        return $this->getCartHtml($table);
    }

    // --- ACTUALIZAR NOTA ---
    public function updateNote(Request $request, OrderDetail $detail)
    {
        if ($detail->status !== 'draft') {
            return response('El plato ya fue enviado a Cocina y no puede modificarse.', 422);
        }

        $request->validate(['note' => 'nullable|string|max:500']);
        $detail->update(['note' => $request->note]);

        return $this->getCartHtml($detail->order->table);
    }

    // --- ELIMINAR ITEM ---
    public function removeItem(OrderDetail $detail)
    {
        $order = $detail->order;
        $table = $order->table;

        if ($detail->status !== 'draft') {
            return response('El plato ya fue enviado a Cocina y no puede eliminarse.', 422);
        }

        $detail->delete();

        if (! $order->details()->exists()) {
            $order->delete();

            return $this->getCartHtml($table);
        }

        $this->recalculateTotal($order);

        return $this->getCartHtml($table);
    }

    // --- CONFIRMAR PEDIDO Y ENVIAR A COCINA ---
    public function sendToKitchen(Order $order)
    {
        if ($order->status !== 'pending') {
            return response('El pedido ya está cerrado.', 422);
        }

        $draftDetails = $order->details()
            ->where('status', 'draft')
            ->get();

        if ($draftDetails->isEmpty()) {
            return response('No hay platos nuevos para enviar a Cocina.', 422);
        }

        $order->details()
            ->where('status', 'draft')
            ->update(['status' => 'pending']);

        return $this->getCartHtml($order->table);
    }

    // --- APLICAR DESCUENTO (Corregido para devolver HTML) ---
    public function applyDiscount(Request $request, Order $order)
    {
        abort_unless($order->status === 'pending', 422, 'El pedido ya está cerrado.');
        $gross = $order->details()->get()->sum(fn ($line) => $line->price * $line->quantity);
        $request->validate(['discount' => 'nullable|numeric|min:0|max:'.$gross, 'tip' => 'nullable|numeric|min:0']);
        $order->discount = $request->input('discount', 0);
        $order->tip = $request->input('tip', 0);
        $order->save();
        $this->recalculateTotal($order);

        return $this->getCartHtml($order->table);
    }

    public function moveTable(Request $request, Order $order)
    {
        $request->validate(['target_table_id' => 'required|exists:tables,id']);
        if (Order::where('table_id', $request->target_table_id)->where('status', 'pending')->exists()) {
            return redirect()->back()->with('error', 'Ocupada.');
        }
        $order->table_id = $request->target_table_id;
        $order->save();

        return redirect()->route('pos.order', $request->target_table_id)->with('success', 'Mesa movida correctamente.');
    }

    public function getSplitContent(Order $order)
    {
        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        $clients = Client::select('id', 'name', 'document_number')
            ->orderBy('name')
            ->get();

        return view(
            'pos.partials.split_content',
            compact('order', 'yapeQr', 'plinQr', 'currency', 'clients')
        );
    }

    public function processSplit(Request $request, Order $order)
    {
        $tableId = $order->table_id;
        $paid = app(\App\Services\OrderPaymentService::class)->pay($request, $order, true);
        $this->sendInvoice($paid);

        return redirect()->route('pos.order', ['table' => $tableId, 'print_order' => $paid->id])->with('success', 'Parte de la cuenta cobrada correctamente.')
            ->with('print_order_id', $paid->id);
    }

    public function precheck(Order $order)
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('sales.ticket', compact('order', 'settings'));
    }

    public function kitchenTicket(Order $order)
    {
        return view('sales.kitchen_ticket', compact('order'));
    }

    public function checkout(Request $request, Order $order)
    {
        $paid = app(\App\Services\OrderPaymentService::class)->pay($request, $order);
        $this->sendInvoice($paid);

        return redirect()->route('pos.index', ['print_order' => $paid->id])->with('success', 'Venta registrada correctamente.')
            ->with('print_order_id', $paid->id);
    }

    private function sendInvoice(Order $order): void
    {
        if (! $order->isInvoice()) {
            return;
        }
        try {
            (new SunatService)->sendInvoice($order->fresh('details.product'));
        } catch (\Throwable $e) {
            Log::error('Error al enviar a SUNAT', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Productos listos para recoger del mozo autenticado.
     */
    public function readyItems()
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->whereHas('details', function ($query) {
                $query->where('status', 'served');
            })
            ->with([
                'table',
                'details' => function ($query) {
                    $query
                        ->where('status', 'served')
                        ->with('product');
                },
            ])
            ->orderByDesc('updated_at')
            ->get();

        $items = [];

        foreach ($orders as $order) {

            foreach ($order->details as $detail) {

                $area = $detail->product?->preparation_area;

                if (! in_array($area, ['kitchen', 'barra'], true)) {
                    continue;
                }

                $items[] = [
                    'id' => $detail->id,
                    'order_id' => $order->id,

                    'table' => $order->table
                        ? $order->table->name
                        : 'Para Llevar',

                    'product' => $detail->product?->name
                        ?? 'Producto',

                    'quantity' => $detail->quantity,

                    'area' => $area,

                    'area_name' => $area === 'kitchen'
                        ? 'Cocina'
                        : 'Barra',
                ];
            }
        }

        return response()->json([
            'items' => $items,
        ]);
    }

    private function recalculateTotal(Order $order)
    {
        $subtotal = $order->details()->get()->sum(fn ($d) => $d->price * $d->quantity);
        $total = ($subtotal - ($order->discount ?? 0)) + ($order->tip ?? 0);
        $order->update(['total' => max(0, $total)]);
    }

    private function getCartHtml(Table $table)
    {
        $order = Order::where('table_id', $table->id)->where('status', 'pending')->with('details.product')->first();
        $clients = Client::select('id', 'name', 'document_number')->orderBy('name')->get();
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';

        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');

        return view('pos.partials.cart', compact('order', 'clients', 'currency', 'yapeQr', 'plinQr'))->render();
    }
}
