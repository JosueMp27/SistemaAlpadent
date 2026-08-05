{{--
    Documentacion de archivo:
    Vista Blade del modulo odontogramas; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .odonto-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 22px;
    }

    .odonto-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: 4px;
    }

    .odonto-subtitle {
        color: #697586;
        margin-bottom: 0;
    }

    .odonto-card,
    .patient-search-card,
    .patient-info-card,
    .indicator-card {
        border: 0;
        border-radius: 8px;
    }

    .patient-option {
        width: 100%;
        border: 1px solid #e4ebf4;
        background: #fff;
        color: #172033;
        border-radius: 8px;
        padding: 10px 12px;
        text-align: left;
        transition: border-color .15s ease, background .15s ease, transform .15s ease;
    }

    .patient-option:hover,
    .patient-option.active {
        border-color: #0d6efd;
        background: #eef7ff;
        transform: translateY(-1px);
    }

    .patient-option small {
        color: #697586;
        display: block;
    }

    .patient-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 22px;
    }

    .patient-info-item {
        display: grid;
        grid-template-columns: 128px minmax(0, 1fr);
        gap: 10px;
        padding: 6px 0;
        border-bottom: 1px solid #edf1f6;
    }

    .patient-info-label {
        color: #526072;
        font-weight: 800;
        font-size: .84rem;
    }

    .patient-info-value {
        color: #1f2b3d;
        overflow-wrap: anywhere;
    }

    .legend-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 8px;
    }

    .legend-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #e4ebf4;
        border-radius: 8px;
        background: #fff;
        padding: 8px 10px;
        font-size: .84rem;
        color: #26364a;
    }

    .legend-color {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        border: 1px solid rgba(0, 0, 0, .12);
        flex: 0 0 auto;
    }

    .odontogram-board {
        background: #fff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        padding: 16px;
        overflow-x: auto;
    }

    .odontogram-row {
        min-width: 940px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 28px;
        padding: 10px 0 18px;
        border-bottom: 1px solid #ccd6e2;
    }

    .odontogram-row:last-child {
        border-bottom: 0;
    }

    .quadrant {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .quadrant.primary {
        padding-inline: 120px 38px;
    }

    .quadrant.primary.right {
        padding-inline: 38px 120px;
    }

    .tooth-card {
        width: 58px;
        min-height: 92px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        gap: 5px;
        position: relative;
    }

    .tooth-number {
        font-weight: 800;
        color: #344256;
        font-size: .86rem;
        line-height: 1;
    }

    .tooth-svg {
        width: 48px;
        height: 48px;
        overflow: visible;
    }

    .tooth-svg.primary-tooth {
        width: 44px;
        height: 44px;
    }

    .tooth-surface {
        stroke: #6bbfc2;
        stroke-width: 1.8;
        cursor: pointer;
        transition: filter .15s ease, stroke .15s ease;
    }

    .tooth-surface:hover {
        filter: brightness(.94);
        stroke: #0d6efd;
        stroke-width: 2.4;
    }

    .tooth-x {
        stroke: #ef4444;
        stroke-width: 4;
        stroke-linecap: round;
        pointer-events: none;
    }

    .tooth-fracture {
        stroke: #b91c1c;
        stroke-width: 3;
        stroke-linecap: round;
        pointer-events: none;
    }

    .tooth-mini-label {
        max-width: 54px;
        min-height: 18px;
        font-size: .66rem;
        font-weight: 800;
        color: #526072;
        text-align: center;
        line-height: 1.1;
    }

    .indices-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(280px, .8fr);
        gap: 16px;
    }

    .indicator-table th,
    .indicator-table td,
    .cpo-table th,
    .cpo-table td {
        border: 1px solid #7a8796;
        vertical-align: middle;
    }

    .indicator-table th,
    .cpo-table th {
        background: #eef4ff;
        color: #1766a6;
        text-align: center;
        font-weight: 800;
        font-size: .82rem;
    }

    .indicator-table td,
    .cpo-table td {
        background: #fff;
        font-size: .84rem;
    }

    .indicator-input {
        min-width: 74px;
    }

    .condition-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
    }

    .condition-btn {
        border-radius: 8px;
        border: 1px solid #d5dfeb;
        background: #fff;
        color: #253244;
        padding: 8px 10px;
        font-size: .84rem;
        font-weight: 800;
        text-align: left;
    }

    .condition-btn.active {
        outline: 2px solid #0d6efd;
        outline-offset: 1px;
    }

    .condition-dot {
        width: 13px;
        height: 13px;
        border-radius: 4px;
        display: inline-block;
        border: 1px solid rgba(0, 0, 0, .12);
        margin-right: 6px;
        vertical-align: -2px;
    }

    @media print {
        .sidebar,
        .navbar,
        .odonto-header button,
        .patient-search-card,
        .no-print {
            display: none !important;
        }

        .content {
            margin-left: 0 !important;
        }

        body {
            background: #fff !important;
        }
    }

    @media (max-width: 991.98px) {
        .patient-info-grid,
        .indices-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .odonto-header {
            flex-direction: column;
        }

        .patient-info-item {
            grid-template-columns: 1fr;
            gap: 2px;
        }
    }
