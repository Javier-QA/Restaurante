<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\RestaurantTestCase;

class InventoryMigrationTest extends RestaurantTestCase
{
    public function test_migration_preserves_sales_and_supports_fractional_inventory(): void
    {
        $product = $this->product(10);
        $order = $this->order($product);
        $order->update(['status' => 'completed']);
        $legacyDate = $order->fresh()->getRawOriginal('updated_at');
        Schema::table('orders', fn ($table) => $table->dropColumn('paid_at'));
        $migration = require database_path('migrations/2026_10_08_000001_improve_payments_and_inventory.php');
        $migration->up();
        $this->assertSame($legacyDate, DB::table('orders')->value('paid_at'));
        $this->assertSame(1, DB::table('orders')->count());
        DB::table('products')->where('id', $product->id)->update(['stock' => 9.125, 'description' => 'Producto de prueba']);
        $this->assertEquals(9.125, $product->fresh()->stock);
        $this->assertSame('Producto de prueba', DB::table('products')->value('description'));
    }
}
