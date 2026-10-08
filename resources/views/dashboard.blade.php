@extends('layouts.app')

@php
    // Pre-compute safe arrays for JS (avoids Blade parse issues with array_fill in @push)
    $jsMonthlySales        = $monthlySales        ?? array_fill(0, 12, 0);
    $jsMonthlyOrders       = $monthlyOrders       ?? array_fill(0, 12, 0);
    $jsMonthlyReservations = $monthlyReservations ?? array_fill(0, 12, 0);
    $jsRadarLabels         = $radarLabels         ?? ['Entradas', 'Platos', 'Bebidas', 'Postres', 'Especiales'];
    $jsRadarData           = $radarData           ?? [0, 0, 0, 0, 0];
    $jsGoalPercent         = $goalPercent         ?? 60;
    $safeGoalPercent       = max(0, min(100, (float) $jsGoalPercent));
    $rawGoalPercent        = (float) ($goalPercent ?? 0);

    if ($rawGoalPercent >= 100) {
        $goalLevel = 'complete';
    } elseif ($rawGoalPercent >= 75) {
        $goalLevel = 'high';
    } elseif ($rawGoalPercent >= 50) {
        $goalLevel = 'medium';
    } elseif ($rawGoalPercent >= 25) {
        $goalLevel = 'low';
    } else {
        $goalLevel = 'start';
    }

    $goalReached = $rawGoalPercent >= 100;
    $goalExceeded = $rawGoalPercent > 100;

    $goalNotificationEnabled =
        (string) (
            \App\Models\Setting::where(
                'key',
                'goal_notification_enabled'
            )->value('value') ?? '1'
        ) === '1';

    $goalConfettiEnabled =
        (string) (
            \App\Models\Setting::where(
                'key',
                'goal_confetti_enabled'
            )->value('value') ?? '1'
        ) === '1';
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | COLORES DINÁMICOS DEL TEMA
    |--------------------------------------------------------------------------
    */
    const themeStyles = getComputedStyle(document.body);

    const cssVar = (name, fallback) => {
        const value = themeStyles.getPropertyValue(name).trim();
        return value || fallback;
    };

    const theme = {
        primary:      cssVar('--primary', '#ff8c00'),
        primaryHover: cssVar('--primary-hover', '#e07b00'),
        dark:         cssVar('--dark-bg', '#063970'),
        dark2:        cssVar('--dark-bg-2', '#0b4f8a'),
        dark3:        cssVar('--dark-bg-3', '#042a54'),
        light:        cssVar('--light-bg', '#eef8fc'),
        text:         cssVar('--text-main', '#172033'),
        muted:        cssVar('--text-muted', '#64748b'),
        border:       cssVar('--border-soft', '#dce7f1'),
        accent1:      cssVar('--accent-1', '#0b84c6'),
        accent2:      cssVar('--accent-2', '#16a34a'),
        accent3:      cssVar('--accent-3', '#ff8c00'),
        accent4:      cssVar('--accent-4', '#06b6d4')
    };

    const hexToRgba = (hex, alpha) => {
        const clean = hex.replace('#', '').trim();

        if (!/^[0-9a-fA-F]{6}$/.test(clean)) {
            return `rgba(11,132,198,${alpha})`;
        }

        const r = parseInt(clean.substring(0, 2), 16);
        const g = parseInt(clean.substring(2, 4), 16);
        const b = parseInt(clean.substring(4, 6), 16);

        return `rgba(${r},${g},${b},${alpha})`;
    };

    const currency = @json($currency ?? 'S/');

    /*
    |--------------------------------------------------------------------------
    | CHART 1 — ACTIVIDAD ANUAL
    |--------------------------------------------------------------------------
    */
    const lineCanvas = document.getElementById('lineChart');

    if (lineCanvas) {
        const lineCtx = lineCanvas.getContext('2d');

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                datasets: [
                    {
                        label: 'Ventas',
                        data: @json($jsMonthlySales),
                        borderColor: theme.primary,
                        backgroundColor: hexToRgba(theme.primary, 0.09),
                        borderWidth: 2.5,
                        pointBackgroundColor: theme.primary,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.45,
                        fill: true,
                        yAxisID: 'ySales'
                    },
                    {
                        label: 'Pedidos',
                        data: @json($jsMonthlyOrders),
                        borderColor: theme.accent1,
                        backgroundColor: hexToRgba(theme.accent1, 0.06),
                        borderWidth: 2,
                        pointBackgroundColor: theme.accent1,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.45,
                        fill: true,
                        yAxisID: 'yCount'
                    },
                    {
                        label: 'Reservas',
                        data: @json($jsMonthlyReservations),
                        borderColor: theme.accent2,
                        backgroundColor: hexToRgba(theme.accent2, 0.05),
                        borderWidth: 2,
                        pointBackgroundColor: theme.accent2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.45,
                        fill: true,
                        yAxisID: 'yCount'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyleWidth: 8,
                            padding: 18,
                            color: theme.muted,
                            font: { family: 'Inter', size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: theme.dark,
                        titleFont: { family: 'Inter', weight: '700' },
                        bodyFont: { family: 'Inter' },
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: theme.muted,
                            font: { family: 'Inter', size: 11 }
                        }
                    },
                    ySales: {
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            color: theme.muted,
                            font: { family: 'Inter', size: 11 },
                            callback: value => `${currency} ${value}`
                        }
                    },
                    yCount: {
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: {
                            color: theme.muted,
                            precision: 0,
                            font: { family: 'Inter', size: 11 }
                        }
                    }
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CHART 2 — PROGRESO DE META
    |--------------------------------------------------------------------------
    */
    const donutCanvas = document.getElementById('donutChart');

    if (donutCanvas) {
        const donutCtx = donutCanvas.getContext('2d');
        const goalPct = @json($safeGoalPercent);

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [goalPct, Math.max(0, 100 - goalPct)],
                    backgroundColor: [
                        theme.primary,
                        hexToRgba(theme.accent1, 0.12)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICACIÓN DE META MENSUAL
    |--------------------------------------------------------------------------
    */
    const goalToastEl = document.getElementById('monthlyGoalToast');

    if (goalToastEl && typeof bootstrap !== 'undefined') {
        const goalKey = "monthly-goal-{{ now()->format('Y-m') }}-{{ number_format((float) ($monthlyGoal ?? 5000), 2, '.', '') }}";

        const completedGoalBar =
            document.querySelector('.goal-fill-complete');

        const stopGoalAnimation = () => {
            if (completedGoalBar) {
                completedGoalBar.classList.add(
                    'goal-animation-paused'
                );
            }
        };

        if (!localStorage.getItem(goalKey)) {
            const goalToast = new bootstrap.Toast(goalToastEl, {
                autohide: false
            });

            goalToastEl.addEventListener(
                'hidden.bs.toast',
                stopGoalAnimation
            );

            goalToast.show();

            
        } else {
            stopGoalAnimation();
        }
    }

});
</script>
@endpush

@section('content')

@if(!$goalConfettiEnabled)
    <style>
        .goal-confetti-piece {
            display: none !important;
        }
    
    .dashboard-top-header {
        position: relative;
        overflow: hidden;
    }

    .dashboard-top-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(
            180deg,
            var(--dash-primary),
            white
        );
    }

    .dashboard-top-header .bi-trophy-fill {
        color: var(--dash-primary);
    }

    .top-product-item {
        transition:
            transform .18s ease,
            background-color .18s ease;
    }

    .top-product-item:hover {
        transform: translateX(3px);
    }

    .top-product-item:nth-child(1) .badge {
        background: var(--dash-primary) !important;
    }

    .top-product-item:nth-child(2) .badge {
        background: var(--dash-accent-1) !important;
    }

    .top-product-item:nth-child(3) .badge {
        background: var(--dash-accent-2) !important;
    }

    .top-product-item:nth-child(4) .badge {
        background: var(--dash-accent-4) !important;
    }

    .top-product-item:nth-child(5) .badge {
        background: var(--dash-dark-2) !important;
    }

</style>
@endif

<div class="container-fluid p-0">

    {{-- ============================================================
         FILA 1: 4 Paneles KPI + Ventas Mes + Meta
    ============================================================ --}}
    <div class="row g-3 mb-3">

        {{-- KPI 1: Ventas de Hoy --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-panel kpi-sales">
                <div class="kpi-icon-wrap">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="kpi-content">
                    <span class="kpi-label">Ventas de Hoy</span>
                    <div class="kpi-value">{{ $currency ?? 'S/' }} {{ number_format($totalSalesToday ?? 0, 2, '.', ',') }}</div>
                    <div class="kpi-sub">
                        <i class="bi bi-receipt me-1"></i>
                        {{ $ordersCountToday ?? 0 }} órdenes completadas
                    </div>
                </div>
                <div class="kpi-badge">HOY</div>
            </div>
        </div>

        {{-- KPI 2: Mesas en Servicio --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-panel kpi-tables">
                <div class="kpi-icon-wrap">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </div>
                <div class="kpi-content">
                    <span class="kpi-label">Mesas en Servicio</span>
                    <div class="kpi-value">{{ $activeTables ?? 0 }}</div>
                    <div class="kpi-sub">
                        <i class="bi bi-clock me-1"></i>
                        Ocupadas ahora mismo
                    </div>
                </div>
                <div class="kpi-badge">LIVE</div>
            </div>
        </div>

        {{-- KPI 3: Ventas del Mes --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-panel kpi-month">
                <div class="kpi-icon-wrap">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="kpi-content">
                    <span class="kpi-label">Ventas del Mes</span>
                    <div class="kpi-value">{{ $currency ?? 'S/' }} {{ number_format($totalSalesMonth ?? 0, 0, '.', ',') }}</div>
                    <div class="kpi-sub">
                        <i class="bi bi-bullseye me-1"></i>
                        Meta: {{ $currency ?? 'S/' }} {{ number_format($monthlyGoal ?? 5000, 0, '.', ',') }}
                    </div>
                </div>
                <div class="kpi-badge">MES</div>
            </div>
        </div>

        {{-- KPI 4: Stock Bajo --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-panel kpi-stock {{ ($lowStockProducts ?? 0) > 0 ? 'kpi-alert' : 'kpi-ok' }}">
                <div class="kpi-icon-wrap">
                    <i class="bi bi-{{ ($lowStockProducts ?? 0) > 0 ? 'exclamation-triangle-fill' : 'box-seam-fill' }}"></i>
                </div>
                <div class="kpi-content">
                    <span class="kpi-label">Alertas de Stock</span>
                    <div class="kpi-value">{{ $lowStockProducts ?? 0 }}</div>
                    <div class="kpi-sub">
                        @if(($lowStockProducts ?? 0) > 0)
                            <i class="bi bi-exclamation-circle me-1"></i> Productos por reponer
                        @else
                            <i class="bi bi-check-circle me-1"></i> Inventario en buen estado
                        @endif
                    </div>
                </div>
                <a href="{{ route('inventory.logs') }}" class="kpi-badge kpi-badge-link">VER</a>
            </div>
        </div>

    </div>

    {{-- ============================================================
         FILA 1B: Meta mensual + Progreso
    ============================================================ --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="goal-bar-panel goal-level-{{ $goalLevel }} {{ $goalReached ? 'goal-completed' : '' }}">
                <div class="goal-bar-left">
                    <div class="goal-icon-wrap">
                        <i class="bi {{ $goalReached ? 'bi-trophy-fill' : 'bi-bullseye' }}"></i>
                    </div>

                    <div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="goal-bar-title">Progreso de Meta Mensual</span>

                            @if($goalReached && $goalNotificationEnabled)
                                <span class="goal-status-badge">
                                    <i class="bi bi-stars me-1"></i>
                                    {{ $goalExceeded ? 'Meta superada' : 'Meta alcanzada' }}
                                </span>
                            @endif
                        </div>

                        <span class="goal-bar-sub">
                            Logrado:
                            <strong>{{ $currency ?? 'S/' }} {{ number_format($totalSalesMonth ?? 0, 2, '.', ',') }}</strong>
                            de
                            <strong>{{ $currency ?? 'S/' }} {{ number_format($monthlyGoal ?? 5000, 2, '.', ',') }}</strong>
                        </span>
                    </div>
                </div>

                <div class="goal-bar-track-wrap">
                    <div class="goal-bar-scale">
                        <span>0%</span>
                        <span>25%</span>
                        <span>50%</span>
                        <span>75%</span>
                        <span>100%</span>
                    </div>

                    <div class="goal-bar-track">
                        <div
                            class="goal-bar-fill goal-fill-{{ $goalLevel }}"
                            style="width: {{ $safeGoalPercent }}%;"
                        >
                            <span class="goal-bar-pct">{{ number_format($rawGoalPercent, 0) }}%</span>
                        </div>

                        <span class="goal-milestone milestone-25"></span>
                        <span class="goal-milestone milestone-50"></span>
                        <span class="goal-milestone milestone-75"></span>
                    </div>

                    <div class="goal-progress-message">
                        @if($rawGoalPercent >= 100)
                            <i class="bi bi-check-circle-fill me-1"></i>
                            {{ $goalExceeded
                                ? 'Excelente trabajo: ya superaste la meta mensual.'
                                : 'Felicitaciones: alcanzaste la meta mensual.' }}
                        @elseif($rawGoalPercent >= 75)
                            <i class="bi bi-rocket-takeoff-fill me-1"></i>
                            Estás muy cerca de alcanzar la meta.
                        @elseif($rawGoalPercent >= 50)
                            <i class="bi bi-graph-up-arrow me-1"></i>
                            Vas por buen camino: ya superaste la mitad.
                        @elseif($rawGoalPercent >= 25)
                            <i class="bi bi-arrow-up-circle-fill me-1"></i>
                            Buen avance, sigue impulsando las ventas.
                        @else
                            <i class="bi bi-hourglass-split me-1"></i>
                            El mes recién comienza: aún hay mucho margen para crecer.
                        @endif
                    </div>
                </div>

                <a href="{{ route('reports.index') }}" class="goal-bar-btn">
                    <i class="bi bi-bar-chart-line-fill me-1"></i>
                    Ver Reportes
                </a>
            </div>
        </div>
    </div>

    @if($goalReached && $goalNotificationEnabled)
        <div
            class="toast-container position-fixed top-0 end-0 p-3"
            style="z-index:1085;"
        >
            <div
                id="monthlyGoalToast"
                class="toast goal-toast border-0 show"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
                data-bs-autohide="false" style="display:block;"
            >
                <div class="toast-header goal-toast-header">
                    <div class="goal-toast-icon me-2">
                        <i class="bi bi-trophy-fill"></i>
                    </div>

                    <strong class="me-auto">
                        {{ $goalExceeded ? '¡Meta superada!' : '¡Meta alcanzada!' }}
                    </strong>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="toast"
                        aria-label="Cerrar"
                    ></button>
                </div>

                <div class="toast-body">
                    <div class="fw-bold mb-1">
                        {{ $goalExceeded
                            ? '¡Felicitaciones! Las ventas superaron el objetivo mensual.'
                            : '¡Felicitaciones! Se alcanzó el objetivo mensual de ventas.' }}
                    </div>

                    <div class="small mb-3">
                        Logrado:
                        <strong>
                            {{ $currency ?? 'S/' }}
                            {{ number_format($totalSalesMonth ?? 0, 2, '.', ',') }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         FILA 2: Actividad Anual | Ingreso Actual
    ============================================================ --}}
    <div class="row g-3">

        {{-- Gráfico Actividad Anual --}}
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                    <span class="dashboard-section-title">
                        <i class="bi bi-bar-chart-line-fill"></i>
                        <span>Actividad del Año</span>
                    </span>
                </div>
                <div class="card-body p-3" style="height:280px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Donut: Ingreso Actual --}}
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                    <span class="dashboard-section-title">
                        <i class="bi bi-wallet2"></i>
                        <span>Ingreso Actual</span>
                    </span>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3 gap-3">
                    {{-- Donut --}}
                    <div style="position:relative;width:150px;height:150px;">
                        <canvas id="donutChart"></canvas>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                            <div style="font-size:1.7rem;font-weight:800;color:var(--dark-bg);line-height:1;">{{ $safeGoalPercent }}%</div>
                            <div style="font-size:.65rem;color:var(--text-muted);font-weight:600;">de meta</div>
                        </div>
                    </div>

                    {{-- Stat --}}
                    <div class="text-center">
                        <div class="current-income-stat">Meta mensual: <strong class="current-income-value">{{ $currency ?? 'S/' }} {{ number_format($monthlyGoal ?? 5000, 0,'.',',') }}</strong></div>
                        <div style="font-size:.7rem;color:var(--text-muted);margin-top:2px;">Logrado: {{ $currency ?? 'S/' }} {{ number_format($totalSalesMonth ?? 0, 0,'.',',') }}</div>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2 w-100">
                        <a href="{{ route('reports.index') }}" class="btn dashboard-primary-btn flex-grow-1 py-2" style="font-size:.8rem;">
                            <i class="bi bi-eye me-1"></i>Ver más
                        </a>
                        <a
    href="{{ route('reports.export.pdf') }}" target="_blank"
    class="btn dashboard-export-btn flex-grow-1 py-2"
    style="font-size:.8rem;"
>
    <i class="bi bi-file-earmark-pdf me-1"></i>
    Exportar PDF
</a>
                    </div>

                    <p style="font-size:.68rem;color:var(--text-muted);text-align:center;margin:0;">
                        Progreso mensual basado en las ventas registradas en el sistema.
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================
         FILA 3: Estado de Salones + Más Vendidos
    ============================================================ --}}
    <div class="row g-3 mt-0">

        <div class="col-lg-8">
            <div class="card dashboard-rooms-card">

                {{-- Encabezado --}}
                <div class="card-header dashboard-rooms-header">
                    <div class="dashboard-rooms-title dashboard-section-title">
                        <i class="bi bi-grid-3x3-gap-fill"></i>

                        <div>
                            <h6>Estado de Salones</h6>
                            <small>Disponibilidad actual de las mesas</small>
                        </div>
                    </div>

                    <div class="dashboard-rooms-legend">
                        <span class="rooms-legend-item is-available">
                            <span class="rooms-legend-dot"></span>
                            Disponible
                        </span>

                        <span class="rooms-legend-item is-busy">
                            <span class="rooms-legend-dot"></span>
                            Ocupada
                        </span>
                    </div>
                </div>

                <div class="card-body dashboard-rooms-body">

                    @if(isset($areas) && count($areas) > 0)

                        {{-- Salones --}}
                        <ul
                            class="nav nav-pills dashboard-room-tabs"
                            id="pills-tab"
                            role="tablist"
                        >
                            @foreach($areas as $index => $area)

                                <li
                                    class="nav-item"
                                    role="presentation"
                                >
                                    <button
                                        class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                        id="pills-{{ $area->id }}-tab"
                                        data-bs-toggle="pill"
                                        data-bs-target="#pills-{{ $area->id }}"
                                        type="button"
                                    >
                                        {{ $area->name }}

                                        <span class="room-table-count">
                                            {{ $area->tables->count() }}
                                        </span>
                                    </button>
                                </li>

                            @endforeach
                        </ul>


                        {{-- Mesas --}}
                        <div class="tab-content">

                            @foreach($areas as $index => $area)

                                <div
                                    class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                    id="pills-{{ $area->id }}"
                                >

                                    <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3">

                                        @foreach($area->tables as $table)

                                            @php
                                                $isBusy = $table->orders->count() > 0;
                                            @endphp

                                            <div class="col">

                                                <a
                                                    href="{{ route('pos.order', $table->id) }}"
                                                    class="dashboard-table-link"
                                                >

                                                    <div
                                                        class="dashboard-table-card {{ $isBusy ? 'is-busy' : 'is-available' }}"
                                                    >

                                                        <div class="dashboard-table-top">

                                                            <div class="dashboard-table-icon">
                                                                <i class="bi {{ $isBusy ? 'bi-person-fill' : 'bi-check-lg' }}"></i>
                                                            </div>

                                                            <span class="dashboard-table-status">
                                                                {{ $isBusy ? 'Ocupada' : 'Disponible' }}
                                                            </span>

                                                        </div>


                                                        <div class="dashboard-table-info">

                                                            <h6>
                                                                {{ $table->name }}
                                                            </h6>

                                                            @if($isBusy)

                                                                <div class="dashboard-table-detail">
                                                                    <span>Consumo actual</span>

                                                                    <strong>
                                                                        {{ $currency ?? 'S/' }}
                                                                        {{ number_format($table->orders->first()->total, 2) }}
                                                                    </strong>
                                                                </div>

                                                            @else

                                                                <div class="dashboard-table-detail">
                                                                    <span>Estado</span>
                                                                    <strong>Libre</strong>
                                                                </div>

                                                            @endif

                                                        </div>

                                                        <div class="dashboard-table-arrow">
                                                            <i class="bi bi-chevron-right"></i>
                                                        </div>

                                                    </div>

                                                </a>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="dashboard-rooms-empty">
                            <i class="bi bi-grid-3x3-gap"></i>

                            <strong>
                                No hay áreas configuradas
                            </strong>

                            <span>
                                Las áreas y mesas aparecerán aquí.
                            </span>
                        </div>

                    @endif

                </div>

            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header py-3 px-4 dashboard-top-header">
                    <h6 class="fw-bold mb-0 dashboard-section-title">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Más Vendidos</span>
                    </h6>
                </div>
                <div class="card-body px-0 py-2">
                    <div class="list-group list-group-flush">
                        @forelse($topProducts ?? [] as $product)
                            <div class="list-group-item border-0 d-flex align-items-center px-4 py-2 top-product-item">
                                <div class="me-3 position-relative flex-shrink-0">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="rounded-3 shadow-sm"
                                             width="48" height="48" style="object-fit:cover;">
                                    @else
                                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                                             style="width:48px;height:48px;background:var(--light-bg);color:var(--accent-1);">
                                            <i class="bi bi-image fs-5"></i>
                                        </div>
                                    @endif
                                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill border border-white"
                                          style="background:var(--primary);font-size:.6rem;">
                                        #{{ $loop->iteration }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark text-truncate" style="font-size:.84rem;">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->total_qty }} unidades</small>
                                </div>
                                @if($loop->iteration === 1)
                                    <i class="bi bi-award-fill ms-2" style="color:#d4af37;font-size:1.1rem;"></i>
                                @elseif($loop->iteration === 2)
                                    <i class="bi bi-award-fill ms-2" style="color:#9ca3af;font-size:1.1rem;"></i>
                                @elseif($loop->iteration === 3)
                                    <i class="bi bi-award-fill ms-2" style="color:#cd7f32;font-size:1.1rem;"></i>
                                @else
                                    <span class="ms-2 fw-bold text-muted" style="font-size:.75rem;">
                                        #{{ $loop->iteration }}
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">Sin datos de ventas aún.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Dashboard-specific styles --}}
<style>
    /*
    |--------------------------------------------------------------------------
    | VARIABLES LOCALES DEL DASHBOARD
    |--------------------------------------------------------------------------
    | Todas parten del tema seleccionado en Configuración.
    */
    .container-fluid {
        --dash-primary: var(--primary, #ff8c00);
        --dash-primary-hover: var(--primary-hover, #e07b00);
        --dash-dark: var(--dark-bg, #063970);
        --dash-dark-2: var(--dark-bg-2, #0b4f8a);
        --dash-light: var(--light-bg, #eef8fc);
        --dash-card: var(--card-bg, #ffffff);
        --dash-text: var(--text-main, #172033);
        --dash-muted: var(--text-muted, #64748b);
        --dash-border: var(--border-soft, #dce7f1);
        --dash-accent-1: var(--accent-1, #0b84c6);
        --dash-accent-2: var(--accent-2, #16a34a);
        --dash-accent-3: var(--accent-3, #ff8c00);
        --dash-accent-4: var(--accent-4, #06b6d4);
    }

    /* Cards generales del dashboard */
    .container-fluid > .row .card {
        background: var(--dash-card);
        border-color: var(--dash-border);
        box-shadow: 0 2px 14px color-mix(in srgb, var(--dash-dark) 7%, transparent);
    }

    .container-fluid > .row .card-header {
        background: var(--dash-card);
        border-bottom-color: var(--dash-border);
        color: var(--dash-text);
    }

    /* ── KPI Panels ───────────────────────────────────────── */
    .kpi-panel {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
        border-radius: 16px;
        background: var(--dash-card);
        box-shadow: 0 2px 14px color-mix(in srgb, var(--dash-dark) 7%, transparent);
        position: relative;
        overflow: hidden;
        transition: transform .22s, box-shadow .22s;
        border: 1px solid var(--dash-border);
        height: 100%;
    }

    .kpi-panel:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px color-mix(in srgb, var(--dash-dark) 14%, transparent);
    }

    .kpi-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        border-radius: 16px 0 0 16px;
    }

    .kpi-sales::before {
    background: linear-gradient(
        180deg,
        var(--dash-primary) 0%,
        color-mix(in srgb, var(--dash-primary) 25%, var(--card-bg, white)) 65%,
        white 100%
    );
}

    .kpi-tables::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-1) 0%,
        color-mix(in srgb, var(--dash-accent-1) 25%, var(--card-bg, white)) 65%,
        white 100%
    );
}

    .kpi-month::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-2) 0%,
        color-mix(in srgb, var(--dash-accent-2) 25%, var(--card-bg, white)) 65%,
        white 100%
    );
}

    /* Stock conserva rojo cuando realmente existe una alerta. */
    .kpi-stock.kpi-alert::before {
    background: linear-gradient(
        180deg,
        #ef4444 0%,
        #fca5a5 60%,
        white 100%
    );
}

    .kpi-stock.kpi-ok::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-4) 0%,
        color-mix(in srgb, var(--dash-accent-4) 25%, var(--card-bg, white)) 65%,
        white 100%
    );
}

    .kpi-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .kpi-sales .kpi-icon-wrap {
        background: color-mix(in srgb, var(--dash-primary) 12%, var(--card-bg, white));
        color: var(--dash-primary);
    }

    .kpi-tables .kpi-icon-wrap {
        background: color-mix(in srgb, var(--dash-accent-1) 12%, var(--card-bg, white));
        color: var(--dash-accent-1);
    }

    .kpi-month .kpi-icon-wrap {
        background: color-mix(in srgb, var(--dash-accent-2) 12%, var(--card-bg, white));
        color: var(--dash-accent-2);
    }

    .kpi-stock.kpi-alert .kpi-icon-wrap {
        background: var(--surface-red, #fff1f2);
        color: #ef4444;
    }

    .kpi-stock.kpi-ok .kpi-icon-wrap {
        background: color-mix(in srgb, var(--dash-accent-4) 12%, var(--card-bg, white));
        color: var(--dash-accent-4);
    }

    .kpi-content {
        flex: 1;
        min-width: 0;
    }

    .kpi-label {
        display: block;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--dash-muted);
        margin-bottom: 4px;
    }

    .kpi-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--dash-text);
        line-height: 1.1;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kpi-sub {
        font-size: .72rem;
        color: var(--dash-muted);
        display: flex;
        align-items: center;
    }

    .kpi-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .08em;
        padding: 3px 8px;
        border-radius: 20px;
        background: color-mix(in srgb, var(--dash-accent-1) 9%, var(--card-bg, white));
        color: var(--dash-dark);
        text-decoration: none;
    }

    .kpi-badge-link:hover {
        background: var(--dash-primary);
        color: #fff;
    }

    /* ── Goal Progress Bar ───────────────────────────────── */
    .goal-bar-panel {
        background: var(--dash-card);
        border-radius: 16px;
        border: 1px solid var(--dash-border);
        box-shadow: 0 2px 14px color-mix(in srgb, var(--dash-dark) 7%, transparent);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
        transition: border-color .25s, box-shadow .25s, transform .25s;
    }

    .goal-bar-panel.goal-completed {
        border-color: color-mix(in srgb, var(--dash-primary) 35%, var(--dash-border));
        box-shadow:
            0 8px 28px color-mix(in srgb, var(--dash-primary) 14%, transparent),
            inset 0 0 0 1px color-mix(in srgb, var(--dash-primary) 8%, transparent);
    }

    .goal-bar-panel.goal-completed::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 12% 30%, color-mix(in srgb, var(--dash-primary) 13%, transparent) 0 3px, transparent 4px),
            radial-gradient(circle at 88% 25%, color-mix(in srgb, var(--dash-accent-1) 14%, transparent) 0 3px, transparent 4px),
            radial-gradient(circle at 75% 78%, color-mix(in srgb, var(--dash-accent-3) 13%, transparent) 0 2px, transparent 3px);
        opacity: .9;
    }

    .goal-bar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .goal-icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
        background: color-mix(in srgb, var(--dash-primary) 12%, var(--card-bg, white));
        color: var(--dash-primary);
        transition: background .25s, color .25s, transform .25s;
    }

    .goal-completed .goal-icon-wrap {
        background: linear-gradient(135deg, var(--dash-primary), var(--dash-accent-3));
        color: #fff;
        transform: rotate(-4deg) scale(1.04);
        box-shadow: 0 6px 18px color-mix(in srgb, var(--dash-primary) 24%, transparent);
    }

    .goal-bar-title {
        display: block;
        font-size: .82rem;
        font-weight: 700;
        color: var(--dash-text);
    }

    .goal-bar-sub {
        display: block;
        font-size: .72rem;
        color: var(--dash-muted);
        margin-top: 2px;
    }

    .goal-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .02em;
        color: #fff;
        background: linear-gradient(135deg, var(--dash-primary), var(--dash-accent-3));
        box-shadow: 0 4px 12px color-mix(in srgb, var(--dash-primary) 18%, transparent);
    }

    .goal-bar-track-wrap {
        flex: 1;
        min-width: 210px;
        position: relative;
        z-index: 1;
    }

    .goal-bar-scale {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
        color: var(--dash-muted);
        font-size: .58rem;
        font-weight: 700;
    }

    .goal-bar-track {
        width: 100%;
        height: 16px;
        background: color-mix(in srgb, var(--dash-accent-1) 9%, var(--card-bg, white));
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0,0,0,.06);
    }

    .goal-bar-fill {
        height: 100%;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
        transition:
            width .75s ease,
            background .35s ease,
            box-shadow .35s ease;
        min-width: 36px;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .goal-fill-start {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-accent-4) 18%, var(--card-bg, white)) 22%,
        var(--dash-accent-4) 100%
    );
}

    .goal-fill-low {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-accent-1) 18%, var(--card-bg, white)) 22%,
        var(--dash-accent-1) 100%
    );
}

    .goal-fill-medium {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-accent-2) 18%, var(--card-bg, white)) 22%,
        var(--dash-accent-2) 100%
    );
}

    .goal-fill-high {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-primary) 18%, var(--card-bg, white)) 22%,
        var(--dash-primary) 100%
    );
}

    .goal-fill-complete {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-primary) 22%, var(--card-bg, white)) 20%,
        var(--dash-primary) 100%
    );

    background-size: 180% 100%;

    animation:
        goalGradientMove 2.4s
        ease-in-out
        infinite;

    box-shadow:
        0 0 14px
        color-mix(
            in srgb,
            var(--dash-primary) 28%,
            transparent
        );
}

    .goal-fill-complete::after {
    display: none !important;
    animation: none !important;
}

    .goal-bar-pct {
        font-size: .62rem;
        font-weight: 800;
        color: #fff;
        white-space: nowrap;
        position: relative;
        z-index: 2;
        text-shadow: 0 1px 2px rgba(0,0,0,.18);
    }

    .goal-milestone {
        position: absolute;
        top: 50%;
        width: 2px;
        height: 9px;
        border-radius: 10px;
        background: rgba(255,255,255,.72);
        transform: translate(-50%, -50%);
        z-index: 2;
        pointer-events: none;
    }

    .milestone-25 { left: 25%; }
    .milestone-50 { left: 50%; }
    .milestone-75 { left: 75%; }

    .goal-progress-message {
        margin-top: 7px;
        color: var(--dash-muted);
        font-size: .67rem;
        font-weight: 600;
    }

    .goal-completed .goal-progress-message {
        color: var(--dash-primary);
        font-weight: 800;
    }

    .goal-bar-btn,
    .dashboard-primary-btn {
        background: var(--dash-primary) !important;
        border-color: var(--dash-primary) !important;
        color: #fff !important;
        transition: transform .2s, box-shadow .2s, background .2s;
        position: relative;
        z-index: 1;
    }

    .goal-bar-btn {
        flex-shrink: 0;
        border-radius: 10px;
        padding: 8px 18px;
        font-size: .8rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .goal-bar-btn:hover,
    .dashboard-primary-btn:hover {
        background: var(--dash-primary-hover) !important;
        border-color: var(--dash-primary-hover) !important;
        transform: translateY(-1px);
        color: #fff !important;
    }

    .goal-toast {
        min-width: 340px;
        max-width: 390px;
        border-radius: 16px;
        overflow: hidden;
        background: var(--dash-card);
        box-shadow: 0 16px 42px rgba(0,0,0,.18);
    }

    .goal-toast-header {
        border: 0;
        background: linear-gradient(
            135deg,
            color-mix(in srgb, var(--dash-primary) 12%, var(--card-bg, white)),
            color-mix(in srgb, var(--dash-accent-1) 10%, var(--card-bg, white))
        );
        color: var(--dash-text);
    }

    .goal-toast-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--dash-primary), var(--dash-accent-3));
        color: #fff;
        box-shadow: 0 4px 12px color-mix(in srgb, var(--dash-primary) 22%, transparent);
    }

    .goal-toast .toast-body {
        color: var(--dash-text);
        background: var(--dash-card);
        border-top: 1px solid var(--dash-border);
    }

    @keyframes goalGradientMove {
        0%   { background-position: 0% 50%; }
        100% { background-position: 220% 50%; }
    }

    @keyframes goalShine {
        0%   { transform: translateX(-110%); }
        55%,
        100% { transform: translateX(130%); }
    }

    /* ── Table cards ───────────────────────────────────────── */
    .table-hover-card {
        border-radius: 14px !important;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04) !important;
    }

    .table-hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px color-mix(in srgb, var(--dash-dark) 13%, transparent) !important;
    }

    /* Estados ocupada/disponible conservan semántica verde/rojo. */
    .nav-pills .nav-link {
        color: var(--dash-muted);
        background: var(--dash-card);
        border-color: var(--dash-border);
        font-size: .84rem;
    }

    .nav-pills .nav-link.active {
        background: var(--dash-primary);
        color: #fff;
        border-color: var(--dash-primary);
        box-shadow: 0 4px 14px color-mix(in srgb, var(--dash-primary) 30%, transparent);
    }

    /* Top products */
    .top-product-item {
        transition: background .15s;
        border-radius: 10px;
    }

    .top-product-item:hover {
        background: var(--dash-light);
    }

    /* Textos internos de cards */
    .container-fluid .text-muted {
        color: var(--dash-muted) !important;
    }

    /* Responsive */
    @media (max-width: 767.98px) {
        .goal-toast {
            min-width: 0;
            width: calc(100vw - 32px);
        }

        .goal-bar-panel {
            padding: 16px;
        }

        .goal-bar-track-wrap {
            width: 100%;
            flex-basis: 100%;
        }

        .goal-bar-btn {
            width: 100%;
            text-align: center;
        }
    }

    /* Cuando el usuario cierra la felicitación,
       la barra queda completa pero deja de moverse */
    .goal-fill-complete.goal-animation-paused {
    animation: none !important;
    background-position: 100% 50% !important;
}

    .goal-fill-complete.goal-animation-paused::after {
        animation: none !important;
        display: none;
    }


    /* BOTÓN VER MÁS
       Usa el color principal del tema */
    .dashboard-primary-btn {
        background: var(--dash-primary) !important;
        border-color: var(--dash-primary) !important;
        color: #fff !important;
    }

    .dashboard-primary-btn:hover {
        background: var(--dash-primary-hover) !important;
        border-color: var(--dash-primary-hover) !important;
        color: #fff !important;
    }


    /* BOTÓN EXPORTAR PDF
       Usa otra combinación del mismo tema */
    a.dashboard-export-btn {
        background: var(--dash-accent-1) !important;
        border-color: var(--dash-accent-1) !important;
        color: #fff !important;

        box-shadow:
            0 4px 12px
            color-mix(
                in srgb,
                var(--dash-accent-1) 20%,
                transparent
            );

        transition:
            transform .2s,
            box-shadow .2s,
            filter .2s;
    }

    a.dashboard-export-btn:hover {
        background: var(--dash-accent-1) !important;
        border-color: var(--dash-accent-1) !important;
        color: #fff !important;

        filter: brightness(.9);
        transform: translateY(-1px);

        box-shadow:
            0 6px 16px
            color-mix(
                in srgb,
                var(--dash-accent-1) 28%,
                transparent
            );
    }


    /* ========================================================
       CONFETIS DE META ALCANZADA
       ======================================================== */

    .goal-confetti-piece {
        position: fixed;
        top: -20px;
        display: block;
        pointer-events: none;
        z-index: 99999;

        animation-name: goalConfettiFall;
        animation-timing-function: linear;
        animation-fill-mode: forwards;

        box-shadow:
            0 2px 4px
            rgba(0,0,0,.08);
    }

    @keyframes goalConfettiFall {

        0% {
            transform:
                translateY(-20px)
                rotate(0deg);

            opacity: 1;
        }

        80% {
            opacity: 1;
        }

        100% {
            transform:
                translateY(110vh)
                rotate(900deg);

            opacity: 0;
        }
    }


    /* ============================================
       NOTIFICACIÓN DE META - EFECTO LATIDO
       ============================================ */

    .goal-toast.show {
        animation: goalToastPulse 1.4s ease-in-out infinite;
        transform-origin: center;
    }

    @keyframes goalToastPulse {
        0% {
            transform: scale(1);
            box-shadow: 0 16px 42px rgba(0,0,0,.18);
        }

        50% {
            transform: scale(1.035);
            box-shadow:
                0 18px 48px
                color-mix(
                    in srgb,
                    var(--dash-primary) 32%,
                    rgba(0,0,0,.15)
                );
        }

        100% {
            transform: scale(1);
            box-shadow: 0 16px 42px rgba(0,0,0,.18);
        }
    }

    .goal-toast.goal-toast-stopped {
        animation: none !important;
    }


    .dashboard-top-header {
        position: relative;
        overflow: hidden;
    }

    .dashboard-top-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(
            180deg,
            var(--dash-primary),
            white
        );
    }

    .dashboard-top-header .bi-trophy-fill {
        color: var(--dash-primary);
    }

    .top-product-item {
        transition:
            transform .18s ease,
            background-color .18s ease;
    }

    .top-product-item:hover {
        transform: translateX(3px);
    }

    .top-product-item:nth-child(1) .badge {
        background: var(--dash-primary) !important;
    }

    .top-product-item:nth-child(2) .badge {
        background: var(--dash-accent-1) !important;
    }

    .top-product-item:nth-child(3) .badge {
        background: var(--dash-accent-2) !important;
    }

    .top-product-item:nth-child(4) .badge {
        background: var(--dash-accent-4) !important;
    }

    .top-product-item:nth-child(5) .badge {
        background: var(--dash-dark-2) !important;
    }

