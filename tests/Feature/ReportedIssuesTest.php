<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use App\Models\DailySummary;
use App\Models\Order;
use App\Models\User;
use App\Services\Sunat\DailySummarySynchronizer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\Support\RestaurantTestCase;

class ReportedIssuesTest extends RestaurantTestCase
{
    public function test_accepted_summary_updates_only_matching_issued_receipts(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            $t->string('sunat_code')->nullable();
            $t->text('sunat_description')->nullable();
        });
        Schema::table('daily_summaries', function (Blueprint $t) {
            $t->string('cdr_path')->nullable();
            $t->string('sunat_code')->nullable();
            $t->text('sunat_description')->nullable();
        });
        Schema::create('daily_summary_details', function (Blueprint $t) {
            $t->id();
            $t->timestamps();
            $t->integer('daily_summary_id');
            $t->integer('order_id');
            $t->string('operation_status');
            $t->string('serie');
            $t->integer('correlativo');
        });
        $summary = DailySummary::create(['sunat_status' => 'TICKET', 'cdr_path' => 'cdr.zip', 'sunat_code' => '0', 'sunat_description' => 'Aceptado']);
        $orders = [];
        foreach (['1', '3', '1'] as $i => $operation) {
            $orders[$i] = Order::create(['document_type' => 'Boleta', 'serie' => 'B001', 'correlativo' => $i + 1, 'sunat_status' => 'PENDING']);
            $summary->details()->create(['order_id' => $orders[$i]->id, 'operation_status' => $operation, 'serie' => $i === 2 ? 'B002' : 'B001', 'correlativo' => $i + 1]);
        }
        $service = new DailySummarySynchronizer;
        $service->synchronize($summary);
        $this->assertSame('PENDING', $orders[0]->fresh()->sunat_status);
        $summary->update(['sunat_status' => 'ACCEPTED']);
        $migration = require database_path('migrations/2026_10_08_060000_sync_accepted_summary_orders.php');
        $migration->up();
        $this->assertSame('ACCEPTED', $orders[0]->fresh()->sunat_status);
        $this->assertSame('0', $orders[0]->fresh()->sunat_code);
        $this->assertSame('PENDING', $orders[1]->fresh()->sunat_status);
        $this->assertSame('PENDING', $orders[2]->fresh()->sunat_status);
        $summary->update(['sunat_status' => 'OBSERVED']);
        $service->synchronize($summary);
        $this->assertSame('ACCEPTED', $orders[0]->fresh()->sunat_status);
    }

    public function test_user_can_save_changes_without_replacing_password(): void
    {
        $user = User::create(['name' => 'Mozo', 'email' => 'mozo@test.com', 'password' => 'password123', 'role' => 'waiter']);
        $password = $user->password;
        (new UserController)->update(Request::create('/', 'POST', ['name' => 'Nuevo nombre', 'email' => $user->email, 'role' => 'cashier', 'password' => null]), $user);
        $this->assertSame('Nuevo nombre', $user->fresh()->name);
        $this->assertSame($password, $user->fresh()->password);
        (new UserController)->update(Request::create('/', 'POST', ['name' => 'Nuevo nombre', 'email' => $user->email, 'role' => 'cashier', 'password' => 'nueva123']), $user);
        $this->assertTrue(Hash::check('nueva123', $user->fresh()->password));
    }

    public function test_short_password_has_readable_spanish_validation(): void
    {
        try {
            (new UserController)->store(Request::create('/', 'POST', ['name' => 'Mozo', 'email' => 'nuevo@test.com', 'role' => 'waiter', 'password' => '123456']));
            $this->fail('Contraseña corta aceptada');
        } catch (ValidationException $e) {
            $this->assertSame('La contraseña debe tener al menos 8 caracteres.', $e->errors()['password'][0]);
        }
    }

    public function test_summary_survives_missing_provider_configuration(): void
    {
        require_once app_path('Services/SysIa/core.php');
        $result = \App\Services\SysIa\ia_resumir('Ventas por hora', ['hora', 'ventas'], [['hora' => 20, 'ventas' => 75]]);
        $this->assertStringContainsString('8:00pm', $result);
        $this->assertStringContainsString('75', $result);
    }
}