</style>

<div class="odonto-header">
    <div>
        <h3 class="odonto-title">Odontograma</h3>
        <p class="odonto-subtitle">Registro visual por paciente, superficies dentales e indices clinicos</p>
    </div>

    <div class="d-flex gap-2 no-print">
        <button type="button" class="btn btn-outline-primary" onclick="recargarOdontograma()" id="btnRecargarOdonto" disabled>
            <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
        </button>
        <button type="button" class="btn btn-primary" onclick="window.print()" id="btnImprimirOdonto" disabled>
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-3">
        <div class="card patient-search-card shadow-sm no-print">
            <div class="card-body">
                <label class="form-label fw-bold">Paciente</label>
                <div class="input-group mb-3">
                    <input type="text" id="buscarPacienteOdonto" class="form-control" placeholder="Historia, nombre o email">
                    <button class="btn btn-outline-primary" type="button" onclick="buscarPacientesOdonto()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div id="listaPacientesOdonto" class="d-grid gap-2">
                    <div class="text-center text-muted py-3">Cargando pacientes...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-9">
        <div id="estadoOdontograma" class="card odonto-card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-person-vcard fs-1 text-primary d-block mb-2"></i>
                Seleccione un paciente
            </div>
        </div>

        <div id="panelOdontograma" class="d-none">
            <div class="card patient-info-card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                        <div>
                            <h5 class="fw-bold mb-1" id="tituloPacienteOdonto">Paciente</h5>
                            <p class="text-muted mb-0" id="subtituloPacienteOdonto">Historia clinica</p>
                        </div>
                        <span class="badge bg-info-subtle text-info fs-6" id="edadPacienteOdonto">--</span>
                    </div>

                    <div class="patient-info-grid">
                        <div class="patient-info-item">
                            <span class="patient-info-label">Correo:</span>
                            <span class="patient-info-value" id="infoCorreoOdonto">--</span>
                        </div>
                        <div class="patient-info-item">
                            <span class="patient-info-label">Telefono:</span>
                            <span class="patient-info-value" id="infoTelefonoOdonto">--</span>
                        </div>
                        <div class="patient-info-item">
                            <span class="patient-info-label">Alergias:</span>
                            <span class="patient-info-value" id="infoAlergiasOdonto">--</span>
                        </div>
                        <div class="patient-info-item">
                            <span class="patient-info-label">Diabetes:</span>
                            <span class="patient-info-value" id="infoDiabetesOdonto">--</span>
                        </div>
                        <div class="patient-info-item">
                            <span class="patient-info-label">Hipertenso:</span>
                            <span class="patient-info-value" id="infoHipertensoOdonto">--</span>
                        </div>
                        <div class="patient-info-item">
                            <span class="patient-info-label">Motivo inicial:</span>
                            <span class="patient-info-value" id="infoMotivoOdonto">--</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card odonto-card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-palette text-primary me-2"></i> Leyenda clinica
                    </h5>
                    <div class="legend-strip" id="leyendaOdonto"></div>
                </div>
            </div>

            <div class="card odonto-card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-grid-3x3-gap text-primary me-2"></i> Piezas dentales
                        </h5>
                        <small class="text-muted">FDI permanente y temporal</small>
                    </div>

                    <div class="odontogram-board" id="odontogramaVisual"></div>
                </div>
            </div>

            <div class="indices-grid">
                <div class="card indicator-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-activity text-primary me-2"></i> Indicadores de salud bucal
                            </h5>
                            <button class="btn btn-sm btn-success no-print" onclick="guardarIndicadoresOdonto()">
                                <i class="bi bi-check2 me-1"></i> Guardar
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table indicator-table mb-0">
                                <thead>
                                    <tr>
                                        <th colspan="3">Higiene oral simplificada</th>
                                        <th>Enfermedad periodontal</th>
                                        <th>Mal oclusion</th>
                                        <th>Fluorosis</th>
                                    </tr>
                                    <tr>
                                        <th>Placa 0-3</th>
                                        <th>Calculo 0-3</th>
                                        <th>Gingivitis 0-1</th>
                                        <th>Severidad</th>
                                        <th>Clasificacion</th>
                                        <th>Severidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="number" id="higienePlaca" class="form-control form-control-sm indicator-input" min="0" max="3"></td>
                                        <td><input type="number" id="higieneCalculo" class="form-control form-control-sm indicator-input" min="0" max="3"></td>
                                        <td><input type="number" id="higieneGingivitis" class="form-control form-control-sm indicator-input" min="0" max="1"></td>
                                        <td>
                                            <select id="enfermedadPeriodontal" class="form-select form-select-sm">
                                                <option value="ninguna">Ninguna</option>
                                                <option value="leve">Leve</option>
                                                <option value="moderada">Moderada</option>
                                                <option value="severa">Severa</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="maloclusion" class="form-select form-select-sm">
                                                <option value="ninguna">Ninguna</option>
                                                <option value="angle_i">Angle I</option>
                                                <option value="angle_ii">Angle II</option>
                                                <option value="angle_iii">Angle III</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="fluorosis" class="form-select form-select-sm">
                                                <option value="ninguna">Ninguna</option>
                                                <option value="leve">Leve</option>
                                                <option value="moderada">Moderada</option>
                                                <option value="severa">Severa</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <textarea id="observacionesOdonto" class="form-control" rows="2" maxlength="1000" placeholder="Observaciones"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card indicator-card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-table text-primary me-2"></i> Indices CPO-ceo
                        </h5>
                        <div class="table-responsive">
                            <table class="table cpo-table mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>C</th>
                                        <th>P</th>
                                        <th>O</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>D</th>
                                        <td id="cpoC">0</td>
                                        <td id="cpoP">0</td>
                                        <td id="cpoO">0</td>
                                        <td id="cpoTotal">0</td>
                                    </tr>
                                    <tr>
                                        <th>d</th>
                                        <td id="ceoC">0</td>
                                        <td id="ceoE">0</td>
                                        <td id="ceoO">0</td>
                                        <td id="ceoTotal">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMarcaOdonto" tabindex="-1" aria-labelledby="modalMarcaOdontoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title text-info fw-bold" id="modalMarcaOdontoLabel">
                    <i class="bi bi-grid-3x3-gap me-2"></i> Diente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formMarcaOdonto">
                    <input type="hidden" id="marcaIdOdonto">
                    <input type="hidden" id="dienteMarcaOdonto">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Superficie</label>
                            <select id="superficieMarcaOdonto" class="form-select"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cita relacionada</label>
                            <select id="citaMarcaOdonto" class="form-select" onchange="sincronizarTratamientoPorCita()">
                                <option value="">Sin cita</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tratamiento</label>
                            <select id="tratamientoMarcaOdonto" class="form-select">
                                <option value="">Sin tratamiento</option>
                            </select>
                        </div>
                    </div>

                    <label class="form-label">Marca</label>
                    <div class="condition-grid mb-3" id="condicionesMarcaOdonto"></div>

                    <div>
                        <label class="form-label">Observacion</label>
                        <textarea id="observacionMarcaOdonto" class="form-control" rows="3" maxlength="1000"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger me-auto" id="btnEliminarMarcaOdonto" onclick="eliminarMarcaOdonto()">
                    <i class="bi bi-trash me-1"></i> Eliminar marca
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="guardarMarcaOdonto()">
                    <i class="bi bi-check2 me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;

const FILAS_ODONTOGRAMA = [
    { tipo: 'permanente', izquierda: [18, 17, 16, 15, 14, 13, 12, 11], derecha: [21, 22, 23, 24, 25, 26, 27, 28] },
    { tipo: 'temporal', izquierda: [55, 54, 53, 52, 51], derecha: [61, 62, 63, 64, 65] },
    { tipo: 'temporal', izquierda: [85, 84, 83, 82, 81], derecha: [71, 72, 73, 74, 75] },
    { tipo: 'permanente', izquierda: [48, 47, 46, 45, 44, 43, 42, 41], derecha: [31, 32, 33, 34, 35, 36, 37, 38] },
];

const SUPERFICIES_LABEL = {
    general: 'Diente completo',
    oclusal: 'Centro',
    vestibular: 'Arriba',
    lingual: 'Abajo',
    mesial: 'Izquierda',
    distal: 'Derecha',
};

let usuarioActual = null;
let pacientesOdonto = [];
let pacienteSeleccionado = null;
let odontogramaActual = null;
let tratamientosOdonto = [];
let condicionSeleccionada = 'cariado';
let modalMarcaOdonto = null;

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', async () => {
    await obtenerUsuarioActual();
    await cargarTratamientosOdonto();
    await buscarPacientesOdonto();

    document.getElementById('buscarPacienteOdonto').addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            buscarPacientesOdonto();
        }
    });
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