</style>


<style>
/* ============================================================
   MAS VENDIDOS - MODO OSCURO
============================================================ */

html[data-color-mode="dark"]
.dashboard-top-header {

    background:
        #111e30 !important;

    border-bottom:
        1px solid #26384d !important;
}


html[data-color-mode="dark"]
.dashboard-top-header h6 {

    color:
        #f3f7fb !important;
}


html[data-color-mode="dark"]
.dashboard-top-header
.bi-trophy-fill {

    color:
        var(--dash-primary) !important;
}


/* ------------------------------------------------------------
   CONTENEDOR DE LA LISTA
------------------------------------------------------------ */

html[data-color-mode="dark"]
.dashboard-top-header
+ .card-body {

    background:
        #111e30 !important;
}


html[data-color-mode="dark"]
.dashboard-top-header
+ .card-body
.list-group {

    background:
        transparent !important;
}


/* ------------------------------------------------------------
   CADA PRODUCTO
------------------------------------------------------------ */

html[data-color-mode="dark"]
.top-product-item {

    position:
        relative;

    background:
        transparent !important;

    border:
        0 !important;

    border-radius:
        0 !important;

    color:
        #dce7f2 !important;

    transition:
        background-color .18s ease,
        transform .18s ease !important;
}


/*
 * Separador entre productos.
 */

