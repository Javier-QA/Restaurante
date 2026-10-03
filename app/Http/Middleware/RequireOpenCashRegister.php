<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CashRegister;

class RequireOpenCashRegister
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admin, cajero y mozo necesitan que exista una caja global abierta.
        if ($user && in_array($user->role, ['admin', 'cashier', 'waiter'])) {
            if (!CashRegister::where('status', 'open')->exists()) {
                // Solo admin y cajero pueden abrir la caja.
                if (in_array($user->role, ['admin', 'cashier'])) {
                    return redirect()->route('cash_registers.create')
                        ->with('warning', 'Debe existir una caja abierta antes de realizar operaciones de cobro o venta.');
                }

                // El mozo no puede abrir caja.
                return redirect()->route('pos.index')
                    ->with('warning', 'No hay una caja abierta. Solicita al administrador o cajero que abra la caja.');
            }
        }

        return $next($request);
    }
}
