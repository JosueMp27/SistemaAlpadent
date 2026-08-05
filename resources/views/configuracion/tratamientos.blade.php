{{--
    Documentacion de archivo:
    Vista Blade del modulo configuracion; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .config-page-header,
    .filters-grid,
    .toolbar-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .config-page-header {
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .config-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: .25rem;
    }

    .config-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .clean-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 .25rem 1rem rgba(0,0,0,.04);
    }

    .clean-card-body {
        padding: 1.25rem;
    }

    .filters-grid {
        align-items: end;
    }

    .filters-grid > div {
        min-width: 190px;
        flex: 1;
    }
</style>

<div class="config-page-header">
    <div>
        <h3 class="config-title">Tratamientos y precios</h3>
        <p class="config-subtitle">Configuracion de tratamientos disponibles para citas, pagos y odontograma.</p>
    </div>
    <div class="toolbar-actions">
        <a href="/configuracion" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <button class="btn btn-primary" onclick="abrirModalTratamiento()">
            <i class="bi bi-plus-circle me-1"></i> Nuevo tratamiento
        </button>
    </div>
</div>

<div class="clean-card mb-4">
    <div class="clean-card-body">
        <div class="filters-grid">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" id="buscar" class="form-control" placeholder="Nombre, categoria o descripcion" onkeyup="if(event.key==='Enter'){paginaActual=1;cargarTratamientos();}">
            </div>
            <div>
                <label class="form-label">Categoria</label>
                <select id="filtroCategoria" class="form-select" onchange="paginaActual=1;cargarTratamientos()">
                    <option value="">Todas</option>
                </select>
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select id="filtroActivo" class="form-select" onchange="paginaActual=1;cargarTratamientos()">
                    <option value="">Todos</option>
                    <option value="true">Activos</option>
                    <option value="false">Inactivos</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" onclick="paginaActual=1;cargarTratamientos()">
                <i class="bi bi-search me-1"></i> Buscar
            </button>
        </div>
    </div>
</div>

<div class="clean-card">
    <div class="clean-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tratamiento</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tablaTratamientos">
                    <tr><td colspan="7" class="text-center text-muted py-4">Cargando tratamientos...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
            <small class="text-muted" id="infoTratamientos">Sin datos</small>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="btnAnterior" onclick="cambiarPagina(-1)" disabled>Anterior</button>
                <button class="btn btn-outline-secondary btn-sm" id="btnSiguiente" onclick="cambiarPagina(1)" disabled>Siguiente</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTratamiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTratamientoLabel">Nuevo tratamiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formTratamiento">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="nombre" class="form-control" maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoria</label>
                            <select id="categoria" class="form-select"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Precio</label>
                            <input type="number" id="precio" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select id="activo" class="form-select">
                                <option value="true">Activo</option>
                                <option value="false">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripcion</label>
                            <textarea id="descripcion" class="form-control" rows="3" maxlength="1000"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarTratamiento()">
                    <i class="bi bi-floppy me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;
let paginaActual = 1;
let ultimaPagina = 1;
let tratamientoEditandoId = null;
let modalTratamiento = null;
let categorias = [];

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', cargarTratamientos);

// Documentacion: Construye los encabezados para llamar la API.
// Como lo hace: Incluye Accept JSON y el token Bearer guardado en localStorage.
function headersApi(json = false) {
    const headers = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('token') };
    if (json) headers['Content-Type'] = 'application/json';
    return headers;
}

// Documentacion: Carga cargar tratamientos.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarTratamientos() {
    const tabla = document.getElementById('tablaTratamientos');
    const search = document.getElementById('buscar').value.trim();
    const categoria = document.getElementById('filtroCategoria').value;
    const activo = document.getElementById('filtroActivo').value;
    let url = `${API_URL}/configuracion/tratamientos?page=${paginaActual}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (categoria) url += `&categoria=${encodeURIComponent(categoria)}`;
    if (activo !== '') url += `&activo=${encodeURIComponent(activo)}`;
    tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Cargando tratamientos...</td></tr>';

    try {
        const response = await fetch(url, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar los tratamientos');
        categorias = resultado.data.categorias ?? [];
        llenarCategorias();
        const data = resultado.data.tratamientos;
        paginaActual = data.current_page ?? 1;
        ultimaPagina = data.last_page ?? 1;
        renderizarTratamientos(data.data ?? []);
        actualizarPaginacion(data.total ?? 0);
    } catch (error) {
        tabla.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${escaparHtml(error.message)}</td></tr>`;
    }
}

// Documentacion: Ejecuta llenar categorias.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function llenarCategorias() {
    const filtro = document.getElementById('filtroCategoria');
    const valorFiltro = filtro.value;
    filtro.innerHTML = '<option value="">Todas</option>' + categorias.map(c => `<option value="${c}">${formatearCategoria(c)}</option>`).join('');
    filtro.value = valorFiltro;
    document.getElementById('categoria').innerHTML = categorias.map(c => `<option value="${c}">${formatearCategoria(c)}</option>`).join('');
}

