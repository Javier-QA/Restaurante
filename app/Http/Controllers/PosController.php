<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\InventoryLog;
use App\Models\Client;
use App\Models\Setting;
use App\Models\DocumentSeries;
use App\Services\Sunat\SunatConfig;
use App\Services\Sunat\SunatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        $areas = Area::with(['tables' => function($q) {
            $q->with(['orders' => function($q) {
                $q->where('status', 'pending');
            }, 'reservations' => function($q) {
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
        $categories = Category::with(['products' => function($q) {
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
        $product = Product::findOrFail($request->product_id);
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

        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $this->addItemToTable($table, $product);
        
        // Devolvemos el HTML actualizado
        return $this->getCartHtml($table);
    }

    // Lógica auxiliar para no repetir código al agregar
    private function addItemToTable(Table $table, Product $product)
    {
        DB::transaction(function() use ($table, $product) {
            $order = Order::firstOrCreate(
                ['table_id' => $table->id, 'status' => 'pending'], 
                ['user_id' => auth()->id() ?? 1, 'total' => 0]
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
                    'status' => 'draft'
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

        $newQty = (int) $request->quantity;

        if ($newQty < 1) {
            $detail->delete();

            if (!$order->details()->exists()) {
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

        if (!$order->details()->exists()) {
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
        $order->discount = $request->input('discount', 0); 
        $order->tip = $request->input('tip', 0); 
        $order->save(); 
        $this->recalculateTotal($order); 
        return $this->getCartHtml($order->table); 
    }
    
    public function moveTable(Request $request, Order $order) {
        $request->validate(['target_table_id' => 'required|exists:tables,id']);
        if (Order::where('table_id', $request->target_table_id)->where('status', 'pending')->exists()) return redirect()->back()->with('error', 'Ocupada.');
        $order->table_id = $request->target_table_id; $order->save();
        return redirect()->route('pos.order', $request->target_table_id);
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
    if ($order->status !== 'pending') {
        return redirect()
            ->route('pos.index')
            ->with('error', 'La orden ya está cerrada.');
    }

    if ($order->details()->whereIn('status', ['draft', 'pending'])->exists()) {
        return redirect()
            ->route('pos.order', $order->table_id)
            ->with('error', 'Cocina debe iniciar la preparación antes de dividir la cuenta.');
    }

    $request->validate([
        'selected_items' => 'required|array|min:1',
        'selected_items.*' => 'integer|exists:order_details,id',
        'split_quantities' => 'required|array',
        'split_quantities.*' => 'nullable|integer|min:0',
        'payment_method' => 'required|in:cash,card,yape,plin',
    ]);

    $selectedIds = $request->input('selected_items', []);
    $splitQuantities = $request->input('split_quantities', []);
    $paymentMethod = $request->input('payment_method', 'cash');

    $documentType = $request->input('document_type', 'Ticket');
    $clientName = trim((string) $request->input('client_name', ''));
    $clientDocument = trim((string) $request->input('client_document', ''));

    $splitClientId = $order->client_id;

    if ($clientDocument !== '') {
        $matchedClient = Client::where('document_number', $clientDocument)->first();

        if ($matchedClient) {
            $splitClientId = $matchedClient->id;
        }
    }

    $receivedAmount = $paymentMethod === 'cash'
        ? (float) $request->input('received_amount', 0)
        : null;

    if ($paymentMethod === 'cash' && $receivedAmount < 0) {
        return redirect()
            ->back()
            ->with('error', 'El monto recibido no puede ser negativo.');
    }

    $selectedDetailsForValidation = OrderDetail::where('order_id', $order->id)
        ->whereIn('id', $selectedIds)
        ->get();

    $splitTotalForValidation = $selectedDetailsForValidation->sum(function ($detail) use ($splitQuantities) {
        $selectedQty = (int) ($splitQuantities[$detail->id] ?? 0);

        if ($selectedQty < 1 || $selectedQty > $detail->quantity) {
            return 0;
        }

        return $detail->price * $selectedQty;
    });

    if ($paymentMethod === 'cash' && $receivedAmount < $splitTotalForValidation) {
        return redirect()
            ->back()
            ->with('error', 'El monto recibido es menor al total a cobrar.');
    }

    if ($documentType === 'Factura') {
        $doc = preg_replace('/\D/', '', $clientDocument);

        if (strlen($doc) !== 11) {
            return redirect()
                ->back()
                ->with('error', 'Para emitir Factura debe ingresar un RUC de 11 dígitos.');
        }

        if ($clientName === '') {
            return redirect()
                ->back()
                ->with('error', 'Para emitir Factura debe indicar la razón social.');
        }
    }

    if ($documentType === 'Boleta' && $clientDocument !== '') {
        $doc = preg_replace('/\D/', '', $clientDocument);

        if (strlen($doc) !== 8) {
            return redirect()
                ->back()
                ->with('error', 'Para Boleta el DNI debe tener 8 dígitos.');
        }
    }
    $splitOrder = null;

    DB::transaction(function () use (
        $order,
        $selectedIds,
        $splitQuantities,
        $paymentMethod,
        $receivedAmount,
        $documentType,
        $clientName,
        $clientDocument,
        $splitClientId,
        &$splitOrder
    ) {

        $selectedDetails = OrderDetail::where('order_id', $order->id)
            ->whereIn('id', $selectedIds)
            ->get();

        if ($selectedDetails->isEmpty()) {
            throw new \Exception('No se seleccionaron productos válidos.');
        }

        $splitTotal = $selectedDetails->sum(function ($detail) use ($splitQuantities) {
            $selectedQty = (int) ($splitQuantities[$detail->id] ?? 0);

            if ($selectedQty < 1 || $selectedQty > $detail->quantity) {
                throw new \Exception('La cantidad seleccionada no es válida.');
            }

            return $detail->price * $selectedQty;
        });

        // Configuración SUNAT para esta parte de la cuenta
        $config = new SunatConfig();
        $igvFactor = $config->igvFactor();

        $isElectronic = in_array(
            $documentType,
            ['Boleta', 'Factura'],
            true
        );

        $totalGravada = 0;
        $igv = 0;

        if ($isElectronic) {
            $totalGravada = round(
                (float) $splitTotal / (1 + $igvFactor),
                2
            );

            $igv = round(
                (float) $splitTotal - $totalGravada,
                2
            );
        }

        $serie = null;
        $correlativo = null;

        if ($isElectronic) {
            $tipo = $documentType === 'Factura'
                ? 'factura'
                : 'boleta';

            $next = DocumentSeries::next($tipo);

            $serie = $next['serie'];
            $correlativo = $next['correlativo'];
        }

        $splitOrder = Order::create([
            'table_id' => $order->table_id,
            'user_id' => $order->user_id,
            'client_id' => $splitClientId,

            'status' => 'completed',
            'total' => $splitTotal,

            'payment_method' => $paymentMethod,

            'received_amount' => $paymentMethod === 'cash'
                ? $receivedAmount
                : $splitTotal,

            'change_amount' => $paymentMethod === 'cash'
                ? max(0, $receivedAmount - $splitTotal)
                : 0,

            'document_type' => $documentType,

            'client_name' => $clientName !== ''
                ? $clientName
                : ($order->client_name ?? 'Público'),

            'client_document' => $clientDocument !== ''
                ? $clientDocument
                : $order->client_document,

            'discount' => 0,
            'tip' => 0,

            'cash_register_id' =>
                Auth::user()->activeCashRegister->id ?? null,

            'serie' => $serie,
            'correlativo' => $correlativo,

            'subtotal' => $totalGravada,
            'igv' => $igv,
            'total_gravada' => $totalGravada,

            'sunat_status' => $isElectronic
                ? 'PENDING'
                : 'NOT_APPLICABLE',
        ]);

        foreach ($selectedDetails as $detail) {
            $selectedQty = (int) ($splitQuantities[$detail->id] ?? 0);

            if ($selectedQty < 1 || $selectedQty > $detail->quantity) {
                throw new \Exception('La cantidad seleccionada no es válida.');
            }

            // Si se cobran todas las unidades, mover la línea completa.
            if ($selectedQty === (int) $detail->quantity) {
                $detail->order_id = $splitOrder->id;
                $detail->save();
                continue;
            }

            // Si se cobra solo una parte, reducir la cantidad
            // que permanece en la mesa.
            $remainingQty = (int) $detail->quantity - $selectedQty;

            $detail->quantity = $remainingQty;
            $detail->save();

            // Crear en la cuenta cobrada únicamente las unidades seleccionadas.
            OrderDetail::create([
                'order_id' => $splitOrder->id,
                'product_id' => $detail->product_id,
                'quantity' => $selectedQty,
                'price' => $detail->price,
                'status' => $detail->status,
                'note' => $detail->note,
            ]);
        }

        $remainingTotal = OrderDetail::where('order_id', $order->id)
            ->get()
            ->sum(function ($detail) {
                return $detail->price * $detail->quantity;
            });

        if ($remainingTotal <= 0) {
            $order->delete();
        } else {
            $order->total = $remainingTotal;
            $order->save();
        }
    });

    $message = 'Parte de la cuenta cobrada correctamente.';

    if ($splitOrder && $splitOrder->isElectronic()) {

        try {

            (new SunatService())->sendInvoice(
                $splitOrder->fresh('details.product')
            );

        } catch (\Throwable $e) {

            Log::error(
                'Error al enviar comprobante dividido a SUNAT',
                [
                    'order_id' => $splitOrder->id,
                    'error' => $e->getMessage(),
                ]
            );
        }

        $splitOrder->refresh();

        $message .= ' Comprobante '
            . ($splitOrder->full_number ?? '')
            . ' - '
            . ($splitOrder->sunat_description
                ?? $splitOrder->sunat_status);
    }

    return redirect()
        ->route('pos.order', $order->table_id)
        ->with('success', $message);
}
    public function precheck(Order $order) { $settings = Setting::pluck('value', 'key')->toArray(); return view('sales.ticket', compact('order', 'settings')); }
    public function kitchenTicket(Order $order) { return view('sales.kitchen_ticket', compact('order')); }

    public function checkout(Request $request, Order $order)
    {
        if($order->status !== 'pending') return redirect()->route('pos.index')->with('error', 'Orden cerrada.');

        if ($order->details()->whereIn('status', ['draft', 'pending'])->exists()) {
            return redirect()
                ->route('pos.order', $order->table_id)
                ->with('error', 'Cocina debe iniciar la preparación antes de cobrar.');
        }

        $method = $request->input('payment_method', 'cash');
        $received = $method === 'cash' ? $request->input('received_amount') : $order->total;
        $change = max(0, $received - $order->total);
        $clientId = $request->input('client_id');
        $clientName = $clientId ? Client::find($clientId)->name : ($request->input('client_name') ?? 'Público');
        $documentType = $request->input('document_type', 'Ticket');
        $clientDocument = $request->input('client_document');

        // Validación específica para Factura (Perú): requiere RUC (11 dígitos) y razón social
        if ($documentType === 'Factura') {
            $doc = preg_replace('/\D/', '', (string) $clientDocument);
            if (strlen($doc) !== 11) {
                return redirect()->back()->with('error', 'Para emitir Factura el cliente debe tener RUC de 11 dígitos.');
            }
            if (empty(trim((string) $clientName)) || $clientName === 'Público') {
                return redirect()->back()->with('error', 'Para emitir Factura debe indicar la razón social del cliente.');
            }
        }

        DB::transaction(function() use ($order, $method, $received, $change, $request, $clientId, $clientName, $documentType, $clientDocument) {
            // 1. Calcular IGV (Perú 18%) si es comprobante electrónico
            $config = new SunatConfig();
            $igvFactor = $config->igvFactor();
            $isElectronic = in_array($documentType, ['Boleta', 'Factura'], true);

            $totalGravada = 0;
            $igv = 0;
            if ($isElectronic) {
                $totalGravada = round((float) $order->total / (1 + $igvFactor), 2);
                $igv = round((float) $order->total - $totalGravada, 2);
            }

            // 2. Asignar serie y correlativo si es electrónico
            $serie = null;
            $correlativo = null;
            if ($isElectronic) {
                $tipo = $documentType === 'Factura' ? 'factura' : 'boleta';
                $next = DocumentSeries::next($tipo);
                $serie = $next['serie'];
                $correlativo = $next['correlativo'];
            }

            // Calcular el consumo total de stock de todo el pedido.
            $stockRequirements = [];

            foreach ($order->details as $detail) {
                $product = Product::with('ingredients')->findOrFail($detail->product_id);

                if ($product->ingredients->count() > 0) {
                    foreach ($product->ingredients as $ingredient) {
                        $required = (float) $ingredient->pivot->quantity
                            * (float) $detail->quantity;

                        if (!isset($stockRequirements[$ingredient->id])) {
                            $stockRequirements[$ingredient->id] = 0;
                        }

                        $stockRequirements[$ingredient->id] += $required;
                    }
                } elseif ($product->controls_stock && !is_null($product->stock)) {
                    if (!isset($stockRequirements[$product->id])) {
                        $stockRequirements[$product->id] = 0;
                    }

                    $stockRequirements[$product->id] += (float) $detail->quantity;
                }
            }

            // Bloquear y validar el stock acumulado antes de completar la venta.
            foreach ($stockRequirements as $productId => $required) {
                $stockItem = Product::whereKey($productId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $available = (float) $stockItem->stock;

                if ($available < $required) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'stock' => 'Stock insuficiente de ' . $stockItem->name .
                            '. Disponible: ' . $available .
                            '. Necesario: ' . $required . '.',
                    ]);
                }
            }
            $order->update([
                'status' => 'completed',
                'payment_method' => $method,
                'received_amount' => $received,
                'change_amount' => $change,
                'document_type' => $documentType,
                'client_id' => $clientId,
                'client_name' => $clientName,
                'client_document' => $clientDocument,
                'cash_register_id' => Auth::user()->activeCashRegister->id ?? null,

                // SUNAT
                'serie' => $serie,
                'correlativo' => $correlativo,
                'subtotal' => $totalGravada,
                'igv' => $igv,
                'total_gravada' => $totalGravada,
                'sunat_status' => $isElectronic ? 'PENDING' : 'NOT_APPLICABLE',
            ]);

            foreach($order->details as $detail) {
                $product = $detail->product;
                $ingredients = $product->ingredients;

                if ($ingredients->count() > 0) {
                    foreach ($ingredients as $ingredient) {
                        $qtyToDeduct = $ingredient->pivot->quantity * $detail->quantity;
                        $oldStock = $ingredient->stock;
                        $ingredient->decrement('stock', $qtyToDeduct);
                        InventoryLog::create([
                            'product_id' => $ingredient->id,
                            'user_id' => Auth::id(),
                            'type' => 'sale',
                            'quantity' => -$qtyToDeduct,
                            'old_stock' => $oldStock,
                            'new_stock' => $oldStock - $qtyToDeduct,
                            'note' => 'Venta: ' . $product->name . ' (Orden #' . $order->id . ')'
                        ]);
                    }
                } else {
                    if ($product->controls_stock && !is_null($product->stock)) {
                        $oldStock = $product->stock;
                        $product->decrement('stock', $detail->quantity);
                        InventoryLog::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'type' => 'sale',
                            'quantity' => -($detail->quantity),
                            'old_stock' => $oldStock,
                            'new_stock' => $oldStock - $detail->quantity,
                            'note' => 'Venta POS #' . $order->id
                        ]);
                    }
                }
            }
        });

        // 3. Envío a SUNAT (no bloqueante: si falla queda en ERROR y puede reintentarse desde Billing)
        if ($order->isElectronic()) {
            try {
                (new SunatService())->sendInvoice($order->fresh('details.product'));
            } catch (\Throwable $e) {
                Log::error('Error al enviar a SUNAT', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                // No interrumpimos la venta; queda PENDING/ERROR para reintento manual.
            }
        }

        $msg = 'Venta registrada.';
        if ($order->isElectronic()) {
            $order->refresh();
            $msg .= ' Comprobante ' . $order->full_number . ' - ' . ($order->sunat_description ?? $order->sunat_status);
        }

        return redirect()->route('pos.index')->with('success', $msg);
    }

    private function recalculateTotal(Order $order)
    {
        $subtotal = $order->details->sum(fn($d) => $d->price * $d->quantity);
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
        return view('pos.partials.cart', compact('order', 'clients', 'currency'))->render();
    }
}