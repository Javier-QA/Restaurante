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

    public function test_delivery_shipping_is_included_in_cash_arqueo(): void
    {
        $register = $this->openRegister();
        $order = $this->order($this->product());
        $order->update(['status' => 'completed', 'cash_register_id' => $register->id, 'payment_method' => 'cash']);
        \App\Models\Delivery::create(['order_id' => $order->id, 'status' => 'delivered', 'delivery_fee' => 5]);
        $this->assertEquals(45, $register->collectedSales('cash'));
    }
}
