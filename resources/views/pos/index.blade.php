@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
         NOTIFICACIÓN DE PAGO EXITOSO
    ========================================================== --}}

    @if(session('success'))

        <div
            id="paymentSuccessToast"
            class="payment-success-toast"
        >

            <div class="payment-toast-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <div class="payment-toast-content">

                <div class="payment-toast-title">
                    PAGO REALIZADO
                </div>

                <div class="payment-toast-message">
                    {{ session('success') }}
                </div>

            </div>

            <button
                type="button"
                class="payment-toast-close"
                onclick="closePaymentToast()"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>

    @endif


    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold text-dark mb-0">
                Punto de Venta
            </h2>

            <p class="text-muted mb-0">
                Selecciona una mesa para comenzar
            </p>

        </div>


        <div class="d-flex gap-3">

            <div class="d-flex align-items-center">

                <span
                    class="d-inline-block bg-success rounded-circle p-1 me-2"
                    style="width: 12px; height: 12px;"
                ></span>

                <small class="fw-bold text-muted">
                    Disponible
                </small>

            </div>


            <div class="d-flex align-items-center">

                <span
                    class="d-inline-block bg-danger rounded-circle p-1 me-2"
                    style="width: 12px; height: 12px;"
                ></span>

                <small class="fw-bold text-muted">
                    Ocupada
                </small>

            </div>


            <div class="d-flex align-items-center">

                <span
                    class="d-inline-block bg-warning rounded-circle p-1 me-2"
                    style="width: 12px; height: 12px;"
                ></span>

                <small class="fw-bold text-muted">
                    Reservada (Hoy)
                </small>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PESTAÑAS DE ÁREAS
    ========================================================== --}}

    <ul
        class="nav nav-tabs nav-pills mb-3"
        id="posTabs"
        role="tablist"
    >

        @foreach($areas as $index => $area)

            <li
                class="nav-item me-2"
                role="presentation"
            >

                <button
                    class="nav-link {{ $index == 0 ? 'active' : '' }} fw-bold px-4 border"
                    id="tab-{{ $area->id }}"
                    data-bs-toggle="tab"
                    data-bs-target="#area-{{ $area->id }}"
                    type="button"
                    role="tab"
                >

                    {{ $area->name }}

                </button>

            </li>

        @endforeach

    </ul>


    {{-- =========================================================
         CONTENIDO DE ÁREAS
    ========================================================== --}}

    <div class="tab-content">

        @foreach($areas as $index => $area)

            <div
                class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                id="area-{{ $area->id }}"
                role="tabpanel"
            >

                <div
                    class="position-relative border rounded-3 shadow-sm bg-light"
                    style="
                        height: 650px;
                        overflow: auto;
                        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
                        background-size: 20px 20px;
                    "
                >

                    @foreach($area->tables as $table)

                        @php

                            $order = $table->orders->first();

                            $reservations =
                                $table->reservations;

                            $isBusy =
                                $order ? true : false;

                            $hasReservations =
                                $reservations->count() > 0;


                            $cardClass = $isBusy

                                ? 'bg-white border-danger border-2 text-danger shadow-sm'

                                : (
                                    $hasReservations

                                    ? 'bg-white border-warning border-2 text-dark shadow-sm'

                                    : 'bg-white border-success border-2 text-success shadow-sm'
                                );


                            $icon =
                                $isBusy
                                    ? 'bi-display-fill'
                                    : 'bi-display';

                        @endphp


                        <a
                            href="{{ route('pos.order', $table->id) }}"
                            class="text-decoration-none text-dark"
                        >

                            <div
                                class="
                                    pos-table-card
                                    position-absolute
                                    d-flex
                                    flex-column
                                    align-items-center
                                    justify-content-between
                                    p-2
                                    rounded-3
                                    {{ $cardClass }}
                                "
                                style="
                                    width: 110px;
                                    height: 110px;
                                    left: {{ $table->x_pos }}px;
                                    top: {{ $table->y_pos }}px;
                                    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                                "
                            >

                                {{-- NOMBRE DE MESA --}}

                                <div
                                    class="w-100 text-center border-bottom pb-1 mb-1"
                                >

                                    <span
                                        class="fw-bold small text-uppercase"
                                        style="font-size: 0.75rem;"
                                    >

                                        {{ $table->name }}

                                    </span>

                                </div>


                                {{-- ICONO / RESERVAS --}}

                                <div
                                    class="
                                        flex-grow-1
                                        d-flex
                                        align-items-center
                                        justify-content-center
                                        position-relative
                                        w-100
                                    "
                                >

                                    <i
                                        class="
                                            bi
                                            {{ $icon }}
                                            fs-1
                                            {{ $isBusy ? 'text-danger' : 'text-secondary opacity-50' }}
                                        "
                                    ></i>


                                    {{-- RESERVAS --}}

                                    @if($hasReservations && !$isBusy)

                                        <div
                                            class="
                                                position-absolute
                                                top-50
                                                start-50
                                                translate-middle
                                                badge
                                                bg-warning
                                                text-dark
                                                border
                                                border-dark
                                                shadow-sm
                                            "
                                            style="
                                                font-size: 0.6rem;
                                                width: 100%;
                                                white-space: normal;
                                                line-height: 1.1;
                                                z-index: 2;
                                                max-height: 60px;
                                                overflow-y: auto;
                                            "
                                        >

                                            @foreach($reservations as $res)

                                                <div
                                                    class="{{ !$loop->last ? 'border-bottom border-dark pb-1 mb-1' : '' }}"
                                                >

                                                    <i class="bi bi-clock-fill"></i>

                                                    <strong>
                                                        {{ $res->reservation_time->format('H:i') }}
                                                    </strong>

                                                    <br>

                                                    {{ Str::limit($res->client_name, 9) }}

                                                </div>

                                            @endforeach

                                        </div>

                                    @endif

                                </div>


                                {{-- ESTADO DE LA MESA --}}

                                <div
                                    class="w-100 text-center mt-1"
                                >

                                    @if($isBusy)

                                        <div
                                            class="badge bg-danger w-100 py-1 shadow-sm"
                                        >

                                            <small
                                                style="font-size: 0.65rem;"
                                            >
                                                CONSUMO
                                            </small>

                                            <br>

                                            <span class="fs-6 fw-bold">

                                                {{ $currency ?? 'S/' }}{{ number_format($order->total, 2) }}

                                            </span>

                                        </div>


                                    @else

                                        @if($hasReservations)

                                            <div
                                                class="
                                                    badge
                                                    bg-warning
                                                    text-dark
                                                    w-100
                                                    py-2
                                                    shadow-sm
                                                    border
                                                    border-warning
                                                "
                                            >

                                                {{ $reservations->count() }}
                                                RESERVA(S)

                                            </div>

                                        @else

                                            <div
                                                class="
                                                    badge
                                                    bg-success
                                                    w-100
                                                    py-2
                                                    shadow-sm
                                                "
                                            >

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


