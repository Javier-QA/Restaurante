@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
       CENTRO DE MANTENIMIENTO
    ========================================================= */

    .maintenance-page {
        width: 100%;
    }

    .maintenance-header {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 22px;
    }

    .maintenance-header > i {
        margin-top: 4px;
        color:var(--text-main);
        font-size: 1.22rem;
    }

    .maintenance-title {
        margin: 0;
        color: var(--text-main);
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -.35px;
    }

    .maintenance-subtitle {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: .8rem;
    }


    /* =========================================================
       TARJETAS
    ========================================================= */

    .maintenance-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;

        border: 1px solid var(--border-soft);
        border-radius: 14px;

        background: var(--card-bg);
        box-shadow: var(--shadow-soft);

        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease;
    }

    .maintenance-card:hover {
        transform: translateY(-2px);
        border-color:
            color-mix(
                in srgb,
                var(--primary) 18%,
                var(--border-soft)
            );

        box-shadow: 0 8px 22px rgba(15, 23, 42, .07);
    }

    .maintenance-card-top {
        height: 4px;
        width: 100%;
    }

    .maintenance-card.backup .maintenance-card-top {
        background: var(--primary);
    }

    .maintenance-card.restore .maintenance-card-top {
        background: #2563eb;
    }

    .maintenance-card.reset .maintenance-card-top {
        background: #dc2626;
    }

    .maintenance-card-header {
        padding: 20px 20px 12px;
    }

    .maintenance-card-heading {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .maintenance-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 10px;

        font-size: 1rem;
    }

    .backup .maintenance-icon {
        color: var(--primary);
        background:
            color-mix(
                in srgb,
                var(--primary) 8%,
                var(--card-bg)
            );

        border: 1px solid
            color-mix(
                in srgb,
                var(--primary) 15%,
                var(--border-soft)
            );
    }

    .restore .maintenance-icon {
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .reset .maintenance-icon {
        color: #dc2626;
        background: #fff1f2;
        border: 1px solid #fecaca;
    }

    .maintenance-card-title {
        margin: 1px 0 3px;

        color: var(--text-main);

        font-size: .88rem;
        font-weight: 800;
    }

    .maintenance-card-description {
        margin: 0;

        color: var(--text-muted);

        font-size: .68rem;
        line-height: 1.4;
    }


    /* =========================================================
       CONTENIDO
    ========================================================= */

    .maintenance-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;

        padding: 5px 20px 20px;
    }

    .maintenance-explanation {
        min-height: 76px;
        margin-bottom: 16px;

        color: var(--text-muted);

        font-size: .7rem;
        line-height: 1.55;
    }


    /* =========================================================
       CAMPOS
    ========================================================= */

    .maintenance-card .form-control {
        min-height: 40px;

        border: 1px solid var(--border-soft);
        border-radius: 8px;

        background: var(--card-bg);

        color: var(--text-main);

        font-size: .7rem;

        box-shadow: none;
    }

    .maintenance-card .form-control:focus {
        border-color: var(--primary);

        box-shadow:
            0 0 0 3px
            color-mix(
                in srgb,
                var(--primary) 9%,
                transparent
            );
    }

    .maintenance-card input[type="file"] {
        padding-top: 7px;
    }


    /* =========================================================
       RESUMEN DE REINICIO
    ========================================================= */

    .reset-summary {
        margin-bottom: 14px;
        padding: 11px 12px;

        border: 1px solid #fecaca;
        border-radius: 9px;

        background: #fffafa;
    }

    .reset-summary-title {
        display: flex;
        align-items: center;
        gap: 6px;

        margin-bottom: 8px;

        color: #b91c1c;

        font-size: .65rem;
        font-weight: 800;
    }

    .reset-summary ul {
        display: grid;
        gap: 5px;

        margin: 0;
        padding: 0;

        list-style: none;
    }

    .reset-summary li {
        display: flex;
        align-items: center;
        gap: 7px;

        color: #7f1d1d;

        font-size: .66rem;
        font-weight: 650;
    }

    .reset-summary li i {
        font-size: .68rem;
    }


    /* =========================================================
       BOTONES
    ========================================================= */

    .maintenance-action {
        min-height: 40px;
        margin-top: auto;

        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        border-radius: 8px;

        font-size: .7rem;
        font-weight: 750;

        transition:
            transform .15s ease,
            background .15s ease;
    }

    .maintenance-action:hover {
        transform: translateY(-1px);
    }

    .maintenance-action.primary {
        border: 1px solid var(--primary);
        background: var(--primary);
        color: #fff;
    }

    .maintenance-action.primary:hover {
        border-color: var(--primary-hover);
        background: var(--primary-hover);
        color: #fff;
    }

    .maintenance-action.warning {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #fff;
    }

    .maintenance-action.warning:hover {
        border-color: #1d4ed8;
        background: #1d4ed8;
        color: #fff;
    }

    .maintenance-action.danger {
        border: 1px solid #dc2626;
        background: #dc2626;
        color: #fff;
    }

    .maintenance-action.danger:hover {
        border-color: #b91c1c;
        background: #b91c1c;
        color: #fff;
    }


    /* =========================================================
       MODALES
    ========================================================= */

    .maintenance-modal .modal-content {
        overflow: hidden;

        border: 1px solid var(--border-soft);
        border-radius: 14px;

        background: var(--card-bg);

        box-shadow: 0 20px 50px rgba(15, 23, 42, .14);
    }

    .maintenance-modal .modal-header {
        padding: 17px 19px;

        border-bottom: 1px solid var(--border-soft);
    }

    .maintenance-modal .modal-body {
        padding: 19px;
    }

    .maintenance-modal .modal-footer {
        padding: 13px 19px;

        border-top: 1px solid var(--border-soft);
    }

    .maintenance-modal-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .maintenance-modal-icon {
        width: 35px;
        height: 35px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 9px;

        font-size: .9rem;
    }

    .maintenance-modal-icon.warning {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #2563eb;
    }

    .maintenance-modal-icon.danger {
        background: #fff1f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }

    .maintenance-modal h5 {
        margin: 0;

        color: var(--text-main);

        font-size: .85rem;
        font-weight: 800;
    }

    .maintenance-modal-subtitle {
        margin-top: 2px;

        color: var(--text-muted);

        font-size: .63rem;
    }

    .maintenance-warning-box {
        padding: 11px 12px;

        border-radius: 9px;

        font-size: .68rem;
        line-height: 1.5;
    }

    .maintenance-warning-box.warning {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1e40af;
    }

    .maintenance-warning-box.danger {
        border: 1px solid #fecaca;
        background: #fff1f2;
        color: #991b1b;
    }

    @media (max-width: 991.98px) {
        .maintenance-explanation {
            min-height: auto;
        }
    }
</style>



<style>
    /* =========================================================
       NOTIFICACIONES - MANTENIMIENTO
    ========================================================= */

    .maintenance-notifications {
        position: fixed;
        top: 82px;
        right: 24px;
        z-index: 1090;

        width: min(380px, calc(100vw - 32px));

        display: flex;
        flex-direction: column;
        gap: 10px;

        pointer-events: none;
    }

    .maintenance-toast {
        position: relative;

        display: flex;
        align-items: flex-start;
        gap: 11px;

        padding: 13px 38px 13px 13px;

        border: 1px solid var(--border-soft);
        border-radius: 11px;

        background: var(--card-bg);

        box-shadow: 0 12px 30px rgba(15, 23, 42, .12);

        overflow: hidden;
        pointer-events: auto;

        animation: maintenanceToastIn .25s ease;
    }

    .maintenance-toast::before {
        content: "";

        position: absolute;
        top: 0;
        left: 0;

        width: 4px;
        height: 100%;
    }

    .maintenance-toast.success::before {
        background: #16a34a;
    }

    .maintenance-toast.error::before {
        background: #dc2626;
    }

    .maintenance-toast.info::before {
        background: var(--primary);
    }

    .maintenance-toast-icon {
        width: 31px;
        height: 31px;
        flex: 0 0 31px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 8px;

        font-size: .8rem;
    }

    .maintenance-toast.success .maintenance-toast-icon {
        color: #15803d;
        background: #f0fdf4;
    }

    .maintenance-toast.error .maintenance-toast-icon {
        color: #dc2626;
        background: #fff1f2;
    }

    .maintenance-toast.info .maintenance-toast-icon {
        color: var(--primary);

        background:
            color-mix(
                in srgb,
                var(--primary) 8%,
                var(--card-bg)
            );
    }

    .maintenance-toast-content {
        min-width: 0;
        flex: 1;
    }

    .maintenance-toast-title {
        margin: 0 0 2px;

        color: var(--text-main);

        font-size: .72rem;
        font-weight: 800;
    }

    .maintenance-toast-message {
        margin: 0;

        color: var(--text-muted);

        font-size: .65rem;
        line-height: 1.45;
    }

    .maintenance-toast-close {
        position: absolute;
        top: 9px;
        right: 9px;

        width: 23px;
        height: 23px;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 0;
        border: 0;
        border-radius: 6px;

        background: transparent;
        color: var(--text-muted);

        font-size: .75rem;
    }

    .maintenance-toast-close:hover {
        background: var(--light-bg);
        color: var(--text-main);
    }

    .maintenance-toast.hiding {
        animation: maintenanceToastOut .2s ease forwards;
    }

    @keyframes maintenanceToastIn {
        from {
            opacity: 0;
            transform: translateX(18px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes maintenanceToastOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }

        to {
            opacity: 0;
            transform: translateX(18px);
        }
    }

    @media (max-width: 575.98px) {
        .maintenance-notifications {
            top: 70px;
            right: 16px;
            left: 16px;

            width: auto;
        }
    }
</style>
<div
    class="maintenance-notifications"
    id="maintenanceNotifications"
>

    @if(session('success'))
        <div class="maintenance-toast success">

            <div class="maintenance-toast-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <div class="maintenance-toast-content">
                <p class="maintenance-toast-title">
                    Operación completada
                </p>

                <p class="maintenance-toast-message">
                    {{ session('success') }}
                </p>
            </div>

            <button
                type="button"
                class="maintenance-toast-close"
                onclick="closeMaintenanceToast(this)"
                aria-label="Cerrar"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>
    @endif


    @if(session('error'))
        <div class="maintenance-toast error">

            <div class="maintenance-toast-icon">
                <i class="bi bi-exclamation-lg"></i>
            </div>

            <div class="maintenance-toast-content">
                <p class="maintenance-toast-title">
                    No se pudo completar
                </p>

                <p class="maintenance-toast-message">
                    {{ session('error') }}
                </p>
            </div>

            <button
                type="button"
                class="maintenance-toast-close"
                onclick="closeMaintenanceToast(this)"
                aria-label="Cerrar"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>
    @endif

</div>
<div class="maintenance-page">

    {{-- ENCABEZADO --}}
    <div class="maintenance-header">
        <i class="bi bi-tools"></i>

        <div>
            <h2 class="maintenance-title">
                Centro de Mantenimiento
            </h2>

            <p class="maintenance-subtitle">
                Gestiona la integridad de tu información y la salud del sistema
            </p>
        </div>
    </div>


    <div class="row g-3">

        {{-- =====================================================
             COPIA DE SEGURIDAD
        ====================================================== --}}
        <div class="col-lg-4">

            <div class="maintenance-card backup">

                <div class="maintenance-card-top"></div>

                <div class="maintenance-card-header">

                    <div class="maintenance-card-heading">

                        <div class="maintenance-icon">
                            <i class="bi bi-cloud-arrow-down-fill"></i>
                        </div>

                        <div>
                            <h5 class="maintenance-card-title">
                                Copia de Seguridad
                            </h5>

                            <p class="maintenance-card-description">
                                Respalda toda la base de datos
                            </p>
                        </div>

                    </div>

                </div>


                <div class="maintenance-card-body">

                    <p class="maintenance-explanation">
                        Genera un archivo <strong>.sql</strong> descargable con la configuración,
                        productos, ventas y movimientos del sistema. Se recomienda realizar
                        una copia antes de efectuar cambios importantes.
                    </p>

                    <form action="{{ route('system.backup') }}" method="POST" onsubmit="showBackupNotification()">
                        @csrf

                        <button
                            type="submit"
                            class="btn maintenance-action primary w-100"
                        >
                            <i class="bi bi-download"></i>
                            Descargar Backup
                        </button>
                    </form>

                </div>

            </div>

        </div>


        {{-- =====================================================
             RESTAURAR SISTEMA
        ====================================================== --}}
        <div class="col-lg-4">

            <div class="maintenance-card restore">

                <div class="maintenance-card-top"></div>

                <div class="maintenance-card-header">

                    <div class="maintenance-card-heading">

                        <div class="maintenance-icon">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>

                        <div>
                            <h5 class="maintenance-card-title">
                                Restaurar Sistema
                            </h5>

                            <p class="maintenance-card-description">
                                Recupera información desde un respaldo
                            </p>
                        </div>

                    </div>

                </div>


                <div class="maintenance-card-body">

                    <p class="maintenance-explanation">
                        Selecciona un archivo <strong>.sql</strong> generado previamente
                        para recuperar un estado anterior del sistema. Los datos actuales
                        serán reemplazados.
                    </p>


                    <div class="mb-3">

                        <input
                            type="file"
                            id="restoreBackupFile"
                            class="form-control"
                            accept=".sql"
                        >

                    </div>


                    <div class="mb-3">

                        <input
                            type="password"
                            id="restorePassword"
                            class="form-control"
                            placeholder="Confirma tu contraseña"
                        >

                    </div>


                    <button
                        type="button"
                        class="btn maintenance-action warning w-100"
                        onclick="openRestoreModal()"
                    >
                        <i class="bi bi-arrow-repeat"></i>
                        Ejecutar Restauración
                    </button>

                </div>

            </div>

        </div>


        {{-- =====================================================
             REINICIO MAESTRO
        ====================================================== --}}
        <div class="col-lg-4">

            <div class="maintenance-card reset">

                <div class="maintenance-card-top"></div>

                <div class="maintenance-card-header">

                    <div class="maintenance-card-heading">

                        <div class="maintenance-icon">
                            <i class="bi bi-trash3-fill"></i>
                        </div>

                        <div>
                            <h5 class="maintenance-card-title">
                                Reinicio Maestro
                            </h5>

                            <p class="maintenance-card-description">
                                Limpia los datos operativos de prueba
                            </p>
                        </div>

                    </div>

                </div>


                <div class="maintenance-card-body">

                    <p class="maintenance-explanation">
                        Elimina órdenes, gastos, cajas y reservas de prueba,
                        manteniendo los productos y la configuración principal
                        del sistema.
                    </p>


                    <div class="reset-summary">

                        <div class="reset-summary-title">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Datos que serán afectados
                        </div>

                        <ul>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                {{ $counts['orders'] }} Órdenes
                            </li>

                            <li>
                                <i class="bi bi-check2-circle"></i>
                                {{ $counts['reservations'] }} Reservas
                            </li>

                            <li>
                                <i class="bi bi-check2-circle"></i>
                                Stock de productos a 0
                            </li>
                        </ul>

                    </div>


                    <div class="mb-3">

                        <input
                            type="password"
                            id="resetPassword"
                            class="form-control"
                            placeholder="Confirma tu contraseña"
                        >

                    </div>


                    <button
                        type="button"
                        class="btn maintenance-action danger w-100"
                        onclick="openResetModal()"
                    >
                        <i class="bi bi-exclamation-octagon"></i>
                        Borrar Datos de Prueba
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     FORMULARIO REAL DE RESTAURACION
========================================================= --}}

