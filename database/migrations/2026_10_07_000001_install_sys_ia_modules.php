<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (DB::connection()->getDriverName() !== 'mysql') throw new RuntimeException('SYS IA requiere MySQL/MariaDB.');
        if (!Schema::hasTable('ia_configuracion')) Schema::create('ia_configuracion', function (Blueprint $t) {
            $t->string('clave')->primary(); $t->text('valor')->nullable();
        });
        if (!Schema::hasTable('ia_consultas')) Schema::create('ia_consultas', function (Blueprint $t) {
            $t->increments('id'); $t->unsignedBigInteger('usuario_id'); $t->string('pregunta',500);
            $t->string('titulo',200)->nullable(); $t->text('sql_texto'); $t->string('grafico',300)->nullable();
            $t->boolean('favorito')->default(false); $t->unsignedInteger('veces')->default(0);
            $t->dateTime('creado_en')->useCurrent(); $t->dateTime('ultimo_uso')->useCurrent();
            $t->index(['usuario_id','favorito','ultimo_uso']);
        });
        foreach (['ia_proveedor', 'ia_url', 'ia_modelo', 'ia_clave', 'ia_resumen'] as $key) {
            if (DB::table('ia_configuracion')->where('clave', $key)->exists()) continue;
            $value = DB::table('settings')->where('key', $key)->value('value');
            if ($value === null) continue;
            if ($key === 'ia_proveedor' && $value === 'openai') $value = 'otro';
            if ($key === 'ia_clave' && $value !== '') {
                try { \Illuminate\Support\Facades\Crypt::decryptString($value); }
                catch (\Illuminate\Contracts\Encryption\DecryptException $e) { $value = \Illuminate\Support\Facades\Crypt::encryptString($value); }
            }
            DB::table('ia_configuracion')->insert(['clave' => $key, 'valor' => $value]);
        }
        foreach (require app_path('Services/SysIa/views.php') as $name=>$view) DB::statement("CREATE OR REPLACE VIEW `$name` AS " . $view['sql']);
    }
    public function down(): void {
        foreach (require app_path('Services/SysIa/views.php') as $name=>$view) DB::statement("DROP VIEW IF EXISTS `$name`");
        Schema::dropIfExists('ia_consultas'); Schema::dropIfExists('ia_configuracion');
    }
};
