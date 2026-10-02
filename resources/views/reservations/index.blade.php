@extends('layouts.app')

@section('content')
<style>
    .reservations-page {
        color: var(--text-main);
    }

    .reservations-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .reservations-title {
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .reservations-title i {
        color:var(--text-main);
    }

    .reservations-subtitle {
        color: var(--text-muted);
        margin: .3rem 0 0;
    }

    .btn-reservation-primary {
        background: var(--primary);
        border: 1px solid var(--primary);
        color: #fff;
        border-radius: 12px;
        padding: .7rem 1.1rem;
        font-weight: 700;
        transition: .2s ease;
    }

    .btn-reservation-primary:hover,
    .btn-reservation-primary:focus {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #fff;
        transform: translateY(-1px);
    }

    .reservation-stat {
        background: var(--card-bg);
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-soft);
        padding: 1.1rem;
        height: 100%;
    }

    .reservation-stat-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary) 12%, transparent);
        color: var(--primary);
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .reservation-stat-label {
        color: var(--text-muted);
        font-size: .82rem;
        font-weight: 600;
        margin-bottom: .1rem;
    }

    .reservation-stat-value {
        color: var(--text-main);
        font-size: 1.55rem;
        font-weight: 800;
        line-height: 1;
    }

    .reservation-card {
        background: var(--card-bg);
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .reservation-card:hover {
        transform: translateY(-2px);
    }

    .reservation-card.cancelled {
        opacity: .7;
    }

    .reservation-client {
        color: var(--text-main);
        font-weight: 800;
    }

    .reservation-phone,
    .reservation-muted {
        color: var(--text-muted);
    }

    .reservation-info {
        background: color-mix(in srgb, var(--primary) 5%, var(--card-bg));
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: .8rem;
    }

    .reservation-info-label {
        display: block;
        color: var(--text-muted);
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .15rem;
    }

    .reservation-info-value {
        color: var(--text-main);
        font-weight: 800;
    }

    .reservation-note {
        background: color-mix(in srgb, var(--primary) 7%, var(--card-bg));
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        color: var(--text-main);
        padding: .7rem .8rem;
        font-size: .88rem;
    }

    .reservation-note i {
        color: var(--primary);
    }

    .reservation-modal .modal-content {
        border: 0;
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-soft);
    }

    .reservation-modal .modal-header {
        background: var(--primary);
        color: #fff;
        border: 0;
        padding: 1.2rem 1.4rem;
    }

    .reservation-modal .modal-body {
        padding: 1.4rem;
    }

    .reservation-modal .modal-footer {
        border-top: 1px solid var(--border-soft);
        padding: 1rem 1.4rem;
    }

    .reservation-modal .form-label {
        color: var(--text-main);
        font-size: .86rem;
        font-weight: 700;
    }

    .reservation-modal .form-control,
    .reservation-modal .form-select {
        border-color: var(--border-soft);
        border-radius: 10px;
        padding: .65rem .8rem;
    }

    .reservation-modal .form-control:focus,
    .reservation-modal .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--primary) 15%, transparent);
    }

    @media (max-width: 767.98px) {
        .reservations-header {
            align-items: stretch;
            flex-direction: column;
        }

        .btn-reservation-primary {
            width: 100%;
        }
    }