html[data-color-mode="dark"]
.top-product-item:not(:last-child)::after {

    content: "";

    position:
        absolute;

    left:
        84px;

    right:
        22px;

    bottom:
        0;

    height:
        1px;

    background:
        #22354b;
}


/* Hover */

html[data-color-mode="dark"]
.top-product-item:hover {

    background:
        #16263a !important;

    transform:
        translateX(2px);
}


/* ------------------------------------------------------------
   NOMBRE DEL PRODUCTO
------------------------------------------------------------ */

html[data-color-mode="dark"]
.top-product-item
.fw-bold {

    color:
        #eef5fb !important;

    font-weight:
        700 !important;
}


/* ------------------------------------------------------------
   CANTIDAD
------------------------------------------------------------ */

html[data-color-mode="dark"]
.top-product-item
.text-muted {

    color:
        #8ea4ba !important;
}


/* ------------------------------------------------------------
   IMAGEN
------------------------------------------------------------ */

html[data-color-mode="dark"]
.top-product-item img {

    border:
        1px solid
        rgba(255,255,255,.08);

    box-shadow:
        0 5px 13px
        rgba(0,0,0,.18) !important;
}


/*
 * Placeholder si el producto no tiene imagen.
 */

html[data-color-mode="dark"]
.top-product-item
.rounded-3.d-flex {

    background:
        #192a3e !important;

    color:
        var(--dash-accent-1) !important;

    border:
        1px solid #293d54;
}


/* ------------------------------------------------------------
   NUMERO DE POSICION
------------------------------------------------------------ */

html[data-color-mode="dark"]
.top-product-item
.badge {

    border-color:
        #111e30 !important;

    color:
        #ffffff !important;

    box-shadow:
        0 3px 8px
        rgba(0,0,0,.16);
}


/* #1 */

html[data-color-mode="dark"]
.top-product-item:nth-child(1)
.badge {

    background:
        var(--dash-primary) !important;
}


/* #2 */

html[data-color-mode="dark"]
.top-product-item:nth-child(2)
.badge {

    background:
        var(--dash-accent-1) !important;
}


/* #3 */

html[data-color-mode="dark"]
.top-product-item:nth-child(3)
.badge {

    background:
        var(--dash-accent-2) !important;
}


/* #4 */

html[data-color-mode="dark"]
.top-product-item:nth-child(4)
.badge {

    background:
        var(--dash-accent-4) !important;
}


/* #5 */

html[data-color-mode="dark"]
.top-product-item:nth-child(5)
.badge {

    background:
        #1686bb !important;
}


/* ------------------------------------------------------------
   MEDALLAS / POSICION DERECHA
------------------------------------------------------------ */

html[data-color-mode="dark"]
.top-product-item
.ms-auto {

    color:
        #8da4ba !important;
}


/* ------------------------------------------------------------
   PRIMEROS TRES PUESTOS
------------------------------------------------------------ */

html[data-color-mode="dark"]
.top-product-item:nth-child(1)
.ms-auto {

    color:
        #e0b229 !important;
}


html[data-color-mode="dark"]
.top-product-item:nth-child(2)
.ms-auto {

    color:
        #aeb9c5 !important;
}


html[data-color-mode="dark"]
.top-product-item:nth-child(3)
.ms-auto {

    color:
        #d98232 !important;
}

</style>

<style>
/* ============================================================
   DASHBOARD DARK - LEGIBILIDAD FINAL
============================================================ */

/* Superficies */

html[data-color-mode="dark"] .card,
html[data-color-mode="dark"] .kpi-panel,
html[data-color-mode="dark"] .goal-bar-panel {

    background:
        #111f32 !important;

    border-color:
        #2a3e55 !important;
}


/* ============================================================
   KPI
============================================================ */

html[data-color-mode="dark"] .kpi-label {

    color:
        #8fb4d5 !important;

    opacity:
        1 !important;
}


html[data-color-mode="dark"] .kpi-value {

    color:
        #ffffff !important;

    opacity:
        1 !important;
}


html[data-color-mode="dark"] .kpi-sub {

    color:
        #9fb3c8 !important;

    opacity:
        1 !important;
}


html[data-color-mode="dark"] .kpi-sub strong {

    color:
        #dce8f3 !important;
}


/* ============================================================
   META MENSUAL
============================================================ */

html[data-color-mode="dark"] .goal-bar-title {

    color:
        #ffffff !important;
}


html[data-color-mode="dark"] .goal-bar-sub {

    color:
        #a5b9cc !important;
}


html[data-color-mode="dark"] .goal-bar-sub strong {

    color:
        #e5eef7 !important;
}


html[data-color-mode="dark"] .goal-bar-scale {

    color:
        #9bb0c4 !important;

    opacity:
        1 !important;
}


html[data-color-mode="dark"] .goal-progress-message {

    color:
        #9fb3c6 !important;

    opacity:
        1 !important;
}


/* ============================================================
   ENCABEZADOS DE CARDS
============================================================ */

html[data-color-mode="dark"] .card-header {

    background:
        #111f32 !important;

    border-bottom:
        1px solid #2a3e55 !important;
}


html[data-color-mode="dark"] .card-header h6,
html[data-color-mode="dark"] .card-header .fw-bold {

    color:
        #f8fbff !important;

    opacity:
        1 !important;
}


/* ============================================================
   TEXTOS SECUNDARIOS
============================================================ */

html[data-color-mode="dark"]
.container-fluid .text-muted {

    color:
        #9db2c7 !important;

    opacity:
        1 !important;
}


html[data-color-mode="dark"]
.card-body small {

    color:
        #9db2c7;
}


/* ============================================================
   ESTADO DE SALONES
============================================================ */

html[data-color-mode="dark"]
.card-body .fw-semibold {

    color:
        #eef5fb !important;
}


html[data-color-mode="dark"]
.card-body .small.text-muted {

    color:
        #9fb4c9 !important;

    opacity:
        1 !important;
}


/* Tabs Salón Principal / Terraza */

html[data-color-mode="dark"]
.nav-pills .nav-link:not(.active) {

    color:
        #d5e1ec !important;

    border-color:
        #6f879e !important;

    background:
        #15263a !important;
}


html[data-color-mode="dark"]
.nav-pills .nav-link:not(.active):hover {

    background:
        #1b3048 !important;

    color:
        #ffffff !important;

    border-color:
        #8ca3b8 !important;
}


/* ============================================================
   INGRESO ACTUAL
============================================================ */



/*
 * Textos cercanos al donut.
 */

html[data-color-mode="dark"]
#donutChart ~ * {

    color:
        #a8bccf;
}


/* ============================================================
   MAS VENDIDOS
   Mantener lo que ya funciona
============================================================ */

html[data-color-mode="dark"]
.top-product-item .fw-bold {

    color:
        #f4f8fc !important;
}


html[data-color-mode="dark"]
.top-product-item .text-muted {

    color:
        #a6bbcf !important;
}


/* ============================================================
   CONTRASTE DE ICONOS
============================================================ */

html[data-color-mode="dark"]
.bi-check-circle-fill {

    filter:
        brightness(1.08);
}


/* ============================================================
   GRÁFICOS
   Canvas conserva sus colores pero gana visibilidad.
============================================================ */

html[data-color-mode="dark"]
canvas {

    opacity:
        1 !important;
}


/* ============================================================
   BOTONES
============================================================ */

html[data-color-mode="dark"]
.dashboard-primary-btn,
html[data-color-mode="dark"]
.dashboard-export-btn,
html[data-color-mode="dark"]
.goal-bar-btn {

    color:
        #ffffff !important;

    opacity:
        1 !important;
}


/* ============================================================
   NO OSCURECER EXCESIVAMENTE ELEMENTOS INTERNOS
============================================================ */

