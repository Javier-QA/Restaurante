<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\RestaurantTestCase;

class PasswordRevealTest extends RestaurantTestCase
{
    public function test_admin_can_read_newly_created_password_but_it_is_encrypted_and_hidden_elsewhere(): void
    {
        $this->post('/users', ['name' => 'Mozo', 'email' => 'nuevo@test.com', 'password' => 'NuevaClave123!', 'role' => 'waiter'])->assertSessionHasNoErrors();
        $user = User::where('email', 'nuevo@test.com')->firstOrFail();
        $stored = DB::table('users')->where('id', $user->id)->value('recoverable_password');
        $this->assertNotSame('NuevaClave123!', $stored);
        $this->assertStringNotContainsString('NuevaClave123!', $stored);
        $this->assertArrayNotHasKey('recoverable_password', $user->toArray());
        $this->assertArrayNotHasKey('password', $user->toArray());
        $response = $this->post(route('users.current_password', $user));
        $response->assertOk()->assertJsonPath('password', 'NuevaClave123!');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_legacy_password_cannot_be_recovered_and_becomes_available_after_reset(): void
    {
        $user = User::create(['name' => 'Mozo', 'email' => 'antiguo@test.com', 'password' => 'Antigua123!', 'role' => 'waiter']);
        $this->post(route('users.current_password', $user))->assertOk()->assertJsonPath('password', null);
        $this->put('/users/'.$user->id, ['name' => $user->name, 'email' => $user->email, 'role' => 'waiter', 'password' => 'NuevaClave123!', 'password_confirmation' => 'NuevaClave123!'])->assertSessionHasNoErrors();
        $this->post(route('users.current_password', $user))->assertOk()->assertJsonPath('password', 'NuevaClave123!');
        $this->assertTrue(Hash::check('NuevaClave123!', $user->fresh()->password));
        $user->update(['password' => 'CambiadaPorOtroFlujo123']);
        $this->post(route('users.current_password', $user))->assertOk()->assertJsonPath('password', null);
    }

    public function test_password_changed_from_profile_also_updates_encrypted_copy(): void
    {
        $user = auth()->user();
        $this->put('/profile', ['name' => $user->name, 'email' => $user->email, 'current_password' => 'testing-password', 'password' => 'NuevaClave123!', 'password_confirmation' => 'NuevaClave123!'])->assertSessionHasNoErrors();
        $this->post(route('users.current_password', $user))->assertOk()->assertJsonPath('password', 'NuevaClave123!');
    }

    public function test_password_read_requires_admin_authentication(): void
    {
        $target = auth()->user();
        $waiter = User::create(['name' => 'Mozo', 'email' => 'mozo@test.com', 'password' => 'password123', 'role' => 'waiter']);
        $this->actingAs($waiter)->post(route('users.current_password', $target))
            ->assertRedirect(route('pos.index'))->assertSessionHas('warning');
        auth()->logout();
        $this->postJson(route('users.current_password', $target))->assertUnauthorized();
    }
}
