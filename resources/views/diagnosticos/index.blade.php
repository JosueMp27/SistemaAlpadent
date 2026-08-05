{{--
    Documentacion de archivo:
    Vista Blade del modulo diagnosticos; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')
<style>
    .diagnostics-header {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .diagnostics-shell {
        display: grid;
        grid-template-columns: 330px minmax(0, 1fr);
        gap: 1.25rem;
        align-items: start;
    }

    .panel {
        background: #fff;
        border: 1px solid #e7ebef;
        border-radius: .5rem;
        box-shadow: 0 .25rem 1rem rgba(15, 23, 42, .05);
    }

    .panel-header {
        padding: 1rem;
        border-bottom: 1px solid #edf0f2;
        font-weight: 700;
    }

    .panel-body {
        padding: 1rem;
    }

    .patient-list {
        display: grid;
        gap: .5rem;
        max-height: 530px;
        overflow-y: auto;
    }

    .patient-option {
        width: 100%;
        text-align: left;
        border: 1px solid #e7ebef;
        background: #fff;
        border-radius: .4rem;
        padding: .7rem .8rem;
        transition: border-color .15s ease, background-color .15s ease;
    }

    .patient-option:hover,
    .patient-option.active {
        border-color: #0d6efd;
        background: #f4f8ff;
    }

    .patient-option strong,
    .patient-option small {
        display: block;
    }

    .patient-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .summary-box {
        border: 1px solid #d9e3ee;
        border-radius: .4rem;
        padding: .7rem .8rem;
        background: #fff;
    }

    .summary-label {
        color: #667085;
        font-size: .8rem;
        margin-bottom: .2rem;
    }

    .summary-value {
        font-weight: 800;
        color: #12355b;
    }

    .ledger-paper {
        background: #efe883;
        border: 1px solid #cfc75f;
        border-radius: .35rem;
        padding: 1rem;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .22);
    }

    .ledger-title {
        text-align: center;
        color: #276078;
        font-weight: 900;
        letter-spacing: .35rem;
        margin-bottom: .35rem;
    }

    .ledger-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        color: #214f63;
        background: rgba(255, 255, 255, .04);
    }

    .ledger-table th,
    .ledger-table td {
        border: 2px solid #2d6f80;
        padding: .45rem .5rem;
        vertical-align: top;
    }

    .ledger-table th {
        text-align: center;
        font-weight: 900;
        font-size: 1.05rem;
        letter-spacing: .06rem;
    }

    .ledger-table td {
        min-height: 42px;
        height: 42px;
        font-weight: 600;
    }

    .ledger-date {
        width: 14%;
        text-align: center;
    }

    .ledger-treatment {
        width: 48%;
    }

    .ledger-money {
        width: 12.66%;
        text-align: right;
        white-space: nowrap;
    }

    .treatment-detail {
        display: block;
        color: #3f6673;
        font-size: .78rem;
        font-weight: 500;
        margin-top: .15rem;
        line-height: 1.25;
    }

    .empty-state {
        color: #667085;
        text-align: center;
        padding: 2.5rem 1rem;
    }

    @media (max-width: 1100px) {
        .diagnostics-shell {
            grid-template-columns: 1fr;
        }

        .patient-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="diagnostics-header">
    <div>
        <h3 class="mb-1 fw-bold">Diagnosticos</h3>
        <p class="text-muted mb-0">Historial visual de citas, tratamientos y presupuesto por paciente</p>
    </div>
</div>

<div class="diagnostics-shell">
    <div class="panel">
        <div class="panel-header">
            <i class="bi bi-search me-2"></i>Buscar paciente
        </div>
        <div class="panel-body">
            <div class="input-group mb-3">
                <input type="text" id="buscarPacienteDiagnostico" class="form-control"
                    placeholder="Nombre, email o historia"
                    onkeyup="if(event.key === 'Enter') buscarPacientesDiagnostico();">
                <button class="btn btn-outline-primary" onclick="buscarPacientesDiagnostico()">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <div id="listaPacientesDiagnostico" class="patient-list">
                <div class="text-muted small">Cargando pacientes...</div>
            </div>
        </div>
    </div>

    <div>
        <div class="patient-summary" id="resumenPacienteDiagnostico">
            <div class="summary-box">
                <div class="summary-label">Paciente</div>
                <div class="summary-value">Seleccione</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">Historia</div>
                <div class="summary-value">N/A</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">Citas</div>
                <div class="summary-value">0</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">Saldo</div>
                <div class="summary-value">$0.00</div>
            </div>
        </div>

        <div class="ledger-paper">
            <div class="ledger-title">CITAS</div>
            <div class="table-responsive">
                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th class="ledger-date" rowspan="2">FECHA</th>
                            <th class="ledger-treatment" rowspan="2">TRATAMIENTO</th>
                            <th colspan="3">Presupuesto</th>
                        </tr>
                        <tr>
                            <th class="ledger-money">Costo</th>
                            <th class="ledger-money">Abono</th>
                            <th class="ledger-money">Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaHistorialDiagnostico">
                        <tr>
                            <td colspan="5" class="empty-state">Seleccione un paciente para ver su historial.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;
let pacienteDiagnosticoSeleccionado = null;
let pacientesDiagnostico = [];

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', () => {
    buscarPacientesDiagnostico();
});

