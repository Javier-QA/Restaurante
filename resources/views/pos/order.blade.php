@extends('layouts.app')

@section('content')

<div class="container-fluid p-0 d-flex flex-column"
     style="height: calc(100vh - 20px); overflow: hidden;">

    {{-- =========================================================
         CABECERA
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center bg-white border-bottom px-4 py-2 mb-3 shadow-sm flex-shrink-0">

        <div class="d-flex align-items-center">

            <a href="{{ route('pos.index') }}"
               class="btn btn-outline-secondary me-3">

                <i class="bi bi-arrow-left"></i>
                Volver

            </a>

            <div>

                <h5 class="fw-bold mb-0 text-primary">
                    Mesa: {{ $table->name }}
                </h5>

                <small class="text-muted">
                    Zona: {{ $table->area->name }}
                </small>

            </div>

        </div>

        <div class="d-flex align-items-center gap-2">

            @if($order)

                <button type="button"
                        class="btn btn-outline-primary btn-sm fw-bold shadow-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#moveTableModal">

                    <i class="bi bi-arrow-left-right me-1"></i>
                    Mover Mesa

                </button>

            @endif

            <div class="vr mx-2"></div>

            <span class="badge bg-light text-dark border p-2">

                <i class="bi bi-person-fill me-1"></i>
                {{ auth()->user()->name }}

            </span>

        </div>

    </div>


    {{-- =========================================================
         CONTENIDO PRINCIPAL
    ========================================================== --}}

    <div class="row g-0 flex-grow-1 overflow-hidden">


        {{-- =====================================================
             CATEGORÍAS
        ====================================================== --}}

        <div class="col-md-2 bg-light border-end overflow-auto h-100 pb-5">

            <div class="list-group list-group-flush">

                <button onclick="filterProducts('all')"
                        class="list-group-item list-group-item-action active text-center py-3 category-btn"
                        id="cat-btn-all">

                    <i class="bi bi-grid-fill d-block fs-4 mb-1"></i>
                    Todo

                </button>

                @foreach($categories as $category)

                    <button onclick="filterProducts('cat-{{ $category->id }}')"
                            class="list-group-item list-group-item-action text-center py-3 category-btn"
                            id="cat-btn-{{ $category->id }}">

                        @if($category->image)

                            <img src="{{ asset('storage/'.$category->image) }}"
                                 class="rounded mb-1"
                                 width="40"
                                 height="40"
                                 style="object-fit: cover;">

                        @else

                            <i class="bi bi-tag d-block fs-4 mb-1"></i>

                        @endif

                        <span class="d-block small fw-bold lh-sm">
                            {{ $category->name }}
                        </span>

                    </button>

                @endforeach

            </div>

        </div>


        {{-- =====================================================
             PRODUCTOS
        ====================================================== --}}

        <div class="col-md-7 bg-white overflow-auto h-100 px-3 pb-5"
             id="products-container">

            <div class="position-relative mb-3 pt-2">

                <div class="input-group input-group-lg shadow-sm">

                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-primary"></i>
                    </span>

                    <input type="text"
                           id="productSearchInput"
                           class="form-control"
                           placeholder="Buscar producto por nombre o categoría..."
                           autocomplete="off">

                    <button type="button"
                            id="clearProductSearch"
                            class="btn btn-light border"
                            style="display: none;">

                        <i class="bi bi-x-lg"></i>

                    </button>

                </div>

                <div id="productSearchResults"
                     class="position-absolute bg-white border rounded shadow-lg w-100 mt-1"
                     style="display: none; z-index: 1050; max-height: 400px; overflow-y: auto;">
                </div>

            </div>


            {{-- GRID DE PRODUCTOS --}}

            <div class="row row-cols-2 row-cols-lg-3 row-cols-xl-4 g-3 pb-5"
                 id="products-grid">

                @foreach($categories as $category)

                    @foreach($category->products as $product)

                        <div class="col product-item cat-{{ $category->id }}"
                             data-product-name="{{ $product->name }}"
                             data-product-id="{{ $product->id }}"
                             data-product-price="{{ $product->price }}"
                             data-product-stock="{{ $product->stock }}"
                             data-product-category="{{ $category->name }}"
                             data-product-image="{{ $product->image ? asset('storage/'.$product->image) : '' }}">

                            <div class="card h-100 border-0 shadow-sm product-card"
                                 onclick="addToOrder({{ $product->id }})"
                                 style="cursor: pointer; transition: transform 0.1s;">

                                <div class="position-relative">

                                    @if($product->image)

                                        <img src="{{ asset('storage/'.$product->image) }}"
                                             class="card-img-top"
                                             style="height: 120px; object-fit: cover;">

                                    @else

                                        <div class="bg-light d-flex justify-content-center align-items-center"
                                             style="height: 120px;">

                                            <i class="bi bi-cup-straw fs-1 text-muted opacity-25"></i>

                                        </div>

                                    @endif


                                    <div class="position-absolute top-0 end-0 m-2">

                                        <span class="badge bg-dark opacity-75">

                                            {{ $currency ?? 'S/' }}{{ number_format($product->price, 0) }}

                                        </span>

                                    </div>


                                    @if(!is_null($product->stock))

                                        <div class="position-absolute bottom-0 start-0 m-2">

                                            <span class="badge {{ $product->stock <= 5 ? 'bg-danger' : 'bg-success' }} border border-white shadow-sm">

                                                Stock: {{ $product->stock }}

                                            </span>

                                        </div>

                                    @endif

                                </div>


                                <div class="card-body p-2 text-center">

                                    <h6 class="card-title fs-6 mb-0 text-truncate">

                                        {{ $product->name }}

                                    </h6>

                                </div>

                            </div>

                        </div>

                    @endforeach

                @endforeach

            </div>


            <div id="noSearchResults"
                 class="text-center py-5 text-muted"
                 style="display: none;">

                <i class="bi bi-search fs-1 d-block mb-2 opacity-50"></i>

                <h6 class="fw-bold">
                    No se encontraron productos
                </h6>

                <small>
                    Intenta buscar con otro nombre o categoría.
                </small>

            </div>

        </div>


        {{-- =====================================================
             CARRITO
        ====================================================== --}}

        <div class="col-md-3 bg-white border-start h-100 d-flex flex-column">

            <div class="p-3 bg-light border-bottom flex-shrink-0">

                <h6 class="fw-bold mb-0">

                    <i class="bi bi-cart"></i>
                    Cuenta Actual

                </h6>

            </div>

            <div id="cart-container"
                 class="flex-grow-1 d-flex flex-column overflow-hidden">

                @include('pos.partials.cart', [
                    'order' => $order
                ])

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL NOTA
========================================================== --}}

