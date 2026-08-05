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

    .color-preview {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-weight: 800;
    }

    .color-dot {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        border: 1px solid rgba(0,0,0,.16);
        display: inline-block;
    }

    .preview-badge {
        border-radius: 6px;
        padding: .35rem .55rem;
        font-weight: 800;
        border: 1px solid rgba(0,0,0,.14);
    }
</style>

<div class="config-page-header">
    <div>
        <h3 class="config-title">Configuracion de odontograma</h3>
        <p class="config-subtitle">CRUD de colores, acciones y significados usados al marcar dientes.</p>
    </div>
    <div class="toolbar-actions">
        <a href="/configuracion" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <button class="btn btn-primary" onclick="abrirModalOpcion()">
            <i class="bi bi-plus-circle me-1"></i> Nueva opcion
        </button>
    </div>
</div>

<div class="clean-card mb-4">
    <div class="clean-card-body">
        <div class="filters-grid">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" id="buscar" class="form-control" placeholder="Clave, accion o grupo" onkeyup="if(event.key==='Enter'){paginaActual=1;cargarOpciones();}">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select id="filtroActivo" class="form-select" onchange="paginaActual=1;cargarOpciones()">
                    <option value="">Todos</option>
                    <option value="true">Activos</option>
                    <option value="false">Inactivos</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" onclick="paginaActual=1;cargarOpciones()">
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
                        <th>Accion</th>
                        <th>Clave</th>
                        <th>Grupo</th>
                        <th>Color</th>
                        <th>Vista previa</th>
                        <th>Estado</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tablaOpciones">
                    <tr><td colspan="8" class="text-center text-muted py-4">Cargando opciones...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
            <small class="text-muted" id="infoOpciones">Sin datos</small>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="btnAnterior" onclick="cambiarPagina(-1)" disabled>Anterior</button>
                <button class="btn btn-outline-secondary btn-sm" id="btnSiguiente" onclick="cambiarPagina(1)" disabled>Siguiente</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOpcion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalOpcionLabel">Nueva opcion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formOpcion">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Accion / significado</label>
                            <input type="text" id="label" class="form-control" maxlength="100" placeholder="Ej: Curacion">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Clave interna</label>
                            <input type="text" id="clave" class="form-control" maxlength="40" placeholder="ej: curacion">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Color</label>
                            <input type="color" id="color" class="form-control form-control-color w-100" value="#0d6efd" oninput="actualizarPreview()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grupo clinico</label>
                            <select id="grupo" class="form-select">
                                <option value="hallazgo">Hallazgo</option>
                                <option value="tratamiento">Tratamiento</option>
                                <option value="neutro">Neutro</option>
                                <option value="cpo_c">Indice CPO - Cariados</option>
                                <option value="cpo_o">Indice CPO - Obturados</option>
                                <option value="cpo_p">Indice CPO - Perdidos</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select id="activo" class="form-select">
                                <option value="true">Activo</option>
                                <option value="false">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Vista previa</label><br>
                            <span class="preview-badge" id="preview">Opcion</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarOpcion()">
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
let opcionEditandoId = null;
let modalOpcion = null;

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('label').addEventListener('input', actualizarPreview);
    cargarOpciones();
});

// Documentacion: Construye los encabezados para llamar la API.
// Como lo hace: Incluye Accept JSON y el token Bearer guardado en localStorage.
function headersApi(json = false) {
    const headers = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    };
    if (json) headers['Content-Type'] = 'application/json';
    return headers;
}

// Documentacion: Carga cargar opciones.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarOpciones() {
    const tabla = document.getElementById('tablaOpciones');
    const search = document.getElementById('buscar').value.trim();
    const activo = document.getElementById('filtroActivo').value;
    let url = `${API_URL}/configuracion/odontograma-opciones?page=${paginaActual}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (activo !== '') url += `&activo=${encodeURIComponent(activo)}`;

    tabla.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Cargando opciones...</td></tr>';

    try {
        const response = await fetch(url, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar las opciones');
        const data = resultado.data;
        paginaActual = data.current_page ?? 1;
        ultimaPagina = data.last_page ?? 1;
        renderizarOpciones(data.data ?? []);
        actualizarPaginacion(data.total ?? 0);
    } catch (error) {
        tabla.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${escaparHtml(error.message)}</td></tr>`;
    }
}

// Documentacion: Renderiza renderizar opciones.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarOpciones(opciones) {
    const tabla = document.getElementById('tablaOpciones');
    if (!opciones.length) {
        tabla.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No hay opciones registradas</td></tr>';
        return;
    }

    tabla.innerHTML = opciones.map((opcion, index) => `
        <tr>
            <td>${((paginaActual - 1) * 15) + index + 1}</td>
            <td>${escaparHtml(opcion.label)}</td>
            <td><code>${escaparHtml(opcion.clave)}</code></td>
            <td>${formatearGrupo(opcion.grupo)}</td>
            <td><span class="color-preview"><span class="color-dot" style="background:${opcion.color};"></span>${escaparHtml(opcion.color)}</span></td>
            <td><span class="preview-badge" style="${estiloPreview(opcion.color)}">${escaparHtml(opcion.label)}</span></td>
            <td>${opcion.activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-warning" onclick="editarOpcion(${opcion.id})"><i class="bi bi-pencil"></i></button>
                ${opcion.activo
                    ? `<button class="btn btn-sm btn-outline-danger" onclick="eliminarOpcion(${opcion.id})"><i class="bi bi-trash"></i></button>`
                    : `<button class="btn btn-sm btn-outline-success" onclick="reactivarOpcion(${opcion.id})"><i class="bi bi-arrow-clockwise"></i></button>`}
            </td>
        </tr>
    `).join('');
}

