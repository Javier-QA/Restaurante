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

        // Si el usuario es admin o cajero, exigimos caja abierta para ciertas rutas
        if ($user && in_array($user->role, ['admin', 'cashier'])) {
            if (!CashRegister::where('status', 'open')->exists()) {
                return redirect()->route('cash_registers.create')
                    ->with('warning', 'Debe existir una caja abierta antes de realizar operaciones de cobro o venta.');
            }
        }

        return $next($request);
    }
}
