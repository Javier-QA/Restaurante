<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Setting; // Importamos el modelo
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Filtro de rango de fechas
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        // 1. Consulta base de ventas
        $ordersQuery = Order::whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->where('status', 'completed');

        // 2. Totales generales de ventas
        $totalCash = (clone $ordersQuery)
            ->where('payment_method', 'cash')
            ->sum('total');

        $totalCard = (clone $ordersQuery)
            ->where('payment_method', 'card')
            ->sum('total');

        $totalYape = (clone $ordersQuery)
            ->where('payment_method', 'yape')
            ->sum('total');

        $totalPlin = (clone $ordersQuery)
            ->where('payment_method', 'plin')
            ->sum('total');

        $totalSales = $totalCash + $totalCard + $totalYape + $totalPlin;

        // 3. Ventas paginadas
        $orders = (clone $ordersQuery)
            ->with(['user', 'table', 'delivery'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'sales_page')
            ->withQueryString();

        // 4. Consulta base de gastos
        $expensesQuery = Expense::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        // Total general de gastos
        $totalExpenses = (clone $expensesQuery)->sum('amount');

        // 5. Gastos paginados
        $expenses = (clone $expensesQuery)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'expenses_page')
            ->withQueryString();

        // 6. Balance final
        $balance = $totalCash - $totalExpenses;

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

    public function ticket(Order $order)
    {
        if ($order->status === 'completed' && in_array($order->document_type, ['Boleta', 'Factura'], true)) {
            return app(BillingPdfController::class)->ticket($order);
        }

        $settings = Setting::pluck('value', 'key')->toArray();
        $settings['currency_symbol'] = $settings['currency_symbol'] ?? 'S/';

        return view('sales.ticket', compact('order', 'settings'));
    }

    public function dailyReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        $orders = Order::whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->where('status', 'completed')
            ->get();

        $stats = [
            'start_date' => Carbon::parse($startDate),
            'end_date' => Carbon::parse($endDate),
            'cash' => $orders->where('payment_method', 'cash')->sum('total'),
            'card' => $orders->where('payment_method', 'card')->sum('total'),
            'yape' => $orders->where('payment_method', 'yape')->sum('total'),
            'plin' => $orders->where('payment_method', 'plin')->sum('total'),
            'orders_count' => $orders->count(),
            'expenses' => 0,
        ];

        if (class_exists('\App\Models\Expense')) {
            $stats['expenses'] = Expense::whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('amount');
        }

        $stats['total'] = $stats['cash'] + $stats['card'] + $stats['yape'] + $stats['plin'];
        $stats['balance'] = $stats['cash'] - $stats['expenses'];

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('sales.daily_report', compact('stats', 'settings'));
    }
}
