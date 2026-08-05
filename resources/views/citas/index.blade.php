{{--
    Documentacion de archivo:
    Vista Blade del modulo citas; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1 fw-bold">Citas</h3>
        <p class="text-muted mb-0">Gestion de agenda del consultorio</p>
    </div>

    <button class="btn btn-primary" onclick="abrirModalCita()">
        <i class="bi bi-plus-circle me-1"></i> Nueva cita
    </button>
</div>

<div class="modal fade" id="modalReagendar" tabindex="-1" aria-labelledby="modalReagendarLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalReagendarLabel">
                    <i class="bi bi-calendar-check me-2"></i> Reagendar cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="infoReagendar" class="alert alert-info mb-3"></div>
                <form id="formReagendar">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Nueva fecha</label>
                            <input type="date" id="reagendar_fecha" class="form-control" onchange="actualizarHorariosReagendar()">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Hora inicio</label>
                            <input type="time" id="reagendar_hora_inicio" class="form-control" step="60">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea id="reagendar_observaciones" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning text-dark" onclick="guardarReagendar()">
                    <i class="bi bi-calendar-check me-1"></i> Confirmar reagendamiento
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select id="filtroEstado" class="form-select" onchange="paginaActual = 1; cargarCitas();">
                    <option value="">Todos</option>
                    <option value="programada">Programada</option>
                    <option value="en_curso">En curso</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                    <option value="no_asistio">No asistio</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100" onclick="cargarCitas()">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Tratamiento</th>
                        <th>Doctor</th>
                        <th>Precio</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaCitas">
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Cargando citas...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted" id="infoCitas">Sin datos</small>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="btnAnteriorCitas" onclick="cambiarPagina(-1)" disabled>
                    Anterior
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="btnSiguienteCitas" onclick="cambiarPagina(1)" disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCita" tabindex="-1" aria-labelledby="modalCitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCitaLabel">Nueva cita</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formCita">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Paciente</label>
                            <select id="paciente_id" class="form-select">
                                <option value="">Seleccione un paciente</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tratamiento</label>
                            <select id="tipo_tratamiento_id" class="form-select" onchange="actualizarPrecioTratamiento()">
                                <option value="">Seleccione un tratamiento</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Profesional</label>
                            <select id="doctor_externo_id" class="form-select">
                                <option value="">Odontologa principal</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha</label>
                            <input type="date" id="fecha" class="form-control" onchange="actualizarHorariosDisponibles()">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Hora inicio</label>
                            <input type="time" id="hora_inicio" class="form-control" step="60">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Precio</label>
                            <input type="text" id="precio_tratamiento" class="form-control" readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Motivo de consulta</label>
                            <input type="text" id="motivo_consulta" class="form-control" maxlength="255">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea id="observaciones" class="form-control" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCita()">
                    <i class="bi bi-floppy me-1"></i> Guardar cita
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;

let paginaActual = 1;
let ultimaPagina = 1;
let modalCita = null;
let modalReagendar = null;
let usuarioActual = null;
let citaParaReagendar = null;
let tiposTratamientoCache = [];
let doctoresExternosCache = [];
let citasDiaSeleccionado = [];

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', async () => {
    await obtenerUsuarioActual();
    await cargarPacientesSelect();
    await cargarTiposTratamientoSelect();
    await cargarDoctoresExternosSelect();
    await cargarCitas();
});

// Documentacion: Ejecuta obtener usuario actual.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function obtenerUsuarioActual() {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/auth/me`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo obtener el usuario actual');
        }

        usuarioActual = resultado.data;
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Carga cargar pacientes select.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarPacientesSelect() {
    const token = localStorage.getItem('token');
    const select = document.getElementById('paciente_id');

    try {
        const response = await fetch(`${API_URL}/pacientes`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudieron cargar los pacientes');
        }

        const pacientes = resultado.data.data ?? [];
        select.innerHTML = `<option value="">Seleccione un paciente</option>`;

        pacientes.forEach(p => {
            select.innerHTML += `
                <option value="${p.id}">
                    ${p.numero_historia} - ${p.nombre} ${p.apellido}
                </option>
            `;
        });
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Carga cargar tipos tratamiento select.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarTiposTratamientoSelect() {
    const token = localStorage.getItem('token');
    const select = document.getElementById('tipo_tratamiento_id');

    try {
        const response = await fetch(`${API_URL}/tratamientos/listado/tipos`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudieron cargar los tratamientos');
        }

        tiposTratamientoCache = resultado.data ?? [];
        select.innerHTML = `<option value="">Seleccione un tratamiento</option>`;

        tiposTratamientoCache.forEach(tipo => {
            select.innerHTML += `
                <option value="${tipo.id}">
                    ${tipo.nombre} - ${formatearPrecio(tipo.precio)}
                </option>
            `;
        });
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Carga cargar doctores externos select.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarDoctoresExternosSelect() {
    const token = localStorage.getItem('token');
    const select = document.getElementById('doctor_externo_id');

    try {
        const response = await fetch(`${API_URL}/doctores-externos/activos`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudieron cargar los doctores externos');
        }

        doctoresExternosCache = resultado.data ?? [];
        select.innerHTML = `<option value="">Odontologa principal</option>`;

        doctoresExternosCache.forEach(doctor => {
            select.innerHTML += `
                <option value="${doctor.id}">
                    ${doctor.nombre} ${doctor.apellido} - ${doctor.especialidad}
                </option>
            `;
        });
    } catch (error) {
        select.innerHTML = `<option value="">Odontologa principal</option>`;
        doctoresExternosCache = [];
        alertaError(error.message);
    }
}

// Documentacion: Actualiza actualizar precio tratamiento.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPrecioTratamiento() {
    const tipoId = Number(document.getElementById('tipo_tratamiento_id').value);
    const tipo = tiposTratamientoCache.find(t => Number(t.id) === tipoId);
    document.getElementById('precio_tratamiento').value = tipo ? formatearPrecio(tipo.precio) : '';
}

// Documentacion: Carga cargar citas.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarCitas() {
    const token = localStorage.getItem('token');
    const estado = document.getElementById('filtroEstado').value;
    const tabla = document.getElementById('tablaCitas');

    tabla.innerHTML = `
        <tr>
            <td colspan="10" class="text-center text-muted py-4">Cargando citas...</td>
        </tr>
    `;

    let url = `${API_URL}/citas?page=${paginaActual}`;
    if (estado) url += `&estado=${encodeURIComponent(estado)}`;

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudieron obtener las citas');
        }

        const citas = resultado.data.data ?? [];
        paginaActual = resultado.data.current_page ?? 1;
        ultimaPagina = resultado.data.last_page ?? 1;

        renderizarCitas(citas);
        actualizarPaginacion(resultado.data.total ?? citas.length);
    } catch (error) {
        tabla.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-danger py-4">Error al cargar citas</td>
            </tr>
        `;
        alertaError(error.message);
    }
}

// Documentacion: Renderiza renderizar citas.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarCitas(citas) {
    const tabla = document.getElementById('tablaCitas');
    tabla.innerHTML = '';

    if (citas.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted py-4">No hay citas registradas</td>
            </tr>
        `;
        return;
    }

    citas.forEach((cita, index) => {
        const numero = ((paginaActual - 1) * 15) + index + 1;
        const tipo = cita.tipo_tratamiento;
        const doctor = cita.doctor_externo;
        const profesional = doctor ? `${doctor.nombre} ${doctor.apellido}` : 'Odontologa principal';

        tabla.innerHTML += `
            <tr>
                <td>${numero}</td>
                <td>${cita.paciente ? `${cita.paciente.nombre} ${cita.paciente.apellido}` : 'N/A'}</td>
                <td>${formatearFecha(cita.fecha_hora_inicio)}</td>
                <td>${formatearHora(cita.fecha_hora_inicio)}</td>
                <td>${tipo ? tipo.nombre : 'Sin tratamiento'}</td>
                <td>${profesional}</td>
                <td>${tipo ? formatearPrecio(tipo.precio) : 'N/A'}</td>
                <td>${cita.motivo_consulta}</td>
                <td>${badgeEstado(cita.estado)}</td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <button class="dropdown-item" onclick="verCita(${cita.id})">
                                    <i class="bi bi-eye text-primary me-2"></i> Ver detalle
                                </button>
                            </li>
                            ${cita.estado === 'programada' ? `
                            <li>
                                <button class="dropdown-item" onclick="abrirModalReagendar(${cita.id})">
                                    <i class="bi bi-calendar-check text-warning me-2"></i> Reagendar
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" onclick="confirmarCancelar(${cita.id})">
                                    <i class="bi bi-x-circle text-danger me-2"></i> Cancelar
                                </button>
                            </li>
                            ` : ''}
                            ${['programada', 'en_curso'].includes(cita.estado) ? `
                            <li>
                                <button class="dropdown-item" onclick="confirmarCompletar(${cita.id})">
                                    <i class="bi bi-check-circle text-success me-2"></i> Completar
                                </button>
                            </li>
                            ` : ''}
                        </ul>
                    </div>
                </td>
            </tr>
        `;
    });
}

// Documentacion: Actualiza actualizar paginacion.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPaginacion(total) {
    document.getElementById('infoCitas').textContent =
        `Pagina ${paginaActual} de ${ultimaPagina} | Total: ${total} citas`;

    document.getElementById('btnAnteriorCitas').disabled = paginaActual <= 1;
    document.getElementById('btnSiguienteCitas').disabled = paginaActual >= ultimaPagina;
}

// Documentacion: Ejecuta cambiar pagina.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPagina(direccion) {
    const nuevaPagina = paginaActual + direccion;
    if (nuevaPagina < 1 || nuevaPagina > ultimaPagina) return;

    paginaActual = nuevaPagina;
    cargarCitas();
}

// Documentacion: Abre abrir modal cita.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalCita() {
    document.getElementById('formCita').reset();
    document.getElementById('precio_tratamiento').value = '';
    citasDiaSeleccionado = [];

    const modalElement = document.getElementById('modalCita');
    modalCita = new bootstrap.Modal(modalElement);
    modalCita.show();
}

// Documentacion: Carga cargar disponibilidad dia.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarDisponibilidadDia(fecha) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/citas/disponibilidad?fecha=${fecha}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo consultar la disponibilidad');
        }

        citasDiaSeleccionado = resultado.data ?? [];
    } catch (error) {
        citasDiaSeleccionado = [];
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta obtener horarios ocupados.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function obtenerHorariosOcupados() {
    return citasDiaSeleccionado
        .filter(c => !['cancelada', 'no_asistio'].includes(c.estado))
        .map(c => ({
            id: c.id,
            inicio: formatearHora(c.fecha_hora_inicio),
            paciente: c.paciente ? `${c.paciente.nombre} ${c.paciente.apellido}` : 'Paciente no identificado'
        }));
}

// Documentacion: Actualiza actualizar horarios disponibles.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
async function actualizarHorariosDisponibles() {
    const fecha = document.getElementById('fecha').value;
    if (!fecha) return;
    await cargarDisponibilidadDia(fecha);
}

// Documentacion: Busca buscar conflicto local.
// Como lo hace: Actualiza filtros de la pantalla y vuelve a consultar la API.
function buscarConflictoLocal(horaInicio, excluirCitaId = null) {
    return obtenerHorariosOcupados().find(o =>
        o.inicio === horaInicio && Number(o.id) !== Number(excluirCitaId)
    ) || null;
}

// Documentacion: Ejecuta mostrar advertencia conflicto.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function mostrarAdvertenciaConflicto(conflicto) {
    Swal.fire({
        icon: 'warning',
        title: 'Horario no disponible',
        html: `
            <p>No puede agendar en este horario porque el paciente</p>
            <p><strong>${conflicto.paciente}</strong></p>
            <p>ya tiene una cita programada a las <strong>${conflicto.inicio}</strong>.</p>
            <p class="text-muted">Por favor, seleccione otro horario.</p>
        `,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#0d6efd',
    });
}

// Documentacion: Ejecuta validar fecha hora futura.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function validarFechaHoraFutura(fecha, hora) {
    const fechaHora = new Date(`${fecha}T${hora}:00`);
    return fechaHora > new Date();
}

// Documentacion: Ejecuta validar formulario cita.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function validarFormularioCita() {
    const pacienteId = document.getElementById('paciente_id').value;
    const tipoTratamientoId = document.getElementById('tipo_tratamiento_id').value;
    const fecha = document.getElementById('fecha').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const motivo = document.getElementById('motivo_consulta').value.trim();

    if (!pacienteId) return alertaError('Debe seleccionar un paciente.'), false;
    if (!tipoTratamientoId) return alertaError('Debe seleccionar un tratamiento.'), false;
    if (!fecha) return alertaError('Debe seleccionar una fecha.'), false;
    if (!horaInicio) return alertaError('Debe seleccionar una hora de inicio.'), false;
    if (!validarFechaHoraFutura(fecha, horaInicio)) return alertaError('La cita debe programarse en un horario futuro.'), false;
    if (motivo.length < 3) return alertaError('El motivo de consulta debe tener al menos 3 caracteres.'), false;

    return true;
}

