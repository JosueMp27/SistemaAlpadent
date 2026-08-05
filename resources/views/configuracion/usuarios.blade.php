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
        <h3 class="config-title">Usuarios del sistema</h3>
        <p class="config-subtitle">CRUD de administradores y secretarias/asistentes.</p>
    </div>
    <div class="toolbar-actions">
        <a href="/configuracion" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <button class="btn btn-primary" onclick="abrirModalUsuario()">
            <i class="bi bi-plus-circle me-1"></i> Nuevo usuario
        </button>
    </div>
</div>

<div class="clean-card mb-4">
    <div class="clean-card-body">
        <div class="filters-grid">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" id="buscar" class="form-control" placeholder="Nombre, apellido o correo" onkeyup="if(event.key==='Enter'){paginaActual=1;cargarUsuarios();}">
            </div>
            <div>
                <label class="form-label">Rol</label>
                <select id="filtroRol" class="form-select" onchange="paginaActual=1;cargarUsuarios()">
                    <option value="">Todos</option>
                    <option value="administrador">Administrador</option>
                    <option value="secretaria">Secretaria/Asistente</option>
                </select>
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select id="filtroActivo" class="form-select" onchange="paginaActual=1;cargarUsuarios()">
                    <option value="">Todos</option>
                    <option value="true">Activos</option>
                    <option value="false">Inactivos</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" onclick="paginaActual=1;cargarUsuarios()">
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
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha y hora de creacion</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tablaUsuarios">
                    <tr><td colspan="7" class="text-center text-muted py-4">Cargando usuarios...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
            <small class="text-muted" id="infoUsuarios">Sin datos</small>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="btnAnterior" onclick="cambiarPagina(-1)" disabled>Anterior</button>
                <button class="btn btn-outline-secondary btn-sm" id="btnSiguiente" onclick="cambiarPagina(1)" disabled>Siguiente</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalUsuarioLabel">Nuevo usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formUsuario">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="nombre" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido</label>
                            <input type="text" id="apellido" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo electronico</label>
                            <input type="email" id="email" class="form-control" maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select id="rol" class="form-select">
                                <option value="administrador">Administrador</option>
                                <option value="secretaria">Secretaria/Asistente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contrasena</label>
                            <input type="password" id="password" class="form-control" minlength="6" placeholder="Minimo 6 caracteres">
                            <small class="text-muted" id="passwordHelp">Requerida para usuarios nuevos.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select id="activo" class="form-select">
                                <option value="true">Activo</option>
                                <option value="false">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">
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
let usuarioEditandoId = null;
let modalUsuario = null;

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', cargarUsuarios);

// Documentacion: Construye los encabezados para llamar la API.
// Como lo hace: Incluye Accept JSON y el token Bearer guardado en localStorage.
function headersApi(json = false) {
    const headers = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('token') };
    if (json) headers['Content-Type'] = 'application/json';
    return headers;
}

// Documentacion: Carga cargar usuarios.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarUsuarios() {
    const tabla = document.getElementById('tablaUsuarios');
    const search = document.getElementById('buscar').value.trim();
    const rol = document.getElementById('filtroRol').value;
    const activo = document.getElementById('filtroActivo').value;
    let url = `${API_URL}/configuracion/usuarios?page=${paginaActual}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (rol) url += `&rol=${encodeURIComponent(rol)}`;
    if (activo !== '') url += `&activo=${encodeURIComponent(activo)}`;
    tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Cargando usuarios...</td></tr>';

    try {
        const response = await fetch(url, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar los usuarios');
        const data = resultado.data;
        paginaActual = data.current_page ?? 1;
        ultimaPagina = data.last_page ?? 1;
        renderizarUsuarios(data.data ?? []);
        actualizarPaginacion(data.total ?? 0);
    } catch (error) {
        tabla.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${escaparHtml(error.message)}</td></tr>`;
    }
}

