<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usar Bootstrap 5 para la paginación de Laravel
        Paginator::useBootstrapFive();

        // Compartir el símbolo de moneda con TODAS las vistas
        // Usamos un try-catch para evitar errores si la tabla settings aún no existe
        try {

            if (Schema::hasTable('settings')) {

                $currency = Setting::where('key', 'currency_symbol')
                    ->value('value') ?? '$';

                // La variable $currency estará disponible en cualquier archivo .blade.php
                View::share('currency', $currency);
            }

        } catch (\Exception $e) {

            // Si falla la base de datos, usamos el valor por defecto
            View::share('currency', '$');
        }
    }
}