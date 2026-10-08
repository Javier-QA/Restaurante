@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark"><i class="bi bi-fire me-2" style="color:var(--text-main);"></i>Monitor de Cocina (KDS)</h2>
            <p class="text-muted">Pedidos pendientes de preparación</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-white text-dark border kitchen-status-pending"><i class="bi bi-circle-fill me-1"></i> Pendiente</span>
            <span class="badge bg-white text-dark border kitchen-status-preparing"><i class="bi bi-circle-fill me-1"></i> Preparando</span>
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
                playKitchenNewOrderSound();

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

<style>
/* DARK MODE - ESTADOS MONITOR COCINA */

/* PENDIENTE - ROJO */
html[data-color-mode="dark"] .kitchen-status-pending {
    background: rgba(239, 68, 68, .12) !important;
    border-color: rgba(239, 68, 68, .32) !important;
    color: #f87171 !important;
}

html[data-color-mode="dark"] .kitchen-status-pending i {
    color: #ef4444 !important;
    -webkit-text-fill-color: #ef4444 !important;
}


/* PREPARANDO - AMARILLO */
html[data-color-mode="dark"] .kitchen-status-preparing {
    background: rgba(245, 158, 11, .12) !important;
    border-color: rgba(245, 158, 11, .32) !important;
    color: #fbbf24 !important;
}

html[data-color-mode="dark"] .kitchen-status-preparing i {
    color: #f59e0b !important;
    -webkit-text-fill-color: #f59e0b !important;
}


/* MODO CLARO - conserva apariencia clara */
html:not([data-color-mode="dark"]) .kitchen-status-pending i {
    color: var(--ink-red, #dc3545) !important;
}

html:not([data-color-mode="dark"]) .kitchen-status-preparing i {
    color: #ffc107 !important;
}

</style>





<style>
/* KDS - PEDIDO PENDIENTE ESTILO ROJO */

/* Cabecera roja de la tarjeta */
.card .card-header {
    background: #e33446 !important;
    border-bottom-color: #e33446 !important;
    color: #ffffff !important;
}

/* Mesa, folio, reloj y hora */
.card .card-header,
.card .card-header h5,
.card .card-header span,
.card .card-header i,
.card .card-header .text-end {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}

/* Cantidad: círculo gris como la referencia */
.card .badge.bg-secondary {
    background: #747d84 !important;
    border: none !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}

/* Empezar: rojo, limpio y sin fondo */
.card .btn-outline-danger {
    background: transparent !important;
    border-color: transparent !important;
    color: var(--ink-red, #e33446) !important;
    font-weight: 700 !important;
    box-shadow: none !important;
}

.card .btn-outline-danger:hover {
    background: rgba(227, 52, 70, .10) !important;
    border-color: transparent !important;
    color: var(--ink-red, #e33446) !important;
}


/* ==========================
   MODO OSCURO
   ========================== */

/* La cabecera sigue ROJA */
html[data-color-mode="dark"] .card .card-header {
    background: #e33446 !important;
    border-bottom-color: #e33446 !important;
}

/* Todo lo de la cabecera permanece blanco */
html[data-color-mode="dark"] .card .card-header,
html[data-color-mode="dark"] .card .card-header h5,
html[data-color-mode="dark"] .card .card-header span,
html[data-color-mode="dark"] .card .card-header i {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}

/* Cantidad gris */
html[data-color-mode="dark"] .card .badge.bg-secondary {
    background: #747d84 !important;
    border-color: transparent !important;
    color: #ffffff !important;
}

/* Empezar rojo */
html[data-color-mode="dark"] .card .btn-outline-danger {
    background: transparent !important;
    border-color: transparent !important;
    color: #fb4d60 !important;
}

html[data-color-mode="dark"] .card .btn-outline-danger:hover {
    background: rgba(251, 77, 96, .12) !important;
    color: #ff6b7b !important;
}

</style>


<style>
/* KDS - ESTADOS CABECERA DEFINITIVOS */

/* =====================================
   PENDIENTE = ROJO
   ===================================== */

#kitchen-orders .card-header.bg-danger {
    background: #e33446 !important;
    border-color: #e33446 !important;
    color: #ffffff !important;
}

#kitchen-orders .card-header.bg-danger h5,
#kitchen-orders .card-header.bg-danger small,
#kitchen-orders .card-header.bg-danger span,
#kitchen-orders .card-header.bg-danger i {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}


/* =====================================
   PREPARANDO = AMARILLO
   ===================================== */

#kitchen-orders .card-header.bg-warning {
    background: #fbbf24 !important;
    border-color: #fbbf24 !important;
    color: var(--text-main, #1f2937) !important;
}

#kitchen-orders .card-header.bg-warning h5,
#kitchen-orders .card-header.bg-warning small,
#kitchen-orders .card-header.bg-warning span,
#kitchen-orders .card-header.bg-warning i {
    color: var(--text-main, #1f2937) !important;
    -webkit-text-fill-color: #1f2937 !important;
}


/* =====================================
   MODO OSCURO
   Los colores de estado se conservan
   ===================================== */

html[data-color-mode="dark"] #kitchen-orders .card-header.bg-danger {
    background: #e33446 !important;
    border-color: #e33446 !important;
}

html[data-color-mode="dark"] #kitchen-orders .card-header.bg-warning {
    background: #fbbf24 !important;
    border-color: #fbbf24 !important;
}