html[data-color-mode="dark"]
.card-body {

    color:
        #dce7f2;
}

</style>

<style>
/* ============================================================
   MESAS DASHBOARD - CONTRASTE DARK
============================================================ */

/*
 * Nombre: Mesa 1, Mesa 2...
 */
html[data-color-mode="dark"]
.table-item .fw-semibold,
html[data-color-mode="dark"]
.table-item .fw-bold {

    color: #f5f9fd !important;
    opacity: 1 !important;
}


/*
 * Estado: Libre / Ocupada
 */
html[data-color-mode="dark"]
.table-item .text-muted,
html[data-color-mode="dark"]
.table-item small {

    color: #a9bed2 !important;
    opacity: 1 !important;
}


/*
 * Cada mesa gana una superficie muy sutil para
 * distinguirse del fondo sin convertirla en una tarjeta pesada.
 */
html[data-color-mode="dark"]
.table-item {

    border-radius: 14px;

    transition:
        background-color .18s ease,
        transform .18s ease;
}


html[data-color-mode="dark"]
.table-item:hover {

    background: rgba(255,255,255,.035);

    transform: translateY(-1px);
}


/*
 * Icono disponible.
 */
html[data-color-mode="dark"]
.table-item .text-success {

    color: #32d583 !important;
}


/*
 * Icono ocupado.
 */
html[data-color-mode="dark"]
.table-item .text-danger {

    color: #fb7185 !important;
}


/*
 * Texto disponible.
 */
html[data-color-mode="dark"]
.table-item .text-success + small,
html[data-color-mode="dark"]
.table-item .status-available {

    color: #8de7b5 !important;
}


/*
 * Texto ocupado.
 */
html[data-color-mode="dark"]
.table-item .text-danger + small,
html[data-color-mode="dark"]
.table-item .status-occupied {

    color: #fda4af !important;
}


/*
 * Encabezado Estado de Salones.
 */
html[data-color-mode="dark"]
.rooms-card .card-header h6,
html[data-color-mode="dark"]
.tables-card .card-header h6 {

    color: #ffffff !important;
}


/*
 * Leyenda Disponible / Ocupada.
 */
html[data-color-mode="dark"]
.rooms-card .badge,
html[data-color-mode="dark"]
.tables-card .badge {

    opacity: 1 !important;
}

</style>

<style>
/* ============================================================
   MESAS DASHBOARD - BORDES DARK
============================================================ */

/*
 * Panel completo de Estado de Salones.
 * Borde más visible, similar al resto del dashboard.
 */
html[data-color-mode="dark"] .rooms-card,
html[data-color-mode="dark"] .tables-card {

    border:
        1px solid #38516b !important;

    box-shadow:
        0 10px 25px
        rgba(0,0,0,.14) !important;
}


/*
 * Si Estado de Salones utiliza una card normal,
 * reforzamos únicamente el panel que contiene las mesas.
 */
html[data-color-mode="dark"] .dashboard-tables-card {

    border:
        1px solid #38516b !important;
}


/*
 * Separación del encabezado.
 */
html[data-color-mode="dark"] .rooms-card .card-header,
html[data-color-mode="dark"] .tables-card .card-header,
html[data-color-mode="dark"] .dashboard-tables-card .card-header {

    border-bottom:
        1px solid #31485f !important;
}


/*
 * Cada mesa:
 * borde discreto para que su espacio pueda apreciarse
 * sin convertirlo en una tarjeta pesada.
 */
html[data-color-mode="dark"] .table-item {

    border:
        1px solid rgba(112, 143, 172, .24) !important;

    border-radius:
        12px !important;

    background:
        rgba(255,255,255,.015) !important;

    padding:
        12px 14px !important;

    transition:
        border-color .18s ease,
        background-color .18s ease,
        transform .18s ease;
}


/*
 * Al pasar el mouse se aprecia un poco más.
 */
html[data-color-mode="dark"] .table-item:hover {

    border-color:
        rgba(132, 165, 195, .48) !important;

    background:
        rgba(255,255,255,.035) !important;

    transform:
        translateY(-1px);
}

</style>

<style>
/* ============================================================
   MESAS DASHBOARD - TARJETA REAL DARK
============================================================ */

/*
 * Contenedor REAL de cada mesa.
 */
html[data-color-mode="dark"] .table-hover-card {

    background:
        #15263a !important;

    border:
        1px solid #38536d !important;

    border-radius:
        14px !important;

    box-shadow:
        0 5px 14px
        rgba(0, 0, 0, .12) !important;

    transition:
        transform .18s ease,
        border-color .18s ease,
        background-color .18s ease,
        box-shadow .18s ease !important;
}


/*
 * Hover de la mesa.
 */
html[data-color-mode="dark"] .table-hover-card:hover {

    background:
        #192d43 !important;

    border-color:
        #587694 !important;

    transform:
        translateY(-3px);

    box-shadow:
        0 9px 20px
        rgba(0, 0, 0, .20) !important;
}


/*
 * Nombre de la mesa.
 */
html[data-color-mode="dark"]
.table-hover-card h6 {

    color:
        #f4f8fc !important;

    opacity:
        1 !important;
}


/*
 * Estado Libre.
 */
html[data-color-mode="dark"]
.table-hover-card small.text-muted {

    color:
        #a9bed2 !important;

    opacity:
        1 !important;
}


/*
 * El icono verde permanece claramente visible.
 */
html[data-color-mode="dark"]
.table-hover-card .bi-check-circle-fill {

    color:
        #22d36f !important;
}


/*
 * Mesa ocupada.
 * El fondo inline #fff1f2 también queda sustituido
 * por una superficie oscura con matiz rojo.
 */
html[data-color-mode="dark"]
.table-hover-card:has(.bi-person-workspace) {

    background:
        #291b29 !important;

    border-color:
        #71394a !important;
}


html[data-color-mode="dark"]
.table-hover-card:has(.bi-person-workspace):hover {

    background:
        #32202e !important;

    border-color:
        #985064 !important;
}


/*
 * Icono de mesa ocupada.
 */
html[data-color-mode="dark"]
.table-hover-card .bi-person-workspace {

    color:
        #fb7185 !important;
}


/*
 * Evitar que Bootstrap vuelva a ocultar el borde.
 * La vista utiliza originalmente border-0.
 */
html[data-color-mode="dark"]
.card.border-0.table-hover-card {

    border:
        1px solid #38536d !important;
}


html[data-color-mode="dark"]
.card.border-0.table-hover-card:has(.bi-person-workspace) {

    border-color:
        #71394a !important;
}

</style>
@includeIf('products.create_modal')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const goalToast =
        document.getElementById('monthlyGoalToast');

    if (!goalToast) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR UNA SOLA VEZ POR DÍA
    |--------------------------------------------------------------------------
    */

    const goalStorageKey =
        "goal-toast-shown-{{ now()->format('Y-m-d') }}-{{ number_format((float) ($monthlyGoal ?? 5000), 2, '.', '') }}";

    const alreadyShown =
        localStorage.getItem(goalStorageKey) === '1';

    const completedGoalBar =
        document.querySelector('.goal-fill-complete');


    /*
    |--------------------------------------------------------------------------
    | SI YA SE MOSTRÓ HOY
    |--------------------------------------------------------------------------
    */

    if (alreadyShown) {

        goalToast.classList.remove('show');

        goalToast.style.display =
            'none';

        if (completedGoalBar) {

            completedGoalBar.classList.add(
                'goal-animation-paused'
            );

        }

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR FELICITACIÓN
    |--------------------------------------------------------------------------
    */

    goalToast.style.display =
        'block';

    goalToast.classList.add(
        'show'
    );


    /*
    |--------------------------------------------------------------------------
    | MARCAR COMO MOSTRADA HOY
    |--------------------------------------------------------------------------
    */

    localStorage.setItem(
        goalStorageKey,
        '1'
    );


    /*
    |--------------------------------------------------------------------------
    | COLORES DEL TEMA
    |--------------------------------------------------------------------------
    */

    const themeStyles =
        getComputedStyle(document.body);

    const colors = [

        themeStyles
            .getPropertyValue('--primary')
            .trim() || '#ff8c00',

        themeStyles
            .getPropertyValue('--accent-1')
            .trim() || '#0b84c6',

        themeStyles
            .getPropertyValue('--accent-2')
            .trim() || '#16a34a',

        themeStyles
            .getPropertyValue('--accent-3')
            .trim() || '#ff8c00',

        themeStyles
            .getPropertyValue('--accent-4')
            .trim() || '#06b6d4'

    ];


    /*
    |--------------------------------------------------------------------------
    | CONFETIS
    |--------------------------------------------------------------------------
    */

    const confettiEnabled =
        {{ $goalConfettiEnabled ? 'true' : 'false' }};

    let confettiInterval = null;

    let confettiActive =
        confettiEnabled;


    function createConfettiBatch() {

        if (!confettiActive) {
            return;
        }

        for (let i = 0; i < 18; i++) {

            const confetti =
                document.createElement('span');

            confetti.className =
                'goal-confetti-piece';

            confetti.style.left =
                Math.random() * 100 + 'vw';

            confetti.style.background =
                colors[
                    Math.floor(
                        Math.random() *
                        colors.length
                    )
                ];

            confetti.style.animationDuration =
                (2.8 + Math.random() * 2.4)
                + 's';

            confetti.style.animationDelay =
                (Math.random() * .5)
                + 's';

            confetti.style.width =
                (6 + Math.random() * 7)
                + 'px';

            confetti.style.height =
                (8 + Math.random() * 10)
                + 'px';

            if (Math.random() > .65) {

                confetti.style.borderRadius =
                    '50%';

            }

            document.body.appendChild(
                confetti
            );

            setTimeout(
                function () {

                    confetti.remove();

                },
                6000
            );

        }

    }


    if (confettiEnabled) {

        createConfettiBatch();

        confettiInterval =
            setInterval(
                createConfettiBatch,
                900
            );

    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR FELICITACIÓN
    |--------------------------------------------------------------------------
    */

    const closeButton =
        goalToast.querySelector(
            '.btn-close'
        );

    if (closeButton) {

        closeButton.addEventListener(
            'click',
            function () {

                /*
                 * Detener confetis
                 */

                confettiActive =
                    false;

                if (confettiInterval) {

                    clearInterval(
                        confettiInterval
                    );

                    confettiInterval =
                        null;

                }


                document
                    .querySelectorAll(
                        '.goal-confetti-piece'
                    )
                    .forEach(
                        function (piece) {

                            piece.remove();

                        }
                    );


                /*
                 * Cerrar notificación
                 */

                goalToast.classList.remove(
                    'show'
                );

                goalToast.classList.add(
                    'goal-toast-stopped'
                );

                goalToast.style.display =
                    'none';


                /*
                 * Detener animación
                 * de la barra
                 */

                if (completedGoalBar) {

                    completedGoalBar
                        .classList
                        .add(
                            'goal-animation-paused'
                        );

                }

            }
        );

    }

});
</script>


<style>
/* ============================================================
   DASHBOARD - MODO OSCURO PROFESIONAL
   Solo se activa con data-color-mode="dark"
============================================================ */

/* ------------------------------------------------------------
   VARIABLES PROPIAS DEL DASHBOARD OSCURO
------------------------------------------------------------ */

html[data-color-mode="dark"] {

    --dashboard-dark-bg: #091321;
    --dashboard-dark-surface: #111e30;
    --dashboard-dark-surface-2: #162438;

    --dashboard-dark-border: #26384d;

    --dashboard-dark-title: #f3f7fb;
    --dashboard-dark-text: #d7e2ee;
    --dashboard-dark-muted: #8298af;

    --dashboard-dark-track: #25374b;
}


/* ------------------------------------------------------------
   CARDS GENERALES
------------------------------------------------------------ */

html[data-color-mode="dark"] .dashboard-card,
html[data-color-mode="dark"] .dashboard-card.card {

    background:
        var(--dashboard-dark-surface) !important;

    border-color:
        var(--dashboard-dark-border) !important;

    color:
        var(--dashboard-dark-text) !important;

    box-shadow:
        0 12px 28px
        rgba(0, 0, 0, .15) !important;
}


