@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">
                <i class="bi bi-cash-coin me-2"></i>Caja y Movimientos
            </h2>
            <p class="text-muted mb-0">Control de Ingresos y Egresos</p>
        </div>

        <div class="d-flex gap-2">

            {{-- FILTRO DE FECHAS --}}
            <form action="{{ route('sales.index') }}"
                  method="GET"
                  class="d-flex align-items-center gap-2 bg-white p-2 rounded shadow-sm border">

                <input
                    type="date"
                    name="start_date"
                    class="form-control form-control-sm"
                    value="{{ $startDate }}"
                >

                <span class="text-muted">-</span>

                <input
                    type="date"
                    name="end_date"
                    class="form-control form-control-sm"
                    value="{{ $endDate }}"
                >

                <button
                    type="submit"
                    class="btn btn-primary btn-sm px-3 fw-bold">
                    <i class="bi bi-search"></i>
                </button>

            </form>

            {{-- CORTE Z --}}
            <a
                href="{{ route('sales.daily.report', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                target="_blank"
                class="btn btn-dark fw-bold d-flex align-items-center">

                <i class="bi bi-printer me-2"></i>
                Corte Z
            </a>

            {{-- REGISTRAR SALIDA --}}
            <button
                class="btn btn-danger fw-bold d-flex align-items-center"
                data-bs-toggle="modal"
                data-bs-target="#expenseModal">

                <i class="bi bi-dash-circle me-2"></i>
                Registrar Salida
            </button>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- RESUMEN DE CAJA --}}
    {{-- ========================================================= --}}

    <div class="row g-3 mb-4">

        {{-- VENTA TOTAL --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body">

                    <small class="opacity-75 text-uppercase fw-bold">
                        Venta Total
                    </small>

                    <h3 class="fw-bold mb-0 mt-1">
                        S/ {{ number_format($totalSales, 2) }}
                    </h3>

                    <small>
                        {{ $orders->count() }} operaciones
                    </small>

                </div>
            </div>
        </div>


        {{-- EFECTIVO --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body">

                    <small class="opacity-75 text-uppercase fw-bold">
                        Efectivo
                    </small>

                    <h3 class="fw-bold mb-0 mt-1">
                        S/ {{ number_format($totalCash, 2) }}
                    </h3>

                    <small>
                        Dinero físico ingresado
                    </small>

                </div>
            </div>
        </div>


        {{-- TARJETA --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm bg-info text-white h-100">
                <div class="card-body">

                    <small class="opacity-75 text-uppercase fw-bold">
                        Tarjeta
                    </small>

                    <h3 class="fw-bold mb-0 mt-1">
                        S/ {{ number_format($totalCard, 2) }}
                    </h3>

                    <small>
                        Pagos con tarjeta
                    </small>

                </div>
            </div>
        </div>


        {{-- YAPE --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div
                class="card border-0 shadow-sm text-white h-100"
                style="background-color: #742284;">

                <div class="card-body">

                    <small class="opacity-75 text-uppercase fw-bold">
                        Yape
                    </small>

                    <h3 class="fw-bold mb-0 mt-1">
                        S/ {{ number_format($totalYape, 2) }}
                    </h3>

                    <small>
                        Pagos con Yape
                    </small>

                </div>
            </div>
        </div>


        {{-- PLIN --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                <div class="card-body">

                    <small class="opacity-75 text-uppercase fw-bold">
                        Plin
                    </small>

                    <h3 class="fw-bold mb-0 mt-1">
                        S/ {{ number_format($totalPlin, 2) }}
                    </h3>

                    <small>
                        Pagos con Plin
                    </small>

                </div>
            </div>
        </div>


        {{-- GASTOS --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm bg-danger text-white h-100">
                <div class="card-body">

                    <small class="opacity-75 text-uppercase fw-bold">
                        Gastos / Salidas
                    </small>

                    <h3 class="fw-bold mb-0 mt-1">
                        S/ {{ number_format($totalExpenses, 2) }}
                    </h3>

                    <small>
                        {{ $expenses->count() }} movimientos
                    </small>

                </div>
            </div>
        </div>


        {{-- DINERO EN CAJA --}}
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100
                {{ $balance >= 0 ? 'bg-dark text-white' : 'bg-warning text-dark' }}">

                <div class="card-body">

                    <small class="opacity-75 text-uppercase fw-bold">
                        Dinero en Caja
                    </small>

                    <h3 class="fw-bold mb-0 mt-1">
                        S/ {{ number_format($balance, 2) }}
                    </h3>

                    <small>
                        Efectivo - Gastos
                    </small>

                </div>
            </div>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- HISTORIAL --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white p-0 border-bottom-0">

            <ul
                class="nav nav-tabs ps-3 pt-3"
                id="salesTabs"
                role="tablist">

                {{-- VENTAS --}}
                <li class="nav-item">

                    <button
                        class="nav-link active fw-bold"
                        id="sales-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#sales"
                        type="button">

                        <i class="bi bi-receipt me-2"></i>
                        Historial de Ventas

                    </button>

                </li>


                {{-- GASTOS --}}
                <li class="nav-item">

                    <button
                        class="nav-link fw-bold text-danger"
                        id="expenses-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#expenses"
                        type="button">

                        <i class="bi bi-journal-minus me-2"></i>
                        Historial de Gastos

                    </button>

                </li>

            </ul>

        </div>


        <div class="card-body p-0">

            <div
                class="tab-content"
                id="salesTabsContent">


                {{-- ================================================= --}}
                {{-- HISTORIAL DE VENTAS --}}
                {{-- ================================================= --}}

                <div
                    class="tab-pane fade show active"
                    id="sales"
                    role="tabpanel">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th class="ps-4">
                                        Hora
                                    </th>

                                    <th>
                                        Folio
                                    </th>

                                    <th>
                                        Cliente
                                    </th>

                                    <th>
                                        Mesa
                                    </th>

                                    <th>
                                        Método
                                    </th>

                                    <th class="text-end">
                                        Total
                                    </th>

                                    <th class="text-center">
                                        Ticket
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($orders as $order)

                                    @php

                                        $paymentLabels = [

                                            'cash' => 'Efectivo',

                                            'card' => 'Tarjeta',

                                            'yape' => 'Yape',

                                            'plin' => 'Plin',

                                        ];


                                        $paymentClasses = [

                                            'cash' => 'bg-success text-success border-success',

                                            'card' => 'bg-primary text-primary border-primary',

                                            'yape' => 'bg-purple text-purple border-purple',

                                            'plin' => 'bg-warning text-warning border-warning',

                                        ];


                                        $paymentMethod =
                                            strtolower(
                                                $order->payment_method ?? 'cash'
                                            );


                                        $paymentLabel =
                                            $paymentLabels[$paymentMethod]
                                            ?? ucfirst($paymentMethod);


                                        $paymentClass =
                                            $paymentClasses[$paymentMethod]
                                            ?? 'bg-secondary text-secondary border-secondary';

                                    @endphp


                                    <tr>

                                        {{-- HORA --}}
                                        <td class="ps-4 text-muted">

                                            {{ $order->created_at->format('H:i') }}

                                        </td>


                                        {{-- FOLIO --}}
                                        <td class="fw-bold">

                                            #{{ str_pad(
                                                $order->id,
                                                6,
                                                '0',
                                                STR_PAD_LEFT
                                            ) }}

                                        </td>


                                        {{-- CLIENTE --}}
                                        <td>

                                            {{ $order->client_name }}

                                            <br>

                                            <small class="text-muted">
                                                {{ $order->document_type }}
                                            </small>

                                        </td>


                                        {{-- MESA --}}
                                        <td>

                                            <span class="badge bg-light text-dark border">

                                                {{ $order->table->name ?? 'Barra' }}

                                            </span>

                                        </td>


                                        {{-- MÉTODO DE PAGO --}}
                                        <td>

                                            @if($paymentMethod === 'yape')

                                                <span
                                                    class="badge border"
                                                    style="background-color: #f3e5f5; color: #742284; border-color: #742284 !important;">

                                                    <i class="bi bi-phone me-1"></i>
                                                    Yape

                                                </span>

                                            @elseif($paymentMethod === 'plin')

                                                <span
                                                    class="badge bg-warning bg-opacity-25 text-dark border border-warning">

                                                    <i class="bi bi-phone me-1"></i>
                                                    Plin

                                                </span>

                                            @else

                                                <span
                                                    class="badge {{ $paymentClass }} bg-opacity-10 border">

                                                    {{ $paymentLabel }}

                                                </span>

                                            @endif

                                        </td>


                                        {{-- TOTAL --}}
                                        <td class="text-end fw-bold">

                                            S/ {{ number_format(
                                                $order->total,
                                                2
                                            ) }}

                                        </td>


                                        {{-- TICKET --}}
                                        <td class="text-center">

                                            <a
                                                href="{{ route('sales.ticket', $order->id) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-dark">

                                                <i class="bi bi-printer"></i>

                                            </a>

                                        </td>

                                    </tr>


                                @empty

                                    <tr>

                                        <td
                                            colspan="7"
                                            class="text-center py-5 text-muted">

                                            No hay ventas en este rango.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- HISTORIAL DE GASTOS --}}
                {{-- ================================================= --}}

                <div
                    class="tab-pane fade"
                    id="expenses"
                    role="tabpanel">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th class="ps-4">
                                        Hora
                                    </th>

                                    <th>
                                        Descripción / Motivo
                                    </th>

                                    <th>
                                        Registrado Por
                                    </th>

                                    <th class="text-end text-danger">
                                        Monto
                                    </th>

                                    <th class="text-end pe-4">
                                        Acción
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($expenses as $expense)

                                    <tr>

                                        <td class="ps-4 text-muted">

                                            {{ $expense->created_at->format('d/m H:i') }}

                                        </td>


                                        <td class="fw-bold">

                                            {{ $expense->description }}

                                        </td>


                                        <td>

                                            <small class="text-muted">

                                                <i class="bi bi-person"></i>

                                                {{ $expense->user->name }}

                                            </small>

                                        </td>


                                        <td class="text-end fw-bold text-danger">

                                            -S/ {{ number_format(
                                                $expense->amount,
                                                2
                                            ) }}

                                        </td>


                                        <td class="text-end pe-4">

                                            <form
                                                action="{{ route(
                                                    'expenses.destroy',
                                                    $expense->id
                                                ) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Eliminar este gasto?')">

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    class="btn btn-sm text-danger p-0">

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            </form>

                                        </td>

                                    </tr>


                                @empty

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="text-center py-5 text-muted">

                                            No hay gastos registrados.

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

</div>


{{-- ============================================================= --}}
{{-- MODAL REGISTRAR SALIDA --}}
{{-- ============================================================= --}}

<div
    class="modal fade"
    id="expenseModal"
    tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <form
            action="{{ route('expenses.store') }}"
            method="POST"
            class="modal-content">

            @csrf


            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title fw-bold">

                    Registrar Salida de Dinero

                </h5>


                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">

                <div
                    class="alert alert-warning d-flex align-items-center"
                    role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <small>
                        Esta acción restará dinero del efectivo en caja.
                    </small>

                </div>


                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Descripción del Gasto
                    </label>

                    <input
                        type="text"
                        name="description"
                        class="form-control"
                        placeholder="Ej: Compra de hielo, Pago proveedor..."
                        required>

                </div>


                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Monto a Retirar
                    </label>


                    <div class="input-group">

                        <span class="input-group-text">
                            S/
                        </span>


                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            name="amount"
                            class="form-control fs-4 fw-bold text-danger"
                            placeholder="0.00"
                            required>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>


                <button
                    type="submit"
                    class="btn btn-danger fw-bold">

                    Registrar Salida

                </button>

            </div>

        </form>

    </div>

</div>

@endsection