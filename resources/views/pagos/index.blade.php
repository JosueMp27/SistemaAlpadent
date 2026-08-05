{{--
    Documentacion de archivo:
    Vista Blade del modulo pagos; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .payments-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 22px;
    }

    .payments-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: 4px;
    }

    .payments-subtitle {
        color: #697586;
        margin-bottom: 0;
    }

    .payment-card {
        border: 0;
        border-radius: 8px;
    }

    .payment-toolbar {
        border-radius: 8px;
        border: 1px solid #e6eef8;
        background: #f7fafe;
        padding: 16px;
    }

    .payment-table thead th {
        background: #dfeaff;
        color: #174a7c;
        font-size: .84rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .payment-table td {
        vertical-align: middle;
        font-size: .88rem;
    }

    .text-money {
        font-weight: 800;
        white-space: nowrap;
    }

    .text-paid {
        color: #159447;
    }

    .text-debt {
        color: #d02035;
    }

    .payment-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 7px;
        padding: 6px 9px;
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .payment-status.paid {
        color: #11733d;
        background: #dcf8e8;
        border: 1px solid #bcebd0;
    }

    .payment-status.pending {
        color: #b42331;
        background: #ffe4e8;
        border: 1px solid #ffc8d0;
    }

    .detail-shell {
        display: none;
    }

    .detail-shell.is-visible {
        display: block;
    }

    .detail-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .detail-panel {
        border-radius: 8px;
        border: 1px solid #e6eef8;
        background: #fff;
        padding: 16px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 16px 28px;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 128px minmax(0, 1fr);
        gap: 12px;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid #edf1f6;
    }

    .detail-row:last-child {
        border-bottom: 0;
    }

    .detail-label {
        font-weight: 800;
        color: #26364a;
        font-size: .86rem;
    }

    .detail-value {
        color: #243244;
        overflow-wrap: anywhere;
    }

    .movement-table thead th {
        background: #dfeaff;
        color: #174a7c;
        font-size: .84rem;
    }

    .modal-pay-header {
        color: #057a55;
    }

    .change-box {
        border-radius: 8px;
        border: 1px solid #fed7aa;
        background: #fff7ed;
        color: #c2410c;
        padding: 10px 12px;
        font-weight: 800;
    }

    @media (max-width: 991.98px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .payments-header {
            flex-direction: column;
        }

        .detail-row {
            grid-template-columns: 1fr;
            gap: 3px;
        }
    }
</style>

<section id="vistaListadoPagos">
    <div class="payments-header">
        <div>
            <h3 class="payments-title">Pago de citas</h3>
            <p class="payments-subtitle">Control de cobros, abonos y saldos por cita</p>
        </div>

        <button type="button" class="btn btn-outline-primary" onclick="cargarCitasPagos()">
            <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
        </button>
    </div>

    <div class="payment-toolbar mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label">Paciente</label>
                <select id="filtroPacientePago" class="form-select" onchange="paginaActual = 1; cargarCitasPagos();">
                    <option value="">Todos los pacientes</option>
                </select>
            </div>

            <div class="col-lg-5">
                <label class="form-label">Buscar cita</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="busquedaPago" class="form-control" placeholder="Buscar por paciente, email, historia clinica, tratamiento o motivo">
                </div>
            </div>

            <div class="col-lg-3 d-flex gap-2">
                <button type="button" class="btn btn-primary flex-fill" onclick="buscarPagos()">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltrosPagos()">
                    Limpiar
                </button>
            </div>
        </div>
    </div>

    <div class="card payment-card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-receipt text-primary me-2"></i> Citas para pagos
                </h5>
                <small class="text-muted" id="infoPagos">Sin datos</small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle payment-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>#HC</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Tratamiento</th>
                            <th>Precio</th>
                            <th>Motivo</th>
                            <th>Pago</th>
                            <th>Costo</th>
                            <th>Pagado</th>
                            <th>Saldo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaPagos">
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">Cargando citas...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end align-items-center mt-3 gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="btnAnteriorPagos" onclick="cambiarPaginaPagos(-1)" disabled>
                    Anterior
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="btnSiguientePagos" onclick="cambiarPaginaPagos(1)" disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</section>

<section id="vistaDetallePago" class="detail-shell">
    <div class="detail-topbar">
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-link text-decoration-none px-0" onclick="volverListadoPagos()">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </button>
            <h4 class="fw-bold mb-0">Detalle de pago</h4>
        </div>

        <button type="button" class="btn btn-primary" id="btnAbrirCobro" onclick="abrirModalCobro()">
            <i class="bi bi-plus-circle me-1"></i> Cobrar
        </button>
    </div>

    <div class="detail-panel shadow-sm mb-4">
        <div class="detail-grid">
            <div>
                <div class="detail-row">
                    <span class="detail-label">Paciente:</span>
                    <span class="detail-value" id="detallePaciente">--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Motivo:</span>
                    <span class="detail-value" id="detalleMotivo">--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tratamiento:</span>
                    <span class="detail-value" id="detalleTratamiento">--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Medico:</span>
                    <span class="detail-value" id="detalleMedico">--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha y hora:</span>
                    <span class="detail-value" id="detalleFechaHora">--</span>
                </div>
            </div>

            <div>
                <div class="detail-row">
                    <span class="detail-label">Estado:</span>
                    <span class="detail-value" id="detalleEstado">--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Costo:</span>
                    <span class="detail-value text-money" id="detalleCosto">--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pagado:</span>
                    <span class="detail-value text-money text-paid" id="detallePagado">--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Saldo:</span>
                    <span class="detail-value text-money text-debt" id="detalleSaldo">--</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card payment-card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-clock-history text-primary me-2"></i> Movimientos de pago
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle movement-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Monto</th>
                            <th>Recibio</th>
                            <th>Metodo</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaMovimientosPago">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Seleccione una cita</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalCobro" tabindex="-1" aria-labelledby="modalCobroLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title modal-pay-header" id="modalCobroLabel">
                    <i class="bi bi-cash-coin me-2"></i> Registro de Pagos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formCobro">
                    <div class="mb-3">
                        <label class="form-label">Monto a cobrar</label>
                        <input type="number" id="montoCobro" class="form-control" min="0.01" step="0.01" placeholder="Monto" oninput="calcularVueltoCobro()">
                        <small class="text-muted">Saldo actual: <strong id="saldoActualCobro">$0.00</strong></small>
                    </div>

                    <div id="grupoVueltoCobro" class="change-box mb-3 d-none">
                        Vuelto a dar: <span id="vueltoCobro">$0.00</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metodo de pago</label>
                        <select id="metodoPagoCobro" class="form-select" onchange="actualizarReferenciaCobro()">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="grupoReferenciaCobro">
                        <label class="form-label">Referencia</label>
                        <input type="text" id="referenciaCobro" class="form-control" maxlength="100" placeholder="Numero o referencia">
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Observaciones</label>
                        <textarea id="observacionesCobro" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="guardarCobro()">
                    <i class="bi bi-check2 me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;

let paginaActual = 1;
let ultimaPagina = 1;
let usuarioActual = null;
let citaSeleccionadaId = null;
let detallePagoActual = null;
let modalCobro = null;

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', async () => {
    await obtenerUsuarioActual();
    await cargarPacientesPago();
    await cargarCitasPagos();

    document.getElementById('busquedaPago').addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            buscarPagos();
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
        const response = await fetch(`${API_URL}/auth/me`, {
            headers: headersApi()
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

// Documentacion: Carga cargar pacientes pago.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarPacientesPago() {
    const select = document.getElementById('filtroPacientePago');
    let pacientes = [];
    let page = 1;
    let lastPage = 1;

    try {
        do {
            const response = await fetch(`${API_URL}/pacientes?page=${page}`, {
                headers: headersApi()
            });
            const resultado = await response.json();

            if (!response.ok || !resultado.success) {
                throw new Error(resultado.message || 'No se pudieron cargar los pacientes');
            }

            pacientes = pacientes.concat(resultado.data.data ?? []);
            lastPage = resultado.data.last_page ?? 1;
            page++;
        } while (page <= lastPage);

        select.innerHTML = '<option value="">Todos los pacientes</option>';

        pacientes.forEach(paciente => {
            select.innerHTML += `
                <option value="${paciente.id}">
                    ${escaparHtml(paciente.numero_historia)} - ${escaparHtml(paciente.nombre)} ${escaparHtml(paciente.apellido)}
                </option>
            `;
        });
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Busca buscar pagos.
// Como lo hace: Actualiza filtros de la pantalla y vuelve a consultar la API.
function buscarPagos() {
    paginaActual = 1;
    cargarCitasPagos();
}

// Documentacion: Limpia limpiar filtros pagos.
// Como lo hace: Reinicia campos o filtros y vuelve al estado base de la pantalla.
function limpiarFiltrosPagos() {
    document.getElementById('filtroPacientePago').value = '';
    document.getElementById('busquedaPago').value = '';
    paginaActual = 1;
    cargarCitasPagos();
}

// Documentacion: Carga cargar citas pagos.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarCitasPagos() {
    const tabla = document.getElementById('tablaPagos');
    const pacienteId = document.getElementById('filtroPacientePago').value;
    const search = document.getElementById('busquedaPago').value.trim();

    tabla.innerHTML = `
        <tr>
            <td colspan="13" class="text-center text-muted py-4">Cargando citas...</td>
        </tr>
    `;

    let url = `${API_URL}/pagos/citas?page=${paginaActual}`;
    if (pacienteId) url += `&paciente_id=${encodeURIComponent(pacienteId)}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    try {
        const response = await fetch(url, {
            headers: headersApi()
        });
        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudieron cargar los pagos');
        }

        const paginacion = resultado.data;
        paginaActual = paginacion.current_page ?? 1;
        ultimaPagina = paginacion.last_page ?? 1;

        renderizarTablaPagos(paginacion.data ?? []);
        actualizarPaginacionPagos(paginacion.total ?? 0);
    } catch (error) {
        tabla.innerHTML = `
            <tr>
                <td colspan="13" class="text-center text-danger py-4">Error al cargar pagos</td>
            </tr>
        `;
        alertaError(error.message);
    }
}

// Documentacion: Renderiza renderizar tabla pagos.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarTablaPagos(citas) {
    const tabla = document.getElementById('tablaPagos');
    tabla.innerHTML = '';

    if (citas.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="13" class="text-center text-muted py-4">No hay citas para mostrar</td>
            </tr>
        `;
        return;
    }

    citas.forEach((cita, index) => {
        const numero = ((paginaActual - 1) * 15) + index + 1;

        tabla.innerHTML += `
            <tr>
                <td>${numero}</td>
                <td>${escaparHtml(cita.numero_historia)}</td>
                <td>${escaparHtml(cita.paciente)}</td>
                <td>${formatearFecha(cita.fecha)}</td>
                <td>${escaparHtml(cita.hora ?? 'N/A')}</td>
                <td>${escaparHtml(cita.tratamiento)}</td>
                <td class="text-money">${formatearPrecio(cita.precio)}</td>
                <td>${escaparHtml(cita.motivo ?? 'Sin motivo')}</td>
                <td>${badgePago(cita.estado_pago)}</td>
                <td class="text-money">${formatearPrecio(cita.costo)}</td>
                <td class="text-money text-paid">${formatearPrecio(cita.pagado)}</td>
                <td class="text-money ${Number(cita.saldo) > 0 ? 'text-debt' : 'text-paid'}">${formatearPrecio(cita.saldo)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary" onclick="abrirDetallePago(${cita.cita_id})">
                        <i class="bi bi-credit-card me-1"></i> Pagar
                    </button>
                </td>
            </tr>
        `;
    });
}

// Documentacion: Actualiza actualizar paginacion pagos.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPaginacionPagos(total) {
    document.getElementById('infoPagos').textContent =
        `Pagina ${paginaActual} de ${ultimaPagina} | Total: ${total} citas`;

    document.getElementById('btnAnteriorPagos').disabled = paginaActual <= 1;
    document.getElementById('btnSiguientePagos').disabled = paginaActual >= ultimaPagina;
}

// Documentacion: Ejecuta cambiar pagina pagos.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPaginaPagos(direccion) {
    const nuevaPagina = paginaActual + direccion;
    if (nuevaPagina < 1 || nuevaPagina > ultimaPagina) return;

    paginaActual = nuevaPagina;
    cargarCitasPagos();
}