/* ------------------------------------------------------------
   KPI
------------------------------------------------------------ */

html[data-color-mode="dark"] .kpi-panel {

    background:
        var(--dashboard-dark-surface) !important;

    border-color:
        var(--dashboard-dark-border) !important;

    box-shadow:
        0 10px 24px
        rgba(0,0,0,.12) !important;
}


html[data-color-mode="dark"] .kpi-panel:hover {

    background:
        #142338 !important;

    border-color:
        #31465e !important;

    box-shadow:
        0 14px 30px
        rgba(0,0,0,.20) !important;
}


/*
 * Etiqueta superior:
 * conserva un tono azulado, pero ahora es legible.
 */

html[data-color-mode="dark"] .kpi-label {

    color:
        #8eabc7 !important;
}


/*
 * Número principal.
 */

html[data-color-mode="dark"] .kpi-value {

    color:
        var(--dashboard-dark-title) !important;
}


/*
 * Descripción inferior.
 */

html[data-color-mode="dark"] .kpi-sub {

    color:
        var(--dashboard-dark-muted) !important;
}


html[data-color-mode="dark"] .kpi-sub strong {

    color:
        #c9d8e7 !important;
}


/*
 * Badges HOY / LIVE / MES / VER.
 */

html[data-color-mode="dark"] .kpi-badge {

    background:
        #eaf4fc !important;

    color:
        #183b5c !important;

    border-color:
        rgba(255,255,255,.10) !important;
}


html[data-color-mode="dark"] .kpi-badge-link:hover {

    background:
        #15263a !important;

    color:
        var(--primary) !important;
}


/* ------------------------------------------------------------
   ICONOS KPI
   Se mantienen sus colores funcionales.
------------------------------------------------------------ */

html[data-color-mode="dark"] .kpi-icon-wrap {

    box-shadow:
        0 7px 18px
        rgba(0,0,0,.13);
}


/* ------------------------------------------------------------
   META MENSUAL
------------------------------------------------------------ */

html[data-color-mode="dark"] .goal-bar-panel {

    background:
        var(--dashboard-dark-surface) !important;

    border-color:
        var(--dashboard-dark-border) !important;

    box-shadow:
        0 10px 24px
        rgba(0,0,0,.12) !important;
}


html[data-color-mode="dark"] .goal-bar-title {

    color:
        var(--dashboard-dark-title) !important;
}


html[data-color-mode="dark"] .goal-bar-sub {

    color:
        var(--dashboard-dark-muted) !important;
}


html[data-color-mode="dark"] .goal-bar-sub strong {

    color:
        #dbe6f1 !important;
}


html[data-color-mode="dark"] .goal-bar-scale {

    color:
        #71879e !important;
}


html[data-color-mode="dark"] .goal-bar-track {

    background:
        #d9e7f2 !important;

    box-shadow:
        inset 0 1px 2px
        rgba(0,0,0,.10) !important;
}


html[data-color-mode="dark"] .goal-progress-message {

    color:
        #8fa3b8 !important;
}


/* ------------------------------------------------------------
   TARJETAS DE GRAFICOS
------------------------------------------------------------ */

html[data-color-mode="dark"] .card {

    background:
        var(--dashboard-dark-surface) !important;

    border-color:
        var(--dashboard-dark-border) !important;
}


html[data-color-mode="dark"] .card-header {

    background:
        var(--dashboard-dark-surface) !important;

    border-bottom-color:
        var(--dashboard-dark-border) !important;
}


html[data-color-mode="dark"] .card-header .fw-bold {

    color:
        var(--dashboard-dark-title) !important;
}


/* ------------------------------------------------------------
   TEXTOS INLINE DEL DONUT
   Sobrescribe color:var(--dark-bg)
------------------------------------------------------------ */

html[data-color-mode="dark"] #donutChart ~ div,
html[data-color-mode="dark"] #donutChart + div {

    color:
        var(--dashboard-dark-title) !important;
}


/*
 * En esta vista hay elementos con estilos inline usando
 * var(--dark-bg). En modo oscuro hacemos que esa variable
 * represente texto claro SOLO dentro del dashboard.
 */

html[data-color-mode="dark"] .dashboard-wrapper {

    --dark-bg:
        #eef5fb;
}


/* Fallback por si el dashboard no usa dashboard-wrapper */

html[data-color-mode="dark"] main {

    --dashboard-inline-title:
        #eef5fb;
}


/* ------------------------------------------------------------
   BOTONES
------------------------------------------------------------ */

html[data-color-mode="dark"] .dashboard-export-btn {

    color:
        #ffffff !important;
}


html[data-color-mode="dark"] .dashboard-primary-btn,
html[data-color-mode="dark"] .goal-bar-btn {

    color:
        #ffffff !important;
}


/* ------------------------------------------------------------
   TOAST DE META
------------------------------------------------------------ */

html[data-color-mode="dark"] .goal-toast {

    background:
        var(--dashboard-dark-surface) !important;

    border-color:
        var(--dashboard-dark-border) !important;

    color:
        var(--dashboard-dark-text) !important;
}


html[data-color-mode="dark"] .goal-toast-header {

    background:
        var(--dashboard-dark-surface-2) !important;

    border-color:
        var(--dashboard-dark-border) !important;

    color:
        var(--dashboard-dark-title) !important;
}


html[data-color-mode="dark"] .goal-toast .toast-body {

    color:
        var(--dashboard-dark-text) !important;
}


/* ------------------------------------------------------------
   MEJOR CONTRASTE DE TEXTOS BOOTSTRAP DEL DASHBOARD
------------------------------------------------------------ */

html[data-color-mode="dark"] .text-muted {

    color:
        var(--dashboard-dark-muted) !important;
}


/* ------------------------------------------------------------
   TRANSICION SUAVE ENTRE MODOS
------------------------------------------------------------ */

.kpi-panel,
.goal-bar-panel,
.card,
.card-header,
.kpi-label,
.kpi-value,
.kpi-sub,
.goal-bar-title,
.goal-bar-sub,
.goal-progress-message {

    transition:
        background-color .22s ease,
        border-color .22s ease,
        color .22s ease,
        box-shadow .22s ease;
}

</style>

<style>
/* DARK MODE - INDICADOR META MENSUAL */

/* Indicador porcentual situado sobre la barra */
html[data-color-mode="dark"] .goal-progress-message {
    color: var(--ink-neutral, #94a3b8) !important;
}

/* Elementos claros dentro del progreso */
html[data-color-mode="dark"] .goal-progress-message span {
    color: inherit;
}

/*
 * El porcentaje flotante de la barra conserva el naranja
 * pero elimina el fondo crema del modo claro.
 */
html[data-color-mode="dark"] .progress-bar > span,
html[data-color-mode="dark"] .goal-progress > span,
html[data-color-mode="dark"] [class*="goal-progress"] > span {
    background: rgba(245, 158, 11, .16) !important;
    color: #fbbf24 !important;
    border-color: rgba(251, 191, 36, .30) !important;
    box-shadow: none !important;
}

</style>


<style>
/* DARK MODE - PORCENTAJE BARRA META */

html[data-color-mode="dark"] .goal-bar-pct {
    background: rgba(245, 158, 11, .18) !important;
    color: #fbbf24 !important;
    border: 1px solid rgba(251, 191, 36, .32) !important;
    box-shadow: none !important;
}


</style>


<style>
/* DARK MODE - BARRA META MENSUAL DEFINITIVA */

/* Pista completa */
html[data-color-mode="dark"] .goal-bar-track {
    background: #20344a !important;
    border: 1px solid #304b66 !important;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.22) !important;
}

/* Porcentajes superiores */
html[data-color-mode="dark"] .goal-bar-scale {
    color: #7892aa !important;
}

/* Marcas verticales internas */
html[data-color-mode="dark"] .goal-bar-track::before,
html[data-color-mode="dark"] .goal-bar-track::after {
    border-color: #49647e !important;
    background-color: #49647e !important;
    opacity: .65 !important;
}

/* Progreso real */
html[data-color-mode="dark"] .goal-bar-fill {
    box-shadow: none !important;
}

/* Porcentaje flotante */
html[data-color-mode="dark"] .goal-bar-pct {
    background: rgba(245,158,11,.18) !important;
    color: #fbbf24 !important;
    border: 1px solid rgba(251,191,36,.32) !important;
}

</style>


<style>
/* DARK MODE - LEGIBILIDAD BARRA META */

/* Fondo de la barra */
html[data-color-mode="dark"] .goal-bar-track {
    background: #263b50 !important;
    border: 1px solid #3d5872 !important;
    box-shadow: inset 0 1px 3px rgba(0,0,0,.30) !important;
}

/* Progreso alcanzado */
html[data-color-mode="dark"] .goal-bar-fill {
    background: linear-gradient(
        90deg,
        #f59e0b,
        #fb923c
    ) !important;

    box-shadow: 0 0 10px rgba(245,158,11,.22) !important;
}

/* 0%, 25%, 50%, 75%, 100% */
html[data-color-mode="dark"] .goal-bar-scale {
    color: #a9bdd0 !important;
    font-weight: 700 !important;
}

/* Porcentaje actual */
html[data-color-mode="dark"] .goal-bar-pct {
    background: #f59e0b !important;
    color: #ffffff !important;
    border: 1px solid #fbbf24 !important;
    font-weight: 800 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,.25) !important;
}

</style>



<style>
/* DARK MODE - FRANJAS KPI DASHBOARD REAL */

/*
 * IMPORTANTE:
 * No se cambia el color de cada KPI.
 * Solo se elimina el blanco del degradado.
 */

/* VENTAS DE HOY */
html[data-color-mode="dark"] .kpi-sales::before {
    background: linear-gradient(
        180deg,
        var(--dash-primary) 0%,
        color-mix(in srgb, var(--dash-primary) 65%, #132338) 58%,
        color-mix(in srgb, var(--dash-primary) 12%, #132338) 100%
    ) !important;
}

/* MESAS EN SERVICIO */
html[data-color-mode="dark"] .kpi-tables::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-1) 0%,
        color-mix(in srgb, var(--dash-accent-1) 65%, #132338) 58%,
        color-mix(in srgb, var(--dash-accent-1) 12%, #132338) 100%
    ) !important;
}

/* VENTAS DEL MES */
html[data-color-mode="dark"] .kpi-month::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-2) 0%,
        color-mix(in srgb, var(--dash-accent-2) 65%, #132338) 58%,
        color-mix(in srgb, var(--dash-accent-2) 12%, #132338) 100%
    ) !important;
}

/* ALERTA DE STOCK */
html[data-color-mode="dark"] .kpi-stock.kpi-alert::before {
    background: linear-gradient(
        180deg,
        #ef4444 0%,
        color-mix(in srgb, #ef4444 65%, #132338) 58%,
        color-mix(in srgb, #ef4444 12%, #132338) 100%
    ) !important;
}

/* STOCK CORRECTO */
html[data-color-mode="dark"] .kpi-stock.kpi-ok::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-4) 0%,
        color-mix(in srgb, var(--dash-accent-4) 65%, #132338) 58%,
        color-mix(in srgb, var(--dash-accent-4) 12%, #132338) 100%
    ) !important;
}

</style>


<style>
/* DARK MODE - INGRESO ACTUAL LEGIBILIDAD FINAL */

/* Tarjeta */
html[data-color-mode="dark"] #donutChart {
    filter: none;
}

/* 0% central */
html[data-color-mode="dark"] #donutChart + div > div:first-child {
    color: #ffffff !important;
    text-shadow: 0 1px 3px rgba(0, 0, 0, .30);
}

/* "de meta" */
html[data-color-mode="dark"] #donutChart + div > div:last-child {
    color: #a9bdd0 !important;
}

/* Meta mensual */
html[data-color-mode="dark"] #donutChart
    + div
    + div {
    color: #a9bdd0 !important;
}

/*
 * Corrige específicamente los textos inline de la tarjeta
 * que actualmente usan --dark-bg.
 */
html[data-color-mode="dark"] #donutChart
    ~ div strong {
    color: #60a5fa !important;
}

/* Textos secundarios dentro del cuerpo */
html[data-color-mode="dark"] #donutChart
    ~ div {
    color: #9fb3c8 !important;
}

