@extends('layouts.app')

@section('content')

<style>
.inventory-page {
    padding-bottom: 20px;
}

/* =========================
   ENCABEZADO
========================= */

.inventory-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 22px;
}

.inventory-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 36px;
    margin-bottom: 11px;
    padding: 0 12px;
    border: 1px solid var(--border-soft);
    border-radius: 9px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .74rem;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 2px 5px rgba(15, 23, 42, .03);
    transition: .16s ease;
}

.inventory-back i {
    font-size: .78rem;
}

.inventory-back:hover {
    border-color: #94a3b8;
    background: var(--light-bg);
    color: var(--text-main);
    transform: translateY(-1px);
}

.inventory-title {
    margin: 0;
    color: var(--text-main);
    font-size: 1.55rem;
    font-weight: 800;
    letter-spacing: -.025em;
}

.inventory-title i {
    color:var(--text-main);
}

.inventory-subtitle {
    margin: 5px 0 0;
    color: var(--text-muted);
    font-size: .86rem;
}

/* =========================
   LEYENDA
========================= */

.inventory-legend {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 10px 14px;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    background: var(--card-bg);
    box-shadow: var(--shadow-soft);
}

.inventory-legend-item {
    display: flex;
    align-items: center;
    gap: 7px;
    color: var(--text-muted);
    font-size: .72rem;
    font-weight: 650;
    white-space: nowrap;
}

.inventory-legend-dot {
    width: 8px;
    height: 8px;
    flex-shrink: 0;
    border-radius: 50%;
}

.inventory-legend-dot.entry {
    background: #22c55e;
    box-shadow: 0 0 0 3px #dcfce7;
}

.inventory-legend-dot.exit {
    background: #ef4444;
    box-shadow: 0 0 0 3px #fee2e2;
}

/* =========================
   TARJETA PRINCIPAL
========================= */

.inventory-card {
    overflow: hidden;
    border: 1px solid var(--border-soft);
    border-radius: var(--radius-xl);
    background: var(--card-bg);
    box-shadow: var(--shadow-soft);
}

.inventory-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 17px 20px;
    border-bottom: 1px solid var(--border-soft);
}

.inventory-card-title {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0;
    color: var(--text-main);
    font-size: .9rem;
    font-weight: 800;
}

.inventory-card-title i {
    color:var(--text-main);
}

.inventory-count {
    padding: 5px 10px;
    border: 1px solid var(--border-soft);
    border-radius: 999px;
    background: var(--light-bg);
    color: var(--text-muted);
    font-size: .71rem;
    font-weight: 700;
}

/* =========================
   TABLA
========================= */

.inventory-table {
    width: 100%;
    margin: 0;
}

.inventory-table thead th {
    padding: 13px 17px;
    border-bottom: 1px solid var(--border-soft);
    background: var(--light-bg);
    color: var(--text-muted);
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .035em;
    text-transform: uppercase;
    white-space: nowrap;
}

.inventory-table tbody td {
    padding: 14px 17px;
    border-color: var(--border-soft);
    color: var(--text-main);
    vertical-align: middle;
}

.inventory-table tbody tr:last-child td {
    border-bottom: 0;
}

.inventory-table tbody tr:hover td {
    background: color-mix(
        in srgb,
        var(--primary) 2.5%,
        var(--card-bg)
    );
}

/* FECHA */

.inventory-date {
    min-width: 92px;
}

.inventory-date-main {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-main);
    font-size: .77rem;
    font-weight: 700;
}

.inventory-date-main i {
    color: var(--text-muted);
    font-size: .72rem;
}

.inventory-time {
    margin-top: 3px;
    padding-left: 18px;
    color: var(--text-muted);
    font-size: .68rem;
}

/* PRODUCTO */

.inventory-product {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 145px;
}

.inventory-product-icon {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid var(--border-soft);
    border-radius: 9px;
    background: var(--light-bg);
    color:var(--text-main);
    font-size: .85rem;
}

.inventory-product-name {
    color: var(--text-main);
    font-size: .8rem;
    font-weight: 750;
}

/* MOVIMIENTOS */

