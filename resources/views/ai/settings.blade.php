@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="bi bi-sliders2-vertical me-2"></i>
                                Configuración de IA
                            </h4>
                            <p class="text-muted mb-0 small">
                                Selecciona el proveedor que utilizarán Chat IA y Asistente IA.
                            </p>
                        </div>

                        <span class="badge {{ $config['configured'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} px-3 py-2">
                            <i class="bi bi-circle-fill me-1"></i>
                            {{ $config['configured'] ? 'Configurada' : 'Pendiente' }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ai.settings.update') }}" id="aiSettingsForm">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Proveedor de IA</label>
                                <select
                                    name="provider"
                                    id="aiProvider"
                                    class="form-select"
                                    required
                                >
                                    @foreach($providers as $key => $provider)
                                        <option
                                            value="{{ $key }}"
                                            data-url="{{ $provider['base_url'] }}"
                                            data-model="{{ $provider['model'] }}"
                                            data-requires-key="{{ $provider['requires_key'] ? '1' : '0' }}"
                                            @selected($config['provider'] === $key)
                                        >
                                            {{ $provider['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    El cambio se aplicará a Chat IA y Asistente IA.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Modelo</label>
                                <input
                                    type="text"
                                    name="model"
                                    id="aiModel"
                                    class="form-control"
                                    value="{{ old('model', $config['model']) }}"
                                    maxlength="150"
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">URL base de la API</label>
                                <input
                                    type="url"
                                    name="base_url"
                                    id="aiBaseUrl"
                                    class="form-control"
                                    value="{{ old('base_url', $config['base_url']) }}"
                                    maxlength="500"
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    API Key
                                </label>
                                <input
                                    type="password"
                                    name="api_key"
                                    class="form-control"
                                    maxlength="2000"
                                    autocomplete="new-password"
                                    placeholder="{{ $config['key_masked'] ?: 'Introduce la API Key' }}"
                                >
                                <div class="form-text">
                                    @if($config['key_masked'])
                                        Ya existe una clave configurada. Déjala vacía para conservarla.
                                    @else
                                        Para Ollama local no es necesaria.
                                    @endif
                                    La clave nunca se muestra completa en pantalla.
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <div class="fw-semibold mb-1">
                                <i class="bi bi-shield-check me-1"></i>
                                Seguridad
                            </div>
                            <small>
                                La selección del proveedor solamente cambia el servicio de lenguaje.
                                Las consultas del restaurante continúan pasando por las validaciones
                                de SQL, las vistas autorizadas y la conexión MySQL de solo lectura.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('ai.chat') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="bi bi-check2-circle me-1"></i>
                                Guardar configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provider = document.getElementById('aiProvider');
    const model = document.getElementById('aiModel');
    const url = document.getElementById('aiBaseUrl');

    function syncProviderDefaults(force = false) {
        const option = provider.options[provider.selectedIndex];

        if (!option) {
            return;
        }

        if (force || !model.value.trim()) {
            model.value = option.dataset.model || '';
        }

        if (force || !url.value.trim()) {
            url.value = option.dataset.url || '';
        }
    }

    provider.addEventListener('change', function () {
        syncProviderDefaults(true);
    });
});
</script>
@endpush
@endsection
