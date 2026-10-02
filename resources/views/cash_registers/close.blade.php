@extends('layouts.app')

@section('content')
@php
    $currency = \App\Models\Setting::where('key','currency_symbol')->value('value') ?? 'S/';
@endphp

<style>
    .cash-close-page {
        width: 100%;
    }

    .cash-close-content {
        max-width: 1180px;
        margin: 0 auto;
    }

    .cash-close-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .cash-close-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 4px;
    }

    .cash-close-title i {
        color:var(--text-main);
    }

    .cash-close-subtitle {
        color: var(--text-muted);
        margin: 0;
        font-size: .9rem;
    }

    .cash-turn-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 13px;
        border-radius: 20px;
        background: color-mix(in srgb, var(--accent-2, #16a34a) 10%, white);
        color: var(--accent-2, #16803d);
        font-size: .75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .cash-turn-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    .cash-kpi {
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

    .cash-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 9px 24px rgba(15, 23, 42, .11);
    }

    .cash-kpi::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 5px;
        height: 100%;
    }

    .cash-kpi.initial::before {
        background: linear-gradient(180deg, #2563eb, #93c5fd, #fff);
    }

    .cash-kpi.cash::before {
        background: linear-gradient(180deg, #16a34a, #86efac, #fff);
    }

    .cash-kpi.digital::before {
        background: linear-gradient(180deg, #7c3aed, #c4b5fd, #fff);
    }

    .cash-kpi.expense::before {
        background: linear-gradient(180deg, #ea580c, #fdba74, #fff);
    }

    .cash-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .initial .cash-kpi-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .cash .cash-kpi-icon {
        background: #f0fdf4;
        color: #16a34a;
    }

    .digital .cash-kpi-icon {
        background: #faf5ff;
        color: #7c3aed;
    }

    .expense .cash-kpi-icon {
        background: #fff7ed;
        color: #ea580c;
    }

    .cash-kpi-label {
        display: block;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 3px;
    }

    .cash-kpi-value {
        color: var(--text-main);
        font-size: 1.25rem;
        line-height: 1.1;
        font-weight: 800;
    }

    .cash-panel {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        height: 100%;
    }

    .cash-panel-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--border-soft);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cash-panel-header i {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: color-mix(in srgb, var(--primary) 10%, white);
        color: var(--primary);
        font-size: 1.05rem;
    }

    .cash-panel-title {
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        font-size: 1rem;
    }

    .cash-panel-body {
        padding: 22px;
    }

    .payment-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 13px 0;
        border-bottom: 1px solid var(--border-soft);
    }

    .payment-row:last-child {
        border-bottom: 0;
    }

    .payment-name {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-muted);
        font-size: .88rem;
        font-weight: 600;
    }

    .payment-name i {
        color: var(--primary);
        font-size: 1rem;
    }

    .payment-value {
        color: var(--text-main);
        font-weight: 800;
    }

    .payment-total {
        margin-top: 14px;
        padding: 15px 16px;
        background: var(--light-bg, #f8fafc);
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .payment-total span:first-child {
        color: var(--text-muted);
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .payment-total strong {
        color: var(--text-main);
        font-size: 1.15rem;
    }

    .expected-box {
        padding: 22px;
        border-radius: 16px;
        background: color-mix(in srgb, var(--primary) 7%, white);
        border: 1px solid color-mix(in srgb, var(--primary) 20%, white);
        text-align: center;
        margin-bottom: 22px;
    }

    .expected-label {
        display: block;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        margin-bottom: 5px;
    }

    .expected-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .expected-help {
        color: var(--text-muted);
        font-size: .75rem;
        margin-top: 7px;
    }

    .cash-close-page .form-label {
        color: var(--text-main);
        font-weight: 700;
        font-size: .82rem;
    }

    .cash-close-page .form-control,
    .cash-close-page .input-group-text {
        border-color: var(--border-soft);
    }

    .cash-close-page .form-control {
        border-radius: 12px;
    }

    .cash-close-page textarea.form-control {
        resize: vertical;
    }

    .closing-input {
        border-radius: 12px;
        overflow: hidden;
    }

    .closing-input .input-group-text {
        background: var(--light-bg, #f8fafc);
        font-weight: 800;
        color: var(--text-main);
    }

    .closing-input .form-control {
        border-radius: 0;
        font-size: 1.15rem;
        font-weight: 700;
    }

    .cash-help {
        color: var(--text-muted);
        font-size: .76rem;
        line-height: 1.45;
    }

    .btn-close-register {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        border-radius: 12px;
        min-height: 48px;
        font-weight: 700;
    }

    .btn-close-register:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #fff;
    }

    .btn-cancel-register {
        border-radius: 12px;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    @media (max-width: 767.98px) {
        .cash-close-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .cash-panel-body {
            padding: 18px;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="cash-close-page">

        <div class="cash-close-header">
            <div>
                <h2 class="cash-close-title">
                    <i class="bi bi-cash-stack me-2"></i>Cierre de Caja
                </h2>
                <p class="cash-close-subtitle">
                    Revisa el resumen del turno y realiza el arqueo del efectivo.
                </p>
            </div>

            <div class="cash-turn-badge">
                Caja abierta desde {{ $cashRegister->opening_time->format('h:i A') }}
            </div>
        </div>

        <div class="row g-3 mb-4">

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-kpi initial">
                    <div class="cash-kpi-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <span class="cash-kpi-label">Monto inicial</span>
                        <div class="cash-kpi-value">
                            {{ $currency }} {{ number_format($cashRegister->opening_amount, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-kpi cash">
                    <div class="cash-kpi-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <span class="cash-kpi-label">Ventas en efectivo</span>
                        <div class="cash-kpi-value">
                            {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-kpi digital">
                    <div class="cash-kpi-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <div>
                        <span class="cash-kpi-label">Ventas digitales</span>
                        <div class="cash-kpi-value">
                            {{ $currency }} {{ number_format($totalSalesCard + $totalSalesYape + $totalSalesPlin, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cash-kpi expense">
                    <div class="cash-kpi-icon">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                    <div>
                        <span class="cash-kpi-label">Gastos del turno</span>
                        <div class="cash-kpi-value">
                            {{ $currency }} {{ number_format($totalExpenses, 2) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">

            <div class="col-lg-6">
                <div class="cash-panel">
                    <div class="cash-panel-header">
                        <i class="bi bi-bar-chart"></i>
                        <div>
                            <h5 class="cash-panel-title">Resumen de ventas</h5>
                            <div class="small text-muted">Detalle por método de pago</div>
                        </div>
                    </div>

                    <div class="cash-panel-body">

                        <div class="payment-row">
                            <div class="payment-name">
                                <i class="bi bi-cash"></i>
                                Efectivo
                            </div>
                            <div class="payment-value">
                                {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                            </div>
                        </div>

                        <div class="payment-row">
                            <div class="payment-name">
                                <i class="bi bi-credit-card"></i>
                                Tarjeta
                            </div>
                            <div class="payment-value">
                                {{ $currency }} {{ number_format($totalSalesCard, 2) }}
                            </div>
                        </div>

                        <div class="payment-row">
                            <div class="payment-name">
                                <i class="bi bi-phone"></i>
                                Yape
                            </div>
                            <div class="payment-value">
                                {{ $currency }} {{ number_format($totalSalesYape, 2) }}
                            </div>
                        </div>

                        <div class="payment-row">
                            <div class="payment-name">
                                <i class="bi bi-phone"></i>
                                Plin
                            </div>
                            <div class="payment-value">
                                {{ $currency }} {{ number_format($totalSalesPlin, 2) }}
                            </div>
                        </div>

                        <div class="payment-total">
                            <span>Total vendido</span>
                            <strong>{{ $currency }} {{ number_format($totalSales, 2) }}</strong>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="cash-panel">
                    <div class="cash-panel-header">
                        <i class="bi bi-calculator"></i>
                        <div>
                            <h5 class="cash-panel-title">Arqueo de caja</h5>
                            <div class="small text-muted">Verificación del efectivo físico</div>
                        </div>
                    </div>

                    <div class="cash-panel-body">

                        <div class="expected-box">
                            <span class="expected-label">Efectivo esperado en caja</span>

                            <div class="expected-value">
                                {{ $currency }} {{ number_format($expectedAmount, 2) }}
                            </div>

                            <div class="expected-help">
                                Monto inicial + ventas en efectivo − gastos
                            </div>
                        </div>

                        <form id="cashCloseForm" action="{{ route('cash_registers.processClose') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label">
                                    Dinero físico contado
                                </label>

                                <div class="input-group closing-input">
                                    <span class="input-group-text">{{ $currency }}</span>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="closing_amount"
                                        value="{{ old('closing_amount') }}"
                                        class="form-control"
                                        placeholder="0.00"
                                        required
                                        autofocus
                                    >
                                </div>

                                @error('closing_amount')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                                <div class="cash-help mt-2">
                                    Cuenta únicamente el efectivo físico disponible en caja.
                                    Yape, Plin y Tarjeta no forman parte de este arqueo.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    Observaciones
                                    <span class="text-muted fw-normal">(Opcional)</span>
                                </label>

                                <textarea
                                    name="notes"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Registra alguna observación sobre el cierre de caja..."
                                >{{ old('notes') }}</textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" id="openCashCloseConfirmation" class="btn btn-close-register">
                                    <i class="bi bi-lock me-2"></i>
                                    Cerrar Caja
                                </button>

                                <a href="{{ route('dashboard') }}"
                                   class="btn btn-light border btn-cancel-register">
                                    Cancelar
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Confirmación de cierre de caja -->
<div class="modal fade" id="cashCloseConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg"
             style="border-radius:var(--radius-md, 14px); overflow:hidden;">

            <div class="modal-header border-0"
                 style="background:var(--primary, #ff8c00); color:#fff;">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-lock me-2"></i>
                    Confirmar cierre
                </h6>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center px-4 py-4">

                <div class="mb-3">
                    <i class="bi bi-cash-stack"
                       style="font-size:2.8rem; color:var(--primary, #ff8c00);"></i>
                </div>

                <div class="fw-bold mb-2">
                    ¿Cerrar caja?
                </div>

                <div class="small text-muted mb-3">
                    Verifica que el dinero físico ingresado sea correcto.
                    Al confirmar se registrará el arqueo y la diferencia encontrada.
                </div>

                <div class="p-3 rounded border"
                     style="background:var(--light-bg, #f8fafc);">
                    <div class="small text-muted mb-1">
                        Efectivo contado
                    </div>

                    <div class="fw-bold fs-5" id="cashCloseAmountPreview">
                        {{ $currency }} 0.00
                    </div>
                </div>

                <div id="cashCloseDifferenceBox"
                     class="p-3 rounded border mt-3">
                    <div class="small mb-1" id="cashCloseDifferenceLabel">
                        Diferencia
                    </div>

                    <div class="fw-bold fs-5" id="cashCloseDifference">
                        {{ $currency }} 0.00
                    </div>

                    <div class="small mt-1" id="cashCloseDifferenceStatus">
                        Caja exacta
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button"
                        class="btn btn-light border flex-fill"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button"
                        id="confirmCashClose"
                        class="btn flex-fill fw-bold text-white"
                        style="background:var(--primary, #ff8c00); border-color:var(--primary, #ff8c00);">
                    Sí, cerrar caja
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('cashCloseForm');
    const openButton = document.getElementById('openCashCloseConfirmation');
    const confirmButton = document.getElementById('confirmCashClose');
    const amountInput = form ? form.querySelector('[name="closing_amount"]') : null;
    const amountPreview = document.getElementById('cashCloseAmountPreview');
    const differenceBox = document.getElementById('cashCloseDifferenceBox');
    const differenceLabel = document.getElementById('cashCloseDifferenceLabel');
    const differenceValue = document.getElementById('cashCloseDifference');
    const differenceStatus = document.getElementById('cashCloseDifferenceStatus');
    const modalElement = document.getElementById('cashCloseConfirmModal');
    const expectedAmount = Number(@json((float) $expectedAmount));

    if (!form || !openButton || !confirmButton || !amountInput || !modalElement) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);

    openButton.addEventListener('click', function () {
        if (!form.reportValidity()) {
            return;
        }

        const amount = parseFloat(amountInput.value || 0);

        const difference = amount - expectedAmount;
        const absoluteDifference = Math.abs(difference);

        const formatMoney = function (value) {
            return @json($currency) + ' ' +
                value.toLocaleString('es-PE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
        };

        amountPreview.textContent = formatMoney(amount);
        differenceValue.textContent = formatMoney(absoluteDifference);

        if (Math.abs(difference) < 0.005) {
            differenceLabel.textContent = 'Diferencia';
            differenceStatus.textContent = 'Caja exacta';

            differenceBox.style.background = '#f0fdf4';
            differenceBox.style.borderColor = '#86efac';
            differenceLabel.style.color = '#15803d';
            differenceValue.style.color = '#15803d';
            differenceStatus.style.color = '#15803d';

        } else if (difference > 0) {
            differenceLabel.textContent = 'Sobrante';
            differenceStatus.textContent = 'Hay más efectivo del esperado';

            differenceBox.style.background = '#eff6ff';
            differenceBox.style.borderColor = '#93c5fd';
            differenceLabel.style.color = '#1d4ed8';
            differenceValue.style.color = '#1d4ed8';
            differenceStatus.style.color = '#1d4ed8';

        } else {
            differenceLabel.textContent = 'Faltante';
            differenceStatus.textContent = 'Hay menos efectivo del esperado';

            differenceBox.style.background = '#fff1f2';
            differenceBox.style.borderColor = '#fca5a5';
            differenceLabel.style.color = '#dc2626';
            differenceValue.style.color = '#dc2626';
            differenceStatus.style.color = '#dc2626';
        }

        modal.show();
    });

    confirmButton.addEventListener('click', function () {
        confirmButton.disabled = true;
        confirmButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Cerrando...';

        form.submit();
    });
});
</script>

<style>
/* DARK MODE - CIERRE DE CAJA */

html[data-color-mode="dark"] .expected-box {
    background: #15263a !important;
    border: 1px solid #304860 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .expected-box .expected-label {
    color: #a9bdd0 !important;
}

html[data-color-mode="dark"] .expected-box .expected-value {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .expected-box .expected-help {
    color: #9fb4c9 !important;
}

</style>

<style>
/* DARK MODE - CIERRE CAJA DETALLES FINALES */

/* ==========================================================
   RESUMEN DE VENTAS + ARQUEO DE CAJA
   ========================================================== */

html[data-color-mode="dark"] .cash-panel-header > i {
    background: rgba(249, 115, 22, .14) !important;
    border: 1px solid rgba(249, 115, 22, .25) !important;
    color: #fb923c !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .cash-panel-header > i::before {
    color: #fb923c !important;
    -webkit-text-fill-color: #fb923c !important;
}


/* ==========================================================
   CAJA ABIERTA
   ========================================================== */

html[data-color-mode="dark"] .cash-turn-badge {
    background: rgba(34, 197, 94, .12) !important;
    border: 1px solid rgba(34, 197, 94, .28) !important;
    color: #4ade80 !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .cash-turn-badge::before {
    background: #22c55e !important;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, .12) !important;
}


/* Texto secundario de los paneles */
html[data-color-mode="dark"] .cash-panel-header .text-muted {
    color: #9fb2c6 !important;
}

</style>

@endsection