<form
    id="restoreForm"
    action="{{ route('system.restore') }}"
    method="POST"
    enctype="multipart/form-data"
    class="d-none"
>
    @csrf

    <input
        type="file"
        name="backup_file"
        id="restoreRealFile"
        accept=".sql"
    >

    <input
        type="password"
        name="password"
        id="restoreRealPassword"
    >
</form>


{{-- =========================================================
     FORMULARIO REAL DE REINICIO
========================================================= --}}

<form
    id="resetForm"
    action="{{ route('system.reset') }}"
    method="POST"
    class="d-none"
>
    @csrf

    <input
        type="password"
        name="password"
        id="resetRealPassword"
    >
</form>


{{-- =========================================================
     MODAL RESTAURAR
========================================================= --}}

<div
    class="modal fade maintenance-modal"
    id="restoreConfirmModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div class="maintenance-modal-title">

                    <div class="maintenance-modal-icon warning">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>

                    <div>
                        <h5>Confirmar restauración</h5>

                        <div class="maintenance-modal-subtitle">
                            Esta operación modificará la información actual
                        </div>
                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"
                ></button>

            </div>


            <div class="modal-body">

                <div class="maintenance-warning-box warning">
                    <strong>Importante:</strong>
                    los datos actuales serán reemplazados por la información
                    contenida en el archivo de respaldo seleccionado.
                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="btn maintenance-action warning px-3"
                    onclick="submitRestore()"
                >
                    <i class="bi bi-arrow-repeat"></i>
                    Sí, restaurar
                </button>

            </div>

        </div>

    </div>
