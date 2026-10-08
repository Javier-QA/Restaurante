@extends('layouts.app')

@section('content')
<div class="container-fluid split-page">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <div class="d-flex justify-content-between align-items-center split-page-header">
                <div>
                    <h2 class="split-page-title"><i class="bi bi-arrows-angle-expand me-2"></i> Dividir Cuenta</h2>
                    <p class="split-page-subtitle">Selecciona los items que deseas cobrar por separado</p>
                </div>
                <a href="{{ route('pos.order', $order->table_id) }}" class="btn btn-outline-secondary split-back-btn">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card split-main-card">
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

                    <div class="card-footer split-payment-panel">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="mb-3">
    <label class="form-label split-section-label">
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
               id="splitClientDocument" oninput="lookupSplitClientByDocument()"
               class="form-control"
               maxlength="11"
               inputmode="numeric"
               placeholder="DNI / RUC">
    </div>

</div>
<label class="form-label split-section-label">Método de Pago para esta parte:</label>
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

        const gross = @json((float) $order->details->sum(fn ($line) => $line->price * $line->quantity));
        const ratio = gross > 0 ? total / gross : 1;
        const discount = Math.round(@json((float) $order->discount) * ratio * 100) / 100;
        const tip = Math.round(@json((float) $order->tip) * ratio * 100) / 100;
        total = Math.round(Math.max(0, total - discount + tip) * 100) / 100;
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
/* =========================================================
   DIVIDIR CUENTA
   Diseño unificado con el sistema
   ========================================================= */

.split-page {
    --split-navy: #0b2e4f;
    --split-navy-hover: #08253f;
    --split-orange: #f49114;
    --split-green: #168a5b;
    --split-border: #e2e8f0;
    --split-muted: #64748b;
    --split-surface: #ffffff;
    --split-soft: #f8fafc;
}

/* CONTENEDOR GENERAL */
.split-page {
    max-width: 1180px;
    margin: 0 auto;
}

.split-page-header {
    margin-bottom: 1.35rem;
}

.split-page-title {
    font-size: 1.55rem;
    font-weight: 800;
    letter-spacing: -.025em;
    color: #17212b;
    margin: 0;
}

.split-page-title i {
    color: var(--split-orange);
}

.split-page-subtitle {
    margin: .3rem 0 0;
    color: var(--split-muted);
    font-size: .9rem;
}

/* TARJETA PRINCIPAL */
.split-main-card {
    background: var(--split-surface);
    border: 1px solid var(--split-border) !important;
    border-radius: 16px !important;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(15, 23, 42, .06) !important;
}

/* ENCABEZADO DE LA ORDEN */
.split-table-header {
    background: var(--split-navy) !important;
    border: 0 !important;
    padding: 1rem 1.25rem !important;
}

.split-table-header h5 {
    font-size: 1rem;
    letter-spacing: -.01em;
}

/* TABLA */
.split-page .table {
    --bs-table-bg: transparent;
    margin: 0;
}

.split-page .table thead th {
    background: #f8fafc !important;
    color: #64748b;
    border-bottom: 1px solid var(--split-border);
    padding-top: .8rem;
    padding-bottom: .8rem;
    font-size: .73rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .045em;
}

.split-page .table tbody td {
    padding-top: .95rem;
    padding-bottom: .95rem;
    border-color: #edf1f5;
    vertical-align: middle;
}

.split-page .table tbody tr {
    transition: background-color .15s ease;
}

.split-page .table tbody tr:hover {
    background: #fbfcfd;
}

.split-page .form-check-input {
    width: 1.15rem;
    height: 1.15rem;
    cursor: pointer;
}

.split-page .form-check-input:checked {
    background-color: var(--split-navy);
    border-color: var(--split-navy);
}

/* CONTROL DE CANTIDAD */
.split-page .split-qty {
    min-width: 42px;
    border-color: #d9e1e8;
    background: #fff;
}

.split-page .input-group .btn {
    min-width: 34px;
    border-color: #d9e1e8;
    font-weight: 800;
}

.split-page .input-group .btn-outline-primary {
    color: var(--split-navy);
}

.split-page .input-group .btn-outline-primary:hover {
    background: var(--split-navy);
    border-color: var(--split-navy);
    color: #fff;
}

/* ZONA DE COBRO */
.split-payment-panel {
    background: #f8fafc !important;
    border-top: 1px solid var(--split-border) !important;
    padding: 1.4rem !important;
}

.split-section-label {
    color: #334155;
    font-size: .82rem;
    font-weight: 800;
    margin-bottom: .55rem;
}

/* COMPROBANTES */
.split-doc-btn {
    min-height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff !important;
    border: 1px solid #d9e2ea !important;
    border-radius: 10px !important;
    color: #475569 !important;
    font-size: .84rem;
    transition: all .15s ease;
}

.split-doc-btn:hover {
    border-color: var(--split-orange) !important;
    color: var(--split-orange) !important;
}

#splitTicket:checked + .split-doc-ticket,
#splitBoleta:checked + .split-doc-boleta,
#splitFactura:checked + .split-doc-factura {
    background: var(--split-orange) !important;
    border-color: var(--split-orange) !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(244,145,20,.18);
}

/* DATOS DEL CLIENTE */
#splitClientData {
    background: #fff;
    border: 1px solid var(--split-border);
    border-radius: 12px;
    padding: 1rem;
}

.split-page .form-control {
    min-height: 42px;
    border: 1px solid #d9e2ea;
    border-radius: 10px;
    color: #17212b;
}

.split-page .form-control:focus {
    border-color: var(--split-navy);
    box-shadow: 0 0 0 .2rem rgba(11,46,79,.08);
}

/* MÉTODOS DE PAGO */
.split-payment-btn {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .25rem;
    border-radius: 10px !important;
    border: 1px solid !important;
    font-size: .84rem;
    transition: all .15s ease;
}

.split-payment-cash {
    background: #f1faf5 !important;
    border-color: #a9d9c1 !important;
    color: #168a5b !important;
}

.split-payment-card {
    background: #f2f7ff !important;
    border-color: #b9d2fa !important;
    color: #2563a9 !important;
}

.split-payment-yape {
    background: #faf5ff !important;
    border-color: #d9c0ec !important;
    color: #742284 !important;
}

.split-payment-plin {
    background: #f0fbf9 !important;
    border-color: #afe0d7 !important;
    color: #087f70 !important;
}

#splitCash:checked + .split-payment-cash {
    background: #168a5b !important;
    border-color: #168a5b !important;
    color: #fff !important;
}

#splitCard:checked + .split-payment-card {
    background: #2563a9 !important;
    border-color: #2563a9 !important;
    color: #fff !important;
}

#splitYape:checked + .split-payment-yape {
    background: #742284 !important;
    border-color: #742284 !important;
    color: #fff !important;
}

#splitPlin:checked + .split-payment-plin {
    background: #087f70 !important;
    border-color: #087f70 !important;
    color: #fff !important;
}

/* EFECTIVO */
.split-cash-group {
    background: #fff;
    border: 1px solid #cfe7da;
    border-radius: 12px;
    padding: 1rem;
}

#splitReceivedAmount {
    background: #fff !important;
    border-color: #a9d9c1 !important;
    font-size: 1.05rem !important;
    color: #17212b !important;
}

#splitReceivedAmount:focus {
    border-color: var(--split-green) !important;
    box-shadow: 0 0 0 .2rem rgba(22,138,91,.08) !important;
}

#splitChangeAmount {
    color: var(--split-green) !important;
}

/* TARJETA */
.split-card-amount {
    padding: .9rem 1rem;
    background: #fff;
    border: 1px solid #c9daf2;
    border-radius: 12px;
    align-items: center;
    justify-content: space-between;
}

#splitCardAmountValue {
    color: #2563a9;
    font-size: 1.15rem;
    font-weight: 800;
}

/* YAPE / PLIN */
.split-qr-box {
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    background: #fff;
}

.split-yape-box {
    border: 1px solid #d9c0ec;
}

.split-plin-box {
    border: 1px solid #afe0d7;
}

.split-qr-title {
    font-weight: 800;
    margin-bottom: .8rem;
}

.split-yape-box .split-qr-title {
    color: #742284;
}

.split-plin-box .split-qr-title {
    color: #087f70;
}

.split-qr-image {
    display: block;
    width: 155px;
    height: 155px;
    object-fit: contain;
    margin: .5rem auto .8rem;
    padding: 5px;
    background: #fff;
    border-radius: 10px;
}

.split-qr-empty {
    padding: 1.25rem .5rem;
    color: #94a3b8;
}

.split-qr-empty i {
    display: block;
    font-size: 1.8rem;
    margin-bottom: .3rem;
}

.split-qr-amount {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .75rem .85rem;
    border-radius: 9px;
    background: #f8fafc;
    font-weight: 700;
}

#splitYapeAmount {
    color: #742284;
}

#splitPlinAmount {
    color: #087f70;
}

/* BOTÓN PRINCIPAL */
#btnSplit {
    min-height: 48px;
    background: var(--split-navy) !important;
    border-color: var(--split-navy) !important;
    border-radius: 11px;
    font-size: .92rem;
    box-shadow: 0 5px 14px rgba(11,46,79,.14);
}

#btnSplit:hover:not(:disabled) {
    background: var(--split-navy-hover) !important;
    border-color: var(--split-navy-hover) !important;
    transform: translateY(-1px);
}

#btnSplit:disabled {
    background: #cbd5e1 !important;
    border-color: #cbd5e1 !important;
    color: #fff !important;
    box-shadow: none;
}

/* VOLVER */
.split-back-btn {
    min-height: 40px;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border-radius: 10px;
    border-color: #d7e0e8;
    color: #475569;
    font-weight: 700;
}

.split-back-btn:hover {
    background: #fff;
    color: var(--split-navy);
    border-color: var(--split-navy);
}

/* MODAL DE CONFIRMACIÓN */
#splitConfirmModal .modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.split-confirm-header {
    background: var(--split-navy);
    color: #fff;
    border: 0;
    padding: .9rem 1.1rem;
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
    color: var(--split-green);
    font-size: 1.65rem;
}

.split-confirm-total {
    color: var(--split-navy);
}

.split-confirm-pay-btn {
    background: var(--split-green) !important;
    border-color: var(--split-green) !important;
    color: #fff !important;
    border-radius: 9px;
}

.split-confirm-pay-btn:hover {
    background: #11754c !important;
    border-color: #11754c !important;
}

/* RESPONSIVE */
@media (max-width: 767.98px) {
    .split-page-title {
        font-size: 1.3rem;
    }

    .split-payment-panel {
        padding: 1rem !important;
    }

    .split-page .table tbody td {
        padding-top: .75rem;
        padding-bottom: .75rem;
    }
}

/* =========================================================
   MODO OSCURO
   ========================================================= */

html[data-color-mode="dark"] .split-page {
    --split-surface: #122238;
    --split-soft: #0f1f32;
    --split-border: #2c4258;
    --split-muted: #91a5b9;
}

html[data-color-mode="dark"] .split-page-title {
    color: #f4f7fa;
}

html[data-color-mode="dark"] .split-page-subtitle {
    color: #91a5b9;
}

html[data-color-mode="dark"] .split-main-card {
    background: #122238;
    border-color: #2c4258 !important;
}

html[data-color-mode="dark"] .split-page .table thead th {
    background: #172a40 !important;
    color: #9db0c3;
    border-color: #2c4258;
}

html[data-color-mode="dark"] .split-page .table tbody td {
    color: #e6edf4;
    border-color: #263b50;
}

html[data-color-mode="dark"] .split-page .table tbody tr:hover {
    background: #162a40;
}

html[data-color-mode="dark"] .split-page .text-muted {
    color: #91a5b9 !important;
}

html[data-color-mode="dark"] .split-payment-panel {
    background: #0f1f32 !important;
    border-color: #2c4258 !important;
}

html[data-color-mode="dark"] .split-section-label,
html[data-color-mode="dark"] .split-payment-panel .form-label {
    color: #dce6ef;
}

