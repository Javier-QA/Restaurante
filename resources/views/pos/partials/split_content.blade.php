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
                                            <input type="checkbox"
                                                   name="selected_items[]"
                                                   value="{{ $detail->id }}"
                                                   class="form-check-input item-check"
                                                   data-detail-id="{{ $detail->id }}"
                                                   data-unit-price="{{ $detail->price }}"
                                                   onchange="toggleSplitItem(this)">
                                        </td>

                                        <td>
                                            <div class="fw-bold">{{ $detail->product->name }}</div>

                                            @if($detail->note)
                                                <small class="text-muted">{{ $detail->note }}</small>
                                            @endif

                                            <small class="d-block text-muted">
                                                Disponible: {{ $detail->quantity }}
                                            </small>
                                        </td>

                                        <td class="text-center">
                                            <div class="input-group input-group-sm flex-nowrap justify-content-center"
                                                 style="max-width: 105px; margin: auto;">

                                                <button type="button"
                                                        class="btn btn-outline-secondary px-2"
                                                        onclick="changeSplitQty({{ $detail->id }}, -1)">
                                                    -
                                                </button>

                                                <input type="text"
                                                       id="splitQty{{ $detail->id }}"
                                                       name="split_quantities[{{ $detail->id }}]"
                                                       class="form-control text-center px-1 fw-bold split-qty"
                                                       value="0"
                                                       data-max="{{ $detail->quantity }}"
                                                       data-price="{{ $detail->price }}"
                                                       readonly>

                                                <button type="button"
                                                        class="btn btn-outline-primary px-2"
                                                        onclick="changeSplitQty({{ $detail->id }}, 1)">
                                                    +
                                                </button>
                                            </div>
                                        </td>

                                        <td class="text-end pe-4 fw-bold">
                                            <span id="splitSubtotal{{ $detail->id }}">
                                                0.00
                                            </span>
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
    const checkboxes = document.querySelectorAll('.item-check');

    checkboxes.forEach((checkbox) => {
        const id = checkbox.dataset.detailId;
        const qtyInput = document.getElementById('splitQty' + id);

        checkbox.checked = source.checked;

        if (qtyInput) {
            qtyInput.value = source.checked
                ? parseInt(qtyInput.dataset.max)
                : 0;
        }

        updateSplitSubtotal(id);
    });

    calculateSplitTotal();
}

function toggleSplitItem(checkbox) {
    const id = checkbox.dataset.detailId;
    const qtyInput = document.getElementById('splitQty' + id);

    if (!qtyInput) return;

    if (checkbox.checked) {
        if (parseInt(qtyInput.value) === 0) {
            qtyInput.value = 1;
        }
    } else {
        qtyInput.value = 0;
    }

    updateSplitSubtotal(id);
    calculateSplitTotal();
}

function changeSplitQty(id, change) {
    const qtyInput = document.getElementById('splitQty' + id);
    const checkbox = document.querySelector(
        '.item-check[data-detail-id="' + id + '"]'
    );

    if (!qtyInput || !checkbox) return;

    const max = parseInt(qtyInput.dataset.max);
    let qty = parseInt(qtyInput.value) || 0;

    qty += change;

    if (qty < 0) qty = 0;
    if (qty > max) qty = max;

    qtyInput.value = qty;
    checkbox.checked = qty > 0;

    updateSplitSubtotal(id);
    calculateSplitTotal();
}

