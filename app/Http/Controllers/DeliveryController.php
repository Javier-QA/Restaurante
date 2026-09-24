<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Delivery;
use App\Models\DeliveryDriver;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /* ══════════════════════════════════════════════
       INDEX — Panel Kanban
    ══════════════════════════════════════════════ */
    public function index()
    {
        $deliveries = Delivery::with(['order.details.product', 'driver', 'client'])
            ->today()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');

        $drivers  = DeliveryDriver::where('is_active', true)->orderBy('name')->get();
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');
        $statuses = Delivery::$statusLabels;

        // Contadores para las columnas del kanban
        $counts = [
            'pending'   => $deliveries->get('pending', collect())->count(),
            'preparing' => $deliveries->get('preparing', collect())->count(),
            'on_way'    => $deliveries->get('on_way', collect())->count(),
            'delivered' => $deliveries->get('delivered', collect())->count(),
        ];

        return view('delivery.index', compact('deliveries', 'drivers', 'currency', 'statuses', 'counts'));
    }

    /* ══════════════════════════════════════════════
       ORDERS — Actualización automática del Kanban
    ══════════════════════════════════════════════ */
    public function orders()
    {
        $deliveries = Delivery::with(['order.details.product', 'driver', 'client'])
            ->today()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');

        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');

        $statuses = ['pending', 'preparing', 'on_way', 'delivered'];

        $columns = [];
        $counts = [];

        foreach ($statuses as $status) {
            $items = $deliveries->get($status, collect());

            $columns[$status] = $items->map(function ($delivery) use ($currency) {
                return view('delivery.partials.card', compact('delivery', 'currency'))->render();
            })->implode('');

            $counts[$status] = $items->count();
        }

        return response()->json([
            'columns' => $columns,
            'counts' => $counts,
            'deliveries' => $deliveries->flatten(1)->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                ];
            })->values(),
        ]);
    }
    /* ══════════════════════════════════════════════
       CREATE — Formulario nuevo pedido
    ══════════════════════════════════════════════ */
    public function create()
    {
        $categories = Category::with(['products' => function ($q) {
            $q->where('is_active', true)->where('is_saleable', true);
        }])->where('is_active', true)->get();

        $clients  = Client::orderBy('name')->get();
        $drivers  = DeliveryDriver::where('is_active', true)->orderBy('name')->get();
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');

        return view('delivery.create', compact('categories', 'clients', 'drivers', 'currency'));
    }

    /* ══════════════════════════════════════════════
       STORE — Crear delivery + orden
    ══════════════════════════════════════════════ */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_type'  => 'required|in:delivery,pickup',
            'client_name'    => 'nullable|string|max:255',
            'client_phone'   => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'reference'      => 'nullable|string|max:255',
            'driver_id'      => 'nullable|exists:delivery_drivers,id',
            'delivery_fee'   => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,yape,plin',
            'products'       => 'required|array|min:1',
            'products.*.id'  => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Crear la Orden (sin mesa — delivery)
            $order = Order::create([
                'table_id'       => null,
                'user_id'        => Auth::id(),
                'client_id'      => $request->client_id ?: null,
                'client_name'    => $request->client_name,
                'status'         => 'pending',
                'total'          => 0,
                'cash_register_id' => Auth::user()->activeCashRegister->id ?? null,
            ]);

            // 2. Agregar productos a la orden
            $subtotal = 0;
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                $line    = $product->price * $item['qty'];
                $subtotal += $line;

                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['qty'],
                    'price'      => $product->price,
                    'status'     => 'pending',
                    'note'       => $item['note'] ?? null,
                ]);
            }

            $isPickup = $request->delivery_type === 'pickup';
            $deliveryFee = $isPickup
                ? 0
                : (float) ($request->delivery_fee ?? 0);

            $driverId = $isPickup
                ? null
                : ($request->driver_id ?: null);
            $order->update(['total' => $subtotal]);

            // 3. Crear el Delivery
            Delivery::create([
                'order_id'        => $order->id,
                'delivery_type'   => $request->delivery_type,
                'client_id'       => $request->client_id ?: null,
                'client_name'     => $request->client_name,
                'client_phone'    => $request->client_phone,
                'address'         => $request->address,
                'reference'       => $request->reference,
                'driver_id'       => $driverId,
                'user_id'         => Auth::id(),
                'cash_register_id'=> Auth::user()->activeCashRegister->id ?? null,
                'status'          => 'pending',
                'payment_method'  => $request->payment_method,
                'delivery_fee'    => $deliveryFee,
                'notes'           => $request->notes,
                'scheduled_at'    => $request->scheduled_at ?: null,
            ]);
        });

        return redirect()->route('delivery.index')->with('success', '¡Pedido creado correctamente!');
    }

    /* ══════════════════════════════════════════════
       SHOW — Detalle del pedido
    ══════════════════════════════════════════════ */
    public function show(Delivery $delivery)
    {
        $delivery->load(['order.details.product', 'driver', 'client', 'user']);
        $drivers  = DeliveryDriver::where('is_active', true)->orderBy('name')->get();
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        $yapeQr = Setting::where('key', 'yape_qr')->value('value');
        $plinQr = Setting::where('key', 'plin_qr')->value('value');
        $statuses = Delivery::$statusLabels;

        return view('delivery.show', compact('delivery', 'drivers', 'currency', 'statuses', 'yapeQr', 'plinQr'));
    }

    /* ══════════════════════════════════════════════
       UPDATE STATUS — AJAX
    ══════════════════════════════════════════════ */
    public function updateStatus(Request $request, Delivery $delivery)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,on_way',
        ]);

        $transitions = [
            'pending'   => 'preparing',
            'preparing' => 'on_way',
        ];

        $nextStatus = $transitions[$delivery->status] ?? null;

        if ($request->status !== $nextStatus) {
            return redirect()->route('delivery.show', $delivery)
                ->with('error', 'El cambio de estado solicitado no está permitido.');
        }

        if ($request->status === 'on_way') {
            $delivery->load('order.details');

            if (!$delivery->order) {
                return redirect()->route('delivery.show', $delivery)
                    ->with('error', 'No se encontró la orden asociada al delivery.');
            }

            $hasPendingItems = $delivery->order->details()
                ->whereIn('status', ['pending', 'cooking'])
                ->exists();

            if ($hasPendingItems) {
                return redirect()->route('delivery.show', $delivery)
                    ->with('error', 'El pedido todavía no está listo en cocina.');
            }
        }

        $delivery->update([
            'status' => $request->status,
        ]);

        return redirect()->route('delivery.show', $delivery)
            ->with('success', 'Estado actualizado a ' . Delivery::$statusLabels[$request->status] . '.');
    }
    /* ══════════════════════════════════════════════
       ASSIGN DRIVER — AJAX
    ══════════════════════════════════════════════ */
    public function assignDriver(Request $request, Delivery $delivery)
    {
        $request->validate(['driver_id' => 'nullable|exists:delivery_drivers,id']);
        $delivery->update(['driver_id' => $request->driver_id ?: null]);

        $driver = $delivery->driver;
        return response()->json([
            'success'     => true,
            'driver_name' => $driver ? $driver->name : 'Sin asignar',
        ]);
    }

    /* ══════════════════════════════════════════════
       CHECKOUT — Cobrar y cerrar
    ══════════════════════════════════════════════ */
    public function checkout(Request $request, Delivery $delivery)
    {
        if (in_array($delivery->status, ['delivered', 'cancelled'])) {
            return redirect()->route('delivery.show', $delivery)
                ->with('error', 'Este pedido ya fue cerrado.');
        }

        if ($delivery->status !== 'on_way') {
            return redirect()->route('delivery.show', $delivery)
                ->with('error', 'El pedido debe estar En camino antes de poder entregarlo y cobrarlo.');
        }

        $documentType = $request->input('document_type', 'Ticket');
        $clientDocument = trim((string) $request->input('client_document'));
        $clientName = trim((string) ($request->input('business_name') ?: $delivery->client_name));

        // Validación específica para Factura: RUC de 11 dígitos + razón social.
        if ($documentType === 'Factura') {
            $doc = preg_replace('/\D/', '', $clientDocument);

            if (strlen($doc) !== 11) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Para emitir Factura el cliente debe tener RUC de 11 dígitos.');
            }

            if ($clientName === '') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Para emitir Factura debe indicar la razón social del cliente.');
            }
        }

        // Validación específica para Boleta: DNI opcional, pero si se registra debe tener 8 dígitos.
        if ($documentType === 'Boleta' && $clientDocument !== '') {
            $doc = preg_replace('/\D/', '', $clientDocument);

            if (strlen($doc) !== 8) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Para emitir Boleta el DNI debe tener 8 dígitos.');
            }
        }

        // Vincular DNI/RUC consultado con la cartera de clientes.
        $clientId = $delivery->client_id;

        if ($clientDocument !== '') {
            $matchedClient = Client::where('document_number', $clientDocument)->first();

            if ($matchedClient) {
                $clientId = $matchedClient->id;
            }
        }

        $paymentMethod = $request->input('payment_method', $delivery->payment_method);
        $received = $paymentMethod === 'cash'
            ? (float) $request->input('received_amount', 0)
            : (float) $delivery->total_with_fee;

        if ($paymentMethod === 'cash' && $received < (float) $delivery->total_with_fee) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El monto recibido no puede ser menor al total.');

        }
        $change = max(
            0,
            $received - (float) $delivery->total_with_fee
        );

        $order = null;

        DB::transaction(function () use (
            $request,
            $delivery,
            $documentType,
            $clientDocument,
            $clientName,
            $clientId,
            $received,
            $change,
            &$order
        ) {
            $order = $delivery->order;

            if (!$order) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'order' => 'No se encontró la orden asociada al Delivery.'
                ]);
            }

            // 1. Calcular IGV igual que en POS.
            $config = new \App\Services\Sunat\SunatConfig();
            $igvFactor = $config->igvFactor();

            $isElectronic = in_array(
                $documentType,
                ['Boleta', 'Factura'],
                true
            );

            $totalBase = (float) $order->total;

            $totalGravada = 0;
            $igv = 0;

            if ($isElectronic) {
                $totalGravada = round(
                    $totalBase / (1 + $igvFactor),
                    2
                );

                $igv = round(
                    $totalBase - $totalGravada,
                    2
                );
            }

            // 2. Serie y correlativo.
            $serie = null;
            $correlativo = null;

            if ($isElectronic) {
                $tipo = $documentType === 'Factura'
                    ? 'factura'
                    : 'boleta';

                $next = \App\Models\DocumentSeries::next($tipo);

                $serie = $next['serie'];
                $correlativo = $next['correlativo'];
            }

            // 3. Calcular y validar stock.
            $stockRequirements = [];

            foreach ($order->details as $detail) {
                $product = Product::with('ingredients')
                    ->findOrFail($detail->product_id);

                if ($product->ingredients->count() > 0) {
                    foreach ($product->ingredients as $ingredient) {

                        $required =
                            (float) $ingredient->pivot->quantity *
                            (float) $detail->quantity;

                        if (!isset($stockRequirements[$ingredient->id])) {
                            $stockRequirements[$ingredient->id] = 0;
                        }

                        $stockRequirements[$ingredient->id] += $required;
                    }
                } elseif (
                    $product->controls_stock &&
                    !is_null($product->stock)
                ) {
                    if (!isset($stockRequirements[$product->id])) {
                        $stockRequirements[$product->id] = 0;
                    }

                    $stockRequirements[$product->id] +=
                        (float) $detail->quantity;
                }
            }

            foreach ($stockRequirements as $productId => $required) {

                $stockItem = Product::whereKey($productId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $available = (float) $stockItem->stock;

                if ($available < $required) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'stock' =>
                            'Stock insuficiente de ' .
                            $stockItem->name .
                            '. Disponible: ' .
                            $available .
                            '. Necesario: ' .
                            $required .
                            '.',
                    ]);
                }
            }

            // 4. Completar orden.
            $order->update([
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'received_amount' => $received,
                'change_amount' => $change,

                'document_type' => $documentType,
                'client_id' => $clientId,
                'client_name' => $clientName,
                'client_document' => $clientDocument ?: null,

                // SUNAT
                'serie' => $serie,
                'correlativo' => $correlativo,
                'subtotal' => $totalGravada,
                'igv' => $igv,
                'total_gravada' => $totalGravada,
                'sunat_status' => $isElectronic
                    ? 'PENDING'
                    : 'NOT_APPLICABLE',

                'cash_register_id' =>
                    Auth::user()->activeCashRegister->id ?? null,
            ]);

            // Sincronizar los datos del cliente con Delivery.
            $delivery->update([
                'client_id' => $clientId,
                'client_name' => $clientName !== '' ? $clientName : null,
            ]);

            // 5. Descontar stock.
            foreach ($order->details as $detail) {

                $product = $detail->product;
                $ingredients = $product->ingredients;

                if ($ingredients->count() > 0) {

                    foreach ($ingredients as $ingredient) {

                        $qty =
                            $ingredient->pivot->quantity *
                            $detail->quantity;

                        $oldStock = $ingredient->stock;

                        $ingredient->decrement('stock', $qty);

                        InventoryLog::create([
                            'product_id' => $ingredient->id,
                            'user_id' => Auth::id(),
                            'type' => 'sale',
                            'quantity' => -$qty,
                            'old_stock' => $oldStock,
                            'new_stock' => $oldStock - $qty,
                            'note' => 'Delivery #' . $delivery->id,
                        ]);
                    }

                } elseif (
                    $product->controls_stock &&
                    !is_null($product->stock)
                ) {

                    $oldStock = $product->stock;

                    $product->decrement(
                        'stock',
                        $detail->quantity
                    );

                    InventoryLog::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'type' => 'sale',
                        'quantity' => -$detail->quantity,
                        'old_stock' => $oldStock,
                        'new_stock' =>
                            $oldStock - $detail->quantity,
                        'note' => 'Delivery #' . $delivery->id,
                    ]);
                }
            }

            // 6. Marcar Delivery como entregado.
            $delivery->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        });

        // 7. Enviar comprobante electrónico a SUNAT.
        if ($order && $order->isElectronic()) {

            try {

                (new \App\Services\Sunat\SunatService())
                    ->sendInvoice(
                        $order->fresh('details.product')
                    );

                $order->refresh();

            } catch (\Throwable $e) {

                \Illuminate\Support\Facades\Log::error(
                    'Error al enviar Delivery a SUNAT',
                    [
                        'order_id' => $order->id,
                        'delivery_id' => $delivery->id,
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

        $message = 'Pedido entregado y cobrado correctamente.';

        if ($order && $order->isElectronic()) {

            $order->refresh();

            $message .=
                ' Comprobante ' .
                ($order->full_number ?? '') .
                ' - ' .
                ($order->sunat_description ??
                 $order->sunat_status);
        }

        return redirect()
            ->route('delivery.index')
            ->with('success', $message);
    }
    /* ══════════════════════════════════════════════
       CANCEL — Cancelar
    ══════════════════════════════════════════════ */
    public function cancel(Delivery $delivery)
    {
        if (in_array($delivery->status, ['delivered', 'cancelled'])) {
            return redirect()->back()->with('error', 'No se puede cancelar este pedido.');
        }

        $delivery->update(['status' => 'cancelled']);
        $delivery->order->update(['status' => 'cancelled']);

        return redirect()->route('delivery.index')->with('success', 'Pedido cancelado.');
    }

    /* ══════════════════════════════════════════════
       DRIVERS — Gestión rápida de repartidores
    ══════════════════════════════════════════════ */
    public function driversIndex()
    {
        $drivers = DeliveryDriver::withCount('deliveries')->orderBy('name')->get();
        return view('delivery.drivers', compact('drivers'));
    }

    public function driversStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:30']);
        DeliveryDriver::create($request->only('name', 'phone') + ['is_active' => true]);
        return redirect()->back()->with('success', 'Repartidor agregado.');
    }

    public function driversUpdate(Request $request, DeliveryDriver $driver)
    {
        $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:30']);
        $driver->update($request->only('name', 'phone', 'is_active'));
        return redirect()->back()->with('success', 'Repartidor actualizado.');
    }

    public function driversDestroy(DeliveryDriver $driver)
    {
        $driver->delete();
        return redirect()->back()->with('success', 'Repartidor eliminado.');
    }
}

