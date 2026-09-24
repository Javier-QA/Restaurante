<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('client_name')->nullable()->change();
            $table->string('client_phone')->nullable()->change();
            $table->text('address')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('client_name')->nullable(false)->change();
            $table->string('client_phone')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
        });
    }
};