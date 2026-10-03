@extends('layouts.app')

@section('content')

<style>
    .users-page {
        max-width: 1600px;
    }

    /* =========================================================
       ENCABEZADO
       ========================================================= */
    .users-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .users-heading {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .users-heading > i {
        margin-top: 4px;
        color:var(--text-main);
        font-size: 1.25rem;
    }

    .users-title {
        margin: 0;
        color: var(--text-main);
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -.35px;
    }

    .users-subtitle {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: .8rem;
    }

    .users-create-btn {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 15px;
        border: 1px solid var(--primary);
        border-radius: 9px;
        background: var(--primary);
        color: #fff;
        font-size: .76rem;
        font-weight: 750;
        box-shadow: 0 4px 10px
            color-mix(in srgb, var(--primary) 18%, transparent);
    }

    .users-create-btn:hover {
        border-color: var(--primary-hover);
        background: var(--primary-hover);
        color: #fff;
        transform: translateY(-1px);
    }


    /* =========================================================
       TABLA
       ========================================================= */
    .users-card {
        border: 1px solid var(--border-soft);
        border-radius: 15px;
        background: var(--card-bg);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .users-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-soft);
    }

    .users-card-heading {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .users-card-heading > i {
        color:var(--text-main);
        font-size: .95rem;
    }

    .users-card-title {
        margin: 0;
        color: var(--text-main);
        font-size: .84rem;
        font-weight: 800;
    }

    .users-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 27px;
        padding: 0 9px;
        border: 1px solid var(--border-soft);
        border-radius: 20px;
        background: var(--light-bg);
        color: var(--text-muted);
        font-size: .66rem;
        font-weight: 700;
    }

    .users-table {
        margin: 0;
    }

    .users-table thead th {
        padding: 11px 18px;
        border-bottom: 1px solid var(--border-soft);
        background: var(--light-bg);
        color: var(--text-muted);
        font-size: .64rem;
        font-weight: 800;
        letter-spacing: .35px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .users-table tbody td {
        padding: 13px 18px;
        border-bottom: 1px solid var(--border-soft);
        color: var(--text-main);
        font-size: .76rem;
        vertical-align: middle;
    }

    .users-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .users-table tbody tr:hover {
        background:
            color-mix(in srgb, var(--primary) 2.5%, var(--card-bg));
    }

    /* Usuario */
    .user-profile {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: .8rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .user-avatar.admin {
        background: #fff1f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .user-avatar.cashier {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    .user-avatar.waiter {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .user-name {
        color: var(--text-main);
        font-size: .77rem;
        font-weight: 750;
    }

    .user-you {
        display: inline-flex;
        align-items: center;
        margin-top: 3px;
        padding: 2px 6px;
        border: 1px solid var(--border-soft);
        border-radius: 5px;
        background: var(--light-bg);
        color: var(--text-muted);
        font-size: .55rem;
        font-weight: 800;
        letter-spacing: .25px;
    }

    /* Roles */
    .user-role {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 27px;
        padding: 0 9px;
        border-radius: 7px;
        font-size: .64rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .user-role.admin {
        border: 1px solid #fecaca;
        background: #fff1f2;
        color: #dc2626;
    }

    .user-role.cashier {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #2563eb;
    }

    .user-role.waiter {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .user-email {
        color: var(--text-muted);
    }

    .user-date {
        color: var(--text-muted);
        white-space: nowrap;
    }


    /* =========================================================
       ACCIONES
       ========================================================= */
    .user-actions {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
    }

    .user-action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 8px;
        font-size: .76rem;
        transition: .15s ease;
    }

    .user-action-edit {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #2563eb;
    }

    .user-action-edit:hover {
        border-color: #93c5fd;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .user-action-delete {
        border: 1px solid #fecaca;
        background: #fff1f2;
        color: #dc2626;
    }

    .user-action-delete:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #b91c1c;
    }

    .user-action-disabled {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-soft);
        border-radius: 8px;
        background: var(--light-bg);
        color: #cbd5e1;
        cursor: not-allowed;
    }


    /* =========================================================
       VACÍO
       ========================================================= */
    .users-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .users-empty-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        border-radius: 50%;
        background: var(--light-bg);
        color: var(--text-muted);
        font-size: 1.2rem;
    }

    .users-empty h6 {
        margin-bottom: 4px;
        color: var(--text-main);
        font-size: .82rem;
        font-weight: 800;
    }

    .users-empty p {
        margin: 0;
        color: var(--text-muted);
        font-size: .7rem;
    }


    /* =========================================================
       MODALES
       ========================================================= */
    .user-modal-content {
        border: 0;
        border-radius: 16px;
        background: var(--card-bg);
        box-shadow: 0 20px 50px rgba(15,23,42,.16);
        overflow: hidden;
    }

    .user-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 21px 22px 12px;
        border: 0;
    }

    .user-modal-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-modal-icon {
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background:
            color-mix(in srgb, var(--primary) 8%, var(--card-bg));
        color: var(--primary);
        font-size: 1rem;
    }

    .user-modal-title {
        margin: 0;
        color: var(--text-main);
        font-size: .96rem;
        font-weight: 800;
    }

    .user-modal-subtitle {
        margin: 3px 0 0;
        color: var(--text-muted);
        font-size: .67rem;
    }

    .user-modal-body {
        padding: 12px 22px 8px;
    }

    .user-modal-label {
        margin-bottom: 6px;
        color: var(--text-main);
        font-size: .71rem;
        font-weight: 750;
    }

    .user-modal-field {
        position: relative;
    }

    .user-modal-field > i {
        position: absolute;
        top: 50%;
        left: 13px;
        z-index: 4;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: .76rem;
        pointer-events: none;
    }

    .user-modal-control {
        min-height: 42px;
        padding-left: 38px;
        border: 1px solid var(--border-soft);
        border-radius: 9px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: .76rem;
    }

    .user-modal-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px
            color-mix(in srgb, var(--primary) 10%, transparent);
    }

    .user-modal-help {
        display: block;
        margin-top: 5px;
        color: var(--text-muted);
        font-size: .62rem;
    }

    .user-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
        padding: 14px 22px 21px;
        border: 0;
    }

    .user-modal-footer > * {
        margin: 0 !important;
    }

    .user-cancel-btn {
        min-height: 39px;
        padding: 0 14px;
        border: 1px solid var(--border-soft);
        border-radius: 9px;
        background: var(--card-bg);
        color: var(--text-main);
        font-size: .72rem;
        font-weight: 700;
    }

    .user-save-btn {
        min-height: 39px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 15px;
        border: 1px solid var(--primary);
        border-radius: 9px;
        background: var(--primary);
        color: #fff;
        font-size: .72rem;
        font-weight: 750;
    }

    .user-save-btn:hover {
        border-color: var(--primary-hover);
        background: var(--primary-hover);
        color: #fff;
    }


    /* Eliminar */
    .user-delete-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2px auto 12px;
        border: 1px solid #fecaca;
        border-radius: 50%;
        background: #fff1f2;
        color: #dc2626;
        font-size: 1.15rem;
    }

    .user-delete-title {
        margin-bottom: 5px;
        color: var(--text-main);
        font-size: .93rem;
        font-weight: 800;
        text-align: center;
    }

    .user-delete-text {
        margin: 0;
        color: var(--text-muted);
        font-size: .7rem;
        line-height: 1.55;
        text-align: center;
    }

    .user-delete-name {
        color: var(--text-main);
        font-weight: 750;
    }

    .user-delete-btn {
        min-height: 39px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 15px;
        border: 1px solid #dc2626;
        border-radius: 9px;
        background: #dc2626;
        color: #fff;
        font-size: .72rem;
        font-weight: 750;
    }

    .user-delete-btn:hover {
        border-color: #b91c1c;
        background: #b91c1c;
        color: #fff;
    }


    @media (max-width: 767.98px) {
        .users-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .users-create-btn {
            width: 100%;
        }

        .user-modal-footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .user-modal-footer .btn {
            width: 100%;
        }
    }
