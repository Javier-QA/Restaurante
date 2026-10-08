# Chatbot y Asistente IA originales de SYS

Desde la carpeta `restaurante` en la terminal de VS Code:

```powershell
php artisan optimize:clear
php artisan migrate
```

Inicia MySQL y entra como administrador. Los enlaces existentes Asistente IA y Chatbot abren las pantallas originales de SYS. El chat flotante está disponible en las pantallas del administrador. La configuración se abre desde el Asistente o desde Configuración del negocio → Configurar IA. Puedes probar conexión, cambiar proveedor/URL/modelo/clave y habilitar resúmenes. La configuración ia_* anterior de settings se conserva, con la clave cifrada con APP_KEY. No se necesita npm build para los recursos estáticos nuevos.

Se conservan los scripts de SYS para preguntas rápidas, seguimiento, tablas, gráficos, CSV, favoritas, recientes y conversación. El adaptador usa autenticación, permisos, CSRF y sesiones de Laravel. Las consultas son de solo lectura con límite de tiempo y filas.

Las vistas nuevas v_ia_sys_* leen pedidos, productos/recetas, inventario, gastos, clientes, reservas, cajas y delivery de Laravel. No reemplazan las vistas antiguas: los registros históricos ai_queries y sus vistas quedan disponibles en la BD. Los reportes nuevos se guardan en ia_consultas. Compras/proveedores y stock mínimo no existen en el esquema entregado; la IA explica cuando no dispone de esos datos. La fecha de venta es paid_at; los registros históricos se inicializan con updated_at como estimación; el costo de utilidad es el actual, no histórico. El costo de envío se presenta por separado.

Validado con PHP, rutas, compilación de plantillas y copia de la base de datos suministrada. La conexión del proveedor se probó con respuestas simuladas; requiere prueba real con tu clave/modelo. Pendiente inspección visual en tu navegador.
