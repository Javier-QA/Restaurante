@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-0"><i class="bi bi-arrows-angle-expand me-2"></i> Dividir Cuenta</h2>
                    <p class="text-muted mb-0">Selecciona los items que deseas cobrar por separado</p>
                </div>
                <a href="{{ route('pos.order', $order->table_id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header text-white py-3 split-table-header">
                    <h5 class="mb-0 fw-bold">Mesa: {{ $order->table->name ?? 'Mesa' }} - Orden #{{ $order->id }}</h5>
                </div>
                
                <form action="{{ route('pos.split', $order->id) }}" method="POST" id="splitPaymentForm">
                    @csrf
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 50px;">
                                            <input type="checkbox" class="form-check-input" id="checkAll" onclick="toggleAll(this)">
                                        </th>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $detail)
                                    <tr>
                                        <td class="ps-4">
                                            <input type="checkbox" name="selected_items[]" value="{{ $detail->id }}" class="form-check-input item-check" data-price="{{ $detail->price * $detail->quantity }}" onchange="calculateSplitTotal()">
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $detail->product->name }}</div>
                                            @if($detail->note) <small class="text-muted">{{ $detail->note }}</small> @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $detail->quantity }}</span>
                                        </td>
                                        <td class="text-end pe-4 fw-bold">
                                            {{ number_format($detail->price * $detail->quantity, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light p-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="mb-3">
    <label class="form-label fw-bold">
        Tipo de comprobante
    </label>

    <div class="row g-2">

        <div class="col-4">
            <input type="radio"
                   class="btn-check"
                   name="document_type"
                   id="splitTicket"
                   value="Ticket"
                   checked>

            <label class="btn split-doc-btn split-doc-ticket w-100 fw-bold"
                   for="splitTicket">
                Ticket
            </label>
        </div>

        <div class="col-4">
            <input type="radio"
                   class="btn-check"
                   name="document_type"
                   id="splitBoleta"
                   value="Boleta">

            <label class="btn split-doc-btn split-doc-boleta w-100 fw-bold"
                   for="splitBoleta">
                Boleta
            </label>
        </div>

        <div class="col-4">
            <input type="radio"
                   class="btn-check"
                   name="document_type"
                   id="splitFactura"
                   value="Factura">

            <label class="btn split-doc-btn split-doc-factura w-100 fw-bold"
                   for="splitFactura">
                Factura
            </label>
        </div>

    </div>
</div>

<div id="splitClientLookupData" class="d-none">
    @foreach($clients as $client)
        <span
            class="split-client-option"
            data-id="{{ $client->id }}"
            data-name="{{ $client->name }}"
            data-document="{{ $client->document_number }}">
        </span>
    @endforeach
</div>
<div id="splitClientData" class="mb-3" style="display:none;">

    <div class="mb-2">
        <label class="form-label small fw-bold">
            Cliente / Razón social
        </label>

        <input type="text"
               name="client_name"
               id="splitClientName"
               class="form-control"
               placeholder="Nombre o razón social">
    </div>

    <div>
        <label class="form-label small fw-bold">
            DNI / RUC
        </label>

        <input type="text"
               name="client_document"
               id="splitClientDocument" oninput="lookupSplitClientByDocument()" oninput="lookupSplitClientByDocument()"
               class="form-control"
               maxlength="11"
               inputmode="numeric"
               placeholder="DNI / RUC">
    </div>

</div>
<label class="form-label fw-bold">Método de Pago para esta parte:</label>
                                <div class="row g-2">

    <div class="col-6">
        <input type="radio"
               class="btn-check"
               name="payment_method"
               id="splitCash"
               value="cash"
               checked>
        <label class="btn split-payment-btn split-payment-cash w-100 fw-bold"
               for="splitCash">
            <i class="bi bi-cash-coin me-1"></i> Efectivo
        </label>
    </div>

    <div class="col-6">
        <input type="radio"
               class="btn-check"
               name="payment_method"
               id="splitCard"
               value="card">
        <label class="btn split-payment-btn split-payment-card w-100 fw-bold"
               for="splitCard">
            <i class="bi bi-credit-card me-1"></i> Tarjeta
        </label>
    </div>

    <div class="col-6">
        <input type="radio"
               class="btn-check"
               name="payment_method"
               id="splitYape"
               value="yape">
        <label class="btn split-payment-btn split-payment-yape w-100 fw-bold"
               for="splitYape">
            <i class="bi bi-phone me-1"></i> Yape
        </label>
    </div>

    <div class="col-6">
        <input type="radio"
               class="btn-check"
               name="payment_method"
               id="splitPlin"
               value="plin">
        <label class="btn split-payment-btn split-payment-plin w-100 fw-bold"
               for="splitPlin">
            <i class="bi bi-phone-vibrate me-1"></i> Plin
        </label>
    </div>

</div>
                            </div>
                            <div id="splitYapeQr" class="split-qr-box split-yape-box mt-3" style="display:none;">

    <div class="split-qr-title">
        <i class="bi bi-phone me-1"></i>
        Paga con Yape
    </div>

    @if(!empty($yapeQr))
        <img src="{{ asset('storage/' . $yapeQr) }}"
             alt="QR Yape"
             class="split-qr-image">
    @else
        <div class="split-qr-empty">
            <i class="bi bi-qr-code"></i>
            <span>QR de Yape no configurado</span>
        </div>
    @endif

    <div class="split-qr-amount">
        <span>Monto a pagar</span>
        <strong id="splitYapeAmount">
            {{ $currency ?? 'S/' }}0.00
        </strong>
    </div>

</div>

<div id="splitPlinQr" class="split-qr-box split-plin-box mt-3" style="display:none;">

    <div class="split-qr-title">
        <i class="bi bi-phone-vibrate me-1"></i>
        Paga con Plin
    </div>

    @if(!empty($plinQr))
        <img src="{{ asset('storage/' . $plinQr) }}"
             alt="QR Plin"
             class="split-qr-image">
    @else
        <div class="split-qr-empty">
            <i class="bi bi-qr-code"></i>
            <span>QR de Plin no configurado</span>
        </div>
    @endif

    <div class="split-qr-amount">
        <span>Monto a pagar</span>
        <strong id="splitPlinAmount">
            {{ $currency ?? 'S/' }}0.00
        </strong>
    </div>

</div>
<div id="splitCardAmount" class="split-card-amount mt-3" style="display:none;">
    <span class="small fw-bold">
        Monto a pagar
    </span>

    <strong id="splitCardAmountValue">
        S/0.00
    </strong>
</div>
<div id="splitCashGroup" class="split-cash-group mt-3">

    <label class="form-label fw-bold mb-1">
        Recibido
    </label>

    <input type="number"
           step="0.01"
           min="0"
           name="received_amount"
           id="splitReceivedAmount"
           class="form-control text-center fw-bold fs-5"
           value="0.00"
           oninput="calculateSplitChange()"
           onclick="this.select()">

    <div class="d-flex justify-content-between align-items-center mt-2">
        <span class="fw-bold small">Cambio:</span>

        <strong id="splitChangeAmount" class="fs-5">
            0.00
        </strong>
    </div>

</div>
<div id="splitTotalDisplay" class="d-none">0.00</div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-primary btn-lg fw-bold" id="btnSplit" onclick="openSplitConfirm()" disabled>
                                <i class="bi bi-check-circle-fill me-2"></i> Cobrar Selección
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="splitConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header split-confirm-header">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-shield-check me-2"></i>
                    Confirmar pago
                </h6>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body text-center p-4">

                <div class="split-confirm-icon mb-3">
                    <i class="bi bi-cash-coin"></i>
                </div>

                <div class="text-muted small mb-1">
                    Estás por cobrar esta parte de la cuenta
                </div>

                <div class="fw-bold fs-3 split-confirm-total"
                     id="splitConfirmTotal">
                    {{ $currency ?? 'S/' }}0.00
                </div>

                <div class="mt-3">
                    <span class="text-muted small">Método:</span>

                    <span class="fw-bold ms-1"
                          id="splitConfirmMethod">
                        Efectivo
                    </span>
                </div>

            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4">

                <button type="button"
                        class="btn btn-light border flex-fill fw-bold"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button"
                        class="btn split-confirm-pay-btn flex-fill fw-bold"
                        onclick="submitSplitPayment()">
                    Confirmar
                </button>

            </div>

        </div>
    </div>
</div>
<script>
    function openSplitConfirm() {

    const selected =
        document.querySelectorAll('.item-check:checked');

    if (selected.length === 0) {
        return;
    }

    const payment =
        document.querySelector('input[name="payment_method"]:checked');

    const methodNames = {
        cash: 'Efectivo',
        card: 'Tarjeta',
        yape: 'Yape',
        plin: 'Plin'
    };

    const method =
        payment
            ? (methodNames[payment.value] || payment.value)
            : 'Método de pago';

    const total =
        document.getElementById('splitTotalDisplay')
            ? document.getElementById('splitTotalDisplay').innerText
            : '0.00';

    document.getElementById('splitConfirmTotal').innerText =
        '{{ $currency ?? "S/" }}' + total;

    document.getElementById('splitConfirmMethod').innerText =
        method;

    const modal = new bootstrap.Modal(
        document.getElementById('splitConfirmModal')
    );

    modal.show();
}


function submitSplitPayment() {

    const form =
        document.getElementById('splitPaymentForm');

    if (!form) {
        console.error('No se encontró splitPaymentForm');
        return;
    }

    const btn =
        document.querySelector(
            '#splitConfirmModal .split-confirm-pay-btn'
        );

    if (btn) {
        btn.disabled = true;
        btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
    }

    form.submit();
}

function toggleSplitPayment(method) {

    const cashGroup =
        document.getElementById('splitCashGroup');

    const cardAmount =
        document.getElementById('splitCardAmount');

    const yapeQr =
        document.getElementById('splitYapeQr');

    const plinQr =
        document.getElementById('splitPlinQr');

    if (cashGroup) {
        cashGroup.style.display =
            method === 'cash' ? 'block' : 'none';
    }

    if (cardAmount) {
        cardAmount.style.display =
            method === 'card' ? 'flex' : 'none';
    }

    if (yapeQr) {
        yapeQr.style.display =
            method === 'yape' ? 'block' : 'none';
    }

    if (plinQr) {
        plinQr.style.display =
            method === 'plin' ? 'block' : 'none';
    }
}

function calculateSplitChange() {

    const total =
        parseFloat(
            document.getElementById('splitTotalDisplay')?.innerText || 0
        );

    const received =
        parseFloat(
            document.getElementById('splitReceivedAmount')?.value || 0
        );

    const change =
        Math.max(0, received - total);

    const output =
        document.getElementById('splitChangeAmount');

    if (output) {
        output.innerText = change.toFixed(2);
    }
}

document.addEventListener('change', function(event) {

    if (event.target.name === 'payment_method') {
        toggleSplitPayment(event.target.value);
    }

});
function toggleAll(source) {
        checkboxes = document.querySelectorAll('.item-check');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
        calculateSplitTotal();
    }

    function calculateSplitTotal() {
        let total = 0;
        let checks = document.querySelectorAll('.item-check:checked');
        let btn = document.getElementById('btnSplit');

        checks.forEach((checkbox) => {
            total += parseFloat(checkbox.getAttribute('data-price'));
        });

        document.getElementById('splitTotalDisplay').innerText = total.toFixed(2);

        const receivedInput =
            document.getElementById('splitReceivedAmount');

        if (receivedInput) {
            receivedInput.value = total.toFixed(2);
        }

        const cardValue =
            document.getElementById('splitCardAmountValue');

        if (cardValue) {
            cardValue.innerText =
                '{{ $currency ?? "S/" }}' + total.toFixed(2);
        }

        const yapeValue =
            document.getElementById('splitYapeAmount');

        const plinValue =
            document.getElementById('splitPlinAmount');

        if (yapeValue) {
            yapeValue.innerText =
                '{{ $currency ?? "S/" }}' + total.toFixed(2);
        }

        if (plinValue) {
            plinValue.innerText =
                '{{ $currency ?? "S/" }}' + total.toFixed(2);
        }

        calculateSplitChange();
        
        // Habilitar botón solo si hay items seleccionados
        if(total > 0) {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Cobrar ${total.toFixed(2)}`;
        } else {
            btn.disabled = true;
            btn.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> Cobrar Selección`;
        }
    }

document.addEventListener('change', function(event) {

    if (event.target.name === 'document_type') {

        const box =
            document.getElementById('splitClientData');

        if (!box) {
            return;
        }

        box.style.display =
            event.target.value === 'Ticket'
                ? 'none'
                : 'block';
    }

});

document.addEventListener('change', function(event) {

    if (event.target.name !== 'document_type') {
        return;
    }

    const docInput =
        document.getElementById('splitClientDocument');

    if (!docInput) {
        return;
    }

    const value = event.target.value;

    if (value === 'Boleta') {

        docInput.maxLength = 8;
        docInput.placeholder = 'DNI';

        if (docInput.value.length > 8) {
            docInput.value =
                docInput.value.substring(0, 8);
        }

    }
    else if (value === 'Factura') {

        docInput.maxLength = 11;
        docInput.placeholder = 'RUC';

        if (docInput.value.length > 11) {
            docInput.value =
                docInput.value.substring(0, 11);
        }

    }
    else {

        docInput.maxLength = 11;
        docInput.placeholder = 'DNI / RUC';

    }

});

window.lookupSplitClientByDocument = async function() {

    const docInput =
        document.getElementById('splitClientDocument');

    const nameInput =
        document.getElementById('splitClientName');

    const selectedType =
        document.querySelector('input[name="document_type"]:checked');

    if (!docInput || !nameInput || !selectedType) {
        return;
    }

    const docNumber =
        docInput.value.replace(/\D/g, '');

    const requiredLength =
        selectedType.value === 'Factura'
            ? 11
            : selectedType.value === 'Boleta'
                ? 8
                : 0;

    if (!requiredLength || docNumber.length !== requiredLength) {
        return;
    }

    try {

        const response = await fetch(
            `{{ url('/clients/document') }}/${docNumber}`,
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        if (data.found && data.client) {
            nameInput.value = data.client.name || '';
        } else {
            nameInput.value = '';
            nameInput.placeholder =
                selectedType.value === 'Factura'
                    ? 'Razón social no registrada'
                    : 'Cliente no registrado';
        }

    } catch (error) {
        console.error('Error buscando cliente:', error);
    }
};
</script>
@endsection
<style>
.split-payment-btn {
    background: #fff;
    border: 2px solid #dee2e6;
    border-radius: 9px;
    padding: 9px 6px;
    transition: all .18s ease;
}

/* EFECTIVO */
.split-payment-cash {
    color: #198754;
    border-color: #198754;
}

.split-payment-cash:hover,
#splitCash:checked + .split-payment-cash {
    background: #198754 !important;
    border-color: #198754 !important;
    color: #fff !important;
}

/* TARJETA */
.split-payment-card {
    color: #0d6efd;
    border-color: #0d6efd;
}

.split-payment-card:hover,
#splitCard:checked + .split-payment-card {
    background: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #fff !important;
}

/* YAPE */
.split-payment-yape {
    color: #742284;
    border-color: #742284;
}

.split-payment-yape:hover,
#splitYape:checked + .split-payment-yape {
    background: #742284 !important;
    border-color: #742284 !important;
    color: #fff !important;
}

/* PLIN */
.split-payment-plin {
    color: #00a884;
    border-color: #00a884;
}

.split-payment-plin:hover,
#splitPlin:checked + .split-payment-plin {
    background: #00a884 !important;
    border-color: #00a884 !important;
    color: #fff !important;
}
</style>