</style>


<div class="container-fluid px-0 users-page">

    {{-- Encabezado --}}
    <div class="users-header">

        <div class="users-heading">
            <i class="bi bi-people-fill"></i>

            <div>
                <h2 class="users-title">Personal</h2>
                <p class="users-subtitle">
                    Gestiona los accesos y roles de tu equipo
                </p>
            </div>
        </div>


        <button type="button"
                class="btn users-create-btn"
                data-bs-toggle="modal"
                data-bs-target="#createUserModal">

            <i class="bi bi-person-plus-fill"></i>
            Nuevo usuario
        </button>

    </div>


    {{-- Lista --}}
    <div class="users-card">

        <div class="users-card-head">

            <div class="users-card-heading">
                <i class="bi bi-person-lines-fill"></i>
                <h6 class="users-card-title">Lista de personal</h6>
            </div>

            <span class="users-count">
                {{ $users->total() }}
                {{ $users->total() == 1 ? 'usuario' : 'usuarios' }}
            </span>

        </div>


        <div class="table-responsive">

            <table class="table users-table align-middle">

                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol / Cargo</th>
                        <th>Email de acceso</th>
                        <th>Fecha de registro</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($users as $user)

                        <tr>

                            {{-- Usuario --}}
                            <td>
                                <div class="user-profile">

                                    <div class="user-avatar {{ $user->role }}">
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <div class="user-name">
                                            {{ $user->name }}
                                        </div>

                                        @if($user->id === Auth::id())
                                            <span class="user-you">
                                                TU CUENTA
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </td>


                            {{-- Rol --}}
                            <td>

                                @if($user->role === 'admin')

                                    <span class="user-role admin">
                                        <i class="bi bi-shield-check"></i>
                                        Administrador
                                    </span>

                                @elseif($user->role === 'cashier')

                                    <span class="user-role cashier">
                                        <i class="bi bi-cash-stack"></i>
                                        Cajero
                                    </span>

                                @elseif($user->role === 'waiter')

                                    <span class="user-role waiter">
                                        <i class="bi bi-person-badge"></i>
                                        Mozo
                                    </span>

                                @elseif($user->role === 'kitchen')

                                    <span class="user-role kitchen">
                                        <i class="bi bi-fire"></i>
                                        Cocina
                                    </span>

                                @elseif($user->role === 'bar')

                                    <span class="user-role bar">
                                        <i class="bi bi-cup-straw"></i>
                                        Barra
                                    </span>

                                @endif

                            </td>


                            {{-- Email --}}
                            <td>
                                <span class="user-email">
                                    {{ $user->email }}
                                </span>
                            </td>


                            {{-- Fecha --}}
                            <td>
                                <span class="user-date">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $user->created_at->format('d/m/Y') }}
                                </span>
                            </td>


                            {{-- Acciones --}}
                            <td>

                                <div class="user-actions">

                                    <button type="button"
                                            class="btn user-action-btn user-action-edit"
                                            onclick='editUser(@json($user))'
                                            title="Editar usuario">

                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    @if($user->id !== Auth::id())

                                        <button type="button"
                                                class="btn user-action-btn user-action-delete"
                                                onclick='openDeleteUserModal(
                                                    {{ $user->id }},
                                                    @json($user->name)
                                                )'
                                                title="Eliminar usuario">

                                            <i class="bi bi-trash3"></i>
                                        </button>

                                    @else

                                        <span class="user-action-disabled"
                                              title="No puedes eliminar tu propia cuenta">

                                            <i class="bi bi-lock"></i>
                                        </span>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="p-0">

                                <div class="users-empty">

                                    <div class="users-empty-icon">
                                        <i class="bi bi-people"></i>
                                    </div>

                                    <h6>No hay personal registrado</h6>

                                    <p>
                                        Registra un usuario para comenzar a gestionar
                                        los accesos del equipo.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <x-system-pagination :paginator="$users" />

    </div>

