@extends('layouts.app')

@section('content')
{{-- Notificaciones del POS --}}




{{-- POS ocupa 100% del viewport independiente del layout --}}
<div id="pos-wrap" class="pos-order-page" style="
    position: fixed;
    inset: 0;
    left: 260px;  /* ancho real del sidebar */
    display: flex;
    flex-direction: column;
    background: #f0eef8;
    z-index: 900;
">
    {{-- TOPBAR POS --}}
    <div style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        padding: 10px 20px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(45,27,94,0.06);
    ">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary btn-sm pos-back-btn">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <div>
                <div class="fw-bold text-primary" style="font-size:.95rem;line-height:1.2;">Mesa: {{ $table->name }}</div>
                <div class="text-muted" style="font-size:.75rem;">Zona: {{ $table->area->name }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            @if($order)
                <button type="button" class="btn btn-sm fw-bold pos-move-table-btn" data-bs-toggle="modal" data-bs-target="#moveTableModal">
                    <i class="bi bi-arrow-left-right me-1"></i> Mover Mesa
                </button>
            @endif
            <span class="badge bg-light text-dark border px-2 py-1" style="font-size:.78rem;">
                <i class="bi bi-person-fill me-1"></i> {{ auth()->user()->name }}
            </span>
        </div>
    </div>

    {{-- CUERPO: tres columnas --}}
    <div style="display:flex; flex:1; min-height:0; overflow:hidden;">

        {{-- Categorías --}}
        <div class="pos-categories-panel">
            <div class="list-group list-group-flush">
                <button onclick="filterProducts('all')" class="list-group-item list-group-item-action active text-center py-3 category-btn" id="cat-btn-all">
                    <i class="bi bi-grid-fill d-block fs-4 mb-1"></i> Todo
                </button>
                @foreach($categories as $category)
                    <button onclick="filterProducts('cat-{{ $category->id }}')" class="list-group-item list-group-item-action text-center py-3 category-btn" id="cat-btn-{{ $category->id }}">
                        @if($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" class="rounded mb-1" width="40" height="40" style="object-fit:cover;">
                        @else
                            <i class="bi bi-tag d-block fs-4 mb-1"></i>
                        @endif
                        <span class="d-block small fw-bold lh-sm">{{ $category->name }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Productos --}}
        <div style="flex:1; min-width:0; background:#ffffff; overflow-y:auto; padding:12px 14px 20px;" id="products-container">
            {{-- Barra búsqueda --}}
            <div style="position:sticky;top:0;background:#ffffff;padding-bottom:10px;z-index:10;">
                <div class="input-group input-group-lg pos-search shadow-sm">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           id="productSearch"
                           class="form-control border-start-0"
                           placeholder="Buscar producto..."
                           autocomplete="off">
                </div>
            </div>

            @php
                // Paleta de colores por categoría (ciclo automático)
                $catPalette = [
                    ['bg'=>'#fff4e6','icon'=>'#ff8c00','border'=>'#ffd08a','iconClass'=>'bi-egg-fried'],
                    ['bg'=>'#f3eeff','icon'=>'#9b5de5','border'=>'#c9a8f5','iconClass'=>'bi-award-fill'],
                    ['bg'=>'#ecfeff','icon'=>'#06b6d4','border'=>'#67e8f9','iconClass'=>'bi-cup-straw'],
                    ['bg'=>'#f0fdf4','icon'=>'#22c55e','border'=>'#86efac','iconClass'=>'bi-cup-hot-fill'],
                    ['bg'=>'#fef2f2','icon'=>'#ef4444','border'=>'#fca5a5','iconClass'=>'bi-fire'],
                    ['bg'=>'#eff6ff','icon'=>'#3b82f6','border'=>'#93c5fd','iconClass'=>'bi-droplet-fill'],
                    ['bg'=>'#fdf4ff','icon'=>'#d946ef','border'=>'#f0abfc','iconClass'=>'bi-flower1'],
                    ['bg'=>'#fff7ed','icon'=>'#f97316','border'=>'#fdba74','iconClass'=>'bi-basket2-fill'],
                ];
                $catColorMap = [];
                $ci = 0;
                foreach($categories as $cat) {
                    $catColorMap[$cat->id] = $catPalette[$ci % count($catPalette)];
                    $ci++;
                }
            @endphp
            <div class="row row-cols-2 row-cols-lg-3 row-cols-xl-4 g-3 pb-5">
                @foreach($categories as $category)
                    @php $pal = $catColorMap[$category->id]; @endphp
                    @foreach($category->products as $product)
                        <div class="col product-item cat-{{ $category->id }}" data-product-name="{{ strtolower($product->name) }}">
                            <div class="pos-product-card" onclick="addToOrder({{ $product->id }})" style="--pal-bg:{{ $pal['bg'] }};--pal-icon:{{ $pal['icon'] }};--pal-border:{{ $pal['border'] }};">

                                {{-- Imagen o bloque de color con ícono --}}
                                <div class="pos-product-img-wrap">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="pos-product-img" alt="{{ $product->name }}">
                                    @else
                                        <div class="pos-product-icon-block">
                                            <i class="bi {{ $pal['iconClass'] }} pos-product-icon"></i>
                                        </div>
                                    @endif

                                    {{-- Badge Precio --}}
                                    <div class="pos-price-badge">
                                        {{ $currency ?? 'S/' }}{{ number_format($product->price, 2) }}
                                    </div>

                                    {{-- Badge Stock --}}
                                    @if($product->controls_stock && !is_null($product->stock))
                                        <div class="pos-stock-badge {{ $product->stock <= $product->minimum_stock ? 'pos-stock-low' : 'pos-stock-ok' }}">
                                            <i class="bi bi-box-seam me-1"></i>{{ $product->stock_display }} {{ $product->unit_display }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Nombre --}}
                                <div class="pos-product-footer">
                                    <div class="pos-product-name">{{ $product->name }}</div>
                                    <div class="pos-product-cat">{{ $category->name }}</div>
                                </div>

                                {{-- Overlay al hover --}}
                                <div class="pos-add-overlay">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span>Agregar</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- CARRITO: columna derecha con ancho fijo --}}
        <div style="
            width: 280px;
            flex-shrink: 0;
            background: #ffffff;
            border-left: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: -4px 0 20px rgba(45,27,94,0.07);
        ">
            <div class="pos-cart-header" style="padding: 14px 16px; background: #20243a; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                <h6 class="fw-bold mb-0"><i class="bi bi-cart"></i> Cuenta Actual</h6>
            </div>
            <div id="cart-container" style="flex: 1; display: flex; flex-direction: column; min-height: 0; overflow-y: auto;">
                @include('pos.partials.cart', ['order' => $order])
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered pos-move-dialog">
        <div class="modal-content">
            <div class="modal-header py-2 pos-modal-note">
                <h6 class="modal-title fw-bold text-dark">Nota del Producto</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pos-move-body">
                <input type="hidden" id="noteDetailId">
                <label id="noteAreaLabel" for="noteText" class="form-label fw-semibold">Indicaciones para cocina</label><textarea id="noteText" class="form-control" rows="3" maxlength="255" placeholder="Ejemplo: sin cebolla, sin picante..."></textarea>
            </div>
            <div class="modal-footer pos-move-footer">
                <button type="button" class="btn system-palette-button w-100 btn-sm fw-bold" onclick="saveNote()">Guardar Nota</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="moveTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered pos-move-dialog">
        <div class="modal-content">
            <div class="modal-header pos-modal-move">
                <h6 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Mover Mesa</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            @if($order)
                <form action="{{ route('pos.move', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body pos-move-body">
                        <label class="form-label pos-move-label"><i class="bi bi-geo-alt me-1"></i>Mesa de destino</label>
                        <div class="position-relative"><select name="target_table_id" class="form-select pos-move-table-select" required>
                            <option value="" selected disabled>-- Elegir Mesa --</option>
                            @foreach($freeTables as $ft)
                                <option value="{{ $ft->id }}">{{ $ft->name }} ({{ $ft->area->name }})</option>
                            @endforeach
                        </select><i class="bi bi-chevron-down pos-move-table-arrow"></i></div>
                    </div>
                    <div class="modal-footer pos-move-footer">
                        <button type="submit" class="btn w-100 fw-bold pos-move-confirm"><i class="bi bi-check2-circle me-2"></i>Confirmar movimiento</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="optionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered pos-move-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 pos-modal-options">
                <h6 class="modal-title fw-bold">Ajustes</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pos-move-body">
<div class="pos-options-total mb-3">
    <span>Total actual</span>
    <strong>
        {{ $currency ?? 'S/' }}{{ number_format($order ? $order->total : 0, 2) }}
    </strong>
</div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Descuento Global</label>
                    <input type="number" step="0.01" min="0" id="inputDiscount" class="form-control" value="{{ $order ? $order->discount : 0 }}" onclick="this.select()">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Propina</label>
                    <input type="number" step="0.01" min="0" id="inputTip" class="form-control" value="{{ $order ? $order->tip : 0 }}" onclick="this.select()">
                </div>
            </div>
            <div class="modal-footer pos-move-footer">
                <button type="button" class="btn btn-primary w-100 btn-sm fw-bold" onclick="applyOptions()">Aplicar Cambios</button>
            </div>
        </div>
    </div>
</div>

@if($order)
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('pos.checkout', $order->id) }}" method="POST" id="checkoutForm" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header py-2 pos-modal-checkout">
                <h6 class="modal-title fw-bold">Cobrar Venta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">CLIENTE</label>
                    <div class="position-relative pos-client-search">

    <div class="input-group">
        <span class="input-group-text pos-client-search-icon">
            <i class="bi bi-search"></i>
        </span>

        <input
            type="text"
            class="form-control"
            id="clientSearchInput" name="client_name"
            placeholder="Buscar cliente..."
            autocomplete="off"
            oninput="filterClientDropdown(this.value)"

        >

        <button
            class="btn pos-client-clear"
            type="button"
            onclick="clearClientSearch()"
            title="Limpiar"
        >
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div id="clientDropdown" class="pos-client-dropdown">

        @forelse($clients as $client)

            <button
                type="button"
                class="pos-client-option"
                data-id="{{ $client->id }}"
                data-name="{{ $client->name }}"
                data-document="{{ $client->document_number }}"
                onclick="selectClient(this)"
            >
                <span class="pos-client-avatar">
                    <i class="bi bi-person-fill"></i>
                </span>

                <span class="pos-client-info">
                    <span class="pos-client-name">
                        {{ $client->name }}
                    </span>

                    <span class="pos-client-document">
                        {{ $client->document_number ?: 'Sin documento' }}
                    </span>
                </span>

                <i class="bi bi-chevron-right pos-client-chevron"></i>
            </button>

        @empty

            <div class="pos-client-empty">
                No hay clientes registrados
            </div>

        @endforelse

    </div>

</div>

<input type="hidden" name="client_id" id="clientId">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-8"><input type="text" name="client_document" id="clientDoc" class="form-control" placeholder="DNI / RUC" maxlength="11" inputmode="numeric" oninput="lookupClientByDocument()"></div>
                    <div class="col-4">
    <div class="position-relative pos-document-dropdown-wrap">

        <button
            type="button"
            class="pos-document-trigger"
            id="documentTypeTrigger"
            onclick="toggleDocumentDropdown()"
        >
            <span id="documentTypeLabel">Ticket</span>
            <i class="bi bi-chevron-down"></i>
        </button>

        <div
            id="documentTypeDropdown"
            class="pos-document-dropdown"
        >

            <button
                type="button"
                class="pos-document-option active"
                data-value="Ticket"
                onclick="selectDocumentType(this)"
            >
                <span class="pos-document-option-icon">
                    <i class="bi bi-receipt"></i>
                </span>

                <span>
                    <strong>Ticket</strong>
                    <small>Comprobante interno</small>
                </span>
            </button>

            <button
                type="button"
                class="pos-document-option"
                data-value="Boleta"
                onclick="selectDocumentType(this)"
            >
                <span class="pos-document-option-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </span>

                <span>
                    <strong>Boleta</strong>
                    <small>Comprobante electrónico</small>
                </span>
            </button>

            <button
                type="button"
                class="pos-document-option"
                data-value="Factura"
                onclick="selectDocumentType(this)"
            >
                <span class="pos-document-option-icon">
                    <i class="bi bi-building"></i>
                </span>

                <span>
                    <strong>Factura</strong>
                    <small>Requiere RUC</small>
                </span>
            </button>

        </div>

        <input
            type="hidden"
            name="document_type"
            id="documentTypeValue"
            value="Ticket"
        >

    </div>
</div>
                </div>
                <div class="mb-3 text-center">
    <div class="row g-2">

        <div class="col-6">
            <input
                type="radio"
                class="btn-check"
                name="payment_method"
                id="payCash"
                value="cash"
                checked
                onclick="selectPaymentMethod('cash')"
            >
            <label class="btn pos-payment-btn pos-payment-cash w-100 fw-bold" for="payCash">
                <i class="bi bi-cash-coin me-1"></i>
                Efectivo
            </label>
        </div>

        <div class="col-6">
            <input
                type="radio"
                class="btn-check"
                name="payment_method"
                id="payCard"
                value="card"
                onclick="selectPaymentMethod('card')"
            >
            <label class="btn pos-payment-btn pos-payment-card w-100 fw-bold" for="payCard">
                <i class="bi bi-credit-card me-1"></i>
                Tarjeta
            </label>
        </div>

        <div class="col-6">
            <input
                type="radio"
                class="btn-check"
                name="payment_method"
                id="payYape"
                value="yape"
                onclick="selectPaymentMethod('yape')"
            >
            <label class="btn pos-payment-btn pos-payment-yape w-100 fw-bold" for="payYape">
                <i class="bi bi-phone me-1"></i>
                Yape
            </label>
        </div>

        <div class="col-6">
            <input
                type="radio"
                class="btn-check"
                name="payment_method"
                id="payPlin"
                value="plin"
                onclick="selectPaymentMethod('plin')"
            >
            <label class="btn pos-payment-btn pos-payment-plin w-100 fw-bold" for="payPlin">
                <i class="bi bi-phone-vibrate me-1"></i>
                Plin
            </label>
        </div>

    </div>
</div>
                <div id="digitalPaymentAmount" class="pos-digital-payment-amount mb-3" style="display:none;">
    <span>Monto a pagar</span>
    <strong id="digitalPaymentAmountValue">
        {{ $currency ?? 'S/' }}0.00
    </strong>
</div>
<div id="cardPaymentAmount"
     class="pos-method-amount pos-method-card mb-3"
     style="display:none;">
    <span>Monto a pagar</span>
    <strong id="cardPaymentAmountValue">
        {{ $currency ?? 'S/' }}0.00
    </strong>
</div>
<div id="digitalPaymentQr" class="pos-digital-qr mb-3" style="display:none;">

    <div id="yapeQrBox" class="pos-qr-box" style="display:none;">
        <div class="pos-qr-title yape-color">
            <i class="bi bi-phone me-1"></i>
            Paga con Yape
        </div>

        @if(!empty($yapeQr))
            <img
                src="{{ asset('storage/' . $yapeQr) }}"
                alt="QR Yape"
                class="pos-qr-image"
            >
        @else
            <div class="pos-qr-empty">
                <i class="bi bi-qr-code"></i>
                <span>QR de Yape no configurado</span>
            </div>
        @endif

        <div class="pos-qr-amount mt-3">
            <span>Monto a pagar</span>
            <strong id="yapeQrAmount">{{ $currency ?? 'S/' }}0.00</strong>
        </div>
    </div>

    <div id="plinQrBox" class="pos-qr-box" style="display:none;">
        <div class="pos-qr-title plin-color">
            <i class="bi bi-phone-vibrate me-1"></i>
            Paga con Plin
        </div>

        @if(!empty($plinQr))
            <img
                src="{{ asset('storage/' . $plinQr) }}"
                alt="QR Plin"
                class="pos-qr-image"
            >
        @else
            <div class="pos-qr-empty">
                <i class="bi bi-qr-code"></i>
                <span>QR de Plin no configurado</span>
            </div>
        @endif

        <div class="pos-qr-amount mt-3">
            <span>Monto a pagar</span>

            <strong id="plinQrAmount">
                {{ $currency ?? 'S/' }}0.00
            </strong>
        </div>
    </div>

</div>

                <div id="cashInputGroup">

    <div class="mb-3">
        <label class="form-label fw-bold small">
            Recibido
        </label>

        <input
            type="number"
            step="0.01"
            name="received_amount"
            id="receivedAmount"
            class="form-control text-center fw-bold fs-4"
            value="{{ number_format($order->total, 2, '.', '') }}"
            oninput="calculateChange()"
            onclick="this.select()"
        >
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <span class="small fw-bold">
            Cambio:
        </span>

        <h4 class="fw-bold mb-0" id="changeAmount">
            0.00
        </h4>
    </div>

</div>
<input type="hidden" id="hiddenTotal" value="{{ number_format($order->total, 2, '.', '') }}">
            </div>
            <div class="modal-footer p-2 bg-light">
                <button type="button" class="btn pos-checkout-confirm-btn w-100 btn-lg fw-bold" onclick="openPaymentConfirm()">CONFIRMAR PAGO</button>
            </div>
        </form>
    </div>
</div>
@endif


<div class="modal fade" id="paymentConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered pos-move-dialog">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header pos-confirm-header">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-shield-check me-2"></i>
                    Confirmar pago
                </h6>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center p-4">

                <div class="pos-confirm-icon mb-3">
                    <i class="bi bi-cash-coin"></i>
                </div>

                <div class="text-muted small mb-1">
                    Estás por registrar un pago de
                </div>

                <div class="fw-bold fs-3 pos-confirm-total"
                     id="confirmPaymentTotal">
                    {{ $currency ?? 'S/' }}0.00
                </div>

                <div class="mt-3">
                    <span class="text-muted small">Método:</span>
                    <span class="fw-bold ms-1"
                          id="confirmPaymentMethod">
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
                        class="btn pos-confirm-pay-btn flex-fill fw-bold"
                        onclick="submitConfirmedPayment()">
                    Confirmar
                </button>
            </div>

        </div>
    </div>
</div>


