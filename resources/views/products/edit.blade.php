@extends('layouts.app')

@section('content')
<style>
    .product-edit-page {
        max-width: 1500px;
    }

    /* Encabezado */
    .product-edit-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 36px;
        margin-bottom: 12px;
        padding: 0 12px;
        border: 1px solid var(--border-soft);
        border-radius: 9px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: .74rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(15,23,42,.03);
        transition: .16s ease;
    }

    .product-edit-back:hover {
        border-color: #94a3b8;
        background: var(--light-bg);
        color: var(--text-main);
        transform: translateY(-1px);
    }

    .product-edit-heading {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 22px;
    }

    .product-edit-heading > i {
        margin-top: 4px;
        color:var(--text-main);
        font-size: 1.22rem;
    }

    .product-edit-title {
        margin: 0;
        color: var(--text-main);
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -.3px;
    }

    .product-edit-subtitle {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: .8rem;
    }

    /* Tarjetas */
    .product-edit-card {
        height: 100%;
        border: 1px solid var(--border-soft);
        border-radius: 15px;
        background: var(--card-bg);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .product-section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 17px 20px;
        border-bottom: 1px solid var(--border-soft);
    }

    .product-section-head > i {
        color:var(--text-main);
        font-size: .95rem;
    }

    .product-section-title {
        margin: 0;
        color: var(--text-main);
        font-size: .88rem;
        font-weight: 800;
    }

    .product-section-description {
        margin: 2px 0 0;
        color: var(--text-muted);
        font-size: .69rem;
    }

    .product-edit-body {
        padding: 20px;
    }

    /* Formularios */
    .product-edit-label {
        margin-bottom: 7px;
        color: var(--text-main);
        font-size: .74rem;
        font-weight: 750;
    }

    .product-edit-input,
    .product-edit-select {
        min-height: 42px;
        border: 1px solid var(--border-soft);
        border-radius: 9px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: .8rem;
    }

    .product-edit-input:focus,
    .product-edit-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px
            color-mix(in srgb, var(--primary) 10%, transparent);
    }

    .product-input-icon {
        position: relative;
    }

    .product-input-icon > i {
        position: absolute;
        top: 50%;
        left: 13px;
        z-index: 4;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: .8rem;
        pointer-events: none;
    }

    .product-input-icon .form-control,
    .product-input-icon .form-select {
        padding-left: 38px;
    }

    .product-field-help {
        display: block;
        margin-top: 5px;
        color: var(--text-muted);
        font-size: .66rem;
    }

    /* Rentabilidad */
    .product-profitability {
        margin-bottom: 19px;
        padding: 14px 16px;
        border: 1px solid #bbf7d0;
        border-radius: 11px;
        background: var(--surface-green, #f0fdf4);
    }

    .product-profitability-head {
        display: flex;
        align-items: center;
        gap: 7px;
        color: var(--ink-green, #166534);
        font-size: .77rem;
        font-weight: 800;
    }

    .product-profitability-content {
        display: grid;
        grid-template-columns: minmax(0,1fr) auto auto;
        align-items: center;
        gap: 25px;
    }

    .product-profitability-description {
        margin: 3px 0 0;
        color: var(--ink-green, #15803d);
        font-size: .67rem;
    }

    .profit-stat {
        text-align: right;
    }

    .profit-stat span {
        display: block;
        color: var(--ink-neutral, #64748b);
        font-size: .61rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .profit-stat strong {
        display: block;
        margin-top: 2px;
        color: var(--text-main);
        font-size: .9rem;
    }

    .profit-stat.margin strong {
        color: var(--ink-green, #15803d);
    }

    /* Opciones */
    .product-option-card {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        min-height: 77px;
        padding: 13px 14px;
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        background: var(--light-bg);
    }

    .product-option-card .form-check-input {
        margin-top: 3px;
    }

    .product-option-title {
        display: block;
        color: var(--text-main);
        font-size: .75rem;
        font-weight: 750;
    }

    .product-option-text {
        display: block;
        margin-top: 3px;
        color: var(--text-muted);
        font-size: .65rem;
        line-height: 1.4;
    }

    /* Carta digital */
    .product-digital-options {
        margin-top: 19px;
        padding: 16px;
        border: 1px solid
            color-mix(in srgb, var(--primary) 18%, var(--border-soft)) !important;
        border-radius: 11px !important;
        background:
            color-mix(in srgb, var(--primary) 5%, var(--card-bg)) !important;
    }

    .digital-options-title {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 14px;
        color: var(--text-main);
        font-size: .78rem;
        font-weight: 800;
    }

    .digital-options-title i {
        color: var(--primary);
    }

    .digital-switch {
        display: flex;
        align-items: center;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid var(--border-soft);
        border-radius: 9px;
        background: var(--card-bg);
    }

    .digital-switch label {
        color: var(--text-main);
        font-size: .72rem;
        font-weight: 700;
    }

    /* Imagen */
    .product-image-box {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .product-current-image,
    .product-image-placeholder {
        width: 66px;
        height: 66px;
        flex: 0 0 66px;
        border: 1px solid var(--border-soft);
        border-radius: 11px;
        background: var(--light-bg);
    }

    .product-current-image {
        object-fit: cover;
    }

    .product-image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    /* Receta */
    .recipe-card {
        position: sticky;
        top: 20px;
    }

    .recipe-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .recipe-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 32px;
        padding: 0 10px;
        border: 1px solid
            color-mix(in srgb, var(--primary) 30%, var(--border-soft));
        border-radius: 8px;
        background:
            color-mix(in srgb, var(--primary) 6%, var(--card-bg));
        color: var(--primary);
        font-size: .68rem;
        font-weight: 750;
    }

    .recipe-add-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .recipe-description {
        margin-bottom: 14px;
        color: var(--text-muted);
        font-size: .7rem;
        line-height: 1.5;
    }

    .recipe-list {
        max-height: 430px;
        overflow-y: auto;
        padding-right: 2px;
    }

    .recipe-row {
        display: grid;
        grid-template-columns: minmax(0,1fr) 82px 34px;
        gap: 6px;
        margin-bottom: 8px;
        align-items: center;
    }

    .recipe-name {
        min-height: 38px;
        display: flex;
        align-items: center;
        padding: 0 11px;
        border: 1px solid var(--border-soft);
        border-radius: 8px;
        background: var(--light-bg);
        color: var(--text-main);
        font-size: .7rem;
        font-weight: 650;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .recipe-quantity,
    .recipe-select {
        min-height: 38px;
        border: 1px solid var(--border-soft);
        border-radius: 8px;
        font-size: .7rem;
    }

    .recipe-remove {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--surface-red, #fecaca);
        border-radius: 8px;
        background: var(--card-bg, #ffffff);
        color: var(--ink-red, #dc2626);
    }

    .recipe-remove:hover {
        background: var(--surface-red, #fff1f2);
        border-color: #fca5a5;
        color: var(--ink-red, #b91c1c);
    }

    /* Botón guardar */
    .product-save-area {
        padding: 14px 20px 18px;
        border-top: 1px solid var(--border-soft);
    }

    .product-save-btn {
        width: 100%;
        min-height: 43px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid var(--primary);
        border-radius: 10px;
        background: var(--primary);
        color: #fff;
        font-size: .77rem;
        font-weight: 750;
    }

    .product-save-btn:hover {
        border-color: var(--primary-hover);
        background: var(--primary-hover);
        color: #fff;
    }

    @media (max-width: 991.98px) {
        .recipe-card {
            position: static;
        }
    }

    @media (max-width: 767.98px) {
        .product-profitability-content {
            grid-template-columns: 1fr 1fr;
        }

        .product-profitability-content > div:first-child {
            grid-column: 1 / -1;
        }

        .profit-stat {
            text-align: left;
        }
    }
</style>


<div class="container-fluid px-0 product-edit-page">

    {{-- Volver --}}
    <a href="{{ route('products.index', ['page' => request('page', 1)]) }}"
       class="product-edit-back">
        <i class="bi bi-arrow-left"></i>
        Volver a Productos
    </a>

    {{-- Encabezado --}}
    <div class="product-edit-heading">
        <i class="bi bi-pencil-square"></i>

        <div>
            <h2 class="product-edit-title">
                Editar Producto
            </h2>

            <p class="product-edit-subtitle">
                Gestiona los detalles y la receta del producto
            </p>
        </div>
    </div>


    <form action="{{ route('products.update', $product->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="row g-4">

        @csrf
        <div class="col-12 mb-3">
            <label for="preparationArea" class="form-label fw-bold">Área de preparación</label>
            <select id="preparationArea" name="preparation_area" class="form-select" required>
                <option value="kitchen" @selected(old('preparation_area', $product->preparation_area ?? 'kitchen') === 'kitchen')>Cocina</option>
                <option value="barra" @selected(old('preparation_area', $product->preparation_area ?? 'kitchen') === 'barra')>Barra</option>
            </select>
        </div>

        @method('PUT')

        <input type="hidden"
               name="page"
               value="{{ request('page', 1) }}">


        {{-- =====================================================
             INFORMACIÓN DEL PRODUCTO
        ====================================================== --}}
        <div class="col-lg-8">

            <div class="product-edit-card">

                <div class="product-section-head">
                    <i class="bi bi-box-seam"></i>

                    <div>
                        <h6 class="product-section-title">
                            Información del producto
                        </h6>

                        <p class="product-section-description">
                            Datos generales, precios y configuración de venta
                        </p>
                    </div>
                </div>


                <div class="product-edit-body">

                    {{-- Nombre y categoría --}}
                    <div class="row g-3 mb-3">

                        <div class="col-md-7">
                            <label class="form-label product-edit-label">
                                Nombre del producto
                                <span class="text-danger">*</span>
                            </label>

                            <div class="product-input-icon">
                                <i class="bi bi-box"></i>

                                <input type="text"
                                       name="name"
                                       class="form-control product-edit-input"
                                       value="{{ $product->name }}"
                                       required>
                            </div>
                        </div>


                        <div class="col-md-5">
                            <label class="form-label product-edit-label">
                                Categoría
                                <span class="text-danger">*</span>
                            </label>

                            <div class="product-input-icon">
                                <i class="bi bi-tags"></i>

                                <select name="category_id"
                                        class="form-select product-edit-select"
                                        required>

                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}{{ !$cat->is_active ? ' (oculta en POS)' : '' }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                    </div>


                    @include('products.inventory-fields')
                    {{-- Costos --}}
                    <div class="row g-3 mb-4">

                        <div class="col-md-4">
                            <label class="form-label product-edit-label">
                                Costo unitario
                            </label>

                            <div class="product-input-icon">
                                <i class="bi bi-cash-stack"></i>

                                <input type="number"
                                       step="0.01"
                                       name="cost"
                                       class="form-control product-edit-input"
                                       value="{{ $product->cost }}"
                                       {{ $product->ingredients->count() > 0 ? 'readonly' : '' }}>
                            </div>

                            @if($product->ingredients->count() > 0)
                                <span class="product-field-help">
                                    <i class="bi bi-calculator me-1"></i>
                                    Calculado automáticamente por la receta.
                                </span>
                            @endif
                        </div>


                        <div class="col-md-4">
                            <label class="form-label product-edit-label">
                                Precio de venta
                                <span class="text-danger">*</span>
                            </label>

                            <div class="product-input-icon">
                                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);z-index:4;color:var(--text-muted);font-size:.72rem;font-weight:800;">S/</span>

                                <input type="number"
                                       step="0.01"
                                       name="price"
                                       class="form-control product-edit-input"
                                       value="{{ $product->price }}"
                                       required>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <label class="form-label product-edit-label">
                                Stock actual
                            </label>

                            <div class="product-input-icon">
                                <i class="bi bi-boxes"></i>

                                <input type="text"
                                       class="form-control product-edit-input"
                                       value="{{ $product->stock_display }}"
                                       readonly>
                            </div>

                            <span class="product-field-help">
                                El stock se modifica desde Ajustar stock.
                            </span>
                        </div>

                    </div>


                    {{-- Rentabilidad --}}
                    @if($product->ingredients->count() > 0)

                        @php
                            $recipeCost = $product->recipe_cost;
                            $margin = $product->price - $recipeCost;
                            $marginPercent = $product->price > 0
                                ? ($margin / $product->price) * 100
                                : 0;
                        @endphp

                        <div class="product-profitability">

                            <div class="product-profitability-content">

                                <div>
                                    <div class="product-profitability-head">
                                        <i class="bi bi-graph-up-arrow"></i>
                                        Escandallo / Rentabilidad
                                    </div>

                                    <p class="product-profitability-description">
                                        Comparación del costo de receta con el precio de venta
                                    </p>
                                </div>


                                <div class="profit-stat">
                                    <span>Costo receta</span>
                                    <strong>
                                        S/ {{ number_format($recipeCost, 2) }}
                                    </strong>
                                </div>


                                <div class="profit-stat margin">
                                    <span>Margen bruto</span>
                                    <strong>
                                        S/ {{ number_format($margin, 2) }}
                                        ({{ number_format($marginPercent, 1) }}%)
                                    </strong>
                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- Configuración --}}
                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="product-option-card"
                                   for="saleableCheck">

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_saleable"
                                       id="saleableCheck"
                                       {{ $product->is_saleable ? 'checked' : '' }}>

                                <span>
                                    <span class="product-option-title">
                                        Disponible para la venta
                                    </span>

                                    <span class="product-option-text">
                                        Se mostrará en el POS y en el menú.
                                        Si se desactiva, podrá utilizarse únicamente como insumo.
                                    </span>
                                </span>

                            </label>

                        </div>


                        <div class="col-md-6">

                            <label class="product-option-card"
                                   for="controlsStockCheck">

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="controls_stock"
                                       id="controlsStockCheck"
                                       {{ $product->controls_stock ? 'checked' : '' }}>

                                <span>
                                    <span class="product-option-title">
                                        Controlar stock
                                    </span>

                                    <span class="product-option-text">
                                        Actívalo para productos controlados por unidades.
                                        Si tiene receta, se controlarán sus ingredientes.
                                    </span>
                                </span>

                            </label>

                        </div>

                    </div>


                    {{-- Carta digital --}}
                    <div class="product-digital-options">

                        <div class="digital-options-title">
                            <i class="bi bi-qr-code-scan"></i>
                            Opciones para Carta Digital (Menú QR)
                        </div>


                        <div class="row g-3">

                            <div class="col-md-4">

                                <label class="form-label product-edit-label">
                                    Precio de promoción
                                </label>

                                <div class="product-input-icon">
                                    <i class="bi bi-tag"></i>

                                    <input type="number"
                                           step="0.01"
                                           name="promotional_price"
                                           class="form-control product-edit-input"
                                           value="{{ $product->promotional_price }}"
                                           placeholder="0.00">
                                </div>

                                <span class="product-field-help">
                                    Si tiene valor, reemplazará visualmente el precio normal.
                                </span>

                            </div>


                            <div class="col-md-4">

                                <label class="form-label product-edit-label">
                                    Recomendación
                                </label>

                                <div class="digital-switch">

                                    <div class="form-check form-switch mb-0">

                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="is_chef_recommendation"
                                               id="chefCheck"
                                               {{ $product->is_chef_recommendation ? 'checked' : '' }}>

                                        <label class="form-check-label ms-1"
                                               for="chefCheck">
                                            Sugerencia del Chef
                                        </label>

                                    </div>

                                </div>

                            </div>


                            <div class="col-md-4">

                                <label class="form-label product-edit-label">
                                    Novedad
                                </label>

                                <div class="digital-switch">

                                    <div class="form-check form-switch mb-0">

                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="is_new"
                                               id="newCheck"
                                               {{ $product->is_new ? 'checked' : '' }}>

                                        <label class="form-check-label ms-1"
                                               for="newCheck">
                                            Producto Nuevo
                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Imagen --}}
                    <div class="mt-4">

                        <label class="form-label product-edit-label">
                            Imagen del producto
                        </label>

                        <div class="product-image-box">

                            @if($product->image)

                                <img src="{{ asset('storage/'.$product->image) }}"
                                     class="product-current-image"
                                     alt="{{ $product->name }}">

                            @else

                                <div class="product-image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>

                            @endif


                            <div class="flex-grow-1">

                                <input type="file"
                                       name="image"
                                       class="form-control product-edit-input"
                                       accept="image/*">

                                <span class="product-field-help">
                                    Selecciona una imagen solo si deseas reemplazar la actual.
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             RECETA / INSUMOS
        ====================================================== --}}
        <div class="col-lg-4">

            <div class="product-edit-card recipe-card">

                <div class="product-section-head recipe-header">

                    <div class="d-flex align-items-center gap-2">

                        <i class="bi bi-basket"></i>

                        <div>
                            <h6 class="product-section-title">
                                Receta / Insumos
                            </h6>

                            <p class="product-section-description">
                                Composición del producto
                            </p>
                        </div>

                    </div>


                    <button type="button"
                            class="btn recipe-add-btn"
                            onclick="addIngredient()">
                        <i class="bi bi-plus-lg"></i>
                        Agregar
                    </button>

                </div>


                <div class="product-edit-body">

                    <p class="recipe-description">
                        Selecciona los insumos que componen este producto.
                        Al venderlo, las cantidades correspondientes se
                        descontarán automáticamente.
                    </p>


                    <div id="ingredients-list"
                         class="recipe-list">

                        @foreach($product->ingredients as $ingredient)

                            <div class="recipe-row"
                                 id="row-{{ $ingredient->id }}">

                                <div class="recipe-name"
                                     title="{{ $ingredient->name }} ({{ $ingredient->unit_display }})">
                                    {{ $ingredient->name }} ({{ $ingredient->unit_display }})
                                </div>

                                <input type="number"
                                       step="0.001" min="0"
                                       name="ingredients[{{ $ingredient->id }}]"
                                       value="{{ $ingredient->pivot->quantity }}"
                                       class="form-control recipe-quantity text-center"
                                       placeholder="Cant.">

                                <button type="button"
                                        class="btn recipe-remove"
                                        onclick="document.getElementById('row-{{ $ingredient->id }}').remove()"
                                        title="Quitar insumo">
                                    <i class="bi bi-x-lg"></i>
                                </button>

                            </div>

                        @endforeach

                    </div>


                    {{-- Plantilla para nuevo insumo --}}
                    <div class="d-none"
                         id="ingredient-select-template">

                        <div class="recipe-row ingredient-row">

                            <select class="form-select recipe-select"
                                    onchange="setIngredientName(this)">

                                <option value="">
                                    Seleccionar insumo
                                </option>

                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}">
                                        {{ $ing->name }} ({{ $ing->unit_display }})
                                    </option>
                                @endforeach

                            </select>

                            <input type="number"
                                   step="0.001" min="0"
                                   class="form-control recipe-quantity text-center"
                                   placeholder="Cant.">

                            <button type="button"
                                    class="btn recipe-remove"
                                    onclick="this.parentElement.remove()"
                                    title="Quitar insumo">
                                <i class="bi bi-x-lg"></i>
                            </button>

                        </div>

                    </div>

                </div>


                <div class="product-save-area">

                    <button type="submit"
                            class="btn product-save-btn">

                        <i class="bi bi-check-lg"></i>
                        Guardar cambios

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>
<script>
    function addIngredient() {
        let container = document.getElementById('ingredients-list');
        let template = document.getElementById('ingredient-select-template').innerHTML;
        let div = document.createElement('div');
        div.innerHTML = template;
        container.appendChild(div.firstElementChild);
    }

    function setIngredientName(select) {
        let row = select.parentElement;
        let inputQty = row.querySelector('input[type="number"]');
        if (select.value) {
            inputQty.name = "ingredients[" + select.value + "]";
            inputQty.required = true;
        }
    }
</script>

<style>
/* DARK MODE - OPCIONES EDITAR PRODUCTO */

html[data-color-mode="dark"] .product-option-card {
    background: #15263a !important;
    border-color: #30475f !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .product-option-card:hover {
    background: #192c42 !important;
    border-color: #3b5874 !important;
}

html[data-color-mode="dark"] .product-option-title {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .product-option-text {
    color: #91a8bd !important;
}

/* Checkbox desactivado */
html[data-color-mode="dark"] .product-option-card .form-check-input {
    background-color: #0f1e2e !important;
    border-color: #48617a !important;
}

/* Checkbox activado: conserva el naranja del sistema */
html[data-color-mode="dark"] .product-option-card .form-check-input:checked {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
}

/* Foco */
html[data-color-mode="dark"] .product-option-card .form-check-input:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 .2rem color-mix(
        in srgb,
        var(--primary) 20%,
        transparent
    ) !important;
}

</style>

@endsection