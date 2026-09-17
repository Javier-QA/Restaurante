<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RestaurantAssistantService
{
    /**
     * Normaliza el mensaje para facilitar su interpretación.
     */
    public function normalize(string $message): string
    {
        $message = mb_strtolower(trim($message), 'UTF-8');

        return strtr($message, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ñ' => 'n',
        ]);
    }

    /**
     * Intenta responder una consulta utilizando datos reales
     * almacenados en el sistema del restaurante.
     *
     * Retorna null cuando la pregunta debe ser atendida
     * por la IA general.
     */

    /**
     * Detecta el período de tiempo mencionado por el usuario.
     */
    public function detectPeriod(string $message): ?array
    {
        $message = $this->normalize($message);

        // LISTADO GENERAL DE PRODUCTOS
        $isProductListQuestion =
            str_contains($message, 'muestrame los productos') ||
            str_contains($message, 'mostrar productos') ||
            str_contains($message, 'lista de productos') ||
            str_contains($message, 'listar productos') ||
            str_contains($message, 'que productos tenemos') ||
            str_contains($message, 'que productos hay') ||
            str_contains($message, 'muestrame el menu') ||
            str_contains($message, 'mostrar menu') ||
            $message === 'productos' ||
            $message === 'menu';

        if ($isProductListQuestion) {
            $products = DB::table('products')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->where('products.is_active', 1)
                ->where('products.is_saleable', 1)
                ->orderBy('categories.name')
                ->orderBy('products.name')
                ->get([
                    'products.id',
                    'products.name',
                    'products.price',
                    'products.promotional_price',
                    'products.stock',
                    'categories.name as category_name',
                ]);

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos activos disponibles actualmente.',
                    'intent' => 'product_list',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $lines = [];
            $currentCategory = null;

            foreach ($products as $product) {
                $category = $product->category_name ?: 'Sin categoría';

                if ($currentCategory !== $category) {
                    if ($currentCategory !== null) {
                        $lines[] = '';
                    }

                    $lines[] = $category . ':';
                    $currentCategory = $category;
                }

                $price = $product->promotional_price !== null
                    ? (float) $product->promotional_price
                    : (float) $product->price;

                $lines[] =
                    '- ' . $product->name .
                    ' | S/ ' . number_format($price, 2);
            }

            return [
                'response' =>
                    'Actualmente tenemos ' . $products->count() .
                    ' productos disponibles en la carta.',
                'intent' => 'product_list',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }
        $now = Carbon::now();

        if (str_contains($message, 'hoy') || str_contains($message, 'dia de hoy')) {
            return [
                'type' => 'today',
                'label' => 'hoy',
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ];
        }

        if (str_contains($message, 'ayer')) {
            return [
                'type' => 'yesterday',
                'label' => 'ayer',
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ];
        }

        // SEMANA PASADA
        if (
            str_contains($message, 'semana pasada') ||
            str_contains($message, 'semana anterior')
        ) {
            return [
                'type' => 'last_week',
                'label' => 'la semana pasada',
                'start' => $now->copy()->subWeek()->startOfWeek(),
                'end' => $now->copy()->subWeek()->endOfWeek(),
            ];
        }

        // MES PASADO
        if (
            str_contains($message, 'mes pasado') ||
            str_contains($message, 'mes anterior')
        ) {
            return [
                'type' => 'last_month',
                'label' => 'el mes pasado',
                'start' => $now->copy()->subMonthNoOverflow()->startOfMonth(),
                'end' => $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ];
        }
        if (str_contains($message, 'esta semana') || str_contains($message, 'semana actual')) {
            return [
                'type' => 'week',
                'label' => 'esta semana',
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ];
        }

        if (str_contains($message, 'este mes') || str_contains($message, 'mes actual')) {
            return [
                'type' => 'month',
                'label' => 'este mes',
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ];
        }

        return null;
    }
    /**
     * Calcula las ventas completadas dentro de un período.
     */
    private function getSalesTotal(array $period, ?string $paymentMethod = null): float
    {
        $query = DB::table('orders')
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $period['start'],
                $period['end'],
            ]);

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        return (float) $query->sum('total');
    }
    /**
     * Cuenta pedidos dentro de un período.
     */
    private function getOrdersCount(array $period, ?string $status = null): int
    {
        $query = DB::table('orders')
            ->whereBetween('created_at', [
                $period['start'],
                $period['end'],
            ]);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->count();
    }
    /**
     * Obtiene el ranking de productos vendidos en un período.
     * Solo considera pedidos completados.
     */
    private function getProductRanking(array $period, int $limit = 5, bool $ascending = false)
    {
        $query = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [
                $period['start'],
                $period['end'],
            ])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_details.quantity) as quantity_sold'),
                DB::raw('SUM(order_details.quantity * order_details.price) as sales_total')
            )
            ->groupBy('products.id', 'products.name');

        if ($ascending) {
            $query->orderBy('quantity_sold', 'asc');
        } else {
            $query->orderByDesc('quantity_sold');
        }

        return $query->limit($limit)->get();
    }
    /**
     * Obtiene productos agotados.
     */
    private function getOutOfStockProducts()
    {
        return DB::table('products')
            ->where('is_active', 1)
            ->where('controls_stock', 1)
            ->where('stock', '<=', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'stock']);
    }

    /**
     * Obtiene productos con stock bajo.
     */
    private function getLowStockProducts(int $minimum = 1, int $maximum = 5)
    {
        return DB::table('products')
            ->where('is_active', 1)
            ->where('controls_stock', 1)
            ->whereBetween('stock', [$minimum, $maximum])
            ->orderBy('stock')
            ->orderBy('name')
            ->get(['id', 'name', 'stock']);
    }

    /**
     * Busca un producto utilizando parte de su nombre.
     */
    private function findProductByName(string $name)
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        return DB::table('products')
            ->where('is_active', 1)
            ->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($name, 'UTF-8') . '%'])
            ->orderBy('name')
            ->first();
    }
    /**
     * Obtiene los últimos movimientos de inventario.
     */
    private function getInventoryMovements(?int $productId = null, int $limit = 10)
    {
        $query = DB::table('inventory_logs')
            ->join('products', 'products.id', '=', 'inventory_logs.product_id')
            ->leftJoin('users', 'users.id', '=', 'inventory_logs.user_id')
            ->select(
                'inventory_logs.id',
                'products.name as product_name',
                'inventory_logs.type',
                'inventory_logs.quantity',
                'inventory_logs.old_stock',
                'inventory_logs.new_stock',
                'inventory_logs.note',
                'inventory_logs.created_at',
                'users.name as user_name'
            );

        if ($productId) {
            $query->where('inventory_logs.product_id', $productId);
        }

        return $query
            ->orderByDesc('inventory_logs.id')
            ->limit($limit)
            ->get();
    }
    /**
     * Obtiene el resumen financiero de la caja abierta.
     */
    private function getOpenCashRegisterSummary(): ?array
    {
        $cashRegister = DB::table('cash_registers')
            ->leftJoin('users', 'users.id', '=', 'cash_registers.user_id')
            ->where('cash_registers.status', 'open')
            ->select(
                'cash_registers.*',
                'users.name as user_name'
            )
            ->orderByDesc('cash_registers.id')
            ->first();

        if (!$cashRegister) {
            return null;
        }

        $sales = DB::table('orders')
            ->where('cash_register_id', $cashRegister->id)
            ->where('status', 'completed')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('payment_method')
            ->get();

        $payments = [
            'cash' => 0.0,
            'card' => 0.0,
            'yape' => 0.0,
            'plin' => 0.0,
            'transfer' => 0.0,
        ];

        $ordersCount = 0;
        $salesTotal = 0.0;

        foreach ($sales as $sale) {
            $method = $sale->payment_method;

            if (array_key_exists($method, $payments)) {
                $payments[$method] = (float) $sale->total;
            }

            $ordersCount += (int) $sale->orders_count;
            $salesTotal += (float) $sale->total;
        }

        $expenses = (float) DB::table('expenses')
            ->where('cash_register_id', $cashRegister->id)
            ->sum('amount');

        $openingAmount = (float) $cashRegister->opening_amount;

        // Solo el efectivo físico entra al efectivo esperado.
        $expectedCash =
            $openingAmount +
            $payments['cash'] -
            $expenses;

        return [
            'id' => $cashRegister->id,
            'user' => $cashRegister->user_name,
            'opening_time' => $cashRegister->opening_time,
            'opening_amount' => $openingAmount,
            'payments' => $payments,
            'orders_count' => $ordersCount,
            'sales_total' => $salesTotal,
            'expenses' => $expenses,
            'expected_cash' => $expectedCash,
        ];
    }
    /**
     * Obtiene el total de gastos dentro de un período.
     */
    private function getExpensesTotal(array $period): float
    {
        return (float) DB::table('expenses')
            ->whereBetween('created_at', [
                $period['start'],
                $period['end'],
            ])
            ->sum('amount');
    }
    /**
     * Obtiene las reservas de un período.
     */
    private function getReservationsByPeriod(array $period)
    {
        return DB::table('reservations')
            ->leftJoin('tables', 'tables.id', '=', 'reservations.table_id')
            ->whereBetween('reservations.reservation_time', [
                $period['start'],
                $period['end'],
            ])
            ->select(
                'reservations.id',
                'reservations.client_name',
                'reservations.phone',
                'reservations.reservation_time',
                'reservations.people',
                'reservations.note',
                'reservations.status',
                'tables.name as table_name'
            )
            ->orderBy('reservations.reservation_time')
            ->get();
    }

    /**
     * Obtiene las mesas y su área.
     */
    private function getTables(?string $status = null)
    {
        $query = DB::table('tables')
            ->join('areas', 'areas.id', '=', 'tables.area_id')
            ->select(
                'tables.id',
                'tables.name',
                'tables.seats',
                'tables.status',
                'areas.name as area_name'
            );

        if ($status !== null) {
            $query->where('tables.status', $status);
        }

        return $query
            ->orderBy('areas.name')
            ->orderBy('tables.name')
            ->get();
    }

    /**
     * Busca mesas disponibles con capacidad suficiente.
     */
    private function getAvailableTablesForPeople(int $people)
    {
        return DB::table('tables')
            ->join('areas', 'areas.id', '=', 'tables.area_id')
            ->where('tables.status', 'available')
            ->where('tables.seats', '>=', $people)
            ->select(
                'tables.id',
                'tables.name',
                'tables.seats',
                'areas.name as area_name'
            )
            ->orderBy('tables.seats')
            ->orderBy('tables.name')
            ->get();
    }
    /**
     * Obtiene deliveries según estado y período opcional.
     */
    private function getDeliveries(?string $status = null, ?array $period = null)
    {
        $query = DB::table('deliveries')
            ->leftJoin('orders', 'orders.id', '=', 'deliveries.order_id')
            ->leftJoin(
                'delivery_drivers',
                'delivery_drivers.id',
                '=',
                'deliveries.driver_id'
            )
            ->select(
                'deliveries.id',
                'deliveries.client_name',
                'deliveries.client_phone',
                'deliveries.address',
                'deliveries.reference',
                'deliveries.status',
                'deliveries.payment_method',
                'deliveries.delivery_fee',
                'deliveries.scheduled_at',
                'deliveries.delivered_at',
                'deliveries.created_at',
                'orders.total as order_total',
                'delivery_drivers.name as driver_name'
            );

        if ($status !== null) {
            $query->where('deliveries.status', $status);
        }

        if ($period !== null) {
            $query->whereBetween('deliveries.created_at', [
                $period['start'],
                $period['end'],
            ]);
        }

        return $query
            ->orderByDesc('deliveries.id')
            ->get();
    }

    /**
     * Obtiene el total cobrado por concepto de delivery.
     */
    private function getDeliveryFeesTotal(?array $period = null): float
    {
        $query = DB::table('deliveries');

        if ($period !== null) {
            $query->whereBetween('created_at', [
                $period['start'],
                $period['end'],
            ]);
        }

        return (float) $query->sum('delivery_fee');
    }
    /**
     * Busca clientes por nombre o número de documento.
     */
    private function findClients(string $search)
    {
        $search = trim($search);

        if ($search === '') {
            return collect();
        }

        // Si la búsqueda es únicamente numérica, priorizar documento exacto.
        if (preg_match('/^\d+$/', $search)) {
            $exactMatches = DB::table('clients')
                ->where('document_number', $search)
                ->get();

            if ($exactMatches->isNotEmpty()) {
                return $exactMatches;
            }

            return collect();
        }

        // Para nombres, realizar búsqueda flexible normalizada.
        $normalizedSearch = $this->normalize($search);

        return DB::table('clients')
            ->get()
            ->filter(function ($client) use ($normalizedSearch) {
                $normalizedName = $this->normalize($client->name ?? '');

                return str_contains(
                    $normalizedName,
                    $normalizedSearch
                );
            })
            ->values();
    }

    /**
     * Obtiene estadísticas de compras de un cliente.
     */
    private function getClientPurchaseStats(int $clientId): array
    {
        $query = DB::table('orders')
            ->where('client_id', $clientId)
            ->where('status', 'completed');

        return [
            'orders_count' => (clone $query)->count(),
            'total_spent' => (float) (clone $query)->sum('total'),
            'last_purchase' => (clone $query)->max('created_at'),
        ];
    }

    /**
     * Obtiene los clientes con mayor monto de compras completadas.
     */
    private function getTopClients(int $limit = 5)
    {
        return DB::table('clients')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.client_id', '=', 'clients.id')
                    ->where('orders.status', '=', 'completed');
            })
            ->select(
                'clients.id',
                'clients.name',
                'clients.document_number',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(orders.total), 0) as total_spent')
            )
            ->groupBy(
                'clients.id',
                'clients.name',
                'clients.document_number'
            )
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();
    }
    /**
     * Obtiene el día con mayor monto de ventas completadas.
     */
    private function getBestSalesDay(?array $period = null)
    {
        $query = DB::table('orders')
            ->where('status', 'completed');

        if ($period !== null) {
            $query->whereBetween('created_at', [
                $period['start'],
                $period['end'],
            ]);
        }

        return $query
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('total')
            ->orderByDesc('orders_count')
            ->first();
    }

    /**
     * Obtiene la hora con mayor monto de ventas completadas.
     */
    private function getBestSalesHour(?array $period = null)
    {
        $query = DB::table('orders')
            ->where('status', 'completed');

        if ($period !== null) {
            $query->whereBetween('created_at', [
                $period['start'],
                $period['end'],
            ]);
        }

        return $query
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderByDesc('total')
            ->orderByDesc('orders_count')
            ->first();
    }
    /**
     * Interpreta expresiones naturales y las convierte en una intención conocida.
     * No consulta la base de datos ni genera respuestas.
     */
    private function interpretIntent(string $message): ?string
    {
        $message = $this->normalize($message);

        // LISTADO GENERAL DE PRODUCTOS
        $isProductListQuestion =
            str_contains($message, 'muestrame los productos') ||
            str_contains($message, 'mostrar productos') ||
            str_contains($message, 'lista de productos') ||
            str_contains($message, 'listar productos') ||
            str_contains($message, 'que productos tenemos') ||
            str_contains($message, 'que productos hay') ||
            str_contains($message, 'muestrame el menu') ||
            str_contains($message, 'mostrar menu') ||
            $message === 'productos' ||
            $message === 'menu';

        if ($isProductListQuestion) {
            $products = DB::table('products')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->where('products.is_active', 1)
                ->where('products.is_saleable', 1)
                ->orderBy('categories.name')
                ->orderBy('products.name')
                ->get([
                    'products.id',
                    'products.name',
                    'products.price',
                    'products.promotional_price',
                    'products.stock',
                    'categories.name as category_name',
                ]);

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos activos disponibles actualmente.',
                    'intent' => 'product_list',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $lines = [];
            $currentCategory = null;

            foreach ($products as $product) {
                $category = $product->category_name ?: 'Sin categoría';

                if ($currentCategory !== $category) {
                    if ($currentCategory !== null) {
                        $lines[] = '';
                    }

                    $lines[] = $category . ':';
                    $currentCategory = $category;
                }

                $price = $product->promotional_price !== null
                    ? (float) $product->promotional_price
                    : (float) $product->price;

                $lines[] =
                    '- ' . $product->name .
                    ' | S/ ' . number_format($price, 2);
            }

            return [
                'response' =>
                    'Actualmente tenemos ' . $products->count() .
                    ' productos disponibles en la carta.',
                'intent' => 'product_list',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }

        $hasAny = function (array $words) use ($message): bool {
            foreach ($words as $word) {
                if (str_contains($message, $word)) {
                    return true;
                }
            }

            return false;
        };

        // Predicción de ventas
        if (
            $hasAny([
                'prediccion',
                'predicción',
                'pronostico',
                'pronóstico',
                'estimar',
                'estimado',
                'estimada',
                'podria vender',
                'podremos vender',
                'venderemos',
                'se estima'
            ]) &&
            $hasAny([
                'venta',
                'ventas',
                'vender',
                'vendere',
                'venderemos',
                'dinero',
                'ingreso',
                'ingresos'
            ])
        ) {
            return 'predict_sales';
        }
        // Día con mayor monto vendido
        if (
            $hasAny(['dia', 'fecha']) &&
            $hasAny(['venta', 'ventas', 'vendi', 'vendio', 'ingreso', 'ingresos']) &&
            $hasAny(['mas', 'mayor', 'mejor', 'maximo'])
        ) {
            return 'best_sales_day';
        }

        // Hora u horario con mayor monto vendido
        if (
            $hasAny(['hora', 'horario', 'momento']) &&
            $hasAny(['venta', 'ventas', 'vendo', 'vendi', 'vendio', 'ingreso', 'ingresos', 'dinero']) &&
            $hasAny(['mas', 'mayor', 'mejor', 'maximo', 'punta'])
        ) {
            return 'best_sales_hour';
        }

        // Cantidad de clientes
        if (
            $hasAny(['cliente', 'clientes']) &&
            $hasAny(['cuantos', 'cantidad', 'total', 'registrados'])
        ) {
            return 'clients_count';
        }

        // Clientes con mayor monto de compras
        if (
            $hasAny(['cliente', 'clientes']) &&
            $hasAny(['compra', 'compran', 'comprado', 'gasta', 'gastan']) &&
            $hasAny(['mas', 'mayor', 'mejor', 'top'])
        ) {
            return 'top_clients';
        }

        // Pedidos
        if ($hasAny(['pedido', 'pedidos'])) {
            if (
                $hasAny([
                    'pendiente',
                    'pendientes',
                    'falta',
                    'faltan',
                    'espera',
                    'sin completar',
                ])
            ) {
                return 'orders_pending';
            }

            if (
                $hasAny([
                    'completado',
                    'completados',
                    'finalizado',
                    'finalizados',
                    'finalizaron',
                    'finalizo',
                    'terminado',
                    'terminados',
                    'atendidos',
                ])
            ) {
                return 'orders_completed';
            }

            if (
                $hasAny([
                    'cuantos',
                    'cantidad',
                    'total',
                    'recibi',
                    'recibimos',
                    'hubo',
                    'tengo',
                    'tenemos',
                ])
            ) {
                return 'orders_count';
            }
        }

        // Inventario agotado - lista completa
        if (
            $hasAny(['producto', 'productos', 'inventario', 'stock']) &&
            $hasAny(['agotado', 'agotados', 'sin stock', 'no tiene stock', 'no tienen stock', 'no hay stock', 'stock cero']) &&
            $hasAny(['todos', 'todas', 'completa', 'completo', 'lista completa'])
        ) {
            return 'out_of_stock_all';
        }

        // Inventario agotado
        if (
            $hasAny(['producto', 'productos', 'inventario', 'stock']) &&
            $hasAny(['agotado', 'agotados', 'sin stock', 'no tiene stock', 'no tienen stock', 'no hay stock', 'stock cero'])
        ) {
            return 'out_of_stock';
        }

        // Inventario bajo
        if (
            $hasAny(['producto', 'productos', 'inventario', 'stock']) &&
            $hasAny([
                'bajo',
                'poco',
                'poca existencia',
                'pocas existencias',
                'por agotarse',
                'acabando',
            ])
        ) {
            return 'low_stock';
        }

        // Caja - consultas flexibles
        if ($hasAny(['caja'])) {
            // Gastos de la caja
            if (
                $hasAny([
                    'gasto',
                    'gastos',
                    'gastado',
                    'gastamos',
                    'hemos gastado',
                    'se gasto',
                ])
            ) {
                return 'cash_register_expenses';
            }

            // Ventas totales de la caja
            if (
                $hasAny([
                    'venta',
                    'ventas',
                    'vendido',
                    'vendimos',
                    'vendio',
                    'recaudado',
                    'recaudamos',
                    'ingreso',
                    'ingresos',
                ])
            ) {
                return 'cash_register_sales';
            }

            // Efectivo físico esperado
            if (
                $hasAny([
                    'efectivo',
                    'dinero',
                    'cuanto hay',
                    'cuanto tengo',
                    'cuanto tenemos',
                ])
            ) {
                return 'cash_register_expected_cash';
            }

            // Estado o resumen general
            if (
                $hasAny([
                    'estado',
                    'resumen',
                    'como va',
                    'como esta',
                    'abierta',
                    'abierto',
                ])
            ) {
                return 'cash_register_summary';
            }
        }

        // Métodos de pago de la caja
        if (
            $hasAny(['yape']) &&
            $hasAny([
                'caja',
                'cuanto',
                'recibi',
                'recibimos',
                'cobre',
                'cobramos',
                'entro',
                'ingreso',
            ])
        ) {
            return 'cash_register_yape';
        }

        if (
            $hasAny(['plin']) &&
            $hasAny([
                'caja',
                'cuanto',
                'recibi',
                'recibimos',
                'cobre',
                'cobramos',
                'entro',
                'ingreso',
            ])
        ) {
            return 'cash_register_plin';
        }

        if (
            $hasAny(['tarjeta', 'card']) &&
            $hasAny([
                'caja',
                'cuanto',
                'recibi',
                'recibimos',
                'cobre',
                'cobramos',
                'entro',
                'ingreso',
            ])
        ) {
            return 'cash_register_card';
        }

        if (
            $hasAny(['transferencia', 'transfer']) &&
            $hasAny([
                'caja',
                'cuanto',
                'recibi',
                'recibimos',
                'cobre',
                'cobramos',
                'entro',
                'ingreso',
            ])
        ) {
            return 'cash_register_transfer';
        }
        return null;
    }
    public function answer(string $message): ?array
    {
        $message = $this->normalize($message);

        // LISTADO GENERAL DE PRODUCTOS
        $isProductListQuestion =
            str_contains($message, 'muestrame los productos') ||
            str_contains($message, 'mostrar productos') ||
            str_contains($message, 'lista de productos') ||
            str_contains($message, 'listar productos') ||
            str_contains($message, 'que productos tenemos') ||
            str_contains($message, 'que productos hay') ||
            str_contains($message, 'muestrame el menu') ||
            str_contains($message, 'mostrar menu') ||
            $message === 'productos' ||
            $message === 'menu';

        if ($isProductListQuestion) {
            $products = DB::table('products')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->where('products.is_active', 1)
                ->where('products.is_saleable', 1)
                ->orderBy('categories.name')
                ->orderBy('products.name')
                ->get([
                    'products.id',
                    'products.name',
                    'products.price',
                    'products.promotional_price',
                    'products.stock',
                    'categories.name as category_name',
                ]);

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos activos disponibles actualmente.',
                    'intent' => 'product_list',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $lines = [];
            $currentCategory = null;

            foreach ($products as $product) {
                $category = $product->category_name ?: 'Sin categoría';

                if ($currentCategory !== $category) {
                    if ($currentCategory !== null) {
                        $lines[] = '';
                    }

                    $lines[] = $category . ':';
                    $currentCategory = $category;
                }

                $price = $product->promotional_price !== null
                    ? (float) $product->promotional_price
                    : (float) $product->price;

                $lines[] =
                    '- ' . $product->name .
                    ' | S/ ' . number_format($price, 2);
            }

            return [
                'response' =>
                    'Actualmente tenemos ' . $products->count() .
                    ' productos disponibles en la carta.',
                'intent' => 'product_list',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }

        // RECOMENDACIONES PARA MEJORAR LAS VENTAS
        $isSalesAdvice =
            str_contains($message, 'como puedo mejorar las ventas') ||
            str_contains($message, 'como mejorar las ventas') ||
            str_contains($message, 'mejorar mis ventas') ||
            str_contains($message, 'aumentar las ventas') ||
            str_contains($message, 'incrementar las ventas');

        if ($isSalesAdvice) {
            return [
                'response' =>
                    "Puedes mejorar las ventas aplicando estas estrategias:\n\n" .
                    "1. Promociona los platos más vendidos y crea combos atractivos.\n" .
                    "2. Ofrece promociones en los días u horarios con menos ventas.\n" .
                    "3. Incentiva la recompra de los clientes frecuentes.\n" .
                    "4. Publica promociones y platos destacados en redes sociales.\n" .
                    "5. Revisa periódicamente las ventas para identificar qué productos tienen mejor rendimiento.",
                'intent' => 'sales_advice',
            ];
        }

        // COMPARACIONES DE VENTAS
        $isComparison = str_contains($message, 'compara') ||
            str_contains($message, 'comparar') ||
            str_contains($message, 'comparado') ||
            str_contains($message, 'comparacion') ||
            str_contains($message, 'subieron') ||
            str_contains($message, 'bajaron');

        if ($isComparison) {
            $period1 = null;
            $period2 = null;

            if (str_contains($message, 'hoy') && str_contains($message, 'ayer')) {
                $period1 = $this->detectPeriod('hoy');
                $period2 = $this->detectPeriod('ayer');
            } elseif (
                str_contains($message, 'esta semana') &&
                (
                    str_contains($message, 'semana pasada') ||
                    str_contains($message, 'semana anterior')
                )
            ) {
                $period1 = $this->detectPeriod('esta semana');
                $period2 = $this->detectPeriod('semana pasada');
            } elseif (
                str_contains($message, 'este mes') &&
                (
                    str_contains($message, 'mes pasado') ||
                    str_contains($message, 'mes anterior')
                )
            ) {
                $period1 = $this->detectPeriod('este mes');
                $period2 = $this->detectPeriod('mes pasado');
            } elseif (str_contains($message, 'esta semana')) {
                $period1 = $this->detectPeriod('esta semana');
                $period2 = $this->detectPeriod('semana pasada');
            } elseif (str_contains($message, 'este mes')) {
                $period1 = $this->detectPeriod('este mes');
                $period2 = $this->detectPeriod('mes pasado');
            }

            if ($period1 && $period2) {
                $total1 = $this->getSalesTotal($period1);
                $total2 = $this->getSalesTotal($period2);

                $difference = $total1 - $total2;

                $percentage = $total2 > 0
                    ? (($difference / $total2) * 100)
                    : null;

                if ($difference > 0) {
                    $trend = 'aumentaron';
                } elseif ($difference < 0) {
                    $trend = 'disminuyeron';
                } else {
                    $trend = 'se mantuvieron iguales';
                }

                $response = 'Ventas ' . $period1['label'] . ': S/ ' .
                    number_format($total1, 2) . '. ' .
                    'Ventas ' . $period2['label'] . ': S/ ' .
                    number_format($total2, 2) . '. ';

                if ($difference == 0.0) {
                    $response .= 'Las ventas se mantuvieron iguales.';
                } else {
                    $response .= 'Las ventas ' . $trend .
                        ' en S/ ' . number_format(abs($difference), 2);

                    if ($percentage !== null) {
                        $response .= ' (' .
                            number_format(abs($percentage), 2) . '%)';
                    }

                    $response .= '.';
                }

                return [
                    'response' => $response,
                    'intent' => 'sales_comparison',
                    'current_period' => $period1['type'],
                    'previous_period' => $period2['type'],
                    'current_total' => $total1,
                    'previous_total' => $total2,
                    'difference' => $difference,
                    'percentage' => $percentage,
                    'trend' => $trend,
                ];
            }
        }
        // INTERPRETACION FLEXIBLE DE INTENCIONES
        $interpretedIntent = $this->interpretIntent($message);

        if ($interpretedIntent === 'best_sales_day') {
            $period = $this->detectPeriod($message);
            $day = $this->getBestSalesDay($period);

            if (!$day) {
                return [
                    'response' => 'No hay ventas completadas' .
                        ($period ? ' durante ' . $period['label'] : '') . '.',
                    'intent' => 'best_sales_day',
                    'period' => $period['type'] ?? null,
                ];
            }

            return [
                'response' => 'El día con mayor monto de ventas' .
                    ($period ? ' durante ' . $period['label'] : '') .
                    ' fue el ' . $day->date .
                    ', con S/ ' .
                    number_format((float) $day->total, 2) .
                    ' en ' . $day->orders_count .
                    ' pedido(s) completado(s).',
                'intent' => 'best_sales_day',
                'period' => $period['type'] ?? null,
                'date' => $day->date,
                'orders_count' => (int) $day->orders_count,
                'total' => (float) $day->total,
            ];
        }

        if ($interpretedIntent === 'best_sales_hour') {
            $period = $this->detectPeriod($message);
            $hour = $this->getBestSalesHour($period);

            if (!$hour) {
                return [
                    'response' => 'No hay ventas completadas' .
                        ($period ? ' durante ' . $period['label'] : '') . '.',
                    'intent' => 'best_sales_hour',
                    'period' => $period['type'] ?? null,
                ];
            }

            $hourLabel = str_pad(
                (string) $hour->hour,
                2,
                '0',
                STR_PAD_LEFT
            ) . ':00';

            return [
                'response' => 'La hora con mayor monto de ventas' .
                    ($period ? ' durante ' . $period['label'] : '') .
                    ' es ' . $hourLabel .
                    ', con S/ ' .
                    number_format((float) $hour->total, 2) .
                    ' en ' . $hour->orders_count .
                    ' pedido(s) completado(s).',
                'intent' => 'best_sales_hour',
                'period' => $period['type'] ?? null,
                'hour' => (int) $hour->hour,
                'orders_count' => (int) $hour->orders_count,
                'total' => (float) $hour->total,
            ];
        }
        // ANALISIS DE DIA Y HORA CON MAYORES VENTAS
        $isBestSalesDayQuestion =
            str_contains($message, 'que dia vendi mas') ||
            str_contains($message, 'que dia se vendio mas') ||
            str_contains($message, 'dia con mas ventas') ||
            str_contains($message, 'dia de mayores ventas') ||
            str_contains($message, 'mejor dia de ventas') ||
            str_contains($message, 'cual fue mi mejor dia');

        if ($isBestSalesDayQuestion) {
            $period = $this->detectPeriod($message);
            $day = $this->getBestSalesDay($period);

            if (!$day) {
                return [
                    'response' => 'No hay ventas completadas' .
                        ($period ? ' durante ' . $period['label'] : '') . '.',
                    'intent' => 'best_sales_day',
                    'period' => $period['type'] ?? null,
                ];
            }

            return [
                'response' => 'El día con mayor monto de ventas' .
                    ($period ? ' durante ' . $period['label'] : '') .
                    ' fue el ' . $day->date .
                    ', con S/ ' .
                    number_format((float) $day->total, 2) .
                    ' en ' . $day->orders_count .
                    ' pedido(s) completado(s).',
                'intent' => 'best_sales_day',
                'period' => $period['type'] ?? null,
                'date' => $day->date,
                'orders_count' => (int) $day->orders_count,
                'total' => (float) $day->total,
            ];
        }

        $isBestSalesHourQuestion =
            str_contains($message, 'a que hora vendo mas') ||
            str_contains($message, 'a que hora vendi mas') ||
            str_contains($message, 'hora con mas ventas') ||
            str_contains($message, 'hora de mayores ventas') ||
            str_contains($message, 'hora punta de ventas') ||
            str_contains($message, 'cual es la hora punta');

        if ($isBestSalesHourQuestion) {
            $period = $this->detectPeriod($message);
            $hour = $this->getBestSalesHour($period);

            if (!$hour) {
                return [
                    'response' => 'No hay ventas completadas' .
                        ($period ? ' durante ' . $period['label'] : '') . '.',
                    'intent' => 'best_sales_hour',
                    'period' => $period['type'] ?? null,
                ];
            }

            $hourLabel = str_pad(
                (string) $hour->hour,
                2,
                '0',
                STR_PAD_LEFT
            ) . ':00';

            return [
                'response' => 'La hora con mayor monto de ventas' .
                    ($period ? ' durante ' . $period['label'] : '') .
                    ' es ' . $hourLabel .
                    ', con S/ ' .
                    number_format((float) $hour->total, 2) .
                    ' en ' . $hour->orders_count .
                    ' pedido(s) completado(s).',
                'intent' => 'best_sales_hour',
                'period' => $period['type'] ?? null,
                'hour' => (int) $hour->hour,
                'orders_count' => (int) $hour->orders_count,
                'total' => (float) $hour->total,
            ];
        }
        // PREDICCION DE VENTAS CON MACHINE LEARNING
        if ($interpretedIntent === 'predict_sales') {

            $ml = app(\App\Services\MachineLearningService::class);

            $period = $this->detectPeriod($message);

            // Si no se indica una fecha, utilizar mañana.
            if (!$period || !isset($period['start'])) {
                $fecha = now()->addDay()->format('Y-m-d');
            } else {
                $fecha = $period['start']->format('Y-m-d');
            }

            $resultado = $ml->predictSales($fecha);

            if (!($resultado['success'] ?? false)) {
                return [
                    'response' => 'No pude obtener la predicción de ventas en este momento.',
                    'intent' => 'predict_sales',
                    'error' => $resultado['error'] ?? 'Error desconocido.'
                ];
            }

            $fechaFormateada = \Carbon\Carbon::parse($resultado['fecha'])
                ->format('d/m/Y');

            $respuesta =
                'Según el modelo de Machine Learning, ' .
                'la venta estimada para el ' . $fechaFormateada .
                ' es de S/ ' .
                number_format(
                    (float) $resultado['venta_estimada'],
                    2
                ) . '.';

            if ($resultado['experimental'] ?? false) {
                $respuesta .=
                    ' Esta predicción es experimental porque el modelo ' .
                    'actualmente cuenta con solo ' .
                    $resultado['dias_entrenamiento'] .
                    ' día(s) de historial.';
            }

            return [
                'response' => $respuesta,
                'intent' => 'predict_sales',
                'prediction' => $resultado
            ];
        }
        // ADAPTACION DE INTENCIONES FLEXIBLES DE PEDIDOS
        if (
            $interpretedIntent === 'orders_count' ||
            $interpretedIntent === 'orders_pending' ||
            $interpretedIntent === 'orders_completed'
        ) {
            $period = $this->detectPeriod($message);

            // Si no se especifica periodo, consultar hoy.
            if (!$period) {
                $period = $this->detectPeriod('hoy');
            }

            $status = null;

            if ($interpretedIntent === 'orders_pending') {
                $status = 'pending';
            } elseif ($interpretedIntent === 'orders_completed') {
                $status = 'completed';
            }

            $count = $this->getOrdersCount(
                $period,
                $status
            );

            if ($status === 'pending') {
                $description = 'pedido(s) pendiente(s)';
            } elseif ($status === 'completed') {
                $description = 'pedido(s) completado(s)';
            } else {
                $description = 'pedido(s)';
            }

            return [
                'response' =>
                    'Hay ' . $count . ' ' .
                    $description . ' durante ' .
                    $period['label'] . '.',
                'intent' => $interpretedIntent,
                'period' => $period['type'],
                'status' => $status,
                'count' => $count,
            ];
        }
        // ADAPTACION DE INTENCIONES FLEXIBLES DE CAJA
        if (
            $interpretedIntent === 'cash_register_summary' ||
            $interpretedIntent === 'cash_register_expected_cash' ||
            $interpretedIntent === 'cash_register_sales' ||
            $interpretedIntent === 'cash_register_expenses' ||
            $interpretedIntent === 'cash_register_yape' ||
            $interpretedIntent === 'cash_register_plin' ||
            $interpretedIntent === 'cash_register_card' ||
            $interpretedIntent === 'cash_register_transfer'
        ) {
            $cash = $this->getOpenCashRegisterSummary();

            if (!$cash) {
                return [
                    'response' => 'Actualmente no hay ninguna caja abierta.',
                    'intent' => 'cash_register_closed',
                ];
            }

            if ($interpretedIntent === 'cash_register_expected_cash') {
                return [
                    'response' =>
                        'El efectivo esperado en la caja #' .
                        $cash['id'] . ' es S/ ' .
                        number_format($cash['expected_cash'], 2) .
                        '. Se calcula con S/ ' .
                        number_format($cash['opening_amount'], 2) .
                        ' de apertura + S/ ' .
                        number_format($cash['payments']['cash'], 2) .
                        ' de ventas en efectivo - S/ ' .
                        number_format($cash['expenses'], 2) .
                        ' de gastos.',
                    'intent' => 'cash_register_expected_cash',
                    'cash_register_id' => $cash['id'],
                    'amount' => $cash['expected_cash'],
                ];
            }

            if ($interpretedIntent === 'cash_register_sales') {
                return [
                    'response' =>
                        'La caja #' . $cash['id'] .
                        ' registra S/ ' .
                        number_format($cash['sales_total'], 2) .
                        ' en ventas completadas, correspondientes a ' .
                        $cash['orders_count'] . ' pedido(s).',
                    'intent' => 'cash_register_sales',
                    'cash_register_id' => $cash['id'],
                    'amount' => $cash['sales_total'],
                    'orders_count' => $cash['orders_count'],
                ];
            }

            if ($interpretedIntent === 'cash_register_expenses') {
                return [
                    'response' =>
                        'La caja #' . $cash['id'] .
                        ' registra S/ ' .
                        number_format($cash['expenses'], 2) .
                        ' en gastos.',
                    'intent' => 'cash_register_expenses',
                    'cash_register_id' => $cash['id'],
                    'amount' => $cash['expenses'],
                ];
            }

            $paymentIntents = [
                'cash_register_yape' => ['yape', 'Yape'],
                'cash_register_plin' => ['plin', 'Plin'],
                'cash_register_card' => ['card', 'Tarjeta'],
                'cash_register_transfer' => ['transfer', 'Transferencia'],
            ];

            if (isset($paymentIntents[$interpretedIntent])) {
                [$key, $label] = $paymentIntents[$interpretedIntent];

                $amount = (float) (
                    $cash['payments'][$key] ?? 0
                );

                return [
                    'response' =>
                        'La caja #' . $cash['id'] .
                        ' registra S/ ' .
                        number_format($amount, 2) .
                        ' en pagos por ' . $label . '.',
                    'intent' => $interpretedIntent,
                    'cash_register_id' => $cash['id'],
                    'payment_method' => $key,
                    'amount' => $amount,
                ];
            }

            return [
                'response' =>
                    'Caja #' . $cash['id'] .
                    ' abierta por ' .
                    ($cash['user'] ?? 'usuario no identificado') .
                    ' desde ' . $cash['opening_time'] . ".\n" .
                    'Monto de apertura: S/ ' .
                    number_format($cash['opening_amount'], 2) . ".\n" .
                    'Ventas completadas: S/ ' .
                    number_format($cash['sales_total'], 2) . ".\n" .
                    'Efectivo: S/ ' .
                    number_format($cash['payments']['cash'], 2) . ".\n" .
                    'Tarjeta: S/ ' .
                    number_format($cash['payments']['card'], 2) . ".\n" .
                    'Yape: S/ ' .
                    number_format($cash['payments']['yape'], 2) . ".\n" .
                    'Plin: S/ ' .
                    number_format($cash['payments']['plin'], 2) . ".\n" .
                    'Transferencia: S/ ' .
                    number_format($cash['payments']['transfer'], 2) . ".\n" .
                    'Gastos: S/ ' .
                    number_format($cash['expenses'], 2) . ".\n" .
                    'Efectivo esperado: S/ ' .
                    number_format($cash['expected_cash'], 2) . '.',
                'intent' => 'cash_register_summary',
                'cash_register' => $cash,
            ];
        }
        // ADAPTACION DE INTENCIONES FLEXIBLES DE INVENTARIO
        if ($interpretedIntent === 'out_of_stock_all') {
            $products = $this->getOutOfStockProducts();

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos agotados actualmente.',
                    'intent' => 'out_of_stock_all',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $lines = [];

            foreach ($products as $product) {
                $lines[] =
                    '- ' . $product->name .
                    ' | Stock: ' . $product->stock;
            }

            return [
                'response' =>
                    'Lista completa de productos agotados (' .
                    $products->count() . "):\n" .
                    implode("\n", $lines),
                'intent' => 'out_of_stock_all',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }
        if ($interpretedIntent === 'out_of_stock') {
            $products = $this->getOutOfStockProducts();

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos agotados actualmente.',
                    'intent' => 'out_of_stock',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $limit = 5;
            $visibleProducts = $products->take($limit);
            $remaining = max(0, $products->count() - $limit);
            $lines = [];

            foreach ($visibleProducts as $product) {
                $lines[] =
                    '- ' . $product->name .
                    ' | Stock: ' . $product->stock;
            }

            $response =
                'Actualmente hay ' . $products->count() .
                " producto(s) agotado(s):\n" .
                implode("\n", $lines);

            if ($remaining > 0) {
                $response .=
                    "\n... y " . $remaining .
                    ' producto(s) agotado(s) más. ' .
                    'Puedes preguntarme "muéstrame todos los productos agotados" ' .
                    'para ver la lista completa.';
            }

            return [
                'response' => $response,
                'intent' => 'out_of_stock',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }

        if ($interpretedIntent === 'low_stock') {
            $products = $this->getLowStockProducts(1, 5);

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos con stock bajo actualmente.',
                    'intent' => 'low_stock',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $lines = [];

            foreach ($products as $product) {
                $lines[] =
                    '- ' . $product->name .
                    ' | Stock: ' . $product->stock;
            }

            return [
                'response' =>
                    'Actualmente hay ' . $products->count() .
                    " producto(s) con stock bajo:\n" .
                    implode("\n", $lines),
                'intent' => 'low_stock',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }
        // ADAPTACION DE INTENCIONES FLEXIBLES DE CLIENTES
        if ($interpretedIntent === 'clients_count') {
            $count = DB::table('clients')->count();

            return [
                'response' => 'Actualmente hay ' . $count .
                    ' cliente(s) registrado(s).',
                'intent' => 'clients_count',
                'count' => $count,
            ];
        }

        if ($interpretedIntent === 'top_clients') {
            $clients = $this->getTopClients(5);

            if ($clients->isEmpty()) {
                return [
                    'response' => 'No hay clientes con compras completadas registradas.',
                    'intent' => 'top_clients',
                    'clients' => [],
                ];
            }

            $lines = [];
            $position = 1;

            foreach ($clients as $client) {
                $lines[] =
                    $position . '. ' . $client->name .
                    ' | ' . $client->orders_count . ' pedido(s)' .
                    ' | S/ ' .
                    number_format((float) $client->total_spent, 2);

                $position++;
            }

            return [
                'response' =>
                    "Clientes con mayor monto de compras completadas:\n" .
                    implode("\n", $lines),
                'intent' => 'top_clients',
                'clients' => $clients->toArray(),
            ];
        }
        // CONSULTAS DE CLIENTES
        if (
            str_contains($message, 'cuantos clientes') ||
            str_contains($message, 'cantidad de clientes') ||
            str_contains($message, 'clientes registrados')
        ) {
            $count = DB::table('clients')->count();

            return [
                'response' => 'Actualmente hay ' . $count .
                    ' cliente(s) registrado(s).',
                'intent' => 'clients_count',
                'count' => $count,
            ];
        }

        if (
            str_contains($message, 'mejores clientes') ||
            str_contains($message, 'clientes que mas compran') ||
            str_contains($message, 'cliente que mas compra') ||
            str_contains($message, 'top clientes')
        ) {
            $clients = $this->getTopClients(5);

            if ($clients->isEmpty()) {
                return [
                    'response' => 'No hay clientes con compras registradas.',
                    'intent' => 'top_clients',
                    'clients' => [],
                ];
            }

            $lines = [];
            $position = 1;

            foreach ($clients as $client) {
                $lines[] =
                    $position . '. ' . $client->name .
                    ' | ' . $client->orders_count . ' pedido(s)' .
                    ' | S/ ' .
                    number_format((float) $client->total_spent, 2);

                $position++;
            }

            return [
                'response' => "Clientes con mayor monto de compras completadas:\n" .
                    implode("\n", $lines),
                'intent' => 'top_clients',
                'clients' => $clients->toArray(),
            ];
        }

        $clientPrefixes = [
            'busca al cliente ',
            'buscar cliente ',
            'busca cliente ',
            'informacion del cliente ',
            'informacion de cliente ',
            'cuanto ha comprado ',
            'cuanto compro ',
            'compras de ',
        ];

        foreach ($clientPrefixes as $prefix) {
            if (str_contains($message, $prefix)) {
                $position = mb_strpos($message, $prefix);

                $search = trim(
                    mb_substr(
                        $message,
                        $position + mb_strlen($prefix)
                    )
                );

                $clients = $this->findClients($search);

                if ($clients->isEmpty()) {
                    return [
                        'response' => 'No encontré clientes que coincidan con "' .
                            $search . '".',
                        'intent' => 'client_not_found',
                        'search' => $search,
                    ];
                }

                // No asumir qué cliente quiso decir si hay varias coincidencias.
                if ($clients->count() > 1) {
                    $lines = [];

                    foreach ($clients as $client) {
                        $lines[] =
                            '#' . $client->id .
                            ' | ' . $client->name .
                            ' | Documento: ' .
                            ($client->document_number ?? 'Sin documento');
                    }

                    return [
                        'response' => 'Encontré ' . $clients->count() .
                            " clientes que coinciden con la búsqueda:\n" .
                            implode("\n", $lines) .
                            "\nIndica el nombre completo o documento para precisar la consulta.",
                        'intent' => 'client_multiple_matches',
                        'search' => $search,
                        'clients' => $clients->toArray(),
                    ];
                }

                $client = $clients->first();
                $stats = $this->getClientPurchaseStats($client->id);

                return [
                    'response' =>
                        'Cliente: ' . $client->name . ".\n" .
                        'Documento: ' .
                        ($client->document_number ?? 'Sin documento') . ".\n" .
                        'Teléfono: ' .
                        ($client->phone ?? 'Sin teléfono') . ".\n" .
                        'Pedidos completados: ' .
                        $stats['orders_count'] . ".\n" .
                        'Total comprado: S/ ' .
                        number_format($stats['total_spent'], 2) . ".\n" .
                        'Última compra: ' .
                        ($stats['last_purchase'] ?? 'Sin compras registradas') . '.',
                    'intent' => 'client_details',
                    'client' => $client,
                    'stats' => $stats,
                ];
            }
        }
        // CONSULTAS DE DELIVERY
        $isDeliveryQuestion =
            str_contains($message, 'delivery') ||
            str_contains($message, 'domicilio') ||
            str_contains($message, 'entrega');

        if ($isDeliveryQuestion) {
            $period = $this->detectPeriod($message);

            // Total cobrado específicamente por concepto de delivery.
            if (
                str_contains($message, 'cuanto cobramos') ||
                str_contains($message, 'cuanto se cobro') ||
                str_contains($message, 'cobrado por delivery') ||
                str_contains($message, 'cobros de delivery')
            ) {
                $total = $this->getDeliveryFeesTotal($period);

                return [
                    'response' => 'El total cobrado por concepto de delivery' .
                        ($period ? ' durante ' . $period['label'] : '') .
                        ' es S/ ' . number_format($total, 2) . '.',
                    'intent' => 'delivery_fees',
                    'period' => $period['type'] ?? null,
                    'total' => $total,
                ];
            }

            // Delivery pendientes.
            if (
                str_contains($message, 'pendiente') ||
                str_contains($message, 'pendientes') ||
                str_contains($message, 'falta entregar') ||
                str_contains($message, 'faltan entregar')
            ) {
                $deliveries = $this->getDeliveries('pending', $period);

                if ($deliveries->isEmpty()) {
                    return [
                        'response' => 'No hay deliveries pendientes' .
                            ($period ? ' para ' . $period['label'] : '') . '.',
                        'intent' => 'deliveries_pending',
                        'count' => 0,
                        'deliveries' => [],
                    ];
                }

                $lines = [];

                foreach ($deliveries as $delivery) {
                    $lines[] =
                        '#' . $delivery->id .
                        ' | Cliente: ' . $delivery->client_name .
                        ' | Dirección: ' . ($delivery->address ?? 'Sin dirección') .
                        ' | Pedido: S/ ' .
                        number_format((float) $delivery->order_total, 2) .
                        ' | Repartidor: ' .
                        ($delivery->driver_name ?? 'Sin asignar');
                }

                return [
                    'response' => 'Hay ' . $deliveries->count() .
                        " delivery(s) pendiente(s):\n" .
                        implode("\n", $lines),
                    'intent' => 'deliveries_pending',
                    'count' => $deliveries->count(),
                    'deliveries' => $deliveries->toArray(),
                ];
            }

            // Delivery entregados.
            if (
                str_contains($message, 'entregado') ||
                str_contains($message, 'entregados') ||
                str_contains($message, 'completado') ||
                str_contains($message, 'completados')
            ) {
                $deliveries = $this->getDeliveries('delivered', $period);

                if ($deliveries->isEmpty()) {
                    return [
                        'response' => 'No hay deliveries entregados' .
                            ($period ? ' para ' . $period['label'] : '') . '.',
                        'intent' => 'deliveries_delivered',
                        'count' => 0,
                        'deliveries' => [],
                    ];
                }

                $lines = [];

                foreach ($deliveries as $delivery) {
                    $lines[] =
                        '#' . $delivery->id .
                        ' | Cliente: ' . $delivery->client_name .
                        ' | Pedido: S/ ' .
                        number_format((float) $delivery->order_total, 2) .
                        ' | Pago: ' .
                        ($delivery->payment_method ?? 'Sin método') .
                        ' | Repartidor: ' .
                        ($delivery->driver_name ?? 'Sin asignar') .
                        ' | Entregado: ' .
                        ($delivery->delivered_at ?? 'Sin fecha');
                }

                return [
                    'response' => 'Hay ' . $deliveries->count() .
                        " delivery(s) entregado(s):\n" .
                        implode("\n", $lines),
                    'intent' => 'deliveries_delivered',
                    'count' => $deliveries->count(),
                    'deliveries' => $deliveries->toArray(),
                ];
            }

            // Consulta general de deliveries.
            $deliveries = $this->getDeliveries(null, $period);

            if ($deliveries->isEmpty()) {
                return [
                    'response' => 'No hay deliveries registrados' .
                        ($period ? ' para ' . $period['label'] : '') . '.',
                    'intent' => 'deliveries',
                    'count' => 0,
                    'deliveries' => [],
                ];
            }

            $pending = $deliveries
                ->where('status', 'pending')
                ->count();

            $delivered = $deliveries
                ->where('status', 'delivered')
                ->count();

            return [
                'response' => 'Hay ' . $deliveries->count() .
                    ' delivery(s)' .
                    ($period ? ' de ' . $period['label'] : '') .
                    ': ' . $pending . ' pendiente(s) y ' .
                    $delivered . ' entregado(s).',
                'intent' => 'deliveries',
                'count' => $deliveries->count(),
                'pending' => $pending,
                'delivered' => $delivered,
                'deliveries' => $deliveries->toArray(),
            ];
        }
        // CONSULTAS DE RESERVAS
        $isReservationQuestion =
            str_contains($message, 'reserva') ||
            str_contains($message, 'reservas');

        if ($isReservationQuestion) {
            $period = $this->detectPeriod($message);

            // Si no se indica un período, consultar hoy.
            if (!$period) {
                $period = $this->detectPeriod('hoy');
            }

            $reservations = $this->getReservationsByPeriod($period);

            if ($reservations->isEmpty()) {
                return [
                    'response' => 'No hay reservas registradas para ' .
                        $period['label'] . '.',
                    'intent' => 'reservations_period',
                    'period' => $period['type'],
                    'count' => 0,
                    'reservations' => [],
                ];
            }

            $lines = [];

            foreach ($reservations as $reservation) {
                $lines[] =
                    '#' . $reservation->id .
                    ' | ' . $reservation->client_name .
                    ' | ' . $reservation->reservation_time .
                    ' | ' . $reservation->people . ' persona(s)' .
                    ' | ' . ($reservation->table_name ?? 'Sin mesa') .
                    ' | Estado: ' . $reservation->status;
            }

            return [
                'response' => 'Reservas de ' . $period['label'] .
                    ' (' . $reservations->count() . "):\n" .
                    implode("\n", $lines),
                'intent' => 'reservations_period',
                'period' => $period['type'],
                'count' => $reservations->count(),
                'reservations' => $reservations->toArray(),
            ];
        }

        // CONSULTAS DE MESAS
        $isTableQuestion =
            str_contains($message, 'mesa') ||
            str_contains($message, 'mesas');

        if ($isTableQuestion) {
            // Ejemplo: "mesa para 4 personas"
            if (
                preg_match(
                    '/(?:mesa|mesas).{0,20}(?:para|de)\s+(\d+)\s+persona/',
                    $message,
                    $matches
                )
            ) {
                $people = (int) $matches[1];

                $tables = $this->getAvailableTablesForPeople($people);

                if ($tables->isEmpty()) {
                    return [
                        'response' => 'No hay una mesa disponible con capacidad para ' .
                            $people . ' persona(s) en este momento.',
                        'intent' => 'available_tables_capacity',
                        'people' => $people,
                        'count' => 0,
                        'tables' => [],
                    ];
                }

                $lines = [];

                foreach ($tables as $table) {
                    $lines[] =
                        $table->name .
                        ' (' . $table->seats . ' personas, ' .
                        $table->area_name . ')';
                }

                return [
                    'response' => 'Hay ' . $tables->count() .
                        ' mesa(s) disponible(s) con capacidad para ' .
                        $people . " persona(s):\n" .
                        implode("\n", $lines),
                    'intent' => 'available_tables_capacity',
                    'people' => $people,
                    'count' => $tables->count(),
                    'tables' => $tables->toArray(),
                ];
            }

            if (
                str_contains($message, 'disponible') ||
                str_contains($message, 'disponibles') ||
                str_contains($message, 'libre') ||
                str_contains($message, 'libres')
            ) {
                $tables = $this->getTables('available');

                if ($tables->isEmpty()) {
                    return [
                        'response' => 'No hay mesas disponibles actualmente.',
                        'intent' => 'available_tables',
                        'count' => 0,
                        'tables' => [],
                    ];
                }

                $lines = [];

                foreach ($tables as $table) {
                    $lines[] =
                        $table->name .
                        ' (' . $table->seats . ' personas, ' .
                        $table->area_name . ')';
                }

                return [
                    'response' => 'Actualmente hay ' . $tables->count() .
                        " mesa(s) disponible(s):\n" .
                        implode("\n", $lines),
                    'intent' => 'available_tables',
                    'count' => $tables->count(),
                    'tables' => $tables->toArray(),
                ];
            }

            $tables = $this->getTables();

            return [
                'response' => 'El restaurante tiene ' .
                    $tables->count() . ' mesa(s) registradas.',
                'intent' => 'tables_total',
                'count' => $tables->count(),
                'tables' => $tables->toArray(),
            ];
        }
        // CONSULTAS DE GASTOS POR PERIODO
        $isExpenseQuestion =
            str_contains($message, 'gasto') ||
            str_contains($message, 'gastos') ||
            str_contains($message, 'gastamos') ||
            str_contains($message, 'gastado') ||
            str_contains($message, 'gasto hoy') ||
            str_contains($message, 'se gasto');

        if (
            $isExpenseQuestion &&
            !str_contains($message, 'gastos de la caja') &&
            !str_contains($message, 'gasto de la caja')
        ) {
            $period = $this->detectPeriod($message);

            // Si no especifica período, se toma hoy.
            if (!$period) {
                $period = $this->detectPeriod('hoy');
            }

            $total = $this->getExpensesTotal($period);

            return [
                'response' => 'Los gastos de ' .
                    $period['label'] . ' suman S/ ' .
                    number_format($total, 2) . '.',
                'intent' => 'expenses_period',
                'period' => $period['type'],
                'period_label' => $period['label'],
                'total' => $total,
            ];
        }
        // CONSULTAS DE CAJA
        $isCashRegisterQuestion =
            str_contains($message, 'caja abierta') ||
            str_contains($message, 'estado de caja') ||
            str_contains($message, 'resumen de caja') ||
            str_contains($message, 'efectivo en caja') ||
            str_contains($message, 'dinero en caja') ||
            str_contains($message, 'cuanto hay en caja') ||
            str_contains($message, 'cuanto vendio la caja') ||
            str_contains($message, 'ventas de la caja') ||
            str_contains($message, 'gastos de la caja') ||
            str_contains($message, 'gasto de la caja') ||
            str_contains($message, 'yape en caja') ||
            str_contains($message, 'yape hay en caja') ||
            str_contains($message, 'yape tiene la caja') ||
            str_contains($message, 'yape de la caja') ||
            str_contains($message, 'plin en caja') ||
            str_contains($message, 'plin hay en caja') ||
            str_contains($message, 'plin tiene la caja') ||
            str_contains($message, 'plin de la caja') ||
            str_contains($message, 'tarjeta en caja') ||
            str_contains($message, 'tarjeta hay en caja') ||
            str_contains($message, 'tarjeta tiene la caja') ||
            str_contains($message, 'tarjeta de la caja') ||
            str_contains($message, 'transferencia en caja') ||
            str_contains($message, 'transferencia hay en caja') ||
            str_contains($message, 'transferencia tiene la caja') ||
            str_contains($message, 'transferencia de la caja');

        if ($isCashRegisterQuestion) {
            $cash = $this->getOpenCashRegisterSummary();

            if (!$cash) {
                return [
                    'response' => 'Actualmente no hay ninguna caja abierta.',
                    'intent' => 'cash_register_closed',
                ];
            }

            // Consultas específicas por método de pago.
            $paymentLabels = [
                'yape' => 'Yape',
                'plin' => 'Plin',
                'tarjeta' => 'Tarjeta',
                'transferencia' => 'Transferencia',
            ];

            $paymentKeys = [
                'yape' => 'yape',
                'plin' => 'plin',
                'tarjeta' => 'card',
                'transferencia' => 'transfer',
            ];

            foreach ($paymentLabels as $word => $label) {
                if (str_contains($message, $word)) {
                    $amount = $cash['payments'][$paymentKeys[$word]];

                    return [
                        'response' => 'La caja #' . $cash['id'] .
                            ' registra S/ ' . number_format($amount, 2) .
                            ' en pagos por ' . $label . '.',
                        'intent' => 'cash_register_payment',
                        'cash_register_id' => $cash['id'],
                        'payment_method' => $paymentKeys[$word],
                        'amount' => $amount,
                    ];
                }
            }

            if (
                str_contains($message, 'gastos de la caja') ||
                str_contains($message, 'gasto de la caja')
            ) {
                return [
                    'response' => 'La caja #' . $cash['id'] .
                        ' registra S/ ' .
                        number_format($cash['expenses'], 2) .
                        ' en gastos.',
                    'intent' => 'cash_register_expenses',
                    'cash_register_id' => $cash['id'],
                    'expenses' => $cash['expenses'],
                ];
            }

            if (
                str_contains($message, 'efectivo en caja') ||
                str_contains($message, 'dinero en caja') ||
                str_contains($message, 'cuanto hay en caja')
            ) {
                return [
                    'response' => 'El efectivo esperado en la caja #' .
                        $cash['id'] . ' es S/ ' .
                        number_format($cash['expected_cash'], 2) .
                        '. Se calcula con S/ ' .
                        number_format($cash['opening_amount'], 2) .
                        ' de apertura + S/ ' .
                        number_format($cash['payments']['cash'], 2) .
                        ' de ventas en efectivo - S/ ' .
                        number_format($cash['expenses'], 2) .
                        ' de gastos.',
                    'intent' => 'cash_register_expected_cash',
                    'cash_register_id' => $cash['id'],
                    'expected_cash' => $cash['expected_cash'],
                ];
            }

            if (
                str_contains($message, 'cuanto vendio la caja') ||
                str_contains($message, 'ventas de la caja')
            ) {
                return [
                    'response' => 'La caja #' . $cash['id'] .
                        ' registra S/ ' .
                        number_format($cash['sales_total'], 2) .
                        ' en ventas completadas, correspondientes a ' .
                        $cash['orders_count'] . ' pedido(s).',
                    'intent' => 'cash_register_sales',
                    'cash_register_id' => $cash['id'],
                    'sales_total' => $cash['sales_total'],
                    'orders_count' => $cash['orders_count'],
                ];
            }

            return [
                'response' =>
                    'Caja #' . $cash['id'] . ' abierta por ' .
                    ($cash['user'] ?? 'usuario no identificado') .
                    ' desde ' . $cash['opening_time'] . ".\n" .
                    'Monto de apertura: S/ ' .
                    number_format($cash['opening_amount'], 2) . ".\n" .
                    'Ventas completadas: S/ ' .
                    number_format($cash['sales_total'], 2) . ".\n" .
                    'Efectivo: S/ ' .
                    number_format($cash['payments']['cash'], 2) . ".\n" .
                    'Tarjeta: S/ ' .
                    number_format($cash['payments']['card'], 2) . ".\n" .
                    'Yape: S/ ' .
                    number_format($cash['payments']['yape'], 2) . ".\n" .
                    'Plin: S/ ' .
                    number_format($cash['payments']['plin'], 2) . ".\n" .
                    'Transferencia: S/ ' .
                    number_format($cash['payments']['transfer'], 2) . ".\n" .
                    'Gastos: S/ ' .
                    number_format($cash['expenses'], 2) . ".\n" .
                    'Efectivo esperado: S/ ' .
                    number_format($cash['expected_cash'], 2) . '.',
                'intent' => 'cash_register_summary',
                'cash_register' => $cash,
            ];
        }
        // CONSULTAS DE KARDEX Y MOVIMIENTOS DE INVENTARIO
        $isInventoryMovementQuestion =
            str_contains($message, 'kardex') ||
            str_contains($message, 'movimientos de inventario') ||
            str_contains($message, 'movimiento de inventario') ||
            str_contains($message, 'ultimos movimientos') ||
            str_contains($message, 'movimientos de stock') ||
            str_contains($message, 'historial de stock');

        if ($isInventoryMovementQuestion) {
            $product = null;

            // Intentar detectar si la pregunta menciona un producto.
            $activeProducts = DB::table('products')
                ->where('is_active', 1)
                ->get(['id', 'name']);

            foreach ($activeProducts as $candidate) {
                $normalizedProductName = $this->normalize($candidate->name);

                if (str_contains($message, $normalizedProductName)) {
                    $product = $candidate;
                    break;
                }
            }

            $movements = $this->getInventoryMovements(
                $product?->id,
                10
            );

            if ($movements->isEmpty()) {
                return [
                    'response' => $product
                        ? 'No hay movimientos de inventario registrados para ' . $product->name . '.'
                        : 'No hay movimientos de inventario registrados.',
                    'intent' => 'inventory_movements',
                    'product' => $product?->name,
                    'movements' => [],
                ];
            }

            $lines = [];

            foreach ($movements as $movement) {
                $typeLabel = match ($movement->type) {
                    'sale' => 'Venta',
                    'adjustment_in' => 'Ajuste de entrada',
                    'adjustment_out' => 'Ajuste de salida',
                    default => $movement->type,
                };

                $lines[] =
                    $movement->product_name .
                    ' | ' . $typeLabel .
                    ' | ' . $movement->old_stock .
                    ' -> ' . $movement->new_stock .
                    ' | Cantidad: ' . $movement->quantity .
                    ' | ' . $movement->created_at;
            }

            return [
                'response' => ($product
                    ? 'Últimos movimientos de ' . $product->name . ":\n"
                    : "Últimos movimientos de inventario:\n") .
                    implode("\n", $lines),
                'intent' => 'inventory_movements',
                'product' => $product?->name,
                'count' => $movements->count(),
                'movements' => $movements->toArray(),
            ];
        }
        // CONSULTAS DE INVENTARIO Y STOCK
        $isOutOfStockQuestion =
            str_contains($message, 'sin stock') ||
            str_contains($message, 'agotado') ||
            str_contains($message, 'agotados') ||
            str_contains($message, 'agotada') ||
            str_contains($message, 'agotadas') ||
            str_contains($message, 'stock cero');

        if ($isOutOfStockQuestion) {
            $products = $this->getOutOfStockProducts();

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos agotados actualmente.',
                    'intent' => 'out_of_stock',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $names = $products->pluck('name')->toArray();

            return [
                'response' => 'Actualmente hay ' . $products->count() .
                    ' producto(s) agotado(s): ' .
                    implode(', ', $names) . '.',
                'intent' => 'out_of_stock',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }

        $isLowStockQuestion =
            str_contains($message, 'stock bajo') ||
            str_contains($message, 'poco stock') ||
            str_contains($message, 'poca existencia') ||
            str_contains($message, 'pocas existencias') ||
            str_contains($message, 'por agotarse') ||
            str_contains($message, 'se estan acabando');

        if ($isLowStockQuestion) {
            $products = $this->getLowStockProducts();

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay productos con stock bajo actualmente.',
                    'intent' => 'low_stock',
                    'count' => 0,
                    'products' => [],
                ];
            }

            $lines = [];

            foreach ($products as $product) {
                $lines[] = $product->name .
                    ' (' . $product->stock . ' disponible(s))';
            }

            return [
                'response' => 'Productos con stock bajo: ' .
                    implode(', ', $lines) . '.',
                'intent' => 'low_stock',
                'count' => $products->count(),
                'products' => $products->toArray(),
            ];
        }

        // Consulta del stock de un producto específico.
        $stockPrefixes = [
            'cuanto stock tiene ',
            'cuanto stock hay de ',
            'cuantas unidades hay de ',
            'cuantas existencias hay de ',
            'stock de ',
            'existencias de ',
        ];

        foreach ($stockPrefixes as $prefix) {
            if (str_contains($message, $prefix)) {
                $position = mb_strpos($message, $prefix);
                $productName = trim(
                    mb_substr(
                        $message,
                        $position + mb_strlen($prefix)
                    )
                );

                $product = $this->findProductByName($productName);

                if (!$product) {
                    return [
                        'response' => 'No encontré un producto activo llamado "' .
                            $productName . '".',
                        'intent' => 'product_stock_not_found',
                        'product' => $productName,
                    ];
                }

                $stock = (float) $product->stock;

                if ($stock <= 0) {
                    $status = 'agotado';
                } elseif ($stock <= 5) {
                    $status = 'stock bajo';
                } else {
                    $status = 'disponible';
                }

                return [
                    'response' => $product->name .
                        ' tiene ' . $product->stock .
                        ' unidad(es) en stock. Estado: ' .
                        $status . '.',
                    'intent' => 'product_stock',
                    'product_id' => $product->id,
                    'product' => $product->name,
                    'stock' => $stock,
                    'status' => $status,
                ];
            }
        }
        // CONSULTAS DE PEDIDOS
        $isOrdersQuestion =
            str_contains($message, 'pedido') ||
            str_contains($message, 'pedidos');

        if ($isOrdersQuestion) {
            $orderPeriod = $this->detectPeriod($message);

            // Si no indica período, se toma hoy por defecto.
            if (!$orderPeriod) {
                $orderPeriod = $this->detectPeriod('hoy');
            }

            $status = null;
            $statusLabel = '';

            if (
                str_contains($message, 'pendiente') ||
                str_contains($message, 'pendientes')
            ) {
                $status = 'pending';
                $statusLabel = ' pendientes';
            } elseif (
                str_contains($message, 'completado') ||
                str_contains($message, 'completados') ||
                str_contains($message, 'completada') ||
                str_contains($message, 'completadas') ||
                str_contains($message, 'finalizado') ||
                str_contains($message, 'finalizados')
            ) {
                $status = 'completed';
                $statusLabel = ' completados';
            }

            $count = $this->getOrdersCount($orderPeriod, $status);

            return [
                'response' => 'Hay ' . $count .
                    ' pedido(s)' . $statusLabel . ' ' .
                    $orderPeriod['label'] . '.',
                'intent' => 'orders_count',
                'period' => $orderPeriod['type'],
                'status' => $status,
                'count' => $count,
            ];
        }
        // ANALISIS DE PRODUCTOS VENDIDOS
        $isProductRankingQuestion =
            str_contains($message, 'mas vendido') ||
            str_contains($message, 'mas vendidos') ||
            str_contains($message, 'menos vendido') ||
            str_contains($message, 'menos vendidos') ||
            str_contains($message, 'producto estrella') ||
            str_contains($message, 'plato estrella') ||
            str_contains($message, 'ranking de productos') ||
            str_contains($message, 'ranking de platos') ||
            str_contains($message, 'productos que mas se venden') ||
            str_contains($message, 'platos que mas se venden') ||
            str_contains($message, 'productos que menos se venden') ||
            str_contains($message, 'platos que menos se venden');

        if ($isProductRankingQuestion) {
            $productPeriod = $this->detectPeriod($message);

            // Si no especifica período, se utiliza el mes actual.
            if (!$productPeriod) {
                $productPeriod = $this->detectPeriod('este mes');
            }

            $ascending =
                str_contains($message, 'menos vendido') ||
                str_contains($message, 'menos vendidos') ||
                str_contains($message, 'menos se venden');

            $limit = 1;

            if (
                str_contains($message, 'ranking') ||
                str_contains($message, '5 mas') ||
                str_contains($message, 'cinco mas') ||
                str_contains($message, '5 menos') ||
                str_contains($message, 'cinco menos') ||
                str_contains($message, 'productos que mas') ||
                str_contains($message, 'platos que mas') ||
                str_contains($message, 'productos que menos') ||
                str_contains($message, 'platos que menos')
            ) {
                $limit = 5;
            }

            $products = $this->getProductRanking(
                $productPeriod,
                $limit,
                $ascending
            );

            if ($products->isEmpty()) {
                return [
                    'response' => 'No hay ventas completadas ' .
                        $productPeriod['label'] .
                        ' para realizar el análisis de productos.',
                    'intent' => 'product_ranking',
                    'period' => $productPeriod['type'],
                    'products' => [],
                ];
            }

            if ($limit === 1) {
                $product = $products->first();

                $typeLabel = $ascending
                    ? 'menos vendido'
                    : 'más vendido';

                return [
                    'response' => 'El producto ' . $typeLabel . ' ' .
                        $productPeriod['label'] . ' es ' .
                        $product->name . ', con ' .
                        (int) $product->quantity_sold .
                        ' unidad(es) vendida(s) y S/ ' .
                        number_format((float) $product->sales_total, 2) .
                        ' generados.',
                    'intent' => $ascending
                        ? 'least_sold_product'
                        : 'best_selling_product',
                    'period' => $productPeriod['type'],
                    'products' => $products->toArray(),
                ];
            }

            $lines = [];

            foreach ($products as $index => $product) {
                $lines[] =
                    ($index + 1) . '. ' .
                    $product->name . ' - ' .
                    (int) $product->quantity_sold .
                    ' unidad(es) - S/ ' .
                    number_format((float) $product->sales_total, 2);
            }

            $rankingLabel = $ascending
                ? 'menos vendidos'
                : 'más vendidos';

            return [
                'response' => 'Los productos ' . $rankingLabel . ' ' .
                    $productPeriod['label'] . " son:\n" .
                    implode("\n", $lines),
                'intent' => $ascending
                    ? 'least_sold_products'
                    : 'best_selling_products',
                'period' => $productPeriod['type'],
                'products' => $products->toArray(),
            ];
        }
        $period = $this->detectPeriod($message);

        $salesWords = [
            'venta',
            'ventas',
            'vendimos',
            'vendido',
            'vendio',
            'vendi',
            'ingreso',
            'ingresos',
            'facturamos',
            'facturado',
            'recaudamos',
            'recaudado',
        ];

        $isSalesQuestion = false;

        foreach ($salesWords as $word) {
            if (str_contains($message, $word)) {
                $isSalesQuestion = true;
                break;
            }
        }

        if ($isSalesQuestion && $period) {
            $query = DB::table('orders')
                ->where('status', 'completed')
                ->whereBetween('created_at', [
                    $period['start'],
                    $period['end'],
                ]);

            $paymentMethods = [
                'efectivo' => 'cash',
                'cash' => 'cash',
                'tarjeta' => 'card',
                'card' => 'card',
                'yape' => 'yape',
                'plin' => 'plin',
                'transferencia' => 'transfer',
                'transfer' => 'transfer',
            ];

            $selectedMethod = null;
            $selectedLabel = null;

            foreach ($paymentMethods as $word => $method) {
                if (str_contains($message, $word)) {
                    $selectedMethod = $method;

                    $selectedLabel = match ($method) {
                        'cash' => 'efectivo',
                        'card' => 'tarjeta',
                        'yape' => 'Yape',
                        'plin' => 'Plin',
                        'transfer' => 'transferencia',
                        default => $method,
                    };

                    break;
                }
            }

            if ($selectedMethod) {
                $query->where('payment_method', $selectedMethod);
            }

            $total = (float) $query->sum('total');

            if ($selectedMethod) {
                return [
                    'response' => 'Las ventas por ' . $selectedLabel . ' ' .
                        $period['label'] . ' fueron de S/ ' .
                        number_format($total, 2),
                    'intent' => 'sales_by_payment_method',
                    'period' => $period['type'],
                    'payment_method' => $selectedMethod,
                    'total' => $total,
                ];
            }

            return [
                'response' => 'Las ventas ' . $period['label'] .
                    ' fueron de S/ ' . number_format($total, 2),
                'intent' => 'sales',
                'period' => $period['type'],
                'total' => $total,
            ];
        }

        return null;
    }
}
