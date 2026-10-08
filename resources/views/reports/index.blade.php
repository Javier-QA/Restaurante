@extends('layouts.app')

@section('content')

@php
    $totalIncome = collect($catValues ?? [])->sum();
    $totalOrders = collect($salesByWaiter ?? [])->sum('orders_count');
    $topWaiter = collect($salesByWaiter ?? [])->first();
    $topProduct = collect($topProducts ?? [])->first();
@endphp

<div class="container-fluid reports-page">

    {{-- =========================================================
         ENCABEZADO + FILTROS
    ========================================================== --}}
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-2">

    <div>
        <h2 class="fw-bold mb-1 reports-title">
            <i class="bi bi-bar-chart-fill me-2"></i>
            Reportes Gerenciales
        </h2>

        <p class="text-muted mb-0">
            Análisis detallado del rendimiento del restaurante
        </p>
    </div>

    <form
        action="{{ route('reports.index') }}"
        method="GET"
        class="reports-filter d-flex flex-column flex-md-row gap-2 align-items-md-end"
    >

        <div>
            <label class="small fw-bold reports-label">
                Desde
            </label>

            <input
                type="date"
                name="start_date"
                class="form-control form-control-sm"
                value="{{ $startDate }}"
            >
        </div>

        <div>
            <label class="small fw-bold reports-label">
                Hasta
            </label>

            <input
                type="date"
                name="end_date"
                class="form-control form-control-sm"
                value="{{ $endDate }}"
            >
        </div>

        <button
            type="submit"
            class="btn reports-primary-btn btn-sm fw-bold px-3"
        >
            <i class="bi bi-filter me-1"></i>
            Analizar
        </button>

        <a
            href="{{ route('reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
            target="_blank"
            class="btn reports-secondary-btn btn-sm fw-bold px-3"
        >
            <i class="bi bi-file-earmark-pdf me-1"></i>
            Exportar PDF
        </a>

    </form>