// Documentacion: Renderiza renderizar usuarios.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarUsuarios(usuarios) {
    const tabla = document.getElementById('tablaUsuarios');
    if (!usuarios.length) {
        tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay usuarios registrados</td></tr>';
        return;
    }
    tabla.innerHTML = usuarios.map((usuario, index) => `
        <tr>
            <td>${((paginaActual - 1) * 15) + index + 1}</td>
            <td>${escaparHtml(usuario.nombre)} ${escaparHtml(usuario.apellido)}</td>
            <td>${escaparHtml(usuario.email)}</td>
            <td>${usuario.rol === 'administrador' ? 'Administrador' : 'Secretaria/Asistente'}</td>
            <td>${usuario.activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</td>
            <td>${formatearFechaHora(usuario.created_at)}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-warning" onclick="editarUsuario(${usuario.id})"><i class="bi bi-pencil"></i></button>
                ${usuario.activo
                    ? `<button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(${usuario.id})"><i class="bi bi-trash"></i></button>`
                    : `<button class="btn btn-sm btn-outline-success" onclick="reactivarUsuario(${usuario.id})"><i class="bi bi-arrow-clockwise"></i></button>`}
            </td>
        </tr>
    `).join('');
}

// Documentacion: Actualiza actualizar paginacion.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPaginacion(total) {
    document.getElementById('infoUsuarios').textContent = `Pagina ${paginaActual} de ${ultimaPagina} | Total: ${total} usuarios`;
    document.getElementById('btnAnterior').disabled = paginaActual <= 1;
    document.getElementById('btnSiguiente').disabled = paginaActual >= ultimaPagina;
}

// Documentacion: Ejecuta cambiar pagina.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPagina(direccion) {
    const nueva = paginaActual + direccion;
    if (nueva < 1 || nueva > ultimaPagina) return;
    paginaActual = nueva;
    cargarUsuarios();
}

// Documentacion: Abre abrir modal usuario.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalUsuario() {
    usuarioEditandoId = null;
    document.getElementById('formUsuario').reset();
    document.getElementById('rol').value = 'secretaria';
    document.getElementById('activo').value = 'true';
    document.getElementById('modalUsuarioLabel').textContent = 'Nuevo usuario';
    document.getElementById('passwordHelp').textContent = 'Requerida para usuarios nuevos.';
    modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
    modalUsuario.show();
}

// Documentacion: Ejecuta editar usuario.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function editarUsuario(id) {
    try {
        const response = await fetch(`${API_URL}/configuracion/usuarios/${id}`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo obtener el usuario');
        const usuario = resultado.data;
        usuarioEditandoId = usuario.id;
        document.getElementById('nombre').value = usuario.nombre ?? '';
        document.getElementById('apellido').value = usuario.apellido ?? '';
        document.getElementById('email').value = usuario.email ?? '';
        document.getElementById('rol').value = usuario.rol ?? 'secretaria';
        document.getElementById('activo').value = usuario.activo ? 'true' : 'false';
        document.getElementById('password').value = '';
        document.getElementById('modalUsuarioLabel').textContent = 'Editar usuario';
        document.getElementById('passwordHelp').textContent = 'Deje vacio para conservar la contrasena actual.';
        modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
        modalUsuario.show();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Guarda guardar usuario.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarUsuario() {
    const datos = {
        nombre: document.getElementById('nombre').value.trim(),
        apellido: document.getElementById('apellido').value.trim(),
        email: document.getElementById('email').value.trim(),
        rol: document.getElementById('rol').value,
        password: document.getElementById('password').value,
        activo: document.getElementById('activo').value === 'true'
    };
    if (datos.nombre.length < 2) return alertaError('El nombre debe tener al menos 2 caracteres.');
    if (datos.apellido.length < 2) return alertaError('El apellido debe tener al menos 2 caracteres.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(datos.email)) return alertaError('El correo no es valido.');
    if (!usuarioEditandoId && datos.password.length < 6) return alertaError('La contrasena debe tener al menos 6 caracteres.');

    const esEdicion = usuarioEditandoId !== null;
    const url = esEdicion ? `${API_URL}/configuracion/usuarios/${usuarioEditandoId}` : `${API_URL}/configuracion/usuarios`;
    const method = esEdicion ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, { method, headers: headersApi(true), body: JSON.stringify(datos) });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo guardar el usuario';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            return Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
        }
        modalUsuario.hide();
        alertaExito(resultado.message || 'Usuario guardado correctamente');
        cargarUsuarios();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Elimina eliminar usuario.
// Como lo hace: Confirma la accion, llama la API y actualiza el listado para reflejar el cambio.
async function eliminarUsuario(id) {
    const confirmacion = await Swal.fire({ title: 'Desactivar usuario', text: 'El usuario no podra iniciar sesion mientras este inactivo.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Si, desactivar' });
    if (!confirmacion.isConfirmed) return;
    await cambiarEstado(id, 'DELETE', 'Usuario desactivado');
}

// Documentacion: Reactiva reactivar usuario.
// Como lo hace: Llama el endpoint de reactivacion y refresca la tabla para mostrar el nuevo estado.
async function reactivarUsuario(id) {
    await cambiarEstado(id, 'POST', 'Usuario reactivado', `${API_URL}/configuracion/usuarios/${id}/reactivar`);
}

// Documentacion: Ejecuta cambiar estado.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function cambiarEstado(id, method, mensaje, url = null) {
    try {
        const response = await fetch(url || `${API_URL}/configuracion/usuarios/${id}`, { method, headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo completar la accion');
        alertaExito(resultado.message || mensaje);
        cargarUsuarios();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Formatea formatear fecha hora.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearFechaHora(valor) {
    if (!valor) return 'N/A';
    const fecha = new Date(valor);
    return fecha.toLocaleString('es-EC', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
}

// Documentacion: Protege texto antes de insertarlo como HTML.
// Como lo hace: Reemplaza caracteres especiales para evitar inyeccion de marcado.
function escaparHtml(valor) {
    return String(valor ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}
</script>

@endsection
