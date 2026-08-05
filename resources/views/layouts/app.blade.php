{{--
    Documentacion de archivo:
    Layout Blade base; envuelve las pantallas con estilos, scripts, navegacion y contenedor general.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ALPADENT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    body {
        display: none;
        background-color: #f4f8fb;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        background: #0d6efd;
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1045;
        transition: left 0.3s ease-in-out;
        overflow-y: auto;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        padding: 10px 14px;
        border-radius: 8px;
        margin-bottom: 4px;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
    }

    .sidebar-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        display: none;
        transition: opacity 0.3s ease;
    }

    .content {
        margin-left: 250px;
        padding: 20px;
        min-height: calc(100vh - 60px);
    }

    .navbar {
        margin-left: 250px;
        transition: margin-left 0.3s ease;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Reglas Responsivas para Móviles y Tablets (< 992px) */
    @media (max-width: 991.98px) {
        .sidebar {
            left: -260px;
        }

        .sidebar.show {
            left: 0;
        }

        .sidebar-backdrop.show {
            display: block;
        }

        .content {
            margin-left: 0 !important;
            padding: 12px !important;
        }

        .navbar {
            margin-left: 0 !important;
        }

        .table-responsive {
            margin-bottom: 1rem;
        }
    }
    </style>

<script>
    (() => {
        if (!localStorage.getItem('token')) {
            window.location.replace('/');
        }
    })();
</script>
</head>

<body>

    @include('components.sidebar')
    @include('components.navbar')

    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ALERTAS GLOBALES -->
    <script>
        // Documentacion: Ejecuta alerta exito.
        // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
        function alertaExito(mensaje) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Documentacion: Ejecuta alerta error.
        // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
        function alertaError(mensaje) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje
            });
        }
        // Documentacion: Funciones globales para controlar el menú lateral en teléfonos y tablets.
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('show');
            if (backdrop) backdrop.classList.remove('show');
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('#mainSidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) closeMobileSidebar();
                });
            });
        });
    </script>


    <script>
        // Documentacion: Ejecuta logout.
        // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
        async function logout() {
            const tokenActual = localStorage.getItem('token');

            localStorage.removeItem('token');
            localStorage.removeItem('usuario');

            if (tokenActual) {
                fetch(`${window.location.origin}/api/v1/auth/logout`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${tokenActual}`
                    }
                }).catch(() => {});
            }

            Swal.fire({
                icon: 'success',
                title: 'Sesión cerrada',
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.replace('/');
            }, 1500);
        }
    </script>

    <script>
        (() => {
            const authApiUrl = `${window.location.origin}/api/v1`;
            const paginaInicialSecretaria = '/dashboard';
            const rutasPermitidasSecretaria = [
                '/dashboard',
                '/pacientes',
                '/citas',
                '/diagnosticos',
                '/odontograma',
                '/pagos',
                '/pagos/citas',
                '/pagos/productos',
                '/inventario/ventas',
                '/reportes'
            ];

            // Documentacion: Ejecuta normalizar ruta.
            // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
            function normalizarRuta(ruta) {
                if (!ruta || ruta === '/') return '/';
                return ruta.endsWith('/') && ruta.length > 1 ? ruta.slice(0, -1) : ruta;
            }

            // Documentacion: Ejecuta ruta permitida para secretaria.
            // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
            function rutaPermitidaParaSecretaria(rutaActual) {
                const ruta = normalizarRuta(rutaActual);
                return rutasPermitidasSecretaria.some((rutaPermitida) => {
                    return ruta === rutaPermitida || ruta.startsWith(`${rutaPermitida}/`);
                });
            }

            // Documentacion: Ejecuta leer usuario guardado.
            // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
            function leerUsuarioGuardado() {
                try {
                    const usuario = localStorage.getItem('usuario');
                    return usuario ? JSON.parse(usuario) : null;
                } catch (error) {
                    localStorage.removeItem('usuario');
                    return null;
                }
            }

            // Documentacion: Ejecuta obtener usuario actual.
            // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
            async function obtenerUsuarioActual() {
                const token = localStorage.getItem('token');

                if (!token) {
                    return null;
                }

                try {
                    const response = await fetch(`${authApiUrl}/auth/me`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (!response.ok) {
                        return null;
                    }

                    const resultado = await response.json();

                    if (!resultado.success || !resultado.data) {
                        return null;
                    }

                    localStorage.setItem('usuario', JSON.stringify(resultado.data));
                    return resultado.data;
                } catch (error) {
                    return leerUsuarioGuardado();
                }
            }

            // Documentacion: Ejecuta aplicar visibilidad por rol.
            // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
            function aplicarVisibilidadPorRol(usuario) {
                const esAdministrador = usuario?.rol === 'administrador';

                document.querySelectorAll('[data-admin-only="true"]').forEach((elemento) => {
                    elemento.classList.toggle('d-none', !esAdministrador);
                });

                document.querySelectorAll('[data-secretaria-allowed="true"]').forEach((elemento) => {
                    elemento.classList.toggle('d-none', false);
                });
            }

            // Documentacion: Actualiza actualizar saludo.
            // Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
            function actualizarSaludo(usuario) {
                const contenedorSaludo = document.getElementById('navbarGreeting');

                if (!contenedorSaludo) return;

                const nombre = [usuario?.nombre, usuario?.apellido].filter(Boolean).join(' ').trim() || 'Usuario';

                contenedorSaludo.textContent = `Hola, ${nombre}`;
            }

            // Documentacion: Limpia limpiar sesion yvolver al login.
            // Como lo hace: Reinicia campos o filtros y vuelve al estado base de la pantalla.
            function limpiarSesionYVolverAlLogin() {
                localStorage.removeItem('token');
                localStorage.removeItem('usuario');
                document.body.style.display = 'none';
                window.location.replace('/');
            }

            // Documentacion: Ejecuta iniciar contexto de sesion.
            // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
            async function iniciarContextoDeSesion() {
                document.body.style.display = 'none';

                const usuario = await obtenerUsuarioActual();

                if (!usuario) {
                    limpiarSesionYVolverAlLogin();
                    return;
                }

                window.usuarioActualSistema = usuario;

                if (usuario.rol !== 'administrador' && !rutaPermitidaParaSecretaria(window.location.pathname)) {
                    window.location.replace(paginaInicialSecretaria);
                    return;
                }

                aplicarVisibilidadPorRol(usuario);
                actualizarSaludo(usuario);
                document.body.style.display = 'block';
            }

            iniciarContextoDeSesion();

            window.addEventListener('pageshow', (event) => {
                if (!localStorage.getItem('token')) {
                    limpiarSesionYVolverAlLogin();
                    return;
                }

                if (event.persisted) {
                    iniciarContextoDeSesion();
                }
            });

            window.addEventListener('storage', (event) => {
                if (event.key === 'token' && !event.newValue) {
                    limpiarSesionYVolverAlLogin();
                }
            });
        })();
    </script>
</body>
</html>