// Documentacion: Ejecuta obtener usuario actual.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function obtenerUsuarioActual() {
    try {
        const response = await fetch(`${API_URL}/auth/me`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo obtener el usuario actual');
        usuarioActual = resultado.data;
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Carga cargar tratamientos odonto.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarTratamientosOdonto() {
    try {
        const response = await fetch(`${API_URL}/tratamientos/listado/tipos`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar los tratamientos');
        tratamientosOdonto = resultado.data ?? [];
    } catch (error) {
        tratamientosOdonto = [];
    }
}

// Documentacion: Busca buscar pacientes odonto.
// Como lo hace: Actualiza filtros de la pantalla y vuelve a consultar la API.
async function buscarPacientesOdonto() {
    const termino = document.getElementById('buscarPacienteOdonto').value.trim();
    const lista = document.getElementById('listaPacientesOdonto');
    lista.innerHTML = '<div class="text-center text-muted py-3">Cargando pacientes...</div>';

    let url = `${API_URL}/pacientes`;
    if (termino) url += `?search=${encodeURIComponent(termino)}`;

    try {
        const response = await fetch(url, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar los pacientes');

        pacientesOdonto = resultado.data?.data ?? resultado.data ?? [];
        renderizarPacientesOdonto();
    } catch (error) {
        lista.innerHTML = `<div class="text-danger text-center py-3">${escaparHtml(error.message)}</div>`;
    }
}

// Documentacion: Renderiza renderizar pacientes odonto.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarPacientesOdonto() {
    const lista = document.getElementById('listaPacientesOdonto');

    if (!pacientesOdonto.length) {
        lista.innerHTML = '<div class="text-center text-muted py-3">No hay pacientes</div>';
        return;
    }

    lista.innerHTML = pacientesOdonto.map(paciente => {
        const activo = pacienteSeleccionado && Number(pacienteSeleccionado.id) === Number(paciente.id) ? 'active' : '';

        return `
            <button type="button" class="patient-option ${activo}" onclick="seleccionarPacienteOdonto(${paciente.id})">
                <strong>${escaparHtml(paciente.numero_historia)}</strong>
                ${escaparHtml(paciente.nombre)} ${escaparHtml(paciente.apellido)}
                <small>${escaparHtml(paciente.email ?? 'Sin correo')}</small>
            </button>
        `;
    }).join('');
}

// Documentacion: Ejecuta seleccionar paciente odonto.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function seleccionarPacienteOdonto(pacienteId) {
    pacienteSeleccionado = pacientesOdonto.find(p => Number(p.id) === Number(pacienteId)) ?? { id: pacienteId };
    renderizarPacientesOdonto();
    await cargarOdontogramaPaciente(pacienteId);
}

// Documentacion: Carga cargar odontograma paciente.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarOdontogramaPaciente(pacienteId) {
    document.getElementById('estadoOdontograma').classList.remove('d-none');
    document.getElementById('panelOdontograma').classList.add('d-none');

    try {
        const response = await fetch(`${API_URL}/odontogramas/paciente/${pacienteId}`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo cargar el odontograma');

        odontogramaActual = resultado.data;
        pacienteSeleccionado = odontogramaActual.paciente;
        renderizarOdontogramaCompleto();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta recargar odontograma.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function recargarOdontograma() {
    if (!pacienteSeleccionado) return;
    cargarOdontogramaPaciente(pacienteSeleccionado.id);
}

// Documentacion: Renderiza renderizar odontograma completo.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarOdontogramaCompleto() {
    document.getElementById('estadoOdontograma').classList.add('d-none');
    document.getElementById('panelOdontograma').classList.remove('d-none');
    document.getElementById('btnRecargarOdonto').disabled = false;
    document.getElementById('btnImprimirOdonto').disabled = false;

    actualizarDatosPaciente();
    renderizarLeyenda();
    renderizarOdontogramaVisual();
    renderizarIndicadores();
    renderizarIndices();
}

// Documentacion: Actualiza actualizar datos paciente.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarDatosPaciente() {
    const paciente = odontogramaActual.paciente;
    const antecedentes = paciente.antecedentes ?? {};

    document.getElementById('tituloPacienteOdonto').textContent = `${paciente.nombre} ${paciente.apellido}`;
    document.getElementById('subtituloPacienteOdonto').textContent = `Historia clinica: ${paciente.numero_historia}`;
    document.getElementById('edadPacienteOdonto').textContent = calcularEdad(paciente.fecha_nacimiento);
    document.getElementById('infoCorreoOdonto').textContent = paciente.email ?? 'Sin correo';
    document.getElementById('infoTelefonoOdonto').textContent = paciente.telefono ?? 'Sin telefono';
    document.getElementById('infoAlergiasOdonto').textContent = antecedentes.alergias_medicamentos ? (antecedentes.detalle_alergias || 'Si') : 'No';
    document.getElementById('infoDiabetesOdonto').textContent = antecedentes.diabetes ? 'Si' : 'No';
    document.getElementById('infoHipertensoOdonto').textContent = antecedentes.hipertenso ? 'Si' : 'No';
    document.getElementById('infoMotivoOdonto').textContent = antecedentes.motivo_consulta_inicial ?? 'Sin motivo registrado';
}

// Documentacion: Renderiza renderizar leyenda.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarLeyenda() {
    const condiciones = odontogramaActual.catalogos.condiciones;
    const orden = ordenarCondiciones(condiciones);

    document.getElementById('leyendaOdonto').innerHTML = orden.map(clave => {
        const item = condiciones[clave];
        if (!item) return '';

        return `
            <div class="legend-pill">
                <span class="legend-color" style="background:${item.color};"></span>
                <span>${escaparHtml(item.label)}</span>
            </div>
        `;
    }).join('');
}

// Documentacion: Renderiza renderizar odontograma visual.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarOdontogramaVisual() {
    const contenedor = document.getElementById('odontogramaVisual');
    contenedor.innerHTML = FILAS_ODONTOGRAMA.map(fila => `
        <div class="odontogram-row">
            <div class="quadrant ${fila.tipo === 'temporal' ? 'primary' : ''}">
                ${fila.izquierda.map(numero => renderizarDiente(numero, fila.tipo)).join('')}
            </div>
            <div class="quadrant right ${fila.tipo === 'temporal' ? 'primary' : ''}">
                ${fila.derecha.map(numero => renderizarDiente(numero, fila.tipo)).join('')}
            </div>
        </div>
    `).join('');
}