</div>


{{-- =========================================================
     MODAL REINICIO
========================================================= --}}

<div
    class="modal fade maintenance-modal"
    id="resetConfirmModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div class="maintenance-modal-title">

                    <div class="maintenance-modal-icon danger">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>

                    <div>
                        <h5>Confirmar reinicio maestro</h5>

                        <div class="maintenance-modal-subtitle">
                            Esta acción no se puede deshacer
                        </div>
                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"
                ></button>

            </div>


            <div class="modal-body">

                <div class="maintenance-warning-box danger">
                    <strong>Advertencia:</strong>
                    se eliminarán los datos operativos de prueba indicados.
                    Los productos y la configuración principal se mantendrán.
                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="btn maintenance-action danger px-3"
                    onclick="submitReset()"
                >
                    <i class="bi bi-trash3"></i>
                    Sí, borrar datos
                </button>

            </div>

        </div>

    </div>
</div>


<script>
    function openRestoreModal() {

        const fileInput =
            document.getElementById('restoreBackupFile');

        const password =
            document.getElementById('restorePassword').value;

        if (!fileInput.files.length) {
            fileInput.reportValidity();
            return;
        }

        if (!password.trim()) {
            document
                .getElementById('restorePassword')
                .reportValidity();

            return;
        }

        new bootstrap.Modal(
            document.getElementById('restoreConfirmModal')
        ).show();
    }


    function submitRestore() {

        const sourceFile =
            document.getElementById('restoreBackupFile');

        const realFile =
            document.getElementById('restoreRealFile');

        const transfer =
            new DataTransfer();

        transfer.items.add(sourceFile.files[0]);

        realFile.files = transfer.files;

        document.getElementById('restoreRealPassword').value =
            document.getElementById('restorePassword').value;

        document.getElementById('restoreForm').submit();
    }


    function openResetModal() {

        const passwordInput =
            document.getElementById('resetPassword');

        if (!passwordInput.value.trim()) {

            passwordInput.setCustomValidity(
                'Ingresa tu contraseña para continuar.'
            );

            passwordInput.reportValidity();

            setTimeout(() => {
                passwordInput.setCustomValidity('');
            }, 100);

            return;
        }

        const modalElement =
            document.getElementById('resetConfirmModal');

        const modal =
            bootstrap.Modal.getOrCreateInstance(modalElement);

        modal.show();
    }


    function submitReset() {

        const password =
            document.getElementById('resetPassword').value;

        document.getElementById('resetRealPassword').value =
            password;

        const modalElement =
            document.getElementById('resetConfirmModal');

        const modal =
            bootstrap.Modal.getInstance(modalElement);

        if (modal) {
            modal.hide();
        }

        document.getElementById('resetForm').submit();
    }
