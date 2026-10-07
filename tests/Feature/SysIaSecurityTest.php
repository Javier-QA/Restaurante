<?php
namespace Tests\Feature;
use RuntimeException;
use Tests\TestCase;
class SysIaSecurityTest extends TestCase
{
    public function test_generated_sql_cannot_mutate_data_or_read_system_metadata(): void
    {
        require_once app_path('Services/SysIa/core.php');
        foreach (['DELETE FROM orders', 'SELECT SLEEP(5)', 'SELECT * FROM mysql.user', 'SELECT * FROM v_ia_sys_ventas; DROP TABLE orders', 'SELECT 1 INTO OUTFILE \'x\''] as $sql) {
            try {
                \App\Services\SysIa\ia_validar_sql($sql);
                $this->fail('SQL inseguro aceptado: '.$sql);
            } catch (RuntimeException $e) {
                $this->assertStringContainsString('Consulta rechazada', $e->getMessage());
            }
        }
    }
    public function test_both_data_endpoints_require_authentication_and_admin_role(): void
    {
        foreach (['assistant.api', 'chatbot.api'] as $name) {
            $route = app('router')->getRoutes()->getByName($name);
            $this->assertNotNull($route);
            $this->assertContains('auth', $route->gatherMiddleware());
            $this->assertContains('role:admin', $route->gatherMiddleware());
        }
    }
    public function test_greetings_and_prompt_follow_the_configured_restaurant_name(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        \Illuminate\Support\Facades\DB::purge('sqlite');
        \Illuminate\Support\Facades\Schema::create('settings', function ($table) { $table->string('key'); $table->text('value'); });
        require_once app_path('Services/SysIa/core.php');
        foreach (['El Capitán', 'Frida Mezcal'] as $name) {
            \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(['key' => 'company_name'], ['value' => $name]);
            $reply = \App\Services\SysIa\ia_chat('hola');
            $this->assertStringContainsString('Soy el asistente de '.$name, $reply['texto']);
            $this->assertSame($name, \App\Services\SysIa\ia_empresa());
        }
    }
}
