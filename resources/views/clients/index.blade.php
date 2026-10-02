@extends('layouts.app')

@section('content')
<style>
.clients-page-marker {}

.client-primary-btn {
    min-height: 44px;
    padding: 0 20px;
    border: 1px solid var(--primary);
    border-radius: 12px;
    background: var(--primary);
    color: #fff;
    font-weight: 700;
    transition: .2s ease;
}

.client-primary-btn:hover {
    background: var(--primary-hover);
    border-color: var(--primary-hover);
    color: #fff;
    transform: translateY(-1px);
}

.client-list-card {
    background: var(--card-bg);
    border: 1px solid var(--border-soft) !important;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}

.client-list-card .table {
    color: var(--text-main);
}

.client-list-card thead th {
    background: var(--light-bg);
    color: var(--text-muted);
    border-bottom: 1px solid var(--border-soft);
    padding-top: 14px;
    padding-bottom: 14px;
    font-size: .76rem;
    font-weight: 700;
    letter-spacing: .03em;
}

.client-list-card tbody td {
    padding-top: 14px;
    padding-bottom: 14px;
    border-color: var(--border-soft);
}

.client-list-card tbody tr {
    transition: background .18s ease;
}

.client-list-card tbody tr:hover {
    background: color-mix(in srgb, var(--primary) 3%, var(--card-bg));
}

.client-avatar {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 10%, var(--card-bg));
    border: 1px solid color-mix(in srgb, var(--primary) 18%, var(--border-soft));
    color: var(--primary);
    font-size: 1rem;
    font-weight: 800;
}

.client-main-info {
    min-width: 0;
}

.client-name {
    display: block;
    color: var(--text-main);
    font-weight: 700;
    text-decoration: none;
    line-height: 1.25;
}

.client-name:hover {
    color: var(--primary);
}

.client-main-info span {
    display: block;
    margin-top: 3px;
    color: var(--text-muted);
    font-size: .72rem;
}

.client-document,
.client-contact {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-main);
    font-size: .84rem;
}

.client-document i,
.client-contact i {
    color: var(--text-muted);
    font-size: .9rem;
}

.client-contact + .client-contact {
    margin-top: 5px;
}

.client-empty {
    color: var(--text-muted);
    font-size: .82rem;
}

.client-visits {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 48px;
    min-height: 32px;
    padding: 0 10px;
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    background: var(--light-bg);
    color: var(--text-muted);
    font-size: .8rem;
    font-weight: 700;
}

.client-visits.active {
    background: color-mix(in srgb, var(--primary) 8%, var(--card-bg));
    border-color: color-mix(in srgb, var(--primary) 22%, var(--border-soft));
    color: var(--primary);
}

.client-actions {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.client-action {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    border: 1px solid var(--border-soft);
    background: var(--card-bg);
    color: var(--text-muted);
    text-decoration: none;
    transition: .18s ease;
}

.client-action.view {
    color: #2563eb;
}

.client-action.edit {
    color: #f59e0b;
}

.client-action.delete {
    color: #dc2626;
}

.client-action.view:hover,
.client-action.edit:hover {
    background: color-mix(in srgb, var(--primary) 8%, var(--card-bg));
    border-color: color-mix(in srgb, var(--primary) 28%, var(--border-soft));
    color: var(--primary);
}

.client-action.delete:hover {
    background: #fff1f2;
    border-color: #fecdd3;
    color: #dc2626;
}

.client-modal-content {
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
    overflow: hidden;
}

.client-modal-content .modal-body {
    background: var(--card-bg);
}

.client-modal-content .form-control {
    min-height: 44px;
    border-color: var(--border-soft);
    border-radius: 11px;
}

.client-modal-content .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--primary) 12%, transparent);
}

/* =========================================================
   MODAL EDITAR CLIENTE
   ========================================================= */

.client-edit-header {
    position: relative;
    padding: 22px 24px;
    background: var(--card-bg);
    border-bottom: 1px solid var(--border-soft);
}

.client-edit-header::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 5px;
    background: linear-gradient(
        180deg,
        #f59e0b 0%,
        #fbbf24 50%,
        #ffedd5 100%
    );
}

.client-modal-heading {
    display: flex;
    align-items: center;
    gap: 12px;
}

