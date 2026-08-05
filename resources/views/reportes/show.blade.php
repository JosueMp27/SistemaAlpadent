{{--
    Documentacion de archivo:
    Vista Blade del modulo reportes; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .report-topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .report-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: .25rem;
    }

    .report-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .report-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: .5rem;
    }

    .report-filter {
        min-width: 210px;
    }

    .report-sheet {
        background: #fff;
        border: 1px solid #e7edf5;
        border-radius: 8px;
        box-shadow: 0 .35rem 1.25rem rgba(22, 34, 51, .06);
        overflow: hidden;
    }

    .report-brand {
        padding: 1.25rem;
        background: #eef6ff;
        border-bottom: 1px solid #dbeafe;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }

    .brand-name {
        font-size: .85rem;
        font-weight: 800;
        color: #0d6efd;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .2rem;
    }

    .brand-report {
        font-size: 1.35rem;
        font-weight: 800;
        color: #172033;
        margin-bottom: .35rem;
    }

    .brand-detail {
        color: #5d6878;
        margin: 0;
        max-width: 760px;
    }

    .report-meta {
        text-align: right;
        color: #516075;
        font-size: .9rem;
        min-width: 260px;
    }

    .report-meta p {
        margin-bottom: .25rem;
    }

    .report-body {
        padding: 1.25rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .8rem;
        margin-bottom: 1.2rem;
    }

    .summary-item {
        border-radius: 8px;
        border: 1px solid #e8eef6;
        padding: .9rem;
        background: #f8fafc;
    }

    .summary-item.primary {
        background: #eef6ff;
        border-color: #cfe4ff;
    }

    .summary-item.success {
        background: #effaf3;
        border-color: #cdeed8;
    }

    .summary-item.danger {
        background: #fff1f2;
        border-color: #ffd4da;
    }

    .summary-item.warning {
        background: #fff8e7;
        border-color: #ffe8a9;
    }

    .summary-item.info {
        background: #edfafa;
        border-color: #c8eeee;
    }

    .summary-label {
        color: #64748b;
        font-size: .82rem;
        margin-bottom: .35rem;
    }

    .summary-value {
        font-size: 1.25rem;
        line-height: 1.1;
        font-weight: 800;
        color: #172033;
        margin: 0;
    }

    .report-table {
        font-size: .88rem;
    }

    .report-table thead th {
        background: #f1f6fd;
        color: #26364d;
        border-bottom: 1px solid #dbe6f2;
        white-space: nowrap;
    }

    .report-table td {
        color: #334155;
    }

    .report-loading,
    .report-empty {
        min-height: 280px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #64748b;
    }

    @media (max-width: 991px) {
        .report-topbar,
        .report-brand {
            flex-direction: column;
        }

        .report-actions {
            justify-content: flex-start;
        }

        .report-meta {
            text-align: left;
            min-width: 0;
        }

        .summary-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 575px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        @page {
            size: landscape;
            margin: 12mm;
        }

        .sidebar,
        .navbar,
        .report-topbar {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .report-sheet {
            box-shadow: none !important;
            border: 0 !important;
        }

        .report-brand {
            background: #fff !important;
            border-bottom: 1px solid #d7dee8;
        }

        .summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .report-table {
            font-size: .68rem;
        }

        .table-responsive {
            overflow: visible !important;
        }
    }
</style>

<div class="report-topbar">
    <div>
        <h3 class="report-title" id="tituloPagina">Reporte</h3>
        <p class="report-subtitle" id="subtituloPagina">Generando reporte...</p>
    </div>

    <div class="report-actions">
        <select class="form-select report-filter d-none" id="selectorOrigen" onchange="cambiarOrigenReporte()">
            <option value="citas">Pagos de citas</option>
            <option value="ventas">Pagos de ventas</option>
        </select>
        <a href="/reportes" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <button class="btn btn-outline-primary" onclick="imprimirReporte()" id="btnImprimir" disabled>
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
        <button class="btn btn-outline-danger" onclick="descargarReporte('pdf')" id="btnPdf" disabled>
            <i class="bi bi-filetype-pdf me-1"></i> Descargar PDF
        </button>
        <button class="btn btn-outline-success" onclick="descargarReporte('excel')" id="btnExcel" disabled>
            <i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel
        </button>
    </div>
</div>

<div class="report-sheet" id="reporteContenedor">
    <div class="report-loading">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <p class="mb-0">Generando reporte...</p>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;
const REPORT_TYPE = @json($tipo);
const REPORT_HAS_ORIGIN = ['pagos', 'movimientos-pagos'].includes(REPORT_TYPE);

let reporteActual = null;
let origenReporte = new URLSearchParams(window.location.search).get('origen') || 'citas';

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', () => {
    prepararSelectorOrigen();
    cargarReporte();
});

// Documentacion: Construye los encabezados para llamar la API.
// Como lo hace: Incluye Accept JSON y el token Bearer guardado en localStorage.
function headersApi() {
    return {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    };
}

// Documentacion: Carga cargar reporte.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarReporte() {
    try {
        const response = await fetch(`${API_URL}/reportes/${REPORT_TYPE}${queryOrigen()}`, {
            method: 'GET',
            headers: headersApi()
        });

        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo generar el reporte');
        }

        reporteActual = resultado.data;
        renderizarReporte(reporteActual);
        habilitarAcciones(true);
    } catch (error) {
        document.getElementById('reporteContenedor').innerHTML = `
            <div class="report-empty">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2"></i>
                <h5 class="fw-bold">No se pudo cargar el reporte</h5>
                <p class="mb-0">${escaparHtml(error.message)}</p>
            </div>
        `;
        habilitarAcciones(false);
    }
}