<style>
/* DIVIDIR CUENTA - COLORES AUN SIN SELECCIONAR */

/* EFECTIVO */
.split-payment-cash {
    background: #edf8f2 !important;
    color: #198754 !important;
    border-color: #198754 !important;
}

.split-payment-cash:hover,
#splitCash:checked + .split-payment-cash {
    background: #198754 !important;
    color: #fff !important;
    border-color: #198754 !important;
}

/* TARJETA */
.split-payment-card {
    background: #eef5ff !important;
    color: #0d6efd !important;
    border-color: #0d6efd !important;
}

.split-payment-card:hover,
#splitCard:checked + .split-payment-card {
    background: #0d6efd !important;
    color: #fff !important;
    border-color: #0d6efd !important;
}

/* YAPE */
.split-payment-yape {
    background: #f6eef8 !important;
    color: #742284 !important;
    border-color: #742284 !important;
}

.split-payment-yape:hover,
#splitYape:checked + .split-payment-yape {
    background: #742284 !important;
    color: #fff !important;
    border-color: #742284 !important;
}

/* PLIN */
.split-payment-plin {
    background: #effaf7 !important;
    color: #00a884 !important;
    border-color: #00a884 !important;
}

.split-payment-plin:hover,
#splitPlin:checked + .split-payment-plin {
    background: #00a884 !important;
    color: #fff !important;
    border-color: #00a884 !important;
}
</style>

