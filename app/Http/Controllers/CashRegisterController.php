<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index()
    {
        $registers = CashRegister::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $currency = \App\Models\Setting::where(
            'key',
            'currency_symbol'
        )->value('value') ?? 'S/';

        $stats = [
            'total' => CashRegister::count(),
            'open' => CashRegister::where('status', 'open')->count(),
            'closed' => CashRegister::where('status', 'closed')->count(),
            'differences' => CashRegister::where('status', 'closed')
                ->whereNotNull('difference')
                ->where(function ($query) {
                    $query->where('difference', '>', 0.004)
                        ->orWhere('difference', '<', -0.004);
                })
                ->count(),
        ];

        return view(
            'cash_registers.index',
            compact('registers', 'currency', 'stats')
        );
    }

    public function show(CashRegister $cashRegister)
    {
        $summary = $this->buildRegisterSummary($cashRegister);

        return view('cash_registers.show', $summary);
    }

    public function pdf(CashRegister $cashRegister)
    {
        $summary = $this->buildRegisterSummary($cashRegister);

        $theme = \App\Models\Setting::where('key', 'dashboard_theme')
            ->value('value') ?? 'ocean-orange';

        if ($theme === 'ocean-coral') {
            $theme = 'ocean-orange';
        }

        $palettes = [
            'ocean-orange' => [
                'primary' => '#ff8c00',
                'dark' => '#063970',
                'secondary' => '#0b84c6',
            ],
            'lime-blue' => [
                'primary' => '#84cc16',
                'dark' => '#063970',
                'secondary' => '#0b4f8a',
            ],
            'purple-orange' => [
                'primary' => '#ff8c00',
                'dark' => '#4c1d95',
                'secondary' => '#7c3aed',
            ],
            'sand-navy' => [
                'primary' => '#c98a52',
                'dark' => '#063970',
                'secondary' => '#0b4f8a',
            ],
            'teal-amber' => [
                'primary' => '#f59e0b',
                'dark' => '#07575b',
                'secondary' => '#0f8b8d',
            ],
            'wine-blue' => [
                'primary' => '#d94f70',
                'dark' => '#791837',
                'secondary' => '#3346a8',
            ],
        ];

        $summary['palette'] = $palettes[$theme] ?? $palettes['ocean-orange'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'cash_registers.pdf',
            $summary
        )->setPaper('a4', 'portrait');

        return $pdf->stream(
            'cierre-caja-turno-' . $cashRegister->id . '.pdf'
        );
    }

    private function buildRegisterSummary(CashRegister $cashRegister): array
    {
        $cashRegister->load([
            'user',
            'expenses.user',
        ]);

        $completedOrders = $cashRegister->orders()
            ->where('status', 'completed');

        $totalSalesCash = (float) (clone $completedOrders)
            ->where('payment_method', 'cash')
            ->sum('total');

        $totalSalesCard = (float) (clone $completedOrders)
            ->where('payment_method', 'card')
            ->sum('total');

        $totalSalesYape = (float) (clone $completedOrders)
            ->where('payment_method', 'yape')
            ->sum('total');

        $totalSalesPlin = (float) (clone $completedOrders)
            ->where('payment_method', 'plin')
            ->sum('total');

        $totalSales = $totalSalesCash
            + $totalSalesCard
            + $totalSalesYape
            + $totalSalesPlin;

        $totalExpenses = (float) $cashRegister->expenses->sum('amount');

        $expectedAmount = $cashRegister->status === 'closed'
            && $cashRegister->expected_amount !== null
                ? (float) $cashRegister->expected_amount
                : (float) $cashRegister->opening_amount
                    + $totalSalesCash
                    - $totalExpenses;

        $closingAmount = $cashRegister->closing_amount !== null
            ? (float) $cashRegister->closing_amount
            : null;

        $difference = $closingAmount !== null
            ? $closingAmount - $expectedAmount
            : null;

        $duration = null;

        if ($cashRegister->opening_time) {
            $end = $cashRegister->closing_time ?? now();

            $totalMinutes = $cashRegister->opening_time
                ->diffInMinutes($end);

            $hours = intdiv($totalMinutes, 60);
            $minutes = $totalMinutes % 60;

            $duration = $hours . ' h ' . $minutes . ' min';
        }

        $currency = \App\Models\Setting::where(
            'key',
            'currency_symbol'
        )->value('value') ?? 'S/';

        return [
            'cashRegister' => $cashRegister,
            'currency' => $currency,
            'totalSalesCash' => $totalSalesCash,
            'totalSalesCard' => $totalSalesCard,
            'totalSalesYape' => $totalSalesYape,
            'totalSalesPlin' => $totalSalesPlin,
            'totalSales' => $totalSales,
            'totalExpenses' => $totalExpenses,
            'expectedAmount' => $expectedAmount,
            'closingAmount' => $closingAmount,
            'difference' => $difference,
            'duration' => $duration,
        ];
    }
    public function destroyAll()
    {
        if (CashRegister::where('status', 'open')->exists()) {
            return redirect()
                ->route('cash_registers.index')
                ->with(
                    'error',
                    'No se puede eliminar el historial mientras exista una caja abierta.'
                );
        }

        \Illuminate\Support\Facades\DB::transaction(function () {
            \Illuminate\Support\Facades\DB::table('orders')
                ->whereNotNull('cash_register_id')
                ->update([
                    'cash_register_id' => null,
                ]);

            \Illuminate\Support\Facades\DB::table('expenses')
                ->whereNotNull('cash_register_id')
                ->update([
                    'cash_register_id' => null,
                ]);

            \Illuminate\Support\Facades\DB::table('cash_registers')
                ->delete();
        });

        \Illuminate\Support\Facades\DB::statement(
            'ALTER TABLE cash_registers AUTO_INCREMENT = 1'
        );

        return redirect()
            ->route('cash_registers.index')
            ->with(
                'success',
                'Todo el historial de caja fue eliminado. El próximo turno comenzará desde #1.'
            );
    }

    public function destroy(CashRegister $cashRegister)
    {
        if ($cashRegister->status !== 'closed') {
            return redirect()
                ->route('cash_registers.index')
                ->with('error', 'No se puede eliminar un turno de caja que todavía está abierto.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($cashRegister) {
            $cashRegister->orders()->update([
                'cash_register_id' => null,
            ]);

            $cashRegister->expenses()->update([
                'cash_register_id' => null,
            ]);

            $cashRegister->delete();
        });

        return redirect()
            ->route('cash_registers.index')
            ->with('success', 'El turno de caja fue eliminado correctamente.');
    }

    public function create()
    {
        if (CashRegister::where('status', 'open')->exists()) {
            return redirect()->route('pos.index')->with('info', 'Ya existe un turno de caja abierto.');
        }
        return view('cash_registers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0'
        ]);

        if (CashRegister::where('status', 'open')->exists()) {
            return redirect()->route('pos.index')->with('info', 'Ya existe una caja abierta.');
        }

        CashRegister::create([
            'user_id' => auth()->id(),
            'opening_time' => Carbon::now(),
            'opening_amount' => $request->opening_amount,
            'status' => 'open'
        ]);

        return redirect()->route('pos.index')->with('success', 'Turno de caja abierto correctamente.');
    }

    public function close()
    {
        $cashRegister = CashRegister::where('status', 'open')->first();

        if (!$cashRegister) {
            return redirect()->route('dashboard')->with('error', 'No existe ninguna caja abierta para cerrar.');
        }

        // Resumen del turno por método de pago
        $completedOrders = $cashRegister->orders()
            ->where('status', 'completed');

        $totalSalesCash = (clone $completedOrders)
            ->where('payment_method', 'cash')
            ->sum('total');

        $totalSalesCard = (clone $completedOrders)
            ->where('payment_method', 'card')
            ->sum('total');

        $totalSalesYape = (clone $completedOrders)
            ->where('payment_method', 'yape')
            ->sum('total');

        $totalSalesPlin = (clone $completedOrders)
            ->where('payment_method', 'plin')
            ->sum('total');

        $totalSales = $totalSalesCash
            + $totalSalesCard
            + $totalSalesYape
            + $totalSalesPlin;

        $totalExpenses = $cashRegister->expenses()->sum('amount');

        // El efectivo esperado solo considera movimientos físicos de caja.
        $expectedAmount = $cashRegister->opening_amount
            + $totalSalesCash
            - $totalExpenses;

        return view('cash_registers.close', compact(
            'cashRegister',
            'expectedAmount',
            'totalSalesCash',
            'totalSalesCard',
            'totalSalesYape',
            'totalSalesPlin',
            'totalSales',
            'totalExpenses'
        ));
    }

    public function processClose(Request $request)
    {
        $request->validate([
            'closing_amount' => 'required|numeric|min:0'
        ]);

        $cashRegister = CashRegister::where('status', 'open')->first();

        if (!$cashRegister) {
            return redirect()->route('dashboard')->with('error', 'No existe ninguna caja abierta.');
        }

        $totalSalesCash = $cashRegister->orders()->where('payment_method', 'cash')->where('status', 'completed')->sum('total');
        $totalExpenses = $cashRegister->expenses()->sum('amount');
        
        $expectedAmount = $cashRegister->opening_amount + $totalSalesCash - $totalExpenses;
        $difference = $request->closing_amount - $expectedAmount;

        $cashRegister->update([
            'closing_time' => Carbon::now(),
            'closed_by' => auth()->id(),
            'closing_amount' => $request->closing_amount,
            'expected_amount' => $expectedAmount,
            'difference' => $difference,
            'status' => 'closed',
            'notes' => $request->notes
        ]);

        return redirect()->route('dashboard')->with('success', 'Turno de caja cerrado correctamente. Diferencia: S/ ' . number_format($difference, 2));
    }
}
