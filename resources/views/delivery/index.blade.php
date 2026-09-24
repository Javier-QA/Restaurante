@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #000 !important;"><i class="bi bi-bicycle me-2" style="color: #000 !important;"></i>Delivery (Pedidos a Domicilio)</h2>
            <p class="text-muted small mb-0 mt-1">Gestión de pedidos para enviar hoy.</p>
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KANBAN BOARD --}}
    <div class="row g-3">
        {{-- Pendiente --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-header border-0 bg-transparent pt-3 pb-2">
                    <h6 class="fw-bold mb-0" style="color: {{ $statuses['pending'] ?? '#f59e0b' }}">
                        <i class="bi bi-hourglass-split me-1"></i> Pendiente
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
                    <h6 class="fw-bold mb-0" style="color: {{ $statuses['preparing'] ?? '#3b82f6' }}">
                        <i class="bi bi-fire me-1"></i> Preparando
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
                    <h6 class="fw-bold mb-0" style="color: {{ $statuses['on_way'] ?? '#f97316' }}">
                        <i class="bi bi-bicycle me-1"></i> Listos / En camino
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
                    <h6 class="fw-bold mb-0" style="color: {{ $statuses['delivered'] ?? '#22c55e' }}">
                        <i class="bi bi-check2-all me-1"></i> Entregados Hoy
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
    color: var(--accent-3);
    font-weight: 700;
}

.delivery-preparing {
    color: var(--accent-1);
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

@endsection