// Documentacion: Renderiza renderizar diente.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarDiente(numero, tipo) {
    const marcas = obtenerMarcasDiente(numero);
    const general = marcas.find(m => m.superficie === 'general');
    const tieneX = general && ['extraccion_indicada', 'perdido'].includes(general.condicion);
    const tieneFractura = marcas.some(m => m.condicion === 'fractura');
    const miniLabel = general ? general.condicion_label : (marcas.length ? `${marcas.length} marca${marcas.length === 1 ? '' : 's'}` : '');

    return `
        <div class="tooth-card">
            <div class="tooth-number">${numero}</div>
            <svg class="tooth-svg ${tipo === 'temporal' ? 'primary-tooth' : ''}" viewBox="0 0 50 50" role="img" aria-label="Diente ${numero}">
                <polygon class="tooth-surface" points="7,7 43,7 35,15 15,15" fill="${colorSuperficie(numero, 'vestibular')}" onclick="abrirModalMarca(${numero}, 'vestibular')"></polygon>
                <polygon class="tooth-surface" points="43,7 43,43 35,35 35,15" fill="${colorSuperficie(numero, 'distal')}" onclick="abrirModalMarca(${numero}, 'distal')"></polygon>
                <polygon class="tooth-surface" points="43,43 7,43 15,35 35,35" fill="${colorSuperficie(numero, 'lingual')}" onclick="abrirModalMarca(${numero}, 'lingual')"></polygon>
                <polygon class="tooth-surface" points="7,43 7,7 15,15 15,35" fill="${colorSuperficie(numero, 'mesial')}" onclick="abrirModalMarca(${numero}, 'mesial')"></polygon>
                <rect class="tooth-surface" x="15" y="15" width="20" height="20" rx="3" fill="${colorSuperficie(numero, 'oclusal')}" onclick="abrirModalMarca(${numero}, 'oclusal')"></rect>
                ${colorSuperficie(numero, 'general') !== '#ffffff' && !tieneX ? `<circle cx="25" cy="25" r="22" fill="none" stroke="${colorSuperficie(numero, 'general')}" stroke-width="4" onclick="abrirModalMarca(${numero}, 'general')"></circle>` : ''}
                ${tieneX ? '<line class="tooth-x" x1="8" y1="8" x2="42" y2="42"></line><line class="tooth-x" x1="42" y1="8" x2="8" y2="42"></line>' : ''}
                ${tieneFractura ? '<line class="tooth-fracture" x1="12" y1="5" x2="38" y2="45"></line>' : ''}
            </svg>
            <button type="button" class="btn btn-link btn-sm p-0 tooth-mini-label" onclick="abrirModalMarca(${numero}, 'general')">
                ${escaparHtml(miniLabel)}
            </button>
        </div>
    `;
}