.client-modal-heading-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 12px;
    background: #fff7ed;
    color: #f59e0b;
    font-size: 1.05rem;
}

.client-modal-title {
    margin: 0;
    color: var(--text-main);
    font-size: 1rem;
    font-weight: 800;
}

.client-modal-subtitle {
    margin: 3px 0 0;
    color: var(--text-muted);
    font-size: .77rem;
}

.client-edit-body {
    padding: 22px 24px 8px;
}

.client-field-label {
    display: block;
    margin-bottom: 7px;
    color: var(--text-main);
    font-size: .76rem;
    font-weight: 750;
}

.client-field-label .required {
    color: #dc2626;
}

.client-input-group {
    position: relative;
}

.client-input-icon {
    position: absolute;
    top: 50%;
    left: 14px;
    z-index: 2;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: .92rem;
    pointer-events: none;
}

.client-input-group .form-control {
    min-height: 46px;
    padding-left: 42px;
    background: var(--card-bg);
}

.client-input-group .form-control:focus + .client-input-icon {
    color: var(--primary);
}

.client-edit-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 24px 22px;
    background: var(--card-bg);
    border-top: 0;
}

.client-cancel-btn {
    min-height: 42px;
    padding: 0 18px;
    border: 1px solid var(--border-soft);
    border-radius: 11px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .82rem;
    font-weight: 700;
}

.client-cancel-btn:hover {
    background: var(--light-bg);
    border-color: var(--border-soft);
    color: var(--text-main);
}

.client-update-btn {
    min-height: 42px;
    padding: 0 19px;
    border: 0;
    border-radius: 11px;
    background: var(--primary);
    color: #fff;
    font-size: .82rem;
    font-weight: 750;
    box-shadow: 0 5px 14px color-mix(in srgb, var(--primary) 22%, transparent);
    transition: .18s ease;
}

.client-update-btn:hover {
    background: var(--primary-hover);
    color: #fff;
    transform: translateY(-1px);
}


/* =========================================================
   MODAL ELIMINAR CLIENTE
   ========================================================= */

.client-delete-modal {
    max-width: 440px;
}

.client-delete-content {
    overflow: hidden;
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    background: var(--card-bg);
    box-shadow: 0 24px 70px rgba(15, 23, 42, .20);
}

.client-delete-body {
    position: relative;
    padding: 30px 28px 22px;
    text-align: center;
}

.client-delete-body::before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(
        90deg,
        #dc2626 0%,
        #fb7185 55%,
        #fecdd3 100%
    );
}

.client-delete-icon {
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
    font-size: 1.55rem;
}

.client-delete-title {
    margin-bottom: 8px;
    color: var(--text-main);
    font-size: 1.15rem;
    font-weight: 800;
}

.client-delete-text {
    max-width: 340px;
    margin: 0 auto;
    color: var(--text-muted);
    font-size: .84rem;
    line-height: 1.6;
}

.client-delete-name {
    color: var(--text-main);
    font-weight: 800;
}

.client-delete-warning {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin: 16px 0 0;
    padding: 9px 12px;
    border: 1px solid #fecdd3;
    border-radius: 10px;
    background: #fff1f2;
    color: #b91c1c;
    font-size: .75rem;
    font-weight: 650;
}

.client-delete-footer {
    display: flex;
    gap: 10px;
    padding: 0 28px 26px;
}

.client-delete-footer button {
    flex: 1;
    min-height: 43px;
    border-radius: 11px;
    font-size: .82rem;
    font-weight: 750;
}

.client-delete-cancel {
    border: 1px solid var(--border-soft);
    background: var(--card-bg);
    color: var(--text-main);
}

.client-delete-cancel:hover {
    background: var(--light-bg);
    color: var(--text-main);
}

.client-delete-confirm {
    border: 1px solid #dc2626;
    background: #dc2626;
    color: #fff;
    box-shadow: 0 5px 14px rgba(220, 38, 38, .18);
}

.client-delete-confirm:hover {
    border-color: #b91c1c;
    background: #b91c1c;
    color: #fff;
}

@media (max-width: 767.98px) {
    .client-primary-btn {
        width: 100%;
    }

    .client-list-card tbody td {
        white-space: nowrap;
    }
}