<div class="modal fade"
     id="noteModal"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-sm modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header py-2 bg-warning">

                <h6 class="modal-title fw-bold text-dark">
                    Nota Cocina
                </h6>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <input type="hidden"
                       id="noteDetailId">

                <textarea id="noteText"
                          class="form-control"
                          rows="3"></textarea>

            </div>

            <div class="modal-footer p-1">

                <button type="button"
                        class="btn btn-warning w-100 btn-sm text-dark fw-bold"
                        onclick="saveNote()">

                    Guardar Nota

                </button>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL MOVER MESA
========================================================== --}}

<div class="modal fade"
     id="moveTableModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-sm modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header py-2 bg-info text-white">

                <h6 class="modal-title fw-bold">
                    Mover Mesa
                </h6>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            @if($order)

                <form action="{{ route('pos.move', $order->id) }}"
                      method="POST">

                    @csrf

                    <div class="modal-body">

                        <label class="form-label small text-muted">
                            Destino:
                        </label>

                        <select name="target_table_id"
                                class="form-select"
                                required>

                            <option value=""
                                    selected
                                    disabled>

                                -- Elegir Mesa --

                            </option>

                            @foreach($freeTables as $ft)

                                <option value="{{ $ft->id }}">

                                    {{ $ft->name }}
                                    ({{ $ft->area->name }})

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="modal-footer p-1">

                        <button type="submit"
                                class="btn btn-info w-100 btn-sm text-white fw-bold">

                            Confirmar

                        </button>

                    </div>

                </form>

            @endif

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL DESCUENTO Y PROPINA
========================================================== --}}

<div class="modal fade"
     id="optionsModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-sm modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-light py-2">

                <h6 class="modal-title fw-bold">
                    Ajustes
                </h6>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label small fw-bold text-muted">
                        Descuento Global
                    </label>

                    <input type="number"
                           step="0.01"
                           id="inputDiscount"
                           class="form-control"
                           value="{{ $order ? $order->discount : 0 }}"
                           onclick="this.select()">

                </div>

                <div class="mb-3">

                    <label class="form-label small fw-bold text-muted">
                        Propina
                    </label>

                    <input type="number"
                           step="0.01"
                           id="inputTip"
                           class="form-control"
                           value="{{ $order ? $order->tip : 0 }}"
                           onclick="this.select()">

                </div>

            </div>

            <div class="modal-footer p-1">

                <button type="button"
                        class="btn btn-primary w-100 btn-sm fw-bold"
                        onclick="applyOptions()">

                    Aplicar Cambios

                </button>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL COBRO
========================================================== --}}

@if($order)