html[data-color-mode="dark"] #kitchen-orders .card-header.bg-warning h5,
html[data-color-mode="dark"] #kitchen-orders .card-header.bg-warning small,
html[data-color-mode="dark"] #kitchen-orders .card-header.bg-warning span,
html[data-color-mode="dark"] #kitchen-orders .card-header.bg-warning i {
    color: var(--text-main, #1f2937) !important;
    -webkit-text-fill-color: #1f2937 !important;
}

</style>


<script id="kitchen-order-sound">
(function () {

    let audioContext = null;
    let audioUnlocked = false;

    function createAudioContext() {

        if (audioContext) {
            return audioContext;
        }

        const AudioContextClass =
            window.AudioContext ||
            window.webkitAudioContext;

        if (!AudioContextClass) {
            console.warn('Este navegador no soporta Web Audio.');
            return null;
        }

        audioContext = new AudioContextClass();

        return audioContext;
    }


    /*
     * Chrome exige que el audio sea creado/reanudado
     * directamente durante una interacción del usuario.
     */
    async function unlockOrderAudio() {

        const ctx = createAudioContext();

        if (!ctx) {
            return;
        }

        try {

            if (ctx.state === 'suspended') {
                await ctx.resume();
            }

            /*
             * Reproducimos un tono prácticamente inaudible.
             * Esto deja habilitado el contexto de audio para
             * futuras notificaciones automáticas.
             */
            const oscillator =
                ctx.createOscillator();

            const gain =
                ctx.createGain();

            gain.gain.value = 0.00001;

            oscillator.connect(gain);
            gain.connect(ctx.destination);

            oscillator.start();
            oscillator.stop(
                ctx.currentTime + 0.01
            );

            audioUnlocked =
                ctx.state === 'running';

            if (audioUnlocked) {
                console.log(
                    '🔊 Sonido de pedidos habilitado'
                );
            }

        } catch (error) {

            console.warn(
                'No se pudo habilitar el sonido:',
                error
            );
        }
    }


    /*
     * La PRIMERA interacción real habilita el audio.
     */
    document.addEventListener(
        'pointerdown',
        unlockOrderAudio,
        { once: true }
    );

    document.addEventListener(
        'keydown',
        unlockOrderAudio,
        { once: true }
    );


    window.playKitchenNewOrderSound = function () {

        if (window.systemSoundsEnabled !== true) {
            return;
        }

        if (
            !audioContext ||
            !audioUnlocked ||
            audioContext.state !== 'running'
        ) {
            console.warn(
                '🔇 Sonido pendiente de habilitación. Haz clic una vez en la pantalla.'
            );
            return;
        }

        const now = audioContext.currentTime;

        /*
         * Campana larga tipo restaurante.
         * Ataque rápido + resonancia prolongada.
         */
        const harmonics = [
            {
                frequency: 784,
                volume: 0.42,
                duration: 3.8
            },
            {
                frequency: 1568,
                volume: 0.20,
                duration: 3.1
            },
            {
                frequency: 1882,
                volume: 0.11,
                duration: 2.5
            },
            {
                frequency: 2391,
                volume: 0.055,
                duration: 1.8
            }
        ];

        harmonics.forEach(function (tone) {

            const oscillator =
                audioContext.createOscillator();

            const gain =
                audioContext.createGain();

            oscillator.type = 'sine';

            oscillator.frequency.setValueAtTime(
                tone.frequency,
                now
            );

            gain.gain.setValueAtTime(
                0.0001,
                now
            );

            // Golpe inicial de la campana
            gain.gain.exponentialRampToValueAtTime(
                tone.volume,
                now + 0.006
            );

            // Resonancia larga
            gain.gain.exponentialRampToValueAtTime(
                tone.volume * 0.30,
                now + 0.55
            );

            gain.gain.exponentialRampToValueAtTime(
                0.0001,
                now + tone.duration
            );

            oscillator.connect(gain);
            gain.connect(audioContext.destination);

            oscillator.start(now);

            oscillator.stop(
                now + tone.duration + 0.1
            );
        });
    };

})();
</script>
@endsection
