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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        $areas = Area::with([
            'tables' => function ($q) {
                $q->with([
                    'orders' => function ($q) {
                        $q->where('status', 'pending');
                    },

                    'reservations' => function ($q) {
                        $q->where('status', 'confirmed')
                            ->whereDate(
                                'reservation_time',
                                Carbon::today()
                            )
                            ->where(
                                'reservation_time',
                                '>=',
                                Carbon::now()->subHours(2)
                            )
                            ->orderBy(
                                'reservation_time',
                                'asc'
                            );
                    }
                ]);
            }
        ])->get();

        $currency = Setting::where(
            'key',
            'currency_symbol'
        )->value('value') ?? 'S/';

        return view(
            'pos.index',
            compact(
                'areas',
                'currency'
            )
        );
    }


    public function order(Table $table)
    {
        // =========================================================
        // PRODUCTOS
        // =========================================================

        $categories = Category::with([
            'products' => function ($q) {
                $q->where(
                    'is_active',
                    true
                )->where(
                    'is_saleable',
                    true
                );
            }
        ])->where(
            'is_active',
            true
        )->get();


        // =========================================================
        // ORDEN ACTUAL
        // =========================================================

        $order = Order::where(
            'table_id',
            $table->id
        )
            ->where(
                'status',
                'pending'
            )
            ->with(
                'details.product'
            )
            ->first();


        // =========================================================
        // MESAS LIBRES
        // =========================================================

        $occupiedTableIds = Order::where(
            'status',
            'pending'
        )->pluck(
            'table_id'
        );

        $freeTables = Table::whereNotIn(
            'id',
            $occupiedTableIds
        )
            ->where(
                'id',
                '!=',
                $table->id
            )
            ->with(
                'area'
            )
            ->get();


        // =========================================================
        // CLIENTES
        // =========================================================

        $clients = Client::select(
            'id',
            'name',
            'document_number'
        )
            ->orderBy(
                'name'
            )
            ->get();


        // =========================================================
        // CONFIGURACIÓN
        // =========================================================

        $settings = Setting::pluck(
            'value',
            'key'
        )->toArray();


        // =========================================================
        // MONEDA
        // =========================================================

        $currency = $settings['currency_symbol'] ?? 'S/';


        // =========================================================
        // POS
        // =========================================================

        return view(
            'pos.order',
            compact(
                'table',
                'categories',
                'order',
                'freeTables',
                'clients',
                'currency',
                'settings'
            )
        );
    }


    // =============================================================
    // AGREGAR PRODUCTO
    // =============================================================

    public function addToOrder(
        Request $request,
        Table $table
    ) {
        $product = Product::findOrFail(
            $request->product_id
        );

        $this->addItemToTable(
            $table,
            $product
        );

        return $this->getCartHtml(
            $table
        );
    }


    // =============================================================
    // LÓGICA PARA AGREGAR PRODUCTO
    // =============================================================

    private function addItemToTable(
        Table $table,
        Product $product
    ) {
        DB::transaction(
            function () use (
                $table,
                $product
            ) {

                $order = Order::firstOrCreate(
                    [
                        'table_id' => $table->id,
                        'status' => 'pending'
                    ],
                    [
                        'user_id' => auth()->id() ?? 1,
                        'total' => 0
                    ]
                );


                $detail = $order->details()
                    ->where(
                        'product_id',
                        $product->id
                    )
                    ->first();


                if ($detail) {

                    $detail->increment(
                        'quantity'
                    );

                } else {

                    $order->details()->create([
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'price' => $product->price,
                        'status' => 'pending'
                    ]);
                }


                $this->recalculateTotal(
                    $order
                );
            }
        );
    }


    // =============================================================
    // ACTUALIZAR CANTIDAD
    // =============================================================

    public function updateQuantity(
        Request $request,
        OrderDetail $detail
    ) {
        $newQty = $request->quantity;

        $order = $detail->order;


        if ($newQty < 1) {

            $detail->delete();

        } else {

            $detail->update([
                'quantity' => $newQty
            ]);
        }


        $this->recalculateTotal(
            $order
        );


        return $this->getCartHtml(
            $order->table
        );
    }


    // =============================================================
    // ACTUALIZAR NOTA
    // =============================================================

    public function updateNote(
        Request $request,
        OrderDetail $detail
    ) {
        $detail->update([
            'note' => $request->note
        ]);

        return $this->getCartHtml(
            $detail->order->table
        );
    }


    // =============================================================
    // ELIMINAR ITEM
    // =============================================================

    public function removeItem(
        OrderDetail $detail
    ) {
        $order = $detail->order;

        $detail->delete();

        $this->recalculateTotal(
            $order
        );

        return $this->getCartHtml(
            $order->table
        );
    }


    // =============================================================
    // DESCUENTO Y PROPINA
    // =============================================================

    public function applyDiscount(
        Request $request,
        Order $order
    ) {
        $order->discount =
            $request->input(
                'discount',
                0
            );

        $order->tip =
            $request->input(
                'tip',
                0
            );

        $order->save();

        $this->recalculateTotal(
            $order
        );

        return $this->getCartHtml(
            $order->table
        );
    }


    // =============================================================
    // MOVER MESA
    // =============================================================

    public function moveTable(
        Request $request,
        Order $order
    ) {
        $request->validate([
            'target_table_id' =>
                'required|exists:tables,id'
        ]);


        if (
            Order::where(
                'table_id',
                $request->target_table_id
            )
            ->where(
                'status',
                'pending'
            )
            ->exists()
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'La mesa seleccionada está ocupada.'
                );
        }


        $order->table_id =
            $request->target_table_id;

        $order->save();


        return redirect()->route(
            'pos.order',
            $request->target_table_id
        );
    }


    // =============================================================
    // DIVIDIR CUENTA
    // =============================================================

    public function getSplitContent(
        Order $order
    ) {
        return view(
            'pos.partials.split_content',
            compact('order')
        );
    }


    public function processSplit(
        Request $request,
        Order $order
    ) {
        return redirect()->back();
    }


    // =============================================================
    // PRECHECK
    // =============================================================

    public function precheck(
        Order $order
    ) {
        $settings = Setting::pluck(
            'value',
            'key'
        )->toArray();

        return view(
            'sales.ticket',
            compact(
                'order',
                'settings'
            )
        );
    }


    // =============================================================
    // TICKET COCINA
    // =============================================================

    public function kitchenTicket(
        Order $order
    ) {
        return view(
            'sales.kitchen_ticket',
            compact('order')
        );
    }


    // =============================================================
    // COBRAR ORDEN
    // =============================================================

    public function checkout(
        Request $request,
        Order $order
    ) {

        // ---------------------------------------------------------
        // VERIFICAR QUE LA ORDEN SIGA PENDIENTE
        // ---------------------------------------------------------

        if (
            $order->status !== 'pending'
        ) {

            return redirect()
                ->route('pos.index')
                ->with(
                    'error',
                    'La orden ya fue cerrada.'
                );
        }


        // ---------------------------------------------------------
        // MÉTODO DE PAGO
        // ---------------------------------------------------------

        $method = $request->input(
            'payment_method',
            'cash'
        );


        // ---------------------------------------------------------
        // MONTO RECIBIDO
        // ---------------------------------------------------------

        $received =
            $method === 'cash'
                ? $request->input(
                    'received_amount'
                )
                : $order->total;


        // ---------------------------------------------------------
        // VALIDAR MONTO RECIBIDO
        // ---------------------------------------------------------

        if (
            $method === 'cash' &&
            (
                is_null($received) ||
                $received < $order->total
            )
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'El monto recibido es insuficiente para completar el pago.'
                );
        }


        // ---------------------------------------------------------
        // CALCULAR CAMBIO
        // ---------------------------------------------------------

        $change = max(
            0,
            $received - $order->total
        );


        // ---------------------------------------------------------
        // CLIENTE
        // ---------------------------------------------------------

        $clientId =
            $request->input(
                'client_id'
            );


        $client = $clientId
            ? Client::find($clientId)
            : null;


        $clientName = $client
            ? $client->name
            : (
                $request->input(
                    'client_name'
                ) ?? 'Público'
            );


        // ---------------------------------------------------------
        // GUARDAR VENTA
        // ---------------------------------------------------------

        DB::transaction(
            function () use (
                $order,
                $method,
                $received,
                $change,
                $request,
                $clientId,
                $clientName
            ) {

                $order->update([

                    'status' =>
                        'completed',

                    'payment_method' =>
                        $method,

                    'received_amount' =>
                        $received,

                    'change_amount' =>
                        $change,

                    'document_type' =>
                        $request->input(
                            'document_type',
                            'Ticket'
                        ),

                    'client_id' =>
                        $clientId,

                    'client_name' =>
                        $clientName,

                    'client_document' =>
                        $request->input(
                            'client_document'
                        )
                ]);


                // -------------------------------------------------
                // DESCONTAR STOCK
                // -------------------------------------------------

                foreach (
                    $order->details as $detail
                ) {

                    $product =
                        $detail->product;

                    $ingredients =
                        $product->ingredients;


                    // -------------------------------------------------
                    // PRODUCTO CON INGREDIENTES
                    // -------------------------------------------------

                    if (
                        $ingredients->count() > 0
                    ) {

                        foreach (
                            $ingredients
                            as $ingredient
                        ) {

                            $qtyToDeduct =
                                $ingredient
                                    ->pivot
                                    ->quantity
                                *
                                $detail->quantity;


                            $oldStock =
                                $ingredient->stock;


                            $ingredient->decrement(
                                'stock',
                                $qtyToDeduct
                            );


                            InventoryLog::create([

                                'product_id' =>
                                    $ingredient->id,

                                'user_id' =>
                                    Auth::id(),

                                'type' =>
                                    'sale',

                                'quantity' =>
                                    -$qtyToDeduct,

                                'old_stock' =>
                                    $oldStock,

                                'new_stock' =>
                                    $oldStock -
                                    $qtyToDeduct,

                                'note' =>
                                    'Venta: '
                                    .
                                    $product->name
                                    .
                                    ' (Orden #'
                                    .
                                    $order->id
                                    .
                                    ')'
                            ]);
                        }


                    // -------------------------------------------------
                    // PRODUCTO SIN INGREDIENTES
                    // -------------------------------------------------

                    } else {

                        if (
                            !is_null(
                                $product->stock
                            )
                        ) {

                            $oldStock =
                                $product->stock;


                            $product->decrement(
                                'stock',
                                $detail->quantity
                            );


                            InventoryLog::create([

                                'product_id' =>
                                    $product->id,

                                'user_id' =>
                                    Auth::id(),

                                'type' =>
                                    'sale',

                                'quantity' =>
                                    -$detail->quantity,

                                'old_stock' =>
                                    $oldStock,

                                'new_stock' =>
                                    $oldStock -
                                    $detail->quantity,

                                'note' =>
                                    'Venta POS #'
                                    .
                                    $order->id
                            ]);
                        }
                    }
                }
            }
        );


        // ---------------------------------------------------------
        // NOMBRE DEL MÉTODO DE PAGO
        // ---------------------------------------------------------

        $paymentNames = [

            'cash' =>
                'Efectivo',

            'yape' =>
                'Yape',

            'plin' =>
                'Plin',

            'card' =>
                'Tarjeta',

            'transfer' =>
                'Transferencia',

            'qr' =>
                'QR'
        ];


        $paymentName =
            $paymentNames[$method]
            ?? ucfirst($method);


        // ---------------------------------------------------------
        // DATOS DE LA MESA
        // ---------------------------------------------------------

        $tableNumber = null;

        if (
            $order->table
        ) {

            $tableNumber =
                $order->table->number
                ?? $order->table->name
                ?? $order->table->id;
        }


        // ---------------------------------------------------------
        // MENSAJE PARA EL USUARIO QUE COBRÓ
        // ---------------------------------------------------------

        $successMessage =
            '✓ PAGO REALIZADO CORRECTAMENTE. ';

        if ($tableNumber) {

            $successMessage .=
                'Mesa '
                .
                $tableNumber
                .
                ' cobrada. ';
        }

        $successMessage .=
            'Total: S/ '
            .
            number_format(
                $order->total,
                2
            )
            .
            ' | Método: '
            .
            $paymentName
            .
            '.';


        // ---------------------------------------------------------
        // REGRESAR AL POS
        // ---------------------------------------------------------

        return redirect()
            ->route('pos.index')
            ->with(
                'success',
                $successMessage
            );
    }


    // =============================================================
    // RECALCULAR TOTAL
    // =============================================================

    private function recalculateTotal(
        Order $order
    ) {

        $subtotal =
            $order->details->sum(
                fn ($d) =>
                    $d->price *
                    $d->quantity
            );


        $total =
            (
                $subtotal -
                ($order->discount ?? 0)
            )
            +
            ($order->tip ?? 0);


        $order->update([
            'total' => max(
                0,
                $total
            )
        ]);
    }


    // =============================================================
    // HTML DEL CARRITO
    // =============================================================

    private function getCartHtml(
        Table $table
    ) {

        $order = Order::where(
            'table_id',
            $table->id
        )
            ->where(
                'status',
                'pending'
            )
            ->with(
                'details.product'
            )
            ->first();


        $clients = Client::select(
            'id',
            'name',
            'document_number'
        )
            ->orderBy(
                'name'
            )
            ->get();


        $currency = Setting::where(
            'key',
            'currency_symbol'
        )->value(
            'value'
        ) ?? 'S/';


        return view(
            'pos.partials.cart',
            compact(
                'order',
                'clients',
                'currency'
            )
        )->render();
    }
}