<div class="modal fade"
     id="checkoutModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <form action="{{ route('pos.checkout', $order->id) }}"
              method="POST"
              class="modal-content border-0 shadow-lg"
              id="checkoutForm">

            @csrf


            <div class="modal-header bg-success text-white py-2">

                <h6 class="modal-title fw-bold">
                    <i class="bi bi-cash-coin me-1"></i>
                    Cobrar Venta
                </h6>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body p-4">


                {{-- =================================================
                     CLIENTE
                ================================================== --}}

                <div class="mb-3">

                    <label class="form-label fw-bold small text-muted">
                        CLIENTE
                    </label>

                    <div class="input-group">

                        <span class="input-group-text bg-light">

                            <i class="bi bi-person"></i>

                        </span>


                        <input type="text"
                               name="client_name"
                               class="form-control"
                               list="clientsList"
                               id="clientSearchInput"
                               placeholder="Nombre del cliente o Público General"
                               autocomplete="off">


                        <button class="btn btn-light border"
                                type="button"
                                onclick="clearClient()">

                            <i class="bi bi-x"></i>

                        </button>

                    </div>


                    <datalist id="clientsList">

                        @foreach($clients as $client)

                            <option value="{{ $client->name }}"
                                    data-id="{{ $client->id }}"
                                    data-document="{{ $client->document_number }}">
                            </option>

                        @endforeach

                    </datalist>


                    <input type="hidden"
                           name="client_id"
                           id="clientId">

                    <small class="text-muted">
                        Puedes seleccionar un cliente registrado o escribir uno nuevo.
                    </small>

                </div>


                {{-- =================================================
                     TIPO DE COMPROBANTE
                ================================================== --}}

                <div class="mb-3">

                    <label class="form-label fw-bold small text-muted">
                        COMPROBANTE
                    </label>

                    <div class="row g-2">

                        <div class="col-md-4">

                            <input type="radio"
                                   class="btn-check"
                                   name="document_type"
                                   id="documentTicket"
                                   value="Ticket"
                                   checked>

                            <label class="btn btn-outline-secondary w-100 fw-bold"
                                   for="documentTicket">

                                <i class="bi bi-receipt me-1"></i>
                                Ticket

                            </label>

                        </div>


                        <div class="col-md-4">

                            <input type="radio"
                                   class="btn-check"
                                   name="document_type"
                                   id="documentBoleta"
                                   value="Boleta">

                            <label class="btn btn-outline-primary w-100 fw-bold"
                                   for="documentBoleta">

                                <i class="bi bi-file-earmark-text me-1"></i>
                                Boleta

                            </label>

                        </div>


                        <div class="col-md-4">

                            <input type="radio"
                                   class="btn-check"
                                   name="document_type"
                                   id="documentFactura"
                                   value="Factura">

                            <label class="btn btn-outline-success w-100 fw-bold"
                                   for="documentFactura">

                                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                                Factura

                            </label>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     DNI / RUC
                ================================================== --}}

                <div id="documentGroup"
                     class="mb-3"
                     style="display: none;">

                    <label id="clientDocumentLabel"
                           class="form-label fw-bold small text-muted">

                        DNI

                    </label>


                    <input type="text"
                           name="client_document"
                           id="clientDoc"
                           class="form-control form-control-lg"
                           placeholder="Ingrese DNI"
                           maxlength="8"
                           inputmode="numeric"
                           autocomplete="off">


                    <div id="documentHelp"
                         class="form-text">

                        Ingrese el DNI de 8 dígitos.

                    </div>

                </div>


                {{-- =================================================
                     RAZÓN SOCIAL
                ================================================== --}}

                <div id="businessNameGroup"
                     class="mb-3"
                     style="display: none;">

                    <label class="form-label fw-bold small text-muted">

                        RAZÓN SOCIAL

                    </label>

                    <input type="text"
                           name="business_name"
                           id="businessName"
                           class="form-control form-control-lg"
                           placeholder="Ingrese la Razón Social"
                           autocomplete="off">

                </div>


                {{-- =================================================
                     MÉTODOS DE PAGO
                ================================================== --}}

                <div class="mb-3 text-center">

                    <label class="form-label fw-bold small text-muted d-block">
                        MÉTODO DE PAGO
                    </label>

                    <div class="btn-group w-100"
                         role="group">


                        {{-- EFECTIVO --}}

                        <input type="radio"
                               class="btn-check"
                               name="payment_method"
                               id="payCash"
                               value="cash"
                               checked>

                        <label class="btn btn-outline-success fw-bold"
                               for="payCash">

                            <i class="bi bi-cash me-1"></i>
                            Efectivo

                        </label>


                        {{-- TARJETA --}}

                        <input type="radio"
                               class="btn-check"
                               name="payment_method"
                               id="payCard"
                               value="card">

                        <label class="btn btn-outline-primary fw-bold"
                               for="payCard">

                            <i class="bi bi-credit-card me-1"></i>
                            Tarjeta

                        </label>


                        {{-- YAPE --}}

                        <input type="radio"
                               class="btn-check"
                               name="payment_method"
                               id="payYape"
                               value="yape">

                        <label class="btn btn-outline-secondary fw-bold"
                               for="payYape">

                            <i class="bi bi-phone me-1"></i>
                            Yape

                        </label>


                        {{-- PLIN --}}

                        <input type="radio"
                               class="btn-check"
                               name="payment_method"
                               id="payPlin"
                               value="plin">

                        <label class="btn btn-outline-info fw-bold"
                               for="payPlin">

                            <i class="bi bi-phone me-1"></i>
                            Plin

                        </label>

                    </div>

                </div>


                {{-- =================================================
                     QR YAPE / PLIN
                ================================================== --}}

                <div id="digitalPaymentGroup"
                     class="text-center mb-3"
                     style="display: none;">

                    <div class="card border-0 shadow-sm bg-light">

                        <div class="card-body p-3">

                            <div id="digitalPaymentTitle"
                                 class="fw-bold fs-5 mb-2">
                            </div>

                            <div id="digitalPaymentQr"
                                 class="d-flex justify-content-center align-items-center">
                            </div>

                            <div class="mt-2 fw-bold">

                                Total a pagar:

                                <span class="text-success">

                                    {{ $currency ?? 'S/' }}

                                    <span class="digital-payment-total">
                                        {{ number_format($order->total, 2) }}
                                    </span>

                                </span>

                            </div>

                            <div id="digitalPaymentMessage"
                                 class="small text-muted mt-2">
                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     EFECTIVO
                ================================================== --}}

                <div id="cashInputGroup">

                    <div class="mb-3">

                        <label class="form-label fw-bold small">
                            Recibido
                        </label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="received_amount"
                               id="receivedAmount"
                               class="form-control text-center fw-bold fs-4 text-success"
                               value="{{ number_format($order->total, 2, '.', '') }}"
                               oninput="calculateChange()"
                               onclick="this.select()">

                    </div>


                    <div class="d-flex justify-content-between align-items-center">

                        <small>
                            Cambio:
                        </small>

                        <h4 class="fw-bold mb-0 text-secondary"
                            id="changeAmount">

                            0.00

                        </h4>

                    </div>

                </div>


                <input type="hidden"
                       id="hiddenTotal"
                       value="{{ number_format($order->total, 2, '.', '') }}">

            </div>


            <div class="modal-footer p-2 bg-light">

                <button type="submit"
                        class="btn btn-success w-100 btn-lg fw-bold">

                    <i class="bi bi-check-circle me-1"></i>
                    CONFIRMAR PAGO

                </button>

            </div>

        </form>

    </div>