<style>
/* =========================================================
   DIVIDIR CUENTA - MISMO ESTILO DE COBRAR VENTA
   ========================================================= */

.split-payment-btn {
    width: 100%;
    min-height: 44px;
    border-width: 2px !important;
    border-style: solid !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    transition:
        background-color .18s ease,
        color .18s ease,
        border-color .18s ease,
        box-shadow .18s ease,
        transform .12s ease !important;
}

/* EFECTIVO */
.split-payment-cash {
    background: #edf8f2 !important;
    color: #198754 !important;
    border-color: #198754 !important;
}

.split-payment-cash:hover,
#splitCash:checked + .split-payment-cash {
    background: #198754 !important;
    color: #fff !important;
    border-color: #198754 !important;
    box-shadow: 0 3px 10px rgba(25,135,84,.22);
}

/* TARJETA */
.split-payment-card {
    background: #eef5ff !important;
    color: #0d6efd !important;
    border-color: #0d6efd !important;
}

.split-payment-card:hover,
#splitCard:checked + .split-payment-card {
    background: #0d6efd !important;
    color: #fff !important;
    border-color: #0d6efd !important;
    box-shadow: 0 3px 10px rgba(13,110,253,.22);
}

/* YAPE */
.split-payment-yape {
    background: #f6eef8 !important;
    color: #742284 !important;
    border-color: #742284 !important;
}

