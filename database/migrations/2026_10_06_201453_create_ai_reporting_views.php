<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Crea vistas de solo lectura destinadas a Chat IA y Asistente IA.
     *
     * Las vistas exponen únicamente información necesaria para análisis
     * operativo y evitan datos personales o archivos fiscales sensibles.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW v_ia_ventas AS
            SELECT
                o.id AS venta_id,
                o.status AS estado,
                o.document_type AS tipo_documento,
                o.total,
                o.subtotal,
                o.igv,
                o.discount AS descuento,
                o.tip AS propina,
                o.payment_method AS metodo_pago,
                o.table_id AS mesa_id,
                t.name AS mesa,
                o.cash_register_id AS caja_id,
                o.created_at AS fecha_venta
            FROM orders o
            LEFT JOIN tables t ON t.id = o.table_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_ia_detalle_ventas AS
            SELECT
                od.id AS detalle_id,
                od.order_id AS venta_id,
                od.product_id AS producto_id,
                p.name AS producto,
                c.name AS categoria,
                od.quantity AS cantidad,
                od.price AS precio_unitario,
                (od.quantity * od.price) AS importe,
                od.status AS estado_detalle,
                o.status AS estado_venta,
                o.created_at AS fecha_venta
            FROM order_details od
            INNER JOIN orders o ON o.id = od.order_id
            INNER JOIN products p ON p.id = od.product_id
            LEFT JOIN categories c ON c.id = p.category_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_ia_productos AS
            SELECT
                p.id AS producto_id,
                p.name AS producto,
                p.code AS codigo,
                c.name AS categoria,
                p.preparation_area AS area_preparacion,
                p.price AS precio,
                p.promotional_price AS precio_promocional,
                p.cost AS costo,
                p.stock,
                p.controls_stock AS controla_stock,
                p.is_saleable AS vendible,
                p.is_active AS activo,
                p.is_chef_recommendation AS recomendacion_chef,
                p.is_new AS nuevo
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_ia_inventario AS
            SELECT
                il.id AS movimiento_id,
                il.product_id AS producto_id,
                p.name AS producto,
                c.name AS categoria,
                il.type AS tipo_movimiento,
                il.quantity AS cantidad,
                il.old_stock AS stock_anterior,
                il.new_stock AS stock_nuevo,
                il.created_at AS fecha_movimiento
            FROM inventory_logs il
            INNER JOIN products p ON p.id = il.product_id
            LEFT JOIN categories c ON c.id = p.category_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_ia_gastos AS
            SELECT
                e.id AS gasto_id,
                e.description AS descripcion,
                e.amount AS monto,
                e.cash_register_id AS caja_id,
                e.created_at AS fecha_gasto
            FROM expenses e
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_ia_cajas AS
            SELECT
                cr.id AS caja_id,
                cr.opening_time AS fecha_apertura,
                cr.closing_time AS fecha_cierre,
                cr.opening_amount AS monto_apertura,
                cr.closing_amount AS monto_cierre,
                cr.expected_amount AS monto_esperado,
                cr.difference AS diferencia,
                cr.status AS estado,
                cr.created_at
            FROM cash_registers cr
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_ia_reservas AS
            SELECT
                r.id AS reserva_id,
                r.reservation_time AS fecha_reserva,
                r.people AS personas,
                r.table_id AS mesa_id,
                t.name AS mesa,
                r.status AS estado,
                r.created_at
            FROM reservations r
            LEFT JOIN tables t ON t.id = r.table_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_ia_delivery AS
            SELECT
                d.id AS delivery_id,
                d.order_id AS venta_id,
                d.delivery_type AS tipo_entrega,
                d.driver_id AS repartidor_id,
                dr.name AS repartidor,
                d.status AS estado,
                d.payment_method AS metodo_pago,
                d.delivery_fee AS costo_delivery,
                d.scheduled_at AS fecha_programada,
                d.delivered_at AS fecha_entrega,
                d.created_at AS fecha_registro
            FROM deliveries d
            LEFT JOIN delivery_drivers dr ON dr.id = d.driver_id
        ");
    }

    /**
     * Elimina las vistas en orden seguro.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_ia_delivery');
        DB::statement('DROP VIEW IF EXISTS v_ia_reservas');
        DB::statement('DROP VIEW IF EXISTS v_ia_cajas');
        DB::statement('DROP VIEW IF EXISTS v_ia_gastos');
        DB::statement('DROP VIEW IF EXISTS v_ia_inventario');
        DB::statement('DROP VIEW IF EXISTS v_ia_productos');
        DB::statement('DROP VIEW IF EXISTS v_ia_detalle_ventas');
        DB::statement('DROP VIEW IF EXISTS v_ia_ventas');
    }
};