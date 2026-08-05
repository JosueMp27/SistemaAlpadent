{{--
    Documentacion de archivo:
    Componente Blade reutilizable que arma la barra superior de navegacion.
--}}

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3 px-lg-4">
    <div class="container-fluid p-0">
        <div class="d-flex align-items-center">
            <button class="btn btn-primary btn-sm d-lg-none me-2 shadow-sm" type="button" id="sidebarToggle" onclick="toggleMobileSidebar()" aria-label="Abrir Menú">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="navbar-brand fw-bold text-primary mb-0 h1 fs-6 fs-md-5">
                <i class="bi bi-journal-medical me-1"></i> ALPADENT
            </span>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <span class="me-2 me-md-3 fw-semibold small d-none d-sm-inline" id="navbarGreeting">Cargando...</span>
            <button class="btn btn-outline-danger btn-sm" onclick="logout()" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right me-1"></i>
                <span class="d-none d-sm-inline">Cerrar sesión</span>
            </button>
        </div>
    </div>
</nav>