// Documentacion: Ejecuta obtener marcas diente.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function obtenerMarcasDiente(numero) {
    return (odontogramaActual.marcas ?? []).filter(m => Number(m.numero_diente) === Number(numero));
}

// Documentacion: Ejecuta obtener marca.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function obtenerMarca(numero, superficie) {
    return obtenerMarcasDiente(numero).find(m => m.superficie === superficie) ?? null;
}

// Documentacion: Ejecuta color superficie.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function colorSuperficie(numero, superficie) {
    const marca = obtenerMarca(numero, superficie);
    if (marca) return marca.color;

    const general = obtenerMarca(numero, 'general');
    if (general && !['extraccion_indicada', 'perdido'].includes(general.condicion)) {
        return general.color;
    }

    return '#ffffff';
}

// Documentacion: Abre abrir modal marca.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalMarca(numero, superficie) {
    if (!odontogramaActual) return;

    const marca = obtenerMarca(numero, superficie);
    condicionSeleccionada = marca?.condicion ?? 'cariado';

    document.getElementById('modalMarcaOdontoLabel').innerHTML =
        `<i class="bi bi-grid-3x3-gap me-2"></i> Diente N° ${numero} - Lado: ${SUPERFICIES_LABEL[superficie]}`;
    document.getElementById('marcaIdOdonto').value = marca?.id ?? '';
    document.getElementById('dienteMarcaOdonto').value = numero;
    document.getElementById('observacionMarcaOdonto').value = marca?.observacion ?? '';
    document.getElementById('btnEliminarMarcaOdonto').classList.toggle('d-none', !marca);

    llenarSelectSuperficies(superficie);
    llenarSelectCitas(marca);
    llenarSelectTratamientos(marca?.tipo_tratamiento_id ?? '');
    renderizarCondicionesModal();

    modalMarcaOdonto = new bootstrap.Modal(document.getElementById('modalMarcaOdonto'));
    modalMarcaOdonto.show();
}

