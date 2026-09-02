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

    <style>

        /* =========================================================
           VARIABLES DE ESTILO "GLAM"
        ========================================================= */

        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --dark-bg: #0f172a;
            --light-bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;

            --radius-xl: 24px;
            --radius-md: 16px;
            --radius-sm: 12px;

            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-main);
            font-size: 0.95rem;
            overflow-x: hidden;
        }


        /* =========================================================
           1. SIDEBAR ESTILO APP
        ========================================================= */

        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--dark-bg);
            color: white;
            z-index: 1050;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }

        .sidebar-header {
            padding: 0 20px 20px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 0;
        }

        .sidebar-menu {
            padding: 10px 15px;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.2);
            border-radius: 20px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255,255,255,0.4);
        }

        .logo-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }

        .brand-name {
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.5px;
            color: white;
        }

        .menu-category {
            color: #94a3b8;
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 20px 10px 10px;
        }

        .nav-link {
            color: #cbd5e1;
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .nav-link i {
            font-size: 1.3rem;
            margin-right: 14px;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.5);
        }

        .nav-link.active i {
            opacity: 1;
        }


        /* =========================================================
           2. MAIN CONTENT & TOPBAR
        ========================================================= */

        .main-content {
            margin-left: 280px;
            padding: 20px 30px;
            min-height: 100vh;
            transition: margin-left 0.3s;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            background: white;
            border-radius: var(--radius-md);
            padding: 10px 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 5px 10px;
            border-radius: 50px;
            transition: background 0.2s;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .user-profile-btn:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #f472b6, #db2777);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }


        /* =========================================================
           3. COMPONENTES GENERALES
        ========================================================= */

        .card {
            border: none;
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            color: white;
        }

        .form-control,
        .form-select {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-sm);
            padding: 0.8rem 1rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            background: white;
        }


        /* =========================================================
           TARJETAS DASHBOARD
        ========================================================= */

        .card-solid {
            color: white !important;
            border-radius: var(--radius-xl);
            position: relative;
            overflow: hidden;
        }

        .bg-gradient-blue {
            background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        .bg-gradient-red {
            background: linear-gradient(135deg, #ef4444, #b91c1c) !important;
        }

        .bg-gradient-cyan {
            background: linear-gradient(135deg, #06b6d4, #0891b2) !important;
        }

        .card-solid h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 10px 0;
        }

        .card-solid .icon-bg {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.15;
            pointer-events: none;
        }


        /* =========================================================
           RESPONSIVE DEL DASHBOARD
        ========================================================= */

        @media (max-width: 991px) {

            .sidebar {
                transform: translateX(-100%);
                width: 100%;
                border-radius: 0;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(15, 23, 42, 0.8);
                z-index: 1040;
                backdrop-filter: blur(4px);
                display: none;
            }

            .mobile-overlay.show {
                display: block;
            }
        }


        /* =========================================================
           CHATBOT
        ========================================================= */

        #chatbot-question-mark {
            position: fixed;
            right: 40px;
            bottom: 88px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            font-weight: 800;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.20);
            z-index: 2001;
            animation: chatbotQuestionBlink 1.3s infinite;
            pointer-events: none;
        }

        @keyframes chatbotQuestionBlink {

            0% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.35;
                transform: scale(0.85);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        #chatbot-bubble {
            position: fixed;
            right: 25px;
            bottom: 25px;
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.20);
            z-index: 2000;
            transition: all 0.25s ease;
        }

        #chatbot-bubble:hover {
            transform: scale(1.08);
            background: var(--primary-hover);
        }

        #chatbot-window {
            position: fixed;
            right: 25px;
            bottom: 95px;
            width: 400px;
            height: 550px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.20);
            overflow: hidden;
            display: none;
            flex-direction: column;
            z-index: 2000;
        }


        /* =========================================================
           HEADER CHATBOT
        ========================================================= */

        #chatbot-header {
            background: var(--primary);
            color: white;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
        }

        .chatbot-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chatbot-header-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chatbot-header-title {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .chatbot-header-title strong {
            font-size: 14px;
        }

        .chatbot-header-title span {
            font-size: 11px;
            font-weight: 400;
            opacity: 0.85;
        }

        #chatbot-close {
            border: none;
            background: transparent;
            color: white;
            font-size: 18px;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        #chatbot-close:hover {
            background: rgba(255,255,255,0.15);
        }


        /* =========================================================
           MENSAJES
        ========================================================= */

        #chatbot-messages {
            flex: 1;
            padding: 18px;
            overflow-y: auto;
            background: #f8fafc;
        }

        .chatbot-message {
            max-width: 85%;
            padding: 10px 14px;
            border-radius: 15px;
            margin-bottom: 10px;
            line-height: 1.4;
            font-size: 14px;
            word-wrap: break-word;
        }

        .chatbot-message.bot {
            background: white;
            color: var(--text-main);
            border: 1px solid #e2e8f0;
            margin-right: auto;
        }

        .chatbot-message.user {
            background: var(--primary);
            color: white;
            margin-left: auto;
        }


        /* =========================================================
           PREGUNTAS RÁPIDAS
        ========================================================= */

        #chatbot-suggestions {
            margin-bottom: 15px;
        }

        .chatbot-suggestions-title {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 9px;
        }

        .chatbot-suggestion {
            width: 100%;
            border: 1px solid #dbe4ea;
            background: white;
            color: #334155;
            border-radius: 10px;
            padding: 9px 11px;
            margin-bottom: 7px;
            text-align: left;
            font-size: 12px;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .chatbot-suggestion i {
            width: 17px;
            color: var(--primary);
            text-align: center;
        }

        .chatbot-suggestion:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateX(3px);
        }

        .chatbot-suggestion:hover i {
            color: white;
        }


        /* =========================================================
           TABLA DE PRODUCTOS
        ========================================================= */

        .chatbot-products-table-wrapper {
            width: 100%;
            max-height: 330px;
            overflow-y: auto;
            overflow-x: auto;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
        }

        .chatbot-products-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 12px;
            margin: 0;
        }

        .chatbot-products-table th {
            background: var(--primary);
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 700;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .chatbot-products-table td {
            padding: 9px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .chatbot-products-table tr:last-child td {
            border-bottom: none;
        }

        .chatbot-products-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .chatbot-products-table td:nth-child(2),
        .chatbot-products-table th:nth-child(2) {
            text-align: right;
            white-space: nowrap;
        }

        .chatbot-products-table td:nth-child(3),
        .chatbot-products-table th:nth-child(3) {
            text-align: center;
            white-space: nowrap;
        }

        .chatbot-stock-sin {
            color: #dc2626;
            font-weight: 700;
        }

        .chatbot-stock-bajo {
            color: #d97706;
            font-weight: 700;
        }

        .chatbot-stock-ok {
            color: #16a34a;
            font-weight: 700;
        }

        .chatbot-stock-null {
            color: #64748b;
            font-weight: 600;
        }


        /* =========================================================
           INPUT CHATBOT
        ========================================================= */

        #chatbot-input-area {
            display: flex;
            gap: 8px;
            padding: 12px;
            background: white;
            border-top: 1px solid #e2e8f0;
        }

        #chatbot-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 25px;
            padding: 10px 14px;
            outline: none;
            font-family: inherit;
            font-size: 13px;
        }

        #chatbot-input:focus {
            border-color: var(--primary);
        }

        #chatbot-send {
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            cursor: pointer;
            flex-shrink: 0;
            transition: 0.2s;
        }

        #chatbot-send:hover {
            background: var(--primary-hover);
        }

        #chatbot-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }


        /* =========================================================
           RESPONSIVE CHATBOT
        ========================================================= */

        @media (max-width: 500px) {

            #chatbot-window {
                right: 10px;
                left: 10px;
                bottom: 85px;
                width: auto;
                height: 70vh;
            }

            #chatbot-bubble {
                right: 15px;
                bottom: 15px;
            }

            #chatbot-question-mark {
                right: 25px;
                bottom: 78px;
                width: 28px;
                height: 28px;
                font-size: 16px;
            }

            .chatbot-products-table-wrapper {
                max-height: 300px;
            }

            .chatbot-products-table {
                font-size: 11px;
            }

            .chatbot-products-table th,
            .chatbot-products-table td {
                padding: 8px 6px;
            }
        }

    </style>
</head>


<body>

<div class="mobile-overlay" id="mobileOverlay" onclick="closeMenu()"></div>


{{-- =============================================================
     SIDEBAR
============================================================= --}}

<div class="sidebar" id="sidebar">

    <div class="sidebar-header">

        @php
            $logo = \App\Models\Setting::where('key', 'company_logo')->value('value');
        @endphp

        @if($logo)

            <img
                src="{{ asset('storage/'.$logo) }}"
                style="width: 40px; height: 40px; object-fit: cover; border-radius: 12px;"
            >

        @else

            <div class="logo-box">
                <i class="bi bi-shop"></i>
            </div>

        @endif

        <div class="brand-name ps-2 text-truncate">
            {{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante' }}
        </div>

        <button
            class="btn btn-sm text-secondary d-lg-none ms-auto"
            onclick="closeMenu()"
        >
            <i class="bi bi-x-lg"></i>
        </button>

    </div>


    <div class="sidebar-menu">

        @php
            $role = Auth::user()->role;
        @endphp


        @if(in_array($role, ['admin', 'cashier']))

            <a
                href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>

        @endif


        @if($role === 'admin')

            <a
                href="{{ route('reports.index') }}"
                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
            >
                <i class="bi bi-bar-chart-line-fill"></i>
                Reportes
            </a>

        @endif


        <div class="menu-category">
            Operaciones
        </div>


        <a
            href="{{ route('pos.index') }}"
            class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}"
        >
            <i class="bi bi-bag-check-fill"></i>
            Punto de Venta
        </a>


        <a
            href="{{ route('reservations.index') }}"
            class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}"
        >
            <i class="bi bi-calendar-event-fill"></i>
            Reservas
        </a>


        @if(in_array($role, ['admin', 'cashier']))

            <a
                href="{{ route('sales.index') }}"
                class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"
            >
                <i class="bi bi-receipt"></i>
                Caja / Historial
            </a>

        @endif


        <a
            href="{{ route('kitchen.index') }}"
            class="nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}"
        >
            <i class="bi bi-fire"></i>
            Cocina (KDS)
        </a>


        <div class="menu-category">
            Gestión
        </div>


        <a
            href="{{ route('clients.index') }}"
            class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}"
        >
            <i class="bi bi-people-fill"></i>
            Clientes
        </a>


        @if($role === 'admin')

            <a
                href="{{ route('categories.index') }}"
                class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
            >
                <i class="bi bi-tags-fill"></i>
                Categorías
            </a>


            <a
                href="{{ route('products.index') }}"
                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
            >
                <i class="bi bi-box-seam-fill"></i>
                Inventario
            </a>


            <a
                href="{{ route('tables.index') }}"
                class="nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}"
            >
                <i class="bi bi-grid-3x3-gap-fill"></i>
                Mesas
            </a>


            <a
                href="{{ route('users.index') }}"
                class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
            >
                <i class="bi bi-person-badge-fill"></i>
                Personal / Usuarios
            </a>


            <a
                href="{{ route('settings.index') }}"
                class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
            >
                <i class="bi bi-gear-fill"></i>
                Configuración
            </a>


            <a
                href="{{ route('system.index') }}"
                class="nav-link {{ request()->routeIs('system.*') ? 'active' : '' }} text-danger"
            >
                <i class="bi bi-exclamation-octagon-fill"></i>
                Reset
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
                    class="btn btn-light border d-lg-none"
                    onclick="openMenu()"
                >
                    <i class="bi bi-list fs-5"></i>
                </button>


                <h5 class="fw-bold mb-0 text-dark d-none d-sm-block">

                    @if(request()->routeIs('dashboard'))

                        Panel de Control

                    @elseif(request()->routeIs('pos.*'))

                        Punto de Venta

                    @elseif(request()->routeIs('products.*'))

                        Inventario

                    @elseif(request()->routeIs('sales.*'))

                        Caja y Movimientos

                    @elseif(request()->routeIs('users.*'))

                        Gestión de Personal

                    @else

                        Sistema de Restaurante

                    @endif

                </h5>

            </div>


            <div class="dropdown">

                <div
                    class="user-profile-btn"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >

                    <div class="text-end d-none d-sm-block">

                        <div class="fw-bold text-dark small">
                            {{ Auth::user()->name }}
                        </div>

                        <div
                            class="text-muted"
                            style="font-size: 0.7rem;"
                        >
                            {{ ucfirst(Auth::user()->role) }}
                        </div>

                    </div>


                    <div class="user-avatar">

                        {{ substr(Auth::user()->name, 0, 1) }}

                    </div>


                    <i class="bi bi-chevron-down text-muted small"></i>

                </div>


                <ul
                    class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-4"
                    style="width: 220px;"
                >

                    <li class="px-2 py-1 text-muted small fw-bold">
                        MI CUENTA
                    </li>


                    <li>

                        <button
                            class="dropdown-item rounded-3 mb-1"
                            data-bs-toggle="modal"
                            data-bs-target="#profileModal"
                        >
                            <i class="bi bi-person-gear me-2 text-primary"></i>
                            Editar Perfil
                        </button>

                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>

                        <form
                            action="{{ route('logout') }}"
                            method="POST"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item rounded-3 text-danger fw-bold"
                            >
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    @endif


    {{-- =============================================================
         MENSAJES DE SESIÓN
    ============================================================= --}}

    @if(session('success'))

        <div
            class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center bg-white border-start border-5 border-success"
        >

            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>

            <div>
                <strong>¡Éxito!</strong>
                {{ session('success') }}
            </div>

            <button
                type="button"
                class="btn-close ms-auto"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if(session('error'))

        <div
            class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center bg-white border-start border-5 border-danger"
        >

            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>

            <div>
                <strong>Error:</strong>
                {{ session('error') }}
            </div>

            <button
                type="button"
                class="btn-close ms-auto"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =============================================================
         CONTENIDO DE CADA PÁGINA
    ============================================================= --}}

    @yield('content')

</div>


{{-- =============================================================
     MODAL PERFIL
============================================================= --}}

<div
    class="modal fade"
    id="profileModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-bottom-0 pb-0">

                <h5 class="modal-title fw-bold">
                    Mi Perfil
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body pt-2">

                <p class="text-muted small mb-3">
                    Actualiza tus datos de acceso.
                </p>


                <form
                    action="{{ route('users.update', Auth::user()->id) }}"
                    method="POST"
                >

                    @csrf
                    @method('PUT')


                    <div class="mb-3">

                        <label class="form-label fw-bold small">
                            Nombre
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ Auth::user()->name }}"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label fw-bold small">
                            Correo (Solo lectura)
                        </label>

                        <input
                            type="email"
                            class="form-control bg-light"
                            value="{{ Auth::user()->email }}"
                            readonly
                        >

                    </div>


                    <hr class="border-dashed">


                    <div class="mb-3">

                        <label class="form-label fw-bold small">
                            Nueva Contraseña (Opcional)
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Dejar en blanco para no cambiar"
                        >

                    </div>


                    <div class="d-grid">

                        <button
                            type="submit"
                            class="btn btn-primary fw-bold"
                        >
                            Guardar Cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- =============================================================
     CHATBOT SOLO PARA ADMINISTRADOR
============================================================= --}}

@auth

    @if(Auth::user()->role === 'admin')

        {{-- =====================================================
             SIGNO DE INTERROGACIÓN
        ====================================================== --}}

        <div id="chatbot-question-mark">
            ?
        </div>


        {{-- =====================================================
             BURBUJA DEL CHATBOT
        ====================================================== --}}

        <div id="chatbot-bubble">

            <i class="bi bi-robot"></i>

        </div>


        {{-- =====================================================
             VENTANA DEL CHATBOT
        ====================================================== --}}

        <div id="chatbot-window">


            {{-- HEADER --}}

            <div id="chatbot-header">

                <div class="chatbot-header-left">

                    <div class="chatbot-header-icon">
                        <i class="bi bi-robot"></i>
                    </div>


                    <div class="chatbot-header-title">

                        <strong>
                            Asistente del restaurante
                        </strong>

                        <span>
                            Disponible para ayudarte
                        </span>

                    </div>

                </div>


                <button
                    type="button"
                    id="chatbot-close"
                    title="Cerrar"
                >

                    <i class="bi bi-x-lg"></i>

                </button>

            </div>


            {{-- MENSAJES --}}

            <div id="chatbot-messages">


                {{-- MENSAJE INICIAL --}}

                <div class="chatbot-message bot">

                    Hola, administrador.
                    ¿En qué puedo ayudarte?

                </div>


                {{-- PREGUNTAS RÁPIDAS --}}

                <div id="chatbot-suggestions">

                    <div class="chatbot-suggestions-title">
                        Preguntas rápidas
                    </div>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuánto vendimos hoy?"
                    >
                        <i class="bi bi-cash-stack"></i>

                        <span>
                            ¿Cuánto vendimos hoy?
                        </span>
                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuánto vendimos esta semana?"
                    >
                        <i class="bi bi-graph-up"></i>

                        <span>
                            ¿Cuánto vendimos esta semana?
                        </span>
                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuántos pedidos tenemos hoy?"
                    >
                        <i class="bi bi-receipt"></i>

                        <span>
                            ¿Cuántos pedidos tenemos hoy?
                        </span>
                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Qué productos están sin stock?"
                    >
                        <i class="bi bi-box-seam"></i>

                        <span>
                            ¿Qué productos están sin stock?
                        </span>
                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Qué productos tienen poco stock?"
                    >
                        <i class="bi bi-exclamation-triangle"></i>

                        <span>
                            ¿Qué productos tienen poco stock?
                        </span>
                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cuál es el producto más vendido?"
                    >
                        <i class="bi bi-trophy"></i>

                        <span>
                            ¿Cuál es el producto más vendido?
                        </span>
                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="Muéstrame los productos"
                    >
                        <i class="bi bi-list-ul"></i>

                        <span>
                            Muéstrame los productos
                        </span>
                    </button>


                    <button
                        type="button"
                        class="chatbot-suggestion"
                        data-question="¿Cómo puedo mejorar las ventas?"
                    >
                        <i class="bi bi-lightbulb"></i>

                        <span>
                            ¿Cómo puedo mejorar las ventas?
                        </span>
                    </button>

                </div>

            </div>


            {{-- INPUT --}}

            <div id="chatbot-input-area">

                <input
                    type="text"
                    id="chatbot-input"
                    placeholder="Escribe tu pregunta..."
                    autocomplete="off"
                >


                <button
                    type="button"
                    id="chatbot-send"
                    title="Enviar"
                >

                    <i class="bi bi-send-fill"></i>

                </button>

            </div>

        </div>

    @endif

@endauth


{{-- =============================================================
     BOOTSTRAP
============================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


{{-- =============================================================
     MENÚ RESPONSIVE
============================================================= --}}

<script>

    function openMenu() {

        document
            .getElementById('sidebar')
            .classList
            .add('show');

        document
            .getElementById('mobileOverlay')
            .classList
            .add('show');

        document.body.style.overflow = 'hidden';
    }


    function closeMenu() {

        document
            .getElementById('sidebar')
            .classList
            .remove('show');

        document
            .getElementById('mobileOverlay')
            .classList
            .remove('show');

        document.body.style.overflow = 'auto';
    }

</script>


{{-- =============================================================
     JAVASCRIPT DEL CHATBOT
============================================================= --}}

@auth

    @if(Auth::user()->role === 'admin')

        <script>

            /* =====================================================
               ELEMENTOS DEL CHATBOT
            ===================================================== */

            const chatbotBubble =
                document.getElementById('chatbot-bubble');

            const chatbotWindow =
                document.getElementById('chatbot-window');

            const chatbotClose =
                document.getElementById('chatbot-close');

            const chatbotInput =
                document.getElementById('chatbot-input');

            const chatbotSend =
                document.getElementById('chatbot-send');

            const chatbotMessages =
                document.getElementById('chatbot-messages');

            const chatbotQuestionMark =
                document.getElementById('chatbot-question-mark');

            const chatbotSuggestionButtons =
                document.querySelectorAll(
                    '.chatbot-suggestion'
                );


            /* =====================================================
               ABRIR CHATBOT
            ===================================================== */

            if (chatbotBubble) {

                chatbotBubble.addEventListener(
                    'click',
                    function () {

                        if (!chatbotWindow) {
                            return;
                        }

                        chatbotWindow.style.display = 'flex';


                        if (chatbotQuestionMark) {

                            chatbotQuestionMark.style.display =
                                'none';

                        }


                        setTimeout(
                            function () {

                                if (chatbotInput) {

                                    chatbotInput.focus();

                                }

                            },
                            100
                        );

                    }
                );

            }


            /* =====================================================
               CERRAR CHATBOT
            ===================================================== */

            if (chatbotClose) {

                chatbotClose.addEventListener(
                    'click',
                    function () {

                        if (chatbotWindow) {

                            chatbotWindow.style.display =
                                'none';

                        }


                        if (chatbotQuestionMark) {

                            chatbotQuestionMark.style.display =
                                'flex';

                        }

                    }
                );

            }


            /* =====================================================
               AGREGAR MENSAJE
            ===================================================== */

            function addChatbotMessage(
                text,
                type
            ) {

                if (!chatbotMessages) {
                    return;
                }


                const message =
                    document.createElement('div');


                message.className =
                    `chatbot-message ${type}`;


                message.textContent =
                    text;


                chatbotMessages.appendChild(
                    message
                );


                chatbotMessages.scrollTop =
                    chatbotMessages.scrollHeight;
            }


            /* =====================================================
               TABLA DE PRODUCTOS
            ===================================================== */

            function addChatbotProductsTable(
                products,
                tableType = 'productos'
            ) {

                if (!chatbotMessages) {
                    return;
                }


                const wrapper =
                    document.createElement('div');


                wrapper.className =
                    'chatbot-products-table-wrapper';


                const table =
                    document.createElement('table');


                table.className =
                    'chatbot-products-table';


                /* CABECERA */

                const thead =
                    document.createElement('thead');


                const headerRow =
                    document.createElement('tr');


                const headerProducto =
                    document.createElement('th');

                headerProducto.textContent =
                    'Producto';


                const headerPrecio =
                    document.createElement('th');

                headerPrecio.textContent =
                    'Precio';


                const headerStock =
                    document.createElement('th');

                headerStock.textContent =
                    'Stock';


                headerRow.appendChild(
                    headerProducto
                );

                headerRow.appendChild(
                    headerPrecio
                );

                headerRow.appendChild(
                    headerStock
                );


                thead.appendChild(
                    headerRow
                );


                /* CUERPO */

                const tbody =
                    document.createElement('tbody');


                products.forEach(
                    product => {

                        const row =
                            document.createElement('tr');


                        /* PRODUCTO */

                        const nameCell =
                            document.createElement('td');


                        nameCell.textContent =
                            product.name;


                        /* PRECIO */

                        const priceCell =
                            document.createElement('td');


                        const price =
                            Number(
                                product.price
                            );


                        priceCell.textContent =
                            'S/ ' +
                            (
                                Number.isFinite(price)
                                    ? price.toFixed(2)
                                    : '0.00'
                            );


                        /* STOCK */

                        const stockCell =
                            document.createElement('td');


                        const stock =
                            product.stock;


                        if (
                            stock === null ||
                            stock === undefined ||
                            stock === ''
                        ) {

                            stockCell.textContent =
                                'No registrado';


                            stockCell.className =
                                'chatbot-stock-null';

                        }

                        else {

                            const stockNumber =
                                Number(stock);


                            if (
                                stockNumber === 0
                            ) {

                                stockCell.textContent =
                                    'Sin stock';


                                stockCell.className =
                                    'chatbot-stock-sin';

                            }

                            else if (
                                Number.isFinite(stockNumber) &&
                                stockNumber <= 5
                            ) {

                                stockCell.textContent =
                                    stockNumber +
                                    ' (Bajo)';


                                stockCell.className =
                                    'chatbot-stock-bajo';

                            }

                            else {

                                stockCell.textContent =
                                    stockNumber;


                                stockCell.className =
                                    'chatbot-stock-ok';

                            }

                        }


                        row.appendChild(
                            nameCell
                        );

                        row.appendChild(
                            priceCell
                        );

                        row.appendChild(
                            stockCell
                        );


                        tbody.appendChild(
                            row
                        );

                    }
                );


                table.appendChild(
                    thead
                );

                table.appendChild(
                    tbody
                );


                wrapper.appendChild(
                    table
                );


                chatbotMessages.appendChild(
                    wrapper
                );


                chatbotMessages.scrollTop =
                    chatbotMessages.scrollHeight;
            }


            /* =====================================================
               ENVIAR MENSAJE
            ===================================================== */

            async function sendChatbotMessage() {

                if (
                    !chatbotInput ||
                    !chatbotSend ||
                    !chatbotMessages
                ) {

                    return;

                }


                const message =
                    chatbotInput.value.trim();


                if (!message) {
                    return;
                }


                /* MENSAJE DEL ADMINISTRADOR */

                addChatbotMessage(
                    message,
                    'user'
                );


                chatbotInput.value =
                    '';


                chatbotSend.disabled =
                    true;


                /* INDICADOR DE CARGA */

                const loadingMessage =
                    document.createElement('div');


                loadingMessage.className =
                    'chatbot-message bot';


                loadingMessage.innerHTML =
                    '<i class="bi bi-arrow-repeat"></i> Procesando...';


                chatbotMessages.appendChild(
                    loadingMessage
                );


                chatbotMessages.scrollTop =
                    chatbotMessages.scrollHeight;


                try {

                    /* PETICIÓN A LARAVEL */

                    const response =
                        await fetch(
                            '{{ route('chatbot.chat') }}',
                            {
                                method: 'POST',

                                headers: {

                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .getAttribute(
                                                'content'
                                            )

                                },

                                body:
                                    JSON.stringify({
                                        message: message
                                    })

                            }
                        );


                    const data =
                        await response.json();


                    /* ELIMINAR CARGANDO */

                    loadingMessage.remove();


                    /* ERROR HTTP */

                    if (!response.ok) {

                        throw new Error(
                            data.message ||
                            'Error al comunicarse con el chatbot.'
                        );

                    }


                    /* RESPUESTA CON PRODUCTOS */

                    if (
                        data.products &&
                        Array.isArray(data.products)
                    ) {

                        addChatbotMessage(
                            data.response,
                            'bot'
                        );


                        addChatbotProductsTable(
                            data.products,
                            data.product_table_type ||
                            'productos'
                        );

                    }

                    else {

                        /* RESPUESTA NORMAL */

                        addChatbotMessage(
                            data.response,
                            'bot'
                        );

                    }


                }

                catch (error) {

                    console.error(
                        'Error del chatbot:',
                        error
                    );


                    if (loadingMessage) {
                        loadingMessage.remove();
                    }


                    addChatbotMessage(
                        'No pude comunicarme con el servidor. Inténtalo nuevamente.',
                        'bot'
                    );

                }


                finally {

                    chatbotSend.disabled =
                        false;


                    chatbotInput.focus();

                }

            }


            /* =====================================================
               BOTÓN ENVIAR
            ===================================================== */

            if (chatbotSend) {

                chatbotSend.addEventListener(
                    'click',
                    sendChatbotMessage
                );

            }


            /* =====================================================
               ENTER PARA ENVIAR
            ===================================================== */

            if (chatbotInput) {

                chatbotInput.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Enter'
                        ) {

                            event.preventDefault();

                            sendChatbotMessage();

                        }

                    }
                );

            }


            /* =====================================================
               PREGUNTAS RÁPIDAS
            ===================================================== */

            chatbotSuggestionButtons.forEach(
                button => {

                    button.addEventListener(
                        'click',
                        function () {

                            const question =
                                this.dataset.question;


                            if (!question) {
                                return;
                            }


                            if (!chatbotInput) {
                                return;
                            }


                            chatbotInput.value =
                                question;


                            sendChatbotMessage();

                        }
                    );

                }
            );

        </script>

    @endif

@endauth

</body>
</html>