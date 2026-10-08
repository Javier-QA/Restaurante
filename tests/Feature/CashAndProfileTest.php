<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Tests\Support\RestaurantTestCase;

class CashAndProfileTest extends RestaurantTestCase
{
    public function test_waiter_sees_closed_cash_page_without_redirect_loop(): void
    {
        $this->actingAs(User::create(['name' => 'Mozo', 'email' => 'mozo@test.com', 'password' => 'testing-password', 'role' => 'waiter']));
        $this->get('/pos')->assertOk()->assertSee('La caja está cerrada');
    }

    public function test_posting_payment_with_closed_cash_is_rejected(): void
    {
        $order = $this->order($this->product());
        $this->postJson('/pos/order/'.$order->id.'/checkout', ['payment_method' => 'cash', 'document_type' => 'Ticket', 'received_amount' => 100])->assertUnprocessable()->assertJsonValidationErrors('cash_register');
    }

    public function test_waiter_expense_is_recorded_in_global_register(): void
    {
        $register = $this->openRegister();
        $this->actingAs(User::create(['name' => 'Mozo', 'email' => 'mozo@test.com', 'password' => 'testing-password', 'role' => 'waiter']));
        $this->post('/expenses', ['description' => 'Compra de hielo', 'amount' => 12])->assertRedirect();
        $this->assertEquals($register->id, Expense::first()->cash_register_id);
    }

    public function test_profile_updates_do_not_change_user_role(): void
    {
        $user = User::create(['name' => 'Mozo', 'email' => 'mozo@test.com', 'password' => 'testing-password', 'role' => 'waiter']);
        $this->actingAs($user)->put('/profile', ['name' => 'Javier', 'email' => $user->email, 'role' => 'admin'])->assertRedirect();
        $this->assertSame('waiter', $user->fresh()->role);
        $this->assertSame('Javier', $user->fresh()->name);
    }

    public function test_cash_cannot_close_with_pending_orders(): void
    {
        $register = $this->openRegister();
        $this->order($this->product());
        $this->postJson('/cash-registers/close', ['closing_amount' => 50])->assertUnprocessable()->assertJsonValidationErrors('cash_register');
        $this->assertSame('open', $register->fresh()->status);
    }

    public function test_cash_closes_without_sales(): void
    {
        $register = $this->openRegister();
        $this->post('/cash-registers/close', ['closing_amount' => 50])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');
        $register->refresh();
        $this->assertSame('closed', $register->status);
        $this->assertEquals(50, $register->expected_amount);
        $this->assertEquals(0, $register->difference);
        $this->assertNotNull($register->closing_time);
    }

    public function test_blocked_cash_close_displays_reason_on_the_page(): void
    {
        $register = $this->openRegister();
        $this->order($this->product());
        $this->from(route('cash_registers.close'))
            ->post('/cash-registers/close', ['closing_amount' => 50])
            ->assertRedirect(route('cash_registers.close'))
            ->assertSessionHasErrors('cash_register');
        $this->get(route('cash_registers.close'))
            ->assertOk()
            ->assertSee('No se pudo cerrar la caja.')
            ->assertSee('Finaliza los pedidos pendientes antes de cerrar la caja.');
        $this->assertSame('open', $register->fresh()->status);
    }

    public function test_empty_pending_order_does_not_block_cash_close(): void
    {
        $register = $this->openRegister();
        \App\Models\Order::create(['status' => 'pending', 'total' => 0, 'user_id' => auth()->id()]);
        $this->post('/cash-registers/close', ['closing_amount' => 50])
            ->assertRedirect(route('dashboard'));
        $this->assertSame('closed', $register->fresh()->status);
    }

    public function test_paid_order_with_stale_pending_status_does_not_block_close(): void
    {
        $register = $this->openRegister();
        $order = $this->order($this->product());
        $order->update(['paid_at' => now()]);
        $this->post('/cash-registers/close', ['closing_amount' => 50])
            ->assertRedirect(route('dashboard'));
        $this->assertSame('closed', $register->fresh()->status);
    }

    public function test_blocking_order_is_identified_in_the_message(): void
    {
        $this->openRegister();
        $order = $this->order($this->product());
        $this->postJson('/cash-registers/close', ['closing_amount' => 50])
            ->assertUnprocessable()
            ->assertJsonPath('errors.cash_register.0',
                'Finaliza los pedidos pendientes antes de cerrar la caja. Pedidos: #'.$order->id.' (mesa ID '.$order->table_id.')');
    }

    public function test_delivery_shipping_is_included_in_cash_arqueo(): void
    {
        $register = $this->openRegister();
        $order = $this->order($this->product());
        $order->update(['status' => 'completed', 'cash_register_id' => $register->id, 'payment_method' => 'cash']);
        \App\Models\Delivery::create(['order_id' => $order->id, 'status' => 'delivered', 'delivery_fee' => 5]);
        $this->assertEquals(45, $register->collectedSales('cash'));
    }
}
