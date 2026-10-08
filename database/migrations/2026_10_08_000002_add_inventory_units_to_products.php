<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Null identifies legacy items whose real unit must be assigned manually.
            $table->string('unit', 10)->nullable();
            $table->decimal('minimum_stock', 14, 3)->default(5);
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['unit', 'minimum_stock']));
    }
};
