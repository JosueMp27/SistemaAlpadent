{{--
    Documentacion de archivo:
    Vista Blade del dashboard principal; muestra tarjetas de resumen y el calendario interactivo de citas.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
    }

    .dashboard-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: 4px;
    }

    .dashboard-subtitle {
        color: #6c7789;
        margin-bottom: 0;
    }

    .stat-card {
        border: 0;
        border-radius: 8px;
        color: #172033;
        min-height: 142px;
        overflow: hidden;
        position: relative;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        right: -42px;
        top: -42px;
        background: rgba(255, 255, 255, 0.42);
    }

    .stat-card .card-body {
        position: relative;
        z-index: 1;
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        background: rgba(255, 255, 255, 0.72);
        color: #172033;
        box-shadow: 0 10px 24px rgba(39, 60, 91, 0.1);
    }

    .stat-label {
        font-size: .86rem;
        color: #516075;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 2rem;
        line-height: 1;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .stat-note {
        font-size: .78rem;
        color: #6d7788;
        margin-bottom: 0;
    }

    .stat-pacientes {
        background: linear-gradient(135deg, #dff7ea 0%, #edfdf5 100%);
    }

    .stat-citas {
        background: linear-gradient(135deg, #dcedff 0%, #f2f8ff 100%);
    }

    .stat-ingresos {
        background: linear-gradient(135deg, #fff0cf 0%, #fff9eb 100%);
    }

    .stat-stock {
        background: linear-gradient(135deg, #ffe2e2 0%, #fff4f4 100%);
    }

    .calendar-card {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .calendar-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 14px;
        border-radius: 8px;
        background: #f7fafe;
        border: 1px solid #e6eef8;
    }

    .calendar-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: 0;
        text-transform: capitalize;
    }

    .view-switch .btn {
        min-width: 76px;
        font-weight: 600;
    }

    .calendar-legend {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        color: #5d6878;
        font-size: .86rem;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .legend-swatch {
        width: 14px;
        height: 14px;
        border-radius: 4px;
        display: inline-block;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .legend-scheduled {
        background: #d7ebff;
    }

    .legend-available {
        background: #dcf8e8;
    }

    .calendar-weekdays,
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .calendar-weekdays {
        gap: 8px;
        margin-top: 18px;
        margin-bottom: 8px;
    }

    .weekday {
        font-size: .77rem;
        font-weight: 800;
        color: #6d7788;
        text-transform: uppercase;
        letter-spacing: .02em;
        text-align: center;
    }

    .calendar-grid {
        gap: 8px;
    }

    .calendar-day {
        min-height: 126px;
        border-radius: 8px;
        border: 1px solid #e4ebf4;
        background: #fff;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 7px;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }

    .calendar-day:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(33, 47, 74, 0.1);
        border-color: #b6d6f8;
    }

    .calendar-day.is-empty {
        background: transparent;
        border: 0;
        cursor: default;
        box-shadow: none;
    }

    .calendar-day.is-available {
        background: #effcf4;
        border-color: #bcebd0;
    }

    .calendar-day.has-appointments {
        background: #eef7ff;
        border-color: #badcff;
    }

    .calendar-day.is-past {
        background: #f8fafc;
        color: #8a94a6;
    }

    .calendar-day.is-today {
        outline: 2px solid #0d6efd;
        outline-offset: 1px;
    }

    .day-number {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #1f2b3d;
        background: rgba(255, 255, 255, 0.72);
    }

    .appointment-chip {
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        border-radius: 7px;
        background: #d7ebff;
        color: #0a4f91;
        font-size: .78rem;
        font-weight: 700;
        line-height: 1.2;
        padding: 6px 7px;
        min-width: 0;
    }

    .appointment-chip span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .availability-note {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #137347;
        font-size: .78rem;
        font-weight: 700;
        margin-top: auto;
    }

    .day-view-shell {
        margin-top: 18px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 16px;
    }

    .day-timeline {
        border-radius: 8px;
        border: 1px solid #bcebd0;
        background: #effcf4;
        padding: 16px;
        min-height: 360px;
    }

    .day-appointment {
        display: grid;
        grid-template-columns: 78px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        border-radius: 8px;
        border: 1px solid #badcff;
        background: #d7ebff;
        padding: 12px;
        margin-bottom: 10px;
    }

    .day-appointment-time {
        font-weight: 800;
        color: #0a4f91;
    }

    .day-appointment-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: 2px;
    }

    .day-appointment-meta {
        color: #516075;
        font-size: .86rem;
        margin-bottom: 0;
    }

    .available-panel {
        border-radius: 8px;
        background: #f6fff9;
        border: 1px solid #bcebd0;
        padding: 14px;
        color: #137347;
    }

    .available-panel h6 {
        font-weight: 800;
        margin-bottom: 6px;
        color: #137347;
    }

    .year-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .month-card {
        min-height: 128px;
        border-radius: 8px;
        border: 1px solid #bcebd0;
        background: #effcf4;
        padding: 14px;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .month-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(33, 47, 74, 0.1);
    }

    .month-card.has-appointments {
        background: #eef7ff;
        border-color: #badcff;
    }

    .month-name {
        font-weight: 800;
        color: #172033;
        text-transform: capitalize;
        margin-bottom: 8px;
    }

    .month-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 7px;
        padding: 6px 8px;
        font-size: .82rem;
        font-weight: 700;
    }

    .month-status.available {
        color: #137347;
        background: #dcf8e8;
    }

    .month-status.scheduled {
        color: #0a4f91;
        background: #d7ebff;
    }

    .calendar-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 260px;
        color: #617086;
    }

    .calendar-empty i {
        font-size: 2.4rem;
        color: #1fa463;
        margin-bottom: 10px;
    }

    @media (max-width: 991.98px) {
        .day-view-shell {
            grid-template-columns: 1fr;
        }

        .year-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-header {
            flex-direction: column;
        }

        .calendar-weekdays {
            display: none;
        }

        .calendar-grid {
            grid-template-columns: 1fr;
        }

        .calendar-day.is-empty {
            display: none;
        }

        .calendar-day {
            min-height: auto;
        }

        .year-grid {
            grid-template-columns: 1fr;
        }

        .view-switch {
            width: 100%;
        }

        .view-switch .btn {
            flex: 1;
        }
    }
</style>

<div class="dashboard-header">
    <div>
        <h3 class="dashboard-title">Dashboard</h3>
        <p class="dashboard-subtitle">Vista rapida del consultorio y agenda programada</p>
    </div>

    <button type="button" class="btn btn-outline-primary" onclick="cargarDashboardCompleto()">
        <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card stat-pacientes shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label">Pacientes activos</p>
                        <h3 class="stat-value" id="cardPacientes">--</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-people"></i></span>
                </div>
                <p class="stat-note">Registros activos en la base de datos</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card stat-citas shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label">Citas hoy</p>
                        <h3 class="stat-value" id="cardCitasHoy">--</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-calendar2-check"></i></span>
                </div>
                <p class="stat-note"><span id="cardCitasProgramadas">--</span> citas programadas en total</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card stat-ingresos shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label">Ingresos del mes</p>
                        <h3 class="stat-value" id="cardIngresos">$0.00</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-cash-stack"></i></span>
                </div>
                <p class="stat-note">Suma de pagos registrados este mes</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card stat-stock shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label">Stock bajo</p>
                        <h3 class="stat-value" id="cardStockBajo">--</h3>
                    </div>
                    <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
                </div>
                <p class="stat-note">Productos activos bajo el minimo</p>
            </div>
        </div>
    </div>
</div>

<div class="card calendar-card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-calendar3 text-primary me-2"></i> Calendario de citas
                </h5>
                <p class="text-muted mb-0">Consulta solo las citas programadas y los espacios sin citas registradas.</p>
            </div>

            <div class="calendar-legend">
                <span class="legend-item">
                    <span class="legend-swatch legend-scheduled"></span>
                    <i class="bi bi-calendar-event text-primary"></i> Citas programadas
                </span>
                <span class="legend-item">
                    <span class="legend-swatch legend-available"></span>
                    <i class="bi bi-check2-circle text-success"></i> Citas disponibles
                </span>
            </div>
        </div>

        <div class="calendar-toolbar">
            <div class="btn-group view-switch" role="group" aria-label="Cambiar vista del calendario">
                <button type="button" class="btn btn-outline-primary" id="btnVistaDia" onclick="cambiarVistaCalendario('dia')">
                    <i class="bi bi-calendar-day me-1"></i> Dia
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnVistaMes" onclick="cambiarVistaCalendario('mes')">
                    <i class="bi bi-calendar-month me-1"></i> Mes
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnVistaAnio" onclick="cambiarVistaCalendario('anio')">
                    <i class="bi bi-calendar4-range me-1"></i> A&ntilde;o
                </button>
            </div>

            <h5 class="calendar-title" id="tituloCalendario">Cargando...</h5>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="moverCalendario(-1)" title="Periodo anterior">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="irAHoy()">
                    Hoy
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="moverCalendario(1)" title="Periodo siguiente">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <div id="contenedorCalendario">
            <div class="calendar-empty">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p class="mb-0">Cargando calendario...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Documentacion general del dashboard:
// Este bloque JavaScript no usa una libreria de calendario externa; arma el calendario con HTML generado desde datos de la API.
// La API entrega citas programadas dentro del rango solicitado y esta vista decide si las muestra por dia, por mes o por anio.
const API_URL = `${window.location.origin}/api/v1`;

// El calendario se limita a 2026 porque el backend tambien normaliza fechas anteriores a ese anio.
const ANIO_MINIMO = 2026;

// Nombres usados para pintar titulos y tarjetas sin depender de traducciones externas.
const nombresMes = [
    'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
];
const diasSemana = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];

// Estado principal del calendario:
// vistaCalendario controla el tipo de render, fechaCalendario es el periodo visible y citasCalendario guarda la respuesta de la API.
let vistaCalendario = 'mes';
let fechaCalendario = normalizarFechaMinima(new Date());
let citasCalendario = [];

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', () => {
    cargarDashboardCompleto();
});