function updateSplitSubtotal(id) {
    const qtyInput = document.getElementById('splitQty' + id);
    const subtotal = document.getElementById('splitSubtotal' + id);

    if (!qtyInput || !subtotal) return;

    const qty = parseInt(qtyInput.value) || 0;
    const price = parseFloat(qtyInput.dataset.price) || 0;

    subtotal.innerText = (qty * price).toFixed(2);
}
function calculateSplitTotal() {
        let total = 0;
        let checks = document.querySelectorAll('.item-check:checked');
        let btn = document.getElementById('btnSplit');

        checks.forEach((checkbox) => {
            const id = checkbox.dataset.detailId;
            const qtyInput = document.getElementById('splitQty' + id);

            if (qtyInput) {
                const qty = parseInt(qtyInput.value) || 0;
                const price = parseFloat(qtyInput.dataset.price) || 0;

                total += qty * price;
            }
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

<style>
/* DIVIDIR CUENTA - MODO OSCURO DEFINITIVO */

/* Contenedor inferior */
html[data-color-mode="dark"] .card-footer.bg-light {
    background: #132338 !important;
    border-color: #30465d !important;
    color: #ffffff !important;
}

/* Etiquetas */
html[data-color-mode="dark"] .card-footer.bg-light .form-label {
    color: #dbe7f3 !important;
}

/* =====================================
   COMPROBANTES
   ===================================== */

html[data-color-mode="dark"] .split-doc-btn {
    background: #182b40 !important;
    border: 1px solid #466078 !important;
    color: #dbe7f3 !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .split-doc-btn:hover {
    border-color: #ff8c00 !important;
    color: #ffb04a !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-doc-ticket {
    background: rgba(255, 140, 0, .18) !important;
    border-color: #ff8c00 !important;
    color: #ffb04a !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-doc-boleta {
    background: rgba(2, 132, 199, .18) !important;
    border-color: #38bdf8 !important;
    color: #7dd3fc !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-doc-factura {
    background: rgba(34, 197, 94, .18) !important;
    border-color: #22c55e !important;
    color: #4ade80 !important;
}


/* =====================================
   MÉTODOS DE PAGO
   ===================================== */

html[data-color-mode="dark"] .split-payment-btn {
    background: #182b40 !important;
    border: 1px solid #466078 !important;
    color: #dbe7f3 !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-payment-cash {
    background: rgba(22, 163, 74, .16) !important;
    border-color: #22c55e !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-payment-card {
    background: rgba(59, 130, 246, .16) !important;
    border-color: #60a5fa !important;
    color: #93c5fd !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-payment-yape {
    background: rgba(132, 56, 255, .18) !important;
    border-color: #a78bfa !important;
    color: #c4b5fd !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-payment-plin {
    background: rgba(6, 182, 212, .16) !important;
    border-color: #22d3ee !important;
    color: #67e8f9 !important;
}


/* =====================================
   INPUTS
   ===================================== */

html[data-color-mode="dark"] .card-footer.bg-light .form-control {
    background: #0f1f32 !important;
    border: 1px solid #40566e !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .card-footer.bg-light .form-control::placeholder {
    color: #8196aa !important;
}

html[data-color-mode="dark"] .card-footer.bg-light .form-control:focus {
    background: #10243a !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 .2rem rgba(59,130,246,.12) !important;
}


/* =====================================
   RECIBIDO / CAMBIO
   ===================================== */

html[data-color-mode="dark"] .split-cash-group {
    background: #182b40 !important;
    border: 1px solid #30465d !important;
    border-radius: 10px !important;
    padding: 12px !important;
}

html[data-color-mode="dark"] .split-cash-group .fw-bold,
html[data-color-mode="dark"] .split-cash-group label {
    color: #dbe7f3 !important;
}

html[data-color-mode="dark"] #splitChangeAmount {
    color: #4ade80 !important;
}


/* =====================================
   MONTOS DE TARJETA / QR
   ===================================== */

html[data-color-mode="dark"] .split-card-amount,
html[data-color-mode="dark"] .split-qr-box {
    background: #182b40 !important;
    border-color: #30465d !important;
    color: #dbe7f3 !important;
}

html[data-color-mode="dark"] .split-card-amount strong,
html[data-color-mode="dark"] .split-qr-amount strong {
    color: #ffffff !important;
}


/* =====================================
   COBRAR SELECCIÓN
   ===================================== */

html[data-color-mode="dark"] #btnSplit {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] #btnSplit:disabled {
    background: #34465a !important;
    border-color: #465a70 !important;
    color: #91a2b3 !important;
    opacity: 1 !important;
}


/* =====================================
   TEXTOS GENERALES
   ===================================== */

html[data-color-mode="dark"] .card-footer.bg-light .text-muted {
    color: #8fa6bd !important;
}

</style>


<style>
/* DIVIDIR CUENTA - COLORES DE PAGO Y RECIBIDO V2 */

/* =====================================================
   MÉTODOS DE PAGO
   Cada método conserva su color incluso sin seleccionar
   ===================================================== */

html[data-color-mode="dark"] .split-payment-cash {
    background: rgba(22, 163, 74, .18) !important;
    border: 1px solid #22c55e !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .split-payment-card {
    background: rgba(59, 130, 246, .18) !important;
    border: 1px solid #60a5fa !important;
    color: #93c5fd !important;
}

html[data-color-mode="dark"] .split-payment-yape {
    background: rgba(168, 85, 247, .18) !important;
    border: 1px solid #c084fc !important;
    color: #d8b4fe !important;
}

html[data-color-mode="dark"] .split-payment-plin {
    background: rgba(20, 184, 166, .18) !important;
    border: 1px solid #2dd4bf !important;
    color: #5eead4 !important;
}


/* =====================================================
   MÉTODO SELECCIONADO
   Un poco más intenso
   ===================================================== */

html[data-color-mode="dark"] .btn-check:checked + .split-payment-cash {
    background: #168c50 !important;
    border-color: #22c55e !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-payment-card {
    background: #2563eb !important;
    border-color: #60a5fa !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-payment-yape {
    background: #9333ea !important;
    border-color: #c084fc !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .btn-check:checked + .split-payment-plin {
    background: #0f9f91 !important;
    border-color: #2dd4bf !important;
    color: #ffffff !important;
}


/* =====================================================
   RECIBIDO
   ===================================================== */

html[data-color-mode="dark"] #splitCashGroup {
    background: #182b40 !important;
    border: 1px solid #38516a !important;
    border-radius: 10px !important;
    padding: 12px !important;
}

html[data-color-mode="dark"] #splitCashGroup label,
html[data-color-mode="dark"] #splitCashGroup span {
    color: #dbe7f3 !important;
}


/* El input 0.00 NO debe quedar blanco */

html[data-color-mode="dark"] #splitReceivedAmount,
html[data-color-mode="dark"] input#splitReceivedAmount,
html[data-color-mode="dark"] #splitCashGroup input[type="number"] {
    background: #0d1b2b !important;
    background-color: #0d1b2b !important;
    color: #ffffff !important;
    border: 2px solid #22a05a !important;
    border-radius: 10px !important;
    box-shadow: none !important;
    -webkit-text-fill-color: #ffffff !important;
}

html[data-color-mode="dark"] #splitReceivedAmount:focus,
html[data-color-mode="dark"] input#splitReceivedAmount:focus {
    background: #102238 !important;
    background-color: #102238 !important;
    color: #ffffff !important;
    border-color: #22c55e !important;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, .12) !important;
}


/* =====================================================
   CAMBIO
   ===================================================== */

html[data-color-mode="dark"] #splitChangeAmount {
    color: #22c55e !important;
    -webkit-text-fill-color: #22c55e !important;
}


/* =====================================================
   EVITAR QUE BOOTSTRAP .bg-light VUELVA A PINTAR BLANCO
   ===================================================== */

html[data-color-mode="dark"] #splitCashGroup,
html[data-color-mode="dark"] #splitCashGroup * {
    --bs-bg-opacity: 1;
}

</style>


<style>
/* DIVIDIR CUENTA - MONTOS A PAGAR V2 */

/* =========================================
   TARJETA - MONTO A PAGAR
   ========================================= */

html[data-color-mode="dark"] #splitCardAmount {
    display: none;
    background: #182b40 !important;
    background-color: #182b40 !important;
    border: 1px solid #38516a !important;
    border-radius: 10px !important;
    color: #dbe7f3 !important;
    padding: 12px 14px !important;
}

html[data-color-mode="dark"] #splitCardAmount span {
    color: #9db2c7 !important;
}

html[data-color-mode="dark"] #splitCardAmount strong {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}


/* =========================================
   YAPE - MONTO A PAGAR
   ========================================= */

html[data-color-mode="dark"] #splitYapeQr {
    background: #182b40 !important;
    background-color: #182b40 !important;
    border-color: #a78bfa !important;
    color: #dbe7f3 !important;
}

html[data-color-mode="dark"] #splitYapeQr .split-qr-title {
    color: #c4b5fd !important;
}

html[data-color-mode="dark"] #splitYapeQr .split-qr-amount {
    background: #0d1b2b !important;
    border: 1px solid #6d28d9 !important;
    color: #dbe7f3 !important;
}

html[data-color-mode="dark"] #splitYapeQr .split-qr-amount span {
    color: #b8a8d8 !important;
}

html[data-color-mode="dark"] #splitYapeQr .split-qr-amount strong {
    color: #d8b4fe !important;
    -webkit-text-fill-color: #d8b4fe !important;
}


/* =========================================
   PLIN - MONTO A PAGAR
   ========================================= */

html[data-color-mode="dark"] #splitPlinQr {
    background: #182b40 !important;
    background-color: #182b40 !important;
    border-color: #2dd4bf !important;
    color: #dbe7f3 !important;
}

html[data-color-mode="dark"] #splitPlinQr .split-qr-title {
    color: #5eead4 !important;
}

html[data-color-mode="dark"] #splitPlinQr .split-qr-amount {
    background: #0d1b2b !important;
    border: 1px solid #0f9f91 !important;
    color: #dbe7f3 !important;
}

html[data-color-mode="dark"] #splitPlinQr .split-qr-amount span {
    color: #9bc9c4 !important;
}

html[data-color-mode="dark"] #splitPlinQr .split-qr-amount strong {
    color: #5eead4 !important;
    -webkit-text-fill-color: #5eead4 !important;
}


/* =========================================
   EVITAR FONDOS BLANCOS INTERNOS
   ========================================= */

html[data-color-mode="dark"] #splitCardAmount *,
html[data-color-mode="dark"] #splitYapeQr *,
html[data-color-mode="dark"] #splitPlinQr * {
    box-shadow: none;
}

</style>

@endsection
<style>
.split-payment-btn {
    background: #ffffff;
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
    background: #ffffff !important;
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
    background: #ffffff;
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
    background: #ffffff;
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
    background: #ffffff;
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