// Documentacion: Renderiza renderizar tratamientos.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarTratamientos(tratamientos) {
    const tabla = document.getElementById('tablaTratamientos');
    if (!tratamientos.length) {
        tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay tratamientos registrados</td></tr>';
        return;
    }
    tabla.innerHTML = tratamientos.map((tratamiento, index) => `
        <tr>
            <td>${((paginaActual - 1) * 15) + index + 1}</td>
            <td>${escaparHtml(tratamiento.nombre)}</td>
            <td>${formatearCategoria(tratamiento.categoria)}</td>
            <td class="fw-bold">${formatearPrecio(tratamiento.precio)}</td>
            <td>${escaparHtml(tratamiento.descripcion ?? 'Sin descripcion')}</td>
            <td>${tratamiento.activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-warning" onclick="editarTratamiento(${tratamiento.id})"><i class="bi bi-pencil"></i></button>
                ${tratamiento.activo
                    ? `<button class="btn btn-sm btn-outline-danger" onclick="eliminarTratamiento(${tratamiento.id})"><i class="bi bi-trash"></i></button>`
                    : `<button class="btn btn-sm btn-outline-success" onclick="reactivarTratamiento(${tratamiento.id})"><i class="bi bi-arrow-clockwise"></i></button>`}
            </td>
        </tr>
    `).join('');
}

// Documentacion: Actualiza actualizar paginacion.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPaginacion(total) {
    document.getElementById('infoTratamientos').textContent = `Pagina ${paginaActual} de ${ultimaPagina} | Total: ${total} tratamientos`;
    document.getElementById('btnAnterior').disabled = paginaActual <= 1;
    document.getElementById('btnSiguiente').disabled = paginaActual >= ultimaPagina;
}

// Documentacion: Ejecuta cambiar pagina.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPagina(direccion) {
    const nueva = paginaActual + direccion;
    if (nueva < 1 || nueva > ultimaPagina) return;
    paginaActual = nueva;
    cargarTratamientos();
}

// Documentacion: Abre abrir modal tratamiento.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalTratamiento() {
    tratamientoEditandoId = null;
    document.getElementById('formTratamiento').reset();
    llenarCategorias();
    document.getElementById('activo').value = 'true';
    document.getElementById('modalTratamientoLabel').textContent = 'Nuevo tratamiento';
    modalTratamiento = new bootstrap.Modal(document.getElementById('modalTratamiento'));
    modalTratamiento.show();
}

// Documentacion: Ejecuta editar tratamiento.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function editarTratamiento(id) {
    try {
        const response = await fetch(`${API_URL}/configuracion/tratamientos/${id}`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo obtener el tratamiento');
        const tratamiento = resultado.data;
        tratamientoEditandoId = tratamiento.id;
        llenarCategorias();
        document.getElementById('nombre').value = tratamiento.nombre ?? '';
        document.getElementById('categoria').value = tratamiento.categoria ?? 'otros';
        document.getElementById('precio').value = tratamiento.precio ?? 0;
        document.getElementById('descripcion').value = tratamiento.descripcion ?? '';
        document.getElementById('activo').value = tratamiento.activo ? 'true' : 'false';
        document.getElementById('modalTratamientoLabel').textContent = 'Editar tratamiento';
        modalTratamiento = new bootstrap.Modal(document.getElementById('modalTratamiento'));
        modalTratamiento.show();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Guarda guardar tratamiento.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarTratamiento() {
    const datos = {
        nombre: document.getElementById('nombre').value.trim(),
        categoria: document.getElementById('categoria').value,
        precio: document.getElementById('precio').value,
        descripcion: document.getElementById('descripcion').value.trim() || null,
        activo: document.getElementById('activo').value === 'true'
    };
    if (datos.nombre.length < 2) return alertaError('El nombre debe tener al menos 2 caracteres.');
    if (Number(datos.precio) < 0 || datos.precio === '') return alertaError('El precio no es valido.');

    const esEdicion = tratamientoEditandoId !== null;
    const url = esEdicion ? `${API_URL}/configuracion/tratamientos/${tratamientoEditandoId}` : `${API_URL}/configuracion/tratamientos`;
    const method = esEdicion ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, { method, headers: headersApi(true), body: JSON.stringify(datos) });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo guardar el tratamiento';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            return Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
        }
        modalTratamiento.hide();
        alertaExito(resultado.message || 'Tratamiento guardado correctamente');
        cargarTratamientos();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Elimina eliminar tratamiento.
// Como lo hace: Confirma la accion, llama la API y actualiza el listado para reflejar el cambio.
async function eliminarTratamiento(id) {
    const confirmacion = await Swal.fire({ title: 'Desactivar tratamiento', text: 'No aparecera para nuevas citas mientras este inactivo.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Si, desactivar' });
    if (!confirmacion.isConfirmed) return;
    await cambiarEstado(id, 'DELETE', 'Tratamiento desactivado');
}

// Documentacion: Reactiva reactivar tratamiento.
// Como lo hace: Llama el endpoint de reactivacion y refresca la tabla para mostrar el nuevo estado.
async function reactivarTratamiento(id) {
    await cambiarEstado(id, 'POST', 'Tratamiento reactivado', `${API_URL}/configuracion/tratamientos/${id}/reactivar`);
}

// Documentacion: Ejecuta cambiar estado.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function cambiarEstado(id, method, mensaje, url = null) {
    try {
        const response = await fetch(url || `${API_URL}/configuracion/tratamientos/${id}`, { method, headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo completar la accion');
        alertaExito(resultado.message || mensaje);
        cargarTratamientos();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Formatea formatear categoria.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearCategoria(categoria) {
    return String(categoria ?? '').replaceAll('_', ' ').replace(/\b\w/g, letra => letra.toUpperCase());
}

// Documentacion: Formatea valores monetarios en dolares.
// Como lo hace: Usa Intl.NumberFormat con moneda USD.
function formatearPrecio(valor) {
    return new Intl.NumberFormat('es-EC', { style: 'currency', currency: 'USD' }).format(Number(valor ?? 0));
}

// Documentacion: Protege texto antes de insertarlo como HTML.
// Como lo hace: Reemplaza caracteres especiales para evitar inyeccion de marcado.
function escaparHtml(valor) {
    return String(valor ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}
</script>

@endsection