.inventory-movement {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 9px;
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: .67rem;
    font-weight: 800;
    white-space: nowrap;
}

.inventory-movement i {
    font-size: .68rem;
}

.inventory-movement.sale {
    border-color: #bfdbfe;
    background: #eff6ff;
    color: #2563eb;
}

.inventory-movement.entry {
    border-color: #bbf7d0;
    background: #f0fdf4;
    color: #15803d;
}

.inventory-movement.adjustment {
    border-color: #fecaca;
    background: #fff1f2;
    color: #dc2626;
}

/* NOTA */

.inventory-note {
    max-width: 230px;
    color: var(--text-muted);
    font-size: .74rem;
    line-height: 1.4;
}

.inventory-note-empty {
    color: #cbd5e1;
}

/* USUARIO */

.inventory-user {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 110px;
}

.inventory-user-avatar {
    width: 29px;
    height: 29px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid var(--border-soft);
    border-radius: 9px;
    background: var(--light-bg);
    color: var(--text-main);
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
}

.inventory-user-name {
    color: var(--text-main);
    font-size: .74rem;
    font-weight: 650;
}

/* CANTIDAD */

.inventory-quantity {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 48px;
    padding: 6px 9px;
    border-radius: 8px;
    font-size: .75rem;
    font-weight: 800;
}

.inventory-quantity.positive {
    background: #f0fdf4;
    color: #15803d;
}

.inventory-quantity.negative {
    background: #fff1f2;
    color: #dc2626;
}

.inventory-quantity.neutral {
    background: var(--light-bg);
    color: var(--text-muted);
}

/* SALDO */

.inventory-stock {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 48px;
    min-height: 31px;
    padding: 5px 9px;
    border: 1px solid var(--border-soft);
    border-radius: 8px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .76rem;
    font-weight: 800;
}

/* VACÍO */

.inventory-empty {
    padding: 55px 20px !important;
    text-align: center;
}

.inventory-empty-icon {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    border-radius: 16px;
    background: var(--light-bg);
    color: var(--text-muted);
    font-size: 1.3rem;
}

.inventory-empty-title {
    margin-bottom: 4px;
    color: var(--text-main);
    font-size: .87rem;
    font-weight: 750;
}

.inventory-empty-text {
    margin: 0;
    color: var(--text-muted);
    font-size: .75rem;
}

/* RESPONSIVE */