</style>
<div class="container-fluid reservations-page">

    <div class="reservations-header">
        <div>
            <h2 class="reservations-title">
                <i class="bi bi-calendar-check me-2"></i>Reservas
            </h2>
            <p class="reservations-subtitle">
                Gestión y seguimiento de reservas del restaurante
            </p>
        </div>

        <button type="button"
                class="btn btn-reservation-primary"
                data-bs-toggle="modal"
                data-bs-target="#createReservationModal">
            <i class="bi bi-plus-lg me-2"></i>Nueva Reserva
        </button>
    </div>

    <div class="row g-3 mb-4 reservation-kpi-row">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="reservation-kpi reservation-kpi-today">
                <div class="reservation-kpi-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div class="reservation-kpi-content">
                    <span class="reservation-kpi-label">Reservas de hoy</span>
                    <div class="reservation-kpi-value">{{ $stats['today'] }}</div>
                    <div class="reservation-kpi-sub">
                        <i class="bi bi-calendar-check me-1"></i>
                        Reservas programadas para hoy
                    </div>
                </div>

                <div class="reservation-kpi-badge">HOY</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="reservation-kpi reservation-kpi-pending">
                <div class="reservation-kpi-icon">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div class="reservation-kpi-content">
                    <span class="reservation-kpi-label">Pendientes</span>
                    <div class="reservation-kpi-value">{{ $stats['pending'] }}</div>
                    <div class="reservation-kpi-sub">
                        <i class="bi bi-hourglass-split me-1"></i>
                        Esperando confirmación
                    </div>
                </div>

                <div class="reservation-kpi-badge">PEND.</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="reservation-kpi reservation-kpi-confirmed">
                <div class="reservation-kpi-icon">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div class="reservation-kpi-content">
                    <span class="reservation-kpi-label">Confirmadas</span>
                    <div class="reservation-kpi-value">{{ $stats['confirmed'] }}</div>
                    <div class="reservation-kpi-sub">
                        <i class="bi bi-check2-circle me-1"></i>
                        Reservas confirmadas
                    </div>
                </div>

                <div class="reservation-kpi-badge">OK</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="reservation-kpi reservation-kpi-people">
                <div class="reservation-kpi-icon">
                    <i class="bi bi-people"></i>
                </div>

                <div class="reservation-kpi-content">
                    <span class="reservation-kpi-label">Personas para hoy</span>
                    <div class="reservation-kpi-value">{{ $stats['people_today'] }}</div>
                    <div class="reservation-kpi-sub">
                        <i class="bi bi-person-check me-1"></i>
                        Personas esperadas hoy
                    </div>
                </div>

                <div class="reservation-kpi-badge">HOY</div>
            </div>
        </div>

    </div>
    <div class="row g-3">
        @forelse($reservations as $res)
            <div class="col-12 col-xl-6">
                <div class="reservation-card {{ $res->status === 'cancelled' ? 'cancelled' : '' }}">
                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="reservation-client-icon">
                                    <i class="bi bi-person"></i>
                                </div>

                                <div>
                                    <h5 class="reservation-client mb-1">
                                        {{ $res->client_name }}
                                    </h5>

                                    <div class="reservation-phone">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $res->phone ?: 'Sin teléfono' }}
                                    </div>
                                </div>
                            </div>

                            @if($res->status === 'confirmed')
                                <span class="reservation-status status-confirmed">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Confirmada
                                </span>
                            @elseif($res->status === 'cancelled')
                                <span class="reservation-status status-cancelled">
                                    <i class="bi bi-x-circle-fill"></i>
                                    Cancelada
                                </span>
                            @else
                                <span class="reservation-status status-pending">
                                    <i class="bi bi-clock-fill"></i>
                                    Pendiente
                                </span>
                            @endif
                        </div>

                        <div class="reservation-info mb-3">
                            <div class="row g-0 align-items-center">

                                <div class="col-6 col-md-3 reservation-detail">
                                    <div class="reservation-detail-icon">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <div>
                                        <span class="reservation-info-label">Fecha</span>
                                        <span class="reservation-info-value">
                                            {{ $res->reservation_time->format('d/m') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-6 col-md-3 reservation-detail">
                                    <div class="reservation-detail-icon detail-time">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <div>
                                        <span class="reservation-info-label">Hora</span>
                                        <span class="reservation-info-value reservation-time">
                                            {{ $res->reservation_time->format('H:i') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-6 col-md-3 reservation-detail">
                                    <div class="reservation-detail-icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div>
                                        <span class="reservation-info-label">Personas</span>
                                        <span class="reservation-info-value">
                                            {{ $res->people }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-6 col-md-3 reservation-detail border-end-0">
                                    <div class="reservation-detail-icon detail-table">
                                        <i class="bi bi-grid"></i>
                                    </div>
                                    <div>
                                        <span class="reservation-info-label">Mesa</span>
                                        <span class="reservation-info-value reservation-table">
                                            {{ $res->table->name ?? 'Por asignar' }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        @if($res->note)
                            <div class="reservation-note mb-3">
                                <i class="bi bi-sticky me-2"></i>
                                {{ $res->note }}
                            </div>
                        @endif

                        <button type="button"
                                class="btn reservation-edit-btn w-100 mb-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editReservationModal{{ $res->id }}">
                            <i class="bi bi-pencil-square me-2"></i>Editar reserva
                        </button>

                        @if($res->status === 'pending')
                            <div class="row g-2">
                                <div class="col-6">
                                    <form action="{{ route('reservations.status', $res->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">

                                        <button type="button"
        class="btn reservation-confirm-btn w-100 reservation-action-btn"
        data-action="confirm"
        data-client="{{ $res->client_name }}">
                                            <i class="bi bi-check-lg me-2"></i>Confirmar
                                        </button>
                                    </form>
                                </div>

                                <div class="col-6">
                                    <form action="{{ route('reservations.status', $res->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">

                                        <button type="button"
        class="btn reservation-cancel-btn w-100 reservation-action-btn"
        data-action="cancel"
        data-client="{{ $res->client_name }}">
                                            <i class="bi bi-x-lg me-2"></i>Cancelar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('reservations.destroy', $res->id) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="button"
        class="btn reservation-delete-btn w-100 reservation-action-btn"
        data-action="delete"
        data-client="{{ $res->client_name }}">
                                    <i class="bi bi-trash3 me-2"></i>Eliminar historial
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
            <div class="modal fade reservation-modal"
                 id="editReservationModal{{ $res->id }}"
                 tabindex="-1"
                 aria-hidden="true">

                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <form action="{{ route('reservations.update', $res->id) }}"
                          method="POST"
                          class="modal-content">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-bold mb-1">
                                    <i class="bi bi-pencil-square me-2"></i>Editar reserva
                                </h5>
                                <small class="opacity-75">
                                    Actualiza los datos de la reserva
                                </small>
                            </div>

                            <button type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"
                                    aria-label="Cerrar">
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-person me-1"></i>
                                        Nombre del cliente
                                    </label>

                                    <input type="text"
                                           name="client_name"
                                           class="form-control"
                                           value="{{ $res->client_name }}"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-telephone me-1"></i>
                                        Teléfono
                                    </label>

                                    <input type="text"
                                           name="phone"
                                           class="form-control"
                                           value="{{ $res->phone }}"
                                           placeholder="Opcional">
                                </div>

                                @php
                                    $editHour = $res->reservation_time->format('h');
                                    $editMinute = $res->reservation_time->format('i');
                                    $editPeriod = $res->reservation_time->format('A');
                                @endphp

                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        Fecha de reserva
                                    </label>

                                    <input type="date"
                                           name="reservation_date"
                                           class="form-control"
                                           value="{{ $res->reservation_time->format('Y-m-d') }}"
                                           required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="bi bi-clock me-1"></i>
                                        Hora
                                    </label>

                                    <div class="reservation-time-group">
                                        <select class="form-select reservation-hour-select"
                                                aria-label="Hora">
                                            @for($hour = 1; $hour <= 12; $hour++)
                                                @php $hourValue = sprintf('%02d', $hour); @endphp

                                                <option value="{{ $hourValue }}"
                                                    {{ $editHour === $hourValue ? 'selected' : '' }}>
                                                    {{ $hourValue }}
                                                </option>
                                            @endfor
                                        </select>

                                        <span class="reservation-time-separator">:</span>

                                        <select class="form-select reservation-minute-select"
                                                aria-label="Minutos">
                                            @foreach(['00', '15', '30', '45'] as $minute)
                                                <option value="{{ $minute }}"
                                                    {{ $editMinute === $minute ? 'selected' : '' }}>
                                                    {{ $minute }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <input type="hidden"
                                               name="reservation_hour"
                                               class="reservation-hour-value"
                                               value="{{ $editHour }}:{{ $editMinute }}">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        Periodo
                                    </label>

                                    <select name="reservation_period"
                                            class="form-select"
                                            required>
                                        <option value="AM"
                                            {{ $editPeriod === 'AM' ? 'selected' : '' }}>
                                            AM
                                        </option>

                                        <option value="PM"
                                            {{ $editPeriod === 'PM' ? 'selected' : '' }}>
                                            PM
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="bi bi-people me-1"></i>
                                        Personas
                                    </label>

                                    <div class="reservation-people-control">
                                        <button type="button"
                                                class="reservation-people-btn reservation-people-minus"
                                                aria-label="Disminuir personas">
                                            <i class="bi bi-dash-lg"></i>
                                        </button>

                                        <input type="number"
                                               name="people"
                                               class="form-control reservation-people-input"
                                               value="{{ $res->people }}"
                                               min="1"
                                               required>

                                        <button type="button"
                                                class="reservation-people-btn reservation-people-plus"
                                                aria-label="Aumentar personas">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-grid me-1"></i>
                                        Mesa
                                    </label>

                                    <select name="table_id" class="form-select">
                                        <option value="">Asignar al llegar</option>

                                        @foreach($tables as $table)
                                            <option value="{{ $table->id }}"
                                                {{ $res->table_id == $table->id ? 'selected' : '' }}>
                                                {{ $table->name }}
                                                @if($table->area)
                                                    - {{ $table->area->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-sticky me-1"></i>
                                        Notas o pedidos especiales
                                    </label>

                                    <textarea name="note"
                                              class="form-control"
                                              rows="3"
                                              maxlength="500"
                                              placeholder="Ej: silla para bebé, ubicación especial...">{{ $res->note }}</textarea>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-light px-4"
                                    data-bs-dismiss="modal">
                                Cancelar
                            </button>

                            <button type="submit"
                                    class="btn btn-reservation-primary px-4">
                                <i class="bi bi-check-lg me-2"></i>
                                Guardar cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="reservation-empty text-center">
                    <div class="reservation-empty-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h5 class="fw-bold mt-3 mb-1">No hay reservas próximas</h5>
                    <p class="mb-0">Las nuevas reservas aparecerán aquí.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade reservation-modal"
     id="createReservationModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('reservations.store') }}"
              method="POST"
              class="modal-content">

            @csrf

            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-1">
                        <i class="bi bi-calendar-plus me-2"></i>Nueva reserva
                    </h5>
                    <small class="opacity-75">
                        Registra una nueva visita al restaurante
                    </small>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-person me-1"></i>
                            Nombre del cliente
                        </label>

                        <input type="text"
                               name="client_name"
                               class="form-control"
                               value="{{ old('client_name') }}"
                               placeholder="Ej: José Pérez"
                               maxlength="255"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-telephone me-1"></i>
                            Teléfono
                        </label>

                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="{{ old('phone') }}"
                               placeholder="Opcional"
                               maxlength="30">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-calendar3 me-1"></i>
                            Fecha de reserva
                        </label>

                        <input type="date"
                               name="reservation_date"
                               class="form-control"
                               value="{{ old('reservation_date') }}"
                               required>
                    </div>

                    <div class="col-md-3">
            <label class="form-label">
                <i class="bi bi-clock me-1"></i>
                Hora
            </label>

            <div class="reservation-time-group">
                <select class="form-select reservation-hour-select"
                        aria-label="Hora">
                    @for($hour = 1; $hour <= 12; $hour++)
                        @php $hourValue = sprintf('%02d', $hour); @endphp
                        <option value="{{ $hourValue }}"
                            {{ old('reservation_hour', '08:00') === $hourValue . ':00' ? 'selected' : '' }}>
                            {{ $hourValue }}
                        </option>
                    @endfor
                </select>

                <span class="reservation-time-separator">:</span>

                <select class="form-select reservation-minute-select"
                        aria-label="Minutos">
                    @foreach(['00', '15', '30', '45'] as $minute)
                        <option value="{{ $minute }}">
                            {{ $minute }}
                        </option>
                    @endforeach
                </select>

                <input type="hidden"
                       name="reservation_hour"
                       class="reservation-hour-value"
                       value="{{ old('reservation_hour', '08:00') }}">
            </div>
        </div>

        <div class="col-md-2">
            <label class="form-label">
                Periodo
            </label>

            <select name="reservation_period"
                    class="form-select"
                    required>
                <option value="AM"
                    {{ old('reservation_period') === 'AM' ? 'selected' : '' }}>
                    AM
                </option>

                <option value="PM"
                    {{ old('reservation_period', 'PM') === 'PM' ? 'selected' : '' }}>
                    PM
                </option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">
                <i class="bi bi-people me-1"></i>
                Personas
            </label>

            <div class="reservation-people-control">
                <button type="button"
                        class="reservation-people-btn reservation-people-minus"
                        aria-label="Disminuir personas">
                    <i class="bi bi-dash-lg"></i>
                </button>

                <input type="number"
                       name="people"
                       class="form-control reservation-people-input"
                       value="{{ old('people', 2) }}"
                       min="1"
                       required>

                <button type="button"
                        class="reservation-people-btn reservation-people-plus"
                        aria-label="Aumentar personas">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        </div>
        <div class="col-12">
                        <label class="form-label">
                            <i class="bi bi-grid me-1"></i>
                            Mesa
                            <span class="reservation-optional">Opcional</span>
                        </label>

                        <select name="table_id" class="form-select">
                            <option value="">Asignar al llegar</option>

                            @foreach($tables as $table)
                                <option value="{{ $table->id }}"
                                    {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                    {{ $table->name }}
                                    @if($table->area)
                                        - {{ $table->area->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div class="reservation-field-help">
                            Puedes dejar la mesa pendiente y asignarla posteriormente.
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            <i class="bi bi-sticky me-1"></i>
                            Notas o pedidos especiales
                        </label>

                        <textarea name="note"
                                  class="form-control"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Ej: silla para bebé, ubicación especial...">{{ old('note') }}</textarea>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn reservation-modal-cancel px-4"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn btn-reservation-primary px-4">
                    <i class="bi bi-calendar-check me-2"></i>
                    Agendar reserva
                </button>
            </div>

        </form>
    </div>
</div>
<style>
.reservation-time-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.reservation-time-group .form-select {
    min-width: 0;
    padding-left: 10px;
    padding-right: 28px;
}

.reservation-time-separator {
    color: var(--text-main);
    font-size: 1.1rem;
    font-weight: 800;
}

.reservation-people-control {
    display: flex;
    align-items: stretch;
    min-height: 38px;
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    overflow: hidden;
    background: var(--card-bg);
}

.reservation-people-control:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--primary) 15%, transparent);
}

.reservation-people-btn {
    width: 38px;
    flex: 0 0 38px;
    border: 0;
    background: color-mix(in srgb, var(--primary) 7%, var(--card-bg));
    color: var(--primary);
    font-weight: 800;
    transition: .2s ease;
}

.reservation-people-btn:hover {
    background: color-mix(in srgb, var(--primary) 14%, var(--card-bg));
}

.reservation-people-input {
    min-width: 42px;
    padding-left: 4px;
    padding-right: 4px;
    border: 0 !important;
    border-radius: 0 !important;
    text-align: center;
    font-weight: 700;
    box-shadow: none !important;
    appearance: textfield;
}

.reservation-people-input::-webkit-inner-spin-button,
.reservation-people-input::-webkit-outer-spin-button {
    margin: 0;
    -webkit-appearance: none;
}

.reservation-optional {
    display: inline-block;
    margin-left: .35rem;
    padding: .15rem .45rem;
    border-radius: 999px;
    background: color-mix(in srgb, var(--text-muted) 8%, var(--card-bg));
    color: var(--text-muted);
    font-size: .65rem;
    font-weight: 700;
}

.reservation-field-help {
    margin-top: .4rem;
    color: var(--text-muted);
    font-size: .76rem;
}

.reservation-modal-cancel {
    border-radius: 12px;
    border: 1px solid var(--border-soft);
    background: var(--card-bg);
    color: var(--text-muted);
    font-weight: 700;
}

.reservation-modal-cancel:hover {
    background: color-mix(in srgb, var(--text-muted) 8%, var(--card-bg));
    border-color: var(--border-soft);
    color: var(--text-main);
}

.reservation-client-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.65rem;
    color: var(--primary);
    background: color-mix(in srgb, var(--primary) 12%, var(--card-bg));
}

.reservation-client {
    font-size: 1.08rem;
    text-transform: uppercase;
}

.reservation-status {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .42rem .75rem;
    border-radius: 999px;
    font-size: .72rem;
    line-height: 1;
    font-weight: 800;
    text-transform: uppercase;
    white-space: nowrap;
}

.status-confirmed {
    color: #15803d;
    background: #dcfce7;
}

.status-pending {
    color: #b45309;
    background: #fef3c7;
}

.status-cancelled {
    color: #b91c1c;
    background: #fee2e2;
}

.reservation-detail {
    min-height: 64px;
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .45rem .75rem;
    border-right: 1px solid var(--border-soft);
}

.reservation-detail-icon {
    font-size: 1.15rem;
    color: var(--primary);
}

.detail-time {
    color: #dc2626;
}

.detail-table {
    color: var(--primary);
}

.reservation-info-value {
    display: block;
    font-size: .95rem;
}

.reservation-time {
    color: #dc2626;
}

.reservation-table {
    color: var(--primary);
}

.reservation-confirm-btn {
    min-height: 44px;
    border-radius: 12px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #16803d;
    font-weight: 700;
}

.reservation-confirm-btn:hover {
    background: #dcfce7;
    border-color: #86efac;
    color: #166534;
}

.reservation-cancel-btn {
    min-height: 44px;
    border-radius: 12px;
    background: #fff1f2;
    border: 1px solid #fecdd3;
    color: #dc2626;
    font-weight: 700;
}

.reservation-cancel-btn:hover {
    background: #fee2e2;
    border-color: #fda4af;
    color: #b91c1c;
}

.reservation-edit-btn {
    min-height: 44px;
    border-radius: 12px;
    background: color-mix(in srgb, var(--primary) 8%, var(--card-bg));
    border: 1px solid color-mix(in srgb, var(--primary) 30%, var(--border-soft));
    color: var(--primary);
    font-weight: 700;
}

.reservation-edit-btn:hover {
    background: color-mix(in srgb, var(--primary) 14%, var(--card-bg));
    border-color: var(--primary);
    color: var(--primary-hover);
}

.reservation-delete-btn {
    min-height: 44px;
    border-radius: 12px;
    background: color-mix(in srgb, var(--text-muted) 7%, var(--card-bg));
    border: 1px solid var(--border-soft);
    color: var(--text-muted);
    font-weight: 700;
}

.reservation-delete-btn:hover {
    background: #fee2e2;
    border-color: #fecaca;
    color: #dc2626;
}

.reservation-empty {
    background: var(--card-bg);
    border: 1px dashed var(--border-soft);
    border-radius: var(--radius-xl);
    padding: 3.5rem 1rem;
    color: var(--text-muted);
}

.reservation-empty-icon {
    width: 60px;
    height: 60px;
    margin: auto;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 10%, var(--card-bg));
    color: var(--primary);
    font-size: 1.7rem;
}

@media (max-width: 767.98px) {
    .reservation-detail {
        border-right: 0;
        border-bottom: 1px solid var(--border-soft);
    }

    .reservation-status {
        font-size: .65rem;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Sincronizar hora y minutos
    document.querySelectorAll('.reservation-time-group').forEach(function (group) {
        const hour = group.querySelector('.reservation-hour-select');
        const minute = group.querySelector('.reservation-minute-select');
        const hidden = group.querySelector('.reservation-hour-value');

        if (!hour || !minute || !hidden) {
            return;
        }

        // Recuperar valor existente, por ejemplo 08:30
        if (hidden.value && hidden.value.includes(':')) {
            const parts = hidden.value.split(':');

            if ([...hour.options].some(option => option.value === parts[0])) {
                hour.value = parts[0];
            }

            if ([...minute.options].some(option => option.value === parts[1])) {
                minute.value = parts[1];
            }
        }

        function updateTime() {
            hidden.value = hour.value + ':' + minute.value;
        }

        hour.addEventListener('change', updateTime);
        minute.addEventListener('change', updateTime);

        updateTime();
    });

    // Controles de personas
    document.querySelectorAll('.reservation-people-control').forEach(function (control) {
        const input = control.querySelector('.reservation-people-input');
        const minus = control.querySelector('.reservation-people-minus');
        const plus = control.querySelector('.reservation-people-plus');

        if (!input || !minus || !plus) {
            return;
        }

        minus.addEventListener('click', function () {
            let current = parseInt(input.value || '1', 10);

            if (current > 1) {
                input.value = current - 1;
            }
        });

        plus.addEventListener('click', function () {
            let current = parseInt(input.value || '1', 10);
            input.value = current + 1;
        });
    });

});
</script>
<style>
#createReservationModal .reservation-time-group {
    display: grid;
    grid-template-columns: minmax(70px, 1fr) auto minmax(70px, 1fr);
    align-items: center;
    gap: 8px;
}

#createReservationModal .reservation-time-separator {
    font-size: 1.15rem;
    font-weight: 800;
    text-align: center;
}

#createReservationModal .reservation-people-control {
    width: 100%;
    min-height: 38px;
}

#createReservationModal .reservation-people-btn {
    width: 42px;
    flex: 0 0 42px;
}

#createReservationModal .reservation-people-input {
    width: 100%;
    min-width: 50px;
}

@media (max-width: 767.98px) {
    #createReservationModal .reservation-time-group {
        grid-template-columns: 1fr auto 1fr;
    }
}
</style>
<style>
/* Flechas visibles en los selectores de Nueva Reserva */
#createReservationModal .reservation-hour-select,
#createReservationModal .reservation-minute-select,
#createReservationModal select[name="reservation_period"] {
    appearance: auto !important;
    -webkit-appearance: menulist !important;
    -moz-appearance: menulist !important;
    cursor: pointer;
    padding-right: 30px !important;
}