html[data-color-mode="dark"] #splitClientData,
html[data-color-mode="dark"] .split-cash-group,
html[data-color-mode="dark"] .split-card-amount,
html[data-color-mode="dark"] .split-qr-box {
    background: #15283d;
    border-color: #365069;
    color: #e6edf4;
}

html[data-color-mode="dark"] .split-page .form-control,
html[data-color-mode="dark"] .split-page .split-qty {
    background: #0c1b2c !important;
    border-color: #3b5269 !important;
    color: #fff !important;
}

html[data-color-mode="dark"] .split-page .form-control::placeholder {
    color: #71869a;
}

html[data-color-mode="dark"] .split-doc-btn {
    background: #15283d !important;
    border-color: #3b5269 !important;
    color: #d6e0e9 !important;
}

html[data-color-mode="dark"] .split-qr-amount {
    background: #0c1b2c;
}

html[data-color-mode="dark"] #splitReceivedAmount {
    background: #0c1b2c !important;
    color: #fff !important;
}

html[data-color-mode="dark"] .split-back-btn {
    color: #d6e0e9;
    border-color: #3b5269;
}

html[data-color-mode="dark"] .split-back-btn:hover {
    background: #15283d;
    color: #fff;
}

/* =========================================================
   AJUSTE DE PALETA GLOBAL DEL SISTEMA
   ========================================================= */

/* Título e icono - modo claro */
.split-page-title {
    color: #111827 !important;
}

.split-page-title i {
    color: #111827 !important;
}

/* Subtítulo - modo claro */
.split-page-subtitle {
    color: #4b5563 !important;
}

/* Elementos principales respetan la paleta configurada */
.split-table-header {
    background: var(--primary) !important;
}

#btnSplit {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
}

#btnSplit:hover:not(:disabled) {
    filter: brightness(.90);
    background: var(--primary) !important;
    border-color: var(--primary) !important;
}

/* Checkbox seleccionado */
.split-page .form-check-input:checked {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
}

/* Cantidad + */
.split-page .input-group .btn-outline-primary {
    color: var(--primary) !important;
}

.split-page .input-group .btn-outline-primary:hover {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #fff !important;
}

/* Focus de campos */
.split-page .form-control:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--primary) 12%, transparent) !important;
}

/* =========================================================
   MODO OSCURO
   ========================================================= */

html[data-color-mode="dark"] .split-page-title {
    color: #f8fafc !important;
}

html[data-color-mode="dark"] .split-page-title i {
    color: #f8fafc !important;
}

html[data-color-mode="dark"] .split-page-subtitle {
    color: #aebdca !important;
}

/* =========================================================
   REFINAMIENTO - COMPROBANTES, PAGOS Y BOTÓN VOLVER
   ========================================================= */

/* ---------- TIPO DE COMPROBANTE ---------- */

.split-doc-btn {
    background: #ffffff !important;
    border: 1.5px solid #d6dee8 !important;
    color: #334155 !important;
    box-shadow: none !important;
    font-weight: 700;
}

.split-doc-btn:hover {
    border-color: var(--primary) !important;
    color: var(--primary) !important;
    background: color-mix(in srgb, var(--primary) 4%, #ffffff) !important;
}

/* Comprobante seleccionado = paleta configurada */
#splitTicket:checked + .split-doc-ticket,
#splitBoleta:checked + .split-doc-boleta,
#splitFactura:checked + .split-doc-factura {
    background: color-mix(in srgb, var(--primary) 12%, #ffffff) !important;
    border: 1.5px solid var(--primary) !important;
    color: var(--primary) !important;
    box-shadow: 0 3px 10px color-mix(in srgb, var(--primary) 10%, transparent) !important;
}


/* ---------- MÉTODOS DE PAGO ---------- */

/* Estado normal */
.split-payment-cash {
    background: #ffffff !important;
    border-color: #b9ddcb !important;
    color: #168a5b !important;
}

.split-payment-card {
    background: #ffffff !important;
    border-color: #bfd4f4 !important;
    color: #2563a9 !important;
}

.split-payment-yape {
    background: #ffffff !important;
    border-color: #dcc8e8 !important;
    color: #742284 !important;
}

.split-payment-plin {
    background: #ffffff !important;
    border-color: #b8dfd9 !important;
    color: #087f70 !important;
}


/* Seleccionados: color suave, NO fondo sólido */

#splitCash:checked + .split-payment-cash {
    background: #edf8f2 !important;
    border: 1.5px solid #55ad80 !important;
    color: #13734c !important;
    box-shadow: 0 3px 9px rgba(22, 138, 91, .08) !important;
}

#splitCard:checked + .split-payment-card {
    background: #f0f5fc !important;
    border: 1.5px solid #78a5dc !important;
    color: #245f9f !important;
    box-shadow: 0 3px 9px rgba(37, 99, 169, .07) !important;
}

#splitYape:checked + .split-payment-yape {
    background: #f8f1fa !important;
    border: 1.5px solid #b486c0 !important;
    color: #6d2679 !important;
    box-shadow: 0 3px 9px rgba(116, 34, 132, .07) !important;
}

#splitPlin:checked + .split-payment-plin {
    background: #edf8f6 !important;
    border: 1.5px solid #69b7aa !important;
    color: #087568 !important;
    box-shadow: 0 3px 9px rgba(8, 127, 112, .07) !important;
}


/* ---------- BOTÓN VOLVER ---------- */

.split-back-btn {
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    color: #334155 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
}