@media (max-width: 991.98px) {
    .inventory-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .inventory-legend {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 575.98px) {
    .inventory-legend {
        align-items: flex-start;
        flex-direction: column;
        gap: 10px;
    }
}
</style>


<div class="container-fluid inventory-page">

    {{-- ENCABEZADO --}}
    <div class="inventory-header">

        <div>

            <a href="{{ route('products.index') }}"
               class="inventory-back">
                <i class="bi bi-arrow-left"></i>
                Volver a Productos
            </a>

            <h2 class="inventory-title">
                <i class="bi bi-clock-history me-2"></i>Kardex de Movimientos
            </h2>

            <p class="inventory-subtitle">
                Auditoría detallada de entradas y salidas de stock
            </p>

        </div>


        <div class="inventory-legend">

            <div class="inventory-legend-item">
                <span class="inventory-legend-dot entry"></span>
                <span>
                    <strong style="color:var(--text-main);">Entrada</strong>
                    · Compra / Reposición
                </span>
            </div>

            <div class="inventory-legend-item">
                <span class="inventory-legend-dot exit"></span>
                <span>
                    <strong style="color:var(--text-main);">Salida</strong>
                    · Venta / Merma
                </span>
            </div>

        </div>

    </div>


    {{-- TARJETA --}}
    <div class="inventory-card">

        <div class="inventory-card-header">

            <h6 class="inventory-card-title">
                <i class="bi bi-arrow-left-right"></i>
                Historial de movimientos
            </h6>

            <span class="inventory-count">
                {{ $logs->total() }}
                {{ $logs->total() === 1 ? 'movimiento' : 'movimientos' }}
            </span>

        </div>


        <div class="table-responsive">

            <table class="table inventory-table align-middle">

                <thead>
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Producto</th>
                        <th>Movimiento</th>
                        <th>Motivo / Nota</th>
                        <th>Usuario</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Saldo</th>
                    </tr>
                </thead>


                <tbody>

                    @forelse($logs as $log)

                        <tr>

                            {{-- FECHA --}}
                            <td>
                                <div class="inventory-date">

                                    <div class="inventory-date-main">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $log->created_at->format('d/m/Y') }}
                                    </div>

                                    <div class="inventory-time">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </div>

                                </div>
                            </td>


                            {{-- PRODUCTO --}}
                            <td>
                                <div class="inventory-product">

                                    <div class="inventory-product-icon">
                                        <i class="bi bi-box-seam"></i>
                                    </div>

                                    <span class="inventory-product-name">
                                        {{ $log->product->name }}
                                    </span>

                                </div>
                            </td>


                            {{-- MOVIMIENTO --}}
                            <td>

                                @if($log->type == 'sale')

                                    <span class="inventory-movement sale">
                                        <i class="bi bi-cart3"></i>
                                        VENTA POS
                                    </span>

                                @elseif($log->type == 'entry')

                                    <span class="inventory-movement entry">
                                        <i class="bi bi-arrow-down"></i>
                                        ENTRADA
                                    </span>

                                @else

                                    <span class="inventory-movement adjustment">
                                        <i class="bi bi-arrow-up"></i>
                                        AJUSTE / MERMA
                                    </span>

                                @endif

                            </td>


                            {{-- NOTA --}}
                            <td>

                                @if($log->note)

                                    <div class="inventory-note">
                                        {{ $log->note }}
                                    </div>

                                @else

                                    <span class="inventory-note-empty">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- USUARIO --}}
                            <td>

                                <div class="inventory-user">

                                    <div class="inventory-user-avatar">
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    </div>

                                    <span class="inventory-user-name">
                                        {{ $log->user->name ?? 'Sistema' }}
                                    </span>

                                </div>

                            </td>


                            {{-- CANTIDAD --}}
                            <td class="text-center">

                                <span class="inventory-quantity
                                    {{ $log->quantity > 0
                                        ? 'positive'
                                        : ($log->quantity < 0 ? 'negative' : 'neutral') }}">

                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}

                                </span>

                            </td>


                            {{-- SALDO --}}
                            <td class="text-center">

                                <span class="inventory-stock">
                                    {{ $log->new_stock }}
                                </span>

                            </td>

                        </tr>


                    @empty

                        <tr>
                            <td colspan="7"
                                class="inventory-empty">

                                <div class="inventory-empty-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>

                                <div class="inventory-empty-title">
                                    No hay movimientos registrados
                                </div>

                                <p class="inventory-empty-text">
                                    Los movimientos de inventario aparecerán aquí.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINACIÓN ESTÁNDAR DEL SISTEMA --}}
        <x-system-pagination :paginator="$logs" />

    </div>

</div>


<style>
/* DARK MODE - KARDEX COMPLETO DEFINITIVO */

/* =========================================================
   CONTENEDORES PRINCIPALES
   ========================================================= */

html[data-color-mode="dark"] .inventory-back,
html[data-color-mode="dark"] .inventory-legend,
html[data-color-mode="dark"] .inventory-card {
    background: #132338 !important;
    border-color: #30465d !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .inventory-back:hover {
    background: #1a3046 !important;
    border-color: #49647d !important;
    color: #ffffff !important;
}


/* =========================================================
   CONTADOR DE MOVIMIENTOS
   ========================================================= */

html[data-color-mode="dark"] .inventory-count {
    background: #1b3045 !important;
    border-color: #3b536b !important;
    color: #a9bdd0 !important;
}


/* =========================================================
   CABECERA DE TABLA
   ========================================================= */

html[data-color-mode="dark"] .inventory-table thead th {
    background: #192d42 !important;
    border-color: #30465d !important;
    color: #9fc5eb !important;
}


/* =========================================================
   FILAS
   ========================================================= */

html[data-color-mode="dark"] .inventory-table tbody td {
    background: #132338 !important;
    border-color: #2d4359 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .inventory-table tbody tr:hover td {
    background: #182c41 !important;
}


/* =========================================================
   ICONO DEL PRODUCTO
   ========================================================= */

