<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="inventoryUnit" class="form-label fw-bold small">Unidad de medida</label>
        <div style="position: relative;">
        <select style="appearance: none !important; -webkit-appearance: none !important; background-image: none !important; padding-right: 2.5rem !important;" id="inventoryUnit" name="unit" class="form-select" required>
            @foreach(\App\Models\Product::UNITS as $code => $label)
                <option value="{{ $code }}" @selected(old('unit', $product->unit ?? 'und') === $code)>{{ $label }}</option>
            @endforeach
        </select>
        <svg aria-hidden="true" viewBox="0 0 16 16" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);width:16px;height:16px;pointer-events:none;fill:none;stroke:currentColor;stroke-width:2;"><path d="m4 6 4 4 4-4"/></svg>
        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const unit = document.getElementById('inventoryUnit');
    const form = unit.closest('form');
    function updateInventoryPrecision() {
        const wholeUnits = ['und', 'paq', 'caja'].includes(unit.value);
        form.querySelectorAll('input[name="stock"], input[name="minimum_stock"]').forEach(function (input) {
            input.step = wholeUnits ? '1' : '0.001';
            input.setCustomValidity(wholeUnits && input.value !== '' && !Number.isInteger(Number(input.value))
                ? 'Unidades, paquetes y cajas requieren números enteros.' : '');
        });
    }
    unit.addEventListener('change', updateInventoryPrecision);
    form.querySelectorAll('input[name="stock"], input[name="minimum_stock"]').forEach(function (input) {
        input.addEventListener('beforeinput', function (event) {
            if (['und', 'paq', 'caja'].includes(unit.value) && event.data && /[.,eE]/.test(event.data)) {
                event.preventDefault();
            }
        });
        input.addEventListener('input', updateInventoryPrecision);
    });
    form.addEventListener('submit', function (event) {
        updateInventoryPrecision();
        if (!form.reportValidity()) event.preventDefault();
    });
    updateInventoryPrecision();
});
</script>