// Documentacion: Ejecuta preparar selector origen.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function prepararSelectorOrigen() {
    if (!REPORT_HAS_ORIGIN) return;

    const selector = document.getElementById('selectorOrigen');
    selector.classList.remove('d-none');
    selector.value = origenReporte === 'ventas' ? 'ventas' : 'citas';

    if (REPORT_TYPE === 'movimientos-pagos') {
        selector.options[0].textContent = 'Movimientos de citas';
        selector.options[1].textContent = 'Movimientos de ventas';
    }
}

// Documentacion: Ejecuta cambiar origen reporte.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarOrigenReporte() {
    origenReporte = document.getElementById('selectorOrigen').value;
    const params = new URLSearchParams(window.location.search);
    params.set('origen', origenReporte);
    window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
    reporteActual = null;
    habilitarAcciones(false);
    document.getElementById('reporteContenedor').innerHTML = `
        <div class="report-loading">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p class="mb-0">Generando reporte...</p>
        </div>
    `;
    cargarReporte();
}

// Documentacion: Ejecuta query origen.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function queryOrigen() {
    return REPORT_HAS_ORIGIN ? `?origen=${encodeURIComponent(origenReporte)}` : '';
}

// Documentacion: Renderiza renderizar reporte.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarReporte(reporte) {
    document.getElementById('tituloPagina').textContent = reporte.titulo;
    document.getElementById('subtituloPagina').textContent = `${reporte.total_registros} registros encontrados`;

    document.getElementById('reporteContenedor').innerHTML = `
        <div class="report-brand">
            <div>
                <div class="brand-name">Alpadent - Reportes</div>
                <h4 class="brand-report">${escaparHtml(reporte.titulo)}</h4>
                <p class="brand-detail">${escaparHtml(reporte.detalle)}</p>
            </div>
            <div class="report-meta">
                <p><strong>Reporte generado por:</strong><br>${escaparHtml(reporte.generado_por)}</p>
                <p><strong>Fecha y hora:</strong><br>${escaparHtml(reporte.generado_en)}</p>
            </div>
        </div>
        <div class="report-body">
            ${renderizarResumen(reporte.resumen)}
            ${renderizarTabla(reporte.columnas, reporte.filas)}
        </div>
    `;
}

// Documentacion: Renderiza renderizar resumen.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarResumen(resumen) {
    return `
        <div class="summary-grid">
            ${resumen.map(item => `
                <div class="summary-item ${escaparHtml(item.tone || 'primary')}">
                    <div class="summary-label">${escaparHtml(item.label)}</div>
                    <p class="summary-value">${escaparHtml(item.value)}</p>
                </div>
            `).join('')}
        </div>
    `;
}

// Documentacion: Renderiza renderizar tabla.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarTabla(columnas, filas) {
    if (!filas || filas.length === 0) {
        return `
            <div class="report-empty">
                <i class="bi bi-inbox fs-1 mb-2"></i>
                <p class="mb-0">No hay registros para mostrar.</p>
            </div>
        `;
    }

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle report-table">
                <thead>
                    <tr>
                        ${columnas.map(columna => `<th>${escaparHtml(columna)}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${filas.map(fila => `
                        <tr>
                            ${columnas.map(columna => `<td>${escaparHtml(fila[columna] ?? '')}</td>`).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Documentacion: Ejecuta habilitar acciones.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function habilitarAcciones(habilitado) {
    document.getElementById('btnImprimir').disabled = !habilitado;
    document.getElementById('btnPdf').disabled = !habilitado;
    document.getElementById('btnExcel').disabled = !habilitado;
}

// Documentacion: Ejecuta imprimir reporte.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function imprimirReporte() {
    if (!reporteActual) return;
    window.print();
}

// Documentacion: Ejecuta descargar reporte.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function descargarReporte(formato) {
    if (!reporteActual) return;

    const boton = formato === 'pdf' ? document.getElementById('btnPdf') : document.getElementById('btnExcel');
    const textoOriginal = boton.innerHTML;
    boton.disabled = true;
    boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Descargando';

    try {
        const response = await fetch(`${API_URL}/reportes/${REPORT_TYPE}/${formato}${queryOrigen()}`, {
            method: 'GET',
            headers: headersApi()
        });

        if (!response.ok) {
            const texto = await response.text();
            let mensaje = 'No se pudo descargar el reporte';

            try {
                mensaje = JSON.parse(texto).message || mensaje;
            } catch (error) {
                mensaje = texto || mensaje;
            }

            throw new Error(mensaje);
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const enlace = document.createElement('a');
        enlace.href = url;
        enlace.download = obtenerNombreArchivo(response, formato);
        document.body.appendChild(enlace);
        enlace.click();
        enlace.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        alertaError(error.message);
    } finally {
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
    }
}

// Documentacion: Ejecuta obtener nombre archivo.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function obtenerNombreArchivo(response, formato) {
    const disposition = response.headers.get('Content-Disposition') || '';
    const match = disposition.match(/filename="?([^"]+)"?/);

    if (match && match[1]) {
        return match[1];
    }

    return `alpadent_reporte_${REPORT_TYPE}.${formato === 'excel' ? 'xls' : 'pdf'}`;
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