.split-payment-yape:hover,
#splitYape:checked + .split-payment-yape {
    background: #742284 !important;
    color: #fff !important;
    border-color: #742284 !important;
    box-shadow: 0 3px 10px rgba(116,34,132,.22);
}

/* PLIN */
.split-payment-plin {
    background: #effaf7 !important;
    color: #00a884 !important;
    border-color: #00a884 !important;
}

.split-payment-plin:hover,
#splitPlin:checked + .split-payment-plin {
    background: #00a884 !important;
    color: #fff !important;
    border-color: #00a884 !important;
    box-shadow: 0 3px 10px rgba(0,168,132,.22);
}

/* EFECTO AL PRESIONAR */
.split-payment-btn:active {
    transform: scale(.97);
}
</style>

<style>
.split-cash-group {
    padding: 12px;
    background: #edf8f2;
    border: 1px solid #198754;
    border-radius: 12px;
}

.split-cash-group .form-label {
    color: #198754;
}

#splitReceivedAmount {
    border: 2px solid #198754 !important;
    color: #198754 !important;
    background: #fff !important;
    border-radius: 10px;
}

#splitReceivedAmount:focus {
    border-color: #198754 !important;
    box-shadow: 0 0 0 .18rem rgba(25,135,84,.15) !important;
}

