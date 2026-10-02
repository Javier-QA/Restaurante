@extends('layouts.app')

@section('content')

<style>
.category-page {
    --cat-green: #16a34a;
    --cat-red: #dc2626;
}

.category-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 22px;
}

.category-title {
    margin: 0;
    color: var(--text-main);
    font-size: 1.55rem;
    font-weight: 800;
    letter-spacing: -.025em;
}

.category-subtitle {
    margin: 5px 0 0;
    color: var(--text-muted);
    font-size: .86rem;
}

.category-new-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 43px;
    padding: 0 17px;
    border: 0;
    border-radius: 11px;
    background: var(--primary);
    color: #fff;
    font-size: .82rem;
    font-weight: 750;
    box-shadow: 0 6px 16px color-mix(in srgb, var(--primary) 22%, transparent);
    transition: .18s ease;
}

.category-new-btn:hover {
    background: var(--primary-hover);
    color: #fff;
    transform: translateY(-1px);
}

.category-list-card {
    overflow: hidden;
    background: var(--card-bg);
    border: 1px solid var(--border-soft);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-soft);
}

.category-list-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 17px 20px;
    border-bottom: 1px solid var(--border-soft);
}

.category-list-title {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0;
    color: var(--text-main);
    font-size: .9rem;
    font-weight: 800;
}

.category-list-title i {
    color:var(--text-main);
}

.category-count {
    padding: 5px 10px;
    border: 1px solid var(--border-soft);
    border-radius: 999px;
    background: var(--light-bg);
    color: var(--text-muted);
    font-size: .72rem;
    font-weight: 700;
}

.category-table {
    margin: 0;
}

.category-table thead th {
    padding: 13px 20px;
    background: var(--light-bg);
    border-bottom: 1px solid var(--border-soft);
    color: var(--text-muted);
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .035em;
    text-transform: uppercase;
    white-space: nowrap;
}

.category-table tbody td {
    padding: 13px 20px;
    border-color: var(--border-soft);
    color: var(--text-main);
    vertical-align: middle;
}

.category-table tbody tr:last-child td {
    border-bottom: 0;
}

.category-table tbody tr:hover td {
    background: color-mix(in srgb, var(--primary) 2.5%, var(--card-bg));
}

.category-image {
    width: 54px;
    height: 54px;
    display: block;
    object-fit: cover;
    border: 1px solid var(--border-soft);
    border-radius: 13px;
    background: var(--light-bg);
}

.category-image-empty {
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px dashed var(--border-soft);
    border-radius: 13px;
    background: var(--light-bg);
    color: var(--text-muted);
    font-size: 1.05rem;
}

.category-name {
    font-size: .86rem;
    font-weight: 750;
}

.category-status-form {
    display: inline-block;
    margin: 0;
}

.category-state-switch {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 6px 10px 6px 7px;
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    background: var(--card-bg);
    cursor: pointer;
    transition: .18s ease;
}

.category-state-switch:hover {
    background: var(--light-bg);
}

.category-state-track {
    position: relative;
    width: 32px;
    height: 18px;
    flex-shrink: 0;
    border-radius: 999px;
    transition: .2s ease;
}

.category-state-dot {
    position: absolute;
    top: 3px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 1px 4px rgba(15, 23, 42, .22);
    transition: .2s ease;
}

.category-state-switch.is-active .category-state-track {
    background: #22c55e;
}

.category-state-switch.is-active .category-state-dot {
    left: 17px;
}

.category-state-switch.is-active .category-state-text {
    color: #15803d;
}

.category-state-switch.is-inactive .category-state-track {
    background: #cbd5e1;
}

.category-state-switch.is-inactive .category-state-dot {
    left: 3px;
}

.category-state-switch.is-inactive .category-state-text {
    color: #64748b;
}

.category-state-text {
    min-width: 47px;
    text-align: left;
    font-size: .72rem;
    font-weight: 750;
    transition: .18s ease;
}
.category-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 7px;
}

.category-action {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid var(--border-soft);
    border-radius: 9px;
    background: var(--card-bg);
    transition: .16s ease;
}

.category-action.edit {
    color: #f59e0b;
}

.category-action.delete {
    color: #dc2626;
}

.category-action.edit:hover {
    background: #fffbeb;
    border-color: #fde68a;
    color: #d97706;
}