/* Mantener las flechas claramente visibles */
#createReservationModal .reservation-hour-select::-ms-expand,
#createReservationModal .reservation-minute-select::-ms-expand,
#createReservationModal select[name="reservation_period"]::-ms-expand {
    display: block;
}
</style>
<style>
/* Selectores de hora de Nueva Reserva */
#createReservationModal .reservation-time-group {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 6px;
    width: 100%;
}

#createReservationModal .reservation-hour-select,
#createReservationModal .reservation-minute-select {
    width: 100%;
    min-width: 0;
    height: 38px;
    padding: 6px 26px 6px 10px !important;
    cursor: pointer;
    appearance: auto !important;
    -webkit-appearance: menulist !important;
    -moz-appearance: menulist !important;
    text-align: left;
}

#createReservationModal select[name="reservation_period"] {
    width: 100%;
    height: 38px;
    padding: 6px 24px 6px 8px !important;
    cursor: pointer;
    appearance: auto !important;
    -webkit-appearance: menulist !important;
    -moz-appearance: menulist !important;
}

#createReservationModal .reservation-time-separator {
    margin: 0 1px;
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-main);
}
</style>
<style>
#createReservationModal .reservation-time-group {
    display: flex !important;
    align-items: center;
    gap: 7px;
    width: 100%;
}

#createReservationModal .reservation-hour-select,
#createReservationModal .reservation-minute-select {
    flex: 1 1 0;
    width: 0;
    min-width: 82px !important;
    height: 40px;
    padding: 6px 8px !important;
    font-size: .9rem;
    cursor: pointer;
    appearance: auto !important;
    -webkit-appearance: menulist !important;
}

#createReservationModal .reservation-time-separator {
    flex: 0 0 auto;
    margin: 0;
    font-weight: 800;
}
</style>
<style>
/* Hora y periodo - Editar Reserva */
.reservation-modal .reservation-time-group {
    display: flex !important;
    align-items: center;
    gap: 7px;
    width: 100%;
}

.reservation-modal .reservation-hour-select,
.reservation-modal .reservation-minute-select {
    flex: 1 1 0;
    width: 0;
    min-width: 82px !important;
    height: 40px;
    padding: 6px 8px !important;
    font-size: .9rem;
    cursor: pointer;
    appearance: auto !important;
    -webkit-appearance: menulist !important;
    -moz-appearance: menulist !important;
}

.reservation-modal select[name="reservation_period"] {
    width: 100%;
    height: 40px;
    padding: 6px 8px !important;
    cursor: pointer;
    appearance: auto !important;
    -webkit-appearance: menulist !important;
    -moz-appearance: menulist !important;
}

