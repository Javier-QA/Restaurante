<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <style>

        /* =========================================================
           COLORES DEL SISTEMA
        ========================================================= */

        :root {
            /* NARANJA */
            --primary: #ff8c00;
            --primary-hover: #e07b00;

            /* AZUL */
            --dark-bg: #063970;
            --dark-bg-2: #0b4f8a;
            --dark-bg-3: #042a54;

            /* FONDOS */
            --light-bg: #eef5fb;
            --card-bg: #ffffff;

            /* TEXTOS */
            --text-main: #172033;
            --text-muted: #64748b;

            /* BORDES */
            --border-soft: #dce7f1;

            /* RADIOS */
            --radius-xl: 22px;
            --radius-md: 15px;
            --radius-sm: 11px;

            /* SOMBRA */
            --shadow-soft: 0 8px 30px rgba(6,57,112,0.10);

            /* Colores auxiliares del dashboard */
            --accent-1: #0b84c6;
            --accent-2: #16a34a;
            --accent-3: #ff8c00;
            --accent-4: #06b6d4;
            --theme-shadow: rgba(6,57,112,.12);
        }

        /* =========================================================
           TEMAS DINÁMICOS DEL SISTEMA
        ========================================================= */

        .theme-ocean-orange {
            --primary: #ff8c00;
            --primary-hover: #e07b00;
            --dark-bg: #063970;
            --dark-bg-2: #0b84c6;
            --dark-bg-3: #042a54;
            --light-bg: #eef8fc;
            --text-main: #17324d;
            --text-muted: #637b91;
            --border-soft: #d8eaf3;
            --accent-1: #0b84c6;
            --accent-2: #16a34a;
            --accent-3: #ff8c00;
            --accent-4: #ffb347;
            --theme-shadow: rgba(6,57,112,.13);
        }

        .theme-lime-blue {
            --primary: #84cc16;
            --primary-hover: #65a30d;
            --dark-bg: #063970;
            --dark-bg-2: #0b4f8a;
            --dark-bg-3: #042a54;
            --light-bg: #f2fbf3;
            --text-main: #173b32;
            --text-muted: #66827a;
            --border-soft: #dcefdc;
            --accent-1: #0b74b8;
            --accent-2: #22a06b;
            --accent-3: #84cc16;
            --accent-4: #f59e0b;
            --theme-shadow: rgba(34,160,107,.13);
        }

        .theme-purple-orange {
            --primary: #ff8c00;
            --primary-hover: #e07b00;
            --dark-bg: #4c1d95;
            --dark-bg-2: #7c3aed;
            --dark-bg-3: #3b176b;
            --light-bg: #f7f2ff;
            --text-main: #33284e;
            --text-muted: #75688e;
            --border-soft: #e8dcf6;
            --accent-1: #7c3aed;
            --accent-2: #a855f7;
            --accent-3: #ff8c00;
            --accent-4: #ec4899;
            --theme-shadow: rgba(76,29,149,.13);
        }

        .theme-sand-navy {
            --primary: #c98a52;
            --primary-hover: #ad7040;
            --dark-bg: #063970;
            --dark-bg-2: #0b4f8a;
            --dark-bg-3: #042a54;
            --light-bg: #f7f1e9;
            --text-main: #42372c;
            --text-muted: #786b5e;
            --border-soft: #eadaca;
            --accent-1: #0b4f8a;
            --accent-2: #7b8f72;
            --accent-3: #c98a52;
            --accent-4: #d9ad78;
            --theme-shadow: rgba(201,138,82,.14);
        }

        .theme-teal-amber {
            --primary: #f59e0b;
            --primary-hover: #d98706;
            --dark-bg: #07575b;
            --dark-bg-2: #0f8b8d;
            --dark-bg-3: #064649;
            --light-bg: #eef9f8;
            --text-main: #183b3c;
            --text-muted: #617f80;
            --border-soft: #d4ece9;
            --accent-1: #0f8b8d;
            --accent-2: #14b8a6;
            --accent-3: #f59e0b;
            --accent-4: #f97316;
            --theme-shadow: rgba(15,139,141,.14);
        }

        .theme-wine-blue {
            --primary: #d94f70;
            --primary-hover: #bd3f5e;
            --dark-bg: #791837;
            --dark-bg-2: #3346a8;
            --dark-bg-3: #5d102a;
            --light-bg: #faf0f4;
            --text-main: #442837;
            --text-muted: #846675;
            --border-soft: #efd9e2;
            --accent-1: #3346a8;
            --accent-2: #7c4dff;
            --accent-3: #d94f70;
            --accent-4: #f59e0b;
            --theme-shadow: rgba(121,24,55,.14);
        }

        /* =========================================================
           COMPONENTES QUE HEREDAN EL TEMA
        ========================================================= */

        body {
            background-color: var(--light-bg) !important;
            color: var(--text-main);
        }

        .sidebar {
            background: linear-gradient(180deg, var(--dark-bg) 0%, var(--dark-bg-3) 100%) !important;
        }

        .logo-box,
        .user-avatar {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover)) !important;
            box-shadow: 0 6px 18px var(--theme-shadow) !important;
        }

        .nav-link.active {
            background: var(--primary) !important;
            box-shadow: 0 5px 16px var(--theme-shadow) !important;
        }

        .top-navbar,
        .card {
            box-shadow: 0 8px 30px var(--theme-shadow) !important;
        }

        .card-header,
        .form-control,
        .form-select {
            border-color: var(--border-soft) !important;
        }

        .btn-primary {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .btn-primary:hover {
            background: var(--primary-hover) !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--theme-shadow) !important;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        /* Tarjetas principales del dashboard: ahora también cambian */
        .bg-gradient-blue {
            background: linear-gradient(135deg, var(--accent-1), var(--dark-bg-2)) !important;
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, var(--accent-2), var(--primary)) !important;
        }

        .bg-gradient-red {
            background: linear-gradient(135deg, var(--accent-3), var(--primary-hover)) !important;
        }

        .bg-gradient-cyan {
            background: linear-gradient(135deg, var(--accent-4), var(--accent-1)) !important;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-main);
            font-size: 0.92rem;
            overflow-x: hidden;
        }


        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            width: 260px;
            height: 100vh;

            position: fixed;

            top: 0;
            left: 0;

            background:
                linear-gradient(
                    180deg,
                    var(--dark-bg) 0%,
                    var(--dark-bg-3) 100%
                );

            color: white;

            z-index: 1050;

            display: flex;
            flex-direction: column;

            transition: transform .3s ease;

            overflow: hidden;
        }

        .sidebar-header {
            min-height: 82px;

            padding: 17px 18px;

            display: flex;
            align-items: center;

            gap: 12px;

            flex-shrink: 0;

            border-bottom:
                1px solid rgba(255,255,255,.08);
        }

        .logo-box {
            width: 46px;
            height: 46px;

            flex-shrink: 0;

            background:
                linear-gradient(
                    135deg,
                    #ff9d1c,
                    #ff7200
                );

            color: white;

            border-radius: 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;

            box-shadow:
                0 6px 18px rgba(255,140,0,.40);
        }

        .brand-name {
            color: white;

            font-size: 17px;
            font-weight: 800;

            letter-spacing: -.4px;

            min-width: 0;
        }

        .sidebar-menu {
            flex: 1;

            padding: 12px 11px 25px;

            overflow-y: auto;

            scrollbar-width: thin;

            scrollbar-color:
                rgba(255,255,255,.18)
                transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.18);
            border-radius: 20px;
        }

        .menu-category {
            margin: 20px 11px 8px;

            color: rgba(255,255,255,.45);

            font-size: .64rem;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: 1.2px;
        }

        .nav-link {
            min-height: 44px;

            margin-bottom: 3px;

            padding: 10px 13px;

            display: flex;
            align-items: center;

            color: rgba(255,255,255,.76);

            text-decoration: none;

            border-radius: 10px;

            font-size: .82rem;
            font-weight: 500;

            transition: all .18s ease;
        }

        .nav-link i {
            width: 22px;

            margin-right: 10px;

            text-align: center;

            font-size: 1.05rem;

            opacity: .85;
        }

        .nav-link:hover {
            color: white;

            background:
                rgba(255,255,255,.10);

            transform: translateX(2px);
        }

        .nav-link.active {
            color: white;

            background: var(--primary);

            box-shadow:
                0 5px 16px rgba(255,140,0,.30);
        }

        .nav-link.active i {
            opacity: 1;
        }


        /* =========================================================
           MAIN CONTENT
        ========================================================= */

        .main-content {
            margin-left: 260px;

            min-height: 100vh;

            padding: 22px 27px;

            display: flex;
            flex-direction: column;

            transition: margin-left .3s;
        }

        body.pos-page .main-content {
            padding: 0;

            height: 100vh;

            overflow: hidden;
        }


        /* =========================================================
           TOP NAVBAR
        ========================================================= */

        .top-navbar {
            min-height: 66px;

            margin-bottom: 22px;

            padding: 10px 17px;

            background: white;

            border-radius: var(--radius-md);

            box-shadow: var(--shadow-soft);

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;
        }

        .user-profile-btn {
            padding: 5px 9px;

            display: flex;
            align-items: center;

            gap: 10px;

            border:
                1px solid transparent;

            border-radius: 50px;

            cursor: pointer;

            transition: all .2s;
        }

        .user-profile-btn:hover {
            background: #f4f8fc;

            border-color: #dce7f1;
        }

        .user-avatar {
            width: 38px;
            height: 38px;

            background:
                linear-gradient(
                    135deg,
                    #ff9d1c,
                    #ff6b35
                );

            color: white;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 800;

            box-shadow:
                0 3px 10px rgba(255,140,0,.20);
        }


        /* =========================================================
           COMPONENTES
        ========================================================= */

        .card {
            border: none;

            background: var(--card-bg);

            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-soft);

            overflow: hidden;
        }

        .card-header {
            padding: 1.3rem;

            background: transparent;

            border-bottom:
                1px solid #e1eaf3;
        }

        .card-body {
            padding: 1.3rem;
        }

        .btn {
            border: none;

            border-radius: 50px;

            padding: .58rem 1.25rem;

            font-size: .84rem;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--primary);

            color: white;

            box-shadow:
                0 4px 12px rgba(255,140,0,.28);
        }

        .btn-primary:hover {
            background: var(--primary-hover);

            color: white;
        }

        .form-control,
        .form-select {
            padding: .72rem .95rem;

            background: #f7faff;

            border:
                1px solid #dce7f1;

            border-radius:
                var(--radius-sm);
        }

        .form-control:focus,
        .form-select:focus {
            border-color:
                var(--primary);

            background: white;

            box-shadow:
                0 0 0 3px rgba(255,140,0,.13);
        }

        .table {
            --bs-table-hover-bg: #f4f8fc;
        }

        .badge {
            border-radius: 50px;
        }


        /* =========================================================
           TARJETAS DASHBOARD
        ========================================================= */

        .card-solid {
            color: white !important;

            border-radius:
                var(--radius-xl);

            position: relative;

            overflow: hidden;
        }

        .bg-gradient-blue {
            background:
                linear-gradient(
                    135deg,
                    #3b82f6,
                    #2563eb
                ) !important;
        }

        .bg-gradient-green {
            background:
                linear-gradient(
                    135deg,
                    #10b981,
                    #059669
                ) !important;
        }

        .bg-gradient-red {
            background:
                linear-gradient(
                    135deg,
                    #ef4444,
                    #b91c1c
                ) !important;
        }

        .bg-gradient-cyan {
            background:
                linear-gradient(
                    135deg,
                    #06b6d4,
                    #0891b2
                ) !important;
        }

        .card-solid h2 {
            margin: 10px 0;

            font-size: 2.3rem;

            font-weight: 800;
        }

        .card-solid .icon-bg {
            position: absolute;

            right: 20px;

            top: 50%;

            transform:
                translateY(-50%);

            font-size: 5rem;

            opacity: .15;

            pointer-events: none;
        }


        /* =========================================================
           MOBILE OVERLAY
        ========================================================= */

        .mobile-overlay {
            position: fixed;

            inset: 0;

            display: none;

            background:
                rgba(3,38,75,.76);

            backdrop-filter:
                blur(4px);

            z-index: 1040;
        }

        .mobile-overlay.show {
            display: block;
        }


        </style>


<style>
    /* =========================================================
       MENU DE CUENTA
    ========================================================= */

    .account-dropdown {
        width: 265px;
        margin-top: 9px !important;
        padding: 8px;

        border: 1px solid var(--border-soft) !important;
        border-radius: 14px;

        background: var(--card-bg);

        box-shadow:
            0 16px 40px rgba(15, 23, 42, .12) !important;
    }

    .account-dropdown-title {
        padding: 7px 10px 8px;

        color: var(--text-muted);

        font-size: .63rem;
        font-weight: 800;

        letter-spacing: .08em;
    }

    .account-dropdown-item {
        min-height: 55px;

        display: flex;
        align-items: center;
        gap: 10px;

        padding: 8px 9px;

        border: 0;
        border-radius: 9px;

        background: transparent;

        transition:
            background-color .15s ease,
            color .15s ease;
    }

    .account-dropdown-item:hover,
    .account-dropdown-item:focus {
        background:
            color-mix(
                in srgb,
                var(--primary) 7%,
                var(--card-bg)
            );
    }

    .account-dropdown-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        border: 1px solid
            color-mix(
                in srgb,
                var(--primary) 15%,
                var(--border-soft)
            );

        border-radius: 9px;

        background:
            color-mix(
                in srgb,
                var(--primary) 7%,
                var(--card-bg)
            );

        color: var(--primary);

        font-size: .86rem;
    }

    .account-dropdown-content {
        min-width: 0;
        flex: 1;

        display: flex;
        flex-direction: column;
        align-items: flex-start;

        line-height: 1.25;
    }

    .account-dropdown-label {
        color: var(--text-main);

        font-size: .72rem;
        font-weight: 750;
    }

    .account-dropdown-description {
        margin-top: 2px;

        color: var(--text-muted);

        font-size: .59rem;
        font-weight: 500;
    }

    .account-dropdown-arrow {
        color: var(--text-muted);

        font-size: .62rem;

        transition: transform .15s ease;
    }

    .account-dropdown-item:hover .account-dropdown-arrow {
        color: var(--primary);
        transform: translateX(2px);
    }

    .account-dropdown-divider {
        height: 1px;

        margin: 6px 7px;

        background: var(--border-soft);
    }


    /* CERRAR SESION */

    .account-dropdown-icon.logout {
        border-color: #fecaca;
        background: #fef2f2;
        color: #dc2626;
    }

    .account-dropdown-logout .account-dropdown-label {
        color: #dc2626;
    }

    .account-dropdown-logout:hover,
    .account-dropdown-logout:focus {
        background: #fef2f2;
    }

    .account-dropdown-logout:hover .account-dropdown-icon {
        border-color: #fca5a5;
        background: #fee2e2;
    }


    @media (max-width: 575.98px) {
        .account-dropdown {
            width: 250px;
        }
    }
</style>

<style>
    /* =========================================================
       MODAL MI PERFIL
    ========================================================= */

    .profile-system-modal .modal-dialog {
        max-width: 510px;
    }

    .profile-system-modal .modal-content {
        overflow: hidden;

        border: 1px solid var(--border-soft);
        border-radius: 15px;

        background: var(--card-bg);

        box-shadow:
            0 24px 60px rgba(15, 23, 42, .16);
    }


    /* ENCABEZADO */

    .profile-modal-header {
        align-items: flex-start;

        padding: 20px 22px 17px;

        border-bottom: 1px solid var(--border-soft);

        background: var(--card-bg);
    }

    .profile-modal-heading {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .profile-modal-heading > i {
        margin-top: 2px;

        color:var(--text-main);

        font-size: 1.05rem;
    }

    .profile-modal-title {
        margin: 0;

        color: var(--text-main);

        font-size: 1rem;
        font-weight: 800;

        letter-spacing: -.2px;
    }

    .profile-modal-subtitle {
        margin: 3px 0 0;

        color: var(--text-muted);

        font-size: .66rem;
    }


    /* CUERPO */

    .profile-modal-body {
        display: grid;
        gap: 16px;

        padding: 20px 22px;
    }

    .profile-section {
        padding: 16px;

        border: 1px solid var(--border-soft);
        border-radius: 11px;

        background: var(--card-bg);
    }

    .profile-security-section {
        background:
            color-mix(
                in srgb,
                var(--light-bg) 45%,
                var(--card-bg)
            );
    }

    .profile-section-title {
        display: flex;
        align-items: center;
        gap: 7px;

        margin-bottom: 14px;

        color: var(--text-main);

        font-size: .7rem;
        font-weight: 800;
    }

    .profile-section-title i {
        color:var(--text-main);

        font-size: .78rem;
    }


    /* CAMPOS */

    .profile-field {
        margin-bottom: 14px;
    }

    .profile-label {
        display: block;

        margin-bottom: 6px;

        color: var(--text-main);

        font-size: .66rem;
        font-weight: 700;
    }

    .profile-input-wrapper {
        position: relative;
    }

    .profile-input-icon {
        position: absolute;
        top: 50%;
        left: 12px;

        z-index: 3;

        display: flex;
        align-items: center;
        justify-content: center;

        color: var(--text-muted);

        font-size: .76rem;

        transform: translateY(-50%);

        pointer-events: none;
    }

    .profile-input {
        min-height: 42px;

        padding-left: 36px;

        border: 1px solid var(--border-soft);
        border-radius: 9px;

        background: var(--card-bg);

        color: var(--text-main);

        font-size: .7rem;

        box-shadow: none !important;

        transition:
            border-color .15s ease,
            box-shadow .15s ease,
            background .15s ease;
    }

    .profile-input:focus {
        border-color: var(--primary);

        background: var(--card-bg);

        box-shadow:
            0 0 0 3px
            color-mix(
                in srgb,
                var(--primary) 8%,
                transparent
            ) !important;
    }

    .profile-input-wrapper:focus-within .profile-input-icon {
        color: var(--primary);
    }


    /* CORREO BLOQUEADO */

    .profile-input-wrapper.readonly .profile-input {
        padding-right: 38px;

        background: var(--light-bg);

        color: var(--text-muted);

        cursor: default;
    }

    .profile-locked {
        position: absolute;
        top: 50%;
        right: 12px;

        display: flex;
        align-items: center;

        color: var(--text-muted);

        font-size: .67rem;

        transform: translateY(-50%);
    }


    /* CONTRASEÑA */

    .profile-password-input {
        padding-right: 43px;
    }

    .profile-password-toggle {
        position: absolute;
        top: 50%;
        right: 7px;

        z-index: 4;

        width: 31px;
        height: 31px;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 0;

        border: 0;
        border-radius: 7px;

        background: transparent;

        color: var(--text-muted);

        font-size: .8rem;

        transform: translateY(-50%);

        transition:
            color .15s ease,
            background .15s ease;
    }

    .profile-password-toggle:hover {
        background:
            color-mix(
                in srgb,
                var(--primary) 7%,
                var(--card-bg)
            );

        color: var(--primary);
    }

    .profile-password-toggle:focus-visible {
        outline: 2px solid
            color-mix(
                in srgb,
                var(--primary) 30%,
                transparent
            );

        outline-offset: 1px;
    }

    .profile-help {
        margin-top: 5px;

        color: var(--text-muted);

        font-size: .59rem;
        line-height: 1.4;
    }


    /* PIE */

    .profile-modal-footer {
        gap: 8px;

        padding: 14px 22px;

        border-top: 1px solid var(--border-soft);

        background:
            color-mix(
                in srgb,
                var(--light-bg) 35%,
                var(--card-bg)
            );
    }

    .profile-btn-cancel,
    .profile-btn-save {
        min-height: 38px;

        padding: 0 15px;

        border-radius: 8px;

        font-size: .68rem;
        font-weight: 750;
    }

    .profile-btn-cancel {
        border: 1px solid var(--border-soft);

        background: var(--card-bg);

        color: var(--text-main);
    }

    .profile-btn-cancel:hover {
        border-color:
            color-mix(
                in srgb,
                var(--text-muted) 30%,
                var(--border-soft)
            );

        background: var(--light-bg);

        color: var(--text-main);
    }

    .profile-btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;

        border: 1px solid var(--primary);

        background: var(--primary);

        color: #fff;
    }

    .profile-btn-save:hover {
        border-color: var(--primary-hover);

        background: var(--primary-hover);

        color: #fff;
    }


    @media (max-width: 575.98px) {

        .profile-system-modal .modal-dialog {
            margin: 12px;
        }

        .profile-modal-header,
        .profile-modal-body,
        .profile-modal-footer {
            padding-left: 17px;
            padding-right: 17px;
        }

        .profile-section {
            padding: 14px;
        }

        .profile-modal-footer {
            flex-wrap: nowrap;
        }

        .profile-modal-footer .btn {
            flex: 1;
        }
    }
</style>

<style>
/* ============================================================
   HEADER SUPERIOR - DISEÑO PROFESIONAL
============================================================ */


/* TITULO DEL MODULO */

.topbar-module-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.topbar-module-icon {
    width: 32px;
    height: 32px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 32px;

    border-radius: 9px;

    background:
        color-mix(
            in srgb,
            var(--primary) 9%,
            var(--card-bg)
        );

    color: var(--primary);

    font-size: .78rem;
}

.topbar-module-title h5 {
    margin: 0;

    color: var(--text-main) !important;

    font-size: .96rem;
    font-weight: 800;

    line-height: 1.2;
    letter-spacing: -.25px;
}


/* ============================================================
   USUARIO
============================================================ */

.header-user-control {
    min-height: 50px;

    display: flex !important;
    align-items: center !important;

    gap: 10px;

    padding: 5px 7px 5px 13px !important;

    border: 1px solid transparent;
    border-radius: 13px;

    background: transparent;

    cursor: pointer;
    user-select: none;

    transition:
        background-color .16s ease,
        border-color .16s ease,
        box-shadow .16s ease;
}

.header-user-control:hover,
.header-user-control.show {
    border-color:
        color-mix(
            in srgb,
            var(--primary) 13%,
            var(--border-soft)
        );

    background:
        color-mix(
            in srgb,
            var(--primary) 5%,
            var(--card-bg)
        );
}

.header-user-control:focus-visible {
    outline: none;

    border-color:
        color-mix(
            in srgb,
            var(--primary) 30%,
            var(--border-soft)
        );

    box-shadow:
        0 0 0 3px
        color-mix(
            in srgb,
            var(--primary) 8%,
            transparent
        );
}


/* INFORMACION */

.header-user-info {
    min-width: 100px;

    flex-direction: column;

    align-items: flex-end;
    justify-content: center;

    line-height: 1.1;
}