// Documentacion: Actualiza actualizar paginacion.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPaginacion(total) {
    document.getElementById('infoOpciones').textContent = `Pagina ${paginaActual} de ${ultimaPagina} | Total: ${total} opciones`;
    document.getElementById('btnAnterior').disabled = paginaActual <= 1;
    document.getElementById('btnSiguiente').disabled = paginaActual >= ultimaPagina;
}

// Documentacion: Ejecuta cambiar pagina.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPagina(direccion) {
    const nueva = paginaActual + direccion;
    if (nueva < 1 || nueva > ultimaPagina) return;
    paginaActual = nueva;
    cargarOpciones();
}

// Documentacion: Abre abrir modal opcion.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalOpcion() {
    opcionEditandoId = null;
    document.getElementById('formOpcion').reset();
    document.getElementById('color').value = '#0d6efd';
    document.getElementById('activo').value = 'true';
    document.getElementById('modalOpcionLabel').textContent = 'Nueva opcion';
    actualizarPreview();
    modalOpcion = new bootstrap.Modal(document.getElementById('modalOpcion'));
    modalOpcion.show();
}

// Documentacion: Ejecuta editar opcion.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function editarOpcion(id) {
    try {
        const response = await fetch(`${API_URL}/configuracion/odontograma-opciones/${id}`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo obtener la opcion');
        const opcion = resultado.data;
        opcionEditandoId = opcion.id;
        document.getElementById('label').value = opcion.label ?? '';
        document.getElementById('clave').value = opcion.clave ?? '';
        document.getElementById('color').value = opcion.color ?? '#0d6efd';
        document.getElementById('grupo').value = opcion.grupo ?? 'hallazgo';
        document.getElementById('activo').value = opcion.activo ? 'true' : 'false';
        document.getElementById('modalOpcionLabel').textContent = 'Editar opcion';
        actualizarPreview();
        modalOpcion = new bootstrap.Modal(document.getElementById('modalOpcion'));
        modalOpcion.show();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Guarda guardar opcion.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarOpcion() {
    const datos = {
        label: document.getElementById('label').value.trim(),
        clave: document.getElementById('clave').value.trim(),
        color: document.getElementById('color').value,
        grupo: document.getElementById('grupo').value,
        activo: document.getElementById('activo').value === 'true'
    };

    if (datos.label.length < 2) return alertaError('La accion debe tener al menos 2 caracteres.');
    if (!/^[a-z0-9_]{2,40}$/.test(datos.clave)) return alertaError('La clave solo puede tener minusculas, numeros y guion bajo.');

    const esEdicion = opcionEditandoId !== null;
    const url = esEdicion ? `${API_URL}/configuracion/odontograma-opciones/${opcionEditandoId}` : `${API_URL}/configuracion/odontograma-opciones`;
    const method = esEdicion ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, { method, headers: headersApi(true), body: JSON.stringify(datos) });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo guardar la opcion';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            return Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
        }
        modalOpcion.hide();
        alertaExito(resultado.message || 'Opcion guardada correctamente');
        cargarOpciones();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Elimina eliminar opcion.
// Como lo hace: Confirma la accion, llama la API y actualiza el listado para reflejar el cambio.
async function eliminarOpcion(id) {
    const confirmacion = await Swal.fire({ title: 'Eliminar opcion', text: 'Si tiene marcas historicas se desactivara.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Si, eliminar' });
    if (!confirmacion.isConfirmed) return;
    await cambiarEstado(id, 'DELETE', 'Opcion eliminada');
}

// Documentacion: Reactiva reactivar opcion.
// Como lo hace: Llama el endpoint de reactivacion y refresca la tabla para mostrar el nuevo estado.
async function reactivarOpcion(id) {
    await cambiarEstado(id, 'POST', 'Opcion reactivada', `${API_URL}/configuracion/odontograma-opciones/${id}/reactivar`);
}

// Documentacion: Ejecuta cambiar estado.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function cambiarEstado(id, method, mensaje, url = null) {
    try {
        const response = await fetch(url || `${API_URL}/configuracion/odontograma-opciones/${id}`, { method, headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo completar la accion');
        alertaExito(resultado.message || mensaje);
        cargarOpciones();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Actualiza actualizar preview.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPreview() {
    const preview = document.getElementById('preview');
    const color = document.getElementById('color').value || '#0d6efd';
    preview.textContent = document.getElementById('label').value.trim() || 'Opcion';
    preview.setAttribute('style', estiloPreview(color));
}

// Documentacion: Ejecuta estilo preview.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function estiloPreview(color) {
    return `background:${color};color:${contraste(color)};border-color:rgba(0,0,0,.14);`;
}

// Documentacion: Ejecuta contraste.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function contraste(hex) {
    const color = hex.replace('#', '');
    const r = parseInt(color.substring(0, 2), 16);
    const g = parseInt(color.substring(2, 4), 16);
    const b = parseInt(color.substring(4, 6), 16);
    return ((r * 299 + g * 587 + b * 114) / 1000) > 155 ? '#172033' : '#ffffff';
}

// Documentacion: Formatea formatear grupo.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearGrupo(grupo) {
    const labels = {
        neutro: 'Neutro',
        cpo_c: 'CPO - Cariados',
        cpo_o: 'CPO - Obturados',
        cpo_p: 'CPO - Perdidos',
        tratamiento: 'Tratamiento',
        hallazgo: 'Hallazgo'
    };
    return labels[grupo] ?? grupo;
}

// Documentacion: Protege texto antes de insertarlo como HTML.
// Como lo hace: Reemplaza caracteres especiales para evitar inyeccion de marcado.
function escaparHtml(valor) {
    return String(valor ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}
</script>

@endsection