// Documentacion: Carga todas las piezas del dashboard.
// Como lo hace: Ejecuta en paralelo el resumen y el calendario para refrescar tarjetas y agenda.
async function cargarDashboardCompleto() {
    await Promise.all([
        cargarResumenDashboard(),
        cargarCalendario()
    ]);
}

// Documentacion: Construye los encabezados para llamar la API.
// Como lo hace: Incluye Accept JSON y el token Bearer guardado en localStorage.
function headersApi() {
    return {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    };
}

// Documentacion: Obtiene datos JSON desde un endpoint.
// Como lo hace: Hace fetch, valida success y lanza error cuando el backend responde fallo.
async function obtenerDatosApi(url) {
    const response = await fetch(url, {
        method: 'GET',
        headers: headersApi()
    });

    const resultado = await response.json();
    if (!response.ok || !resultado.success) {
        throw new Error(resultado.message || 'No se pudo obtener la informacion');
    }

    return resultado.data;
}

// Documentacion: Carga las tarjetas superiores del dashboard.
// Como lo hace: Consulta el resumen y escribe pacientes, citas, ingresos y stock bajo en el DOM.
async function cargarResumenDashboard() {
    try {
        const data = await obtenerDatosApi(`${API_URL}/dashboard/resumen`);
        document.getElementById('cardPacientes').textContent = formatearNumero(data.pacientes_activos);
        document.getElementById('cardCitasHoy').textContent = formatearNumero(data.citas_hoy);
        document.getElementById('cardCitasProgramadas').textContent = formatearNumero(data.citas_programadas);
        document.getElementById('cardIngresos').textContent = formatearPrecio(data.ingresos_mes);
        document.getElementById('cardStockBajo').textContent = formatearNumero(data.stock_bajo);
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Carga las citas del calendario.
// Como lo hace: Actualiza botones/titulo, pide datos por vista y fecha, guarda citas y renderiza.
async function cargarCalendario() {
    actualizarBotonesVista();
    actualizarTituloCalendario();
    mostrarCargandoCalendario();

    try {
        const fecha = obtenerIsoFecha(fechaCalendario);
        // La fecha se envia en formato ISO local para que backend y frontend calculen el mismo rango sin desfases de zona horaria.
        const data = await obtenerDatosApi(`${API_URL}/dashboard/calendario?vista=${vistaCalendario}&fecha=${fecha}`);
        citasCalendario = data.citas ?? [];
        renderizarCalendario();
    } catch (error) {
        document.getElementById('contenedorCalendario').innerHTML = `
            <div class="calendar-empty">
                <i class="bi bi-exclamation-triangle text-danger"></i>
                <p class="mb-0 text-danger">${escaparHtml(error.message)}</p>
            </div>
        `;
    }
}

// Documentacion: Muestra estado de carga del calendario.
// Como lo hace: Reemplaza el contenedor por un spinner mientras llega la API.
function mostrarCargandoCalendario() {
    document.getElementById('contenedorCalendario').innerHTML = `
        <div class="calendar-empty">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p class="mb-0">Cargando calendario...</p>
        </div>
    `;
}

// Documentacion: Decide que vista del calendario pintar.
// Como lo hace: Redirige a dia, mes o anio segun el estado vistaCalendario.
function renderizarCalendario() {
    if (vistaCalendario === 'dia') {
        renderizarDia();
        return;
    }

    if (vistaCalendario === 'anio') {
        renderizarAnio();
        return;
    }

    renderizarMes();
}

// Documentacion: Pinta la vista mensual del calendario.
// Como lo hace: Calcula dias del mes, espacios iniciales, agrupa citas por fecha y crea tarjetas por dia.
function renderizarMes() {
    const contenedor = document.getElementById('contenedorCalendario');
    const anio = fechaCalendario.getFullYear();
    const mes = fechaCalendario.getMonth();
    const primerDia = new Date(anio, mes, 1);
    const totalDias = new Date(anio, mes + 1, 0).getDate();
    // getDay() inicia en domingo; esta conversion mueve el lunes a la primera columna del calendario.
    const espaciosInicio = (primerDia.getDay() + 6) % 7;
    // Agrupar por fecha evita recorrer todas las citas por cada dia del mes.
    const citasPorFecha = agruparCitasPorFecha(citasCalendario);

    let html = `
        <div class="calendar-weekdays">
            ${diasSemana.map(dia => `<div class="weekday">${dia}</div>`).join('')}
        </div>
        <div class="calendar-grid">
    `;

    for (let i = 0; i < espaciosInicio; i++) {
        html += `<div class="calendar-day is-empty"></div>`;
    }

    for (let dia = 1; dia <= totalDias; dia++) {
        const fecha = new Date(anio, mes, dia);
        const iso = obtenerIsoFecha(fecha);
        const citasDia = citasPorFecha[iso] ?? [];
        const esHoy = iso === obtenerIsoFecha(new Date());
        const esPasado = esFechaPasada(fecha);
        const tieneCitas = citasDia.length > 0;
        // Un dia se considera disponible solo si no tiene citas y tampoco pertenece al pasado.
        const estaDisponible = !tieneCitas && !esPasado;
        const clases = [
            'calendar-day',
            tieneCitas ? 'has-appointments' : '',
            estaDisponible ? 'is-available' : '',
            esPasado ? 'is-past' : '',
            esHoy ? 'is-today' : ''
        ].filter(Boolean).join(' ');

        html += `
            <div class="${clases}" onclick="irADia('${iso}')">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="day-number">${dia}</span>
                    ${tieneCitas ? `<span class="badge bg-primary">${citasDia.length}</span>` : ''}
                </div>
                ${renderizarChipsCitas(citasDia)}
                ${estaDisponible ? `
                    <span class="availability-note">
                        <i class="bi bi-check2-circle"></i> Disponible
                    </span>
                ` : ''}
            </div>
        `;
    }

    html += `</div>`;
    contenedor.innerHTML = html;
}

// Documentacion: Pinta la vista diaria del calendario.
// Como lo hace: Filtra citas de la fecha actual, las ordena por hora y muestra disponibilidad si no hay citas.
function renderizarDia() {
    const contenedor = document.getElementById('contenedorCalendario');
    const iso = obtenerIsoFecha(fechaCalendario);
    // La vista diaria recibe las mismas citas del rango, pero aqui se filtra solo la fecha seleccionada.
    const citasDia = citasCalendario
        .filter(cita => cita.fecha === iso)
        .sort((a, b) => a.hora.localeCompare(b.hora));

    const citasHtml = citasDia.length
        ? citasDia.map(cita => `
            <div class="day-appointment">
                <div class="day-appointment-time">
                    <i class="bi bi-clock me-1"></i>${escaparHtml(cita.hora)}
                </div>
                <div>
                    <p class="day-appointment-title">${escaparHtml(cita.paciente)}</p>
                    <p class="day-appointment-meta">
                        <i class="bi bi-clipboard2-pulse me-1"></i>${escaparHtml(cita.tratamiento)}
                    </p>
                    <p class="day-appointment-meta">
                        <i class="bi bi-chat-left-text me-1"></i>${escaparHtml(cita.motivo_consulta || 'Sin motivo registrado')}
                    </p>
                </div>
            </div>
        `).join('')
        : `
            <div class="calendar-empty">
                <i class="bi bi-check2-circle"></i>
                <h5 class="fw-bold mb-1">Dia disponible</h5>
                <p class="mb-0">No hay citas programadas para esta fecha.</p>
            </div>
        `;

    contenedor.innerHTML = `
        <div class="day-view-shell">
            <div class="day-timeline">
                ${citasHtml}
            </div>
            <div class="available-panel">
                <h6><i class="bi bi-info-circle me-1"></i> Lectura rapida</h6>
                <p class="mb-2">
                    Las citas azules son horarios ocupados. El espacio verde indica que no hay otra cita programada registrada para ese dia.
                </p>
                <p class="mb-0">
                    <strong>${citasDia.length}</strong> cita${citasDia.length === 1 ? '' : 's'} programada${citasDia.length === 1 ? '' : 's'}.
                </p>
            </div>
        </div>
    `;
}

// Documentacion: Pinta la vista anual del calendario.
// Como lo hace: Cuenta citas por mes y crea tarjetas de meses disponibles u ocupados.
function renderizarAnio() {
    const contenedor = document.getElementById('contenedorCalendario');
    const anio = fechaCalendario.getFullYear();
    const conteoPorMes = new Array(12).fill(0);

    // La vista anual no necesita mostrar cada cita: solo resume cuantos registros existen por mes.
    citasCalendario.forEach(cita => {
        const fecha = fechaLocalDesdeIso(cita.fecha);
        if (fecha.getFullYear() === anio) {
            conteoPorMes[fecha.getMonth()]++;
        }
    });

    const mesesHtml = nombresMes.map((nombre, indice) => {
        const total = conteoPorMes[indice];
        const tieneCitas = total > 0;

        return `
            <div class="month-card ${tieneCitas ? 'has-appointments' : ''}" onclick="irAMes(${indice})">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <p class="month-name">${nombre}</p>
                    <i class="bi ${tieneCitas ? 'bi-calendar-event text-primary' : 'bi-check2-circle text-success'} fs-4"></i>
                </div>
                <span class="month-status ${tieneCitas ? 'scheduled' : 'available'}">
                    <i class="bi ${tieneCitas ? 'bi-calendar2-check' : 'bi-check2'}"></i>
                    ${tieneCitas ? `${total} cita${total === 1 ? '' : 's'}` : 'Disponible'}
                </span>
            </div>
        `;
    }).join('');

    contenedor.innerHTML = `<div class="year-grid">${mesesHtml}</div>`;
}

// Documentacion: Pinta resumenes pequenos de citas.
// Como lo hace: Muestra hasta tres chips por dia y agrega contador de citas restantes.
function renderizarChipsCitas(citasDia) {
    if (citasDia.length === 0) return '';

    const visibles = citasDia.slice(0, 3).map(cita => `
        <div class="appointment-chip" title="${escaparHtml(cita.paciente)}">
            <i class="bi bi-clock"></i>
            <span>${escaparHtml(cita.hora)} ${escaparHtml(cita.paciente)}</span>
        </div>
    `).join('');

    const restantes = citasDia.length > 3
        ? `<div class="appointment-chip"><i class="bi bi-plus-circle"></i><span>${citasDia.length - 3} mas</span></div>`
        : '';

    return visibles + restantes;
}

// Documentacion: Cambia entre dia, mes y anio.
// Como lo hace: Actualiza el estado de vista, normaliza fecha y vuelve a cargar datos.
function cambiarVistaCalendario(vista) {
    vistaCalendario = vista;
    fechaCalendario = normalizarFechaMinima(fechaCalendario);
    cargarCalendario();
}

// Documentacion: Mueve el calendario al periodo anterior o siguiente.
// Como lo hace: Suma dias, meses o anios segun la vista activa y recarga.
function moverCalendario(direccion) {
    const nuevaFecha = new Date(fechaCalendario);

    if (vistaCalendario === 'dia') {
        nuevaFecha.setDate(nuevaFecha.getDate() + direccion);
    } else if (vistaCalendario === 'anio') {
        nuevaFecha.setFullYear(nuevaFecha.getFullYear() + direccion);
    } else {
        nuevaFecha.setMonth(nuevaFecha.getMonth() + direccion);
    }

    fechaCalendario = normalizarFechaMinima(nuevaFecha);
    cargarCalendario();
}

// Documentacion: Regresa el calendario a hoy.
// Como lo hace: Normaliza la fecha actual y solicita nuevamente los datos.
function irAHoy() {
    fechaCalendario = normalizarFechaMinima(new Date());
    cargarCalendario();
}

// Documentacion: Abre la vista diaria de una fecha.
// Como lo hace: Convierte ISO a fecha local, cambia vista a dia y recarga.
function irADia(iso) {
    fechaCalendario = fechaLocalDesdeIso(iso);
    vistaCalendario = 'dia';
    cargarCalendario();
}

// Documentacion: Abre la vista mensual de un mes.
// Como lo hace: Fija el mes elegido en el anio actual del calendario y recarga.
function irAMes(indiceMes) {
    fechaCalendario = new Date(fechaCalendario.getFullYear(), indiceMes, 1);
    vistaCalendario = 'mes';
    cargarCalendario();
}

// Documentacion: Sincroniza estilos de botones de vista.
// Como lo hace: Marca como primario el boton de la vista activa y los demas como outline.
function actualizarBotonesVista() {
    const botones = {
        dia: document.getElementById('btnVistaDia'),
        mes: document.getElementById('btnVistaMes'),
        anio: document.getElementById('btnVistaAnio')
    };

    Object.entries(botones).forEach(([vista, boton]) => {
        boton.classList.toggle('btn-primary', vista === vistaCalendario);
        boton.classList.toggle('btn-outline-primary', vista !== vistaCalendario);
    });
}

// Documentacion: Actualiza el titulo del calendario.
// Como lo hace: Formatea la fecha segun vista diaria, mensual o anual.
function actualizarTituloCalendario() {
    const titulo = document.getElementById('tituloCalendario');

    if (vistaCalendario === 'dia') {
        titulo.textContent = fechaCalendario.toLocaleDateString('es-EC', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
        return;
    }

    if (vistaCalendario === 'anio') {
        titulo.textContent = fechaCalendario.getFullYear();
        return;
    }

    titulo.textContent = `${nombresMes[fechaCalendario.getMonth()]} ${fechaCalendario.getFullYear()}`;
}

// Documentacion: Agrupa citas por dia.
// Como lo hace: Reduce el arreglo de citas a un objeto indexado por fecha ISO.
function agruparCitasPorFecha(citas) {
    return citas.reduce((grupos, cita) => {
        if (!grupos[cita.fecha]) grupos[cita.fecha] = [];
        grupos[cita.fecha].push(cita);
        return grupos;
    }, {});
}

// Documentacion: Impide navegar antes del anio minimo.
// Como lo hace: Si la fecha es anterior a 2026 devuelve el 1 de enero de 2026.
function normalizarFechaMinima(fecha) {
    const minima = new Date(ANIO_MINIMO, 0, 1);
    if (fecha.getFullYear() < ANIO_MINIMO) {
        return minima;
    }

    return fecha;
}

// Documentacion: Convierte fecha ISO a Date local.
// Como lo hace: Separa anio, mes y dia para evitar desfases por zona horaria.
function fechaLocalDesdeIso(iso) {
    // Se construye con anio/mes/dia en vez de new Date(iso), porque algunos navegadores interpretan el ISO como UTC.
    const [anio, mes, dia] = iso.split('-').map(Number);
    return new Date(anio, mes - 1, dia);
}

// Documentacion: Convierte un Date a YYYY-MM-DD.
// Como lo hace: Rellena mes y dia con ceros para enviar fechas estables a la API.
function obtenerIsoFecha(fecha) {
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const dia = String(fecha.getDate()).padStart(2, '0');
    return `${anio}-${mes}-${dia}`;
}

// Documentacion: Indica si una fecha ya paso.
// Como lo hace: Compara ambas fechas a medianoche para ignorar horas.
function esFechaPasada(fecha) {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    const comparada = new Date(fecha);
    comparada.setHours(0, 0, 0, 0);

    return comparada < hoy;
}

// Documentacion: Formatea numeros para Ecuador.
// Como lo hace: Usa Intl.NumberFormat es-EC con valor cero por defecto.
function formatearNumero(valor) {
    return new Intl.NumberFormat('es-EC').format(Number(valor ?? 0));
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
