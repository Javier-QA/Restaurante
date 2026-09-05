<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Filtro de fechas
        $startDate = $request->input(
            'start_date',
            Carbon::now()->startOfMonth()->format('Y-m-d')
        );

        $endDate = $request->input(
            'end_date',
            Carbon::now()->endOfMonth()->format('Y-m-d')
        );

        // 1. VENTAS POR CATEGORÍA
        $salesByCategory = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw(
                    'SUM(order_details.quantity * order_details.price) as total'
                )
            )
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->groupBy('categories.name')
            ->get();

        $catLabels = $salesByCategory->pluck('name');
        $catValues = $salesByCategory->pluck('total');

        // 2. RENDIMIENTO DE PERSONAL
        $salesByWaiter = Order::select(
                'users.name',
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(orders.id) as orders_count')
            )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->groupBy('users.name')
            ->orderByDesc('total_sales')
            ->get();

        $waiterLabels = $salesByWaiter->pluck('name');
        $waiterValues = $salesByWaiter->pluck('total_sales');

        // 3. TOP 5 PRODUCTOS MÁS VENDIDOS
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) as qty'),
                DB::raw(
                    'SUM(order_details.quantity * order_details.price) as revenue'
                )
            )
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->groupBy('products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // 4. TOP 5 PRODUCTOS MENOS VENDIDOS
        $worstProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) as qty')
            )
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->groupBy('products.name')
            ->orderBy('qty', 'asc')
            ->limit(5)
            ->get();

        // Moneda
        $currency = Setting::where(
            'key',
            'currency_symbol'
        )->value('value') ?? 'S/';

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'catLabels',
            'catValues',
            'waiterLabels',
            'waiterValues',
            'topProducts',
            'worstProducts',
            'salesByWaiter',
            'currency'
        ));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input(
            'start_date',
            Carbon::now()->startOfMonth()->format('Y-m-d')
        );

        $endDate = $request->input(
            'end_date',
            Carbon::now()->endOfMonth()->format('Y-m-d')
        );

        // Ventas totales
        $totalSales = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total');

        // Cantidad de pedidos
        $ordersCount = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Productos más vendidos
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) as qty'),
                DB::raw(
                    'SUM(order_details.quantity * order_details.price) as revenue'
                )
            )
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->groupBy('products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // Productos menos vendidos
        $worstProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) as qty')
            )
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->groupBy('products.name')
            ->orderBy('qty', 'asc')
            ->limit(5)
            ->get();

        // Rendimiento del personal
        $salesByWaiter = Order::select(
                'users.name',
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(orders.id) as orders_count')
            )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->groupBy('users.name')
            ->orderByDesc('total_sales')
            ->get();

        // Configuración
        $currency = Setting::where(
            'key',
            'currency_symbol'
        )->value('value') ?? 'S/';

        $companyName = Setting::where(
            'key',
            'company_name'
        )->value('value') ?? 'Mi Restaurante';

        $monthlyGoal = (float) (
            Setting::where(
                'key',
                'monthly_goal'
            )->value('value') ?? 5000
        );

        // Porcentaje de la meta
        $goalPercent = $monthlyGoal > 0
            ? min(
                100,
                round(($totalSales / $monthlyGoal) * 100, 1)
            )
            : 0;

        $generatedAt = Carbon::now();

        // Generar PDF
        $pdf = Pdf::loadView(
            'reports.pdf',
            compact(
                'startDate',
                'endDate',
                'totalSales',
                'ordersCount',
                'topProducts',
                'worstProducts',
                'salesByWaiter',
                'currency',
                'companyName',
                'monthlyGoal',
                'goalPercent',
                'generatedAt'
            )
        );

        $pdf->setPaper('a4', 'portrait');

        $fileName =
            'reporte_ventas_' .
            $startDate .
            '_al_' .
            $endDate .
            '.pdf';

        return $pdf->stream($fileName);
    }
}