</div>

@endif


<script>

    const tableId = {{ $table->id }};

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

    const csrfToken = csrfMeta
        ? csrfMeta.getAttribute('content')
        : '';


    // =========================================================
    // BÚSQUEDA DE PRODUCTOS
    // =========================================================

    const productSearchInput =
        document.getElementById('productSearchInput');

    const clearProductSearch =
        document.getElementById('clearProductSearch');

    const productSearchResults =
        document.getElementById('productSearchResults');

    const noSearchResults =
        document.getElementById('noSearchResults');


    function normalizeText(text) {

        return (text || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');

    }


    function escapeHtml(text) {

        const div =
            document.createElement('div');

        div.textContent =
            text ?? '';

        return div.innerHTML;

    }


    function searchProducts() {

        if (!productSearchInput) return;

        const search =
            normalizeText(
                productSearchInput.value.trim()
            );

        const items =
            Array.from(
                document.querySelectorAll('.product-item')
            );


        if (clearProductSearch) {

            clearProductSearch.style.display =
                search ? 'block' : 'none';

        }


        if (!search) {

            productSearchResults.style.display =
                'none';

            productSearchResults.innerHTML = '';

            noSearchResults.style.display =
                'none';


            items.forEach(item => {

                item.style.display =
                    'block';

            });

            return;

        }


        const activeCategory =
            document.querySelector(
                '.category-btn.active'
            );


        let currentCategory =
            'all';


        if (
            activeCategory &&
            activeCategory.id !== 'cat-btn-all'
        ) {

            currentCategory =
                activeCategory.id.replace(
                    'cat-btn-',
                    'cat-'
                );

        }


        const matches =
            items.filter(item => {

                const belongsToCategory =
                    currentCategory === 'all' ||
                    item.classList.contains(
                        currentCategory
                    );


                if (!belongsToCategory) {
                    return false;
                }


                const name =
                    normalizeText(
                        item.dataset.productName
                    );


                const category =
                    normalizeText(
                        item.dataset.productCategory
                    );


                return (
                    name.includes(search) ||
                    category.includes(search)
                );

            });


        items.forEach(item => {

            const belongsToCategory =
                currentCategory === 'all' ||
                item.classList.contains(
                    currentCategory
                );


            const isMatch =
                matches.includes(item);


            item.style.display =
                belongsToCategory && isMatch
                    ? 'block'
                    : 'none';

        });


        productSearchResults.innerHTML = '';


        if (matches.length === 0) {

            productSearchResults.style.display =
                'none';

            noSearchResults.style.display =
                'block';

            return;

        }


        noSearchResults.style.display =
            'none';


        matches
            .slice(0, 8)
            .forEach((item, index) => {

                const name =
                    item.dataset.productName;

                const category =
                    item.dataset.productCategory;

                const price =
                    parseFloat(
                        item.dataset.productPrice || 0
                    );

                const stock =
                    item.dataset.productStock;

                const image =
                    item.dataset.productImage;


                const result =
                    document.createElement('div');


                result.className =
                    'd-flex align-items-center gap-3 p-2 border-bottom';


                result.style.cursor =
                    'pointer';


                result.dataset.index =
                    index;


                let imageHtml = '';


                if (image) {

                    imageHtml = `
                        <img src="${image}"
                             width="55"
                             height="55"
                             class="rounded"
                             style="object-fit: cover;">
                    `;

                } else {

                    imageHtml = `
                        <div class="bg-light rounded d-flex justify-content-center align-items-center"
                             style="width:55px;height:55px;">

                            <i class="bi bi-cup-straw fs-4 text-muted"></i>

                        </div>
                    `;

                }


                result.innerHTML = `
                    ${imageHtml}

                    <div class="flex-grow-1">

                        <div class="fw-bold text-dark">
                            ${escapeHtml(name)}
                        </div>

                        <small class="text-muted">
                            ${escapeHtml(category)}
                        </small>

                    </div>

                    <div class="text-end">

                        <div class="fw-bold text-primary">
                            S/ ${price.toFixed(2)}
                        </div>

                        <small class="text-muted">
                            Stock: ${escapeHtml(stock)}
                        </small>

                    </div>
                `;


                result.addEventListener(
                    'mouseenter',
                    function() {

                        this.classList.add(
                            'bg-light'
                        );

                    }
                );


                result.addEventListener(
                    'mouseleave',
                    function() {

                        this.classList.remove(
                            'bg-light'
                        );

                    }
                );


                result.addEventListener(
                    'click',
                    function() {

                        const productId =
                            item.dataset.productId;


                        addToOrder(
                            productId
                        );


                        productSearchInput.value =
                            '';

                        clearProductSearch.style.display =
                            'none';

                        productSearchResults.style.display =
                            'none';

                        productSearchResults.innerHTML =
                            '';

                        searchProducts();

                        productSearchInput.focus();

                    }
                );


                productSearchResults.appendChild(
                    result
                );

            });


        productSearchResults.style.display =
            'block';

    }


    if (productSearchInput) {

        productSearchInput.addEventListener(
            'input',
            searchProducts
        );


        productSearchInput.addEventListener(
            'keydown',
            function(event) {

                const results =
                    productSearchResults.querySelectorAll(
                        '[data-index]'
                    );


                if (!results.length) {
                    return;
                }


                let active =
                    productSearchResults.querySelector(
                        '.search-active'
                    );


                let currentIndex =
                    active
                        ? parseInt(
                            active.dataset.index
                        )
                        : -1;


                if (event.key === 'ArrowDown') {

                    event.preventDefault();

                    currentIndex++;

                    if (
                        currentIndex >=
                        results.length
                    ) {

                        currentIndex = 0;

                    }

                }


                if (event.key === 'ArrowUp') {

                    event.preventDefault();

                    currentIndex--;

                    if (currentIndex < 0) {

                        currentIndex =
                            results.length - 1;

                    }

                }


                if (event.key === 'Enter') {

                    event.preventDefault();

                    if (active) {
                        active.click();
                    }

                    return;

                }


                if (event.key === 'Escape') {

                    productSearchResults.style.display =
                        'none';

                    return;

                }


                results.forEach(result => {

                    result.classList.remove(
                        'search-active'
                    );

                });


                if (currentIndex >= 0) {

                    results[currentIndex]
                        .classList.add(
                            'search-active'
                        );


                    results[currentIndex]
                        .scrollIntoView({
                            block: 'nearest'
                        });

                }

            }
        );

    }


    if (clearProductSearch) {

        clearProductSearch.addEventListener(
            'click',
            function() {

                productSearchInput.value =
                    '';

                searchProducts();

                productSearchInput.focus();

            }
        );

    }


    document.addEventListener(
        'click',
        function(event) {

            if (
                !productSearchResults ||
                !productSearchInput
            ) {
                return;
            }


            if (
                !productSearchResults.contains(
                    event.target
                ) &&
                !productSearchInput.contains(
                    event.target
                ) &&
                !clearProductSearch.contains(
                    event.target
                )
            ) {

                productSearchResults.style.display =
                    'none';

            }

        }
    );


    // =========================================================
    // AJAX - AGREGAR PRODUCTO
    // =========================================================

    window.addToOrder = function(productId) {

        fetch(
            `{{ url('/pos/order') }}/${tableId}/add`,
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        csrfToken
                },

                body: JSON.stringify({
                    product_id:
                        productId
                })
            }
        )
        .then(r => r.text())
        .then(html => {

            document
                .getElementById('cart-container')
                .innerHTML = html;

            updateCheckoutTotal();

        });

    };


    // =========================================================
    // ACTUALIZAR CANTIDAD
    // =========================================================

    window.updateQty = function(id, qty) {

        if (
            qty < 1 &&
            !confirm('¿Eliminar producto?')
        ) {
            return;
        }


        fetch(
            `{{ url('/pos/detail') }}/${id}/update`,
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        csrfToken
                },

                body: JSON.stringify({
                    quantity:
                        qty
                })
            }
        )
        .then(r => r.text())
        .then(html => {

            document
                .getElementById('cart-container')
                .innerHTML = html;

            updateCheckoutTotal();

        });

    };


    // =========================================================
    // ELIMINAR PRODUCTO
    // =========================================================

    window.removeItem = function(id) {

        fetch(
            `{{ url('/pos/detail') }}/${id}`,
            {
                method: 'DELETE',

                headers: {
                    'X-CSRF-TOKEN':
                        csrfToken
                }
            }
        )
        .then(r => r.text())
        .then(html => {

            document
                .getElementById('cart-container')
                .innerHTML = html;

            updateCheckoutTotal();

        });

    };


    // =========================================================
    // DESCUENTO Y PROPINA
    // =========================================================

    window.applyOptions = function() {

        var discount =
            document
                .getElementById(
                    'inputDiscount'
                )
                .value;


        var tip =
            document
                .getElementById(
                    'inputTip'
                )
                .value;


        var modal =
            bootstrap.Modal.getInstance(
                document.getElementById(
                    'optionsModal'
                )
            );


        if (modal) {
            modal.hide();
        }


        fetch(
            `{{ url('/pos/order') }}/{{ $order ? $order->id : 0 }}/discount`,
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        csrfToken
                },

                body: JSON.stringify({
                    discount:
                        discount,

                    tip:
                        tip
                })
            }
        )
        .then(r => r.text())
        .then(html => {

            document
                .getElementById(
                    'cart-container'
                )
                .innerHTML = html;

            updateCheckoutTotal();

        });

    };


    // =========================================================
    // FILTRAR PRODUCTOS
    // =========================================================

    window.filterProducts = function(cat) {

        document
            .querySelectorAll('.category-btn')
            .forEach(btn =>
                btn.classList.remove(
                    'active'
                )
            );


        document
            .getElementById(
                cat === 'all'
                    ? 'cat-btn-all'
                    : 'cat-btn-' +
                      cat.replace(
                          'cat-',
                          ''
                      )
            )
            .classList.add(
                'active'
            );


        document
            .querySelectorAll('.product-item')
            .forEach(item => {

                item.style.display =
                    (
                        cat === 'all' ||
                        item.classList.contains(
                            cat
                        )
                    )
                    ? 'block'
                    : 'none';

            });


        if (
            productSearchInput &&
            productSearchInput.value.trim() !== ''
        ) {

            searchProducts();

        }

    };


    // =========================================================
    // ACTUALIZAR TOTAL DEL CHECKOUT
    // =========================================================

    window.updateCheckoutTotal = function() {

        setTimeout(() => {

            var cartTotalElement =
                document.getElementById(
                    'cartTotalValue'
                );


            var newTotal =
                cartTotalElement
                    ? cartTotalElement.value
                    : 0;


            var hiddenInput =
                document.getElementById(
                    'hiddenTotal'
                );


            var receivedInput =
                document.getElementById(
                    'receivedAmount'
                );


            if (hiddenInput) {

                hiddenInput.value =
                    newTotal;

            }


            if (
                typeof updateDigitalPaymentTotal ===
                'function'
            ) {

                updateDigitalPaymentTotal(
                    newTotal
                );

            }


            if (receivedInput) {

                receivedInput.value =
                    newTotal;

            }


            if (
                typeof calculateChange ===
                'function'
            ) {

                calculateChange();

            }

        }, 500);

    };


    // =========================================================
    // MODAL DE NOTAS
    // =========================================================

    var noteModalEl =
        document.getElementById(
            'noteModal'
        );


    if (noteModalEl) {

        noteModalEl.addEventListener(
            'show.bs.modal',
            function(event) {

                var button =
                    event.relatedTarget;


                if (!button) {
                    return;
                }


                document.getElementById(
                    'noteDetailId'
                ).value =
                    button.getAttribute(
                        'data-detail-id'
                    );


                document.getElementById(
                    'noteText'
                ).value =
                    button.getAttribute(
                        'data-note-content'
                    ) || '';


                setTimeout(
                    () =>
                        document.getElementById(
                            'noteText'
                        ).focus(),
                    500
                );

            }
        );

    }


    window.saveNote = function() {

        var detailId =
            document.getElementById(
                'noteDetailId'
            ).value;


        var note =
            document.getElementById(
                'noteText'
            ).value;


        var modal =
            bootstrap.Modal.getInstance(
                document.getElementById(
                    'noteModal'
                )
            );


        if (modal) {
            modal.hide();
        }


        fetch(
            `{{ url('/pos/detail') }}/${detailId}/note`,
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        csrfToken
                },

                body: JSON.stringify({
                    note:
                        note
                })
            }
        )
        .then(r => r.text())
        .then(html => {

            document
                .getElementById(
                    'cart-container'
                )
                .innerHTML = html;

        });

    };


    // =========================================================
    // YAPE / PLIN
    // =========================================================

    window.updateDigitalPaymentTotal = function(total) {

        document
            .querySelectorAll('.digital-payment-total')
            .forEach(function(element) {

                element.innerText =
                    parseFloat(total || 0)
                    .toFixed(2);

            });

    };


    window.togglePaymentMethod = function(method) {

        var cashGroup =
            document.getElementById(
                'cashInputGroup'
            );


        var digitalGroup =
            document.getElementById(
                'digitalPaymentGroup'
            );


        var digitalTitle =
            document.getElementById(
                'digitalPaymentTitle'
            );


        var digitalQr =
            document.getElementById(
                'digitalPaymentQr'
            );


        var digitalMessage =
            document.getElementById(
                'digitalPaymentMessage'
            );


        if (
            !cashGroup ||
            !digitalGroup ||
            !digitalTitle ||
            !digitalQr ||
            !digitalMessage
        ) {
            return;
        }


        if (method === 'cash') {

            cashGroup.style.display =
                'block';

            digitalGroup.style.display =
                'none';

            calculateChange();

            return;

        }


        if (method === 'card') {

            cashGroup.style.display =
                'none';

            digitalGroup.style.display =
                'none';

            return;

        }


        if (method === 'yape') {

            cashGroup.style.display =
                'none';

            digitalGroup.style.display =
                'block';


            digitalTitle.innerHTML =
                '<i class="bi bi-phone me-1"></i> Pago con Yape';


            @if(!empty($settings['yape_qr']))

                digitalQr.innerHTML = `
                    <img
                        src="{{ asset('storage/'.$settings['yape_qr']) }}"
                        alt="QR de Yape"
                        class="img-fluid rounded border shadow-sm"
                        style="width: 240px; height: 240px; object-fit: contain; background: white; padding: 8px;">
                `;

                digitalMessage.innerHTML =
                    'Escanea el código QR para realizar el pago.';

            @else

                digitalQr.innerHTML = `
                    <div class="alert alert-warning mb-0">

                        <i class="bi bi-exclamation-triangle me-1"></i>

                        No hay un QR de Yape configurado.

                    </div>
                `;

                digitalMessage.innerHTML =
                    'Configúralo desde Administración → Configuración.';

            @endif


            updateDigitalPaymentTotal(
                document.getElementById(
                    'hiddenTotal'
                )?.value || 0
            );


            return;

        }


        if (method === 'plin') {

            cashGroup.style.display =
                'none';

            digitalGroup.style.display =
                'block';


            digitalTitle.innerHTML =
                '<i class="bi bi-phone me-1"></i> Pago con Plin';


            @if(!empty($settings['plin_qr']))

                digitalQr.innerHTML = `
                    <img
                        src="{{ asset('storage/'.$settings['plin_qr']) }}"
                        alt="QR de Plin"
                        class="img-fluid rounded border shadow-sm"
                        style="width: 240px; height: 240px; object-fit: contain; background: white; padding: 8px;">
                `;

                digitalMessage.innerHTML =
                    'Escanea el código QR para realizar el pago.';

            @else

                digitalQr.innerHTML = `
                    <div class="alert alert-warning mb-0">

                        <i class="bi bi-exclamation-triangle me-1"></i>

                        No hay un QR de Plin configurado.

                    </div>
                `;

                digitalMessage.innerHTML =
                    'Configúralo desde Administración → Configuración.';

            @endif


            updateDigitalPaymentTotal(
                document.getElementById(
                    'hiddenTotal'
                )?.value || 0
            );


            return;

        }

    };


    // =========================================================
    // COMPATIBILIDAD
    // =========================================================

    window.toggleCashInput = function(show) {

        var cashGroup =
            document.getElementById(
                'cashInputGroup'
            );


        if (cashGroup) {

            cashGroup.style.display =
                show ? 'block' : 'none';

        }

    };


    // =========================================================
    // CALCULAR CAMBIO
    // =========================================================

    window.calculateChange = function() {

        var hiddenTotal =
            document.getElementById(
                'hiddenTotal'
            );


        var receivedAmount =
            document.getElementById(
                'receivedAmount'
            );


        var changeAmount =
            document.getElementById(
                'changeAmount'
            );


        if (
            !hiddenTotal ||
            !receivedAmount ||
            !changeAmount
        ) {
            return;
        }


        var total =
            parseFloat(
                hiddenTotal.value
            ) || 0;


        var received =
            parseFloat(
                receivedAmount.value
            ) || 0;


        var change =
            received - total;


        changeAmount.innerText =
            change.toFixed(2);


        if (change < 0) {

            changeAmount.classList.remove(
                'text-secondary'
            );

            changeAmount.classList.add(
                'text-danger'
            );

        } else {

            changeAmount.classList.remove(
                'text-danger'
            );

            changeAmount.classList.add(
                'text-success'
            );

        }

    };


    // =========================================================
    // BUSCAR CLIENTE REGISTRADO
    // =========================================================

    window.searchClient = function(input) {

        var list =
            document.getElementById(
                'clientsList'
            );


        var clientId =
            document.getElementById(
                'clientId'
            );


        var clientDoc =
            document.getElementById(
                'clientDoc'
            );


        if (
            !list ||
            !clientId ||
            !clientDoc
        ) {
            return;
        }


        var value =
            (input.value || '').trim();


        if (value === '') {

            clientId.value = '';

            return;

        }


        for (
            var i = 0;
            i < list.options.length;
            i++
        ) {

            if (
                list.options[i].value ===
                value
            ) {

                clientId.value =
                    list.options[i]
                        .getAttribute(
                            'data-id'
                        ) || '';


                var registeredDocument =
                    list.options[i]
                        .getAttribute(
                            'data-document'
                        ) || '';


                if (registeredDocument) {

                    clientDoc.value =
                        registeredDocument;

                }


                return;

            }

        }


        // Cliente nuevo escrito manualmente.
        // No borramos el DNI/RUC.

        clientId.value = '';

    };


    // =========================================================
    // LIMPIAR CLIENTE
    // =========================================================

    window.clearClient = function() {

        var input =
            document.getElementById(
                'clientSearchInput'
            );


        var clientId =
            document.getElementById(
                'clientId'
            );


        var clientDoc =
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


        if (input) {
            input.focus();
        }

    };


    // =========================================================
    // TIPO DE COMPROBANTE
    // =========================================================

    window.updateDocumentFields = function() {

        var documentType =
            document.querySelector(
                'input[name="document_type"]:checked'
            );


        var documentGroup =
            document.getElementById(
                'documentGroup'
            );


        var documentLabel =
            document.getElementById(
                'clientDocumentLabel'
            );


        var documentInput =
            document.getElementById(
                'clientDoc'
            );


        var documentHelp =
            document.getElementById(
                'documentHelp'
            );


        var businessGroup =
            document.getElementById(
                'businessNameGroup'
            );


        var businessInput =
            document.getElementById(
                'businessName'
            );


        if (
            !documentType ||
            !documentGroup ||
            !documentInput
        ) {
            return;
        }


        var type =
            documentType.value;


        // TICKET

        if (type === 'Ticket') {

            documentGroup.style.display =
                'none';

            businessGroup.style.display =
                'none';


            documentInput.value =
                '';

            documentInput.removeAttribute(
                'required'
            );


            if (businessInput) {

                businessInput.value =
                    '';

                businessInput.removeAttribute(
                    'required'
                );

            }


            return;

        }


        // BOLETA

        if (type === 'Boleta') {

            documentGroup.style.display =
                'block';

            businessGroup.style.display =
                'none';


            documentLabel.innerText =
                'DNI';


            documentInput.placeholder =
                'Ingrese DNI de 8 dígitos';


            documentInput.maxLength =
                8;


            documentInput.setAttribute(
                'maxlength',
                '8'
            );


            documentInput.setAttribute(
                'required',
                'required'
            );


            documentHelp.innerText =
                'El DNI debe tener exactamente 8 dígitos.';


            if (businessInput) {

                businessInput.value =
                    '';

                businessInput.removeAttribute(
                    'required'
                );

            }


            return;

        }


        // FACTURA

        if (type === 'Factura') {

            documentGroup.style.display =
                'block';

            businessGroup.style.display =
                'block';


            documentLabel.innerText =
                'RUC';


            documentInput.placeholder =
                'Ingrese RUC de 11 dígitos';


            documentInput.maxLength =
                11;


            documentInput.setAttribute(
                'maxlength',
                '11'
            );


            documentInput.setAttribute(
                'required',
                'required'
            );


            documentHelp.innerText =
                'El RUC debe tener exactamente 11 dígitos.';


            if (businessInput) {

                businessInput.setAttribute(
                    'required',
                    'required'
                );

            }

        }

    };


    // =========================================================
    // SOLO NÚMEROS EN DNI / RUC
    // =========================================================

    document.addEventListener(
        'input',
        function(event) {

            if (
                event.target &&
                event.target.id === 'clientDoc'
            ) {

                event.target.value =
                    event.target.value
                        .replace(/\D/g, '');

            }

        }
    );


    // =========================================================
    // VALIDAR COBRO
    // =========================================================

    var checkoutForm =
        document.getElementById(
            'checkoutForm'
        );


    if (checkoutForm) {

        checkoutForm.addEventListener(
            'submit',
            function(event) {

                var documentType =
                    document.querySelector(
                        'input[name="document_type"]:checked'
                    );


                var clientName =
                    document.getElementById(
                        'clientSearchInput'
                    );


                var clientDocument =
                    document.getElementById(
                        'clientDoc'
                    );


                var businessName =
                    document.getElementById(
                        'businessName'
                    );


                var paymentMethod =
                    document.querySelector(
                        'input[name="payment_method"]:checked'
                    );


                if (!documentType) {
                    return;
                }


                var type =
                    documentType.value;


                var name =
                    clientName
                        ? clientName.value.trim()
                        : '';


                var documentNumber =
                    clientDocument
                        ? clientDocument.value.trim()
                        : '';


                var business =
                    businessName
                        ? businessName.value.trim()
                        : '';


                // BOLETA

                if (type === 'Boleta') {

                    if (name === '') {

                        event.preventDefault();

                        alert(
                            'Para emitir una Boleta debe ingresar el nombre del cliente.'
                        );

                        clientName.focus();

                        return;

                    }


                    if (!/^\d{8}$/.test(documentNumber)) {

                        event.preventDefault();

                        alert(
                            'El DNI debe tener exactamente 8 dígitos.'
                        );

                        clientDocument.focus();

                        return;

                    }

                }


                // FACTURA

                if (type === 'Factura') {

                    if (name === '') {

                        event.preventDefault();

                        alert(
                            'Para emitir una Factura debe ingresar el nombre del cliente.'
                        );

                        clientName.focus();

                        return;

                    }


                    if (!/^\d{11}$/.test(documentNumber)) {

                        event.preventDefault();

                        alert(
                            'El RUC debe tener exactamente 11 dígitos.'
                        );

                        clientDocument.focus();

                        return;

                    }


                    if (business === '') {

                        event.preventDefault();

                        alert(
                            'Para emitir una Factura debe ingresar la Razón Social.'
                        );

                        businessName.focus();

                        return;

                    }

                }


                // EFECTIVO

                if (
                    paymentMethod &&
                    paymentMethod.value === 'cash'
                ) {

                    var total =
                        parseFloat(
                            document.getElementById(
                                'hiddenTotal'
                            )?.value || 0
                        );


                    var received =
                        parseFloat(
                            document.getElementById(
                                'receivedAmount'
                            )?.value || 0
                        );


                    if (received < total) {

                        event.preventDefault();

                        alert(
                            'El monto recibido es menor que el total de la venta.'
                        );

                        document.getElementById(
                            'receivedAmount'
                        ).focus();

                        return;

                    }

                }

            }
        );

    }


    // =========================================================
    // EVENTOS DEL COMPROBANTE
    // =========================================================

    document.querySelectorAll(
        'input[name="document_type"]'
    ).forEach(function(radio) {

        radio.addEventListener(
            'change',
            updateDocumentFields
        );

    });


    // =========================================================
    // EVENTOS MÉTODO DE PAGO
    // =========================================================

    document.querySelectorAll(
        'input[name="payment_method"]'
    ).forEach(function(radio) {

        radio.addEventListener(
            'change',
            function() {

                togglePaymentMethod(
                    this.value
                );

            }
        );

    });


    // =========================================================
    // CLIENTE
    // =========================================================

    var clientSearchInput =
        document.getElementById(
            'clientSearchInput'
        );


    if (clientSearchInput) {

        clientSearchInput.addEventListener(
            'input',
            function() {

                searchClient(this);

            }
        );


        clientSearchInput.addEventListener(
            'change',
            function() {

                searchClient(this);

            }
        );

    }


    // =========================================================
    // INICIALIZACIÓN
    // =========================================================

    document.addEventListener(
        'DOMContentLoaded',
        function() {

            if (
                document.getElementById(
                    'receivedAmount'
                )
            ) {

                calculateChange();

            }


            if (
                document.querySelector(
                    'input[name="document_type"]'
                )
            ) {

                updateDocumentFields();

            }


            var checkedPayment =
                document.querySelector(
                    'input[name="payment_method"]:checked'
                );


            if (checkedPayment) {

                togglePaymentMethod(
                    checkedPayment.value
                );

            }

        }
    );

</script>


<style>

    .product-card:active {

        transform: scale(0.95);
        background-color: #f8f9fa;

    }


    #productSearchResults [data-index] {

        transition:
            background-color 0.15s ease;

    }


    #productSearchResults .search-active {

        background-color:
            #f8f9fa;

    }


    #productSearchInput:focus {

        box-shadow:
            0 0 0 0.2rem
            rgba(13, 110, 253, 0.15);

    }


    #clientDoc {

        letter-spacing: 1px;

    }


    #businessName {

        text-transform: uppercase;

    }


    #checkoutModal .btn-check:checked + label {

        box-shadow:
            0 0 0 2px rgba(0, 0, 0, 0.15);

    }

</style>

@endsection