</div>



{{-- =========================================================
     MODAL CREAR USUARIO
     ========================================================= --}}
<div class="modal fade"
     id="createUserModal"
     tabindex="-1"
     aria-labelledby="createUserModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered"
         style="max-width:520px;">

        <form action="{{ route('users.store') }}"
              method="POST"
              class="modal-content user-modal-content">

            @csrf


            <div class="user-modal-header">

                <div class="user-modal-heading">

                    <div class="user-modal-icon">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>

                    <div>
                        <h5 id="createUserModalLabel"
                            class="user-modal-title">
                            Nuevo usuario
                        </h5>

                        <p class="user-modal-subtitle">
                            Registra los datos de acceso del personal
                        </p>
                    </div>

                </div>


                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>


            <div class="user-modal-body">

                <div class="mb-3">

                    <label class="form-label user-modal-label">
                        Nombre completo
                        <span class="text-danger">*</span>
                    </label>

                    <div class="user-modal-field">
                        <i class="bi bi-person"></i>

                        <input type="text"
                               name="name"
                               class="form-control user-modal-control"
                               placeholder="Ej. Juan Pérez"
                               required>
                    </div>

                </div>


                <div class="mb-3">

                    <label class="form-label user-modal-label">
                        Correo electrónico
                        <span class="text-danger">*</span>
                    </label>

                    <div class="user-modal-field">
                        <i class="bi bi-envelope"></i>

                        <input type="email"
                               name="email"
                               class="form-control user-modal-control"
                               placeholder="usuario@restaurante.com"
                               required>
                    </div>

                    <span class="user-modal-help">
                        Este correo será utilizado para iniciar sesión.
                    </span>

                </div>


                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label user-modal-label">
                            Contraseña
                            <span class="text-danger">*</span>
                        </label>

                        <div class="user-modal-field">
                            <i class="bi bi-lock"></i>

                            <input type="password"
       name="password"
       id="create_user_password"
       class="form-control user-modal-control"
       placeholder="Mínimo 6 caracteres"
       required>

