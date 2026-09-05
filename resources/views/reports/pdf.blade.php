<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Reporte de Ventas</title>

    @php
        $dashboardTheme = \App\Models\Setting::where(
            'key',
            'dashboard_theme'
        )->value('value') ?? 'ocean-orange';

        $pdfThemes = [

            'ocean-orange' => [
                'primary' => '#ff8c00',
                'dark'    => '#063970',
                'accent'  => '#0b84c6',
                'light'   => '#eef8fc',
            ],

            'lime-blue' => [
                'primary' => '#84cc16',
                'dark'    => '#063970',
                'accent'  => '#22a06b',
                'light'   => '#f2fbf3',
            ],

            'purple-orange' => [
                'primary' => '#ff8c00',
                'dark'    => '#4c1d95',
                'accent'  => '#7c3aed',
                'light'   => '#f7f2ff',
            ],

            'sand-navy' => [
                'primary' => '#c98a52',
                'dark'    => '#063970',
                'accent'  => '#e7c6a5',
                'light'   => '#f7f1e9',
            ],

            'teal-amber' => [
                'primary' => '#f59e0b',
                'dark'    => '#115e59',
                'accent'  => '#0f766e',
                'light'   => '#f0fdfa',
            ],

            'wine-blue' => [
                'primary' => '#2563eb',
                'dark'    => '#7f1d1d',
                'accent'  => '#b91c1c',
                'light'   => '#fef2f2',
            ],
        ];

        $pdfTheme =
            $pdfThemes[$dashboardTheme]
            ?? $pdfThemes['ocean-orange'];
    @endphp

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            color: {{ $pdfTheme['dark'] }};
        }

        .header p {
            margin: 6px 0 0 0;
            color: #64748b;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary td {
            width: 33.33%;
            padding: 12px;
            border: 1px solid #dbe4ee;
            vertical-align: top;
        }

        .summary-label {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: {{ $pdfTheme['dark'] }};
        }

        .section {
            margin-top: 22px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: {{ $pdfTheme['dark'] }};
            padding-bottom: 6px;
            margin-bottom: 10px;
            border-bottom: 2px solid {{ $pdfTheme['primary'] }};
        }

        .progress-wrap {
            margin-top: 10px;
        }

        .progress {
            width: 100%;
            height: 14px;
            background: {{ $pdfTheme['light'] }};
            border-radius: 7px;
            overflow: hidden;
        }

        .progress-bar {
            height: 14px;
            background: {{ $pdfTheme['primary'] }};
        }

        .progress-text {
            margin-bottom: 7px;
            font-weight: bold;
            color: {{ $pdfTheme['dark'] }};
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: {{ $pdfTheme['dark'] }};
            color: #ffffff;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        table.data td {
            padding: 8px;
            border: 1px solid #dbe4ee;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .empty {
            text-align: center;
            color: #64748b;
            padding: 15px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dbe4ee;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="header">

        <h1>{{ $companyName }}</h1>

        <p>
            Reporte de ventas
        </p>

        <p>
            Periodo:
            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
            al
            {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </p>

    </div>


    <table class="summary">

        <tr>

            <td>
                <div class="summary-label">
                    Ventas totales
                </div>

                <div class="summary-value">
                    {{ $currency }}
                    {{ number_format($totalSales, 2, '.', ',') }}
                </div>
            </td>


            <td>
                <div class="summary-label">
                    Pedidos completados
                </div>

                <div class="summary-value">
                    {{ $ordersCount }}
                </div>
            </td>


            <td>
                <div class="summary-label">
                    Meta mensual
                </div>

                <div class="summary-value">
                    {{ $currency }}
                    {{ number_format($monthlyGoal, 2, '.', ',') }}
                </div>
            </td>

        </tr>

    </table>


    <div class="section">

        <div class="section-title">
            Progreso de Meta Mensual
        </div>

        <div class="progress-wrap">

            <div class="progress-text">

                Logrado:
                {{ $currency }}
                {{ number_format($totalSales, 2, '.', ',') }}

                de

                {{ $currency }}
                {{ number_format($monthlyGoal, 2, '.', ',') }}

                — {{ $goalPercent }}%

            </div>


            <div class="progress">

                <div
                    class="progress-bar"
                    style="width: {{ $goalPercent }}%;"
                ></div>

            </div>

        </div>

    </div>


    <div class="section">

        <div class="section-title">
            Top 5 Productos Más Vendidos
        </div>

        <table class="data">

            <thead>

                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Ingresos</th>
                </tr>

            </thead>


            <tbody>

                @forelse($topProducts as $product)

                    <tr>

                        <td>
                            {{ $product->name }}
                        </td>

                        <td class="text-center">
                            {{ $product->qty }}
                        </td>

                        <td class="text-right">

                            {{ $currency }}
                            {{ number_format($product->revenue, 2, '.', ',') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="3"
                            class="empty"
                        >
                            No existen ventas registradas en este periodo.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="section">

        <div class="section-title">
            Rendimiento del Personal
        </div>

        <table class="data">

            <thead>

                <tr>
                    <th>Personal</th>
                    <th class="text-center">Pedidos</th>
                    <th class="text-right">Ventas</th>
                </tr>

            </thead>


            <tbody>

                @forelse($salesByWaiter as $waiter)

                    <tr>

                        <td>
                            {{ $waiter->name }}
                        </td>

                        <td class="text-center">
                            {{ $waiter->orders_count }}
                        </td>

                        <td class="text-right">

                            {{ $currency }}
                            {{ number_format($waiter->total_sales, 2, '.', ',') }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="3"
                            class="empty"
                        >
                            No existen datos de personal para este periodo.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="section">

        <div class="section-title">
            Productos con Menor Venta
        </div>

        <table class="data">

            <thead>

                <tr>
                    <th>Producto</th>
                    <th class="text-center">
                        Cantidad Vendida
                    </th>
                </tr>

            </thead>


            <tbody>

                @forelse($worstProducts as $product)

                    <tr>

                        <td>
                            {{ $product->name }}
                        </td>

                        <td class="text-center">
                            {{ $product->qty }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="2"
                            class="empty"
                        >
                            No existen datos disponibles.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="footer">

        Reporte generado el
        {{ $generatedAt->format('d/m/Y H:i') }}

    </div>

</body>
</html>