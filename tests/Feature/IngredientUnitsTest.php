<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Services\OrderPaymentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\Support\RestaurantTestCase;

class IngredientUnitsTest extends RestaurantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
            $table->boolean('is_chef_recommendation')->default(false);
            $table->boolean('is_new')->default(false);
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->boolean('is_active'); $table->timestamps(); $table->softDeletes();
        });
        $migration = require database_path('migrations/2026_10_08_000002_add_inventory_units_to_products.php');
        $migration->up();
    }

    private function fields(Category $category, array $extra = []): array
    {
        return $extra + ['name' => 'Limón', 'category_id' => $category->id, 'preparation_area' => 'kitchen', 'price' => 0, 'cost' => 4, 'unit' => 'kg', 'minimum_stock' => 0.5, 'controls_stock' => 'on'];
    }

    public function test_hidden_pos_category_is_available_for_inventory_and_fractional_ingredient(): void
    {
        $category = Category::create(['name' => 'Insumos', 'is_active' => false]);
        $view = app(ProductController::class)->create();
        $this->assertTrue($view->getData()['categories']->contains('id', $category->id));
        app(ProductController::class)->store(Request::create('/products', 'POST', $this->fields($category, ['stock' => 3.125])));
        $ingredient = Product::firstOrFail();
        $this->assertSame('kg', $ingredient->unit);
        $this->assertFalse($ingredient->is_saleable);
        $this->assertEquals(3.125, $ingredient->stock);
        $this->assertEquals(3.125, InventoryLog::firstOrFail()->quantity);
        $this->assertEquals(0.5, $ingredient->minimum_stock);
        $this->assertFalse((bool) $category->fresh()->is_active);
    }

    public function test_unit_migration_preserves_legacy_stock_cost_and_recipe(): void
    {
        $ingredient = $this->product(3.125);
        $dish = $this->product();
        $dish->ingredients()->attach($ingredient->id, ['quantity' => 0.125]);
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['unit', 'minimum_stock']));
        (require database_path('migrations/2026_10_08_000002_add_inventory_units_to_products.php'))->up();
        $this->assertNull($ingredient->fresh()->unit);
        $this->assertSame('und', $ingredient->unit_display);
        $this->assertEquals(3.125, $ingredient->fresh()->stock);
        $this->assertEquals(0.125, $dish->ingredients()->first()->pivot->quantity);
    }

    public function test_assigned_units_cannot_be_relabelled_and_change_stock_meaning(): void
    {
        $category = Category::create(['name' => 'Insumos', 'is_active' => false]);
        $ingredient = $this->product(3);
        $ingredient->update(['unit' => 'kg', 'is_saleable' => false]);
        try {
            app(ProductController::class)->update(Request::create('/products/'.$ingredient->id, 'PUT', $this->fields($category, ['unit' => 'g'])), $ingredient);
            $this->fail('Expected validation error');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('unit', $e->errors());
        }
        $this->assertSame('kg', $ingredient->fresh()->unit);
        $this->assertEquals(3, $ingredient->fresh()->stock);
    }

    public function test_payment_consumes_recipe_in_its_assigned_unit_without_changing_sales(): void
    {
        $this->openRegister();
        $ingredient = $this->product(3);
        $ingredient->update(['unit' => 'kg', 'is_saleable' => false]);
        $dish = $this->product();
        $dish->ingredients()->attach($ingredient->id, ['quantity' => 0.125]);
        $order = $this->order($dish, 2);
        app(OrderPaymentService::class)->pay(Request::create('/pos/pay', 'POST', ['payment_method' => 'cash', 'document_type' => 'Ticket', 'received_amount' => 100]), $order);
        $this->assertEquals(2.75, $ingredient->fresh()->stock);
        $this->assertSame('kg', $ingredient->fresh()->unit);
        $this->assertEquals(40, $order->fresh()->total);
        $this->assertEquals(-0.25, InventoryLog::sum('quantity'));
    }
}