<button type="button"
        class="user-password-toggle"
        id="createPasswordToggle"
        aria-label="Mostrar contraseña">
    <i class="bi bi-eye" id="createPasswordIcon"></i>
</button>
                        </div>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label user-modal-label">
                            Rol / Permisos
                            <span class="text-danger">*</span>
                        </label>

                        <div class="user-modal-field">
                            <i class="bi bi-person-gear"></i>

                            <select name="role" class="form-select user-modal-control user-role-select" required>

                                <option value="waiter">
                                    Mozo
                                </option>

                                <option value="cashier">
                                    Cajero
                                </option>

                                <option value="kitchen">
                                    Cocina
                                </option>

                                <option value="bar">
                                    Barra
                                </option>

                                <option value="admin">
                                    Administrador
                                </option>

                            </select>
                        </div>

                    </div>

                </div>

            </div>


            <div class="user-modal-footer">

                <button type="button"
                        class="btn user-cancel-btn"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn user-save-btn">

                    <i class="bi bi-check-lg"></i>
                    Guardar usuario
                </button>

            </div>

        </form>

    </div>

</div>



{{-- =========================================================
     MODAL EDITAR USUARIO
     ========================================================= --}}
<div class="modal fade"
     id="editUserModal"
     tabindex="-1"
     aria-labelledby="editUserModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered"
         style="max-width:520px;">

        <form id="editUserForm"
              method="POST"
              class="modal-content user-modal-content">

            @csrf
            @method('PUT')


            <div class="user-modal-header">

                <div class="user-modal-heading">

                    <div class="user-modal-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>

                    <div>
                        <h5 id="editUserModalLabel"
                            class="user-modal-title">
                            Editar usuario
                        </h5>

                        <p class="user-modal-subtitle">
                            Actualiza los datos y permisos del usuario
                        </p>
                    </div>

                </div>


                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>


            <div class="user-modal-body">

                <div class="mb-3">

                    <label class="form-label user-modal-label">
                        Nombre completo
                        <span class="text-danger">*</span>
                    </label>

                    <div class="user-modal-field">
                        <i class="bi bi-person"></i>

                        <input type="text"
                               name="name"
                               id="edit_name"
                               class="form-control user-modal-control"
                               required>
                    </div>

                </div>


                <div class="mb-3">

                    <label class="form-label user-modal-label">
                        Correo electrónico
                        <span class="text-danger">*</span>
                    </label>

                    <div class="user-modal-field">
                        <i class="bi bi-envelope"></i>

                        <input type="email"
                               name="email"
                               id="edit_email"
                               class="form-control user-modal-control"
                               required>
                    </div>

                </div>


                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label user-modal-label">
                            Nueva contraseña
                        </label>

                        <div class="user-modal-field">
                            <i class="bi bi-lock"></i>

                            <input type="password"
       name="password"
       id="edit_user_password"
       class="form-control user-modal-control"
       placeholder="Opcional">

