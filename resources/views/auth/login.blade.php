{{--
    Documentacion de archivo:
    Vista Blade de autenticacion; permite iniciar sesion y guardar el token de Sanctum en el navegador.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Alpadent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Documentacion: Ejecuta redirigir si hay sesion activa.
        // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
        function redirigirSiHaySesionActiva() {
            if (localStorage.getItem('token')) {
                window.location.replace('/dashboard');
            }
        }

        redirigirSiHaySesionActiva();
        window.addEventListener('pageshow', redirigirSiHaySesionActiva);
    </script>

    <style>
        :root {
            --alpadent-blue: #104a7a;
            --alpadent-blue-dark: #0d416f;
            --alpadent-blue-soft: #2d84c6;
            --alpadent-line: #dce8f5;
            --alpadent-text: #0f2440;
            --alpadent-muted: #6980a5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background:
                radial-gradient(circle at 8% 0%, rgba(255, 255, 255, 0.08) 0 150px, transparent 151px),
                linear-gradient(135deg, var(--alpadent-blue-dark), var(--alpadent-blue));
            color: var(--alpadent-text);
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 28px 18px;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.035);
            pointer-events: none;
        }

        body::before {
            width: 118px;
            height: 118px;
            left: 30px;
            bottom: 56px;
        }

        body::after {
            width: 210px;
            height: 210px;
            right: -38px;
            bottom: -54px;
        }

        .decor-circle {
            position: fixed;
            width: 70px;
            height: 70px;
            right: 72px;
            top: 76px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.035);
            pointer-events: none;
        }

        .login-card {
            position: relative;
            width: min(100%, 380px);
            border-radius: 20px;
            padding: 40px 32px 26px;
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(6, 28, 54, 0.28);
            animation: fadeIn 0.55s ease-in-out;
            z-index: 1;
        }

        .login-card::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -5px;
            height: 6px;
            border-radius: 0 0 18px 18px;
            background: var(--alpadent-blue-soft);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-mark {
            width: 62px;
            height: 62px;
            margin: 0 auto 20px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: var(--alpadent-blue-dark);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(13, 65, 111, 0.18);
        }

        .brand-mark svg {
            width: 30px;
            height: 30px;
        }

        .brand-name {
            margin: 0;
            color: #0054a6;
            font-size: 1.08rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0;
        }

        .brand-subtitle {
            margin: 4px 0 30px;
            color: var(--alpadent-muted);
            font-size: 0.72rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1.7px;
        }

        .login-divider {
            width: 100%;
            height: 1px;
            margin-bottom: 28px;
            background: var(--alpadent-line);
        }

        .form-label {
            margin-bottom: 7px;
            color: #45628d;
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 1.7px;
            text-transform: uppercase;
        }

        .input-shell {
            display: flex;
            align-items: center;
            min-height: 46px;
            border: 1px solid #d5e1f0;
            border-radius: 10px;
            background: #f7fbff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .input-shell:focus-within {
            border-color: var(--alpadent-blue-soft);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(45, 132, 198, 0.12);
        }

        .input-icon,
        .password-toggle {
            width: 42px;
            flex: 0 0 42px;
            display: grid;
            place-items: center;
            color: #2b70ad;
        }

        .login-input {
            width: 100%;
            min-width: 0;
            border: 0;
            outline: 0;
            background: #ffffff;
            color: #111111;
            font-size: 0.95rem;
            padding: 9px 12px;
            border-radius: 7px;
        }

        .login-input::placeholder {
            color: #8d99a9;
        }

        .password-toggle {
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .forgot-link {
            display: block;
            margin-top: 10px;
            color: #0054a6;
            font-size: 0.78rem;
            text-align: right;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: var(--alpadent-blue-dark);
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            min-height: 48px;
            margin-top: 22px;
            border: 0;
            border-radius: 8px;
            background: var(--alpadent-blue-soft);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 14px 28px rgba(45, 132, 198, 0.24);
            transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .login-button:hover {
            background: #1f73b0;
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(45, 132, 198, 0.3);
        }

        .login-button:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        .secure-note {
            margin: 24px 0 0;
            color: #7488a8;
            font-size: 0.74rem;
            text-align: center;
        }

        .secure-note i {
            color: #2b70ad;
            margin-right: 4px;
        }

        @media (max-width: 420px) {
            body {
                padding: 18px 14px;
                overflow: auto;
            }

            .login-card {
                padding: 34px 22px 24px;
                border-radius: 18px;
            }

            .decor-circle {
                right: 24px;
                top: 36px;
            }
        }
    </style>
</head>
<body>

<span class="decor-circle" aria-hidden="true"></span>

<main class="login-card" aria-labelledby="loginTitle">
    <div class="brand-mark" aria-hidden="true">
        <svg viewBox="0 0 64 64" role="img" focusable="false">
            <path fill="currentColor" d="M21.4 10.2c3.7-2.2 7.1-.6 10.6-.6s6.9-1.6 10.6.6c7.4 4.4 6.7 16.3 2.7 25.2-1.7 3.7-2.2 8.1-3 11.9-.8 3.9-2 7.2-5.2 7.2-3.6 0-3.3-5.2-4.3-9.2-.3-1.2-.7-2.1-1.4-2.1s-1.1.9-1.4 2.1c-1 4-0.7 9.2-4.3 9.2-3.2 0-4.4-3.3-5.2-7.2-.8-3.8-1.3-8.2-3-11.9-4-8.9-4.7-20.8 2.7-25.2Z"/>
            <path fill="#cbe8ff" d="M23.7 14.5c2.8-1.7 5.3-.2 8.3-.2s5.5-1.5 8.3.2c1.4.8 2.4 2.1 3.1 3.7-4.9-2-7.6.4-11.4.4s-6.5-2.4-11.4-.4c.7-1.6 1.7-2.9 3.1-3.7Z"/>
        </svg>
    </div>

    <h1 class="brand-name" id="loginTitle">Alpadent</h1>
    <p class="brand-subtitle">Sistema de gesti&oacute;n cl&iacute;nica</p>
    <div class="login-divider"></div>

    <form id="formLogin">
        
        <div class="mb-3">
            <label class="form-label" for="email">Correo electr&oacute;nico</label>
            <div class="input-shell">
                <span class="input-icon" aria-hidden="true"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" class="login-input" placeholder="ejemplo@clinica.com" autocomplete="email" required>
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label" for="password">Contrase&ntilde;a</label>
            <div class="input-shell">
                <span class="input-icon" aria-hidden="true"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" class="login-input" placeholder="********" autocomplete="current-password" required>
                <button class="password-toggle" type="button" id="togglePassword" aria-label="Mostrar contrase&ntilde;a">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>


        <button class="login-button" type="submit" id="btnLogin">
            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al sistema
        </button>
    </form>

    <p class="secure-note">
        <i class="bi bi-shield-check"></i>Acceso: Solo personal autorizado
    </p>
</main>

<script>
const passwordInput = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');
const loginButton = document.getElementById('btnLogin');

togglePassword.addEventListener('click', function() {
    const mostrarPassword = passwordInput.type === 'password';

    passwordInput.type = mostrarPassword ? 'text' : 'password';
    togglePassword.innerHTML = mostrarPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    togglePassword.setAttribute('aria-label', mostrarPassword ? 'Ocultar contrasena' : 'Mostrar contrasena');
});

document.getElementById('formLogin').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = passwordInput.value;

    loginButton.disabled = true;
    loginButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Ingresando';

    try {
        const response = await fetch(`${window.location.origin}/api/v1/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.success) {
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('usuario', JSON.stringify(data.data.usuario));

            Swal.fire({
                icon: 'success',
                title: 'Bienvenido',
                text: 'Acceso concedido, redirigiendo...',
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.replace('/dashboard');
            }, 1500);

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });

            loginButton.disabled = false;
            loginButton.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al sistema';
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexi&oacute;n',
            text: 'No se pudo conectar al servidor'
        });

        loginButton.disabled = false;
        loginButton.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al sistema';
    }
});
</script>

</body>
</html>
