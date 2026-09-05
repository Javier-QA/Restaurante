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
                    <span class="fw-bold" style="font-size:.85rem;">Actividad del Año</span>
                    <div style="background:var(--primary);width:22px;height:22px;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <span style="color:white;font-size:13px;line-height:1;">—</span>
                    </div>
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
                    <span class="fw-bold" style="font-size:.85rem;">Ingreso Actual</span>
                    <div style="background:var(--primary);width:22px;height:22px;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <span style="color:white;font-size:13px;line-height:1;">—</span>
                    </div>
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
                        <div style="font-size:.72rem;color:var(--text-muted);">Meta mensual: <strong style="color:var(--dark-bg);">{{ $currency ?? 'S/' }} {{ number_format($monthlyGoal ?? 5000, 0,'.',',') }}</strong></div>
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
                    <h6 class="fw-bold mb-0">Estado de Salones</h6>
                    <div class="d-flex gap-2">
                        <span class="badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:.72rem;">● Disponible</span>
                        <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:.72rem;">● Ocupada</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    @if(isset($areas) && count($areas) > 0)
                        <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
                            @foreach($areas as $index => $area)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index == 0 ? 'active' : '' }} rounded-pill px-4 fw-bold border"
                                            id="pills-{{ $area->id }}-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-{{ $area->id }}" type="button">
                                        {{ $area->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($areas as $index => $area)
                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="pills-{{ $area->id }}">
                                    <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-5 g-3">
                                        @foreach($area->tables as $table)
                                            @php $isBusy = $table->orders->count() > 0; @endphp
                                            <div class="col">
                                                <a href="{{ route('pos.order', $table->id) }}" class="text-decoration-none">
                                                    <div class="card border-0 text-center py-3 table-hover-card"
                                                         style="background:{{ $isBusy ? '#fff1f2' : 'var(--light-bg)' }};">
                                                        <div class="card-body p-2">
                                                            <div class="mb-2">
                                                                <i class="bi {{ $isBusy ? 'bi-person-workspace' : 'bi-check-circle-fill' }} fs-1"
                                                                   style="color:{{ $isBusy ? '#e11d48' : '#22c55e' }};"></i>
                                                            </div>
                                                            <h6 class="fw-bold text-dark mb-1" style="font-size:.82rem;">{{ $table->name }}</h6>
                                                            @if($isBusy)
                                                                <span class="badge rounded-pill" style="background:#e11d48;font-size:.7rem;">
                                                                    {{ $currency ?? 'S/' }} {{ number_format($table->orders->first()->total, 2) }}
                                                                </span>
                                                            @else
                                                                <small class="text-muted" style="font-size:.7rem;">Libre</small>
                                                            @endif
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
                        <div class="text-center py-5 text-muted">No hay áreas configuradas.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header py-3 px-4">
                    <h6 class="fw-bold mb-0">🏆 Más Vendidos</h6>
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
                                <i class="bi bi-trophy-fill text-warning ms-2" style="opacity:.6;"></i>
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
        color-mix(in srgb, var(--dash-primary) 25%, white) 65%,
        white 100%
    );
}

    .kpi-tables::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-1) 0%,
        color-mix(in srgb, var(--dash-accent-1) 25%, white) 65%,
        white 100%
    );
}

    .kpi-month::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-2) 0%,
        color-mix(in srgb, var(--dash-accent-2) 25%, white) 65%,
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
        color-mix(in srgb, var(--dash-accent-4) 25%, white) 65%,
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
        background: color-mix(in srgb, var(--dash-primary) 12%, white);
        color: var(--dash-primary);
    }

    .kpi-tables .kpi-icon-wrap {
        background: color-mix(in srgb, var(--dash-accent-1) 12%, white);
        color: var(--dash-accent-1);
    }

    .kpi-month .kpi-icon-wrap {
        background: color-mix(in srgb, var(--dash-accent-2) 12%, white);
        color: var(--dash-accent-2);
    }

    .kpi-stock.kpi-alert .kpi-icon-wrap {
        background: #fff1f2;
        color: #ef4444;
    }

    .kpi-stock.kpi-ok .kpi-icon-wrap {
        background: color-mix(in srgb, var(--dash-accent-4) 12%, white);
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
        background: color-mix(in srgb, var(--dash-accent-1) 9%, white);
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
        background: color-mix(in srgb, var(--dash-primary) 12%, white);
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
        background: color-mix(in srgb, var(--dash-accent-1) 9%, white);
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
        color-mix(in srgb, var(--dash-accent-4) 18%, white) 22%,
        var(--dash-accent-4) 100%
    );
}

    .goal-fill-low {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-accent-1) 18%, white) 22%,
        var(--dash-accent-1) 100%
    );
}

    .goal-fill-medium {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-accent-2) 18%, white) 22%,
        var(--dash-accent-2) 100%
    );
}

    .goal-fill-high {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-primary) 18%, white) 22%,
        var(--dash-primary) 100%
    );
}

    .goal-fill-complete {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        color-mix(in srgb, var(--dash-primary) 22%, white) 20%,
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
            color-mix(in srgb, var(--dash-primary) 12%, white),
            color-mix(in srgb, var(--dash-accent-1) 10%, white)
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

@endsection
