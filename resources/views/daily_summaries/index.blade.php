@extends('layouts.app')

@section('content')
<style>
    .ds-page {
        --ds-text: var(--text-main, #172033);
        --ds-muted: var(--text-muted, #64748b);
        --ds-border: var(--border-soft, #dce7f1);
        width: 100%;
    }

    .ds-header {
        margin-bottom: 22px;
    }

    .ds-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        margin-bottom: 12px;
        border: 1px solid #94a3b8;
        border-radius: 8px;
        background: var(--card-bg, #ffffff);
        color: var(--ink-neutral, #475569);
        font-size: .78rem;
        font-weight: 700;
        text-decoration: none;
        transition: .15s ease;
    }

    .ds-back:hover {
        background: var(--card-bg, #ffffff);
        border-color: #64748b;
        color: var(--text-main, #0f172a);
    }

    .ds-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: var(--ds-text);
        font-size: 1.65rem;
        font-weight: 800;
    }

    .ds-title-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: transparent;
        color:var(--text-main);
        font-size: 1.65rem;
    }

    .ds-subtitle {
        margin: 7px 0 0 48px;
        color: var(--ds-muted);
        font-size: .82rem;
    }

    .ds-create-card,
    .ds-table-card {
        border: 1px solid var(--ds-border);
        border-radius: 14px;
        background: var(--card-bg, #fff);
        box-shadow: var(--shadow-soft, 0 5px 20px rgba(15,23,42,.05));
    }

    .ds-create-card {
        margin-bottom: 20px;
        overflow: hidden;
    }

    .ds-create-head {
        padding: 15px 18px;
        border-bottom: 1px solid var(--ds-border);
        background: var(--card-bg, #ffffff);
    }

    .ds-create-head strong {
        display: block;
        color: var(--ds-text);
        font-size: .88rem;
    }

    .ds-create-head span {
        display: block;
        margin-top: 2px;
        color: var(--ds-muted);
        font-size: .72rem;
    }

    .ds-create-body {
        padding: 18px;
    }

    .ds-label {
        margin-bottom: 6px;
        color: var(--ink-neutral, #475569);
        font-size: .72rem;
        font-weight: 800;
    }

    .ds-date {
        height: 40px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: .82rem;
    }

    .ds-send {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 8px 15px;
        border: 1px solid var(--primary, #ff8c00);
        border-radius: 8px;
        background: var(--primary, #ff8c00);
        color: #fff;
        font-size: .78rem;
        font-weight: 800;
    }

    .ds-send:hover {
        background: var(--primary-hover, #e07b00);
        border-color: var(--primary-hover, #e07b00);
        color: #fff;
    }

    .ds-info {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 12px;
        border: 1px solid var(--surface-blue, #dbeafe);
        border-radius: 8px;
        background: var(--surface-blue, #eff6ff);
        color: var(--ink-neutral, #475569);
        font-size: .72rem;
        line-height: 1.5;
    }

    .ds-info i {
        color: var(--ink-blue, #2563eb);
        margin-top: 1px;
    }

    .ds-table-card {
        overflow: hidden;
    }

    .ds-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 18px;
        border-bottom: 1px solid var(--ds-border);
    }

    .ds-table-head strong {
        color: var(--ds-text);
        font-size: .88rem;
    }

    .ds-table-head span {
        color: var(--ds-muted);
        font-size: .7rem;
    }

    .ds-table {
        margin: 0;
        min-width: 1000px;
    }

    .ds-table thead th {
        padding: 11px 14px;
        border-bottom: 1px solid var(--ds-border);
        background: var(--card-bg, #ffffff);
        color: var(--ink-neutral, #64748b);
        font-size: .66rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .03em;
        white-space: nowrap;
    }

    .ds-table tbody td {
        padding: 13px 14px;
        border-color: #edf2f7;
        color: var(--text-main, #334155);
        font-size: .78rem;
        vertical-align: middle;
    }

    .ds-table tbody tr.ds-main-row:hover td {
        background: var(--card-bg, #ffffff);
    }

    .ds-identifier {
        color:var(--text-main);
        font-weight: 800;
    }

    .ds-ticket {
        display: inline-block;
        max-width: 180px;
        overflow: hidden;
        color: var(--ink-neutral, #475569);
        font-size: .68rem;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    .ds-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border: 1px solid;
        border-radius: 999px;
        font-size: .65rem;
        font-weight: 800;
    }

    .ds-status.accepted {
        color: var(--ink-green, #166534);
        background: var(--surface-green, #f0fdf4);
        border-color: #bbf7d0;
    }

    .ds-status.observed {
        color: var(--ink-amber, #92400e);
        background: var(--surface-amber, #fffbeb);
        border-color: var(--surface-amber, #fde68a);
    }

    .ds-status.ticket {
        color: #075985;
        background: var(--surface-blue, #eff6ff);
        border-color: #bae6fd;
    }

    .ds-status.pending {
        color: var(--ink-neutral, #475569);
        background: var(--card-bg, #ffffff);
        border-color: #cbd5e1;
    }

    .ds-status.error {
        color: var(--ink-red, #991b1b);
        background: var(--surface-red, #fff1f2);
        border-color: var(--surface-red, #fecaca);
    }

    .ds-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 7px !important;
        background: var(--card-bg, #ffffff);
    }

    .ds-detail-row td {
        padding: 0 14px 14px !important;
        background: var(--card-bg, #ffffff) !important;
    }

    .ds-detail-box {
        padding: 12px 14px;
        border: 1px solid var(--surface-neutral, #e2e8f0);
        border-radius: 9px;
        background: var(--card-bg, #ffffff);
    }

    .ds-response {
        margin-bottom: 8px;
        color: var(--ink-neutral, #475569);
        font-size: .72rem;
    }

    .ds-consulted {
        margin-bottom: 8px;
        color: var(--ink-neutral, #64748b);
        font-size: .68rem;
    }

    .ds-included-title {
        margin-bottom: 7px;
        color: var(--text-main, #334155);
        font-size: .7rem;
        font-weight: 800;
    }

    .ds-document {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border: 1px solid #dbe1e8;
        border-radius: 6px;
        background: var(--card-bg, #ffffff);
        color: var(--ink-neutral, #475569);
        font-size: .67rem;
        font-weight: 700;
    }

    .ds-empty {
        padding: 45px 20px !important;
        text-align: center;
        color: var(--ink-neutral, #64748b) !important;
    }

    .ds-empty i {
        display: block;
        margin-bottom: 8px;
        color: var(--ink-neutral, #94a3b8);
        font-size: 1.7rem;
    }

    .ds-footer {
        padding: 12px 16px;
        border-top: 1px solid var(--ds-border);
        background: var(--card-bg, #ffffff);
    }

    @media (max-width: 768px) {
        .ds-subtitle {
            margin-left: 0;
        }

        .ds-title {
            font-size: 1.35rem;
        }
    }
</style>

<div class="container-fluid ds-page">

    <div class="ds-header">
        <a href="{{ route('billing.index') }}" class="ds-back">
            <i class="bi bi-arrow-left"></i>
            Comprobantes
        </a>

        <h2 class="ds-title">
            <span class="ds-title-icon">
                <i class="bi bi-calendar-week"></i>
            </span>
            Resumen Diario de Boletas
        </h2>

        <p class="ds-subtitle">
            Comunicación a SUNAT en bloque de las boletas (tipo 03) emitidas un día.
        </p>
    </div>





    <div class="ds-create-card">
        <div class="ds-create-head">
            <strong>Generar nuevo resumen</strong>
            <span>Selecciona la fecha de las boletas que deseas comunicar a SUNAT.</span>
        </div>

        <div class="ds-create-body">
            <form action="{{ route('daily_summaries.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf

                <div class="col-lg-4 col-md-5">
                    <label class="form-label ds-label">FECHA DE LAS BOLETAS</label>
                    <input
                        type="date"
                        name="reference_date"
                        class="form-control ds-date"
                        value="{{ now()->subDay()->toDateString() }}"
                        required
                    >
                </div>

                <div class="col-lg-3 col-md-4">
                    <button
                        type="submit"
                        class="btn ds-send w-100"
                        onclick="event.preventDefault(); const form=this.closest('form'); SystemNotify.confirm({type:'warning', title:'Generar resumen SUNAT', text:'El resumen será generado y enviado a SUNAT. Esta operación no se puede deshacer.', confirmText:'Generar y enviar', icon:'bi-send', onConfirm:()=>form.submit()})"
                    >
                        <i class="bi bi-send"></i>
                        Generar y enviar
                    </button>
                </div>

                <div class="col-lg-5 col-md-12">
                    <div class="ds-info">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>
                            El envío es asíncrono. SUNAT devuelve un ticket y posteriormente se consulta su estado.
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ds-table-card">
        <div class="ds-table-head">
            <div>
                <strong>Historial de resúmenes</strong>
                <span class="d-block mt-1">Seguimiento de los envíos realizados a SUNAT.</span>
            </div>

            <span>
                {{ $summaries->total() }} {{ $summaries->total() === 1 ? 'registro' : 'registros' }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table ds-table align-middle">
                <thead>
                    <tr>
                        <th>Identificador</th>
                        <th>Fecha boletas</th>
                        <th>Generación</th>
                        <th class="text-center">Boletas</th>
                        <th class="text-end">Total</th>
                        <th>Ticket SUNAT</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($summaries as $s)
                        @php
                            $statusData = match($s->sunat_status) {
                                'ACCEPTED' => ['accepted', 'Aceptado', 'check-circle-fill'],
                                'OBSERVED' => ['observed', 'Observado', 'exclamation-triangle-fill'],
                                'TICKET'   => ['ticket', 'Ticket', 'clock-history'],
                                'PENDING'  => ['pending', 'Pendiente', 'clock-fill'],
                                default    => ['error', $s->sunat_status, 'exclamation-circle-fill'],
                            };
                        @endphp

                        <tr class="ds-main-row">
                            <td>
                                <span class="ds-identifier">{{ $s->identifier }}</span>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($s->reference_date)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($s->generation_date)->format('d/m/Y') }}
                            </td>

                            <td class="text-center fw-bold">
                                {{ $s->total_documents }}
                            </td>

                            <td class="text-end fw-bold">
                                S/ {{ number_format($s->total_amount, 2) }}
                            </td>

                            <td>
                                <code class="ds-ticket" title="{{ $s->ticket }}">
                                    {{ $s->ticket ?? '—' }}
                                </code>
                            </td>

                            <td>
                                <span class="ds-status {{ $statusData[0] }}">
                                    <i class="bi bi-{{ $statusData[2] }}"></i>
                                    {{ $statusData[1] }}
                                </span>

                                @if($s->sunat_code)
                                    <small class="text-muted d-block mt-1">
                                        Código {{ $s->sunat_code }}
                                    </small>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if($s->ticket && $s->sunat_status !== 'ACCEPTED')
                                        <form
                                            method="POST"
                                            action="{{ route('daily_summaries.check', $s) }}"
                                            class="d-inline"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="btn btn-outline-info ds-action"
                                                title="Consultar ticket"
                                            >
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($s->xml_path)
                                        <a
                                            href="{{ route('daily_summaries.xml', $s) }}"
                                            class="btn btn-outline-secondary ds-action"
                                            title="Descargar XML"
                                        >
                                            <i class="bi bi-filetype-xml"></i>
                                        </a>
                                    @endif

                                    @if($s->cdr_path)
                                        <a
                                            href="{{ route('daily_summaries.cdr', $s) }}"
                                            class="btn btn-outline-success ds-action"
                                            title="Descargar CDR"
                                        >
                                            <i class="bi bi-archive"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <tr class="ds-detail-row">
                            <td colspan="8">
                                <div class="ds-detail-box">

                                    @if($s->sunat_description)
                                        <div class="ds-response">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="bi bi-info-circle"></i>
                                                <strong>Respuesta de SUNAT</strong>
                                                @if($s->sunat_code)
                                                    <span class="badge text-bg-light border">Código {{ $s->sunat_code }}</span>
                                                @endif
                                            </div>
                                            <div class="mt-1 text-muted">@if($s->sunat_status === "ACCEPTED") Resumen procesado correctamente por SUNAT. @elseif((string) $s->sunat_code === "2671") <strong>No aceptado:</strong> La fecha de generación del resumen debe ser igual o posterior a la fecha de emisión de las boletas. @else <strong>No aceptado:</strong> {{ $s->sunat_description }} @endif</div>
                                        </div>
                                    @endif

                                    @if($s->consulted_at)
                                        <div class="ds-consulted">
                                            <i class="bi bi-clock me-1"></i>
                                            Última consulta:
                                            {{ $s->consulted_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    @endif

                                    @if($s->details->count())
                                        <div class="ds-included-title">
                                            <i class="bi bi-receipt me-1"></i>
                                            Boletas incluidas
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($s->details as $detail)
                                                <span class="ds-document">
                                                    {{ $detail->serie }}-{{ $detail->correlativo }}
                                                    <span>·</span>
                                                    S/ {{ number_format($detail->total_amount, 2) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="ds-empty">
                                <i class="bi bi-calendar2-x"></i>
                                No hay resúmenes diarios generados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-system-pagination :paginator="$summaries" />
    </div>
</div>

<style>
/* DARK MODE - RESUMEN DIARIO SUNAT DEFINITIVO */

/* Botón volver */
html[data-color-mode="dark"] .ds-back {
    background: #17283d !important;
    border-color: #29445f !important;
    color: #dce8f4 !important;
}

html[data-color-mode="dark"] .ds-back:hover {
    background: #20364e !important;
    border-color: #3b5874 !important;
    color: #ffffff !important;
}


/* =========================================================
   TARJETA GENERAR RESUMEN
   ========================================================= */

html[data-color-mode="dark"] .ds-create-card {
    background: #111f32 !important;
    border-color: #29445f !important;
}

html[data-color-mode="dark"] .ds-create-head {
    background: #17283d !important;
    border-color: #29445f !important;
}

html[data-color-mode="dark"] .ds-create-head strong {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .ds-create-head span {
    color: #8fa7bd !important;
}

html[data-color-mode="dark"] .ds-create-body {
    background: #111f32 !important;
}


/* Aviso de envío asíncrono */
html[data-color-mode="dark"] .ds-info {
    background: rgba(59, 130, 246, .12) !important;
    border-color: rgba(96, 165, 250, .32) !important;
    color: #bfdbfe !important;
}

html[data-color-mode="dark"] .ds-info i {
    color: #60a5fa !important;
}

html[data-color-mode="dark"] .ds-info span {
    color: #bfdbfe !important;
}


/* =========================================================
   HISTORIAL
   ========================================================= */

html[data-color-mode="dark"] .ds-table-card {
    background: #111f32 !important;
    border-color: #29445f !important;
}

html[data-color-mode="dark"] .ds-table thead th {
    background: #17283d !important;
    color: #9fc1df !important;
    border-color: #29445f !important;
}

html[data-color-mode="dark"] .ds-table tbody td {
    border-color: #29445f !important;
}

html[data-color-mode="dark"] .ds-table tbody tr.ds-main-row:hover td {
    background: #17283d !important;
}


/* =========================================================
   ESTADOS SUNAT
   ========================================================= */

html[data-color-mode="dark"] .ds-status.accepted {
    background: rgba(34, 197, 94, .14) !important;
    color: #86efac !important;
    border-color: rgba(74, 222, 128, .34) !important;
}

html[data-color-mode="dark"] .ds-status.observed {
    background: rgba(245, 158, 11, .14) !important;
    color: #fcd34d !important;
    border-color: rgba(251, 191, 36, .34) !important;
}

html[data-color-mode="dark"] .ds-status.ticket {
    background: rgba(59, 130, 246, .14) !important;
    color: #93c5fd !important;
    border-color: rgba(96, 165, 250, .34) !important;
}

html[data-color-mode="dark"] .ds-status.pending {
    background: #17283d !important;
    color: #c7d7e7 !important;
    border-color: #36516d !important;
}

html[data-color-mode="dark"] .ds-status.error {
    background: rgba(239, 68, 68, .14) !important;
    color: #fca5a5 !important;
    border-color: rgba(248, 113, 113, .34) !important;
}


/* =========================================================
   BOTONES DE ACCIÓN
   ========================================================= */

html[data-color-mode="dark"] .ds-action {
    background: #17283d !important;
    border-color: #29445f !important;
}

html[data-color-mode="dark"] .ds-action.btn-outline-info {
    color: #7dd3fc !important;
}

html[data-color-mode="dark"] .ds-action.btn-outline-secondary {
    color: #cbd5e1 !important;
}

html[data-color-mode="dark"] .ds-action.btn-outline-success {
    color: #86efac !important;
}

html[data-color-mode="dark"] .ds-action:hover {
    background: #20364e !important;
    border-color: #496785 !important;
}


/* =========================================================
   RESPUESTA DE SUNAT
   ========================================================= */

html[data-color-mode="dark"] .ds-detail-row td {
    background: #111f32 !important;
    border-color: #29445f !important;
}

html[data-color-mode="dark"] .ds-detail-box {
    background: #17283d !important;
    border-color: #29445f !important;
    color: #dce8f4 !important;
}

html[data-color-mode="dark"] .ds-response {
    color: #c7d7e7 !important;
}

html[data-color-mode="dark"] .ds-response strong {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .ds-response > .d-flex > i {
    color: #93c5fd !important;
}

html[data-color-mode="dark"] .ds-response .text-muted {
    color: #8fa7bd !important;
}


/* Código SUNAT */
html[data-color-mode="dark"] .ds-response .badge.text-bg-light {
    background: #20364e !important;
    border-color: #36516d !important;
    color: #c7d7e7 !important;
}


/* =========================================================
   BOLETAS INCLUIDAS
   ========================================================= */

html[data-color-mode="dark"] .ds-included-title {
    color: #dce8f4 !important;
}

html[data-color-mode="dark"] .ds-included-title i {
    color: #93c5fd !important;
}

html[data-color-mode="dark"] .ds-document {
    background: #111f32 !important;
    border-color: #36516d !important;
    color: #dce8f4 !important;
}

html[data-color-mode="dark"] .ds-document span {
    color: #7892aa !important;
}


/* =========================================================
   FOOTER
   ========================================================= */

html[data-color-mode="dark"] .ds-footer {
    background: #17283d !important;
    border-color: #29445f !important;
}


/* Inputs de esta pantalla */
html[data-color-mode="dark"] .ds-create-card .form-control,
html[data-color-mode="dark"] .ds-create-card .form-select {
    background-color: #111f32 !important;
    border-color: #36516d !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .ds-create-card .form-control:focus,
html[data-color-mode="dark"] .ds-create-card .form-select:focus {
    background-color: #111f32 !important;
    color: #ffffff !important;
    border-color: var(--primary) !important;
}


/* Evitar textos negros heredados */
html[data-color-mode="dark"] .ds-table-card .text-dark {
    color: #ffffff !important;
}

</style>

@endsection
