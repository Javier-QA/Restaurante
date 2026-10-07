<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AiAccessTest extends TestCase
{
    private function userWithRole(string $role): User
    {
        $user = new User();
        $user->id = match ($role) {
            'admin' => 1,
            'waiter' => 8,
            'kitchen' => 11,
            'bar' => 12,
            'cashier' => 13,
            default => 999,
        };
        $user->name = ucfirst($role);
        $user->role = $role;
        $user->exists = true;

        return $user;
    }

    private function checkRole(string $role): mixed
    {
        $user = $this->userWithRole($role);

        Auth::setUser($user);

        $request = Request::create('/ai/assistant', 'GET');

        $middleware = app(CheckRole::class);

        return $middleware->handle(
            $request,
            fn () => response('PERMITIDO', 200),
            'admin'
        );
    }

    public function test_admin_is_allowed(): void
    {
        $response = $this->checkRole('admin');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('PERMITIDO', $response->getContent());
    }

    public function test_waiter_is_redirected(): void
    {
        $response = $this->checkRole('waiter');

        $this->assertTrue($response->isRedirect(route('pos.index')));
    }

    public function test_kitchen_is_redirected(): void
    {
        $response = $this->checkRole('kitchen');

        $this->assertTrue($response->isRedirect(route('kitchen.index')));
    }

    public function test_bar_is_redirected(): void
    {
        $response = $this->checkRole('bar');

        $this->assertTrue($response->isRedirect(route('barra.index')));
    }

    public function test_cashier_is_redirected(): void
    {
        $response = $this->checkRole('cashier');

        $this->assertTrue($response->isRedirect(route('dashboard')));
    }
}