// Documentacion: Busca buscar pacientes diagnostico.
// Como lo hace: Actualiza filtros de la pantalla y vuelve a consultar la API.
async function buscarPacientesDiagnostico() {
    const token = localStorage.getItem('token');
    const termino = document.getElementById('buscarPacienteDiagnostico').value.trim();
    const lista = document.getElementById('listaPacientesDiagnostico');

    lista.innerHTML = '<div class="text-muted small">Buscando pacientes...</div>';

    let url = `${API_URL}/pacientes`;
    if (termino) url += `?search=${encodeURIComponent(termino)}`;

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
            throw new Error(resultado.message || 'No se pudieron cargar los pacientes');
        }

        pacientesDiagnostico = resultado.data?.data ?? resultado.data ?? [];
        renderizarPacientesDiagnostico();
    } catch (error) {
        lista.innerHTML = `<div class="text-danger small">${error.message}</div>`;
    }
}

// Documentacion: Renderiza renderizar pacientes diagnostico.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarPacientesDiagnostico() {
    const lista = document.getElementById('listaPacientesDiagnostico');

    if (!pacientesDiagnostico.length) {
        lista.innerHTML = '<div class="text-muted small">No se encontraron pacientes.</div>';
        return;
    }

    lista.innerHTML = '';

    pacientesDiagnostico.forEach(paciente => {
        const activo = pacienteDiagnosticoSeleccionado && pacienteDiagnosticoSeleccionado.id === paciente.id ? 'active' : '';
        lista.innerHTML += `
            <button type="button" class="patient-option ${activo}" onclick="seleccionarPacienteDiagnostico(${paciente.id})">
                <strong>${paciente.nombre} ${paciente.apellido}</strong>
                <small>${paciente.numero_historia ?? 'Sin historia'} | ${paciente.email ?? 'Sin email'}</small>
            </button>
        `;
    });
}

// Documentacion: Ejecuta seleccionar paciente diagnostico.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function seleccionarPacienteDiagnostico(pacienteId) {
    pacienteDiagnosticoSeleccionado = pacientesDiagnostico.find(p => Number(p.id) === Number(pacienteId)) ?? null;
    renderizarPacientesDiagnostico();
    await cargarHistorialDiagnostico(pacienteId);
}

// Documentacion: Carga cargar historial diagnostico.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarHistorialDiagnostico(pacienteId) {
    const token = localStorage.getItem('token');
    const tbody = document.getElementById('tablaHistorialDiagnostico');

    tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Cargando historial...</td></tr>';

    try {
        const response = await fetch(`${API_URL}/diagnosticos/paciente/${pacienteId}/historial`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo cargar el historial');
        }

        actualizarResumenDiagnostico(resultado.data);
        renderizarHistorialDiagnostico(resultado.data.registros ?? []);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="5" class="empty-state text-danger">${error.message}</td></tr>`;
    }
}

// Documentacion: Actualiza actualizar resumen diagnostico.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarResumenDiagnostico(data) {
    const paciente = data.paciente;
    const resumen = data.resumen;
    const contenedor = document.getElementById('resumenPacienteDiagnostico');

    contenedor.innerHTML = `
        <div class="summary-box">
            <div class="summary-label">Paciente</div>
            <div class="summary-value">${paciente.nombre} ${paciente.apellido}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Historia</div>
            <div class="summary-value">${paciente.numero_historia ?? 'N/A'}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Citas</div>
            <div class="summary-value">${resumen.total_citas ?? 0}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Saldo</div>
            <div class="summary-value">${formatearDinero(resumen.total_saldo ?? 0)}</div>
        </div>
    `;
}

// Documentacion: Renderiza renderizar historial diagnostico.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarHistorialDiagnostico(registros) {
    const tbody = document.getElementById('tablaHistorialDiagnostico');
    tbody.innerHTML = '';

    if (!registros.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Este paciente aun no tiene citas registradas.</td></tr>';
        agregarFilasVacias(18);
        return;
    }

    registros.forEach(registro => {
        tbody.innerHTML += `
            <tr>
                <td class="ledger-date">${formatearFecha(registro.fecha)}<span class="treatment-detail">${registro.hora ?? ''}</span></td>
                <td class="ledger-treatment">
                    ${registro.tratamiento}
                    ${registro.diagnostico ? `<span class="treatment-detail">${registro.diagnostico}</span>` : ''}
                    <span class="treatment-detail">${formatearEstado(registro.estado)}</span>
                </td>
                <td class="ledger-money">${formatearDinero(registro.costo)}</td>
                <td class="ledger-money">${formatearDinero(registro.abono)}</td>
                <td class="ledger-money">${formatearDinero(registro.saldo)}</td>
            </tr>
        `;
    });

    agregarFilasVacias(Math.max(0, 18 - registros.length));
}

// Documentacion: Ejecuta agregar filas vacias.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function agregarFilasVacias(total) {
    const tbody = document.getElementById('tablaHistorialDiagnostico');

    for (let i = 0; i < total; i++) {
        tbody.innerHTML += `
            <tr>
                <td class="ledger-date">&nbsp;</td>
                <td class="ledger-treatment">&nbsp;</td>
                <td class="ledger-money">&nbsp;</td>
                <td class="ledger-money">&nbsp;</td>
                <td class="ledger-money">&nbsp;</td>
            </tr>
        `;
    }
}

// Documentacion: Formatea formatear fecha.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const [anio, mes, dia] = fecha.split('-');
    return `${dia}/${mes}/${anio}`;
}

// Documentacion: Formatea formatear dinero.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearDinero(valor) {
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(Number(valor ?? 0));
}

// Documentacion: Formatea formatear estado.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearEstado(estado) {
    const estados = {
        programada: 'Programada',
        en_curso: 'En curso',
        completada: 'Completada',
        cancelada: 'Cancelada',
        no_asistio: 'No asistio',
    };

    return estados[estado] ?? 'Sin estado';
}
</script>
@endsection