// Documentacion: Abre abrir detalle pago.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
async function abrirDetallePago(citaId) {
    citaSeleccionadaId = citaId;

    try {
        const response = await fetch(`${API_URL}/pagos/cita/${citaId}`, {
            headers: headersApi()
        });
        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            throw new Error(resultado.message || 'No se pudo obtener el detalle de pago');
        }

        detallePagoActual = resultado.data;
        renderizarDetallePago(detallePagoActual);
        document.getElementById('vistaListadoPagos').classList.add('d-none');
        document.getElementById('vistaDetallePago').classList.add('is-visible');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Vuelve desde volver listado pagos.
// Como lo hace: Oculta el panel de detalle y restaura el listado principal.
function volverListadoPagos() {
    document.getElementById('vistaDetallePago').classList.remove('is-visible');
    document.getElementById('vistaListadoPagos').classList.remove('d-none');
    detallePagoActual = null;
    citaSeleccionadaId = null;
    cargarCitasPagos();
}

// Documentacion: Renderiza renderizar detalle pago.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarDetallePago(detalle) {
    const cita = detalle.cita;
    const saldo = Number(cita.saldo ?? 0);

    document.getElementById('detallePaciente').textContent = `${cita.numero_historia} - ${cita.paciente}`;
    document.getElementById('detalleMotivo').textContent = cita.motivo || 'Sin motivo';
    document.getElementById('detalleTratamiento').textContent = cita.tratamiento;
    document.getElementById('detalleMedico').textContent = detalle.medico || 'No asignado';
    document.getElementById('detalleFechaHora').textContent = `${formatearFecha(cita.fecha)} ${cita.hora ?? ''}`;
    document.getElementById('detalleEstado').innerHTML = badgePago(cita.estado_pago);
    document.getElementById('detalleCosto').textContent = formatearPrecio(cita.costo);
    document.getElementById('detallePagado').textContent = formatearPrecio(cita.pagado);
    document.getElementById('detalleSaldo').textContent = formatearPrecio(cita.saldo);

    const botonCobro = document.getElementById('btnAbrirCobro');
    botonCobro.disabled = saldo <= 0 || Number(cita.costo) <= 0;
    botonCobro.innerHTML = saldo <= 0
        ? '<i class="bi bi-check2-circle me-1"></i> Pagado'
        : '<i class="bi bi-plus-circle me-1"></i> Cobrar';

    renderizarMovimientosPago(detalle.movimientos ?? []);
}

