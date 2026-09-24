@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-gray-800"><i class="bi bi-bicycle me-2 text-primary"></i>Delivery #{{ $delivery->id }}</h3>
            <span class="badge rounded-pill mt-2" style="background-color: {{ \App\Models\Delivery::$statusColors[$delivery->status] ?? '#6c757d' }}">
                {{ $delivery->status_label }}
            </span>
        </div>
        <div>
            <a href="{{ route('delivery.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            @if(!in_array($delivery->status, ['delivered', 'cancelled']))
                <button class="btn btn-danger me-2" onclick="if(confirm('¿Seguro que desea cancelar este pedido?')) document.getElementById('cancel-form').submit();">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <form id="cancel-form" action="{{ route('delivery.cancel', $delivery) }}" method="POST" class="d-none">@csrf</form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Detalles del Cliente y Estado --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2"></i>Datos del Cliente</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block fw-bold">Nombre / Teléfono</small>
                        <div class="fw-bold fs-6">{{ $delivery->client_name }}</div>
                        <div><i class="bi bi-telephone text-primary me-2"></i>{{ $delivery->client_phone }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block fw-bold">Dirección de Entrega</small>
                        <div><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $delivery->address }}</div>
                        @if($delivery->reference)
                            <div class="text-muted small mt-1"><i class="bi bi-info-circle me-1"></i>{{ $delivery->reference }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block fw-bold">Método de Pago</small>
                        <div class="text-capitalize"><i class="bi bi-credit-card-2-front me-2"></i>
                            @if($delivery->payment_method == 'cash') Efectivo
                            @elseif($delivery->payment_method == 'card') Tarjeta
                            @else Transferencia / Yape @endif
                        </div>
                    </div>
                    @if($delivery->notes)
                        <div class="mb-3">
                            <small class="text-muted d-block fw-bold">Notas</small>
                            <div class="bg-light p-2 rounded small border">{{ $delivery->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if(!in_array($delivery->status, ['delivered', 'cancelled']))
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i>{{ $delivery->delivery_type === 'pickup' ? 'Gestión de Recojo' : 'Gestión de Envío' }}</h6>
                    </div>
                    <div class="card-body">
                        @if($delivery->delivery_type === 'delivery')
                        <form action="{{ route('delivery.driver', $delivery) }}" method="POST" class="mb-4" id="driverForm">
                            @csrf
                            <label class="form-label small fw-bold">Delivery Asignado</label>
                            <div class="input-group">
                                <select name="driver_id" class="form-select" id="driver_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" {{ $delivery->driver_id == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-primary" type="submit" id="updateDriverBtn">Actualizar</button>
                            </div>
                            <div id="driverUpdateMessage" class="mt-2" style="display:none;"></div>
                        </form>
                        @endif

                        @if($delivery->status === 'pending')
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-hourglass-split me-1"></i>
                                Pedido pendiente de preparación en cocina.
                            </div>
                        @elseif($delivery->status === 'preparing')
                            <div class="alert alert-primary mb-0">
                                <i class="bi bi-fire me-1"></i>
                                El pedido se está preparando en cocina.
                            </div>
                        @elseif($delivery->status === 'on_way')
                            <div class="alert alert-info mb-0">
                                @if($delivery->delivery_type === 'pickup')
                                    <i class="bi bi-bag-check me-1"></i>
                                    El pedido está listo para recoger.
                                @else
                                    <i class="bi bi-truck me-1"></i>
                                    El pedido está en camino.
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Detalles de la Orden y Cobro --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Detalle de la Orden</h6>
                    <small class="text-muted"><i class="bi bi-clock me-1"></i>Pedido: {{ $delivery->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-body d-flex flex-column p-0">
                    <div class="table-responsive flex-grow-1 p-3">
                        <table class="table table-borderless align-middle">
                            <thead class="border-bottom text-muted small">
                                <tr>
                                    <th>CANT.</th>
                                    <th>PRODUCTO</th>
                                    <th class="text-end">P.UNIT</th>
                                    <th class="text-end">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delivery->order->details as $detail)
                                    <tr class="border-bottom">
                                        <td class="fw-bold">{{ $detail->quantity }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $detail->product->name }}</span>
                                            @if($detail->note)
                                                <br><small class="text-muted"><i class="bi bi-chat-left-text me-1"></i>{{ $detail->note }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted">{{ $currency }}{{ number_format($detail->price, 2) }}</td>
                                        <td class="text-end fw-bold">{{ $currency }}{{ number_format($detail->quantity * $detail->price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-light p-4 border-top flex-shrink-0">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-2 text-muted">
                                    <span>Subtotal de productos:</span>
                                    <span>{{ $currency }}{{ number_format($delivery->order->total, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-muted">
                                    <span>Costo de envío:</span>
                                    <span>+ {{ $currency }}{{ number_format($delivery->delivery_fee, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-4 fs-4 fw-bold border-top pt-2">
                                    <span>TOTAL:</span>
                                    <span class="text-primary">{{ $currency }}{{ number_format($delivery->total_with_fee, 2) }}</span>
                                </div>
                                
                                @if($delivery->status === 'on_way')
                                    <button class="btn btn-success btn-lg w-100 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                                        <i class="bi bi-check-circle me-2"></i> MARCAR COMO ENTREGADO Y COBRAR
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE COBRO --}}
@if(!in_array($delivery->status, ['delivered', 'cancelled']))
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-cash-coin me-2"></i>Completar Pedido
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('delivery.checkout', $delivery) }}" method="POST">
                @csrf

                <div class="modal-body p-4">

                    {{-- TOTAL --}}
                    <div class="text-center mb-4">
                        <div class="text-muted small fw-bold mb-1">TOTAL A COBRAR</div>
                        <h2 class="display-4 fw-bold text-success mb-0">
                            {{ $currency }}{{ number_format($delivery->total_with_fee, 2) }}
                        </h2>
                    </div>

                    {{-- COMPROBANTE --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Comprobante</label>

                        <select name="document_type"
                                id="document_type"
                                class="form-select form-select-lg">

                            <option value="Ticket">Ticket de Venta</option>
                            <option value="Boleta">Boleta Electrónica</option>
                            <option value="Factura">Factura Electrónica</option>

                        </select>
                    </div>

                    {{-- DATOS DEL CLIENTE PARA COMPROBANTE --}}
                    <div id="electronicClientFields"
                         class="border rounded p-3 mb-3 bg-light"
                         style="display:none;">

                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-person-vcard me-2"></i>
                            Datos del comprobante
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold" id="documentLabel">
                                DNI
                            </label>

                            <input type="text"
                                   name="client_document"
                                   id="client_document"
                                   class="form-control"
                                   placeholder="Ingrese DNI"
                                   maxlength="11">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold" id="nameLabel">
                                Nombre del cliente
                            </label>

                            <input type="text"
                                   name="business_name"
                                   id="business_name"
                                   class="form-control"
                                   value="{{ $delivery->client_name }}"
                                   placeholder="Nombre del cliente">
                        </div>

                    </div>

                    {{-- MÉTODO DE PAGO --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Método de pago</label>
                        <select name="payment_method" id="payment_method" class="form-select form-select-lg" required>
                            <option value="cash" {{ $delivery->payment_method === "cash" ? "selected" : "" }}>Efectivo</option>
                            <option value="card" {{ $delivery->payment_method === "card" ? "selected" : "" }}>Tarjeta</option>
                            <option value="yape" {{ $delivery->payment_method === "yape" ? "selected" : "" }}>Yape</option>
                            <option value="plin" {{ $delivery->payment_method === "plin" ? "selected" : "" }}>Plin</option>
                        </select>
                    </div>

                    {{-- EFECTIVO --}}
                    <div id="cashPaymentFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Monto recibido</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">{{ $currency }}</span>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       name="received_amount"
                                       id="received_amount"
                                       class="form-control fw-bold text-end"
                                       value="{{ number_format($delivery->total_with_fee, 2, ".", "") }}">
                            </div>
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center bg-light p-3 rounded border">
                            <span class="fw-bold text-muted">Vuelto a entregar:</span>
                            <span class="fs-4 fw-bold text-danger" id="change_amount">{{ $currency }}0.00</span>
                        </div>
                    </div>

                    {{-- TARJETA --}}
                    <div id="cardPaymentFields" class="alert alert-primary" style="display:none;">
                        <i class="bi bi-credit-card me-2"></i>
                        Pago con <strong>Tarjeta</strong>.
                    </div>

                    {{-- YAPE --}}
                    <div id="yapePaymentFields" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-phone me-2"></i>
                            Pago con <strong>Yape</strong>
                        </div>

                        <div class="text-center border rounded p-3 bg-light">
                            <h6 class="fw-bold mb-3">Escanea el QR de Yape</h6>

                            @if($yapeQr)
                                <img src="{{ Storage::url($yapeQr) }}"
                                     alt="QR Yape"
                                     class="img-fluid rounded border bg-white p-2"
                                     style="width:240px;height:240px;object-fit:contain;">

                                <div class="mt-3 fw-bold text-success fs-4">
                                    {{ $currency }}{{ number_format($delivery->total_with_fee, 2) }}
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    No se ha configurado el QR de Yape.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- PLIN --}}
                    <div id="plinPaymentFields" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-phone me-2"></i>
                            Pago con <strong>Plin</strong>
                        </div>

                        <div class="text-center border rounded p-3 bg-light">
                            <h6 class="fw-bold mb-3">Escanea el QR de Plin</h6>

                            @if($plinQr)
                                <img src="{{ Storage::url($plinQr) }}"
                                     alt="QR Plin"
                                     class="img-fluid rounded border bg-white p-2"
                                     style="width:240px;height:240px;object-fit:contain;">

                                <div class="mt-3 fw-bold text-success fs-4">
                                    {{ $currency }}{{ number_format($delivery->total_with_fee, 2) }}
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    No se ha configurado el QR de Plin.
                                </div>
                            @endif
                        </div>
                    </div>


                    @else

                        <input type="hidden"
                               name="received_amount"
                               value="{{ $delivery->total_with_fee }}">

                    @endif

                </div>

                <div class="modal-footer bg-light border-0">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-success px-4 fw-bold shadow-sm">

                        <i class="bi bi-check2-circle me-2"></i>
                        Finalizar y Cobrar

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<style>

/* =========================================================
   DISEÑO - DETALLE DELIVERY / RECOJO
   ========================================================= */

.container-fluid.py-4 {
    max-width: 1500px;
    padding-left: 28px;
    padding-right: 28px;
}

/* TARJETAS */

.card {
    border-radius: 16px !important;
    overflow: hidden;
}

.card.shadow-sm {
    box-shadow: var(--theme-shadow) !important;
}

.card-header {
    padding: 18px 20px !important;
}

.card-header h6 {
    font-size: 0.95rem;
    letter-spacing: 0.01em;
}

.card-body {
    color: var(--text-main);
}

/* INTEGRACION CON PALETAS */

.card {
    background: var(--card-bg);
    border-color: var(--border-soft) !important;
}

.card-header {
    background: var(--card-bg);
    color: var(--text-main);
    border-color: var(--border-soft) !important;
}

.card-body {
    background: var(--card-bg);
}

.card-body small.text-muted,
.text-muted {
    color: var(--text-muted) !important;
}

.form-select,
.form-control,
.input-group-text {
    background-color: var(--card-bg);
    color: var(--text-main);
}

.table {
    --bs-table-bg: var(--card-bg);
    --bs-table-color: var(--text-main);
    --bs-table-border-color: var(--border-soft);
}

.table tbody td {
    color: var(--text-main);
}

#checkoutModal .modal-content {
    background: var(--card-bg);
    color: var(--text-main);
    border-color: var(--border-soft);
}

#checkoutModal .modal-header,
#checkoutModal .modal-footer {
    background: var(--card-bg);
    border-color: var(--border-soft);
}

#checkoutModal .modal-title {
    color: var(--text-main);
}
/* INFORMACION DEL CLIENTE */

.card-body small.text-muted.d-block {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.045em;
    margin-bottom: 5px;
}

.card-body .fw-bold.fs-6 {
    color: var(--text-main);
}

/* ESTADO */

.badge.rounded-pill {
    padding: 7px 13px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* BOTONES */

.btn {
    border-radius: 9px;
}

.btn-lg {
    border-radius: 11px;
}

.btn-success.btn-lg {
    min-height: 52px;
}

/* SELECT */

select.form-select {
    appearance: auto !important;
    -webkit-appearance: auto !important;
    -moz-appearance: auto !important;
    cursor: pointer;
    border-radius: 9px;
}

.form-select,
.form-control {
    border-color: var(--border-soft);
}

.form-select:focus,
.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem color-mix(in srgb, var(--primary) 16%, transparent);
}

/* GESTION DE ENVIO */

#driverForm .input-group {
    border-radius: 9px;
}

#driverForm .form-select {
    min-height: 42px;
}

#updateDriverBtn {
    min-width: 105px;
}

/* ALERTAS DE ESTADO */

.card-body > .alert {
    border: 0;
    border-radius: 10px;
    font-size: 0.9rem;
}

/* TABLA DE PRODUCTOS */

.table {
    margin-bottom: 0;
}

.table thead th {
    padding-top: 12px;
    padding-bottom: 12px;
    font-size: 0.72rem;
    letter-spacing: 0.045em;
    color: var(--text-muted);
}

.table tbody td {
    padding-top: 16px;
    padding-bottom: 16px;
    vertical-align: middle;
}

.table tbody tr:last-child {
    border-bottom: 0 !important;
}

/* RESUMEN */

.bg-light.p-4.border-top {
    background: var(--light-bg) !important;
}

.bg-light.p-4.border-top .fs-4 {
    color: var(--text-main);
}

/* MODAL DE COBRO */

#checkoutModal .modal-content {
    border-radius: 18px;
    overflow: hidden;
}

#checkoutModal .modal-header {
    padding: 18px 24px;
}

#checkoutModal .modal-body {
    padding: 28px !important;
}

#checkoutModal .modal-footer {
    padding: 16px 24px;
}

#checkoutModal .display-4 {
    font-size: 2.8rem;
}

#checkoutModal .form-select-lg,
#checkoutModal .form-control {
    border-radius: 10px;
}

/* DATOS DEL COMPROBANTE */

#electronicClientFields {
    border-color: var(--border-soft) !important;
    border-radius: 12px !important;
    padding: 18px !important;
}

