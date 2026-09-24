<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE order_details MODIFY status ENUM('draft','pending','cooking','served') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::table('order_details')
            ->where('status', 'draft')
            ->update(['status' => 'pending']);

        DB::statement("ALTER TABLE order_details MODIFY status ENUM('pending','cooking','served') NOT NULL DEFAULT 'pending'");
    }
};