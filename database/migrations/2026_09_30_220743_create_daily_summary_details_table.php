<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_summary_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_summary_id')
                ->constrained('daily_summaries')
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();

            // Estado comunicado en el Resumen Diario:
            // 1 = Adicionar, 2 = Modificar, 3 = Anular
            $table->char('operation_status', 1)->default('1');

            // Datos enviados a SUNAT para conservar trazabilidad histórica
            $table->string('document_type', 2)->default('03');
            $table->string('serie', 4);
            $table->unsignedInteger('correlativo');
            $table->decimal('total_amount', 14, 2);

            $table->timestamps();

            $table->unique(
                ['daily_summary_id', 'order_id'],
                'daily_summary_details_summary_order_unique'
            );

            $table->index('order_id');
            $table->index('operation_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_summary_details');
    }
};