<script>
    const tableId = {{ $table->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    window.addToOrder = function(productId) {
        fetch(`{{ url('/pos/order') }}/${tableId}/add`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ product_id: productId })
        }).then(r => r.text()).then(html => {
            document.getElementById('cart-container').innerHTML = html;
            updateCheckoutTotal();
        });
    };

    window.updateQty = function(id, qty) {

        const updateQuantity = function() {
            fetch(`{{ url('/pos/detail') }}/${id}/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ quantity: qty })
            }).then(r => r.text()).then(html => {
                document.getElementById('cart-container').innerHTML = html;
                updateCheckoutTotal();
            });
        };

        if (qty < 1) {
            SystemNotify.confirm({
                type: 'danger',
                title: 'Eliminar producto',
                text: '¿Deseas retirar este producto de la cuenta actual?',
                confirmText: 'Eliminar producto',
                icon: 'bi-trash3',
                onConfirm: updateQuantity
            });

            return;
        }

        updateQuantity();
    };

    window.removeItem = function(id) {
        fetch(`{{ url('/pos/detail') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        }).then(r => r.text()).then(html => {
            document.getElementById('cart-container').innerHTML = html;
            updateCheckoutTotal();
        });
    };

    window.showPosNotification = function(message) {
        let notification = document.getElementById('posSystemNotification');

        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'posSystemNotification';
            notification.style.cssText = `
                position: fixed;
                top: 24px;
                right: 24px;
                z-index: 99999;
                width: min(390px, calc(100vw - 48px));
                background: var(--card-bg, #ffffff);
                color: var(--text-main, #172033);
                border: 1px solid var(--border-soft, #e5e7eb);
                border-left: 5px solid var(--primary, #ff8c00);
                border-radius: var(--radius-md, 12px);
                box-shadow: var(--shadow-soft, 0 10px 30px rgba(0,0,0,.15));
                padding: 14px 16px;
                display: none;
            `;

            document.body.appendChild(notification);
        }

        notification.innerHTML = `
            <div style="display:flex; align-items:flex-start; gap:12px;">
                <i class="bi bi-exclamation-triangle-fill"
                   style="color:var(--primary, #ff8c00); font-size:1.25rem;"></i>

                <div style="flex:1;">
                    <div style="font-weight:700; margin-bottom:2px;">
                        Pedido pendiente de envío
                    </div>
                    <div style="font-size:.9rem;">
                        ${message}
                    </div>
                </div>

                <button type="button"
                        onclick="this.closest('#posSystemNotification').style.display='none'"
                        style="border:0;background:transparent;color:var(--text-muted,#6b7280);font-size:1.2rem;line-height:1;">
                    &times;
                </button>
            </div>
        `;

        notification.style.display = 'block';

        clearTimeout(window.posNotificationTimer);

        window.posNotificationTimer = setTimeout(() => {
            notification.style.display = 'none';
        }, 4000);
    };

    window.showPosSuccessNotification = function(message) {
        let notification = document.getElementById('posSuccessNotification');

        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'posSuccessNotification';

            notification.style.cssText = `
                position: fixed;
                top: 24px;
                right: 24px;
                z-index: 99999;
                width: min(390px, calc(100vw - 48px));
                background: var(--card-bg, #ffffff);
                color: var(--text-main, #172033);
                border: 1px solid var(--border-soft, #e5e7eb);
                border-left: 5px solid var(--accent-2, #198754);
                border-radius: var(--radius-md, 12px);
                box-shadow: var(--shadow-soft, 0 10px 30px rgba(0,0,0,.15));
                padding: 14px 16px;
                display: none;
            `;

            document.body.appendChild(notification);
        }

        notification.innerHTML = `
            <div style="display:flex; align-items:flex-start; gap:12px;">

                <i class="bi bi-check-circle-fill"
                   style="color:var(--accent-2, #198754); font-size:1.25rem;"></i>

                <div style="flex:1;">
                    <div style="font-weight:700; margin-bottom:2px;">
                        Pedido enviado con éxito
                    </div>

                    <div style="font-size:.9rem;">
                        ${message}
                    </div>
                </div>

                <button type="button"
                        onclick="this.closest('#posSuccessNotification').style.display='none'"
                        style="border:0;background:transparent;color:var(--text-muted,#6b7280);font-size:1.2rem;line-height:1;">
                    &times;
                </button>
            </div>
        `;

        notification.style.display = 'block';

        clearTimeout(window.posSuccessNotificationTimer);

        window.posSuccessNotificationTimer = setTimeout(() => {
            notification.style.display = 'none';
        }, 4000);
    };

    window.confirmAndSendToKitchen = function(orderId) {
        let modalElement = document.getElementById('sendKitchenConfirmModal');

        if (!modalElement) {
            modalElement = document.createElement('div');
            modalElement.className = 'modal fade';
            modalElement.id = 'sendKitchenConfirmModal';
            modalElement.tabIndex = -1;

            modalElement.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow-lg"
                         style="border-radius:var(--radius-md, 14px); overflow:hidden;">

                        <div class="modal-header border-0"
                             style="background:var(--primary, #ff8c00); color:#fff;">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-send-check me-2"></i>
                                Enviar pedido
                            </h6>

                            <button type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body text-center px-4 py-4">
                            <div class="mb-3">
                                <i class="bi bi-question-circle"
                                   style="font-size:2.8rem; color:var(--primary, #ff8c00);"></i>
                            </div>

                            <div class="fw-bold mb-2">
                                ¿Enviar pedido?
                            </div>

                            <div class="small text-muted">
                                Los productos serán enviados a Cocina o Barra según corresponda y ya no podrán modificarse.
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                            <button type="button"
                                    class="btn btn-light border flex-fill"
                                    data-bs-dismiss="modal">
                                Cancelar
                            </button>

                            <button type="button"
                                    id="confirmSendKitchenBtn"
                                    class="btn flex-fill fw-bold text-white"
                                    style="background:var(--primary, #ff8c00);">
                                Aceptar
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modalElement);
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const confirmBtn = document.getElementById('confirmSendKitchenBtn');

        confirmBtn.onclick = function() {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

            fetch(`{{ url('/pos/order') }}/${orderId}/send-kitchen`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'text/html'
                }
            })
            .then(async response => {
                const responseContent = await response.text();

                if (!response.ok) {
                    throw new Error(
                        responseContent || 'No se pudo enviar el pedido a Cocina.'
                    );
                }

                return responseContent;
            })
            .then(html => {
                document.getElementById('cart-container').innerHTML = html;
                updateCheckoutTotal();

                modal.hide();

                setTimeout(() => {
                    showPosSuccessNotification(
                        'Los platos fueron enviados correctamente a Cocina.'
                    );
                }, 250);
            })
            .catch(error => {
                modal.hide();

                setTimeout(() => {
                    showPosNotification(error.message);
                }, 250);
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Aceptar';
            });
        };

        modal.show();
    };
    window.applyOptions = function() {
        var discount = document.getElementById('inputDiscount').value;
        var tip = document.getElementById('inputTip').value;
        var modal = bootstrap.Modal.getInstance(document.getElementById('optionsModal'));
        modal.hide();

        fetch(`{{ url('/pos/order') }}/{{ $order ? $order->id : 0 }}/discount`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ discount: discount, tip: tip })
        }).then(r => r.text()).then(html => {
            document.getElementById('cart-container').innerHTML = html;
            updateCheckoutTotal();
        });
    };

    // Busqueda instantanea de productos
    const productSearch = document.getElementById('productSearch');

    if (productSearch) {
        productSearch.addEventListener('input', function () {
            const search = this.value
                .toLowerCase()
                .trim()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');

            document.querySelectorAll('.product-item').forEach(item => {
                const name = (item.dataset.productName || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '');

                item.style.display = name.includes(search)
                    ? 'block'
                    : 'none';
            });

            if (search !== '') {
                document.querySelectorAll('.category-btn')
                    .forEach(btn => btn.classList.remove('active'));

                const all = document.getElementById('cat-btn-all');
                if (all) all.classList.add('active');
            }
        });
    }

    // Utils
    window.filterProducts = function(cat) {
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(cat === 'all' ? 'cat-btn-all' : 'cat-btn-' + cat.replace('cat-', '')).classList.add('active');
        document.querySelectorAll('.product-item').forEach(item => {
            item.style.display = (cat === 'all' || item.classList.contains(cat)) ? 'block' : 'none';
        });
    };

    window.updateCheckoutTotal = function() {
        setTimeout(() => {
            var newTotal = document.getElementById('cartTotalValue') ? document.getElementById('cartTotalValue').value : 0;
            var hiddenInput = document.getElementById('hiddenTotal');
            var receivedInput = document.getElementById('receivedAmount');
            if(hiddenInput) hiddenInput.value = newTotal;
            if(receivedInput) receivedInput.value = newTotal;
        }, 500);
    };

    // Modal Notas
    var noteModalEl = document.getElementById('noteModal');
    if(noteModalEl){
        noteModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('noteDetailId').value = button.getAttribute('data-detail-id');
            document.getElementById('noteText').value = button.getAttribute('data-note-content') || '';
            var area = button.getAttribute('data-preparation-area') || 'kitchen';
            document.getElementById('noteAreaLabel').textContent = area === 'barra' ? 'Indicaciones para barra' : 'Indicaciones para cocina';
            document.getElementById('noteText').placeholder = area === 'barra' ? 'Ejemplo: poco hielo, sin azúcar...' : 'Ejemplo: sin cebolla, sin picante...';
            setTimeout(() => document.getElementById('noteText').focus(), 500);
        });
    }
    window.saveNote = function() {
        var detailId = document.getElementById('noteDetailId').value;
        var note = document.getElementById('noteText').value;
        var modal = bootstrap.Modal.getInstance(document.getElementById('noteModal'));
        modal.hide();
        fetch(`{{ url('/pos/detail') }}/${detailId}/note`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note: note })
        }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
    };

    // Cobro
    window.toggleCashInput = function(show) { document.getElementById('cashInputGroup').style.display = show ? 'block' : 'none'; }
    window.calculateChange = function() {
        var total = parseFloat(document.getElementById('hiddenTotal').value) || 0;
        var received = parseFloat(document.getElementById('receivedAmount').value) || 0;
        var el = document.getElementById('changeAmount');
        if(el) el.innerText = (received - total).toFixed(2);
    }
    // ============================================================
    // BUSCADOR PROFESIONAL DE CLIENTES
    // ============================================================

    window.openClientDropdown = function() {

        const dropdown =
            document.getElementById('clientDropdown');

        if (dropdown) {
            dropdown.classList.add('show');
        }
    };


    window.filterClientDropdown = function(value) {

    const dropdown =
        document.getElementById('clientDropdown');

    if (!dropdown) {
        return;
    }

    const search =
        (value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();

    if (search.length === 0) {
        dropdown.classList.remove('show');

        dropdown
            .querySelectorAll('.pos-client-option')
            .forEach(function(option) {
                option.style.display = 'flex';
            });

        const noResults =
            dropdown.querySelector('.pos-client-no-results');

        if (noResults) {
            noResults.style.display = 'none';
        }

        return;
    }

    dropdown.classList.add('show');

    let visible = 0;

    dropdown
        .querySelectorAll('.pos-client-option')
        .forEach(function(option) {

            const name =
                (option.dataset.name || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '');

            const documentNumber =
                (option.dataset.document || '')
                    .toLowerCase();

            const match =
                name.includes(search) ||
                documentNumber.includes(search);

            option.style.display =
                match ? 'flex' : 'none';

            if (match) {
                visible++;
            }
        });

    let noResults =
        dropdown.querySelector('.pos-client-no-results');

    if (visible === 0) {

        if (!noResults) {
            noResults = document.createElement('div');
            noResults.className = 'pos-client-no-results';
            noResults.innerHTML =
                '<i class="bi bi-search me-1"></i> No se encontraron clientes';

            dropdown.appendChild(noResults);
        }

        noResults.style.display = 'block';

    } else if (noResults) {

        noResults.style.display = 'none';
    }
};


    window.selectClient = function(option) {

        const input =
            document.getElementById(
                'clientSearchInput'
            );

        const clientId =
            document.getElementById(
                'clientId'
            );

        const clientDoc =
            document.getElementById(
                'clientDoc'
            );

        const dropdown =
            document.getElementById(
                'clientDropdown'
            );

        if (input) {
            input.value =
                option.dataset.name || '';
        }

        if (clientId) {
            clientId.value =
                option.dataset.id || '';
        }

        if (clientDoc) {
            clientDoc.value =
                option.dataset.document || '';
        }

        if (dropdown) {
            dropdown.classList.remove('show');
        }
    };


    window.clearClientSearch = function() {

        const input =
            document.getElementById(
                'clientSearchInput'
            );

        const clientId =
            document.getElementById(
                'clientId'
            );

        const clientDoc =
            document.getElementById(
                'clientDoc'
            );

        if (input) {
            input.value = '';
        }

        if (clientId) {
            clientId.value = '';
        }

        if (clientDoc) {
            clientDoc.value = '';
        }

        filterClientDropdown('');

        if (input) {
            input.focus();
        }
    };


    document.addEventListener(
        'click',
        function(event) {

            const wrapper =
                document.querySelector(
                    '.pos-client-search'
                );

            const dropdown =
                document.getElementById(
                    'clientDropdown'
                );

            if (
                wrapper &&
                dropdown &&
                !wrapper.contains(event.target)
            ) {
                dropdown.classList.remove('show');
            }
        }
    );

    window.openPaymentConfirm = function() {

        const payment = document.querySelector(
            'input[name="payment_method"]:checked'
        );

        const methodNames = {
            cash: 'Efectivo',
            card: 'Tarjeta',
            yape: 'Yape',
            plin: 'Plin'
        };

        const method = payment
            ? (methodNames[payment.value] || payment.value)
            : 'Método de pago';

        const totalInput = document.getElementById('hiddenTotal');
        const total = totalInput ? totalInput.value : '0.00';

        document.getElementById('confirmPaymentTotal').textContent =
            '{{ $currency ?? "S/" }}' + total;

        document.getElementById('confirmPaymentMethod').textContent =
            method;

        const modal = new bootstrap.Modal(
            document.getElementById('paymentConfirmModal')
        );

        modal.show();
    };


    window.submitConfirmedPayment = function() {

        const form = document.getElementById('checkoutForm');

        if (!form) {
            console.error('No se encontró checkoutForm');
            return;
        }

        const btn = document.querySelector(
            '#paymentConfirmModal .pos-confirm-pay-btn'
        );

        if (btn) {
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
        }

        form.submit();
    };

    window.toggleDocumentDropdown = function() {

        const dropdown =
            document.getElementById('documentTypeDropdown');

        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    };


    window.selectDocumentType = function(option) {

        const value =
            option.dataset.value || 'Ticket';

        const hidden =
            document.getElementById('documentTypeValue');

        const label =
            document.getElementById('documentTypeLabel');

        const dropdown =
            document.getElementById('documentTypeDropdown');

        if (hidden) {
            hidden.value = value;
        }

        if (label) {
            label.textContent = value;
        }

        const clientDoc =
            document.getElementById('clientDoc');

        if (clientDoc) {

            if (value === 'Boleta') {
                clientDoc.maxLength = 8;

                if (clientDoc.value.length > 8) {
                    clientDoc.value =
                        clientDoc.value.substring(0, 8);
                }

                clientDoc.placeholder = 'DNI';
            }
            else if (value === 'Factura') {
                clientDoc.maxLength = 11;

                if (clientDoc.value.length > 11) {
                    clientDoc.value =
                        clientDoc.value.substring(0, 11);
                }

                clientDoc.placeholder = 'RUC';
            }
            else {
                clientDoc.maxLength = 11;
                clientDoc.placeholder = 'DNI / RUC';
            }
        }

        document
            .querySelectorAll('.pos-document-option')
            .forEach(function(item) {
                item.classList.remove('active');
            });

        option.classList.add('active');

        if (dropdown) {
            dropdown.classList.remove('show');
        }
    };


    document.addEventListener('click', function(event) {

        const wrapper =
            document.querySelector(
                '.pos-document-dropdown-wrap'
            );

        const dropdown =
            document.getElementById(
                'documentTypeDropdown'
            );

        if (
            wrapper &&
            dropdown &&
            !wrapper.contains(event.target)
        ) {
            dropdown.classList.remove('show');
        }

    });

window.selectPaymentMethod = function(method) {

    const cashGroup =
        document.getElementById('cashInputGroup');

    const cashAmount =
        document.getElementById('cashPaymentAmount');

    const cardAmount =
        document.getElementById('cardPaymentAmount');

    const qrWrap =
        document.getElementById('digitalPaymentQr');

    const yapeBox =
        document.getElementById('yapeQrBox');

    const plinBox =
        document.getElementById('plinQrBox');

    const totalInput =
        document.getElementById('hiddenTotal');

    const total =
        totalInput
            ? parseFloat(totalInput.value || 0)
            : 0;

    const formatted =
        '{{ $currency ?? "S/" }}' + total.toFixed(2);

    const cashValue =
        document.getElementById('cashPaymentAmountValue');

    const cardValue =
        document.getElementById('cardPaymentAmountValue');

    const yapeValue =
        document.getElementById('yapeQrAmount');

    const plinValue =
        document.getElementById('plinQrAmount');

    if (cashValue) {
        cashValue.textContent = formatted;
    }

    if (cardValue) {
        cardValue.textContent = formatted;
    }

    if (yapeValue) {
        yapeValue.textContent = formatted;
    }

    if (plinValue) {
        plinValue.textContent = formatted;
    }

    if (cashGroup) {
        cashGroup.style.display =
            method === 'cash'
                ? 'block'
                : 'none';
    }

    if (cashAmount) {
        cashAmount.style.display =
            method === 'cash'
                ? 'block'
                : 'none';
    }

    if (cardAmount) {
        cardAmount.style.display =
            method === 'card'
                ? 'block'
                : 'none';
    }

    if (qrWrap) {
        qrWrap.style.display =
            (method === 'yape' || method === 'plin')
                ? 'block'
                : 'none';
    }

    if (yapeBox) {
        yapeBox.style.display =
            method === 'yape'
                ? 'block'
                : 'none';
    }

    if (plinBox) {
        plinBox.style.display =
            method === 'plin'
                ? 'block'
                : 'none';
    }
};



window.lookupClientByDocument = async function() {

    const docInput =
        document.getElementById('clientDoc');

    const nameInput =
        document.getElementById('clientSearchInput');

    const clientId =
        document.getElementById('clientId');

    const documentType =
        document.getElementById('documentTypeValue');

    if (!docInput || !nameInput || !documentType) {
        return;
    }

    const docNumber =
        docInput.value.replace(/\D/g, '');

    const type =
        documentType.value || 'Ticket';

    if (type === 'Ticket') {
        return;
    }

    const requiredLength =
        type === 'Factura' ? 11 : 8;

    if (docNumber.length !== requiredLength) {
        return;
    }

    nameInput.value = '';
    nameInput.placeholder = 'Consultando...';

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

            nameInput.value =
                data.client.name || '';

            if (clientId) {
                clientId.value =
                    data.client.id || '';
            }

            nameInput.placeholder =
                type === 'Factura'
                    ? 'Razón social'
                    : 'Nombre del cliente';

        } else {

            nameInput.value = '';

            if (clientId) {
                clientId.value = '';
            }

            nameInput.placeholder =
                data.message ||
                (type === 'Factura'
                    ? 'Razón social no encontrada'
                    : 'Nombre no encontrado');
        }

    } catch (error) {

        console.error(
            'Error consultando DNI/RUC:',
            error
        );

        nameInput.value = '';

        nameInput.placeholder =
            'No se pudo consultar el documento';
    }
};
</script>

<style>
    /* ── POS Product Cards ─────────────────────────────────────── */
    .pos-product-card {
        position: relative;
        border-radius: 16px;
        border: 2px solid var(--pal-border);
        background: #ffffff;
        cursor: pointer;
        overflow: hidden;
        transition: transform .18s, box-shadow .18s, border-color .18s;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        height: 100%;
        display: flex;
        flex-direction: column;
        user-select: none;
    }
    .pos-product-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 10px 28px rgba(0,0,0,0.14);
        border-color: var(--pal-icon);
    }
    .pos-product-card:active {
        transform: scale(0.97);
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    }

    /* Image / icon block */
    .pos-product-img-wrap {
        position: relative;
        width: 100%;
        height: 115px;
        flex-shrink: 0;
    }
    .pos-product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .pos-product-icon-block {
        width: 100%;
        height: 100%;
        background: var(--pal-bg);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pos-product-icon {
        font-size: 2.6rem;
        color: var(--pal-icon);
        opacity: .85;
    }

    /* Price badge */
    .pos-price-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--pal-icon);
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        letter-spacing: .02em;
    }

    /* Stock badge */
    .pos-stock-badge {
        position: absolute;
        bottom: 8px;
        left: 8px;
        font-size: .67rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        border: 2px solid #fff;
        box-shadow: 0 1px 6px rgba(0,0,0,0.15);
    }
    .pos-stock-ok  { background: #22c55e; color: #fff; }
    .pos-stock-low { background: #ef4444; color: #fff; animation: pulse-red 1.2s infinite; }
    @keyframes pulse-red {
        0%,100% { opacity: 1; } 50% { opacity: .65; }
    }

    /* Footer */
    .pos-product-footer {
        padding: 8px 10px 10px;
        text-align: center;
        border-top: 1.5px solid var(--pal-border);
        background: var(--pal-bg);
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .pos-product-name {
        font-size: .82rem;
        font-weight: 700;
        color: #1e1b3a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }
    .pos-product-cat {
        font-size: .65rem;
        color: var(--pal-icon);
        font-weight: 600;
        margin-top: 2px;
        opacity: .8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Hover overlay */
    .pos-add-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.38);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        opacity: 0;
        transition: opacity .18s;
        color: #fff;
        pointer-events: none;
        border-radius: 14px;
    }
    .pos-add-overlay i { font-size: 2rem; }
    .pos-add-overlay span { font-size: .8rem; font-weight: 700; letter-spacing: .06em; }
    .pos-product-card:hover .pos-add-overlay { opacity: 1; }

    /* =========================================================
       POS PEDIDOS - PALETA DINAMICA DEL SISTEMA
    ========================================================== */

    .pos-order-page {
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

    #pos-wrap.pos-order-page {
        background:
            color-mix(in srgb, var(--pos-primary) 5%, #ffffff) !important;
    }

    /* CATEGORIAS */

    .pos-order-page .category-btn {
        color: var(--pos-text);
        border-color: var(--pos-border);
        background: var(--pos-card);
        transition: all .18s ease;
    }

    .pos-order-page .category-btn:hover {
        color: var(--pos-primary);
        background:
            color-mix(in srgb, var(--pos-primary) 8%, #ffffff);
    }

    .pos-order-page .category-btn.active {
        color: #ffffff !important;
        background: var(--pos-primary) !important;
        border-color: var(--pos-primary) !important;
    }

    /* BUSCADOR */

    .pos-order-page .pos-search {
        border-radius: 12px;
    }

    .pos-order-page .pos-search .input-group-text,
    .pos-order-page .pos-search .form-control {
        background: var(--pos-card);
        border-color: var(--pos-border);
    }

    .pos-order-page .pos-search .input-group-text {
        color: var(--pos-primary);
        border-radius: 12px 0 0 12px;
    }

    .pos-order-page .pos-search .form-control {
        color: var(--pos-text);
        border-radius: 0 12px 12px 0;
    }

    .pos-order-page .pos-search:focus-within .input-group-text,
    .pos-order-page .pos-search:focus-within .form-control {
        border-color: var(--pos-primary);
    }

    .pos-order-page .pos-search .form-control:focus {
        box-shadow: none;
    }

    /* TARJETAS */

    .pos-order-page .pos-product-card {
        background: var(--pos-card);
        border-color:
            color-mix(
                in srgb,
                var(--pos-primary) 35%,
                var(--pos-border)
            );
    }

    .pos-order-page .pos-product-card:hover {
        border-color: var(--pos-primary);
        box-shadow: 0 10px 25px
            color-mix(in srgb, var(--pos-dark) 15%, transparent);
    }

    .pos-order-page .pos-product-name {
        color: var(--pos-text);
    }

    /* PRECIO */

    .pos-order-page .pos-price-badge {
        background: var(--pos-primary);
        color: #ffffff;
    }

    /* PIE DE PRODUCTO */

    .pos-order-page .pos-product-footer {
        background:
            color-mix(in srgb, var(--pos-primary) 7%, #ffffff);
        border-top-color:
            color-mix(in srgb, var(--pos-primary) 25%, var(--pos-border));
    }

    .pos-order-page .pos-product-cat {
        color: var(--pos-primary);
    }

    /* OVERLAY AGREGAR */

    .pos-order-page .pos-add-overlay {
        background:
            color-mix(in srgb, var(--pos-dark) 78%, transparent);
    }


    /* =========================================================
       PANEL DE CATEGORIAS POS
    ========================================================== */

    .pos-order-page .pos-categories-panel {
        width: 190px;
        min-width: 190px;
        flex-shrink: 0;
        background: var(--pos-card);
        border-right: 1px solid var(--pos-border);
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: var(--pos-primary) transparent;
    }

    .pos-order-page .pos-categories-panel::-webkit-scrollbar {
        width: 6px;
    }

    .pos-order-page .pos-categories-panel::-webkit-scrollbar-track {
        background: transparent;
    }

    .pos-order-page .pos-categories-panel::-webkit-scrollbar-thumb {
        background: var(--pos-primary);
        border-radius: 20px;
    }

    .pos-order-page .pos-categories-panel .category-btn {
        min-height: 82px;
        padding: 12px 10px !important;
        border-left: 0;
        border-right: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        white-space: normal;
        overflow: hidden;
    }

    .pos-order-page .pos-categories-panel .category-btn span {
        width: 100%;
        white-space: normal !important;
        overflow-wrap: break-word;
        word-break: normal;
        text-align: center;
        line-height: 1.15 !important;
        font-size: .82rem;
    }

    .pos-order-page .pos-categories-panel .category-btn > i {
        flex-shrink: 0;
        margin-bottom: 2px !important;
    }

    .pos-order-page .pos-categories-panel .category-btn img {
        width: 38px;
        height: calc(1.5em + .75rem + 2px);
        object-fit: cover;
        flex-shrink: 0;
    }

    @media (max-width: 1200px) {
        .pos-order-page .pos-categories-panel {
            width: 170px;
            min-width: 170px;
        }
    }


    /* =========================================================
       CORRECCION FINAL NOMBRES DE CATEGORIAS
    ========================================================== */

    .pos-order-page .pos-categories-panel .category-btn {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
        padding-left: 14px !important;
        padding-right: 14px !important;
        overflow: hidden !important;
    }

    .pos-order-page .pos-categories-panel .category-btn span {
        display: block !important;
        width: 100% !important;
        max-width: 150px;
        margin: 0 auto;
        padding: 0 4px;
        box-sizing: border-box;

        white-space: normal !important;
        overflow: visible !important;
        text-overflow: clip !important;

        word-break: normal !important;
        overflow-wrap: break-word !important;

        text-align: center !important;
        line-height: 1.2 !important;
        font-size: .78rem !important;
    }


    /* =========================================================
       MODALES POS - PALETA DINAMICA
    ========================================================== */

    .pos-modal-note {
        background: var(--accent-2, #16a34a) !important;
        color: #ffffff !important;
    }

    .pos-modal-move {
        background: var(--primary, #ff8c00) !important;
        color: #ffffff !important;
    }

    .pos-modal-options {
        background: var(--dark-bg, #063970) !important;
        color: #ffffff !important;
    }

    .pos-modal-checkout {
        background: var(--primary, #ff8c00) !important;
        color: #ffffff !important;
    }

    .pos-modal-note .modal-title,
    .pos-modal-move .modal-title,
    .pos-modal-options .modal-title,
    .pos-modal-checkout .modal-title {
        color: #ffffff !important;
    }

    .pos-modal-note .btn-close,
    .pos-modal-move .btn-close,
    .pos-modal-options .btn-close,
    .pos-modal-checkout .btn-close {
        filter: brightness(0) invert(1);
        opacity: .9;
    }

    .pos-modal-note .btn-close:hover,
    .pos-modal-move .btn-close:hover,
    .pos-modal-options .btn-close:hover,
    .pos-modal-checkout .btn-close:hover {
        opacity: 1;
    }


    /* =========================================================
       METODOS DE PAGO
    ========================================================== */

    .pos-payment-btn {
        border: 1px solid var(--pos-border, #dce7f1) !important;
        background: #ffffff !important;
        color: var(--pos-text, #172033) !important;
        padding: 10px 8px;
        border-radius: 10px;
        transition: all .18s ease;
    }

    .pos-payment-btn:hover {
        border-color: var(--primary, #ff8c00) !important;
        color: var(--primary, #ff8c00) !important;
        transform: translateY(-1px);
    }

    #payCash:checked + .pos-payment-cash {
        background: var(--accent-2, #16a34a) !important;
        border-color: var(--accent-2, #16a34a) !important;
        color: #ffffff !important;
    }

    #payCard:checked + .pos-payment-card {
        background: var(--primary, #ff8c00) !important;
        border-color: var(--accent-1, #0b84c6) !important;
        color: #ffffff !important;
    }

    #payYape:checked + .pos-payment-yape {
        background: var(--primary, #ff8c00) !important;
        border-color: var(--primary, #ff8c00) !important;
        color: #ffffff !important;
    }

    #payPlin:checked + .pos-payment-plin {
        background: var(--dark-bg, #063970) !important;
        border-color: var(--dark-bg, #063970) !important;
        color: #ffffff !important;
    }


    /* =========================================================
       COLORES YAPE Y PLIN
    ========================================================== */

    /* YAPE */
    .pos-payment-yape {
        color: #742284 !important;
        border-color: #742284 !important;
        background: #ffffff !important;
    }

    .pos-payment-yape:hover {
        background: #742284 !important;
        border-color: #742284 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(116, 34, 132, .20);
    }

    #payYape:checked + .pos-payment-yape {
        background: #742284 !important;
        border-color: #742284 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(116, 34, 132, .25);
    }


    /* PLIN */
    .pos-payment-plin {
        color: #00a884 !important;
        border-color: #00a884 !important;
        background: #ffffff !important;
    }

    .pos-payment-plin:hover {
        background: #00a884 !important;
        border-color: #00a884 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 168, 132, .20);
    }

    #payPlin:checked + .pos-payment-plin {
        background: #00a884 !important;
        border-color: #00a884 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 168, 132, .25);
    }

    /* =========================================================
       COLORES EFECTIVO Y TARJETA
    ========================================================== */

    /* EFECTIVO */
    .pos-payment-cash {
        color: #198754 !important;
        border-color: #198754 !important;
        background: #ffffff !important;
    }

    .pos-payment-cash:hover {
        background: #198754 !important;
        border-color: #198754 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(25, 135, 84, .20);
    }

    #payCash:checked + .pos-payment-cash {
        background: #198754 !important;
        border-color: #198754 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(25, 135, 84, .25);
    }


    /* TARJETA */
    .pos-payment-card {
        color: #0d6efd !important;
        border-color: #0d6efd !important;
        background: #ffffff !important;
    }

    .pos-payment-card:hover {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, .20);
    }

    #payCard:checked + .pos-payment-card {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(13, 110, 253, .25);
    }

    /* SELECT TICKET / BOLETA / FACTURA */

    .pos-document-wrap {
        width: 100%;
    }

    .pos-document-select {
        cursor: pointer;
        padding-right: 38px !important;
        background-image: none !important;
    }

    .pos-document-arrow {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--primary, #ff8c00);
        font-size: .85rem;
        z-index: 5;
    }

    .pos-document-select:focus {
        border-color: var(--primary, #ff8c00) !important;
        box-shadow: 0 0 0 .15rem
            color-mix(
                in srgb,
                var(--primary, #ff8c00) 18%,
                transparent
            ) !important;
    }

    /* =========================================================
       BUSCADOR PROFESIONAL DE CLIENTES
    ========================================================== */

    .pos-client-search {
        width: 100%;
    }

    .pos-client-search .input-group {
        position: relative;
        z-index: 2;
    }

    .pos-client-search-icon {
        background: #ffffff !important;
        border-color: var(--pos-border, #dce7f1) !important;
        color: var(--primary, #ff8c00) !important;
    }

    .pos-client-search .form-control {
        border-color: var(--pos-border, #dce7f1) !important;
        background: #ffffff;
    }

    .pos-client-search .form-control:focus {
        border-color: var(--primary, #ff8c00) !important;
        box-shadow: none !important;
    }

    .pos-client-clear {
        background: #ffffff !important;
        border: 1px solid var(--pos-border, #dce7f1) !important;
        color: var(--pos-muted, #64748b) !important;
    }

    .pos-client-clear:hover {
        color: #dc2626 !important;
        background: #fff1f2 !important;
    }


    /* DROPDOWN */

    .pos-client-dropdown {
        position: absolute;
        top: calc(100% + 7px);
        left: 0;
        right: 0;

        display: none;

        max-height: 260px;
        overflow-y: auto;
        overflow-x: hidden;

        background: #ffffff;

        border: 1px solid
            var(--pos-border, #dce7f1);

        border-radius: 12px;

        box-shadow:
            0 14px 35px
            rgba(15, 23, 42, .16);

        padding: 6px;

        z-index: 1100;
    }

    .pos-client-dropdown.show {
        display: block;
    }


    /* CLIENTE */

    .pos-client-option {
        width: 100%;

        display: flex;
        align-items: center;
        gap: 10px;

        padding: 9px 10px;

        border: 0;
        border-radius: 9px;

        background: transparent;

        text-align: left;

        transition:
            background .15s ease,
            transform .15s ease;
    }

    .pos-client-option:hover {
        background:
            color-mix(
                in srgb,
                var(--primary, #ff8c00) 8%,
                white
            );

        transform: translateX(2px);
    }


    /* AVATAR */

    .pos-client-avatar {
        width: 34px;
        height: 34px;

        border-radius: 9px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        background:
            color-mix(
                in srgb,
                var(--primary, #ff8c00) 12%,
                white
            );

        color:
            var(--primary, #ff8c00);
    }


    /* DATOS */

    .pos-client-info {
        min-width: 0;
        flex: 1;

        display: flex;
        flex-direction: column;
    }

    .pos-client-name {
        color: var(--pos-text, #172033);

        font-size: .82rem;
        font-weight: 700;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pos-client-document {
        color: var(--pos-muted, #64748b);

        font-size: .68rem;

        margin-top: 1px;
    }

    .pos-client-chevron {
        color: var(--pos-muted, #64748b);
        font-size: .72rem;
    }

    .pos-client-option:hover .pos-client-chevron {
        color: var(--primary, #ff8c00);
    }


    /* VACIO */

    .pos-client-empty,
    .pos-client-no-results {
        padding: 18px 12px;

        color: var(--pos-muted, #64748b);

        text-align: center;

        font-size: .78rem;
    }


    /* SCROLL */

    .pos-client-dropdown::-webkit-scrollbar {
        width: 5px;
    }

    .pos-client-dropdown::-webkit-scrollbar-track {
        background: transparent;
    }

    .pos-client-dropdown::-webkit-scrollbar-thumb {
        background:
            var(--primary, #ff8c00);

        border-radius: 20px;
    }

    /* Confirmación de pago */
    .pos-confirm-header {
        background: var(--primary, #ff8c00);
        color: #fff;
        border: 0;
    }

    .pos-confirm-header .modal-title {
        color: #fff !important;
    }

    .pos-confirm-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff4e6;
        color: var(--primary, #ff8c00);
        font-size: 1.8rem;
    }

    .pos-confirm-total {
        color: var(--primary, #ff8c00);
    }

    .pos-confirm-pay-btn {
        background: var(--primary, #ff8c00) !important;
        border-color: var(--primary, #ff8c00) !important;
        color: #fff !important;
    }

    .pos-confirm-pay-btn:hover {
        filter: brightness(.92);
        color: #fff !important;
    }

    /* =========================================================
       COBRAR VENTA Y CONFIRMAR PAGO - PALETA DEL SISTEMA
       ========================================================= */

    .pos-modal-checkout,
    .pos-confirm-header {
        background: var(--primary) !important;
        color: #ffffff !important;
        border: 0 !important;
    }

    .pos-modal-checkout .modal-title,
    .pos-confirm-header .modal-title,
    .pos-confirm-header .modal-title i {
        color: #ffffff !important;
    }

    .pos-confirm-icon {
        background: color-mix(
            in srgb,
            var(--primary) 14%,
            #ffffff
        ) !important;
        color: var(--primary) !important;
    }

    .pos-confirm-total {
        color: var(--primary) !important;
    }

    .pos-confirm-pay-btn {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
        box-shadow: none !important;
    }

    .pos-confirm-pay-btn:hover,
    .pos-confirm-pay-btn:focus,
    .pos-confirm-pay-btn:active {
        background: color-mix(
            in srgb,
            var(--primary) 82%,
            #000000
        ) !important;

        border-color: color-mix(
            in srgb,
            var(--primary) 82%,
            #000000
        ) !important;

        color: #ffffff !important;
        box-shadow: none !important;
    }

    /* CONFIRMAR PAGO - MODO OSCURO */
    html[data-color-mode="dark"] #paymentConfirmModal .modal-content,
    html[data-color-mode="dark"] #paymentConfirmModal .modal-body {
        background: #132338 !important;
        color: #f8fafc !important;
    }

    html[data-color-mode="dark"] #paymentConfirmModal .text-muted {
        color: #9db2c7 !important;
    }

    html[data-color-mode="dark"] #paymentConfirmModal .pos-confirm-icon {
        background: color-mix(
            in srgb,
            var(--primary) 18%,
            #132338
        ) !important;
        color: var(--primary) !important;
    }

    html[data-color-mode="dark"] #paymentConfirmModal .btn-light {
        background: #182b40 !important;
        border-color: #38516a !important;
        color: #f8fafc !important;
    }

    html[data-color-mode="dark"] #paymentConfirmModal .btn-light:hover {
        background: #20364d !important;
        border-color: #49637c !important;
        color: #ffffff !important;
    }
    /* =========================================================
       DESPLEGABLE PROFESIONAL TICKET / BOLETA / FACTURA
    ========================================================== */

    .pos-document-dropdown-wrap {
        width: 100%;
    }

    .pos-document-trigger {
        width: 100%;
        height: calc(1.5em + .75rem + 2px);

        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 0 12px;

        background: #ffffff;

        border: 1px solid
            var(--pos-border, #dce7f1);

        border-radius: 7px;

        color: var(--pos-text, #172033);

        font-size: .82rem;
        font-weight: 700;

        transition: all .15s ease;
    }

    .pos-document-trigger:hover,
    .pos-document-trigger:focus {
        border-color:
            var(--primary, #ff8c00);

        box-shadow:
            0 0 0 .15rem
            color-mix(
                in srgb,
                var(--primary, #ff8c00) 14%,
                transparent
            );
    }

    .pos-document-trigger i {
        color: var(--primary, #ff8c00);
        font-size: .75rem;
    }


    .pos-document-dropdown {
        position: absolute;

        top: calc(100% + 7px);
        right: 0;

        width: 270px;

        display: none;

        padding: 6px;

        background: #ffffff;

        border: 1px solid
            var(--pos-border, #dce7f1);

        border-radius: 12px;

        box-shadow:
            0 14px 35px
            rgba(15, 23, 42, .16);

        z-index: 1200;
    }

    .pos-document-dropdown.show {
        display: block;
    }


    .pos-document-option {
        width: 100%;

        display: flex;
        align-items: center;
        gap: 10px;

        padding: 9px 10px;

        border: 0;
        border-radius: 9px;

        background: transparent;

        text-align: left;

        transition:
            background .15s ease,
            transform .15s ease;
    }

    .pos-document-option:hover,
    .pos-document-option.active {
        background:
            color-mix(
                in srgb,
                var(--primary, #ff8c00) 8%,
                white
            );
    }

    .pos-document-option:hover {
        transform: translateX(2px);
    }


    .pos-document-option-icon {
        width: 34px;
        height: 34px;

        border-radius: 9px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        background:
            color-mix(
                in srgb,
                var(--primary, #ff8c00) 12%,
                white
            );

        color:
            var(--primary, #ff8c00);
    }


    .pos-document-option > span:nth-child(2) {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .pos-document-option strong {
        color: var(--pos-text, #172033);
        font-size: .80rem;
    }

    .pos-document-option small {
        color: var(--pos-muted, #64748b);
        font-size: .66rem;
        margin-top: 1px;
    }

    /* ALINEAR TICKET CON DNI/RUC */
    .pos-document-trigger {
        height: 38px !important;
        min-height: 38px !important;
        padding-top: .375rem !important;
        padding-bottom: .375rem !important;
        line-height: 1.5 !important;
    }

    /* ALINEACION EXACTA DOCUMENTO + DNI/RUC */
    #clientDoc,
    .pos-document-trigger {
        height: 38px !important;
        min-height: 38px !important;
        padding: .375rem .75rem !important;
        font-size: 1rem !important;
        line-height: 1.5 !important;
        border-radius: .375rem !important;
        box-sizing: border-box !important;
    }

    .pos-document-trigger {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

/* QR YAPE / PLIN */

.pos-digital-qr {
    text-align: center;
}

.pos-qr-box {
    padding: 14px;
    border: 1px solid var(--pos-border, #dce7f1);
    border-radius: 14px;
    background: #ffffff;
}

.pos-qr-title {
    font-weight: 800;
    margin-bottom: 10px;
}

.pos-qr-image {
    width: 190px;
    height: 190px;
    object-fit: contain;
    padding: 8px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
}

.pos-qr-empty {
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #64748b;
    background: #ffffff;
    border-radius: 12px;
}

.pos-qr-empty i {
    font-size: 2rem;
}

/* MONTO QR */

.pos-qr-amount {
    padding: 10px 12px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
}

.pos-qr-amount span {
    display: block;
    color: #64748b;
    font-size: .72rem;
    margin-bottom: 2px;
}

.pos-qr-amount strong {
    display: block;
    font-size: 1.35rem;
    font-weight: 800;
    color: #172033;
}

#yapeQrBox .pos-qr-amount strong {
    color: #742284;
}

#plinQrBox .pos-qr-amount strong {
    color: #00a884;
}

/* MONTO DE PAGOS DIGITALES */

.pos-digital-payment-amount {
    padding: 12px 14px;
    text-align: center;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.pos-digital-payment-amount span {
    display: block;
    color: #64748b;
    font-size: .72rem;
    margin-bottom: 2px;
}

.pos-digital-payment-amount strong {
    display: block;
    color: #172033;
    font-size: 1.45rem;
    font-weight: 800;
}

/* MONTOS SEGUN METODO DE PAGO */

.pos-method-amount,
.pos-qr-amount {
    padding: 11px 14px;
    border-radius: 12px;
    text-align: center;
    background: #ffffff;
}

.pos-method-amount span,
.pos-qr-amount span {
    display: block;
    font-size: .72rem;
    margin-bottom: 2px;
    color: #64748b;
}

.pos-method-amount strong,
.pos-qr-amount strong {
    display: block;
    font-size: 1.45rem;
    font-weight: 800;
}

/* TARJETA */
.pos-method-card {
    border: 1px solid #0d6efd;
    background: #eff6ff;
}

.pos-method-card strong {
    color: #0d6efd;
}

/* YAPE */
#yapeQrBox .pos-qr-amount {
    border: 1px solid #742284;
    background: #f8effa;
}

#yapeQrBox .pos-qr-amount strong {
    color: #742284;
}

/* PLIN */
#plinQrBox .pos-qr-amount {
    border: 1px solid #00a884;
    background: #effaf7;
}

#plinQrBox .pos-qr-amount strong {
    color: #00a884;
}

/* EFECTIVO */
.pos-method-cash {
    border: 1px solid #198754;
    background: #edf8f2;
}

.pos-method-cash strong {
    color: #198754;
}

/* EFECTIVO - RECIBIDO */
#cashInputGroup {
    padding: 12px;
    background: #edf8f2;
    border: 1px solid #198754;
    border-radius: 12px;
}

#cashInputGroup .form-label {
    color: #198754;
    font-weight: 700;
}

#receivedAmount {
    border: 2px solid #198754 !important;
    background: #ffffff !important;
    color: #198754 !important;
    border-radius: 10px;
    font-weight: 800 !important;
}

#receivedAmount:focus {
    border-color: #198754 !important;
    box-shadow: 0 0 0 .18rem rgba(25, 135, 84, .15) !important;
}

#changeAmount {
    color: #198754 !important;
    font-weight: 800 !important;
}

/* OPCIONES: DESCUENTO Y PROPINA */

#optionsModal .modal-content {
    border-radius: 16px;
    overflow: hidden;
}

#optionsModal .modal-body {
    padding: 18px;
}

.pos-options-total {
    padding: 12px;
    text-align: center;
    border-radius: 12px;
    background:
        color-mix(
            in srgb,
            var(--primary, #ff8c00) 8%,
            white
        );
    border: 1px solid
        color-mix(
            in srgb,
            var(--primary, #ff8c00) 25%,
            white
        );
}

.pos-options-total span {
    display: block;
    font-size: .72rem;
    color: #64748b;
}

.pos-options-total strong {
    display: block;
    margin-top: 2px;
    font-size: 1.35rem;
    color: var(--primary, #ff8c00);
}

#inputDiscount {
    border: 1px solid #dc3545;
}

#inputDiscount:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 .15rem rgba(220,53,69,.12);
}

#inputTip {
    border: 1px solid #198754;
}

#inputTip:focus {
    border-color: #198754;
    box-shadow: 0 0 0 .15rem rgba(25,135,84,.12);
}

#optionsModal .modal-footer .btn-primary {
    background: var(--primary, #ff8c00) !important;
    border-color: var(--primary, #ff8c00) !important;
}

/* AJUSTES - ELIMINAR AZUL */
#optionsModal .pos-modal-options {
    background: var(--primary, #ff8c00) !important;
    color: #ffffff !important;
}

#optionsModal .pos-modal-options .modal-title {
    color: #ffffff !important;
}

#optionsModal .pos-modal-options .btn-close {
    filter: brightness(0) invert(1);
}

#optionsModal .modal-footer .btn-primary {
    background: var(--primary, #ff8c00) !important;
    border-color: var(--primary, #ff8c00) !important;
    color: #ffffff !important;
}

#optionsModal .modal-footer .btn-primary:hover,
#optionsModal .modal-footer .btn-primary:focus {
    background: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    border-color: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    color: #ffffff !important;
}
</style>

<style>
/* DARK MODE - POS PRODUCTOS Y CUENTA FINAL */

/* Tarjeta completa */
html[data-color-mode="dark"] .pos-order-page .pos-product-card {
    background: #132338 !important;
    border-color: #38516b !important;
}

/* Parte inferior que actualmente aparece crema */
html[data-color-mode="dark"] .pos-order-page .pos-product-footer {
    background: #132338 !important;
    border-top: 1px solid #30465d !important;
}

/* Nombre del producto */
html[data-color-mode="dark"] .pos-order-page .pos-product-name {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}

/* Categoría: mantener su color */
html[data-color-mode="dark"] .pos-order-page .pos-product-cat {
    opacity: 1 !important;
}

/* Panel de Cuenta Actual */
html[data-color-mode="dark"] #cart-container {
    background: #132338 !important;
    color: #ffffff !important;
}

/* Eliminar cualquier zona clara dentro de la cuenta vacía */
html[data-color-mode="dark"] #cart-container > div {
    border-color: #30465d !important;
}

/* Texto Cuenta vacía */
html[data-color-mode="dark"] #cart-container .text-muted {
    color: #8fa6bd !important;
}

</style>


<style>
/* DARK MODE - CUENTA ACTUAL DEFINITIVO */

html[data-color-mode="dark"] .pos-cart-panel {
    background: #132338 !important;
    border-left-color: #30465d !important;
    box-shadow: -4px 0 20px rgba(0,0,0,.20) !important;
}

html[data-color-mode="dark"] .pos-cart-header {
    background: #101f32 !important;
    border-bottom-color: #30465d !important;
}

html[data-color-mode="dark"] .pos-cart-header h6,
html[data-color-mode="dark"] .pos-cart-header h6 i {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
}

/* Área completa del carrito */
html[data-color-mode="dark"] #cart-container {
    background: #132338 !important;
    color: #ffffff !important;
}

/* Elementos internos cuando la cuenta está vacía */
html[data-color-mode="dark"] #cart-container > * {
    background-color: transparent !important;
}

/* Texto secundario */
html[data-color-mode="dark"] #cart-container .text-muted {
    color: #8fa6bd !important;
}

/* Iconos secundarios */
html[data-color-mode="dark"] #cart-container i {
    color: #8fa6bd;
}

</style>


<style>
/* POS - CUENTA ACTUAL MODO CLARO */

/* Panel derecho */
html:not([data-color-mode="dark"]) .pos-cart-panel {
    background: #ffffff !important;
    border-left: 1px solid var(--border-soft) !important;
}

/* Cabecera Cuenta Actual */
html:not([data-color-mode="dark"]) .pos-cart-header {
    background: #ffffff !important;
    border-bottom: 1px solid var(--border-soft) !important;
}

/* Título e icono */
html:not([data-color-mode="dark"]) .pos-cart-header h6,
html:not([data-color-mode="dark"]) .pos-cart-header h6 i {
    color: var(--text-main) !important;
    -webkit-text-fill-color: var(--text-main) !important;
}

/* Cuerpo de la cuenta */
html:not([data-color-mode="dark"]) #cart-container {
    background: #ffffff !important;
    color: var(--text-main) !important;
}

/* Cuenta vacía */
html:not([data-color-mode="dark"]) #cart-container .text-muted {
    color: var(--text-muted) !important;
}

</style>


<style>
/* POS - BOTON VOLVER CON BORDE */

.pos-back-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;

    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;

    background: #ffffff !important;
    color: var(--text-main) !important;

    padding: 6px 11px !important;
    box-shadow: none !important;
}

.pos-back-btn i {
    color: inherit !important;
}

.pos-back-btn:hover {
    border-color: var(--primary) !important;
    background: color-mix(in srgb, var(--primary) 7%, white) !important;
    color: var(--primary) !important;
}


/* MODO OSCURO */

html[data-color-mode="dark"] .pos-back-btn {
    background: #132338 !important;
    border-color: #40566e !important;
    color: #b9cce0 !important;
}

html[data-color-mode="dark"] .pos-back-btn:hover {
    background: color-mix(in srgb, var(--primary) 12%, #132338) !important;
    border-color: var(--primary) !important;
    color: var(--primary) !important;
}

</style>


<style>
/* POS AJUSTES - MODO OSCURO DEFINITIVO */

/* =========================================
   CONTENEDOR DEL MODAL
   ========================================= */

html[data-color-mode="dark"] #optionsModal .modal-content {
    background: #122238 !important;
    background-color: #122238 !important;
    border: 1px solid #304861 !important;
    color: #ffffff !important;
    border-radius: 14px !important;
    overflow: hidden;
}


/* =========================================
   CABECERA
   ========================================= */

html[data-color-mode="dark"] #optionsModal .pos-modal-options {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] #optionsModal .pos-modal-options .modal-title {
    color: #ffffff !important;
}


/* Botón X */
html[data-color-mode="dark"] #optionsModal .pos-modal-options .btn-close {
    filter: brightness(0) invert(1) !important;
    opacity: 1 !important;
}


/* =========================================
   CUERPO
   ========================================= */

html[data-color-mode="dark"] #optionsModal .modal-body {
    background: #122238 !important;
    color: #ffffff !important;
}


/* =========================================
   TOTAL ACTUAL
   ========================================= */

html[data-color-mode="dark"] #optionsModal .pos-options-total {
    background: #182b40 !important;
    background-color: #182b40 !important;
    border: 1px solid #38516a !important;
    border-radius: 12px !important;
    color: #9db2c7 !important;
    padding: 14px !important;
}

html[data-color-mode="dark"] #optionsModal .pos-options-total span {
    color: #9db2c7 !important;
}

html[data-color-mode="dark"] #optionsModal .pos-options-total strong {
    color: var(--primary) !important;
    -webkit-text-fill-color: var(--primary) !important;
}


/* =========================================
   ETIQUETAS
   ========================================= */

html[data-color-mode="dark"] #optionsModal .form-label {
    color: #aebfd0 !important;
}

html[data-color-mode="dark"] #optionsModal .text-muted {
    color: #aebfd0 !important;
}


/* =========================================
   DESCUENTO Y PROPINА
   ========================================= */

html[data-color-mode="dark"] #optionsModal .form-control {
    background: #0d1b2b !important;
    background-color: #0d1b2b !important;
    border: 1px solid #38516a !important;
    color: #ffffff !important;
    border-radius: 10px !important;
    box-shadow: none !important;
    -webkit-text-fill-color: #ffffff !important;
}

html[data-color-mode="dark"] #optionsModal .form-control:focus {
    background: #102238 !important;
    background-color: #102238 !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(255, 140, 0, .12) !important;
}


/* =========================================
   FOOTER
   ========================================= */

html[data-color-mode="dark"] #optionsModal .modal-footer {
    background: #0d1b2b !important;
    border-top: 1px solid #304861 !important;
}


/* =========================================
   APLICAR CAMBIOS
   ========================================= */

html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary:hover {
    background: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    border-color: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    color: #ffffff !important;
}


/* =========================================
   PLACEHOLDER
   ========================================= */

html[data-color-mode="dark"] #optionsModal .form-control::placeholder {
    color: #71869b !important;
}

</style>

@endsection
<style>
.pos-move-table-btn {
    background: var(--primary) !important;
    border: 2px solid var(--primary) !important;
    color: #ffffff !important;
    border-radius: 9px !important;
    padding: 6px 12px !important;
    transition: all .18s ease;
}

.pos-move-table-btn:hover,
.pos-move-table-btn:focus {
    background: var(--primary-hover) !important;
    border-color: var(--primary-hover) !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 .15rem color-mix(in srgb, var(--primary) 18%, transparent) !important;
}

.pos-move-table-btn:active {
    transform: scale(.97);
}

    .pos-move-table-arrow { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-muted); font-size: 0.85rem; z-index: 5; }

/* =========================================================
   MOVER MESA - DISEÑO MODERNO / PALETA DEL SISTEMA
   ========================================================= */

.pos-move-table-btn {
    background: var(--primary) !important;
    border: 1px solid var(--primary) !important;
    color: #ffffff !important;
    border-radius: 12px !important;
    padding: 8px 14px !important;
    box-shadow: 0 3px 10px rgba(15, 23, 42, .12);
    transition: all .18s ease;
}

.pos-move-table-btn,
.pos-move-table-btn .bi,
.pos-move-table-btn .bi::before {
    color: #ffffff !important;
}

.pos-move-table-btn .bi,
.pos-move-table-btn .bi::before {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

.pos-move-table-btn:hover,
.pos-move-table-btn:focus {
    background: var(--primary-hover) !important;
    border-color: var(--primary-hover) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}


/* MODAL */

#moveTableModal .modal-content {
    border: 0 !important;
    border-radius: 16px !important;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 20px 50px rgba(15, 23, 42, .22) !important;
}

#moveTableModal .pos-modal-move {
    background: var(--primary) !important;
    color: #ffffff !important;
    border: 0 !important;
    padding: 16px 18px !important;
}

#moveTableModal .pos-modal-move .modal-title,
#moveTableModal .pos-modal-move .modal-title .bi,
#moveTableModal .pos-modal-move .modal-title .bi::before {
    color: #ffffff !important;
}

#moveTableModal .pos-modal-move .modal-title .bi,
#moveTableModal .pos-modal-move .modal-title .bi::before {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

#moveTableModal .pos-move-body {
    padding: 20px !important;
}

#moveTableModal .pos-move-label {
    display: block;
    margin-bottom: 8px;
    color: #334155;
    font-size: .82rem;
    font-weight: 700;
}

#moveTableModal .pos-move-table-select {
    min-height: 46px;
    padding: 10px 42px 10px 13px;
    border: 1px solid #d8e1eb !important;
    border-radius: 10px !important;
    background: #ffffff !important;
    color: #172033 !important;
    box-shadow: none !important;
}

#moveTableModal .pos-move-table-select:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px color-mix(
        in srgb,
        var(--primary) 18%,
        transparent
    ) !important;
}

#moveTableModal .pos-move-table-arrow {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: var(--primary) !important;
}

