@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--text-main) !important;"><i class="bi bi-bicycle me-2" style="color:var(--text-main) !important;"></i>Delivery y Para Llevar</h2>
            <p class="text-muted small mb-0 mt-1">Gestión de pedidos a domicilio y para llevar.</p>
        </div>
        <div>
            <a href="{{ route('delivery.drivers') }}" class="btn btn-outline-secondary me-2" style="border: 1.5px solid #6c757d !important;">
                <i class="bi bi-person-vcard me-1"></i> Delivery
            </a>
            <a href="{{ route('delivery.create') }}" class="btn btn-primary shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Pedido
            </a>
        </div>
    </div>



    {{-- KANBAN BOARD --}}
    <div class="row g-3">
        {{-- Pendiente --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-header border-0 bg-transparent pt-3 pb-2">
                    <h6 class="fw-bold mb-0 delivery-column-title" style="--status-color: {{ $statuses['pending'] ?? '#f59e0b' }}; color: var(--status-color) !important;">
                        <i class="bi bi-hourglass-split me-1" style="color: {{ $statuses['pending'] ?? '#f59e0b' }} !important;"></i> <span style="color: {{ $statuses['pending'] ?? '#f59e0b' }} !important;">Pendiente</span>
                        <span id="count-pending" class="badge bg-warning text-dark rounded-pill float-end">{{ $counts['pending'] }}</span>
                    </h6>
                </div>
                <div class="card-body p-2" id="col-pending">
                    @foreach($deliveries->get('pending', []) as $delivery)
                        @include('delivery.partials.card', ['delivery' => $delivery])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Preparando --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-header border-0 bg-transparent pt-3 pb-2">
                    <h6 class="fw-bold mb-0 delivery-column-title" style="--status-color: {{ $statuses['preparing'] ?? '#3b82f6' }}; color: var(--status-color) !important;">
                        <i class="bi bi-fire me-1" style="color: {{ $statuses['preparing'] ?? '#3b82f6' }} !important;"></i> <span style="color: {{ $statuses['preparing'] ?? '#3b82f6' }} !important;">Preparando</span>
                        <span id="count-preparing" class="badge bg-primary rounded-pill float-end">{{ $counts['preparing'] }}</span>
                    </h6>
                </div>
                <div class="card-body p-2" id="col-preparing">
                    @foreach($deliveries->get('preparing', []) as $delivery)
                        @include('delivery.partials.card', ['delivery' => $delivery])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- En Camino --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-header border-0 bg-transparent pt-3 pb-2">
                    <h6 class="fw-bold mb-0 delivery-column-title" style="--status-color: {{ $statuses['on_way'] ?? '#f97316' }}; color: var(--status-color) !important;">
                        <i class="bi bi-bicycle me-1" style="color: {{ $statuses['on_way'] ?? '#f97316' }} !important;"></i> <span style="color: {{ $statuses['on_way'] ?? '#f97316' }} !important;">Listos / En camino</span>
                        <span id="count-on_way" class="badge rounded-pill float-end" style="background-color: #f97316;">{{ $counts['on_way'] }}</span>
                    </h6>
                </div>
                <div class="card-body p-2" id="col-on_way">
                    @foreach($deliveries->get('on_way', []) as $delivery)
                        @include('delivery.partials.card', ['delivery' => $delivery])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Entregado --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-header border-0 bg-transparent pt-3 pb-2">
                    <h6 class="fw-bold mb-0 delivery-column-title" style="--status-color: {{ $statuses['delivered'] ?? '#22c55e' }}; color: var(--status-color) !important;">
                        <i class="bi bi-check2-all me-1" style="color: {{ $statuses['delivered'] ?? '#22c55e' }} !important;"></i> <span style="color: {{ $statuses['delivered'] ?? '#22c55e' }} !important;">Entregados Hoy</span>
                        <span id="count-delivered" class="badge bg-success rounded-pill float-end">{{ $counts['delivered'] }}</span>
                    </h6>
                </div>
                <div class="card-body p-2" id="col-delivered">
                    @foreach($deliveries->get('delivered', []) as $delivery)
                        @include('delivery.partials.card', ['delivery' => $delivery])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<style>

/* =========================================================
   TABLERO DELIVERY / RECOJO
   ========================================================= */

.delivery-order-card {
    background: var(--card-bg);
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    padding: 15px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    box-shadow: 0 2px 8px var(--theme-shadow);
}

.delivery-order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px var(--theme-shadow);
    border-color: var(--primary);
}

.delivery-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 13px;
}

