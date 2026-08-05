{{--
    Documentacion de archivo:
    Vista Blade del modulo doctores externos; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .external-doctors-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .external-doctors-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .summary-card,
    .clean-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 .25rem 1rem rgba(0,0,0,.04);
    }

    .summary-card {
        padding: 1rem 1.2rem;
    }

    .summary-label {
        font-size: .85rem;
        color: #6c757d;
        margin-bottom: .35rem;
    }

    .summary-value {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
    }

    .clean-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #edf0f2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        gap: 1rem;
    }

    .clean-card-body {
        padding: 1.25rem;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(180px, .6fr) auto auto;
        gap: 1rem;
        align-items: end;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    @media (max-width: 991px) {
        .external-doctors-header,
        .clean-card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .summary-grid,
        .filters-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="external-doctors-header">
    <div>
        <h3 class="mb-1 fw-bold">Doctores externos</h3>
        <p class="external-doctors-subtitle">Configuracion de profesionales contratados para citas especificas</p>
    </div>

    <button class="btn btn-primary px-4" onclick="abrirModalDoctor()">
        <i class="bi bi-plus-circle me-1"></i> Nuevo doctor
    </button>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-label">Total cargados</div>
        <p class="summary-value text-primary" id="statTotal">0</p>
    </div>
    <div class="summary-card">
        <div class="summary-label">Activos</div>
        <p class="summary-value text-success" id="statActivos">0</p>
    </div>
    <div class="summary-card">
        <div class="summary-label">Inactivos</div>
        <p class="summary-value text-danger" id="statInactivos">0</p>
    </div>
</div>

<div class="clean-card mb-4">
    <div class="clean-card-header">
        <span><i class="bi bi-funnel me-2"></i>Filtros</span>
        <button class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltrosDoctores()">Limpiar</button>
    </div>
    <div class="clean-card-body">
        <div class="filters-grid">
            <div>
                <label class="form-label">Buscar doctor</label>
                <input type="text" id="buscarDoctor" class="form-control" placeholder="Buscar por nombre, especialidad, telefono o correo" onkeyup="if(event.key==='Enter'){paginaActual=1;cargarDoctores();}">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select id="filtroEstadoDoctor" class="form-select" onchange="paginaActual=1;cargarDoctores()">
                    <option value="">Todos</option>
                    <option value="true">Activos</option>
                    <option value="false">Inactivos</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" onclick="paginaActual=1;cargarDoctores()">
                <i class="bi bi-search me-1"></i> Buscar
            </button>
            <button class="btn btn-outline-secondary" onclick="cargarDoctores()">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
            </button>
        </div>
    </div>
</div>

<div class="clean-card">
    <div class="clean-card-header">
        <span><i class="bi bi-person-badge me-2"></i>Listado de doctores externos</span>
        <small class="text-muted" id="infoDoctores">Sin datos</small>
    </div>
    <div class="clean-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Doctor</th>
                        <th>Especialidad</th>
                        <th>Contacto</th>
                        <th>Citas</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaDoctores">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Cargando doctores externos...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end align-items-center gap-2 mt-3">
            <button class="btn btn-outline-secondary btn-sm" id="btnAnteriorDoctores" onclick="cambiarPagina(-1)" disabled>
                Anterior
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="btnSiguienteDoctores" onclick="cambiarPagina(1)" disabled>
                Siguiente
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDoctor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-0" id="modalDoctorLabel">Nuevo doctor externo</h5>
                    <small class="opacity-75">Datos del profesional contratado</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formDoctor">
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
                            <label class="form-label">Especialidad</label>
                            <input type="text" id="especialidad" class="form-control" maxlength="150" placeholder="Ej: Cirugia maxilofacial">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select id="activo" class="form-select">
                                <option value="true">Activo</option>
                                <option value="false">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" id="telefono" class="form-control" maxlength="20">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo electronico</label>
                            <input type="email" id="email" class="form-control" maxlength="150" placeholder="doctor@correo.com">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarDoctor" onclick="guardarDoctor()">
                    <i class="bi bi-floppy me-1"></i> Guardar doctor
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;

let paginaActual = 1;
let ultimaPagina = 1;
let modalDoctor = null;
let doctorEditandoId = null;
let doctoresCache = [];

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', () => {
    cargarDoctores();
});

// Documentacion: Construye los encabezados para llamar la API.
// Como lo hace: Incluye Accept JSON y el token Bearer guardado en localStorage.
function headersApi() {
    return {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    };
}

// Documentacion: Carga cargar doctores.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarDoctores() {
    const tbody = document.getElementById('tablaDoctores');
    const busqueda = document.getElementById('buscarDoctor').value.trim();
    const estado = document.getElementById('filtroEstadoDoctor').value;

    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Cargando doctores externos...</td></tr>';

    try {
        let url = `${API_URL}/doctores-externos?page=${paginaActual}`;
        if (busqueda) url += `&search=${encodeURIComponent(busqueda)}`;
        if (estado !== '') url += `&activo=${encodeURIComponent(estado)}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: headersApi()
        });

        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudieron cargar los doctores externos');
        }

        const doctores = resultado.data?.data ?? [];
        paginaActual = resultado.data?.current_page ?? 1;
        ultimaPagina = resultado.data?.last_page ?? 1;
        doctoresCache = doctores;

        renderizarDoctores(doctores);
        actualizarResumenDoctores(doctores);
        actualizarPaginacionDoctores(resultado.data?.total ?? doctores.length);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${escaparHtml(error.message)}</td></tr>`;
        document.getElementById('infoDoctores').textContent = 'Error al cargar';
    }
}