// Documentacion: Renderiza renderizar movimientos pago.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarMovimientosPago(movimientos) {
    const tabla = document.getElementById('tablaMovimientosPago');
    tabla.innerHTML = '';

    if (movimientos.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Aun no hay movimientos de pago para esta cita</td>
            </tr>
        `;
        return;
    }

    movimientos.forEach((movimiento, index) => {
        tabla.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${formatearFecha(movimiento.fecha)}</td>
                <td>${escaparHtml(movimiento.hora ?? 'N/A')}</td>
                <td class="text-money text-paid">${formatearPrecio(movimiento.monto)}</td>
                <td>${escaparHtml(movimiento.recibio)}</td>
                <td>${formatearMetodoPago(movimiento.metodo_pago)}</td>
                <td>${escaparHtml(movimiento.observaciones ?? '')}</td>
            </tr>
        `;
    });
}

// Documentacion: Abre abrir modal cobro.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalCobro() {
    if (!detallePagoActual) return;

    const saldo = Number(detallePagoActual.cita.saldo ?? 0);

    if (saldo <= 0) {
        alertaExito('Esta cita ya esta pagada.');
        return;
    }

    document.getElementById('formCobro').reset();
    document.getElementById('saldoActualCobro').textContent = formatearPrecio(saldo);
    document.getElementById('grupoVueltoCobro').classList.add('d-none');
    document.getElementById('grupoReferenciaCobro').classList.add('d-none');

    modalCobro = new bootstrap.Modal(document.getElementById('modalCobro'));
    modalCobro.show();
}

// Documentacion: Calcula calcular vuelto cobro.
// Como lo hace: Toma valores numericos de la pantalla y deriva totales, saldos o vuelto.
function calcularVueltoCobro() {
    if (!detallePagoActual) return;

    const saldo = Number(detallePagoActual.cita.saldo ?? 0);
    const monto = Number(document.getElementById('montoCobro').value || 0);
    const vuelto = Math.max(monto - saldo, 0);
    const grupoVuelto = document.getElementById('grupoVueltoCobro');

    document.getElementById('vueltoCobro').textContent = formatearPrecio(vuelto);
    grupoVuelto.classList.toggle('d-none', vuelto <= 0);
}

// Documentacion: Actualiza actualizar referencia cobro.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarReferenciaCobro() {
    const metodo = document.getElementById('metodoPagoCobro').value;
    const grupo = document.getElementById('grupoReferenciaCobro');
    const referencia = document.getElementById('referenciaCobro');

    grupo.classList.toggle('d-none', metodo !== 'transferencia');
    if (metodo !== 'transferencia') referencia.value = '';
}

// Documentacion: Guarda guardar cobro.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarCobro() {
    if (!detallePagoActual || !citaSeleccionadaId) return;

    const monto = Number(document.getElementById('montoCobro').value || 0);
    const metodo = document.getElementById('metodoPagoCobro').value;
    const referencia = document.getElementById('referenciaCobro').value.trim();
    const observaciones = document.getElementById('observacionesCobro').value.trim();

    if (!usuarioActual || !usuarioActual.id) {
        alertaError('No se pudo identificar el usuario actual.');
        return;
    }

    if (monto <= 0) {
        alertaError('Ingrese un monto mayor a 0.');
        return;
    }

    if (metodo === 'transferencia' && referencia.length < 3) {
        alertaError('La referencia de transferencia es requerida.');
        return;
    }

    const datos = {
        usuario_id: usuarioActual.id,
        monto,
        metodo_pago: metodo,
        referencia: referencia || null,
        observaciones: observaciones || null,
    };

    try {
        const response = await fetch(`${API_URL}/pagos/cita/${citaSeleccionadaId}/cobrar`, {
            method: 'POST',
            headers: headersApi(true),
            body: JSON.stringify(datos)
        });

        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo registrar el cobro';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            return;
        }

        modalCobro.hide();

        const vuelto = Number(resultado.data.vuelto ?? 0);
        Swal.fire({
            icon: 'success',
            title: 'Cobro registrado',
            html: vuelto > 0
                ? `Pago aplicado correctamente.<br><strong>Vuelto a dar: ${formatearPrecio(vuelto)}</strong>`
                : 'Pago aplicado correctamente.',
            confirmButtonColor: '#0d6efd',
            timer: 2600,
            timerProgressBar: true
        });

        await abrirDetallePago(citaSeleccionadaId);
        cargarCitasPagos();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Genera badge para badge pago.
// Como lo hace: Mapea estados internos a clases y textos visuales.
function badgePago(estado) {
    if (estado === 'pagado') {
        return `
            <span class="payment-status paid">
                <i class="bi bi-check-circle"></i> Pagado
            </span>
        `;
    }

    return `
        <span class="payment-status pending">
            <i class="bi bi-hourglass-split"></i> Pendiente
        </span>
    `;
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

// Documentacion: Formatea formatear metodo pago.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearMetodoPago(metodo) {
    const metodos = {
        efectivo: '<span class="badge bg-success-subtle text-success"><i class="bi bi-cash me-1"></i>Efectivo</span>',
        transferencia: '<span class="badge bg-primary-subtle text-primary"><i class="bi bi-bank me-1"></i>Transferencia</span>',
        tarjeta: '<span class="badge bg-warning-subtle text-warning"><i class="bi bi-credit-card me-1"></i>Tarjeta</span>',
    };

    return metodos[metodo] ?? escaparHtml(metodo || 'N/A');
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
