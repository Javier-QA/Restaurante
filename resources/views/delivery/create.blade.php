@extends('layouts.app')

@section('content')
<style>
    /* =========================================================
       NUEVO PEDIDO - DELIVERY / RECOJO
       Diseño conectado a las paletas configurables del sistema
       ========================================================= */

    .delivery-create-page {
        height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .delivery-create-header {
        margin-bottom: 1rem;
        flex-shrink: 0;
    }

    .delivery-create-title {
        color: var(--text-main);
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    .delivery-create-title-icon {
        width: 38px;
        height: 38px;
        border-radius: var(--radius-md);
        background: var(--primary);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .delivery-create-subtitle {
        color: var(--text-muted);
        font-size: .84rem;
        margin: .2rem 0 0 48px;
    }

    #deliveryForm {
        min-height: 0;
    }

    #deliveryForm .card {
        background: var(--card-bg);
        border: 1px solid var(--border-soft) !important;
        border-radius: var(--radius-xl);
        box-shadow: var(--theme-shadow) !important;
        overflow: hidden;
    }

    #deliveryForm .card-header {
        background: var(--card-bg) !important;
        color: var(--text-main);
        border-bottom: 1px solid var(--border-soft) !important;
        padding: 1rem 1.15rem !important;
    }

    #deliveryForm .card-header h6 {
        font-weight: 800;
        display: flex;
        align-items: center;
        margin: 0;
    }

    #deliveryForm .card-header h6 i {
        color: var(--primary);
        font-size: 1.05rem;
    }

    #deliveryForm .card-body {
        color: var(--text-main);
    }

    #deliveryForm .form-label {
        color: var(--text-main);
        margin-bottom: .4rem;
    }

    #deliveryForm .text-muted {
        color: var(--text-muted) !important;
    }

    #deliveryForm .form-control,
    #deliveryForm .form-select {
        background-color: var(--card-bg);
        color: var(--text-main);
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-md);
        min-height: 42px;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    #deliveryForm textarea.form-control {
        min-height: auto;
    }

    #deliveryForm .form-control::placeholder {
        color: var(--text-muted);
        opacity: .75;
    }

    #deliveryForm .form-control:focus,
    #deliveryForm .form-select:focus {
        background-color: var(--card-bg);
        color: var(--text-main);
        border-color: var(--primary);
        box-shadow: 0 0 0 .18rem color-mix(in srgb, var(--primary) 18%, transparent);
    }

    #deliveryForm select.form-select {
        appearance: auto !important;
        -webkit-appearance: auto !important;
        -moz-appearance: auto !important;
        background-image: initial !important;
    }

    /* Panel de productos */
    #productGrid {
        background: var(--light-bg) !important;
        border-color: var(--border-soft) !important;
    }

    #searchProduct {
        background: var(--card-bg);
    }

    #deliveryForm .product-card > .card {
        border: 1px solid var(--border-soft) !important;
        border-radius: var(--radius-md);
        box-shadow: none !important;
        transition: transform .16s ease,
                    border-color .16s ease,
                    box-shadow .16s ease;
    }

    #deliveryForm .product-card > .card:hover {
        transform: translateY(-2px);
        border-color: var(--primary) !important;
        box-shadow: var(--theme-shadow) !important;
    }

    #deliveryForm .product-card h6 {
        color: var(--text-main);
    }

    #deliveryForm .product-card .text-primary {
        color: var(--primary) !important;
    }

    /* Carrito */
    #deliveryCartContainer {
        background: var(--card-bg);
    }

    #deliveryCartContainer table {
        color: var(--text-main);
    }

    #deliveryCartContainer td {
        color: var(--text-main);
        border-color: var(--border-soft);
    }

    .delivery-cart-summary {
        background: var(--light-bg) !important;
        border-color: var(--border-soft) !important;
    }

    #cartItemCount {
        background: var(--primary) !important;
        color: #fff;
        padding: .45rem .7rem;
        font-weight: 700;
    }

    #totalAmount {
        color: var(--primary);
    }

    #btnSubmitDelivery {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        border-radius: var(--radius-md);
        min-height: 46px;
        font-weight: 800 !important;
        letter-spacing: .015em;
        box-shadow: var(--theme-shadow) !important;
    }

    #btnSubmitDelivery:hover:not(:disabled) {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
    }

    #btnSubmitDelivery:disabled {
        opacity: .55;
        box-shadow: none !important;
    }

    /* Botón volver */
    .delivery-back-btn {
        color: var(--text-main);
        background: var(--card-bg);
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-md);
        font-weight: 600;
    }

    .delivery-back-btn:hover {
        color: var(--primary);
        background: var(--light-bg);
        border-color: var(--primary);
    }

    /* Scroll */
    #deliveryForm .overflow-auto {
        scrollbar-width: thin;
        scrollbar-color: var(--border-soft) transparent;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .delivery-create-page {
            height: auto;
            min-height: calc(100vh - 60px);
        }

        #deliveryForm {
            overflow: visible !important;
        }

        #deliveryForm > .col-md-4,
        #deliveryForm > .col-md-8 {
            height: auto !important;
        }

        #deliveryForm .card {
            min-height: 500px;
        }
    }

    @media (max-width: 767.98px) {
        .delivery-create-page {
            padding-top: .75rem;
        }

        .delivery-create-subtitle {
            margin-left: 0;
        }

        #deliveryForm .card-body.d-flex {
            flex-direction: column !important;
        }

        #productGrid,
        #deliveryForm .card-body > .w-50 {
            width: 100% !important;
        }

        #productGrid {
            border-right: 0 !important;
            border-bottom: 1px solid var(--border-soft);
            max-height: 420px;
        }
    }