.client-pagination {
    min-height: 82px;
    padding: 14px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: var(--card-bg);
    border-top: 1px solid var(--border-soft);
}

.client-pagination-info {
    width: 100%;
    color: var(--text-muted);
    font-size: .82rem;
    text-align: center;
}

.client-pagination-info strong {
    color: var(--text-main);
    font-weight: 700;
}

.client-pagination-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.client-page-btn {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    background: var(--card-bg);
    color: var(--text-main);
    font-size: .82rem;
    font-weight: 700;
    text-decoration: none;
    transition: .18s ease;
}

.client-page-btn:hover {
    background: color-mix(in srgb, var(--primary) 8%, var(--card-bg));
    border-color: color-mix(in srgb, var(--primary) 30%, var(--border-soft));
    color: var(--primary);
}

.client-page-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
    box-shadow: 0 4px 12px color-mix(in srgb, var(--primary) 22%, transparent);
}

.client-page-btn.disabled {
    opacity: .4;
    cursor: default;
}

@media (max-width: 575.98px) {
    .client-pagination {
        flex-direction: column;
        align-items: flex-start;
    }

    .client-pagination-controls {
        width: 100%;
        justify-content: center;
    }
}
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="bi bi-people-fill me-2"></i>Cartera de Clientes</h2>
            <p class="text-muted mb-0">Administra la información, contacto e historial de tus clientes.</p>
        </div>
        <button class="btn client-primary-btn" data-bs-toggle="modal" data-bs-target="#createClientModal">
            <i class="bi bi-person-plus-fill me-2"></i>Nuevo cliente
        </button>
    </div>

    <div class="card client-list-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre Cliente</th>
                            <th>Documento / RUC</th>
                            <th>Contacto</th>
                            <th class="text-center">Visitas</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="client-avatar">
                                            {{ mb_strtoupper(mb_substr($client->name, 0, 1)) }}
                                        </div>
                                        <div class="client-main-info">
                                            <a href="{{ route('clients.show', $client->id) }}" class="client-name">
                                                {{ $client->name }}
                                            </a>
                                            <span>Cliente #{{ str_pad($client->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="client-document">
                                        <i class="bi bi-person-vcard"></i>
                                        <span>{{ $client->document_number ?? 'Sin documento' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($client->phone)
                                        <div class="client-contact">
                                            <i class="bi bi-telephone"></i>
                                            <span>{{ $client->phone }}</span>
                                        </div>
                                    @endif
                                    @if($client->email)
                                        <div class="client-contact">
                                            <i class="bi bi-envelope"></i>
                                            <span>{{ $client->email }}</span>
                                        </div>
                                    @endif
                                    @if(!$client->phone && !$client->email)
                                        <span class="client-empty">Sin datos de contacto</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="client-visits {{ $client->orders_count > 0 ? 'active' : '' }}">
                                        <i class="bi bi-bag-check"></i>
                                        {{ $client->orders_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="client-actions">
                                        <a href="{{ route('clients.show', $client->id) }}"
                                           class="client-action view"
                                           title="Ver perfil">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <button type="button"
                                                class="client-action edit"
                                                onclick="editClient({{ $client }})"
                                                title="Editar cliente">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button type="button"
        class="client-action delete"
        title="Eliminar cliente"
        onclick='confirmDeleteClient(@json(["id" => $client->id, "name" => $client->name]))'>
    <i class="bi bi-trash3"></i>
</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-system-pagination :paginator="$clients" />
        </div>
    </div>
</div>

<div class="modal fade"
     id="createClientModal"
     tabindex="-1"
     aria-labelledby="createClientModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('clients.store') }}"
              method="POST"
              class="modal-content client-modal-content">

            @csrf

            {{-- Encabezado --}}
            <div class="modal-header border-0 px-4 pt-4 pb-2">

                <div class="d-flex align-items-center gap-3">

                    <div class="d-flex align-items-center justify-content-center"
                         style="
                            width:46px;
                            height:46px;
                            border-radius:13px;
                            background:color-mix(in srgb, var(--primary) 10%, var(--card-bg));
                            color:var(--primary);
                            font-size:1.05rem;
                         ">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>

                    <div>
                        <h5 id="createClientModalLabel"
                            class="fw-bold mb-1"
                            style="color:var(--text-main);font-size:1.05rem;">
                            Nuevo cliente
                        </h5>

                        <p class="mb-0"
                           style="color:var(--text-muted);font-size:.78rem;">
                            Registra la información del nuevo cliente
                        </p>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>

            {{-- Cuerpo --}}
            <div class="modal-body px-4 pt-3 pb-2">

                <div class="row g-3">

                    {{-- Nombre --}}
                    <div class="col-12">
                        <label for="create_name"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Nombre completo
                            <span class="text-danger">*</span>
                        </label>

                        <div class="position-relative">
                            <i class="bi bi-person position-absolute top-50 translate-middle-y"
                               style="
                                    left:14px;
                                    z-index:3;
                                    color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="name"
                                   id="create_name"
                                   class="form-control ps-5"
                                   placeholder="Nombre del cliente"
                                   autocomplete="name"
                                   required>
                        </div>
                    </div>

                    {{-- DNI / RUC --}}
                    <div class="col-md-6">
                        <label for="create_document"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            DNI / RUC
                        </label>

                        <div class="position-relative">
                            <i class="bi bi-card-text position-absolute top-50 translate-middle-y"
                               style="
                                    left:14px;
                                    z-index:3;
                                    color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="document_number"
                                   id="create_document"
                                   class="form-control ps-5"
                                   placeholder="Documento">
                        </div>
                    </div>

                    {{-- Teléfono --}}
                    <div class="col-md-6">
                        <label for="create_phone"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Teléfono
                        </label>

                        <div class="position-relative">
                            <i class="bi bi-telephone position-absolute top-50 translate-middle-y"
                               style="
                                    left:14px;
                                    z-index:3;
                                    color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="phone"
                                   id="create_phone"
                                   class="form-control ps-5"
                                   placeholder="Número de teléfono"
                                   autocomplete="tel">
                        </div>
                    </div>

                    {{-- Correo --}}
                    <div class="col-12">
                        <label for="create_email"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Correo electrónico
                        </label>

                        <div class="position-relative">
                            <i class="bi bi-envelope position-absolute top-50 translate-middle-y"
                               style="
                                    left:14px;
                                    z-index:3;
                                    color:var(--text-muted);
                               ">
                            </i>

                            <input type="email"
                                   name="email"
                                   id="create_email"
                                   class="form-control ps-5"
                                   placeholder="correo@ejemplo.com"
                                   autocomplete="email">
                        </div>
                    </div>

                    {{-- Dirección --}}
                    <div class="col-12">
                        <label for="create_address"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Dirección
                        </label>

                        <div class="position-relative">
                            <i class="bi bi-geo-alt position-absolute top-50 translate-middle-y"
                               style="
                                    left:14px;
                                    z-index:3;
                                    color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="address"
                                   id="create_address"
                                   class="form-control ps-5"
                                   placeholder="Dirección del cliente"
                                   autocomplete="street-address">
                        </div>
                    </div>

                </div>

            </div>

            {{-- Pie --}}
            <div class="modal-footer border-0 px-4 pt-3 pb-4">

                <button type="button"
                        class="btn"
                        data-bs-dismiss="modal"
                        style="
                            min-height:42px;
                            padding:0 18px;
                            border:1px solid var(--border-soft);
                            border-radius:10px;
                            background:var(--card-bg);
                            color:var(--text-main);
                            font-size:.78rem;
                            font-weight:700;
                        ">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn text-white d-inline-flex align-items-center gap-2"
                        style="
                            min-height:42px;
                            padding:0 19px;
                            border-radius:10px;
                            background:var(--primary);
                            border-color:var(--primary);
                            font-size:.78rem;
                            font-weight:700;
                        ">
                    <i class="bi bi-person-check"></i>
                    Guardar cliente
                </button>

            </div>

        </form>
    </div>
</div>
<div class="modal fade"
     id="editClientModal"
     tabindex="-1"
     aria-labelledby="editClientModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <form id="editClientForm"
              method="POST"
              class="modal-content client-modal-content">

            @csrf
            @method('PUT')

            {{-- Encabezado --}}
            <div class="modal-header border-0 px-4 pt-4 pb-2">

                <div class="d-flex align-items-center gap-3">

                    <div class="d-flex align-items-center justify-content-center"
                         style="
                            width:46px;
                            height:46px;
                            border-radius:13px;
                            background:color-mix(in srgb, var(--primary) 10%, var(--card-bg));
                            color:var(--primary);
                            font-size:1.05rem;
                         ">
                        <i class="bi bi-pencil-square"></i>
                    </div>

                    <div>
                        <h5 id="editClientModalLabel"
                            class="fw-bold mb-1"
                            style="color:var(--text-main);font-size:1.05rem;">
                            Editar cliente
                        </h5>

                        <p class="mb-0"
                           style="color:var(--text-muted);font-size:.78rem;">
                            Actualiza la información registrada
                        </p>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>


            {{-- Cuerpo --}}
            <div class="modal-body px-4 pt-3 pb-2">

                <div class="row g-3">

                    {{-- Nombre --}}
                    <div class="col-12">

                        <label for="edit_name"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Nombre completo
                            <span class="text-danger">*</span>
                        </label>

                        <div class="position-relative">

                            <i class="bi bi-person position-absolute top-50 translate-middle-y"
                               style="
                                  left:14px;
                                  z-index:3;
                                  color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="name"
                                   id="edit_name"
                                   class="form-control ps-5"
                                   placeholder="Nombre del cliente"
                                   required>

                        </div>
                    </div>


                    {{-- Documento --}}
                    <div class="col-md-6">

                        <label for="edit_doc"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            DNI / RUC
                        </label>

                        <div class="position-relative">

                            <i class="bi bi-person-vcard position-absolute top-50 translate-middle-y"
                               style="
                                  left:14px;
                                  z-index:3;
                                  color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="document_number"
                                   id="edit_doc"
                                   class="form-control ps-5"
                                   placeholder="Documento">

                        </div>
                    </div>


                    {{-- Teléfono --}}
                    <div class="col-md-6">

                        <label for="edit_phone"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Teléfono
                        </label>

                        <div class="position-relative">

                            <i class="bi bi-telephone position-absolute top-50 translate-middle-y"
                               style="
                                  left:14px;
                                  z-index:3;
                                  color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="phone"
                                   id="edit_phone"
                                   class="form-control ps-5"
                                   placeholder="Número de teléfono">

                        </div>
                    </div>


                    {{-- Email --}}
                    <div class="col-12">

                        <label for="edit_email"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Correo electrónico
                        </label>

                        <div class="position-relative">

                            <i class="bi bi-envelope position-absolute top-50 translate-middle-y"
                               style="
                                  left:14px;
                                  z-index:3;
                                  color:var(--text-muted);
                               ">
                            </i>

                            <input type="email"
                                   name="email"
                                   id="edit_email"
                                   class="form-control ps-5"
                                   placeholder="correo@ejemplo.com">

                        </div>
                    </div>


                    {{-- Dirección --}}
                    <div class="col-12">

                        <label for="edit_address"
                               class="form-label fw-bold mb-2"
                               style="font-size:.78rem;color:var(--text-main);">
                            Dirección
                        </label>

                        <div class="position-relative">

                            <i class="bi bi-geo-alt position-absolute top-50 translate-middle-y"
                               style="
                                  left:14px;
                                  z-index:3;
                                  color:var(--text-muted);
                               ">
                            </i>

                            <input type="text"
                                   name="address"
                                   id="edit_address"
                                   class="form-control ps-5"
                                   placeholder="Dirección del cliente">

                        </div>
                    </div>

                </div>

            </div>


            {{-- Botones --}}
            <div class="modal-footer border-0 px-4 pt-3 pb-4 gap-2">

                <button type="button"
                        class="btn px-4"
                        data-bs-dismiss="modal"
                        style="
                           min-height:43px;
                           border:1px solid var(--border-soft);
                           border-radius:11px;
                           background:var(--card-bg);
                           color:var(--text-main);
                           font-size:.82rem;
                           font-weight:700;
                        ">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn text-white px-4"
                        style="
                           min-height:43px;
                           border:0;
                           border-radius:11px;
                           background:var(--primary);
                           font-size:.82rem;
                           font-weight:700;
                           box-shadow:0 5px 14px color-mix(in srgb, var(--primary) 20%, transparent);
                        ">

                    <i class="bi bi-check2-circle me-1"></i>
                    Guardar cambios

                </button>

            </div>

        </form>
    </div>
</div>

{{-- MODAL ELIMINAR CLIENTE --}}
<div class="modal fade" id="deleteClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
        <div class="modal-content client-modal-content border-0">

            <div class="modal-body text-center p-4">

                <div class="d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width:68px;height:68px;border-radius:20px;background:#fff1f2;border:1px solid #fecdd3;color:#dc2626;font-size:1.55rem;">
                    <i class="bi bi-trash3"></i>
                </div>

                <h5 class="fw-bold mb-2" style="color:var(--text-main);">
                    Eliminar cliente
                </h5>

                <p class="mb-3"
                   style="color:var(--text-muted);font-size:.88rem;line-height:1.6;">
                    ¿Estás seguro de eliminar a
                    <strong id="deleteClientName" style="color:var(--text-main);"></strong>?
                </p>

                <div class="d-flex align-items-center justify-content-center gap-2 px-3 py-2 mb-4"
                     style="background:#fff1f2;border:1px solid #fecdd3;border-radius:11px;color:#b91c1c;font-size:.77rem;font-weight:650;">
                    <i class="bi bi-exclamation-triangle"></i>
                    Esta acción no se puede deshacer.
                </div>

                <div class="d-flex gap-2">

                    <button type="button"
                            class="btn flex-fill"
                            data-bs-dismiss="modal"
                            style="min-height:43px;border:1px solid var(--border-soft);border-radius:11px;background:var(--card-bg);color:var(--text-main);font-weight:700;">
                        Cancelar
                    </button>

                    <form id="deleteClientForm"
                          method="POST"
                          class="d-flex flex-fill">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn text-white flex-fill"
                                style="min-height:43px;border-radius:11px;background:#dc2626;border-color:#dc2626;font-weight:700;">
                            <i class="bi bi-trash3 me-1"></i>
                            Sí, eliminar
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<script>
    function editClient(client) {
        document.getElementById('edit_name').value = client.name ?? '';
        document.getElementById('edit_doc').value = client.document_number ?? '';
        document.getElementById('edit_phone').value = client.phone ?? '';
        document.getElementById('edit_email').value = client.email ?? '';
        document.getElementById('edit_address').value = client.address ?? '';

        document.getElementById('editClientForm').action =
            "{{ url('/clients') }}/" + client.id;

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById('editClientModal'))
            .show();
    }

    function confirmDeleteClient(client) {
        document.getElementById('deleteClientName').textContent =
            client.name ?? 'este cliente';

        document.getElementById('deleteClientForm').action =
            "{{ url('/clients') }}/" + client.id;

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById('deleteClientModal'))
            .show();
    }
</script>

<style>
/* DARK MODE - CLIENTES AVATAR Y VISITAS */

/* ==========================================================
   AVATAR DEL CLIENTE
   ========================================================== */

html[data-color-mode="dark"] .client-avatar {
    background: color-mix(
        in srgb,
        var(--primary) 14%,
        #132338
    ) !important;

    border: 1px solid color-mix(
        in srgb,
        var(--primary) 30%,
        #30465d
    ) !important;

    color: var(--primary) !important;
    box-shadow: none !important;
}


/* ==========================================================
   VISITAS = 0
   Estado neutro
   ========================================================== */

html[data-color-mode="dark"] .client-visits:not(.active) {
    background: #17283d !important;
    border-color: #30465d !important;
    color: #8fa6bd !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .client-visits:not(.active) i {
    color: #8fa6bd !important;
    -webkit-text-fill-color: #8fa6bd !important;
}


/* ==========================================================
   VISITAS > 0
   Respeta el color principal del tema
   ========================================================== */

html[data-color-mode="dark"] .client-visits.active {
    background: color-mix(
        in srgb,
        var(--primary) 14%,
        #132338
    ) !important;

    border-color: color-mix(
        in srgb,
        var(--primary) 34%,
        #30465d
    ) !important;

    color: var(--primary) !important;
    box-shadow: none !important;
}

html[data-color-mode="dark"] .client-visits.active i {
    color: var(--primary) !important;
    -webkit-text-fill-color: var(--primary) !important;
}

</style>

@endsection