.reservation-modal .reservation-time-separator {
    flex: 0 0 auto;
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
}
</style>
<div class="modal fade" id="reservationActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg"
             style="border-radius:var(--radius-md, 14px); overflow:hidden;">

            <div class="modal-header border-0"
                 style="background:var(--primary, #ff8c00); color:#fff;">
                <h6 class="modal-title fw-bold" id="reservationActionHeader">
                    <i class="bi bi-question-circle me-2"></i>
                    Confirmar acción
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

                <div class="fw-bold mb-2"
                     id="reservationActionTitle">
                    ¿Confirmar acción?
                </div>

                <div class="small text-muted"
                     id="reservationActionText">
                </div>

                <div class="small fw-semibold mt-2"
                     id="reservationActionClient">
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button"
                        class="btn btn-light border flex-fill"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button"
                        id="reservationActionConfirm"
                        class="btn flex-fill fw-bold text-white"
                        style="background:var(--primary, #ff8c00); border-color:var(--primary, #ff8c00);">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
<style>
.reservation-confirm-dialog {
    max-width: 430px;
}

.reservation-confirm-modal {
    border: 0;
    border-radius: 22px;
    box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
    overflow: hidden;
}

.reservation-confirm-icon {
    width: 68px;
    height: 68px;
    margin: 0 auto;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    transition: .2s ease;
}

