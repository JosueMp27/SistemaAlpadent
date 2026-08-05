{{--
    Documentacion de archivo:
    Vista Blade del modulo pacientes; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')
<style>
    .patients-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .patients-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .summary-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        padding: 1rem 1.2rem;
        box-shadow: 0 0.25rem 1rem rgba(0,0,0,.04);
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

    .clean-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        box-shadow: 0 0.25rem 1rem rgba(0,0,0,.04);
    }

    .clean-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #edf0f2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
    }

    .clean-card-body {
        padding: 1.25rem;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: 1.4fr .8fr .8fr auto;
        gap: 1rem;
        align-items: end;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .modal-section {
        border: 1px solid #edf0f2;
        border-radius: 1rem;
        padding: 1rem;
        height: 100%;
        background: #fcfcfd;
    }

    .modal-section-title {
        font-size: .95rem;
        font-weight: 700;
        color: #0d6efd;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .mini-note {
        font-size: .82rem;
        color: #6c757d;
    }

    .readonly-box {
        background: #f8f9fa;
    }

    #modalPaciente .modal-content {
        max-height: calc(100vh - 2rem);
    }

    #modalPaciente .modal-body {
        overflow-y: auto;
    }

    #modalPaciente .modal-footer {
        flex-shrink: 0;
        background: #fff;
        border-top: 1px solid #edf0f2;
    }

    @media (max-width: 991px) {
        .summary-grid,
        .filters-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="patients-header">
    <div>
        <h3 class="mb-1 fw-bold">Gestión de pacientes</h3>
        <p class="patients-subtitle">Registro, búsqueda y control de pacientes del consultorio</p>
    </div>

    <button class="btn btn-primary px-4" onclick="abrirModalPaciente()">
        <i class="bi bi-plus-circle me-1"></i> Nuevo paciente
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
    <div class="summary-card">
        <div class="summary-label">Menores de edad</div>
        <p class="summary-value text-warning" id="statMenores">0</p>
    </div>
</div>

<div class="clean-card mb-4">
    <div class="clean-card-header">
        <span><i class="bi bi-funnel me-2"></i>Filtros</span>
        <button class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltrosPacientes()">Limpiar</button>
    </div>
    <div class="clean-card-body">
        <div class="filters-grid">
            <div>
                <label class="form-label">Buscar paciente</label>
                <input type="text" id="buscarPaciente" class="form-control" placeholder="Buscar por nombre, apellido, historia o teléfono" onkeyup="if(event.key==='Enter'){paginaActual=1;cargarPacientes();}">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select id="filtroEstadoPaciente" class="form-select" onchange="filtrarFrontendPacientes()">
                    <option value="">Todos</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
            <div>
                <label class="form-label">Sexo</label>
                <select id="filtroSexoPaciente" class="form-select" onchange="filtrarFrontendPacientes()">
                    <option value="">Todos</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div>
                <button class="btn btn-outline-primary w-100" onclick="paginaActual=1;cargarPacientes()">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="clean-card">
    <div class="clean-card-header">
        <span><i class="bi bi-people me-2"></i>Listado de pacientes</span>
        <small class="text-muted" id="infoPacientes">Sin datos</small>
    </div>
    <div class="clean-card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Historia</th>
                        <th>Paciente</th>
                        <th>Edad</th>
                        <th>Sexo</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaPacientes">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Cargando pacientes...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end align-items-center gap-2 mt-3">
            <button class="btn btn-outline-secondary btn-sm" id="btnAnterior" onclick="cambiarPagina(-1)" disabled>
                Anterior
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="btnSiguiente" onclick="cambiarPagina(1)" disabled>
                Siguiente
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-0" id="modalPacienteLabel">Nuevo paciente</h5>
                    <small class="opacity-75">Registro general del paciente</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formPaciente">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <div class="modal-section">
                                <div class="modal-section-title">
                                    <i class="bi bi-person-vcard"></i> Datos personales
                                </div>

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
                                        <label class="form-label">Fecha de nacimiento</label>
                                        <input type="date" id="fecha_nacimiento" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Sexo</label>
                                        <select id="sexo" class="form-select" onchange="toggleCampoEmbarazo()">
                                            <option value="">Seleccione</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" id="telefono" class="form-control" maxlength="20">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Correo electrónico</label>
                                        <input type="email" id="email" class="form-control" maxlength="150" placeholder="ejemplo@gmail.com">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" id="direccion" class="form-control" maxlength="255">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="modal-section">
                                <div class="modal-section-title">
                                    <i class="bi bi-journal-medical"></i> Historia clínica inicial
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Bajo tratamiento médico</label>
                                        <select id="bajo_tratamiento_medico" class="form-select">
                                            <option value="false">No</option>
                                            <option value="true">Sí</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Propenso a hemorragia</label>
                                        <select id="problemas_hemorragicos" class="form-select">
                                            <option value="false">No</option>
                                            <option value="true">Sí</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Alérgico a medicamentos</label>
                                        <select id="alergias_medicamentos" class="form-select" onchange="toggleDetalleAlergias()">
                                            <option value="false">No</option>
                                            <option value="true">Sí</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Hipertenso</label>
                                        <select id="hipertenso" class="form-select">
                                            <option value="false">No</option>
                                            <option value="true">Sí</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Diabético</label>
                                        <select id="diabetes" class="form-select">
                                            <option value="false">No</option>
                                            <option value="true">Sí</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Embarazada</label>
                                        <select id="embarazo" class="form-select">
                                            <option value="false">No</option>
                                            <option value="true">Sí</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Detalle de alergias</label>
                                        <textarea id="detalle_alergias" class="form-control" rows="2" maxlength="1000"
                                            placeholder="Ej: Penicilina, ibuprofeno..." disabled></textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Motivo de la consulta inicial</label>
                                        <textarea id="motivo_consulta_inicial" class="form-control" rows="2" maxlength="255"
                                            placeholder="Ej: Dolor de muelas"></textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Diagnóstico</label>
                                        <textarea class="form-control readonly-box" rows="2" disabled
                                            placeholder="Se registrará después, cuando se atienda la cita"></textarea>
                                    </div>
                                </div>
                                <div class="mini-note mt-3">
                                    El diagnóstico no se registra aquí. Solo se guarda el motivo de consulta inicial y los antecedentes clínicos.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPaciente" onclick="guardarPaciente()">
                    <i class="bi bi-floppy me-1"></i> Guardar paciente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const API_URL = `${window.location.origin}/api/v1`;
    let paginaActual = 1;
    let ultimaPagina = 1;
    let modalPaciente = null;
    let pacienteEditandoId = null;
    let pacientesCache = [];
    let pacientesFiltrados = [];

    // Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
    // Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
    document.addEventListener('DOMContentLoaded', () => {
        toggleCampoEmbarazo();
        cargarPacientes();
    });

    // Documentacion: Carga cargar pacientes.
    // Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
    async function cargarPacientes() {
        const tbody = document.getElementById('tablaPacientes');
        const busqueda = document.getElementById('buscarPaciente').value.trim();
        const token = localStorage.getItem('token');

        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Cargando pacientes...</td></tr>';

        try {
            let url = `${API_URL}/pacientes?page=${paginaActual}`;
            if (busqueda) url += `&search=${encodeURIComponent(busqueda)}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });

            const resultado = await response.json();

            if (!response.ok || !resultado.success) {
                throw new Error(resultado.message || 'Error al obtener los pacientes');
            }

            const pacientes = resultado.data?.data ?? resultado.data ?? [];
            paginaActual = resultado.data?.current_page ?? 1;
            ultimaPagina = resultado.data?.last_page ?? 1;
            pacientesCache = pacientes;
            pacientesFiltrados = aplicarFiltrosPacientes(pacientesCache);

            renderizarPacientes(pacientesFiltrados);
            actualizarResumenPacientes(pacientesCache);
            actualizarPaginacionPacientes(resultado.data?.total ?? pacientes.length);

        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger py-4">${error.message}</td></tr>`;
            document.getElementById('infoPacientes').textContent = 'Error al cargar';
        }
    }

    // Documentacion: Ejecuta aplicar filtros pacientes.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    function aplicarFiltrosPacientes(pacientes) {
        const estado = document.getElementById('filtroEstadoPaciente').value;
        const sexo = document.getElementById('filtroSexoPaciente').value;

        return pacientes.filter(p => {
            const cumpleEstado = !estado || (estado === 'activo' ? !!p.activo : !p.activo);
            const cumpleSexo = !sexo || p.sexo === sexo;
            return cumpleEstado && cumpleSexo;
        });
    }

    // Documentacion: Ejecuta filtrar frontend pacientes.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    function filtrarFrontendPacientes() {
        pacientesFiltrados = aplicarFiltrosPacientes(pacientesCache);
        renderizarPacientes(pacientesFiltrados);
    }

    // Documentacion: Limpia limpiar filtros pacientes.
    // Como lo hace: Reinicia campos o filtros y vuelve al estado base de la pantalla.
    function limpiarFiltrosPacientes() {
        document.getElementById('buscarPaciente').value = '';
        document.getElementById('filtroEstadoPaciente').value = '';
        document.getElementById('filtroSexoPaciente').value = '';
        paginaActual = 1;
        cargarPacientes();
    }

    // Documentacion: Renderiza renderizar pacientes.
    // Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
    function renderizarPacientes(pacientes) {
        const tbody = document.getElementById('tablaPacientes');

        if (!pacientes || pacientes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No se encontraron pacientes</td></tr>';
            return;
        }

        let html = '';
        pacientes.forEach((p, index) => {
            const numeroFila = ((paginaActual - 1) * 15) + index + 1;
            html += `
                <tr>
                    <td>${numeroFila}</td>
                    <td>${p.numero_historia ?? 'N/A'}</td>
                    <td>${p.nombre} ${p.apellido}</td>
                    <td>${calcularEdad(p.fecha_nacimiento)}</td>
                    <td>${p.sexo === 'M' ? 'Masculino' : 'Femenino'}</td>
                    <td>
                        <div>${p.telefono ?? 'N/A'}</div>
                        <small class="text-muted">${p.email ?? 'Sin correo'}</small>
                    </td>
                    <td>${p.direccion ?? 'N/A'}</td>
                    <td>
                        ${p.activo
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-danger">Inactivo</span>'}
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                            <button class="btn btn-sm btn-outline-primary" onclick="verPaciente(${p.id})" title="Ver">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="editarPaciente(${p.id})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            ${p.activo
                                ? `<button class="btn btn-sm btn-outline-danger" onclick="eliminarPaciente(${p.id})" title="Desactivar">
                                        <i class="bi bi-trash"></i>
                                   </button>`
                                : `<button class="btn btn-sm btn-outline-success" onclick="reactivarPaciente(${p.id})" title="Reactivar">
                                        <i class="bi bi-arrow-clockwise"></i>
                                   </button>`}
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Documentacion: Actualiza actualizar resumen pacientes.
    // Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
    function actualizarResumenPacientes(pacientes) {
        const total = pacientes.length;
        const activos = pacientes.filter(p => !!p.activo).length;
        const inactivos = pacientes.filter(p => !p.activo).length;
        const menores = pacientes.filter(p => calcularEdadNumero(p.fecha_nacimiento) < 18).length;

        document.getElementById('statTotal').textContent = total;
        document.getElementById('statActivos').textContent = activos;
        document.getElementById('statInactivos').textContent = inactivos;
        document.getElementById('statMenores').textContent = menores;
    }

    // Documentacion: Actualiza actualizar paginacion pacientes.
    // Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
    function actualizarPaginacionPacientes(total) {
        document.getElementById('infoPacientes').textContent = `Página ${paginaActual} de ${ultimaPagina} | Total: ${total} pacientes`;
        document.getElementById('btnAnterior').disabled = paginaActual <= 1;
        document.getElementById('btnSiguiente').disabled = paginaActual >= ultimaPagina;
    }

    // Documentacion: Ejecuta cambiar pagina.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    function cambiarPagina(direccion) {
        const nuevaPagina = paginaActual + direccion;
        if (nuevaPagina > 0 && nuevaPagina <= ultimaPagina) {
            paginaActual = nuevaPagina;
            cargarPacientes();
        }
    }

    // Documentacion: Abre abrir modal paciente.
    // Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
    function abrirModalPaciente() {
        limpiarFormularioPaciente();
        pacienteEditandoId = null;
        document.getElementById('modalPacienteLabel').textContent = 'Nuevo paciente';
        document.getElementById('btnGuardarPaciente').innerHTML = '<i class="bi bi-floppy me-1"></i> Guardar paciente';

        const modalElement = document.getElementById('modalPaciente');
        modalPaciente = new bootstrap.Modal(modalElement);
        modalPaciente.show();
    }

    // Documentacion: Limpia limpiar formulario paciente.
    // Como lo hace: Reinicia campos o filtros y vuelve al estado base de la pantalla.
    function limpiarFormularioPaciente() {
        document.getElementById('formPaciente').reset();
        pacienteEditandoId = null;

        document.getElementById('bajo_tratamiento_medico').value = 'false';
        document.getElementById('problemas_hemorragicos').value = 'false';
        document.getElementById('alergias_medicamentos').value = 'false';
        document.getElementById('hipertenso').value = 'false';
        document.getElementById('diabetes').value = 'false';
        document.getElementById('embarazo').value = 'false';
        toggleCampoEmbarazo();
        document.getElementById('detalle_alergias').value = '';
        document.getElementById('detalle_alergias').disabled = true;
        document.getElementById('motivo_consulta_inicial').value = '';
    }

    // Documentacion: Ejecuta toggle detalle alergias.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    function toggleDetalleAlergias() {
        const alergias = document.getElementById('alergias_medicamentos').value === 'true';
        const detalle = document.getElementById('detalle_alergias');

        detalle.disabled = !alergias;

        if (!alergias) {
            detalle.value = '';
        }
    }

    // Documentacion: Ejecuta toggle campo embarazo.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    function toggleCampoEmbarazo() {
        const sexo = document.getElementById('sexo').value;
        const embarazo = document.getElementById('embarazo');
        const esMasculino = sexo === 'M';

        embarazo.disabled = esMasculino;

        if (esMasculino) {
            embarazo.value = 'false';
        }
    }

    // Documentacion: Ejecuta validar formulario paciente.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    function validarFormularioPaciente() {
        const nombre = document.getElementById('nombre').value.trim();
        const apellido = document.getElementById('apellido').value.trim();
        const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
        const sexo = document.getElementById('sexo').value;
        const telefono = document.getElementById('telefono').value.trim();
        const email = document.getElementById('email').value.trim();

        const alergiasMedicamentos = document.getElementById('alergias_medicamentos').value === 'true';
        const detalleAlergias = document.getElementById('detalle_alergias').value.trim();
        const motivoConsultaInicial = document.getElementById('motivo_consulta_inicial').value.trim();

        if (nombre.length < 2) return alertaError('El nombre debe tener al menos 2 caracteres'), false;
        if (apellido.length < 2) return alertaError('El apellido debe tener al menos 2 caracteres'), false;
        if (!fechaNacimiento) return alertaError('La fecha de nacimiento es requerida'), false;

        const hoy = new Date();
        const fechaIngresada = new Date(fechaNacimiento + 'T00:00:00');
        if (fechaIngresada >= hoy) return alertaError('La fecha de nacimiento debe ser anterior a hoy'), false;

        if (sexo !== 'M' && sexo !== 'F') return alertaError('Debe seleccionar el sexo'), false;

        if (telefono !== '') {
            const regexTelefono = /^[0-9+() -]{7,20}$/;
            if (!regexTelefono.test(telefono)) return alertaError('El formato del teléfono no es válido'), false;
        }

        if (email !== '') {
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(email)) return alertaError('El correo electrónico no es válido'), false;
        }

        if (alergiasMedicamentos && detalleAlergias.length < 3) {
            return alertaError('Debe detallar las alergias del paciente.'), false;
        }

        if (motivoConsultaInicial !== '' && motivoConsultaInicial.length < 3) {
            return alertaError('El motivo de consulta inicial debe tener al menos 3 caracteres.'), false;
        }

        return true;
    }

    // Documentacion: Guarda guardar paciente.
    // Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
    async function guardarPaciente() {
        if (!validarFormularioPaciente()) return;

        const token = localStorage.getItem('token');

        const datos = {
            nombre: document.getElementById('nombre').value.trim(),
            apellido: document.getElementById('apellido').value.trim(),
            fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
            sexo: document.getElementById('sexo').value,
            telefono: document.getElementById('telefono').value.trim() || null,
            email: document.getElementById('email').value.trim() || null,
            direccion: document.getElementById('direccion').value.trim() || null,

            bajo_tratamiento_medico: document.getElementById('bajo_tratamiento_medico').value === 'true',
            problemas_hemorragicos: document.getElementById('problemas_hemorragicos').value === 'true',
            alergias_medicamentos: document.getElementById('alergias_medicamentos').value === 'true',
            detalle_alergias: document.getElementById('detalle_alergias').value.trim() || null,
            hipertenso: document.getElementById('hipertenso').value === 'true',
            diabetes: document.getElementById('diabetes').value === 'true',
            embarazo: document.getElementById('sexo').value === 'F' && document.getElementById('embarazo').value === 'true',
            motivo_consulta_inicial: document.getElementById('motivo_consulta_inicial').value.trim() || null
        };

        const esEdicion = pacienteEditandoId !== null;
        const url = esEdicion ? `${API_URL}/pacientes/${pacienteEditandoId}` : `${API_URL}/pacientes`;
        const metodo = esEdicion ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: metodo,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(datos)
            });

            const resultado = await response.json();

            if (!response.ok || !resultado.success) {
                let mensaje = resultado.message || (esEdicion ? 'No se pudo actualizar el paciente' : 'No se pudo registrar el paciente');
                if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
                return Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            }

            if (modalPaciente) modalPaciente.hide();
            pacienteEditandoId = null;

            alertaExito(resultado.message || (esEdicion ? 'Paciente actualizado correctamente' : 'Paciente registrado correctamente'));
            paginaActual = 1;
            cargarPacientes();

        } catch (error) {
            alertaError(esEdicion ? 'Ocurrió un error al actualizar el paciente' : 'Ocurrió un error al guardar el paciente');
        }
    }

    // Documentacion: Ejecuta ver paciente.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    async function verPaciente(id) {
        const token = localStorage.getItem('token');

        try {
            const response = await fetch(`${API_URL}/pacientes/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });

            const resultado = await response.json();
            if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo obtener el paciente');

            const p = resultado.data;
            const a = p.antecedentes || {};

            Swal.fire({
                title: 'Detalle del paciente',
                html: `
                    <div class="text-start">
                        <p><strong>Historia:</strong> ${p.numero_historia ?? 'N/A'}</p>
                        <p><strong>Nombre:</strong> ${p.nombre} ${p.apellido}</p>
                        <p><strong>Fecha de nacimiento:</strong> ${formatearFecha(p.fecha_nacimiento)}</p>
                        <p><strong>Edad:</strong> ${calcularEdad(p.fecha_nacimiento)}</p>
                        <p><strong>Sexo:</strong> ${p.sexo === 'M' ? 'Masculino' : 'Femenino'}</p>
                        <p><strong>Teléfono:</strong> ${p.telefono ?? 'No registrado'}</p>
                        <p><strong>Correo:</strong> ${p.email ?? 'No registrado'}</p>
                        <p><strong>Dirección:</strong> ${p.direccion ?? 'No registrada'}</p>
                        <hr>
                        <p><strong>Bajo tratamiento médico:</strong> ${a.bajo_tratamiento_medico ? 'Sí' : 'No'}</p>
                        <p><strong>Propenso a hemorragia:</strong> ${a.problemas_hemorragicos ? 'Sí' : 'No'}</p>
                        <p><strong>Alérgico a medicamentos:</strong> ${a.alergias_medicamentos ? 'Sí' : 'No'}</p>
                        <p><strong>Detalle alergias:</strong> ${a.detalle_alergias ?? 'No registrado'}</p>
                        <p><strong>Hipertenso:</strong> ${a.hipertenso ? 'Sí' : 'No'}</p>
                        <p><strong>Diabético:</strong> ${a.diabetes ? 'Sí' : 'No'}</p>
                        <p><strong>Embarazo:</strong> ${a.embarazo ? 'Sí' : 'No'}</p>
                        <p><strong>Motivo consulta inicial:</strong> ${a.motivo_consulta_inicial ?? 'No registrado'}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar'
            });

        } catch (error) {
            alertaError(error.message);
        }
    }

    // Documentacion: Ejecuta editar paciente.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    async function editarPaciente(id) {
        const token = localStorage.getItem('token');

        try {
            const response = await fetch(`${API_URL}/pacientes/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });

            const resultado = await response.json();
            if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo obtener el paciente');

            const paciente = resultado.data;
            const antecedentes = paciente.antecedentes || {};

            pacienteEditandoId = paciente.id;

            document.getElementById('nombre').value = paciente.nombre ?? '';
            document.getElementById('apellido').value = paciente.apellido ?? '';
            document.getElementById('fecha_nacimiento').value = paciente.fecha_nacimiento ?? '';
            document.getElementById('sexo').value = paciente.sexo ?? '';
            document.getElementById('telefono').value = paciente.telefono ?? '';
            document.getElementById('email').value = paciente.email ?? '';
            document.getElementById('direccion').value = paciente.direccion ?? '';

            document.getElementById('bajo_tratamiento_medico').value = antecedentes.bajo_tratamiento_medico ? 'true' : 'false';
            document.getElementById('problemas_hemorragicos').value = antecedentes.problemas_hemorragicos ? 'true' : 'false';
            document.getElementById('alergias_medicamentos').value = antecedentes.alergias_medicamentos ? 'true' : 'false';
            document.getElementById('hipertenso').value = antecedentes.hipertenso ? 'true' : 'false';
            document.getElementById('diabetes').value = antecedentes.diabetes ? 'true' : 'false';
            document.getElementById('embarazo').value = antecedentes.embarazo ? 'true' : 'false';
            document.getElementById('detalle_alergias').value = antecedentes.detalle_alergias ?? '';
            document.getElementById('motivo_consulta_inicial').value = antecedentes.motivo_consulta_inicial ?? '';

            toggleDetalleAlergias();
            toggleCampoEmbarazo();

            document.getElementById('modalPacienteLabel').textContent = 'Editar paciente';
            document.getElementById('btnGuardarPaciente').innerHTML = '<i class=\"bi bi-pencil-square me-1\"></i> Actualizar paciente';

            const modalElement = document.getElementById('modalPaciente');
            modalPaciente = new bootstrap.Modal(modalElement);
            modalPaciente.show();

        } catch (error) {
            alertaError(error.message);
        }
    }

    // Documentacion: Elimina eliminar paciente.
    // Como lo hace: Confirma la accion, llama la API y actualiza el listado para reflejar el cambio.
    async function eliminarPaciente(id) {
        const confirmacion = await Swal.fire({
            title: '¿Desactivar paciente?',
            text: 'El paciente dejará de estar activo en el sistema.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) return;

        const token = localStorage.getItem('token');

        try {
            const response = await fetch(`${API_URL}/pacientes/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });

            const resultado = await response.json();
            if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo desactivar el paciente');

            alertaExito(resultado.message || 'Paciente desactivado correctamente');
            cargarPacientes();
        } catch (error) {
            alertaError(error.message);
        }
    }

    // Documentacion: Reactiva reactivar paciente.
    // Como lo hace: Llama el endpoint de reactivacion y refresca la tabla para mostrar el nuevo estado.
    async function reactivarPaciente(id) {
        const token = localStorage.getItem('token');

        const confirmacion = await Swal.fire({
            title: '¿Reactivar paciente?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, reactivar'
        });

        if (!confirmacion.isConfirmed) return;

        try {
            const res = await fetch(`${API_URL}/pacientes/${id}/reactivar`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });

            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message || 'No se pudo reactivar el paciente');

            alertaExito('Paciente reactivado');
            cargarPacientes();
        } catch (error) {
            alertaError(error.message);
        }
    }

    // Documentacion: Calcula calcular edad.
    // Como lo hace: Toma valores numericos de la pantalla y deriva totales, saldos o vuelto.
    function calcularEdad(fecha) {
        const edad = calcularEdadNumero(fecha);
        return edad === null ? 'N/A' : edad;
    }

    // Documentacion: Calcula calcular edad numero.
    // Como lo hace: Toma valores numericos de la pantalla y deriva totales, saldos o vuelto.
    function calcularEdadNumero(fecha) {
        if (!fecha) return null;

        const [anio, mes, dia] = fecha.split('-').map(Number);
        const hoy = new Date();
        let edad = hoy.getFullYear() - anio;
        const mesActual = hoy.getMonth() + 1;
        const diaActual = hoy.getDate();

        if (mesActual < mes || (mesActual === mes && diaActual < dia)) edad--;
        return edad;
    }

    // Documentacion: Formatea formatear fecha.
    // Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
    function formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        const [anio, mes, dia] = fecha.split('-');
        return `${dia}/${mes}/${anio}`;
    }

    // Documentacion: Ejecuta alerta error.
    // Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
    function alertaError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: mensaje
        });
    }

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
</script>

@endsection
