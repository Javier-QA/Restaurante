@extends('layouts.app')

@section('content')

<style>
.product-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 36px;
    margin-bottom: 11px;
    padding: 0 12px;
    border: 1px solid var(--border-soft);
    border-radius: 9px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .74rem;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 2px 5px rgba(15, 23, 42, .03);
    transition: .16s ease;
}

.product-back-btn i {
    font-size: .78rem;
}

.product-back-btn:hover {
    border-color: #94a3b8;
    background: var(--light-bg);
    color: var(--text-main);
    transform: translateY(-1px);
}
</style>

<div class="container-fluid px-0">
    <div class="col-12 col-xl-10">
        <div class="mb-4">

            <a href="{{ route('products.index') }}"
               class="product-back-btn">
                <i class="bi bi-arrow-left"></i>
                Volver a Productos
            </a>

            <h2 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-box-seam-fill text-dark"></i>
                Nuevo Producto
            </h2>

        </div>

        <div class="card border-0 shadow-sm">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="card-body p-4">
                @csrf
        <div class="col-12 mb-3">
            <label for="preparationArea" class="form-label fw-bold">Área de preparación</label>
            <select id="preparationArea" name="preparation_area" class="form-select" required>
                <option value="kitchen" @selected(old('preparation_area', $product->preparation_area ?? 'kitchen') === 'kitchen')>Cocina</option>
                <option value="barra" @selected(old('preparation_area', $product->preparation_area ?? 'kitchen') === 'barra')>Barra</option>
            </select>
        </div>

                
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Información General</h6>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Nombre del Producto <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-lg" placeholder="Ej: Lomo Saltado" required autofocus>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Categoría <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="" selected disabled>-- Seleccionar --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}{{ !$cat->is_active ? ' (oculta en POS)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @include('products.inventory-fields')
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Costo Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" step="0.01" name="cost" class="form-control" placeholder="0.00" value="0.00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Precio de Venta <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Stock Inicial</label>
                        <input type="number" name="stock" step="0.001" min="0" class="form-control" placeholder="0">
                        <small class="text-muted" style="font-size: 0.75rem;">Se creará un registro de entrada en el Kardex.</small>
                    </div>
                </div>

                <div class="form-check form-switch mb-4 bg-light p-3 rounded border">
                    <input class="form-check-input" type="checkbox" name="is_saleable" id="saleableCheck" checked>
                    <label class="form-check-label fw-bold ms-2" for="saleableCheck">
                        Disponible para la venta (POS y Menú)
                    </label>
                    <small class="d-block text-muted ms-5" style="font-size: 0.75rem;">Si se desmarca, solo será un insumo para recetas.</small>
                </div>

                <div class="form-check form-switch mb-4 bg-light p-3 rounded border">
    <input class="form-check-input" type="checkbox" name="controls_stock" id="controlsStockCheck">
    <label class="form-check-label fw-bold ms-2" for="controlsStockCheck">
        Controlar stock de este producto
    </label>
    <small class="d-block text-muted ms-5" style="font-size: 0.75rem;">
        Actívalo para controlar existencias por la unidad de medida seleccionada. Si el producto tiene receta, se controlarán sus ingredientes.
    </small>
</div>
<div class="product-digital-options bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 mb-4 p-3">
                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-qr-code-scan me-2"></i>Opciones para Carta Digital (Menú QR)</h6>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Precio de Promoción</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" name="promotional_price" class="form-control" placeholder="0.00">
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Si tiene valor, tachará el precio normal.</small>
                        </div>
                        <div class="col-md-4 d-flex align-items-center mt-4 pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_chef_recommendation" id="chefCheck">
                                <label class="form-check-label fw-bold text-dark ms-2" for="chefCheck">Sugerencia del Chef</label>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center mt-4 pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_new" id="newCheck">
                                <label class="form-check-label fw-bold text-dark ms-2" for="newCheck">Producto Nuevo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Imagen (Opcional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                        <i class="bi bi-save me-2"></i> Guardar Producto
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection