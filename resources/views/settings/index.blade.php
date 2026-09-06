@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
       SELECTOR DE TEMAS
    ========================================================= */

    .theme-section {
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e4edf5;
        border-radius: 20px;
        padding: 22px;
    }

    .theme-option {
        display: block;
        width: 100%;
        cursor: pointer;
        margin: 0;
    }

    .theme-radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .theme-card {
        height: 100%;
        min-height: 168px;
        padding: 17px;
        background: white;
        border: 2px solid #e7edf3;
        border-radius: 17px;
        position: relative;
        overflow: hidden;
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            border-color .2s ease;
    }

    .theme-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 50, 80, .10);
    }

    .theme-radio:checked + .theme-card {
        border-color: #ff8c00;
        box-shadow:
            0 0 0 3px rgba(255, 140, 0, .12),
            0 10px 25px rgba(15, 50, 80, .10);
    }

    .theme-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .theme-name {
        font-size: .92rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 3px;
    }

    .theme-description {
        font-size: .71rem;
        line-height: 1.4;
        color: #64748b;
    }

    .theme-check {
        width: 27px;
        height: 27px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #e9eef3;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(.8);
        transition: .2s ease;
    }

    .theme-radio:checked + .theme-card .theme-check {
        background: #ff8c00;
        opacity: 1;
        transform: scale(1);
    }

    .theme-preview {
        display: grid;
        grid-template-columns: 70px 1fr;
        height: 73px;
        border-radius: 11px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, .06);
        margin-bottom: 12px;
    }

    .theme-preview-sidebar {
        position: relative;
        padding: 10px 7px;
    }

    .theme-preview-sidebar::before,
    .theme-preview-sidebar::after {
        content: '';
        display: block;
        height: 6px;
        background: rgba(255,255,255,.45);
        border-radius: 10px;
        margin-bottom: 7px;
    }

    .theme-preview-sidebar::before {
        width: 80%;
    }

    .theme-preview-sidebar::after {
        width: 55%;
    }

    .theme-preview-main {
        padding: 9px;
        position: relative;
    }

    .theme-preview-top {
        width: 100%;
        height: 9px;
        border-radius: 8px;
        background: rgba(255,255,255,.90);
        margin-bottom: 8px;
    }

    .theme-preview-cards {
        display: flex;
        gap: 5px;
    }

    .theme-preview-mini {
        flex: 1;
        height: 32px;
        background: white;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,.05);
        border-top: 4px solid transparent;
    }

    .theme-colors {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .theme-color {
        width: 24px;
        height: 24px;
        display: inline-block;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 1px #d6dde5;
    }

    .theme-current-label {
        margin-left: auto;
        font-size: .67rem;
        font-weight: 700;
        color: #ff8c00;
        display: none;
    }

    .theme-radio:checked + .theme-card .theme-current-label {
        display: inline-block;
    }

    /* =========================================================
       VISTA PREVIA GRANDE
    ========================================================= */

    .theme-live-preview {
        margin-top: 20px;
        border-radius: 17px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .theme-live-preview-header {
        padding: 12px 16px;
        background: white;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .theme-live-preview-screen {
        min-height: 150px;
        display: flex;
        transition: background .25s ease;
    }

    .theme-live-sidebar {
        width: 105px;
        padding: 17px 10px;
        transition: background .25s ease;
    }

    .theme-live-logo {
        width: 34px;
        height: 34px;
        margin: 0 auto 17px;
        border-radius: 10px;
        transition: background .25s ease;
    }

    .theme-live-menu {
        height: 7px;
        border-radius: 10px;
        background: rgba(255,255,255,.40);
        margin-bottom: 9px;
    }

    .theme-live-menu.active {
        height: 23px;
        border-radius: 7px;
        transition: background .25s ease;
    }

    .theme-live-content {
        flex: 1;
        padding: 15px;
    }

    .theme-live-navbar {
        height: 25px;
        background: white;
        border-radius: 8px;
        margin-bottom: 12px;
        box-shadow: 0 2px 7px rgba(0,0,0,.05);
    }

    .theme-live-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 9px;
    }

    .theme-live-stat {
        height: 54px;
        background: white;
        border-radius: 10px;
        border-top: 5px solid;
        box-shadow: 0 2px 7px rgba(0,0,0,.05);
        transition: border-color .25s ease;
    }

    @media (max-width: 768px) {
        .theme-live-preview-screen {
            min-height: 120px;
        }

        .theme-live-sidebar {
            width: 75px;
        }
    }
</style>


<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-11">

            {{-- =====================================================
                 ENCABEZADO
            ====================================================== --}}

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="bi bi-gear-fill me-2"></i>
                        Configuración
                    </h2>

                    <p class="text-muted mb-0">
                        Personaliza la identidad, región, apariencia y facturación de tu negocio
                    </p>
                </div>

            </div>


            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">


                    <form
                        action="{{ route('settings.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        @csrf


                        {{-- =================================================
                             DATOS DE LA EMPRESA
                        ================================================== --}}

                        <h5 class="fw-bold text-primary mb-3">

                            <i class="bi bi-shop me-2"></i>
                            Datos de la Empresa

                        </h5>


                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    Nombre del Restaurante
                                </label>

                                <input
                                    type="text"
                                    name="company_name"
                                    class="form-control"
                                    value="{{ $settings['company_name'] ?? '' }}"
                                    placeholder="Ej: El Capitán - Cevichería y Más"
                                    required
                                >

                            </div>


                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    Teléfono / Pedidos
                                </label>

                                <input
                                    type="text"
                                    name="company_phone"
                                    class="form-control"
                                    value="{{ $settings['company_phone'] ?? '' }}"
                                    placeholder="Ej: 999-888-777"
                                >

                            </div>


                            <div class="col-12">

                                <label class="form-label fw-bold">
                                    Dirección
                                </label>

                                <input
                                    type="text"
                                    name="company_address"
                                    class="form-control"
                                    value="{{ $settings['company_address'] ?? '' }}"
                                    placeholder="Ej: Av. Principal 123, Ica"
                                >

                            </div>

                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =================================================
                             REGIÓN Y SISTEMA
                        ================================================== --}}

                        <h5 class="fw-bold text-primary mb-3">

                            <i class="bi bi-globe-americas me-2"></i>
                            Región y Sistema

                        </h5>


                        <div class="row g-3 mb-4">


                            <div class="col-md-6">

                                <label class="form-label fw-bold">

                                    <i class="bi bi-clock"></i>
                                    Zona Horaria

                                </label>


                                <select
                                    name="timezone"
                                    class="form-select"
                                >

                                    @foreach($timezones as $tz => $label)

                                        <option
                                            value="{{ $tz }}"
                                            {{ ($settings['timezone'] ?? 'America/Lima') == $tz ? 'selected' : '' }}
                                        >

                                            {{ $label }}

                                        </option>

                                    @endforeach

                                </select>


                                <small class="text-muted">

                                    Hora actual del sistema:

                                    <strong>
                                        {{ \Carbon\Carbon::now()->format('H:i:s') }}
                                    </strong>

                                </small>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">
                                    Moneda
                                </label>


                                <select
                                    name="currency_symbol"
                                    class="form-select"
                                >

                                    <option
                                        value="S/"
                                        {{ ($settings['currency_symbol'] ?? '') == 'S/' ? 'selected' : '' }}
                                    >
                                        S/ (Soles)
                                    </option>

                                    <option
                                        value="$"
                                        {{ ($settings['currency_symbol'] ?? '') == '$' ? 'selected' : '' }}
                                    >
                                        $ (Dólares)
                                    </option>

                                    <option
                                        value="€"
                                        {{ ($settings['currency_symbol'] ?? '') == '€' ? 'selected' : '' }}
                                    >
                                        € (Euros)
                                    </option>

                                </select>

                            </div>


                            <div class="col-md-12">

                                <label class="form-label fw-bold">
                                    Mensaje Pie de Ticket
                                </label>

                                <input
                                    type="text"
                                    name="ticket_footer"
                                    class="form-control"
                                    value="{{ $settings['ticket_footer'] ?? '¡Gracias por su visita!' }}"
                                >

                            </div>


                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    <i class="bi bi-bullseye me-1"></i>
                                    Meta mensual de ventas
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        {{ $settings['currency_symbol'] ?? 'S/' }}
                                    </span>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="monthly_goal"
                                        class="form-control"
                                        value="{{ old('monthly_goal', $settings['monthly_goal'] ?? 5000) }}"
                                        placeholder="5000.00"
                                    >

                                </div>

                                <small class="text-muted">
                                    Esta meta se utiliza para calcular el progreso mensual del Dashboard.
                                </small>

                            </div>

                        </div>


                        {{-- =================================================
                             CELEBRACIÓN DE META MENSUAL
                        ================================================== --}}

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 bg-light">

                                    <div class="form-check form-switch mb-1">

                                        <input
                                            type="hidden"
                                            name="goal_notification_enabled"
                                            value="0"
                                        >

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="goal_notification_enabled"
                                            name="goal_notification_enabled"
                                            value="1"
                                            {{ ($settings['goal_notification_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                        >

                                        <label
                                            class="form-check-label fw-bold"
                                            for="goal_notification_enabled"
                                        >
                                            <i class="bi bi-bell-fill me-1"></i>
                                            Notificación de meta
                                        </label>

                                    </div>

                                    <small class="text-muted">
                                        Muestra una felicitación cuando se alcanza o supera la meta mensual.
                                    </small>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 bg-light">

                                    <div class="form-check form-switch mb-1">

                                        <input
                                            type="hidden"
                                            name="goal_confetti_enabled"
                                            value="0"
                                        >

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="goal_confetti_enabled"
                                            name="goal_confetti_enabled"
                                            value="1"
                                            {{ ($settings['goal_confetti_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                        >

                                        <label
                                            class="form-check-label fw-bold"
                                            for="goal_confetti_enabled"
                                        >
                                            <i class="bi bi-stars me-1"></i>
                                            Confetis de celebración
                                        </label>

                                    </div>

                                    <small class="text-muted">
                                        Muestra confetis mientras está visible la felicitación.
                                    </small>

                                </div>
                            </div>

                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =================================================
                             APARIENCIA DEL SISTEMA
                        ================================================== --}}

                        @php

                            $currentTheme =
                                $settings['dashboard_theme']
                                ?? 'ocean-orange';


                            $themes = [

                                'ocean-orange' => [

                                    'name' =>
                                        'Océano y Naranja',

                                    'description' =>
                                        'Fresco, profesional y relacionado con el mar.',

                                    'sidebar' =>
                                        '#063970',

                                    'sidebar2' =>
                                        '#0b4f8a',

                                    'primary' =>
                                        '#ff8c00',

                                    'background' =>
                                        '#eef8fc',

                                    'colors' => [
                                        '#063970',
                                        '#0b84c6',
                                        '#ff8c00',
                                        '#eef8fc'
                                    ]

                                ],


                                'lime-blue' => [

                                    'name' =>
                                        'Verde Lima y Azul',

                                    'description' =>
                                        'Natural, fresco y con fuerte contraste visual.',

                                    'sidebar' =>
                                        '#063970',

                                    'sidebar2' =>
                                        '#0b4f8a',

                                    'primary' =>
                                        '#84cc16',

                                    'background' =>
                                        '#f2fbf3',

                                    'colors' => [
                                        '#063970',
                                        '#22a06b',
                                        '#84cc16',
                                        '#f2fbf3'
                                    ]

                                ],


                                'purple-orange' => [

                                    'name' =>
                                        'Morado y Naranja',

                                    'description' =>
                                        'Creativo, vibrante y moderno.',

                                    'sidebar' =>
                                        '#4c1d95',

                                    'sidebar2' =>
                                        '#7c3aed',

                                    'primary' =>
                                        '#ff8c00',

                                    'background' =>
                                        '#f7f2ff',

                                    'colors' => [
                                        '#4c1d95',
                                        '#7c3aed',
                                        '#ff8c00',
                                        '#f7f2ff'
                                    ]

                                ],


                                'sand-navy' => [

                                    'name' =>
                                        'Arena y Azul Marino',

                                    'description' =>
                                        'Cálido, sobrio y elegante.',

                                    'sidebar' =>
                                        '#063970',

                                    'sidebar2' =>
                                        '#0b4f8a',

                                    'primary' =>
                                        '#c98a52',

                                    'background' =>
                                        '#f7f1e9',

                                    'colors' => [
                                        '#063970',
                                        '#c98a52',
                                        '#e7c6a5',
                                        '#f7f1e9'
                                    ]

                                ],


                                'teal-amber' => [

                                    'name' =>
                                        'Teal y Ámbar',

                                    'description' =>
                                        'Minimalista, moderno y equilibrado.',

                                    'sidebar' =>
                                        '#07575b',

                                    'sidebar2' =>
                                        '#0f8b8d',

                                    'primary' =>
                                        '#f59e0b',

                                    'background' =>
                                        '#eef9f8',

                                    'colors' => [
                                        '#07575b',
                                        '#0f8b8d',
                                        '#f59e0b',
                                        '#eef9f8'
                                    ]

                                ],


                                'wine-blue' => [

                                    'name' =>
                                        'Vino y Azul',

                                    'description' =>
                                        'Premium, diferente y sofisticado.',

                                    'sidebar' =>
                                        '#791837',

                                    'sidebar2' =>
                                        '#3346a8',

                                    'primary' =>
                                        '#d94f70',

                                    'background' =>
                                        '#faf0f4',

                                    'colors' => [
                                        '#791837',
                                        '#3346a8',
                                        '#d94f70',
                                        '#faf0f4'
                                    ]

                                ]

                            ];

                        @endphp


                        <div class="theme-section mb-4">


                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    justify-content-between
                                    flex-wrap
                                    gap-2
                                    mb-4
                                "
                            >

                                <div>

                                    <h5 class="fw-bold mb-1">

                                        <i class="bi bi-palette-fill me-2"></i>
                                        Tema visual del sistema

                                    </h5>


                                    <p class="text-muted small mb-0">

                                        El administrador puede elegir la apariencia del panel administrativo.

                                    </p>

                                </div>


                                <span
                                    class="
                                        badge
                                        rounded-pill
                                        bg-light
                                        text-dark
                                        border
                                        px-3
                                        py-2
                                    "
                                >

                                    <i class="bi bi-brush me-1"></i>

                                    6 temas disponibles

                                </span>

                            </div>


                            <div class="row g-3">


                                @foreach($themes as $value => $theme)


                                    <div class="col-md-6 col-xl-4">


                                        <label
                                            class="theme-option"
                                            for="theme_{{ $value }}"
                                        >


                                            <input
                                                type="radio"
                                                class="theme-radio"
                                                id="theme_{{ $value }}"
                                                name="dashboard_theme"
                                                value="{{ $value }}"
                                                data-sidebar="{{ $theme['sidebar'] }}"
                                                data-sidebar2="{{ $theme['sidebar2'] }}"
                                                data-primary="{{ $theme['primary'] }}"
                                                data-background="{{ $theme['background'] }}"
                                                {{ $currentTheme === $value ? 'checked' : '' }}
                                            >


                                            <div class="theme-card">


                                                <div class="theme-card-header">


                                                    <div>

                                                        <div class="theme-name">

                                                            {{ $theme['name'] }}

                                                        </div>


                                                        <div class="theme-description">

                                                            {{ $theme['description'] }}

                                                        </div>

                                                    </div>


                                                    <div class="theme-check">

                                                        <i class="bi bi-check-lg"></i>

                                                    </div>


                                                </div>


                                                {{-- MINI PREVIEW --}}

                                                <div class="theme-preview">


                                                    <div
                                                        class="theme-preview-sidebar"
                                                        style="
                                                            background:
                                                                linear-gradient(
                                                                    180deg,
                                                                    {{ $theme['sidebar'] }},
                                                                    {{ $theme['sidebar2'] }}
                                                                );
                                                        "
                                                    ></div>


                                                    <div
                                                        class="theme-preview-main"
                                                        style="
                                                            background:
                                                                {{ $theme['background'] }};
                                                        "
                                                    >


                                                        <div class="theme-preview-top"></div>


                                                        <div class="theme-preview-cards">


                                                            <div
                                                                class="theme-preview-mini"
                                                                style="
                                                                    border-color:
                                                                        {{ $theme['primary'] }};
                                                                "
                                                            ></div>


                                                            <div
                                                                class="theme-preview-mini"
                                                                style="
                                                                    border-color:
                                                                        {{ $theme['sidebar2'] }};
                                                                "
                                                            ></div>


                                                            <div
                                                                class="theme-preview-mini"
                                                                style="
                                                                    border-color:
                                                                        {{ $theme['primary'] }};
                                                                "
                                                            ></div>


                                                        </div>


                                                    </div>


                                                </div>


                                                {{-- PALETA --}}

                                                <div class="theme-colors">


                                                    @foreach($theme['colors'] as $color)


                                                        <span
                                                            class="theme-color"
                                                            style="
                                                                background:
                                                                    {{ $color }};
                                                            "
                                                        ></span>


                                                    @endforeach


                                                    <span class="theme-current-label">

                                                        Tema actual

                                                    </span>


                                                </div>


                                            </div>


                                        </label>


                                    </div>


                                @endforeach


                            </div>


                            {{-- =================================================
                                 PREVISUALIZACIÓN EN VIVO
                            ================================================== --}}

                            <div class="theme-live-preview">


                                <div class="theme-live-preview-header">


                                    <div>

                                        <div class="fw-bold small">

                                            Previsualización

                                        </div>


                                        <small class="text-muted">

                                            Así se verá aproximadamente el panel.

                                        </small>

                                    </div>


                                    <span
                                        class="
                                            badge
                                            bg-light
                                            text-dark
                                            border
                                        "
                                        id="themePreviewName"
                                    >

                                        {{ $themes[$currentTheme]['name'] ?? 'Océano y Naranja' }}

                                    </span>


                                </div>


                                <div
                                    class="theme-live-preview-screen"
                                    id="themePreviewScreen"
                                    style="
                                        background:
                                            {{ $themes[$currentTheme]['background'] ?? '#eef8fc' }};
                                    "
                                >


                                    <div
                                        class="theme-live-sidebar"
                                        id="themePreviewSidebar"
                                        style="
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    {{ $themes[$currentTheme]['sidebar'] ?? '#063970' }},
                                                    {{ $themes[$currentTheme]['sidebar2'] ?? '#0b4f8a' }}
                                                );
                                        "
                                    >


                                        <div
                                            class="theme-live-logo"
                                            id="themePreviewLogo"
                                            style="
                                                background:
                                                    {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                            "
                                        ></div>


                                        <div class="theme-live-menu active"
                                             id="themePreviewMenuActive"
                                             style="
                                                background:
                                                    {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                             "
                                        ></div>


                                        <div class="theme-live-menu"></div>

                                        <div class="theme-live-menu"></div>

                                        <div class="theme-live-menu"></div>


                                    </div>


                                    <div class="theme-live-content">


                                        <div class="theme-live-navbar"></div>


                                        <div class="theme-live-stats">


                                            <div
                                                class="theme-live-stat"
                                                data-theme-stat
                                                style="
                                                    border-color:
                                                        {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                                "
                                            ></div>


                                            <div
                                                class="theme-live-stat"
                                                data-theme-stat
                                                style="
                                                    border-color:
                                                        {{ $themes[$currentTheme]['sidebar2'] ?? '#0b4f8a' }};
                                                "
                                            ></div>


                                            <div
                                                class="theme-live-stat"
                                                data-theme-stat
                                                style="
                                                    border-color:
                                                        {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                                "
                                            ></div>


                                        </div>


                                    </div>


                                </div>


                            </div>


                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =================================================
                             LOGOTIPO
                        ================================================== --}}

                        <h5 class="fw-bold text-primary mb-3">

                            <i class="bi bi-image me-2"></i>
                            Logotipo

                        </h5>


                        <div class="row align-items-center mb-4">


                            <div class="col-md-8">

                                <label class="form-label">
                                    Subir Logo (Ticket y Sistema)
                                </label>

                                <input
                                    type="file"
                                    name="company_logo"
                                    class="form-control"
                                    accept="image/*"
                                >

                            </div>


                            <div class="col-md-4 text-center">


                                @if(isset($settings['company_logo']) && $settings['company_logo'])


                                    <img
                                        src="{{ asset('storage/'.$settings['company_logo']) }}"
                                        class="img-thumbnail"
                                        style="max-height:80px;"
                                        alt="Logo"
                                    >


                                @else


                                    <div
                                        class="
                                            p-3
                                            border
                                            rounded
                                            bg-light
                                            text-muted
                                        "
                                    >

                                        <i class="bi bi-image fs-1"></i>

                                    </div>


                                @endif


                            </div>


                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =================================================
                             SUNAT
                        ================================================== --}}

                        <h5 class="fw-bold text-danger mb-3">

                            <i class="bi bi-receipt-cutoff me-2"></i>

                            Facturación Electrónica · SUNAT

                        </h5>


                        @php

                            $ambiente =
                                $settings['sunat_environment']
                                ?? 'beta';


                            $tieneCert =
                                !empty(
                                    $settings['sunat_cert_path']
                                );

                        @endphp


                        <div
                            class="
                                alert
                                {{ $ambiente === 'produccion' ? 'alert-danger' : 'alert-warning' }}
                                py-2
                                mb-3
                            "
                        >


                            <strong>
                                Ambiente actual:
                            </strong>


                            {{ $ambiente === 'produccion'
                                ? 'PRODUCCIÓN (emisión real)'
                                : 'BETA (pruebas)'
                            }}


                            @if(!$tieneCert)

                                ·

                                <span class="badge bg-secondary">

                                    Usando certificado demo

                                </span>

                            @endif


                        </div>


                        <div class="row g-3 mb-3">


                            <div class="col-md-4">

                                <label class="form-label fw-bold">

                                    RUC del emisor

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="sunat_ruc"
                                    class="form-control"
                                    value="{{ $settings['sunat_ruc'] ?? '' }}"
                                    maxlength="11"
                                    pattern="\d{11}"
                                    placeholder="20000000001"
                                >

                            </div>


                            <div class="col-md-5">

                                <label class="form-label fw-bold">

                                    Razón social

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="sunat_razon_social"
                                    class="form-control"
                                    value="{{ $settings['sunat_razon_social'] ?? '' }}"
                                    placeholder="MI EMPRESA SAC"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Nombre comercial

                                </label>


                                <input
                                    type="text"
                                    name="sunat_nombre_comercial"
                                    class="form-control"
                                    value="{{ $settings['sunat_nombre_comercial'] ?? '' }}"
                                >

                            </div>


                            <div class="col-12">

                                <label class="form-label fw-bold">

                                    Dirección fiscal

                                </label>


                                <input
                                    type="text"
                                    name="sunat_direccion_fiscal"
                                    class="form-control"
                                    value="{{ $settings['sunat_direccion_fiscal'] ?? '' }}"
                                    placeholder="AV. PRINCIPAL 123"
                                >

                            </div>


                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Ubigeo
                                </label>

                                <input
                                    type="text"
                                    name="sunat_ubigeo"
                                    class="form-control"
                                    value="{{ $settings['sunat_ubigeo'] ?? '150101' }}"
                                    maxlength="6"
                                    placeholder="150101"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">
                                    Departamento
                                </label>

                                <input
                                    type="text"
                                    name="sunat_departamento"
                                    class="form-control"
                                    value="{{ $settings['sunat_departamento'] ?? 'LIMA' }}"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">
                                    Provincia
                                </label>

                                <input
                                    type="text"
                                    name="sunat_provincia"
                                    class="form-control"
                                    value="{{ $settings['sunat_provincia'] ?? 'LIMA' }}"
                                >

                            </div>


                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Distrito
                                </label>

                                <input
                                    type="text"
                                    name="sunat_distrito"
                                    class="form-control"
                                    value="{{ $settings['sunat_distrito'] ?? 'LIMA' }}"
                                >

                            </div>


                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Urbanización
                                </label>

                                <input
                                    type="text"
                                    name="sunat_urbanizacion"
                                    class="form-control"
                                    value="{{ $settings['sunat_urbanizacion'] ?? '-' }}"
                                >

                            </div>


                        </div>


                        <div class="row g-3 mb-3">


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Ambiente

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <select
                                    name="sunat_environment"
                                    class="form-select fw-bold"
                                >

                                    <option
                                        value="beta"
                                        {{ $ambiente === 'beta' ? 'selected' : '' }}
                                    >

                                        BETA (Pruebas)

                                    </option>


                                    <option
                                        value="produccion"
                                        {{ $ambiente === 'produccion' ? 'selected' : '' }}
                                    >

                                        PRODUCCIÓN (Real)

                                    </option>

                                </select>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    IGV (%)

                                </label>


                                <input
                                    type="number"
                                    step="0.01"
                                    name="sunat_igv_rate"
                                    class="form-control"
                                    value="{{ $settings['sunat_igv_rate'] ?? '18' }}"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Usuario SOL

                                </label>


                                <input
                                    type="text"
                                    name="sunat_sol_user"
                                    class="form-control"
                                    value="{{ $settings['sunat_sol_user'] ?? 'MODDATOS' }}"
                                >


                                <small class="text-muted">

                                    En BETA:

                                    <code>
                                        MODDATOS
                                    </code>

                                </small>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Clave SOL

                                </label>


                                <input
                                    type="password"
                                    name="sunat_sol_pass"
                                    class="form-control"
                                    value="{{ $settings['sunat_sol_pass'] ?? '' }}"
                                    placeholder="••••••••"
                                >

                            </div>


                        </div>


                        <div class="row g-3 mb-3">


                            <div class="col-md-8">


                                <label class="form-label fw-bold">

                                    <i class="bi bi-shield-lock"></i>

                                    Certificado digital (.pfx)

                                </label>


                                <input
                                    type="file"
                                    name="sunat_cert_file"
                                    class="form-control"
                                    accept=".pfx,.p12"
                                >


                                @if($tieneCert)


                                    <small class="text-success">

                                        <i class="bi bi-check-circle"></i>

                                        Cargado:

                                        <code>
                                            {{ $settings['sunat_cert_path'] }}
                                        </code>

                                    </small>


                                @else


                                    <small class="text-warning">

                                        Sin certificado real.

                                        Para BETA usa el demo

                                        (<code>php artisan sunat:cert:demo</code>).

                                        Para producción sube tu

                                        <code>.pfx</code>

                                        oficial.

                                    </small>


                                @endif


                            </div>


                            <div class="col-md-4">

                                <label class="form-label fw-bold">

                                    Contraseña del .pfx

                                </label>


                                <input
                                    type="password"
                                    name="sunat_cert_password"
                                    class="form-control"
                                    value="{{ $settings['sunat_cert_password'] ?? '' }}"
                                    placeholder="(opcional, depende del cert)"
                                >

                            </div>


                        </div>


                        {{-- =================================================
                             MÉTODOS DE PAGO - QR
                        ================================================== --}}

                        <hr class="text-muted opacity-25">

                        <div class="mb-4">
                            <h5 class="fw-bold mb-1" style="color:#198754;">
                                <i class="bi bi-qr-code me-2"></i>
                                Métodos de pago
                            </h5>

                            <p class="text-muted small mb-4">
                                Configura los códigos QR que se mostrarán al cobrar mediante Yape o Plin.
                            </p>

                            <div class="row g-4">

                                {{-- YAPE --}}
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100"
                                         style="border-radius:18px !important; border-top:4px solid #742284 !important;">

                                        <div class="card-body p-4">

                                            <div class="d-flex align-items-center mb-3">
                                                <div class="d-flex align-items-center justify-content-center me-3"
                                                     style="width:46px;height:46px;border-radius:13px;background:#f5e9f8;color:#742284;font-size:1.4rem;">
                                                    <i class="bi bi-qr-code"></i>
                                                </div>

                                                <div>
                                                    <div class="fw-bold fs-5" style="color:#742284;">
                                                        Yape
                                                    </div>
                                                    <small class="text-muted">
                                                        Código QR para pagos con Yape
                                                    </small>
                                                </div>
                                            </div>

                                            @if(!empty($settings['yape_qr']))
                                                <div class="text-center mb-3">
                                                    <img
                                                        src="{{ asset('storage/' . $settings['yape_qr']) }}"
                                                        alt="QR Yape"
                                                        style="
                                                            width:170px;
                                                            height:170px;
                                                            object-fit:contain;
                                                            border-radius:15px;
                                                            border:1px solid #e5e7eb;
                                                            padding:8px;
                                                            background:white;
                                                        "
                                                    >
                                                </div>

                                                <div class="text-center mb-3">
                                                    <span class="badge rounded-pill"
                                                          style="background:#f5e9f8;color:#742284;">
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                        QR configurado
                                                    </span>
                                                </div>
                                            @else
                                                <div class="text-center py-4 mb-3"
                                                     style="border:2px dashed #d8b9df;border-radius:15px;background:#fcf8fd;">
                                                    <i class="bi bi-qr-code d-block mb-2"
                                                       style="font-size:2.5rem;color:#742284;"></i>
                                                    <span class="text-muted small">
                                                        Aún no se ha cargado un QR
                                                    </span>
                                                </div>
                                            @endif

                                            <label class="form-label fw-bold">
                                                {{ !empty($settings['yape_qr']) ? 'Cambiar QR' : 'Subir QR' }}
                                            </label>

                                            <input
                                                type="file"
                                                name="yape_qr"
                                                class="form-control"
                                                accept="image/png,image/jpeg,image/webp"
                                            >

                                            <small class="text-muted">
                                                PNG, JPG o WEBP. Máximo 2 MB.
                                            </small>

                                        </div>
                                    </div>
                                </div>


                                {{-- PLIN --}}
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100"
                                         style="border-radius:18px !important; border-top:4px solid #00a884 !important;">

                                        <div class="card-body p-4">

                                            <div class="d-flex align-items-center mb-3">
                                                <div class="d-flex align-items-center justify-content-center me-3"
                                                     style="width:46px;height:46px;border-radius:13px;background:#e8f8f3;color:#00a884;font-size:1.4rem;">
                                                    <i class="bi bi-qr-code"></i>
                                                </div>

                                                <div>
                                                    <div class="fw-bold fs-5" style="color:#00a884;">
                                                        Plin
                                                    </div>
                                                    <small class="text-muted">
                                                        Código QR para pagos con Plin
                                                    </small>
                                                </div>
                                            </div>

                                            @if(!empty($settings['plin_qr']))
                                                <div class="text-center mb-3">
                                                    <img
                                                        src="{{ asset('storage/' . $settings['plin_qr']) }}"
                                                        alt="QR Plin"
                                                        style="
                                                            width:170px;
                                                            height:170px;
                                                            object-fit:contain;
                                                            border-radius:15px;
                                                            border:1px solid #e5e7eb;
                                                            padding:8px;
                                                            background:white;
                                                        "
                                                    >
                                                </div>

                                                <div class="text-center mb-3">
                                                    <span class="badge rounded-pill"
                                                          style="background:#e8f8f3;color:#008b6d;">
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                        QR configurado
                                                    </span>
                                                </div>
                                            @else
                                                <div class="text-center py-4 mb-3"
                                                     style="border:2px dashed #a7e3d4;border-radius:15px;background:#f5fcfa;">
                                                    <i class="bi bi-qr-code d-block mb-2"
                                                       style="font-size:2.5rem;color:#00a884;"></i>
                                                    <span class="text-muted small">
                                                        Aún no se ha cargado un QR
                                                    </span>
                                                </div>
                                            @endif

                                            <label class="form-label fw-bold">
                                                {{ !empty($settings['plin_qr']) ? 'Cambiar QR' : 'Subir QR' }}
                                            </label>

                                            <input
                                                type="file"
                                                name="plin_qr"
                                                class="form-control"
                                                accept="image/png,image/jpeg,image/webp"
                                            >

                                            <small class="text-muted">
                                                PNG, JPG o WEBP. Máximo 2 MB.
                                            </small>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- =================================================
                             GUARDAR
                        ================================================== --}}

                        <div
                            class="
                                mt-5
                                d-flex
                                justify-content-end
                            "
                        >

                            <button
                                type="submit"
                                class="
                                    btn
                                    btn-primary
                                    px-5
                                    fw-bold
                                    shadow
                                "
                            >

                                <i class="bi bi-save me-2"></i>

                                Guardar Configuración

                            </button>

                        </div>


                    </form>


                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | PREVISUALIZACIÓN DE TEMAS
    |--------------------------------------------------------------------------
    */

    const themeRadios =
        document.querySelectorAll(
            'input[name="dashboard_theme"]'
        );


    const previewScreen =
        document.getElementById(
            'themePreviewScreen'
        );


    const previewSidebar =
        document.getElementById(
            'themePreviewSidebar'
        );


    const previewLogo =
        document.getElementById(
            'themePreviewLogo'
        );


    const previewActive =
        document.getElementById(
            'themePreviewMenuActive'
        );


    const previewName =
        document.getElementById(
            'themePreviewName'
        );


    const previewStats =
        document.querySelectorAll(
            '[data-theme-stat]'
        );


    themeRadios.forEach(
        radio => {


            radio.addEventListener(
                'change',
                function () {


                    const sidebar =
                        this.dataset.sidebar;


                    const sidebar2 =
                        this.dataset.sidebar2;


                    const primary =
                        this.dataset.primary;


                    const background =
                        this.dataset.background;


                    const card =
                        this.closest(
                            '.theme-card'
                        );


                    const themeName =
                        card
                            ?.querySelector(
                                '.theme-name'
                            )
                            ?.textContent
                            ?.trim();


                    /*
                     * Fondo
                     */

                    if (previewScreen) {

                        previewScreen.style.background =
                            background;

                    }


                    /*
                     * Sidebar
                     */

                    if (previewSidebar) {

                        previewSidebar.style.background =
                            `linear-gradient(
                                180deg,
                                ${sidebar},
                                ${sidebar2}
                            )`;

                    }


                    /*
                     * Logo
                     */

                    if (previewLogo) {

                        previewLogo.style.background =
                            primary;

                    }


                    /*
                     * Menú activo
                     */

                    if (previewActive) {

                        previewActive.style.background =
                            primary;

                    }


                    /*
                     * Nombre
                     */

                    if (previewName) {

                        previewName.textContent =
                            themeName ??
                            'Tema seleccionado';

                    }


                    /*
                     * Tarjetas
                     */

                    if (
                        previewStats.length >= 3
                    ) {

                        previewStats[0]
                            .style
                            .borderColor =
                                primary;


                        previewStats[1]
                            .style
                            .borderColor =
                                sidebar2;


                        previewStats[2]
                            .style
                            .borderColor =
                                primary;

                    }


                }
            );


        }
    );

</script>

@endpush
