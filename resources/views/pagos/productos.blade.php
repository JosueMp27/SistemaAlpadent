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

    .payment-table thead th,
    .movement-table thead th {
        background: #dfeaff;
        color: #174a7c;
        font-size: .84rem;
        white-space: nowrap;
    }

    .pay-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 7px;
        padding: 6px 9px;
        font-weight: 800;
        font-size: .78rem;
    }

    .pay-paid {
        background: #dcf8e8;
        color: #11733d;
    }

    .pay-pending {
        background: #ffe4e8;
        color: #b42331;
    }

    .detail-shell {
        display: none;
    }

    .detail-shell.is-visible {
        display: block;
    }

    .detail-panel {
        border-radius: 8px;
        border: 1px solid #e6eef8;
        background: #fff;
        padding: 16px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 130px minmax(0, 1fr);
        gap: 10px;
        border-bottom: 1px solid #edf1f6;
        padding: 8px 0;
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
</style>

<section id="vistaListadoPagoProductos">
    <div class="payments-header">
        <div>
            <h3 class="payments-title">Pago de productos</h3>
            <p class="text-muted mb-0">Cobros y abonos de ventas de productos odontologicos</p>
        </div>

        <button type="button" class="btn btn-outline-primary" onclick="cargarVentasPago()">
            <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
        </button>
    </div>

    <div class="payment-toolbar mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Paciente</label>
                <select id="filtroPacientePagoProducto" class="form-select" onchange="paginaActual = 1; cargarVentasPago();">
                    <option value="">Todos los pacientes</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select id="filtroEstadoPagoProducto" class="form-select" onchange="paginaActual = 1; cargarVentasPago();">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="parcial">Parcial</option>
                    <option value="pagado">Pagado</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary w-100" onclick="cargarVentasPago()">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
            </div>
        </div>
    </div>

    <div class="card payment-card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-bag-check text-primary me-2"></i> Ventas de productos
                </h5>
                <small class="text-muted" id="infoPagoProductos">Sin datos</small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle payment-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaPagoProductos">
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Cargando ventas...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button class="btn btn-outline-secondary btn-sm" id="btnAnteriorPagoProductos" onclick="cambiarPagina(-1)" disabled>Anterior</button>
                <button class="btn btn-outline-secondary btn-sm" id="btnSiguientePagoProductos" onclick="cambiarPagina(1)" disabled>Siguiente</button>
            </div>
        </div>
    </div>
</section>

<section id="vistaDetallePagoProducto" class="detail-shell">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
        <button type="button" class="btn btn-link text-decoration-none px-0" onclick="volverListado()">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </button>
        <button type="button" class="btn btn-primary" id="btnAbrirCobroProducto" onclick="abrirModalCobroProducto()">
            <i class="bi bi-plus-circle me-1"></i> Cobrar
        </button>
    </div>

    <div class="detail-panel shadow-sm mb-4">
        <h4 class="fw-bold mb-3">Detalle de venta</h4>
        <div class="detail-grid">
            <div>
                <div class="detail-row"><strong>Paciente:</strong><span id="detallePaciente">--</span></div>
                <div class="detail-row"><strong>Fecha:</strong><span id="detalleFecha">--</span></div>
                <div class="detail-row"><strong>Vendido por:</strong><span id="detalleUsuario">--</span></div>
                <div class="detail-row"><strong>Estado:</strong><span id="detalleEstado">--</span></div>
            </div>
            <div>
                <div class="detail-row"><strong>Total:</strong><span class="fw-bold" id="detalleTotal">--</span></div>
                <div class="detail-row"><strong>Pagado:</strong><span class="fw-bold text-success" id="detallePagado">--</span></div>
                <div class="detail-row"><strong>Saldo:</strong><span class="fw-bold text-danger" id="detalleSaldo">--</span></div>
                <div class="detail-row"><strong>Observaciones:</strong><span id="detalleObservaciones">--</span></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card payment-card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Productos vendidos</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle movement-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tablaDetalleProductos"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card payment-card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Movimientos de pago</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle movement-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Metodo</th>
                                    <th>Recibio</th>
                                </tr>
                            </thead>
                            <tbody id="tablaAbonosProductos"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalCobroProducto" tabindex="-1" aria-labelledby="modalCobroProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="modalCobroProductoLabel">
                    <i class="bi bi-cash-coin me-2"></i> Pago de productos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formCobroProducto">
                    <div class="mb-3">
                        <label class="form-label">Monto recibido</label>
                        <input type="number" id="montoCobroProducto" class="form-control" min="0.01" step="0.01" placeholder="Monto" oninput="calcularVuelto()">
                        <small class="text-muted">Saldo actual: <strong id="saldoActualProducto">$0.00</strong></small>
                    </div>
                    <div id="grupoVueltoProducto" class="change-box mb-3 d-none">
                        Vuelto a dar: <span id="vueltoProducto">$0.00</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metodo de pago</label>
                        <select id="metodoPagoProducto" class="form-select" onchange="actualizarReferencia()">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="grupoReferenciaProducto">
                        <label class="form-label">Referencia</label>
                        <input type="text" id="referenciaProducto" class="form-control" maxlength="100">
                    </div>
                    <div>
                        <label class="form-label">Observaciones</label>
                        <textarea id="observacionesPagoProducto" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarCobroProducto()">
                    <i class="bi bi-check2 me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;
let usuarioActual = null;
let paginaActual = 1;
let ultimaPagina = 1;
let ventaActual = null;
let modalCobroProducto = null;

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', async () => {
    await obtenerUsuarioActual();
    await cargarPacientes();
    await cargarVentasPago();
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
    const response = await fetch(`${API_URL}/auth/me`, { headers: headersApi() });
    const resultado = await response.json();
    if (response.ok && resultado.success) usuarioActual = resultado.data;
}