<button type="button"
        class="user-password-toggle edit-password-toggle"
        id="editPasswordToggle"
        aria-label="Mostrar contraseña"
        title="Mostrar contraseña">
    <i class="bi bi-eye" id="editPasswordIcon"></i>
</button>
                        </div>

                        <span class="user-modal-help">
                            Déjala vacía para conservar la contraseña actual.
                        </span>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label user-modal-label">
                            Rol / Permisos
                            <span class="text-danger">*</span>
                        </label>

                        <div class="user-modal-field">
                            <i class="bi bi-person-gear"></i>

                            <select name="role" id="edit_role" class="form-select user-modal-control user-role-select" required>

                                <option value="waiter">Mozo</option>
                                <option value="cashier">Cajero</option>
                                <option value="kitchen">Cocina</option>
                                <option value="bar">Barra</option>
                                <option value="admin">Administrador</option>

                            </select>
                        </div>

                    </div>

                </div>

            </div>


            <div class="user-modal-footer">

                <button type="button"
                        class="btn user-cancel-btn"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn user-save-btn">

                    <i class="bi bi-check-lg"></i>
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>



{{-- =========================================================
     MODAL ELIMINAR USUARIO
     ========================================================= --}}




<script>
    function editUser(user) {
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;

        document.getElementById('editUserForm').action =
            "{{ url('/users') }}/" + user.id;

        new bootstrap.Modal(
            document.getElementById('editUserModal')
        ).show();
    }


    function openDeleteUserModal(id, name) {
    const form = document.getElementById('deleteUserForm');

    if (!form || !id) return;

    form.action = "{{ url('/users') }}/" + id;

    SystemNotify.confirm({
        type: 'danger',
        title: 'Eliminar usuario',
        text: '¿Deseas eliminar al usuario "' + name + '" del sistema?',
        confirmText: 'Eliminar usuario',
        icon: 'bi-person-x',
        onConfirm: function () {
            form.submit();
        }
    });
}
</script>


<style>
/* =========================================================
   AJUSTE VISUAL - COLUMNA USUARIO
   ========================================================= */

.users-table tbody td {
    padding-top: 11px !important;
    padding-bottom: 11px !important;
}

/* Usuario */
.user-profile {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}

/* Avatar más pequeño y discreto */
.user-avatar {
    width: 34px !important;
    height: 34px !important;
    flex: 0 0 34px !important;
    border-radius: 9px !important;
    font-size: .72rem !important;
    font-weight: 800 !important;
}

/* Administrador */
.user-avatar.admin {
    background: #fff1f2 !important;
    border: 1px solid #fecaca !important;
    color: #dc2626 !important;
}

/* Cajero */
.user-avatar.cashier {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #2563eb !important;
}

/* Mozo */
.user-avatar.waiter {
    background: #f0fdf4 !important;
    border: 1px solid #bbf7d0 !important;
    color: #15803d !important;
}

