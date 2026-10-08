<?php

namespace Tests\Feature;

use App\Http\Controllers\BarraController;
use App\Http\Controllers\KitchenController;
use Illuminate\Http\Request;
use Tests\Support\RestaurantTestCase;

class PreparationNotificationsTest extends RestaurantTestCase
{
    public function test_kitchen_and_bar_return_confirmed_status_for_ajax_notifications(): void
    {
        foreach (['kitchen' => KitchenController::class, 'barra' => BarraController::class] as $area => $controller) {
            $product = $this->product();
            $product->update(['preparation_area' => $area]);
            $order = $this->order($product);
            $detail = $order->details()->first();
            $detail->update(['status' => 'pending']);
            $request = Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
            $response = (new $controller)->updateStatus($request, $detail);
            $this->assertSame(200, $response->getStatusCode());
            $this->assertTrue($response->getData(true)['success']);
            $this->assertSame('cooking', $response->getData(true)['status']);
            $this->assertSame('cooking', $detail->fresh()->status);
        }
    }

    public function test_preparation_error_does_not_report_success_or_change_status(): void
    {
        $product = $this->product();
        $product->update(['preparation_area' => 'barra']);
        $detail = $this->order($product)->details()->first();
        $detail->update(['status' => 'pending']);
        $request = Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = (new KitchenController)->updateStatus($request, $detail);
        $this->assertSame(403, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertSame('pending', $detail->fresh()->status);
    }
}