.split-back-btn:hover {
    background: color-mix(in srgb, var(--primary) 5%, #ffffff) !important;
    border-color: var(--primary) !important;
    color: var(--primary) !important;
}


/* =========================================================
   MODO OSCURO
   ========================================================= */

/* Comprobantes */
html[data-color-mode="dark"] .split-doc-btn {
    background: #15283d !important;
    border-color: #3a5269 !important;
    color: #dce5ed !important;
}

html[data-color-mode="dark"] .split-doc-btn:hover {
    background: color-mix(in srgb, var(--primary) 12%, #15283d) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] #splitTicket:checked + .split-doc-ticket,
html[data-color-mode="dark"] #splitBoleta:checked + .split-doc-boleta,
html[data-color-mode="dark"] #splitFactura:checked + .split-doc-factura {
    background: color-mix(in srgb, var(--primary) 18%, #15283d) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}


/* Métodos de pago - modo oscuro */

html[data-color-mode="dark"] .split-payment-cash {
    background: #15283d !important;
    border-color: #35654e !important;
    color: #74c99d !important;
}

html[data-color-mode="dark"] .split-payment-card {
    background: #15283d !important;
    border-color: #3c5e83 !important;
    color: #86b6ec !important;
}

html[data-color-mode="dark"] .split-payment-yape {
    background: #15283d !important;
    border-color: #68486f !important;
    color: #c996d3 !important;
}

html[data-color-mode="dark"] .split-payment-plin {
    background: #15283d !important;
    border-color: #356b65 !important;
    color: #79c9bd !important;
}


/* Seleccionados oscuros */

html[data-color-mode="dark"] #splitCash:checked + .split-payment-cash {
    background: #18382d !important;
    border-color: #54a87c !important;
    color: #a4dfc0 !important;
}

html[data-color-mode="dark"] #splitCard:checked + .split-payment-card {
    background: #192f49 !important;
    border-color: #628ec1 !important;
    color: #acd0f5 !important;
}

html[data-color-mode="dark"] #splitYape:checked + .split-payment-yape {
    background: #32243a !important;
    border-color: #9d6aa8 !important;
    color: #dab5e1 !important;
}

html[data-color-mode="dark"] #splitPlin:checked + .split-payment-plin {
    background: #173936 !important;
    border-color: #559c91 !important;
    color: #a0d8d0 !important;
}


/* Volver - modo oscuro */

html[data-color-mode="dark"] .split-back-btn {
    background: #15283d !important;
    border: 1.5px solid #496176 !important;
    color: #e1e8ee !important;
}

html[data-color-mode="dark"] .split-back-btn:hover {
    background: color-mix(in srgb, var(--primary) 15%, #15283d) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
}


/* ===== REFINAMIENTO FINAL DE SELECCIONES ===== */

/* Comprobante seleccionado - paleta del sistema más suave */
#splitTicket:checked + .split-doc-ticket,
#splitBoleta:checked + .split-doc-boleta,
#splitFactura:checked + .split-doc-factura {
    background: color-mix(in srgb, var(--primary) 8%, #ffffff) !important;
    border: 1.5px solid var(--primary) !important;
    color: var(--primary) !important;
    box-shadow: none !important;
}

/* Métodos seleccionados - selección más sutil */
#splitCash:checked + .split-payment-cash {
    background: #f5faf7 !important;
    border-color: #8fc5a9 !important;
    color: #16734d !important;
    box-shadow: none !important;
}

#splitCard:checked + .split-payment-card {
    background: #f6f9fd !important;
    border-color: #9dbce0 !important;
    color: #285f9b !important;
    box-shadow: none !important;
}

#splitYape:checked + .split-payment-yape {
    background: #fbf7fc !important;
    border-color: #c7a7ce !important;
    color: #762985 !important;
    box-shadow: none !important;
}

#splitPlin:checked + .split-payment-plin {
    background: #f5fbfa !important;
    border-color: #8fcac1 !important;
    color: #08786b !important;
    box-shadow: none !important;
}

/* Volver */
.split-back-btn {
    border: 1.5px solid #94a3b8 !important;
}

/* ===== MODO OSCURO ===== */

html[data-color-mode="dark"]
#splitTicket:checked + .split-doc-ticket,
html[data-color-mode="dark"]
#splitBoleta:checked + .split-doc-boleta,
html[data-color-mode="dark"]
#splitFactura:checked + .split-doc-factura {
    background: color-mix(in srgb, var(--primary) 10%, #15283d) !important;
    border-color: var(--primary) !important;
    color: color-mix(in srgb, var(--primary) 70%, #ffffff) !important;
}

/* En oscuro evitamos fondos fuertes */
html[data-color-mode="dark"]
#splitCash:checked + .split-payment-cash {
    background: #183027 !important;
    border-color: #497b62 !important;
    color: #8bc8a8 !important;
}

html[data-color-mode="dark"]
#splitCard:checked + .split-payment-card {
    background: #182b3f !important;
    border-color: #52749a !important;
    color: #94b9df !important;
}

html[data-color-mode="dark"]
#splitYape:checked + .split-payment-yape {
    background: #292232 !important;
    border-color: #795881 !important;
    color: #c397cc !important;
}

html[data-color-mode="dark"]
#splitPlin:checked + .split-payment-plin {
    background: #18312f !important;
    border-color: #497c75 !important;
    color: #8bc6bd !important;
}

html[data-color-mode="dark"] .split-back-btn {
    background: transparent !important;
    border: 1.5px solid #526b82 !important;
    color: #e2e8f0 !important;
}

html[data-color-mode="dark"] .split-back-btn:hover {
    border-color: var(--primary) !important;
    color: var(--primary) !important;
}


/* =========================================================
   AJUSTE VISUAL - PALETA + PAGOS EN MODO OSCURO
   ========================================================= */

/* Encabezado Mesa / Orden siempre usa la paleta configurada */
.split-table-header {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
}

.split-table-header h5,
.split-table-header span,
.split-table-header strong,
.split-table-header i {
    color: #ffffff !important;
}


/* =========================================================
   MÉTODOS DE PAGO - MODO OSCURO
   Colores identificables pero sin saturar demasiado
   ========================================================= */

html[data-color-mode="dark"] .split-payment-cash {
    background: rgba(22, 138, 91, .14) !important;
    border: 1.5px solid #3fa374 !important;
    color: #7ed6a9 !important;
}

html[data-color-mode="dark"] .split-payment-card {
    background: rgba(37, 99, 169, .15) !important;
    border: 1.5px solid #548bc8 !important;
    color: #8fc0f4 !important;
}

html[data-color-mode="dark"] .split-payment-yape {
    background: rgba(116, 34, 132, .16) !important;
    border: 1.5px solid #a35caf !important;
    color: #d39bdd !important;
}

html[data-color-mode="dark"] .split-payment-plin {
    background: rgba(8, 127, 112, .15) !important;
    border: 1.5px solid #39a394 !important;
    color: #7bd5c8 !important;
}


