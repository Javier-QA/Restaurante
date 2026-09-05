<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();

            // Documento afectado
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Numeración de la nota
            $table->string('serie', 4);                             // FC01 / BC01
            $table->unsignedInteger('correlativo');
            $table->string('document_type', 30)->default('nota_credito');

            // Motivo (Catálogo 09 SUNAT)
            // 01 Anulación de la operación
            // 02 Anulación por error en el RUC
            // 03 Corrección por error en la descripción
            // 06 Devolución total
            // 07 Devolución por ítem
            // 13 Ajustes - montos y/o fechas de pago
            $table->string('reason_code', 2);
            $table->string('reason_description');

            // Importes (Perú: IGV 18%)
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Trazabilidad SUNAT
            $table->string('sunat_status', 30)->default('PENDING');
            $table->string('sunat_code', 10)->nullable();
            $table->text('sunat_description')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('cdr_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('hash', 100)->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['serie', 'correlativo'], 'credit_notes_serie_corr_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
