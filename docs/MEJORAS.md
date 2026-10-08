# Mejoras operativas

## Pedidos, cobros e inventario

- Servicio compartido para cobro normal y división de cuentas. Valida método de pago, efectivo recibido, cliente/documento y pertenencia de los productos seleccionados.
- Bloquea caja, pedido e inventario dentro de la transacción; un pedido cerrado no puede cobrarse otra vez.
- Cobro parcial: consume únicamente las unidades cobradas, reparte descuentos y propinas proporcionalmente, y conserva el saldo restante. La pantalla muestra el importe ajustado.
- Stock y kardex con tres decimales. Los ingredientes compartidos se acumulan antes de validar existencias; si falta stock, se revierte todo el cobro.
- Ajustes de stock transaccionales con cantidades positivas y validación de existencias.
- Registro de pedidos por clic: solo productos activos y vendibles. La mesa se bloquea para evitar crear pedidos simultáneos duplicados desde esta operación.

## Caja

- Caja global para registrar gastos, aunque quien los registre sea un mozo o cajero distinto de quien abrió el turno.
- Pantalla de caja cerrada para mozos; operaciones de escritura bloqueadas sin caja abierta.
- Apertura serializada y cierre transaccional; hay que finalizar los pedidos pendientes antes de cerrar.
- El arqueo incluye la tarifa de delivery efectivamente cobrada. Los reportes de ventas de productos continúan mostrando los importes de los pedidos; la tarifa de envío se registra separada.

## Clientes, productos y configuración

- Formularios de alta/edición de clientes y validación de DNI/RUC, email y campos de contacto.
- Selección Cocina/Barra en los formularios de productos y persistencia del área elegida.
- Recetas limitadas a insumos activos. La edición del producto y la receta se guarda en una transacción.
- Mi Perfil funciona para todos los roles y no permite elevar permisos.
- Validación de configuración antes de guardar, límites de peticiones en login/API y límite de llamadas reales al proveedor de IA.
- Restauración con argumentos escapados y búsqueda del ejecutable MySQL de Laragon. Reinicio elimina también registros dependientes y restablece claves foráneas incluso ante errores.

## SUNAT y fechas

- Prueba de regresión del orden de argumentos al enviar el resumen diario de boletas.
- Lectura del certificado desde el disco privado, con compatibilidad para certificados antiguos y extensiones `.pfx`/`.p12`.
- Facturas: detalles y cabecera usan el importe final ajustado del pedido, con reparto de redondeos. Validación del estado aceptado también al registrar una nota de crédito.
- Fecha de pago independiente `paid_at` para ventas, dashboard, reportes, comprobantes y vistas de IA.

## Límites y actualización

La fecha de pago histórica se estima desde `updated_at`; no recupera una fecha que nunca se guardó. Los reportes de utilidad siguen utilizando el costo actual de la receta/producto y no un costo histórico. El rollback conserva decimales y descripciones para evitar pérdida de datos; elimina la fecha de pago nueva.

La mejora no reconstruye consumos de inventario omitidos en cobros divididos antiguos. La migración preserva pedidos e inventario existente. Realiza un respaldo y ejecuta `php artisan migrate` al actualizar. Conserva `APP_KEY`.

Pruebas: cobro parcial/final, inventario fraccionario, stock insuficiente y reversión, pago insuficiente, cobro duplicado, productos de otro pedido, métodos/documentos inválidos, boleta con serie, importes de factura, caja cerrada, gasto en caja global, permisos de perfil, cierre con pedidos pendientes, tarifa de envío, migración, argumentos del resumen SUNAT y certificado privado P12. No se enviaron comprobantes ni consultas a servicios externos durante las pruebas.