/* SELECCIONADOS - más visibles */

html[data-color-mode="dark"]
#splitCash:checked + .split-payment-cash {
    background: rgba(22, 138, 91, .28) !important;
    border-color: #62c392 !important;
    color: #b1e8cb !important;
    box-shadow: 0 0 0 1px rgba(98,195,146,.15) !important;
}

html[data-color-mode="dark"]
#splitCard:checked + .split-payment-card {
    background: rgba(37, 99, 169, .30) !important;
    border-color: #76a9df !important;
    color: #c2ddf8 !important;
    box-shadow: 0 0 0 1px rgba(118,169,223,.15) !important;
}

html[data-color-mode="dark"]
#splitYape:checked + .split-payment-yape {
    background: rgba(116, 34, 132, .30) !important;
    border-color: #bd7ac7 !important;
    color: #e4bce9 !important;
    box-shadow: 0 0 0 1px rgba(189,122,199,.15) !important;
}

html[data-color-mode="dark"]
#splitPlin:checked + .split-payment-plin {
    background: rgba(8, 127, 112, .28) !important;
    border-color: #59b9ac !important;
    color: #b2e5de !important;
    box-shadow: 0 0 0 1px rgba(89,185,172,.15) !important;
}


/* =========================================================
   COMPROBANTE SELECCIONADO - PALETA DEL SISTEMA
   ========================================================= */

html[data-color-mode="dark"]
#splitTicket:checked + .split-doc-ticket,
html[data-color-mode="dark"]
#splitBoleta:checked + .split-doc-boleta,
html[data-color-mode="dark"]
#splitFactura:checked + .split-doc-factura {
    background:
        color-mix(in srgb, var(--primary) 20%, #15283d) !important;

    border: 1.5px solid var(--primary) !important;

    color:
        color-mix(in srgb, var(--primary) 65%, #ffffff) !important;
}


/* =========================================================
   VOLVER - MODO OSCURO
   ========================================================= */

html[data-color-mode="dark"] .split-back-btn {
    background: transparent !important;
    border: 1.5px solid #60778d !important;
    color: #e5edf4 !important;
}

html[data-color-mode="dark"] .split-back-btn:hover {
    background:
        color-mix(in srgb, var(--primary) 12%, transparent) !important;

    border-color: var(--primary) !important;
    color: var(--primary) !important;
}


/* =========================================================
   PAGOS - CONTRASTE DEFINITIVO EN MODO OSCURO
   ========================================================= */

/* NO SELECCIONADOS */
html[data-color-mode="dark"] .split-payment-cash {
    background: #14283a !important;
    border: 1.5px solid #29966a !important;
    color: #65c99a !important;
}

html[data-color-mode="dark"] .split-payment-card {
    background: #14283a !important;
    border: 1.5px solid #4c83bd !important;
    color: #7db2e8 !important;
}

html[data-color-mode="dark"] .split-payment-yape {
    background: #14283a !important;
    border: 1.5px solid #9854a6 !important;
    color: #c780d2 !important;
}

html[data-color-mode="dark"] .split-payment-plin {
    background: #14283a !important;
    border: 1.5px solid #299b8e !important;
    color: #67c9bd !important;
}


/* EFECTIVO SELECCIONADO */
html[data-color-mode="dark"]
#splitCash:checked + .split-payment-cash {
    background: #1d503b !important;
    border: 2px solid #5fd095 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(95, 208, 149, .12) !important;
}

/* TARJETA SELECCIONADA */
html[data-color-mode="dark"]
#splitCard:checked + .split-payment-card {
    background: #234e7b !important;
    border: 2px solid #78b5f0 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(120, 181, 240, .12) !important;
}

/* YAPE SELECCIONADO */
html[data-color-mode="dark"]
#splitYape:checked + .split-payment-yape {
    background: #583063 !important;
    border: 2px solid #cf83da !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(207, 131, 218, .12) !important;
}

/* PLIN SELECCIONADO */
html[data-color-mode="dark"]
#splitPlin:checked + .split-payment-plin {
    background: #18564f !important;
    border: 2px solid #64cfc0 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(100, 207, 192, .12) !important;
}


/* =========================================================
   COMPROBANTE - SELECCIÓN MÁS CLARA EN OSCURO
   ========================================================= */

html[data-color-mode="dark"]
#splitTicket:checked + .split-doc-ticket,
html[data-color-mode="dark"]
#splitBoleta:checked + .split-doc-boleta,
html[data-color-mode="dark"]
#splitFactura:checked + .split-doc-factura {
    background:
        color-mix(in srgb, var(--primary) 28%, #15283d) !important;

    border: 2px solid var(--primary) !important;

    color:
        color-mix(in srgb, var(--primary) 65%, #ffffff) !important;

    box-shadow:
        0 0 0 2px
        color-mix(in srgb, var(--primary) 12%, transparent) !important;
}


/* =========================================================
   MÉTODOS DE PAGO - CONTRASTE CLARO Y OSCURO
   ========================================================= */

/* ---------- MODO CLARO: NO SELECCIONADOS ---------- */

.split-payment-cash {
    background: #ffffff !important;
    border: 1.5px solid #65b88d !important;
    color: #14764d !important;
}

.split-payment-card {
    background: #ffffff !important;
    border: 1.5px solid #7da9da !important;
    color: #245f9f !important;
}

.split-payment-yape {
    background: #ffffff !important;
    border: 1.5px solid #b77bc2 !important;
    color: #762985 !important;
}

.split-payment-plin {
    background: #ffffff !important;
    border: 1.5px solid #64b8ac !important;
    color: #08786b !important;
}


/* ---------- MODO CLARO: SELECCIONADOS ---------- */

#splitCash:checked + .split-payment-cash {
    background: #dff3e8 !important;
    border: 2px solid #168a5b !important;
    color: #0f6843 !important;
    box-shadow: 0 0 0 2px rgba(22,138,91,.08) !important;
}

#splitCard:checked + .split-payment-card {
    background: #e1edfa !important;
    border: 2px solid #3979bb !important;
    color: #205b98 !important;
    box-shadow: 0 0 0 2px rgba(57,121,187,.08) !important;
}

