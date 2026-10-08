<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Support\RestaurantTestCase;

class PasswordChangeTest extends RestaurantTestCase
{
    public function test_profile_password_change_requires_correct_previous_password_and_confirmation(): void
    {
        $user = auth()->user();
        $original = $user->password;
        $data = ['name' => 'Admin actualizado', 'email' => $user->email, 'password' => 'nueva12345', 'password_confirmation' => 'nueva12345'];
        $this->put('/profile', $data)->assertSessionHasErrors('current_password');
        $this->put('/profile', $data + ['current_password' => 'incorrecta'])->assertSessionHasErrors('current_password');
        $this->assertSame($original, $user->fresh()->password);
        $this->assertSame('Admin', $user->fresh()->name);
        $this->put('/profile', array_replace($data, ['current_password' => 'testing-password', 'password_confirmation' => 'diferente']))->assertSessionHasErrors('password');
        $this->put('/profile', $data + ['current_password' => 'testing-password'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('nueva12345', $user->fresh()->password));
        $this->assertFalse(Hash::check('testing-password', $user->fresh()->password));
        $this->assertArrayNotHasKey('password', $user->fresh()->toArray());
    }

    public function test_admin_changing_own_password_must_verify_previous_password(): void
    {
        $user = auth()->user();
        $data = ['name' => $user->name, 'email' => $user->email, 'role' => 'admin', 'password' => 'nueva12345', 'password_confirmation' => 'nueva12345'];
        $this->put('/users/'.$user->id, $data)->assertSessionHasErrors('current_password');
        $this->put('/users/'.$user->id, $data + ['current_password' => 'testing-password'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('nueva12345', $user->fresh()->password));
    }

    public function test_admin_can_reset_another_users_password_and_confirmation_is_required(): void
    {
        $user = User::create(['name' => 'Mozo', 'email' => 'otro@test.com', 'password' => 'anterior123', 'role' => 'waiter']);
        $data = ['name' => $user->name, 'email' => $user->email, 'role' => 'waiter', 'password' => 'nueva12345'];
        $this->put('/users/'.$user->id, $data)->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('anterior123', $user->fresh()->password));
        $this->put('/users/'.$user->id, $data + ['password_confirmation' => 'nueva12345'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('nueva12345', $user->fresh()->password));
    }

    public function test_receipt_retry_returns_to_same_page_with_original_notification(): void
    {
        $order = Order::create(['document_type' => 'Boleta', 'serie' => 'B001', 'correlativo' => 1, 'sunat_status' => 'PENDING']);
        $this->from(route('billing.index'))->post(route('billing.retry', $order))
            ->assertRedirect(route('billing.index'))
            ->assertSessionHas('error', 'Las boletas se comunican a SUNAT mediante Resumen Diario. No corresponde realizar un reenvío individual.');
    }
}
