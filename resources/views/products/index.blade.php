@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

<div>
    <h2 class="fw-bold text-dark mb-0">
        <i class="bi bi-box-seam-fill me-2"></i>
        Inventario de Productos
    </h2>

    <p class="text-muted mb-0">
        Gestión de carta y existencias
    </p>
</div>

<div>
    <a href="{{ route('inventory.logs') }}" class="btn btn-dark me-2">
        <i class="bi bi-clock-history me-1"></i>
        Ver Kardex
    </a>

    <a href="{{ route('products.create') }}" class="btn btn-primary fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Nuevo Producto
    </a>
</div>

</div>

{{-- CONTENEDOR PRINCIPAL --}}

<div class="card border-0 shadow-sm overflow-hidden">

{{-- TABLA --}}
<div class="card-body p-0">

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="bg-light">
                <tr>

                    <th class="ps-4 text-uppercase text-muted small fw-bold">
                        Producto
                    </th>

                    <th class="text-uppercase text-muted small fw-bold">
                        Categoría
                    </th>

                    <th class="text-uppercase text-muted small fw-bold">
                        Precio
                    </th>

                    <th class="text-uppercase text-muted small fw-bold">
                        Stock
                    </th>

                    <th class="text-center text-uppercase text-muted small fw-bold">
                        Estado
                    </th>

                    <th class="text-end pe-4 text-uppercase text-muted small fw-bold">
                        Acciones
                    </th>

                </tr>
            </thead>


            <tbody>

                @forelse($products as $product)

                    <tr>

                        {{-- PRODUCTO --}}
                        <td class="ps-4">

                            <div class="d-flex align-items-center">

                                @if($product->image)

                                    <img
                                        src="{{ asset('storage/'.$product->image) }}"
                                        class="rounded me-3 border"
                                        width="48"
                                        height="48"
                                        style="object-fit: cover;"
                                        alt="{{ $product->name }}"
                                    >

                                @else

                                    <div
                                        class="bg-light rounded me-3 d-flex align-items-center justify-content-center border text-muted"
                                        style="width:48px;height:48px;"
                                    >
                                        <i class="bi bi-image"></i>
                                    </div>

                                @endif


                                <div>

                                    <div class="fw-bold text-dark">
                                        {{ $product->name }}
                                    </div>

                                    @if(!$product->is_saleable)

                                        <span
                                            class="badge bg-secondary"
                                            style="font-size:0.65rem;"
                                        >
                                            <i class="bi bi-eye-slash me-1"></i>
                                            Solo Insumo
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- CATEGORÍA --}}
                        <td>

                            <span class="badge bg-light text-dark border">
                                {{ $product->category->name }}
                            </span>

                        </td>


                        {{-- PRECIO --}}
                        <td class="fw-bold text-primary">

                            S/
                            {{ number_format($product->price, 2) }}

                        </td>


                        {{-- STOCK --}}
                        <td>

                            @if(is_null($product->stock))

                                <span class="text-muted small">
                                    --
                                </span>

                            @elseif($product->stock <= 5)

                                <span class="badge bg-warning text-dark border border-warning">
                                    Bajo: {{ $product->stock }}
                                </span>

                            @else

                                <span class="badge bg-light text-success border border-success fw-bold">
                                    {{ $product->stock }}
                                </span>

                            @endif

                        </td>


                        {{-- ESTADO --}}
                        <td class="text-center">

                            <form
                                action="{{ route('products.toggle', $product->id) }}"
                                method="POST"
                                class="m-0"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-sm
                                    {{ $product->is_active
                                        ? 'btn-outline-success'
                                        : 'btn-outline-secondary'
                                    }}
                                    rounded-pill px-3 fw-bold"
                                    style="font-size:0.75rem;"
                                >

                                    {{ $product->is_active ? 'ACTIVO' : 'INACTIVO' }}

                                </button>

                            </form>

                        </td>


                        {{-- ACCIONES --}}
                        <td class="text-end pe-4">

                            <div class="btn-group">

                                {{-- AJUSTAR STOCK --}}
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#adjustStock{{ $product->id }}"
                                    title="Ajustar Stock"
                                >
                                    <i class="bi bi-arrow-left-right"></i>
                                </button>


                                {{-- EDITAR --}}
                                <a
                                    href="{{ route('products.edit', $product->id) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Editar"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </a>


                                {{-- ELIMINAR --}}
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="if(confirm('¿Eliminar producto?')) document.getElementById('del-{{ $product->id }}').submit()"
                                    title="Eliminar"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>

                            </div>


                            {{-- FORMULARIO ELIMINAR --}}
                            <form
                                id="del-{{ $product->id }}"
                                action="{{ route('products.destroy', $product->id) }}"
                                method="POST"
                                class="d-none"
                            >

                                @csrf
                                @method('DELETE')

                            </form>


                            {{-- MODAL AJUSTAR STOCK --}}
                            <div
                                class="modal fade"
                                id="adjustStock{{ $product->id }}"
                                tabindex="-1"
                                aria-hidden="true"
                            >

                                <div class="modal-dialog modal-sm modal-dialog-centered">

                                    <form
                                        action="{{ route('products.adjust', $product->id) }}"
                                        method="POST"
                                        class="modal-content"
                                    >

                                        @csrf


                                        <div class="modal-header py-2 bg-light">

                                            <h6 class="modal-title fw-bold">
                                                Ajustar Stock
                                            </h6>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                            ></button>

                                        </div>


                                        <div class="modal-body text-start">

                                            <p class="small mb-2">

                                                Producto:

                                                <strong>
                                                    {{ $product->name }}
                                                </strong>

                                            </p>


                                            <p class="small text-muted mb-3">

                                                Stock actual:

                                                <strong>

                                                    {{ is_null($product->stock)
                                                        ? '--'
                                                        : $product->stock
                                                    }}

                                                </strong>

                                            </p>


                                            <div class="input-group mb-3">

                                                <select
                                                    name="type"
                                                    class="form-select"
                                                    style="max-width:90px;"
                                                    required
                                                >

                                                    <option value="add">
                                                        +
                                                    </option>

                                                    <option value="sub">
                                                        -
                                                    </option>

                                                </select>


                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    class="form-control"
                                                    placeholder="Cantidad"
                                                    required
                                                    min="1"
                                                >

                                            </div>

                                        </div>


                                        <div class="modal-footer p-1">

                                            <button
                                                type="submit"
                                                class="btn btn-primary w-100 btn-sm"
                                            >
                                                Guardar Ajuste
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-5 text-muted"
                        >

                            <i class="bi bi-box-seam fs-2 d-block mb-2"></i>

                            No hay productos registrados.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- PAGINACIÓN --}}
