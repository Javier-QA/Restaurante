<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre de Caja - Turno #{{ $cashRegister->id }}</title>

    <style>
        @page {
            margin: 28px 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #172033;
            margin: 0;
        }

        .header {
            background: {{ $palette['dark'] }};
            color: #ffffff;
            padding: 20px 22px;
            border-bottom: 5px solid {{ $palette['primary'] }};
            margin-bottom: 18px;
        }

        .header-table,
        .info-table,
        .cards-table,
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            font-size: 19px;
            font-weight: bold;
            margin: 0;
        }

        .header-subtitle {
            margin-top: 5px;
            color: #e5e7eb;
            font-size: 9px;
        }

        .turn-number {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: {{ $palette['dark'] }};
            border-left: 4px solid {{ $palette['primary'] }};
            padding: 5px 8px;
            margin-bottom: 9px;
            background: #15263a;
        }

        .info-box {
            border: 1px solid #dce7f1;
            padding: 12px;
        }

        .info-table td {
            width: 33.33%;
            vertical-align: top;
            padding: 5px 8px;
        }

        .label {
            color: #64748b;
            font-size: 8px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .value {
            font-weight: bold;
            font-size: 10px;
        }

        .cards-table td {
            width: 25%;
            padding: 0 4px;
            vertical-align: top;
        }

        .cards-table td:first-child {
            padding-left: 0;
        }

        .cards-table td:last-child {
            padding-right: 0;
        }

        .metric {
            border: 1px solid #dce7f1;
            border-top: 4px solid {{ $palette['primary'] }};
            padding: 11px 9px;
            min-height: 55px;
        }

        .metric-label {
            color: #64748b;
            font-size: 8px;
            margin-bottom: 5px;
        }

        .metric-value {
            color: {{ $palette['dark'] }};
            font-size: 12px;
            font-weight: bold;
        }

        .content-table td {
            width: 50%;
            vertical-align: top;
        }

        .left-column {
            padding-right: 7px;
        }

        .right-column {
            padding-left: 7px;
        }

        .panel {
            border: 1px solid #dce7f1;
            padding: 12px;
        }

        .row-line {
            width: 100%;
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 0;
        }

        .row-line:last-child {
            border-bottom: none;
        }

        .row-label {
            color: #475569;
        }

        .row-value {
            float: right;
            font-weight: bold;
        }

        .total-box {
            margin-top: 10px;
            padding: 10px;
            background: #15263a;
            border: 1px solid {{ $palette['primary'] }};
            color: {{ $palette['dark'] }};
            font-weight: bold;
        }

        .total-box span:last-child {
            float: right;
        }

        .difference {
            margin-top: 12px;
            padding: 11px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #dce7f1;
        }

        .difference.exact {
            background: #123225;
            color: #15803d;
            border-color: #86efac;
        }

        .difference.surplus {
            background: #142b3e;
            color: #1d4ed8;
            border-color: #93c5fd;
        }

        .difference.shortage {
            background: #351b24;
            color: #dc2626;
            border-color: #fca5a5;
        }

        .expenses {
            width: 100%;
            border-collapse: collapse;
        }

        .expenses th {
            background: {{ $palette['dark'] }};
            color: #ffffff;
            padding: 7px;
            text-align: left;
            font-size: 8px;
        }

        .expenses td {
            padding: 7px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }

        .expenses .amount {
            text-align: right;
            font-weight: bold;
        }

        .expenses-total td {
            background: #15263a;
            font-weight: bold;
            border-top: 2px solid {{ $palette['primary'] }};
        }

        .notes {
            border: 1px solid #dce7f1;
            padding: 11px;
            min-height: 36px;
            color: #475569;
        }

        .formula {
            color: #64748b;
            font-size: 8px;
            margin-top: 7px;
            line-height: 1.4;
        }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #dce7f1;
            color: #94a3b8;
            font-size: 7px;
            text-align: center;
        }
    </style>
</head>

