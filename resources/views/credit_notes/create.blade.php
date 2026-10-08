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
        background: var(--card-bg, #ffffff);
        color: var(--cn-black);
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .cn-card {
        border: 1px solid var(--cn-border);
        border-radius: 15px;
        background: var(--card-bg, #ffffff);
        box-shadow: 0 6px 22px rgba(15,23,42,.05);
        overflow: hidden;
    }

    .cn-card-header {
        padding: 17px 22px;
        border-bottom: 1px solid var(--cn-border);
        background: var(--card-bg, #ffffff);
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
        border: 1px solid var(--surface-blue, #dbe1e8);
        border-radius: 9px;
        background: var(--card-bg, #ffffff);
        color: var(--ink-neutral, #475569);
        font-size: .76rem;
        line-height: 1.45;
    }

    .cn-info i {
        color: var(--text-main, #334155);
        font-size: .95rem;
        margin-top: 1px;
    }

    .cn-label {
        display: block;
        margin-bottom: 6px;
        color: var(--text-main, #334155);
        font-size: .75rem;
        font-weight: 800;
    }

    .cn-required {
        color: var(--ink-red, #991b1b);
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
        background: var(--card-bg, #ffffff);
        border-bottom: 1px solid var(--cn-border);
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .cn-document-header i {
        color: var(--ink-neutral, #475569);
    }

    .cn-document-header span {
        color:var(--text-main);
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
        border-bottom: 1px solid var(--surface-neutral, #f1f5f9);
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
        color:var(--text-main);
        font-weight: 700;
        text-align: right;
    }

    .cn-number {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border: 1px solid var(--surface-blue, #dbe1e8);
        border-radius: 7px;
        background: var(--card-bg, #ffffff);
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
        border-color:var(--text-main);
        color: #fff;
    }

    .cn-submit:hover {
        background: #000;
        border-color:var(--text-main);
        color: #fff;
    }

    .cn-footer-note {
        margin-top: 14px;
        text-align: center;
        color: var(--ink-neutral, #94a3b8);
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
                                S/ {{ number_format($order->collected_total, 2) }}
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
                            onclick="event.preventDefault(); const form=this.closest('form'); SystemNotify.confirm({type:'warning', title:'Emitir Nota de Crédito', text:'La Nota de Crédito será generada y enviada a SUNAT. ¿Deseas continuar?', confirmText:'Emitir y enviar', icon:'bi-receipt-cutoff', onConfirm:()=>form.submit()})">
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


<style>
/* DARK MODE - EMITIR NOTA DE CREDITO DEFINITIVO */

/* ==========================================================
   TARJETA PRINCIPAL
   ========================================================== */

html[data-color-mode="dark"] .cn-card {
    background: #101f32 !important;
    border-color: #30465d !important;
    box-shadow: none !important;
}


/* CABECERA */
html[data-color-mode="dark"] .cn-card-header {
    background: #101f32 !important;
    border-bottom-color: #30465d !important;
}

html[data-color-mode="dark"] .cn-card-header h6 {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .cn-card-header p {
    color: #8fa6bd !important;
}


/* CUERPO */
html[data-color-mode="dark"] .cn-body {
    background: #101f32 !important;
    color: #ffffff !important;
}


/* ==========================================================
   AVISO SUNAT
   ========================================================== */

html[data-color-mode="dark"] .cn-info {
    background: rgba(59, 130, 246, .08) !important;
    border-color: rgba(96, 165, 250, .25) !important;
    color: #b9cce0 !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .cn-info i {
    color: #60a5fa !important;
    -webkit-text-fill-color: #60a5fa !important;
}

html[data-color-mode="dark"] .cn-info div {
    color: #b9cce0 !important;
}


/* ==========================================================
   LABELS
   ========================================================== */

html[data-color-mode="dark"] .cn-label {
    color: #dbe7f3 !important;
}

html[data-color-mode="dark"] .cn-required {
    color: #f87171 !important;
}


/* ==========================================================
   SELECT Y TEXTAREA
   Mantenerlos oscuros y uniformes
   ========================================================== */

html[data-color-mode="dark"] .cn-select,
html[data-color-mode="dark"] .cn-textarea {
    background-color: #132338 !important;
    border-color: #30465d !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .cn-select:focus,
html[data-color-mode="dark"] .cn-textarea:focus {
    background-color: #132338 !important;
    border-color: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 3px
        color-mix(in srgb, var(--primary) 14%, transparent) !important;
}

html[data-color-mode="dark"] .cn-textarea::placeholder {
    color: #7890a8 !important;
    opacity: 1 !important;
}


/* ==========================================================
   TARJETAS / BLOQUES INTERNOS
   ========================================================== */

html[data-color-mode="dark"] .cn-summary,
html[data-color-mode="dark"] .cn-document,
html[data-color-mode="dark"] .cn-document-card,
html[data-color-mode="dark"] .cn-reference,
html[data-color-mode="dark"] .cn-reference-card {
    background: #132338 !important;
    border-color: #30465d !important;
    color: #ffffff !important;
    box-shadow: none !important;
}


/* Separadores internos */
html[data-color-mode="dark"] .cn-card hr,
html[data-color-mode="dark"] .cn-body hr {
    border-color: #30465d !important;
    opacity: 1 !important;
}

</style>


<style>
/* DARK MODE - DOCUMENTO AFECTADO DEFINITIVO */

/* CONTENEDOR */
html[data-color-mode="dark"] .cn-document {
    background: #132338 !important;
    border-color: #30465d !important;
}


/* CABECERA QUE ESTABA BLANCA */
html[data-color-mode="dark"] .cn-document-header {
    background: #101f32 !important;
    border-bottom-color: #30465d !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .cn-document-header i {
    color: var(--primary) !important;
    -webkit-text-fill-color: var(--primary) !important;
}

html[data-color-mode="dark"] .cn-document-header span {
    color: #ffffff !important;
}


/* CUERPO */
html[data-color-mode="dark"] .cn-document-body {
    background: #132338 !important;
}


/* FILAS */
html[data-color-mode="dark"] .cn-document-row {
    border-bottom-color: #30465d !important;
}

html[data-color-mode="dark"] .cn-document-label {
    color: #8fa6bd !important;
}

html[data-color-mode="dark"] .cn-document-value {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .cn-document-value .text-muted {
    color: #8fa6bd !important;
}


/* COMPROBANTE: BOLETA / FACTURA */
html[data-color-mode="dark"] .cn-number {
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


/* IMPORTE A ANULAR */
html[data-color-mode="dark"] .cn-total {
    color: var(--primary) !important;
}

</style>














<style>
/* NOTA CREDITO - ICONO IZQUIERDO FINAL */

/* Cabecera normal */
.cn-header {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

/* El icono vuelve a ser visible */
.cn-header-icon {
    position: static !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    width: 48px !important;
    height: 48px !important;
    min-width: 48px !important;

    margin: 0 !important;

    border-radius: 12px !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Icono blanco en oscuro */
html[data-color-mode="dark"] .cn-header-icon {
    background: #132338 !important;
    border: 1px solid #30465d !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .cn-header-icon i,
html[data-color-mode="dark"] .cn-header-icon i::before {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    opacity: 1 !important;
    visibility: visible !important;
}

</style>


<style>
/* NOTA CREDITO - BORDES VOLVER Y CANCELAR */

/* VOLVER AL COMPROBANTE */
.cn-back {
    border: 1px solid var(--border-soft) !important;
    border-radius: 8px !important;
    padding: 7px 11px !important;
    background: var(--card-bg) !important;
    text-decoration: none !important;
    transition: all .2s ease !important;
}

/* CANCELAR */
.cn-actions .btn-outline-secondary {
    border: 1px solid var(--border-soft) !important;
    background: var(--card-bg) !important;
    color: var(--text-muted) !important;
    box-shadow: none !important;
}


/* =========================================
   MODO OSCURO
   ========================================= */

html[data-color-mode="dark"] .cn-back,
html[data-color-mode="dark"] .cn-actions .btn-outline-secondary {
    background: #132338 !important;
    border: 1px solid #40566e !important;
    color: #b9cce0 !important;
}

/* Iconos */
html[data-color-mode="dark"] .cn-back i,
html[data-color-mode="dark"] .cn-actions .btn-outline-secondary i {
    color: #b9cce0 !important;
    -webkit-text-fill-color: #b9cce0 !important;
}

/* Hover */
html[data-color-mode="dark"] .cn-back:hover,
html[data-color-mode="dark"] .cn-actions .btn-outline-secondary:hover {
    background: color-mix(
        in srgb,
        var(--primary) 10%,
        #132338
    ) !important;

    border-color: var(--primary) !important;
    color: var(--primary) !important;
}

html[data-color-mode="dark"] .cn-back:hover i,
html[data-color-mode="dark"] .cn-actions .btn-outline-secondary:hover i {
    color: var(--primary) !important;
    -webkit-text-fill-color: var(--primary) !important;
}

</style>


<style>
/* NOTA CREDITO - CONTENEDORES A LA IZQUIERDA */

/* Contenedor general */
.cn-create {
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin-left: 0 !important;
}

/* Wrapper principal: eliminar centrado */
.cn-wrapper {
    width: 100% !important;
    max-width: none !important;

    margin-left: 0 !important;
    margin-right: 0 !important;

    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* Cabecera */
.cn-header {
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

/* Título y subtítulo */
.cn-header > div:first-child,
.cn-title,
.cn-subtitle {
    margin-left: 0 !important;
    padding-left: 0 !important;
    text-align: left !important;
}

/* Contenedor principal del formulario */
.cn-card {
    width: 100% !important;
    max-width: none !important;

    margin-left: 0 !important;
    margin-right: 0 !important;
}

/* Encabezado del formulario */
.cn-card-header {
    text-align: left !important;
}

/* Contenido del formulario */
.cn-body {
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

</style>

@endsection
