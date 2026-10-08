# Restaurante — sistema de gestión y punto de venta

Aplicación Laravel para pedidos por mesas, cocina y barra, caja, ventas, delivery/recojo, reservas, clientes, productos, recetas, inventario, facturación SUNAT y consultas con IA. El nombre, logo y colores del negocio se administran desde Configuración.

## Requisitos

- PHP 8.2 o superior y Composer.
- MySQL o MariaDB. Las vistas y consultas de IA requieren este motor.
- Extensiones PHP: PDO MySQL, cURL, mbstring, XML/DOM, SOAP, OpenSSL, fileinfo y ZIP.
- Node.js compatible con Vite 7 si se recompilan los recursos de frontend.

## Actualizar una instalación en Laragon

Primero genera una copia de seguridad desde Mantenimiento. Desde la terminal de VS Code:

```powershell
cd C:\laragon\www\restaurante
git fetch origin
git switch mejoras-integrales
git pull --ff-only origin mejoras-integrales
composer install
php artisan optimize:clear
php artisan migrate
php artisan view:cache
```

No cambies `APP_KEY`: se utiliza para descifrar la clave del proveedor de IA existente. No ejecutes `migrate:fresh` ni `db:seed` sobre una base con información real.

La migración nueva añade fecha de cobro, descripción de producto y stock/kardex con tres decimales; actualiza las vistas de IA. Los cobros históricos usan `updated_at` como estimación inicial. Los cobros nuevos guardan su fecha independiente.

## Instalación nueva

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Configura `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env`, crea esa base y ejecuta:

```powershell
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

El seeder crea una cuenta local de demostración `admin@admin.com` con contraseña `password`. Cámbiala antes de utilizar el sistema con información real. Abre la caja para registrar pedidos y cobros.

## Roles

Administrador: gestión completa e IA. Cajero: caja, ventas y facturación. Mozo: pedidos, clientes, reservas y delivery. Cocina y barra: sus propias estaciones de preparación. Cada usuario puede actualizar su nombre y contraseña desde Mi Perfil sin cambiar su rol.

## IA y SUNAT

Consulta [docs/IA_SYS.md](docs/IA_SYS.md) para el chatbot y asistente. La configuración admite Gemini, Groq, OpenRouter, Ollama y otros proveedores compatibles con `/chat/completions`; la disponibilidad de cada modelo depende del proveedor. La IA consulta datos, genera tablas/gráficos y exporta CSV; no ejecuta cambios en los datos del negocio.

Para SUNAT configura ambiente, RUC, datos fiscales, credenciales y certificado en Configuración. Facturas: envío individual. Boletas: resumen diario. Certificados `.pfx` y `.p12`: disco privado. La aceptación por SUNAT requiere una prueba real con las credenciales del negocio.

## Validación

```powershell
php artisan test
php artisan route:list
php artisan view:cache
```

Las pruebas funcionales utilizan SQLite en memoria y datos aislados; no conectan al proveedor de IA ni a SUNAT. La validación SQL adicional de la mejora se ejecutó con MariaDB 10.11 sobre una copia del respaldo incluido. Las pruebas de concurrencia entre varias conexiones y la inspección visual en Laragon siguen requiriendo comprobación local.

Consulta [docs/MEJORAS.md](docs/MEJORAS.md) para los cambios y límites de datos históricos.
