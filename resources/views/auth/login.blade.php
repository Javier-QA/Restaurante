<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar Sesión - El Capitán</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            background-color: #eef1f5;

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: Arial, Helvetica, sans-serif;
        }


        /* ==========================================
           CONTENEDOR PRINCIPAL
        ========================================== */

        .login-container {
            width: 90%;
            max-width: 950px;
            min-height: 570px;

            display: flex;

            background-color: white;

            border-radius: 25px;

            overflow: hidden;

            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }


        /* ==========================================
           PARTE IZQUIERDA
        ========================================== */

        .login-left {
            width: 50%;

            background-color: #042B6E;

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 50px;
        }

        .left-content {
            max-width: 380px;
        }


        /* NOMBRE DEL NEGOCIO */

        .logo-title {
            font-family: Georgia, "Times New Roman", serif;

            font-size: 55px;

            font-weight: bold;

            letter-spacing: 2px;

            margin: 0 0 10px;
        }


        /* LÍNEA DECORATIVA */

        .logo-line {
            width: 100px;

            height: 3px;

            background-color: white;

            margin: 20px auto;
        }


        /* SUBTÍTULO */

        .left-subtitle {
            font-size: 18px;

            letter-spacing: 1px;

            margin-bottom: 25px;
        }


        /* DESCRIPCIÓN */

        .left-description {
            font-size: 15px;

            line-height: 1.7;

            opacity: 0.85;

            margin: 0;
        }


        /* ==========================================
           PARTE DERECHA
        ========================================== */

        .login-right {
            width: 50%;

            background-color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 50px;
        }

        .login-form {
            width: 100%;

            max-width: 360px;
        }


        /* ==========================================
           TÍTULO
        ========================================== */

        .login-form h2 {
            text-align: center;

            color: #042B6E;

            font-size: 30px;

            font-weight: 700;

            margin: 0 0 8px;
        }

        .welcome-text {
            text-align: center;

            color: #777;

            font-size: 15px;

            margin: 0 0 35px;
        }


        /* ==========================================
           CAMPOS
        ========================================== */

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;

            color: #333;

            font-size: 14px;

            font-weight: 600;

            margin-bottom: 8px;
        }

        .input-container {
            position: relative;
        }

        .form-control {
            width: 100%;

            height: 48px;

            padding: 0 15px;

            border: 1px solid #d7d7d7;

            border-radius: 8px;

            background-color: #f7f7f7;

            font-size: 15px;

            outline: none;

            transition: 0.2s;
        }

        .form-control:focus {
            background-color: white;

            border-color: #042B6E;

            box-shadow: 0 0 0 3px rgba(4, 43, 110, 0.10);
        }


        /* ==========================================
           CONTRASEÑA
        ========================================== */

        .password-container .form-control {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;

            right: 12px;

            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            color: #777;

            cursor: pointer;

            font-size: 18px;

            padding: 5px;
        }

        .toggle-password:hover {
            color: #042B6E;
        }


        /* ==========================================
           BOTÓN INGRESAR
        ========================================== */

        .btn-login {
            width: 100%;

            height: 48px;

            border: none;

            border-radius: 8px;

            background-color: #042B6E;

            color: white;

            font-size: 14px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.2s;

            margin-top: 5px;
        }

        .btn-login:hover {
            background-color: #031f52;

            transform: translateY(-1px);

            box-shadow: 0 5px 12px rgba(4, 43, 110, 0.20);
        }


        /* ==========================================
           MENSAJE DE ERROR
        ========================================== */

        .text-danger {
            display: block;

            color: #dc3545;

            font-size: 12px;

            margin-top: 5px;
        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 768px) {

            .login-container {
                width: 92%;

                min-height: auto;

                flex-direction: column;

                border-radius: 20px;
            }

            .login-left {
                width: 100%;

                padding: 35px 25px;
            }

            .login-right {
                width: 100%;

                padding: 40px 25px;
            }

            .logo-title {
                font-size: 42px;
            }

            .left-description {
                display: none;
            }

        }

    </style>
</head>


<body>


    <!-- ==========================================
         CONTENEDOR DEL LOGIN
    ========================================== -->

    <div class="login-container">


        <!-- ======================================
             PARTE IZQUIERDA
        ======================================= -->

        <div class="login-left">

            <div class="left-content">

                <h1 class="logo-title">
                    EL CAPITÁN
                </h1>

                <div class="logo-line"></div>

                <div class="left-subtitle">
                    SISTEMA DE GESTIÓN PROFESIONAL
                </div>

                <p class="left-description">
                    Accede al sistema para gestionar
                    las operaciones de El Capitán.
                </p>

            </div>

        </div>


        <!-- ======================================
             PARTE DERECHA
        ======================================= -->

        <div class="login-right">

            <div class="login-form">


                <!-- TÍTULO -->

                <h2>
                    INICIAR SESIÓN
                </h2>

                <p class="welcome-text">
                    
                </p>


                <!-- ==================================
                     FORMULARIO
                =================================== -->

                <form
                    action="{{ route('login.perform') }}"
                    method="POST"
                >

                    @csrf


                    <!-- ==============================
                         CORREO ELECTRÓNICO
                    =============================== -->

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Correo Electrónico
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="admin@admin.com"
                            required
                            autofocus
                        >

                        @error('email')

                            <span class="text-danger">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    <!-- ==============================
                         CONTRASEÑA
                    =============================== -->

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Contraseña
                        </label>

                        <div class="input-container password-container">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="******"
                                required
                            >

                            <!-- MOSTRAR / OCULTAR -->

                            <button
                                type="button"
                                class="toggle-password"
                                id="togglePassword"
                                aria-label="Mostrar contraseña"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>


                    <!-- ==============================
                         BOTÓN
                    =============================== -->

                    <button
                        type="submit"
                        class="btn-login"
                    >
                        INGRESAR AL SISTEMA
                    </button>

                </form>

            </div>

        </div>

    </div>


    <!-- ==========================================
         JAVASCRIPT
         MOSTRAR / OCULTAR CONTRASEÑA
    =========================================== -->

    <script>

        const password =
            document.getElementById('password');

        const togglePassword =
            document.getElementById('togglePassword');


        togglePassword.addEventListener('click', function () {

            if (password.type === 'password') {

                password.type = 'text';

                this.innerHTML =
                    '<i class="bi bi-eye-slash"></i>';

                this.setAttribute(
                    'aria-label',
                    'Ocultar contraseña'
                );

            } else {

                password.type = 'password';

                this.innerHTML =
                    '<i class="bi bi-eye"></i>';

                this.setAttribute(
                    'aria-label',
                    'Mostrar contraseña'
                );

            }

        });

    </script>


</body>

</html>