#splitChangeAmount {
    color: #198754 !important;
}
</style>

<style>
/* TARJETA - DIVIDIR CUENTA */
.split-card-amount {
    padding: 12px 14px;
    background: #eef5ff;
    border: 1px solid #0d6efd;
    border-radius: 12px;
    align-items: center;
    justify-content: space-between;
}

.split-card-amount span {
    color: #0d6efd;
}

#splitCardAmountValue {
    color: #0d6efd;
    font-size: 1.25rem;
    font-weight: 800;
}
</style>

<style>
.split-qr-box {
    padding: 14px;
    border-radius: 12px;
    text-align: center;
}

.split-yape-box {
    background: #f8effa;
    border: 1px solid #742284;
}

.split-plin-box {
    background: #effaf7;
    border: 1px solid #00a884;
}

.split-qr-title {
    font-weight: 800;
    margin-bottom: 10px;
}

.split-yape-box .split-qr-title {
    color: #742284;
}

.split-plin-box .split-qr-title {
    color: #00a884;
}

.split-qr-image {
    display: block;
    width: 170px;
    height: 170px;
    object-fit: contain;
    margin: 8px auto 12px;
    background: #fff;
    padding: 6px;
    border-radius: 10px;
}

.split-qr-empty {
    padding: 20px 10px;
    color: #6c757d;
}

