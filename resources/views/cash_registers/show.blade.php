@extends('layouts.app')

@section('content')
<style>
    .turn-detail-title {
        color: var(--text-main);
        font-weight: 800;
    }

    .turn-detail-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .turn-detail-card .card-header {
        background: var(--card-bg, #fff) !important;
        border-bottom: 1px solid var(--border-soft);
        padding: 18px 22px;
    }

    .turn-section-title {
        color: var(--text-main);
        font-weight: 800;
        font-size: .95rem;
        margin: 0;
    }

    .turn-info-label {
        color: var(--text-muted);
        font-size: .74rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .turn-info-value {
        color: var(--text-main);
        font-size: .94rem;
        font-weight: 700;
    }

    .turn-kpi {
        height: 100%;
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 18px;
        position: relative;
        overflow: hidden;
    }

    .turn-kpi::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--primary);
    }

    .turn-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--primary) 10%, var(--card-bg, white));
        color: var(--primary);
        font-size: 1.15rem;
        margin-bottom: 13px;
    }

    .turn-kpi-label {
        color: var(--text-muted);
        font-size: .74rem;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .turn-kpi-value {
        color: var(--text-main);
        font-size: 1.2rem;
        font-weight: 800;
    }

    .payment-row,
    .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 0;
        border-bottom: 1px solid var(--border-soft);
    }

    .payment-row:last-child,
    .summary-row:last-child {
        border-bottom: 0;
    }

    .payment-name {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-main);
        font-weight: 600;
    }

    .payment-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--light-bg, #f8fafc);
        color: var(--primary);
    }

    .payment-amount {
        color: var(--text-main);
        font-weight: 800;
    }

    .total-sales-box {
        background: color-mix(in srgb, var(--primary) 7%, var(--card-bg, white));
        border: 1px solid color-mix(in srgb, var(--primary) 20%, var(--card-bg, white));
        border-radius: 12px;
        padding: 15px 16px;
        margin-top: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-sales-box strong {
        color: var(--primary);
        font-size: 1.08rem;
    }

    .difference-box {
        border-radius: 13px;
        padding: 18px;
        text-align: center;
    }

    .difference-exact {
        background: var(--surface-green, #f0fdf4);
        border: 1px solid var(--surface-green, #86efac);
        color: var(--ink-green, #15803d);
    }

    .difference-surplus {
        background: var(--surface-blue, #eff6ff);
        border: 1px solid #93c5fd;
        color: var(--ink-blue, #1d4ed8);
    }

    .difference-shortage {
        background: var(--surface-red, #fff1f2);
        border: 1px solid #fca5a5;
        color: var(--ink-red, #dc2626);
    }

    .expense-table th {
        color: var(--text-muted);
        font-size: .72rem;
        text-transform: uppercase;
        border-bottom-color: var(--border-soft);
    }

    .expense-table td {
        color: var(--text-main);
        border-bottom-color: var(--border-soft);
    }

    .btn-turn-primary {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        border-radius: 10px;
        font-weight: 700;
    }

    .btn-turn-primary:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        color: #fff;
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="turn-detail-title mb-1">
                <i class="bi bi-receipt-cutoff me-2" style="color:var(--text-main);"></i>
                Detalle del Turno #{{ $cashRegister->id }}
            </h2>
            <p class="text-muted mb-0">
                Resumen detallado de apertura, ventas, gastos y cierre de caja.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('cash_registers.index') }}"
               class="btn btn-light border">
                <i class="bi bi-arrow-left me-1"></i>
                Volver
            </a>

            @if($cashRegister->status === 'closed')
                <a href="{{ route('cash_registers.pdf', $cashRegister) }}"
                   target="_blank"
                   class="btn btn-turn-primary">
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    Imprimir PDF
                </a>
            @endif
        </div>
    </div>

    <div class="turn-detail-card mb-4">
        <div class="card-header">
            <h5 class="turn-section-title">
                <i class="bi bi-info-circle me-2"></i>
                Información del turno
            </h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">

                <div class="col-md-4">
                    <div class="turn-info-label">Cajero</div>
                    <div class="turn-info-value">
                        {{ $cashRegister->user->name ?? 'Usuario no disponible' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="turn-info-label">Apertura</div>
                    <div class="turn-info-value">
                        {{ $cashRegister->opening_time->format('d/m/Y - h:i A') }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="turn-info-label">Cierre</div>
                    <div class="turn-info-value">
                        {{ $cashRegister->closing_time
                            ? $cashRegister->closing_time->format('d/m/Y - h:i A')
                            : 'Turno aún abierto' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="turn-info-label">Duración</div>
                    <div class="turn-info-value">{{ $duration }}</div>
                </div>

                <div class="col-md-4">
                    <div class="turn-info-label">Estado</div>
                    <div>
                        @if($cashRegister->status === 'closed')
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                <i class="bi bi-door-closed me-1"></i>
                                Cerrada
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                <i class="bi bi-door-open me-1"></i>
                                Abierta
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="turn-info-label">Fondo inicial</div>
                    <div class="turn-info-value">
                        {{ $currency }} {{ number_format((float) $cashRegister->opening_amount, 2) }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="turn-kpi">
                <div class="turn-kpi-icon">
                    <i class="bi bi-cash"></i>
                </div>
                <div class="turn-kpi-label">Ventas en efectivo</div>
                <div class="turn-kpi-value">
                    {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="turn-kpi">
                <div class="turn-kpi-icon">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div class="turn-kpi-label">Ventas digitales</div>
                <div class="turn-kpi-value">
                    {{ $currency }} {{ number_format($totalSalesCard + $totalSalesYape + $totalSalesPlin, 2) }}
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="turn-kpi">
                <div class="turn-kpi-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="turn-kpi-label">Total vendido</div>
                <div class="turn-kpi-value">
                    {{ $currency }} {{ number_format($totalSales, 2) }}
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="turn-kpi">
                <div class="turn-kpi-icon">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
                <div class="turn-kpi-label">Gastos del turno</div>
                <div class="turn-kpi-value">
                    {{ $currency }} {{ number_format($totalExpenses, 2) }}
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="turn-detail-card h-100">
                <div class="card-header">
                    <h5 class="turn-section-title">
                        <i class="bi bi-wallet2 me-2"></i>
                        Ventas por método de pago
                    </h5>
                </div>

                <div class="card-body p-4">

                    <div class="payment-row">
                        <div class="payment-name">
                            <span class="payment-icon"><i class="bi bi-cash"></i></span>
                            Efectivo
                        </div>
                        <div class="payment-amount">
                            {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                        </div>
                    </div>

                    <div class="payment-row">
                        <div class="payment-name">
                            <span class="payment-icon"><i class="bi bi-credit-card"></i></span>
                            Tarjeta
                        </div>
                        <div class="payment-amount">
                            {{ $currency }} {{ number_format($totalSalesCard, 2) }}
                        </div>
                    </div>

                    <div class="payment-row">
                        <div class="payment-name">
                            <span class="payment-icon"><i class="bi bi-phone"></i></span>
                            Yape
                        </div>
                        <div class="payment-amount">
                            {{ $currency }} {{ number_format($totalSalesYape, 2) }}
                        </div>
                    </div>

                    <div class="payment-row">
                        <div class="payment-name">
                            <span class="payment-icon"><i class="bi bi-phone"></i></span>
                            Plin
                        </div>
                        <div class="payment-amount">
                            {{ $currency }} {{ number_format($totalSalesPlin, 2) }}
                        </div>
                    </div>

                    <div class="total-sales-box">
                        <span class="fw-bold">Total vendido</span>
                        <strong>
                            {{ $currency }} {{ number_format($totalSales, 2) }}
                        </strong>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="turn-detail-card h-100">
                <div class="card-header">
                    <h5 class="turn-section-title">
                        <i class="bi bi-calculator me-2"></i>
                        Arqueo de caja
                    </h5>
                </div>

                <div class="card-body p-4">

                    <div class="summary-row">
                        <span class="text-muted">Fondo inicial</span>
                        <strong>
                            {{ $currency }} {{ number_format((float) $cashRegister->opening_amount, 2) }}
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span class="text-muted">Ventas en efectivo</span>
                        <strong>
                            + {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span class="text-muted">Gastos</span>
                        <strong>
                            - {{ $currency }} {{ number_format($totalExpenses, 2) }}
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span class="fw-bold">Efectivo esperado</span>
                        <strong>
                            {{ $currency }} {{ number_format($expectedAmount, 2) }}
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span class="fw-bold">Efectivo contado</span>
                        <strong>
                            @if($closingAmount !== null)
                                {{ $currency }} {{ number_format($closingAmount, 2) }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>

                    @if($difference !== null)
                        @php
                            $differenceClass = abs($difference) < 0.005
                                ? 'difference-exact'
                                : ($difference > 0
                                    ? 'difference-surplus'
                                    : 'difference-shortage');

                            $differenceText = abs($difference) < 0.005
                                ? 'Caja exacta'
                                : ($difference > 0 ? 'Sobrante' : 'Faltante');
                        @endphp

                        <div class="difference-box {{ $differenceClass }} mt-4">
                            <div class="small fw-bold mb-1">
                                {{ $differenceText }}
                            </div>

                            <div class="fs-4 fw-bold">
                                {{ $currency }} {{ number_format(abs($difference), 2) }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

    <div class="turn-detail-card mb-4">
        <div class="card-header">
            <h5 class="turn-section-title">
                <i class="bi bi-receipt me-2"></i>
                Gastos registrados
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table expense-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Registrado por</th>
                            <th class="px-4 py-3">Fecha y hora</th>
                            <th class="px-4 py-3 text-end">Monto</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($cashRegister->expenses as $expense)
                            <tr>
                                <td class="px-4 py-3">
                                    {{ $expense->description }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $expense->user->name ?? 'No disponible' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $expense->created_at->format('d/m/Y h:i A') }}
                                </td>

                                <td class="px-4 py-3 text-end fw-bold">
                                    {{ $currency }} {{ number_format((float) $expense->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No se registraron gastos durante este turno.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($cashRegister->expenses->isNotEmpty())
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-4 py-3 fw-bold text-end">
                                    Total de gastos
                                </td>
                                <td class="px-4 py-3 fw-bold text-end">
                                    {{ $currency }} {{ number_format($totalExpenses, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="turn-detail-card">
        <div class="card-header">
            <h5 class="turn-section-title">
                <i class="bi bi-journal-text me-2"></i>
                Observaciones del cierre
            </h5>
        </div>

        <div class="card-body p-4">
            @if($cashRegister->notes)
                <div style="white-space:pre-line; color:var(--text-main);">
                    {{ $cashRegister->notes }}
                </div>
            @else
                <span class="text-muted">
                    No se registraron observaciones para este turno.
                </span>
            @endif
        </div>
    </div>

</div>
@endsection