<body>

    @php
        $differenceType = 'exact';
        $differenceLabel = 'Caja exacta';

        if ($difference !== null && $difference > 0.004) {
            $differenceType = 'surplus';
            $differenceLabel = 'Sobrante';
        } elseif ($difference !== null && $difference < -0.004) {
            $differenceType = 'shortage';
            $differenceLabel = 'Faltante';
        }
    @endphp

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="header-title">Reporte de Cierre de Caja</div>
                    <div class="header-subtitle">
                        Resumen detallado del turno y arqueo de efectivo
                    </div>
                </td>

                <td class="turn-number">
                    TURNO #{{ $cashRegister->id }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Información del turno</div>

        <div class="info-box">
            <table class="info-table">
                <tr>
                    <td>
                        <div class="label">Cajero</div>
                        <div class="value">
                            {{ $cashRegister->user->name ?? 'No disponible' }}
                        </div>
                    </td>

                    <td>
                        <div class="label">Apertura</div>
                        <div class="value">
                            {{ $cashRegister->opening_time->format('d/m/Y h:i A') }}
                        </div>
                    </td>

                    <td>
                        <div class="label">Cierre</div>
                        <div class="value">
                            {{ $cashRegister->closing_time
                                ? $cashRegister->closing_time->format('d/m/Y h:i A')
                                : 'Turno abierto' }}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="label">Duración</div>
                        <div class="value">{{ $duration }}</div>
                    </td>

                    <td>
                        <div class="label">Estado</div>
                        <div class="value">
                            {{ $cashRegister->status === 'closed' ? 'Cerrada' : 'Abierta' }}
                        </div>
                    </td>

                    <td>
                        <div class="label">Fondo inicial</div>
                        <div class="value">
                            {{ $currency }} {{ number_format((float) $cashRegister->opening_amount, 2) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <table class="cards-table">
            <tr>
                <td>
                    <div class="metric">
                        <div class="metric-label">VENTAS EFECTIVO</div>
                        <div class="metric-value">
                            {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                        </div>
                    </div>
                </td>

                <td>
                    <div class="metric">
                        <div class="metric-label">VENTAS DIGITALES</div>
                        <div class="metric-value">
                            {{ $currency }} {{ number_format($totalSalesCard + $totalSalesYape + $totalSalesPlin, 2) }}
                        </div>
                    </div>
                </td>

                <td>
                    <div class="metric">
                        <div class="metric-label">TOTAL VENDIDO</div>
                        <div class="metric-value">
                            {{ $currency }} {{ number_format($totalSales, 2) }}
                        </div>
                    </div>
                </td>

                <td>
                    <div class="metric">
                        <div class="metric-label">GASTOS</div>
                        <div class="metric-value">
                            {{ $currency }} {{ number_format($totalExpenses, 2) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="content-table">
            <tr>
                <td class="left-column">
                    <div class="section-title">Ventas por método de pago</div>

                    <div class="panel">
                        <div class="row-line">
                            <span class="row-label">Efectivo</span>
                            <span class="row-value">
                                {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                            </span>
                        </div>

                        <div class="row-line">
                            <span class="row-label">Tarjeta</span>
                            <span class="row-value">
                                {{ $currency }} {{ number_format($totalSalesCard, 2) }}
                            </span>
                        </div>

                        <div class="row-line">
                            <span class="row-label">Yape</span>
                            <span class="row-value">
                                {{ $currency }} {{ number_format($totalSalesYape, 2) }}
                            </span>
                        </div>

                        <div class="row-line">
                            <span class="row-label">Plin</span>
                            <span class="row-value">
                                {{ $currency }} {{ number_format($totalSalesPlin, 2) }}
                            </span>
                        </div>

                        <div class="total-box">
                            <span>Total vendido</span>
                            <span>
                                {{ $currency }} {{ number_format($totalSales, 2) }}
                            </span>
                        </div>
                    </div>
                </td>

                <td class="right-column">
                    <div class="section-title">Arqueo de caja</div>

                    <div class="panel">
                        <div class="row-line">
                            <span class="row-label">Fondo inicial</span>
                            <span class="row-value">
                                {{ $currency }} {{ number_format((float) $cashRegister->opening_amount, 2) }}
                            </span>
                        </div>

                        <div class="row-line">
                            <span class="row-label">Ventas en efectivo</span>
                            <span class="row-value">
                                + {{ $currency }} {{ number_format($totalSalesCash, 2) }}
                            </span>
                        </div>

                        <div class="row-line">
                            <span class="row-label">Gastos</span>
                            <span class="row-value">
                                - {{ $currency }} {{ number_format($totalExpenses, 2) }}
                            </span>
                        </div>

                        <div class="row-line">
                            <span class="row-label"><strong>Efectivo esperado</strong></span>
                            <span class="row-value">
                                {{ $currency }} {{ number_format($expectedAmount, 2) }}
                            </span>
                        </div>

                        <div class="row-line">
                            <span class="row-label"><strong>Efectivo contado</strong></span>
                            <span class="row-value">
                                {{ $closingAmount !== null
                                    ? $currency . ' ' . number_format($closingAmount, 2)
                                    : '-' }}
                            </span>
                        </div>

                        @if($difference !== null)
                            <div class="difference {{ $differenceType }}">
                                {{ $differenceLabel }}:
                                {{ $currency }} {{ number_format(abs($difference), 2) }}
                            </div>
                        @endif

                        <div class="formula">
                            Efectivo esperado = fondo inicial + ventas en efectivo - gastos.
                            Los pagos con Tarjeta, Yape y Plin no forman parte del efectivo físico.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalle de gastos del turno</div>

        <table class="expenses">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Registrado por</th>
                    <th>Fecha y hora</th>
                    <th style="text-align:right;">Monto</th>
                </tr>
            </thead>

            <tbody>
                @forelse($cashRegister->expenses as $expense)
                    <tr>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->user->name ?? 'No disponible' }}</td>
                        <td>{{ $expense->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="amount">
                            {{ $currency }} {{ number_format((float) $expense->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#64748b;">
                            No se registraron gastos durante este turno.
                        </td>
                    </tr>
                @endforelse

                @if($cashRegister->expenses->isNotEmpty())
                    <tr class="expenses-total">
                        <td colspan="3" style="text-align:right;">
                            Total de gastos
                        </td>
                        <td class="amount">
                            {{ $currency }} {{ number_format($totalExpenses, 2) }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Observaciones del cierre</div>

        <div class="notes">
            @if($cashRegister->notes)
                {!! nl2br(e($cashRegister->notes)) !!}
            @else
                No se registraron observaciones para este turno.
            @endif
        </div>
    </div>

    <div class="footer">
        Reporte de cierre de caja generado por el sistema ·
        Turno #{{ $cashRegister->id }}
    </div>

</body>
</html>