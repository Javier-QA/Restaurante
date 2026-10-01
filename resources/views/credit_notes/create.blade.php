@extends('layouts.app')

@section('content')

<style>
    .cn-create {
        --cn-black: #111827;
        --cn-text: #334155;
        --cn-muted: #64748b;
        --cn-border: #e5e7eb;
        --cn-soft: #f8fafc;
    }

    .cn-wrapper {
        max-width: 900px;
        margin: 0 auto;
    }

    .cn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--cn-muted);
        text-decoration: none;
        font-size: .78rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .cn-back:hover {
        color: var(--cn-black);
    }

    .cn-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .cn-title {
        margin: 0;
        color: var(--cn-black);
        font-size: 1.55rem;
        font-weight: 800;
    }

    .cn-subtitle {
        margin: 6px 0 0;
        color: var(--cn-muted);
        font-size: .82rem;
    }

    .cn-header-icon {
        width: 48px;
        height: 48px;
        border: 1px solid var(--cn-border);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: var(--cn-black);
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .cn-card {
        border: 1px solid var(--cn-border);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 6px 22px rgba(15,23,42,.05);
        overflow: hidden;
    }

    .cn-card-header {
        padding: 17px 22px;
        border-bottom: 1px solid var(--cn-border);
        background: #fff;
    }

    .cn-card-header h6 {
        margin: 0;
        font-size: .88rem;
        font-weight: 800;
        color: var(--cn-black);
    }

    .cn-card-header p {
        margin: 4px 0 0;
        font-size: .72rem;
        color: var(--cn-muted);
    }

    .cn-body {
        padding: 22px;
    }

    .cn-info {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        margin-bottom: 22px;
        border: 1px solid #dbe1e8;
        border-radius: 9px;
        background: #f8fafc;
        color: #475569;
        font-size: .76rem;
        line-height: 1.45;
    }

    .cn-info i {
        color: #334155;
        font-size: .95rem;
        margin-top: 1px;
    }

    .cn-label {
        display: block;
        margin-bottom: 6px;
        color: #334155;
        font-size: .75rem;
        font-weight: 800;
    }

    .cn-required {
        color: #991b1b;
    }

    .cn-select,
    .cn-textarea {
        border-color: var(--cn-border);
        border-radius: 9px;
        font-size: .8rem;
    }

    .cn-select {
        min-height: 42px;
    }

    .cn-textarea {
        min-height: 92px;
        resize: vertical;
    }

    .cn-select:focus,
    .cn-textarea:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148,163,184,.14);
    }

    .cn-help {
        display: block;
        margin-top: 5px;
        color: var(--cn-muted);
        font-size: .68rem;
    }

    .cn-document {
        margin-top: 22px;
        border: 1px solid var(--cn-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .cn-document-header {
        padding: 13px 16px;
        background: #f8fafc;
        border-bottom: 1px solid var(--cn-border);
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .cn-document-header i {
        color: #475569;
    }

    .cn-document-header span {
        color: #111827;
        font-size: .78rem;
        font-weight: 800;
    }

    .cn-document-body {
        padding: 4px 16px;
    }

    .cn-document-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        min-height: 42px;
        border-bottom: 1px solid #f1f5f9;
        font-size: .76rem;
    }

    .cn-document-row:last-child {
        border-bottom: 0;
    }

    .cn-document-label {
        color: var(--cn-muted);
        font-weight: 600;
    }

    .cn-document-value {
        color: #111827;
        font-weight: 700;
        text-align: right;
    }

    .cn-number {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border: 1px solid #dbe1e8;
        border-radius: 7px;
        background: #f8fafc;
        font-weight: 800;
    }

    .cn-total {
        font-size: .95rem;
        font-weight: 800;
    }

    .cn-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid var(--cn-border);
    }

    .cn-actions .btn {
        min-height: 39px;
        border-radius: 8px;
        font-size: .76rem;
        font-weight: 700;
        padding: 7px 15px;
    }

    .cn-submit {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .cn-submit:hover {
        background: #000;
        border-color: #000;
        color: #fff;
    }

    .cn-footer-note {
        margin-top: 14px;
        text-align: center;
        color: #94a3b8;
        font-size: .66rem;
    }

    @media (max-width: 576px) {
        .cn-header {
            align-items: flex-start;
        }

        .cn-body {
            padding: 16px;
        }

        .cn-document-row {
            align-items: flex-start;
            flex-direction: column;
            gap: 4px;
            padding: 10px 0;
        }

        .cn-document-value {
            text-align: left;
        }

        .cn-actions {
            flex-direction: column-reverse;
        }

        .cn-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid cn-create">

    <div class="cn-wrapper">

        {{-- Encabezado --}}
        <div class="cn-header">

            <div>
                <a href="{{ route('billing.show', $order) }}" class="cn-back">
                    <i class="bi bi-arrow-left"></i>
                    Volver al comprobante
                </a>

                <h2 class="cn-title">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                    Emitir Nota de Crédito
                </h2>

                <p class="cn-subtitle">
                    Genera una nota de crédito asociada al comprobante seleccionado.
                </p>
            </div>

            <div class="cn-header-icon">
                <i class="bi bi-receipt"></i>
            </div>

        </div>

        {{-- Formulario --}}
        <form action="{{ route('credit_notes.store', $order) }}"
              method="POST"
              class="cn-card">

            @csrf

            <div class="cn-card-header">
                <h6>Datos de la Nota de Crédito</h6>
                <p>Completa la información necesaria para emitir el documento electrónico.</p>
            </div>

            <div class="cn-body">

                {{-- Información --}}
                <div class="cn-info">
                    <i class="bi bi-info-circle"></i>

                    <div>
                        La nota de crédito será enviada a SUNAT inmediatamente después
                        de emitirla. Si se presenta algún inconveniente, podrás
                        utilizar la opción de reintento posteriormente.
                    </div>
                </div>

                {{-- Motivo --}}
                <div class="mb-4">

                    <label class="cn-label">
                        Motivo de la nota de crédito
                        <span class="cn-required">*</span>
                    </label>

                    <select name="reason_code"
                            class="form-select cn-select"
                            required>

                        <option value="">
                            Seleccionar motivo...
                        </option>

                        @foreach($reasons as $code => $desc)
                            <option value="{{ $code }}"
                                {{ old('reason_code') === $code ? 'selected' : '' }}>
                                {{ $code }} — {{ $desc }}
                            </option>
                        @endforeach

                    </select>

                    <small class="cn-help">
                        Selecciona el motivo correspondiente según el Catálogo 09 de SUNAT.
                    </small>

                </div>

                {{-- Descripción --}}
                <div class="mb-0">

                    <label class="cn-label">
                        Descripción del motivo
                        <span class="cn-required">*</span>
                    </label>

                    <textarea
                        name="reason_description"
                        class="form-control cn-textarea"
                        rows="3"
                        maxlength="255"
                        required
                        placeholder="Ej.: Anulación por error en los datos del cliente">{{ old('reason_description') }}</textarea>

                    <small class="cn-help">
                        Máximo 255 caracteres.
                    </small>

                </div>

                {{-- Documento afectado --}}
                <div class="cn-document">

                    <div class="cn-document-header">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Documento afectado</span>
                    </div>

                    <div class="cn-document-body">

                        <div class="cn-document-row">
                            <span class="cn-document-label">
                                Comprobante
                            </span>

                            <span class="cn-document-value">
                                <span class="cn-number">
                                    {{ $order->document_type }} {{ $order->full_number }}
                                </span>
                            </span>
                        </div>

                        <div class="cn-document-row">
                            <span class="cn-document-label">
                                Cliente
                            </span>

                            <span class="cn-document-value">
                                {{ $order->client_name ?: 'Cliente Varios' }}
                                @if($order->client_document)
                                    <span class="text-muted fw-normal">
                                        · {{ $order->client_document }}
                                    </span>
                                @endif
                            </span>
                        </div>

                        <div class="cn-document-row">
                            <span class="cn-document-label">
                                Fecha de emisión
                            </span>

                            <span class="cn-document-value">
                                {{ $order->created_at->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="cn-document-row">
                            <span class="cn-document-label">
                                Importe a anular
                            </span>

                            <span class="cn-document-value cn-total">
                                S/ {{ number_format($order->total, 2) }}
                            </span>
                        </div>

                    </div>

                </div>

                {{-- Acciones --}}
                <div class="cn-actions">

                    <a href="{{ route('billing.show', $order) }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancelar
                    </a>

                    <button type="submit"
                            class="btn cn-submit"
                            onclick="return confirm('¿Emitir la Nota de Crédito? Se enviará a SUNAT.')">
                        <i class="bi bi-send me-1"></i>
                        Emitir y enviar a SUNAT
                    </button>

                </div>

                <div class="cn-footer-note">
                    Documento electrónico · SUNAT
                </div>

            </div>

        </form>

    </div>

</div>

@endsection