.header-user-name {
    max-width: 155px;

    overflow: hidden;

    color: var(--text-main);

    font-size: .7rem;
    font-weight: 800;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.header-user-role {
    margin-top: 4px;

    color: var(--text-muted);

    font-size: .58rem;
    font-weight: 600;

    text-transform: capitalize;
}


/* AVATAR */

.header-user-avatar {
    width: 40px !important;
    height: 40px !important;

    flex: 0 0 40px;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;

    border:
        1px solid
        color-mix(
            in srgb,
            var(--primary) 25%,
            transparent
        ) !important;

    border-radius: 11px !important;

    background:
        linear-gradient(
            135deg,
            var(--primary),
            var(--primary-hover)
        ) !important;

    color: #fff !important;

    font-size: .78rem !important;
    font-weight: 800 !important;

    box-shadow:
        0 6px 14px
        color-mix(
            in srgb,
            var(--primary) 18%,
            transparent
        ) !important;
}


/* FLECHA */

.header-user-chevron {
    width: 24px;
    height: 24px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 24px;

    border-radius: 7px;

    color: var(--text-muted);

    transition:
        color .16s ease,
        background-color .16s ease,
        transform .18s ease;
}

.header-user-chevron i {
    font-size: .6rem;
}

.header-user-control:hover .header-user-chevron {
    background:
        color-mix(
            in srgb,
            var(--primary) 8%,
            transparent
        );

    color: var(--primary);
}

.header-user-control.show .header-user-chevron {
    color: var(--primary);
    transform: rotate(180deg);
}


/* ============================================================
   RESPONSIVE
============================================================ */

@media (max-width: 575.98px) {

    .header-user-control {
        min-height: 44px;

        gap: 5px;

        padding: 3px 5px !important;
    }

    .header-user-avatar {
        width: 37px !important;
        height: 37px !important;

        flex-basis: 37px;

        border-radius: 10px !important;
    }

    .header-user-chevron {
        width: 19px;
        flex-basis: 19px;
    }
}

</style>

<style>
/* ============================================================
   MODO OSCURO GLOBAL - EL CAPITAN
============================================================ */

/* ------------------------------------------------------------
   BOTON SOL / LUNA
------------------------------------------------------------ */

.system-theme-toggle {

    width: 42px;
    height: 42px;

    flex: 0 0 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    margin-right: 5px;
    padding: 0;

    border:
        1px solid
        var(--border-soft);

    border-radius: 12px;

    background:
        var(--card-bg);

    color:
        var(--text-muted);

    font-size: .95rem;

    cursor: pointer;

    box-shadow:
        0 4px 13px
        rgba(15, 23, 42, .055);

    transition:
        color .18s ease,
        border-color .18s ease,
        background .18s ease,
        transform .18s ease,
        box-shadow .18s ease;
}


.system-theme-toggle:hover {

    color:
        var(--primary);

    border-color:
        color-mix(
            in srgb,
            var(--primary) 22%,
            var(--border-soft)
        );

    background:
        color-mix(
            in srgb,
            var(--primary) 5%,
            var(--card-bg)
        );

    transform:
        translateY(-1px);

    box-shadow:
        0 7px 17px
        rgba(15, 23, 42, .08);
}


.system-theme-toggle:focus-visible {

    outline: none;

    box-shadow:
        0 0 0 3px
        color-mix(
            in srgb,
            var(--primary) 15%,
            transparent
        );
}


.system-theme-toggle i {

    display: inline-flex;

    transition:
        transform .3s ease;
}


.system-theme-toggle:hover i {
    transform: rotate(-12deg);
}


/* ------------------------------------------------------------
   PALETA OSCURA
------------------------------------------------------------ */

html[data-color-mode="dark"] {

    color-scheme: dark;

    --light-bg: #0b1220;
    --card-bg: #111c2d;

    --text-main: #e8eef6;
    --text-muted: #8fa2b8;

    --border-soft: #223249;

    --theme-shadow:
        rgba(0, 0, 0, .25);
}


/* ------------------------------------------------------------
   DOCUMENTO
------------------------------------------------------------ */

html[data-color-mode="dark"] body {

    background:
        #0b1220 !important;

    color:
        var(--text-main) !important;
}


/* ------------------------------------------------------------
   CONTENIDO PRINCIPAL
------------------------------------------------------------ */

html[data-color-mode="dark"] .main-content,
html[data-color-mode="dark"] .content-wrapper {

    background:
        #0b1220 !important;
}


/* ------------------------------------------------------------
   HEADER
------------------------------------------------------------ */

html[data-color-mode="dark"] .top-navbar {

    background:
        #111c2d !important;

    border-color:
        #223249 !important;

    box-shadow:
        0 7px 22px
        rgba(0,0,0,.20) !important;
}


html[data-color-mode="dark"] .topbar-module-title h5,
html[data-color-mode="dark"] .header-user-name {

    color:
        #e8eef6 !important;
}


html[data-color-mode="dark"] .header-user-role {

    color:
        #8fa2b8 !important;
}


html[data-color-mode="dark"] .header-user-control {

    border-color:
        #223249 !important;

    background:
        #111c2d !important;
}


html[data-color-mode="dark"] .header-user-control:hover,
html[data-color-mode="dark"] .header-user-control.show {

    background:
        #162438 !important;
}


/* ------------------------------------------------------------
   SIDEBAR
------------------------------------------------------------ */

html[data-color-mode="dark"] .sidebar {

    box-shadow:
        8px 0 25px
        rgba(0,0,0,.18);
}


/* ------------------------------------------------------------
   CARDS / CONTENEDORES
------------------------------------------------------------ */

html[data-color-mode="dark"] .card,
html[data-color-mode="dark"] .modal-content,
html[data-color-mode="dark"] .dropdown-menu,
html[data-color-mode="dark"] .account-dropdown {

    background:
        #111c2d !important;

    border-color:
        #223249 !important;

    color:
        #e8eef6 !important;
}


html[data-color-mode="dark"] .card-header,
html[data-color-mode="dark"] .card-footer,
html[data-color-mode="dark"] .modal-header,
html[data-color-mode="dark"] .modal-footer {

    background:
        #111c2d !important;

    border-color:
        #223249 !important;
}


/* ------------------------------------------------------------
   TEXTOS GENERALES
------------------------------------------------------------ */

html[data-color-mode="dark"] h1,
html[data-color-mode="dark"] h2,
html[data-color-mode="dark"] h3,
html[data-color-mode="dark"] h4,
html[data-color-mode="dark"] h5,
html[data-color-mode="dark"] h6,
html[data-color-mode="dark"] .text-dark,
html[data-color-mode="dark"] .fw-bold,
html[data-color-mode="dark"] .fw-semibold {

    color:
        #e8eef6 !important;
}


html[data-color-mode="dark"] .text-muted {

    color:
        #8fa2b8 !important;
}


/* ------------------------------------------------------------
   TABLAS
------------------------------------------------------------ */

html[data-color-mode="dark"] .table {

    --bs-table-bg:
        transparent;

    --bs-table-color:
        #dce6f1;

    --bs-table-border-color:
        #223249;

    --bs-table-striped-bg:
        rgba(255,255,255,.018);

    --bs-table-hover-bg:
        rgba(255,255,255,.035);

    color:
        #dce6f1 !important;
}


html[data-color-mode="dark"] .table thead th {

    background:
        #162438 !important;

    color:
        #aebed0 !important;

    border-color:
        #223249 !important;
}


html[data-color-mode="dark"] .table tbody td {

    border-color:
        #223249 !important;
}


/* ------------------------------------------------------------
   FORMULARIOS
------------------------------------------------------------ */

html[data-color-mode="dark"] .form-control,
html[data-color-mode="dark"] .form-select,
html[data-color-mode="dark"] .input-group-text {

    background:
        #0e1929 !important;

    border-color:
        #293b53 !important;

    color:
        #e4edf6 !important;
}


html[data-color-mode="dark"] .form-control::placeholder {

    color:
        #667d95 !important;
}


html[data-color-mode="dark"] .form-control:focus,
html[data-color-mode="dark"] .form-select:focus {

    background:
        #101d2f !important;

    border-color:
        var(--primary) !important;

    color:
        #ffffff !important;

    box-shadow:
        0 0 0 .2rem
        color-mix(
            in srgb,
            var(--primary) 14%,
            transparent
        ) !important;
}


html[data-color-mode="dark"] .form-control:disabled,
html[data-color-mode="dark"] .form-control[readonly] {

    background:
        #172335 !important;

    color:
        #8da0b5 !important;
}


/* ------------------------------------------------------------
   DROPDOWN DE CUENTA
------------------------------------------------------------ */

html[data-color-mode="dark"] .account-dropdown-title,
html[data-color-mode="dark"] .account-dropdown-description {

    color:
        #8fa2b8 !important;
}


html[data-color-mode="dark"] .account-dropdown-label {

    color:
        #e8eef6 !important;
}


html[data-color-mode="dark"] .account-dropdown-item:hover,
html[data-color-mode="dark"] .account-dropdown-item:focus {

    background:
        #18263a !important;
}


/* Logout conserva rojo */

html[data-color-mode="dark"]
.account-dropdown-icon.logout {

    background:
        rgba(220,38,38,.12) !important;

    border-color:
        rgba(248,113,113,.22) !important;

    color:
        #f87171 !important;
}


html[data-color-mode="dark"]
.account-dropdown-logout
.account-dropdown-label {

    color:
        #f87171 !important;
}


/* ------------------------------------------------------------
   MODALES
------------------------------------------------------------ */

html[data-color-mode="dark"] .modal-backdrop.show {

    opacity:
        .65;
}


/* ------------------------------------------------------------
   BOTONES SECUNDARIOS
------------------------------------------------------------ */

html[data-color-mode="dark"] .btn-light,
html[data-color-mode="dark"] .btn-outline-secondary {

    background:
        #162438 !important;

    border-color:
        #2a3c54 !important;

    color:
        #dce6f1 !important;
}


html[data-color-mode="dark"] .btn-light:hover,
html[data-color-mode="dark"] .btn-outline-secondary:hover {

    background:
        #1d2d43 !important;
}


/* ------------------------------------------------------------
   PAGINACION
------------------------------------------------------------ */

html[data-color-mode="dark"] .pagination .page-link {

    background:
        #111c2d !important;

    border-color:
        #26384f !important;

    color:
        #aabbd0 !important;
}


html[data-color-mode="dark"] .pagination .page-link:hover {

    background:
        #19283c !important;

    color:
        var(--primary) !important;
}


/* ------------------------------------------------------------
   BOTON DE TEMA EN OSCURO
------------------------------------------------------------ */

html[data-color-mode="dark"]
.system-theme-toggle {

    background:
        #162438;

    border-color:
        #2a3c54;

    color:
        #f5b942;

    box-shadow:
        0 5px 15px
        rgba(0,0,0,.15);
}


html[data-color-mode="dark"]
.system-theme-toggle:hover {

    color:
        #ffc857;

    background:
        #1a2b41;
}


/* ------------------------------------------------------------
   TRANSICION CIRCULAR
------------------------------------------------------------ */

::view-transition-old(root),
::view-transition-new(root) {

    animation:
        none;

    mix-blend-mode:
        normal;
}


html.theme-transition-to-dark
::view-transition-new(root),

html.theme-transition-to-light
::view-transition-old(root) {

    z-index:
        999999;
}


html.theme-transition-to-dark
::view-transition-old(root),

html.theme-transition-to-light
::view-transition-new(root) {

    z-index:
        1;
}


@media (prefers-reduced-motion: reduce) {

    ::view-transition-old(root),
    ::view-transition-new(root) {

        animation:
            none !important;
    }

}


/* ------------------------------------------------------------
   RESPONSIVE
------------------------------------------------------------ */

@media (max-width: 575.98px) {

    .system-theme-toggle {

        width: 38px;
        height: 38px;

        flex-basis: 38px;

        margin-right: 2px;

        border-radius: 10px;
    }

}
</style>

<script id="theme-preload-el-capitan">
    (function () {

        try {

            const savedMode =
                localStorage.getItem('restaurant-color-mode');

            if (savedMode === 'dark') {

                document.documentElement
                    .setAttribute(
                        'data-color-mode',
                        'dark'
                    );
            }

        } catch (error) {
            /* localStorage no disponible */
        }

    })();
</script>

<style>
/* ============================================================
   AJUSTE POSICION BOTON TEMA
============================================================ */

/*
 * El contenedor derecho del navbar mantiene
 * todos sus controles en una sola línea.
 */

.top-navbar .dropdown {
    display: flex !important;
    align-items: center !important;
    gap: 8px;
}


/*
 * Botón de tema:
 * mismo nivel visual que la cuenta.
 */

.system-theme-toggle {

    width: 40px !important;
    height: 40px !important;

    flex: 0 0 40px !important;

    margin:
        0 2px 0 0 !important;

    align-self:
        center !important;

    border-radius:
        11px !important;

    position:
        relative;

    top:
        0 !important;
}


/*
 * Usuario
 */

.header-user-control {

    margin:
        0 !important;

    align-self:
        center !important;
}


/*
 * En pantallas pequeñas mantenemos todo centrado.
 */

@media (max-width: 575.98px) {

    .top-navbar .dropdown {
        gap: 5px;
    }

    .system-theme-toggle {

        width: 37px !important;
        height: 37px !important;

        flex-basis:
            37px !important;

        margin:
            0 !important;
    }

}
</style>

<style>
/* ============================================================
   DARK MODE GLOBAL - SISTEMA COMPLETO
   Capa visual compartida por todos los modulos
============================================================ */

html[data-color-mode="dark"] {

    color-scheme: dark;

    --dm-page: #091321;
    --dm-page-2: #0b1727;

    --dm-surface: #111f32;
    --dm-surface-2: #15253a;
    --dm-surface-3: #192b42;
    --dm-surface-hover: #1b3048;

    --dm-border: #2b4058;
    --dm-border-soft: #22354a;

    --dm-title: #f4f8fc;
    --dm-text: #d9e5f0;
    --dm-muted: #9eb3c8;
    --dm-placeholder: #728aa2;

    --dm-input: #0e1a2b;

    --dm-success: #32d583;
    --dm-danger: #fb7185;
    --dm-warning: #f7b955;
    --dm-info: #38bdf8;

    --bs-body-bg: var(--dm-page);
    --bs-body-color: var(--dm-text);
    --bs-border-color: var(--dm-border);
}


/* ============================================================
   PAGINA / CONTENIDO
============================================================ */

html[data-color-mode="dark"],
html[data-color-mode="dark"] body {

    background:
        var(--dm-page) !important;

    color:
        var(--dm-text) !important;
}


html[data-color-mode="dark"] main,
html[data-color-mode="dark"] .main-content,
html[data-color-mode="dark"] .content-wrapper {

    color:
        var(--dm-text);
}


/* ============================================================
   TEXTOS
============================================================ */

html[data-color-mode="dark"] h1,
html[data-color-mode="dark"] h2,
html[data-color-mode="dark"] h3,
html[data-color-mode="dark"] h4,
html[data-color-mode="dark"] h5,
html[data-color-mode="dark"] h6 {

    color:
        var(--dm-title) !important;
}


html[data-color-mode="dark"] .text-dark,
html[data-color-mode="dark"] .text-body,
html[data-color-mode="dark"] .text-body-emphasis {

    color:
        var(--dm-title) !important;
}


html[data-color-mode="dark"] .text-muted,
html[data-color-mode="dark"] .text-secondary {

    color:
        var(--dm-muted) !important;

    opacity:
        1 !important;
}


html[data-color-mode="dark"] label,
html[data-color-mode="dark"] .form-label {

    color:
        #c8d7e5 !important;
}


html[data-color-mode="dark"] small {

    color:
        inherit;
}


/* ============================================================
   TARJETAS
============================================================ */

html[data-color-mode="dark"] .card {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;

    border-color:
        var(--dm-border) !important;

    box-shadow:
        0 10px 26px rgba(0,0,0,.13);
}


html[data-color-mode="dark"] .card-header {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-title) !important;

    border-bottom-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] .card-footer {

    background:
        var(--dm-surface) !important;

    border-top-color:
        var(--dm-border) !important;

    color:
        var(--dm-muted) !important;
}


html[data-color-mode="dark"] .card-title {

    color:
        var(--dm-title) !important;
}


html[data-color-mode="dark"] .card-text {

    color:
        var(--dm-text);
}


/* ============================================================
   FONDOS BOOTSTRAP CLAROS
============================================================ */

html[data-color-mode="dark"] .bg-white,
html[data-color-mode="dark"] .bg-light {

    background-color:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;
}


html[data-color-mode="dark"] .bg-body,
html[data-color-mode="dark"] .bg-body-tertiary,
html[data-color-mode="dark"] .bg-body-secondary {

    background-color:
        var(--dm-surface-2) !important;

    color:
        var(--dm-text) !important;
}


/* ============================================================
   BORDES
============================================================ */

html[data-color-mode="dark"] .border,
html[data-color-mode="dark"] .border-top,
html[data-color-mode="dark"] .border-bottom,
html[data-color-mode="dark"] .border-start,
html[data-color-mode="dark"] .border-end {

    border-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] hr {

    border-color:
        var(--dm-border) !important;

    opacity:
        1;
}


/* ============================================================
   TABLAS
============================================================ */

html[data-color-mode="dark"] .table {

    --bs-table-bg: transparent;
    --bs-table-color: var(--dm-text);
    --bs-table-border-color: var(--dm-border-soft);

    --bs-table-striped-bg: rgba(255,255,255,.025);
    --bs-table-striped-color: var(--dm-text);

    --bs-table-hover-bg: rgba(255,255,255,.045);
    --bs-table-hover-color: #ffffff;

    color:
        var(--dm-text) !important;
}


html[data-color-mode="dark"] .table > :not(caption) > * > * {

    background-color:
        transparent;

    color:
        inherit;

    border-color:
        var(--dm-border-soft) !important;
}


html[data-color-mode="dark"] .table thead th,
html[data-color-mode="dark"] .table-light > * > * {

    background:
        var(--dm-surface-2) !important;

    color:
        #b9cadd !important;

    border-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] .table tbody tr:hover > * {

    background:
        rgba(255,255,255,.035) !important;
}


html[data-color-mode="dark"] .table-responsive {

    color:
        var(--dm-text);
}


/* ============================================================
   FORMULARIOS
============================================================ */

html[data-color-mode="dark"] .form-control,
html[data-color-mode="dark"] .form-select,
html[data-color-mode="dark"] .input-group-text {

    background-color:
        var(--dm-input) !important;

    color:
        #e9f1f8 !important;

    border-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] .form-control::placeholder {

    color:
        var(--dm-placeholder) !important;

    opacity:
        1;
}


html[data-color-mode="dark"] .form-control:focus,
html[data-color-mode="dark"] .form-select:focus {

    background:
        #101e30 !important;

    color:
        #ffffff !important;

    border-color:
        var(--primary) !important;

    box-shadow:
        0 0 0 .2rem
        color-mix(
            in srgb,
            var(--primary) 18%,
            transparent
        ) !important;
}


html[data-color-mode="dark"] .form-control:disabled,
html[data-color-mode="dark"] .form-control[readonly],
html[data-color-mode="dark"] .form-select:disabled {

    background:
        #172538 !important;

    color:
        #91a6bb !important;

    opacity:
        1;
}


html[data-color-mode="dark"] .form-check-input {

    background-color:
        #0d1929;

    border-color:
        #526a83;
}


html[data-color-mode="dark"] .form-check-input:checked {

    background-color:
        var(--primary);

    border-color:
        var(--primary);
}


html[data-color-mode="dark"] .form-text {

    color:
        var(--dm-muted) !important;
}


/* ============================================================
   SELECT / OPTIONS
============================================================ */

html[data-color-mode="dark"] select option {

    background:
        #111f32;

    color:
        #e7eef6;
}


/* ============================================================
   MODALES
============================================================ */

html[data-color-mode="dark"] .modal-content {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;

    border:
        1px solid var(--dm-border) !important;

    box-shadow:
        0 24px 60px rgba(0,0,0,.38) !important;
}


html[data-color-mode="dark"] .modal-header {

    background:
        var(--dm-surface) !important;

    border-bottom-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] .modal-body {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;
}


html[data-color-mode="dark"] .modal-footer {

    background:
        var(--dm-surface) !important;

    border-top-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] .modal-title {

    color:
        var(--dm-title) !important;
}


html[data-color-mode="dark"] .btn-close {

    filter:
        invert(1) grayscale(100%) brightness(180%);
}


/* ============================================================
   DROPDOWNS
============================================================ */

html[data-color-mode="dark"] .dropdown-menu {

    --bs-dropdown-bg: var(--dm-surface);
    --bs-dropdown-color: var(--dm-text);
    --bs-dropdown-border-color: var(--dm-border);
    --bs-dropdown-link-color: var(--dm-text);
    --bs-dropdown-link-hover-color: #ffffff;
    --bs-dropdown-link-hover-bg: var(--dm-surface-3);

    background:
        var(--dm-surface) !important;

    border-color:
        var(--dm-border) !important;

    box-shadow:
        0 16px 38px rgba(0,0,0,.28) !important;
}


html[data-color-mode="dark"] .dropdown-item {

    color:
        var(--dm-text);
}


html[data-color-mode="dark"] .dropdown-item:hover,
html[data-color-mode="dark"] .dropdown-item:focus {

    background:
        var(--dm-surface-3) !important;

    color:
        #ffffff !important;
}


html[data-color-mode="dark"] .dropdown-divider {

    border-color:
        var(--dm-border) !important;
}


/* ============================================================
   LIST GROUP
============================================================ */

html[data-color-mode="dark"] .list-group {

    --bs-list-group-bg: transparent;
    --bs-list-group-color: var(--dm-text);
    --bs-list-group-border-color: var(--dm-border);
}


html[data-color-mode="dark"] .list-group-item {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;

    border-color:
        var(--dm-border) !important;
}


/* ============================================================
   TABS / PILLS
============================================================ */

html[data-color-mode="dark"] .nav-tabs {

    border-bottom-color:
        var(--dm-border);
}


html[data-color-mode="dark"] .nav-tabs .nav-link {

    color:
        var(--dm-muted);

    border-color:
        transparent;
}


html[data-color-mode="dark"] .nav-tabs .nav-link:hover {

    color:
        #ffffff;

    border-color:
        var(--dm-border);
}


html[data-color-mode="dark"] .nav-tabs .nav-link.active {

    background:
        var(--dm-surface) !important;

    color:
        #ffffff !important;

    border-color:
        var(--dm-border)
        var(--dm-border)
        var(--dm-surface) !important;
}


/* ============================================================
   ACCORDION
============================================================ */

html[data-color-mode="dark"] .accordion {

    --bs-accordion-bg: var(--dm-surface);
    --bs-accordion-color: var(--dm-text);
    --bs-accordion-border-color: var(--dm-border);
    --bs-accordion-btn-bg: var(--dm-surface);
    --bs-accordion-btn-color: var(--dm-title);
    --bs-accordion-active-bg: var(--dm-surface-2);
    --bs-accordion-active-color: #ffffff;
}


html[data-color-mode="dark"] .accordion-item,
html[data-color-mode="dark"] .accordion-button {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;

    border-color:
        var(--dm-border) !important;
}


/* ============================================================
   PAGINACION
============================================================ */

html[data-color-mode="dark"] .pagination {

    --bs-pagination-bg: var(--dm-surface);
    --bs-pagination-color: var(--dm-muted);
    --bs-pagination-border-color: var(--dm-border);

    --bs-pagination-hover-bg: var(--dm-surface-3);
    --bs-pagination-hover-color: #ffffff;
    --bs-pagination-hover-border-color: var(--dm-border);

    --bs-pagination-disabled-bg: #0d1827;
    --bs-pagination-disabled-color: #60778e;
    --bs-pagination-disabled-border-color: var(--dm-border);
}


html[data-color-mode="dark"] .page-link {

    background:
        var(--dm-surface) !important;

    border-color:
        var(--dm-border) !important;

    color:
        var(--dm-muted) !important;
}


html[data-color-mode="dark"] .page-item.active .page-link {

    background:
        var(--primary) !important;

    border-color:
        var(--primary) !important;

    color:
        #ffffff !important;
}


/* ============================================================
   BOTONES CLAROS / SECUNDARIOS
============================================================ */

html[data-color-mode="dark"] .btn-light,
html[data-color-mode="dark"] .btn-outline-secondary {

    background:
        var(--dm-surface-2) !important;

    border-color:
        var(--dm-border) !important;

    color:
        #dce7f2 !important;
}


html[data-color-mode="dark"] .btn-light:hover,
html[data-color-mode="dark"] .btn-outline-secondary:hover {

    background:
        var(--dm-surface-3) !important;

    border-color:
        #4b6680 !important;

    color:
        #ffffff !important;
}


/* ============================================================
   BADGES
   No alterar success/danger/warning/info.
============================================================ */

html[data-color-mode="dark"] .badge.text-bg-light,
html[data-color-mode="dark"] .badge.bg-light {

    background:
        #20344a !important;

    color:
        #dce8f3 !important;
}


/* ============================================================
   ALERTAS BOOTSTRAP
============================================================ */

html[data-color-mode="dark"] .alert {

    border:
        1px solid transparent !important;

    box-shadow:
        0 10px 28px rgba(0,0,0,.18) !important;
}


html[data-color-mode="dark"] .alert-success {

    background:
        #102b26 !important;

    border-color:
        #225c4b !important;

    color:
        #9ce7c6 !important;
}


html[data-color-mode="dark"] .alert-danger {

    background:
        #321c26 !important;

    border-color:
        #6d3445 !important;

    color:
        #f5a7b5 !important;
}


html[data-color-mode="dark"] .alert-warning {

    background:
        #322819 !important;

    border-color:
        #66502a !important;

    color:
        #f3d28c !important;
}


html[data-color-mode="dark"] .alert-info {

    background:
        #122a38 !important;

    border-color:
        #28576d !important;

    color:
        #9bdcf4 !important;
}


/* ============================================================
   NOTIFICACIONES / TOASTS
============================================================ */

html[data-color-mode="dark"] .toast {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;

    border:
        1px solid var(--dm-border) !important;

    box-shadow:
        0 16px 38px rgba(0,0,0,.30) !important;
}


html[data-color-mode="dark"] .toast-header {

    background:
        var(--dm-surface-2) !important;

    color:
        var(--dm-title) !important;

    border-bottom-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] .toast-body {

    color:
        var(--dm-text) !important;
}


/* Notificaciones personalizadas */

html[data-color-mode="dark"]
[class*="notification"],
html[data-color-mode="dark"]
[class*="toast"] {

    border-color:
        var(--dm-border);
}


/* ============================================================
   POPOVERS / TOOLTIPS
============================================================ */

html[data-color-mode="dark"] .popover {

    --bs-popover-bg: var(--dm-surface);
    --bs-popover-border-color: var(--dm-border);
    --bs-popover-body-color: var(--dm-text);
    --bs-popover-header-bg: var(--dm-surface-2);
    --bs-popover-header-color: var(--dm-title);
}


html[data-color-mode="dark"] .tooltip {

    --bs-tooltip-bg: #24364c;
    --bs-tooltip-color: #ffffff;
}


/* ============================================================
   OFFCANVAS
============================================================ */

html[data-color-mode="dark"] .offcanvas {

    background:
        var(--dm-surface) !important;

    color:
        var(--dm-text) !important;

    border-color:
        var(--dm-border) !important;
}


html[data-color-mode="dark"] .offcanvas-header {

    border-color:
        var(--dm-border) !important;
}


/* ============================================================
   PROGRESS
============================================================ */

html[data-color-mode="dark"] .progress {

    background:
        #26394e !important;
}


/* ============================================================
   BREADCRUMB
============================================================ */

html[data-color-mode="dark"] .breadcrumb-item {

    color:
        var(--dm-muted);
}


html[data-color-mode="dark"] .breadcrumb-item.active {

    color:
        var(--dm-text);
}


html[data-color-mode="dark"]
.breadcrumb-item + .breadcrumb-item::before {

    color:
        #657d95;
}


/* ============================================================
   EMPTY STATES / FILTROS / CONTENEDORES COMUNES
============================================================ */

html[data-color-mode="dark"]
[class*="empty-state"],
html[data-color-mode="dark"]
[class*="filter-card"],
html[data-color-mode="dark"]
[class*="filters-card"],
html[data-color-mode="dark"]
[class*="summary-card"],
html[data-color-mode="dark"]
[class*="content-card"] {

    background-color:
        var(--dm-surface) !important;

    border-color:
        var(--dm-border) !important;

    color:
        var(--dm-text) !important;
}


/* ============================================================
   SOMBRAS
============================================================ */

html[data-color-mode="dark"] .shadow,
html[data-color-mode="dark"] .shadow-sm,
html[data-color-mode="dark"] .shadow-lg {

    --bs-box-shadow:
        0 12px 30px rgba(0,0,0,.20);

    --bs-box-shadow-sm:
        0 7px 18px rgba(0,0,0,.16);

    --bs-box-shadow-lg:
        0 22px 55px rgba(0,0,0,.30);
}


/* ============================================================
   LINKS
============================================================ */

html[data-color-mode="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item) {

    text-decoration-color:
        color-mix(
            in srgb,
            var(--primary) 45%,
            transparent
        );
}


/* ============================================================
   TRANSICION
============================================================ */

html[data-color-mode="dark"] .card,
html[data-color-mode="dark"] .modal-content,
html[data-color-mode="dark"] .dropdown-menu,
html[data-color-mode="dark"] .form-control,
html[data-color-mode="dark"] .form-select,
html[data-color-mode="dark"] .list-group-item,
html[data-color-mode="dark"] .table,
html[data-color-mode="dark"] .alert,
html[data-color-mode="dark"] .toast {

    transition:
        background-color .20s ease,
        border-color .20s ease,
        color .20s ease;
}


/* ============================================================
   SCROLLBAR
============================================================ */

html[data-color-mode="dark"] {

    scrollbar-color:
        #405b76 #0b1625;
}


html[data-color-mode="dark"] ::-webkit-scrollbar {

    width:
        10px;

    height:
        10px;
}


html[data-color-mode="dark"] ::-webkit-scrollbar-track {

    background:
        #0b1625;
}


html[data-color-mode="dark"] ::-webkit-scrollbar-thumb {

    background:
        #405b76;

    border:
        2px solid #0b1625;

    border-radius:
        20px;
}


html[data-color-mode="dark"] ::-webkit-scrollbar-thumb:hover {

    background:
        #526e89;
}

</style>

<style>
/* DARK MODE DEFINITIVO - VARIABLES Y POS */

html[data-color-mode="dark"] {
    /* Variables usadas por los módulos */
    --card-bg: #111f32 !important;
    --light-bg: #15263a !important;
    --text-main: #e8f0f7 !important;
    --text-muted: #9fb4c9 !important;
    --border-soft: #304860 !important;

    /* Variables propias del POS */
    --pos-card: #111f32 !important;
    --pos-bg: #091321 !important;
    --pos-surface: #15263a !important;
    --pos-text: #e8f0f7 !important;
    --pos-muted: #9fb4c9 !important;
    --pos-border: #304860 !important;
}

/* =========================================================
   CAJA
========================================================= */

html[data-color-mode="dark"] .history-kpi,
html[data-color-mode="dark"] .turn-kpi,
html[data-color-mode="dark"] .cash-kpi {
    background: #111f32 !important;
    border-color: #304860 !important;
    color: #e8f0f7 !important;
}

html[data-color-mode="dark"] .history-kpi-value,
html[data-color-mode="dark"] .turn-kpi-value,
html[data-color-mode="dark"] .cash-kpi-value {
    color: #f5f8fc !important;
}

html[data-color-mode="dark"] .history-kpi-label,
html[data-color-mode="dark"] .turn-kpi-label,
html[data-color-mode="dark"] .cash-kpi-label {
    color: #9fb4c9 !important;
}

/* =========================================================
   POS - CONTENEDOR DE PRODUCTOS
========================================================= */

html[data-color-mode="dark"] #products-container {
    background: #091321 !important;
    color: #e8f0f7 !important;
}

html[data-color-mode="dark"] #products-container > div:first-child {
    background: #091321 !important;
}

/* =========================================================
   POS - TARJETAS DE PRODUCTOS
========================================================= */

html[data-color-mode="dark"] .pos-order-page .pos-product-card,
html[data-color-mode="dark"] .pos-product-card {
    background: #111f32 !important;
    color: #e8f0f7 !important;
}

html[data-color-mode="dark"] .pos-order-page .pos-product-card:hover,
html[data-color-mode="dark"] .pos-product-card:hover {
    background: #17283d !important;
}

/* =========================================================
   POS - PANELES
========================================================= */

html[data-color-mode="dark"] .pos-order-page {
    --pos-card: #111f32 !important;
    --pos-bg: #091321 !important;
    --pos-text: #e8f0f7 !important;
    --pos-muted: #9fb4c9 !important;
}

html[data-color-mode="dark"] .pos-order-page .card,
html[data-color-mode="dark"] .pos-order-page .modal-content {
    background-color: #111f32 !important;
    color: #e8f0f7 !important;
    border-color: #304860 !important;
}

/* panel claro del resumen del pedido */
html[data-color-mode="dark"]
.pos-order-page [style*="background: #f8f7ff"],
html[data-color-mode="dark"]
.pos-order-page [style*="background:#f8f7ff"] {
    background: #15263a !important;
    border-color: #304860 !important;
}

/* =========================================================
   TEXTO NEGRO DE LOS MODULOS
========================================================= */

html[data-color-mode="dark"] .text-dark,
html[data-color-mode="dark"] [style*="color:#000"],
html[data-color-mode="dark"] [style*="color: #000"],
html[data-color-mode="dark"] [style*="color:#111827"],
html[data-color-mode="dark"] [style*="color: #111827"] {
    color: #f5f8fc !important;
}

/* =========================================================
   TARJETAS BOOTSTRAP
========================================================= */

html[data-color-mode="dark"] .card {
    --bs-card-bg: #111f32;
    --bs-card-color: #e8f0f7;
    --bs-card-border-color: #304860;

    background-color: #111f32 !important;
    color: #e8f0f7 !important;
    border-color: #304860 !important;
}

/* =========================================================
   TABLAS
========================================================= */

html[data-color-mode="dark"] .table {
    --bs-table-bg: #111f32;
    --bs-table-color: #dce7f2;
    --bs-table-border-color: #304860;
    --bs-table-hover-bg: #17283d;
    --bs-table-hover-color: #ffffff;
}

/* =========================================================
   FORMULARIOS
========================================================= */

html[data-color-mode="dark"] .form-control,
html[data-color-mode="dark"] .form-select,
html[data-color-mode="dark"] .input-group-text {
    background-color: #0e1b2c !important;
    color: #e8f0f7 !important;
    border-color: #304860 !important;
}

/* =========================================================
   MODALES / DROPDOWN
========================================================= */

html[data-color-mode="dark"] .modal-content,
html[data-color-mode="dark"] .dropdown-menu {
    background: #111f32 !important;
    color: #e8f0f7 !important;
    border-color: #304860 !important;
}

/* =========================================================
   FONDOS BLANCOS ANTIGUOS DENTRO DEL CONTENIDO
========================================================= */

html[data-color-mode="dark"] main .bg-white,
html[data-color-mode="dark"] main .bg-light {
    background-color: #111f32 !important;
    color: #e8f0f7 !important;
}

</style>

<style>
/* DARK MODE - TEXTO E ICONOS BLANCOS */

/* Variable principal */
html[data-color-mode="dark"] {
    --text-main: #ffffff !important;
}

/* Títulos */
html[data-color-mode="dark"] h1,
html[data-color-mode="dark"] h2,
html[data-color-mode="dark"] h3,
html[data-color-mode="dark"] h4,
html[data-color-mode="dark"] h5,
html[data-color-mode="dark"] h6 {
    color: #ffffff !important;
}

/* Textos que originalmente son oscuros */
html[data-color-mode="dark"] .text-dark,
html[data-color-mode="dark"] .text-body,
html[data-color-mode="dark"] .text-body-emphasis,
html[data-color-mode="dark"] .fw-bold.text-dark,
html[data-color-mode="dark"] .fw-semibold.text-dark {
    color: #ffffff !important;
}

/* Elementos que ya migramos a la variable del sistema */
html[data-color-mode="dark"] [style*="color:var(--text-main)"],
html[data-color-mode="dark"] [style*="color: var(--text-main)"] {
    color: #ffffff !important;
}

/* Negros antiguos que todavía pudieran existir */
html[data-color-mode="dark"] [style*="color:#000"],
html[data-color-mode="dark"] [style*="color: #000"],
html[data-color-mode="dark"] [style*="color:#111827"],
html[data-color-mode="dark"] [style*="color: #111827"],
html[data-color-mode="dark"] [style*="color:#212529"],
html[data-color-mode="dark"] [style*="color: #212529"],
html[data-color-mode="dark"] [style*="color:#1f2937"],
html[data-color-mode="dark"] [style*="color: #1f2937"] {
    color: #ffffff !important;
}

/* Iconos que originalmente acompañan textos oscuros */
html[data-color-mode="dark"] .text-dark i,
html[data-color-mode="dark"] i.text-dark,
html[data-color-mode="dark"] i[style*="color:var(--text-main)"],
html[data-color-mode="dark"] i[style*="color: var(--text-main)"],
html[data-color-mode="dark"] i[style*="color:#000"],
html[data-color-mode="dark"] i[style*="color: #000"] {
    color: #ffffff !important;
}

/* Labels y nombres principales */
html[data-color-mode="dark"] label:not(.text-success):not(.text-danger):not(.text-warning),
html[data-color-mode="dark"] .form-label {
    color: #ffffff;
}

/*
 * NO modificamos:
 * text-success
 * text-danger
 * text-warning
 * text-primary
 * text-info
 *
 * porque representan estados y acciones.
 */

</style>

<style>
/* FIX DEFINITIVO TEXT MAIN DARK */

html[data-color-mode="dark"],
html[data-color-mode="dark"] body,
html[data-color-mode="dark"] .main-content {
    --text-main: #ffffff !important;
    --bs-body-color: #ffffff !important;
}

/* Todo elemento que utiliza el color principal */
html[data-color-mode="dark"] [style*="var(--text-main)"] {
    color: #ffffff !important;
}

/* Clases oscuras de Bootstrap */
html[data-color-mode="dark"] .text-dark,
html[data-color-mode="dark"] .text-body,
html[data-color-mode="dark"] .text-body-emphasis {
    color: #ffffff !important;
}

</style>

<style>
/* DARK MODE - TARJETAS PROFESIONALES */

/* Tarjeta general del sistema */
html[data-color-mode="dark"] .card,
html[data-color-mode="dark"] .history-kpi,
html[data-color-mode="dark"] .turn-kpi,
html[data-color-mode="dark"] .cash-kpi,
html[data-color-mode="dark"] .sales-kpi,
html[data-color-mode="dark"] .billing-kpi,
html[data-color-mode="dark"] .reservation-kpi,
html[data-color-mode="dark"] .report-kpi,
html[data-color-mode="dark"] .client-stat-card {
    background: #111f32 !important;
    color: #ffffff !important;
    border-color: #2b4058 !important;
}

/* Tarjetas que usan bg-white / bg-light */
html[data-color-mode="dark"] .bg-white,
html[data-color-mode="dark"] .bg-light {
    background-color: #111f32 !important;
}

/* Textos principales dentro de tarjetas */
html[data-color-mode="dark"] .card h1,
html[data-color-mode="dark"] .card h2,
html[data-color-mode="dark"] .card h3,
html[data-color-mode="dark"] .card h4,
html[data-color-mode="dark"] .card h5,
html[data-color-mode="dark"] .card h6,
html[data-color-mode="dark"] .card .fw-bold,
html[data-color-mode="dark"] .card .fw-semibold,
html[data-color-mode="dark"] .cash-kpi-value,
html[data-color-mode="dark"] .sales-kpi-value,
html[data-color-mode="dark"] .history-kpi-value,
html[data-color-mode="dark"] .turn-kpi-value {
    color: #ffffff !important;
}

/* Textos secundarios */
html[data-color-mode="dark"] .card .text-muted,
html[data-color-mode="dark"] .cash-kpi-label,
html[data-color-mode="dark"] .history-kpi-label,
html[data-color-mode="dark"] .turn-kpi-label {
    color: #a9bdd0 !important;
}

/* Hover */
html[data-color-mode="dark"] .card:hover {
    border-color: #3a5672;
}

/* Mantener colores de iconos y badges.
   Solo oscurecemos sus fondos claros. */
html[data-color-mode="dark"] .cash-kpi-icon,
html[data-color-mode="dark"] .history-kpi-icon,
html[data-color-mode="dark"] .turn-kpi-icon,
html[data-color-mode="dark"] .sales-kpi-icon,
html[data-color-mode="dark"] .billing-kpi-icon,
html[data-color-mode="dark"] .reservation-kpi-icon {
    background-color:
        color-mix(in srgb, currentColor 12%, #15263a) !important;
}

/* Paneles internos */
html[data-color-mode="dark"] .card-header,
html[data-color-mode="dark"] .card-footer {
    background-color: #15263a !important;
    border-color: #2b4058 !important;
}

/* Listas dentro de tarjetas */
html[data-color-mode="dark"] .list-group-item {
    background-color: #111f32 !important;
    color: #e7eef6 !important;
    border-color: #2b4058 !important;
}

</style>

<style>
/* =========================================================
   DARK MODE - COMPATIBILIDAD COMPLETA UI
========================================================= */

html[data-color-mode="dark"] {
    --dm-bg: #091321;
    --dm-card: #111f32;
    --dm-card-2: #15263a;
    --dm-hover: #1a3048;
    --dm-border: #304860;
    --dm-text: #ffffff;
    --dm-muted: #9fb4c9;
}

/* ---------- SUPERFICIES CLARAS ---------- */

html[data-color-mode="dark"] .bg-white,
html[data-color-mode="dark"] .bg-light,
html[data-color-mode="dark"] .bg-body,
html[data-color-mode="dark"] .bg-body-tertiary {
    background-color: var(--dm-card-2) !important;
    color: var(--dm-text) !important;
}

/* ---------- CARDS / PANELES ---------- */

html[data-color-mode="dark"] .card,
html[data-color-mode="dark"] .card-body,
html[data-color-mode="dark"] .card-header,
html[data-color-mode="dark"] .card-footer {
    background-color: var(--dm-card) !important;
    color: var(--dm-text) !important;
    border-color: var(--dm-border) !important;
}

/* ---------- BOTONES CLAROS ---------- */

html[data-color-mode="dark"] .btn-light,
html[data-color-mode="dark"] .btn-white,
html[data-color-mode="dark"] .btn-outline-dark {
    background: var(--dm-card-2) !important;
    border-color: var(--dm-border) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .btn-light:hover,
html[data-color-mode="dark"] .btn-white:hover,
html[data-color-mode="dark"] .btn-outline-dark:hover {
    background: var(--dm-hover) !important;
    border-color: #45627f !important;
    color: #ffffff !important;
}

/* ---------- BOTONES CON TEXTO OSCURO ---------- */

html[data-color-mode="dark"] button.text-dark,
html[data-color-mode="dark"] a.text-dark,
html[data-color-mode="dark"] .btn.text-dark {
    color: #ffffff !important;
}

/* ---------- INPUTS ---------- */

html[data-color-mode="dark"] .form-control,
html[data-color-mode="dark"] .form-select,
html[data-color-mode="dark"] .input-group-text {
    background: #0e1b2c !important;
    border-color: var(--dm-border) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .form-control::placeholder {
    color: #7890a8 !important;
}

html[data-color-mode="dark"] .form-control:disabled,
html[data-color-mode="dark"] .form-control[readonly] {
    background: #15263a !important;
    color: #b8c8d8 !important;
}

/* ---------- TABLAS ---------- */

html[data-color-mode="dark"] .table {
    --bs-table-bg: var(--dm-card);
    --bs-table-color: #e7eef6;
    --bs-table-border-color: var(--dm-border);
    --bs-table-hover-bg: var(--dm-hover);
    --bs-table-hover-color: #ffffff;
}

html[data-color-mode="dark"] .table-light {
    --bs-table-bg: #15263a;
    --bs-table-color: #ffffff;
}

/* ---------- LISTAS ---------- */

html[data-color-mode="dark"] .list-group-item {
    background: var(--dm-card) !important;
    border-color: var(--dm-border) !important;
    color: #ffffff !important;
}

/* ---------- TABS ---------- */

html[data-color-mode="dark"] .nav-tabs {
    border-color: var(--dm-border) !important;
}

html[data-color-mode="dark"] .nav-tabs .nav-link {
    color: var(--dm-muted) !important;
    border-color: transparent !important;
}

html[data-color-mode="dark"] .nav-tabs .nav-link:hover {
    background: var(--dm-card-2) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .nav-tabs .nav-link.active {
    background: var(--dm-card-2) !important;
    color: #ffffff !important;
    border-color: var(--dm-border) !important;
}

/* ---------- PILLS ---------- */

html[data-color-mode="dark"] .nav-pills .nav-link:not(.active) {
    color: #b5c6d7 !important;
}

html[data-color-mode="dark"] .nav-pills .nav-link:not(.active):hover {
    background: var(--dm-card-2) !important;
    color: #ffffff !important;
}

/* ---------- DROPDOWN ---------- */

html[data-color-mode="dark"] .dropdown-menu {
    background: var(--dm-card) !important;
    border-color: var(--dm-border) !important;
}

html[data-color-mode="dark"] .dropdown-item {
    color: #e7eef6 !important;
}

html[data-color-mode="dark"] .dropdown-item:hover,
html[data-color-mode="dark"] .dropdown-item:focus {
    background: var(--dm-hover) !important;
    color: #ffffff !important;
}

/* ---------- MODALES ---------- */

html[data-color-mode="dark"] .modal-content {
    background: var(--dm-card) !important;
    border-color: var(--dm-border) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .modal-header,
html[data-color-mode="dark"] .modal-footer {
    border-color: var(--dm-border) !important;
}

/* X del modal */
html[data-color-mode="dark"] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* ---------- ACCORDION ---------- */

html[data-color-mode="dark"] .accordion-item,
html[data-color-mode="dark"] .accordion-button {
    background: var(--dm-card) !important;
    border-color: var(--dm-border) !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .accordion-button:not(.collapsed) {
    background: var(--dm-card-2) !important;
}

/* ---------- PAGINACION ---------- */

html[data-color-mode="dark"] .page-link {
    background: var(--dm-card) !important;
    border-color: var(--dm-border) !important;
    color: #dbe7f2 !important;
}

html[data-color-mode="dark"] .page-link:hover {
    background: var(--dm-hover) !important;
    color: #ffffff !important;
}

/* ---------- BORDES CLAROS ---------- */

html[data-color-mode="dark"] .border,
html[data-color-mode="dark"] .border-top,
html[data-color-mode="dark"] .border-bottom,
html[data-color-mode="dark"] .border-start,
html[data-color-mode="dark"] .border-end {
    border-color: var(--dm-border) !important;
}

/* ---------- TEXTOS ---------- */

html[data-color-mode="dark"] .text-dark,
html[data-color-mode="dark"] .text-body,
html[data-color-mode="dark"] .text-body-emphasis {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .text-muted,
html[data-color-mode="dark"] small.text-muted {
    color: var(--dm-muted) !important;
}

/* ---------- SOMBRAS ---------- */

html[data-color-mode="dark"] .shadow,
html[data-color-mode="dark"] .shadow-sm {
    box-shadow: 0 8px 22px rgba(0,0,0,.18) !important;
}

/* ---------- COMPONENTES CON FONDO BLANCO INLINE ---------- */
/* Solo dentro del sistema autenticado */

html[data-color-mode="dark"] .main-content [style*="background:#fff"],
html[data-color-mode="dark"] .main-content [style*="background: #fff"],
html[data-color-mode="dark"] .main-content [style*="background:#ffffff"],
html[data-color-mode="dark"] .main-content [style*="background: #ffffff"] {
    background: var(--dm-card-2) !important;
    color: #ffffff !important;
}

/* ---------- FONDOS CLAROS CONOCIDOS ---------- */

html[data-color-mode="dark"] .main-content [style*="background:#f8f9fa"],
html[data-color-mode="dark"] .main-content [style*="background: #f8f9fa"],
html[data-color-mode="dark"] .main-content [style*="background:#f8fafc"],
html[data-color-mode="dark"] .main-content [style*="background: #f8fafc"],
html[data-color-mode="dark"] .main-content [style*="background:#f8f7ff"],
html[data-color-mode="dark"] .main-content [style*="background: #f8f7ff"] {
    background: var(--dm-card-2) !important;
    color: #ffffff !important;
}

/* ---------- BADGES NEUTRALES ---------- */

html[data-color-mode="dark"] .badge.bg-light,
html[data-color-mode="dark"] .badge.text-bg-light {
    background: #20344b !important;
    color: #ffffff !important;
}

/* ---------- HR ---------- */

html[data-color-mode="dark"] hr {
    border-color: var(--dm-border) !important;
    opacity: 1;
}

</style>

<style>
/* DARK MODE - ELIMINAR FONDOS PASTEL */

/* Naranjas / crema */
html[data-color-mode="dark"] .main-content [style*="#fff7ed"],
html[data-color-mode="dark"] .main-content [style*="#fffbeb"],
html[data-color-mode="dark"] .main-content [style*="#fff8f0"],
html[data-color-mode="dark"] .main-content [style*="#fff4e6"],

/* Rojos claros */
html[data-color-mode="dark"] .main-content [style*="#fff1f2"],
html[data-color-mode="dark"] .main-content [style*="#fef2f2"],

/* Verdes claros */
html[data-color-mode="dark"] .main-content [style*="#f0fdf4"],
html[data-color-mode="dark"] .main-content [style*="#ecfdf5"],

/* Azules claros */
html[data-color-mode="dark"] .main-content [style*="#eff6ff"],
html[data-color-mode="dark"] .main-content [style*="#eaf2ff"],

/* Morados claros */
html[data-color-mode="dark"] .main-content [style*="#faf5ff"],
html[data-color-mode="dark"] .main-content [style*="#f5e9f8"],

/* Turquesas claros */
html[data-color-mode="dark"] .main-content [style*="#f0fdfa"],
html[data-color-mode="dark"] .main-content [style*="#e8f8f3"],

/* Grises / blancos claros */
html[data-color-mode="dark"] .main-content [style*="#f8fafc"],
html[data-color-mode="dark"] .main-content [style*="#f9fafb"],
html[data-color-mode="dark"] .main-content [style*="#f8f9fa"],
html[data-color-mode="dark"] .main-content [style*="#ffffff"],
html[data-color-mode="dark"] .main-content [style*="#fff"] {
    background-color: #15263a !important;
    background: #15263a !important;
    color: #ffffff !important;
}

/* Títulos y cantidades dentro de esos paneles */
html[data-color-mode="dark"] .main-content [style*="#fff7ed"] .fw-bold,
html[data-color-mode="dark"] .main-content [style*="#fffbeb"] .fw-bold,
html[data-color-mode="dark"] .main-content [style*="#fff1f2"] .fw-bold,
html[data-color-mode="dark"] .main-content [style*="#f0fdf4"] .fw-bold,
html[data-color-mode="dark"] .main-content [style*="#eff6ff"] .fw-bold,
html[data-color-mode="dark"] .main-content [style*="#faf5ff"] .fw-bold {
    color: #ffffff !important;
}

/* Fondos claros definidos mediante variables */
html[data-color-mode="dark"] .main-content [style*="var(--light-bg)"] {
    background-color: #15263a !important;
}

/* Bootstrap */
html[data-color-mode="dark"] .main-content .bg-white,
html[data-color-mode="dark"] .main-content .bg-light {
    background: #15263a !important;
    color: #ffffff !important;
}

</style>

<style>
/* DARK MODE - CONTROLES Y COMPONENTES FINALES */

/* =========================================================
   1. ICONOS Y TITULOS PRINCIPALES
   ========================================================= */

html[data-color-mode="dark"] .main-content h1,
html[data-color-mode="dark"] .main-content h2,
html[data-color-mode="dark"] .main-content h3,
html[data-color-mode="dark"] .main-content h4,
html[data-color-mode="dark"] .main-content h5,
html[data-color-mode="dark"] .main-content h6 {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .main-content h1 > i,
html[data-color-mode="dark"] .main-content h2 > i,
html[data-color-mode="dark"] .main-content h3 > i,
html[data-color-mode="dark"] .main-content h4 > i,
html[data-color-mode="dark"] .main-content h5 > i,
html[data-color-mode="dark"] .main-content h6 > i {
    color: #ffffff !important;
}

html[data-color-mode="dark"] .main-content [style*="color:var(--text-main)"] {
    color: #ffffff !important;
}


/* =========================================================
   2. TOTAL VENDIDO / BLOQUES DE RESUMEN
   ========================================================= */

html[data-color-mode="dark"] .payment-total {
    background: #15263a !important;
    border: 1px solid #304860 !important;
    color: #ffffff !important;
}

html[data-color-mode="dark"] .payment-total span {
    color: #a9bdd0 !important;
}

html[data-color-mode="dark"] .payment-total strong {
    color: #ffffff !important;
}


/* =========================================================
   3. INPUTS DE ARCHIVO
   ========================================================= */

html[data-color-mode="dark"] input[type="file"],
html[data-color-mode="dark"] .form-control[type="file"] {
    background-color: #111f32 !important;
    color: #dce7f2 !important;
    border-color: #30445b !important;
}

html[data-color-mode="dark"] input[type="file"]::file-selector-button,
html[data-color-mode="dark"] .form-control[type="file"]::file-selector-button {
    background: #1c3047 !important;
    color: #ffffff !important;
    border: 0 !important;
    border-right: 1px solid #3a5068 !important;
    padding: .55rem .85rem;
    margin-right: .75rem;
}

html[data-color-mode="dark"] input[type="file"]::file-selector-button:hover,
html[data-color-mode="dark"] .form-control[type="file"]::file-selector-button:hover {
    background: #263d56 !important;
    color: #ffffff !important;
}


/* Compatibilidad Chrome / Edge */

html[data-color-mode="dark"] input[type="file"]::-webkit-file-upload-button {
    background: #1c3047 !important;
    color: #ffffff !important;
    border: 0 !important;
    border-right: 1px solid #3a5068 !important;
}


/* =========================================================
   4. SELECT / OPTION
   ========================================================= */

html[data-color-mode="dark"] .form-select,
html[data-color-mode="dark"] select {
    background-color: #111f32 !important;
    color: #ffffff !important;
    border-color: #30445b !important;
}

html[data-color-mode="dark"] select option {
    background: #111f32 !important;
    color: #ffffff !important;
}


/* =========================================================
   5. INPUTS / TEXTAREA
   ========================================================= */

html[data-color-mode="dark"] .form-control,
html[data-color-mode="dark"] textarea {
    background-color: #111f32 !important;
    color: #ffffff !important;
    border-color: #30445b !important;
}

html[data-color-mode="dark"] .form-control::placeholder,
html[data-color-mode="dark"] textarea::placeholder {
    color: #8297ac !important;
}


/* =========================================================
   6. INPUT GROUP
   ========================================================= */

html[data-color-mode="dark"] .input-group-text {
    background: #1a2b40 !important;
    color: #dce7f2 !important;
    border-color: #30445b !important;
}


/* =========================================================
   7. BOTONES CLAROS
   ========================================================= */

html[data-color-mode="dark"] .btn-light,
html[data-color-mode="dark"] .btn-outline-secondary {
    background: #182a3f !important;
    color: #ffffff !important;
    border-color: #344b63 !important;
}

html[data-color-mode="dark"] .btn-light:hover,
html[data-color-mode="dark"] .btn-outline-secondary:hover {
    background: #243a52 !important;
    color: #ffffff !important;
    border-color: #49627c !important;
}


/* =========================================================
   8. CAMPOS DESHABILITADOS / READONLY
   ========================================================= */

html[data-color-mode="dark"] .form-control:disabled,
html[data-color-mode="dark"] .form-control[readonly],
html[data-color-mode="dark"] .form-select:disabled {
    background: #0e1b2b !important;
    color: #8fa5bb !important;
    border-color: #293e54 !important;
    opacity: 1 !important;
}

</style>

<style>
/* DARK MODE - KPI UNIFICADO ESTILO RESERVAS */

/* =========================================================
   TARJETA BASE
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-panel,
    .billing-kpi,
    .cash-kpi,
    .history-kpi,
    .turn-kpi,
    .client-stat-card,
    .report-kpi,
    .reservation-kpi,
    .sales-kpi
) {
    background: #111f32 !important;
    border: 1px solid #29445f !important;
    color: #ffffff !important;
    box-shadow: none !important;
}


/* =========================================================
   TITULOS
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-label,
    .billing-kpi-label,
    .cash-kpi-label,
    .history-kpi-label,
    .turn-kpi-label,
    .client-stat-label,
    .report-kpi-label,
    .reservation-kpi-label,
    .sales-kpi-label
) {
    color: #82a4c6 !important;
}


/* =========================================================
   VALORES
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-value,
    .billing-kpi-value,
    .cash-kpi-value,
    .history-kpi-value,
    .turn-kpi-value,
    .client-stat-value,
    .report-kpi-value,
    .reservation-kpi-value,
    .sales-kpi-value
) {
    color: #ffffff !important;
}


/* =========================================================
   DESCRIPCIONES
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-sub,
    .reservation-kpi-sub,
    .sales-kpi-sub
) {
    color: #6889aa !important;
}


/* =========================================================
   ICONOS
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
) {
    box-shadow: none !important;
}


/* =========================================================
   BADGES KPI
   ========================================================= */




/* =========================================================
   DASHBOARD
   ========================================================= */

/* Ventas */
html[data-color-mode="dark"] .kpi-sales {
}

html[data-color-mode="dark"] .kpi-sales .kpi-icon-wrap {
    background: rgba(59,130,246,.16) !important;
    color: #60a5fa !important;
}

/* Mesas */
html[data-color-mode="dark"] .kpi-tables {
}

html[data-color-mode="dark"] .kpi-tables .kpi-icon-wrap {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
}

/* Mes */
html[data-color-mode="dark"] .kpi-month {
}

html[data-color-mode="dark"] .kpi-month .kpi-icon-wrap {
    background: rgba(168,85,247,.15) !important;
    color: #c084fc !important;
}

/* Stock */
html[data-color-mode="dark"] .kpi-stock {
}

html[data-color-mode="dark"] .kpi-stock .kpi-icon-wrap {
    background: rgba(245,158,11,.15) !important;
    color: #fbbf24 !important;
}


/* =========================================================
   RESERVAS
   Se conserva exactamente su identidad actual.
   ========================================================= */

html[data-color-mode="dark"] .reservation-kpi-today {
}

html[data-color-mode="dark"] .reservation-kpi-pending {
}

html[data-color-mode="dark"] .reservation-kpi-confirmed {
}

html[data-color-mode="dark"] .reservation-kpi-people {
}


/* =========================================================
   CAJA Y MOVIMIENTOS
   ========================================================= */

html[data-color-mode="dark"] .sales-kpi {
}


/* =========================================================
   CIERRE DE CAJA
   ========================================================= */

html[data-color-mode="dark"] .cash-kpi.initial {
}

html[data-color-mode="dark"] .cash-kpi.cash {
}

html[data-color-mode="dark"] .cash-kpi.digital {
}

html[data-color-mode="dark"] .cash-kpi.expense {
}


/* =========================================================
   HISTORIAL / TURNO
   ========================================================= */

html[data-color-mode="dark"] .history-kpi,
html[data-color-mode="dark"] .turn-kpi {
}


/* =========================================================
   CLIENTES
   ========================================================= */

html[data-color-mode="dark"] .client-stat-card.spent {
}

html[data-color-mode="dark"] .client-stat-card.visits {
}

html[data-color-mode="dark"] .client-stat-card.favorite {
}


/* =========================================================
   REPORTES
   ========================================================= */

html[data-color-mode="dark"] .report-kpi.kpi-income {
}

html[data-color-mode="dark"] .report-kpi.kpi-orders {
}

html[data-color-mode="dark"] .report-kpi.kpi-waiter {
}

html[data-color-mode="dark"] .report-kpi.kpi-product {
}


/* =========================================================
   FACTURACION
   ========================================================= */

html[data-color-mode="dark"] .billing-kpi {
}


/* =========================================================
   HOVER SUAVE
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-panel,
    .billing-kpi,
    .cash-kpi,
    .history-kpi,
    .turn-kpi,
    .client-stat-card,
    .report-kpi,
    .reservation-kpi,
    .sales-kpi
):hover {
    background: #13243a !important;
    border-top-color: #36536f !important;
    border-right-color: #36536f !important;
    border-bottom-color: #36536f !important;
}

</style>

<style>
/* DARK MODE - BADGES KPI DEFINITIVOS */

/* =========================================================
   BASE DE TODOS LOS BADGES KPI
   ========================================================= */




/* =========================================================
   RESERVAS
   ========================================================= */

/* HOY - azul */
html[data-color-mode="dark"]
.reservation-kpi-today .reservation-kpi-badge {
    background: rgba(59, 130, 246, .14) !important;
    color: #93c5fd !important;
    border-color: rgba(96, 165, 250, .40) !important;
}

/* PEND. - amarillo/naranja */
html[data-color-mode="dark"]
.reservation-kpi-pending .reservation-kpi-badge {
    background: rgba(245, 158, 11, .14) !important;
    color: #fcd34d !important;
    border-color: rgba(251, 191, 36, .40) !important;
}

/* OK - verde */
html[data-color-mode="dark"]
.reservation-kpi-confirmed .reservation-kpi-badge {
    background: rgba(34, 197, 94, .14) !important;
    color: #86efac !important;
    border-color: rgba(74, 222, 128, .40) !important;
}

/* HOY / Personas - morado */
html[data-color-mode="dark"]
.reservation-kpi-people .reservation-kpi-badge {
    background: rgba(168, 85, 247, .14) !important;
    color: #d8b4fe !important;
    border-color: rgba(192, 132, 252, .40) !important;
}


/* =========================================================
   DASHBOARD
   ========================================================= */

html[data-color-mode="dark"]
.kpi-sales .kpi-badge {
    background: rgba(59, 130, 246, .14) !important;
    color: #93c5fd !important;
    border-color: rgba(96, 165, 250, .40) !important;
}

html[data-color-mode="dark"]
.kpi-tables .kpi-badge {
    background: rgba(34, 197, 94, .14) !important;
    color: #86efac !important;
    border-color: rgba(74, 222, 128, .40) !important;
}

html[data-color-mode="dark"]
.kpi-month .kpi-badge {
    background: rgba(168, 85, 247, .14) !important;
    color: #d8b4fe !important;
    border-color: rgba(192, 132, 252, .40) !important;
}

html[data-color-mode="dark"]
.kpi-stock .kpi-badge {
    background: rgba(245, 158, 11, .14) !important;
    color: #fcd34d !important;
    border-color: rgba(251, 191, 36, .40) !important;
}


/* =========================================================
   CAJA Y MOVIMIENTOS
   Los colores propios de cada tarjeta se conservan.
   Evita cualquier badge blanco/pastel.
   ========================================================= */

html[data-color-mode="dark"]
.sales-kpi .sales-kpi-badge {
    background-color: #17283d !important;
    color: #c7d7e7 !important;
    border-color: #36516d !important;
}


/* Si el badge ya tiene un color semantico mediante texto,
   respetamos ese color pero oscurecemos el fondo. */

html[data-color-mode="dark"]
.sales-kpi .sales-kpi-badge.text-success {
    background: rgba(34, 197, 94, .14) !important;
    color: #86efac !important;
    border-color: rgba(74, 222, 128, .40) !important;
}

html[data-color-mode="dark"]
.sales-kpi .sales-kpi-badge.text-danger {
    background: rgba(239, 68, 68, .14) !important;
    color: #fca5a5 !important;
    border-color: rgba(248, 113, 113, .40) !important;
}

html[data-color-mode="dark"]
.sales-kpi .sales-kpi-badge.text-warning {
    background: rgba(245, 158, 11, .14) !important;
    color: #fcd34d !important;
    border-color: rgba(251, 191, 36, .40) !important;
}

html[data-color-mode="dark"]
.sales-kpi .sales-kpi-badge.text-primary,
html[data-color-mode="dark"]
.sales-kpi .sales-kpi-badge.text-info {
    background: rgba(59, 130, 246, .14) !important;
    color: #93c5fd !important;
    border-color: rgba(96, 165, 250, .40) !important;
}


/* =========================================================
   PROTECCION CONTRA BG-WHITE / BG-LIGHT
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-badge,
    .reservation-kpi-badge,
    .sales-kpi-badge
).bg-white,

html[data-color-mode="dark"] :is(
    .kpi-badge,
    .reservation-kpi-badge,
    .sales-kpi-badge
).bg-light {
    background-color: #17283d !important;
    color: #c7d7e7 !important;
    border-color: #36516d !important;
}

</style>

<style>
/* DARK MODE - ICONOS KPI DEFINITIVOS */

/* Base: elimina cualquier fondo blanco/pastel */
html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
) {
    background: #17283d !important;
    border-color: transparent !important;
    box-shadow: none !important;
}


/* =========================================================
   COLORES SEMANTICOS
   Si el icono ya tiene color, el fondo queda oscuro teñido.
   ========================================================= */

/* AZUL */
html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
):has(.text-primary),

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
).text-primary {
    background: rgba(59,130,246,.16) !important;
}


/* VERDE */
html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
):has(.text-success),

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
).text-success {
    background: rgba(34,197,94,.15) !important;
}


/* NARANJA / AMARILLO */
html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
):has(.text-warning),

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
).text-warning {
    background: rgba(245,158,11,.16) !important;
}


/* ROJO */
html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
):has(.text-danger),

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
).text-danger {
    background: rgba(239,68,68,.15) !important;
}


