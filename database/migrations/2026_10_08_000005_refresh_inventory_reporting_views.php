<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (require app_path('Services/SysIa/views.php') as $name => $view) {
                DB::statement("CREATE OR REPLACE VIEW `$name` AS ".$view['sql']);
            }
        }
    }

    public function down(): void
    {
        // Preserve compatible views; no business data is removed.
    }
};