.split-qr-empty i {
    display: block;
    font-size: 2rem;
    margin-bottom: 5px;
}

.split-qr-amount {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    background: #fff;
    border-radius: 9px;
    font-weight: 700;
}

.split-yape-box .split-qr-amount {
    border: 1px solid #742284;
}

.split-plin-box .split-qr-amount {
    border: 1px solid #00a884;
}

#splitYapeAmount {
    color: #742284;
    font-size: 1.2rem;
    font-weight: 800;
}

#splitPlinAmount {
    color: #00a884;
    font-size: 1.2rem;
    font-weight: 800;
}
</style>

<style>
.split-confirm-header {
    background: #198754;
    color: #fff;
    border-bottom: 0;
}

.split-confirm-icon {
    width: 58px;
    height: 58px;
    margin: auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #edf8f2;
    color: #198754;
    font-size: 1.7rem;
}

.split-confirm-total {
    color: #198754;
}

.split-confirm-pay-btn {
    background: #198754 !important;
    border: 1px solid #198754 !important;
    color: #fff !important;
}

.split-confirm-pay-btn:hover {
    background: #157347 !important;
    border-color: #146c43 !important;
    color: #fff !important;
}
</style>

<style>
/* TARJETA - IGUALAR TEXTO A YAPE Y PLIN */
.split-card-amount span {
    font-size: 1rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
}
</style>

<style>
/* TARJETA - TEXTO IGUAL A YAPE Y PLIN */
#splitCardAmount > span {
    color: #212529 !important;
    font-size: 1rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
}

/* SOLO EL MONTO EN AZUL */
#splitCardAmountValue {
    color: #0d6efd !important;
    font-size: 1.2rem !important;
    font-weight: 800 !important;
}
</style>

<style>
/* TARJETA - MISMA TIPOGRAFIA QUE YAPE/PLIN */
#splitCardAmount {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#splitCardAmount > span {
    color: #212529 !important;
    font-size: 1rem !important;
    font-weight: 700 !important;
    line-height: 1.5 !important;
}

#splitCardAmountValue {
    color: #0d6efd !important;
    font-size: 1.2rem !important;
    font-weight: 800 !important;
    line-height: 1.5 !important;
}
</style>

<style>
/* TARJETA - MISMO ESPACIADO QUE YAPE/PLIN */
#splitCardAmount {
    background: #ffffff !important;
    border: 1px solid #0d6efd !important;
    border-radius: 9px !important;

    padding: 10px 12px !important;

    justify-content: space-between !important;
    align-items: center !important;
}

#splitCardAmount > span {
    color: #212529 !important;
    font-size: 1rem !important;
    font-weight: 700 !important;
    margin: 0 !important;
}

#splitCardAmountValue {
    color: #0d6efd !important;
    font-size: 1.2rem !important;
    font-weight: 800 !important;
    margin: 0 !important;
}
</style>

<style>
.split-table-header {
    background: #ff8c00 !important;
    border-color: #ff8c00 !important;
}
</style>

<style>
.split-doc-btn {
    background: #fff;
    border: 1px solid #dce7f1;
    border-radius: 9px;
    padding: 8px 5px;
    color: #475569;
}

.split-doc-btn:hover {
    border-color: #ff8c00;
    color: #ff8c00;
}

#splitTicket:checked + .split-doc-ticket,
#splitBoleta:checked + .split-doc-boleta,
#splitFactura:checked + .split-doc-factura {
    background: #ff8c00 !important;
    border-color: #ff8c00 !important;
    color: #fff !important;
}
</style>

<style>
/* TIPO DE COMPROBANTE - DIVIDIR CUENTA */
.split-doc-btn {
    background: #ffffff !important;
    border: 2px solid #ff8c00 !important;
    color: #ff8c00 !important;
    border-radius: 9px !important;
    padding: 9px 6px !important;
    font-weight: 700 !important;
    transition: all .18s ease;
}

.split-doc-btn:hover {
    background: #fff7ed !important;
    border-color: #ff8c00 !important;
    color: #ff8c00 !important;
}

#splitTicket:checked + .split-doc-ticket,
#splitBoleta:checked + .split-doc-boleta,
#splitFactura:checked + .split-doc-factura {
    background: #ff8c00 !important;
    border-color: #ff8c00 !important;
    color: #ffffff !important;
}
</style>
