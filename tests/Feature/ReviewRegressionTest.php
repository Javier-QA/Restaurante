<?php
namespace Tests\Feature;

use App\Http\Controllers\{ProductController, CategoryController, PosController, SaleController};
use App\Models\{Category, Product, InventoryLog, Delivery};
use App\Services\{InventoryService, OrderPaymentService};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Schema, DB};
use Illuminate\Validation\ValidationException;
use Tests\Support\RestaurantTestCase;

class ReviewRegressionTest extends RestaurantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::table('products', function(Blueprint $t) {
            $t->softDeletes(); $t->string('unit')->nullable(); $t->decimal('minimum_stock',14,3)->default(5);
            $t->boolean('is_chef_recommendation')->default(false); $t->boolean('is_new')->default(false);
        });
        Schema::create('categories', function(Blueprint $t) {
            $t->id(); $t->string('name'); $t->boolean('is_active')->default(true); $t->string('image')->nullable(); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('tables', function(Blueprint $t) { $t->id(); $t->string('name'); $t->timestamps(); });
    }

    public function test_deleted_product_disappears_but_history_survives(): void
    {
        $p=$this->product(); app(ProductController::class)->destroy($p);
        $this->assertNotNull($p->fresh()->deleted_at);
        $data=app(ProductController::class)->index(Request::create('/products'))->getData();
        $this->assertSame(0,$data['products']->total()); $this->assertSame(1,Product::count());
    }
    public function test_category_with_only_deleted_products_can_be_removed(): void
    {
        $c=Category::create(['name'=>'Prueba']);$p=$this->product();$p->update(['category_id'=>$c->id]);
        app(ProductController::class)->destroy($p); app(CategoryController::class)->destroy($c);
        $this->assertNull(Category::find($c->id)); $this->assertSame($c->id,$p->fresh()->category->id);
    }
    public function test_fractional_whole_unit_stock_is_rejected_for_all_count_units(): void
    {
        foreach(['und','paq','caja'] as $unit) {
            $p=$this->product();$p->update(['unit'=>$unit]);
            try { app(ProductController::class)->adjustStock(Request::create('/adjust','POST',['quantity'=>0.5,'type'=>'add']),$p); $this->fail('Accepted fractional stock'); }
            catch(ValidationException $e) { $this->assertArrayHasKey('quantity',$e->errors()); }
        }
    }
    public function test_fractional_weight_stock_is_accepted(): void
    {
        $p=$this->product();$p->update(['unit'=>'kg']);
        app(ProductController::class)->adjustStock(Request::create('/adjust','POST',['quantity'=>0.5,'type'=>'add']),$p);
        $this->assertEquals(10.5,$p->fresh()->stock);$this->assertEquals(0.5,InventoryLog::first()->quantity);
    }
    public function test_payment_sets_automatic_print_reference(): void
    {
        $this->openRegister();$p=$this->product();$o=$this->order($p);
        $r=app(PosController::class)->checkout(Request::create('/pay','POST',['payment_method'=>'cash','document_type'=>'Ticket','received_amount'=>100]),$o);
        $this->assertStringContainsString('print_order='.$o->id,$r->getTargetUrl());
        $this->assertSame($o->id,session('print_order_id'));
    }
    public function test_partial_payment_sets_print_reference_for_paid_order(): void
    {
        $this->openRegister();$p=$this->product();$o=$this->order($p);$line=$o->details()->first();
        $r=app(PosController::class)->processSplit(Request::create('/split','POST',['payment_method'=>'cash','document_type'=>'Ticket','received_amount'=>100,'selected_items'=>[$line->id],'split_quantities'=>[$line->id=>1]]),$o);
        $this->assertNotSame($o->id,session('print_order_id'));
        $this->assertStringContainsString('print_order='.session('print_order_id'),$r->getTargetUrl());
    }
    public function test_ingredient_used_in_recipe_cannot_be_deleted(): void
    {
        $p=$this->product();$p->update(['is_saleable'=>false,'unit'=>'kg']);$dish=$this->product();
        $dish->ingredients()->attach($p->id,['quantity'=>0.5]);
        try { app(ProductController::class)->destroy($p); $this->fail('Ingredient was deleted'); }
        catch (ValidationException $e) { $this->assertArrayHasKey('product', $e->errors()); }
        $this->assertNull($p->fresh()->deleted_at); $this->assertEquals(10, $p->fresh()->stock);
    }
    public function test_closed_order_cannot_be_moved(): void
    {
        DB::table('tables')->insert(['id'=>2,'name'=>'Mesa 2']);$p=$this->product();$o=$this->order($p);$o->update(['status'=>'completed']);
        try { app(PosController::class)->moveTable(Request::create('/move','POST',['target_table_id'=>2]),$o); $this->fail('Paid order was moved'); }
        catch (ValidationException $e) { $this->assertArrayHasKey('target_table_id', $e->errors()); }
        $this->assertSame('1',(string)$o->fresh()->table_id);
    }
    public function test_delivery_shipping_is_included_in_sales_summary(): void
    {
        $r=$this->openRegister();$p=$this->product();$o=$this->order($p,1);
        $o->update(['status'=>'completed','paid_at'=>now(),'payment_method'=>'cash','cash_register_id'=>$r->id]);
        Delivery::create(['order_id'=>$o->id,'status'=>'delivered','delivery_fee'=>5]);
        $view=app(SaleController::class)->index(Request::create('/sales'))->getData();
        $this->assertEquals(25,$r->collectedSales('cash'));$this->assertEquals(25,$view['totalCash']);
    }
    public function test_reset_kardex_zeros_stock_and_is_admin_only(): void
    {
        $p=$this->product();$dish=$this->product();$dish->update(['controls_stock'=>false]);
        InventoryLog::create(['product_id'=>$p->id,'user_id'=>auth()->id(),'type'=>'entry','quantity'=>10,'old_stock'=>0,'new_stock'=>10]);
        $this->delete('/inventory/logs/reset/all')->assertRedirect(route('inventory.logs'));
        $this->assertEquals(0,$p->fresh()->stock);$this->assertEquals(10,$dish->fresh()->stock);$this->assertSame(0,InventoryLog::count());
        auth()->user()->update(['role'=>'waiter']); $p->update(['stock'=>7]); $this->delete('/inventory/logs/reset/all')->assertRedirect(route('pos.index')); $this->assertEquals(7,$p->fresh()->stock);
    }
    public function test_credit_note_lines_match_discounted_header(): void
    {
        $p=$this->product();$o=$this->order($p,2);
        $o->update(['document_type'=>'Boleta','serie'=>'B001','correlativo'=>1,'discount'=>10,'total'=>30,'total_gravada'=>25.42,'igv'=>4.58]);
        $cn=new \App\Models\CreditNote(['order_id'=>$o->id,'serie'=>'BC01','correlativo'=>1,'reason_code'=>'01','reason_description'=>'Anulación','total'=>30,'subtotal'=>25.42,'igv'=>4.58]);
        $note=(new \App\Services\Sunat\CreditNoteBuilder(new \App\Services\Sunat\SunatConfig([])))->build($cn);
        $lines=array_sum(array_map(fn($l)=>$l->getMtoValorVenta()+$l->getIgv(),$note->getDetails()));
        $this->assertEquals(30,$note->getMtoImpVenta());$this->assertEquals(30,$lines);
    }
    public function test_delivery_document_lines_and_print_include_shipping_and_discount(): void
    {
        $p = $this->product(); $o = $this->order($p, 2);
        $o->update(['status'=>'completed', 'paid_at'=>now(), 'document_type'=>'Boleta', 'serie'=>'B001', 'correlativo'=>1, 'discount'=>10, 'total'=>30]);
        Delivery::create(['order_id'=>$o->id,'status'=>'delivered','delivery_fee'=>5]);
        $config = new \App\Services\Sunat\SunatConfig([]);
        $invoice = (new \App\Services\Sunat\InvoiceBuilder($config))->build($o);
        $this->assertEquals(35, $invoice->getMtoImpVenta());
        $lines = $invoice->getDetails();
        $this->assertCount(2, $lines);
        $this->assertEquals(30, $lines[0]->getMtoValorVenta() + $lines[0]->getIgv());
        $this->assertEquals(5, $lines[1]->getMtoValorVenta() + $lines[1]->getIgv());
        $html = view('billing.pdf.ticket', ['order'=>$o, 'documentLines'=>$lines, 'config'=>$config, 'company'=>['ruc'=>'123','razon_social'=>'Prueba','nombre_comercial'=>'Prueba','direccion'=>'Prueba'], 'qrBase64'=>''])->render();
        $this->assertStringContainsString('Servicio de delivery', $html);
        $this->assertStringContainsString('35.00', $html);
    }

    public function test_historical_deleted_recipe_ingredient_blocks_payment_without_consuming_stock(): void
    {
        $ingredient = $this->product(); $dish = $this->product();
        $dish->ingredients()->attach($ingredient->id, ['quantity'=>0.5]);
        $ingredient->deleted_at = now(); $ingredient->save();
        $o = $this->order($dish, 1);
        try { DB::transaction(fn()=>app(InventoryService::class)->consume($o->details, 'Test')); $this->fail('Consumed deleted ingredient'); }
        catch (ValidationException $e) { $this->assertArrayHasKey('stock', $e->errors()); }
        $this->assertEquals(10, $ingredient->fresh()->stock);
        $this->assertSame(0, InventoryLog::count());
    }

    public function test_ticket_and_precheck_have_distinct_titles(): void
    {
        $p = $this->product(); $o = $this->order($p, 1);
        $this->assertStringContainsString('PRECUENTA #', view('sales.ticket', ['order'=>$o, 'settings'=>[]])->render());
        $o->update(['status'=>'completed', 'paid_at'=>now(), 'document_type'=>'Ticket']);
        $html = app(SaleController::class)->ticket($o)->render();
        $this->assertStringContainsString('TICKET #', $html);
        $this->assertStringNotContainsString('PRECUENTA #', $html);
    }
    public function test_cart_total_does_not_apply_discount_and_tip_twice(): void
    {
        $p = $this->product(); $o = $this->order($p, 2);
        $o->update(['discount'=>10, 'tip'=>2, 'total'=>32]);
        $html = view('pos.partials.cart', ['order'=>$o,'currency'=>'S/'])->render();
        $this->assertStringContainsString('id="cartTotalValue" value="32.00"', $html);
        $this->assertStringNotContainsString('value="24.00"', $html);
    }
    public function test_document_rounding_does_not_make_last_line_negative(): void
    {
        $p = $this->product(10, 1); $o = $this->order($p, 1);
        for ($i=0; $i<3; $i++) { $o->details()->create(['product_id'=>$p->id, 'quantity'=>1, 'price'=>1, 'status'=>'served']); }
        $o->update(['total'=>0.02, 'discount'=>3.98]);
        $lines = (new \App\Services\Sunat\InvoiceBuilder(new \App\Services\Sunat\SunatConfig([])))->buildDetails($o);
        $sum = 0;
        foreach ($lines as $line) {
            $amount = $line->getMtoValorVenta() + $line->getIgv();
            $this->assertGreaterThanOrEqual(0, $amount);
            $this->assertGreaterThanOrEqual(0, $line->getIgv());
            $sum += $amount;
        }
        $this->assertEqualsWithDelta(0.02, $sum, 0.000001);
    }
}
