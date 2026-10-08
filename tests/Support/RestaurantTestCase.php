<?php

namespace Tests\Support;

use App\Models\CashRegister;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

abstract class RestaurantTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['users', 'cash_registers', 'orders', 'order_details', 'products', 'product_ingredients', 'inventory_logs', 'clients', 'settings', 'expenses', 'deliveries', 'document_series', 'daily_summaries'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->id();
                $table->timestamps();
                $columns = match ($name) {
                    'users' => ['name', 'email', 'password', 'role'],
                    'cash_registers' => ['user_id', 'closed_by', 'opening_time', 'closing_time', 'opening_amount', 'closing_amount', 'expected_amount', 'difference', 'status', 'notes'],
                    'orders' => ['table_id', 'user_id', 'client_id', 'status', 'total', 'discount', 'tip', 'payment_method', 'received_amount', 'change_amount', 'document_type', 'client_name', 'client_document', 'cash_register_id', 'serie', 'correlativo', 'subtotal', 'igv', 'total_gravada', 'sunat_status', 'paid_at'],
                    'order_details' => ['order_id', 'product_id', 'quantity', 'price', 'status', 'note'],
                    'products' => ['category_id', 'name', 'price', 'cost', 'stock', 'controls_stock', 'is_saleable', 'is_active', 'preparation_area'],
                    'product_ingredients' => ['product_id', 'ingredient_id', 'quantity'],
                    'inventory_logs' => ['product_id', 'user_id', 'type', 'quantity', 'old_stock', 'new_stock', 'note'],
                    'clients' => ['name', 'document_number', 'email', 'phone', 'address'],
                    'settings' => ['key', 'value'],
                    'deliveries' => ['order_id', 'status', 'delivery_fee'],
                    'daily_summaries' => ['reference_date', 'generation_date', 'correlativo', 'identifier', 'total_documents', 'total_amount', 'sunat_status', 'user_id'],
                    'document_series' => ['document_code', 'document_type', 'serie', 'last_number', 'is_active'],
                    'expenses' => ['description', 'amount', 'user_id', 'cash_register_id'],
                };
                foreach ($columns as $column) {
                    if (in_array($column, ['stock', 'quantity', 'old_stock', 'new_stock', 'price', 'cost', 'total', 'discount', 'tip', 'opening_amount', 'closing_amount', 'expected_amount', 'difference', 'received_amount', 'change_amount', 'subtotal', 'igv', 'total_gravada', 'amount'])) {
                        $table->decimal($column, 14, 3)->nullable();
                    } else {
                        $table->string($column)->nullable();
                    }
                }
            });
        }
        DB::table('settings')->insert(['key' => 'company_name', 'value' => 'El Capitán']);
        $this->actingAs(User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'testing-password', 'role' => 'admin']));
    }

    protected function openRegister(): CashRegister
    {
        return CashRegister::create(['user_id' => auth()->id(), 'status' => 'open', 'opening_amount' => 50, 'opening_time' => now()]);
    }

    protected function product(float $stock = 10, float $price = 20): Product
    {
        return Product::create(['name' => 'Producto', 'price' => $price, 'stock' => $stock, 'controls_stock' => true, 'is_saleable' => true, 'is_active' => true, 'preparation_area' => 'kitchen']);
    }

    protected function order(Product $product, int $qty = 2): Order
    {
        $order = Order::create(['table_id' => 1, 'user_id' => auth()->id(), 'status' => 'pending', 'total' => $product->price * $qty, 'discount' => 0, 'tip' => 0]);
        $order->details()->create(['product_id' => $product->id, 'quantity' => $qty, 'price' => $product->price, 'status' => 'served']);

        return $order;
    }
}