// Documentacion: Ejecuta llenar select superficies.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function llenarSelectSuperficies(superficieActual) {
    const select = document.getElementById('superficieMarcaOdonto');
    select.innerHTML = Object.entries(SUPERFICIES_LABEL).map(([valor, label]) => `
        <option value="${valor}" ${valor === superficieActual ? 'selected' : ''}>${label}</option>
    `).join('');
}

// Documentacion: Ejecuta llenar select citas.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function llenarSelectCitas(marca) {
    const select = document.getElementById('citaMarcaOdonto');
    const citas = odontogramaActual.citas ?? [];

    select.innerHTML = '<option value="">Sin cita</option>' + citas.map(cita => `
        <option value="${cita.id}" data-tipo="${cita.tipo_tratamiento_id ?? ''}" ${Number(marca?.cita_id) === Number(cita.id) ? 'selected' : ''}>
            ${formatearFecha(cita.fecha)} ${cita.hora ?? ''} - ${escaparHtml(cita.tratamiento ?? cita.motivo ?? 'Consulta')}
        </option>
    `).join('');
}

// Documentacion: Ejecuta llenar select tratamientos.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function llenarSelectTratamientos(valorActual) {
    const select = document.getElementById('tratamientoMarcaOdonto');
    select.innerHTML = '<option value="">Sin tratamiento</option>' + tratamientosOdonto.map(tipo => `
        <option value="${tipo.id}" ${Number(valorActual) === Number(tipo.id) ? 'selected' : ''}>
            ${escaparHtml(tipo.nombre)} - ${formatearPrecio(tipo.precio)}
        </option>
    `).join('');
}

// Documentacion: Ejecuta sincronizar tratamiento por cita.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function sincronizarTratamientoPorCita() {
    const option = document.getElementById('citaMarcaOdonto').selectedOptions[0];
    const tipo = option?.dataset?.tipo;
    if (tipo) document.getElementById('tratamientoMarcaOdonto').value = tipo;
}

// Documentacion: Renderiza renderizar condiciones modal.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarCondicionesModal() {
    const condiciones = odontogramaActual.catalogos.condiciones;
    const orden = ordenarCondiciones(condiciones);

    document.getElementById('condicionesMarcaOdonto').innerHTML = orden.map(clave => {
        const item = condiciones[clave];
        if (!item) return '';
        const active = condicionSeleccionada === clave ? 'active' : '';

        return `
            <button type="button" class="condition-btn ${active}" onclick="seleccionarCondicionOdonto('${clave}')">
                <span class="condition-dot" style="background:${item.color};"></span>${escaparHtml(item.label)}
            </button>
        `;
    }).join('');
}

// Documentacion: Ejecuta ordenar condiciones.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function ordenarCondiciones(condiciones) {
    const base = ['cariado', 'obturado', 'extraccion_indicada', 'perdido', 'endodoncia', 'corona', 'puente', 'implante', 'sellante', 'fractura', 'tratamiento_indicado', 'sano'];
    const extras = Object.keys(condiciones).filter(clave => !base.includes(clave));
    return [...base.filter(clave => condiciones[clave]), ...extras];
}

// Documentacion: Ejecuta seleccionar condicion odonto.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function seleccionarCondicionOdonto(condicion) {
    condicionSeleccionada = condicion;
    renderizarCondicionesModal();
}