// Documentacion: Guarda guardar cita.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarCita() {
    if (!validarFormularioCita()) return;

    if (!usuarioActual || !usuarioActual.id) {
        alertaError('No se pudo identificar el usuario actual.');
        return;
    }

    const token = localStorage.getItem('token');
    const fecha = document.getElementById('fecha').value;
    const horaInicio = document.getElementById('hora_inicio').value;

    await cargarDisponibilidadDia(fecha);
    const conflicto = buscarConflictoLocal(horaInicio);

    if (conflicto) {
        mostrarAdvertenciaConflicto(conflicto);
        return;
    }

    const datos = {
        paciente_id: document.getElementById('paciente_id').value,
        usuario_id: usuarioActual.id,
        tipo_tratamiento_id: document.getElementById('tipo_tratamiento_id').value,
        doctor_externo_id: document.getElementById('doctor_externo_id').value || null,
        fecha_hora_inicio: `${fecha} ${horaInicio}:00`,
        motivo_consulta: document.getElementById('motivo_consulta').value.trim(),
        observaciones: document.getElementById('observaciones').value.trim() || null
    };

    try {
        const response = await fetch(`${API_URL}/citas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(datos)
        });

        const resultado = await response.json();

        if (response.status === 409) {
            mostrarAdvertenciaConflicto(resultado.conflicto);
            return;
        }

        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo registrar la cita';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            return;
        }

        modalCita.hide();
        alertaExito(resultado.message || 'Cita registrada correctamente');
        paginaActual = 1;
        cargarCitas();
    } catch (error) {
        alertaError('Ocurrio un error al guardar la cita.');
    }
}

// Documentacion: Ejecuta ver cita.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function verCita(id) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/citas/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo obtener la cita');
        }

        const c = resultado.data;
        const tipo = c.tipo_tratamiento;
        const doctor = c.doctor_externo;
        const profesional = doctor ? `${doctor.nombre} ${doctor.apellido}` : 'Odontologa principal';

        Swal.fire({
            title: 'Detalle de la cita',
            html: `
                <div class="text-start">
                    <p><strong>Paciente:</strong> ${c.paciente ? `${c.paciente.nombre} ${c.paciente.apellido}` : 'N/A'}</p>
                    <p><strong>Fecha:</strong> ${formatearFecha(c.fecha_hora_inicio)}</p>
                    <p><strong>Hora:</strong> ${formatearHora(c.fecha_hora_inicio)}</p>
                    <p><strong>Tratamiento:</strong> ${tipo ? tipo.nombre : 'Sin tratamiento'}</p>
                    <p><strong>Profesional:</strong> ${profesional}</p>
                    <p><strong>Precio:</strong> ${tipo ? formatearPrecio(tipo.precio) : 'N/A'}</p>
                    <p><strong>Motivo:</strong> ${c.motivo_consulta ?? 'N/A'}</p>
                    <p><strong>Estado:</strong> ${c.estado ?? 'N/A'}</p>
                    <p><strong>Observaciones:</strong> ${c.observaciones ?? 'Sin observaciones'}</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Cerrar'
        });
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Abre abrir modal reagendar.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
async function abrirModalReagendar(id) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/citas/${id}`, {
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
        });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message);

        const c = resultado.data;
        citaParaReagendar = c;

        const nombrePaciente = c.paciente ? `${c.paciente.nombre} ${c.paciente.apellido}` : 'N/A';
        const fechaActual = formatearFecha(c.fecha_hora_inicio);
        const horaInicio = formatearHora(c.fecha_hora_inicio);

        document.getElementById('infoReagendar').innerHTML = `
            <strong>Paciente:</strong> ${nombrePaciente}<br>
            <strong>Cita actual:</strong> ${fechaActual} a las ${horaInicio}
        `;

        document.getElementById('formReagendar').reset();
        citasDiaSeleccionado = [];

        const modalEl = document.getElementById('modalReagendar');
        modalReagendar = new bootstrap.Modal(modalEl);
        modalReagendar.show();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Actualiza actualizar horarios reagendar.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
async function actualizarHorariosReagendar() {
    const fecha = document.getElementById('reagendar_fecha').value;
    if (!fecha) return;
    await cargarDisponibilidadDia(fecha);
}

// Documentacion: Guarda guardar reagendar.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarReagendar() {
    const fecha = document.getElementById('reagendar_fecha').value;
    const horaInicio = document.getElementById('reagendar_hora_inicio').value;
    const obs = document.getElementById('reagendar_observaciones').value.trim();

    if (!fecha) return alertaError('Debe seleccionar una nueva fecha.');
    if (!horaInicio) return alertaError('Debe seleccionar una hora de inicio.');
    if (!validarFechaHoraFutura(fecha, horaInicio)) return alertaError('La cita debe programarse en un horario futuro.');

    await cargarDisponibilidadDia(fecha);
    const conflicto = buscarConflictoLocal(horaInicio, citaParaReagendar.id);
    if (conflicto) return mostrarAdvertenciaConflicto(conflicto);

    const token = localStorage.getItem('token');
    const datos = {
        fecha_hora_inicio: `${fecha} ${horaInicio}:00`,
        observaciones: obs || null,
    };

    try {
        const response = await fetch(`${API_URL}/citas/${citaParaReagendar.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(datos)
        });

        const resultado = await response.json();

        if (response.status === 409) {
            mostrarAdvertenciaConflicto(resultado.conflicto);
            return;
        }

        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo reagendar la cita');
        }

        modalReagendar.hide();
        Swal.fire({
            icon: 'success',
            title: 'Cita reagendada',
            html: `La cita fue reagendada para el <strong>${formatearFecha(datos.fecha_hora_inicio)}</strong> a las <strong>${horaInicio}</strong>.`,
            confirmButtonColor: '#0d6efd',
            timer: 2500,
            timerProgressBar: true
        });
        paginaActual = 1;
        cargarCitas();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta confirmar cancelar.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function confirmarCancelar(id) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/citas/${id}`, {
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
        });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message);

        const c = resultado.data;
        const nombrePaciente = c.paciente ? `${c.paciente.nombre} ${c.paciente.apellido}` : 'N/A';
        const fecha = formatearFecha(c.fecha_hora_inicio);
        const horaInicio = formatearHora(c.fecha_hora_inicio);

        const { value: cancelacion } = await Swal.fire({
            icon: 'warning',
            title: 'Cancelar esta cita',
            html: `
                <div class="text-start">
                    <p>Esta a punto de cancelar la siguiente cita:</p>
                    <ul>
                        <li><strong>Paciente:</strong> ${nombrePaciente}</li>
                        <li><strong>Fecha:</strong> ${fecha}</li>
                        <li><strong>Hora:</strong> ${horaInicio}</li>
                        <li><strong>Motivo:</strong> ${c.motivo_consulta ?? 'N/A'}</li>
                    </ul>

                    <label class="form-label mt-2">Indique por que no se dio la cita</label>
                    <div class="border rounded p-2">
                        <div class="form-check">
                            <input class="form-check-input motivo-cancelacion" type="checkbox" value="no_asistio" id="motivoNoAsistio">
                            <label class="form-check-label" for="motivoNoAsistio">
                                El paciente no asistio
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input motivo-cancelacion" type="checkbox" value="paciente_cancelo" id="motivoPacienteCancelo">
                            <label class="form-check-label" for="motivoPacienteCancelo">
                                El paciente cancelo con anticipacion
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input motivo-cancelacion" type="checkbox" value="reprogramar" id="motivoReprogramar">
                            <label class="form-check-label" for="motivoReprogramar">
                                El paciente solicitara reprogramacion
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input motivo-cancelacion" type="checkbox" value="otro" id="motivoOtro">
                            <label class="form-check-label" for="motivoOtro">
                                Otro
                            </label>
                        </div>
                    </div>

                    <div id="motivoOtroContainer" class="mt-2 d-none">
                        <label class="form-label">Detalle del motivo</label>
                        <textarea id="motivoCancelacionOtro" class="form-control" rows="2" maxlength="500" placeholder="Ingrese el motivo..."></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-x-circle me-1"></i> Si, cancelar cita',
            cancelButtonText: 'No, mantener cita',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            didOpen: () => {
                const checks = document.querySelectorAll('.motivo-cancelacion');
                const otro = document.getElementById('motivoOtro');
                const otroContainer = document.getElementById('motivoOtroContainer');
                const otroTextarea = document.getElementById('motivoCancelacionOtro');

                checks.forEach(check => {
                    check.addEventListener('change', () => {
                        if (check.checked) {
                            checks.forEach(other => {
                                if (other !== check) other.checked = false;
                            });
                        }

                        const mostrarOtro = otro.checked;
                        otroContainer.classList.toggle('d-none', !mostrarOtro);

                        if (mostrarOtro) {
                            otroTextarea.focus();
                        } else {
                            otroTextarea.value = '';
                        }
                    });
                });
            },
            preConfirm: () => {
                const seleccionado = document.querySelector('.motivo-cancelacion:checked');

                if (!seleccionado) {
                    Swal.showValidationMessage('Debe seleccionar un motivo.');
                    return false;
                }

                if (seleccionado.value === 'otro') {
                    const detalle = document.getElementById('motivoCancelacionOtro').value.trim();

                    if (detalle.length < 3) {
                        Swal.showValidationMessage('Debe ingresar el detalle del motivo.');
                        return false;
                    }

                    return {
                        estado: 'cancelada',
                        observaciones: detalle,
                    };
                }

                const motivos = {
                    no_asistio: {
                        estado: 'no_asistio',
                        observaciones: 'El paciente no asistio',
                    },
                    paciente_cancelo: {
                        estado: 'cancelada',
                        observaciones: 'El paciente cancelo con anticipacion',
                    },
                    reprogramar: {
                        estado: 'cancelada',
                        observaciones: 'El paciente solicitara reprogramacion',
                    },
                };

                return motivos[seleccionado.value];
            }
        });

        if (!cancelacion) return;
        await ejecutarCancelar(id, cancelacion.estado, cancelacion.observaciones);
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta ejecutar cancelar.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function ejecutarCancelar(id, estado, observaciones) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/citas/${id}/cancelar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ estado, observaciones })
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message);

        Swal.fire({
            icon: 'success',
            title: 'Cita cancelada',
            text: 'La cita fue cancelada correctamente.',
            confirmButtonColor: '#0d6efd',
            timer: 2500,
            timerProgressBar: true
        });
        paginaActual = 1;
        cargarCitas();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta confirmar completar.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function confirmarCompletar(id) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/citas/${id}`, {
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
        });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message);

        const c = resultado.data;
        const nombrePaciente = c.paciente ? `${c.paciente.nombre} ${c.paciente.apellido}` : 'N/A';
        const fecha = formatearFecha(c.fecha_hora_inicio);
        const horaInicio = formatearHora(c.fecha_hora_inicio);

        const confirmacion = await Swal.fire({
            icon: 'question',
            title: 'Marcar cita como completada',
            html: `
                <div class="text-start">
                    <p>Va a completar la siguiente cita:</p>
                    <ul>
                        <li><strong>Paciente:</strong> ${nombrePaciente}</li>
                        <li><strong>Fecha:</strong> ${fecha}</li>
                        <li><strong>Hora:</strong> ${horaInicio}</li>
                        <li><strong>Motivo:</strong> ${c.motivo_consulta ?? 'N/A'}</li>
                    </ul>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Si, completar cita',
            cancelButtonText: 'No, volver',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
        });

        if (!confirmacion.isConfirmed) return;
        await ejecutarCompletar(id);
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta ejecutar completar.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function ejecutarCompletar(id) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/citas/${id}/completar`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message);

        Swal.fire({
            icon: 'success',
            title: 'Cita completada',
            text: 'La cita fue marcada como completada correctamente.',
            confirmButtonColor: '#0d6efd',
            timer: 2500,
            timerProgressBar: true
        });
        paginaActual = 1;
        cargarCitas();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Formatea formatear fecha.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearFecha(fechaHora) {
    if (!fechaHora) return 'N/A';
    const partes = fechaHora.replace('T', ' ').split(' ');
    const [anio, mes, dia] = partes[0].split('-');
    return `${dia}/${mes}/${anio}`;
}

// Documentacion: Formatea formatear hora.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearHora(fechaHora) {
    if (!fechaHora) return 'N/A';
    const partes = fechaHora.replace('T', ' ').split(' ');
    if (partes.length < 2) return 'N/A';
    return partes[1].substring(0, 5);
}

// Documentacion: Formatea valores monetarios en dolares.
// Como lo hace: Usa Intl.NumberFormat con moneda USD.
function formatearPrecio(valor) {
    const numero = Number(valor ?? 0);
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(numero);
}

// Documentacion: Genera badge para badge estado.
// Como lo hace: Mapea estados internos a clases y textos visuales.
function badgeEstado(estado) {
    switch (estado) {
        case 'programada':
            return '<span class="badge bg-primary">Programada</span>';
        case 'en_curso':
            return '<span class="badge bg-warning text-dark">En curso</span>';
        case 'completada':
            return '<span class="badge bg-success">Completada</span>';
        case 'cancelada':
            return '<span class="badge bg-danger">Cancelada</span>';
        case 'no_asistio':
            return '<span class="badge bg-secondary">No asistio</span>';
        default:
            return '<span class="badge bg-light text-dark">Desconocido</span>';
    }
}
</script>

@endsection