/* Nombre */
.user-name {
    line-height: 1.2 !important;
    color: var(--text-main) !important;
    font-size: .76rem !important;
    font-weight: 750 !important;
}

/* Etiqueta de cuenta actual */
.user-you {
    display: inline-flex !important;
    align-items: center !important;
    width: fit-content !important;
    min-height: 18px !important;
    margin-top: 4px !important;
    padding: 0 6px !important;

    border: 1px solid
        color-mix(in srgb, var(--primary) 20%, var(--border-soft)) !important;

    border-radius: 5px !important;

    background:
        color-mix(in srgb, var(--primary) 5%, var(--card-bg)) !important;

    color: var(--primary) !important;

    font-size: .52rem !important;
    font-weight: 800 !important;
    letter-spacing: .15px !important;
}
</style>

<style>
/* DARK MODE - PERSONAL DEFINITIVO */

/* Contador */
html[data-color-mode="dark"] .users-count {
    background: #17283d !important;
    color: #cbd5e1 !important;
    border: 1px solid #29445f !important;
}

/* Avatares */
html[data-color-mode="dark"] .user-avatar {
    background: #17283d !important;
    border: 1px solid #29445f !important;
}

html[data-color-mode="dark"] .user-avatar.admin {
    background: rgba(239,68,68,.14) !important;
    color: #f87171 !important;
    border-color: rgba(248,113,113,.30) !important;
}

html[data-color-mode="dark"] .user-avatar.waiter {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
    border-color: rgba(74,222,128,.30) !important;
}

html[data-color-mode="dark"] .user-avatar.cashier {
    background: rgba(59,130,246,.14) !important;
    color: #60a5fa !important;
    border-color: rgba(96,165,250,.30) !important;
}

/* Roles */
html[data-color-mode="dark"] .user-role {
    border: 1px solid transparent !important;
}

html[data-color-mode="dark"] .user-role.admin {
    background: rgba(239,68,68,.14) !important;
    color: #fca5a5 !important;
    border-color: rgba(248,113,113,.30) !important;
}

html[data-color-mode="dark"] .user-role.waiter {
    background: rgba(34,197,94,.14) !important;
    color: #86efac !important;
    border-color: rgba(74,222,128,.30) !important;
}

html[data-color-mode="dark"] .user-role.cashier {
    background: rgba(59,130,246,.14) !important;
    color: #93c5fd !important;
    border-color: rgba(96,165,250,.30) !important;
}

/* TU CUENTA */
html[data-color-mode="dark"] .user-you {
    background: rgba(245,158,11,.14) !important;
    color: #fbbf24 !important;
    border-color: rgba(251,191,36,.30) !important;
}

/* Editar */
html[data-color-mode="dark"] .user-action-edit {
    background: rgba(59,130,246,.14) !important;
    color: #60a5fa !important;
    border-color: rgba(96,165,250,.30) !important;
}

html[data-color-mode="dark"] .user-action-edit:hover {
    background: rgba(59,130,246,.24) !important;
    color: #93c5fd !important;
}

/* Eliminar */
html[data-color-mode="dark"] .user-action-delete {
    background: rgba(239,68,68,.14) !important;
    color: #f87171 !important;
    border-color: rgba(248,113,113,.30) !important;
}

html[data-color-mode="dark"] .user-action-delete:hover {
    background: rgba(239,68,68,.24) !important;
    color: #fca5a5 !important;
}

/* Acción deshabilitada */
html[data-color-mode="dark"] .user-action-disabled {
    background: #17283d !important;
    color: #64748b !important;
    border-color: #29445f !important;
}

/* Iconos internos */
html[data-color-mode="dark"] .user-action-btn i,
html[data-color-mode="dark"] .user-action-disabled i {
    color: inherit !important;
}

</style>


<form id="deleteUserForm"
      method="POST"
      style="display:none;">
    @csrf
    @method('DELETE')
</form>