#splitYape:checked + .split-payment-yape {
    background: #f0dff4 !important;
    border: 2px solid #8c3b9b !important;
    color: #6d247a !important;
    box-shadow: 0 0 0 2px rgba(140,59,155,.08) !important;
}

#splitPlin:checked + .split-payment-plin {
    background: #dcf2ef !important;
    border: 2px solid #168f80 !important;
    color: #087367 !important;
    box-shadow: 0 0 0 2px rgba(22,143,128,.08) !important;
}


/* ---------- MODO OSCURO: NO SELECCIONADOS ---------- */

html[data-color-mode="dark"] .split-payment-cash {
    background: #14283a !important;
    border: 1.5px solid #3fa374 !important;
    color: #7ed6a9 !important;
}

html[data-color-mode="dark"] .split-payment-card {
    background: #14283a !important;
    border: 1.5px solid #5a91ca !important;
    color: #93c2f1 !important;
}

html[data-color-mode="dark"] .split-payment-yape {
    background: #14283a !important;
    border: 1.5px solid #a65db2 !important;
    color: #d59ade !important;
}

html[data-color-mode="dark"] .split-payment-plin {
    background: #14283a !important;
    border: 1.5px solid #43a99b !important;
    color: #83d7cb !important;
}


/* ---------- MODO OSCURO: SELECCIONADOS ---------- */

html[data-color-mode="dark"]
#splitCash:checked + .split-payment-cash {
    background: #245a42 !important;
    border: 2px solid #69d49b !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(105,212,155,.13) !important;
}

html[data-color-mode="dark"]
#splitCard:checked + .split-payment-card {
    background: #285887 !important;
    border: 2px solid #83bdf5 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(131,189,245,.13) !important;
}

html[data-color-mode="dark"]
#splitYape:checked + .split-payment-yape {
    background: #61366d !important;
    border: 2px solid #d58adf !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(213,138,223,.13) !important;
}

html[data-color-mode="dark"]
#splitPlin:checked + .split-payment-plin {
    background: #206159 !important;
    border: 2px solid #70d5c6 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(112,213,198,.13) !important;
}


/* =========================================================
   QR YAPE / PLIN - MEJOR CONTRASTE EN MODO OSCURO
   ========================================================= */

/* Panel general */
html[data-color-mode="dark"] .split-qr-box {
    background: #162b40 !important;
    border: 1px solid #456078 !important;
}

/* Título Yape */
html[data-color-mode="dark"] .split-yape-box .split-qr-title {
    color: #e1b4e8 !important;
    font-weight: 800 !important;
}

/* Título Plin */
html[data-color-mode="dark"] .split-plin-box .split-qr-title {
    color: #9de1d7 !important;
    font-weight: 800 !important;
}

/* QR: mantener fondo blanco para lectura */
html[data-color-mode="dark"] .split-qr-image {
    background: #ffffff !important;
    border: 5px solid #ffffff !important;
    border-radius: 10px !important;
}

/* Caja de Monto a pagar */
html[data-color-mode="dark"] .split-qr-amount {
    background: #0c1b2c !important;
    border: 1px solid #304a62 !important;
    color: #ffffff !important;
}

/* Texto Monto a pagar */
html[data-color-mode="dark"] .split-qr-amount span:first-child,
html[data-color-mode="dark"] .split-qr-amount strong:first-child {
    color: #f8fafc !important;
}

/* Monto Yape */
html[data-color-mode="dark"] #splitYapeAmount {
    color: #e6b5ed !important;
    font-size: 1.05rem !important;
    font-weight: 800 !important;
}

/* Monto Plin */
html[data-color-mode="dark"] #splitPlinAmount {
    color: #9de1d7 !important;
    font-size: 1.05rem !important;
    font-weight: 800 !important;
}

/* Si no existe QR */
html[data-color-mode="dark"] .split-qr-empty {
    color: #a9bac9 !important;
}


/* =========================================================
   EFECTIVO Y TARJETA - MEJOR CONTRASTE EN MODO OSCURO
   ========================================================= */

/* ---------- EFECTIVO ---------- */

html[data-color-mode="dark"] .split-cash-group {
    background: #162b40 !important;
    border: 1px solid #3f6b58 !important;
    color: #f8fafc !important;
}

html[data-color-mode="dark"] .split-cash-group label,
html[data-color-mode="dark"] .split-cash-group .form-label {
    color: #f8fafc !important;
}

html[data-color-mode="dark"] #splitReceivedAmount {
    background: #0c1b2c !important;
    border: 1px solid #4c8068 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] #splitReceivedAmount:focus {
    border-color: #69d49b !important;
    box-shadow: 0 0 0 .2rem rgba(105, 212, 155, .10) !important;
}

html[data-color-mode="dark"] #splitChangeAmount {
    color: #8de0b4 !important;
    font-weight: 800 !important;
}


/* ---------- TARJETA ---------- */

html[data-color-mode="dark"] .split-card-amount {
    background: #162b40 !important;
    border: 1px solid #4d6f94 !important;
    color: #f8fafc !important;
}

html[data-color-mode="dark"] .split-card-amount span,
html[data-color-mode="dark"] .split-card-amount strong {
    color: #f8fafc;
}

html[data-color-mode="dark"] #splitCardAmountValue {
    color: #9bc8f5 !important;
    font-size: 1.05rem !important;
    font-weight: 800 !important;
}


/* ---------- TEXTOS SECUNDARIOS ---------- */

html[data-color-mode="dark"] .split-cash-group .text-muted,
html[data-color-mode="dark"] .split-card-amount .text-muted {
    color: #aebdca !important;
}


/* =========================================================
   ENCABEZADO MESA / ORDEN - PALETA DEL SISTEMA
   ========================================================= */

/* Modo claro */
.split-page .split-table-header {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
}

.split-page .split-table-header h5,
.split-page .split-table-header span,
.split-page .split-table-header strong,
.split-page .split-table-header i {
    color: #ffffff !important;
}

