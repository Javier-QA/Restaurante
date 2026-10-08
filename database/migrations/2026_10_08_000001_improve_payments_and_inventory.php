<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'paid_at')) {
            Schema::table('orders', fn (Blueprint $table) => $table->dateTime('paid_at')->nullable()->index());
            // Legacy payments have no independent timestamp; retain the best existing estimate.
            DB::table('orders')->where('status', 'completed')->update(['paid_at' => DB::raw('updated_at')]);
        }
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 14, 3)->nullable()->change();
            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable();
            }
        });
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->decimal('quantity', 14, 3)->change();
            $table->decimal('old_stock', 14, 3)->nullable()->change();
            $table->decimal('new_stock', 14, 3)->nullable()->change();
        });
        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (require app_path('Services/SysIa/views.php') as $name => $view) {
                DB::statement("CREATE OR REPLACE VIEW `$name` AS ".$view['sql']);
            }
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (require app_path('Services/SysIa/views.php') as $name => $view) {
                DB::statement("DROP VIEW IF EXISTS `$name`");
            }
        }
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('paid_at'));
        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (require app_path('Services/SysIa/views.php') as $name => $view) {
                DB::statement("CREATE OR REPLACE VIEW `$name` AS ".$view['sql']);
            }
        }
        // Keep decimal stock and descriptions: reverting would destroy fractional inventory/data.
    }
};
