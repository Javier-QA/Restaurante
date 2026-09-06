@extends('layouts.app')

@section('content')
<div class="container-fluid pos-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Punto de Venta</h2>
            <p class="text-muted mb-0">Selecciona una mesa para comenzar</p>
        </div>
        <div class="d-flex gap-3">
            <div class="d-flex align-items-center">
                <span class="d-inline-block bg-success rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                <small class="fw-bold text-muted">Disponible</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="d-inline-block bg-danger rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                <small class="fw-bold text-muted">Ocupada</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="d-inline-block bg-warning rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                <small class="fw-bold text-muted">Reservada (Hoy)</small>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs nav-pills mb-3" id="posTabs" role="tablist">
        @foreach($areas as $index => $area)
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link {{ $index == 0 ? 'active' : '' }} fw-bold px-4 border" 
                        id="tab-{{ $area->id }}" 
                        data-bs-toggle="tab" 
                        data-bs-target="#area-{{ $area->id }}" 
                        type="button" role="tab">
                    {{ $area->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach($areas as $index => $area)
            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="area-{{ $area->id }}" role="tabpanel">
                
                <div class="pos-floor position-relative border rounded-3 shadow-sm" 
                     style="height: 720px; overflow: auto; background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px;">
                    
                    @foreach($area->tables as $table)
                        @php
                            $order = $table->orders->first(); // Orden activa
                            $reservations = $table->reservations; // TODAS las reservas confirmadas de hoy
                            
                            $isBusy = $order ? true : false;
                            $hasReservations = $reservations->count() > 0;
                            
                            // Estilos dinámicos
                            $cardClass = $isBusy 
                                ? 'pos-table-busy' 
                                : ($hasReservations ? 'pos-table-reserved' : 'pos-table-free');
                                
                            $icon = $isBusy ? 'bi-display-fill' : 'bi-display';
                        @endphp

                        <a href="{{ route('pos.order', $table->id) }}" class="text-decoration-none text-dark">
                            <div class="pos-table-card position-absolute d-flex flex-column align-items-center justify-content-between p-2 rounded-3 {{ $cardClass }}"
                                 style="width: 155px; height: 155px; 
                                        left: {{ $table->x_pos }}px; 
                                        top: {{ $table->y_pos }}px; 
                                        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                                
                                <div class="w-100 text-center border-bottom pb-1 mb-1">
                                    <span class="fw-bold small text-uppercase" style="font-size: 0.75rem;">{{ $table->name }}</span>
                                </div>

                                <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative w-100">
                                    <i class="bi {{ $icon }} fs-1 {{ $isBusy ? 'text-danger' : 'text-secondary opacity-50' }}"></i>
                                    
                                    @if($hasReservations && !$isBusy)
                                        <div class="position-absolute top-50 start-50 translate-middle badge bg-warning text-dark border border-dark shadow-sm" 
                                             style="font-size: 0.6rem; width: 100%; white-space: normal; line-height: 1.1; z-index: 2; max-height: 60px; overflow-y: auto;">
                                            
                                            @foreach($reservations as $res)
                                                <div class="{{ !$loop->last ? 'border-bottom border-dark pb-1 mb-1' : '' }}">
                                                    <i class="bi bi-clock-fill"></i> <strong>{{ $res->reservation_time->format('H:i') }}</strong>
                                                    <br>{{ Str::limit($res->client_name, 9) }}
                                                </div>
                                            @endforeach

                                        </div>
                                    @endif
                                </div>

                                <div class="w-100 text-center mt-1">
                                    @if($isBusy)
                                        <div class="badge bg-danger w-100 py-1 shadow-sm">
                                            <small style="font-size: 0.65rem;">CONSUMO</small><br>
                                            <span class="fs-6 fw-bold">{{ $currency ?? 'S/' }}{{ number_format($order->total, 2) }}</span>
                                        </div>
                                    @else
                                        @if($hasReservations)
                                            <div class="badge bg-warning text-dark w-100 py-2 shadow-sm border border-warning">
                                                {{ $reservations->count() }} RESERVA(S)
                                            </div>
                                        @else
                                            <div class="badge bg-success w-100 py-2 shadow-sm">
                                                LIBRE
                                            </div>
                                        @endif
                                    @endif
                                </div>

                            </div>
                        </a>
                    @endforeach

                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .pos-table-card:hover {
        transform: scale(1.1) translateY(-5px); 
        z-index: 100 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        cursor: pointer;
    }
    /* Scrollbar invisible para el badge de reservas */
    .badge::-webkit-scrollbar { width: 0px; background: transparent; }

    /* =========================================================
       PUNTO DE VENTA - PALETA DINÁMICA
    ========================================================== */

    .pos-page {
        --pos-primary: var(--primary, #ff8c00);
        --pos-primary-hover: var(--primary-hover, #e07b00);
        --pos-dark: var(--dark-bg, #063970);
        --pos-dark-2: var(--dark-bg-2, #0b4f8a);
        --pos-light: var(--light-bg, #eef8fc);
        --pos-card: var(--card-bg, #ffffff);
        --pos-text: var(--text-main, #172033);
        --pos-muted: var(--text-muted, #64748b);
        --pos-border: var(--border-soft, #dce7f1);
    }

    .pos-page h2 {
        color: var(--pos-dark) !important;
    }

    .pos-page #posTabs {
        border-bottom: 0;
        gap: 6px;
    }

    .pos-page .nav-pills .nav-link {
        color: var(--pos-muted);
        background: var(--pos-card);
        border: 1px solid var(--pos-border) !important;
        border-radius: 10px;
        transition: all .2s ease;
    }

    .pos-page .nav-pills .nav-link:hover {
        color: var(--pos-primary);
        border-color: var(--pos-primary) !important;
        transform: translateY(-1px);
    }

    .pos-page .nav-pills .nav-link.active {
        background: var(--pos-primary);
        color: #fff;
        border-color: var(--pos-primary) !important;
        box-shadow: 0 5px 14px
            color-mix(
                in srgb,
                var(--pos-primary) 28%,
                transparent
            );
    }


    /* =========================================================
       TARJETAS DE MESAS
    ========================================================== */

    .pos-table-card {
        background: var(--pos-card);
        border: 2px solid transparent;
        box-shadow: 0 5px 14px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .pos-table-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    /* DISPONIBLE */

    .pos-table-free {
        border-color: #22c55e;
        color: #15803d;
    }

    .pos-table-free::before {
        background: #22c55e;
    }

    /* OCUPADA */

    .pos-table-busy {
        border-color: #ef4444;
        color: #dc2626;
    }

    .pos-table-busy::before {
        background: #ef4444;
    }

    /* RESERVADA */

    .pos-table-reserved {
        border-color: #f59e0b;
        color: #92400e;
    }

    .pos-table-reserved::before {
        background: #f59e0b;
    }

    .pos-table-card:hover {
        transform: scale(1.06) translateY(-4px);
        z-index: 100 !important;
        box-shadow: 0 12px 26px rgba(15, 23, 42, .16) !important;
    }


    /* =========================================================
       LEGIBILIDAD DEL CONTENIDO DE LAS MESAS
    ========================================================== */

    .pos-table-card {
        color: var(--pos-text) !important;
    }

    /* Nombre de la mesa */
    .pos-table-card > div:first-child {
        color: var(--pos-text) !important;
        border-color: var(--pos-border) !important;
    }

    .pos-table-card > div:first-child span {
        color: var(--pos-text) !important;
        opacity: 1 !important;
    }

    /* Icono central */
    .pos-table-free .bi-display,
    .pos-table-free .bi-display-fill {
        color: #16a34a !important;
        opacity: .75 !important;
    }

    .pos-table-busy .bi-display,
    .pos-table-busy .bi-display-fill {
        color: #dc2626 !important;
        opacity: .85 !important;
    }

    .pos-table-reserved .bi-display,
    .pos-table-reserved .bi-display-fill {
        color: #d97706 !important;
        opacity: .8 !important;
    }

    /* Estados inferiores */
    .pos-table-card .bg-success {
        background: #16a34a !important;
        color: #ffffff !important;
    }

    .pos-table-card .bg-danger {
        background: #dc2626 !important;
        color: #ffffff !important;
    }

    .pos-table-card .bg-warning {
        background: #f59e0b !important;
        color: #1f2937 !important;
    }

    /* Asegurar lectura de consumo */
    .pos-table-card .badge span,
    .pos-table-card .badge small {
        opacity: 1 !important;
    }


    /* VISIBILIDAD FINAL DE MESAS */

    .pos-table-card {
        background-color: #ffffff !important;
    }

    .pos-table-card > div:first-child span {
        color: #1f2937 !important;
        font-size: .82rem !important;
        font-weight: 800 !important;
    }

    .pos-table-card .badge {
        opacity: 1 !important;
        visibility: visible !important;
    }

    .pos-table-card .bg-success {
        background-color: #16a34a !important;
        color: #ffffff !important;
    }

    .pos-table-card .bg-danger {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }

    .pos-table-card .bg-warning {
        background-color: #f59e0b !important;
        color: #111827 !important;
    }


    /* MAPA DEL SALÓN */

    .pos-floor {
        background-color: color-mix(in srgb, var(--pos-primary) 12%, #ffffff) !important;
        border-color: var(--pos-border) !important;
        background-image:
            radial-gradient(
                color-mix(in srgb, var(--pos-dark) 28%, transparent) 1px,
                transparent 1px
            ) !important;
        background-size: 22px 22px !important;
        box-shadow: inset 0 0 30px rgba(15, 23, 42, .03) !important;
    }

</style>

@if(session('success'))
    <div id="paymentSuccessToast" class="payment-success-toast">
        <div class="payment-success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <div class="payment-success-content">
            <strong>Pago completado</strong>
            <span>{{ session('success') }}</span>
        </div>

        <button
            type="button"
            class="payment-success-close"
            onclick="closePaymentSuccess()"
        >
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
@endif

<script>
    window.closePaymentSuccess = function() {
        const toast = document.getElementById('paymentSuccessToast');

        if (toast) {
            toast.classList.add('hide');

            setTimeout(function() {
                toast.remove();
            }, 250);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {

        const toast =
            document.getElementById('paymentSuccessToast');

        if (!toast) {
            return;
        }

        setTimeout(function() {

            if (toast) {
                toast.classList.add('hide');

                setTimeout(function() {
                    toast.remove();
                }, 250);
            }

        }, 5000);

    });
</script>

<style>
    .payment-success-toast {
        position: fixed;
        top: 22px;
        right: 24px;

        min-width: 320px;
        max-width: 420px;

        display: flex;
        align-items: center;
        gap: 12px;

        padding: 14px 16px;

        background: #ffffff;

        border-left: 5px solid #16a34a;
        border-radius: 12px;

        box-shadow:
            0 14px 35px
            rgba(15, 23, 42, .18);

        z-index: 9999;

        animation:
            paymentToastIn .28s ease;
    }

    .payment-success-icon {
        width: 38px;
        height: 38px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 50%;

        background: #dcfce7;
        color: #16a34a;

        font-size: 1.15rem;
    }

    .payment-success-content {
        flex: 1;
        min-width: 0;

        display: flex;
        flex-direction: column;
    }

    .payment-success-content strong {
        color: #166534;
        font-size: .86rem;
    }

    .payment-success-content span {
        color: #64748b;
        font-size: .76rem;
        margin-top: 2px;
    }

    .payment-success-close {
        border: 0;
        background: transparent;
        color: #94a3b8;

        width: 28px;
        height: 28px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;
    }

    .payment-success-close:hover {
        background: #f1f5f9;
        color: #475569;
    }

    .payment-success-toast.hide {
        opacity: 0;
        transform: translateX(20px);
        transition:
            opacity .25s ease,
            transform .25s ease;
    }

    @keyframes paymentToastIn {
        from {
            opacity: 0;
            transform: translateX(24px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

@endsection