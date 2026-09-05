<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Iniciar Sesión — {{ \App\Models\Setting::where('key','company_name')->value('value') ?? 'Mi Restaurante' }}
    </title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    >

    <style>

        /* =========================================================
           CONFIGURACIÓN GENERAL
        ========================================================= */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --orange: #ff8c00;
            --orange-hover: #e97d00;

            --blue-dark: #063970;
            --blue-deep: #042a54;
            --blue-medium: #0b4f8a;

            --input-bg: #edf5ff;
            --input-border: #d4e2f0;

            --text-dark: #172033;
            --text-muted: #64748b;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;

            height: 100vh;

            display: flex;

            background: #eef5fb;

            overflow: hidden;
        }


        /* =========================================================
           PANEL IZQUIERDO - IMAGEN
        ========================================================= */

        .login-hero {
            flex: 1;

            position: relative;

            display: none;

            overflow: hidden;
        }

        @media (min-width: 900px) {

            .login-hero {
                display: block;
            }

        }

        .login-hero img {
            width: 100%;
            height: 100%;

            object-fit: cover;
            object-position: center;
        }

        /* Capa azul/naranja sobre la imagen */

        .login-hero::after {
            content: '';

            position: absolute;

            inset: 0;

            background:
                linear-gradient(
                    135deg,
                    rgba(4, 42, 84, 0.76) 0%,
                    rgba(4, 42, 84, 0.45) 50%,
                    rgba(255, 140, 0, 0.25) 100%
                );
        }


        /* =========================================================
           CONTENIDO SOBRE LA IMAGEN
        ========================================================= */

        .hero-content {
            position: absolute;

            inset: 0;

            z-index: 2;

            display: flex;
            flex-direction: column;
            justify-content: flex-end;

            padding: 48px;

            color: white;
        }

        .hero-badge {
            width: fit-content;

            display: inline-flex;
            align-items: center;

            gap: 8px;

            margin-bottom: 20px;

            padding: 7px 16px;

            background: rgba(255, 140, 0, 0.92);

            color: white;

            border-radius: 50px;

            font-size: .75rem;
            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1px;

            backdrop-filter: blur(6px);

            box-shadow:
                0 5px 18px rgba(255, 140, 0, 0.25);
        }

        .hero-title {
            margin-bottom: 14px;

            font-size: 2.6rem;
            font-weight: 800;

            line-height: 1.15;

            letter-spacing: -1px;

            text-shadow:
                0 2px 20px rgba(0, 0, 0, 0.42);
        }

        .hero-subtitle {
            max-width: 410px;

            margin-bottom: 36px;

            font-size: 1rem;

            line-height: 1.6;

            opacity: .92;
        }


        /* =========================================================
           ESTADÍSTICAS
        ========================================================= */

        .hero-stats {
            display: flex;

            gap: 30px;
        }

        .hero-stat {
            display: flex;
            flex-direction: column;
        }

        .hero-stat strong {
            color: var(--orange);

            font-size: 1.6rem;
            font-weight: 800;
        }

        .hero-stat span {
            margin-top: 1px;

            font-size: .72rem;

            text-transform: uppercase;

            letter-spacing: .5px;

            opacity: .80;
        }


        /* =========================================================
           PANEL DERECHO
        ========================================================= */

        .login-panel {
            width: 100%;

            /*
             * Un poco más ancho para permitir que el nombre
             * EL CAPITÁN - CEVICHERÍA Y MÁS aparezca completo.
             */
            max-width: 540px;

            padding: 48px 52px;

            position: relative;

            overflow: hidden;

            display: flex;
            flex-direction: column;
            justify-content: center;

            background:
                linear-gradient(
                    180deg,
                    #0a477f 0%,
                    var(--blue-dark) 42%,
                    var(--blue-deep) 100%
                );
        }


        /* Círculo decorativo superior */

        .login-panel::before {
            content: '';

            position: absolute;

            width: 300px;
            height: 300px;

            top: -90px;
            right: -90px;

            border-radius: 50%;

            background:
                rgba(255, 255, 255, 0.06);

            pointer-events: none;
        }


        /* Círculo decorativo inferior */

        .login-panel::after {
            content: '';

            position: absolute;

            width: 210px;
            height: 210px;

            bottom: -75px;
            left: -70px;

            border-radius: 50%;

            background:
                rgba(255, 255, 255, 0.05);

            pointer-events: none;
        }


        /* =========================================================
           MARCA / LOGO
        ========================================================= */

        .login-brand {`r`n            justify-content: center;
            position: relative;

            z-index: 2;

            display: flex;
            align-items: center;

            gap: 14px;

            margin-bottom: 42px;

            width: 100%;
        }

        .login-logo {
            width: 72px; height: 72px;

            flex: 0 0 72px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: var(--orange);

            color: white;

            border-radius: 14px;

            font-size: 24px;

            box-shadow:
                0 6px 20px rgba(255, 140, 0, 0.42);
        }

        .login-brand-text {
            flex: 1;

            min-width: 0;
        }

        .login-brand-text h1 {
            color: white;

            font-size: 1.25rem; font-weight: 800;

            line-height: 1.2;

            letter-spacing: -0.25px;

            /*
             * Mantiene el nombre en una sola línea.
             */
            white-space: nowrap;
        }

        .login-brand-text p {
            margin-top: 5px;

            color:
                rgba(255, 255, 255, 0.62);

            font-size: .82rem;

            line-height: 1.2;
        }


        /* =========================================================
           BIENVENIDA
        ========================================================= */

        .login-heading {
            position: relative;

            z-index: 2;

            margin-bottom: 32px;
        }

        .login-heading h2 {
            margin-bottom: 9px;

            color: white;

            font-size: 1.75rem;
            font-weight: 800;

            line-height: 1.2;

            letter-spacing: -0.5px;
        }

        .login-heading p {
            color:
                rgba(255, 255, 255, 0.68);

            font-size: .84rem;

            line-height: 1.5;
        }


        /* =========================================================
           FORMULARIO
        ========================================================= */

        form {
            position: relative;

            z-index: 2;
        }

        .field-group {
            margin-bottom: 20px;
        }

        .field-group label {
            display: block;

            margin-bottom: 8px;

            color:
                rgba(255, 255, 255, 0.80);

            font-size: .77rem;
            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .5px;
        }

        .field-wrap {
            position: relative;

            width: 100%;
        }


        /* =========================================================
           ICONOS DE LOS INPUTS
        ========================================================= */

        .field-icon {
            position: absolute;

            left: 16px;

            top: 50%;

            transform: translateY(-50%);

            z-index: 3;

            width: 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--blue-medium);

            font-size: 1rem;

            pointer-events: none;

            transition: color .2s ease;
        }

        .field-wrap:focus-within .field-icon {
            color: var(--orange);
        }


        /* =========================================================
           INPUTS
        ========================================================= */

        .field-wrap input {
            width: 100%;

            height: 49px;

            padding:
                0 48px 0 45px;

            background: var(--input-bg);

            color: var(--text-dark);

            border:
                1.5px solid var(--input-border);

            border-radius: 12px;

            outline: none;

            font-family: 'Inter', sans-serif;

            font-size: .90rem;
            font-weight: 500;

            transition:
                border-color .2s,
                background .2s,
                box-shadow .2s;
        }

        .field-wrap input::placeholder {
            color: #94a3b8;
        }

        .field-wrap input:focus {
            background: white;

            border-color: var(--orange);

            box-shadow:
                0 0 0 3px rgba(255, 140, 0, 0.14);
        }


        /* =========================================================
           BOTÓN VER CONTRASEÑA
        ========================================================= */

        .password-toggle {
            position: absolute;

            right: 8px;

            top: 50%;

            transform: translateY(-50%);

            z-index: 4;

            width: 36px;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;

            background: transparent;

            color: #71869b;

            border-radius: 50%;

            cursor: pointer;

            font-size: 1.05rem;

            transition:
                color .2s,
                background .2s;
        }

        .password-toggle:hover {
            color: var(--orange);

            background:
                rgba(255, 140, 0, 0.10);
        }

        .password-toggle:focus {
            outline: none;

            box-shadow:
                0 0 0 2px rgba(255, 140, 0, 0.15);
        }


        /* =========================================================
           MENSAJES DE ERROR
        ========================================================= */

        .field-error {
            margin-top: 6px;

            display: flex;
            align-items: center;

            gap: 5px;

            color: #ffb0b0;

            font-size: .75rem;
        }

        .alert-error {
            position: relative;

            z-index: 2;

            margin-bottom: 22px;

            padding: 12px 16px;

            display: flex;
            align-items: center;

            gap: 10px;

            background:
                rgba(255, 107, 107, 0.15);

            color: #ffb5b5;

            border:
                1px solid rgba(255, 107, 107, 0.35);

            border-radius: 12px;

            font-size: .83rem;
        }


        /* =========================================================
           BOTÓN INGRESAR
        ========================================================= */

        .btn-login {
            width: 100%;

            margin-top: 8px;

            padding: 15px;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 10px;

            border: none;

            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    #ff9d1c,
                    #ff7a00
                );

            color: white;

            font-family: 'Inter', sans-serif;

            font-size: .82rem;
            font-weight: 700;

            letter-spacing: .2px;

            cursor: pointer;

            box-shadow:
                0 6px 22px rgba(255, 140, 0, 0.40);

            transition:
                transform .15s,
                box-shadow .2s,
                background .2s;
        }

        .btn-login:hover {
            background:
                linear-gradient(
                    135deg,
                    #ff8c00,
                    #e86f00
                );

            transform: translateY(-2px);

            box-shadow:
                0 10px 28px rgba(255, 140, 0, 0.48);
        }

        .btn-login:active {
            transform: translateY(0);
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .login-footer {
            position: relative;

            z-index: 2;

            margin-top: 34px;

            color:
                rgba(255, 255, 255, 0.40);

            text-align: center;

            font-size: .82rem;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1100px) {

            .login-panel {
                max-width: 500px;

                padding-left: 40px;
                padding-right: 40px;
            }

            .login-brand-text h1 {
                font-size: .92rem;
            }

        }


        @media (max-width: 899px) {

            body {
                overflow-y: auto;
            }

            .login-panel {
                min-height: 100vh;

                max-width: 100%;

                padding:
                    36px 28px;
            }

            .login-brand {`r`n            justify-content: center;
                margin-bottom: 34px;
            }

            .login-brand-text h1 {
                font-size: 1rem;
            }

            .login-heading {
                margin-bottom: 28px;
            }

        }


        @media (max-width: 480px) {

            .login-panel {
                padding:
                    30px 22px;
            }

            .login-logo {
                width: 46px;
                height: 46px;

                flex-basis: 46px;
            }

            .login-brand-text h1 {
                font-size: .82rem;
            }

            .login-heading h2 {
                font-size: 1.55rem;
            }

        }

    </style>

</head>


<body>


    <!-- =========================================================
         PANEL IZQUIERDO
    ========================================================== -->

    <div class="login-hero">

        <img
            src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=85&auto=format&fit=crop"
            alt="Restaurante elegante"
            loading="eager"
        >


        <div class="hero-content">


            <div class="hero-badge">

                <i class="bi bi-shop"></i>

                Sistema de Restaurante

            </div>


            <h2 class="hero-title">

                Gestión integral
                <br>
                de tu restaurante

            </h2>


            <p class="hero-subtitle">

                Controla pedidos, mesas, cocina e inventario
                desde un solo lugar. Rápido, moderno y confiable.

            </p>


            <div class="hero-stats">


                <div class="hero-stat">

                    <strong>100%</strong>

                    <span>
                        En tiempo real
                    </span>

                </div>


                <div class="hero-stat">

                    <strong>POS</strong>

                    <span>
                        Integrado
                    </span>

                </div>


                <div class="hero-stat">

                    <strong>KDS</strong>

                    <span>
                        Cocina digital
                    </span>

                </div>


            </div>


        </div>


    </div>


    <!-- =========================================================
         PANEL DERECHO
    ========================================================== -->

    <div class="login-panel">


        <!-- =====================================================
             LOGO Y NOMBRE
        ====================================================== -->

        <div class="login-brand">


            @php

                $logo = \App\Models\Setting::where(
                    'key',
                    'company_logo'
                )->value('value');

            @endphp


            @if($logo)

                <img
                    src="{{ asset('storage/'.$logo) }}"
                    class="login-logo"
                    style="object-fit: cover;"
                    alt="Logo de {{ \App\Models\Setting::where('key','company_name')->value('value') ?? 'Restaurante' }}"
                >

            @else

                <div class="login-logo">

                    <i class="bi bi-shop"></i>

                </div>

            @endif


            <div class="login-brand-text">

                <h1>
                    {{ \App\Models\Setting::where('key','company_name')->value('value') ?? 'Mi Restaurante' }}
                </h1>

                <p>
                    Sistema de Gestión Profesional
                </p>

            </div>


        </div>


        <!-- =====================================================
             BIENVENIDA
        ====================================================== -->

        <div class="login-heading">

            <h2>
                Bienvenido de vuelta
            </h2>

            <p>
                Ingresa tus credenciales para acceder al sistema
            </p>

        </div>


        <!-- =====================================================
             MENSAJE DE ERROR
        ====================================================== -->

        @if(session('error'))

            <div class="alert-error">

                <i class="bi bi-exclamation-triangle-fill"></i>

                {{ session('error') }}

            </div>

        @endif


        <!-- =====================================================
             FORMULARIO
        ====================================================== -->

        <form
            action="{{ route('login.perform') }}"
            method="POST"
            novalidate
        >

            @csrf


            <!-- =================================================
                 CORREO ELECTRÓNICO
            ================================================== -->

            <div class="field-group">

                <label for="email">
                    Correo Electrónico
                </label>


                <div class="field-wrap">

                    <i
                        class="bi bi-envelope-fill field-icon"
                    ></i>


                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="correo@restaurante.com"
                        autocomplete="email"
                        required
                        autofocus
                    >

                </div>


                @error('email')

                    <div class="field-error">

                        <i class="bi bi-exclamation-circle"></i>

                        {{ $message }}

                    </div>

                @enderror

            </div>


            <!-- =================================================
                 CONTRASEÑA
            ================================================== -->

            <div class="field-group">

                <label for="password">
                    Contraseña
                </label>


                <div class="field-wrap">


                    <!-- Candado -->

                    <i
                        class="bi bi-lock-fill field-icon"
                    ></i>


                    <!-- Contraseña -->

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >


                    <!-- Botón mostrar / ocultar contraseña -->

                    <button
                        type="button"
                        class="password-toggle"
                        id="passwordToggle"
                        title="Mostrar contraseña"
                        aria-label="Mostrar contraseña"
                    >

                        <i
                            class="bi bi-eye"
                            id="passwordToggleIcon"
                        ></i>

                    </button>


                </div>


                @error('password')

                    <div class="field-error">

                        <i class="bi bi-exclamation-circle"></i>

                        {{ $message }}

                    </div>

                @enderror


            </div>


            <!-- =================================================
                 BOTÓN INGRESAR
            ================================================== -->

            <button
                type="submit"
                class="btn-login"
            >

                <i class="bi bi-box-arrow-in-right"></i>

                Ingresar al Sistema

            </button>


        </form>


        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <div class="login-footer">

            &copy; {{ date('Y') }}
            Sistema de Restaurante · Desarrollado con Amor

        </div>


    </div>


    <!-- =========================================================
         MOSTRAR / OCULTAR CONTRASEÑA
    ========================================================== -->

    <script>

        const passwordInput =
            document.getElementById('password');

        const passwordToggle =
            document.getElementById('passwordToggle');

        const passwordToggleIcon =
            document.getElementById('passwordToggleIcon');


        passwordToggle.addEventListener('click', function () {

            const passwordIsHidden =
                passwordInput.type === 'password';


            if (passwordIsHidden) {

                /*
                 * Mostrar contraseña
                 */

                passwordInput.type = 'text';


                passwordToggleIcon.classList.remove(
                    'bi-eye'
                );

                passwordToggleIcon.classList.add(
                    'bi-eye-slash'
                );


                passwordToggle.setAttribute(
                    'title',
                    'Ocultar contraseña'
                );

                passwordToggle.setAttribute(
                    'aria-label',
                    'Ocultar contraseña'
                );

            } else {

                /*
                 * Ocultar contraseña
                 */

                passwordInput.type = 'password';


                passwordToggleIcon.classList.remove(
                    'bi-eye-slash'
                );

                passwordToggleIcon.classList.add(
                    'bi-eye'
                );


                passwordToggle.setAttribute(
                    'title',
                    'Mostrar contraseña'
                );

                passwordToggle.setAttribute(
                    'aria-label',
                    'Mostrar contraseña'
                );

            }


            /*
             * Mantener el cursor dentro
             * del campo contraseña.
             */

            passwordInput.focus();

        });

    </script>


</body>

</html>





