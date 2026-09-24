<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Permitir temporalmente los valores antiguos y nuevos
        DB::statement("ALTER TABLE deliveries MODIFY payment_method ENUM('cash','card','transfer','yape','plin') NOT NULL DEFAULT 'cash'");

        // Convertir registros antiguos de Transferencia / Yape a Yape
        DB::table('deliveries')
            ->where('payment_method', 'transfer')
            ->update(['payment_method' => 'yape']);

        // Dejar únicamente los métodos de pago actuales
        DB::statement("ALTER TABLE deliveries MODIFY payment_method ENUM('cash','card','yape','plin') NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        // Permitir temporalmente ambos formatos
        DB::statement("ALTER TABLE deliveries MODIFY payment_method ENUM('cash','card','transfer','yape','plin') NOT NULL DEFAULT 'cash'");

        // Restaurar Yape al formato antiguo
        DB::table('deliveries')
            ->where('payment_method', 'yape')
            ->update(['payment_method' => 'transfer']);

        DB::table('deliveries')
            ->where('payment_method', 'plin')
            ->update(['payment_method' => 'transfer']);

        DB::statement("ALTER TABLE deliveries MODIFY payment_method ENUM('cash','card','transfer') NOT NULL DEFAULT 'cash'");
    }
};