html[data-color-mode="dark"] .inventory-product-icon {
    background: #1c3349 !important;
    border: 1px solid #36516a !important;
    color: #8fb9df !important;
}

html[data-color-mode="dark"] .inventory-product-icon i {
    color: #8fb9df !important;
}

html[data-color-mode="dark"] .inventory-product-name {
    color: #ffffff !important;
}


/* =========================================================
   USUARIO
   ========================================================= */

html[data-color-mode="dark"] .inventory-user-avatar {
    background: #1c3349 !important;
    border: 1px solid #36516a !important;
    color: #dbeafe !important;
}

html[data-color-mode="dark"] .inventory-user-name {
    color: #ffffff !important;
}


/* =========================================================
   MOVIMIENTO - VENTA POS
   ========================================================= */

html[data-color-mode="dark"] .inventory-movement.sale {
    background: rgba(37, 99, 235, .15) !important;
    border-color: rgba(96, 165, 250, .34) !important;
    color: #60a5fa !important;
}

html[data-color-mode="dark"] .inventory-movement.sale i {
    color: #60a5fa !important;
}


/* =========================================================
   MOVIMIENTO - ENTRADA
   ========================================================= */

html[data-color-mode="dark"] .inventory-movement.entry {
    background: rgba(34, 197, 94, .14) !important;
    border-color: rgba(74, 222, 128, .32) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .inventory-movement.entry i {
    color: #4ade80 !important;
}


/* =========================================================
   MOVIMIENTO - AJUSTE / MERMA
   ========================================================= */

html[data-color-mode="dark"] .inventory-movement.adjustment {
    background: rgba(239, 68, 68, .14) !important;
    border-color: rgba(248, 113, 113, .32) !important;
    color: #f87171 !important;
}

html[data-color-mode="dark"] .inventory-movement.adjustment i {
    color: #f87171 !important;
}


/* =========================================================
   CANTIDADES
   ========================================================= */

html[data-color-mode="dark"] .inventory-quantity.positive {
    background: rgba(34, 197, 94, .14) !important;
    border: 1px solid rgba(74, 222, 128, .26) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .inventory-quantity.negative {
    background: rgba(239, 68, 68, .14) !important;
    border: 1px solid rgba(248, 113, 113, .26) !important;
    color: #f87171 !important;
}

html[data-color-mode="dark"] .inventory-quantity.neutral {
    background: #1b3045 !important;
    border: 1px solid #3b536b !important;
    color: #a9bdd0 !important;
}


/* =========================================================
   SALDO
   ========================================================= */

html[data-color-mode="dark"] .inventory-stock {
    background: #102235 !important;
    border-color: #7890a5 !important;
    color: #ffffff !important;
}


/* =========================================================
   FECHA / HORA / NOTAS
   ========================================================= */

html[data-color-mode="dark"] .inventory-date-main {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .inventory-date-main i,
html[data-color-mode="dark"] .inventory-time,
html[data-color-mode="dark"] .inventory-note {
    color: #7fa4c6 !important;
}

html[data-color-mode="dark"] .inventory-note-empty {
    color: #647f98 !important;
}


/* =========================================================
   LEYENDA SUPERIOR
   ========================================================= */

html[data-color-mode="dark"] .inventory-legend {
    background: #132338 !important;
    border: 1px solid #49647d !important;
}

html[data-color-mode="dark"] .inventory-legend-item strong {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .inventory-legend-item {
    color: #7fa4c6 !important;
}

/* Quitar anillos pastel */
html[data-color-mode="dark"] .inventory-legend-dot.entry {
    background: #22c55e !important;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, .18) !important;
}

html[data-color-mode="dark"] .inventory-legend-dot.exit {
    background: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, .18) !important;
}


/* =========================================================
   ESTADO VACIO
   ========================================================= */

html[data-color-mode="dark"] .inventory-empty-icon {
    background: #1b3045 !important;
    border: 1px solid #36516a !important;
    color: #8fa8bf !important;
}

html[data-color-mode="dark"] .inventory-empty-title {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .inventory-empty-text {
    color: #91a8bd !important;
}

</style>

@endsection