<style id="user-create-controls-final">
.user-modal-field:has(#create_user_password) {
    position: relative;
}

#create_user_password {
    padding-right: 48px !important;
}

.user-password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    z-index: 5;
}

.user-password-toggle:hover {
    color: var(--primary);
    background: color-mix(in srgb, var(--primary) 10%, transparent);
}

.user-role-select {
    cursor: pointer;
    padding-right: 45px !important;
}
</style>

<script id="user-create-password-script">
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('create_user_password');
    const button = document.getElementById('createPasswordToggle');
    const icon = document.getElementById('createPasswordIcon');

    if (!input || !button || !icon) return;

    button.addEventListener('click', () => {
        const mostrar = input.type === 'password';

        input.type = mostrar ? 'text' : 'password';

        icon.classList.toggle('bi-eye', !mostrar);
        icon.classList.toggle('bi-eye-slash', mostrar);

        button.setAttribute(
            'aria-label',
            mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );
    });
});
</script>


<style id="user-role-arrow-final">

/* Rol / Permisos - flecha personalizada */
.user-modal-field:has(.user-role-select) {
    position: relative;
}

.user-role-select {
    appearance: none !important;
    -webkit-appearance: none !important;
    background-image: none !important;
    padding-right: 48px !important;
    cursor: pointer;
}

.user-modal-field:has(.user-role-select)::after {
    content: "\F282";
    font-family: "bootstrap-icons";
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: .9rem;
    font-weight: 700;
    color: var(--primary);
    pointer-events: none;
    z-index: 6;
}

html[data-color-mode="dark"] .user-modal-field:has(.user-role-select)::after {
    color: var(--primary);
}

</style>


<style id="user-kitchen-bar-role-styles">

/* Cocina */
.user-role.kitchen {
    color: #ea580c;
    background: #fff7ed;
    border: 1px solid #fdba74;
}

/* Barra */
.user-role.bar {
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #93c5fd;
}

/* Modo oscuro */
html[data-color-mode="dark"] .user-role.kitchen {
    color: #fb923c;
    background: rgba(234, 88, 12, .12);
    border-color: rgba(251, 146, 60, .35);
}

html[data-color-mode="dark"] .user-role.bar {
    color: #60a5fa;
    background: rgba(37, 99, 235, .12);
    border-color: rgba(96, 165, 250, .35);
}

</style>


<style id="user-kitchen-bar-avatar-final">

/* Avatar Cocina - mismo estilo visual que los demás */
.user-avatar.kitchen {
    color: #ea580c !important;
    background: #fff7ed !important;
    border: 1px solid #fed7aa !important;
}

/* Avatar Barra */
.user-avatar.bar {
    color: #2563eb !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
}

/* Modo oscuro */
html[data-color-mode="dark"] .user-avatar.kitchen {
    color: #fb923c !important;
    background: rgba(234, 88, 12, .10) !important;
    border-color: rgba(251, 146, 60, .32) !important;
}

html[data-color-mode="dark"] .user-avatar.bar {
    color: #60a5fa !important;
    background: rgba(37, 99, 235, .10) !important;
    border-color: rgba(96, 165, 250, .32) !important;
}

</style>


<style id="user-edit-controls-final">

.user-modal-field:has(#edit_user_password) {
    position: relative;
}

#edit_user_password {
    padding-right: 48px !important;
}

</style>

<script id="user-edit-password-script">
document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('edit_user_password');
    const button = document.getElementById('editPasswordToggle');
    const icon = document.getElementById('editPasswordIcon');

    if (!input || !button || !icon) return;

    button.addEventListener('click', () => {

        const mostrar = input.type === 'password';

        input.type = mostrar ? 'text' : 'password';

        icon.classList.toggle('bi-eye', !mostrar);
        icon.classList.toggle('bi-eye-slash', mostrar);

        button.setAttribute(
            'aria-label',
            mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );

        button.setAttribute(
            'title',
            mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );
    });

});
</script>

@endsection