/* EFECTIVO */

#cashPaymentFields .input-group-text {
    font-weight: 700;
    border-color: var(--border-soft);
}

#change_amount {
    min-width: 110px;
    text-align: right;
}

/* QR */

#yapePaymentFields .text-center,
#plinPaymentFields .text-center {
    border-radius: 14px !important;
}

#yapePaymentFields img,
#plinPaymentFields img {
    width: 220px !important;
    height: 220px !important;
    border-radius: 12px !important;
}

/* RESPONSIVE */

@media (max-width: 767.98px) {

    .container-fluid.py-4 {
        padding-left: 14px;
        padding-right: 14px;
    }

    .container-fluid.py-4 > .d-flex:first-child {
        align-items: flex-start !important;
        gap: 15px;
        flex-direction: column;
    }

    .container-fluid.py-4 > .d-flex:first-child > div:last-child {
        width: 100%;
        display: flex;
    }

    .container-fluid.py-4 > .d-flex:first-child > div:last-child .btn {
        flex: 1;
    }

    .col-md-4 {
        margin-bottom: 20px;
    }

    #checkoutModal .modal-body {
        padding: 20px !important;
    }

    #checkoutModal .display-4 {
        font-size: 2.2rem;
    }

}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const total = {{ number_format($delivery->total_with_fee, 2, '.', '') }};

    // =====================================================
    // METODO DE PAGO
    // =====================================================

    const receivedInput = document.getElementById('received_amount');
    const changeSpan = document.getElementById('change_amount');

    const paymentMethod = document.getElementById('payment_method');
    const cashPaymentFields = document.getElementById('cashPaymentFields');
    const cardPaymentFields = document.getElementById('cardPaymentFields');
    const yapePaymentFields = document.getElementById('yapePaymentFields');
    const plinPaymentFields = document.getElementById('plinPaymentFields');

    function updatePaymentFields() {

        if (!paymentMethod) return;

        const method = paymentMethod.value;

        if (cashPaymentFields) {
            cashPaymentFields.style.display =
                method === 'cash' ? 'block' : 'none';
        }

        if (cardPaymentFields) {
            cardPaymentFields.style.display =
                method === 'card' ? 'block' : 'none';
        }

        if (yapePaymentFields) {
            yapePaymentFields.style.display =
                method === 'yape' ? 'block' : 'none';
        }

        if (plinPaymentFields) {
            plinPaymentFields.style.display =
                method === 'plin' ? 'block' : 'none';
        }

        if (receivedInput) {
            receivedInput.required = method === 'cash';
        }

        if (receivedInput && method !== 'cash') {
            receivedInput.value = total.toFixed(2);
        }

        if (receivedInput && changeSpan) {
            const received = parseFloat(receivedInput.value) || 0;
            const change = Math.max(0, received - total);

            changeSpan.innerText =
                '{{ $currency }}' + change.toFixed(2);
        }
    }

    if (paymentMethod) {
        paymentMethod.addEventListener('change', updatePaymentFields);
        updatePaymentFields();
    }


    // =====================================================
    // COMPROBANTE
    // =====================================================

    const documentType = document.getElementById('document_type');
    const electronicFields =
        document.getElementById('electronicClientFields');

    const documentInput =
        document.getElementById('client_document');

    const documentLabel =
        document.getElementById('documentLabel');

    const nameLabel =
        document.getElementById('nameLabel');

    const businessName =
        document.getElementById('business_name');

    let documentSearchTimer = null;

    function updateDocumentFields() {

        if (!documentType || !electronicFields) return;

        const type = documentType.value;

        if (type === 'Ticket') {

            electronicFields.style.display = 'block';

            if (documentInput) {
                documentInput.required = false;
                documentInput.value = '';
                documentInput.placeholder = 'No requerido para Ticket';
            }

            if (documentLabel) {
                documentLabel.innerText = 'Documento';
            }

            if (businessName) {
                businessName.required = false;
                businessName.value = @json($delivery->client_name);
                businessName.placeholder = 'Nombre del cliente';
            }

            if (nameLabel) {
                nameLabel.innerText = 'Nombre del cliente';
            }

        } else if (type === 'Boleta') {

            electronicFields.style.display = 'block';

            documentLabel.innerText = 'DNI';
            documentInput.placeholder = 'Ingrese DNI';
            documentInput.maxLength = 8;

            nameLabel.innerText = 'Nombre del cliente';
            businessName.placeholder = 'Nombre del cliente';

            documentInput.required = true;
            businessName.required = true;

        } else if (type === 'Factura') {

            electronicFields.style.display = 'block';

            documentLabel.innerText = 'RUC';
            documentInput.placeholder = 'Ingrese RUC';
            documentInput.maxLength = 11;

            nameLabel.innerText = 'Razón social';
            businessName.placeholder = 'Razón social';

            documentInput.required = true;
            businessName.required = true;
        }
    }


    // =====================================================
    // BUSCAR CLIENTE POR DNI / RUC
    // =====================================================

    async function searchClientByDocument() {

        if (!documentInput || !businessName || !documentType) {
            return;
        }

        const document = documentInput.value.trim();
        const type = documentType.value;

        let requiredLength = 0;

        if (type === 'Boleta') {
            requiredLength = 8;
        } else if (type === 'Factura') {
            requiredLength = 11;
        } else {
            return;
        }

        if (document.length !== requiredLength) {
            return;
        }

        businessName.placeholder = 'Consultando...';

        try {

            const response = await fetch(
                "{{ url('/clients/document') }}/" +
                encodeURIComponent(document),
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            const data = await response.json();

            if (response.ok && data.found && data.client) {

                businessName.value = data.client.name || '';

                businessName.placeholder =
                    type === 'Factura'
                        ? 'Razón social'
                        : 'Nombre del cliente';

            } else {

                businessName.value = '';

                businessName.placeholder =
                    type === 'Factura'
                        ? 'Ingrese razón social'
                        : 'Ingrese nombre del cliente';
            }

        } catch (error) {

            console.error(
                'Error al consultar DNI/RUC:',
                error
            );

            businessName.placeholder =
                type === 'Factura'
                    ? 'Ingrese razón social'
                    : 'Ingrese nombre del cliente';
        }
    }


    if (documentType) {
        documentType.addEventListener(
            'change',
            updateDocumentFields
        );

        updateDocumentFields();
    }


    if (documentInput) {

        documentInput.addEventListener('input', function () {

            clearTimeout(documentSearchTimer);

            const type =
                documentType ? documentType.value : '';

            const maxLength =
                type === 'Factura' ? 11 : 8;

            this.value =
                this.value
                    .replace(/\D/g, '')
                    .slice(0, maxLength);

            const requiredLength =
                type === 'Factura' ? 11 : 8;

            if (this.value.length === requiredLength) {

                documentSearchTimer =
                    setTimeout(
                        searchClientByDocument,
                        300
                    );
            }
        });
    }


    // =====================================================
    // CALCULO DE VUELTO
    // =====================================================

    if (receivedInput && changeSpan) {

        receivedInput.addEventListener(
            'input',
            function () {

                const received =
                    parseFloat(this.value) || 0;

                const change =
                    Math.max(0, received - total);

                changeSpan.innerText =
                    '{{ $currency }}' +
                    change.toFixed(2);
            }
        );
    }


    // =====================================================
    // ENFOCAR MONTO RECIBIDO
    // =====================================================

    const checkoutModal =
        document.getElementById('checkoutModal');

    if (checkoutModal && receivedInput) {

        checkoutModal.addEventListener(
            'shown.bs.modal',
            function () {

                if (paymentMethod &&
                    paymentMethod.value === 'cash') {

                    receivedInput.focus();
                    receivedInput.select();
                }
            }
        );
    }

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const driverForm = document.getElementById('driverForm');
    const driverSelect = document.getElementById('driver_id');
    const updateDriverBtn = document.getElementById('updateDriverBtn');
    const driverUpdateMessage = document.getElementById('driverUpdateMessage');

    if (!driverForm) return;

    driverForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        updateDriverBtn.disabled = true;
        updateDriverBtn.innerText = 'Actualizando...';

        driverUpdateMessage.style.display = 'none';

        try {

            const response = await fetch(driverForm.action, {
                method: 'POST',
                body: new FormData(driverForm),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'No se pudo actualizar el delivery.'
                );
            }

            driverUpdateMessage.className =
                'mt-2 alert alert-success py-2 mb-0';

            driverUpdateMessage.innerText =
                'Delivery actualizado: ' +
                (data.driver_name || 'Sin asignar');

            driverUpdateMessage.style.display = 'block';

        } catch (error) {

            console.error(error);

            driverUpdateMessage.className =
                'mt-2 alert alert-danger py-2 mb-0';

            driverUpdateMessage.innerText =
                error.message || 'Error al actualizar el delivery.';

            driverUpdateMessage.style.display = 'block';

        } finally {

            updateDriverBtn.disabled = false;
            updateDriverBtn.innerText = 'Actualizar';

        }

    });

});
</script>

@endsection