/* Modo oscuro: NO reemplazar por azul oscuro */
html[data-color-mode="dark"] .split-page .split-table-header {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .split-page .split-table-header h5,
html[data-color-mode="dark"] .split-page .split-table-header span,
html[data-color-mode="dark"] .split-page .split-table-header strong,
html[data-color-mode="dark"] .split-page .split-table-header i {
    color: #ffffff !important;
}


/* =========================================================
   MÉTODOS DE PAGO - MODO CLARO MÁS VISIBLE
   ========================================================= */

/* EFECTIVO */
.split-payment-cash {
    background: #eef9f3 !important;
    border: 1.5px solid #72c49a !important;
    color: #087a4b !important;
}

#splitCash:checked + .split-payment-cash {
    background: #ccebdc !important;
    border: 2px solid #07965c !important;
    color: #05683f !important;
    box-shadow: 0 3px 9px rgba(7,150,92,.14) !important;
}


/* TARJETA */
.split-payment-card {
    background: #f0f6fd !important;
    border: 1.5px solid #7eafe2 !important;
    color: #1763ad !important;
}

#splitCard:checked + .split-payment-card {
    background: #d5e7f9 !important;
    border: 2px solid #347fc6 !important;
    color: #165b9b !important;
    box-shadow: 0 3px 9px rgba(52,127,198,.14) !important;
}


/* YAPE */
.split-payment-yape {
    background: #faf2fc !important;
    border: 1.5px solid #c989d3 !important;
    color: #812b91 !important;
}

#splitYape:checked + .split-payment-yape {
    background: #edd5f2 !important;
    border: 2px solid #9b47aa !important;
    color: #70247d !important;
    box-shadow: 0 3px 9px rgba(155,71,170,.14) !important;
}


/* PLIN */
.split-payment-plin {
    background: #eefaf8 !important;
    border: 1.5px solid #68c0b4 !important;
    color: #087c6d !important;
}

#splitPlin:checked + .split-payment-plin {
    background: #d0eee9 !important;
    border: 2px solid #159687 !important;
    color: #076d62 !important;
    box-shadow: 0 3px 9px rgba(21,150,135,.14) !important;
}


/* Texto e iconos un poco más definidos */
.split-payment-btn {
    font-weight: 700 !important;
}

.split-payment-btn i {
    font-weight: 700;
}


/* ===== BOTÓN COBRAR - CONTRASTE ===== */

#btnSplit,
#btnSplit i {
    color: #ffffff !important;
}

/* Modo oscuro */
html[data-color-mode="dark"] #btnSplit,
html[data-color-mode="dark"] #btnSplit i {
    color: #ffffff !important;
}

/* Deshabilitado */
#btnSplit:disabled,
#btnSplit:disabled i,
html[data-color-mode="dark"] #btnSplit:disabled,
html[data-color-mode="dark"] #btnSplit:disabled i {
    color: rgba(255,255,255,.65) !important;
}


/* COBRAR - ICONO Y TEXTO SIEMPRE BLANCOS */
.split-page #btnSplit,
.split-page #btnSplit i,
.split-page #btnSplit .bi,
html[data-color-mode="dark"] .split-page #btnSplit,
html[data-color-mode="dark"] .split-page #btnSplit i,
html[data-color-mode="dark"] .split-page #btnSplit .bi {
    color: #ffffff !important;
}


/* DIVIDIR CUENTA - TEXTOS DE PAGO BLANCOS EN MODO OSCURO */

/* EFECTIVO */
html[data-color-mode="dark"] #splitCashGroup,
html[data-color-mode="dark"] #splitCashGroup label,
html[data-color-mode="dark"] #splitCashGroup span,
html[data-color-mode="dark"] #splitCashGroup strong,
html[data-color-mode="dark"] #splitReceivedAmount,
html[data-color-mode="dark"] #splitChangeAmount {
    color: #ffffff !important;
}

/* TARJETA */
html[data-color-mode="dark"] #splitCardAmount,
html[data-color-mode="dark"] #splitCardAmount span,
html[data-color-mode="dark"] #splitCardAmount strong,
html[data-color-mode="dark"] #splitCardAmountValue {
    color: #ffffff !important;
}

/* YAPE */
html[data-color-mode="dark"] #splitYapeQr,
html[data-color-mode="dark"] #splitYapeQr span,
html[data-color-mode="dark"] #splitYapeQr strong,
html[data-color-mode="dark"] #splitYapeAmount {
    color: #ffffff !important;
}

/* PLIN */
html[data-color-mode="dark"] #splitPlinQr,
html[data-color-mode="dark"] #splitPlinQr span,
html[data-color-mode="dark"] #splitPlinQr strong,
html[data-color-mode="dark"] #splitPlinAmount {
    color: #ffffff !important;
}

/* DIVIDIR CUENTA - TITULOS YAPE Y PLIN BLANCOS EN MODO OSCURO */
html[data-color-mode="dark"] #splitYapeQr .split-qr-title,
html[data-color-mode="dark"] #splitYapeQr .split-qr-title *,
html[data-color-mode="dark"] #splitPlinQr .split-qr-title,
html[data-color-mode="dark"] #splitPlinQr .split-qr-title * {
    color: #ffffff !important;
}

/* DIVIDIR CUENTA - COLOR SEGUN SELECCION EN MODO OSCURO */

/* NO SELECCIONADOS */
html[data-color-mode="dark"] #splitCash:not(:checked) + .split-payment-cash,
html[data-color-mode="dark"] #splitCash:not(:checked) + .split-payment-cash i {
    color: #6ee7a8 !important;
}

html[data-color-mode="dark"] #splitCard:not(:checked) + .split-payment-card,
html[data-color-mode="dark"] #splitCard:not(:checked) + .split-payment-card i {
    color: #78b9f4 !important;
}

html[data-color-mode="dark"] #splitYape:not(:checked) + .split-payment-yape,
html[data-color-mode="dark"] #splitYape:not(:checked) + .split-payment-yape i {
    color: #d99be4 !important;
}

html[data-color-mode="dark"] #splitPlin:not(:checked) + .split-payment-plin,
html[data-color-mode="dark"] #splitPlin:not(:checked) + .split-payment-plin i {
    color: #6edbd0 !important;
}

