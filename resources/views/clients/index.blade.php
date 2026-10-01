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

                                        <form action="{{ route('clients.destroy', $client->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar cliente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="client-action delete"
                                                    title="Eliminar cliente">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
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

<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('clients.store') }}" method="POST" class="modal-content client-modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Registrar Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="fw-bold form-label">Nombre Completo *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="fw-bold form-label">DNI / RUC</label>
                        <input type="text" name="document_number" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="fw-bold form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="fw-bold form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="fw-bold form-label">Dirección</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editClientForm" method="POST" class="modal-content client-modal-content">
            @csrf @method('PUT')
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Doc</label><input type="text" name="document_number" id="edit_doc" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Teléfono</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" id="edit_email" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="address" id="edit_address" class="form-control"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning fw-bold">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editClient(client) {
        document.getElementById('edit_name').value = client.name;
        document.getElementById('edit_doc').value = client.document_number;
        document.getElementById('edit_phone').value = client.phone;
        document.getElementById('edit_email').value = client.email;
        document.getElementById('edit_address').value = client.address;
        document.getElementById('editClientForm').action = "{{ url('/clients') }}/" + client.id;
        new bootstrap.Modal(document.getElementById('editClientModal')).show();
    }
</script>
@endsection
