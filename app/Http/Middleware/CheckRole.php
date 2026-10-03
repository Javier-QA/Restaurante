<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Si el usuario tiene uno de los roles permitidos, pasa.
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Si no tiene permiso, regresar a una zona permitida
        // y mostrar una notificación propia del sistema.
        $route = match ($user->role) {
            'admin', 'cashier' => 'dashboard',
            'waiter' => 'pos.index',
            'kitchen' => 'kitchen.index',
            'bar' => 'barra.index',
            default => 'login',
        };

        return redirect()
            ->route($route)
            ->with('warning', 'No tienes permiso para acceder a esta sección.');
    }
}