{{-- =============================================================
     ESTILOS
============================================================= --}}

<style>

    /* ---------------------------------------------------------
       TARJETAS DE MESAS
    --------------------------------------------------------- */

    .pos-table-card:hover {

        transform:
            scale(1.1)
            translateY(-5px);

        z-index: 100 !important;

        box-shadow:
            0 10px 15px -3px
            rgba(0, 0, 0, 0.1)
            !important;

        cursor: pointer;
    }


    /* ---------------------------------------------------------
       SCROLLBAR RESERVAS
    --------------------------------------------------------- */

    .badge::-webkit-scrollbar {

        width: 0px;

        background: transparent;
    }


    /* ---------------------------------------------------------
       PESTAÑAS
    --------------------------------------------------------- */

    .nav-pills .nav-link.active {

        background-color: #0d6efd;

        color: white;

        box-shadow:
            0 4px 6px
            rgba(13, 110, 253, 0.3);
    }


    .nav-pills .nav-link {

        color: #495057;

        background-color: #fff;
    }


    /* =========================================================
       NOTIFICACIÓN DE PAGO
    ========================================================= */

    .payment-success-toast {

        position: fixed;

        top: 85px;

        right: 25px;

        width: 390px;

        max-width: calc(100vw - 30px);

        display: flex;

        align-items: center;

        gap: 14px;

        padding: 16px 18px;

        background: #ffffff;

        border-left: 5px solid #198754;

        border-radius: 12px;

        box-shadow:
            0 10px 35px
            rgba(0, 0, 0, 0.18);

        z-index: 99999;

        animation:
            paymentToastIn
            0.45s
            ease-out;
    }


    /* ICONO */

    .payment-toast-icon {

        width: 45px;

        height: 45px;

        min-width: 45px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background: #198754;

        color: #ffffff;

        font-size: 24px;
    }


    /* CONTENIDO */

    .payment-toast-content {

        flex: 1;

        min-width: 0;
    }


    .payment-toast-title {

        font-size: 14px;

        font-weight: 800;

        color: #198754;

        margin-bottom: 3px;

        letter-spacing: 0.3px;
    }


    .payment-toast-message {

        font-size: 13px;

        line-height: 1.45;

        color: #495057;
    }


    /* BOTÓN CERRAR */

    .payment-toast-close {

        border: none;

        background: transparent;

        color: #6c757d;

        font-size: 15px;

        padding: 4px;

        cursor: pointer;

        transition: 0.2s;
    }


    .payment-toast-close:hover {

        color: #212529;

        transform: scale(1.1);
    }


    /* ANIMACIÓN ENTRADA */

    @keyframes paymentToastIn {

        from {

            opacity: 0;

            transform:
                translateX(100px)
                scale(0.95);
        }

        to {

            opacity: 1;

            transform:
                translateX(0)
                scale(1);
        }
    }


    /* ANIMACIÓN SALIDA */

    .payment-success-toast.hide {

        animation:
            paymentToastOut
            0.35s
            ease-in
            forwards;
    }


    @keyframes paymentToastOut {

        from {

            opacity: 1;

            transform: translateX(0);
        }

        to {

            opacity: 0;

            transform: translateX(100px);
        }
    }


    /* ---------------------------------------------------------
       RESPONSIVE
    --------------------------------------------------------- */

    @media (max-width: 576px) {

        .payment-success-toast {

            top: 70px;

            right: 15px;

            left: 15px;

            width: auto;
        }

    }

</style>


{{-- =============================================================
     JAVASCRIPT DE NOTIFICACIÓN
============================================================= --}}

@if(session('success'))

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const toast =
                document.getElementById(
                    'paymentSuccessToast'
                );

            if (!toast) {
                return;
            }


            // -----------------------------------------------------
            // CERRAR AUTOMÁTICAMENTE DESPUÉS DE 5 SEGUNDOS
            // -----------------------------------------------------

            setTimeout(
                function () {

                    closePaymentToast();

                },
                5000
            );

        }
    );


    function closePaymentToast() {

        const toast =
            document.getElementById(
                'paymentSuccessToast'
            );

        if (!toast) {
            return;
        }


        toast.classList.add(
            'hide'
        );


        setTimeout(
            function () {

                toast.remove();

            },
            350
        );
    }

</script>

@endif

@endsection