</style>

<div class="container-fluid delivery-create-page">
    <div class="d-flex justify-content-between align-items-center delivery-create-header">
        <div>
            <h4 class="delivery-create-title"><span class="delivery-create-title-icon"><i class="bi bi-receipt"></i></span>Nuevo Pedido</h4><p class="delivery-create-subtitle">Registra un pedido para delivery o recojo en local.</p>
        </div>
        <a href="{{ route('delivery.index') }}" class="btn btn-sm delivery-back-btn">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ route('delivery.store') }}" method="POST" id="deliveryForm" class="row g-3 flex-grow-1 overflow-hidden" style="min-height: 0;">
        @csrf
        
        {{-- PANEL IZQUIERDO: Datos del Cliente --}}
        <div class="col-md-4 h-100 d-flex flex-column">
            <div class="card shadow-sm border-0 h-100 flex-column d-flex">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2"></i>Datos del Pedido</h6>
                </div>
                <div class="card-body overflow-auto">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tipo de entrega</label>
                        <select name="delivery_type" id="delivery_type" class="form-select">
                            <option value="delivery">Delivery</option>
                            <option value="pickup">Recojo en local</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Cliente (Opcional)</label>
                        <select name="client_id" id="client_id" class="form-select">
                            <option value="">Consumidor Final / Nuevo</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" data-name="{{ $client->name }}" data-phone="{{ $client->phone }}" data-address="{{ $client->address }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre <span class="text-muted fw-normal">(Opcional)</span></label>
                        <input type="text" name="client_name" id="client_name" class="form-control" placeholder="Consumidor final">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Teléfono <span class="text-muted fw-normal">(Opcional)</span></label>
                        <input type="text" name="client_phone" id="client_phone" class="form-control" placeholder="Número de contacto">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Dirección <span class="text-muted fw-normal">(Opcional)</span></label>
                        <textarea name="address" id="address" class="form-control" rows="2" placeholder="Dirección de entrega"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Referencia (Opcional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="Ej. Casa verde, puerta blanca">
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Método Pago *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Efectivo</option>
                                <option value="card">Tarjeta</option>
                                <option value="yape">Yape</option>
                                <option value="plin">Plin</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3" id="deliveryFeeGroup">
                            <label class="form-label small fw-bold">Costo Envío ({{ $currency }})</label>
                            <input type="number" step="0.10" min="0" name="delivery_fee" id="delivery_fee" class="form-control" value="0.00">
                        </div>
                    </div>
                    
                    <div class="mb-3" id="driverGroup">
                        <label class="form-label small fw-bold">Delivery (Opcional)</label>
                        <select name="driver_id" id="driver_id" class="form-select">
                            <option value="">Sin asignar por ahora</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notas adicionales</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ej. Llevar vuelto de 50"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- PANEL DERECHO: Productos y Carrito --}}
        <div class="col-md-8 h-100 d-flex flex-column">
            <div class="card shadow-sm border-0 h-100 flex-column d-flex">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Productos</h6>
                    <span class="badge bg-primary rounded-pill" id="cartItemCount">0 items</span>
                </div>
                
                <div class="card-body p-0 d-flex flex-row overflow-hidden" style="flex: 1;">
                    {{-- Lista de Productos --}}
                    <div class="w-50 bg-light border-end overflow-auto p-3" id="productGrid">
                        {{-- Buscador --}}
                        <div class="mb-3">
                            <input type="text" id="searchProduct" class="form-control" placeholder="Buscar producto...">
                        </div>
                        
                        <div class="row g-2">
                            @foreach($categories as $category)
                                @foreach($category->products as $product)
                                    <div class="col-6 product-card" data-name="{{ strtolower($product->name) }}" data-cat="{{ $category->id }}">
                                        <div class="card h-100 border-0 shadow-sm" style="cursor: pointer;" onclick="addDeliveryProduct({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                                            <div class="card-body p-2 text-center">
                                                <h6 class="small fw-bold mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                                                <div class="text-primary fw-bold small">{{ $currency }}{{ number_format($product->price, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Carrito --}}
                    <div class="w-50 d-flex flex-column bg-white">
                        <div class="flex-grow-1 overflow-auto p-2" id="deliveryCartContainer">
                            <table class="table table-sm table-borderless align-middle mb-0" style="width: 100%; table-layout: fixed;">
                                <tbody id="deliveryCartItems">
                                    <tr><td colspan="4" class="text-center text-muted py-4 small">Seleccione productos para agregar a la orden.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top flex-shrink-0 delivery-cart-summary">
                            <div class="d-flex justify-content-between fw-bold mb-2 small text-muted">
                                <span>Subtotal:</span>
                                <span>{{ $currency }}<span id="subtotalAmount">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold mb-3 fs-5">
                                <span>TOTAL:</span>
                                <span class="text-primary">{{ $currency }}<span id="totalAmount">0.00</span></span>
                            </div>
                            <button type="submit" class="btn w-100 py-2 fw-bold" id="btnSubmitDelivery" disabled>
                                <i class="bi bi-check-circle me-1"></i> CONFIRMAR PEDIDO
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let deliveryCart = [];
    
    const deliveryType = document.getElementById('delivery_type');
    const deliveryFeeGroup = document.getElementById('deliveryFeeGroup');
    const driverGroup = document.getElementById('driverGroup');
    const deliveryFee = document.getElementById('delivery_fee');
    const driverSelect = document.getElementById('driver_id');

    function updateDeliveryTypeFields() {
        const isPickup = deliveryType.value === 'pickup';

        if (deliveryFeeGroup) {
            deliveryFeeGroup.style.display = isPickup ? 'none' : '';
        }

        if (driverGroup) {
            driverGroup.style.display = isPickup ? 'none' : '';
        }

        if (isPickup) {
            deliveryFee.value = '0.00';
            driverSelect.value = '';
        }

        renderDeliveryCart();
    }

    deliveryType.addEventListener('change', updateDeliveryTypeFields);
    updateDeliveryTypeFields();

    document.getElementById('client_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('client_name').value = selectedOption.getAttribute('data-name');
            document.getElementById('client_phone').value = selectedOption.getAttribute('data-phone');
            document.getElementById('address').value = selectedOption.getAttribute('data-address');
        } else {
            document.getElementById('client_name').value = '';
            document.getElementById('client_phone').value = '';
            document.getElementById('address').value = '';
        }
    });

    document.getElementById('searchProduct').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(el => {
            el.style.display = el.getAttribute('data-name').includes(val) ? 'block' : 'none';
        });
    });

    document.getElementById('delivery_fee').addEventListener('input', renderDeliveryCart);

    function addDeliveryProduct(id, name, price) {
        const existing = deliveryCart.find(i => i.id === id);
        if(existing) existing.qty++;
        else deliveryCart.push({ id, name, price, qty: 1, note: '' });
        renderDeliveryCart();
    }

    function removeDeliveryProduct(index) {
        deliveryCart.splice(index, 1);
        renderDeliveryCart();
    }
    
    function updateDeliveryQty(index, delta) {
        deliveryCart[index].qty += delta;
        if(deliveryCart[index].qty < 1) deliveryCart[index].qty = 1;
        renderDeliveryCart();
    }

    function openDeliveryNote(index) {
        document.getElementById('deliveryNoteIndex').value = index;
        document.getElementById('deliveryNoteText').value = deliveryCart[index].note || '';
        new bootstrap.Modal(document.getElementById('deliveryNoteModal')).show();
    }

    function saveDeliveryNote() {
        const index = parseInt(document.getElementById('deliveryNoteIndex').value);
        const note = document.getElementById('deliveryNoteText').value.trim();
        if (!deliveryCart[index]) return;
        deliveryCart[index].note = note;
        bootstrap.Modal.getInstance(document.getElementById('deliveryNoteModal')).hide();
        renderDeliveryCart();
    }

    function updateDeliveryNote(index, value) {
        if (!deliveryCart[index]) return;
        deliveryCart[index].note = value;
    }

    function renderDeliveryCart() {
        const tbody = document.getElementById('deliveryCartItems');
        let subtotal = 0;
        
        if (deliveryCart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4 small">Seleccione productos para agregar a la orden.</td></tr>';
            document.getElementById('subtotalAmount').innerText = '0.00';
            document.getElementById('totalAmount').innerText = '0.00';
            document.getElementById('cartItemCount').innerText = '0 items';
            document.getElementById('btnSubmitDelivery').disabled = true;
            return;
        }

        tbody.innerHTML = '';
        deliveryCart.forEach((item, index) => {
            const lineTotal = item.price * item.qty;
            subtotal += lineTotal;
            
            tbody.innerHTML += `
                <tr class="border-bottom">
                    <td style="width:25px;" class="px-0">
                        <button type="button" class="btn btn-sm text-danger p-0" onclick="removeDeliveryProduct(${index})"><i class="bi bi-x-circle-fill"></i></button>
                    </td>
                    <td class="small fw-bold lh-sm px-1" title="${item.name}">${item.name}<br><span class="text-muted fw-normal" style="font-size:0.7rem;">S/${item.price.toFixed(2)}</span><br><button type="button" class="btn btn-link btn-sm p-0 mt-1 text-decoration-none text-warning" onclick="openDeliveryNote(${index})"><i class="bi bi-chat-left-text me-1"></i>${item.note ? 'Editar nota' : 'Agregar nota'}</button></td>
                    <td style="width:80px;" class="px-0">
                        <div class="input-group input-group-sm flex-nowrap">
                            <button type="button" class="btn btn-outline-secondary px-1 py-0" onclick="updateDeliveryQty(${index}, -1)">-</button>
                            <input type="text" class="form-control text-center px-0 py-0 fw-bold bg-white" value="${item.qty}" readonly>
                            <button type="button" class="btn btn-outline-primary px-1 py-0" onclick="updateDeliveryQty(${index}, 1)">+</button>
                        </div>
                        <input type="hidden" name="products[${index}][id]" value="${item.id}">
                        <input type="hidden" name="products[${index}][qty]" value="${item.qty}"><input type="hidden" name="products[${index}][note]" value="${item.note || ''}">
                    </td>
                    <td style="width:60px;" class="text-end fw-bold px-0 small">
                        ${lineTotal.toFixed(2)}
                    </td>
                </tr>
            `;
        });

        const fee = parseFloat(document.getElementById('delivery_fee').value) || 0;
        const total = subtotal + fee;

        document.getElementById('subtotalAmount').innerText = subtotal.toFixed(2);
        document.getElementById('totalAmount').innerText = total.toFixed(2);
        document.getElementById('cartItemCount').innerText = deliveryCart.reduce((acc, curr) => acc + curr.qty, 0) + ' items';
        document.getElementById('btnSubmitDelivery').disabled = false;
    }
</script>
<div class="modal fade" id="deliveryNoteModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Nota del Plato</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="deliveryNoteIndex"><label for="deliveryNoteText" class="form-label fw-semibold">Indicaciones para cocina</label><textarea id="deliveryNoteText" class="form-control" rows="3" maxlength="255" placeholder="Ejemplo: sin cebolla, sin picante..."></textarea></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" onclick="saveDeliveryNote()">Guardar Nota</button></div></div></div></div>
@endsection