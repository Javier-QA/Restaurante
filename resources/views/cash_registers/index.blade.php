@extends('layouts.app')

@section('content')
<style>
    .history-title {
        color: var(--text-main);
        font-weight: 800;
        font-size: 1.65rem;
    }

    .history-subtitle {
        color: var(--text-muted);
        font-size: .9rem;
    }

    .history-kpi {
        position: relative;
        height: 100%;
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px 22px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .history-kpi::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(
            180deg,
            var(--primary) 0%,
            color-mix(in srgb, var(--primary) 45%, white) 55%,
            #ffffff 100%
        );
        border-radius: 16px 0 0 16px;
    }

    .history-kpi-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .history-kpi-icon {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--primary) 10%, white);
        color: var(--primary);
        font-size: 1.25rem;
    }

    .history-kpi-label {
        color: var(--text-muted);
        font-size: .73rem;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .history-kpi-value {
        color: var(--text-main);
        font-size: 1.45rem;
        line-height: 1.1;
        font-weight: 800;
    }

    .history-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .history-card-header {
        padding: 19px 22px;
        border-bottom: 1px solid var(--border-soft);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .history-card-title {
        color: var(--text-main);
        font-size: .98rem;
        font-weight: 800;
        margin: 0 0 2px;
    }

    .history-card-subtitle {
        color: var(--text-muted);
        font-size: .76rem;
        margin: 0;
    }

    .history-table {
        margin: 0;
    }

    .history-table thead th {
        padding: 13px 16px;
        background: var(--light-bg, #f8fafc);
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-soft);
        font-size: .68rem;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .history-table tbody td {
        padding: 15px 16px;
        border-bottom-color: var(--border-soft);
        color: var(--text-main);
        vertical-align: middle;
        white-space: nowrap;
    }

    .history-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .turn-id {
        color: var(--primary);
        font-weight: 800;
    }

    .cashier-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: color-mix(in srgb, var(--primary) 12%, white);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .history-date {
        font-size: .82rem;
        font-weight: 700;
    }

    .history-time {
        display: block;
        color: var(--text-muted);
        font-size: .71rem;
        margin-top: 2px;
    }

    .money-value {
        font-size: .82rem;
        font-weight: 800;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
    }

    .status-open {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-closed {
        background: #f1f5f9;
        color: #475569;
    }

    .difference-exact {
        background: #f0fdf4;
        color: #15803d;
    }

    .difference-surplus {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .difference-shortage {
        background: #fff1f2;
        color: #dc2626;
    }

    .history-action {
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .history-action-view {
        background: color-mix(in srgb, var(--primary) 10%, white);
        border: 1px solid color-mix(in srgb, var(--primary) 22%, white);
        color: var(--primary);
    }

    .history-action-view:hover {
        background: var(--primary);
        color: #fff;
    }

    .history-action-pdf {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #dc2626;
    }

    .history-action-pdf:hover {
        background: #dc2626;
        color: #fff;
    }

    .history-action-delete {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #dc2626;
        cursor: pointer;
    }

    .history-action-delete:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }

    .history-empty {
        padding: 55px 20px !important;
        text-align: center;
        color: var(--text-muted) !important;
    }

    .history-empty i {
        font-size: 2.4rem;
        display: block;
        margin-bottom: 10px;
        color: var(--primary);
    }

    .history-pagination {
        padding: 16px 22px;
        border-top: 1px solid var(--border-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        flex-wrap: wrap;
        background: var(--card-bg, #fff);
    }

    .history-pagination-info {
        color: var(--text-muted);
        font-size: .82rem;
    }

    .history-pagination-info strong {
        color: var(--text-main);
        font-weight: 700;
    }

    .history-pages {
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .history-pages li a,
    .history-pages li span {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: -1px;
        border: 1px solid var(--border-soft);
        background: #ffffff;
        color: var(--primary);
        text-decoration: none;
        font-size: .82rem;
        font-weight: 600;
        transition: .2s ease;
    }

    .history-pages li:first-child a,
    .history-pages li:first-child span {
        margin-left: 0;
        border-radius: 8px 0 0 8px;
    }

    .history-pages li:last-child a,
    .history-pages li:last-child span {
        border-radius: 0 8px 8px 0;
    }

    .history-pages li.active span {
        position: relative;
        z-index: 1;
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .history-pages li:not(.active):not(.disabled) a:hover {
        position: relative;
        z-index: 1;
        background: color-mix(in srgb, var(--primary) 8%, white);
        border-color: var(--primary);
    }

    .history-pages li.disabled span {
        color: #94a3b8;
        background: #ffffff;
        cursor: default;
    }

    .history-pages i {
        font-size: .72rem;
    }

    @media (max-width: 767px) {
        .history-pagination {
            justify-content: center;
        }

        .history-pagination-info {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
        <div>
            <h2 class="history-title mb-1">
                <i class="bi bi-clock-history me-2" style="color:var(--text-main);"></i>
                Historial de Turnos de Caja
            </h2>

            <p class="history-subtitle mb-0">
                Consulta los turnos de caja, resultados del arqueo y reportes de cierre.
            </p>
        </div>

        @if($stats['total'] > 0 && auth()->user()->role === 'admin')
            <button type="button"
                    class="btn btn-danger fw-semibold px-3 py-2"
                    style="border-radius:10px;" onclick="confirmDeleteAllCashRegisters()">
                <i class="bi bi-trash3 me-2"></i>
                Eliminar historial
            </button>
        @endif
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="history-kpi">
                <div class="history-kpi-content">
                    <div class="history-kpi-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="history-kpi-label">TOTAL DE TURNOS</div>
                        <div class="history-kpi-value">
                            {{ $stats['total'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="history-kpi">
                <div class="history-kpi-content">
                    <div class="history-kpi-icon">
                        <i class="bi bi-door-open"></i>
                    </div>
                    <div>
                        <div class="history-kpi-label">TURNOS ABIERTOS</div>
                        <div class="history-kpi-value">
                            {{ $stats['open'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="history-kpi">
                <div class="history-kpi-content">
                    <div class="history-kpi-icon">
                        <i class="bi bi-door-closed"></i>
                    </div>
                    <div>
                        <div class="history-kpi-label">TURNOS CERRADOS</div>
                        <div class="history-kpi-value">
                            {{ $stats['closed'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="history-kpi">
                <div class="history-kpi-content">
                    <div class="history-kpi-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div>
                        <div class="history-kpi-label">CON DIFERENCIAS</div>
                        <div class="history-kpi-value">
                            {{ $stats['differences'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="history-card">

        <div class="history-card-header">
            <div>
                <h5 class="history-card-title">
                    Registro de turnos
                </h5>
                <p class="history-card-subtitle">
                    Se muestran primero los turnos más recientes.
                </p>
            </div>

            <div class="text-muted small">
                {{ $registers->total() }}
                {{ $registers->total() === 1 ? 'registro' : 'registros' }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover history-table">

                <thead>
                    <tr>
                        <th>Turno</th>
                        <th>Cajero</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th>Esperado</th>
                        <th>Contado</th>
                        <th>Diferencia</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($registers as $reg)

                        @php
                            $regDifference = $reg->difference !== null
                                ? (float) $reg->difference
                                : null;
                        @endphp

                        <tr>

                            <td>
                                <span class="turn-id">
                                    #{{ $reg->id }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cashier-avatar">
                                        {{ strtoupper(substr($reg->user->name ?? 'U', 0, 1)) }}
                                    </div>

                                    <span class="fw-semibold small">
                                        {{ $reg->user->name ?? 'No disponible' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span class="history-date">
                                    {{ $reg->opening_time->format('d/m/Y') }}
                                </span>
                                <span class="history-time">
                                    {{ $reg->opening_time->format('h:i A') }}
                                </span>
                            </td>

                            <td>
                                @if($reg->closing_time)
                                    <span class="history-date">
                                        {{ $reg->closing_time->format('d/m/Y') }}
                                    </span>
                                    <span class="history-time">
                                        {{ $reg->closing_time->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            <td>
                                <span class="money-value">
                                    {{ $currency }}
                                    {{ number_format((float) ($reg->expected_amount ?? $reg->opening_amount), 2) }}
                                </span>
                            </td>

                            <td>
                                @if($reg->status === 'closed' && $reg->closing_amount !== null)
                                    <span class="money-value">
                                        {{ $currency }}
                                        {{ number_format((float) $reg->closing_amount, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            <td>
                                @if($reg->status === 'closed' && $regDifference !== null)

                                    @if(abs($regDifference) < 0.005)
                                        <span class="status-pill difference-exact">
                                            {{ $currency }} 0.00 · Exacta
                                        </span>

                                    @elseif($regDifference > 0)
                                        <span class="status-pill difference-surplus">
                                            + {{ $currency }} {{ number_format(abs($regDifference), 2) }} · Sobrante
                                        </span>

                                    @else
                                        <span class="status-pill difference-shortage">
                                            - {{ $currency }} {{ number_format(abs($regDifference), 2) }} · Faltante
                                        </span>
                                    @endif

                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            <td>
                                @if($reg->status === 'open')
                                    <span class="status-pill status-open">
                                        <i class="bi bi-door-open me-1"></i>
                                        Abierta
                                    </span>
                                @else
                                    <span class="status-pill status-closed">
                                        <i class="bi bi-door-closed me-1"></i>
                                        Cerrada
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('cash_registers.show', $reg) }}"
                                       class="history-action history-action-view"
                                       title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if($reg->status === 'closed')
                                        <a href="{{ route('cash_registers.pdf', $reg) }}"
                                           target="_blank"
                                           class="history-action history-action-pdf"
                                           title="Imprimir PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>

                                        <button type="button"
                                                class="history-action history-action-delete"
                                                title="Eliminar turno" onclick="confirmDeleteCashRegister(this)"
                                                data-id="{{ $reg->id }}"
                                                data-action="{{ route('cash_registers.destroy', $reg) }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="history-empty">
                                <i class="bi bi-inbox"></i>
                                No hay turnos de caja registrados.
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>
        </div>
        <x-system-pagination :paginator="$registers" />


    </div>

</div>





<form id="deleteAllCashRegistersForm" action="{{ route('cash_registers.destroy_all') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
<script id="cash-register-system-confirmations">
function confirmDeleteAllCashRegisters() {
    const form = document.getElementById('deleteAllCashRegistersForm');
    if (!form) return;
    SystemNotify.confirm({
        type: 'danger', title: 'Eliminar historial de caja',
        text: 'Se eliminarán los turnos de caja. Las ventas y gastos se conservarán. Esta acción no se puede deshacer.',
        confirmText: 'Eliminar todo', icon: 'bi-trash3', onConfirm: function () { form.submit(); }
    });
}
function confirmDeleteCashRegister(button) {
    const action = button.dataset.action;
    const id = button.dataset.id;

    const form = document.getElementById('deleteCashRegisterForm');

    if (!form || !action) return;

    form.action = action;

    SystemNotify.confirm({
        type: 'danger',
        title: 'Eliminar turno',
        text: 'Se eliminará el turno #' + id + ' del historial. Las ventas y gastos registrados se conservarán.',
        confirmText: 'Eliminar turno',
        icon: 'bi-trash3',
        onConfirm: function () {
            form.submit();
        }
    });
}
</script>



<style>
/* DARK MODE - REGISTRO DE TURNOS COMPONENTES */

/* ==========================================================
   AVATAR DEL CAJERO
   ========================================================== */

html[data-color-mode="dark"] .cashier-avatar {
    background: color-mix(in srgb, var(--primary) 16%, #132338) !important;
    border: 1px solid color-mix(in srgb, var(--primary) 30%, #30465d) !important;
    color: var(--primary) !important;
    box-shadow: none !important;
}


/* ==========================================================
   ESTADO - CAJA ABIERTA
   ========================================================== */

html[data-color-mode="dark"] .status-pill.status-open {
    background: color-mix(in srgb, var(--primary) 14%, #132338) !important;
    border: 1px solid color-mix(in srgb, var(--primary) 30%, #30465d) !important;
    color: var(--primary) !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .status-pill.status-open i {
    color: var(--primary) !important;
    -webkit-text-fill-color: var(--primary) !important;
}


/* ==========================================================
   ACCIÓN - VER DETALLE
   ========================================================== */

html[data-color-mode="dark"] .history-action.history-action-view {
    background: color-mix(in srgb, var(--primary) 14%, #132338) !important;
    border: 1px solid color-mix(in srgb, var(--primary) 30%, #30465d) !important;
    color: var(--primary) !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .history-action.history-action-view i {
    color: var(--primary) !important;
    -webkit-text-fill-color: var(--primary) !important;
}

html[data-color-mode="dark"] .history-action.history-action-view:hover {
    background: color-mix(in srgb, var(--primary) 23%, #132338) !important;
    border-color: color-mix(in srgb, var(--primary) 48%, #30465d) !important;
    color: var(--primary) !important;
    transform: translateY(-1px);
}

</style>


<form id="deleteCashRegisterForm"
      method="POST"
      style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection