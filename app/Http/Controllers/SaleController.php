<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Caja y Movimientos
     */
    public function index(Request $request)
    {
        // Filtro de rango de fechas
        $startDate = $request->input(
            'start_date',
            Carbon::today()->format('Y-m-d')
        );

        $endDate = $request->input(
            'end_date',
            Carbon::today()->format('Y-m-d')
        );

        // =====================================================
        // 1. OBTENER VENTAS COMPLETADAS
        // =====================================================

        $orders = Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();

        // =====================================================
        // 2. TOTALES POR MÉTODO DE PAGO
        // =====================================================

        $totalCash = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'cash';
            })
            ->sum('total');

        $totalCard = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'card';
            })
            ->sum('total');

        $totalYape = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'yape';
            })
            ->sum('total');

        $totalPlin = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'plin';
            })
            ->sum('total');

        // =====================================================
        // 3. VENTA TOTAL
        // =====================================================

        $totalSales =
            $totalCash +
            $totalCard +
            $totalYape +
            $totalPlin;

        // =====================================================
        // 4. OBTENER GASTOS
        // =====================================================

        $expenses = Expense::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        // =====================================================
        // 5. DINERO EN CAJA
        // =====================================================
        // SOLO EFECTIVO FÍSICO.
        //
        // Tarjeta, Yape y Plin forman parte de las ventas,
        // pero NO aumentan el dinero físico de caja.
        // =====================================================

        $balance = $totalCash - $totalExpenses;

        // =====================================================
        // 6. ENVIAR DATOS A LA VISTA
        // =====================================================

        return view('sales.index', compact(
            'orders',
            'expenses',
            'startDate',
            'endDate',
            'totalCash',
            'totalCard',
            'totalYape',
            'totalPlin',
            'totalSales',
            'totalExpenses',
            'balance'
        ));
    }


    /**
     * Imprimir ticket
     */
    public function ticket(Order $order)
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $settings['currency_symbol'] =
            $settings['currency_symbol'] ?? 'S/';

        return view(
            'sales.ticket',
            compact('order', 'settings')
        );
    }


    /**
     * Reporte diario / Corte Z
     */
    public function dailyReport(Request $request)
    {
        $startDate = $request->input(
            'start_date',
            Carbon::today()->format('Y-m-d')
        );

        $endDate = $request->input(
            'end_date',
            Carbon::today()->format('Y-m-d')
        );

        // =====================================================
        // 1. OBTENER VENTAS COMPLETADAS
        // =====================================================

        $orders = Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', 'completed')
            ->get();

        // =====================================================
        // 2. TOTALES POR MÉTODO DE PAGO
        // =====================================================

        $cash = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'cash';
            })
            ->sum('total');

        $card = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'card';
            })
            ->sum('total');

        $yape = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'yape';
            })
            ->sum('total');

        $plin = $orders
            ->filter(function ($order) {
                return strtolower(trim($order->payment_method ?? '')) === 'plin';
            })
            ->sum('total');

        // =====================================================
        // 3. OBTENER GASTOS
        // =====================================================

        $expenses = Expense::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('amount');

        // =====================================================
        // 4. ESTADÍSTICAS
        // =====================================================

        $stats = [

            'start_date' => Carbon::parse($startDate),

            'end_date' => Carbon::parse($endDate),

            'cash' => $cash,

            'card' => $card,

            'yape' => $yape,

            'plin' => $plin,

            'orders_count' => $orders->count(),

            'expenses' => $expenses,

            // Venta total = todos los métodos
            'total' =>
                $cash +
                $card +
                $yape +
                $plin,

            // Caja física = efectivo - gastos
            'balance' =>
                $cash -
                $expenses,
        ];

        // =====================================================
        // 5. CONFIGURACIÓN
        // =====================================================

        $settings = Setting::pluck(
            'value',
            'key'
        )->toArray();

        // =====================================================
        // 6. MOSTRAR REPORTE
        // =====================================================

        return view(
            'sales.daily_report',
            compact(
                'stats',
                'settings'
            )
        );
    }
}