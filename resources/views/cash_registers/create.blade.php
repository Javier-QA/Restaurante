@extends('layouts.app')

@section('content')
@php
    $currency = \App\Models\Setting::where('key','currency_symbol')->value('value') ?? 'S/';
@endphp

<style>
    .cash-open-page {
        width: 100%;
    }

    .cash-open-header {
        margin-bottom: 24px;
    }

    .cash-open-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 4px;
    }

    .cash-open-title i {
        color:var(--text-main);
    }

    .cash-open-subtitle {
        color: var(--text-muted);
        margin: 0;
        font-size: .9rem;
    }

    .cash-open-panel {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft, #e5e7eb);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .cash-open-panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-soft);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .cash-open-header-icon {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--primary) 11%, white);
        color: var(--primary);
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .cash-open-panel-title {
        color: var(--text-main);
        font-size: 1rem;
        font-weight: 800;
        margin: 0 0 2px;
    }

    .cash-open-panel-subtitle {
        color: var(--text-muted);
        font-size: .78rem;
        margin: 0;
    }

    .cash-open-body {
        padding: 28px;
    }

    .cash-info-box {
        display: flex;
        gap: 13px;
        padding: 16px;
        margin-bottom: 25px;
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary) 6%, white);
        border: 1px solid color-mix(in srgb, var(--primary) 17%, white);
    }

    .cash-info-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: color-mix(in srgb, var(--primary) 12%, white);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cash-info-title {
        color: var(--text-main);
        font-size: .84rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .cash-info-text {
        color: var(--text-muted);
        font-size: .76rem;
        line-height: 1.45;
    }

    .cash-open-page .form-label {
        color: var(--text-main);
        font-size: .82rem;
        font-weight: 700;
    }

    .opening-input {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .05);
    }

    .opening-input .input-group-text {
        background: var(--light-bg, #f8fafc);
        border-color: var(--border-soft);
        color: var(--text-main);
        font-size: 1.1rem;
        font-weight: 800;
        padding-left: 18px;
        padding-right: 18px;
    }

    .opening-input .form-control {
        border-color: var(--border-soft);
        border-radius: 0;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        min-height: 58px;
    }

    .opening-input .form-control:focus {
        border-color: var(--primary);
        box-shadow: none;
    }

    .cash-opening-help {
        color: var(--text-muted);
        font-size: .76rem;
        line-height: 1.5;
        margin-top: 9px;
    }

    .cash-opening-help i {
        color: var(--primary);
    }

    .btn-open-register {
        min-height: 50px;
        border-radius: 12px;
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        font-weight: 700;
    }

    .btn-open-register:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #fff;
    }

    .btn-cancel-open {
        min-height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    @media (max-width: 767.98px) {
        .cash-open-body {
            padding: 20px;
        }

        .cash-open-panel-header {
            padding: 18px 20px;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="cash-open-page">

        <div class="cash-open-header">
            <h2 class="cash-open-title">
                <i class="bi bi-box-arrow-in-right me-2"></i>Apertura de Caja
            </h2>

            <p class="cash-open-subtitle">
                Registra el fondo inicial para comenzar las operaciones del turno.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="cash-open-panel">

                    <div class="cash-open-panel-header">
                        <div class="cash-open-header-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>

                        <div>
                            <h5 class="cash-open-panel-title">
                                Fondo inicial de caja
                            </h5>
                            <p class="cash-open-panel-subtitle">
                                Dinero disponible al iniciar el turno
                            </p>
                        </div>
                    </div>

                    <div class="cash-open-body">

                        @if(session('warning'))
                            <div class="alert alert-warning border-0 mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ session('warning') }}
                            </div>
                        @endif

                        <div class="cash-info-box">
                            <div class="cash-info-icon">
                                <i class="bi bi-info-circle"></i>
                            </div>

                            <div>
                                <div class="cash-info-title">
                                    ¿Qué debes registrar?
                                </div>

                                <div class="cash-info-text">
                                    Ingresa únicamente el dinero físico disponible
                                    en caja al comenzar el turno, incluyendo billetes
                                    y monedas destinados para dar vuelto.
                                </div>
                            </div>
                        </div>

                        <form id="cashOpenForm"
                              action="{{ route('cash_registers.store') }}"
                              method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label">
                                    Monto inicial
                                </label>

                                <div class="input-group opening-input">
                                    <span class="input-group-text">
                                        {{ $currency }}
                                    </span>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="opening_amount"
                                        value="{{ old('opening_amount', '0.00') }}"
                                        class="form-control"
                                        required
                                        autofocus
                                    >
                                </div>

                                @error('opening_amount')
                                    <div class="text-danger small mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="cash-opening-help">
                                    <i class="bi bi-wallet2 me-1"></i>
                                    Este monto será utilizado posteriormente para
                                    calcular el efectivo esperado durante el cierre.
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">

                                <button type="button"
                                        id="openCashConfirmation"
                                        class="btn btn-open-register">
                                    <i class="bi bi-unlock me-2"></i>
                                    Abrir Turno de Caja
                                </button>

                                <a href="{{ route('dashboard') }}"
                                   class="btn btn-light border btn-cancel-open">
                                    Cancelar y volver
                                </a>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>


<!-- Confirmación de apertura -->
<div class="modal fade" id="cashOpenConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">

        <div class="modal-content border-0 shadow-lg"
             style="border-radius:var(--radius-md, 14px); overflow:hidden;">

            <div class="modal-header border-0"
                 style="background:var(--primary, #ff8c00); color:#fff;">

                <h6 class="modal-title fw-bold">
                    <i class="bi bi-unlock me-2"></i>
                    Confirmar apertura
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
                    ¿Abrir turno de caja?
                </div>

                <div class="small text-muted mb-3">
                    Verifica que el fondo inicial registrado sea correcto.
                </div>

                <div class="p-3 rounded border"
                     style="background:var(--light-bg, #f8fafc);">

                    <div class="small text-muted mb-1">
                        Fondo inicial
                    </div>

                    <div class="fw-bold fs-5" id="cashOpenAmountPreview">
                        {{ $currency }} 0.00
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
                        id="confirmCashOpen"
                        class="btn flex-fill fw-bold text-white"
                        style="background:var(--primary, #ff8c00); border-color:var(--primary, #ff8c00);">
                    Sí, abrir caja
                </button>

            </div>

        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('cashOpenForm');
    const openButton = document.getElementById('openCashConfirmation');
    const confirmButton = document.getElementById('confirmCashOpen');
    const amountInput = form
        ? form.querySelector('[name="opening_amount"]')
        : null;

    const amountPreview =
        document.getElementById('cashOpenAmountPreview');

    const modalElement =
        document.getElementById('cashOpenConfirmModal');

    if (!form || !openButton || !confirmButton ||
        !amountInput || !modalElement) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);

    openButton.addEventListener('click', function () {

        if (!form.reportValidity()) {
            return;
        }

        const amount = parseFloat(amountInput.value || 0);

        amountPreview.textContent =
            @json($currency) + ' ' +
            amount.toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        modal.show();
    });

    confirmButton.addEventListener('click', function () {

        confirmButton.disabled = true;

        confirmButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Abriendo...';

        form.submit();
    });

});
</script>

@endsection