// Documentacion: Carga cargar pacientes.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarPacientes() {
    let page = 1;
    let lastPage = 1;
    let pacientes = [];

    do {
        const response = await fetch(`${API_URL}/pacientes?page=${page}`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar pacientes');
        pacientes = pacientes.concat(resultado.data.data ?? []);
        lastPage = resultado.data.last_page ?? 1;
        page++;
    } while (page <= lastPage);

    document.getElementById('filtroPacientePagoProducto').innerHTML = '<option value="">Todos los pacientes</option>' +
        pacientes.map(p => `<option value="${p.id}">${escaparHtml(p.numero_historia)} - ${escaparHtml(p.nombre)} ${escaparHtml(p.apellido)}</option>`).join('');
}

// Documentacion: Carga cargar ventas pago.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarVentasPago() {
    const pacienteId = document.getElementById('filtroPacientePagoProducto').value;
    const estado = document.getElementById('filtroEstadoPagoProducto').value;
    let url = `${API_URL}/inventario/ventas/listado?page=${paginaActual}`;
    if (pacienteId) url += `&paciente_id=${pacienteId}`;
    if (estado) url += `&estado=${estado}`;

    const tabla = document.getElementById('tablaPagoProductos');
    tabla.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Cargando ventas...</td></tr>';

    try {
        const response = await fetch(url, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar ventas');

        const data = resultado.data;
        paginaActual = data.current_page ?? 1;
        ultimaPagina = data.last_page ?? 1;
        renderizarVentasPago(data.data ?? []);
        document.getElementById('infoPagoProductos').textContent = `Pagina ${paginaActual} de ${ultimaPagina} | Total: ${data.total ?? 0} ventas`;
        document.getElementById('btnAnteriorPagoProductos').disabled = paginaActual <= 1;
        document.getElementById('btnSiguientePagoProductos').disabled = paginaActual >= ultimaPagina;
    } catch (error) {
        tabla.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar ventas</td></tr>';
    }
}

// Documentacion: Renderiza renderizar ventas pago.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarVentasPago(ventas) {
    const tabla = document.getElementById('tablaPagoProductos');
    if (!ventas.length) {
        tabla.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay ventas registradas</td></tr>';
        return;
    }

    tabla.innerHTML = ventas.map((venta, index) => `
        <tr>
            <td>${((paginaActual - 1) * 15) + index + 1}</td>
            <td>${venta.paciente ? `${escaparHtml(venta.paciente.nombre)} ${escaparHtml(venta.paciente.apellido)}` : 'Consumidor final'}</td>
            <td>${formatearFechaHora(venta.created_at)}</td>
            <td>${(venta.detalles ?? []).map(d => `${escaparHtml(d.producto?.nombre ?? 'Producto')} x${d.cantidad}`).join('<br>')}</td>
            <td class="fw-bold">${formatearPrecio(venta.total)}</td>
            <td class="fw-bold text-success">${formatearPrecio(venta.monto_pagado)}</td>
            <td class="fw-bold ${Number(venta.saldo_pendiente) > 0 ? 'text-danger' : 'text-success'}">${formatearPrecio(venta.saldo_pendiente)}</td>
            <td>${badgePago(venta.estado)}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-primary" onclick="abrirDetalle(${venta.id})">
                    <i class="bi bi-credit-card me-1"></i> Pagar
                </button>
            </td>
        </tr>
    `).join('');
}

// Documentacion: Ejecuta cambiar pagina.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPagina(direccion) {
    const nueva = paginaActual + direccion;
    if (nueva < 1 || nueva > ultimaPagina) return;
    paginaActual = nueva;
    cargarVentasPago();
}

// Documentacion: Abre abrir detalle.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
async function abrirDetalle(id) {
    try {
        const response = await fetch(`${API_URL}/inventario/ventas/${id}`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo obtener la venta');

        ventaActual = resultado.data;
        renderizarDetalle();
        document.getElementById('vistaListadoPagoProductos').classList.add('d-none');
        document.getElementById('vistaDetallePagoProducto').classList.add('is-visible');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Vuelve desde volver listado.
// Como lo hace: Oculta el panel de detalle y restaura el listado principal.
function volverListado() {
    document.getElementById('vistaDetallePagoProducto').classList.remove('is-visible');
    document.getElementById('vistaListadoPagoProductos').classList.remove('d-none');
    ventaActual = null;
    cargarVentasPago();
}

// Documentacion: Renderiza renderizar detalle.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarDetalle() {
    document.getElementById('detallePaciente').textContent = ventaActual.paciente ? `${ventaActual.paciente.nombre} ${ventaActual.paciente.apellido}` : 'Consumidor final';
    document.getElementById('detalleFecha').textContent = formatearFechaHora(ventaActual.created_at);
    document.getElementById('detalleUsuario').textContent = ventaActual.usuario ? `${ventaActual.usuario.nombre} ${ventaActual.usuario.apellido}` : 'N/A';
    document.getElementById('detalleEstado').innerHTML = badgePago(ventaActual.estado);
    document.getElementById('detalleTotal').textContent = formatearPrecio(ventaActual.total);
    document.getElementById('detallePagado').textContent = formatearPrecio(ventaActual.monto_pagado);
    document.getElementById('detalleSaldo').textContent = formatearPrecio(ventaActual.saldo_pendiente);
    document.getElementById('detalleObservaciones').textContent = ventaActual.observaciones ?? 'Sin observaciones';
    document.getElementById('btnAbrirCobroProducto').disabled = Number(ventaActual.saldo_pendiente) <= 0;

    document.getElementById('tablaDetalleProductos').innerHTML = (ventaActual.detalles ?? []).map(detalle => `
        <tr>
            <td>${escaparHtml(detalle.producto?.nombre ?? 'Producto')}</td>
            <td>${detalle.cantidad}</td>
            <td>${formatearPrecio(detalle.precio_unitario)}</td>
            <td class="fw-bold">${formatearPrecio(detalle.subtotal)}</td>
        </tr>
    `).join('');

    const abonos = ventaActual.abonos ?? [];
    document.getElementById('tablaAbonosProductos').innerHTML = abonos.length
        ? abonos.map(abono => `
            <tr>
                <td>${formatearFechaHora(abono.fecha)}</td>
                <td class="fw-bold text-success">${formatearPrecio(abono.monto)}</td>
                <td>${formatearMetodo(abono.metodo_pago)}</td>
                <td>${abono.usuario ? `${escaparHtml(abono.usuario.nombre)} ${escaparHtml(abono.usuario.apellido)}` : 'N/A'}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="4" class="text-center text-muted py-4">Sin movimientos de pago</td></tr>';
}

// Documentacion: Abre abrir modal cobro producto.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalCobroProducto() {
    if (!ventaActual || Number(ventaActual.saldo_pendiente) <= 0) return;
    document.getElementById('formCobroProducto').reset();
    document.getElementById('saldoActualProducto').textContent = formatearPrecio(ventaActual.saldo_pendiente);
    document.getElementById('grupoVueltoProducto').classList.add('d-none');
    document.getElementById('grupoReferenciaProducto').classList.add('d-none');
    modalCobroProducto = new bootstrap.Modal(document.getElementById('modalCobroProducto'));
    modalCobroProducto.show();
}

// Documentacion: Calcula calcular vuelto.
// Como lo hace: Toma valores numericos de la pantalla y deriva totales, saldos o vuelto.
function calcularVuelto() {
    const monto = Number(document.getElementById('montoCobroProducto').value || 0);
    const vuelto = Math.max(monto - Number(ventaActual?.saldo_pendiente ?? 0), 0);
    document.getElementById('vueltoProducto').textContent = formatearPrecio(vuelto);
    document.getElementById('grupoVueltoProducto').classList.toggle('d-none', vuelto <= 0);
}

// Documentacion: Actualiza actualizar referencia.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarReferencia() {
    const metodo = document.getElementById('metodoPagoProducto').value;
    document.getElementById('grupoReferenciaProducto').classList.toggle('d-none', metodo !== 'transferencia');
    if (metodo !== 'transferencia') document.getElementById('referenciaProducto').value = '';
}

// Documentacion: Guarda guardar cobro producto.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarCobroProducto() {
    if (!usuarioActual || !ventaActual) return;
    const monto = Number(document.getElementById('montoCobroProducto').value || 0);
    const metodo = document.getElementById('metodoPagoProducto').value;
    const referencia = document.getElementById('referenciaProducto').value.trim();

    if (monto <= 0) return alertaError('Ingrese un monto mayor a 0.');
    if (metodo === 'transferencia' && referencia.length < 3) return alertaError('La referencia es requerida.');

    const datos = {
        usuario_id: usuarioActual.id,
        monto,
        metodo_pago: metodo,
        referencia: referencia || null,
        observaciones: document.getElementById('observacionesPagoProducto').value.trim() || null,
    };

    try {
        const response = await fetch(`${API_URL}/inventario/ventas/${ventaActual.id}/cobrar`, {
            method: 'POST',
            headers: headersApi(true),
            body: JSON.stringify(datos)
        });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo registrar el pago';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            return;
        }

        modalCobroProducto.hide();
        const vuelto = Number(resultado.data.vuelto ?? 0);
        Swal.fire({
            icon: 'success',
            title: 'Pago registrado',
            html: vuelto > 0 ? `Vuelto a dar: <strong>${formatearPrecio(vuelto)}</strong>` : 'Pago aplicado correctamente.',
            timer: 2400,
            timerProgressBar: true,
            confirmButtonColor: '#0d6efd'
        });
        await abrirDetalle(ventaActual.id);
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Genera badge para badge pago.
// Como lo hace: Mapea estados internos a clases y textos visuales.
function badgePago(estado) {
    if (estado === 'pagado') return '<span class="pay-status pay-paid"><i class="bi bi-check-circle"></i> Pagado</span>';
    return '<span class="pay-status pay-pending"><i class="bi bi-hourglass-split"></i> Pendiente</span>';
}

// Documentacion: Formatea formatear metodo.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearMetodo(metodo) {
    const metodos = {
        efectivo: '<span class="badge bg-success-subtle text-success">Efectivo</span>',
        transferencia: '<span class="badge bg-primary-subtle text-primary">Transferencia</span>',
        tarjeta: '<span class="badge bg-warning-subtle text-warning">Tarjeta</span>',
    };
    return metodos[metodo] ?? 'N/A';
}

// Documentacion: Formatea formatear fecha hora.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearFechaHora(valor) {
    if (!valor) return 'N/A';
    const fecha = new Date(String(valor).replace(' ', 'T'));
    return fecha.toLocaleDateString('es-EC') + ' ' + fecha.toLocaleTimeString('es-EC', { hour: '2-digit', minute: '2-digit' });
}

// Documentacion: Formatea valores monetarios en dolares.
// Como lo hace: Usa Intl.NumberFormat con moneda USD.
function formatearPrecio(valor) {
    return new Intl.NumberFormat('es-EC', { style: 'currency', currency: 'USD' }).format(Number(valor ?? 0));
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