/* Pie explicativo */
html[data-color-mode="dark"] #donutChart
    ~ p {
    color: #8fa7bd !important;
}

</style>


<style>
/* DARK MODE - VALORES INGRESO ACTUAL */

/* Modo claro conserva el aspecto del sistema */
.current-income-stat {
    font-size: .76rem;
    color: var(--text-muted);
}

.current-income-achieved {
    margin-top: 4px;
}

.current-income-value {
    color: var(--dark-bg);
    font-weight: 800;
}

/* Modo oscuro */
html[data-color-mode="dark"] .current-income-stat {
    color: #a9bdd0 !important;
    font-weight: 600;
}

html[data-color-mode="dark"] .current-income-value {
    color: #38aaf0 !important;
    font-weight: 800 !important;
}

html[data-color-mode="dark"] .current-income-achieved {
    color: #c7d5e2 !important;
}

/* El importe de Logrado también debe destacar */
html[data-color-mode="dark"] .current-income-achieved {
    font-weight: 600 !important;
}

</style>



<style>
/* DARK MODE - FRANJAS KPI DEGRADADO RESTAURADO */

/* Ventas de hoy */
html[data-color-mode="dark"] .kpi-sales::before {
    background: linear-gradient(
        180deg,
        var(--dash-primary) 0%,
        color-mix(in srgb, var(--dash-primary) 55%, #132338) 60%,
        #132338 100%
    ) !important;
}

/* Mesas en servicio */
html[data-color-mode="dark"] .kpi-tables::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-1) 0%,
        color-mix(in srgb, var(--dash-accent-1) 55%, #132338) 60%,
        #132338 100%
    ) !important;
}

/* Ventas del mes */
html[data-color-mode="dark"] .kpi-month::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-2) 0%,
        color-mix(in srgb, var(--dash-accent-2) 55%, #132338) 60%,
        #132338 100%
    ) !important;
}

/* Stock con alerta */
html[data-color-mode="dark"] .kpi-stock.kpi-alert::before {
    background: linear-gradient(
        180deg,
        #ef4444 0%,
        color-mix(in srgb, #ef4444 55%, #132338) 60%,
        #132338 100%
    ) !important;
}

/* Stock correcto */
html[data-color-mode="dark"] .kpi-stock.kpi-ok::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-4) 0%,
        color-mix(in srgb, var(--dash-accent-4) 55%, #132338) 60%,
        #132338 100%
    ) !important;
}

</style>


<style>
/* DARK MODE - FRANJA MAS VENDIDOS */

/* Mantener el degradado naranja, pero sin terminar en blanco */
html[data-color-mode="dark"] .dashboard-top-header {
    position: relative;
    overflow: hidden;
}

html[data-color-mode="dark"] .dashboard-top-header::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;

    background: linear-gradient(
        180deg,
        var(--primary) 0%,
        color-mix(in srgb, var(--primary) 55%, #132338) 60%,
        #132338 100%
    ) !important;
}

/* Evitar que otra regla deje una segunda franja clara */
html[data-color-mode="dark"] .dashboard-top-header {
    border-left-color: transparent !important;
}

/* Título e icono permanecen por encima de la franja */
html[data-color-mode="dark"] .dashboard-top-header h6 {
    position: relative;
    z-index: 1;
}

</style>


<style>
/* DARK MODE - ICONO META MENSUAL NARANJA */

html[data-color-mode="dark"] .goal-icon-wrap {
    background: rgba(255, 136, 0, .16) !important;
    border: 1px solid rgba(255, 136, 0, .25) !important;
    color: #ff8800 !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .goal-icon-wrap i {
    color: #ff8800 !important;
}

</style>


<style id="dashboard-rooms-redesign">

/* ============================================================
   ESTADO DE SALONES
   ============================================================ */

.dashboard-rooms-card {
    overflow: hidden;
}


/* ------------------------------------------------------------
   ENCABEZADO
   ------------------------------------------------------------ */

.dashboard-rooms-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    padding: 17px 22px !important;

    background: var(--card-bg);
    border-bottom: 1px solid var(--border-soft);
}


.dashboard-rooms-title {
    display: flex;
    align-items: center;
    gap: 11px;
}


.dashboard-rooms-title > i {
    color: var(--text-main);
    font-size: 18px;
}


.dashboard-rooms-title h6 {
    margin: 0;

    color: var(--text-main);

    font-size: .92rem;
    font-weight: 800;
}


.dashboard-rooms-title small {
    display: block;

    margin-top: 2px;

    color: var(--text-muted);

    font-size: .68rem;
    font-weight: 500;
}


/* ------------------------------------------------------------
   LEYENDA
   ------------------------------------------------------------ */

.dashboard-rooms-legend {
    display: flex;
    align-items: center;
    gap: 8px;
}


.rooms-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 5px 9px;

    border: 1px solid var(--border-soft);
    border-radius: 999px;

    background: var(--card-bg);

    font-size: .67rem;
    font-weight: 700;
}


.rooms-legend-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;
}


.rooms-legend-item.is-available {
    color: var(--ink-green, #15803d);
}


.rooms-legend-item.is-available
.rooms-legend-dot {
    background: #22c55e;
}


.rooms-legend-item.is-busy {
    color: #e11d48;
}


.rooms-legend-item.is-busy
.rooms-legend-dot {
    background: #f43f5e;
}


/* ------------------------------------------------------------
   CONTENIDO
   ------------------------------------------------------------ */

.dashboard-rooms-body {
    padding: 18px 22px 22px !important;
}


/* ------------------------------------------------------------
   PESTAÑAS DE SALONES
   ------------------------------------------------------------ */

.dashboard-room-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;

    margin-bottom: 18px;
}


.dashboard-room-tabs .nav-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 8px 13px;

    border: 1px solid var(--border-soft) !important;
    border-radius: 10px !important;

    background: var(--card-bg);

    color: var(--text-muted);

    font-size: .73rem;
    font-weight: 750;

    transition:
        background-color .18s ease,
        border-color .18s ease,
        color .18s ease,
        transform .18s ease,
        box-shadow .18s ease;
}


.dashboard-room-tabs .nav-link:hover {
    color: var(--text-main);

    border-color:
        color-mix(
            in srgb,
            var(--primary) 30%,
            var(--border-soft)
        ) !important;

    transform: translateY(-1px);
}


.dashboard-room-tabs .nav-link.active {
    color: #fff !important;

    background: var(--primary) !important;

    border-color: var(--primary) !important;

    box-shadow:
        0 5px 12px
        color-mix(
            in srgb,
            var(--primary) 20%,
            transparent
        );
}


.room-table-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 20px;
    height: 20px;

    padding: 0 5px;

    border-radius: 6px;

    background:
        color-mix(
            in srgb,
            var(--text-muted) 9%,
            transparent
        );

    font-size: .62rem;
    font-weight: 800;
}


.dashboard-room-tabs
.nav-link.active
.room-table-count {
    background: rgba(255,255,255,.18);
    color: #fff;
}


/* ------------------------------------------------------------
   TARJETAS DE MESAS
   ------------------------------------------------------------ */

.dashboard-table-link {
    display: block;

    height: 100%;

    color: inherit;
    text-decoration: none;
}


.dashboard-table-card {
    position: relative;

    display: flex;
    flex-direction: column;

    min-height: 132px;
    height: 100%;

    padding: 15px;

    overflow: hidden;

    border: 1px solid var(--border-soft);
    border-radius: 14px;

    background: var(--card-bg);

    box-shadow:
        0 3px 10px
        rgba(15, 23, 42, .035);

    transition:
        transform .18s ease,
        box-shadow .18s ease,
        border-color .18s ease;
}


.dashboard-table-card::before {
    content: "";

    position: absolute;

    top: 0;
    left: 0;
    bottom: 0;

    width: 3px;

    border-radius:
        14px 0 0 14px;
}


.dashboard-table-card.is-available::before {
    background: #22c55e;
}


.dashboard-table-card.is-busy::before {
    background: #f43f5e;
}


.dashboard-table-card.is-available {
    border-color:
        color-mix(
            in srgb,
            #22c55e 18%,
            var(--border-soft)
        );
}


.dashboard-table-card.is-busy {
    border-color:
        color-mix(
            in srgb,
            #f43f5e 20%,
            var(--border-soft)
        );

    background:
        color-mix(
            in srgb,
            #f43f5e 3%,
            var(--card-bg)
        );
}


.dashboard-table-card:hover {
    transform: translateY(-2px);

    box-shadow:
        0 8px 18px
        rgba(15, 23, 42, .08);
}


.dashboard-table-card.is-available:hover {
    border-color:
        color-mix(
            in srgb,
            #22c55e 45%,
            var(--border-soft)
        );
}


.dashboard-table-card.is-busy:hover {
    border-color:
        color-mix(
            in srgb,
            #f43f5e 45%,
            var(--border-soft)
        );
}


/* ------------------------------------------------------------
   PARTE SUPERIOR DE CADA MESA
   ------------------------------------------------------------ */

.dashboard-table-top {
    display: flex;
    align-items: center;

    gap: 8px;
}


.dashboard-table-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 31px;
    height: 31px;

    flex: 0 0 31px;

    border-radius: 9px;

    font-size: 14px;
}


