<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - EL CAPITÁN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .login-header {
            background: #00246b;
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #00246b;
        }
        /* Nuevo color para el botón */
        .btn-capitan {
            background-color: #00246b;
            border-color: #00246b;
            color: white;
        }
        .btn-capitan:hover {
            background-color: #001a4d;
            border-color: #001a4d;
            color: white;
        }
        /* Estilo para el input de contraseña */
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }
    </style>
</head>
<body>

    <div class="card login-card">
        <div class="login-header">
            <h3 class="fw-bold mb-0">EL CAPITÁN</h3>
        </div>
        <div class="card-body p-4">
            <h5 class="text-center text-muted mb-4">Bienvenido de nuevo</h5>
            
            <form action="{{ route('login.perform') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label text-secondary fw-bold">Correo Electrónico</label>
                    <input type="email" class="form-control py-2" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@admin.com">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-secondary fw-bold">Contraseña</label>
                    <div class="password-container">
                        <input type="password" class="form-control py-2" id="password" name="password" required placeholder="******">
                        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-capitan py-2 fw-bold shadow-sm">
                        INGRESAR AL SISTEMA
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer bg-white text-center py-3 border-0 rounded-bottom">
            <small class="text-muted">Desarrollado para El Capitán</small>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            // Cambiar el tipo de input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Cambiar el icono
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>