.category-action.delete:hover {
    background: #fff1f2;
    border-color: #fecdd3;
    color: #dc2626;
}

.category-empty {
    padding: 55px 20px !important;
    text-align: center;
}

.category-empty-icon {
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

.category-empty-title {
    margin-bottom: 3px;
    color: var(--text-main);
    font-size: .87rem;
    font-weight: 750;
}

.category-empty-text {
    margin: 0;
    color: var(--text-muted);
    font-size: .78rem;
}


/* MODALES */

.category-modal-content {
    overflow: hidden;
    border: 1px solid var(--border-soft);
    border-radius: 19px;
    background: var(--card-bg);
    box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
}

.category-modal-header {
    padding: 22px 24px 12px;
    border: 0;
}

.category-modal-heading {
    display: flex;
    align-items: center;
    gap: 12px;
}

.category-modal-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 13px;
    background: color-mix(in srgb, var(--primary) 10%, var(--card-bg));
    color: var(--primary);
    font-size: 1rem;
}

.category-modal-title {
    margin: 0;
    color: var(--text-main);
    font-size: 1rem;
    font-weight: 800;
}

.category-modal-subtitle {
    margin: 3px 0 0;
    color: var(--text-muted);
    font-size: .75rem;
}

.category-modal-body {
    padding: 10px 24px 8px;
}

.category-field-label {
    margin-bottom: 7px;
    color: var(--text-main);
    font-size: .77rem;
    font-weight: 750;
}

.category-modal-content .form-control {
    min-height: 45px;
    border-color: var(--border-soft);
    border-radius: 11px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .84rem;
}

.category-modal-content .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--primary) 12%, transparent);
}

.category-file-box {
    padding: 13px;
    border: 1px solid var(--border-soft);
    border-radius: 13px;
    background: var(--light-bg);
}

.category-current-image {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
}

.category-help {
    display: block;
    margin-top: 7px;
    color: var(--text-muted);
    font-size: .7rem;
}

.category-modal-footer {
    gap: 8px;
    padding: 15px 24px 22px;
    border: 0;
}

.category-cancel-btn,
.category-save-btn {
    min-height: 42px;
    padding: 0 18px;
    border-radius: 11px;
    font-size: .81rem;
    font-weight: 750;
}

.category-cancel-btn {
    border: 1px solid var(--border-soft);
    background: var(--card-bg);
    color: var(--text-main);
}

.category-cancel-btn:hover {
    background: var(--light-bg);
    color: var(--text-main);
}

.category-save-btn {
    border: 0;
    background: var(--primary);
    color: #fff;
    box-shadow: 0 5px 14px color-mix(in srgb, var(--primary) 20%, transparent);
}

.category-save-btn:hover {
    background: var(--primary-hover);
    color: #fff;
}


/* ELIMINAR */

.category-delete-content {
    overflow: hidden;
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    background: var(--card-bg);
    box-shadow: 0 24px 70px rgba(15, 23, 42, .20);
}

.category-delete-body {
    padding: 30px 28px 20px;
    text-align: center;
}

.category-delete-icon {
    width: 66px;
    height: 66px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 17px;
    border: 1px solid #fecdd3;
    border-radius: 20px;
    background: #fff1f2;
    color: #dc2626;
    font-size: 1.5rem;
}

.category-delete-title {
    margin-bottom: 8px;
    color: var(--text-main);
    font-size: 1.1rem;
    font-weight: 800;
}

.category-delete-text {
    margin: 0;
    color: var(--text-muted);
    font-size: .84rem;
    line-height: 1.6;
}

.category-delete-name {
    color: var(--text-main);
    font-weight: 800;
}

.category-delete-warning {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin-top: 16px;
    padding: 9px 12px;
    border: 1px solid #fecdd3;
    border-radius: 10px;
    background: #fff1f2;
    color: #b91c1c;
    font-size: .74rem;
    font-weight: 650;
}

.category-delete-footer {
    display: flex;
    gap: 9px;
    padding: 0 28px 26px;
}

.category-delete-footer > * {
    flex: 1;
}

.category-delete-footer button {
    width: 100%;
    min-height: 43px;
    border-radius: 11px;
    font-size: .81rem;
    font-weight: 750;
}