</div>


    {{-- =========================================================
         KPIs DEL PERIODO
    ========================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="report-kpi kpi-income">
                <div class="report-kpi-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div class="flex-grow-1">
                    <span class="report-kpi-label">
                        Ingresos del periodo
                    </span>

                    <div class="report-kpi-value">
                        {{ $currency }} {{ number_format($totalIncome, 2) }}
                    </div>

                    <small class="text-muted">
                        Ventas completadas
                    </small>
                </div>
            </div>
        </div>


        <div class="col-md-6 col-xl-3">
            <div class="report-kpi kpi-orders">
                <div class="report-kpi-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <div class="flex-grow-1">
                    <span class="report-kpi-label">
                        Pedidos completados
                    </span>

                    <div class="report-kpi-value">
                        {{ number_format($totalOrders) }}
                    </div>

                    <small class="text-muted">
                        En el periodo seleccionado
                    </small>
                </div>
            </div>
        </div>


        <div class="col-md-6 col-xl-3">
            <div class="report-kpi kpi-waiter">
                <div class="report-kpi-icon">
                    <i class="bi bi-person-badge"></i>
                </div>

                <div class="flex-grow-1">
                    <span class="report-kpi-label">
                        Mejor rendimiento
                    </span>

                    <div class="report-kpi-value report-kpi-text">
                        {{ $topWaiter->name ?? 'Sin datos' }}
                    </div>

                    <small class="text-muted">
                        @if($topWaiter)
                            {{ $currency }} {{ number_format($topWaiter->total_sales ?? 0, 2) }}
                        @else
                            Sin ventas registradas
                        @endif
                    </small>
                </div>
            </div>
        </div>


        <div class="col-md-6 col-xl-3">
            <div class="report-kpi kpi-product">
                <div class="report-kpi-icon">
                    <i class="bi bi-star-fill"></i>
                </div>

                <div class="flex-grow-1">
                    <span class="report-kpi-label">
                        Producto estrella
                    </span>

                    <div class="report-kpi-value report-kpi-text">
                        {{ $topProduct->name ?? 'Sin datos' }}
                    </div>

                    <small class="text-muted">
                        @if($topProduct)
                            {{ number_format($topProduct->qty ?? 0) }} unidades
                        @else
                            Sin ventas registradas
                        @endif
                    </small>
                </div>
            </div>
        </div>

    </div>


    {{-- =========================================================
         GRÁFICOS
    ========================================================== --}}
    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card report-card h-100">

                <div class="card-header report-card-header">
                    <div>
                        <h5 class="fw-bold mb-1">
                            Ingresos por Categoría
                        </h5>

                        <small class="text-muted">
                            Distribución de ingresos según tipo de producto
                        </small>
                    </div>

                    <div class="report-header-icon">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>

                <div class="card-body">
                    <div class="report-chart-wrap">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-lg-6">
            <div class="card report-card h-100">

                <div class="card-header report-card-header">
                    <div>
                        <h5 class="fw-bold mb-1">
                            Ranking de Ventas por Personal
                        </h5>

                        <small class="text-muted">
                            Comparación de ventas generadas por colaborador
                        </small>
                    </div>

                    <div class="report-header-icon">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                </div>

                <div class="card-body">
                    <div class="report-chart-wrap">
                        <canvas id="waiterChart"></canvas>
                    </div>
                </div>

            </div>
        </div>

    </div>


    {{-- =========================================================
         TOP PRODUCTOS
    ========================================================== --}}
    <div class="row g-4">

        <div class="col-lg-7">
            <div class="card report-card">

                <div class="card-header report-card-header report-star-header report-ranking-header">
                    <div class="report-ranking-heading">
                        <div class="report-heading-icon">
                            <i class="bi bi-trophy-fill"></i>
                        </div>

                        <div>
                            <h6 class="fw-bold mb-1">
                                Top 5: Platos Estrella
                            </h6>

                            <small class="text-muted">
                                Productos con mayor cantidad vendida
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle report-table">

                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">
                                        Cant. Vendida
                                    </th>
                                    <th class="text-end pe-4">
                                        Ingresos Generados
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($topProducts as $prod)

                                    <tr>
                                        <td class="ps-4">
                                            <span class="rank-badge">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>

                                        <td class="fw-bold">
                                            {{ $prod->name }}
                                        </td>

                                        <td class="text-center">
                                            <span class="qty-badge">
                                                {{ $prod->qty }}
                                            </span>
                                        </td>

                                        <td class="text-end pe-4 fw-bold report-money">
                                            {{ $currency }} {{ number_format($prod->revenue, 2) }}
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td
                                            colspan="4"
                                            class="text-center text-muted py-4"
                                        >
                                            <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                                            Sin datos para el periodo seleccionado
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
        </div>


        <div class="col-lg-5">
    <div class="card report-card">

        <div class="card-header report-card-header report-low-header report-ranking-header">
            <div class="report-ranking-heading">
                <div class="report-heading-icon">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>

                <div>
                    <h6 class="fw-bold mb-1">
                        Menos Vendidos
                    </h6>

                    <small class="text-muted">
                        Productos que requieren atención comercial
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle report-table">

                    <thead>
                        <tr>
                            <th class="ps-4">
                                Producto
                            </th>

                            <th class="text-center">
                                Estado
                            </th>

                            <th class="text-end pe-4">
                                Cant. Vendida
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($worstProducts as $prod)

                            <tr>
                                <td class="ps-4">
                                    {{ $prod->name }}
                                </td>

                                <td class="text-center">

                                    @if($prod->qty <= 2)

                                        <span class="report-status status-critical">
                                            Crítico
                                        </span>

                                    @elseif($prod->qty <= 5)

                                        <span class="report-status status-low">
                                            Bajo
                                        </span>

                                    @else

                                        <span class="report-status status-regular">
                                            Regular
                                        </span>

                                    @endif

                                </td>

                                <td class="text-end pe-4 fw-bold">
                                    {{ $prod->qty }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="3"
                                    class="text-center text-muted py-4"
                                >
                                    <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                                    Sin datos para el periodo seleccionado
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

    </div>
</div>

</div>


{{-- =========================================================
     ESTILOS DINÁMICOS
========================================================== --}}
<style>

    .reports-page {
        --report-primary: var(--primary, #ff8c00);
        --report-primary-hover: var(--primary-hover, #e07b00);
        --report-dark: var(--dark-bg, #063970);
        --report-dark-2: var(--dark-bg-2, #0b4f8a);
        --report-light: var(--light-bg, #eef8fc);
        --report-card: var(--card-bg, #ffffff);
        --report-text: var(--text-main, #172033);
        --report-muted: var(--text-muted, #64748b);
        --report-border: var(--border-soft, #dce7f1);
        --report-accent-1: var(--accent-1, #0b84c6);
        --report-accent-2: var(--accent-2, #16a34a);
        --report-accent-3: var(--accent-3, #ff8c00);
        --report-accent-4: var(--accent-4, #06b6d4);
    }

    .reports-title {
        color: var(--report-text);
    }

    .reports-filter {
        background: var(--report-card);
        border: 1px solid var(--report-border);
        border-radius: 14px;
        padding: 10px 12px;
        box-shadow:
            0 3px 14px
            color-mix(
                in srgb,
                var(--report-dark) 7%,
                transparent
            );
    }

    .reports-label {
        display: block;
        margin-bottom: 4px;
        color: var(--report-muted);
    }

    .reports-primary-btn {
        background: var(--report-primary) !important;
        border-color: var(--report-primary) !important;
        color: #fff !important;
    }

    .reports-primary-btn:hover {
        background: var(--report-primary-hover) !important;
        border-color: var(--report-primary-hover) !important;
        color: #fff !important;
    }

    .reports-secondary-btn {
        background: var(--report-accent-1) !important;
        border-color: var(--report-accent-1) !important;
        color: #fff !important;
        min-height: 31px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .reports-secondary-btn:hover {
        filter: brightness(.9);
        color: #fff !important;
    }


    /* KPIs */
    .report-kpi {
        height: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px;
        border-radius: 16px;
        background: var(--report-card);
        border: 1px solid var(--report-border);
        box-shadow:
            0 3px 14px
            color-mix(
                in srgb,
                var(--report-dark) 7%,
                transparent
            );
        position: relative;
        overflow: hidden;
    }

    .report-kpi::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
    }

    .kpi-income::before {
        background: linear-gradient(
            180deg,
            var(--report-primary),
            var(--card-bg, white)
        );
    }

    .kpi-orders::before {
        background: linear-gradient(
            180deg,
            var(--report-accent-1),
            var(--card-bg, white)
        );
    }

    .kpi-waiter::before {
        background: linear-gradient(
            180deg,
            var(--report-accent-2),
            var(--card-bg, white)
        );
    }

    .kpi-product::before {
        background: linear-gradient(
            180deg,
            var(--report-accent-4),
            var(--card-bg, white)
        );
    }

    .report-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .kpi-income .report-kpi-icon {
        color: var(--report-primary);
        background:
            color-mix(
                in srgb,
                var(--report-primary) 12%,
                var(--card-bg, white)
            );
    }

    .kpi-orders .report-kpi-icon {
        color: var(--report-accent-1);
        background:
            color-mix(
                in srgb,
                var(--report-accent-1) 12%,
                var(--card-bg, white)
            );
    }

    .kpi-waiter .report-kpi-icon {
        color: var(--report-accent-2);
        background:
            color-mix(
                in srgb,
                var(--report-accent-2) 12%,
                var(--card-bg, white)
            );
    }

    .kpi-product .report-kpi-icon {
        color: var(--report-accent-4);
        background:
            color-mix(
                in srgb,
                var(--report-accent-4) 12%,
                var(--card-bg, white)
            );
    }

    .report-kpi-label {
        display: block;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
        color: var(--report-muted);
        margin-bottom: 4px;
    }

    .report-kpi-value {
        color: var(--report-text);
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.15;
    }

    .report-kpi-text {
        font-size: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 190px;
    }


    /* Cards */
    .report-card {
        border: 1px solid var(--report-border) !important;
        border-radius: 16px;
        overflow: hidden;
        background: var(--report-card);
        box-shadow:
            0 3px 14px
            color-mix(
                in srgb,
                var(--report-dark) 7%,
                transparent
            ) !important;
    }

    .report-card-header {
        background: var(--report-card) !important;
        border-bottom: 1px solid var(--report-border);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .report-header-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background:
            color-mix(
                in srgb,
                var(--report-primary) 11%,
                var(--card-bg, white)
            );
        color: var(--report-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .report-chart-wrap {
        height: 310px;
    }


    /* Tablas */
    .report-table thead th {
        border-bottom: 1px solid var(--report-border);
        background: var(--report-light);
        color: var(--report-muted);
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        font-weight: 700;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .report-table tbody td {
        border-color:
            color-mix(
                in srgb,
                var(--report-border) 70%,
                transparent
            );
        color: var(--report-text);
        padding-top: 13px;
        padding-bottom: 13px;
    }

    .report-table tbody tr:hover td {
        background: var(--report-light);
    }

    .rank-badge,
    .qty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 27px;
        height: 27px;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 800;
    }

    .rank-badge {
        background: var(--report-primary);
        color: #fff;
    }

    .qty-badge {
        padding: 0 9px;
        background:
            color-mix(
                in srgb,
                var(--report-accent-2) 12%,
                var(--card-bg, white)
            );
        color: var(--report-accent-2);
    }

    .report-money {
        color: var(--report-primary) !important;
    }


    @media (max-width: 767.98px) {

        .reports-filter {
            width: 100%;
        }

        .reports-filter > div,
        .reports-filter input,
        .reports-primary-btn,
        .reports-secondary-btn {
            width: 100%;
        }

        .report-kpi-text {
            max-width: 220px;
        }

        .report-chart-wrap {
            height: 270px;
        }

    }


    .report-star-header {
        position: relative;
        overflow: hidden;
    }

    .report-star-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(
            180deg,
            var(--report-primary),
            var(--card-bg, white)
        );
    }

    .report-low-header {
        position: relative;
        overflow: hidden;
    }

    .report-low-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(
            180deg,
            var(--report-accent-1),
            var(--card-bg, white)
        );
    }

    .report-star-header i {
        color: var(--report-primary);
    }

    .report-low-header i {
        color: var(--report-accent-1);
    }

    .report-table tbody tr {
        transition:
            transform .18s ease,
            background-color .18s ease;
    }

    .report-table tbody tr:hover {
        transform: translateX(3px);
    }

    .rank-badge {
        box-shadow:
            0 3px 8px
            color-mix(
                in srgb,
                var(--report-primary) 25%,
                transparent
            );
    }

    .report-table tbody tr:nth-child(1) .rank-badge {
        background: var(--report-primary);
    }

    .report-table tbody tr:nth-child(2) .rank-badge {
        background: var(--report-accent-1);
    }

    .report-table tbody tr:nth-child(3) .rank-badge {
        background: var(--report-accent-2);
    }

    .report-table tbody tr:nth-child(4) .rank-badge {
        background: var(--report-accent-4);
    }

    .report-table tbody tr:nth-child(5) .rank-badge {
        background: var(--report-dark-2);
    }


    /* =========================================================
       ESTADO DE PRODUCTOS MENOS VENDIDOS
    ========================================================== */

    .report-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .status-critical {
        color: var(--ink-red, #dc2626);
        background: var(--surface-red, #fff1f2);
        border-color: var(--surface-red, #fecaca);
    }

    .status-low {
        color: var(--report-primary);
        background:
            color-mix(
                in srgb,
                var(--report-primary) 10%,
                var(--card-bg, white)
            );
        border-color:
            color-mix(
                in srgb,
                var(--report-primary) 25%,
                var(--card-bg, white)
            );
    }

    .status-regular {
        color: var(--report-accent-1);
        background:
            color-mix(
                in srgb,
                var(--report-accent-1) 10%,
                var(--card-bg, white)
            );
        border-color:
            color-mix(
                in srgb,
                var(--report-accent-1) 25%,
                var(--card-bg, white)
            );
    }
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const styles = getComputedStyle(document.body);

    const cssVar = (name, fallback) => {
        const value = styles.getPropertyValue(name).trim();
        return value || fallback;
    };

    const theme = {
        primary: cssVar('--primary', '#ff8c00'),
        dark: cssVar('--dark-bg', '#063970'),
        accent1: cssVar('--accent-1', '#0b84c6'),
        accent2: cssVar('--accent-2', '#16a34a'),
        accent3: cssVar('--accent-3', '#ff8c00'),
        accent4: cssVar('--accent-4', '#06b6d4'),
        muted: cssVar('--text-muted', '#64748b')
    };


    function hexToRgb(hex) {

        const clean = hex.replace('#', '').trim();

        if (!/^[0-9a-fA-F]{6}$/.test(clean)) {
            return { r: 11, g: 132, b: 198 };
        }

        return {
            r: parseInt(clean.substring(0, 2), 16),
            g: parseInt(clean.substring(2, 4), 16),
            b: parseInt(clean.substring(4, 6), 16)
        };
    }


    function mixColor(hex, amount, target = 255) {

        const rgb = hexToRgb(hex);

        const r = Math.round(
            rgb.r + (target - rgb.r) * amount
        );

        const g = Math.round(
            rgb.g + (target - rgb.g) * amount
        );

        const b = Math.round(
            rgb.b + (target - rgb.b) * amount
        );

        return `rgb(${r}, ${g}, ${b})`;
    }


    function generateCategoryColors(count) {

        const bases = [
            theme.primary,
            theme.accent1,
            theme.accent2,
            theme.accent4,
            theme.dark
        ];

        const colors = [];

        for (let i = 0; i < count; i++) {

            const base = bases[i % bases.length];
            const cycle = Math.floor(i / bases.length);

            if (cycle === 0) {
                colors.push(base);
                continue;
            }

            if (cycle % 2 === 1) {

                const amount =
                    Math.min(
                        0.18 + cycle * 0.05,
                        0.45
                    );

                colors.push(
                    mixColor(
                        base,
                        amount,
                        255
                    )
                );

            } else {

                const amount =
                    Math.min(
                        0.10 + cycle * 0.04,
                        0.30
                    );

                colors.push(
                    mixColor(
                        base,
                        amount,
                        0
                    )
                );

            }

        }

        return colors;
    }


    function hexToRgba(hex, alpha) {

        const clean = hex.replace('#', '').trim();

        if (!/^[0-9a-fA-F]{6}$/.test(clean)) {
            return `rgba(11,132,198,${alpha})`;
        }

        const rgb = hexToRgb(hex);

        return `rgba(${rgb.r},${rgb.g},${rgb.b},${alpha})`;
    }


    // ============================================================
    // INGRESOS POR CATEGORÍA
    // ============================================================

    const ctxCat =
        document.getElementById('categoryChart');

    if (ctxCat) {

        const categoryLabels =
            @json($catLabels);

        const categoryValues =
            @json($catValues);

        new Chart(ctxCat, {

            type: 'doughnut',

            data: {

                labels: categoryLabels,

                datasets: [{

                    data: categoryValues,

                    backgroundColor:
                        generateCategoryColors(
                            categoryLabels.length
                        ),

                    borderColor: '#ffffff',

                    borderWidth: 2,

                    hoverOffset: 6

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '62%',

                plugins: {

                    legend: {

                        position: 'right',

                        labels: {
                            usePointStyle: true,
                            padding: 16,
                            color: theme.muted,
                            font: {
                                size: 11
                            }
                        }

                    },

                    tooltip: {

                        backgroundColor:
                            theme.dark,

                        callbacks: {

                            label: function(context) {

                                const value =
                                    Number(
                                        context.raw || 0
                                    );

                                const values =
                                    context.dataset.data.map(Number);

                                const total =
                                    values.reduce(
                                        function(sum, item) {
                                            return sum + item;
                                        },
                                        0
                                    );

                                const percentage =
                                    total > 0
                                        ? ((value / total) * 100).toFixed(1)
                                        : '0.0';

                                return (
                                    context.label +
                                    ': {{ $currency }} ' +
                                    value.toLocaleString(
                                        'es-PE',
                                        {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }
                                    ) +
                                    ' (' +
                                    percentage +
                                    '%)'
                                );
                            }

                        }

                    }

                }

            }

        });

    }


    // ============================================================
    // RANKING DE PERSONAL
    // ============================================================

    const ctxWait =
        document.getElementById('waiterChart');

    if (ctxWait) {

        const waiterLabels =
            @json($waiterLabels);

        const waiterValues =
            @json($waiterValues);

        const waiterOrders =
            @json($salesByWaiter->pluck('orders_count')->values());

        new Chart(ctxWait, {

            type: 'bar',

            data: {

                labels: waiterLabels,

                datasets: [{

                    label:
                        'Ventas Totales ({{ $currency }})',

                    data:
                        waiterValues,

                    backgroundColor:
                        hexToRgba(
                            theme.accent1,
                            .72
                        ),

                    borderColor:
                        theme.accent1,

                    borderWidth: 1,

                    borderRadius: 7,

                    maxBarThickness: 48

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        backgroundColor:
                            theme.dark,

                        callbacks: {

                            label: function(context) {

                                const value =
                                    Number(
                                        context.raw || 0
                                    );

                                const ordersCount =
                                    Number(
                                        waiterOrders[
                                            context.dataIndex
                                        ] || 0
                                    );

                                return (
                                    '{{ $currency }} ' +
                                    value.toLocaleString(
                                        'es-PE',
                                        {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }
                                    ) +
                                    ' · ' +
                                    ordersCount +
                                    ' pedidos'
                                );
                            }

                        }

                    }

                },

                scales: {

                    x: {

                        grid: {
                            display: false
                        },

                        ticks: {
                            color: theme.muted
                        }

                    },

                    y: {

                        beginAtZero: true,

                        grid: {
                            color: 'rgba(0,0,0,.05)'
                        },

                        ticks: {

                            color: theme.muted,

                            callback: function(value) {
                                return '{{ $currency }} ' + value;
                            }

                        }

                    }

                }

            }

        });

    }

});
</script>


<style id="reports-header-icons-theme-dark">
/* ============================================================
   DARK MODE - ICONOS CABECERA REPORTES
   Respeta automáticamente la paleta seleccionada
   ============================================================ */

html[data-color-mode="dark"] .report-header-icon {

    color: var(--report-primary) !important;

    background:
        color-mix(
            in srgb,
            var(--report-primary) 12%,
            #111e30
        ) !important;

    border:
        1px solid
        color-mix(
            in srgb,
            var(--report-primary) 42%,
            #30465d
        ) !important;

    box-shadow: none !important;
}

html[data-color-mode="dark"] .report-header-icon i {
    color: var(--report-primary) !important;
}

</style>


<style id="reports-ranking-redesign-final">

/* ============================================================
   TOP 5 + MENOS VENDIDOS
   Rediseño visual final
   ============================================================ */


/* ------------------------------------------------------------
   TARJETAS
   ------------------------------------------------------------ */

.report-star-header,
.report-low-header {
    border-left: 0 !important;
}

.report-star-header::before,
.report-low-header::before {
    display: none !important;
    content: none !important;
}


/* ------------------------------------------------------------
   ENCABEZADOS
   ------------------------------------------------------------ */

.report-ranking-header {
    padding: 18px 24px !important;

    background: var(--card-bg) !important;

    border-bottom:
        1px solid var(--border-soft) !important;
}

.report-ranking-heading {
    display: flex;
    align-items: center;
    gap: 12px;
}


/* Icono con identidad del sistema */
.report-heading-icon {
    width: 34px;
    height: 34px;

    flex: 0 0 34px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    color: var(--report-primary);

    background:
        color-mix(
            in srgb,
            var(--report-primary) 7%,
            var(--card-bg)
        );

    border:
        1px solid
        color-mix(
            in srgb,
            var(--report-primary) 35%,
            var(--border-soft)
        );
}

.report-heading-icon i {
    color: var(--report-primary) !important;

    font-size: 15px;

    line-height: 1;
}


/* Título */
.report-ranking-header h6 {
    margin: 0;

    color: var(--text-main);

    font-size: .93rem;

    font-weight: 800 !important;
}


/* Subtítulo */
.report-ranking-header small {
    display: block;

    margin-top: 3px;

    color: var(--text-muted) !important;

    font-size: .77rem;
}


/* ------------------------------------------------------------
   TABLAS
   ------------------------------------------------------------ */

.report-ranking-header + .card-body
.report-table {
    margin: 0;
}


/* Cabecera */
.report-ranking-header + .card-body
.report-table thead th {
    padding-top: 12px;
    padding-bottom: 12px;

    color: var(--text-muted);

    background:
        color-mix(
            in srgb,
            var(--light-bg) 78%,
            var(--card-bg)
        );

    border-bottom:
        1px solid var(--border-soft);

    font-size: .70rem;

    font-weight: 800;

    letter-spacing: .025em;

    text-transform: uppercase;

    vertical-align: middle;
}


/* Filas */
.report-ranking-header + .card-body
.report-table tbody td {
    padding-top: 14px;
    padding-bottom: 14px;

    color: var(--text-main);

    border-bottom:
        1px solid
        color-mix(
            in srgb,
            var(--border-soft) 75%,
            transparent
        );

    vertical-align: middle;
}


/* Última fila */
.report-ranking-header + .card-body
.report-table tbody tr:last-child td {
    border-bottom: 0;
}


/* Hover más sutil */
.report-ranking-header + .card-body
.report-table tbody tr {
    transition:
        background-color .18s ease;
}

.report-ranking-header + .card-body
.report-table tbody tr:hover {
    background:
        color-mix(
            in srgb,
            var(--report-primary) 3.5%,
            var(--card-bg)
        );
}


/* Evitar desplazamiento lateral antiguo */
.report-ranking-header + .card-body
.report-table tbody tr:hover {
    transform: none !important;
}


/* ------------------------------------------------------------
   RANKING 1 - 5
   ------------------------------------------------------------ */

.report-ranking-header + .card-body
.rank-badge {
    width: 30px;
    min-width: 30px;
    height: 30px;

    padding: 0;

    border-radius: 9px;

    font-size: .72rem;
    font-weight: 800;

    box-shadow: none;
}


/* Primer puesto */
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(1) .rank-badge {
    background:
        color-mix(
            in srgb,
            var(--report-primary) 15%,
            var(--card-bg)
        ) !important;

    color: var(--report-primary) !important;

    border:
        1px solid
        color-mix(
            in srgb,
            var(--report-primary) 30%,
            transparent
        );
}


/* Segundo puesto */
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(2) .rank-badge {
    background: var(--surface-blue, #eff6ff) !important;
    color: #0284c7 !important;
    border: 1px solid var(--surface-blue, #bae6fd);
}


/* Tercer puesto */
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(3) .rank-badge {
    background: var(--surface-green, #ecfdf5) !important;
    color: #059669 !important;
    border: 1px solid var(--surface-green, #a7f3d0);
}


/* Cuarto puesto */
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(4) .rank-badge {
    background: var(--surface-amber, #fff7ed) !important;
    color: #ea580c !important;
    border: 1px solid var(--surface-amber, #fed7aa);
}


/* Quinto puesto */
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(5) .rank-badge {
    background: var(--surface-neutral, #f1f5f9) !important;
    color: var(--ink-neutral, #475569) !important;
    border: 1px solid var(--surface-blue, #cbd5e1);
}


/* ------------------------------------------------------------
   CANTIDAD VENDIDA
   ------------------------------------------------------------ */

.report-ranking-header + .card-body
.qty-badge {
    min-width: 34px;
    height: 28px;

    padding: 0 10px;

    border-radius: 8px;

    color: var(--ink-green, #15803d);

    background: var(--surface-green, #f0fdf4);

    border: 1px solid var(--surface-green, #bbf7d0);

    font-size: .72rem;
    font-weight: 800;
}


/* ------------------------------------------------------------
   INGRESOS
   ------------------------------------------------------------ */

.report-ranking-header + .card-body
.report-money {
    color: var(--text-main) !important;

    font-weight: 800 !important;

    white-space: nowrap;
}


/* ------------------------------------------------------------
   ESTADOS DE MENOS VENDIDOS
   ------------------------------------------------------------ */

.report-ranking-header + .card-body
.report-status {
    min-width: 78px;

    padding: 5px 11px;

    border-radius: 999px;

    font-size: .68rem;

    font-weight: 800;
}


/* Crítico */
.report-ranking-header + .card-body
.status-critical {
    color: var(--ink-red, #dc2626) !important;
    background: var(--surface-red, #fff1f2) !important;
    border-color: var(--surface-red, #fecaca) !important;
}


/* Bajo */
.report-ranking-header + .card-body
.status-low {
    color: #65a30d !important;
    background: var(--surface-green, #f7fee7) !important;
    border-color: var(--surface-green, #d9f99d) !important;
}


/* Regular */
.report-ranking-header + .card-body
.status-regular {
    color: var(--ink-blue, #0369a1) !important;
    background: var(--surface-blue, #f0f9ff) !important;
    border-color: var(--surface-blue, #bae6fd) !important;
}


/* ============================================================
   MODO OSCURO
   ============================================================ */

html[data-color-mode="dark"]
.report-ranking-header {
    background: #111e30 !important;

    border-bottom-color:
        rgba(255,255,255,.09) !important;
}


html[data-color-mode="dark"]
.report-heading-icon {
    color: var(--report-primary);

    background:
        color-mix(
            in srgb,
            var(--report-primary) 12%,
            #111e30
        );

    border-color:
        color-mix(
            in srgb,
            var(--report-primary) 42%,
            #30465d
        );
}


html[data-color-mode="dark"]
.report-ranking-header h6 {
    color: #f3f7fb !important;
}


html[data-color-mode="dark"]
.report-ranking-header small {
    color: #91a8bd !important;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table thead th {
    color: #a8bdd0 !important;

    background: #17283c !important;

    border-bottom-color:
        rgba(255,255,255,.08) !important;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table tbody td {
    color: #edf4fa !important;

    border-bottom-color:
        rgba(255,255,255,.07) !important;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table tbody tr:hover {
    background: #16283b !important;
}


/* Ranking oscuro */
html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(1) .rank-badge {
    background:
        color-mix(
            in srgb,
            var(--report-primary) 18%,
            #132338
        ) !important;

    color: var(--report-primary) !important;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(2) .rank-badge {
    background: #102d43 !important;
    color: #38bdf8 !important;
    border-color: #155e75;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(3) .rank-badge {
    background: #0f302b !important;
    color: #34d399 !important;
    border-color: #166534;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(4) .rank-badge {
    background: #382414 !important;
    color: #fb923c !important;
    border-color: #7c2d12;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.report-table tbody tr:nth-child(5) .rank-badge {
    background: #263548 !important;
    color: #cbd5e1 !important;
    border-color: #475569;
}


/* Cantidad */
html[data-color-mode="dark"]
.report-ranking-header + .card-body
.qty-badge {
    color: #86efac;

    background: #123226;

    border-color: #166534;
}


/* Estados */
html[data-color-mode="dark"]
.report-ranking-header + .card-body
.status-critical {
    color: #fca5a5 !important;
    background: #3b1920 !important;
    border-color: #7f1d1d !important;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.status-low {
    color: #bef264 !important;
    background: #263414 !important;
    border-color: #4d7c0f !important;
}


html[data-color-mode="dark"]
.report-ranking-header + .card-body
.status-regular {
    color: #7dd3fc !important;
    background: #102d43 !important;
    border-color: #075985 !important;
}


/* Responsive */
@media (max-width: 767.98px) {

    .report-ranking-header {
        padding: 15px 17px !important;
    }

    .report-heading-icon {
        width: 32px;
        height: 32px;
        flex-basis: 32px;
    }

    .report-ranking-header + .card-body
    .report-table tbody td {
        padding-top: 12px;
        padding-bottom: 12px;
    }
}

</style>

@endsection