.delivery-order-number {
    background: var(--light-bg);
    color: var(--text-main);
    border-radius: 7px;
    padding: 4px 8px;
    font-size: .72rem;
    font-weight: 700;
}

.delivery-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 20px;
    padding: 4px 8px;
    font-size: .68rem;
    font-weight: 700;
}

.delivery-type-badge.delivery {
    background: var(--light-bg);
    color: var(--accent-1);
}

.delivery-type-badge.pickup {
    background: var(--light-bg);
    color: var(--accent-2);
}

.delivery-time {
    color: var(--text-muted);
    font-size: .73rem;
    white-space: nowrap;
}

.delivery-customer {
    color: var(--text-main);
    font-size: .95rem;
    font-weight: 700;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.delivery-location {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    color: var(--text-muted);
    font-size: .78rem;
    margin-bottom: 13px;
}

.delivery-location i {
    flex-shrink: 0;
}

.delivery-location span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.delivery-card-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 10px 0;
    border-top: 1px solid var(--border-soft);
}

.delivery-payment,
.delivery-driver,
.delivery-ready,
.delivery-pending,
.delivery-preparing {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: .72rem;
}

.delivery-payment {
    color: var(--text-muted);
}

.delivery-driver {
    color: var(--text-muted);
    min-width: 0;
    max-width: 110px;
}

.delivery-driver span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.delivery-ready {
    color: var(--accent-2);
    font-weight: 700;
}

.delivery-pending {
    color: #ef4444;
    font-weight: 700;
}

.delivery-preparing {
    color: var(--delivery-card-status-color) !important;
    font-weight: 700;
}

.delivery-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding-top: 11px;
    border-top: 1px solid var(--border-soft);
}

.delivery-total-label {
    display: block;
    color: var(--text-muted);
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 1px;
}

.delivery-total {
    color: var(--text-main);
    font-size: 1rem;
    font-weight: 800;
}

.delivery-open {
    display: flex;
    align-items: center;
    gap: 3px;
    color: var(--accent-1);
    font-size: .7rem;
    font-weight: 600;
}

/* COLUMNAS KANBAN */

#col-pending,
#col-preparing,
#col-on_way,
#col-delivered {
    min-height: 180px;
}

.row.g-3 > .col-md-3 > .card {
    border-radius: 15px;
    background: var(--light-bg) !important;
}

.row.g-3 > .col-md-3 > .card > .card-header {
    padding: 16px 14px 10px !important;
}

.row.g-3 > .col-md-3 > .card > .card-body {
    padding: 8px !important;
}

.row.g-3 .badge.rounded-pill {
    min-width: 27px;
    padding: 5px 8px;
}

/* RESPONSIVE */

@media (max-width: 991.98px) {
    .row.g-3 > .col-md-3 {
        width: 50%;
    }
}

@media (max-width: 767.98px) {
    .row.g-3 > .col-md-3 {
        width: 100%;
    }

    .container-fluid > .d-flex:first-child {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 15px;
    }

    .container-fluid > .d-flex:first-child > div:last-child {
        display: flex;
        width: 100%;
    }

    .container-fluid > .d-flex:first-child > div:last-child .btn {
        flex: 1;
    }
}

</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deliveryOrdersUrl = '{{ route('delivery.orders') }}';
    const statuses = ['pending', 'preparing', 'on_way', 'delivered'];

    let deliveryRequestRunning = false;

    async function refreshDeliveries() {
        if (deliveryRequestRunning || document.hidden) {
            return;
        }

        deliveryRequestRunning = true;

        try {
            const response = await fetch(deliveryOrdersUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error(`Error HTTP ${response.status}`);
            }

            const data = await response.json();

            statuses.forEach(function (status) {
                const column = document.getElementById(`col-${status}`);
                const counter = document.getElementById(`count-${status}`);

                if (column && data.columns && data.columns[status] !== undefined) {
                    column.innerHTML = data.columns[status];
                }

                if (counter && data.counts && data.counts[status] !== undefined) {
                    counter.textContent = data.counts[status];
                }
            });
        } catch (error) {
            console.error('Error al actualizar Delivery:', error);
        } finally {
            deliveryRequestRunning = false;
        }
    }

    refreshDeliveries();

    setInterval(refreshDeliveries, 2000);
});
</script>


<style>
/* DARK MODE - COLORES ESTADO DELIVERY FINAL */

/* PENDIENTE - ROJO */
html[data-color-mode="dark"] .delivery-pending {
    color: #f87171 !important;
}

html[data-color-mode="dark"] .delivery-pending i,
html[data-color-mode="dark"] .delivery-pending span {
    color: #f87171 !important;
}


/* PREPARANDO - AMARILLO */
html[data-color-mode="dark"] .delivery-preparing {
    color: var(--delivery-card-status-color) !important;
}

html[data-color-mode="dark"] .delivery-preparing i,
html[data-color-mode="dark"] .delivery-preparing span {
    color: var(--delivery-card-status-color) !important;
}


/* BADGES DE TARJETA DELIVERY */
.delivery-card .order-number-badge,
.delivery-card .delivery-type-badge {
    background: color-mix(in srgb, var(--primary) 10%, var(--card-bg)) !important;
    color: var(--delivery-card-status-color) !important;
    border: 1px solid color-mix(in srgb, var(--primary) 22%, transparent) !important;
}

.delivery-card .delivery-type-badge i {
    color: var(--delivery-card-status-color) !important;
}

html[data-color-mode="dark"] .delivery-card .order-number-badge,
html[data-color-mode="dark"] .delivery-card .delivery-type-badge {
    background: color-mix(in srgb, var(--primary) 15%, #132338) !important;
    color: var(--delivery-card-status-color) !important;
    border-color: color-mix(in srgb, var(--primary) 35%, #30465d) !important;
}

html[data-color-mode="dark"] .delivery-card .delivery-type-badge i {
    color: var(--delivery-card-status-color) !important;
}

/* IDENTIFICADORES DE TARJETA */
.delivery-order-number {
    background: color-mix(in srgb, var(--primary) 12%, var(--card-bg)) !important;
    color: var(--delivery-card-status-color) !important;
    border: 1px solid color-mix(in srgb, var(--primary) 28%, transparent) !important;
}

.delivery-type-badge.delivery {
    background: color-mix(in srgb, var(--primary) 12%, var(--card-bg)) !important;
    color: var(--delivery-card-status-color) !important;
    border: 1px solid color-mix(in srgb, var(--primary) 28%, transparent) !important;
}

.delivery-type-badge.delivery i {
    color: var(--delivery-card-status-color) !important;
}

/* MODO OSCURO */
html[data-color-mode="dark"] .delivery-order-number,
html[data-color-mode="dark"] .delivery-type-badge.delivery {
    background: color-mix(in srgb, var(--primary) 16%, #132338) !important;
    color: var(--delivery-card-status-color) !important;
    border-color: color-mix(in srgb, var(--primary) 38%, #30465d) !important;
}

html[data-color-mode="dark"] .delivery-type-badge.delivery i {
    color: var(--delivery-card-status-color) !important;
}

/* COLORES DE ENCABEZADOS POR ESTADO */
.delivery-column-title,
.delivery-column-title i {
    color: var(--status-color) !important;
}

html[data-color-mode="dark"] .delivery-column-title,
html[data-color-mode="dark"] .delivery-column-title i {
    color: var(--status-color) !important;
}

/* =========================================
   TARJETA PENDIENTE - COLOR SEMANTICO
   ========================================= */

/* Borde de la tarjeta dentro de Pendiente */
#col-pending .delivery-order-card {
    border-color: var(--delivery-card-status-color) !important;
    box-shadow: 0 0 0 1px color-mix(
        in srgb,
        #f59e0b 20%,
        transparent
    );
}

/* Numero del pedido */
#col-pending .delivery-order-number {
    color: var(--delivery-card-status-color) !important;
    background: color-mix(
        in srgb,
        #f59e0b 12%,
        var(--card-bg)
    ) !important;
    border-color: color-mix(
        in srgb,
        #f59e0b 45%,
        transparent
    ) !important;
}

/* Badge Delivery */
#col-pending .delivery-type-badge {
    color: var(--delivery-card-status-color) !important;
    background: color-mix(
        in srgb,
        #f59e0b 12%,
        var(--card-bg)
    ) !important;
    border-color: color-mix(
        in srgb,
        #f59e0b 45%,
        transparent
    ) !important;
}

#col-pending .delivery-type-badge i {
    color: var(--delivery-card-status-color) !important;
}

