<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ─── Config ───────────────────────────────────────────────────────────
        $currency = Setting::where('key', 'currency_symbol')->value('value') ?? 'S/';
        $today = Carbon::today();
        $year = Carbon::now()->year;

        // ─── KPIs del día ─────────────────────────────────────────────────────
        $totalSalesToday = Order::where('status', 'completed')
            ->whereDate('paid_at', $today)
            ->sum('total');

        $ordersCountToday = Order::where('status', 'completed')
            ->whereDate('paid_at', $today)
            ->count();

        // Mesas realmente ocupadas: pedidos pendientes asociados a una mesa.
        // distinct evita contar dos veces la misma mesa.
        $activeTables = Order::where('status', 'pending')
            ->whereNotNull('table_id')
            ->distinct()
            ->count('table_id');

        $lowStockProducts = Product::where('is_active', true)
            ->where('controls_stock', true)
            ->where('stock', '<=', 5)
            ->count();

        // ─── Ventas del mes actual ─────────────────────────────────────────────
        $totalSalesMonth = Order::where('status', 'completed')
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', Carbon::now()->month)
            ->sum('total');

        // ─── Meta mensual (de configuración o 5000 por defecto) ───────────────
        $monthlyGoal = (float) (Setting::where('key', 'monthly_goal')->value('value') ?? 5000);
        $goalPercent = $monthlyGoal > 0
            ? min(100, round(($totalSalesMonth / $monthlyGoal) * 100))
            : 0;

        // ─── Datos mensuales para el Line Chart (año actual) ──────────────────
        $monthlySalesRaw = Order::where('status', 'completed')
            ->whereYear('paid_at', $year)
            ->select(DB::raw('MONTH(paid_at) as month'), DB::raw('SUM(total) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyOrdersRaw = Order::where('status', 'completed')
            ->whereYear('paid_at', $year)
            ->select(DB::raw('MONTH(paid_at) as month'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('month')
            ->pluck('cnt', 'month')
            ->toArray();

        $monthlyReservationsRaw = Reservation::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('month')
            ->pluck('cnt', 'month')
            ->toArray();

        // Rellenar los 12 meses con 0 donde no haya datos
        $monthlySales = [];
        $monthlyOrders = [];
        $monthlyReservations = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlySales[] = round($monthlySalesRaw[$m] ?? 0, 2);
            $monthlyOrders[] = $monthlyOrdersRaw[$m] ?? 0;
            $monthlyReservations[] = $monthlyReservationsRaw[$m] ?? 0;
        }

        // ─── Datos para el Radar Chart (por categoría) ────────────────────────
        $categoryStats = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->select('categories.name', DB::raw('SUM(order_details.quantity) as total_qty'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        $radarLabels = $categoryStats->pluck('name')->toArray();
        $radarData = $categoryStats->pluck('total_qty')->map(fn ($v) => (int) $v)->toArray();

        // Si no hay datos suficientes, usar placeholders
        if (count($radarLabels) < 3) {
            $radarLabels = ['Entradas', 'Platos', 'Bebidas', 'Postres', 'Especiales'];
            $radarData = [0, 0, 0, 0, 0];
        }

        // ─── Monitor de Mesas ─────────────────────────────────────────────────
        $areas = Area::with(['tables' => function ($q) {
            $q->with(['orders' => function ($o) {
                $o->where('status', 'pending');
            }]);
        }])->get();

        // ─── Top Productos ────────────────────────────────────────────────────
        $topProducts = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->select('products.name', 'products.image', DB::raw('SUM(order_details.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ─── Datos legacy (por si alguna vista los usa) ───────────────────────
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->locale('es')->isoFormat('dd D');
            $chartValues[] = Order::where('status', 'completed')
                ->whereDate('paid_at', $date->format('Y-m-d'))
                ->sum('total');
        }

        return view('dashboard', compact(
            'currency',
            'totalSalesToday',
            'ordersCountToday',
            'activeTables',
            'lowStockProducts',
            'totalSalesMonth',
            'monthlyGoal',
            'goalPercent',
            'monthlySales',
            'monthlyOrders',
            'monthlyReservations',
            'radarLabels',
            'radarData',
            'areas',
            'topProducts',
            'chartLabels',
            'chartValues'
        ));
    }
}
