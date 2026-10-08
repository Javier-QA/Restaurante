<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="inventoryUnit" class="form-label fw-bold small">Unidad de medida</label>
        <select id="inventoryUnit" name="unit" class="form-select" required>
            @foreach(\App\Models\Product::UNITS as $code => $label)
                <option value="{{ $code }}" @selected(old('unit', $product->unit ?? 'und') === $code)>{{ $label }}</option>
            @endforeach
        </select>
        <small class="text-muted">Stock, costo por unidad y cantidades de receta usan esta misma unidad. No hay conversión automática.</small>
        @if(isset($product) && !$product->unit)
            <small class="d-block text-warning">Producto anterior: confirme la unidad real antes de guardar. Las cantidades existentes se conservan.</small>
        @endif
        @error('unit')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="minimumStock" class="form-label fw-bold small">Stock mínimo</label>
        <input id="minimumStock" type="number" name="minimum_stock" class="form-control" step="0.001" min="0" max="99999999999" value="{{ old('minimum_stock', $product->minimum_stock ?? 5) }}">
        <small class="text-muted">Aviso de stock bajo en la unidad seleccionada.</small>
        @error('minimum_stock')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>