.reservation-confirm-icon.confirm {
    background: #f0fdf4;
    color: #16803d;
}

.reservation-confirm-icon.cancel {
    background: #fff7ed;
    color: #ea580c;
}

.reservation-confirm-icon.delete {
    background: #fff1f2;
    color: #dc2626;
}

.reservation-confirm-title {
    color: var(--text-main);
    font-weight: 800;
}

.reservation-confirm-text {
    color: var(--text-muted);
    line-height: 1.55;
}

.reservation-confirm-client {
    display: inline-block;
    padding: 7px 14px;
    border-radius: 10px;
    background: var(--light-bg);
    color: var(--text-main);
    font-weight: 700;
}

.reservation-action-back,
.reservation-action-confirm {
    min-width: 135px;
    min-height: 44px;
    border-radius: 12px;
    font-weight: 700;
}

.reservation-action-back {
    background: var(--card-bg);
    border: 1px solid var(--border-soft);
    color: var(--text-main);
}

.reservation-action-confirm.confirm {
    background: #16803d;
    border-color: #16803d;
    color: #fff;
}

.reservation-action-confirm.cancel {
    background: #ea580c;
    border-color: #ea580c;
    color: #fff;
}

.reservation-action-confirm.delete {
    background: #dc2626;
    border-color: #dc2626;
    color: #fff;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalElement = document.getElementById('reservationActionModal');

    if (!modalElement) {
        return;
    }

    const actionModal = new bootstrap.Modal(modalElement);
    const header = document.getElementById('reservationActionHeader');
    const title = document.getElementById('reservationActionTitle');
    const text = document.getElementById('reservationActionText');
    const client = document.getElementById('reservationActionClient');
    const confirmButton = document.getElementById('reservationActionConfirm');

    let currentForm = null;

    const configurations = {
        confirm: {
            title: '¿Confirmar reserva?',
            text: 'La reserva pasará al estado Confirmada.',
            button: 'Sí, confirmar',
            icon: 'bi-check-lg',
            style: 'confirm'
        },

        cancel: {
            title: '¿Cancelar reserva?',
            text: 'La reserva pasará al estado Cancelada.',
            button: 'Sí, cancelar',
            icon: 'bi-x-lg',
            style: 'cancel'
        },

        delete: {
            title: '¿Eliminar reserva?',
            text: 'Esta acción eliminará definitivamente el registro de la reserva.',
            button: 'Sí, eliminar',
            icon: 'bi-trash3',
            style: 'delete'
        }
    };

    document.querySelectorAll('.reservation-action-btn').forEach(function (button) {

        button.addEventListener('click', function () {

            const action = this.dataset.action;
            const config = configurations[action];

            if (!config) {
                return;
            }

            currentForm = this.closest('form');

            title.textContent = config.title;
            text.textContent = config.text;
            client.textContent = this.dataset.client || 'Cliente';

            const actionColors = {
                confirm: '#16803d',
                cancel: '#ea580c',
                delete: '#dc2626'
            };

            const actionColor = actionColors[action] || 'var(--primary, #ff8c00)';

            header.innerHTML =
                '<i class="bi ' + config.icon + ' me-2"></i>' +
                config.title.replace('¿', '').replace('?', '');

            header.closest('.modal-header').style.background = actionColor;

            const bodyIcon = modalElement.querySelector(
                '.modal-body .bi-question-circle'
            );

            if (bodyIcon) {
                bodyIcon.style.color = actionColor;
            }

            confirmButton.className =
                'btn flex-fill fw-bold text-white';

            confirmButton.style.background = actionColor;
            confirmButton.style.borderColor = actionColor;
            confirmButton.textContent = config.button;

            actionModal.show();
        });

    });

    confirmButton.addEventListener('click', function () {

        if (!currentForm) {
            return;
        }

        confirmButton.disabled = true;

        currentForm.submit();
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        currentForm = null;
        confirmButton.disabled = false;
    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    window.showReservationNotification = function(message) {

        let notification =
            document.getElementById('reservationSystemNotification');

        if (!notification) {

            notification = document.createElement('div');
            notification.id = 'reservationSystemNotification';

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
                        Operación realizada con éxito
                    </div>

                    <div style="font-size:.9rem;">
                        ${message}
                    </div>
                </div>

                <button type="button"
                        onclick="this.closest('#reservationSystemNotification').style.display='none'"
                        style="border:0;background:transparent;color:var(--text-muted,#6b7280);font-size:1.2rem;line-height:1;">
                    &times;
                </button>

            </div>
        `;

        notification.style.display = 'block';

        clearTimeout(window.reservationNotificationTimer);

        window.reservationNotificationTimer = setTimeout(() => {
            notification.style.display = 'none';
        }, 4000);
    };

    @if(session('success'))
        showReservationNotification(@json(session('success')));
    @endif

});
</script>
<style>
/* Colores de indicadores de Reservas */
.reservations-page .row.g-3.mb-4 > div:nth-child(1) .reservation-stat {
    border-left: 4px solid #2563eb;
}

.reservations-page .row.g-3.mb-4 > div:nth-child(1) .reservation-stat-icon {
    background: #eff6ff;
    color: #2563eb !important;
}

.reservations-page .row.g-3.mb-4 > div:nth-child(2) .reservation-stat {
    border-left: 4px solid #f59e0b;
}

.reservations-page .row.g-3.mb-4 > div:nth-child(2) .reservation-stat-icon {
    background: #fff7ed;
    color: #f59e0b !important;
}

.reservations-page .row.g-3.mb-4 > div:nth-child(3) .reservation-stat {
    border-left: 4px solid #16a34a;
}

.reservations-page .row.g-3.mb-4 > div:nth-child(3) .reservation-stat-icon {
    background: #f0fdf4;
    color: #16a34a !important;
}

.reservations-page .row.g-3.mb-4 > div:nth-child(4) .reservation-stat {
    border-left: 4px solid #9333ea;
}

.reservations-page .row.g-3.mb-4 > div:nth-child(4) .reservation-stat-icon {
    background: #faf5ff;
    color: #9333ea !important;
}
</style>
<style>
/* KPI de Reservas - estilo Dashboard */
.reservation-kpi {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 22px;
    border-radius: 16px;
    background: var(--card-bg, #ffffff);
    box-shadow: 0 2px 14px rgba(15, 23, 42, .07);
    position: relative;
    overflow: hidden;
    transition: transform .22s, box-shadow .22s;
    border: 1px solid var(--border-soft, #e5e7eb);
    height: 100%;
}

.reservation-kpi:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(15, 23, 42, .14);
}

.reservation-kpi::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    border-radius: 16px 0 0 16px;
}

/* Reservas de hoy - azul */
.reservation-kpi-today::before {
    background: linear-gradient(180deg, #2563eb 0%, #93c5fd 65%, white 100%);
}

.reservation-kpi-today .reservation-kpi-icon {
    background: #eff6ff;
    color: #2563eb;
}

/* Pendientes - naranja */
.reservation-kpi-pending::before {
    background: linear-gradient(180deg, #f59e0b 0%, #fcd34d 65%, white 100%);
}

.reservation-kpi-pending .reservation-kpi-icon {
    background: #fff7ed;
    color: #f59e0b;
}

/* Confirmadas - verde */
.reservation-kpi-confirmed::before {
    background: linear-gradient(180deg, #16a34a 0%, #86efac 65%, white 100%);
}

.reservation-kpi-confirmed .reservation-kpi-icon {
    background: #f0fdf4;
    color: #16a34a;
}

/* Personas - morado */
.reservation-kpi-people::before {
    background: linear-gradient(180deg, #9333ea 0%, #d8b4fe 65%, white 100%);
}

.reservation-kpi-people .reservation-kpi-icon {
    background: #faf5ff;
    color: #9333ea;
}

.reservation-kpi-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.reservation-kpi-content {
    flex: 1;
    min-width: 0;
}

.reservation-kpi-label {
    display: block;
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--text-muted, #64748b);
    margin-bottom: 4px;
}

.reservation-kpi-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--text-main, #172033);
    line-height: 1.1;
    margin-bottom: 5px;
}

.reservation-kpi-sub {
    font-size: .72rem;
    color: var(--text-muted, #64748b);
    display: flex;
    align-items: center;
}

.reservation-kpi-badge {
    position: absolute;
    top: 14px;
    right: 14px;
    font-size: .6rem;
    font-weight: 700;
    letter-spacing: .08em;
    padding: 3px 8px;
    border-radius: 20px;
    background: #eff6ff;
    color: var(--text-main, #172033);
}

.reservation-kpi-pending .reservation-kpi-badge {
    background: #fff7ed;
}

.reservation-kpi-confirmed .reservation-kpi-badge {
    background: #f0fdf4;
}

.reservation-kpi-people .reservation-kpi-badge {
    background: #faf5ff;
}

@media (max-width: 575.98px) {
    .reservation-kpi {
        padding: 18px;
    }

    .reservation-kpi-icon {
        width: 46px;
        height: 46px;
    }
}
</style>

<style>
/* =========================================================
   DARK MODE - BADGES KPI RESERVAS
   ========================================================= */

/* HOY - Azul */
html[data-color-mode="dark"]
.reservation-kpi-today .reservation-kpi-badge {
    background: rgba(37, 99, 235, .16) !important;
    border: 1px solid rgba(96, 165, 250, .30) !important;
    color: #93c5fd !important;
}

/* PENDIENTES - Naranja */
html[data-color-mode="dark"]
.reservation-kpi-pending .reservation-kpi-badge {
    background: rgba(245, 158, 11, .15) !important;
    border: 1px solid rgba(251, 191, 36, .30) !important;
    color: #fbbf24 !important;
}

/* CONFIRMADAS - Verde */
html[data-color-mode="dark"]
.reservation-kpi-confirmed .reservation-kpi-badge {
    background: rgba(22, 163, 74, .16) !important;
    border: 1px solid rgba(74, 222, 128, .30) !important;
    color: #86efac !important;
}

/* PERSONAS PARA HOY - Violeta */
html[data-color-mode="dark"]
.reservation-kpi-people .reservation-kpi-badge {
    background: rgba(147, 51, 234, .16) !important;
    border: 1px solid rgba(192, 132, 252, .30) !important;
    color: #d8b4fe !important;
}

</style>
@endsection
<style>
.reservation-card {
    background: #111f33 !important;
    border: 1px solid #9fb0c4 !important;
    border-radius: 22px !important;
    overflow: hidden;
}

.reservation-card .reservation-info {
    background: #202632 !important;
    border: 1px solid #9fb0c4 !important;
    border-radius: 15px !important;
    overflow: hidden;
}

.reservation-card .reservation-detail {
    border-right: 1px solid #9fb0c4 !important;
}

.reservation-card .reservation-detail-icon {
    color: #ff8c00 !important;
}

.reservation-card .reservation-info-label {
    color: #6f8eaf !important;
}

.reservation-card .reservation-info-value {
    color: #ffffff !important;
}

.reservation-card .reservation-time {
    color: #ff263f !important;
}

.reservation-card .reservation-table {
    color: #ff8c00 !important;
}

.reservation-card .reservation-edit-btn {
    background: #202632 !important;
    border: 1px solid #d8c59a !important;
    color: #ff8c00 !important;
    border-radius: 12px !important;
}

.reservation-card .reservation-confirm-btn {
    background: #effff4 !important;
    border: 1px solid #b7e8c7 !important;
    color: #16803d !important;
    border-radius: 12px !important;
}

.reservation-card .reservation-cancel-btn {
    background: #fff1f2 !important;
    border: 1px solid #f3b8c0 !important;
    color: #dc2626 !important;
    border-radius: 12px !important;
}

.reservation-card .status-pending {
    background: rgba(255, 140, 0, .14) !important;
    border: 1px solid rgba(255, 140, 0, .35) !important;
    color: #ffb13b !important;
}

.reservation-card .status-confirmed {
    background: rgba(22, 163, 74, .14) !important;
    border: 1px solid rgba(34, 197, 94, .35) !important;
    color: #4ade80 !important;
}

.reservation-card .status-cancelled {
    background: rgba(220, 38, 38, .14) !important;
    border: 1px solid rgba(239, 68, 68, .35) !important;
    color: #ff6b6b !important;
}
</style>


<style>
/* ===== RESERVAS - DISEÑO OSCURO ===== */

.reservation-card,
.reservation-card .card-body {
    background: #111f33 !important;
    color: #ffffff !important;
    border: 0 !important;
}

.reservation-card {
    border: 1px solid #9fb0c4 !important;
    border-radius: 22px !important;
    overflow: hidden !important;
}

/* Cliente */
.reservation-card .reservation-client {
    color: #ffffff !important;
}

.reservation-card .reservation-phone {
    color: #6f8eaf !important;
}

/* Icono del cliente */
.reservation-card .reservation-client-icon {
    background: #202632 !important;
    color: #ff8c00 !important;
    border: 1px solid #303f54 !important;
}

/* Información de reserva */
.reservation-card .reservation-info {
    background: #202632 !important;
    border: 1px solid #9fb0c4 !important;
    border-radius: 15px !important;
    overflow: hidden !important;
}

.reservation-card .reservation-detail {
    border-right: 1px solid #9fb0c4 !important;
}

.reservation-card .reservation-detail:last-child {
    border-right: 0 !important;
}

.reservation-card .reservation-detail-icon {
    color: #ff8c00 !important;
}

.reservation-card .reservation-info-label {
    color: #6f8eaf !important;
}

.reservation-card .reservation-info-value {
    color: #ffffff !important;
}

.reservation-card .reservation-time {
    color: #ff263f !important;
}

.reservation-card .reservation-table {
    color: #ff8c00 !important;
}

/* Editar */
.reservation-card .reservation-edit-btn {
    background: #202632 !important;
    border: 1px solid #d8c59a !important;
    color: #ff8c00 !important;
    border-radius: 12px !important;
}

/* Confirmar */
.reservation-card .reservation-confirm-btn {
    background: #effff4 !important;
    border: 1px solid #b7e8c7 !important;
    color: #16803d !important;
    border-radius: 12px !important;
}

/* Cancelar */
.reservation-card .reservation-cancel-btn {
    background: #fff1f2 !important;
    border: 1px solid #f3b8c0 !important;
    color: #dc2626 !important;
    border-radius: 12px !important;
}

/* Estados */
.reservation-card .status-pending {
    background: rgba(255, 140, 0, .14) !important;
    border: 1px solid rgba(255, 140, 0, .35) !important;
    color: #ffb13b !important;
}

.reservation-card .status-confirmed {
    background: rgba(22, 163, 74, .14) !important;
    border: 1px solid rgba(34, 197, 94, .35) !important;
    color: #4ade80 !important;
}

.reservation-card .status-cancelled {
    background: rgba(220, 38, 38, .14) !important;
    border: 1px solid rgba(239, 68, 68, .35) !important;
    color: #ff6b6b !important;
}

/* Nota */
.reservation-card .reservation-note {
    background: #202632 !important;
    border: 1px solid #9fb0c4 !important;
    color: #ffffff !important;
    border-radius: 10px !important;
}
</style>



<style>
/* ===== BOTONES DE RESERVAS ===== */

.reservation-card .reservation-confirm-btn,
.reservation-card .reservation-confirm-btn:hover,
.reservation-card .reservation-confirm-btn:focus,
.reservation-card .reservation-confirm-btn:active {
    background-color: #16803d !important;
    background-image: none !important;
    border: 1px solid #16803d !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

.reservation-card .reservation-cancel-btn,
.reservation-card .reservation-cancel-btn:hover,
.reservation-card .reservation-cancel-btn:focus,
.reservation-card .reservation-cancel-btn:active {
    background-color: #dc2626 !important;
    background-image: none !important;
    border: 1px solid #dc2626 !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

/* Iconos */
.reservation-card .reservation-confirm-btn i,
.reservation-card .reservation-cancel-btn i {
    color: #ffffff !important;
}
</style>

