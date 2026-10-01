@extends('layouts.app')

@section('content')

<style>
    .credit-page {
        --cn-primary: #111827;
        --cn-muted: #64748b;
        --cn-border: #e5e7eb;
        --cn-soft: #f8fafc;
        --cn-white: #ffffff;
    }

    .credit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .credit-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--cn-muted);
        text-decoration: none;
        font-size: .78rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .credit-back:hover {
        color: var(--cn-primary);
    }

    .credit-title {
        margin: 0;
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--cn-primary);
        letter-spacing: -.3px;
    }

    .credit-subtitle {
        margin: 5px 0 0;
        color: var(--cn-muted);
        font-size: .82rem;
    }

    .credit-header-icon {
        width: 46px;
        height: 46px;
        border: 1px solid var(--cn-border);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: var(--cn-primary);
        font-size: 1.2rem;
    }

    .credit-alert {
        border: 0;
        border-radius: 10px;
        font-size: .82rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }

    .credit-filter {
        border: 1px solid var(--cn-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(15,23,42,.04);
        margin-bottom: 18px;
    }

    .credit-filter-title {
        font-size: .82rem;
        font-weight: 800;
        color: var(--cn-primary);
        margin-bottom: 12px;
    }

    .credit-filter .form-label {
        color: var(--cn-muted);
        font-size: .72rem;
        font-weight: 700;
    }

    .credit-filter .form-control,
    .credit-filter .form-select {
        border-color: var(--cn-border);
        border-radius: 8px;
        font-size: .78rem;
        min-height: 36px;
    }

    .credit-filter .form-control:focus,
    .credit-filter .form-select:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148,163,184,.15);
    }

    .credit-filter-btn {
        height: 36px;
        border-radius: 8px;
        background: #111827;
        border: 1px solid #111827;
        font-size: .76rem;
        font-weight: 700;
    }

    .credit-filter-btn:hover {
        background: #000;
        border-color: #000;
    }

    .credit-table-card {
        border: 1px solid var(--cn-border);
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 5px 20px rgba(15,23,42,.045);
    }

    .credit-table-head {
        padding: 17px 20px;
        border-bottom: 1px solid var(--cn-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .credit-table-title {
        margin: 0;
        font-size: .9rem;
        font-weight: 800;
        color: var(--cn-primary);
    }

    .credit-table-count {
        font-size: .72rem;
        color: var(--cn-muted);
    }

    .credit-table {
        margin: 0;
    }

    .credit-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid var(--cn-border);
        color: #475569;
        font-size: .67rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .3px;
        padding: 12px 14px;
        white-space: nowrap;
    }

    .credit-table tbody td {
        padding: 14px;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
    }

    .credit-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .credit-table tbody tr {
        transition: background .15s ease;
    }

    .credit-table tbody tr:hover {
        background: #fafafa;
    }

    .credit-number {
        font-size: .82rem;
        font-weight: 800;
        color: #111827;
        white-space: nowrap;
    }

    .credit-date {
        font-size: .74rem;
        color: #475569;
        white-space: nowrap;
    }

    .credit-document {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #111827;
        text-decoration: none;
        font-size: .78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .credit-document:hover {
        text-decoration: underline;
    }

    .credit-document-icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--cn-border);
        border-radius: 7px;
        background: #f8fafc;
        color: #475569;
        font-size: .75rem;
    }

    .credit-reason {
        max-width: 260px;
        font-size: .74rem;
        line-height: 1.35;
        color: #475569;
    }

    .credit-reason-code {
        display: inline-block;
        margin-right: 5px;
        padding: 2px 5px;
        border: 1px solid #dbe1e8;
        border-radius: 4px;
        background: #f8fafc;
        color: #334155;
        font-size: .65rem;
        font-weight: 800;
    }

    .credit-total {
        font-size: .82rem;
        font-weight: 800;
        color: #111827;
        white-space: nowrap;
    }

    .credit-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border: 1px solid;
        border-radius: 999px;
        font-size: .65rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .credit-status.accepted {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .credit-status.observed {
        color: #92400e;
        background: #fffbeb;
        border-color: #fde68a;
    }

    .credit-status.pending {
        color: #475569;
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .credit-status.rejected,
    .credit-status.error {
        color: #991b1b;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .credit-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .credit-actions .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        font-size: .78rem;
    }

    .credit-actions .btn:hover {
        transform: translateY(-1px);
    }

    .credit-empty {
        padding: 55px 20px !important;
        text-align: center;
        color: var(--cn-muted);
    }

    .credit-empty-icon {
        width: 54px;
        height: 54px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid var(--cn-border);
        color: #64748b;
        font-size: 1.25rem;
    }

    .credit-empty-title {
        margin: 0 0 4px;
        color: #334155;
        font-size: .85rem;
        font-weight: 800;
    }

    .credit-empty-text {
        margin: 0;
        font-size: .75rem;
    }

    .credit-pagination {
        padding: 14px 18px;
        border-top: 1px solid var(--cn-border);
        background: #fff;
    }

    @media (max-width: 992px) {
        .credit-table {
            min-width: 950px;
        }

        .credit-header {
            align-items: flex-start;
        }
    }

.credit-back { padding: 7px 12px !important; border: 1px solid #94a3b8 !important; border-radius: 8px !important; background: #ffffff !important; color: #475569 !important; }
.credit-back:hover { background: #f8fafc !important; border-color: #64748b !important; color: #0f172a !important; }
</style>

<div class="container-fluid credit-page">

    {{-- Encabezado --}}
    <div class="credit-header">
        <div>
            <a href="{{ route('billing.index') }}" class="credit-back">
                <i class="bi bi-arrow-left"></i>
                Comprobantes
            </a>

            <h2 class="credit-title">
                <i class="bi bi-arrow-counterclockwise me-2"></i>
                Notas de Crédito
            </h2>

            <p class="credit-subtitle">
                Gestión y seguimiento de notas de crédito electrónicas.
            </p>
        </div>

        <div class="credit-header-icon">
            <i class="bi bi-receipt"></i>
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success credit-alert mb-3">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger credit-alert mb-3">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="credit-filter">
        <div class="card-body p-3">
            <div class="credit-filter-title">
                <i class="bi bi-funnel me-1"></i>
                Filtrar notas de crédito
            </div>

            <form method="GET" class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label mb-1">Estado SUNAT</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>

                        @foreach([
                            'PENDING' => 'Pendiente',
                            'ACCEPTED' => 'Aceptado',
                            'OBSERVED' => 'Observado',
                            'REJECTED' => 'Rechazado',
                            'ERROR' => 'Error'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label mb-1">Buscar comprobante</label>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control form-control-sm"
                        placeholder="Ej. B001-10 o F001-25">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm credit-filter-btn w-100">
                        <i class="bi bi-search me-1"></i>
                        Buscar
                    </button>
                </div>

                <div class="col-md-2">
                    @if(request('status') || request('q'))
                        <a href="{{ route('credit_notes.index') }}"
                           class="btn btn-outline-secondary btn-sm w-100"
                           style="height:36px;border-radius:8px;font-size:.76rem;font-weight:700;">
                            <i class="bi bi-x-lg me-1"></i>
                            Limpiar
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="credit-table-card">

        <div class="credit-table-head">
            <div>
                <h6 class="credit-table-title">
                    Notas registradas
                </h6>

                <div class="credit-table-count">
                    Historial de notas de crédito emitidas
                </div>
            </div>

            <span class="badge text-bg-light border">
                {{ $notes->total() }} registros
            </span>
        </div>

        <div class="table-responsive">
            <table class="table credit-table align-middle">
                <thead>
                    <tr>
                        <th>N.C.</th>
                        <th>Fecha</th>
                        <th>Comprobante afectado</th>
                        <th>Motivo</th>
                        <th class="text-end">Total</th>
                        <th>Estado SUNAT</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($notes as $cn)

                        @php
                            $statusData = match($cn->sunat_status) {
                                'ACCEPTED' => [
                                    'accepted',
                                    'Aceptado',
                                    'check-circle-fill'
                                ],
                                'OBSERVED' => [
                                    'observed',
                                    'Observado',
                                    'exclamation-triangle-fill'
                                ],
                                'PENDING' => [
                                    'pending',
                                    'Pendiente',
                                    'clock-fill'
                                ],
                                'REJECTED' => [
                                    'rejected',
                                    'Rechazado',
                                    'x-circle-fill'
                                ],
                                'ERROR' => [
                                    'error',
                                    'Error',
                                    'exclamation-circle-fill'
                                ],
                                default => [
                                    'pending',
                                    $cn->sunat_status,
                                    'question-circle-fill'
                                ],
                            };
                        @endphp

                        <tr>

                            {{-- Número --}}
                            <td>
                                <div class="credit-number">
                                    {{ $cn->full_number }}
                                </div>
                            </td>

                            {{-- Fecha --}}
                            <td>
                                <div class="credit-date">
                                    {{ $cn->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-muted" style="font-size:.67rem;">
                                    {{ $cn->created_at->format('H:i') }}
                                </div>
                            </td>

                            {{-- Documento afectado --}}
                            <td>
                                <a href="{{ route('billing.show', $cn->order_id) }}"
                                   class="credit-document">

                                    <span class="credit-document-icon">
                                        <i class="bi bi-receipt"></i>
                                    </span>

                                    {{ $cn->order?->full_number ?? '#' . $cn->order_id }}
                                </a>
                            </td>

                            {{-- Motivo --}}
                            <td>
                                <div class="credit-reason">
                                    <span class="credit-reason-code">
                                        {{ $cn->reason_code }}
                                    </span>

                                    {{ $cn->reason_description }}
                                </div>
                            </td>

                            {{-- Total --}}
                            <td class="text-end">
                                <span class="credit-total">
                                    S/ {{ number_format($cn->total, 2) }}
                                </span>
                            </td>

                            {{-- Estado --}}
                            <td>
                                <span class="credit-status {{ $statusData[0] }}">
                                    <i class="bi bi-{{ $statusData[2] }}"></i>
                                    {{ $statusData[1] }}
                                </span>

                                @if($cn->sunat_code)
                                    <div class="text-muted mt-1"
                                         style="font-size:.64rem;">
                                        Código: {{ $cn->sunat_code }}
                                    </div>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td>
                                <div class="credit-actions">

                                    <a href="{{ route('credit_notes.show', $cn) }}"
                                       class="btn btn-outline-secondary"
                                       title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if($cn->xml_path)
                                        <a href="{{ route('credit_notes.xml', $cn) }}"
                                           class="btn btn-outline-dark"
                                           title="Descargar XML">
                                            <i class="bi bi-filetype-xml"></i>
                                        </a>
                                    @endif

                                    @if($cn->cdr_path)
                                        <a href="{{ route('credit_notes.cdr', $cn) }}"
                                           class="btn btn-outline-success"
                                           title="Descargar CDR">
                                            <i class="bi bi-archive"></i>
                                        </a>
                                    @endif

                                    @if(in_array($cn->sunat_status, [
                                        'PENDING',
                                        'ERROR',
                                        'REJECTED'
                                    ]))
                                        <form method="POST"
                                              action="{{ route('credit_notes.retry', $cn) }}"
                                              class="d-inline">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-outline-warning"
                                                title="Reintentar envío"
                                                onclick="return confirm('¿Deseas reintentar el envío de esta Nota de Crédito a SUNAT?')">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>

                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="credit-empty">

                                <div class="credit-empty-icon">
                                    <i class="bi bi-receipt"></i>
                                </div>

                                <h6 class="credit-empty-title">
                                    No hay notas de crédito
                                </h6>

                                <p class="credit-empty-text">
                                    No existen notas que coincidan con los filtros seleccionados.
                                </p>

                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="credit-pagination">
            {{ $notes->links() }}
        </div>

    </div>

</div>

@endsection
