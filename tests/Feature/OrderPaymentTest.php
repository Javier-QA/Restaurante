<?php

namespace Tests\Feature;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\Support\RestaurantTestCase;

class OrderPaymentTest extends RestaurantTestCase
{
    private function payment(array $extra = []): Request
    {
        return Request::create('/pos/pay', 'POST', $extra + ['payment_method' => 'cash', 'document_type' => 'Ticket', 'received_amount' => 100]);
    }

    public function test_partial_and_final_payments_preserve_adjustments_and_consume_stock_once(): void
    {
        $this->openRegister();
        $product = $this->product();
        $order = $this->order($product, 3);
        $order->update(['discount' => 6, 'tip' => 3, 'total' => 57]);
        $line = $order->details()->first();
        $part = app(OrderPaymentService::class)->pay($this->payment(['selected_items' => [$line->id], 'split_quantities' => [$line->id => 1]]), $order, true);
        $this->assertEquals(19, $part->total);
        $this->assertEquals(2, $part->discount);
        $this->assertEquals(1, $part->tip);
        $this->assertEquals(9, $product->fresh()->stock);
        $this->assertEquals(38, $order->fresh()->total);
        $last = app(OrderPaymentService::class)->pay($this->payment(), $order->fresh());
        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertEquals(57, Order::where('status', 'completed')->sum('total'));
        $this->assertNotNull($last->paid_at);
        $this->assertEquals(-3, InventoryLog::sum('quantity'));
    }

    public function test_shared_recipe_ingredients_are_aggregated_with_fractional_stock(): void
    {
        $this->openRegister();
        $ingredient = $this->product(1.5);
        $ingredient->update(['is_saleable' => false]);
        $product = $this->product();
        $product->ingredients()->attach($ingredient->id, ['quantity' => 0.25]);
        $order = $this->order($product, 2);
        app(OrderPaymentService::class)->pay($this->payment(), $order);
        $this->assertEquals(1, $ingredient->fresh()->stock);
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertEquals(-0.5, InventoryLog::sum('quantity'));
    }

    public function test_insufficient_stock_rolls_back_the_entire_payment(): void
    {
        $this->openRegister();
        $product = $this->product(1);
        $order = $this->order($product, 2);
        try {
            app(OrderPaymentService::class)->pay($this->payment(), $order);
            $this->fail('Expected validation error');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('stock', $e->errors());
        }
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertEquals(1, $product->fresh()->stock);
        $this->assertSame(0, InventoryLog::count());
    }

    public function test_underpayment_is_rejected_before_stock_is_changed(): void
    {
        $this->openRegister();
        $product = $this->product();
        $order = $this->order($product);
        try {
            app(OrderPaymentService::class)->pay($this->payment(['received_amount' => 1]), $order);
            $this->fail('Expected validation error');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('received_amount', $e->errors());
        }
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_an_order_cannot_be_charged_twice(): void
    {
        $this->openRegister();
        $product = $this->product();
        $order = $this->order($product);
        app(OrderPaymentService::class)->pay($this->payment(), $order);
        try {
            app(OrderPaymentService::class)->pay($this->payment(), $order);
            $this->fail('Expected validation error');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('order', $e->errors());
        }
        $this->assertEquals(8, $product->fresh()->stock);
        $this->assertSame(1, InventoryLog::count());
    }

    public function test_foreign_split_items_are_rejected(): void
    {
        $this->openRegister();
        $product = $this->product();
        $order = $this->order($product);
        $foreign = $this->order($product)->details()->first();
        $this->expectException(ValidationException::class);
        app(OrderPaymentService::class)->pay($this->payment(['selected_items' => [$foreign->id], 'split_quantities' => [$foreign->id => 1]]), $order, true);
    }

    public function test_payment_requires_open_cash_register_even_when_service_is_called_directly(): void
    {
        $order = $this->order($this->product());
        $this->expectException(ValidationException::class);
        app(OrderPaymentService::class)->pay($this->payment(), $order);
    }

    public function test_invalid_payment_methods_and_documents_do_not_complete_the_order(): void
    {
        $this->openRegister();
        $order = $this->order($this->product());
        foreach ([['payment_method' => 'invalid'], ['document_type' => 'Factura', 'client_document' => '123'], ['document_type' => 'Boleta', 'client_document' => '123']] as $invalid) {
            try {
                app(OrderPaymentService::class)->pay($this->payment($invalid), $order);
                $this->fail('Expected validation error');
            } catch (ValidationException $e) {
                $this->assertSame('pending', $order->fresh()->status);
            }
        }
    }

    public function test_invoice_details_and_header_match_discounted_payment(): void
    {
        $product = $this->product();
        $order = $this->order($product, 3);
        $order->update(['discount' => 6, 'tip' => 3, 'total' => 57, 'document_type' => 'Factura', 'serie' => 'F001', 'correlativo' => 1, 'client_name' => 'Cliente SAC', 'client_document' => '20123456789']);
        $invoice = (new \App\Services\Sunat\InvoiceBuilder(new \App\Services\Sunat\SunatConfig([])))->build($order->load('details.product'));
        $this->assertEquals(57, $invoice->getMtoImpVenta());
        $this->assertEquals(57, array_sum(array_map(fn ($line) => $line->getMtoValorVenta() + $line->getIgv(), $invoice->getDetails())));
    }

    public function test_boleta_assigns_series_and_paid_date(): void
    {
        $this->openRegister();
        \App\Models\DocumentSeries::create(['document_type' => 'boleta', 'serie' => 'B001', 'last_number' => 0, 'is_active' => true]);
        $paid = app(OrderPaymentService::class)->pay($this->payment(['document_type' => 'Boleta', 'client_document' => '12345678']), $this->order($this->product()));
        $this->assertSame('B001-1', $paid->full_number);
        $this->assertNotNull($paid->paid_at);
        $this->assertEquals(40, (float) $paid->total_gravada + (float) $paid->igv);
    }
}