/* Estado Pendiente dentro de la tarjeta */
#col-pending .delivery-pending,
#col-pending .delivery-pending span,
#col-pending .delivery-pending i {
    color: var(--delivery-card-status-color) !important;
}

/* MODO OSCURO */
html[data-color-mode="dark"] #col-pending .delivery-order-number,
html[data-color-mode="dark"] #col-pending .delivery-type-badge {
    color: #fbbf24 !important;
    background: color-mix(
        in srgb,
        #f59e0b 14%,
        #132338
    ) !important;
    border-color: color-mix(
        in srgb,
        #f59e0b 55%,
        #30465d
    ) !important;
}

html[data-color-mode="dark"] #col-pending .delivery-type-badge i,
html[data-color-mode="dark"] #col-pending .delivery-pending,
html[data-color-mode="dark"] #col-pending .delivery-pending span,
html[data-color-mode="dark"] #col-pending .delivery-pending i {
    color: #fbbf24 !important;
}

html[data-color-mode="dark"] #col-pending .delivery-order-card {
    border-color: var(--delivery-card-status-color) !important;
}

/* TARJETA PENDIENTE - TEXTO CON COLOR DEL ESTADO */
#col-pending .delivery-order-card,
#col-pending .delivery-order-card .delivery-order-number,
#col-pending .delivery-order-card .delivery-type-badge,
#col-pending .delivery-order-card .delivery-type-badge i,
#col-pending .delivery-order-card .delivery-time,
#col-pending .delivery-order-card .delivery-time i,
#col-pending .delivery-order-card .delivery-customer,
#col-pending .delivery-order-card .delivery-location,
#col-pending .delivery-order-card .delivery-location i,
#col-pending .delivery-order-card .delivery-payment,
#col-pending .delivery-order-card .delivery-payment i,
#col-pending .delivery-order-card .delivery-pending,
#col-pending .delivery-order-card .delivery-pending i,
#col-pending .delivery-order-card .delivery-pending span,
#col-pending .delivery-order-card .delivery-total,
#col-pending .delivery-order-card .delivery-total * {
    color: var(--delivery-card-status-color) !important;
}

/* MODO OSCURO */
html[data-color-mode="dark"] #col-pending .delivery-order-card,
html[data-color-mode="dark"] #col-pending .delivery-order-card * {
    color: #fbbf24 !important;
}

/* Ver pedido sigue siendo una accion del sistema */
html[data-color-mode="dark"] #col-pending .delivery-order-card a,
html[data-color-mode="dark"] #col-pending .delivery-order-card a * {
    color: var(--primary) !important;
}

/* LINEAS DE TARJETA PENDIENTE */
#col-pending .delivery-order-card hr {
    border-color: var(--delivery-card-status-color) !important;
    opacity: 1 !important;
}

#col-pending .delivery-order-card {
    --bs-border-color: #f59e0b;
}

#col-pending .delivery-order-card .border-top,
#col-pending .delivery-order-card .border-bottom {
    border-color: var(--delivery-card-status-color) !important;
}

/* MODO OSCURO */
html[data-color-mode="dark"] #col-pending .delivery-order-card hr,
html[data-color-mode="dark"] #col-pending .delivery-order-card .border-top,
html[data-color-mode="dark"] #col-pending .delivery-order-card .border-bottom {
    border-color: #fbbf24 !important;
    opacity: 1 !important;
}

/* LINEAS REALES - TARJETA PENDIENTE */
#col-pending .delivery-order-card .delivery-card-info,
#col-pending .delivery-order-card .delivery-card-footer {
    border-top-color: var(--delivery-card-status-color) !important;
    border-top-width: 1px !important;
    border-top-style: solid !important;
}

/* MODO OSCURO */
html[data-color-mode="dark"] #col-pending .delivery-order-card .delivery-card-info,
html[data-color-mode="dark"] #col-pending .delivery-order-card .delivery-card-footer {
    border-top-color: #fbbf24 !important;
}

/* ENCABEZADO DE CADA COLUMNA - RESPETAR COLOR DEL ESTADO */
.delivery-column-title,
.delivery-column-title > i,
.delivery-column-title > span:not(.badge) {
    color: var(--status-color) !important;
}