#moveTableModal .pos-move-footer {
    padding: 0 20px 20px !important;
    border: 0 !important;
    background: transparent !important;
}

#moveTableModal .pos-move-confirm {
    min-height: 44px;
    border: 1px solid var(--primary) !important;
    border-radius: 10px !important;
    background: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

#moveTableModal .pos-move-confirm,
#moveTableModal .pos-move-confirm .bi,
#moveTableModal .pos-move-confirm .bi::before {
    color: #ffffff !important;
}

#moveTableModal .pos-move-confirm:hover {
    background: var(--primary-hover) !important;
    border-color: var(--primary-hover) !important;
}


/* MODO OSCURO */

html[data-color-mode="dark"] #moveTableModal .modal-content {
    background: var(--dm-card, #101c2b) !important;
    border: 1px solid var(--dm-border, #2c4056) !important;
}

html[data-color-mode="dark"] #moveTableModal .pos-modal-move {
    background: var(--primary) !important;
}

html[data-color-mode="dark"] #moveTableModal .pos-move-body {
    background: var(--dm-card, #101c2b) !important;
}

html[data-color-mode="dark"] #moveTableModal .pos-move-label {
    color: #e5edf6 !important;
}

html[data-color-mode="dark"] #moveTableModal .pos-move-table-select {
    background: var(--dm-surface-2, #162438) !important;
    border-color: var(--dm-border, #344b63) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] #moveTableModal .pos-move-table-select option {
    background: #162438;
    color: #ffffff;
}

html[data-color-mode="dark"] #moveTableModal .pos-move-footer {
    background: var(--dm-card, #101c2b) !important;
}

/* AJUSTE FINAL - MODAL MOVER MESA */

#moveTableModal .pos-move-dialog {
    max-width: 430px !important;
}

#moveTableModal .modal-content {
    border-radius: 14px !important;
}

#moveTableModal .pos-modal-move {
    min-height: 56px !important;
    padding: 13px 18px !important;
    align-items: center !important;
}

#moveTableModal .pos-modal-move .modal-title {
    font-size: 1rem !important;
    line-height: 1.2 !important;
    display: flex !important;
    align-items: center !important;
}

#moveTableModal .pos-modal-move .modal-title .bi {
    font-size: 1rem !important;
}

#moveTableModal .pos-modal-move .btn-close {
    width: .8rem !important;
    height: .8rem !important;
    padding: .45rem !important;
    margin: 0 !important;
}

#moveTableModal .pos-move-body {
    padding: 20px 22px 16px !important;
}

#moveTableModal .pos-move-label {
    margin-bottom: 7px !important;
    font-size: .78rem !important;
    font-weight: 700 !important;
}

#moveTableModal .pos-move-table-select {
    min-height: 44px !important;
    height: 44px !important;
    padding: 8px 42px 8px 13px !important;
    border-radius: 9px !important;
    font-size: .92rem !important;
}

#moveTableModal .pos-move-table-arrow {
    right: 15px !important;
    font-size: .8rem !important;
}

#moveTableModal .pos-move-footer {
    padding: 0 22px 20px !important;
}

#moveTableModal .pos-move-confirm {
    min-height: 42px !important;
    height: 42px !important;
    border-radius: 9px !important;
    font-size: .88rem !important;
    padding: 7px 14px !important;
}

#moveTableModal .pos-move-confirm .bi {
    font-size: .9rem !important;
}

/* Quitar espacio inferior excesivo - Mover Mesa */
#moveTableModal .modal-content {
    height: auto !important;
    min-height: 0 !important;
}

#moveTableModal form {
    margin: 0 !important;
    padding: 0 !important;
}

#moveTableModal .pos-move-body {
    padding-bottom: 14px !important;
}

#moveTableModal .pos-move-footer {
    min-height: 0 !important;
    height: auto !important;
    margin: 0 !important;
    padding: 0 22px 16px !important;
}

#moveTableModal .pos-move-confirm {
    margin: 0 !important;
}

/* APLICAR CAMBIOS - HOVER DERIVADO DE LA PALETA ACTIVA */
#optionsModal .modal-footer .btn-primary:hover,
#optionsModal .modal-footer .btn-primary:focus,
#optionsModal .modal-footer .btn-primary:active,
html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary:hover,
html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary:focus,
html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary:active {
    background: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    background-color: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    border-color: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

/* AJUSTES - ELIMINAR RESPLANDOR DEL BOTON */
#optionsModal .modal-footer .btn-primary,
#optionsModal .modal-footer .btn-primary:hover,
#optionsModal .modal-footer .btn-primary:focus,
#optionsModal .modal-footer .btn-primary:active,
html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary,
html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary:hover,
html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary:focus,
html[data-color-mode="dark"] #optionsModal .modal-footer .btn-primary:active {
    box-shadow: none !important;
    outline: none !important;
}
/* COBRAR VENTA - BOTON PRINCIPAL */
#checkoutModal .pos-checkout-confirm-btn {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

#checkoutModal .pos-checkout-confirm-btn:hover,
#checkoutModal .pos-checkout-confirm-btn:focus,
#checkoutModal .pos-checkout-confirm-btn:active {
    background: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    border-color: color-mix(in srgb, var(--primary) 82%, #000000) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

/* METODOS DE PAGO - MISMO DISEÑO QUE DIVIDIR CUENTA */
#checkoutModal .pos-payment-cash {
    background: #eef9f3 !important;
    border: 1.5px solid #72c49a !important;
    color: #087a4b !important;
}

#checkoutModal #payCash:checked + .pos-payment-cash {
    background: #ccebdc !important;
    border: 2px solid #07965c !important;
    color: #05683f !important;
    box-shadow: 0 3px 9px rgba(7,150,92,.14) !important;
}

#checkoutModal .pos-payment-card {
    background: #f0f6fd !important;
    border: 1.5px solid #7eafe2 !important;
    color: #1763ad !important;
}

#checkoutModal #payCard:checked + .pos-payment-card {
    background: #d5e7f9 !important;
    border: 2px solid #347fc6 !important;
    color: #165b9b !important;
    box-shadow: 0 3px 9px rgba(52,127,198,.14) !important;
}

#checkoutModal .pos-payment-yape {
    background: #faf2fc !important;
    border: 1.5px solid #c989d3 !important;
    color: #812b91 !important;
}

#checkoutModal #payYape:checked + .pos-payment-yape {
    background: #edd5f2 !important;
    border: 2px solid #9b47aa !important;
    color: #70247d !important;
    box-shadow: 0 3px 9px rgba(155,71,170,.14) !important;
}

#checkoutModal .pos-payment-plin {
    background: #eefaf8 !important;
    border: 1.5px solid #68c0b4 !important;
    color: #087c6d !important;
}

#checkoutModal #payPlin:checked + .pos-payment-plin {
    background: #d0eee9 !important;
    border: 2px solid #159687 !important;
    color: #076d62 !important;
    box-shadow: 0 3px 9px rgba(21,150,135,.14) !important;
}

#checkoutModal .pos-payment-btn {
    font-weight: 700 !important;
}

/* COBRAR VENTA - EFECTIVO EN MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal .pos-cash-box {
    background: #162a2b !important;
    border-color: #198754 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-cash-box label,
html[data-color-mode="dark"] #checkoutModal .pos-cash-box .text-muted {
    color: #a9bdc9 !important;
}

html[data-color-mode="dark"] #checkoutModal #receivedAmount {
    background: #102033 !important;
    border-color: #198754 !important;
    color: #38c77a !important;
}

html[data-color-mode="dark"] #checkoutModal #changeAmount {
    color: #38c77a !important;
}

/* EFECTIVO - MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal #cashInputGroup {
    background: #162a2b !important;
    border-color: #198754 !important;
}

html[data-color-mode="dark"] #checkoutModal #cashInputGroup .form-label {
    color: #7dd9a5 !important;
}

html[data-color-mode="dark"] #checkoutModal #receivedAmount {
    background: #102033 !important;
    border-color: #198754 !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] #checkoutModal #changeAmount {
    color: #4ade80 !important;
}

/* METODOS DE PAGO - MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal .pos-payment-cash {
    background: #12352d !important;
    border-color: #28a66d !important;
    color: #6ee7a8 !important;
}

html[data-color-mode="dark"] #checkoutModal #payCash:checked + .pos-payment-cash {
    background: #164c3b !important;
    border-color: #35c883 !important;
    color: #8af0ba !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-payment-card {
    background: #162d48 !important;
    border-color: #438bcf !important;
    color: #78b9f4 !important;
}

html[data-color-mode="dark"] #checkoutModal #payCard:checked + .pos-payment-card {
    background: #1b3d61 !important;
    border-color: #5aa5e8 !important;
    color: #9acbfa !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-payment-yape {
    background: #33213c !important;
    border-color: #a75bb5 !important;
    color: #d99be4 !important;
}

html[data-color-mode="dark"] #checkoutModal #payYape:checked + .pos-payment-yape {
    background: #482951 !important;
    border-color: #c06dce !important;
    color: #e8b3f0 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-payment-plin {
    background: #123b3a !important;
    border-color: #35a99d !important;
    color: #6edbd0 !important;
}

html[data-color-mode="dark"] #checkoutModal #payPlin:checked + .pos-payment-plin {
    background: #164b47 !important;
    border-color: #49c4b7 !important;
    color: #8ae9df !important;
}

/* METODOS DE PAGO - MODO OSCURO SOBRIO */
html[data-color-mode="dark"] #checkoutModal #payCash:checked + .pos-payment-cash {
    background: #164536 !important;
    border: 2px solid #38c982 !important;
    color: #7cebb1 !important;
    box-shadow: 0 0 0 2px rgba(56,201,130,.12) !important;
}

html[data-color-mode="dark"] #checkoutModal #payCard:checked + .pos-payment-card {
    background: #1b3a5a !important;
    border: 2px solid #5aa4e8 !important;
    color: #8bc6ff !important;
    box-shadow: 0 0 0 2px rgba(90,164,232,.12) !important;
}

html[data-color-mode="dark"] #checkoutModal #payYape:checked + .pos-payment-yape {
    background: #42294a !important;
    border: 2px solid #b968c7 !important;
    color: #dda1e7 !important;
    box-shadow: 0 0 0 2px rgba(185,104,199,.12) !important;
}

html[data-color-mode="dark"] #checkoutModal #payPlin:checked + .pos-payment-plin {
    background: #164642 !important;
    border: 2px solid #3bc5b4 !important;
    color: #82e5da !important;
    box-shadow: 0 0 0 2px rgba(59,197,180,.12) !important;
}


/* QR - MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal .pos-qr-box {
    background: #132338 !important;
    border: 1px solid #2b4058 !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-qr-image {
    background: #ffffff !important;
    padding: 8px !important;
    border-radius: 10px !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-qr-amount {
    background: #102033 !important;
    border-color: #31465e !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-qr-amount span {
    color: #91a5bb !important;
}

/* COBRAR VENTA - AJUSTE FINAL MODO OSCURO */

