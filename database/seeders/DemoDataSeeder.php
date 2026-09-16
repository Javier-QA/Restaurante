<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Area;
use App\Models\Table;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Reservation;
use App\Models\Expense;
use App\Models\InventoryLog;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // 1. LIMPIAR BASE DE DATOS (MANTENER ADMIN Y SETTINGS)
        DB::table('inventory_logs')->truncate();
        DB::table('product_ingredients')->truncate();
        DB::table('order_details')->truncate();
        DB::table('orders')->truncate();
        DB::table('reservations')->truncate();
        DB::table('expenses')->truncate();
        DB::table('tables')->truncate();
        DB::table('areas')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('clients')->truncate();
        
        // Limpiar usuarios excepto el administrador (asumiendo que el admin es ID 1 o tiene rol admin)
        User::where('role', '!=', 'admin')->delete();

        Schema::enableForeignKeyConstraints();

        $adminId = User::where('role', 'admin')->first()->id ?? 1;
        $now = Carbon::now();

        // 2. CREAR USUARIOS (ROLES) - Hasta llegar a 10
        $roles = ['cashier', 'waiter', 'kitchen', 'waiter', 'cashier', 'waiter', 'kitchen', 'waiter', 'waiter'];
        foreach ($roles as $index => $role) {
            User::create([
                'name' => ucfirst($role) . ' ' . ($index + 1),
                'email' => $role . ($index + 1) . '@restaurante.com',
                'password' => bcrypt('password'),
                'role' => $role,
            ]);
        }

        // 3. CREAR CATEGORÍAS (9)
        $categoryNames = ['Entradas', 'Ceviches y Tiraditos', 'Calientes', 'Los Bravos', 'Dúos', 'Tríos', 'Para los Engreídos', 'Para la Sed', 'Para Toda Ocasión'];
        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[] = Category::create([
                'name' => $name,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        // 4. CREAR PRODUCTOS (52)
        $productsData = [
            // ENTRADAS
            ['LANGOSTINOS AL PANKO (8 unid.)', 1, 28.00],
            ['CONCHITAS A LA PARMESANA (10 unid.)', 1, 35.00],
            ['CHORITOS A LA CHALACA (10 unid.)', 1, 28.00],
            ['LECHE DE TIGRE', 1, 18.00],
            ['LECHE DE TIGRE EL CAPITÁN', 1, 25.00],
            ['CAUSA DE PULPA DE CANGREJO', 1, 30.00],
            ['CAUSA DE LANGOSTINOS', 1, 28.00],
            ['CAUSA ACEVICHADA', 1, 27.00],

            // CEVICHES Y TIRADITOS
            ['CEVICHE EL CAPITÁN', 2, 45.00],
            ['CEVICHE DE PESCADO', 2, 32.00],
            ['CEVICHE MIXTO', 2, 35.00],
            ['CEVICHE DE PULPO', 2, 43.00],
            ['CEVICHE CON ESQUINA', 2, 35.00],
            ['TIRADITO DE PESCADO', 2, 30.00],
            ['TIRADITO DE PEJERREY', 2, 25.00],

            // CALIENTES
            ['CHITA AL AJO', 3, 50.00],
            ['PESCADO A LO MACHO', 3, 50.00],
            ['PESCADO FRITO', 3, 45.00],
            ['CHICHARRÓN DE PESCADO', 3, 32.00],
            ['CHICHARRÓN MIXTO', 3, 35.00],
            ['CHICHARRÓN DE CALAMAR', 3, 40.00],
            ['JALEA EL CAPITÁN', 3, 40.00],
            ['ARROZ CON MARISCOS', 3, 32.00],
            ['ARROZ CON LANGOSTINOS', 3, 35.00],
            ['FILETE PESCADO', 3, 32.00],
            ['CHAUFA DE LANGOSTINOS', 3, 32.00],

            // LOS BRAVOS
            ['SUDADO DE PESCADO', 4, 45.00],
            ['PARIHUELA', 4, 45.00],
            ['CHILCANO DE PESCADO', 4, 12.00],

            // DÚOS
            ['DÚO CLASICO', 5, 34.00],
            ['DÚO CHINITO', 5, 34.00],
            ['DÚO FRITO', 5, 34.00],
            ['DÚO MI CAUSA', 5, 38.00],

            // TRÍOS
            ['EL CASUAL', 6, 44.00],
            ['EL CAPITÁN', 6, 44.00],
            ['EL ORIENTAL', 6, 44.00],

            // PARA LOS ENGREÍDOS
            ['SPAGUETTI SALTADO', 7, 35.00],
            ['LOMO SALTADO', 7, 35.00],
            ['LOMO SALTADO A LO POBRE', 7, 37.00],
            ['MILANESA DE POLLO', 7, 28.00],
            ['POLLO A LA PLANCHA', 7, 25.00],

            // PARA LA SED
            ['CHICHA MORADA', 8, 18.00],
            ['MARACUYA', 8, 18.00],
            ['LIMONADA', 8, 15.00],
            ['GASEOSA 600ml', 8, 5.00],
            ['AGUA SAN MATEO', 8, 3.00],

            // PARA TODA OCASIÓN
            ['CERVEZA STELLA ARTOIS 330ml', 9, 8.00],
            ['CERVEZA HEINEKEN 330ml', 9, 8.00],
            ['CERVEZA CUSQUEÑA 310ml', 9, 7.00],
            ['CERVEZA PILSEN 630ml', 9, 10.00],
            ['PISCO SOUR', 9, 15.00],
            ['CHILCANO DE PISCO', 9, 15.00]
        ];
        $products = [];
        foreach ($productsData as $index => $p) {
            $products[] = Product::create([
                'category_id' => $categories[$p[1] - 1]->id,
                'name' => $p[0],
                'price' => $p[2],
                'stock' => rand(20, 100),
                'is_saleable' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 5. CREAR ÁREAS Y MESAS (10 Mesas en total)
        $area1 = Area::create(['name' => 'Salón Principal']);
        $area2 = Area::create(['name' => 'Terraza']);
        $tables = [];
        for ($i = 1; $i <= 6; $i++) {
            $tables[] = Table::create(['area_id' => $area1->id, 'name' => 'Mesa ' . $i, 'seats' => 4, 'status' => 'available']);
        }
        for ($i = 1; $i <= 4; $i++) {
            $tables[] = Table::create(['area_id' => $area2->id, 'name' => 'T-' . $i, 'seats' => 2, 'status' => 'available']);
        }

        // 6. CREAR CLIENTES (10)
        $clients = [];
        for ($i = 1; $i <= 10; $i++) {
            $clients[] = Client::create([
                'name' => 'Cliente Frecuente ' . $i,
                'document_type' => 'DNI',
                'document_number' => 70000000 + $i,
                'email' => 'cliente'.$i.'@correo.com',
                'phone' => '99988877'.$i,
            ]);
        }

        // 7. CREAR GASTOS (10)
        $expenseDesc = ['Compra de verduras', 'Pago de luz', 'Compra de carnes', 'Mantenimiento', 'Artículos limpieza', 'Pago de agua', 'Publicidad', 'Compra de bebidas', 'Gas', 'Transporte'];
        foreach ($expenseDesc as $index => $desc) {
            Expense::create([
                'description' => $desc,
                'amount' => rand(50, 300),
                'user_id' => $adminId,
                'created_at' => $now->copy()->subDays(rand(1, 30)),
            ]);
        }

        // 8. CREAR RESERVAS (10 - distribuidas entre pasado y futuro)
        for ($i = 1; $i <= 10; $i++) {
            Reservation::create([
                'client_name' => 'Reserva ' . $i,
                'phone' => '90010020'.$i,
                'reservation_time' => $now->copy()->addDays(rand(-10, 10))->setHour(rand(12, 21))->setMinute(0),
                'people' => rand(2, 6),
                'table_id' => $tables[rand(0, 9)]->id,
                'status' => rand(0, 1) ? 'confirmed' : 'pending',
                'note' => 'Reserva generada automáticamente',
            ]);
        }

        // 9. CREAR ÓRDENES HISTÓRICAS PARA EL GRÁFICO (Últimos 12 meses + Este mes)
        // Generaremos unas 150 órdenes para tener una curva bonita
        for ($i = 0; $i < 150; $i++) {
            $orderDate = $now->copy()->subDays(rand(0, 360));
            
            $orderTotal = 0;
            $order = Order::create([
                'table_id' => $tables[rand(0, 9)]->id,
                'user_id' => $adminId,
                'client_id' => rand(0, 1) ? $clients[rand(0, 9)]->id : null,
                'client_name' => 'Público General',
                'status' => 'completed',
                'document_type' => 'Ticket',
                'payment_method' => rand(0, 2) ? 'cash' : 'card',
                'total' => 0, // Se actualiza luego
                'received_amount' => 0,
                'change_amount' => 0,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Detalles de orden (1 a 4 productos por orden)
            $numItems = rand(1, 4);
            for ($j = 0; $j < $numItems; $j++) {
                $prod = $products[rand(0, 9)];
                $qty = rand(1, 3);
                $price = $prod->price;
                $subtotal = $qty * $price;
                $orderTotal += $subtotal;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $prod->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'status' => 'served',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
                
                // Inventario Log
                InventoryLog::create([
                    'product_id' => $prod->id,
                    'user_id' => $adminId,
                    'type' => 'sale',
                    'quantity' => -$qty,
                    'note' => 'Venta Orden #' . $order->id,
                    'created_at' => $orderDate,
                ]);
            }

            $order->update([
                'total' => $orderTotal,
                'received_amount' => $orderTotal,
            ]);
        }

        // 10. CREAR ÓRDENES ACTIVAS (Para que se vean mesas activas en el dashboard)
        for ($i = 0; $i < 3; $i++) {
            $table = $tables[$i];
            $table->update(['status' => 'occupied']);
            
            $order = Order::create([
                'table_id' => $table->id,
                'user_id' => $adminId,
                'status' => 'pending',
                'client_name' => 'Público General',
                'total' => 0,
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);

            $prod = $products[rand(0, 9)];
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $prod->id,
                'quantity' => 2,
                'price' => $prod->price,
                'status' => 'cooking',
                'created_at' => clone $now,
                'updated_at' => clone $now,
            ]);
            $order->update(['total' => $prod->price * 2]);
        }

        // Setear meta mensual para el dashboard
        Setting::updateOrCreate(
            ['key' => 'monthly_goal'],
            ['value' => '8000']
        );
    }
}
