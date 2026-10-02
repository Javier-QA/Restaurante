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

        .login-brand {
            justify-content: center;
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

            .login-brand {
            justify-content: center;
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


        /*
             * El formulario conserva el diseño original,
             * pero ahora se ubica a la izquierda.
             */

            .login-panel {

                flex:
                    0 0
                    min(540px, 42vw);

                width:
                    min(540px, 42vw);

                max-width: none;

                min-height: 100vh;
            }


            /*
             * La fotografía utiliza todo el espacio restante.
             */

            .login-hero {

                flex: 1;

                min-width: 0;
            }

        }


        /*
         * Separación sutil entre ambos paneles.
         */

        .login-panel {

            box-shadow:
                12px 0 34px
                rgba(4, 42, 84, .12);
        }


        /*
         * La fotografía mantiene el estilo original,
         * sin convertirla en una landing page.
         */

        .login-hero img {

            width: 100%;
            height: 100%;

            object-fit: cover;

            object-position: center;
        }

        /*
             * La zona del formulario tiene mayor espacio,
             * como en la referencia.
             */

            .login-panel {

                flex: 0 0 56% !important;

                width: 56% !important;
                max-width: none !important;

                min-height: 100vh;

                padding:
                    42px
                    clamp(70px, 9vw, 165px) !important;

                justify-content: center;
            }


            /*
             * Fotografia a la derecha.
             */

            .login-hero {

                flex: 0 0 44% !important;

                width: 44% !important;

                min-height: 100vh;
            }


            /*
             * Todo el formulario queda contenido,
             * en lugar de ocupar el ancho del panel.
             */

            .login-panel > * {

                width: 100%;

                max-width: 460px;

                margin-left: auto;
                margin-right: auto;
            }


            /*
             * Marca superior más compacta.
             */

            .login-brand {

                margin-bottom: 28px !important;

                gap: 11px !important;
            }


            .login-logo {

                width: 66px !important;
                height: 66px !important;

                flex:
                    0 0
                    66px !important;

                border-radius: 14px !important;
            }


            .login-brand-text h1 {

                font-size: 1rem !important;

                line-height: 1.25 !important;
            }


            .login-brand-text p {

                margin-top: 3px !important;

                font-size: .70rem !important;
            }


            /*
             * Encabezado del formulario.
             */

            .login-heading {

                margin-bottom: 25px !important;
            }


            .login-heading h2 {

                margin-bottom: 7px !important;

                font-size: 1.38rem !important;

                letter-spacing: -.35px !important;
            }


            .login-heading p {

                font-size: .74rem !important;

                line-height: 1.45 !important;
            }


            /*
             * Campos.
             */

            .field-group {

                margin-bottom: 17px !important;
            }


            .field-group label {

                margin-bottom: 7px !important;

                font-size: .70rem !important;
            }


            .field-wrap input {

                height: 45px !important;

                border-radius: 9px !important;

                font-size: .78rem !important;
            }


            /*
             * Boton.
             */

            .btn-login {

                min-height: 45px !important;

                margin-top: 5px !important;

                padding: 12px 15px !important;

                border-radius: 9px !important;

                font-size: .76rem !important;
            }


            /*
             * Footer.
             */

            .login-footer {

                margin-top: 25px !important;

                font-size: .67rem !important;
            }


            /* =====================================================
               CONTENIDO DE LA FOTOGRAFIA
            ====================================================== */

            .hero-content {

                justify-content: flex-end !important;

                padding:
                    54px
                    clamp(36px, 4vw, 72px) !important;
            }


            /*
             * No dejamos que el bloque de texto sea demasiado ancho.
             */

            .hero-content > * {

                max-width: 520px;
            }


            .hero-badge {

                margin-bottom: 14px !important;

                padding:
                    6px 11px !important;

                font-size: .59rem !important;

                letter-spacing: .75px !important;
            }


            /*
             * Este es el cambio principal:
             * el titulo deja de ser enorme.
             */

            .hero-title {

                max-width: 520px !important;

                margin-bottom: 13px !important;

                font-size:
                    clamp(
                        1.75rem,
                        2.15vw,
                        2.35rem
                    ) !important;

                line-height: 1.08 !important;

                letter-spacing:
                    -.9px !important;
            }


            .hero-title-accent {

                margin-top: 2px;

                font-size: inherit;
            }


            .hero-subtitle {

                max-width: 500px !important;

                margin-bottom: 18px !important;

                font-size: .73rem !important;

                line-height: 1.55 !important;
            }


            /*
             * Etiquetas inferiores pequeñas,
             * como en la referencia.
             */

            .hero-modules {

                gap: 6px !important;
            }


            .hero-module {

                padding:
                    6px 9px !important;

                gap: 5px !important;

                border-radius: 7px !important;

                font-size: .61rem !important;
            }


            .hero-module i {

                font-size: .68rem;
            }

        }


        /* =========================================================
           PANTALLAS INTERMEDIAS
        ========================================================= */

        @media (min-width: 900px) and (max-width: 1200px) {

            .login-panel {

                padding:
                    36px
                    55px !important;
            }


            .hero-content {

                padding:
                    42px
                    32px !important;
            }


            .hero-title {

                font-size:
                    1.75rem !important;
            }


            .hero-subtitle {

                font-size:
                    .69rem !important;
            }

        }


        /* =========================================================
           LOGIN EL CAPITAN - DISEÑO FINAL CONSOLIDADO
        ========================================================= */

        /* Orden */

        .login-panel {
            order: 1;
        }

        .login-hero {
            order: 2;
        }


        /* =========================================================
           ESCRITORIO
        ========================================================= */

        @media (min-width: 900px) {

            /* -----------------------------------------------------
               PANEL IZQUIERDO
            ----------------------------------------------------- */

            .login-panel {

                position: relative;

                flex: 0 0 56% !important;

                width: 56% !important;
                max-width: none !important;

                min-height: 100vh;

                padding:
                    42px
                    clamp(55px, 8vw, 145px) !important;

                display: flex;
                flex-direction: column;
                justify-content: center;

                overflow: hidden;

                background:
                    radial-gradient(
                        circle at 10% 10%,
                        rgba(46, 130, 193, .24),
                        transparent 32%
                    ),
                    radial-gradient(
                        circle at 92% 92%,
                        rgba(3, 37, 70, .34),
                        transparent 36%
                    ),
                    linear-gradient(
                        145deg,
                        #12639a 0%,
                        #0a5187 38%,
                        #064575 70%,
                        #04375f 100%
                    ) !important;

                box-shadow:
                    10px 0 35px
                    rgba(3, 36, 67, .15);
            }


            /*
             * Decoración discreta del fondo.
             */

                        .login-panel::after {

                content: "";

                position: absolute;

                z-index: 0;

                left: 0;
                bottom: 0;

                width: 235px;
                height: 300px;

                /*
                 * Dos círculos rellenos como la referencia:
                 * - uno grande detrás
                 * - uno pequeño delante
                 * ambos salen de la esquina inferior izquierda
                 */

                background:

                    radial-gradient(
                        circle 150px
                        at 6px 170px,
                        rgba(48, 132, 193, .17) 0 99%,
                        transparent 100%
                    ),

                    radial-gradient(
                        circle 94px
                        at 5px 292px,
                        rgba(62, 145, 204, .23) 0 99%,
                        transparent 100%
                    );

                pointer-events: none;
            }


            /* -----------------------------------------------------
               TARJETA
            ----------------------------------------------------- */

            .login-panel::before {

                content: "";

                position: absolute;

                z-index: 0;

                top: 50%;
                left: 50%;

                transform:
                    translate(-50%, -50%);

                width:
                    min(
                        calc(100% - 110px),
                        425px
                    );

                height:
                    min(
                        calc(100vh - 100px),
                        565px
                    );

                border:
                    1px solid
                    rgba(255,255,255,.38);

                border-radius:
                    20px;

                background:
                    linear-gradient(
                        145deg,
                        #e4f0f8 0%,
                        #dcebf5 48%,
                        #d3e5f1 100%
                    );

                box-shadow:
                    0 28px 58px
                    rgba(2, 28, 52, .25),
                    inset 0 1px 0
                    rgba(255,255,255,.75);

                pointer-events: none;
            }


            /*
             * Contenido de la tarjeta.
             */

            .login-panel > * {

                position: relative;

                z-index: 1;

                width: 100%;

                max-width:
                    350px !important;

                margin-left: auto;
                margin-right: auto;
            }


            /* -----------------------------------------------------
               LOGO
            ----------------------------------------------------- */

            .login-brand {

                display: flex !important;

                flex-direction:
                    column !important;

                align-items:
                    center !important;

                justify-content:
                    center !important;

                gap: 0 !important;

                margin-bottom:
                    21px !important;

                text-align:
                    center !important;
            }


            .login-logo {

                width:
                    88px !important;

                height:
                    88px !important;

                flex:
                    0 0
                    88px !important;

                margin:
                    0 auto !important;

                border-radius:
                    18px !important;

                object-fit:
                    contain !important;

                box-shadow:
                    0 10px 22px
                    rgba(5, 53, 99, .16);
            }


            /*
             * La marca ya está dentro del logo.
             */

            .login-brand-text {
                display: none !important;
            }


            /* -----------------------------------------------------
               TITULO
            ----------------------------------------------------- */

            .login-heading {

                margin-bottom:
                    22px !important;

                text-align:
                    center !important;
            }


            .login-heading h2 {

                margin-bottom:
                    7px !important;

                color:
                    #07365f !important;

                font-size:
                    1.28rem !important;

                font-weight:
                    800 !important;

                line-height:
                    1.18 !important;

                letter-spacing:
                    -.35px !important;
            }


            .login-heading p {

                margin:
                    0 auto !important;

                color:
                    #55748d !important;

                font-size:
                    .70rem !important;

                line-height:
                    1.45 !important;
            }


            /* -----------------------------------------------------
               FORMULARIO
            ----------------------------------------------------- */

            .field-group {

                margin-bottom:
                    14px !important;
            }


            .field-group label {

                display: block;

                margin-bottom:
                    6px !important;

                color:
                    #244d6d !important;

                font-size:
                    .66rem !important;

                font-weight:
                    650 !important;

                text-transform:
                    none !important;

                letter-spacing:
                    0 !important;
            }


            .field-wrap {

                border-radius:
                    10px;
            }


            .field-wrap input {

                height:
                    43px !important;

                padding:
                    0 43px
                    0 41px !important;

                border:
                    1px solid
                    #b5ccdd !important;

                border-radius:
                    10px !important;

                background:
                    rgba(247,251,254,.82)
                    !important;

                color:
                    #102f49 !important;

                font-size:
                    .74rem !important;

                font-weight:
                    500;

                box-shadow:
                    0 2px 5px
                    rgba(34,76,108,.025)
                    !important;

                transition:
                    border-color .18s ease,
                    background .18s ease,
                    box-shadow .18s ease;
            }


            .field-wrap input::placeholder {

                color:
                    #879dad !important;
            }


            .field-wrap input:hover {

                border-color:
                    #91b2ca !important;

                background:
                    rgba(255,255,255,.91)
                    !important;
            }


            .field-wrap input:focus {

                background:
                    #ffffff !important;

                border-color:
                    #ff8c00 !important;

                box-shadow:
                    0 0 0 3px
                    rgba(255,140,0,.10)
                    !important;
            }


            .field-icon {

                left:
                    14px !important;

                color:
                    #557b9c !important;

                font-size:
                    .86rem !important;
            }


            .password-toggle {

                color:
                    #557b9c !important;

                transition:
                    color .18s ease;
            }


            .password-toggle:hover {

                color:
                    #073d6d !important;
            }


            .field-wrap:focus-within
            .field-icon {

                color:
                    #ff8c00 !important;
            }


            /* -----------------------------------------------------
               BOTON PRINCIPAL
            ----------------------------------------------------- */

            .btn-login {

                min-height:
                    43px !important;

                margin-top:
                    5px !important;

                padding:
                    11px 14px !important;

                border:
                    0 !important;

                border-radius:
                    10px !important;

                background:
                    linear-gradient(
                        135deg,
                        #ff9d1c 0%,
                        #ff8500 52%,
                        #f97600 100%
                    ) !important;

                color:
                    #ffffff !important;

                font-size:
                    .72rem !important;

                font-weight:
                    700 !important;

                box-shadow:
                    0 10px 20px
                    rgba(244, 118, 0, .20)
                    !important;

                transition:
                    transform .18s ease,
                    box-shadow .18s ease,
                    filter .18s ease;
            }


            .btn-login:hover {

                transform:
                    translateY(-1px);

                filter:
                    brightness(1.025);

                box-shadow:
                    0 13px 25px
                    rgba(244,118,0,.27)
                    !important;
            }


            .btn-login:active {

                transform:
                    translateY(0);
            }


            /* -----------------------------------------------------
               ERRORES
            ----------------------------------------------------- */

            .alert-error {

                padding:
                    9px 11px !important;

                border:
                    1px solid
                    rgba(220,53,69,.15)
                    !important;

                background:
                    rgba(220,53,69,.06)
                    !important;

                color:
                    #a92838 !important;

                font-size:
                    .66rem !important;
            }


            .field-error {

                color:
                    #c93445 !important;

                font-size:
                    .62rem !important;
            }


            /* -----------------------------------------------------
               FOOTER
            ----------------------------------------------------- */

            .login-footer {

                margin-top:
                    20px !important;

                padding-top:
                    14px;

                border-top:
                    1px solid
                    rgba(60,101,132,.17);

                color:
                    #698399 !important;

                font-size:
                    .60rem !important;
            }


            /* =====================================================
               PANEL DERECHO
            ====================================================== */

            .login-hero {

                position: relative;

                flex:
                    0 0
                    44% !important;

                width:
                    44% !important;

                min-width: 0;
                min-height: 100vh;

                display: block;

                overflow: hidden;
            }


            .login-hero img {

                width: 100%;
                height: 100%;

                object-fit: cover;

                object-position:
                    center center;

                transform:
                    scale(1.015);
            }


            /*
             * Overlay:
             * arriba deja ver la fotografía,
             * abajo mejora la lectura.
             */

            .login-hero::after {

                content: "";

                position: absolute;
                inset: 0;

                z-index: 1;

                background:
                    linear-gradient(
                        180deg,
                        rgba(4,42,84,.08) 0%,
                        rgba(4,42,84,.12) 34%,
                        rgba(4,42,84,.38) 62%,
                        rgba(3,35,67,.88) 100%
                    );

                pointer-events: none;
            }


            /* -----------------------------------------------------
               CONTENIDO DE LA FOTO
            ----------------------------------------------------- */

            .hero-content {

                position: absolute;

                inset: 0;

                z-index: 2;

                display: flex;

                flex-direction:
                    column;

                align-items:
                    flex-start;

                justify-content:
                    flex-end;

                padding:
                    46px
                    clamp(34px, 3.5vw, 58px);

                color:
                    #ffffff;
            }


            .hero-badge {

                display:
                    inline-flex;

                align-items:
                    center;

                width:
                    fit-content;

                gap:
                    6px;

                margin-bottom:
                    13px;

                padding:
                    6px 11px;

                border:
                    1px solid
                    rgba(255,255,255,.23);

                border-radius:
                    999px;

                background:
                    rgba(5,58,101,.62);

                color:
                    #ffffff;

                font-size:
                    .57rem;

                font-weight:
                    800;

                letter-spacing:
                    .75px;

                text-transform:
                    uppercase;

                backdrop-filter:
                    blur(8px);

                -webkit-backdrop-filter:
                    blur(8px);
            }


            .hero-badge i {
                color: #ff9a13;
            }


            .hero-title {

                max-width:
                    500px;

                margin:
                    0 0 12px;

                color:
                    #ffffff;

                font-size:
                    clamp(
                        1.78rem,
                        2.05vw,
                        2.22rem
                    );

                font-weight:
                    800;

                line-height:
                    1.06;

                letter-spacing:
                    -.9px;

                text-shadow:
                    0 3px 16px
                    rgba(0,0,0,.22);
            }


            .hero-title-accent {

                display:
                    block;

                margin-top:
                    2px;

                color:
                    #ff9410;
            }


            .hero-subtitle {

                max-width:
                    475px;

                margin:
                    0 0 17px;

                color:
                    rgba(255,255,255,.88);

                font-size:
                    .70rem;

                line-height:
                    1.55;

                text-shadow:
                    0 2px 8px
                    rgba(0,0,0,.16);
            }


            .hero-modules {

                display:
                    flex;

                flex-wrap:
                    wrap;

                gap:
                    6px;
            }


            .hero-module {

                display:
                    inline-flex;

                align-items:
                    center;

                gap:
                    5px;

                padding:
                    6px 9px;

                border:
                    1px solid
                    rgba(255,255,255,.19);

                border-radius:
                    7px;

                background:
                    rgba(3,43,78,.67);

                color:
                    #ffffff;

                font-size:
                    .59rem;

                font-weight:
                    650;

                backdrop-filter:
                    blur(7px);

                -webkit-backdrop-filter:
                    blur(7px);

                box-shadow:
                    0 5px 12px
                    rgba(0,0,0,.08);
            }


            .hero-module i {

                color:
                    #ff9814;

                font-size:
                    .66rem;
            }

        }


        /* =========================================================
           ESCRITORIOS DE MENOR ALTURA
        ========================================================= */

        @media
        (min-width: 900px)
        and (max-height: 720px) {

            .login-panel::before {

                height:
                    calc(100vh - 42px);

                max-height:
                    535px;
            }


            .login-logo {

                width:
                    72px !important;

                height:
                    72px !important;

                flex-basis:
                    72px !important;
            }


            .login-brand {

                margin-bottom:
                    15px !important;
            }


            .login-heading {

                margin-bottom:
                    16px !important;
            }


            .field-group {

                margin-bottom:
                    11px !important;
            }


            .login-footer {

                margin-top:
                    14px !important;
            }

        }


        /* =========================================================
           TABLET
        ========================================================= */

        @media
        (min-width: 900px)
        and (max-width: 1150px) {

            .login-panel {

                flex-basis:
                    54% !important;

                width:
                    54% !important;

                padding:
                    35px 48px !important;
            }


            .login-hero {

                flex-basis:
                    46% !important;

                width:
                    46% !important;
            }


            .hero-content {

                padding:
                    38px 28px;
            }


            .hero-title {

                font-size:
                    1.62rem;
            }


            .hero-subtitle {

                font-size:
                    .66rem;
            }

        }


        /* =========================================================
           MOVIL
        ========================================================= */

        @media (max-width: 899px) {

            .login-hero {
                display: none !important;
            }


            .login-panel {

                order: 1;

                width:
                    100% !important;

                max-width:
                    none !important;

                min-height:
                    100vh;

                padding:
                    30px 22px !important;

                background:
                    linear-gradient(
                        145deg,
                        #0f6098,
                        #064575 55%,
                        #04375f
                    ) !important;
            }


            .login-panel > * {

                width:
                    100%;

                max-width:
                    360px;

                margin-left:
                    auto;

                margin-right:
                    auto;
            }

        }

        /* =========================================================
           DECORACION INFERIOR IZQUIERDA
           Dos circulos solidos como la referencia
        ========================================================= */

        @media (min-width: 900px) {

            /*
             * Desactivar cualquier decoración anterior
             * creada mediante pseudo-elemento.
             */

            .login-panel::after {
                display: none !important;
            }


            /*
             * Contenedor decorativo.
             */

            .login-bg-decoration {

                position: absolute;

                z-index: 0;

                left: 0;
                bottom: 0;

                width: 245px;
                height: 245px;

                overflow: hidden;

                pointer-events: none;
            }


            /*
             * Base de ambos círculos.
             */

            .login-bg-circle {

                position: absolute;

                display: block;

                border-radius: 50%;

                border: 0;

                box-shadow: none;

                pointer-events: none;
            }


            /*
             * Círculo grande:
             * parcialmente fuera del borde izquierdo
             * y parcialmente fuera del borde inferior.
             */

            .login-bg-circle-large {

                width: 230px;
                height: 230px;

                left: -92px;
                bottom: -54px;

                background:
                    rgba(54, 139, 198, .20);
            }


            /*
             * Círculo pequeño:
             * delante del grande y más abajo.
             */

            .login-bg-circle-small {

                width: 145px;
                height: 145px;

                left: -43px;
                bottom: -76px;

                background:
                    rgba(78, 157, 211, .25);
            }


            /*
             * La tarjeta y su contenido siempre quedan
             * por encima de la decoración.
             */

            .login-panel::before {
                z-index: 1 !important;
            }


            .login-panel > *:not(.login-bg-decoration) {

                position: relative;

                z-index: 2;
            }

        }


        /* En móvil también dejamos la decoración discreta */

        @media (max-width: 899px) {

            .login-bg-decoration {

                position: absolute;

                left: 0;
                bottom: 0;

                width: 180px;
                height: 180px;

                overflow: hidden;

                pointer-events: none;
            }


            .login-bg-circle {

                position: absolute;

                display: block;

                border-radius: 50%;
            }


            .login-bg-circle-large {

                width: 175px;
                height: 175px;

                left: -75px;
                bottom: -42px;

                background:
                    rgba(54,139,198,.16);
            }


            .login-bg-circle-small {

                width: 110px;
                height: 110px;

                left: -35px;
                bottom: -58px;

                background:
                    rgba(78,157,211,.21);
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
            src="{{ asset('images/login-restaurante.jpg') }}"
            alt="Restaurante elegante"
            loading="eager"
        >


        <div class="hero-content">

    <div class="hero-badge">
        <i class="bi bi-shop"></i>
        Sistema de Gestión
    </div>

    <h2 class="hero-title">

        Todo tu restaurante,

        <span class="hero-title-accent">
            en un solo lugar
        </span>

    </h2>

    <p class="hero-subtitle">

        Gestiona pedidos, cocina, caja e inventario
        desde una plataforma diseñada para mantener
        tu operación organizada y en tiempo real.

    </p>

    <div class="hero-modules">

        <span class="hero-module">
            <i class="bi bi-receipt-cutoff"></i>
            Pedidos
        </span>

        <span class="hero-module">
            <i class="bi bi-fire"></i>
            Cocina
        </span>

        <span class="hero-module">
            <i class="bi bi-cash-stack"></i>
            Caja
        </span>

        <span class="hero-module">
            <i class="bi bi-box-seam"></i>
            Inventario
        </span>

    </div>

</div>


    </div>


    <!-- =========================================================
         PANEL DERECHO
    ========================================================== -->

    <div class="login-panel">

        <!-- Decoración inferior izquierda -->
        <div class="login-bg-decoration" aria-hidden="true">
            <span class="login-bg-circle login-bg-circle-large"></span>
            <span class="login-bg-circle login-bg-circle-small"></span>
        </div>


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
            Sistema de Gestión · Desarrollado con Amor

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