/* Tarjeta - monto a pagar */
html[data-color-mode="dark"] #checkoutModal .pos-method-card {
    background: #162d48 !important;
    border: 1.5px solid #438bcf !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-method-card span {
    color: #91a5bb !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-method-card strong {
    color: #60a5fa !important;
}

/* Seleccionados */
html[data-color-mode="dark"] #checkoutModal #payCash:checked + .pos-payment-cash {
    background: #176044 !important;
    border: 2px solid #4ade80 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(74,222,128,.18) !important;
}

html[data-color-mode="dark"] #checkoutModal #payCard:checked + .pos-payment-card {
    background: #245b91 !important;
    border: 2px solid #60a5fa !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(96,165,250,.18) !important;
}

html[data-color-mode="dark"] #checkoutModal #payYape:checked + .pos-payment-yape {
    background: #673473 !important;
    border: 2px solid #d17cde !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(209,124,222,.18) !important;
}

html[data-color-mode="dark"] #checkoutModal #payPlin:checked + .pos-payment-plin {
    background: #176b61 !important;
    border: 2px solid #45d6c3 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(69,214,195,.18) !important;
}

/* YAPE Y PLIN - MONTO A PAGAR EN MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount {
    background: #2b1831 !important;
    border: 1.5px solid #9b47aa !important;
}

html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount span {
    color: #c79acf !important;
}

html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount strong {
    color: #b13bc3 !important;
}

html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount {
    background: #123532 !important;
    border: 1.5px solid #159687 !important;
}

html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount span {
    color: #8fcac2 !important;
}

html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount strong {
    color: #20bba9 !important;
}

/* BUSCADOR Y DOCUMENTO - MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal .pos-client-clear {
    background: #16263a !important;
    border-color: #30465f !important;
    color: #8fa6bd !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-client-clear:hover {
    background: #1d3047 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-trigger {
    background: #16263a !important;
    border-color: #30465f !important;
    color: #d7e2ee !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-trigger:hover,
html[data-color-mode="dark"] #checkoutModal .pos-document-trigger:focus {
    background: #1d3047 !important;
    border-color: var(--primary) !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-trigger .bi-chevron-down {
    color: var(--primary) !important;
}

/* DESPLEGABLES COBRAR VENTA - MODO OSCURO */

/* Buscar cliente */
html[data-color-mode="dark"] #checkoutModal .pos-client-dropdown {
    background: #132338 !important;
    border: 1px solid #30465f !important;
    box-shadow: 0 12px 28px rgba(0,0,0,.30) !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-client-option {
    background: transparent !important;
    color: #dce7f2 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-client-option:hover {
    background: #1d3047 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-client-name {
    color: #e7eef7 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-client-avatar {
    background: color-mix(in srgb, var(--primary) 14%, #132338) !important;
    color: var(--primary) !important;
}


/* Ticket / Boleta / Factura */
html[data-color-mode="dark"] #checkoutModal .pos-document-dropdown {
    background: #132338 !important;
    border: 1px solid #30465f !important;
    box-shadow: 0 12px 28px rgba(0,0,0,.30) !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-option {
    background: transparent !important;
    color: #dce7f2 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-option:hover {
    background: #1d3047 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-option.active {
    background: color-mix(in srgb, var(--primary) 12%, #132338) !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-option strong {
    color: #e7eef7 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-document-option-icon {
    background: color-mix(in srgb, var(--primary) 14%, #132338) !important;
    color: var(--primary) !important;
}

/* CABEZALES COBRAR VENTA Y CONFIRMAR PAGO */
#checkoutModal .pos-modal-checkout,
#paymentConfirmModal .pos-confirm-header {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
}

#checkoutModal .pos-modal-checkout .modal-title,
#checkoutModal .pos-modal-checkout .bi,
#paymentConfirmModal .pos-confirm-header .modal-title,
#paymentConfirmModal .pos-confirm-header .bi {
    color: #ffffff !important;
}

#checkoutModal .pos-modal-checkout .btn-close,
#paymentConfirmModal .pos-confirm-header .btn-close {
    filter: brightness(0) invert(1) !important;
    opacity: .9 !important;
}

/* COLORES DE TEXTO SEGUN METODO DE PAGO */

/* EFECTIVO */
#checkoutModal #cashInputGroup .form-label,
#checkoutModal #receivedAmount,
#checkoutModal #changeAmount {
    color: #198754 !important;
}

/* TARJETA */
#checkoutModal .pos-method-card span,
#checkoutModal .pos-method-card strong {
    color: #0d6efd !important;
}

/* YAPE */
#checkoutModal #yapeQrBox .pos-qr-title,
#checkoutModal #yapeQrBox .pos-qr-amount span,
#checkoutModal #yapeQrBox .pos-qr-amount strong {
    color: #9b27a8 !important;
}

/* PLIN */
#checkoutModal #plinQrBox .pos-qr-title,
#checkoutModal #plinQrBox .pos-qr-amount span,
#checkoutModal #plinQrBox .pos-qr-amount strong {
    color: #00a995 !important;
}

/* MODO OSCURO - VERSION MAS CLARA DE CADA COLOR */
html[data-color-mode="dark"] #checkoutModal #cashInputGroup .form-label,
html[data-color-mode="dark"] #checkoutModal #receivedAmount,
html[data-color-mode="dark"] #checkoutModal #changeAmount {
    color: #4ade80 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-method-card span,
html[data-color-mode="dark"] #checkoutModal .pos-method-card strong {
    color: #60a5fa !important;
}

html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-title,
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount span,
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount strong {
    color: #d17cde !important;
}

html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-title,
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount span,
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount strong {
    color: #45d6c3 !important;
}

/* METODOS DE PAGO - NO SELECCIONADOS EN MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal .pos-payment-cash {
    background: transparent !important;
    border: 1.5px solid #28a66d !important;
    color: #6ee7a8 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-payment-card {
    background: transparent !important;
    border: 1.5px solid #438bcf !important;
    color: #78b9f4 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-payment-yape {
    background: transparent !important;
    border: 1.5px solid #a75bb5 !important;
    color: #d99be4 !important;
}

html[data-color-mode="dark"] #checkoutModal .pos-payment-plin {
    background: transparent !important;
    border: 1.5px solid #35a99d !important;
    color: #6edbd0 !important;
}

/* EFECTIVO - TEXTO BLANCO EN MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal #cashInputGroup .form-label,
html[data-color-mode="dark"] #checkoutModal #cashInputGroup #receivedAmount,
html[data-color-mode="dark"] #checkoutModal #cashInputGroup #changeAmount,
html[data-color-mode="dark"] #checkoutModal #cashInputGroup strong,
html[data-color-mode="dark"] #checkoutModal #cashInputGroup span {
    color: #ffffff !important;
}

/* TARJETA, YAPE Y PLIN - TEXTO BLANCO EN MODO OSCURO */

/* TARJETA */
html[data-color-mode="dark"] #checkoutModal .pos-method-card,
html[data-color-mode="dark"] #checkoutModal .pos-method-card span,
html[data-color-mode="dark"] #checkoutModal .pos-method-card strong {
    color: #ffffff !important;
}

/* YAPE */
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-title,
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-title *,
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount,
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount span,
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount strong {
    color: #ffffff !important;
}

/* PLIN */
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-title,
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-title *,
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount,
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount span,
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount strong {
    color: #ffffff !important;
}

/* COBRAR VENTA - YAPE Y PLIN SOLO BORDES */

/* YAPE - MODO CLARO */
html:not([data-color-mode="dark"]) #checkoutModal #yapeQrBox {
    background: #ffffff !important;
    border-color: #9b47aa !important;
}

html:not([data-color-mode="dark"]) #checkoutModal #yapeQrBox .pos-qr-amount {
    background: #ffffff !important;
    border: 1.5px solid #9b47aa !important;
}

/* PLIN - MODO CLARO */
html:not([data-color-mode="dark"]) #checkoutModal #plinQrBox {
    background: #ffffff !important;
    border-color: #159687 !important;
}

html:not([data-color-mode="dark"]) #checkoutModal #plinQrBox .pos-qr-amount {
    background: #ffffff !important;
    border: 1.5px solid #159687 !important;
}


/* YAPE - MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal #yapeQrBox {
    background: #132338 !important;
    border-color: #b968c7 !important;
}

html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount {
    background: #102033 !important;
    border: 1.5px solid #b968c7 !important;
}

/* PLIN - MODO OSCURO */
html[data-color-mode="dark"] #checkoutModal #plinQrBox {
    background: #132338 !important;
    border-color: #3bc5b4 !important;
}

html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount {
    background: #102033 !important;
    border: 1.5px solid #3bc5b4 !important;
}

/* MONTO A PAGAR - FONDO SEGUN METODO */

/* YAPE - CLARO */
html:not([data-color-mode="dark"]) #checkoutModal #yapeQrBox .pos-qr-amount {
    background: #faf2fc !important;
    border: 1.5px solid #9b47aa !important;
}

/* PLIN - CLARO */
html:not([data-color-mode="dark"]) #checkoutModal #plinQrBox .pos-qr-amount {
    background: #eefaf8 !important;
    border: 1.5px solid #159687 !important;
}

/* YAPE - OSCURO */
html[data-color-mode="dark"] #checkoutModal #yapeQrBox .pos-qr-amount {
    background: #2b1831 !important;
    border: 1.5px solid #b968c7 !important;
}

/* PLIN - OSCURO */
html[data-color-mode="dark"] #checkoutModal #plinQrBox .pos-qr-amount {
    background: #123532 !important;
    border: 1.5px solid #3bc5b4 !important;
}

/* EFECTIVO - ESTRUCTURA CORRECTA MODO OSCURO */

/* Contenedor exterior: neutro + borde verde */
html[data-color-mode="dark"] #checkoutModal #cashInputGroup {
    background: #132338 !important;
    border: 1.5px solid #28a66d !important;
}

/* Campo Recibido: fondo verde */
html[data-color-mode="dark"] #checkoutModal #receivedAmount {
    background: #12352d !important;
    border: 1.5px solid #28a66d !important;
    color: #ffffff !important;
}

/* Textos y números blancos */
html[data-color-mode="dark"] #checkoutModal #cashInputGroup .form-label,
html[data-color-mode="dark"] #checkoutModal #changeAmount {
    color: #ffffff !important;
}

/* EFECTIVO - ESTRUCTURA CORRECTA MODO CLARO */

/* Contenedor exterior: neutro + borde verde */
html:not([data-color-mode="dark"]) #checkoutModal #cashInputGroup {
    background: #ffffff !important;
    border: 1.5px solid #198754 !important;
}

/* Campo Recibido: fondo verde suave */
html:not([data-color-mode="dark"]) #checkoutModal #receivedAmount {
    background: #eef9f3 !important;
    border: 1.5px solid #198754 !important;
    color: #087a4b !important;
}

/* Textos */
html:not([data-color-mode="dark"]) #checkoutModal #cashInputGroup .form-label {
    color: #111827 !important;
}

html:not([data-color-mode="dark"]) #checkoutModal #changeAmount {
    color: #087a4b !important;
}

/* COBRAR VENTA - METODO DE PAGO MODO CLARO */
/* NO SELECCIONADOS: SOLO BORDE */

html:not([data-color-mode="dark"]) #checkoutModal #payCash:not(:checked) + .pos-payment-cash {
    background: transparent !important;
    border: 1.5px solid #72c49a !important;
    color: #087a4b !important;
}

html:not([data-color-mode="dark"]) #checkoutModal #payCard:not(:checked) + .pos-payment-card {
    background: transparent !important;
    border: 1.5px solid #7eafe2 !important;
    color: #1763ad !important;
}

html:not([data-color-mode="dark"]) #checkoutModal #payYape:not(:checked) + .pos-payment-yape {
    background: transparent !important;
    border: 1.5px solid #c989d3 !important;
    color: #812b91 !important;
}

html:not([data-color-mode="dark"]) #checkoutModal #payPlin:not(:checked) + .pos-payment-plin {
    background: transparent !important;
    border: 1.5px solid #68c0b4 !important;
    color: #087c6d !important;
}
</style>



