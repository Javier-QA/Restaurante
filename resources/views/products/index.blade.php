@extends('layouts.app')

@section('content')

<style>
.product-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 7px;
}

.product-action {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid var(--border-soft);
    border-radius: 9px;
    background: var(--card-bg);
    text-decoration: none;
    cursor: pointer;
    transition: all .16s ease;
}

.product-action i {
    font-size: .88rem;
    line-height: 1;
}

.product-action:hover {
    transform: translateY(-1px);
}

/* Ajustar stock */
.product-action-stock {
    color: var(--ink-neutral, #475569);
}

.product-action-stock:hover {
    color: var(--text-main, #0f172a);
    border-color: #94a3b8;
    background: var(--card-bg, #ffffff);
}

/* Editar */
.product-action-edit {
    color: var(--primary);
}

.product-action-edit:hover {
    color: var(--primary);
    border-color: var(--primary);
    background: color-mix(
        in srgb,
        var(--primary) 7%,
        var(--card-bg, white)
    );
}

/* Desactivar */
.product-action-delete {
    color: var(--ink-red, #dc2626);
}

.product-action-delete:hover {
    color: var(--ink-red, #b91c1c);
    border-color: var(--surface-red, #fecaca);
    background: var(--surface-red, #fff1f2);
}

</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam-fill me-2"></i>Inventario de Productos</h2>
        <p class="text-muted mb-0">Gestión de carta y existencias</p>
    </div>
    <div>
        <a href="{{ route('inventory.logs') }}" class="btn btn-dark me-2">
            <i class="bi bi-clock-history me-1"></i> Ver Kardex
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
        </a>
    </div>
</div>

<form id="inventorySearchForm" action="{{ route('products.index') }}" method="GET" class="mb-3">
    <label for="inventorySearch" class="visually-hidden">Buscar producto por nombre</label>
    <div class="d-flex align-items-center gap-2">
        <div class="position-relative flex-grow-1">
            <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px; pointer-events: none"></i>
            <input id="inventorySearch" name="search" type="search" maxlength="100"
                   value="{{ $search }}" class="form-control" style="padding-left: 40px; min-height: 42px"
                   placeholder="Buscar producto por nombre…" autocomplete="off" aria-controls="inventoryResults">
        </div>
        <a id="inventoryClear" href="{{ route('products.index') }}" class="btn btn-outline-secondary border border-secondary d-inline-flex align-items-center justify-content-center px-4">Limpiar</a>
    </div>
    <div id="inventorySearchStatus" class="small text-muted mt-2" role="status" aria-live="polite">{{ $products->total() }} productos encontrados</div>
</form>

<div id="inventoryResults" data-total="{{ $products->total() }}">
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-uppercase text-muted small fw-bold">Producto</th>
                        <th class="text-uppercase text-muted small fw-bold">Categoría</th>
                        <th class="text-uppercase text-muted small fw-bold">Costo</th>
                        <th class="text-uppercase text-muted small fw-bold">Precio</th>
                        <th class="text-uppercase text-muted small fw-bold">Stock</th>
                        <th class="text-center text-uppercase text-muted small fw-bold">Estado</th>
                        <th class="text-end pe-4 text-uppercase text-muted small fw-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="rounded me-3 border" width="48" height="48" style="object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center border text-muted" style="width: 48px; height: 48px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>

                                        @if(!$product->is_saleable)
                                            <span class="badge bg-secondary" style="font-size: 0.65rem;"><i class="bi bi-eye-slash me-1"></i>Solo Insumo</span>
                                        @endif
                                        @if($product->ingredients->count() > 0)
                                            <span class="badge bg-info text-dark" style="font-size: 0.65rem;"><i class="bi bi-diagram-3 me-1"></i>Tiene Receta</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                {{ $product->category?->name ?? 'Sin categoría' }} </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">
                                    S/ {{ number_format((float) ($product->cost ?? 0), 2) }}
                                </span>
                            </td>
                            <td class="fw-bold text-primary">S/ {{ number_format($product->price, 2) }}</td>
                            <td>
                                @if(!$product->controls_stock)
                                    <span class="text-muted small">Sin control de stock</span>
                                @elseif(is_null($product->stock))
                                    <span class="text-muted small">--</span>
                                @elseif($product->stock <= ($product->minimum_stock ?? 5))
                                    <span class="badge bg-warning text-dark border border-warning">Bajo: {{ $product->stock_display }} {{ $product->unit_display }}</span>
                                @else
                                    <span class="badge bg-light text-success border border-success fw-bold">{{ $product->stock_display }} {{ $product->unit_display }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('products.toggle', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm product-status-btn {{ $product->is_active ? 'product-status-active' : 'product-status-inactive' }} rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">
                                        {{ $product->is_active ? 'ACTIVO' : 'INACTIVO' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <div class="product-actions">

    @if($product->controls_stock)
    <button type="button"
            class="product-action product-action-stock"
            data-bs-toggle="modal"
            data-bs-target="#adjustStock{{ $product->id }}"
            title="Ajustar stock">
        <i class="bi bi-arrow-left-right"></i>
    </button>
    @endif

    <a href="{{ route('products.edit', ['product' => $product->id, 'page' => $products->currentPage()]) }}"
       class="product-action product-action-edit"
       title="Editar producto">
        <i class="bi bi-pencil-square"></i>
    </a>

    <button type="button"
            class="product-action product-action-delete"
            data-product-id="{{ $product->id }}"
            data-product-name="{{ $product->name }}"
            onclick="confirmDeleteProduct(this)"
            title="Eliminar producto">
        <i class="bi bi-trash3"></i>
    </button>

</div>

                                <form id="del-{{$product->id}}" action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>

                                <div class="modal fade"
     id="adjustStock{{ $product->id }}"
     tabindex="-1"
     aria-labelledby="adjustStockLabel{{ $product->id }}"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered"
         style="max-width:460px;">

        <form action="{{ route('products.adjust', $product->id) }}"
              method="POST"
              class="modal-content stock-modal-content">

            @csrf

            {{-- Encabezado --}}
            <div class="modal-header border-0 px-4 pt-4 pb-2">

                <div class="d-flex align-items-center gap-3">

                    <div class="stock-modal-icon">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>

                    <div>
                        <h5 id="adjustStockLabel{{ $product->id }}"
                            class="stock-modal-title">
                            Ajustar stock
                        </h5>

                        <p class="stock-modal-subtitle">
                            Registra una entrada o salida de existencias
                        </p>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>

            {{-- Cuerpo --}}
            <div class="modal-body px-4 pt-3 pb-2">

                {{-- Producto --}}
                <div class="stock-product-card">

                    <div class="stock-product-info">
                        <span class="stock-product-label">
                            Producto
                        </span>

                        <strong>
                            {{ $product->name }}
                        </strong>
                    </div>

                    <div class="stock-current">

                        <span class="stock-product-label">
                            Stock actual
                        </span>

                        <strong>
                            {{ $product->stock_display }} {{ $product->unit_display }}
                        </strong>

                    </div>

                </div>

                <div class="row g-3">

                    {{-- Tipo --}}
                    <div class="col-md-6">

                        <label class="stock-field-label">
                            Tipo de movimiento
                            <span class="text-danger">*</span>
                        </label>

                        <div class="position-relative">

                            <i class="bi bi-arrow-down-up stock-field-icon"></i>

                            <select name="type"
                                    class="form-select stock-form-control stock-select"
                                    required>

                                <option value="add">
                                    Entrada
                                </option>

                                <option value="sub">
                                    Salida
                                </option>

                            </select>

                        </div>

                    </div>

                    {{-- Cantidad --}}
                    <div class="col-md-6">

                        <label class="stock-field-label">
                            Cantidad ({{ $product->unit_display }})
                            <span class="text-danger">*</span>
                        </label>

                        <div class="position-relative">

                            <i class="bi bi-box-seam stock-field-icon"></i>

                            <input type="number"
                                   name="quantity" step="{{ in_array($product->unit_display, ['und', 'paq', 'caja']) ? '1' : '0.001' }}"
                                   class="form-control stock-form-control"
                                   min="{{ in_array($product->unit_display, ['und', 'paq', 'caja']) ? '1' : '0.001' }}"
                                   placeholder="Ej. 10"
                                   required>

                        </div>

                    </div>

                </div>

                {{-- Información --}}
                <div class="stock-kardex-notice">

                    <i class="bi bi-info-circle"></i>

                    <div>
                        <strong>Registro automático</strong>

                        <span>
                            Este movimiento quedará registrado
                            automáticamente en el Kardex.
                        </span>
                    </div>

                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer border-0 px-4 pt-3 pb-4">

                <button type="button"
                        class="btn stock-cancel-btn"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn stock-save-btn">

                    <i class="bi bi-check-lg"></i>
                    Guardar ajuste

                </button>

            </div>

        </form>

    </div>

</div>
</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No hay productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <x-system-pagination :paginator="$products" />
</div>



</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('inventorySearchForm');
    const input = document.getElementById('inventorySearch');
    const status = document.getElementById('inventorySearchStatus');
    let timer;
    let controller;
    let requestId = 0;
    async function searchInventory() {
        clearTimeout(timer);
        if (controller) controller.abort();
        controller = new AbortController();
        const currentId = ++requestId;
        const url = new URL(form.action);
        const term = input.value.trim();
        if (term) url.searchParams.set('search', term);
        status.textContent = 'Buscando…';
        const results = document.getElementById('inventoryResults');
        results.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, {signal: controller.signal});
            if (!response.ok) throw new Error('Search failed');
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            const updated = doc.getElementById('inventoryResults');
            if (!updated) throw new Error('Missing results');
            if (currentId !== requestId) return;
            results.replaceWith(updated);
            status.textContent = updated.dataset.total + ' productos encontrados';
            history.replaceState(null, '', url);
        } catch (error) {
            if (error.name !== 'AbortError' && currentId === requestId) {
                status.textContent = 'No se pudo actualizar. Pulsa Enter para reintentar.';
            }
        } finally {
            if (currentId === requestId) document.getElementById('inventoryResults').removeAttribute('aria-busy');
        }
    }
    input.addEventListener('input', () => {
        if (controller) controller.abort();
        ++requestId;
        clearTimeout(timer);
        timer = setTimeout(searchInventory, 250);
    });
    form.addEventListener('submit', event => {
        event.preventDefault();
        searchInventory();
    });
    document.getElementById('inventoryClear').addEventListener('click', event => {
        event.preventDefault();
        input.value = '';
        input.focus();
        searchInventory();
    });
});
</script>

<script>
let deleteProductFormId = null;

function confirmDeleteProduct(button) {
    deleteProductFormId = 'del-' + button.dataset.productId;

    const productName =
        button.dataset.productName || 'este producto';

    const form =
        document.getElementById(deleteProductFormId);

    if (!form) return;

    SystemNotify.confirm({
        type: 'danger',
        title: 'Eliminar producto',
        text: '¿Deseas eliminar "' + productName + '" del sistema?',
        confirmText: 'Eliminar producto',
        icon: 'bi-trash3',
        onConfirm: function () {
            form.submit();
        }
    });
}

function submitDeleteProduct() {

    if (!deleteProductFormId) {
        return;
    }

    const form =
        document.getElementById(deleteProductFormId);

    if (form) {
        form.submit();
    }
}
</script>

<style>
.product-delete-modal {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 22px 55px rgba(15, 23, 42, .16);
}

.product-delete-modal .modal-body {
    padding: 28px 25px 24px;
    text-align: center;
}

.product-delete-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: var(--surface-red, #fff1f2);
    color: var(--ink-red, #dc2626);
    font-size: 1.1rem;
}

.product-delete-modal h5 {
    margin-bottom: 7px;
    color: var(--text-main);
    font-size: 1rem;
    font-weight: 800;
}

.product-delete-question {
    margin-bottom: 17px;
    color: var(--text-muted);
    font-size: .78rem;
}

.product-delete-question strong {
    color: var(--text-main);
}

.product-delete-info {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    padding: 11px 12px;
    border: 1px solid #fed7aa;
    border-radius: 9px;
    background: var(--surface-amber, #fff7ed);
    color: var(--ink-amber, #9a3412);
    font-size: .7rem;
    line-height: 1.45;
    text-align: left;
}

.product-delete-info i {
    margin-top: 1px;
}

.product-delete-buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 20px;
}

.product-cancel-btn,
.product-delete-btn {
    min-height: 38px;
    padding: 0 15px;
    border-radius: 9px;
    font-size: .75rem;
    font-weight: 750;
}

.product-cancel-btn {
    border: 1px solid var(--border-soft);
    background: var(--card-bg);
    color: var(--text-main);
}

.product-cancel-btn:hover {
    border-color: #94a3b8;
    background: var(--light-bg);
}

.product-delete-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #dc2626;
    background: #dc2626;
    color: #fff;
}

.product-delete-btn:hover {
    border-color: #b91c1c;
    background: #b91c1c;
    color: #fff;
}
</style>

<style>
.stock-modal-content {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
    background: var(--card-bg);
    box-shadow: 0 24px 60px rgba(15,23,42,.16);
}

.stock-modal-icon {
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: color-mix(
        in srgb,
        var(--primary) 10%,
        var(--card-bg)
    );
    color: var(--primary);
    font-size: 1.05rem;
}

.stock-modal-title {
    margin: 0 0 3px;
    color: var(--text-main);
    font-size: 1.05rem;
    font-weight: 800;
}

.stock-modal-subtitle {
    margin: 0;
    color: var(--text-muted);
    font-size: .76rem;
}

.stock-product-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 19px;
    padding: 13px 15px;
    border: 1px solid var(--border-soft);
    border-radius: 11px;
    background: var(--light-bg);
}

.stock-product-info {
    min-width: 0;
}

.stock-product-info strong {
    display: block;
    margin-top: 3px;
    color: var(--text-main);
    font-size: .84rem;
    font-weight: 750;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.stock-product-label {
    display: block;
    color: var(--text-muted);
    font-size: .65rem;
    font-weight: 750;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.stock-current {
    flex: 0 0 auto;
    text-align: right;
}

.stock-current strong {
    display: block;
    margin-top: 2px;
    color: var(--text-main);
    font-size: 1rem;
    font-weight: 800;
}

.stock-field-label {
    display: block;
    margin-bottom: 7px;
    color: var(--text-main);
    font-size: .75rem;
    font-weight: 750;
}

.stock-field-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    z-index: 4;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .82rem;
    pointer-events: none;
}

.stock-form-control {
    min-height: 42px;
    padding-left: 39px;
    border: 1px solid var(--border-soft);
    border-radius: 9px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .8rem;
}

.stock-select {
    padding-left: 39px;
}

.stock-form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px
        color-mix(in srgb, var(--primary) 10%, transparent);
}

.stock-kardex-notice {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 18px;
    padding: 11px 13px;
    border: 1px solid
        color-mix(in srgb, var(--primary) 18%, var(--border-soft));
    border-radius: 10px;
    background:
        color-mix(in srgb, var(--primary) 5%, var(--card-bg));
    color: var(--text-muted);
}

.stock-kardex-notice > i {
    margin-top: 1px;
    color: var(--primary);
    font-size: .85rem;
}

.stock-kardex-notice strong {
    display: block;
    margin-bottom: 1px;
    color: var(--text-main);
    font-size: .72rem;
    font-weight: 750;
}

.stock-kardex-notice span {
    display: block;
    font-size: .69rem;
    line-height: 1.45;
}

.stock-cancel-btn,
.stock-save-btn {
    min-height: 41px;
    padding: 0 17px;
    border-radius: 10px;
    font-size: .76rem;
    font-weight: 750;
}

.stock-cancel-btn {
    border: 1px solid var(--border-soft);
    background: var(--card-bg);
    color: var(--text-main);
}

.stock-cancel-btn:hover {
    border-color: #94a3b8;
    background: var(--light-bg);
    color: var(--text-main);
}

.stock-save-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--primary);
    background: var(--primary);
    color: #fff;
}

.stock-save-btn:hover {
    border-color: var(--primary-hover);
    background: var(--primary-hover);
    color: #fff;
}

@media (max-width: 575.98px) {
    .stock-product-card {
        align-items: flex-start;
    }

    .stock-current {
        text-align: right;
    }

    .stock-modal-content .modal-footer {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<style>
/* =========================================================
   AJUSTE DE ALINEACION - MODAL STOCK
   ========================================================= */

/* Header */
.stock-modal-content .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stock-modal-content .modal-header > .d-flex {
    align-items: center !important;
}

.stock-modal-icon {
    flex: 0 0 46px;
}

.stock-modal-title,
.stock-modal-subtitle {
    text-align: left !important;
}


/* Producto / stock */
.stock-product-card {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center !important;
    gap: 24px;
}

.stock-product-info {
    text-align: left !important;
}

.stock-current {
    min-width: 90px;
    text-align: right !important;
}

.stock-product-info .stock-product-label,
.stock-current .stock-product-label {
    text-align: inherit !important;
}


/* Campos */
.stock-modal-content .row.g-3 {
    align-items: start;
}

.stock-modal-content .col-md-6 {
    text-align: left !important;
}

.stock-field-label {
    width: 100%;
    margin-bottom: 7px !important;
    text-align: left !important;
}

.stock-modal-content .position-relative {
    width: 100%;
}

.stock-form-control,
.stock-select {
    width: 100%;
    text-align: left !important;
}


/* Aviso Kardex */
.stock-kardex-notice {
    display: grid !important;
    grid-template-columns: 18px minmax(0, 1fr);
    align-items: start !important;
    gap: 10px !important;
    text-align: left !important;
}

.stock-kardex-notice > i {
    width: 18px;
    margin-top: 2px !important;
    text-align: center;
}

.stock-kardex-notice > div {
    min-width: 0;
    text-align: left !important;
}

.stock-kardex-notice strong {
    display: block !important;
    margin: 0 0 3px !important;
    text-align: left !important;
}

.stock-kardex-notice span {
    display: block !important;
    margin: 0 !important;
    text-align: left !important;
}


/* Botones */
.stock-modal-content .modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
}

.stock-modal-content .modal-footer > * {
    margin: 0 !important;
}

.stock-cancel-btn,
.stock-save-btn {
    min-height: 42px;
}


/* Responsive */
@media (max-width: 575.98px) {

    .stock-product-card {
        grid-template-columns: 1fr auto !important;
        gap: 15px;
    }

    .stock-modal-content .modal-footer {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        width: 100%;
    }

    .stock-cancel-btn,
    .stock-save-btn {
        width: 100%;
    }
}
</style>


<style>
/* DARK MODE - MODALES PRODUCTOS DEFINITIVO */

/* =========================================================
   AJUSTAR STOCK
   ========================================================= */

html[data-color-mode="dark"] .stock-modal-content {
    background: #132338 !important;
    border: 1px solid #30465d !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .stock-product-card {
    background: #1a2e43 !important;
    border-color: #354d64 !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .stock-product-info strong,
html[data-color-mode="dark"] .stock-current strong {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .stock-product-label {
    color: #91a8bd !important;
}

html[data-color-mode="dark"] .stock-modal-content .form-label {
    color: #dce7f1 !important;
}

html[data-color-mode="dark"] .stock-modal-content .form-control,
html[data-color-mode="dark"] .stock-modal-content .form-select {
    background-color: #102235 !important;
    border-color: #354d64 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .stock-modal-content .form-control::placeholder {
    color: #7890a5 !important;
}


/* =========================================================
   ELIMINAR PRODUCTO
   ========================================================= */

html[data-color-mode="dark"] .product-delete-modal {
    background: #132338 !important;
    border: 1px solid #30465d !important;
    color: #ffffff !important;
}

/* Icono de papelera */
html[data-color-mode="dark"] .product-delete-modal .product-delete-icon {
    background: rgba(239, 68, 68, .13) !important;
    border: 1px solid rgba(248, 113, 113, .30) !important;
    color: #f87171 !important;
}

html[data-color-mode="dark"] .product-delete-modal .product-delete-icon i {
    color: #f87171 !important;
}

/* Título */
html[data-color-mode="dark"] .product-delete-modal h5 {
    color: #ffffff !important;
}

/* Pregunta */
html[data-color-mode="dark"] .product-delete-question {
    color: #91a8bd !important;
}

html[data-color-mode="dark"] .product-delete-question strong {
    color: #ffffff !important;
}

/* Aviso: esta acción eliminará... */
html[data-color-mode="dark"] .product-delete-modal .product-delete-info {
    background: rgba(245, 158, 11, .10) !important;
    border-color: rgba(245, 158, 11, .35) !important;
    color: #d9e3ec !important;
}

html[data-color-mode="dark"] .product-delete-modal .product-delete-info i {
    color: #f59e0b !important;
}

html[data-color-mode="dark"] .product-delete-modal .product-delete-info span {
    color: #b9c9d8 !important;
}


/* =========================================================
   BOTONES DEL MODAL
   ========================================================= */

html[data-color-mode="dark"] .product-delete-modal .product-cancel-btn {
    background: transparent !important;
    border-color: #7890a5 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .product-delete-modal .product-cancel-btn:hover {
    background: #20364b !important;
    border-color: #91a8bd !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .product-delete-modal .product-delete-btn {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .product-delete-modal .product-delete-btn:hover {
    background: #b91c1c !important;
    border-color: #b91c1c !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .product-delete-modal .product-delete-btn i {
    color: #ffffff !important;
}

</style>


<style>
/* PRODUCTOS - ESTADO ACTIVO E INACTIVO */

.product-status-btn {
    min-width: 82px;
    border-width: 1px !important;
    border-style: solid !important;
    box-shadow: none !important;
    transition: all .2s ease !important;
}

/* ACTIVO */
.product-status-active {
    background: #ecfdf3 !important;
    border-color: #22c55e !important;
    color: var(--ink-green, #15803d) !important;
}

.product-status-active:hover {
    background: var(--surface-green, #dcfce7) !important;
    border-color: #16a34a !important;
    color: var(--ink-green, #166534) !important;
}

/* INACTIVO */
.product-status-inactive {
    background: var(--surface-neutral, #f8fafc) !important;
    border-color: #94a3b8 !important;
    color: var(--ink-neutral, #64748b) !important;
}


/* =========================
   MODO OSCURO
   ========================= */

html[data-color-mode="dark"] .product-status-active {
    background: rgba(34, 197, 94, .14) !important;
    border-color: #22c55e !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .product-status-active:hover {
    background: rgba(34, 197, 94, .22) !important;
    border-color: #4ade80 !important;
    color: #86efac !important;
}

html[data-color-mode="dark"] .product-status-inactive {
    background: rgba(148, 163, 184, .10) !important;
    border-color: #64748b !important;
    color: var(--ink-neutral, #94a3b8) !important;
}

html[data-color-mode="dark"] .product-status-inactive:hover {
    background: rgba(148, 163, 184, .17) !important;
    border-color: #94a3b8 !important;
    color: #cbd5e1 !important;
}

</style>

@endsection