@extends('layouts.app')

@section('content')

<style>
    .billing-page {
        width: 100%;
    }

    .billing-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .billing-title {
        margin: 0;
        color: var(--text-main, #111827);
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .billing-title i {
    color:var(--text-main) !important;
}

    .billing-subtitle {
        margin: 6px 0 0;
        color: var(--text-muted, #64748b);
        font-size: .88rem;
    }

    .billing-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .billing-header-actions .btn {
        border-radius: 10px;
        font-weight: 700;
        padding: 10px 14px;
    }

    .billing-kpi {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 19px;
        border-radius: 16px;
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft, #e5e7eb);
        box-shadow: 0 2px 14px rgba(15, 23, 42, .07);
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }

    .billing-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 9px 24px rgba(15, 23, 42, .11);
    }

    .billing-kpi::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 5px;
        height: 100%;
    }

    .billing-kpi.accepted::before {
        background: linear-gradient(180deg, #16a34a, #86efac, #fff);
    }

    .billing-kpi.observed::before {
        background: linear-gradient(180deg, #f59e0b, #fcd34d, #fff);
    }

    .billing-kpi.pending::before {
        background: linear-gradient(180deg, #64748b, #cbd5e1, #fff);
    }

    .billing-kpi.error::before {
        background: linear-gradient(180deg, #dc2626, #fca5a5, #fff);
    }

    .billing-kpi.rejected::before {
        background: linear-gradient(180deg, #b91c1c, #fca5a5, #fff);
    }

    .billing-kpi-content {
        display: flex;
        align-items: center;
        gap: 14px;
        width: 100%;
    }

    .billing-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .billing-kpi.accepted .billing-kpi-icon {
        background: #dcfce7;
        color: #16a34a;
    }

    .billing-kpi.observed .billing-kpi-icon {
        background: #fef3c7;
        color: #d97706;
    }

    .billing-kpi.pending .billing-kpi-icon {
        background: #f1f5f9;
        color: #64748b;
    }

    .billing-kpi.error .billing-kpi-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .billing-kpi.rejected .billing-kpi-icon {
        background: #fee2e2;
        color: #b91c1c;
    }

    .billing-kpi-label {
        color: var(--text-muted);
        font-size: .73rem;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .billing-kpi-value {
        color: var(--text-main);
        font-size: 1.45rem;
        line-height: 1.1;
        font-weight: 800;
    }

    .billing-filter-card,
    .billing-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 16px;
        box-shadow: var(--shadow-soft, 0 8px 24px rgba(0,0,0,.06));
        overflow: hidden;
    }

    .billing-filter-card {
        margin-bottom: 18px;
    }

    .billing-filter-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-soft, #e5e7eb);
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 800;
        color: var(--text-main, #111827);
    }

    .billing-filter-header i {
        color: var(--primary);
    }

    .billing-filter-body {
        padding: 18px 20px;
    }

    .billing-page .form-label {
        color: var(--text-main, #111827);
        font-weight: 700;
        font-size: .76rem;
    }

    .billing-page .form-control,
    .billing-page .form-select {
        border-color: var(--border-soft, #e5e7eb);
        border-radius: 10px;
    }

    .billing-page .form-control:focus,
    .billing-page .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--theme-shadow, rgba(0,0,0,.08));
    }

    .billing-filter-btn {
        background: var(--primary);
        border-color: var(--primary);
        border-radius: 10px;
        font-weight: 700;
        min-height: 38px;
    }

    .billing-clear-btn {
        border-radius: 10px;
        font-weight: 700;
        min-height: 38px;
    }



    .billing-pagination {
        padding: 16px 20px;
        border-top: 1px solid var(--border-soft, #e5e7eb);
        background: var(--card-bg, #fff);
    }

    .billing-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 16px;
        box-shadow: 0 2px 14px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .billing-table-card .table {
        margin: 0;
    }

    .billing-table-card thead th {
        background: var(--light-bg, #f8fafc);
        color: var(--text-muted, #64748b);
        font-size: .70rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .035em;
        padding: 15px 16px;
        border-bottom: 1px solid var(--border-soft, #e5e7eb);
        white-space: nowrap;
    }

    .billing-table-card tbody tr {
        transition: background-color .15s ease;
    }

    .billing-table-card tbody tr:hover {
        background: color-mix(in srgb, var(--primary) 3%, white);
    }

    .billing-table-card tbody td {
        padding: 15px 16px;
        border-bottom: 1px solid var(--border-soft, #eef2f7);
        vertical-align: middle;
    }

    .billing-table-card tbody tr:last-child td {
        border-bottom: 0;
    }

    .billing-number {
        color: var(--text-main, #111827);
        font-size: .88rem;
        font-weight: 800;
    }

    .billing-document-type {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 70px;
        padding: 5px 9px;
        border-radius: 8px;
        font-size: .68rem;
        font-weight: 800;
    }

    .billing-document-type.factura {
        background: color-mix(in srgb, var(--primary) 10%, white);
        color: var(--primary);
    }

    .billing-document-type.boleta {
        background: #e0f2fe;
        color: #0284c7;
    }

    .billing-date {
        color: var(--text-main, #111827);
        font-size: .80rem;
        font-weight: 700;
    }

    .billing-time {
        color: var(--text-muted, #64748b);
        font-size: .72rem;
        margin-top: 2px;
    }

    .billing-client-name {
        color: var(--text-main, #111827);
        font-size: .82rem;
        font-weight: 700;
    }

    .billing-client-document {
        color: var(--text-muted, #64748b);
        font-size: .72rem;
        margin-top: 2px;
    }

    .billing-total {
        color: var(--text-main, #111827);
        font-size: .86rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .billing-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .68rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .billing-status-code {
        color: var(--text-muted, #64748b);
        font-size: .68rem;
        margin-top: 4px;
    }

    .billing-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .billing-actions .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .billing-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .10);
    }

    .billing-actions form {
        margin: 0;
    }
    .billing-confirm-modal {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, .18);
        overflow: hidden;
    }

    .billing-confirm-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto 16px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fef3c7;
        color: #d97706;
        font-size: 1.65rem;
    }

    .billing-confirm-document {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 8px;
        padding: 7px 14px;
        border-radius: 10px;
        background: var(--light-bg, #f8fafc);
        color: var(--text-main, #111827);
        font-size: .86rem;
        font-weight: 800;
    }

    .billing-modal-btn {
        min-width: 135px;
        border-radius: 10px;
        font-weight: 700;
        padding: 9px 16px;
    }

    .billing-confirm-btn {
        background: var(--primary);
        border: 1px solid var(--primary);
        color: #fff;
    }

    .billing-confirm-btn:hover,
    .billing-confirm-btn:focus {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #fff;
    }
    @media (max-width: 768px) {
        .billing-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .billing-header-actions {
            width: 100%;
        }

        .billing-header-actions .btn {
            flex: 1;
        }

        .billing-title {
            font-size: 1.4rem;
        }
    }
</style>

<div class="container-fluid billing-page">

    {{-- Cabecera --}}
    <div class="billing-header">
        <div>
            <h2 class="billing-title">
                <i class="bi bi-receipt-cutoff me-2"></i>
                Comprobantes Electrónicos
            </h2>

            <p class="billing-subtitle">
                Boletas y Facturas emitidas a SUNAT
            </p>
        </div>

        <div class="billing-header-actions">
            <a href="{{ route('credit_notes.index') }}"
               class="btn btn-outline-warning">
                <i class="bi bi-arrow-counterclockwise me-1"></i>
                Notas de Crédito
            </a>

            <a href="{{ route('daily_summaries.index') }}"
               class="btn btn-outline-info">
                <i class="bi bi-calendar-week me-1"></i>
                Resumen Diario
            </a>
        </div>
    </div>

    {{-- Mensajes flash --}}




    {{-- Estados --}}
    <div class="row g-3 mb-4">

        @foreach([
            'ACCEPTED' => [
                'Aceptados',
                '#16a34a',
                'check-circle-fill'
            ],
            'OBSERVED' => [
                'Observados',
                '#f59e0b',
                'exclamation-triangle-fill'
            ],
            'PENDING' => [
                'Pendientes',
                '#64748b',
                'hourglass-split'
            ],
            'ERROR' => [
                'Con error',
                '#dc2626',
                'x-circle-fill'
            ],
            'REJECTED' => [
                'Rechazados',
                '#b91c1c',
                'slash-circle-fill'
            ],
        ] as $key => [$label, $color, $icon])

            @php
                $kpiClass = match($key) {
                    'ACCEPTED' => 'accepted',
                    'OBSERVED' => 'observed',
                    'PENDING'  => 'pending',
                    'ERROR'    => 'error',
                    'REJECTED' => 'rejected',
                    default    => 'pending',
                };
            @endphp

            <div class="col-6 col-md">
                <div class="billing-kpi {{ $kpiClass }}">

                    <div class="billing-kpi-content">

                        <div class="billing-kpi-icon">
                            <i class="bi bi-{{ $icon }}"></i>
                        </div>

                        <div>
                            <div class="billing-kpi-label">
                                {{ $label }}
                            </div>

                            <div class="billing-kpi-value">
                                {{ (int) ($stats[$key] ?? 0) }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        @endforeach

    </div>

    {{-- Filtros --}}
    <div class="billing-filter-card">

        <div class="billing-filter-header">
            <i class="bi bi-funnel-fill"></i>
            Filtros de comprobantes
        </div>

        <div class="billing-filter-body">

            <form method="GET"
                  class="row g-3 align-items-end">

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">
                        Tipo
                    </label>

                    <select name="type"
                            class="form-select form-select-sm">

                        <option value="">Todos</option>

                        <option value="Boleta"
                            {{ request('type') === 'Boleta' ? 'selected' : '' }}>
                            Boleta
                        </option>

                        <option value="Factura"
                            {{ request('type') === 'Factura' ? 'selected' : '' }}>
                            Factura
                        </option>

                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">
                        Estado SUNAT
                    </label>

                    <select name="status"
                            class="form-select form-select-sm">

                        <option value="">Todos</option>

                        @foreach([
                            'PENDING'  => 'Pendiente',
                            'ACCEPTED' => 'Aceptado',
                            'OBSERVED' => 'Observado',
                            'REJECTED' => 'Rechazado',
                            'ERROR'    => 'Con error',
                            'VOIDED'   => 'Anulado',
                        ] as $st => $stLabel)

                            <option value="{{ $st }}"
                                {{ request('status') === $st ? 'selected' : '' }}>
                                {{ $stLabel }}
                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">
                        Desde
                    </label>

                    <input type="date"
                           name="from"
                           value="{{ request('from') }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">
                        Hasta
                    </label>

                    <input type="date"
                           name="to"
                           value="{{ request('to') }}"
                           class="form-control form-control-sm">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">
                        Buscar
                    </label>

                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Serie, número, cliente, RUC/DNI"
                           class="form-control form-control-sm">
                </div>

                <div class="col-6 col-md-1">
                    <button type="submit"
                            class="btn billing-filter-btn btn-sm text-white w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                @if(request()->hasAny(['type','status','from','to','q']))
                    <div class="col-6 col-md-auto">
                        <a href="{{ route('billing.index') }}"
                           class="btn btn-outline-secondary billing-clear-btn btn-sm">
                            <i class="bi bi-x-circle me-1"></i>
                            Limpiar
                        </a>
                    </div>
                @endif

            </form>

        </div>
    </div>

    {{-- Tabla --}}
    <div class="billing-table-card">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Comprobante</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th class="text-end">Total</th>
                        <th>Estado SUNAT</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($orders as $o)

                        <tr>

                            <td>
                                <div class="d-flex align-items-center gap-2">

                                    <span class="billing-document-type {{ $o->document_type === 'Factura' ? 'factura' : 'boleta' }}">
                                        {{ $o->document_type }}
                                    </span>

                                    <span class="billing-number">
                                        {{ $o->full_number }}
                                    </span>

                                </div>
                            </td>

                            <td>
                                <div class="billing-date">
                                    {{ $o->created_at->format('d/m/Y') }}
                                </div>

                                <div class="billing-time">
                                    {{ $o->created_at->format('H:i') }}
                                </div>
                            </td>

                            <td>
                                <div class="billing-client-name">
                                    {{ $o->client_name ?: 'Cliente' }}
                                </div>

                                <div class="billing-client-document">
                                    {{ $o->client_document ?: 'Sin documento' }}
                                </div>
                            </td>

                            <td class="text-end">
                                <span class="billing-total">
                                    S/ {{ number_format($o->total, 2) }}
                                </span>
                            </td>

                            <td>

                                @php
                                    $statusData = match($o->sunat_status) {
                                        'ACCEPTED' => [
                                            'success',
                                            'Aceptado',
                                            'check-circle-fill'
                                        ],
                                        'OBSERVED' => [
                                            'warning',
                                            'Observado',
                                            'exclamation-triangle-fill'
                                        ],
                                        'PENDING' => [
                                            'secondary',
                                            'Pendiente',
                                            'hourglass-split'
                                        ],
                                        'VOIDED' => [
                                            'dark',
                                            'Anulado',
                                            'slash-circle-fill'
                                        ],
                                        'REJECTED' => [
                                            'danger',
                                            'Rechazado',
                                            'x-circle-fill'
                                        ],
                                        default => [
                                            'danger',
                                            'Con error',
                                            'x-circle-fill'
                                        ],
                                    };

                                    [$statusColor, $statusLabel, $statusIcon] = $statusData;
                                @endphp

                                <span class="badge bg-{{ $statusColor }} billing-status">
                                    <i class="bi bi-{{ $statusIcon }} me-1"></i>
                                    {{ $statusLabel }}
                                </span>

                                @if($o->sunat_code)
                                    <div class="billing-status-code">
                                        Código: {{ $o->sunat_code }}
                                    </div>
                                @endif

                            </td>

                            <td>

                                @php
                   $dailySummaryDetail = $o->dailySummaryDetails()
                       ->where('operation_status', '1')
                       ->whereHas('dailySummary', function ($query) {
                           $query->whereIn('sunat_status', ['ACCEPTED', 'OBSERVED']);
                       })
                       ->with('dailySummary')
                       ->latest('id')
                       ->first();

                   $dailySummary = $dailySummaryDetail?->dailySummary;

                   $hasXml = !empty($o->xml_path) || !empty($dailySummary?->xml_path);
                   $hasCdr = !empty($o->cdr_path) || !empty($dailySummary?->cdr_path);
               @endphp

               <div class="billing-actions">

                                    <a href="{{ route('billing.show', $o) }}"
                                       class="btn btn-outline-secondary"
                                       title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('billing.pdf', $o) }}"
                                       target="_blank"
                                       class="btn btn-outline-danger"
                                       title="PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>

                                    @if($hasXml)
                                        <a href="{{ route('billing.xml', $o) }}"
                                           class="btn btn-outline-info"
                                           title="Descargar XML">
                                            <i class="bi bi-filetype-xml"></i>
                                        </a>
                                    @endif

                                    @if($hasCdr)
                                        <a href="{{ route('billing.cdr', $o) }}"
                                           class="btn btn-outline-success"
                                           title="Descargar CDR">
                                            <i class="bi bi-archive"></i>
                                        </a>
                                    @endif

                                    @if(in_array($o->sunat_status, [
                                        'PENDING',
                                        'ERROR',
                                        'REJECTED'
                                    ]))

                                        <form method="POST"
                                              action="{{ route('billing.retry', $o) }}"
                                              class="d-inline" id="retry-form-{{ $o->id }}">

                                            @csrf

                                            <button type="button"
                                                    class="btn btn-outline-warning billing-retry-btn"
                                                    title="Reintentar envío"
                                                    data-form-id="retry-form-{{ $o->id }}"
                                                    data-document="{{ $o->full_number }}">

                                                <i class="bi bi-arrow-repeat"></i>

                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="billing-empty">

                                <div class="billing-empty-icon">
                                    <i class="bi bi-receipt"></i>
                                </div>

                                <h6 class="fw-bold mb-1">
                                    No hay comprobantes
                                </h6>

                                <p class="text-muted small mb-0">
                                    No existen comprobantes que coincidan con los filtros seleccionados.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <x-system-pagination :paginator="$orders" />

    </div>

</div>

{{-- Modal: Reintentar envio a SUNAT --}}
<div class="modal fade"
     id="retrySunatModal"
     tabindex="-1"
     aria-labelledby="retrySunatModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content billing-confirm-modal">

            <div class="modal-body p-4 text-center">

                <div class="billing-confirm-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>

                <h5 class="fw-bold mb-2"
                    id="retrySunatModalLabel">
                    ¿Reintentar envío?
                </h5>

                <p class="text-muted mb-1">
                    Se volverá a enviar el comprobante
                </p>

                <div class="billing-confirm-document"
                     id="retrySunatDocument">
                </div>

                <p class="text-muted small mt-2 mb-4">
                    El sistema realizará un nuevo intento de envío a SUNAT.
                </p>

                <div class="d-flex justify-content-center gap-2">

                    <button type="button"
                            class="btn btn-outline-secondary billing-modal-btn"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="button"
                            class="btn billing-confirm-btn billing-modal-btn"
                            id="confirmRetrySunat">
                        <i class="bi bi-arrow-repeat me-1"></i>
                        Reintentar envío
                    </button>

                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('retrySunatModal');
    const confirmButton = document.getElementById('confirmRetrySunat');
    const documentLabel = document.getElementById('retrySunatDocument');

    if (!modalElement || !confirmButton || !documentLabel) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);
    let selectedForm = null;

    document.querySelectorAll('.billing-retry-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const formId = this.dataset.formId;
            const documentNumber = this.dataset.document;

            selectedForm = document.getElementById(formId);
            documentLabel.textContent = documentNumber || 'Comprobante';

            modal.show();
        });
    });

    confirmButton.addEventListener('click', function () {
        if (!selectedForm) {
            return;
        }

        confirmButton.disabled = true;
        confirmButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';

        selectedForm.submit();
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        selectedForm = null;
        confirmButton.disabled = false;
        confirmButton.innerHTML =
            '<i class="bi bi-arrow-repeat me-1"></i> Reintentar envío';
    });
});
</script>


<style>
    /* Botones superiores: Notas de Crédito y Resumen Diario */
    .billing-header-actions .btn {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 8px 15px !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: 10px !important;
        background: var(--card-bg, #fff) !important;
        font-size: .76rem;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        transition:
            transform .15s ease,
            box-shadow .15s ease,
            background .15s ease;
    }

    .billing-header-actions .btn-outline-warning {
        border-color: #f59e0b !important;
        color: #d97706 !important;
    }

    .billing-header-actions .btn-outline-info {
        border-color: #38bdf8 !important;
        color: #0284c7 !important;
    }

    .billing-header-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .08);
    }

    .billing-header-actions .btn-outline-warning:hover {
        background: #fffbeb !important;
    }

    .billing-header-actions .btn-outline-info:hover {
        background: #eff6ff !important;
    }

    .billing-header-actions .btn i {
        font-size: .82rem;
    }
</style>


<style>
    /* KPI Aceptados - verde esmeralda */
    .billing-kpi.accepted::before {
        background: linear-gradient(
            180deg,
            #059669,
            #6ee7b7,
            #ffffff
        ) !important;
    }

    .billing-kpi.accepted .billing-kpi-icon {
        background: #f0fdf4 !important;
        color: #059669 !important;
    }

    .billing-kpi.accepted .billing-kpi-value {
        color: var(--text-main, #172033) !important;
    }
</style>


<style>
/* DARK MODE - TIPO COMPROBANTE */

/* BOLETA - AZUL */
html[data-color-mode="dark"] .billing-document-type.boleta {
    background: rgba(14, 165, 233, .14) !important;
    border: 1px solid rgba(56, 189, 248, .28) !important;
    color: #38bdf8 !important;
    box-shadow: none !important;
}


/* FACTURA - COLOR PRINCIPAL DEL TEMA */
html[data-color-mode="dark"] .billing-document-type.factura {
    background: color-mix(
        in srgb,
        var(--primary) 14%,
        #132338
    ) !important;

    border: 1px solid color-mix(
        in srgb,
        var(--primary) 32%,
        #30465d
    ) !important;

    color: var(--primary) !important;
    box-shadow: none !important;
}

</style>

@endsection