/* SELECCIONADO */
html[data-color-mode="dark"] #splitCash:checked + .split-payment-cash,
html[data-color-mode="dark"] #splitCash:checked + .split-payment-cash i,
html[data-color-mode="dark"] #splitCard:checked + .split-payment-card,
html[data-color-mode="dark"] #splitCard:checked + .split-payment-card i,
html[data-color-mode="dark"] #splitYape:checked + .split-payment-yape,
html[data-color-mode="dark"] #splitYape:checked + .split-payment-yape i,
html[data-color-mode="dark"] #splitPlin:checked + .split-payment-plin,
html[data-color-mode="dark"] #splitPlin:checked + .split-payment-plin i {
    color: #ffffff !important;
}

/* DIVIDIR CUENTA - BORDES YAPE Y PLIN EN MODO OSCURO */

/* YAPE */
html[data-color-mode="dark"] #splitYapeQr {
    border-color: #b968c7 !important;
}

html[data-color-mode="dark"] #splitYapeQr .split-qr-amount {
    border-color: #b968c7 !important;
}

/* PLIN */
html[data-color-mode="dark"] #splitPlinQr {
    border-color: #3bc5b4 !important;
}

html[data-color-mode="dark"] #splitPlinQr .split-qr-amount {
    border-color: #3bc5b4 !important;
}

/* DIVIDIR CUENTA - ESTRUCTURA DE BLOQUES DE PAGO */

/* ========== MODO CLARO ========== */

/* EFECTIVO */
html:not([data-color-mode="dark"]) #splitCashGroup {
    background: #ffffff !important;
    border: 1.5px solid #198754 !important;
}

html:not([data-color-mode="dark"]) #splitReceivedAmount {
    background: #eef9f3 !important;
    border: 1.5px solid #198754 !important;
}

/* TARJETA */
html:not([data-color-mode="dark"]) #splitCardAmount {
    background: #eff6ff !important;
    border: 1.5px solid #347fc6 !important;
}

/* YAPE */
html:not([data-color-mode="dark"]) #splitYapeQr {
    background: #ffffff !important;
    border: 1.5px solid #9b47aa !important;
}

html:not([data-color-mode="dark"]) #splitYapeQr .split-qr-amount {
    background: #faf2fc !important;
    border: 1.5px solid #9b47aa !important;
}

/* PLIN */
html:not([data-color-mode="dark"]) #splitPlinQr {
    background: #ffffff !important;
    border: 1.5px solid #159687 !important;
}

html:not([data-color-mode="dark"]) #splitPlinQr .split-qr-amount {
    background: #eefaf8 !important;
    border: 1.5px solid #159687 !important;
}


/* ========== MODO OSCURO ========== */

/* EFECTIVO */
html[data-color-mode="dark"] #splitCashGroup {
    background: #132338 !important;
    border: 1.5px solid #28a66d !important;
}

html[data-color-mode="dark"] #splitReceivedAmount {
    background: #12352d !important;
    border: 1.5px solid #28a66d !important;
}

/* TARJETA */
html[data-color-mode="dark"] #splitCardAmount {
    background: #162d48 !important;
    border: 1.5px solid #438bcf !important;
}

/* YAPE */
html[data-color-mode="dark"] #splitYapeQr {
    background: #132338 !important;
    border: 1.5px solid #b968c7 !important;
}

html[data-color-mode="dark"] #splitYapeQr .split-qr-amount {
    background: #2b1831 !important;
    border: 1.5px solid #b968c7 !important;
}

/* PLIN */
html[data-color-mode="dark"] #splitPlinQr {
    background: #132338 !important;
    border: 1.5px solid #3bc5b4 !important;
}

html[data-color-mode="dark"] #splitPlinQr .split-qr-amount {
    background: #123532 !important;
    border: 1.5px solid #3bc5b4 !important;
}

/* DIVIDIR CUENTA - COLORES DE MONTOS EN MODO CLARO */

/* EFECTIVO */
html:not([data-color-mode="dark"]) #splitCashGroup .form-label,
html:not([data-color-mode="dark"]) #splitReceivedAmount,
html:not([data-color-mode="dark"]) #splitChangeAmount {
    color: #087a4b !important;
}

/* TARJETA */
html:not([data-color-mode="dark"]) #splitCardAmount span,
html:not([data-color-mode="dark"]) #splitCardAmount strong,
html:not([data-color-mode="dark"]) #splitCardAmountValue {
    color: #1763ad !important;
}

/* YAPE */
html:not([data-color-mode="dark"]) #splitYapeQr .split-qr-amount span,
html:not([data-color-mode="dark"]) #splitYapeQr .split-qr-amount strong,
html:not([data-color-mode="dark"]) #splitYapeAmount {
    color: #812b91 !important;
}

/* PLIN */
html:not([data-color-mode="dark"]) #splitPlinQr .split-qr-amount span,
html:not([data-color-mode="dark"]) #splitPlinQr .split-qr-amount strong,
html:not([data-color-mode="dark"]) #splitPlinAmount {
    color: #087c6d !important;
}

/* DIVIDIR CUENTA - CAMBIO EFECTIVO MODO CLARO */
html:not([data-color-mode="dark"]) #splitCashGroup .small {
    color: #087a4b !important;
}

/* DIVIDIR CUENTA - METODO DE PAGO MODO CLARO */
/* NO SELECCIONADOS: SOLO BORDE */

html:not([data-color-mode="dark"]) .split-payment-cash {
    background: transparent !important;
    border: 1.5px solid #72c49a !important;
    color: #087a4b !important;
}

html:not([data-color-mode="dark"]) .split-payment-card {
    background: transparent !important;
    border: 1.5px solid #7eafe2 !important;
    color: #1763ad !important;
}

html:not([data-color-mode="dark"]) .split-payment-yape {
    background: transparent !important;
    border: 1.5px solid #c989d3 !important;
    color: #812b91 !important;
}

html:not([data-color-mode="dark"]) .split-payment-plin {
    background: transparent !important;
    border: 1.5px solid #68c0b4 !important;
    color: #087c6d !important;
}
</style>

@endsection



