html[data-color-mode="dark"] .delivery-column-title,
html[data-color-mode="dark"] .delivery-column-title > i,
html[data-color-mode="dark"] .delivery-column-title > span:not(.badge) {
    color: var(--status-color) !important;
}

/* PENDIENTE - TODO NARANJA EN MODO CLARO */
html:not([data-color-mode="dark"]) #col-pending .delivery-order-card,
html:not([data-color-mode="dark"]) #col-pending .delivery-order-card * {
    color: var(--delivery-card-status-color) !important;
}

html:not([data-color-mode="dark"]) #col-pending .delivery-order-card {
    border-color: var(--delivery-card-status-color) !important;
}

html:not([data-color-mode="dark"]) #col-pending .delivery-card-info,
html:not([data-color-mode="dark"]) #col-pending .delivery-card-footer {
    border-top-color: var(--delivery-card-status-color) !important;
}

/* El enlace tambien queda naranja */
html:not([data-color-mode="dark"]) #col-pending .delivery-order-card a,
html:not([data-color-mode="dark"]) #col-pending .delivery-order-card a * {
    color: var(--delivery-card-status-color) !important;
}

/* =====================================================
   COLOR DINAMICO DE TODA LA TARJETA SEGUN SU ESTADO
   ===================================================== */

.delivery-order-card {
    border-color: var(--delivery-card-status-color) !important;
}

/* TODOS LOS TEXTOS E ICONOS */
.delivery-order-card,
.delivery-order-card * {
    color: var(--delivery-card-status-color) !important;
}

/* LINEAS DIVISORIAS */
.delivery-order-card .delivery-card-info,
.delivery-order-card .delivery-card-footer {
    border-top-color: var(--delivery-card-status-color) !important;
}

/* NUMERO DEL PEDIDO */
.delivery-order-card .delivery-order-number {
    color: var(--delivery-card-status-color) !important;
    border-color: color-mix(
        in srgb,
        var(--delivery-card-status-color) 45%,
        transparent
    ) !important;

    background: color-mix(
        in srgb,
        var(--delivery-card-status-color) 10%,
        var(--card-bg)
    ) !important;
}

/* DELIVERY / RECOJO */
.delivery-order-card .delivery-type-badge {
    color: var(--delivery-card-status-color) !important;

    border-color: color-mix(
        in srgb,
        var(--delivery-card-status-color) 45%,
        transparent
    ) !important;

    background: color-mix(
        in srgb,
        var(--delivery-card-status-color) 10%,
        var(--card-bg)
    ) !important;
}

.delivery-order-card .delivery-type-badge i {
    color: var(--delivery-card-status-color) !important;
}

/* MODO OSCURO */
html[data-color-mode="dark"] .delivery-order-card,
html[data-color-mode="dark"] .delivery-order-card * {
    color: var(--delivery-card-status-color) !important;
}

html[data-color-mode="dark"] .delivery-order-card .delivery-card-info,
html[data-color-mode="dark"] .delivery-order-card .delivery-card-footer {
    border-top-color: var(--delivery-card-status-color) !important;
}

html[data-color-mode="dark"] .delivery-order-card .delivery-order-number,
html[data-color-mode="dark"] .delivery-order-card .delivery-type-badge {
    color: var(--delivery-card-status-color) !important;

    background: color-mix(
        in srgb,
        var(--delivery-card-status-color) 14%,
        #132338
    ) !important;

    border-color: color-mix(
        in srgb,
        var(--delivery-card-status-color) 55%,
        #30465d
    ) !important;
}

/* BADGE DELIVERY / RECOJO - HEREDA EL COLOR DEL ESTADO */
.delivery-order-card .delivery-type-badge,
.delivery-order-card .delivery-type-badge span,
.delivery-order-card .delivery-type-badge i {
    color: var(--delivery-card-status-color) !important;
}

.delivery-order-card .delivery-type-badge {
    border-color: var(--delivery-card-status-color) !important;

    background: color-mix(
        in srgb,
        var(--delivery-card-status-color) 10%,
        var(--card-bg)
    ) !important;
}

html[data-color-mode="dark"] .delivery-order-card .delivery-type-badge {
    background: color-mix(
        in srgb,
        var(--delivery-card-status-color) 14%,
        #132338
    ) !important;

    border-color: var(--delivery-card-status-color) !important;
}
</style>

@endsection
