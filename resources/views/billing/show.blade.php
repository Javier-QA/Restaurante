@extends('layouts.app')

@section('content')

@php
    $statusData = match($order->sunat_status) {
        'ACCEPTED' => ['success', 'Aceptado', 'check-circle-fill'],
        'OBSERVED' => ['warning', 'Observado', 'exclamation-triangle-fill'],
        'PENDING'  => ['secondary', 'Pendiente', 'hourglass-split'],
        'VOIDED'   => ['dark', 'Anulado', 'slash-circle-fill'],
        'REJECTED' => ['danger', 'Rechazado', 'x-circle-fill'],
        default    => ['danger', 'Con error', 'x-circle-fill'],
    };

    [$statusColor, $statusLabel, $statusIcon] = $statusData;
@endphp

<div class="billing-detail-page">

    <div class="billing-detail-header">

        <div>
            <a href="{{ route('billing.index') }}"
               class="billing-back">
                <i class="bi bi-arrow-left"></i>
                Volver a comprobantes
            </a>

            <div class="billing-detail-title">
                <i class="bi bi-receipt"></i>

                <div>
                    <h2>
                        {{ $order->document_type }}
                        {{ $order->full_number }}
                    </h2>

                    <div class="billing-detail-subtitle">
                        Detalle del comprobante electrónico
                    </div>
                </div>
            </div>
        </div>

        <span class="badge bg-{{ $statusColor }} billing-main-status">
            <i class="bi bi-{{ $statusIcon }}"></i>
            {{ $statusLabel }}
        </span>

    </div>

    {{-- Acciones --}}
    <div class="billing-detail-actions">

        <a href="{{ route('billing.pdf', $order) }}"
           target="_blank"
           class="btn billing-action-primary">
            <i class="bi bi-file-pdf"></i>
            PDF A4
        </a>

        <a href="{{ route('billing.pdf.ticket', $order) }}"
           target="_blank"
           class="btn btn-outline-secondary">
            <i class="bi bi-receipt"></i>
            Ticket 80 mm
        </a>

        @if($order->xml_path)
            <a href="{{ route('billing.xml', $order) }}"
               class="btn btn-outline-info">
                <i class="bi bi-filetype-xml"></i>
                XML
            </a>
        @endif

        @if($order->cdr_path)
            <a href="{{ route('billing.cdr', $order) }}"
               class="btn btn-outline-success">
                <i class="bi bi-archive"></i>
                CDR
            </a>
        @endif

        @if(in_array($order->sunat_status, ['PENDING','ERROR','REJECTED']))
            <form method="POST"
                  action="{{ route('billing.retry', $order) }}"
                  id="billingRetryForm"
                  class="d-inline">
                @csrf

                <button type="button"
                        class="btn btn-outline-warning"
                        id="billingRetryButton">
                    <i class="bi bi-arrow-repeat"></i>
                    Reintentar
                </button>
            </form>
        @endif

        @if($order->sunat_status === 'ACCEPTED')
            <a href="{{ route('credit_notes.create', $order) }}"
               class="btn btn-outline-warning">
                <i class="bi bi-arrow-counterclockwise"></i>
                Emitir N.C.
            </a>
        @endif

    </div>

    @if(session('success'))
        <div class="alert alert-success billing-detail-alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger billing-detail-alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">

        {{-- Detalle de productos --}}
        <div class="col-12 col-xl-8">

            <div class="billing-detail-card">

                <div class="billing-card-header">
                    <div class="billing-card-title">
                        <span class="billing-card-icon">
                            <i class="bi bi-bag-check"></i>
                        </span>

                        <div>
                            <h5>Detalle del comprobante</h5>
                            <span>Productos incluidos en la venta</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="table billing-products-table">

                        <thead>
                            <tr>
                                <th style="width: 80px;">Cant.</th>
                                <th>Producto</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($order->details as $d)

                                <tr>
                                    <td>
                                        <span class="billing-quantity">
                                            {{ $d->quantity }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="billing-product-name">
                                            {{ $d->product->name ?? 'Producto no disponible' }}
                                        </div>
                                    </td>

                                    <td class="text-end billing-money">
                                        S/ {{ number_format($d->price, 2) }}
                                    </td>

                                    <td class="text-end billing-money fw-bold">
                                        S/ {{ number_format($d->price * $d->quantity, 2) }}
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="billing-totals">

                    <div class="billing-total-row">
                        <span>Op. Gravada</span>
                        <strong>
                            S/ {{ number_format($order->total_gravada, 2) }}
                        </strong>
                    </div>

                    <div class="billing-total-row">
                        <span>IGV (18%)</span>
                        <strong>
                            S/ {{ number_format($order->igv, 2) }}
                        </strong>
                    </div>

                    <div class="billing-total-row billing-grand-total">
                        <span>Total</span>
                        <strong>
                            S/ {{ number_format($order->total, 2) }}
                        </strong>
                    </div>

                </div>

            </div>

            @if($order->creditNotes->isNotEmpty())

                <div class="billing-detail-card mt-4">

                    <div class="billing-card-header">
                        <div class="billing-card-title">
                            <span class="billing-card-icon">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </span>

                            <div>
                                <h5>Notas de crédito asociadas</h5>
                                <span>Documentos vinculados al comprobante</span>
                            </div>
                        </div>
                    </div>

                    <div class="billing-credit-list">

                        @foreach($order->creditNotes as $cn)

                            @php
                                $cnAccepted = $cn->sunat_status === 'ACCEPTED';
                            @endphp

                            <a href="{{ route('credit_notes.show', $cn) }}"
                               class="billing-credit-item">

                                <div>
                                    <strong>{{ $cn->full_number }}</strong>

                                    <span>
                                        [{{ $cn->reason_code }}]
                                        {{ $cn->reason_description }}
                                    </span>
                                </div>

                                <span class="badge bg-{{ $cnAccepted ? 'success' : 'danger' }}">
                                    {{ $cnAccepted ? 'Aceptado' : $cn->sunat_status }}
                                </span>

                            </a>

                        @endforeach

                    </div>

                </div>

            @endif

        </div>

        {{-- Informacion lateral --}}
        <div class="col-12 col-xl-4">

            <div class="billing-detail-card">

                <div class="billing-card-header">
                    <div class="billing-card-title">
                        <span class="billing-card-icon">
                            <i class="bi bi-person"></i>
                        </span>

                        <div>
                            <h5>Datos del cliente</h5>
                            <span>Información asociada al comprobante</span>
                        </div>
                    </div>
                </div>

                <div class="billing-info-body">

                    <div class="billing-info-row">
                        <span>Nombre</span>
                        <strong>
                            {{ $order->client_name ?: 'Cliente' }}
                        </strong>
                    </div>

                    <div class="billing-info-row">
                        <span>Documento</span>
                        <strong>
                            {{ $order->client_document ?: 'Sin documento' }}
                        </strong>
                    </div>

                    <div class="billing-info-row">
                        <span>Tipo</span>
                        <strong>{{ $order->document_type }}</strong>
                    </div>

                    <div class="billing-info-row">
                        <span>Comprobante</span>
                        <strong>{{ $order->full_number }}</strong>
                    </div>

                    <div class="billing-info-row">
                        <span>Fecha de emisión</span>
                        <strong>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </strong>
                    </div>

                </div>

            </div>

            <div class="billing-detail-card mt-4">

                <div class="billing-card-header">
                    <div class="billing-card-title">
                        <span class="billing-card-icon">
                            <i class="bi bi-cloud-check"></i>
                        </span>

                        <div>
                            <h5>Trazabilidad SUNAT</h5>
                            <span>Información del envío electrónico</span>
                        </div>
                    </div>
                </div>

                <div class="billing-info-body">

                    <div class="billing-info-row">
                        <span>Estado</span>

                        <span class="badge bg-{{ $statusColor }} billing-trace-status">
                            <i class="bi bi-{{ $statusIcon }}"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="billing-info-row">
                        <span>Código</span>
                        <strong>
                            {{ $order->sunat_code ?? '—' }}
                        </strong>
                    </div>

                    <div class="billing-info-row billing-info-description">
                        <span>Descripción</span>
                        <strong>
                            {{ $order->sunat_description ?? '—' }}
                        </strong>
                    </div>

                    <div class="billing-info-row">
                        <span>Enviado</span>
                        <strong>
                            {{ $order->sent_at?->format('d/m/Y H:i:s') ?? '—' }}
                        </strong>
                    </div>

                    <div class="billing-trace-block">
                        <span>Hash</span>
                        <code>{{ $order->hash ?? '—' }}</code>
                    </div>

                    <div class="billing-trace-block">
                        <span>XML</span>
                        <code>{{ $order->xml_path ?? '—' }}</code>
                    </div>

                    <div class="billing-trace-block">
                        <span>CDR</span>
                        <code>{{ $order->cdr_path ?? '—' }}</code>
                    </div>

                </div>

            </div>

        </div>

    </div>

<style>
    .billing-detail-page {
        width: 100%;
        padding-bottom: 30px;
    }

    .billing-detail-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 16px;
    }

    .billing-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted, #64748b);
        text-decoration: none;
        font-size: .78rem;
        font-weight: 700;
        margin-bottom: 10px;
        transition: color .15s ease;
    }

    .billing-back:hover {
        color: var(--primary);
    }

    .billing-detail-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .billing-detail-title > i {
        color: #000 !important;
        font-size: 1.55rem;
    }

    .billing-detail-title h2 {
        color: var(--text-main, #111827);
        font-size: 1.55rem;
        line-height: 1.15;
        font-weight: 800;
        margin: 0;
    }

    .billing-detail-subtitle {
        color: var(--text-muted, #64748b);
        font-size: .78rem;
        margin-top: 3px;
    }

    .billing-main-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 8px 13px;
        font-size: .74rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .billing-detail-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .billing-detail-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 38px;
        border-radius: 10px;
        font-size: .78rem;
        font-weight: 700;
        padding: 8px 13px;
    }

    .billing-detail-actions form {
        margin: 0;
    }

    .billing-action-primary {
        background: var(--primary);
        border: 1px solid var(--primary);
        color: #fff;
    }

    .billing-action-primary:hover,
    .billing-action-primary:focus {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #fff;
    }

    .billing-detail-alert {
        border: 0;
        border-radius: 12px;
        font-size: .82rem;
        margin-bottom: 18px;
    }

    .billing-detail-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 16px;
        box-shadow: 0 2px 14px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .billing-card-header {
        padding: 17px 19px;
        border-bottom: 1px solid var(--border-soft, #e5e7eb);
        background: var(--card-bg, #fff);
    }

    .billing-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .billing-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: color-mix(in srgb, var(--primary) 10%, white);
        color: var(--primary);
        font-size: 1.05rem;
    }

    .billing-card-title h5 {
        color: var(--text-main, #111827);
        font-size: .92rem;
        font-weight: 800;
        margin: 0 0 2px;
    }

    .billing-card-title span {
        color: var(--text-muted, #64748b);
        font-size: .72rem;
    }
</style>

<style>
    .billing-products-table {
        margin: 0;
    }

    .billing-products-table thead th {
        background: var(--light-bg, #f8fafc);
        color: var(--text-muted, #64748b);
        border-bottom: 1px solid var(--border-soft, #e5e7eb);
        padding: 13px 16px;
        font-size: .70rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .035em;
        white-space: nowrap;
    }

    .billing-products-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-soft, #eef2f7);
        font-size: .80rem;
    }

    .billing-products-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .billing-quantity {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 30px;
        padding: 0 8px;
        border-radius: 9px;
        background: var(--light-bg, #f8fafc);
        color: var(--text-main, #111827);
        font-weight: 800;
    }

    .billing-product-name {
        color: var(--text-main, #111827);
        font-weight: 700;
    }

    .billing-money {
        color: var(--text-main, #111827);
        white-space: nowrap;
    }

    .billing-totals {
        margin-left: auto;
        width: min(100%, 360px);
        padding: 16px 20px 20px;
    }

    .billing-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 7px 0;
        color: var(--text-muted, #64748b);
        font-size: .80rem;
    }

    .billing-total-row strong {
        color: var(--text-main, #111827);
    }

    .billing-grand-total {
        margin-top: 7px;
        padding: 13px 14px;
        border-radius: 12px;
        background: color-mix(in srgb, var(--primary) 9%, white);
        color: var(--text-main, #111827);
        font-size: .90rem;
        font-weight: 800;
    }

    .billing-grand-total strong {
        color: var(--primary);
        font-size: 1.05rem;
    }

    .billing-info-body {
        padding: 8px 19px 16px;
    }

    .billing-info-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        padding: 11px 0;
        border-bottom: 1px solid var(--border-soft, #eef2f7);
        font-size: .78rem;
    }

    .billing-info-row:last-of-type {
        border-bottom: 0;
    }

    .billing-info-row > span:first-child {
        color: var(--text-muted, #64748b);
        flex-shrink: 0;
    }

    .billing-info-row strong {
        color: var(--text-main, #111827);
        font-weight: 700;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .billing-info-description strong {
        max-width: 68%;
    }

    .billing-trace-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .68rem;
        font-weight: 800;
    }

    .billing-trace-block {
        margin-top: 12px;
    }

    .billing-trace-block > span {
        display: block;
        color: var(--text-muted, #64748b);
        font-size: .70rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .billing-trace-block code {
        display: block;
        width: 100%;
        padding: 9px 10px;
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 9px;
        background: var(--light-bg, #f8fafc);
        color: var(--text-main, #111827);
        font-size: .68rem;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .billing-credit-list {
        padding: 6px 18px 12px;
    }

    .billing-credit-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 12px 2px;
        border-bottom: 1px solid var(--border-soft, #eef2f7);
        text-decoration: none;
    }

    .billing-credit-item:last-child {
        border-bottom: 0;
    }

    .billing-credit-item strong {
        display: block;
        color: var(--text-main, #111827);
        font-size: .80rem;
    }

    .billing-credit-item div > span {
        display: block;
        color: var(--text-muted, #64748b);
        font-size: .70rem;
        margin-top: 2px;
    }

    @media (max-width: 767.98px) {
        .billing-detail-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .billing-detail-title h2 {
            font-size: 1.25rem;
        }

        .billing-detail-actions .btn,
        .billing-detail-actions form {
            width: 100%;
        }

        .billing-detail-actions form .btn {
            width: 100%;
        }

        .billing-totals {
            width: 100%;
        }

        .billing-info-row {
            gap: 12px;
        }
    }
</style>

    {{-- Modal de reintento SUNAT --}}
    @if(in_array($order->sunat_status, ['PENDING','ERROR','REJECTED']))

        <div class="modal fade"
             id="billingRetryModal"
             tabindex="-1"
             aria-labelledby="billingRetryModalLabel"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content billing-confirm-modal">

                    <div class="modal-body p-4 text-center">

                        <div class="billing-confirm-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>

                        <h5 class="fw-bold mb-2"
                            id="billingRetryModalLabel">
                            ¿Reintentar envío?
                        </h5>

                        <p class="text-muted mb-1">
                            Se volverá a enviar el comprobante
                        </p>

                        <div class="billing-confirm-document">
                            {{ $order->full_number }}
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
                                    id="billingConfirmRetry">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Reintentar envío
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>

<style>
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
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 700;
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
</style>

@if(in_array($order->sunat_status, ['PENDING','ERROR','REJECTED']))

<script>
document.addEventListener('DOMContentLoaded', function () {
    const retryButton = document.getElementById('billingRetryButton');
    const retryForm = document.getElementById('billingRetryForm');
    const modalElement = document.getElementById('billingRetryModal');
    const confirmButton = document.getElementById('billingConfirmRetry');

    if (!retryButton || !retryForm || !modalElement || !confirmButton) {
        return;
    }

    const retryModal = new bootstrap.Modal(modalElement);

    retryButton.addEventListener('click', function () {
        retryModal.show();
    });

    confirmButton.addEventListener('click', function () {
        confirmButton.disabled = true;

        confirmButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';

        retryForm.submit();
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        confirmButton.disabled = false;

        confirmButton.innerHTML =
            '<i class="bi bi-arrow-repeat me-1"></i> Reintentar envío';
    });
});
</script>

@endif


<style>
    /* Mejorar barra de acciones del detalle */
    .billing-detail-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin: 4px 0 20px;
    }

    .billing-detail-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 8px 15px;
        border-radius: 10px;
        border-width: 1px !important;
        border-style: solid !important;
        background: var(--card-bg, #fff);
        font-size: .76rem;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        transition:
            transform .15s ease,
            box-shadow .15s ease,
            background .15s ease;
    }

    .billing-detail-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .08);
    }

    /* PDF A4 - acción principal */
    .billing-detail-actions .billing-action-primary {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #fff !important;
    }

    .billing-detail-actions .billing-action-primary:hover {
        background: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
    }

    /* Ticket */
    .billing-detail-actions .btn-outline-secondary {
        background: var(--card-bg, #fff);
        border-color: #94a3b8 !important;
        color: #475569;
    }

    /* XML */
    .billing-detail-actions .btn-outline-info {
        background: var(--card-bg, #fff);
        border-color: #38bdf8 !important;
        color: #0284c7;
    }

    /* CDR */
    .billing-detail-actions .btn-outline-success {
        background: var(--card-bg, #fff);
        border-color: #22c55e !important;
        color: #15803d;
    }

    /* Nota de crédito / Reintentar */
    .billing-detail-actions .btn-outline-warning {
        background: var(--card-bg, #fff);
        border-color: #f59e0b !important;
        color: #d97706;
    }

    .billing-detail-actions .btn i {
        font-size: .82rem;
    }

    @media (max-width: 767.98px) {
        .billing-detail-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .billing-detail-actions .btn,
        .billing-detail-actions form,
        .billing-detail-actions form .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .billing-detail-actions {
            grid-template-columns: 1fr;
        }
    }
</style>


<style>
    /* Botón volver a comprobantes */
    .billing-back {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 36px;
        padding: 7px 12px;
        margin-bottom: 13px;
        border: 1px solid var(--border-soft, #cbd5e1);
        border-radius: 9px;
        background: var(--card-bg, #fff);
        color: var(--text-main, #334155);
        text-decoration: none !important;
        font-size: .75rem;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease,
            transform .15s ease,
            box-shadow .15s ease;
    }

    .billing-back i {
        font-size: .82rem;
        color: var(--primary);
    }

    .billing-back:hover {
        background: color-mix(in srgb, var(--primary) 7%, white);
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .07);
    }

    .billing-back:hover i {
        color: var(--primary);
    }
</style>


<style>
    /* Botón volver - tamaño más grande */
    .billing-back {
        min-height: 43px !important;
        padding: 10px 17px !important;
        font-size: .86rem !important;
        border-radius: 11px !important;
        gap: 9px !important;
        margin-bottom: 16px !important;
        border-width: 1px !important;
    }

    .billing-back i {
        font-size: 1rem !important;
    }
</style>


<style>
    /* Organización general del detalle */
    .billing-detail-page .row.g-4 {
        --bs-gutter-x: 20px;
        --bs-gutter-y: 20px;
        align-items: flex-start;
    }

    /* Tarjetas más compactas */
    .billing-detail-card {
        border-radius: 16px;
    }

    .billing-card-header {
        min-height: 70px;
        display: flex;
        align-items: center;
    }

    /* Tabla de productos */
    .billing-products-table tbody td {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    /* Totales separados visualmente de los productos */
    .billing-totals {
        width: 340px;
        max-width: calc(100% - 32px);
        margin: 4px 16px 16px auto;
        padding: 13px 15px;
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 13px;
        background: var(--light-bg, #f8fafc);
    }

    .billing-total-row {
        padding: 6px 2px;
    }

    .billing-grand-total {
        margin-top: 7px;
        padding: 11px 12px;
        background: color-mix(in srgb, var(--primary) 10%, white);
    }

    /* Columna derecha */
    .billing-info-body {
        padding: 7px 17px 15px;
    }

    .billing-info-row {
        min-height: 39px;
        align-items: center;
        padding: 9px 0;
    }

    .billing-info-row strong {
        max-width: 68%;
    }

    /* SUNAT */
    .billing-trace-block {
        margin-top: 10px;
    }

    .billing-trace-block code {
        padding: 9px 10px;
        min-height: 35px;
        display: flex;
        align-items: center;
    }

    /* Separación uniforme entre tarjetas derechas */
    .billing-detail-page .col-xl-4 > .billing-detail-card.mt-4 {
        margin-top: 20px !important;
    }

    @media (max-width: 1199.98px) {
        .billing-totals {
            width: 360px;
        }
    }

    @media (max-width: 575.98px) {
        .billing-totals {
            width: auto;
            max-width: none;
            margin: 10px 12px 12px;
        }

        .billing-info-row strong {
            max-width: 62%;
        }
    }
</style>


<style>
    /* Resumen de importes */
    .billing-totals {
        width: 350px;
        max-width: calc(100% - 32px);
        margin: 12px 16px 16px auto;
        padding: 15px 17px 14px;
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 14px;
        background: var(--card-bg, #fff);
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
    }

    .billing-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 7px 2px;
        color: var(--text-muted, #64748b);
        font-size: .80rem;
    }

    .billing-total-row strong {
        color: var(--text-main, #111827);
        font-size: .82rem;
        font-weight: 800;
    }

    .billing-grand-total {
        margin-top: 9px;
        padding: 13px 14px;
        border-top: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 10px;
        background: color-mix(in srgb, var(--primary) 7%, white);
        color: var(--text-main, #111827);
    }

    .billing-grand-total span {
        font-size: .88rem;
        font-weight: 800;
    }

    .billing-grand-total strong {
        color: var(--primary);
        font-size: 1.20rem;
        font-weight: 900;
        letter-spacing: -.02em;
    }

    @media (max-width: 575.98px) {
        .billing-totals {
            width: auto;
            max-width: none;
            margin: 12px;
        }
    }
</style>

@endsection