.is-available
.dashboard-table-icon {
    color: var(--ink-green, #16a34a);
    background: #ecfdf3;
}


.is-busy
.dashboard-table-icon {
    color: #e11d48;
    background: var(--surface-red, #fff1f2);
}


.dashboard-table-status {
    font-size: .65rem;
    font-weight: 750;
}


.is-available
.dashboard-table-status {
    color: var(--ink-green, #16a34a);
}


.is-busy
.dashboard-table-status {
    color: #e11d48;
}


/* ------------------------------------------------------------
   INFORMACIÓN
   ------------------------------------------------------------ */

.dashboard-table-info {
    margin-top: 13px;
}


.dashboard-table-info h6 {
    margin: 0 0 7px;

    color: var(--text-main);

    font-size: .84rem;
    font-weight: 800;
}


.dashboard-table-detail {
    display: flex;
    flex-direction: column;

    gap: 1px;
}


.dashboard-table-detail span {
    color: var(--text-muted);

    font-size: .61rem;
    font-weight: 500;
}


.dashboard-table-detail strong {
    color: var(--text-main);

    font-size: .70rem;
    font-weight: 750;
}


.is-available
.dashboard-table-detail strong {
    color: var(--ink-green, #15803d);
}


.is-busy
.dashboard-table-detail strong {
    color: #e11d48;
}


/* ------------------------------------------------------------
   FLECHA
   ------------------------------------------------------------ */

.dashboard-table-arrow {
    position: absolute;

    right: 12px;
    bottom: 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    width: 23px;
    height: 23px;

    border-radius: 7px;

    color: var(--text-muted);

    background:
        color-mix(
            in srgb,
            var(--text-muted) 6%,
            transparent
        );

    font-size: 10px;

    transition:
        transform .18s ease,
        color .18s ease,
        background-color .18s ease;
}


.dashboard-table-card:hover
.dashboard-table-arrow {
    transform: translateX(2px);

    color: var(--primary);

    background:
        color-mix(
            in srgb,
            var(--primary) 9%,
            transparent
        );
}


/* ------------------------------------------------------------
   SIN ÁREAS
   ------------------------------------------------------------ */

.dashboard-rooms-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    min-height: 180px;

    color: var(--text-muted);

    text-align: center;
}


.dashboard-rooms-empty i {
    margin-bottom: 8px;

    font-size: 25px;
}


.dashboard-rooms-empty strong {
    color: var(--text-main);

    font-size: .82rem;
}


.dashboard-rooms-empty span {
    margin-top: 3px;

    font-size: .68rem;
}


/* ============================================================
   DARK MODE
   ============================================================ */

html[data-color-mode="dark"]
.dashboard-rooms-card {
    border-color: #38516b !important;
}


html[data-color-mode="dark"]
.dashboard-rooms-header {
    border-bottom-color: #31485f;
}


html[data-color-mode="dark"]
.rooms-legend-item {
    border-color: #30465d;

    background: #132338;
}


html[data-color-mode="dark"]
.rooms-legend-item.is-available {
    color: #75dfa3;
}


html[data-color-mode="dark"]
.rooms-legend-item.is-busy {
    color: #fda4af;
}


html[data-color-mode="dark"]
.dashboard-room-tabs .nav-link {
    color: #c6d5e3 !important;

    background: #15263a !important;

    border-color: #38516b !important;
}


html[data-color-mode="dark"]
.dashboard-room-tabs .nav-link:hover {
    color: #fff !important;

    background: #1a2d43 !important;

    border-color: #55718d !important;
}


html[data-color-mode="dark"]
.dashboard-room-tabs .nav-link.active {
    color: #fff !important;

    background: var(--primary) !important;

    border-color: var(--primary) !important;
}


html[data-color-mode="dark"]
.dashboard-table-card {
    background: #15263a;

    border-color: #314b64;

    box-shadow:
        0 5px 14px
        rgba(0,0,0,.13);
}


html[data-color-mode="dark"]
.dashboard-table-card.is-available {
    border-color:
        color-mix(
            in srgb,
            #22c55e 25%,
            #314b64
        );
}


html[data-color-mode="dark"]
.dashboard-table-card.is-busy {
    border-color:
        color-mix(
            in srgb,
            #f43f5e 28%,
            #314b64
        );

    background:
        color-mix(
            in srgb,
            #f43f5e 5%,
            #15263a
        );
}


html[data-color-mode="dark"]
.is-available
.dashboard-table-icon {
    color: #75dfa3;

    background:
        rgba(34,197,94,.12);
}


html[data-color-mode="dark"]
.is-busy
.dashboard-table-icon {
    color: #fda4af;

    background:
        rgba(244,63,94,.12);
}


html[data-color-mode="dark"]
.is-available
.dashboard-table-status,
html[data-color-mode="dark"]
.is-available
.dashboard-table-detail strong {
    color: #75dfa3;
}


html[data-color-mode="dark"]
.is-busy
.dashboard-table-status,
html[data-color-mode="dark"]
.is-busy
.dashboard-table-detail strong {
    color: #fda4af;
}


/* ============================================================
   RESPONSIVE
   ============================================================ */

@media (max-width: 767.98px) {

    .dashboard-rooms-header {
        align-items: flex-start;
        flex-direction: column;

        gap: 12px;
    }

    .dashboard-rooms-legend {
        width: 100%;
    }

    .dashboard-rooms-body {
        padding:
            15px 16px 18px !important;
    }

    .dashboard-table-card {
        min-height: 125px;

        padding: 13px;
    }
}

</style>


<style id="dashboard-top-products-clean-header">

/* ============================================================
   MÁS VENDIDOS - ENCABEZADO LIMPIO
   Sin franja lateral
   ============================================================ */

.dashboard-top-header::before {
    display: none !important;
    content: none !important;
}

.dashboard-top-header {
    border-left: 0 !important;
    overflow: visible;
}

html[data-color-mode="dark"]
.dashboard-top-header::before {
    display: none !important;
    content: none !important;
}

html[data-color-mode="dark"]
.dashboard-top-header {
    border-left: 0 !important;
}

</style>





<style id="dashboard-section-icons-final">

/* ============================================================
   ENCABEZADOS DEL DASHBOARD
   Iconos con el color principal de la paleta
   ============================================================ */

.dashboard-section-title {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    color: var(--text-main);

    font-weight: 800;
}


/* Iconos */
.dashboard-section-title > i,
.dashboard-rooms-title.dashboard-section-title > i,
.dashboard-top-header .dashboard-section-title > i {
    color: var(--dash-primary) !important;

    font-size: 15px;

    line-height: 1;

    opacity: 1;
}


/* Actividad e Ingreso */
.card-header > .dashboard-section-title {
    font-size: .85rem;
}


/* Más Vendidos */
.dashboard-top-header
.dashboard-section-title {
    font-size: .92rem;
}


/* Estado de Salones conserva su estructura */
.dashboard-rooms-title.dashboard-section-title {
    gap: 10px;
}


/* ============================================================
   MODO OSCURO
   Mantener el color de la paleta
   ============================================================ */

html[data-color-mode="dark"]
.dashboard-section-title > i,

html[data-color-mode="dark"]
.dashboard-rooms-title.dashboard-section-title > i,

html[data-color-mode="dark"]
.dashboard-top-header
.dashboard-section-title > i {
    color: var(--dash-primary) !important;
}

</style>


<style id="dashboard-section-icons-bordered-final">

/* ============================================================
   ICONOS DE ENCABEZADOS
   Borde + fondo suave con color de la paleta
   ============================================================ */

.dashboard-section-title > i,
.dashboard-rooms-title.dashboard-section-title > i,
.dashboard-top-header .dashboard-section-title > i {

    display: inline-flex !important;
    align-items: center;
    justify-content: center;

    width: 30px;
    height: 30px;

    flex: 0 0 30px;

    margin: 0 !important;

    border: 1px solid
        color-mix(
            in srgb,
            var(--dash-primary) 35%,
            var(--border-soft)
        );

    border-radius: 9px;

    color: var(--dash-primary) !important;

    background:
        color-mix(
            in srgb,
            var(--dash-primary) 7%,
            var(--card-bg)
        );

    font-size: 14px !important;
    line-height: 1;

    transition:
        background-color .2s ease,
        border-color .2s ease,
        color .2s ease;
}


/* ------------------------------------------------------------
   MODO OSCURO
   ------------------------------------------------------------ */

html[data-color-mode="dark"]
.dashboard-section-title > i,

html[data-color-mode="dark"]
.dashboard-rooms-title.dashboard-section-title > i,

html[data-color-mode="dark"]
.dashboard-top-header
.dashboard-section-title > i {

    color: var(--dash-primary) !important;

    background:
        color-mix(
            in srgb,
            var(--dash-primary) 12%,
            #132338
        );

    border-color:
        color-mix(
            in srgb,
            var(--dash-primary) 42%,
            #30465d
        );
}

</style>


<style id="dashboard-goal-theme-dark-final">

/* META MENSUAL - respetar paleta activa en modo oscuro */

/* Icono */
html[data-color-mode="dark"] .goal-bar-icon {
    color: var(--dash-primary) !important;

    background:
        color-mix(
            in srgb,
            var(--dash-primary) 12%,
            #132338
        ) !important;

    border-color:
        color-mix(
            in srgb,
            var(--dash-primary) 42%,
            #30465d
        ) !important;
}

html[data-color-mode="dark"] .goal-bar-icon i {
    color: var(--dash-primary) !important;
}

/* Barra de progreso */
html[data-color-mode="dark"] .goal-bar-fill {
    background: var(--dash-primary) !important;
}

/* Porcentaje dentro de la barra */
html[data-color-mode="dark"] .goal-bar-percent {
    background: var(--dash-primary) !important;
}

</style>


<style id="dashboard-goal-icon-theme-fix">

/* META MENSUAL - icono según paleta activa */

html[data-color-mode="dark"] .goal-icon-wrap {
    color: var(--dash-primary) !important;

    background:
        color-mix(
            in srgb,
            var(--dash-primary) 12%,
            #132338
        ) !important;

    border-color:
        color-mix(
            in srgb,
            var(--dash-primary) 42%,
            #30465d
        ) !important;

    box-shadow: none !important;
}

html[data-color-mode="dark"] .goal-icon-wrap i {
    color: var(--dash-primary) !important;
}

</style>


<style id="dashboard-goal-percent-theme-fix">

/* Porcentaje de Meta Mensual según paleta activa */
html[data-color-mode="dark"] .goal-bar-panel .goal-percent,
html[data-color-mode="dark"] .goal-bar-panel .goal-bar-percent,
html[data-color-mode="dark"] .goal-bar-panel .goal-progress-label {
    background: var(--dash-primary) !important;
    border-color: var(--dash-primary) !important;
    color: #fff !important;
}

</style>


<style id="dashboard-goal-pct-theme-final">

/* Porcentaje dentro de la barra según paleta activa */
html[data-color-mode="dark"] .goal-bar-pct {
    background: var(--dash-primary) !important;
    border-color: var(--dash-primary) !important;
    color: #fff !important;
}

</style>


<style id="dashboard-goal-pct-clean-final">

/* Porcentaje integrado dentro de la barra */
html[data-color-mode="dark"] .goal-bar-pct {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;

    color: #fff !important;

    padding: 0 6px !important;
    border-radius: 0 !important;

    font-weight: 800;
}

</style>


<style id="dashboard-goal-gradient-dark-final">

/* Meta mensual - degradado dinámico según paleta */
html[data-color-mode="dark"] .goal-bar-fill {

    background: linear-gradient(
        90deg,
        color-mix(in srgb, var(--dash-primary) 22%, #07111c) 0%,
        color-mix(in srgb, var(--dash-primary) 70%, #07111c) 28%,
        var(--dash-primary) 58%,
        color-mix(in srgb, var(--dash-primary) 38%, var(--card-bg, white)) 100%
    ) !important;

}

</style>

@endsection










<style id="dashboard-kpi-badge-final">

/* =========================================================
   BADGES KPI - COLORES Y BORDES
   ========================================================= */

.kpi-panel .kpi-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    min-width: 40px !important;
    height: 22px !important;
    padding: 2px 9px !important;

    border-width: 1px !important;
    border-style: solid !important;
    border-radius: 999px !important;

    font-size: .68rem !important;
    font-weight: 800 !important;
    line-height: 1 !important;

    box-sizing: border-box !important;
}

/* =========================
   HOY - VENTAS
   ========================= */

.kpi-panel.kpi-sales .kpi-badge {
    background: #fff1df !important;
    color: #ff8c00 !important;
    border-color: #ffb45c !important;
}

/* =========================
   LIVE - MESAS
   ========================= */

.kpi-panel.kpi-tables .kpi-badge {
    background: #e8f3ff !important;
    color: #1683c7 !important;
    border-color: #75bde8 !important;
}

/* =========================
   MES - VENTAS DEL MES
   ========================= */

.kpi-panel.kpi-month .kpi-badge {
    background: #e8f8ee !important;
    color: #16a05d !important;
    border-color: #70cf94 !important;
}

/* =========================
   VER - STOCK
   ========================= */

.kpi-panel.kpi-stock .kpi-badge {
    background: #fff1df !important;
    color: #ff8c00 !important;
    border-color: #ffb45c !important;
}


/* =========================================================
   MODO OSCURO
   ========================================================= */

html[data-color-mode="dark"] .kpi-panel.kpi-sales .kpi-badge {
    background: rgba(255, 140, 0, .14) !important;
    color: #ffb45c !important;
    border-color: rgba(255, 180, 92, .55) !important;
}

html[data-color-mode="dark"] .kpi-panel.kpi-tables .kpi-badge {
    background: rgba(22, 131, 199, .16) !important;
    color: #75bde8 !important;
    border-color: rgba(117, 189, 232, .55) !important;
}

html[data-color-mode="dark"] .kpi-panel.kpi-month .kpi-badge {
    background: rgba(22, 160, 93, .16) !important;
    color: #70cf94 !important;
    border-color: rgba(112, 207, 148, .55) !important;
}

html[data-color-mode="dark"] .kpi-panel.kpi-stock .kpi-badge {
    background: rgba(255, 140, 0, .14) !important;
    color: #ffb45c !important;
    border-color: rgba(255, 180, 92, .55) !important;
}


/* =========================================================
   SIN CAMBIO DE COLOR POR HOVER
   ========================================================= */

.kpi-panel.kpi-sales .kpi-badge:hover {
    background: #fff1df !important;
    color: #ff8c00 !important;
    border-color: #ffb45c !important;
}

.kpi-panel.kpi-tables .kpi-badge:hover {
    background: #e8f3ff !important;
    color: #1683c7 !important;
    border-color: #75bde8 !important;
}

.kpi-panel.kpi-month .kpi-badge:hover {
    background: #e8f8ee !important;
    color: #16a05d !important;
    border-color: #70cf94 !important;
}

.kpi-panel.kpi-stock .kpi-badge:hover {
    background: #fff1df !important;
    color: #ff8c00 !important;
    border-color: #ffb45c !important;
}

</style>