.category-delete-cancel {
    border: 1px solid var(--border-soft);
    background: var(--card-bg);
    color: var(--text-main);
}

.category-delete-confirm {
    border: 1px solid #dc2626;
    background: #dc2626;
    color: #fff;
}

.category-delete-confirm:hover {
    border-color: #b91c1c;
    background: #b91c1c;
    color: #fff;
}

@media (max-width: 767.98px) {
    .category-header {
        align-items: stretch;
        flex-direction: column;
    }

    .category-new-btn {
        width: 100%;
    }

    .category-table tbody td,
    .category-table thead th {
        white-space: nowrap;
    }
}
</style>


<div class="container-fluid category-page">

    {{-- ENCABEZADO --}}
    <div class="category-header">

                <div>
            <h2 class="category-title">
                <i class="bi bi-tags-fill me-2" style="color:var(--text-main);"></i>Categorías
            </h2>
            <p class="category-subtitle">
                Organiza y administra las categorías de los productos del menú
            </p>
        </div>

        <button type="button"
                class="category-new-btn"
                data-bs-toggle="modal"
                data-bs-target="#createCategoryModal">
            <i class="bi bi-plus-lg"></i>
            Nueva categoría
        </button>

    </div>


    {{-- LISTADO --}}
    <div class="category-list-card">

        <div class="category-list-head">
            <h6 class="category-list-title">
                <i class="bi bi-tags"></i>
                Lista de categorías
            </h6>

            <span class="category-count">
                {{ $categories->total() }}
                {{ $categories->total() === 1 ? 'categoría' : 'categorías' }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table category-table align-middle">

                <thead>
                    <tr>
                        <th style="width:90px;">Imagen</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)

                        <tr>

                            <td>
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}"
                                         alt="{{ $category->name }}"
                                         class="category-image">
                                @else
                                    <div class="category-image-empty">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="category-name">
                                    {{ $category->name }}
                                </span>
                            </td>

                            <td>
                                <form action="{{ route('categories.update', $category) }}"
                                      method="POST"
                                      class="category-status-form">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden"
                                           name="name"
                                           value="{{ $category->name }}">

                                    <input type="hidden"
                                           name="is_active"
                                           value="{{ $category->is_active ? 0 : 1 }}">

                                    <button type="submit"
                                            class="category-state-switch {{ $category->is_active ? 'is-active' : 'is-inactive' }}"
                                            title="{{ $category->is_active ? 'Desactivar categoría' : 'Activar categoría' }}">

                                        <span class="category-state-track">
                                            <span class="category-state-dot"></span>
                                        </span>

                                        <span class="category-state-text">
                                            {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </button>
                                </form>
                            </td>

                            <td>
                                <div class="category-actions">

                                    <button type="button"
                                            class="category-action edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal{{ $category->id }}"
                                            title="Editar categoría">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button type="button"
                                            class="category-action delete"
                                            title="Eliminar categoría"
                                            data-category-id="{{ $category->id }}"
                                            data-category-name="{{ $category->name }}"
                                            onclick="confirmDeleteCategory(this)">
                                        <i class="bi bi-trash3"></i>
                                    </button>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="category-empty">

                                <div class="category-empty-icon">
                                    <i class="bi bi-tags"></i>
                                </div>

                                <div class="category-empty-title">
                                    No hay categorías registradas
                                </div>

                                <p class="category-empty-text">
                                    Crea una categoría para comenzar a organizar tus productos.
                                </p>

                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

        <x-system-pagination :paginator="$categories" />

    </div>

</div>


{{-- ======================================================
     EDITAR CATEGORÍA
====================================================== --}}
@foreach($categories as $category)

<div class="modal fade"
     id="editCategoryModal{{ $category->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content category-modal-content">

            <form action="{{ route('categories.update', $category) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="modal-header category-modal-header">

                    <div class="category-modal-heading">

                        <div class="category-modal-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>

                        <div>
                            <h5 class="category-modal-title">
                                Editar categoría
                            </h5>

                            <p class="category-modal-subtitle">
                                Actualiza la información de la categoría
                            </p>
                        </div>

                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">
                    </button>

                </div>


                <div class="modal-body category-modal-body">

                    <div class="mb-3">

                        <label class="form-label category-field-label">
                            Nombre de la categoría
                        </label>

                        <input type="text"
                               class="form-control"
                               name="name"
                               value="{{ $category->name }}"
                               required
                               maxlength="50">

                    </div>


                    <div class="mb-2">

                        <label class="form-label category-field-label">
                            Imagen
                            <span style="color:var(--text-muted);font-weight:500;">
                                (opcional)
                            </span>
                        </label>

                        <div class="category-file-box">

                            @if($category->image)
                                <div class="d-flex align-items-center gap-3 mb-3">

                                    <img src="{{ asset('storage/' . $category->image) }}"
                                         alt="{{ $category->name }}"
                                         class="category-current-image">

                                    <div>
                                        <div class="fw-bold"
                                             style="font-size:.78rem;color:var(--text-main);">
                                            Imagen actual
                                        </div>

                                        <div style="font-size:.7rem;color:var(--text-muted);">
                                            Puedes reemplazarla seleccionando otra imagen.
                                        </div>
                                    </div>

                                </div>
                            @endif

                            <input type="file"
                                   class="form-control"
                                   name="image"
                                   accept="image/*">

                            <small class="category-help">
                                Si no seleccionas otra imagen, se conservará la actual.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="modal-footer category-modal-footer">

                    <button type="button"
                            class="btn category-cancel-btn"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn category-save-btn">
                        <i class="bi bi-check2-circle me-1"></i>
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endforeach


{{-- ======================================================
     NUEVA CATEGORÍA
====================================================== --}}
<div class="modal fade"
     id="createCategoryModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content category-modal-content">

            <form action="{{ route('categories.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="modal-header category-modal-header">

                    <div class="category-modal-heading">

                        <div class="category-modal-icon">
                            <i class="bi bi-plus-lg"></i>
                        </div>

                        <div>
                            <h5 class="category-modal-title">
                                Nueva categoría
                            </h5>

                            <p class="category-modal-subtitle">
                                Agrega una nueva categoría al menú
                            </p>
                        </div>

                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">
                    </button>

                </div>


                <div class="modal-body category-modal-body">

                    <div class="mb-3">

                        <label for="name"
                               class="form-label category-field-label">
                            Nombre de la categoría
                        </label>

                        <input type="text"
                               class="form-control"
                               id="name"
                               name="name"
                               required
                               maxlength="50"
                               placeholder="Ej: Bebidas calientes">

                    </div>


                    <div class="mb-2">

                        <label for="image"
                               class="form-label category-field-label">
                            Imagen
                            <span style="color:var(--text-muted);font-weight:500;">
                                (opcional)
                            </span>
                        </label>

                        <div class="category-file-box">

                            <input type="file"
                                   class="form-control"
                                   id="image"
                                   name="image"
                                   accept="image/*">

                            <small class="category-help">
                                Selecciona una imagen representativa para la categoría.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="modal-footer category-modal-footer">

                    <button type="button"
                            class="btn category-cancel-btn"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn category-save-btn">
                        <i class="bi bi-plus-lg me-1"></i>
                        Crear categoría
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


{{-- ======================================================
     ELIMINAR CATEGORÍA
====================================================== --}}



<script>
function confirmDeleteCategory(button) {
    const categoryId = button.dataset.categoryId;
    const categoryName = button.dataset.categoryName || 'esta categoría';

    const form = document.getElementById('deleteCategoryForm');

    if (!form || !categoryId) return;

    form.action = "{{ url('/categories') }}/" + categoryId;

    SystemNotify.confirm({
        type: 'danger',
        title: 'Eliminar categoría',
        text: '¿Deseas eliminar la categoría "' + categoryName + '" del sistema?',
        confirmText: 'Eliminar categoría',
        icon: 'bi-trash3',
        onConfirm: function () {
            form.submit();
        }
    });
}
</script>


<style>
/* DARK MODE - CONTADOR CATEGORIAS */

html[data-color-mode="dark"] .category-count {
    background: color-mix(
        in srgb,
        var(--primary) 12%,
        #132338
    ) !important;

    border: 1px solid color-mix(
        in srgb,
        var(--primary) 45%,
        #30465d
    ) !important;

    color: var(--primary) !important;

    box-shadow: none !important;
}

</style>


<form id="deleteCategoryForm"
      method="POST"
      style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection