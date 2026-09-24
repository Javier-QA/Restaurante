@extends('layouts.app')

@section('content')

<style>
    .sales-kpi {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
        min-height: 132px;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 14px rgba(15, 23, 42, .07);
        position: relative;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .sales-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(15, 23, 42, .14);
    }

    .sales-kpi::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, var(--kpi-color) 0%, color-mix(in srgb, var(--kpi-color) 25%, white) 65%, white 100%);
        border-radius: 16px 0 0 16px;
    }

    .sales-kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.5rem;
        color: var(--kpi-color);
        background: var(--kpi-bg);
    }

    .sales-kpi-content {
        flex: 1;
        min-width: 0;
    }

    .sales-kpi-label {
        display: block;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 4px;
    }

    .sales-kpi-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 5px;
        white-space: nowrap;
    }

    .sales-kpi-sub {
        font-size: .72rem;
        color: #64748b;
        display: flex;
        align-items: center;
    }
    .sales-kpi-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .08em;
        padding: 3px 8px;
        border-radius: 20px;
        color: var(--kpi-color);
        background: var(--kpi-bg);
    }

    /* Selector Historial de Ventas / Gastos */
    .sales-history-tabs {
        display: inline-flex;
        gap: 6px;
        padding: 5px;
        margin: 14px 16px;
        background: #f1f5f9;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
    }

    .sales-history-tabs .nav-link {
        border: 0 !important;
        border-radius: 10px !important;
        padding: 9px 16px;
        font-size: .82rem;
        font-weight: 700;
        color: #64748b;
        background: transparent;
        transition: all .2s ease;
    }

    .sales-history-tabs .nav-link:hover {
        background: #fff;
        color: #0f172a;
    }

    .sales-history-tabs .nav-link.active {
        background: #fff !important;
        color: #198754 !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .10);
    }

    .sales-history-tabs .expenses-history-tab.active {
        color: #dc3545 !important;
    }

    .sales-history-tabs .nav-link i {
        font-size: 1rem;
    }

    /* Acciones de Caja */
    .sales-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 40px;
        padding: 7px 14px;
        border-radius: 11px;
        font-size: .82rem;
        font-weight: 700;
        border: 1px solid transparent;
        transition: all .2s ease;
        text-decoration: none;
        box-shadow: 0 2px 7px rgba(15, 23, 42, .06);
    }

    .sales-action-btn i {
        width: 25px;
        height: 25px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        font-size: .9rem;
    }

    .sales-action-report {
        color: #334155;
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .sales-action-report i {
        color: #1e293b;
        background: #e2e8f0;
    }

    .sales-action-report:hover {
        color: #0f172a;
        background: #e2e8f0;
        border-color: #94a3b8;
        transform: translateY(-1px);
        box-shadow: 0 5px 12px rgba(15, 23, 42, .10);
    }

    .sales-action-expense {
        color: #dc2626;
        background: #fff1f2;
        border-color: #fecaca;
    }

    .sales-action-expense i {
        color: #dc2626;
        background: #ffe4e6;
    }

    .sales-action-expense:hover {
        color: #b91c1c;
        background: #ffe4e6;
        border-color: #fca5a5;
        transform: translateY(-1px);
        box-shadow: 0 5px 12px rgba(220, 38, 38, .12);
    }
</style>
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="bi bi-cash-coin me-2"></i>Caja y Movimientos</h2>
            <p class="text-muted mb-0">Control de Ingresos y Egresos</p>
        </div>
        
        <div class="d-flex gap-2">
            <form action="{{ route('sales.index') }}" method="GET" class="d-flex align-items-center gap-2 bg-white p-2 rounded shadow-sm border">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                <span class="text-muted">-</span>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold"><i class="bi bi-search"></i></button>
            </form>
            
            <a href="{{ route('sales.daily.report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="sales-action-btn sales-action-report">
                <i class="bi bi-printer"></i><span>Corte Z</span>
            </a>
            
            <button class="sales-action-btn sales-action-expense" data-bs-toggle="modal" data-bs-target="#expenseModal">
                <i class="bi bi-dash-circle"></i><span>Registrar Salida</span>
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4 align-items-stretch">

        {{-- VENTA TOTAL - BLOQUE PRINCIPAL --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="sales-kpi" style="--kpi-color:#84cc16; --kpi-bg:#f1f8e5;">
                <div class="sales-kpi-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>

                <div class="sales-kpi-content">
                    <span class="sales-kpi-label">Venta Total</span>

                    <div class="sales-kpi-value">
                        S/ {{ number_format($totalSales, 2) }}
                    </div>

                    <div class="sales-kpi-sub">
                        <i class="bi bi-receipt me-1"></i>
                        {{ $orders->count() }} operaciones
                    </div>
                </div>

                <div class="sales-kpi-badge">TOTAL</div>
            </div>
        </div>

        {{-- MÉTODOS, GASTOS Y BALANCE --}}
        
            

                {{-- EFECTIVO --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="sales-kpi" style="--kpi-color:#198754; --kpi-bg:#e8f5ee;">
                        <div class="sales-kpi-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="sales-kpi-content">
                            <span class="sales-kpi-label">Efectivo</span>
                            <div class="sales-kpi-value">S/ {{ number_format($totalCash, 2) }}</div>
                            <div class="sales-kpi-sub">
                                <i class="bi bi-wallet2 me-1"></i> Dinero físico ingresado
                            </div>
                        </div>
                        <div class="sales-kpi-badge">CAJA</div>
                    </div>
                </div>

                {{-- YAPE --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="sales-kpi" style="--kpi-color:#742284; --kpi-bg:#f5e9f8;">
                        <div class="sales-kpi-icon">
                            <i class="bi bi-qr-code"></i>
                        </div>
                        <div class="sales-kpi-content">
                            <span class="sales-kpi-label">Yape</span>
                            <div class="sales-kpi-value">S/ {{ number_format($totalYape, 2) }}</div>
                            <div class="sales-kpi-sub">
                                <i class="bi bi-phone me-1"></i> Pagos digitales
                            </div>
                        </div>
                        <div class="sales-kpi-badge">YAPE</div>
                    </div>
                </div>

                {{-- PLIN --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="sales-kpi" style="--kpi-color:#00a884; --kpi-bg:#e8f8f3;">
                        <div class="sales-kpi-icon">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <div class="sales-kpi-content">
                            <span class="sales-kpi-label">Plin</span>
                            <div class="sales-kpi-value">S/ {{ number_format($totalPlin, 2) }}</div>
                            <div class="sales-kpi-sub">
                                <i class="bi bi-phone me-1"></i> Pagos digitales
                            </div>
                        </div>
                        <div class="sales-kpi-badge">PLIN</div>
                    </div>
                </div>

                {{-- TARJETA --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="sales-kpi" style="--kpi-color:#0d6efd; --kpi-bg:#eaf2ff;">
                        <div class="sales-kpi-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="sales-kpi-content">
                            <span class="sales-kpi-label">Tarjeta</span>
                            <div class="sales-kpi-value">S/ {{ number_format($totalCard, 2) }}</div>
                            <div class="sales-kpi-sub">
                                <i class="bi bi-credit-card-2-front me-1"></i> Pagos con tarjeta
                            </div>
                        </div>
                        <div class="sales-kpi-badge">POS</div>
                    </div>
                </div>

                {{-- GASTOS --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="sales-kpi" style="--kpi-color:#ef4444; --kpi-bg:#fff1f2;">
                        <div class="sales-kpi-icon">
                            <i class="bi bi-arrow-down-circle"></i>
                        </div>
                        <div class="sales-kpi-content">
                            <span class="sales-kpi-label">Gastos / Salidas</span>
                            <div class="sales-kpi-value">S/ {{ number_format($totalExpenses, 2) }}</div>
                            <div class="sales-kpi-sub">
                                <i class="bi bi-journal-minus me-1"></i>
                                {{ $expenses->count() }} movimientos
                            </div>
                        </div>
                        <div class="sales-kpi-badge">SALIDA</div>
                    </div>
                </div>

                {{-- BALANCE --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="sales-kpi"
                         style="--kpi-color:{{ $balance >= 0 ? '#0f766e' : '#ef4444' }}; --kpi-bg:{{ $balance >= 0 ? '#e7f7f5' : '#fff1f2' }};">
                        <div class="sales-kpi-icon">
                            <i class="bi bi-safe2"></i>
                        </div>
                        <div class="sales-kpi-content">
                            <span class="sales-kpi-label">Balance de Caja</span>
                            <div class="sales-kpi-value">S/ {{ number_format($balance, 2) }}</div>
                            <div class="sales-kpi-sub">
                                <i class="bi bi-calculator me-1"></i> Efectivo - gastos
                            </div>
                        </div>
                        <div class="sales-kpi-badge">BALANCE</div>
                    </div>
                </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-0 border-bottom-0">
            <ul class="nav sales-history-tabs" id="salesTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button">
                        <i class="bi bi-receipt me-2"></i> Historial de Ventas
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link expenses-history-tab" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button">
                        <i class="bi bi-journal-minus me-2"></i> Historial de Gastos
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="salesTabsContent">
                
                <div class="tab-pane fade show active" id="sales" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Hora</th>
                                    <th>Folio</th>
                                    <th>Cliente</th>
                                    <th>Mesa</th>
                                    <th>Método</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Ticket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $order->created_at->format('H:i') }}</td>
                                    <td class="fw-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $order->client_name }} <br><small class="text-muted">{{ $order->document_type }}</small></td>
                                    <td>
                                        @if($order->delivery)
                                            <span class="badge bg-primary">
                                                <i class="bi bi-bicycle me-1"></i> Delivery
                                            </span>
                                        @elseif($order->table)
                                            <span class="badge bg-light text-dark border">
                                                {{ $order->table->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border">Barra</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $paymentStyles = [
                                                'cash' => ['label' => 'Efectivo', 'color' => '#198754', 'bg' => '#e8f5ee'],
                                                'card' => ['label' => 'Tarjeta', 'color' => '#0d6efd', 'bg' => '#eaf2ff'],
                                                'yape' => ['label' => 'Yape', 'color' => '#742284', 'bg' => '#f5e9f8'],
                                                'plin' => ['label' => 'Plin', 'color' => '#00a884', 'bg' => '#e8f8f3'],
                                            ];
                                            $payment = $paymentStyles[$order->payment_method] ?? [
                                                'label' => ucfirst($order->payment_method ?? 'Sin definir'),
                                                'color' => '#6c757d',
                                                'bg' => '#f1f3f5'
                                            ];
                                        @endphp
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background:{{ $payment['bg'] }}; color:{{ $payment['color'] }}; border:1px solid {{ $payment['color'] }};">
                                            {{ $payment['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($order->total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('sales.ticket', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="bi bi-printer"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted">No hay ventas en este rango.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="expenses" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Hora</th>
                                    <th>Descripción / Motivo</th>
                                    <th>Registrado Por</th>
                                    <th class="text-end text-danger">Monto</th>
                                    <th class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $expense->created_at->format('d/m H:i') }}</td>
                                    <td class="fw-bold">{{ $expense->description }}</td>
                                    <td><small class="text-muted"><i class="bi bi-person"></i> {{ $expense->user->name }}</small></td>
                                    <td class="text-end fw-bold text-danger">-{{ number_format($expense->amount, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este gasto?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm text-danger p-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No hay gastos registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('expenses.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Registrar Salida de Dinero</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <small>Esta acción restará dinero del efectivo en caja.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción del Gasto</label>
                    <input type="text" name="description" class="form-control" placeholder="Ej: Compra de hielo, Pago proveedor..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Monto a Retirar</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold">S/</span>
                        <input type="number" step="0.01" name="amount" class="form-control fs-4 fw-bold text-danger" placeholder="0.00" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Registrar Salida</button>
            </div>
        </form>
    </div>
</div>
@endsection