@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark"><i class="bi bi-fire text-danger me-2"></i>Monitor de Cocina (KDS)</h2>
            <p class="text-muted">Pedidos pendientes de preparación</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-white text-dark border"><i class="bi bi-circle-fill text-danger me-1"></i> Pendiente</span>
            <span class="badge bg-white text-dark border"><i class="bi bi-circle-fill text-warning me-1"></i> Preparando</span>
            <div id="reloj" class="fw-bold fs-5 ms-3">00:00:00</div>
        </div>
    </div>

    <div class="row g-3" id="kitchen-orders">
        @forelse($orders as $order)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header text-white d-flex justify-content-between align-items-center py-3 {{ $order->details->contains('status', 'cooking') ? 'bg-warning text-dark' : 'bg-danger' }}">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $order->table ? 'Mesa: ' . $order->table->name : 'Para Llevar' }}</h5>
                            <small>Folio #{{ $order->id }}</small>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-clock-history"></i>
                            <span class="d-block fw-bold">{{ $order->created_at->format('H:i') }}</span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($order->details as $detail)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary rounded-pill me-2 fs-6">{{ $detail->quantity }}</span>
                                            <span class="fw-bold {{ $detail->status == 'served' ? 'text-decoration-line-through text-muted' : '' }}">
                                                {{ $detail->product->name }}
                                            </span>
                                        </div>
                                        
                                        @if($detail->note)
                                            <div class="ms-5 mt-1">
                                                <span class="badge bg-warning text-dark border border-dark">
                                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $detail->note }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <form action="{{ route('kitchen.update', $detail) }}" method="POST">
                                        @csrf
                                        @if($detail->status == 'pending')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Empezar
                                            </button>
                                        @elseif($detail->status == 'cooking')
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="bi bi-check-lg"></i> Listo
                                            </button>
                                        @endif
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="opacity-50">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <h2 class="mt-3 text-muted">Todo en orden, Chef.</h2>
                    <p>No hay pedidos pendientes en este momento.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    const kitchenOrdersUrl = @json(route('kitchen.orders'));
    const kitchenUpdateBaseUrl = @json(url('/kitchen'));
    const csrfToken = @json(csrf_token());

    let knownDetailIds = new Set(
        @json(
            $orders->flatMap(function ($order) {
                return $order->details->pluck('id');
            })->values()
        )
    );

    let firstKitchenCheck = true;
    let kitchenRequestRunning = false;

    // Reloj
    function updateClock() {
        const now = new Date();
        document.getElementById('reloj').innerText =
            now.toLocaleTimeString();
    }

    updateClock();
    setInterval(updateClock, 1000);

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function renderKitchenOrders(orders) {
        const container = document.getElementById('kitchen-orders');

        if (!orders.length) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="opacity-50">
                        <i class="bi bi-check-circle-fill text-success"
                           style="font-size: 5rem;"></i>
                        <h2 class="mt-3 text-muted">
                            Todo en orden, Chef.
                        </h2>
                        <p>
                            No hay pedidos pendientes en este momento.
                        </p>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = orders.map(order => {
            const cooking = order.details.some(
                detail => detail.status === 'cooking'
            );

            const details = order.details.map(detail => {
                const note = detail.note
                    ? `
                        <div class="ms-5 mt-1">
                            <span class="badge bg-warning text-dark border border-dark">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                ${escapeHtml(detail.note)}
                            </span>
                        </div>
                    `
                    : '';

                const button = detail.status === 'pending'
                    ? `
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger kitchen-status-btn"
                            data-detail="${detail.id}">
                            Empezar
                        </button>
                    `
                    : `
                        <button
                            type="button"
                            class="btn btn-sm btn-warning kitchen-status-btn"
                            data-detail="${detail.id}">
                            <i class="bi bi-check-lg"></i> Listo
                        </button>
                    `;

                return `
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary rounded-pill me-2 fs-6">
                                    ${escapeHtml(detail.quantity)}
                                </span>

                                <span class="fw-bold">
                                    ${escapeHtml(detail.product)}
                                </span>
                            </div>

                            ${note}
                        </div>

                        ${button}
                    </li>
                `;
            }).join('');

            return `
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header text-white d-flex justify-content-between align-items-center py-3 ${
                            cooking
                                ? 'bg-warning text-dark'
                                : 'bg-danger'
                        }">
                            <div>
                                <h5 class="fw-bold mb-0">
                                    ${order.table === 'Para Llevar'
                                        ? 'Para Llevar'
                                        : 'Mesa: ' + escapeHtml(order.table)}
                                </h5>

                                <small>
                                    Folio #${escapeHtml(order.id)}
                                </small>
                            </div>

                            <div class="text-end">
                                <i class="bi bi-clock-history"></i>
                                <span class="d-block fw-bold">
                                    ${escapeHtml(order.time)}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                ${details}
                            </ul>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function refreshKitchen() {
        if (kitchenRequestRunning) {
            return;
        }

        kitchenRequestRunning = true;

        try {
            const response = await fetch(kitchenOrdersUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error(
                    `Error HTTP ${response.status}`
                );
            }

            const data = await response.json();
            const orders = data.orders ?? [];

            const currentDetailIds = new Set();

            orders.forEach(order => {
                order.details.forEach(detail => {
                    currentDetailIds.add(detail.id);
                });
            });

            const hasNewDetails = [...currentDetailIds].some(
                id => !knownDetailIds.has(id)
            );

            renderKitchenOrders(orders);

            if (!firstKitchenCheck && hasNewDetails) {
                console.log('Nuevo pedido recibido en cocina');
            }

            knownDetailIds = currentDetailIds;
            firstKitchenCheck = false;

        } catch (error) {
            console.error(
                'No se pudo actualizar Cocina:',
                error
            );
        } finally {
            kitchenRequestRunning = false;
        }
    }

    document.addEventListener('click', async function (event) {
        const button = event.target.closest(
            '.kitchen-status-btn'
        );

        if (!button) {
            return;
        }

        const detailId = button.dataset.detail;

        button.disabled = true;

        try {
            const response = await fetch(
                `${kitchenUpdateBaseUrl}/${detailId}/status`,
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error(
                    `Error HTTP ${response.status}`
                );
            }

            await refreshKitchen();

        } catch (error) {
            console.error(
                'No se pudo cambiar el estado:',
                error
            );

            button.disabled = false;
        }
    });

    // Primera sincronización
    refreshKitchen();

    // Actualización automática sin recargar la página
    setInterval(refreshKitchen, 2000);
</script>
@endsection