/* INFO / CELESTE */
html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
):has(.text-info),

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
).text-info {
    background: rgba(6,182,212,.15) !important;
}


/* =========================================================
   RESERVAS - REFERENCIA VISUAL
   ========================================================= */

html[data-color-mode="dark"]
.reservation-kpi-today .reservation-kpi-icon {
    background: rgba(59,130,246,.16) !important;
}

html[data-color-mode="dark"]
.reservation-kpi-pending .reservation-kpi-icon {
    background: rgba(245,158,11,.16) !important;
}

html[data-color-mode="dark"]
.reservation-kpi-confirmed .reservation-kpi-icon {
    background: rgba(34,197,94,.15) !important;
}

html[data-color-mode="dark"]
.reservation-kpi-people .reservation-kpi-icon {
    background: rgba(168,85,247,.16) !important;
}


/* =========================================================
   PROTECCION CONTRA FONDOS CLAROS
   ========================================================= */

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
).bg-white,

html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .client-stat-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon
).bg-light {
    background: #17283d !important;
}

</style>

<style>
/* DARK MODE - COMPONENTES SEMANTICOS DEL SISTEMA */

/* ==========================================================
   PALETA BASE
   ========================================================== */

html[data-color-mode="dark"] {
    --dm-surface: #111f32;
    --dm-surface-2: #17283d;
    --dm-border: #29445f;

    --dm-blue-bg: rgba(59,130,246,.14);
    --dm-blue-text: #93c5fd;
    --dm-blue-border: rgba(96,165,250,.34);

    --dm-green-bg: rgba(34,197,94,.14);
    --dm-green-text: #86efac;
    --dm-green-border: rgba(74,222,128,.34);

    --dm-orange-bg: rgba(245,158,11,.14);
    --dm-orange-text: #fcd34d;
    --dm-orange-border: rgba(251,191,36,.34);

    --dm-red-bg: rgba(239,68,68,.14);
    --dm-red-text: #fca5a5;
    --dm-red-border: rgba(248,113,113,.34);

    --dm-purple-bg: rgba(168,85,247,.14);
    --dm-purple-text: #d8b4fe;
    --dm-purple-border: rgba(192,132,252,.34);

    --dm-teal-bg: rgba(20,184,166,.14);
    --dm-teal-text: #99f6e4;
    --dm-teal-border: rgba(45,212,191,.34);
}


/* ==========================================================
   FONDOS PASTEL VERDES
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    [style*="background:#f0fdf4"],
    [style*="background: #f0fdf4"],
    [style*="background:#ecfdf5"],
    [style*="background: #ecfdf5"],
    [style*="background:#dcfce7"],
    [style*="background: #dcfce7"]
) {
    background: var(--dm-green-bg) !important;
    color: var(--dm-green-text) !important;
    border-color: var(--dm-green-border) !important;
}


/* ==========================================================
   FONDOS PASTEL ROJOS
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    [style*="background:#fff1f2"],
    [style*="background: #fff1f2"],
    [style*="background:#fef2f2"],
    [style*="background: #fef2f2"],
    [style*="background:#fee2e2"],
    [style*="background: #fee2e2"]
) {
    background: var(--dm-red-bg) !important;
    color: var(--dm-red-text) !important;
    border-color: var(--dm-red-border) !important;
}


/* ==========================================================
   FONDOS PASTEL AZULES
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    [style*="background:#eff6ff"],
    [style*="background: #eff6ff"],
    [style*="background:#dbeafe"],
    [style*="background: #dbeafe"],
    [style*="background:#eaf2ff"],
    [style*="background: #eaf2ff"]
) {
    background: var(--dm-blue-bg) !important;
    color: var(--dm-blue-text) !important;
    border-color: var(--dm-blue-border) !important;
}


/* ==========================================================
   FONDOS PASTEL NARANJA / AMARILLO
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    [style*="background:#fff7ed"],
    [style*="background: #fff7ed"],
    [style*="background:#fffbeb"],
    [style*="background: #fffbeb"],
    [style*="background:#fef3c7"],
    [style*="background: #fef3c7"]
) {
    background: var(--dm-orange-bg) !important;
    color: var(--dm-orange-text) !important;
    border-color: var(--dm-orange-border) !important;
}


/* ==========================================================
   FONDOS PASTEL MORADOS
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    [style*="background:#faf5ff"],
    [style*="background: #faf5ff"],
    [style*="background:#f5e9f8"],
    [style*="background: #f5e9f8"]
) {
    background: var(--dm-purple-bg) !important;
    color: var(--dm-purple-text) !important;
    border-color: var(--dm-purple-border) !important;
}


/* ==========================================================
   FONDOS PASTEL TURQUESA
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    [style*="background:#f0fdfa"],
    [style*="background: #f0fdfa"],
    [style*="background:#e8f8f3"],
    [style*="background: #e8f8f3"]
) {
    background: var(--dm-teal-bg) !important;
    color: var(--dm-teal-text) !important;
    border-color: var(--dm-teal-border) !important;
}


/* ==========================================================
   FACTURACION - ICONOS KPI
   ========================================================== */

html[data-color-mode="dark"]
.billing-kpi.accepted .billing-kpi-icon {
    background: var(--dm-green-bg) !important;
    color: var(--dm-green-text) !important;
    border-color: var(--dm-green-border) !important;
}

html[data-color-mode="dark"]
.billing-kpi.observed .billing-kpi-icon {
    background: var(--dm-orange-bg) !important;
    color: var(--dm-orange-text) !important;
    border-color: var(--dm-orange-border) !important;
}

html[data-color-mode="dark"]
.billing-kpi.error .billing-kpi-icon,

html[data-color-mode="dark"]
.billing-kpi.rejected .billing-kpi-icon {
    background: var(--dm-red-bg) !important;
    color: var(--dm-red-text) !important;
    border-color: var(--dm-red-border) !important;
}


/* ==========================================================
   RESERVAS - ESTADOS
   ========================================================== */

html[data-color-mode="dark"] .status-confirmed {
    background: var(--dm-green-bg) !important;
    color: var(--dm-green-text) !important;
    border-color: var(--dm-green-border) !important;
}

html[data-color-mode="dark"] .status-pending {
    background: var(--dm-orange-bg) !important;
    color: var(--dm-orange-text) !important;
    border-color: var(--dm-orange-border) !important;
}

html[data-color-mode="dark"] .status-cancelled {
    background: var(--dm-red-bg) !important;
    color: var(--dm-red-text) !important;
    border-color: var(--dm-red-border) !important;
}


/* ==========================================================
   NOTAS DE CREDITO - ESTADOS
   ========================================================== */

html[data-color-mode="dark"] .credit-status.accepted {
    background: var(--dm-green-bg) !important;
    color: var(--dm-green-text) !important;
    border-color: var(--dm-green-border) !important;
}

html[data-color-mode="dark"] .credit-status.observed {
    background: var(--dm-orange-bg) !important;
    color: var(--dm-orange-text) !important;
    border-color: var(--dm-orange-border) !important;
}

html[data-color-mode="dark"] .credit-status.rejected,
html[data-color-mode="dark"] .credit-status.error {
    background: var(--dm-red-bg) !important;
    color: var(--dm-red-text) !important;
    border-color: var(--dm-red-border) !important;
}


/* ==========================================================
   BARRA / COCINA - LEYENDA
   ========================================================== */

html[data-color-mode="dark"] .main-content
.badge.bg-white.text-dark.border {
    background: var(--dm-surface-2) !important;
    color: #dce8f4 !important;
    border-color: var(--dm-border) !important;
}


/* ==========================================================
   BOTONES / ICONOS CUADRADOS CLAROS
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    .btn-light,
    .btn-white
) {
    background: var(--dm-surface-2) !important;
    color: #ffffff !important;
    border-color: var(--dm-border) !important;
}

html[data-color-mode="dark"] .main-content
:is(
    .btn-light,
    .btn-white
):hover {
    background: #20364e !important;
    color: #ffffff !important;
    border-color: #3b5874 !important;
}


/* ==========================================================
   PANELES BOOTSTRAP CLAROS
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    .bg-white,
    .bg-light
) {
    background-color: var(--dm-surface-2) !important;
}


/* ==========================================================
   AVISOS INFORMATIVOS
   ========================================================== */

html[data-color-mode="dark"] .main-content .alert-info {
    background: var(--dm-blue-bg) !important;
    color: #bfdbfe !important;
    border-color: var(--dm-blue-border) !important;
}

html[data-color-mode="dark"] .main-content .alert-success {
    background: var(--dm-green-bg) !important;
    color: var(--dm-green-text) !important;
    border-color: var(--dm-green-border) !important;
}

html[data-color-mode="dark"] .main-content .alert-warning {
    background: var(--dm-orange-bg) !important;
    color: var(--dm-orange-text) !important;
    border-color: var(--dm-orange-border) !important;
}

html[data-color-mode="dark"] .main-content .alert-danger {
    background: var(--dm-red-bg) !important;
    color: var(--dm-red-text) !important;
    border-color: var(--dm-red-border) !important;
}


/* ==========================================================
   PANELES INTERNOS / COLLAPSE
   Ej.: Respuesta de SUNAT
   ========================================================== */

html[data-color-mode="dark"] .main-content
:is(
    .collapse .card,
    .collapsing .card,
    .accordion-body
) {
    background: var(--dm-surface) !important;
    color: #dce8f4 !important;
    border-color: var(--dm-border) !important;
}


/* ==========================================================
   TABLAS DENTRO DE PANELES OSCUROS
   ========================================================== */

html[data-color-mode="dark"] .main-content .table-light > * > * {
    background: var(--dm-surface-2) !important;
    color: #dce8f4 !important;
    border-color: var(--dm-border) !important;
}


/* ==========================================================
   TEXTO OSCURO BOOTSTRAP
   Solo dentro del contenido principal.
   ========================================================== */

html[data-color-mode="dark"] .main-content .text-dark {
    color: #ffffff !important;
}

</style>

<style>
/* DARK MODE - CONTENEDORES DE ICONOS COMPLETOS */

/*
 * Fondo base para contenedores de iconos.
 * Solo actúa en modo oscuro.
 */
html[data-color-mode="dark"] :is(
    .kpi-icon-wrap,
    .billing-kpi-icon,
    .billing-card-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .payment-icon,
    .client-stat-icon,
    .client-detail-icon,
    .client-history-title-icon,
    .client-modal-heading-icon,
    .report-kpi-icon,
    .reservation-kpi-icon,
    .reservation-stat-icon,
    .sales-kpi-icon,
    .cash-open-header-icon,
    .cash-info-icon,
    .category-modal-icon,
    .category-empty-icon,
    .credit-document-icon,
    .credit-empty-icon,
    .cn-document-icon,
    .cn-header-icon,
    .credit-header-icon,
    .delivery-create-title-icon,
    .ds-title-icon,
    .goal-icon-wrap
) {
    background: #17283d !important;
    border-color: #29445f !important;
    box-shadow: none !important;
}


/* =========================================================
   DASHBOARD
   ========================================================= */

html[data-color-mode="dark"] .kpi-sales .kpi-icon-wrap {
    background: rgba(59,130,246,.15) !important;
    color: #60a5fa !important;
}

