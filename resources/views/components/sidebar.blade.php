{{--
    Documentacion de archivo:
    Componente Blade reutilizable que arma la barra lateral de navegacion responsiva.
--}}

<div class="sidebar p-3 shadow" id="mainSidebar">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h4 class="text-center fw-bold m-0 text-white">
            <i class="bi bi-hospital me-2"></i>ALPADENT
        </h4>
        <button type="button" class="btn-close btn-close-white d-lg-none" id="sidebarClose" onclick="closeMobileSidebar()" aria-label="Cerrar"></button>
    </div>

    <nav class="nav flex-column">
        <a href="/dashboard" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="/pacientes" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-people me-2"></i> Pacientes</a>
        <a href="/citas" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-calendar-check me-2"></i> Citas</a>
        <a href="/doctores-externos" data-admin-only="true" class="nav-link"><i class="bi bi-person-badge me-2"></i> Doctores externos</a>
        <a href="/diagnosticos" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-clipboard2-pulse me-2"></i> Diagnósticos</a>
        <a href="/odontograma" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-grid-3x3-gap me-2"></i> Odontograma</a>
        <a href="/pagos/citas" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-cash me-2"></i> Pago de citas</a>
        <a href="/pagos/productos" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-bag-check me-2"></i> Pago de ventas</a>
        <a href="/reportes" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes</a>
        <a href="/inventario/productos" data-admin-only="true" class="nav-link"><i class="bi bi-box me-2"></i> Productos</a>
        <a href="/inventario/ventas" data-secretaria-allowed="true" class="nav-link"><i class="bi bi-cart-check me-2"></i> Ventas</a>
        <a href="/configuracion" data-admin-only="true" class="nav-link"><i class="bi bi-gear me-2"></i> Configuración</a>
    </nav>
</div>
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>
