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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Pantalla principal del POS.
     */
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
                            ->whereDate('reservation_time', Carbon::today())
                            ->where(
                                'reservation_time',
                                '>=',
                                Carbon::now()->subHours(2)
                            )
                            ->orderBy('reservation_time', 'asc');
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
            compact('areas', 'currency')
        );
    }

    /**
     * Abrir una mesa en el POS.
     */
    public function order(Table $table)
    {
        $categories = Category::with([
            'products' => function ($q) {
                $q->where('is_active', true)
                    ->where('is_saleable', true);
            }
        ])
            ->where('is_active', true)
            ->get();

        $order = Order::where('table_id', $table->id)
            ->where('status', 'pending')
            ->with('details.product')
            ->first();

        $occupiedTableIds = Order::where('status', 'pending')
            ->pluck('table_id');

        $freeTables = Table::whereNotIn(
            'id',
            $occupiedTableIds
        )
            ->where('id', '!=', $table->id)
            ->with('area')
            ->get();

        $clients = Client::select(
            'id',
            'name',
            'document_number'
        )
            ->orderBy('name')
            ->get();

        $settings = Setting::pluck(
            'value',
            'key'
        )->toArray();

        $currency = $settings['currency_symbol'] ?? 'S/';

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

    /**
     * Agregar producto a la orden.
     */
    public function addToOrder(
        Request $request,
        Table $table
    ) {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail(
            $request->product_id
        );

        if (!$product->is_active || !$product->is_saleable) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está disponible para la venta.'
            ], 422);
        }

        $this->addItemToTable(
            $table,
            $product
        );

        return $this->getCartHtml($table);
    }

    /**
     * Lógica para agregar producto.
     */
    private function addItemToTable(
        Table $table,
        Product $product
    ) {
        DB::transaction(function () use (
            $table,
            $product
        ) {
            $order = Order::firstOrCreate(
                [
                    'table_id' => $table->id,
                    'status' => 'pending'
                ],
                [
                    'user_id' => Auth::id() ?? 1,
                    'total' => 0,
                    'discount' => 0,
                    'tip' => 0
                ]
            );

            $detail = $order->details()
                ->where('product_id', $product->id)
                ->first();

            if ($detail) {
                $detail->increment('quantity');
            } else {
                $order->details()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price,
                    'status' => 'pending'
                ]);
            }

            $this->recalculateTotal($order);
        });
    }

    /**
     * Actualizar cantidad.
     */
    public function updateQuantity(
        Request $request,
        OrderDetail $detail
    ) {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $newQty = (int) $request->quantity;
        $order = $detail->order;

        if ($newQty < 1) {
            $detail->delete();
        } else {
            $detail->update([
                'quantity' => $newQty
            ]);
        }

        $this->recalculateTotal($order);

        return $this->getCartHtml(
            $order->table
        );
    }

    /**
     * Actualizar nota.
     */
    public function updateNote(
        Request $request,
        OrderDetail $detail
    ) {
        $detail->update([
            'note' => $request->input('note')
        ]);

        return $this->getCartHtml(
            $detail->order->table
        );
    }

    /**
     * Eliminar producto.
     */
    public function removeItem(
        OrderDetail $detail
    ) {
        $order = $detail->order;

        $detail->delete();

        $this->recalculateTotal($order);

        return $this->getCartHtml(
            $order->table
        );
    }

    /**
     * Aplicar descuento y propina.
     */
    public function applyDiscount(
        Request $request,
        Order $order
    ) {
        $discount = max(
            0,
            (float) $request->input('discount', 0)
        );

        $tip = max(
            0,
            (float) $request->input('tip', 0)
        );

        $order->update([
            'discount' => $discount,
            'tip' => $tip
        ]);

        $this->recalculateTotal($order);

        return $this->getCartHtml(
            $order->table
        );
    }

    /**
     * Mover mesa.
     */
    public function moveTable(
        Request $request,
        Order $order
    ) {
        $request->validate([
            'target_table_id' => 'required|exists:tables,id'
        ]);

        $occupied = Order::where(
            'table_id',
            $request->target_table_id
        )
            ->where('status', 'pending')
            ->exists();

        if ($occupied) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'La mesa seleccionada está ocupada.'
                );
        }

        $order->update([
            'table_id' => $request->target_table_id
        ]);

        return redirect()->route(
            'pos.order',
            $request->target_table_id
        );
    }

    /**
     * Contenido para dividir cuenta.
     */
    public function getSplitContent(
        Order $order
    ) {
        return view(
            'pos.partials.split_content',
            compact('order')
        );
    }

    /**
     * Procesar división de cuenta.
     */
    public function processSplit(
        Request $request,
        Order $order
    ) {
        return redirect()->back();
    }

    /**
     * Previsualización de venta.
     */
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

    /**
     * Ticket para cocina.
     */
    public function kitchenTicket(
        Order $order
    ) {
        return view(
            'sales.kitchen_ticket',
            compact('order')
        );
    }

    /**
     * COBRAR ORDEN.
     *
     * Soporta:
     * - Ticket
     * - Boleta + DNI
     * - Factura + RUC + Razón Social
     * - Efectivo
     * - Yape
     * - Plin
     * - Tarjeta
     * - Transferencia
     * - QR
     */
    public function checkout(
        Request $request,
        Order $order
    ) {
        /*
        |--------------------------------------------------------------------------
        | 1. VERIFICAR ESTADO
        |--------------------------------------------------------------------------
        */

        if ($order->status !== 'pending') {
            return redirect()
                ->route('pos.index')
                ->with(
                    'error',
                    'La orden ya fue cerrada.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. TIPO DE COMPROBANTE
        |--------------------------------------------------------------------------
        */

        $documentType = trim(
            $request->input(
                'document_type',
                'Ticket'
            )
        );

        $validDocumentTypes = [
            'Ticket',
            'Boleta',
            'Factura'
        ];

        if (!in_array(
            $documentType,
            $validDocumentTypes,
            true
        )) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'El tipo de comprobante no es válido.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. MÉTODO DE PAGO
        |--------------------------------------------------------------------------
        */

        $method = strtolower(
            trim(
                $request->input(
                    'payment_method',
                    'cash'
                )
            )
        );

        $validPaymentMethods = [
            'cash',
            'yape',
            'plin',
            'card',
            'transfer',
            'qr'
        ];

        if (!in_array(
            $method,
            $validPaymentMethods,
            true
        )) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'El método de pago no es válido.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. DATOS DEL CLIENTE
        |--------------------------------------------------------------------------
        */

        $clientId = $request->input('client_id');

        $client = null;

        if ($clientId) {
            $client = Client::find($clientId);

            if (!$client) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'El cliente seleccionado no existe.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | NOMBRE DEL CLIENTE
        |--------------------------------------------------------------------------
        */

        $clientName = $client
            ? trim($client->name)
            : trim(
                $request->input(
                    'client_name',
                    ''
                )
            );

        /*
        |--------------------------------------------------------------------------
        | DNI / RUC
        |--------------------------------------------------------------------------
        */

        $clientDocument = trim(
            (string) $request->input(
                'client_document',
                ''
            )
        );

        /*
        | Si se seleccionó un cliente registrado y
        | el campo está vacío, utilizar su documento.
        */
        if (
            $clientDocument === '' &&
            $client &&
            !empty($client->document_number)
        ) {
            $clientDocument = trim(
                (string) $client->document_number
            );
        }

        /*
        | Solo permitir números.
        */
        $clientDocument = preg_replace(
            '/\D/',
            '',
            $clientDocument
        );

        /*
        |--------------------------------------------------------------------------
        | RAZÓN SOCIAL
        |--------------------------------------------------------------------------
        */

        $businessName = trim(
            $request->input(
                'business_name',
                ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | 5. TICKET
        |--------------------------------------------------------------------------
        */

        if ($documentType === 'Ticket') {
            $clientDocument = null;
            $businessName = null;

            if ($clientName === '') {
                $clientName = 'Público General';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. BOLETA
        |--------------------------------------------------------------------------
        */

        if ($documentType === 'Boleta') {
            if ($clientName === '') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Para una Boleta debe ingresar el nombre del cliente.'
                    );
            }

            if (!preg_match(
                '/^\d{8}$/',
                $clientDocument
            )) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'El DNI debe tener exactamente 8 dígitos.'
                    );
            }

            $businessName = null;
        }

        /*
        |--------------------------------------------------------------------------
        | 7. FACTURA
        |--------------------------------------------------------------------------
        */

        if ($documentType === 'Factura') {
            if ($clientName === '') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Para una Factura debe ingresar el nombre del cliente.'
                    );
            }

            if (!preg_match(
                '/^\d{11}$/',
                $clientDocument
            )) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'El RUC debe tener exactamente 11 dígitos.'
                    );
            }

            if ($businessName === '') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Para una Factura debe ingresar la Razón Social.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 8. MONTO RECIBIDO
        |--------------------------------------------------------------------------
        */

        $total = (float) $order->total;

        if ($method === 'cash') {
            $received = (float) $request->input(
                'received_amount',
                0
            );
        } else {
            $received = $total;
        }

        /*
        |--------------------------------------------------------------------------
        | 9. VALIDAR EFECTIVO
        |--------------------------------------------------------------------------
        */

        if (
            $method === 'cash' &&
            $received < $total
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'El monto recibido es insuficiente para completar el pago.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 10. CALCULAR CAMBIO
        |--------------------------------------------------------------------------
        */

        $change = $method === 'cash'
            ? max(
                0,
                $received - $total
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 11. GUARDAR VENTA
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $order,
            $method,
            $received,
            $change,
            $clientId,
            $clientName,
            $clientDocument,
            $documentType,
            $businessName
        ) {
            /*
            | Guardar información de la venta.
            */
            $order->update([
                'status' => 'completed',
                'payment_method' => $method,
                'received_amount' => $received,
                'change_amount' => $change,
                'document_type' => $documentType,
                'client_id' => $clientId,
                'client_name' => $clientName,
                'client_document' => $clientDocument,
                'business_name' => $businessName
            ]);

            /*
            | Actualizar documento del cliente registrado.
            */
            if (
                $clientId &&
                $clientDocument
            ) {
                $client = Client::find($clientId);

                if ($client) {
                    $client->update([
                        'document_number' => $clientDocument
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DESCONTAR STOCK
            |--------------------------------------------------------------------------
            */

            $order->load([
                'details.product.ingredients'
            ]);

            foreach (
                $order->details as $detail
            ) {
                $product = $detail->product;

                if (!$product) {
                    continue;
                }

                $ingredients = $product->ingredients;

                /*
                |--------------------------------------------------------------------------
                | PRODUCTO CON INGREDIENTES
                |--------------------------------------------------------------------------
                */

                if ($ingredients->count() > 0) {
                    foreach (
                        $ingredients as $ingredient
                    ) {
                        $ingredientQuantity = (float) (
                            $ingredient->pivot->quantity
                        );

                        $qtyToDeduct =
                            $ingredientQuantity *
                            (float) $detail->quantity;

                        $oldStock =
                            (float) $ingredient->stock;

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
                                'Venta: ' .
                                $product->name .
                                ' (Orden #' .
                                $order->id .
                                ')'
                        ]);
                    }
                } else {
                    /*
                    |--------------------------------------------------------------------------
                    | PRODUCTO SIN INGREDIENTES
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !is_null(
                            $product->stock
                        )
                    ) {
                        $oldStock =
                            (float) $product->stock;

                        $qtyToDeduct =
                            (float) $detail->quantity;

                        $product->decrement(
                            'stock',
                            $qtyToDeduct
                        );

                        InventoryLog::create([
                            'product_id' =>
                                $product->id,

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
                                'Venta POS #' .
                                $order->id
                        ]);
                    }
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | 12. NOMBRE DEL MÉTODO DE PAGO
        |--------------------------------------------------------------------------
        */

        $paymentNames = [
            'cash' => 'Efectivo',
            'yape' => 'Yape',
            'plin' => 'Plin',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
            'qr' => 'QR'
        ];

        $paymentName =
            $paymentNames[$method]
            ?? ucfirst($method);

        /*
        |--------------------------------------------------------------------------
        | 13. DATOS DE MESA
        |--------------------------------------------------------------------------
        */

        $tableNumber = null;

        if ($order->table) {
            $tableNumber =
                $order->table->number
                ?? $order->table->name
                ?? $order->table->id;
        }

        /*
        |--------------------------------------------------------------------------
        | 14. MENSAJE
        |--------------------------------------------------------------------------
        */

        $successMessage =
            '✓ PAGO REALIZADO CORRECTAMENTE. ';

        if ($tableNumber) {
            $successMessage .=
                'Mesa ' .
                $tableNumber .
                ' cobrada. ';
        }

        $successMessage .=
            'Total: S/ ' .
            number_format($total, 2) .
            ' | Comprobante: ' .
            $documentType .
            ' | Método: ' .
            $paymentName .
            '.';

        /*
        |--------------------------------------------------------------------------
        | 15. INFORMACIÓN DEL CLIENTE
        |--------------------------------------------------------------------------
        */

        if ($documentType === 'Boleta') {
            $successMessage .=
                ' DNI: ' .
                $clientDocument .
                '.';
        }

        if ($documentType === 'Factura') {
            $successMessage .=
                ' RUC: ' .
                $clientDocument .
                ' | Razón Social: ' .
                $businessName .
                '.';
        }

        /*
        |--------------------------------------------------------------------------
        | 16. CAMBIO
        |--------------------------------------------------------------------------
        */

        if ($method === 'cash') {
            $successMessage .=
                ' Cambio: S/ ' .
                number_format(
                    $change,
                    2
                ) .
                '.';
        }

        /*
        |--------------------------------------------------------------------------
        | 17. REGRESAR AL POS
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('pos.index')
            ->with(
                'success',
                $successMessage
            );
    }

    /**
     * Recalcular total.
     */
    private function recalculateTotal(
        Order $order
    ) {
        $order->loadMissing('details');

        $subtotal = $order->details->sum(
            function ($detail) {
                return
                    (float) $detail->price *
                    (int) $detail->quantity;
            }
        );

        $discount =
            (float) ($order->discount ?? 0);

        $tip =
            (float) ($order->tip ?? 0);

        $total =
            ($subtotal - $discount) +
            $tip;

        $order->update([
            'total' => max(
                0,
                $total
            )
        ]);
    }

    /**
     * HTML del carrito.
     */
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
            ->orderBy('name')
            ->get();

        $currency = Setting::where(
            'key',
            'currency_symbol'
        )->value('value') ?? 'S/';

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