html[data-color-mode="dark"] .kpi-tables .kpi-icon-wrap {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .kpi-month .kpi-icon-wrap {
    background: rgba(168,85,247,.15) !important;
    color: #c084fc !important;
}

html[data-color-mode="dark"] .kpi-stock .kpi-icon-wrap {
    background: rgba(245,158,11,.15) !important;
    color: #fbbf24 !important;
}


/* =========================================================
   FACTURACION
   ========================================================= */

html[data-color-mode="dark"] .billing-kpi.accepted .billing-kpi-icon {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .billing-kpi.observed .billing-kpi-icon {
    background: rgba(245,158,11,.15) !important;
    color: #fbbf24 !important;
}

html[data-color-mode="dark"] .billing-kpi.pending .billing-kpi-icon {
    background: rgba(148,163,184,.13) !important;
    color: #cbd5e1 !important;
}

html[data-color-mode="dark"] :is(
    .billing-kpi.error,
    .billing-kpi.rejected
) .billing-kpi-icon {
    background: rgba(239,68,68,.14) !important;
    color: #f87171 !important;
}


/* =========================================================
   CIERRE DE CAJA
   ========================================================= */

html[data-color-mode="dark"] .cash-kpi.initial .cash-kpi-icon {
    background: rgba(59,130,246,.15) !important;
    color: #60a5fa !important;
}

html[data-color-mode="dark"] .cash-kpi.cash .cash-kpi-icon {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .cash-kpi.digital .cash-kpi-icon {
    background: rgba(168,85,247,.15) !important;
    color: #c084fc !important;
}

html[data-color-mode="dark"] .cash-kpi.expense .cash-kpi-icon {
    background: rgba(245,158,11,.15) !important;
    color: #fb923c !important;
}


/* =========================================================
   PERFIL DEL CLIENTE
   ========================================================= */

html[data-color-mode="dark"] .client-stat-card.spent .client-stat-icon {
    background: rgba(59,130,246,.15) !important;
    color: #60a5fa !important;
}

html[data-color-mode="dark"] .client-stat-card.visits .client-stat-icon {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] .client-stat-card.favorite .client-stat-icon {
    background: rgba(245,158,11,.15) !important;
    color: #fbbf24 !important;
}


/* =========================================================
   RESERVAS
   ========================================================= */

html[data-color-mode="dark"]
.reservations-page .row.g-3.mb-4 > div:nth-child(1) .reservation-stat-icon,
html[data-color-mode="dark"] .reservation-kpi-today .reservation-kpi-icon {
    background: rgba(59,130,246,.15) !important;
    color: #60a5fa !important;
}

html[data-color-mode="dark"]
.reservations-page .row.g-3.mb-4 > div:nth-child(2) .reservation-stat-icon,
html[data-color-mode="dark"] .reservation-kpi-pending .reservation-kpi-icon {
    background: rgba(245,158,11,.15) !important;
    color: #fbbf24 !important;
}

html[data-color-mode="dark"]
.reservations-page .row.g-3.mb-4 > div:nth-child(3) .reservation-stat-icon,
html[data-color-mode="dark"] .reservation-kpi-confirmed .reservation-kpi-icon {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"]
.reservations-page .row.g-3.mb-4 > div:nth-child(4) .reservation-stat-icon,
html[data-color-mode="dark"] .reservation-kpi-people .reservation-kpi-icon {
    background: rgba(168,85,247,.15) !important;
    color: #c084fc !important;
}


/* =========================================================
   COLORES BOOTSTRAP DENTRO DE OTROS ICONOS
   ========================================================= */

html[data-color-mode="dark"] :is(
    .billing-card-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .payment-icon,
    .report-kpi-icon,
    .sales-kpi-icon,
    .cash-open-header-icon,
    .cash-info-icon,
    .category-modal-icon,
    .client-modal-heading-icon,
    .credit-document-icon,
    .cn-document-icon,
    .delivery-create-title-icon
):has(.text-primary) {
    background: rgba(59,130,246,.15) !important;
    color: #60a5fa !important;
}

html[data-color-mode="dark"] :is(
    .billing-card-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .payment-icon,
    .report-kpi-icon,
    .sales-kpi-icon,
    .cash-open-header-icon,
    .cash-info-icon
):has(.text-success) {
    background: rgba(34,197,94,.14) !important;
    color: #4ade80 !important;
}

html[data-color-mode="dark"] :is(
    .billing-card-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .payment-icon,
    .report-kpi-icon,
    .sales-kpi-icon,
    .cash-open-header-icon,
    .cash-info-icon
):has(.text-warning) {
    background: rgba(245,158,11,.15) !important;
    color: #fbbf24 !important;
}

html[data-color-mode="dark"] :is(
    .billing-card-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .payment-icon,
    .report-kpi-icon,
    .sales-kpi-icon
):has(.text-danger) {
    background: rgba(239,68,68,.14) !important;
    color: #f87171 !important;
}

html[data-color-mode="dark"] :is(
    .billing-card-icon,
    .history-kpi-icon,
    .turn-kpi-icon,
    .payment-icon,
    .report-kpi-icon,
    .sales-kpi-icon
):has(.text-info) {
    background: rgba(6,182,212,.14) !important;
    color: #67e8f9 !important;
}


/* El icono siempre hereda el color semántico del contenedor */
html[data-color-mode="dark"] :is(
    .billing-card-icon,
    .payment-icon,
    .client-stat-icon,
    .client-detail-icon,
    .report-kpi-icon,
    .reservation-stat-icon,
    .reservation-kpi-icon,
    .sales-kpi-icon,
    .cash-kpi-icon,
    .history-kpi-icon,
    .turn-kpi-icon
) > i {
    color: inherit !important;
}

</style>








<style>
/* DARK MODE - FRANJAS KPI SIN BLANCO */

/* DASHBOARD */
html[data-color-mode="dark"] .kpi-sales::before {
    background: linear-gradient(
        180deg,
        var(--primary, #ff8c00) 0%,
        rgba(255, 140, 0, .65) 48%,
        rgba(255, 140, 0, .08) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-tables::before {
    background: linear-gradient(
        180deg,
        #2563eb 0%,
        rgba(37, 99, 235, .65) 48%,
        rgba(37, 99, 235, .08) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-month::before {
    background: linear-gradient(
        180deg,
        #9333ea 0%,
        rgba(147, 51, 234, .65) 48%,
        rgba(147, 51, 234, .08) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-stock.kpi-alert::before {
    background: linear-gradient(
        180deg,
        #dc2626 0%,
        rgba(220, 38, 38, .65) 48%,
        rgba(220, 38, 38, .08) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-stock.kpi-ok::before {
    background: linear-gradient(
        180deg,
        #16a34a 0%,
        rgba(22, 163, 74, .65) 48%,
        rgba(22, 163, 74, .08) 100%
    ) !important;
}


/* FACTURACION */
html[data-color-mode="dark"] .billing-kpi.accepted::before {
    background: linear-gradient(180deg, #16a34a, #22c55e, rgba(22,163,74,.08)) !important;
}

html[data-color-mode="dark"] .billing-kpi.observed::before {
    background: linear-gradient(180deg, #f59e0b, #fbbf24, rgba(245,158,11,.08)) !important;
}

html[data-color-mode="dark"] .billing-kpi.pending::before {
    background: linear-gradient(180deg, #64748b, #94a3b8, rgba(100,116,139,.08)) !important;
}

html[data-color-mode="dark"] .billing-kpi.error::before,
html[data-color-mode="dark"] .billing-kpi.rejected::before {
    background: linear-gradient(180deg, #dc2626, #ef4444, rgba(220,38,38,.08)) !important;
}


/* CIERRE DE CAJA */
html[data-color-mode="dark"] .cash-kpi.initial::before {
    background: linear-gradient(180deg, #2563eb, #3b82f6, rgba(37,99,235,.08)) !important;
}

html[data-color-mode="dark"] .cash-kpi.cash::before {
    background: linear-gradient(180deg, #16a34a, #22c55e, rgba(22,163,74,.08)) !important;
}

html[data-color-mode="dark"] .cash-kpi.digital::before {
    background: linear-gradient(180deg, #7c3aed, #8b5cf6, rgba(124,58,237,.08)) !important;
}

html[data-color-mode="dark"] .cash-kpi.expense::before {
    background: linear-gradient(180deg, #ea580c, #f97316, rgba(234,88,12,.08)) !important;
}


/* RESERVAS */
html[data-color-mode="dark"] .reservation-kpi-today::before {
    background: linear-gradient(180deg, #2563eb, #3b82f6, rgba(37,99,235,.08)) !important;
}

html[data-color-mode="dark"] .reservation-kpi-pending::before {
    background: linear-gradient(180deg, #f59e0b, #fbbf24, rgba(245,158,11,.08)) !important;
}

html[data-color-mode="dark"] .reservation-kpi-confirmed::before {
    background: linear-gradient(180deg, #16a34a, #22c55e, rgba(22,163,74,.08)) !important;
}

html[data-color-mode="dark"] .reservation-kpi-people::before {
    background: linear-gradient(180deg, #9333ea, #a855f7, rgba(147,51,234,.08)) !important;
}


/* VENTAS / MOVIMIENTOS */
html[data-color-mode="dark"] .sales-kpi::before {
    background: linear-gradient(
        180deg,
        var(--kpi-color) 0%,
        color-mix(in srgb, var(--kpi-color) 75%, #132338) 58%,
        color-mix(in srgb, var(--kpi-color) 8%, #132338) 100%
    ) !important;
}

</style>





<style>
/* DARK MODE - FRANJAS ORIGINALES SIN BLANCO */

/* =========================================================
   DASHBOARD
   Conserva exactamente los colores originales del tema
   ========================================================= */

html[data-color-mode="dark"] .kpi-sales::before {
    background: linear-gradient(
        180deg,
        var(--dash-primary) 0%,
        color-mix(in srgb, var(--dash-primary) 25%, white) 65%,
        color-mix(in srgb, var(--dash-primary) 12%, #132338) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-tables::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-1) 0%,
        color-mix(in srgb, var(--dash-accent-1) 25%, white) 65%,
        color-mix(in srgb, var(--dash-accent-1) 12%, #132338) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-month::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-2) 0%,
        color-mix(in srgb, var(--dash-accent-2) 25%, white) 65%,
        color-mix(in srgb, var(--dash-accent-2) 12%, #132338) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-stock.kpi-alert::before {
    background: linear-gradient(
        180deg,
        #ef4444 0%,
        #fca5a5 60%,
        color-mix(in srgb, #ef4444 12%, #132338) 100%
    ) !important;
}

html[data-color-mode="dark"] .kpi-stock.kpi-ok::before {
    background: linear-gradient(
        180deg,
        var(--dash-accent-4) 0%,
        color-mix(in srgb, var(--dash-accent-4) 25%, white) 65%,
        color-mix(in srgb, var(--dash-accent-4) 12%, #132338) 100%
    ) !important;
}


/* =========================================================
   CLIENTES
   Azul, verde y naranja originales
   ========================================================= */

html[data-color-mode="dark"] .client-stat-card.spent::before {
    background: linear-gradient(
        180deg,
        #3b82f6 0%,
        #93c5fd 55%,
        #132338 100%
    ) !important;
}

html[data-color-mode="dark"] .client-stat-card.visits::before {
    background: linear-gradient(
        180deg,
        #22c55e 0%,
        #86efac 55%,
        #132338 100%
    ) !important;
}

html[data-color-mode="dark"] .client-stat-card.favorite::before {
    background: linear-gradient(
        180deg,
        #f59e0b 0%,
        #fbbf24 48%,
        #132338 100%
    ) !important;
}


/* =========================================================
   REPORTES
   Conserva las variables originales del módulo
   ========================================================= */

html[data-color-mode="dark"] .kpi-income::before {
    background: linear-gradient(
        180deg,
        var(--report-primary),
        #132338
    ) !important;
}

html[data-color-mode="dark"] .kpi-orders::before {
    background: linear-gradient(
        180deg,
        var(--report-accent-1),
        #132338
    ) !important;
}

html[data-color-mode="dark"] .kpi-waiter::before {
    background: linear-gradient(
        180deg,
        var(--report-accent-2),
        #132338
    ) !important;
}

html[data-color-mode="dark"] .kpi-product::before {
    background: linear-gradient(
        180deg,
        var(--report-accent-4),
        #132338
    ) !important;
}

</style>


<style>
/* DARK MODE - FRANJA HISTORIAL DE TURNOS */

html[data-color-mode="dark"] .history-kpi::before {
    background: linear-gradient(
        180deg,
        var(--primary) 0%,
        color-mix(in srgb, var(--primary) 45%, white) 55%,
        color-mix(in srgb, var(--primary) 12%, #132338) 100%
    ) !important;
}

</style>


<style>
/* DARK MODE - RESPETAR COLORES ORIGINALES KPI */

/*
 * No asignamos azul/verde/morado/amarillo manualmente.
 * Cada KPI conserva las variables originales del Dashboard.
 */

/* VENTAS DE HOY */
html[data-color-mode="dark"] .kpi-sales .kpi-icon-wrap {
    color: var(--dash-primary) !important;
    background: color-mix(
        in srgb,
        var(--dash-primary) 18%,
        #132338
    ) !important;
}

/* MESAS EN SERVICIO */
html[data-color-mode="dark"] .kpi-tables .kpi-icon-wrap {
    color: var(--dash-accent-1) !important;
    background: color-mix(
        in srgb,
        var(--dash-accent-1) 18%,
        #132338
    ) !important;
}

/* VENTAS DEL MES */
html[data-color-mode="dark"] .kpi-month .kpi-icon-wrap {
    color: var(--dash-accent-2) !important;
    background: color-mix(
        in srgb,
        var(--dash-accent-2) 18%,
        #132338
    ) !important;
}

/* ALERTA DE STOCK */
html[data-color-mode="dark"] .kpi-stock.kpi-alert .kpi-icon-wrap {
    color: #ef4444 !important;
    background: color-mix(
        in srgb,
        #ef4444 18%,
        #132338
    ) !important;
}

/* STOCK CORRECTO */
html[data-color-mode="dark"] .kpi-stock.kpi-ok .kpi-icon-wrap {
    color: var(--dash-accent-4) !important;
    background: color-mix(
        in srgb,
        var(--dash-accent-4) 18%,
        #132338
    ) !important;
}

/* El icono interno siempre hereda el color real del contenedor */
html[data-color-mode="dark"] .kpi-icon-wrap i {
    color: inherit !important;
}

</style>


<style>
/* DARK MODE - BADGES DASHBOARD RESPETAR TEMA */

/* HOY - mismo color original de Ventas de hoy */
html[data-color-mode="dark"] .kpi-sales .kpi-badge {
    background: color-mix(
        in srgb,
        var(--dash-primary) 14%,
        #132338
    ) !important;

    color: var(--dash-primary) !important;

    border-color: color-mix(
        in srgb,
        var(--dash-primary) 45%,
        #30465d
    ) !important;
}


/* LIVE - mismo color original de Mesas */
html[data-color-mode="dark"] .kpi-tables .kpi-badge {
    background: color-mix(
        in srgb,
        var(--dash-accent-1) 14%,
        #132338
    ) !important;

    color: var(--dash-accent-1) !important;

    border-color: color-mix(
        in srgb,
        var(--dash-accent-1) 45%,
        #30465d
    ) !important;
}


/* MES - mismo color original de Ventas del mes */
html[data-color-mode="dark"] .kpi-month .kpi-badge {
    background: color-mix(
        in srgb,
        var(--dash-accent-2) 14%,
        #132338
    ) !important;

    color: var(--dash-accent-2) !important;

    border-color: color-mix(
        in srgb,
        var(--dash-accent-2) 45%,
        #30465d
    ) !important;
}


/* VER - mismo color original de Stock */
html[data-color-mode="dark"] .kpi-stock .kpi-badge {
    background: color-mix(
        in srgb,
        var(--dash-accent-4) 14%,
        #132338
    ) !important;

    color: var(--dash-accent-4) !important;

    border-color: color-mix(
        in srgb,
        var(--dash-accent-4) 45%,
        #30465d
    ) !important;
}


/* Hover de VER */
html[data-color-mode="dark"] .kpi-stock .kpi-badge-link:hover {
    background: var(--dash-accent-4) !important;
    border-color: var(--dash-accent-4) !important;
    color: #ffffff !important;
}

</style>

<style id="system-sound-toggle-style">

.system-sound-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    width: 40px;
    height: 40px;
    min-height: 40px;
    padding: 0;

    border: 1px solid var(--border-soft);
    border-radius: 10px;

    background: var(--card-bg);
    color: var(--text-main);

    font-size: 13px;
    font-weight: 700;

    transition:
        background-color .2s ease,
        border-color .2s ease,
        color .2s ease,
        transform .2s ease;
}

.system-sound-toggle:hover {
    border-color:
        color-mix(
            in srgb,
            var(--primary) 45%,
            var(--border-soft)
        );

    color: var(--primary);

    transform: translateY(-1px);
}

.system-sound-toggle.is-active {
    color: #198754;

    background:
        color-mix(
            in srgb,
            #198754 9%,
            var(--card-bg)
        );

    border-color:
        color-mix(
            in srgb,
            #198754 38%,
            var(--border-soft)
        );
}

.system-sound-toggle i {
    font-size: 15px;
}

html[data-color-mode="dark"]
.system-sound-toggle {
    background: #132338;
    border-color: #30465d;
}

html[data-color-mode="dark"]
.system-sound-toggle.is-active {
    color: #65d99b;

    background:
        color-mix(
            in srgb,
            #198754 17%,
            #132338
        );

    border-color:
        color-mix(
            in srgb,
            #198754 50%,
            #30465d
        );
}

@media (max-width: 768px) {

    .system-sound-toggle span {
        display: none;
    }

    .system-sound-toggle {
        width: 38px;
        padding-left: 0;
        padding-right: 0;
    }
}

</style>

<style id="sidebar-collapse-final">

/* ==========================================================
   SIDEBAR COMPRIMIBLE - ESCRITORIO
   ========================================================== */

@media (min-width: 992px) {

    .sidebar,
    .main-content {
        transition:
            width .25s ease,
            margin-left .25s ease !important;
    }

    .sidebar-collapse-btn {
        position: absolute;
        top: 67px;
        right: -13px;

        width: 27px;
        height: 27px;

        align-items: center;
        justify-content: center;

        padding: 0;
        border-radius: 50%;

        border: 1px solid rgba(255,255,255,.16);
        background: var(--primary);
        color: #fff;

        box-shadow: 0 4px 12px rgba(0,0,0,.18);

        z-index: 1100;

        cursor: pointer;

        transition:
            transform .2s ease,
            background .2s ease,
            box-shadow .2s ease;
    }

    .sidebar-collapse-btn:hover {
        background: var(--primary-hover);
        box-shadow: 0 6px 16px rgba(0,0,0,.25);
    }


    /* SIDEBAR COMPRIMIDO */

    body.sidebar-collapsed .sidebar {
        width: 78px !important;
    }

    body.sidebar-collapsed .main-content {
        margin-left: 78px !important;
    }


    /* ENCABEZADO */

    body.sidebar-collapsed .sidebar-header {
        padding-left: 16px;
        padding-right: 16px;
        justify-content: center;
    }

    body.sidebar-collapsed .sidebar-header .brand-name {
        display: none !important;
    }

    body.sidebar-collapsed .sidebar-header > img {
        width: 42px !important;
        height: 42px !important;
        flex: 0 0 42px;
    }

    body.sidebar-collapsed .sidebar-header .logo-box {
        width: 42px;
        height: 42px;
        min-width: 42px;
    }


    /* BOTÓN */

    body.sidebar-collapsed .sidebar-collapse-btn {
        right: -13px;
    }

    body.sidebar-collapsed .sidebar-collapse-btn i {
        transform: rotate(180deg);
    }


    /* MENÚ */

    body.sidebar-collapsed .sidebar-menu {
        padding-left: 10px;
        padding-right: 10px;
        overflow-x: hidden;
    }

    body.sidebar-collapsed .menu-category {
        height: 1px;
        margin: 15px 10px;
        padding: 0;
        overflow: hidden;
        font-size: 0;
        background: rgba(255,255,255,.10);
    }

    body.sidebar-collapsed .sidebar .nav-link {
        justify-content: center;
        gap: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    body.sidebar-collapsed .sidebar .nav-link > i {
        margin: 0 !important;
        font-size: 1.18rem;
    }

    body.sidebar-collapsed .sidebar .nav-link {
        font-size: 0;
    }

    body.sidebar-collapsed .sidebar .nav-link > i {
        font-size: 1.18rem;
    }


    /* Ocultar textos auxiliares del sidebar */

    body.sidebar-collapsed .sidebar-menu .badge,
    body.sidebar-collapsed .sidebar-menu small {
        display: none !important;
    }
}

</style>

<style id="sidebar-collapse-visual-fix">

@media (min-width: 992px) {

    /* El encabezado permite ubicar correctamente el control */
    .sidebar-header {
        position: relative;
    }

    /* Botón más discreto e integrado */
    .sidebar-collapse-btn {
        position: absolute !important;
        top: 50% !important;
        right: 10px !important;

        transform: translateY(-50%) !important;

        width: 30px !important;
        height: 30px !important;

        border-radius: 9px !important;

        background: rgba(255,255,255,.10) !important;
        border: 1px solid rgba(255,255,255,.13) !important;

        color: rgba(255,255,255,.82) !important;

        box-shadow: none !important;

        z-index: 5 !important;
    }

    .sidebar-collapse-btn:hover {
        background: rgba(255,255,255,.18) !important;
        color: #fff !important;
        transform: translateY(-50%) !important;
    }

    /* Espacio para que el nombre no choque con la flecha */
    .sidebar-header .brand-name {
        padding-right: 30px;
    }


    /* ==========================
       ESTADO COMPRIMIDO
       ========================== */

    body.sidebar-collapsed .sidebar {
        width: 78px !important;
    }

    body.sidebar-collapsed .main-content {
        margin-left: 78px !important;
    }

    body.sidebar-collapsed .sidebar-header {
        min-height: 82px;
        padding: 14px 10px !important;
        justify-content: center;
    }

    body.sidebar-collapsed .sidebar-header .brand-name {
        display: none !important;
    }

    /* En comprimido ocultamos el logo del encabezado
       para que no compita con el botón */
    body.sidebar-collapsed .sidebar-header > img,
    body.sidebar-collapsed .sidebar-header > .logo-box {
        display: none !important;
    }

    body.sidebar-collapsed .sidebar-collapse-btn {
        position: relative !important;

        top: auto !important;
        right: auto !important;

        transform: none !important;

        width: 38px !important;
        height: 38px !important;

        border-radius: 11px !important;
    }

    body.sidebar-collapsed .sidebar-collapse-btn:hover {
        transform: none !important;
    }

    body.sidebar-collapsed .sidebar-collapse-btn i {
        transform: rotate(180deg);
    }


    /* Menú comprimido más limpio */
    body.sidebar-collapsed .sidebar-menu {
        padding: 10px 9px 20px !important;
    }

    body.sidebar-collapsed .menu-category {
        margin: 14px 8px 9px !important;
        height: 1px !important;

        font-size: 0 !important;

        background: rgba(255,255,255,.10);
    }

    body.sidebar-collapsed .sidebar .nav-link {
        width: 56px;
        min-height: 52px;

        margin-left: auto;
        margin-right: auto;

        padding: 0 !important;

        display: flex !important;
        align-items: center;
        justify-content: center !important;

        font-size: 0 !important;

        border-radius: 13px;
    }

    body.sidebar-collapsed .sidebar .nav-link > i {
        width: auto !important;
        margin: 0 !important;

        font-size: 20px !important;

        display: flex;
        align-items: center;
        justify-content: center;
    }

    body.sidebar-collapsed .sidebar .nav-link.active {
        width: 56px;
    }
}

</style>

<style id="sidebar-collapse-professional">

@media (min-width: 992px) {

    /* =========================================
       TRANSICIÓN GENERAL
       ========================================= */

    .sidebar {
        transition:
            width .28s cubic-bezier(.4,0,.2,1),
            box-shadow .28s ease !important;
    }

    .main-content {
        transition:
            margin-left .28s cubic-bezier(.4,0,.2,1) !important;
    }


    /* =========================================
       BOTÓN PROFESIONAL
       ========================================= */

    .sidebar-collapse-btn {
        width: 32px !important;
        height: 32px !important;

        right: 11px !important;

        border-radius: 10px !important;

        background: rgba(255,255,255,.075) !important;
        border: 1px solid rgba(255,255,255,.12) !important;

        color: rgba(255,255,255,.72) !important;

        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.06) !important;

        backdrop-filter: blur(8px);

        transition:
            background .18s ease,
            color .18s ease,
            border-color .18s ease !important;
    }

    .sidebar-collapse-btn i {
        font-size: 13px;
        line-height: 1;

        transition: transform .28s cubic-bezier(.4,0,.2,1);
    }

    .sidebar-collapse-btn:hover {
        background: rgba(255,255,255,.14) !important;
        border-color: rgba(255,255,255,.20) !important;
        color: #fff !important;
    }


    /* =========================================
       SIDEBAR COMPRIMIDO
       ========================================= */

    body.sidebar-collapsed .sidebar {
        width: 80px !important;

        box-shadow:
            6px 0 24px rgba(0,0,0,.08) !important;
    }

    body.sidebar-collapsed .main-content {
        margin-left: 80px !important;
    }


    /* =========================================
       ENCABEZADO COMPRIMIDO
       ========================================= */

    body.sidebar-collapsed .sidebar-header {
        min-height: 82px !important;

        padding: 15px 12px !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    body.sidebar-collapsed .sidebar-header .brand-name,
    body.sidebar-collapsed .sidebar-header > img,
    body.sidebar-collapsed .sidebar-header > .logo-box {
        display: none !important;
    }

    body.sidebar-collapsed .sidebar-collapse-btn {
        position: relative !important;

        top: auto !important;
        right: auto !important;

        width: 38px !important;
        height: 38px !important;

        transform: none !important;

        border-radius: 11px !important;

        background: rgba(255,255,255,.08) !important;
    }

    body.sidebar-collapsed .sidebar-collapse-btn:hover {
        transform: none !important;
        background: rgba(255,255,255,.15) !important;
    }

    body.sidebar-collapsed .sidebar-collapse-btn i {
        transform: rotate(180deg);
    }


    /* =========================================
       MENÚ COMPRIMIDO
       ========================================= */

    body.sidebar-collapsed .sidebar-menu {
        padding:
            12px 10px
            24px !important;
    }


    /* Separadores de categorías */

    body.sidebar-collapsed .menu-category {
        width: 30px !important;
        height: 1px !important;

        margin:
            16px auto
            10px !important;

        padding: 0 !important;

        font-size: 0 !important;

        overflow: hidden;

        background:
            rgba(255,255,255,.12) !important;

        border: 0 !important;
    }


    /* =========================================
       BOTONES DEL MENÚ
       ========================================= */

    body.sidebar-collapsed .sidebar .nav-link {
        width: 56px !important;
        height: 52px !important;
        min-height: 52px !important;

        margin:
            3px auto !important;

        padding: 0 !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        gap: 0 !important;

        border-radius: 14px !important;

        font-size: 0 !important;

        transition:
            background .18s ease,
            transform .18s ease,
            box-shadow .18s ease !important;
    }


    /* Iconos */

    body.sidebar-collapsed .sidebar .nav-link > i {
        width: 24px !important;
        height: 24px !important;

        margin: 0 !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        font-size: 19px !important;
        line-height: 1 !important;
    }


    /* Hover */

    body.sidebar-collapsed .sidebar .nav-link:hover {
        background:
            rgba(255,255,255,.09) !important;

        transform:
            translateY(-1px);
    }


    /* Activo */

    body.sidebar-collapsed .sidebar .nav-link.active {
        width: 56px !important;

        box-shadow:
            0 7px 18px
            var(--theme-shadow) !important;

        transform: none !important;
    }


    /* Eliminar textos secundarios */

    body.sidebar-collapsed .sidebar-menu .badge,
    body.sidebar-collapsed .sidebar-menu small {
        display: none !important;
    }


    /* =========================================
       TOOLTIP NATIVO
       ========================================= */

    body.sidebar-collapsed .sidebar .nav-link {
        position: relative;
    }
}

</style>

<style id="sidebar-groups-professional">

/* ==========================================================
   GRUPOS DESPLEGABLES DEL SIDEBAR
   ========================================================== */

.sidebar-menu .sidebar-group {
    margin: 5px 0;
}

.sidebar-menu .sidebar-group-toggle {
    width: 100%;
    min-height: 38px;

    display: flex;
    align-items: center;

    padding: 7px 11px;

    border: 0;
    border-radius: 10px;

    background: transparent;
    color: rgba(255,255,255,.48);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: .75px;
    text-transform: uppercase;

    cursor: pointer;

    transition:
        color .18s ease,
        background .18s ease;
}

.sidebar-menu .sidebar-group-toggle:hover {
    color: rgba(255,255,255,.82);
    background: rgba(255,255,255,.045);
}

.sidebar-group-title {
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.sidebar-group-arrow {
    margin-left: auto;

    font-size: 11px;

    transition:
        transform .22s cubic-bezier(.4,0,.2,1);
}

.sidebar-group.open .sidebar-group-arrow {
    transform: rotate(90deg);
}


/* Contenido desplegable */

.sidebar-group-content {
    display: grid;
    grid-template-rows: 0fr;

    opacity: .55;

    transition:
        grid-template-rows .25s cubic-bezier(.4,0,.2,1),
        opacity .20s ease;
}

.sidebar-group-content-inner {
    min-height: 0;
    overflow: hidden;
}

.sidebar-group.open .sidebar-group-content {
    grid-template-rows: 1fr;
    opacity: 1;
}

.sidebar-group.open .sidebar-group-content-inner {
    padding-top: 3px;
    padding-bottom: 4px;
}


/* Categoría original reemplazada por el nuevo botón */

.sidebar-menu .sidebar-group > .menu-category {
    display: none !important;
}


/* ==========================================================
   TOOLTIP PROFESIONAL EN MODO COMPRIMIDO
   ========================================================== */

.sidebar-icon-tooltip {
    position: fixed;

    z-index: 99999;

    padding: 8px 11px;

    border-radius: 8px;

    background: #111827;
    color: #fff;

    border: 1px solid rgba(255,255,255,.08);

    box-shadow:
        0 8px 24px rgba(0,0,0,.20);

    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;

    white-space: nowrap;

    pointer-events: none;

    opacity: 0;
    visibility: hidden;

    transform: translateX(-4px);

    transition:
        opacity .14s ease,
        transform .14s ease,
        visibility .14s ease;
}

.sidebar-icon-tooltip.show {
    opacity: 1;
    visibility: visible;

    transform: translateX(0);
}

.sidebar-icon-tooltip::before {
    content: "";

    position: absolute;

    left: -5px;
    top: 50%;

    width: 9px;
    height: 9px;

    background: #111827;

    border-left:
        1px solid rgba(255,255,255,.08);

    border-bottom:
        1px solid rgba(255,255,255,.08);

    transform:
        translateY(-50%)
        rotate(45deg);
}


/* ==========================================================
   COMPORTAMIENTO CUANDO EL SIDEBAR ESTÁ COMPRIMIDO
   ========================================================== */

@media (min-width: 992px) {

    body.sidebar-collapsed
    .sidebar-group-toggle {
        width: 30px !important;
        height: 1px !important;
        min-height: 1px !important;

        margin: 15px auto 10px !important;
        padding: 0 !important;

        background:
            rgba(255,255,255,.12) !important;

        border-radius: 20px !important;

        pointer-events: none;
    }

    body.sidebar-collapsed
    .sidebar-group-title,

    body.sidebar-collapsed
    .sidebar-group-arrow {
        display: none !important;
    }

    /*
       Al comprimir, mostramos todos los iconos.
       No tiene sentido esconderlos dentro de submenús
       cuando ya no podemos ver el título del grupo.
    */

    body.sidebar-collapsed
    .sidebar-group-content {
        display: block !important;

        opacity: 1 !important;

        grid-template-rows: none !important;
    }

    body.sidebar-collapsed
    .sidebar-group-content-inner {
        overflow: visible !important;
        padding: 0 !important;
    }
}

</style>

<style id="sidebar-expand-all-style">

.sidebar-expand-all-control {
    margin: 4px 10px 10px;
    padding-bottom: 10px;

    border-bottom: 1px solid rgba(255,255,255,.08);
}

.sidebar-expand-all-btn {
    width: 100%;
    min-height: 36px;

    padding: 7px 10px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px;

    background: rgba(255,255,255,.045);
    color: rgba(255,255,255,.72);

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;

    transition:
        background .18s ease,
        border-color .18s ease,
        color .18s ease;
}

.sidebar-expand-all-btn:hover {
    background: rgba(255,255,255,.09);
    border-color: rgba(255,255,255,.14);
    color: #fff;
}

.sidebar-expand-all-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sidebar-expand-all-left > i {
    font-size: 13px;
}

.sidebar-expand-all-arrow {
    font-size: 11px;

    transition:
        transform .22s cubic-bezier(.4,0,.2,1);
}

.sidebar-expand-all-btn.all-open
.sidebar-expand-all-arrow {
    transform: rotate(180deg);
}


/* Ocultarlo cuando el sidebar esté comprimido */

@media (min-width: 992px) {

    body.sidebar-collapsed
    .sidebar-expand-all-control {
        display: none !important;
    }

}

</style>

<style id="sidebar-tooltip-only-style">

/* ==========================================================
   TOOLTIP DEL SIDEBAR COMPRIMIDO
   ========================================================== */

.sidebar-hover-tooltip {
    position: fixed;
    z-index: 999999;

    padding: 8px 11px;

    background: #111827;
    color: #ffffff;

    border: 1px solid rgba(255,255,255,.08);
    border-radius: 8px;

    box-shadow: 0 8px 24px rgba(0,0,0,.22);

    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;

    white-space: nowrap;

    pointer-events: none;

    opacity: 0;
    visibility: hidden;

    transform: translateX(-5px);

    transition:
        opacity .14s ease,
        transform .14s ease,
        visibility .14s ease;
}

.sidebar-hover-tooltip.show {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}

/* Flechita del tooltip */

.sidebar-hover-tooltip::before {
    content: "";

    position: absolute;

    left: -5px;
    top: 50%;

    width: 9px;
    height: 9px;

    background: #111827;

    transform:
        translateY(-50%)
        rotate(45deg);
}


/* Solo se utiliza en escritorio */

@media (max-width: 991.98px) {

    .sidebar-hover-tooltip {
        display: none !important;
    }

}

</style>
</head>


@php
    $dashboardTheme = \App\Models\Setting::where('key', 'dashboard_theme')->value('value') ?? 'ocean-orange';

    // Compatibilidad con el nombre anterior del tema.
    if ($dashboardTheme === 'ocean-coral') {
        $dashboardTheme = 'ocean-orange';
    }
@endphp

<body class="{{ request()->routeIs('pos.order') ? 'pos-page' : '' }} theme-{{ $dashboardTheme }}">


<div
    class="mobile-overlay"
    id="mobileOverlay"
    onclick="closeMenu()"
></div>


{{-- =============================================================
     SIDEBAR
============================================================= --}}

<div
    class="sidebar"
    id="sidebar"
>

    <div class="sidebar-header">

        @php
            $logo = \App\Models\Setting::where('key', 'company_logo')->value('value');
        @endphp


        @if($logo)

            <img
                src="{{ asset('storage/'.$logo) }}"
                style="
                    width:46px;
                    height:46px;
                    object-fit:cover;
                    border-radius:14px;
                "
                alt="Logo"
            >

        @else

            <div class="logo-box">

                <i class="bi bi-shop"></i>

            </div>

        @endif


        <button
            type="button"
            class="sidebar-collapse-btn d-none d-lg-flex"
            id="sidebarCollapseBtn"
            title="Comprimir menú"
            aria-label="Comprimir menú"
        >
            <i class="bi bi-chevron-left" id="sidebarCollapseIcon"></i>
        </button>


        <div class="brand-name text-truncate">

            {{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante' }}

        </div>


        <button
            type="button"
            class="
                btn
                btn-sm
                text-white-50
                d-lg-none
                ms-auto
                p-1
            "
            onclick="closeMenu()"
        >

            <i class="bi bi-x-lg"></i>

        </button>

    </div>


    <div class="sidebar-menu">

        @php
            $role = Auth::user()->role;
        @endphp


        {{-- DASHBOARD --}}

        @if(in_array($role, ['admin', 'cashier']))

            <a
                href="{{ route('dashboard') }}"
                class="
                    nav-link
                    {{ request()->routeIs('dashboard') ? 'active' : '' }}
                "
            >

                <i class="bi bi-grid-1x2-fill"></i>

                Dashboard

            </a>

        @endif


        {{-- REPORTES --}}

        @if($role === 'admin')

            <a
                href="{{ route('reports.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('reports.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-bar-chart-line-fill"></i>

                Reportes

            </a>

            {{-- CHAT IA --}}
            <a
                href="{{ route('ai.chat') }}"
                class="
                    nav-link
                    {{ request()->routeIs('ai.chat*') ? 'active' : '' }}
                "
            >
                <i class="bi bi-robot"></i>
                Chat IA
            </a>

            {{-- ASISTENTE IA --}}
            <a
                href="{{ route('ai.assistant') }}"
                class="
                    nav-link
                    {{ request()->routeIs('ai.assistant*') ? 'active' : '' }}
                "
            >
                <i class="bi bi-stars"></i>
                Asistente IA
            </a>

        @endif


        {{-- =====================================================
             OPERACIONES
        ====================================================== --}}

        <div class="menu-category">

            Operaciones

        </div>


        @if(in_array($role, ['admin', 'cashier', 'waiter']))
        <a
            href="{{ route('pos.index') }}"
            class="
                nav-link
                {{ request()->routeIs('pos.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-bag-check-fill"></i>

            Punto de Venta

        </a>
        @endif


        @if(in_array($role, ['admin', 'cashier', 'waiter']))

            <a
                href="{{ route('delivery.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('delivery.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-bicycle"></i>

                Delivery

            </a>

        @endif


        <a
            href="{{ route('reservations.index') }}"
            class="
                nav-link
                {{ request()->routeIs('reservations.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-calendar-event-fill"></i>

            Reservas

        </a>


        @if(in_array($role, ['admin', 'cashier']))

            <a
                href="{{ route('sales.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('sales.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-receipt"></i>

                Historial de Ventas

            </a>

        @endif


        @if(in_array($role, ['admin', 'kitchen']))
        <a
            href="{{ route('kitchen.index') }}"
            class="
                nav-link
                {{ request()->routeIs('kitchen.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-fire"></i>

            Cocina (KDS)

        </a>
        @endif

        @if(in_array($role, ['admin', 'bar']))
        <a
            href="{{ route('barra.index') }}"
            class="
                nav-link
                {{ request()->routeIs('barra.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-cup-straw"></i>

            Barra

        </a>
        @endif


        {{-- =====================================================
             CAJA
        ====================================================== --}}

        @if(in_array($role, ['admin', 'cashier']))

            <div class="menu-category">

                Caja / Arqueo

            </div>


            @if(\App\Models\CashRegister::where('status', 'open')->exists())

                <a
                    href="{{ route('cash_registers.close') }}"
                    class="
                        nav-link
                        {{ request()->routeIs('cash_registers.close') ? 'active' : '' }}
                    "
                >

                    <i class="bi bi-box-arrow-left"></i>

                    Cerrar Caja

                </a>

            @else

                <a
                    href="{{ route('cash_registers.create') }}"
                    class="
                        nav-link
                        {{ request()->routeIs('cash_registers.create') ? 'active' : '' }}
                    "
                >

                    <i class="bi bi-box-arrow-in-right"></i>

                    Abrir Caja

                </a>

            @endif


            @if(in_array($role, ['admin', 'cashier']))

                <a
                    href="{{ route('cash_registers.index') }}"
                    class="
                        nav-link
                        {{ request()->routeIs('cash_registers.index') ? 'active' : '' }}
                    "
                >

                    <i class="bi bi-clock-history"></i>

                    Historial de Turnos

                </a>

            @endif

        @endif


        {{-- =====================================================
             FACTURACIÓN ELECTRÓNICA
        ====================================================== --}}

        @if(in_array($role, ['admin', 'cashier']))

            <div class="menu-category">

                Facturación Electrónica

            </div>


            <a
                href="{{ route('billing.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('billing.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-receipt-cutoff"></i>

                Comprobantes

            </a>


            <a
                href="{{ route('credit_notes.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('credit_notes.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-arrow-counterclockwise"></i>

                Notas de Crédito

            </a>


            <a
                href="{{ route('daily_summaries.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('daily_summaries.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-calendar-week"></i>

                Resumen Diario

            </a>

        @endif


        {{-- =====================================================
             GESTIÓN
        ====================================================== --}}

        <div class="menu-category">

            Gestión

        </div>


        <a
            href="{{ route('clients.index') }}"
            class="
                nav-link
                {{ request()->routeIs('clients.*') ? 'active' : '' }}
            "
        >

            <i class="bi bi-people-fill"></i>

            Clientes

        </a>


        @if($role === 'admin')

            <a
                href="{{ route('categories.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('categories.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-tags-fill"></i>

                Categorías

            </a>


            <a
                href="{{ route('products.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('products.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-box-seam-fill"></i>

                Inventario

            </a>


            <a
                href="{{ route('menu.index') }}"
                target="_blank"
                class="nav-link"
            >

                <i class="bi bi-qr-code-scan"></i>

                Carta Digital


                <i
                    class="
                        bi
                        bi-box-arrow-up-right
                        ms-auto
                    "
                    style="
                        margin-right:0;
                        font-size:.68rem;
                    "
                ></i>

            </a>


            <a
                href="{{ route('tables.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('tables.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-grid-3x3-gap-fill"></i>

                Mesas

            </a>


            <a
                href="{{ route('users.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('users.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-person-badge-fill"></i>

                Personal / Usuarios

            </a>


            <a
                href="{{ route('settings.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('settings.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-gear-fill"></i>

                Configuración

            </a>


            <a
                href="{{ route('system.index') }}"
                class="
                    nav-link
                    {{ request()->routeIs('system.*') ? 'active' : '' }}
                "
            >

                <i class="bi bi-tools"></i>

                Mantenimiento

            </a>

        @endif

    </div>

</div>


{{-- =============================================================
     CONTENIDO PRINCIPAL
============================================================= --}}

<div class="main-content">


    @if(!request()->routeIs('pos.order'))

        <div class="top-navbar">


            <div class="d-flex align-items-center gap-3">


                <button
                    type="button"
                    class="
                        btn
                        btn-light
                        border
                        d-lg-none
                        px-2
                        py-1
                    "
                    onclick="openMenu()"
                >

                    <i class="bi bi-list fs-5"></i>

                </button>


                <div class="topbar-module-title d-none d-sm-flex">

                    <span class="topbar-module-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>

                    <h5 class="mb-0">


                    @if(request()->routeIs('dashboard'))

                        Panel de Control


                    @elseif(request()->routeIs('pos.*'))

                        Punto de Venta


                    @elseif(request()->routeIs('delivery.*'))

                        Delivery


                    @elseif(request()->routeIs('products.*'))

                        Inventario


                    @elseif(request()->routeIs('sales.*'))

                        Ventas y Movimientos


                    @elseif(request()->routeIs('billing.*'))

                        Facturación Electrónica


                    @elseif(request()->routeIs('credit_notes.*'))

                        Notas de Crédito


                    @elseif(request()->routeIs('daily_summaries.*'))

                        Resumen Diario


                    @elseif(request()->routeIs('cash_registers.*'))

                        Caja / Arqueo


                    @elseif(request()->routeIs('reservations.*'))

                        Reservas


                    @elseif(request()->routeIs('kitchen.*'))

                        Cocina


                    @elseif(request()->routeIs('categories.*'))

                        Categorías


                    @elseif(request()->routeIs('users.*'))

                        Gestión de Personal


                    @elseif(request()->routeIs('settings.*'))

                        Configuración


                    @else

                        Sistema de Restaurante

                    @endif


                    </h5>

                </div>

            </div>


            {{-- USUARIO --}}

            <div class="dropdown">


                <!-- Modo claro / oscuro -->
                <button
                    type="button"
                    class="system-theme-toggle"
                    id="systemThemeToggle"
                    aria-label="Activar modo oscuro"
                    title="Modo oscuro"
                >
                    <i
                        class="bi bi-moon-stars-fill"
                        id="systemThemeIcon"
                    ></i>
                </button>
                <button
                    type="button"
                    class="btn system-sound-toggle"
                    id="systemSoundToggle"
                    title="Activar sonidos"
                    aria-label="Activar sonidos"
                    aria-pressed="false"
                >
                    <i
                        class="bi bi-bell-fill"
                        id="systemSoundIcon"
                    ></i>

                    <span id="systemSoundText" class="visually-hidden">
                        Activar sonidos
                    </span>
                </button>

                <div
                    class="user-profile-btn header-user-control"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    role="button"
                    tabindex="0"
                >

                    <div class="header-user-info d-none d-sm-flex">

                        <span class="header-user-name">
                            {{ Auth::user()->name }}
                        </span>

                        <span class="header-user-role">
                            {{ match(Auth::user()->role) {
    'admin' => 'Administrador',
    'cashier' => 'Cajero',
    'waiter' => 'Mozo',
    'kitchen' => 'Cocina',
    'bar' => 'Barra',
    default => ucfirst(Auth::user()->role),
} }}
                        </span>

                    </div>

                    <div class="user-avatar header-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <span class="header-user-chevron">
                        <i class="bi bi-chevron-down"></i>
                    </span>

                </div>


                <ul
                    class="
                        dropdown-menu
                        dropdown-menu-end
                        account-dropdown
                    "
                >

                    <li class="account-dropdown-title">
                        MI CUENTA
                    </li>


                    @if($role === 'admin')

                        <li>

                            <button
                                type="button"
                                class="
                                    dropdown-item
                                    account-dropdown-item
                                "
                                data-bs-toggle="modal"
                                data-bs-target="#profileModal"
                            >

                                <span class="account-dropdown-icon">
                                    <i class="bi bi-person-gear"></i>
                                </span>

                                <span class="account-dropdown-content">

                                    <span class="account-dropdown-label">
                                        Editar perfil
                                    </span>

                                    <span class="account-dropdown-description">
                                        Información de tu cuenta
                                    </span>

                                </span>

                                <i class="bi bi-chevron-right account-dropdown-arrow"></i>

                            </button>

                        </li>

                    @endif


                    <li>
                        <div class="account-dropdown-divider"></div>
                    </li>


                    <li>

                        <form action="{{ route('logout') }}" method="POST" id="logoutForm">

                            @csrf

                            <button
                                type="button" onclick="openSystemLogoutConfirm()"
                                class="
                                    dropdown-item
                                    account-dropdown-item
                                    account-dropdown-logout
                                "
                            >

                                <span class="account-dropdown-icon logout">
                                    <i class="bi bi-box-arrow-right"></i>
                                </span>

                                <span class="account-dropdown-content">

                                    <span class="account-dropdown-label">
                                        Cerrar sesión
                                    </span>

                                    <span class="account-dropdown-description">
                                        Salir de forma segura
                                    </span>

                                </span>

                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    @endif


@yield('content')

</div>



{{-- =============================================================
     PERFIL
============================================================= --}}

@if($role === 'admin')

    <div
        class="modal fade profile-system-modal"
        id="profileModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                {{-- ENCABEZADO --}}
                <div class="modal-header profile-modal-header">

                    <div class="profile-modal-heading">

                        <i class="bi bi-person-circle"></i>

                        <div>
                            <h5 class="profile-modal-title">
                                Mi Perfil
                            </h5>

                            <p class="profile-modal-subtitle">
                                Actualiza tus datos de acceso
                            </p>
                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>

                </div>


                <form
                    action="{{ route('users.update', Auth::user()->id) }}"
                    method="POST"
                >

                    @csrf
                    @method('PUT')


                    <div class="modal-body profile-modal-body">

                        {{-- DATOS PERSONALES --}}
                        <div class="profile-section">

                            <div class="profile-section-title">
                                <i class="bi bi-person"></i>
                                Datos personales
                            </div>


                            {{-- NOMBRE --}}
                            <div class="profile-field">

                                <label
                                    for="profileName"
                                    class="profile-label"
                                >
                                    Nombre
                                </label>

                                <div class="profile-input-wrapper">

                                    <span class="profile-input-icon">
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="profileName"
                                        name="name"
                                        class="form-control profile-input"
                                        value="{{ Auth::user()->name }}"
                                        autocomplete="name"
                                        required
                                    >

                                </div>

                            </div>


                            {{-- CORREO --}}
                            <div class="profile-field mb-0">

                                <label class="profile-label">
                                    Correo electrónico
                                </label>

                                <div class="profile-input-wrapper readonly">

                                    <span class="profile-input-icon">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input
                                        type="email"
                                        class="form-control profile-input"
                                        value="{{ Auth::user()->email }}"
                                        readonly
                                    >

                                    <span
                                        class="profile-locked"
                                        title="El correo no se puede modificar desde aquí"
                                    >
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                </div>

                                <div class="profile-help">
                                    Este correo se utiliza para acceder al sistema.
                                </div>

                            </div>

                        </div>


                        {{-- SEGURIDAD --}}
                        <div class="profile-section profile-security-section">

                            <div class="profile-section-title">
                                <i class="bi bi-shield-lock"></i>
                                Seguridad
                            </div>


                            <div class="profile-field mb-0">

                                <label
                                    for="profilePassword"
                                    class="profile-label"
                                >
                                    Nueva contraseña
                                </label>

                                <div class="profile-input-wrapper">

                                    <span class="profile-input-icon">
                                        <i class="bi bi-key"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="profilePassword"
                                        name="password"
                                        class="form-control profile-input profile-password-input"
                                        placeholder="Ingresa una nueva contraseña"
                                        autocomplete="new-password"
                                    >

                                    <button
                                        type="button"
                                        class="profile-password-toggle"
                                        id="profilePasswordToggle"
                                        aria-label="Mostrar contraseña"
                                        title="Mostrar contraseña"
                                    >
                                        <i
                                            class="bi bi-eye"
                                            id="profilePasswordIcon"
                                        ></i>
                                    </button>

                                </div>

                                <div class="profile-help">
                                    Déjala en blanco si deseas conservar tu contraseña actual.
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- PIE --}}
                    <div class="modal-footer profile-modal-footer">

                        <button
                            type="button"
                            class="btn profile-btn-cancel"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="btn profile-btn-save"
                        >
                            <i class="bi bi-check2"></i>
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endif





{{-- =============================================================
     SCRIPTS
============================================================= --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
></script>

<script
    src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
></script>


<script>

    function openMenu() {

        document
            .getElementById('sidebar')
            ?.classList
            .add('show');


        document
            .getElementById('mobileOverlay')
            ?.classList
            .add('show');


        document.body.style.overflow =
            'hidden';

    }


    function closeMenu() {

        document
            .getElementById('sidebar')
            ?.classList
            .remove('show');


        document
            .getElementById('mobileOverlay')
            ?.classList
            .remove('show');


        document.body.style.overflow =
            'auto';

    }


    document
        .querySelectorAll('.alert:not(#driverUpdateMessage):not(#deliveryStatusAlert)')
        .forEach(
            alertElement => {

                setTimeout(
                    () => {

                        alertElement.style.transition =
                            'opacity .5s';


                        alertElement.style.opacity =
                            '0';


                        setTimeout(
                            () => {
                                alertElement.remove();
                            },
                            500
                        );

                    },
                    5000
                );

            }
        );

</script>





<!-- ======================================================
     SISTEMA GLOBAL DE NOTIFICACIONES - EL CAPITAN
     ====================================================== -->


<style id="system-confirm-design-final">
/* ============================================================
   SYSTEM NOTIFY - DISEÑO UNIFICADO DEFINITIVO
   ============================================================ */

.system-confirm-modal .modal-dialog {
    width: calc(100% - 32px) !important;
    max-width: 430px !important;
    margin-left: auto !important;
    margin-right: auto !important;
}

.system-confirm-modal .modal-content {
    overflow: hidden !important;
    border: 1px solid var(--border-soft) !important;
    border-radius: 18px !important;
    background: var(--card-bg) !important;
    box-shadow: 0 24px 70px rgba(15, 23, 42, .20) !important;
}


/* FRANJA SUPERIOR */

.system-confirm-strip {
    height: 4px !important;
    background: var(--sys-color) !important;
}


/* CUERPO */

.system-confirm-body {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;

    padding: 29px 32px 23px !important;

    background: var(--card-bg) !important;
    text-align: center !important;
}


/* BOTON CERRAR */

.system-confirm-close {
    top: 15px !important;
    right: 15px !important;

    width: 32px !important;
    height: 32px !important;

    padding: 0 !important;

    border: 1px solid var(--border-soft) !important;
    border-radius: 9px !important;

    background: var(--card-bg) !important;
    color: var(--text-muted) !important;

    font-size: .85rem !important;

    box-shadow: none !important;
}

.system-confirm-close:hover {
    border-color:
        color-mix(
            in srgb,
            var(--sys-color) 35%,
            var(--border-soft)
        ) !important;

    background:
        color-mix(
            in srgb,
            var(--sys-color) 7%,
            var(--card-bg)
        ) !important;

    color: var(--sys-color) !important;
}


/* ICONO */

.system-confirm-icon {
    width: 62px !important;
    height: 62px !important;

    flex: 0 0 62px !important;

    margin: 0 auto 17px !important;

    border:
        1px solid
        color-mix(
            in srgb,
            var(--sys-color) 30%,
            transparent
        ) !important;

    border-radius: 16px !important;

    background:
        color-mix(
            in srgb,
            var(--sys-color) 10%,
            var(--card-bg)
        ) !important;

    color: var(--sys-color) !important;

    box-shadow:
        0 8px 20px
        color-mix(
            in srgb,
            var(--sys-color) 9%,
            transparent
        ) !important;
}

.system-confirm-icon i {
    color: var(--sys-color) !important;
    font-size: 1.45rem !important;
    line-height: 1 !important;
}


/* TITULO */

.system-confirm-title {
    width: 100% !important;

    margin: 0 42px 9px !important;

    color: var(--text-main) !important;

    font-size: 1.03rem !important;
    font-weight: 800 !important;
    line-height: 1.3 !important;

    letter-spacing: -.2px !important;
}


/* MENSAJE */

.system-confirm-text {
    width: 100% !important;
    max-width: 345px !important;

    margin: 0 auto !important;

    color: var(--text-muted) !important;

    font-size: .76rem !important;
    font-weight: 500 !important;
    line-height: 1.55 !important;
}


/* PIE */

.system-confirm-footer {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;

    gap: 10px !important;

    padding: 15px 20px 18px !important;

    border-top: 1px solid var(--border-soft) !important;

    background:
        color-mix(
            in srgb,
            var(--light-bg) 50%,
            var(--card-bg)
        ) !important;
}


/* BOTONES */

.system-confirm-btn {
    width: 100% !important;
    min-height: 42px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 14px !important;

    border-radius: 9px !important;

    font-size: .72rem !important;
    font-weight: 750 !important;

    box-shadow: none !important;
}


/* CANCELAR */

.system-confirm-cancel {
    border: 1px solid var(--border-soft) !important;

    background: var(--card-bg) !important;

    color: var(--text-main) !important;
}

.system-confirm-cancel:hover {
    background: var(--light-bg) !important;
    color: var(--text-main) !important;
}


/* ACCION PRINCIPAL */

.system-confirm-accept {
    border: 1px solid var(--sys-color) !important;

    background: var(--sys-color) !important;

    color: #ffffff !important;

    box-shadow:
        0 7px 16px
        color-mix(
            in srgb,
            var(--sys-color) 18%,
            transparent
        ) !important;
}

.system-confirm-accept:hover,
.system-confirm-accept:focus {
    border-color:
        color-mix(
            in srgb,
            var(--sys-color) 84%,
            black
        ) !important;

    background:
        color-mix(
            in srgb,
            var(--sys-color) 84%,
            black
        ) !important;

    color: #ffffff !important;

    transform: translateY(-1px);
}


/* BACKDROP */

.modal-backdrop.show {
    opacity: .42;
}


/* ============================================================
   MODO OSCURO
   ============================================================ */

html[data-color-mode="dark"]
.system-confirm-modal .modal-content,

html[data-color-mode="dark"]
.system-confirm-body {
    background: #132338 !important;
}

html[data-color-mode="dark"]
.system-confirm-footer {
    background: #17283d !important;
    border-color: #30465d !important;
}

html[data-color-mode="dark"]
.system-confirm-close,

html[data-color-mode="dark"]
.system-confirm-cancel {
    background: #17283d !important;
    border-color: #36516d !important;
}

html[data-color-mode="dark"]
.system-confirm-icon {
    background:
        color-mix(
            in srgb,
            var(--sys-color) 14%,
            #132338
        ) !important;

    border-color:
        color-mix(
            in srgb,
            var(--sys-color) 38%,
            #30465d
        ) !important;
}


/* MOVIL */

@media (max-width: 575.98px) {

    .system-confirm-modal .modal-dialog {
        width: calc(100% - 24px) !important;
    }

    .system-confirm-body {
        padding: 28px 20px 21px !important;
    }

    .system-confirm-footer {
        grid-template-columns: 1fr !important;
        padding: 13px 16px 16px !important;
    }

    .system-confirm-accept {
        grid-row: 1;
    }

    .system-confirm-cancel {
        grid-row: 2;
    }
}
</style>

<style id="system-confirm-reference-finish">
/* ============================================================
   SYSTEM CONFIRM - ACABADO DE REFERENCIA
   Degradado superior + boton X
   ============================================================ */

/* El cuerpo recibe el degradado segun el color de la accion */
.system-confirm-modal .system-confirm-body {
    background:
        radial-gradient(
            ellipse 85% 55% at 50% 0%,
            color-mix(
                in srgb,
                var(--sys-color) 9%,
                var(--card-bg)
            ) 0%,
            color-mix(
                in srgb,
                var(--sys-color) 4%,
                var(--card-bg)
            ) 42%,
            transparent 72%
        ),
        var(--card-bg) !important;
}


/* Franja superior */
.system-confirm-modal .system-confirm-strip {
    height: 4px !important;

    background:
        linear-gradient(
            90deg,
            var(--sys-color) 0%,
            color-mix(
                in srgb,
                var(--sys-color) 70%,
                white
            ) 100%
        ) !important;
}


/* X exactamente como control independiente */
.system-confirm-modal .system-confirm-close {
    position: absolute !important;

    top: 18px !important;
    right: 16px !important;
    z-index: 5 !important;

    width: 38px !important;
    height: 38px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    margin: 0 !important;
    padding: 0 !important;

    border:
        1px solid
        color-mix(
            in srgb,
            var(--text-muted) 38%,
            var(--border-soft)
        ) !important;

    border-radius: 11px !important;

    background:
        color-mix(
            in srgb,
            var(--light-bg) 68%,
            var(--card-bg)
        ) !important;

    color: var(--text-muted) !important;

    font-size: 1rem !important;
    line-height: 1 !important;

    opacity: 1 !important;

    box-shadow: none !important;

    transition:
        background-color .2s ease,
        border-color .2s ease,
        color .2s ease,
        transform .2s ease !important;
}

.system-confirm-modal .system-confirm-close i {
    display: block !important;
    margin: 0 !important;

    color: inherit !important;

    font-size: 1rem !important;
    line-height: 1 !important;
}


/* Hover de la X */
.system-confirm-modal .system-confirm-close:hover {
    border-color:
        color-mix(
            in srgb,
            var(--sys-color) 38%,
            var(--border-soft)
        ) !important;

    background:
        color-mix(
            in srgb,
            var(--sys-color) 8%,
            var(--card-bg)
        ) !important;

    color: var(--sys-color) !important;

    transform: translateY(-1px);
}


/* ============================================================
   MODO OSCURO
   ============================================================ */

html[data-color-mode="dark"]
.system-confirm-modal .system-confirm-body {
    background:
        linear-gradient(
            180deg,
            color-mix(
                in srgb,
                var(--sys-color) 16%,
                #132338
            ) 0%,
            color-mix(
                in srgb,
                var(--sys-color) 9%,
                #132338
            ) 42%,
            color-mix(
                in srgb,
                var(--sys-color) 3%,
                #132338
            ) 76%,
            #132338 100%
        ) !important;
}

html[data-color-mode="dark"]
.system-confirm-modal .system-confirm-close {
    background: #17283d !important;
    border-color: #46617c !important;
    color: #9fb1c5 !important;
}

html[data-color-mode="dark"]
.system-confirm-modal .system-confirm-close:hover {
    background:
        color-mix(
            in srgb,
            var(--sys-color) 12%,
            #17283d
        ) !important;

    border-color:
        color-mix(
            in srgb,
            var(--sys-color) 42%,
            #46617c
        ) !important;

    color:
        color-mix(
            in srgb,
            var(--sys-color) 78%,
            white
        ) !important;
}
</style>
<style id="system-notifications-style">
:root {
    --sys-danger: #ef3340;
    --sys-success: #16a34a;
    --sys-warning: #f59e0b;
    --sys-info: #1683c7;
}

.system-confirm-modal {
    --sys-color: var(--primary, #ff8c00);
    --sys-soft: color-mix(in srgb, var(--sys-color) 10%, white);
    --sys-border: color-mix(in srgb, var(--sys-color) 28%, white);
}

.system-confirm-modal.type-danger {
    --sys-color: var(--sys-danger);
}

.system-confirm-modal.type-success {
    --sys-color: var(--sys-success);
}

.system-confirm-modal.type-warning {
    --sys-color: var(--sys-warning);
}

.system-confirm-modal.type-info {
    --sys-color: var(--sys-info);
}

.system-confirm-modal .modal-dialog {
    max-width: 550px;
}

.system-confirm-modal .modal-content {
    position: relative;
    overflow: hidden;
    border: 1px solid var(--border-soft, #dbe4ee);
    border-radius: 22px;
    background: var(--card-bg, #fff);
    color: var(--text-main, #0f172a);
    box-shadow: 0 24px 70px rgba(15, 23, 42, .22);
}

.system-confirm-strip {
    height: 5px;
    background: linear-gradient(
        90deg,
        var(--sys-color),
        color-mix(in srgb, var(--sys-color) 50%, white)
    );
}

.system-confirm-close {
    position: absolute;
    top: 19px;
    right: 16px;
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border-soft, #dbe4ee);
    border-radius: 13px;
    background: var(--light-bg, #f8fafc);
    color: var(--text-muted, #64748b);
    font-size: 1.25rem;
    transition: .2s ease;
}

.system-confirm-close:hover {
    color: var(--sys-color);
    border-color: color-mix(in srgb, var(--sys-color) 45%, transparent);
    background: var(--sys-soft);
}

.system-confirm-body {
    padding: 29px 36px 25px;
    text-align: center;
}

.system-confirm-icon {
    width: 84px;
    height: 84px;
    margin: 0 auto 17px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 25px;
    background: var(--sys-soft);
    border: 5px solid var(--sys-border);
    color: var(--sys-color);
    font-size: 2rem;
}

.system-confirm-title {
    margin: 0 45px 12px;
    color: var(--text-main, #0f172a);
    font-size: 1.35rem;
    font-weight: 800;
}

.system-confirm-text {
    max-width: 440px;
    margin: 0 auto;
    color: var(--text-muted, #64748b);
    font-size: .96rem;
    line-height: 1.65;
}

.system-confirm-footer {
    display: flex;
    gap: 12px;
    padding: 19px 25px 23px;
    border-top: 1px solid var(--border-soft, #e2e8f0);
}

.system-confirm-btn {
    min-height: 49px;
    flex: 1;
    border-radius: 12px;
    font-weight: 750;
    transition: .2s ease;
}

.system-confirm-cancel {
    border: 1px solid var(--border-soft, #cbd5e1);
    background: var(--light-bg, #f8fafc);
    color: var(--text-main, #0f172a);
}

.system-confirm-cancel:hover {
    border-color: #94a3b8;
    transform: translateY(-1px);
}

.system-confirm-accept {
    border: 1px solid var(--sys-color);
    background: var(--sys-color);
    color: #fff;
}

.system-confirm-accept:hover {
    background: color-mix(in srgb, var(--sys-color) 86%, black);
    border-color: color-mix(in srgb, var(--sys-color) 86%, black);
    color: #fff;
    transform: translateY(-1px);
}


/* TOAST PROPIO DEL SISTEMA */

#systemToastContainer {
    position: fixed;
    top: 82px;
    right: 22px;
    z-index: 10950;
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: min(380px, calc(100vw - 30px));
    pointer-events: none;
}

.system-toast {
    --sys-color: var(--primary, #ff8c00);
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px;
    border: 1px solid color-mix(in srgb, var(--sys-color) 30%, var(--border-soft, #e2e8f0));
    border-left: 4px solid var(--sys-color);
    border-radius: 14px;
    background: var(--card-bg, #fff);
    color: var(--text-main, #0f172a);
    box-shadow: 0 15px 40px rgba(15, 23, 42, .16);
    pointer-events: auto;
    animation: systemToastIn .28s ease both;
}

.system-toast.type-success { --sys-color: var(--sys-success); }
.system-toast.type-danger  { --sys-color: var(--sys-danger); }
.system-toast.type-warning { --sys-color: var(--sys-warning); }
.system-toast.type-info    { --sys-color: var(--sys-info); }

.system-toast-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: var(--sys-color);
    background: color-mix(in srgb, var(--sys-color) 12%, var(--card-bg, white));
    font-size: 1.05rem;
}

.system-toast-content {
    min-width: 0;
    flex: 1;
}

.system-toast-title {
    margin-bottom: 2px;
    font-size: .84rem;
    font-weight: 800;
}

.system-toast-text {
    color: var(--text-muted, #64748b);
    font-size: .78rem;
    line-height: 1.45;
}

.system-toast-close {
    border: 0;
    background: transparent;
    color: var(--text-muted, #64748b);
    padding: 1px 2px;
}

.system-toast.system-toast-out {
    animation: systemToastOut .22s ease both;
}

@keyframes systemToastIn {
    from {
        opacity: 0;
        transform: translateX(25px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes systemToastOut {
    to {
        opacity: 0;
        transform: translateX(25px);
    }
}


/* MODO OSCURO */

html[data-color-mode="dark"] .system-confirm-modal {
    --sys-soft: color-mix(in srgb, var(--sys-color) 14%, #132338);
    --sys-border: color-mix(in srgb, var(--sys-color) 35%, #30465d);
}

html[data-color-mode="dark"] .system-confirm-modal .modal-content {
    background: #132338 !important;
    border-color: #30465d !important;
}

html[data-color-mode="dark"] .system-confirm-title {
    color: #f8fafc !important;
}

html[data-color-mode="dark"] .system-confirm-text {
    color: #9fb1c5 !important;
}

html[data-color-mode="dark"] .system-confirm-close,
html[data-color-mode="dark"] .system-confirm-cancel {
    background: #17283d !important;
    border-color: #36516d !important;
    color: #d8e4ef !important;
}

html[data-color-mode="dark"] .system-confirm-footer {
    border-color: #30465d !important;
}

html[data-color-mode="dark"] .system-toast {
    background: #132338 !important;
    border-top-color: #30465d;
    border-right-color: #30465d;
    border-bottom-color: #30465d;
}

html[data-color-mode="dark"] .system-toast-title {
    color: #f8fafc;
}

html[data-color-mode="dark"] .system-toast-text {
    color: #9fb1c5;
}

@media (max-width: 576px) {
    .system-confirm-modal .modal-dialog {
        margin: 12px;
    }

    .system-confirm-body {
        padding: 28px 22px 22px;
    }

    .system-confirm-footer {
        padding: 16px;
    }

    #systemToastContainer {
        top: 70px;
        right: 15px;
        left: 15px;
        width: auto;
    }
}
</style>


<div class="modal fade system-confirm-modal"
     id="systemConfirmModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="system-confirm-strip"></div>

            <button type="button"
                    class="system-confirm-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="system-confirm-body">

                <div class="system-confirm-icon">
                    <i id="systemConfirmIcon" class="bi bi-question-lg"></i>
                </div>

                <h5 class="system-confirm-title"
                    id="systemConfirmTitle">
                    Confirmar acción
                </h5>

                <p class="system-confirm-text"
                   id="systemConfirmText">
                    ¿Deseas continuar con esta acción?
                </p>

            </div>

            <div class="system-confirm-footer">

                <button type="button"
                        class="btn system-confirm-btn system-confirm-cancel"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button"
                        class="btn system-confirm-btn system-confirm-accept"
                        id="systemConfirmAccept">
                    Confirmar
                </button>

            </div>

        </div>
    </div>
</div>

<div id="systemToastContainer"
     aria-live="polite"
     aria-atomic="true">
</div>


<script>
window.openSystemLogoutConfirm = function () {

    const logoutForm = document.getElementById('logoutForm');

    if (!logoutForm) {
        return;
    }

    SystemNotify.confirm({
        type: 'danger',
        title: 'Cerrar sesión',
        text: 'Tu sesión actual finalizará y tendrás que iniciar sesión nuevamente.',
        confirmText: 'Cerrar sesión',
        icon: 'bi-box-arrow-right',
        onConfirm: function () {
            logoutForm.submit();
        }
    });
};
</script>
<script id="system-notifications-script">
(function () {

    const icons = {
        danger:  'bi-trash3',
        success: 'bi-check-lg',
        warning: 'bi-exclamation-triangle',
        info:    'bi-info-lg',
        primary: 'bi-question-lg'
    };

    const toastIcons = {
        danger:  'bi-x-lg',
        success: 'bi-check-lg',
        warning: 'bi-exclamation-lg',
        info:    'bi-info-lg',
        primary: 'bi-bell'
    };

    let confirmCallback = null;

    function getModalElement() {
        return document.getElementById('systemConfirmModal');
    }

    function getModalInstance() {
        return bootstrap.Modal.getOrCreateInstance(getModalElement());
    }

    window.SystemNotify = {

        confirm(options = {}) {

            const modal = getModalElement();
            const type = options.type || 'primary';

            modal.classList.remove(
                'type-danger',
                'type-success',
                'type-warning',
                'type-info',
                'type-primary'
            );

            modal.classList.add('type-' + type);

            document.getElementById('systemConfirmTitle').textContent =
                options.title || 'Confirmar acción';

            document.getElementById('systemConfirmText').textContent =
                options.text || '¿Deseas continuar con esta acción?';

            document.getElementById('systemConfirmAccept').textContent =
                options.confirmText || 'Confirmar';

            document.getElementById('systemConfirmIcon').className =
                'bi ' + (options.icon || icons[type] || icons.primary);

            confirmCallback =
                typeof options.onConfirm === 'function'
                    ? options.onConfirm
                    : null;

            getModalInstance().show();
        },

        toast(message, type = 'info', title = null, duration = 4000) {

            const container =
                document.getElementById('systemToastContainer');

            if (!container) return;

            const titles = {
                success: 'Operación realizada',
                danger: 'Ocurrió un problema',
                warning: 'Atención',
                info: 'Información',
                primary: 'Notificación'
            };

            const toast = document.createElement('div');
            toast.className = 'system-toast type-' + type;

            const icon = document.createElement('div');
            icon.className = 'system-toast-icon';
            icon.innerHTML =
                '<i class="bi ' +
                (toastIcons[type] || toastIcons.info) +
                '"></i>';

            const content = document.createElement('div');
            content.className = 'system-toast-content';

            const toastTitle = document.createElement('div');
            toastTitle.className = 'system-toast-title';
            toastTitle.textContent =
                title || titles[type] || titles.info;

            const text = document.createElement('div');
            text.className = 'system-toast-text';
            text.textContent = message;

            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'system-toast-close';
            close.innerHTML = '<i class="bi bi-x-lg"></i>';

            content.appendChild(toastTitle);
            content.appendChild(text);

            toast.appendChild(icon);
            toast.appendChild(content);
            toast.appendChild(close);

            container.appendChild(toast);

            let timer;

            const removeToast = () => {
                if (!toast.isConnected) return;

                toast.classList.add('system-toast-out');

                setTimeout(() => toast.remove(), 220);
            };

            close.addEventListener('click', removeToast);

            if (duration > 0) {
                timer = setTimeout(removeToast, duration);

                toast.addEventListener('mouseenter', () => {
                    clearTimeout(timer);
                });

                toast.addEventListener('mouseleave', () => {
                    timer = setTimeout(removeToast, 1500);
                });
            }
        },

        success(message, title = null) {
            this.toast(message, 'success', title);
        },

        error(message, title = null) {
            this.toast(message, 'danger', title);
        },

        warning(message, title = null) {
            this.toast(message, 'warning', title);
        },

        info(message, title = null) {
            this.toast(message, 'info', title);
        }
    };


    document.addEventListener('DOMContentLoaded', function () {

        const accept =
            document.getElementById('systemConfirmAccept');

        const modal =
            document.getElementById('systemConfirmModal');

        if (!accept || !modal) return;

        accept.addEventListener('click', function () {

            const callback = confirmCallback;
            confirmCallback = null;

            getModalInstance().hide();

            if (callback) {
                setTimeout(callback, 150);
            }
        });

        modal.addEventListener('hidden.bs.modal', function () {
            confirmCallback = null;
        });

    });

})();
</script>

<!-- FIN SISTEMA GLOBAL DE NOTIFICACIONES -->


{{-- =========================================================
     NOTIFICACIONES FLASH DEL SISTEMA
========================================================= --}}
<script id="system-flash-notifications">
document.addEventListener('DOMContentLoaded', function () {

    if (!window.SystemNotify) {
        console.error('SystemNotify no está disponible.');
        return;
    }

    @if(session('success'))
        SystemNotify.success(
            @json(session('success')),
            'Operación realizada'
        );
    @endif

    @if(session('error'))
        SystemNotify.error(
            @json(session('error')),
            'Ocurrió un problema'
        );
    @endif

    @if(session('warning'))
        SystemNotify.warning(
            @json(session('warning')),
            'Atención'
        );
    @endif

    @if(session('info'))
        SystemNotify.info(
            @json(session('info')),
            'Información'
        );
    @endif

});
</script>

<style id="global-file-clear-controls">
/* =========================================================
   SELECTORES DE ARCHIVO - BOTÓN GLOBAL PARA QUITAR ARCHIVO
========================================================= */

.system-file-input-wrapper {
    position: relative;
    width: 100%;
}

.system-file-input-wrapper > input[type="file"] {
    width: 100%;
}

.system-file-clear {
    position: absolute;
    top: 50%;
    right: 8px;
    transform: translateY(-50%);

    width: 34px;
    height: 34px;

    padding: 0;

    border:
        1px solid
        color-mix(
            in srgb,
            #dc2626 30%,
            var(--border-soft)
        );

    border-radius: 9px;

    background:
        color-mix(
            in srgb,
            #dc2626 5%,
            var(--card-bg)
        );

    color: #dc2626;

    display: none;
    align-items: center;
    justify-content: center;

    cursor: pointer;
    z-index: 6;

    transition:
        background .18s ease,
        border-color .18s ease,
        transform .18s ease,
        box-shadow .18s ease;
}

.system-file-input-wrapper.has-file
.system-file-clear {
    display: flex;
}

.system-file-clear:hover {
    background:
        color-mix(
            in srgb,
            #dc2626 11%,
            var(--card-bg)
        );

    border-color:
        color-mix(
            in srgb,
            #dc2626 55%,
            var(--border-soft)
        );

    box-shadow:
        0 4px 12px
        rgba(220, 38, 38, .12);
}

.system-file-clear:active {
    transform:
        translateY(-50%)
        scale(.94);
}

.system-file-clear i {
    font-size: 15px;
    line-height: 1;
}


/* Cuando hay archivo dejamos espacio para la X */
.system-file-input-wrapper.has-file
> input[type="file"] {
    padding-right: 52px;
}


/* MODO OSCURO */
html[data-color-mode="dark"]
.system-file-clear {
    background:
        color-mix(
            in srgb,
            #ef4444 8%,
            var(--card-bg)
        );

    border-color:
        color-mix(
            in srgb,
            #ef4444 32%,
            var(--border-soft)
        );

    color: #f87171;
}

html[data-color-mode="dark"]
.system-file-clear:hover {
    background:
        color-mix(
            in srgb,
            #ef4444 15%,
            var(--card-bg)
        );

    border-color: #ef4444;
}
</style>

<script id="global-file-clear-script">
document.addEventListener('DOMContentLoaded', function () {

    const fileInputs =
        document.querySelectorAll('input[type="file"]');

    fileInputs.forEach(function (input) {

        /*
         * No modificar campos ocultos.
         * Tampoco modificar Restaurar Sistema porque
         * ya tiene su propio control personalizado.
         */
        if (
            input.id === 'restoreRealFile' ||
            input.id === 'restoreBackupFile' ||
            input.closest('.d-none') ||
            input.type !== 'file'
        ) {
            return;
        }

        /*
         * Evitar procesar dos veces el mismo input.
         */
        if (input.dataset.systemFileClear === 'true') {
            return;
        }

        input.dataset.systemFileClear = 'true';

        /*
         * Crear contenedor sin modificar atributos,
         * name, accept, required, id ni eventos existentes.
         */
        const wrapper = document.createElement('div');

        wrapper.className =
            'system-file-input-wrapper';

        input.parentNode.insertBefore(
            wrapper,
            input
        );

        wrapper.appendChild(input);

        /*
         * Crear botón X.
         */
        const clearButton =
            document.createElement('button');

        clearButton.type = 'button';

        clearButton.className =
            'system-file-clear';

        clearButton.title =
            'Quitar archivo seleccionado';

        clearButton.setAttribute(
            'aria-label',
            'Quitar archivo seleccionado'
        );

        clearButton.innerHTML =
            '<i class="bi bi-x-lg"></i>';

        wrapper.appendChild(clearButton);


        /*
         * Mostrar la X únicamente cuando realmente
         * exista un archivo seleccionado.
         */
        const updateState = function () {

            const hasFile =
                input.files &&
                input.files.length > 0;

            wrapper.classList.toggle(
                'has-file',
                hasFile
            );
        };


        /*
         * Cuando el usuario selecciona archivo.
         */
        input.addEventListener(
            'change',
            updateState
        );


        /*
         * Quitar archivo.
         */
        clearButton.addEventListener(
            'click',
            function () {

                input.value = '';

                /*
                 * Disparar change para que cualquier
                 * preview o lógica propia del módulo
                 * también pueda reaccionar.
                 */
                input.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true
                        }
                    )
                );

                updateState();

                input.focus();
            }
        );

        updateState();
    });

});
</script>
<script id="waiter-ready-notifications-script">
(function () {

    const readyItemsUrl = @json(route('pos.ready-items'));

    let knownReadyIds = new Set();
    let firstReadyCheck = true;
    let readyRequestRunning = false;

    let readyAudioContext = null;
    let readyAudioUnlocked = false;


    /* ========================================================
       AUDIO DEL USUARIO RESPONSABLE DEL PEDIDO
       ======================================================== */

    function createReadyAudioContext() {

        if (readyAudioContext) {
            return readyAudioContext;
        }

        const AudioContextClass =
            window.AudioContext ||
            window.webkitAudioContext;

        if (!AudioContextClass) {
            console.warn(
                'Este navegador no soporta notificaciones de audio.'
            );

            return null;
        }

        readyAudioContext =
            new AudioContextClass();

        return readyAudioContext;
    }


    async function unlockReadyAudio() {

        const ctx =
            createReadyAudioContext();

        if (!ctx) {
            return;
        }

        try {

            if (ctx.state === 'suspended') {
                await ctx.resume();
            }

            const oscillator =
                ctx.createOscillator();

            const gain =
                ctx.createGain();

            gain.gain.value = 0.00001;

            oscillator.connect(gain);
            gain.connect(ctx.destination);

            oscillator.start();

            oscillator.stop(
                ctx.currentTime + 0.01
            );

            readyAudioUnlocked =
                ctx.state === 'running';

            if (readyAudioUnlocked) {
                console.log(
                    '🔊 Avisos de pedidos listos habilitados'
                );
            }

        } catch (error) {

            console.warn(
                'No se pudo habilitar el sonido de pedidos listos:',
                error
            );
        }
    }


    document.addEventListener(
        'pointerdown',
        unlockReadyAudio,
        { once: true }
    );

    document.addEventListener(
        'keydown',
        unlockReadyAudio,
        { once: true }
    );


    /* ========================================================
       SONIDO "PEDIDO LISTO"
       Distinto a la campana de Cocina / Barra
       ======================================================== */

    function playReadySound() {

        if (window.systemSoundsEnabled !== true) {
            return;
        }

        if (
            !readyAudioContext ||
            !readyAudioUnlocked ||
            readyAudioContext.state !== 'running'
        ) {
            console.warn(
                '🔇 Aviso de pedido listo pendiente de habilitación.'
            );

            return;
        }

        const ctx = readyAudioContext;
        const now = ctx.currentTime;

        /*
         * Dos notas ascendentes.
         * Se diferencia claramente de la campana de preparación.
         */
        const notes = [
            {
                frequency: 659.25,
                start: 0,
                duration: 0.55,
                volume: 0.28
            },
            {
                frequency: 987.77,
                start: 0.32,
                duration: 1.15,
                volume: 0.34
            }
        ];

        notes.forEach(function (note) {

            const oscillator =
                ctx.createOscillator();

            const gain =
                ctx.createGain();

            const noteStart =
                now + note.start;

            const noteEnd =
                noteStart + note.duration;

            oscillator.type = 'sine';

            oscillator.frequency.setValueAtTime(
                note.frequency,
                noteStart
            );

            gain.gain.setValueAtTime(
                0.0001,
                noteStart
            );

            gain.gain.exponentialRampToValueAtTime(
                note.volume,
                noteStart + 0.015
            );

            gain.gain.exponentialRampToValueAtTime(
                0.0001,
                noteEnd
            );

            oscillator.connect(gain);
            gain.connect(ctx.destination);

            oscillator.start(noteStart);
            oscillator.stop(noteEnd + 0.05);
        });
    }


    /* ========================================================
       NOTIFICACIÓN VISUAL
       ======================================================== */

    function showReadyNotification(items) {

        if (!items.length) {
            return;
        }

        playReadySound();

        if (!window.SystemNotify) {
            return;
        }

        if (items.length === 1) {

            const item = items[0];

            SystemNotify.success(
                item.table +
                ' · ' +
                item.product +
                ' x' +
                item.quantity +
                ' · ' +
                item.area_name,
                'Pedido listo',
                6500
            );

            return;
        }

        const tables = [
            ...new Set(
                items.map(function (item) {
                    return item.table;
                })
            )
        ];

        let message =
            items.length +
            ' productos están listos para recoger';

        if (tables.length === 1) {
            message += ' · ' + tables[0];
        } else {
            message += ' · ' +
                tables.length +
                ' mesas';
        }

        SystemNotify.success(
            message,
            'Pedidos listos',
            7000
        );
    }


    /* ========================================================
       CONSULTAR PRODUCTOS SERVED DEL USUARIO ACTUAL
       ======================================================== */

    async function refreshReadyItems() {

        if (readyRequestRunning) {
            return;
        }

        readyRequestRunning = true;

        try {

            const response =
                await fetch(
                    readyItemsUrl,
                    {
                        headers: {
                            'Accept':
                                'application/json',
                            'X-Requested-With':
                                'XMLHttpRequest'
                        },
                        credentials:
                            'same-origin',
                        cache:
                            'no-store'
                    }
                );

            if (!response.ok) {

                /*
                 * Si la sesión terminó, no llenamos
                 * la consola de errores cada 2 segundos.
                 */
                if (
                    response.status === 401 ||
                    response.status === 419
                ) {
                    return;
                }

                throw new Error(
                    'HTTP ' + response.status
                );
            }

            const data =
                await response.json();

            const items =
                Array.isArray(data.items)
                    ? data.items
                    : [];

            const currentReadyIds =
                new Set(
                    items.map(function (item) {
                        return Number(item.id);
                    })
                );


            /*
             * Primera consulta:
             * solo memoriza lo que ya estaba listo.
             * No genera sonidos ni notificaciones.
             */
            if (firstReadyCheck) {

                knownReadyIds =
                    currentReadyIds;

                firstReadyCheck =
                    false;

                return;
            }


            const newReadyItems =
                items.filter(function (item) {

                    return !knownReadyIds.has(
                        Number(item.id)
                    );
                });


            if (newReadyItems.length > 0) {

                console.log(
                    '🔔 Producto listo para recoger:',
                    newReadyItems
                );

                showReadyNotification(
                    newReadyItems
                );
            }


            knownReadyIds =
                currentReadyIds;

        } catch (error) {

            console.error(
                'No se pudieron actualizar los pedidos listos:',
                error
            );

        } finally {

            readyRequestRunning =
                false;
        }
    }


    /* Primera consulta inmediata */
    refreshReadyItems();

    /* Después, cada 2 segundos */
    setInterval(
        refreshReadyItems,
        2000
    );

})();
</script>
<script id="system-sound-toggle-script">
(function () {

    const STORAGE_KEY =
        'restaurant-system-sounds';

    /*
     * La preferencia permanece entre:
     * - módulos
     * - recargas
     * - navegación del sistema
     */
    window.systemSoundsEnabled =
        localStorage.getItem(STORAGE_KEY) === 'enabled';


    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const button =
                document.getElementById('systemSoundToggle');

            const icon =
                document.getElementById('systemSoundIcon');

            const text =
                document.getElementById('systemSoundText');

            if (!button || !icon || !text) {
                return;
            }


            function renderSoundState() {

                const enabled =
                    window.systemSoundsEnabled === true;

                button.classList.toggle(
                    'is-active',
                    enabled
                );

                button.setAttribute(
                    'aria-pressed',
                    enabled ? 'true' : 'false'
                );

                if (enabled) {

                    button.title =
                        'Desactivar sonidos';

                    button.setAttribute(
                        'aria-label',
                        'Desactivar sonidos'
                    );

                    icon.className =
                        'bi bi-volume-up-fill';

                    text.textContent =
                        'Sonidos activados';

                } else {

                    button.title =
                        'Activar sonidos';

                    button.setAttribute(
                        'aria-label',
                        'Activar sonidos'
                    );

                    icon.className =
                        'bi bi-volume-mute-fill';

                    text.textContent =
                        'Sonidos desactivados';
                }
            }


            button.addEventListener(
                'click',
                function () {

                    window.systemSoundsEnabled =
                        !window.systemSoundsEnabled;

                    localStorage.setItem(
                        STORAGE_KEY,
                        window.systemSoundsEnabled
                            ? 'enabled'
                            : 'disabled'
                    );

                    renderSoundState();

                    if (window.systemSoundsEnabled) {

                        console.log(
                            '🔊 Sonidos del sistema activados'
                        );

                    } else {

                        console.log(
                            '🔇 Sonidos del sistema desactivados'
                        );
                    }
                }
            );


            /*
             * Al entrar a otro módulo o actualizar:
             *
             * Si el usuario ya había elegido tener sonidos,
             * conservamos esa preferencia.
             *
             * La primera interacción normal con la página
             * permitirá que Chrome reanude los AudioContext
             * correspondientes.
             */
            if (window.systemSoundsEnabled) {

                const restoreAudio = function () {

                    document.dispatchEvent(
                        new CustomEvent(
                            'system-audio-restore'
                        )
                    );
                };

                document.addEventListener(
                    'pointerdown',
                    restoreAudio,
                    {
                        once: true,
                        capture: true
                    }
                );

                document.addEventListener(
                    'keydown',
                    restoreAudio,
                    {
                        once: true,
                        capture: true
                    }
                );
            }


            renderSoundState();
        }
    );

})();
</script>
@stack('scripts')


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const passwordInput =
            document.getElementById('profilePassword');

        const toggleButton =
            document.getElementById('profilePasswordToggle');

        const toggleIcon =
            document.getElementById('profilePasswordIcon');

        if (!passwordInput || !toggleButton || !toggleIcon) {
            return;
        }

        toggleButton.addEventListener('click', function () {

            const showing =
                passwordInput.type === 'text';

            passwordInput.type =
                showing ? 'password' : 'text';

            toggleIcon.classList.toggle(
                'bi-eye',
                showing
            );

            toggleIcon.classList.toggle(
                'bi-eye-slash',
                !showing
            );

            toggleButton.setAttribute(
                'aria-label',
                showing
                    ? 'Mostrar contraseña'
                    : 'Ocultar contraseña'
            );

            toggleButton.setAttribute(
                'title',
                showing
                    ? 'Mostrar contraseña'
                    : 'Ocultar contraseña'
            );

            passwordInput.focus();
        });

    });
</script>



<script>
/* ============================================================
   TRANSICION DE TEMA - EL CAPITAN
============================================================ */

(function () {

    const STORAGE_KEY =
        'restaurant-color-mode';


    function getCurrentMode() {

        return document.documentElement
            .getAttribute('data-color-mode') === 'dark'
                ? 'dark'
                : 'light';
    }


    function updateThemeButton() {

        const button =
            document.getElementById('systemThemeToggle');

        const icon =
            document.getElementById('systemThemeIcon');

        if (!button || !icon) {
            return;
        }

        const dark =
            getCurrentMode() === 'dark';


        icon.className =
            dark
                ? 'bi bi-sun-fill'
                : 'bi bi-moon-stars-fill';


        const text =
            dark
                ? 'Activar modo claro'
                : 'Activar modo oscuro';


        button.setAttribute(
            'aria-label',
            text
        );

        button.setAttribute(
            'title',
            dark
                ? 'Modo claro'
                : 'Modo oscuro'
        );
    }


    function applyTheme(mode) {

        if (mode === 'dark') {

            document.documentElement
                .setAttribute(
                    'data-color-mode',
                    'dark'
                );

        } else {

            document.documentElement
                .removeAttribute(
                    'data-color-mode'
                );
        }


        try {

            localStorage.setItem(
                STORAGE_KEY,
                mode
            );

        } catch (error) {
            /* localStorage no disponible */
        }


        updateThemeButton();
    }


    function toggleTheme(event) {

        const current =
            getCurrentMode();

        const next =
            current === 'dark'
                ? 'light'
                : 'dark';


        const button =
            event.currentTarget;


        /*
         * Si el navegador no soporta View Transition,
         * cambia el tema normalmente.
         */

        if (
            !document.startViewTransition ||
            window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches
        ) {

            applyTheme(next);
            return;
        }


        /*
         * Centro de la expansión:
         * exactamente desde el botón.
         */

        const rect =
            button.getBoundingClientRect();

        const x =
            rect.left +
            rect.width / 2;

        const y =
            rect.top +
            rect.height / 2;


        /*
         * Radio necesario para cubrir toda la pantalla.
         */

        const maxX =
            Math.max(
                x,
                window.innerWidth - x
            );

        const maxY =
            Math.max(
                y,
                window.innerHeight - y
            );

        const radius =
            Math.hypot(
                maxX,
                maxY
            );


        const root =
            document.documentElement;


        root.classList.add(
            next === 'dark'
                ? 'theme-transition-to-dark'
                : 'theme-transition-to-light'
        );


        const transition =
            document.startViewTransition(
                function () {
                    applyTheme(next);
                }
            );


        transition.ready.then(
            function () {

                const goingDark =
                    next === 'dark';


                document.documentElement.animate(
                    {
                        clipPath:
                            goingDark
                                ? [
                                    `circle(0px at ${x}px ${y}px)`,
                                    `circle(${radius}px at ${x}px ${y}px)`
                                  ]
                                : [
                                    `circle(${radius}px at ${x}px ${y}px)`,
                                    `circle(0px at ${x}px ${y}px)`
                                  ]
                    },
                    {
                        duration:
                            620,

                        easing:
                            'cubic-bezier(.4, 0, .2, 1)',

                        pseudoElement:
                            goingDark
                                ? '::view-transition-new(root)'
                                : '::view-transition-old(root)'
                    }
                );

            }
        );


        transition.finished.finally(
            function () {

                root.classList.remove(
                    'theme-transition-to-dark',
                    'theme-transition-to-light'
                );
            }
        );
    }


    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const button =
                document.getElementById(
                    'systemThemeToggle'
                );


            /*
             * Recuperar preferencia.
             */

            let savedMode = null;

            try {

                savedMode =
                    localStorage.getItem(
                        STORAGE_KEY
                    );

            } catch (error) {
                /* localStorage no disponible */
            }


            if (
                savedMode === 'dark' ||
                savedMode === 'light'
            ) {

                applyTheme(savedMode);
            }


            updateThemeButton();


            if (button) {

                button.addEventListener(
                    'click',
                    toggleTheme
                );
            }

        }
    );

})();
</script>

<script id="sidebar-collapse-script">
document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('sidebarCollapseBtn');

    if (!button) {
        return;
    }

    const storageKey = 'restaurant_sidebar_collapsed';

    function applySidebarState(collapsed) {

        document.body.classList.toggle(
            'sidebar-collapsed',
            collapsed
        );

        button.title = collapsed
            ? 'Expandir menú'
            : 'Comprimir menú';

        button.setAttribute(
            'aria-label',
            collapsed
                ? 'Expandir menú'
                : 'Comprimir menú'
        );
    }


    if (window.innerWidth >= 992) {

        applySidebarState(
            localStorage.getItem(storageKey) === '1'
        );

    }


    button.addEventListener('click', function () {

        if (window.innerWidth < 992) {
            return;
        }

        const collapsed =
            !document.body.classList.contains(
                'sidebar-collapsed'
            );

        applySidebarState(collapsed);

        localStorage.setItem(
            storageKey,
            collapsed ? '1' : '0'
        );

    });


    window.addEventListener('resize', function () {

        if (window.innerWidth < 992) {

            document.body.classList.remove(
                'sidebar-collapsed'
            );

            return;
        }

        applySidebarState(
            localStorage.getItem(storageKey) === '1'
        );

    });

});
</script>

<script id="sidebar-groups-professional">
document.addEventListener('DOMContentLoaded', function () {

    const menu = document.querySelector('#sidebar .sidebar-menu');

    if (!menu) {
        return;
    }


    /* ======================================================
       1. CREAR GRUPOS DESPLEGABLES AUTOMÁTICAMENTE
       ====================================================== */

    const categories = Array.from(
        menu.querySelectorAll(':scope > .menu-category')
    );


    categories.forEach(function (category, index) {

        const title =
            category.textContent
                .replace(/\s+/g, ' ')
                .trim();

        if (!title) {
            return;
        }


        const elements = [];

        let sibling = category.nextElementSibling;


        while (
            sibling &&
            !sibling.classList.contains('menu-category')
        ) {

            elements.push(sibling);

            sibling = sibling.nextElementSibling;
        }


        if (!elements.length) {
            return;
        }


        const group =
            document.createElement('div');

        group.className = 'sidebar-group';


        const toggle =
            document.createElement('button');

        toggle.type = 'button';
        toggle.className = 'sidebar-group-toggle';

        toggle.setAttribute(
            'aria-expanded',
            'false'
        );


        const titleElement =
            document.createElement('span');

        titleElement.className =
            'sidebar-group-title';

        titleElement.textContent = title;


        const arrow =
            document.createElement('i');

        arrow.className =
            'bi bi-chevron-right sidebar-group-arrow';


        toggle.appendChild(titleElement);
        toggle.appendChild(arrow);


        const content =
            document.createElement('div');

        content.className =
            'sidebar-group-content';


        const inner =
            document.createElement('div');

        inner.className =
            'sidebar-group-content-inner';


        category.parentNode.insertBefore(
            group,
            category
        );


        group.appendChild(category);
        group.appendChild(toggle);
        group.appendChild(content);

        content.appendChild(inner);


        elements.forEach(function (element) {
            inner.appendChild(element);
        });


        const hasActive =
            !!inner.querySelector('.nav-link.active');


        /*
           Abrimos automáticamente:
           - el grupo donde está la página actual
           - Operaciones inicialmente si ninguno está activo
        */

        if (hasActive) {
            group.classList.add('open');

            toggle.setAttribute(
                'aria-expanded',
                'true'
            );
        }


        toggle.addEventListener(
            'click',
            function () {

                if (
                    document.body.classList.contains(
                        'sidebar-collapsed'
                    )
                ) {
                    return;
                }


                const willOpen =
                    !group.classList.contains('open');


                /*
                   Comportamiento tipo acordeón:
                   un grupo principal abierto a la vez.
                */

                menu
                    .querySelectorAll('.sidebar-group.open')
                    .forEach(function (other) {

                        if (other === group) {
                            return;
                        }

                        other.classList.remove('open');

                        const otherToggle =
                            other.querySelector(
                                '.sidebar-group-toggle'
                            );

                        if (otherToggle) {
                            otherToggle.setAttribute(
                                'aria-expanded',
                                'false'
                            );
                        }
                    });


                group.classList.toggle(
                    'open',
                    willOpen
                );

                toggle.setAttribute(
                    'aria-expanded',
                    willOpen
                        ? 'true'
                        : 'false'
                );
            }
        );

    });
    /*
       No abrir ningún grupo automáticamente.
       Solo permanece abierto el grupo que contiene
       la opción activa de la página actual.
    */
}


    /* ======================================================
       2. TOOLTIP PROFESIONAL PARA ICONOS
       ====================================================== */

    const tooltip =
        document.createElement('div');

    tooltip.className =
        'sidebar-icon-tooltip';

    document.body.appendChild(tooltip);


    let currentLink = null;


    function getLinkName(link) {

        const clone =
            link.cloneNode(true);

        clone
            .querySelectorAll('i, .badge, small')
            .forEach(function (element) {
                element.remove();
            });

        return clone.textContent
            .replace(/\s+/g, ' ')
            .trim();
    }


    function showTooltip(link) {

        if (
            window.innerWidth < 992 ||
            !document.body.classList.contains(
                'sidebar-collapsed'
            )
        ) {
            return;
        }


        const name =
            getLinkName(link);

        if (!name) {
            return;
        }


        currentLink = link;

        tooltip.textContent = name;


        const rect =
            link.getBoundingClientRect();


        tooltip.style.left =
            (rect.right + 12) + 'px';


        /*
           Primero posicionamos aproximadamente.
           Después corregimos usando su altura real.
        */

        tooltip.style.top =
            rect.top + 'px';


        tooltip.classList.add('show');


        requestAnimationFrame(function () {

            const tooltipRect =
                tooltip.getBoundingClientRect();

            let top =
                rect.top +
                (rect.height / 2) -
                (tooltipRect.height / 2);


            const padding = 8;


            if (top < padding) {
                top = padding;
            }


            if (
                top + tooltipRect.height >
                window.innerHeight - padding
            ) {
                top =
                    window.innerHeight -
                    tooltipRect.height -
                    padding;
            }


            tooltip.style.top =
                top + 'px';
        });

    }


    function hideTooltip() {

        currentLink = null;

        tooltip.classList.remove('show');
    }


    menu.addEventListener(
        'mouseover',
        function (event) {

            const link =
                event.target.closest('.nav-link');

            if (
                !link ||
                !menu.contains(link) ||
                link === currentLink
            ) {
                return;
            }

            showTooltip(link);
        }
    );


    menu.addEventListener(
        'mouseout',
        function (event) {

            const link =
                event.target.closest('.nav-link');

            if (!link) {
                return;
            }


            if (
                event.relatedTarget &&
                link.contains(event.relatedTarget)
            ) {
                return;
            }

            hideTooltip();
        }
    );


    menu.addEventListener(
        'scroll',
        hideTooltip
    );


    window.addEventListener(
        'resize',
        hideTooltip
    );

});
</script>

<script id="sidebarExpandAllControl">
document.addEventListener('DOMContentLoaded', function () {

    const menu =
        document.querySelector('#sidebar .sidebar-menu');

    if (!menu) {
        return;
    }


    const groups =
        Array.from(
            menu.querySelectorAll('.sidebar-group')
        );

    if (!groups.length) {
        return;
    }


    /* ===============================================
       CREAR CONTROL GENERAL
       =============================================== */

    const control =
        document.createElement('div');

    control.className =
        'sidebar-expand-all-control';


    const button =
        document.createElement('button');

    button.type = 'button';

    button.className =
        'sidebar-expand-all-btn';

    button.innerHTML = `
        <span class="sidebar-expand-all-left">

            <i class="bi bi-layout-sidebar-inset"></i>

            <span class="sidebar-expand-all-text">
                Desplegar todo
            </span>

        </span>

        <i class="
            bi
            bi-chevron-down
            sidebar-expand-all-arrow
        "></i>
    `;


    control.appendChild(button);

    menu.insertBefore(
        control,
        menu.firstChild
    );


    const text =
        button.querySelector(
            '.sidebar-expand-all-text'
        );


    /* ===============================================
       ACTUALIZAR TEXTO DEL BOTÓN
       =============================================== */

    function updateButton() {

        const allOpen =
            groups.every(function (group) {
                return group.classList.contains('open');
            });


        button.classList.toggle(
            'all-open',
            allOpen
        );


        text.textContent =
            allOpen
                ? 'Contraer todo'
                : 'Desplegar todo';


        button.title =
            allOpen
                ? 'Contraer todas las secciones'
                : 'Desplegar todas las secciones';

    }


    /* ===============================================
       ABRIR / CERRAR TODOS
       =============================================== */

    button.addEventListener(
        'click',
        function () {

            const allOpen =
                groups.every(function (group) {
                    return group.classList.contains('open');
                });


            const shouldOpen =
                !allOpen;


            groups.forEach(function (group) {

                group.classList.toggle(
                    'open',
                    shouldOpen
                );


                const toggle =
                    group.querySelector(
                        '.sidebar-group-toggle'
                    );


                if (toggle) {

                    toggle.setAttribute(
                        'aria-expanded',
                        shouldOpen
                            ? 'true'
                            : 'false'
                    );

                }

            });


            updateButton();

        }
    );


    /* ===============================================
       SI EL USUARIO ABRE/CIERRA UN GRUPO MANUALMENTE
       ACTUALIZAMOS EL CONTROL GENERAL
       =============================================== */

    groups.forEach(function (group) {

        const toggle =
            group.querySelector(
                '.sidebar-group-toggle'
            );


        if (!toggle) {
            return;
        }


        toggle.addEventListener(
            'click',
            function () {

                /*
                   El listener anterior del grupo se ejecuta
                   primero. Esperamos al siguiente ciclo para
                   leer su estado definitivo.
                */

                setTimeout(
                    updateButton,
                    0
                );

            }
        );

    });


    updateButton();

});
</script>

<script id="sidebar-tooltip-only-script">

document.addEventListener('DOMContentLoaded', function () {

    const sidebar =
        document.getElementById('sidebar');

    if (!sidebar) {
        return;
    }


    const menu =
        sidebar.querySelector('.sidebar-menu');

    if (!menu) {
        return;
    }


    /* Crear un único tooltip para todo el sidebar */

    const tooltip =
        document.createElement('div');

    tooltip.className =
        'sidebar-hover-tooltip';

    document.body.appendChild(tooltip);


    let currentLink = null;


    /* Obtener solamente el nombre del botón */

    function getLinkName(link) {

        const clone =
            link.cloneNode(true);

        clone
            .querySelectorAll(
                'i, .badge, small'
            )
            .forEach(function (element) {
                element.remove();
            });


        return clone.textContent
            .replace(/\s+/g, ' ')
            .trim();
    }


    function showTooltip(link) {

        /* Solo cuando el sidebar está comprimido */

        if (
            window.innerWidth < 992 ||
            !document.body.classList.contains(
                'sidebar-collapsed'
            )
        ) {
            return;
        }


        const name =
            getLinkName(link);


        if (!name) {
            return;
        }


        currentLink = link;

        tooltip.textContent = name;


        const rect =
            link.getBoundingClientRect();


        tooltip.style.left =
            (rect.right + 13) + 'px';

        tooltip.style.top =
            rect.top + 'px';


        tooltip.classList.add('show');


        requestAnimationFrame(function () {

            const tooltipRect =
                tooltip.getBoundingClientRect();


            let top =
                rect.top +
                (rect.height / 2) -
                (tooltipRect.height / 2);


            if (top < 8) {
                top = 8;
            }


            if (
                top + tooltipRect.height >
                window.innerHeight - 8
            ) {
                top =
                    window.innerHeight -
                    tooltipRect.height -
                    8;
            }


            tooltip.style.top =
                top + 'px';

        });

    }


    function hideTooltip() {

        currentLink = null;

        tooltip.classList.remove('show');

    }


    /* Detectar cualquier botón/enlace del menú */

    menu.addEventListener(
        'mouseover',
        function (event) {

            const link =
                event.target.closest('.nav-link');


            if (
                !link ||
                !menu.contains(link) ||
                link === currentLink
            ) {
                return;
            }


            showTooltip(link);

        }
    );


    menu.addEventListener(
        'mouseout',
        function (event) {

            const link =
                event.target.closest('.nav-link');


            if (!link) {
                return;
            }


            if (
                event.relatedTarget &&
                link.contains(event.relatedTarget)
            ) {
                return;
            }


            hideTooltip();

        }
    );


    /* Evitar que quede flotando */

    menu.addEventListener(
        'scroll',
        hideTooltip
    );


    window.addEventListener(
        'resize',
        hideTooltip
    );


    document.addEventListener(
        'click',
        hideTooltip
    );

});

</script>
</body>
</html>















<style id="theme-circle-transition">

/* =========================================================
   TRANSICIÓN CIRCULAR CLARO / OSCURO
   ========================================================= */

::view-transition-old(root),
::view-transition-new(root) {
    animation: none;
}

::view-transition-old(root) {
    z-index: 1;
}

::view-transition-new(root) {
    z-index: 2;
    animation: theme-circle-reveal .55s ease-in-out both;
}

@keyframes theme-circle-reveal {

    from {
        clip-path: circle(
            0px at var(--theme-x) var(--theme-y)
        );
    }

    to {
        clip-path: circle(
            var(--theme-radius) at
            var(--theme-x) var(--theme-y)
        );
    }

}

/* Compatibilidad visual */
html,
body {
    transition:
        background-color .25s ease,
        color .25s ease;
}

@media (prefers-reduced-motion: reduce) {

    ::view-transition-old(root),
    ::view-transition-new(root) {
        animation: none !important;
    }

}

</style>

<script id="theme-circle-transition-script">
(function () {

    const STORAGE_KEY = 'restaurant-color-mode';

    const root = document.documentElement;


    function getCurrentMode() {

        return root.getAttribute('data-color-mode') === 'dark'
            ? 'dark'
            : 'light';

    }


    function updateButton(mode) {

        const button =
            document.getElementById('systemThemeToggle');

        const icon =
            document.getElementById('systemThemeIcon');

        if (!button || !icon) {
            return;
        }


        if (mode === 'dark') {

            icon.className = 'bi bi-sun-fill';

            button.setAttribute(
                'title',
                'Modo claro'
            );

            button.setAttribute(
                'aria-label',
                'Activar modo claro'
            );

        } else {

            icon.className = 'bi bi-moon-stars-fill';

            button.setAttribute(
                'title',
                'Modo oscuro'
            );

            button.setAttribute(
                'aria-label',
                'Activar modo oscuro'
            );

        }

    }


    function applyMode(mode) {

        if (mode === 'dark') {

            root.setAttribute(
                'data-color-mode',
                'dark'
            );

        } else {

            root.removeAttribute(
                'data-color-mode'
            );

        }


        try {

            localStorage.setItem(
                STORAGE_KEY,
                mode
            );

        } catch (error) {}

        updateButton(mode);

    }


    function getRadius(x, y) {

        const width =
            window.innerWidth;

        const height =
            window.innerHeight;


        return Math.ceil(
            Math.max(
                Math.hypot(x, y),
                Math.hypot(width - x, y),
                Math.hypot(x, height - y),
                Math.hypot(
                    width - x,
                    height - y
                )
            )
        );

    }


    function toggleTheme(event) {

        const button =
            event.target.closest(
                '#systemThemeToggle'
            );

        if (!button) {
            return;
        }


        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();


        const rect =
            button.getBoundingClientRect();


        const x =
            rect.left +
            rect.width / 2;


        const y =
            rect.top +
            rect.height / 2;


        const radius =
            getRadius(x, y);


        root.style.setProperty(
            '--theme-x',
            x + 'px'
        );

        root.style.setProperty(
            '--theme-y',
            y + 'px'
        );

        root.style.setProperty(
            '--theme-radius',
            radius + 'px'
        );


        const newMode =
            getCurrentMode() === 'dark'
                ? 'light'
                : 'dark';


        /*
         * TRANSICIÓN CIRCULAR
         */
        if (
            document.startViewTransition &&
            !window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches
        ) {

            document.startViewTransition(() => {

                applyMode(newMode);

            });

        } else {

            /*
             * Fallback para navegadores
             * que no soporten View Transition.
             */

            applyMode(newMode);

        }

    }


    /*
     * Capturamos el botón antes que
     * el código original del tema.
     */
    document.addEventListener(
        'click',
        toggleTheme,
        true
    );


    /*
     * Recuperar tema guardado.
     */
    document.addEventListener(
        'DOMContentLoaded',
        function () {

            let savedMode = null;

            try {

                savedMode =
                    localStorage.getItem(
                        STORAGE_KEY
                    );

            } catch (error) {

                savedMode = null;

            }


            if (savedMode === 'dark') {

                applyMode('dark');

            } else {

                applyMode('light');

            }

        }
    );

})();

</script>




<style id="kpi-colors-global">

/* =========================================================
   COLORES KPI - MODO CLARO
   ========================================================= */

.kpi-sales .kpi-badge,
.sales-kpi .sales-kpi-badge {
    background: #fff1df !important;
    color: #ff8c00 !important;
    border: 1px solid #ffb45c !important;
}

.kpi-tables .kpi-badge {
    background: #e8f3ff !important;
    color: #1683c7 !important;
    border: 1px solid #75bde8 !important;
}

.kpi-month .kpi-badge {
    background: #e8f8ee !important;
    color: #16a05d !important;
    border: 1px solid #70cf94 !important;
}

.kpi-stock .kpi-badge {
    background: #fff1df !important;
    color: #ff8c00 !important;
    border: 1px solid #ffb45c !important;
}


/* =========================================================
   RESERVAS
   ========================================================= */

.reservation-kpi-today .reservation-kpi-badge {
    background: #e8f3ff !important;
    color: #1683c7 !important;
    border: 1px solid #75bde8 !important;
}

.reservation-kpi-pending .reservation-kpi-badge {
    background: #fff5df !important;
    color: #d88900 !important;
    border: 1px solid #f2c36b !important;
}

.reservation-kpi-confirmed .reservation-kpi-badge {
    background: #e8f8ee !important;
    color: #16a05d !important;
    border: 1px solid #70cf94 !important;
}

.reservation-kpi-people .reservation-kpi-badge {
    background: #f3eaff !important;
    color: #8b45c7 !important;
    border: 1px solid #c59ae8 !important;
}


/* =========================================================
   HISTORIAL DE VENTAS
   ========================================================= */

.sales-kpi .sales-kpi-badge.text-success {
    background: #e8f8ee !important;
    color: #16a05d !important;
    border: 1px solid #70cf94 !important;
}

.sales-kpi .sales-kpi-badge.text-danger {
    background: #ffe9e9 !important;
    color: #dc3545 !important;
    border: 1px solid #f09a9a !important;
}

.sales-kpi .sales-kpi-badge.text-warning {
    background: #fff5df !important;
    color: #d88900 !important;
    border: 1px solid #f2c36b !important;
}

.sales-kpi .sales-kpi-badge.text-primary,
.sales-kpi .sales-kpi-badge.text-info {
    background: #e8f3ff !important;
    color: #1683c7 !important;
    border: 1px solid #75bde8 !important;
}


/* =========================================================
   MODO OSCURO
   ========================================================= */

html[data-color-mode="dark"] .kpi-sales .kpi-badge,
html[data-color-mode="dark"] .sales-kpi .sales-kpi-badge {
    background: rgba(255,140,0,.14) !important;
    color: #ffb45c !important;
    border-color: rgba(255,180,92,.55) !important;
}

html[data-color-mode="dark"] .kpi-tables .kpi-badge,
html[data-color-mode="dark"] .reservation-kpi-today .reservation-kpi-badge {
    background: rgba(22,131,199,.16) !important;
    color: #75bde8 !important;
    border-color: rgba(117,189,232,.55) !important;
}

html[data-color-mode="dark"] .kpi-month .kpi-badge,
html[data-color-mode="dark"] .reservation-kpi-confirmed .reservation-kpi-badge {
    background: rgba(22,160,93,.16) !important;
    color: #70cf94 !important;
    border-color: rgba(112,207,148,.55) !important;
}

html[data-color-mode="dark"] .kpi-stock .kpi-badge,
html[data-color-mode="dark"] .reservation-kpi-pending .reservation-kpi-badge {
    background: rgba(245,158,11,.14) !important;
    color: #fcd34d !important;
    border-color: rgba(251,191,36,.45) !important;
}

html[data-color-mode="dark"] .reservation-kpi-people .reservation-kpi-badge {
    background: rgba(168,85,247,.14) !important;
    color: #d8b4fe !important;
    border-color: rgba(192,132,252,.45) !important;
}

html[data-color-mode="dark"] .sales-kpi .sales-kpi-badge.text-success {
    background: rgba(34,197,94,.14) !important;
    color: #86efac !important;
    border-color: rgba(74,222,128,.45) !important;
}

html[data-color-mode="dark"] .sales-kpi .sales-kpi-badge.text-danger {
    background: rgba(239,68,68,.14) !important;
    color: #fca5a5 !important;
    border-color: rgba(248,113,113,.45) !important;
}

html[data-color-mode="dark"] .sales-kpi .sales-kpi-badge.text-warning {
    background: rgba(245,158,11,.14) !important;
    color: #fcd34d !important;
    border-color: rgba(251,191,36,.45) !important;
}

html[data-color-mode="dark"] .sales-kpi .sales-kpi-badge.text-primary,
html[data-color-mode="dark"] .sales-kpi .sales-kpi-badge.text-info {
    background: rgba(59,130,246,.14) !important;
    color: #93c5fd !important;
    border-color: rgba(96,165,250,.45) !important;
}


/* =========================================================
   TRANSICIÓN
   ========================================================= */

.kpi-badge,
.reservation-kpi-badge,
.sales-kpi-badge {
    transition:
        background-color .35s ease,
        color .35s ease,
        border-color .35s ease,
        box-shadow .35s ease !important;
}

</style>



<style id="sales-kpi-original-colors">

/* =========================================================
   HISTORIAL DE VENTAS
   RESPETAR COLORES ORIGINALES DE CADA TARJETA
   ========================================================= */

.sales-kpi .sales-kpi-badge {
    color: var(--kpi-color) !important;
    background: var(--kpi-bg) !important;
    border: 1px solid color-mix(
        in srgb,
        var(--kpi-color) 35%,
        white
    ) !important;
}

/* TOTAL */
.sales-kpi[style*="#84cc16"] .sales-kpi-badge {
    color: #84cc16 !important;
    background: #f1f8e5 !important;
    border-color: #b7dc72 !important;
}

/* CAJA / EFECTIVO */
.sales-kpi[style*="#198754"] .sales-kpi-badge {
    color: #198754 !important;
    background: #e8f5ee !important;
    border-color: #8bc9a8 !important;
}

/* YAPE */
.sales-kpi[style*="#742284"] .sales-kpi-badge {
    color: #742284 !important;
    background: #f5e9f8 !important;
    border-color: #c99bd3 !important;
}

/* PLIN */
.sales-kpi[style*="#00a884"] .sales-kpi-badge {
    color: #00a884 !important;
    background: #e8f8f3 !important;
    border-color: #80d4c1 !important;
}

/* TARJETA */
.sales-kpi[style*="#0d6efd"] .sales-kpi-badge {
    color: #0d6efd !important;
    background: #eaf2ff !important;
    border-color: #8bb8fa !important;
}

/* GASTOS / SALIDA */
.sales-kpi[style*="#ef4444"] .sales-kpi-badge {
    color: #ef4444 !important;
    background: #fff1f2 !important;
    border-color: #f5a3aa !important;
}

/* BALANCE POSITIVO */
.sales-kpi[style*="#0f766e"] .sales-kpi-badge {
    color: #0f766e !important;
    background: #e7f7f5 !important;
    border-color: #80c8c0 !important;
}


/* =========================================================
   MODO OSCURO
   ========================================================= */

html[data-color-mode="dark"]
.sales-kpi .sales-kpi-badge {
    background: color-mix(
        in srgb,
        var(--kpi-color) 14%,
        #132338
    ) !important;

    color: color-mix(
        in srgb,
        var(--kpi-color) 82%,
        white
    ) !important;

    border-color: color-mix(
        in srgb,
        var(--kpi-color) 48%,
        #30465d
    ) !important;
}


/* Transición */
.sales-kpi-badge {
    transition:
        background-color .35s ease,
        color .35s ease,
        border-color .35s ease !important;
}

</style>