</script>


<script>
    function closeMaintenanceToast(button) {

        const toast = button.closest('.maintenance-toast');

        if (!toast) {
            return;
        }

        toast.classList.add('hiding');

        setTimeout(() => {
            toast.remove();
        }, 200);
    }


    function createMaintenanceToast(type, title, message) {

        const container =
            document.getElementById('maintenanceNotifications');

        if (!container) {
            return;
        }

        const icons = {
            success: 'bi-check-lg',
            error: 'bi-exclamation-lg',
            info: 'bi-arrow-clockwise'
        };

        const toast = document.createElement('div');

        toast.className =
            'maintenance-toast ' + type;

        toast.innerHTML = `
            <div class="maintenance-toast-icon">
                <i class="bi ${icons[type] || icons.info}"></i>
            </div>

            <div class="maintenance-toast-content">
                <p class="maintenance-toast-title">
                    ${title}
                </p>

                <p class="maintenance-toast-message">
                    ${message}
                </p>
            </div>

            <button
                type="button"
                class="maintenance-toast-close"
                aria-label="Cerrar"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        toast
            .querySelector('.maintenance-toast-close')
            .addEventListener('click', function () {
                closeMaintenanceToast(this);
            });

        container.appendChild(toast);

        setTimeout(() => {

            if (toast.isConnected) {

                toast.classList.add('hiding');

                setTimeout(() => {
                    toast.remove();
                }, 200);
            }

        }, 5500);
    }


    function showBackupNotification() {

        createMaintenanceToast(
            'info',
            'Generando copia de seguridad',
            'Estamos preparando el archivo de respaldo para su descarga.'
        );
    }


    document.addEventListener('DOMContentLoaded', function () {

        document
            .querySelectorAll('.maintenance-toast')
            .forEach(function (toast) {

                setTimeout(() => {

                    if (toast.isConnected) {

                        toast.classList.add('hiding');

                        setTimeout(() => {
                            toast.remove();
                        }, 200);
                    }

                }, 5500);

            });

    });
</script>

<style>
/* DARK MODE - RESUMEN REINICIO MAESTRO */

html[data-color-mode="dark"] .reset-summary {
    background: rgba(220, 38, 38, 0.10) !important;
    border: 1px solid rgba(248, 113, 113, 0.38) !important;
    color: #fecaca !important;
    box-shadow: none !important;
}

/* Título de advertencia */
html[data-color-mode="dark"] .reset-summary-title {
    color: #fca5a5 !important;
}

html[data-color-mode="dark"] .reset-summary-title i {
    color: #f87171 !important;
}

/* Lista de datos afectados */
html[data-color-mode="dark"] .reset-summary ul,
html[data-color-mode="dark"] .reset-summary li {
    color: #fecaca !important;
}

/* Iconos de la lista */
html[data-color-mode="dark"] .reset-summary li i {
    color: #f87171 !important;
}

/* Números y textos resaltados */
html[data-color-mode="dark"] .reset-summary strong,
html[data-color-mode="dark"] .reset-summary b {
    color: #ffffff !important;
}

</style>

<style>
/* DARK MODE - MANTENIMIENTO ICONOS DEFINITIVO */

/* Base */
html[data-color-mode="dark"] .maintenance-icon {
    background: #17283d !important;
    border: 1px solid #29445f !important;
    box-shadow: none !important;
}

/* Backup */
html[data-color-mode="dark"]
.maintenance-card.backup .maintenance-icon {
    background: rgba(245,158,11,.14) !important;
    color: #fbbf24 !important;
    border-color: rgba(251,191,36,.30) !important;
}

/* Restaurar */
html[data-color-mode="dark"]
.maintenance-card.restore .maintenance-icon {
    background: rgba(59,130,246,.14) !important;
    color: #60a5fa !important;
    border-color: rgba(96,165,250,.30) !important;
}

/* Reinicio Maestro */
html[data-color-mode="dark"]
.maintenance-card.reset .maintenance-icon {
    background: rgba(239,68,68,.14) !important;
    color: #f87171 !important;
    border-color: rgba(248,113,113,.30) !important;
}

/* El icono debe heredar el color */
html[data-color-mode="dark"]
.maintenance-card .maintenance-icon i {
    color: inherit !important;
}


/* Modal Restaurar */
html[data-color-mode="dark"]
.maintenance-modal-icon.warning {
    background: rgba(245,158,11,.14) !important;
    color: #fbbf24 !important;
    border: 1px solid rgba(251,191,36,.30) !important;
}

/* Modal Reinicio */
html[data-color-mode="dark"]
.maintenance-modal-icon.danger {
    background: rgba(239,68,68,.14) !important;
    color: #f87171 !important;
    border: 1px solid rgba(248,113,113,.30) !important;
}


/* Aviso restauración */
html[data-color-mode="dark"]
.maintenance-warning-box.warning {
    background: rgba(245,158,11,.10) !important;
    color: #fde68a !important;
    border-color: rgba(251,191,36,.30) !important;
}

/* Aviso reinicio */
html[data-color-mode="dark"]
.maintenance-warning-box.danger {
    background: rgba(239,68,68,.10) !important;
    color: #fecaca !important;
    border-color: rgba(248,113,113,.30) !important;
}


/* Resumen Reinicio Maestro */
html[data-color-mode="dark"] .reset-summary {
    background: rgba(239,68,68,.08) !important;
    border-color: rgba(248,113,113,.28) !important;
}

html[data-color-mode="dark"] .reset-summary-title {
    color: #fca5a5 !important;
}

</style>

@endsection