// Documentacion: Guarda guardar marca odonto.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarMarcaOdonto() {
    if (!pacienteSeleccionado || !usuarioActual) return;

    const datos = {
        numero_diente: Number(document.getElementById('dienteMarcaOdonto').value),
        superficie: document.getElementById('superficieMarcaOdonto').value,
        condicion: condicionSeleccionada,
        cita_id: document.getElementById('citaMarcaOdonto').value || null,
        tipo_tratamiento_id: document.getElementById('tratamientoMarcaOdonto').value || null,
        usuario_id: usuarioActual.id,
        observacion: document.getElementById('observacionMarcaOdonto').value.trim() || null,
    };

    try {
        const response = await fetch(`${API_URL}/odontogramas/paciente/${pacienteSeleccionado.id}/marcas`, {
            method: 'POST',
            headers: headersApi(true),
            body: JSON.stringify(datos)
        });
        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo guardar la marca';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            return;
        }

        odontogramaActual = resultado.data;
        modalMarcaOdonto.hide();
        renderizarOdontogramaCompleto();
        alertaExito('Odontograma actualizado');
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Elimina eliminar marca odonto.
// Como lo hace: Confirma la accion, llama la API y actualiza el listado para reflejar el cambio.
async function eliminarMarcaOdonto() {
    const marcaId = document.getElementById('marcaIdOdonto').value;
    if (!marcaId) return;

    try {
        const response = await fetch(`${API_URL}/odontogramas/marcas/${marcaId}`, {
            method: 'DELETE',
            headers: headersApi()
        });
        const resultado = await response.json();

        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo eliminar la marca');

        odontogramaActual = resultado.data;
        modalMarcaOdonto.hide();
        renderizarOdontogramaCompleto();
        alertaExito('Marca eliminada');
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Renderiza renderizar indicadores.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarIndicadores() {
    const odonto = odontogramaActual.odontograma;
    document.getElementById('higienePlaca').value = odonto.higiene_placa ?? '';
    document.getElementById('higieneCalculo').value = odonto.higiene_calculo ?? '';
    document.getElementById('higieneGingivitis').value = odonto.higiene_gingivitis ?? '';
    document.getElementById('enfermedadPeriodontal').value = odonto.enfermedad_periodontal ?? 'ninguna';
    document.getElementById('maloclusion').value = odonto.maloclusion ?? 'ninguna';
    document.getElementById('fluorosis').value = odonto.fluorosis ?? 'ninguna';
    document.getElementById('observacionesOdonto').value = odonto.observaciones ?? '';
}

// Documentacion: Guarda guardar indicadores odonto.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarIndicadoresOdonto() {
    if (!pacienteSeleccionado || !usuarioActual) return;

    const datos = {
        usuario_id: usuarioActual.id,
        higiene_placa: valorEnteroNullable('higienePlaca'),
        higiene_calculo: valorEnteroNullable('higieneCalculo'),
        higiene_gingivitis: valorEnteroNullable('higieneGingivitis'),
        enfermedad_periodontal: document.getElementById('enfermedadPeriodontal').value,
        maloclusion: document.getElementById('maloclusion').value,
        fluorosis: document.getElementById('fluorosis').value,
        observaciones: document.getElementById('observacionesOdonto').value.trim() || null,
    };

    try {
        const response = await fetch(`${API_URL}/odontogramas/paciente/${pacienteSeleccionado.id}/indicadores`, {
            method: 'PUT',
            headers: headersApi(true),
            body: JSON.stringify(datos)
        });
        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudieron guardar los indicadores';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            return;
        }

        odontogramaActual = resultado.data;
        renderizarOdontogramaCompleto();
        alertaExito('Indicadores actualizados');
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Renderiza renderizar indices.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarIndices() {
    const indices = odontogramaActual.indices;
    document.getElementById('cpoC').textContent = indices.cpo.cariados;
    document.getElementById('cpoP').textContent = indices.cpo.perdidos;
    document.getElementById('cpoO').textContent = indices.cpo.obturados;
    document.getElementById('cpoTotal').textContent = indices.cpo.total;
    document.getElementById('ceoC').textContent = indices.ceo.cariados;
    document.getElementById('ceoE').textContent = indices.ceo.extraidos;
    document.getElementById('ceoO').textContent = indices.ceo.obturados;
    document.getElementById('ceoTotal').textContent = indices.ceo.total;
}

// Documentacion: Ejecuta valor entero nullable.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function valorEnteroNullable(id) {
    const valor = document.getElementById(id).value;
    return valor === '' ? null : Number(valor);
}

// Documentacion: Calcula calcular edad.
// Como lo hace: Toma valores numericos de la pantalla y deriva totales, saldos o vuelto.
function calcularEdad(fechaNacimiento) {
    if (!fechaNacimiento) return 'Sin edad';
    const nacimiento = new Date(fechaNacimiento);
    const hoy = new Date();
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) edad--;
    return `${edad} años`;
}

// Documentacion: Formatea formatear fecha.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const [anio, mes, dia] = fecha.split('-');
    return `${dia}/${mes}/${anio}`;
}

// Documentacion: Formatea valores monetarios en dolares.
// Como lo hace: Usa Intl.NumberFormat con moneda USD.
function formatearPrecio(valor) {
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(Number(valor ?? 0));
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
