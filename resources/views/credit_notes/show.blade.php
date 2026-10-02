@extends('layouts.app')

@section('content')

<style>
    .cn-show {
        --cn-black: #111827;
        --cn-text: #334155;
        --cn-muted: #64748b;
        --cn-border: #e5e7eb;
        --cn-soft: #f8fafc;
    }

    .cn-wrapper {
        max-width: none; margin: 0; width: 100%;
    }

    .cn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--cn-muted);
        text-decoration: none;
        font-size: .78rem;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .cn-back:hover {
        color: var(--cn-black);
    }

    .cn-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
        margin-bottom: 22px;
    }

    .cn-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cn-title {
        margin: 0;
        color: var(--cn-black);
        font-size: 1.5rem;
        font-weight: 800;
    }

    .cn-subtitle {
        margin: 6px 0 0;
        color: var(--cn-muted);
        font-size: .78rem;
    }

    .cn-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border: 1px solid;
        border-radius: 999px;
        font-size: .66rem;
        font-weight: 800;
    }

    .cn-status.accepted {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .cn-status.observed {
        color: #92400e;
        background: #fffbeb;
        border-color: #fde68a;
    }

    .cn-status.pending {
        color: #475569;
        background: #ffffff;
        border-color: #cbd5e1;
    }

    .cn-status.rejected,
    .cn-status.error {
        color: #991b1b;
        background: #fff1f2;
        border-color: #fecaca;
    }

    .cn-header-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .cn-header-actions .btn {
        min-height: 34px;
        border-radius: 8px;
        font-size: .72rem;
        font-weight: 700;
    }

    .cn-card {
        border: 1px solid var(--cn-border);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 5px 20px rgba(15,23,42,.045);
        overflow: hidden;
        margin-bottom: 16px;
    }

    .cn-card-header {
        padding: 15px 19px;
        border-bottom: 1px solid var(--cn-border);
        background: #ffffff;
    }

    .cn-card-header h6 {
        margin: 0;
        color: var(--cn-black);
        font-size: .84rem;
        font-weight: 800;
    }

    .cn-card-header p {
        margin: 4px 0 0;
        color: var(--cn-muted);
        font-size: .7rem;
    }

    .cn-card-body {
        padding: 19px;
    }

    .cn-document-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 15px;
        border: 1px solid var(--cn-border);
        border-radius: 10px;
        background: var(--cn-soft);
    }

    .cn-document-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cn-document-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        border: 1px solid #dbe1e8;
        background: #ffffff;
        color: #475569;
        font-size: 1rem;
    }

    .cn-document-label {
        color: var(--cn-muted);
        font-size: .68rem;
        margin-bottom: 3px;
    }

    .cn-document-number {
        color: var(--cn-black);
        font-size: .9rem;
        font-weight: 800;
        text-decoration: none;
    }

    .cn-document-number:hover {
        text-decoration: underline;
    }

    .cn-document-date {
        text-align: right;
        color: var(--cn-muted);
        font-size: .7rem;
    }

    .cn-section-label {
        color: var(--cn-muted);
        font-size: .67rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 6px;
    }

    .cn-reason {
        color: var(--cn-text);
        font-size: .78rem;
        line-height: 1.45;
    }

    .cn-code {
        display: inline-flex;
        padding: 3px 7px;
        margin-right: 5px;
        border: 1px solid #dbe1e8;
        border-radius: 5px;
        background: #ffffff;
        color: #334155;
        font-size: .66rem;
        font-weight: 800;
    }

    .cn-total-box {
        padding: 15px;
        border: 1px solid var(--cn-border);
        border-radius: 10px;
        background: #ffffff;
    }

    .cn-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #64748b;
        font-size: .74rem;
    }

    .cn-total-row:last-child {
        border-bottom: 0;
    }

    .cn-total-row strong {
        color: #334155;
    }

    .cn-total-final {
        margin-top: 4px;
        padding-top: 12px !important;
        border-top: 1px solid #cbd5e1;
        border-bottom: 0 !important;
    }

    .cn-total-final span {
        color:var(--text-main);
        font-size: .78rem;
        font-weight: 800;
    }

    .cn-total-final strong {
        color:var(--text-main);
        font-size: 1.15rem;
        font-weight: 800;
    }

    .cn-trace-table {
        margin: 0;
    }

    .cn-trace-table tr {
        border-bottom: 1px solid #eef2f7;
    }

    .cn-trace-table tr:last-child {
        border-bottom: 0;
    }

    .cn-trace-table th {
        width: 24%;
        padding: 11px 12px;
        color: var(--cn-muted);
        font-size: .7rem;
        font-weight: 700;
        background: #ffffff;
        border-right: 1px solid #eef2f7;
    }

    .cn-trace-table td {
        padding: 11px 12px;
        color: #334155;
        font-size: .73rem;
        word-break: break-word;
    }

    .cn-trace-code {
        display: inline-block;
        max-width: 100%;
        padding: 4px 7px;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        background: #ffffff;
        color: #475569;
        font-size: .66rem;
        word-break: break-all;
    }

    .cn-alert {
        border: 0;
        border-radius: 9px;
        font-size: .75rem;
    }

    .cn-footer {
        text-align: center;
        color: #94a3b8;
        font-size: .65rem;
        margin-top: 18px;
    }

    @media (max-width: 768px) {
        .cn-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .cn-header-actions {
            justify-content: flex-start;
        }

        .cn-document-box {
            align-items: flex-start;
            flex-direction: column;
        }

        .cn-document-date {
            text-align: left;
        }

        .cn-trace-table th {
            width: 38%;
        }
    }

.cn-back { padding: 7px 12px !important; border: 1px solid #94a3b8 !important; border-radius: 8px !important; background: #ffffff !important; color: #475569 !important; }

.cn-retry-btn { border: 1px solid #d97706 !important; border-radius: 8px !important; padding: 7px 12px !important; background: #fff7ed !important; color: #b45309 !important; }

.cn-retry-btn:hover { border-color: #b45309 !important; }
</style>

<div class="container-fluid cn-show">

    <div class="cn-wrapper">

        @php
            $statusData = match($cn->sunat_status) {
                'ACCEPTED' => ['accepted', 'Aceptado', 'check-circle-fill'],
                'OBSERVED' => ['observed', 'Observado', 'exclamation-triangle-fill'],
                'PENDING' => ['pending', 'Pendiente', 'clock-fill'],
                'REJECTED' => ['rejected', 'Rechazado', 'x-circle-fill'],
                'ERROR' => ['error', 'Error', 'exclamation-circle-fill'],
                default => ['pending', $cn->sunat_status, 'question-circle-fill'],
            };
        @endphp

        {{-- Encabezado --}}
        <div class="cn-header">

            <div>
                <a href="{{ route('credit_notes.index') }}" class="cn-back">
                    <i class="bi bi-arrow-left"></i>
                    Notas de Crédito
                </a>

                <div class="cn-title-row">
                    <h2 class="cn-title">
                        Nota de Crédito {{ $cn->full_number }}
                    </h2>

                    <span class="cn-status {{ $statusData[0] }}">
                        <i class="bi bi-{{ $statusData[2] }}"></i>
                        {{ $statusData[1] }}
                    </span>
                </div>

                <p class="cn-subtitle">
                    Detalle y trazabilidad del comprobante electrónico.
                </p>
            </div>

            <div class="cn-header-actions">

                @if($cn->xml_path)
                    <a href="{{ route('credit_notes.xml', $cn) }}"
                       class="btn btn-outline-dark">
                        <i class="bi bi-filetype-xml me-1"></i>
                        XML
                    </a>
                @endif

                @if($cn->cdr_path)
                    <a href="{{ route('credit_notes.cdr', $cn) }}"
                       class="btn btn-outline-success">
                        <i class="bi bi-archive me-1"></i>
                        CDR
                    </a>
                @endif

                @if(in_array($cn->sunat_status, ['PENDING','ERROR','REJECTED']))
                    <form method="POST"
                          action="{{ route('credit_notes.retry', $cn) }}"
                          class="d-inline">

                        @csrf

                        <button type="submit"
                                class="btn btn-outline-warning cn-retry-btn"
                                onclick="return confirm('¿Reintentar envío a SUNAT?')">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            Reintentar
                        </button>

                    </form>
                @endif

            </div>

        </div>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="alert alert-success cn-alert mb-3">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger cn-alert mb-3">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Documento afectado --}}
        <div class="cn-card">

            <div class="cn-card-header">
                <h6>
                    <i class="bi bi-file-earmark-text me-1"></i>
                    Documento afectado
                </h6>
                <p>Comprobante al que se encuentra asociada esta Nota de Crédito.</p>
            </div>

            <div class="cn-card-body">

                <div class="cn-document-box">

                    <div class="cn-document-left">

                        <div class="cn-document-icon">
                            <i class="bi bi-receipt"></i>
                        </div>

                        <div>
                            <div class="cn-document-label">
                                Comprobante original
                            </div>

                            <a href="{{ route('billing.show', $cn->order_id) }}"
                               class="cn-document-number">
                                {{ $cn->order?->document_type }} {{ $cn->order?->full_number }}
                            </a>
                        </div>

                    </div>

                    <div class="cn-document-date">
                        <div>Fecha de emisión de la NC</div>
                        <strong class="text-dark">
                            {{ $cn->created_at->format('d/m/Y H:i') }}
                        </strong>
                    </div>

                </div>

            </div>

        </div>

        {{-- Motivo y totales --}}
        <div class="row g-3">

            <div class="col-lg-7">

                <div class="cn-card h-100 mb-0">

                    <div class="cn-card-header">
                        <h6>
                            <i class="bi bi-chat-left-text me-1"></i>
                            Motivo
                        </h6>
                        <p>Información registrada para la emisión.</p>
                    </div>

                    <div class="cn-card-body">

                        <div class="cn-section-label">
                            Catálogo 09 SUNAT
                        </div>

                        <div class="cn-reason">
                            <span class="cn-code">
                                {{ $cn->reason_code }}
                            </span>

                            {{ $cn->reason_description }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="cn-card h-100 mb-0">

                    <div class="cn-card-header">
                        <h6>
                            <i class="bi bi-calculator me-1"></i>
                            Importes
                        </h6>
                        <p>Resumen económico de la nota.</p>
                    </div>

                    <div class="cn-card-body">

                        <div class="cn-total-box">

                            <div class="cn-total-row">
                                <span>Subtotal</span>
                                <strong>
                                    S/ {{ number_format($cn->subtotal, 2) }}
                                </strong>
                            </div>

                            <div class="cn-total-row">
                                <span>IGV</span>
                                <strong>
                                    S/ {{ number_format($cn->igv, 2) }}
                                </strong>
                            </div>

                            <div class="cn-total-row cn-total-final">
                                <span>TOTAL</span>
                                <strong>
                                    S/ {{ number_format($cn->total, 2) }}
                                </strong>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Trazabilidad SUNAT --}}
        <div class="cn-card mt-3">

            <div class="cn-card-header">
                <h6>
                    <i class="bi bi-shield-check me-1"></i>
                    Trazabilidad SUNAT
                </h6>
                <p>Información técnica y estado de comunicación del comprobante.</p>
            </div>

            <div class="table-responsive">

                <table class="table cn-trace-table mb-0">

                    <tbody>

                        <tr>
                            <th>Estado</th>
                            <td>
                                <span class="cn-status {{ $statusData[0] }}">
                                    <i class="bi bi-{{ $statusData[2] }}"></i>
                                    {{ $statusData[1] }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Código SUNAT</th>
                            <td>
                                {{ $cn->sunat_code ?? '—' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Descripción</th>
                            <td>
                                {{ $cn->sunat_description ?? '—' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Hash (DigestValue)</th>
                            <td>
                                @if($cn->hash)
                                    <code class="cn-trace-code">
                                        {{ $cn->hash }}
                                    </code>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Fecha de envío</th>
                            <td>
                                {{ $cn->sent_at?->format('d/m/Y H:i:s') ?? '—' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Archivo XML</th>
                            <td>
                                @if($cn->xml_path)
                                    <code class="cn-trace-code">
                                        {{ $cn->xml_path }}
                                    </code>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Archivo CDR</th>
                            <td>
                                @if($cn->cdr_path)
                                    <code class="cn-trace-code">
                                        {{ $cn->cdr_path }}
                                    </code>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <div class="cn-footer">
            Documento electrónico · SUNAT
        </div>

    </div>

</div>

@endsection
