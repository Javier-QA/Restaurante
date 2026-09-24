<div class="p-2 pos-cart-body">
    @if($order && $order->details->count() > 0)
        <div class="table-responsive" style="overflow-x: hidden;">
            <table class="table table-borderless align-middle mb-0" style="width: 100%; table-layout: fixed;">
                <thead class="text-muted small border-bottom">
                    <tr>
                        <th style="width: 30px;"></th> <th>PROD.</th>
                        <th class="text-center" style="width: 85px;">CANT.</th>
                        <th class="text-end" style="width: 65px;">TOT.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $detail)
                        <tr class="border-bottom">

                            <td class="px-0 align-middle">
                                @if($detail->status === 'draft')
                                    <button class="btn btn-sm text-danger p-0"
                                            onclick="removeItem({{ $detail->id }})"
                                            title="Eliminar">
                                        <i class="bi bi-x-circle-fill fs-5"></i>
                                    </button>
                                @else
                                    <i class="bi bi-lock-fill text-muted"
                                       title="Plato enviado a Cocina"></i>
                                @endif
                            </td>

                            <td class="align-middle px-1">
                                <div class="fw-bold text-dark text-truncate"
                                     style="width: 100%; font-size: 0.85rem;"
                                     title="{{ $detail->product->name }}">
                                    {{ $detail->product->name }}
                                </div>

                                <div class="d-flex flex-column align-items-start">
                                    <small class="text-muted me-1" style="font-size: 0.7rem;">
                                        {{ $currency ?? 'S/' }}{{ number_format($detail->price, 2) }}
                                    </small>

                                    @if($detail->status === 'draft')
                                        <a href="javascript:void(0)"
                                           class="text-decoration-none text-warning text-nowrap"
                                           style="font-size: 0.72rem;"
                                           data-bs-toggle="modal"
                                           data-bs-target="#noteModal"
                                           data-detail-id="{{ $detail->id }}"
                                           data-note-content="{{ $detail->note }}">
                                            <i class="bi bi-chat-left-text me-1"></i>{{ $detail->note ? 'Editar nota' : 'Agregar nota' }}
                                        </a>
                                    @else
                                        @if($detail->note)
                                            <small class="text-muted text-truncate"
                                                   style="font-size: 0.7rem; max-width: 150px;"
                                                   title="{{ $detail->note }}">
                                                <i class="bi bi-chat-left-text me-1"></i>{{ $detail->note }}
                                            </small>
                                        @endif

                                        @if($detail->status === 'pending')
                                            <small class="fw-semibold text-primary" style="font-size: 0.7rem;">
                                                <i class="bi bi-send-check me-1"></i>Enviado a cocina
                                            </small>
                                        @elseif($detail->status === 'cooking')
                                            <small class="fw-semibold text-warning" style="font-size: 0.7rem;">
                                                <i class="bi bi-fire me-1"></i>En preparación
                                            </small>
                                        @elseif($detail->status === 'served')
                                            <small class="fw-semibold text-success" style="font-size: 0.7rem;">
                                                <i class="bi bi-check-circle me-1"></i>Preparado
                                            </small>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <td class="align-middle px-0">
                                @if($detail->status === 'draft')
                                    <div class="input-group input-group-sm flex-nowrap">
                                        <button class="btn btn-outline-secondary px-1 py-0"
                                                style="font-size: 0.8rem;"
                                                onclick="updateQty({{ $detail->id }}, {{ $detail->quantity - 1 }})">-</button>

                                        <input type="text"
                                               class="form-control text-center px-0 py-0 fw-bold bg-white border-secondary"
                                               value="{{ $detail->quantity }}"
                                               readonly
                                               style="font-size: 0.85rem;">

                                        <button class="btn btn-outline-primary px-1 py-0"
                                                style="font-size: 0.8rem;"
                                                onclick="updateQty({{ $detail->id }}, {{ $detail->quantity + 1 }})">+</button>
                                    </div>
                                @else
                                    <div class="text-center fw-bold">
                                        {{ $detail->quantity }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-end align-middle fw-bold text-dark px-0"
                                style="font-size: 0.9rem;">
                                {{ number_format($detail->quantity * $detail->price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted opacity-50">
            <i class="bi bi-basket3 display-1 mb-3"></i>
            <p class="small text-center m-0">Cuenta vacía</p>
        </div>
    @endif
</div>

<div class="pos-cart-summary border-top p-3" style="flex-shrink: 0; margin-top: auto;">
    @if($order)
        <input type="hidden" id="cartTotalValue" value="{{ number_format($order->total + ($order->tip ?? 0) - ($order->discount ?? 0), 2, '.', '') }}">

        <div class="row mb-1" style="font-size: 0.8rem;">
            <div class="col-6 text-muted">Subtotal:</div>
            <div class="col-6 text-end">{{ number_format($order->total, 2) }}</div>
        </div>
        
        @if($order->discount > 0)
            <div class="row mb-1 text-danger fw-bold" style="font-size: 0.8rem;">
                <div class="col-6">Descuento:</div>
                <div class="col-6 text-end">-{{ number_format($order->discount, 2) }}</div>
            </div>
        @endif

        @if($order->tip > 0)
            <div class="row mb-1 text-success fw-bold" style="font-size: 0.8rem;">
                <div class="col-6">Propina:</div>
                <div class="col-6 text-end">+{{ number_format($order->tip, 2) }}</div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-2 mt-2 border-top pt-2">
            <h5 class="mb-0 fw-bold text-dark fs-6">TOTAL:</h5>
            <h3 class="mb-0 fw-bold text-primary fs-4">
                {{ $currency ?? 'S/' }}{{ number_format($order->total + ($order->tip ?? 0) - ($order->discount ?? 0), 2) }}
            </h3>
        </div>

        <div class="row g-1 mb-2">
            <div class="col-4">
                <button class="btn btn-light w-100 border text-muted btn-sm fw-bold py-2" data-bs-toggle="modal" data-bs-target="#optionsModal" title="Opciones">
                    <i class="bi bi-sliders"></i> <span style="font-size: 0.7rem;">Opc.</span>
                </button>
            </div>
            <div class="col-4">
                @if($order->details->contains('status', 'draft'))
                    <button type="button"
                            class="btn btn-light w-100 border text-muted btn-sm fw-bold py-2"
                            title="Dividir"
                            onclick="showPosNotification('Primero debes enviar todos los platos a Cocina antes de dividir la cuenta.')">
                        <i class="bi bi-scissors"></i>
                        <span style="font-size: 0.7rem;">Div.</span>
                    </button>
                @elseif($order->details->contains('status', 'pending'))
                    <button type="button"
                            class="btn btn-light w-100 border text-muted btn-sm fw-bold py-2"
                            title="Dividir"
                            onclick="showPosNotification('Cocina debe iniciar la preparación antes de dividir la cuenta.')">
                        <i class="bi bi-scissors"></i>
                        <span style="font-size: 0.7rem;">Div.</span>
                    </button>
                @else
                    <a href="{{ route('pos.split.content', $order->id) }}"
                       class="btn btn-light w-100 border text-muted btn-sm fw-bold py-2"
                       title="Dividir">
                        <i class="bi bi-scissors"></i>
                        <span style="font-size: 0.7rem;">Div.</span>
                    </a>
                @endif
            </div>
            <div class="col-4">
                <a href="{{ route('pos.precheck', $order->id) }}"
   class="btn btn-light w-100 border text-muted btn-sm fw-bold py-2"
   title="Pre-cuenta"
   onclick="window.open(this.href, 'precuenta', 'width=430,height=720,resizable=yes,scrollbars=yes'); return false;">
                    <i class="bi bi-receipt"></i> <span style="font-size: 0.7rem;">Pre</span>
                </a>
            </div>
        </div>

        @if($order->details->contains('status', 'draft'))
            <div class="d-grid mb-2">
                <button type="button"
                        class="btn pos-send-order-btn fw-bold py-2 shadow-sm"
                        onclick="confirmAndSendToKitchen({{ $order->id }})">
                    <i class="bi bi-send-check me-2"></i>
                    ENVIAR PEDIDO
                </button>
            </div>
        @endif
        <div class="d-grid">
            @if($order->details->contains('status', 'draft'))
                <button type="button"
                        class="btn btn-success fw-bold py-2 shadow-sm"
                        onclick="showPosNotification('Primero debes enviar todos los platos a Cocina antes de cobrar.')">
                    <i class="bi bi-cash-coin me-2"></i> COBRAR
                </button>
            @elseif($order->details->contains('status', 'pending'))
                <button type="button"
                        class="btn btn-success fw-bold py-2 shadow-sm"
                        onclick="showPosNotification('Cocina debe iniciar la preparación antes de cobrar.')">
                    <i class="bi bi-cash-coin me-2"></i> COBRAR
                </button>
            @else
                <button type="button"
                        class="btn btn-success fw-bold py-2 shadow-sm"
                        onclick="var m=document.getElementById('checkoutModal'); if(m){ new bootstrap.Modal(m).show(); }">
                    <i class="bi bi-cash-coin me-2"></i> COBRAR
                </button>
            @endif
        </div>
    @endif
</div>

<style>
    .pos-send-order-btn {
        background: var(--accent-1, #0b84c6) !important;
        border-color: var(--accent-1, #0b84c6) !important;
        color: #ffffff !important;
    }

    .pos-send-order-btn:hover {
        background: color-mix(in srgb, var(--accent-1, #0b84c6) 85%, #000000) !important;
        border-color: color-mix(in srgb, var(--accent-1, #0b84c6) 85%, #000000) !important;
        color: #ffffff !important;
    }

    .pos-cart-body {
        color: var(--pos-text, #172033);
    }

    .pos-cart-body table thead {
        color: var(--pos-muted, #64748b) !important;
    }

    .pos-cart-body tbody tr {
        transition: background .15s ease;
    }

    .pos-cart-body tbody tr:hover {
        background:
            color-mix(
                in srgb,
                var(--pos-primary, #ff8c00) 5%,
                #ffffff
            );
    }

    .pos-cart-body .text-primary {
        color: var(--pos-primary, #ff8c00) !important;
    }

    .pos-cart-body .btn-outline-primary {
        color: var(--pos-primary, #ff8c00) !important;
        border-color: var(--pos-primary, #ff8c00) !important;
    }

    .pos-cart-body .btn-outline-primary:hover {
        background: var(--pos-primary, #ff8c00) !important;
        color: #fff !important;
    }

    .pos-cart-summary {
        background:
            color-mix(
                in srgb,
                var(--pos-primary, #ff8c00) 4%,
                #ffffff
            ) !important;

        border-color: var(--pos-border, #dce7f1) !important;
    }

    .pos-cart-summary h3.text-primary {
        color: var(--pos-primary, #ff8c00) !important;
    }

    .pos-cart-summary .btn-light {
        background: #ffffff !important;
        border-color: var(--pos-border, #dce7f1) !important;
        color: var(--pos-muted, #64748b) !important;
    }

    .pos-cart-summary .btn-light:hover {
        color: var(--pos-primary, #ff8c00) !important;
        border-color: var(--pos-primary, #ff8c00) !important;
    }

    .pos-cart-summary .btn-success {
        background: var(--pos-primary, #ff8c00) !important;
        border-color: var(--pos-primary, #ff8c00) !important;
        color: #fff !important;
    }

    .pos-cart-summary .btn-success:hover {
        background: var(--pos-primary-hover, #e07b00) !important;
        border-color: var(--pos-primary-hover, #e07b00) !important;
    }

</style>