// Documentacion: Renderiza renderizar doctores.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarDoctores(doctores) {
    const tbody = document.getElementById('tablaDoctores');

    if (!doctores || doctores.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay doctores externos registrados</td></tr>';
        return;
    }

    tbody.innerHTML = doctores.map((doctor, index) => {
        const numeroFila = ((paginaActual - 1) * 15) + index + 1;
        const nombreCompleto = `${doctor.nombre ?? ''} ${doctor.apellido ?? ''}`.trim();

        return `
            <tr>
                <td>${numeroFila}</td>
                <td>${escaparHtml(nombreCompleto)}</td>
                <td>${escaparHtml(doctor.especialidad ?? 'N/A')}</td>
                <td>
                    <div>${escaparHtml(doctor.telefono ?? 'Sin telefono')}</div>
                    <small class="text-muted">${escaparHtml(doctor.email ?? 'Sin correo')}</small>
                </td>
                <td>${Number(doctor.citas_count ?? 0)}</td>
                <td>
                    ${doctor.activo
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>'}
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                        <button class="btn btn-sm btn-outline-primary" onclick="verDoctor(${doctor.id})" title="Ver">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="editarDoctor(${doctor.id})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        ${doctor.activo
                            ? `<button class="btn btn-sm btn-outline-danger" onclick="eliminarDoctor(${doctor.id})" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                               </button>`
                            : `<button class="btn btn-sm btn-outline-success" onclick="reactivarDoctor(${doctor.id})" title="Reactivar">
                                    <i class="bi bi-arrow-clockwise"></i>
                               </button>`}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Documentacion: Actualiza actualizar resumen doctores.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarResumenDoctores(doctores) {
    const total = doctores.length;
    const activos = doctores.filter(d => !!d.activo).length;
    const inactivos = doctores.filter(d => !d.activo).length;

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statActivos').textContent = activos;
    document.getElementById('statInactivos').textContent = inactivos;
}

// Documentacion: Actualiza actualizar paginacion doctores.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPaginacionDoctores(total) {
    document.getElementById('infoDoctores').textContent = `Pagina ${paginaActual} de ${ultimaPagina} | Total: ${total} doctores`;
    document.getElementById('btnAnteriorDoctores').disabled = paginaActual <= 1;
    document.getElementById('btnSiguienteDoctores').disabled = paginaActual >= ultimaPagina;
}

// Documentacion: Ejecuta cambiar pagina.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPagina(direccion) {
    const nuevaPagina = paginaActual + direccion;
    if (nuevaPagina < 1 || nuevaPagina > ultimaPagina) return;

    paginaActual = nuevaPagina;
    cargarDoctores();
}

// Documentacion: Limpia limpiar filtros doctores.
// Como lo hace: Reinicia campos o filtros y vuelve al estado base de la pantalla.
function limpiarFiltrosDoctores() {
    document.getElementById('buscarDoctor').value = '';
    document.getElementById('filtroEstadoDoctor').value = '';
    paginaActual = 1;
    cargarDoctores();
}

// Documentacion: Abre abrir modal doctor.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalDoctor() {
    limpiarFormularioDoctor();
    doctorEditandoId = null;
    document.getElementById('modalDoctorLabel').textContent = 'Nuevo doctor externo';
    document.getElementById('btnGuardarDoctor').innerHTML = '<i class="bi bi-floppy me-1"></i> Guardar doctor';

    modalDoctor = new bootstrap.Modal(document.getElementById('modalDoctor'));
    modalDoctor.show();
}

// Documentacion: Limpia limpiar formulario doctor.
// Como lo hace: Reinicia campos o filtros y vuelve al estado base de la pantalla.
function limpiarFormularioDoctor() {
    document.getElementById('formDoctor').reset();
    document.getElementById('activo').value = 'true';
}

// Documentacion: Ejecuta validar formulario doctor.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function validarFormularioDoctor() {
    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const especialidad = document.getElementById('especialidad').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const email = document.getElementById('email').value.trim();

    if (nombre.length < 2) return alertaError('El nombre debe tener al menos 2 caracteres.'), false;
    if (apellido.length < 2) return alertaError('El apellido debe tener al menos 2 caracteres.'), false;
    if (especialidad.length < 2) return alertaError('La especialidad debe tener al menos 2 caracteres.'), false;

    if (telefono !== '') {
        const regexTelefono = /^[0-9+() -]{7,20}$/;
        if (!regexTelefono.test(telefono)) return alertaError('El formato del telefono no es valido.'), false;
    }

    if (email !== '') {
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(email)) return alertaError('El correo electronico no es valido.'), false;
    }

    return true;
}

// Documentacion: Guarda guardar doctor.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarDoctor() {
    if (!validarFormularioDoctor()) return;

    const datos = {
        nombre: document.getElementById('nombre').value.trim(),
        apellido: document.getElementById('apellido').value.trim(),
        especialidad: document.getElementById('especialidad').value.trim(),
        telefono: document.getElementById('telefono').value.trim() || null,
        email: document.getElementById('email').value.trim() || null,
        activo: document.getElementById('activo').value === 'true'
    };

    const esEdicion = doctorEditandoId !== null;
    const url = esEdicion ? `${API_URL}/doctores-externos/${doctorEditandoId}` : `${API_URL}/doctores-externos`;
    const metodo = esEdicion ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: metodo,
            headers: {
                ...headersApi(),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || (esEdicion ? 'No se pudo actualizar el doctor externo' : 'No se pudo crear el doctor externo');
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            return;
        }

        if (modalDoctor) modalDoctor.hide();
        doctorEditandoId = null;
        alertaExito(resultado.message || (esEdicion ? 'Doctor actualizado correctamente' : 'Doctor creado correctamente'));
        paginaActual = 1;
        cargarDoctores();
    } catch (error) {
        alertaError('Ocurrio un error al guardar el doctor externo.');
    }
}

// Documentacion: Ejecuta obtener doctor.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function obtenerDoctor(id) {
    const response = await fetch(`${API_URL}/doctores-externos/${id}`, {
        method: 'GET',
        headers: headersApi()
    });

    const resultado = await response.json();
    if (!response.ok || !resultado.success) {
        throw new Error(resultado.message || 'No se pudo obtener el doctor externo');
    }

    return resultado.data;
}

// Documentacion: Ejecuta ver doctor.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function verDoctor(id) {
    try {
        const doctor = await obtenerDoctor(id);
        const nombreCompleto = `${doctor.nombre ?? ''} ${doctor.apellido ?? ''}`.trim();

        Swal.fire({
            title: 'Detalle del doctor externo',
            html: `
                <div class="text-start">
                    <p><strong>Nombre:</strong> ${escaparHtml(nombreCompleto)}</p>
                    <p><strong>Especialidad:</strong> ${escaparHtml(doctor.especialidad ?? 'N/A')}</p>
                    <p><strong>Telefono:</strong> ${escaparHtml(doctor.telefono ?? 'No registrado')}</p>
                    <p><strong>Correo:</strong> ${escaparHtml(doctor.email ?? 'No registrado')}</p>
                    <p><strong>Citas asociadas:</strong> ${Number(doctor.citas_count ?? 0)}</p>
                    <p><strong>Estado:</strong> ${doctor.activo ? 'Activo' : 'Inactivo'}</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Cerrar'
        });
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta editar doctor.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function editarDoctor(id) {
    try {
        const doctor = await obtenerDoctor(id);
        doctorEditandoId = doctor.id;

        document.getElementById('nombre').value = doctor.nombre ?? '';
        document.getElementById('apellido').value = doctor.apellido ?? '';
        document.getElementById('especialidad').value = doctor.especialidad ?? '';
        document.getElementById('telefono').value = doctor.telefono ?? '';
        document.getElementById('email').value = doctor.email ?? '';
        document.getElementById('activo').value = doctor.activo ? 'true' : 'false';

        document.getElementById('modalDoctorLabel').textContent = 'Editar doctor externo';
        document.getElementById('btnGuardarDoctor').innerHTML = '<i class="bi bi-pencil-square me-1"></i> Actualizar doctor';

        modalDoctor = new bootstrap.Modal(document.getElementById('modalDoctor'));
        modalDoctor.show();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Elimina eliminar doctor.
// Como lo hace: Confirma la accion, llama la API y actualiza el listado para reflejar el cambio.
async function eliminarDoctor(id) {
    const confirmacion = await Swal.fire({
        title: 'Eliminar doctor externo',
        text: 'El doctor dejara de estar disponible para nuevas citas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const response = await fetch(`${API_URL}/doctores-externos/${id}`, {
            method: 'DELETE',
            headers: headersApi()
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo eliminar el doctor externo');
        }

        alertaExito(resultado.message || 'Doctor externo eliminado correctamente');
        cargarDoctores();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Reactiva reactivar doctor.
// Como lo hace: Llama el endpoint de reactivacion y refresca la tabla para mostrar el nuevo estado.
async function reactivarDoctor(id) {
    const confirmacion = await Swal.fire({
        title: 'Reactivar doctor externo',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Si, reactivar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const response = await fetch(`${API_URL}/doctores-externos/${id}/reactivar`, {
            method: 'POST',
            headers: headersApi()
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo reactivar el doctor externo');
        }

        alertaExito(resultado.message || 'Doctor externo reactivado correctamente');
        cargarDoctores();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Protege texto antes de insertarlo como HTML.
// Como lo hace: Reemplaza caracteres especiales para evitar inyeccion de marcado.
function escaparHtml(valor) {
    return String(valor ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
</script>

@endsection