@if($products->total() > 0)

<div class="card-footer bg-white border-top px-4 py-3">

    {{-- BOTONES DE PAGINACIÓN --}}
    @if($products->hasPages())

        <nav
            aria-label="Paginación de productos"
            class="d-flex justify-content-center"
        >

            <ul class="pagination pagination-sm mb-2">

                {{-- ANTERIOR --}}
                @if($products->onFirstPage())

                    <li class="page-item disabled">
                        <span class="page-link">‹</span>
                    </li>

                @else

                    <li class="page-item">
                        <a
                            class="page-link"
                            href="{{ $products->previousPageUrl() }}"
                            aria-label="Anterior"
                        >
                            ‹
                        </a>
                    </li>

                @endif


                {{-- NÚMEROS --}}
                @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)

                    @if($page == $products->currentPage())

                        <li class="page-item active">
                            <span class="page-link">
                                {{ $page }}
                            </span>
                        </li>

                    @else

                        <li class="page-item">
                            <a
                                class="page-link"
                                href="{{ $url }}"
                            >
                                {{ $page }}
                            </a>
                        </li>

                    @endif

                @endforeach


                {{-- SIGUIENTE --}}
                @if($products->hasMorePages())

                    <li class="page-item">
                        <a
                            class="page-link"
                            href="{{ $products->nextPageUrl() }}"
                            aria-label="Siguiente"
                        >
                            ›
                        </a>
                    </li>

                @else

                    <li class="page-item disabled">
                        <span class="page-link">›</span>
                    </li>

                @endif

            </ul>

        </nav>

    @endif


    {{-- INFORMACIÓN DE PRODUCTOS --}}
    <div class="text-center text-muted small mt-2">

        Mostrando

        <strong class="text-dark">
            {{ $products->firstItem() }}
        </strong>

        a

        <strong class="text-dark">
            {{ $products->lastItem() }}
        </strong>

        de

        <strong class="text-dark">
            {{ $products->total() }}
        </strong>

        productos

    </div>

</div>

@endif

{{-- ESTILOS DEL INVENTARIO --}}

<style>

    /* Filas compactas */
    .table > :not(caption) > * > * {
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
    }


    /* Pie de tabla */
    .card-footer {
        min-height: auto !important;
    }


    /* Paginación */
    .pagination {
        margin-bottom: 0 !important;
        justify-content: center;
    }


    .pagination .page-link {
        min-width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 6px !important;

        margin-left: 3px;
        margin-right: 3px;

        font-weight: 500;
    }


    .pagination .page-item.active .page-link {
        font-weight: 700;
    }


    /* Responsive */
    @media (max-width: 768px) {

        .card-footer {
            text-align: center;
        }

        .pagination {
            justify-content: center;
        }

    }

</style>

@endsection