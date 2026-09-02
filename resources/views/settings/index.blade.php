@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold text-dark mb-0">

                        <i class="bi bi-gear-fill me-2"></i>
                        Configuración

                    </h2>

                    <p class="text-muted mb-0">
                        Personaliza la identidad y región de tu negocio
                    </p>

                </div>

            </div>


            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <form action="{{ route('settings.update') }}"
                          method="POST"
                          enctype="multipart/form-data">

                        @csrf


                        {{-- =====================================================
                             DATOS DE LA EMPRESA
                        ====================================================== --}}

                        <h5 class="fw-bold text-primary mb-3">
                            Datos de la Empresa
                        </h5>


                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    Nombre del Restaurante
                                </label>

                                <input type="text"
                                       name="company_name"
                                       class="form-control"
                                       value="{{ $settings['company_name'] ?? '' }}"
                                       placeholder="Ej: Restaurante Vito"
                                       required>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    Teléfono / Pedidos
                                </label>

                                <input type="text"
                                       name="company_phone"
                                       class="form-control"
                                       value="{{ $settings['company_phone'] ?? '' }}"
                                       placeholder="Ej: 999-888-777">

                            </div>


                            <div class="col-12">

                                <label class="form-label fw-bold">
                                    Dirección
                                </label>

                                <input type="text"
                                       name="company_address"
                                       class="form-control"
                                       value="{{ $settings['company_address'] ?? '' }}"
                                       placeholder="Ej: Av. Principal 123, Ica">

                            </div>

                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =====================================================
                             REGIÓN Y SISTEMA
                        ====================================================== --}}

                        <h5 class="fw-bold text-primary mb-3">
                            Región y Sistema
                        </h5>


                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label class="form-label fw-bold">

                                    <i class="bi bi-clock"></i>
                                    Zona Horaria

                                </label>


                                <select name="timezone"
                                        class="form-select bg-light border-primary">

                                    @foreach($timezones as $tz => $label)

                                        <option value="{{ $tz }}"
                                            {{ ($settings['timezone'] ?? 'America/Lima') == $tz ? 'selected' : '' }}>

                                            {{ $label }}

                                        </option>

                                    @endforeach

                                </select>


                                <small class="text-muted">

                                    Hora actual del sistema:

                                    <strong>
                                        {{ \Carbon\Carbon::now()->format('H:i:s') }}
                                    </strong>

                                </small>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">
                                    Moneda
                                </label>


                                <select name="currency_symbol"
                                        class="form-select">

                                    <option value="S/"
                                        {{ ($settings['currency_symbol'] ?? '') == 'S/' ? 'selected' : '' }}>

                                        S/ (Soles)

                                    </option>


                                    <option value="$"
                                        {{ ($settings['currency_symbol'] ?? '') == '$' ? 'selected' : '' }}>

                                        $ (Dólares)

                                    </option>


                                    <option value="€"
                                        {{ ($settings['currency_symbol'] ?? '') == '€' ? 'selected' : '' }}>

                                        € (Euros)

                                    </option>

                                </select>

                            </div>


                            <div class="col-md-12">

                                <label class="form-label fw-bold">
                                    Mensaje Pie de Ticket
                                </label>

                                <input type="text"
                                       name="ticket_footer"
                                       class="form-control"
                                       value="{{ $settings['ticket_footer'] ?? '¡Gracias por su visita!' }}">

                            </div>

                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =====================================================
                             LOGOTIPO
                        ====================================================== --}}

                        <h5 class="fw-bold text-primary mb-3">
                            Logotipo
                        </h5>


                        <div class="row align-items-center mb-4">

                            <div class="col-md-8">

                                <label class="form-label">
                                    Subir Logo (Ticket y Sistema)
                                </label>


                                <input type="file"
                                       name="company_logo"
                                       class="form-control"
                                       accept="image/*">


                                <small class="text-muted">
                                    Formatos de imagen permitidos.
                                    Tamaño máximo: 2 MB.
                                </small>

                            </div>


                            <div class="col-md-4 text-center">

                                @if(isset($settings['company_logo']) && $settings['company_logo'])

                                    <img src="{{ asset('storage/'.$settings['company_logo']) }}"
                                         class="img-thumbnail"
                                         style="max-height: 80px;">

                                @else

                                    <div class="p-3 border rounded bg-light text-muted">

                                        <i class="bi bi-image fs-1"></i>

                                    </div>

                                @endif

                            </div>

                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =====================================================
                             PAGOS DIGITALES
                        ====================================================== --}}

                        <h5 class="fw-bold text-primary mb-1">

                            <i class="bi bi-qr-code me-2"></i>
                            Pagos Digitales

                        </h5>


                        <p class="text-muted small mb-4">

                            Configura los códigos QR que aparecerán
                            al momento de cobrar una venta mediante
                            Yape o Plin.

                        </p>


                        <div class="row g-4">


                            {{-- =================================================
                                 YAPE
                            ================================================== --}}

                            <div class="col-md-6">

                                <div class="card border h-100">

                                    <div class="card-body">

                                        <div class="d-flex align-items-center mb-3">

                                            <div class="bg-light rounded p-2 me-2">

                                                <i class="bi bi-qr-code fs-4 text-dark"></i>

                                            </div>

                                            <div>

                                                <h6 class="fw-bold mb-0">
                                                    QR de Yape
                                                </h6>

                                                <small class="text-muted">
                                                    Código para recibir pagos por Yape
                                                </small>

                                            </div>

                                        </div>


                                        <label class="form-label fw-bold">

                                            Seleccionar QR de Yape

                                        </label>


                                        <input type="file"
                                               name="yape_qr"
                                               class="form-control"
                                               accept="image/*">


                                        <small class="text-muted d-block mt-2">

                                            Imagen máxima: 2 MB.

                                        </small>


                                        <div class="text-center mt-3 p-3 border rounded bg-light">

                                            @if(isset($settings['yape_qr']) && $settings['yape_qr'])

                                                <img src="{{ asset('storage/'.$settings['yape_qr']) }}"
                                                     alt="QR Yape"
                                                     class="img-fluid"
                                                     style="max-width: 220px; max-height: 220px; object-fit: contain;">

                                                <div class="mt-2">

                                                    <span class="badge bg-success">

                                                        QR configurado

                                                    </span>

                                                </div>

                                            @else

                                                <i class="bi bi-qr-code fs-1 text-muted"></i>

                                                <p class="text-muted small mb-0 mt-2">

                                                    No hay un QR de Yape configurado.

                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                                 PLIN
                            ================================================== --}}

                            <div class="col-md-6">

                                <div class="card border h-100">

                                    <div class="card-body">

                                        <div class="d-flex align-items-center mb-3">

                                            <div class="bg-light rounded p-2 me-2">

                                                <i class="bi bi-qr-code fs-4 text-dark"></i>

                                            </div>

                                            <div>

                                                <h6 class="fw-bold mb-0">
                                                    QR de Plin
                                                </h6>

                                                <small class="text-muted">
                                                    Código para recibir pagos por Plin
                                                </small>

                                            </div>

                                        </div>


                                        <label class="form-label fw-bold">

                                            Seleccionar QR de Plin

                                        </label>


                                        <input type="file"
                                               name="plin_qr"
                                               class="form-control"
                                               accept="image/*">


                                        <small class="text-muted d-block mt-2">

                                            Imagen máxima: 2 MB.

                                        </small>


                                        <div class="text-center mt-3 p-3 border rounded bg-light">

                                            @if(isset($settings['plin_qr']) && $settings['plin_qr'])

                                                <img src="{{ asset('storage/'.$settings['plin_qr']) }}"
                                                     alt="QR Plin"
                                                     class="img-fluid"
                                                     style="max-width: 220px; max-height: 220px; object-fit: contain;">

                                                <div class="mt-2">

                                                    <span class="badge bg-success">

                                                        QR configurado

                                                    </span>

                                                </div>

                                            @else

                                                <i class="bi bi-qr-code fs-1 text-muted"></i>

                                                <p class="text-muted small mb-0 mt-2">

                                                    No hay un QR de Plin configurado.

                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =====================================================
                             GUARDAR
                        ====================================================== --}}

                        <div class="mt-5 d-flex justify-content-end">

                            <button type="submit"
                                    class="btn btn-primary px-5 fw-bold shadow">

                                <i class="bi bi-save me-2"></i>

                                Guardar Configuración

                            </button>

                        </div>


                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection