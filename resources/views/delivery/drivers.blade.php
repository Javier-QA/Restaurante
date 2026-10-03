@extends('layouts.app')

@section('content')
<div class="container-fluid delivery-drivers-page">
    <div class="d-flex justify-content-between align-items-center mb-4 delivery-drivers-header">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--text-main) !important;"><i class="bi bi-person-vcard me-2" style="color:var(--text-main) !important;"></i>Delivery</h2>
            <p class="text-muted small mb-0 mt-1">Gestión de delivery.</p>
        </div>
        <div>
            <a href="{{ route('delivery.index') }}" class="btn btn-outline-secondary me-2 delivery-back-btn">
                <i class="bi bi-arrow-left"></i> Volver a Delivery
            </a>
            <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#driverModal">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Delivery
            </button>
        </div>
    </div>



    <div class="card delivery-drivers-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 delivery-drivers-table">
                    <thead>
                        <tr>
                            <th class="ps-4">NOMBRE</th>
                            <th>TELÉFONO</th>
                            <th>ESTADO</th>
                            <th>PEDIDOS ASIGNADOS</th>
                            <th class="text-end pe-4">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr class="delivery-driver-row">
                                <td class="ps-4 fw-bold">
                                    <div class="d-flex align-items-center">
                                        <div class="delivery-driver-avatar me-3">
                                            {{ strtoupper(substr($driver->name, 0, 1)) }}
                                        </div>
                                        {{ $driver->name }}
                                    </div>
                                </td>
                                <td>{{ $driver->phone ?? '-' }}</td>
                                <td>
                                    @if($driver->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-circle-fill small me-1" style="font-size: 0.5rem;"></i>Activo</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="bi bi-circle-fill small me-1" style="font-size: 0.5rem;"></i>Inactivo</span>
                                    @endif
                                </td>
                                <td><span class="delivery-orders-count"><i class="bi bi-bag-check me-1"></i>{{ $driver->deliveries_count }}</span></td>
                                <td class="text-end pe-4">
                                    <button class="btn delivery-driver-action delivery-driver-edit me-1" data-bs-toggle="modal" data-bs-target="#editDriverModal{{ $driver->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('delivery.drivers.destroy', $driver) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); const form=this; SystemNotify.confirm({type:'danger', title:'Eliminar delivery', text:'¿Deseas eliminar este repartidor del sistema?', confirmText:'Eliminar', icon:'bi-trash3', onConfirm:()=>form.submit()});">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn delivery-driver-action delivery-driver-delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal Edit --}}
                            <div class="modal fade delivery-driver-modal" id="editDriverModal{{ $driver->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content delivery-driver-modal-content">
                                        <div class="modal-header delivery-driver-modal-header">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Delivery</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('delivery.drivers.update', $driver) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Nombre *</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $driver->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Teléfono</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $driver->phone }}">
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $driver->id }}" {{ $driver->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="active{{ $driver->id }}">Delivery Activo</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn delivery-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn delivery-modal-save"><i class="bi bi-check2-circle me-2"></i>Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x display-4 d-block mb-3 opacity-50"></i>
                                    No hay delivery registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <x-system-pagination :paginator="$drivers" />
            </div>
        </div>
    </div>
</div>

{{-- Modal Nuevo --}}
<div class="modal fade delivery-driver-modal" id="driverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content delivery-driver-modal-content">
            <div class="modal-header delivery-driver-modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Nuevo Delivery</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delivery.drivers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nombre *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Ej. Juan Pérez">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Teléfono</label>
                        <input type="text" name="phone" class="form-control" placeholder="Ej. 987654321">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn delivery-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn delivery-modal-save"><i class="bi bi-check2-circle me-2"></i>Guardar Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* =========================================================
   GESTIÓN DE DELIVERY - REPARTIDORES
   ========================================================= */

.delivery-drivers-page {
    padding-top: 4px;
    padding-bottom: 28px;
}

.delivery-drivers-header h2 {
    letter-spacing: -0.03em;
}

.delivery-drivers-card {
    overflow: hidden;
    border: 1px solid var(--border-soft) !important;
    border-radius: 18px !important;
    background: var(--card-bg);
    box-shadow: 0 8px 28px rgba(15, 23, 42, .06) !important;
}

.delivery-drivers-table thead th {
    padding-top: 15px;
    padding-bottom: 15px;
    color: var(--text-muted);
    background: color-mix(in srgb, var(--card-bg) 94%, var(--primary) 6%);
    border-bottom: 1px solid var(--border-soft);
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: .035em;
}

.delivery-drivers-table tbody td {
    padding-top: 16px;
    padding-bottom: 16px;
    color: var(--text-main);
    background: var(--card-bg);
    border-color: var(--border-soft);
    vertical-align: middle;
}

.delivery-driver-row {
    transition: background-color .18s ease;
}

.delivery-driver-row:hover td {
    background: color-mix(in srgb, var(--primary) 4%, var(--card-bg));
}

.delivery-driver-avatar {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    color: #fff;
    background: var(--primary);
    font-size: 1rem;
    font-weight: 800;
    box-shadow: 0 5px 12px color-mix(in srgb, var(--primary) 22%, transparent);
}

.delivery-orders-count {
    min-width: 34px;
    height: 30px;
    padding: 0 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: var(--text-main);
    background: color-mix(in srgb, var(--text-main) 7%, transparent);
    border: 1px solid var(--border-soft);
    font-size: .8rem;
    font-weight: 800;
}

.delivery-driver-action {
    width: 36px;
    height: 36px;
    padding: 0 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px !important;
    background: transparent;
    transition: all .18s ease;
}

.delivery-driver-edit {
    color: var(--primary) !important;
    border: 1px solid color-mix(in srgb, var(--primary) 35%, transparent) !important;
}

.delivery-driver-edit:hover,
.delivery-driver-edit:focus {
    color: #fff !important;
    background: var(--primary) !important;
    border-color: var(--primary) !important;
}

.delivery-driver-delete {
    color: #dc3545 !important;
    border: 1px solid rgba(220, 53, 69, .30) !important;
}

.delivery-driver-delete:hover,
.delivery-driver-delete:focus {
    color: #fff !important;
    background: #dc3545 !important;
    border-color: #dc3545 !important;
}

html[data-color-mode="dark"] .delivery-drivers-card {
    box-shadow: 0 10px 30px rgba(0, 0, 0, .18) !important;
}

html[data-color-mode="dark"] .delivery-orders-count {
    background: rgba(255,255,255,.06);
}

@media (max-width: 767.98px) {
    .delivery-drivers-header {
        align-items: flex-start !important;
        flex-direction: column;
        gap: 16px;
    }

    .delivery-drivers-header > div:last-child {
        width: 100%;
        display: flex;
        gap: 8px;
    }

    .delivery-drivers-header > div:last-child .btn {
        flex: 1;
    }
}
</style>

<style>
/* =========================================================
   MODALES - REPARTIDORES
   ========================================================= */

.delivery-driver-modal .modal-dialog {
    max-width: 500px;
}

.delivery-driver-modal-content {
    overflow: hidden;
    border: 1px solid var(--border-soft) !important;
    border-radius: 18px !important;
    background: var(--card-bg) !important;
    box-shadow: 0 22px 60px rgba(15, 23, 42, .18) !important;
}

.delivery-driver-modal-header {
    padding: 18px 22px !important;
    color: #fff !important;
    background: var(--primary) !important;
    border-bottom: 0 !important;
}

.delivery-driver-modal-header .modal-title,
.delivery-driver-modal-header .modal-title i {
    color: #fff !important;
}

.delivery-driver-modal .modal-body {
    padding: 24px !important;
    background: var(--card-bg);
}

.delivery-driver-modal .form-label {
    margin-bottom: 7px;
    color: var(--text-main);
    font-size: .79rem !important;
    font-weight: 800 !important;
}

.delivery-driver-modal .form-control {
    min-height: 46px;
    padding: 10px 13px;
    color: var(--text-main);
    background: var(--card-bg);
    border: 1px solid var(--border-soft);
    border-radius: 11px;
    box-shadow: none !important;
}

.delivery-driver-modal .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 14%, transparent) !important;
}

.delivery-driver-modal .form-control::placeholder {
    color: var(--text-muted);
    opacity: .65;
}

.delivery-driver-modal .form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.delivery-driver-modal .form-check-label {
    color: var(--text-main);
    font-weight: 600;
}

.delivery-driver-modal .modal-footer {
    gap: 8px;
    padding: 16px 22px !important;
    background: var(--card-bg) !important;
    border-top: 1px solid var(--border-soft) !important;
}

.delivery-modal-cancel,
.delivery-modal-save {
    min-height: 42px;
    padding: 8px 17px !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
}

.delivery-modal-cancel {
    color: var(--text-main) !important;
    background: transparent !important;
    border: 1px solid var(--border-soft) !important;
}

.delivery-modal-cancel:hover,
.delivery-modal-cancel:focus {
    color: var(--primary) !important;
    background: color-mix(in srgb, var(--primary) 7%, transparent) !important;
    border-color: var(--primary) !important;
}

.delivery-modal-save {
    color: #fff !important;
    background: var(--primary) !important;
    border: 1px solid var(--primary) !important;
}

.delivery-modal-save:hover,
.delivery-modal-save:focus {
    color: #fff !important;
    background: var(--primary-hover) !important;
    border-color: var(--primary-hover) !important;
}

html[data-color-mode="dark"] .delivery-driver-modal-content {
    box-shadow: 0 24px 65px rgba(0, 0, 0, .38) !important;
}

html[data-color-mode="dark"] .delivery-driver-modal-header {
    background: var(--primary) !important;
}

html[data-color-mode="dark"] .delivery-driver-modal .form-control {
    color: #fff;
    background: color-mix(in srgb, var(--card-bg) 94%, white 6%);
}

html[data-color-mode="dark"] .delivery-modal-cancel {
    color: #f8fafc !important;
}
</style>

<style>
.delivery-back-btn {
    color: var(--text-main) !important;
    background: transparent !important;
    border: 1.5px solid #6c757d !important;
    border-radius: 10px !important;
    font-weight: 600;
}

.delivery-back-btn:hover,
.delivery-back-btn:focus {
    color: var(--primary) !important;
    background: color-mix(in srgb, var(--primary) 7%, transparent) !important;
    border-color: var(--primary) !important;
}

html[data-color-mode="dark"] .delivery-back-btn {
    color: #f8fafc !important;
    border-color: #64748b !important;
}

html[data-color-mode="dark"] .delivery-back-btn:hover,
html[data-color-mode="dark"] .delivery-back-btn:focus {
    color: var(--primary) !important;
    border-color: var(--primary) !important;
}
</style>

<style>
.delivery-back-btn {
    border-radius: 50rem !important;
    padding-left: 22px !important;
    padding-right: 22px !important;
}
</style>
@endsection
