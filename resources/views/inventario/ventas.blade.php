{{--
    Documentacion de archivo:
    Vista Blade del modulo inventario; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .sales-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 22px;
    }

    .sales-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: 4px;
    }

    .sales-card {
        border: 0;
        border-radius: 8px;
    }

    .sales-panel {
        border-radius: 8px;
        border: 1px solid #e6eef8;
        background: #fff;
        padding: 16px;
    }

    .cart-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 90px 110px 110px 40px;
        gap: 8px;
        align-items: center;
        border-bottom: 1px solid #edf1f6;
        padding: 10px 0;
    }

    .cart-row:last-child {
        border-bottom: 0;
    }

    .sales-table thead th {
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

    .total-box {
        background: #f7fafe;
        border: 1px solid #dbe8f7;
        border-radius: 8px;
        padding: 14px;
    }

    @media (max-width: 767.98px) {
        .cart-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="sales-header">
    <div>
        <h3 class="sales-title">Ventas de productos</h3>
        <p class="text-muted mb-0">Selecciona paciente, productos y confirma la compra</p>
    </div>

    <button type="button" class="btn btn-outline-primary" onclick="cargarVentas()">
        <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-5">
        <div class="sales-panel shadow-sm">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-cart-plus text-primary me-2"></i> Nueva venta
            </h5>

            <div class="mb-3">
                <label class="form-label">Paciente</label>
                <select id="pacienteVenta" class="form-select">
                    <option value="">Consumidor final</option>
                </select>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-8">
                    <label class="form-label">Producto</label>
                    <select id="productoVenta" class="form-select" onchange="actualizarProductoSeleccionado()">
                        <option value="">Seleccione producto</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="cantidadVenta" class="form-control" min="1" value="1">
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="agregarProductoVenta()">
                        <i class="bi bi-plus-circle me-1"></i> Agregar producto
                    </button>
                </div>
            </div>

            <div id="carritoVenta" class="mb-3">
                <div class="text-center text-muted py-3">No hay productos agregados</div>
            </div>

            <div class="total-box mb-3">
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total de la venta</span>
                    <span class="fw-bold fs-5" id="totalVenta">$0.00</span>
                </div>
                <small class="text-muted">El pago quedara pendiente en Pago de productos.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea id="observacionesVenta" class="form-control" rows="2" maxlength="500"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success flex-fill" onclick="confirmarVenta()">
                    <i class="bi bi-check2-circle me-1"></i> Confirmar compra
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="cancelarVenta()">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card sales-card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-receipt text-primary me-2"></i> Historial de ventas
                    </h5>
                    <small class="text-muted" id="infoVentas">Sin datos</small>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-5">
                        <select id="filtroPacienteVenta" class="form-select" onchange="paginaVentas = 1; cargarVentas();">
                            <option value="">Todos los pacientes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="filtroEstadoVenta" class="form-select" onchange="paginaVentas = 1; cargarVentas();">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="parcial">Parcial</option>
                            <option value="pagado">Pagado</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle sales-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Pago</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="tablaVentas">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Cargando ventas...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button class="btn btn-outline-secondary btn-sm" id="btnAnteriorVentas" onclick="cambiarPaginaVentas(-1)" disabled>Anterior</button>
                    <button class="btn btn-outline-secondary btn-sm" id="btnSiguienteVentas" onclick="cambiarPaginaVentas(1)" disabled>Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;
let usuarioActual = null;
let productosDisponibles = [];
let carrito = [];
let paginaVentas = 1;
let ultimaPaginaVentas = 1;

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', async () => {
    await obtenerUsuarioActual();
    await Promise.all([cargarPacientes(), cargarProductosDisponibles()]);
    await cargarVentas();
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

    const opciones = pacientes.map(p => `<option value="${p.id}">${escaparHtml(p.numero_historia)} - ${escaparHtml(p.nombre)} ${escaparHtml(p.apellido)}</option>`).join('');
    document.getElementById('pacienteVenta').innerHTML = '<option value="">Consumidor final</option>' + opciones;
    document.getElementById('filtroPacienteVenta').innerHTML = '<option value="">Todos los pacientes</option>' + opciones;
}

// Documentacion: Carga cargar productos disponibles.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarProductosDisponibles() {
    let page = 1;
    let lastPage = 1;
    let productos = [];

    do {
        const response = await fetch(`${API_URL}/inventario/productos?page=${page}`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar productos');

        productos = productos.concat(resultado.data.data ?? []);
        lastPage = resultado.data.last_page ?? 1;
        page++;
    } while (page <= lastPage);

    productosDisponibles = productos;
    document.getElementById('productoVenta').innerHTML = '<option value="">Seleccione producto</option>' +
        productosDisponibles.map(p => `
            <option value="${p.id}" ${Number(p.stock_actual) <= 0 ? 'disabled' : ''}>
                ${escaparHtml(p.nombre)} - ${formatearPrecio(p.precio_venta)} | Stock: ${p.stock_actual}
            </option>
        `).join('');
}

// Documentacion: Actualiza actualizar producto seleccionado.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarProductoSeleccionado() {
    document.getElementById('cantidadVenta').value = 1;
}

// Documentacion: Ejecuta agregar producto venta.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function agregarProductoVenta() {
    const productoId = Number(document.getElementById('productoVenta').value);
    const cantidad = Number(document.getElementById('cantidadVenta').value || 0);
    const producto = productosDisponibles.find(p => Number(p.id) === productoId);

    if (!producto) return alertaError('Seleccione un producto.');
    if (cantidad <= 0) return alertaError('La cantidad debe ser mayor a 0.');
    if (cantidad > Number(producto.stock_actual)) return alertaError('No hay stock suficiente.');

    const existente = carrito.find(item => Number(item.producto_id) === productoId);
    const cantidadActual = existente ? existente.cantidad : 0;

    if ((cantidadActual + cantidad) > Number(producto.stock_actual)) {
        return alertaError('La cantidad total supera el stock disponible.');
    }

    if (existente) {
        existente.cantidad += cantidad;
        existente.subtotal = existente.cantidad * existente.precio_unitario;
    } else {
        carrito.push({
            producto_id: producto.id,
            nombre: producto.nombre,
            stock_actual: producto.stock_actual,
            cantidad,
            precio_unitario: Number(producto.precio_venta),
            subtotal: cantidad * Number(producto.precio_venta),
        });
    }

    renderizarCarrito();
}

// Documentacion: Renderiza renderizar carrito.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarCarrito() {
    const contenedor = document.getElementById('carritoVenta');

    if (!carrito.length) {
        contenedor.innerHTML = '<div class="text-center text-muted py-3">No hay productos agregados</div>';
        document.getElementById('totalVenta').textContent = formatearPrecio(0);
        return;
    }

    contenedor.innerHTML = carrito.map((item, index) => `
        <div class="cart-row">
            <div>
                <strong>${escaparHtml(item.nombre)}</strong>
                <small class="text-muted d-block">Stock disponible: ${item.stock_actual}</small>
            </div>
            <input type="number" class="form-control form-control-sm" min="1" max="${item.stock_actual}" value="${item.cantidad}" onchange="actualizarCantidadCarrito(${index}, this.value)">
            <span>${formatearPrecio(item.precio_unitario)}</span>
            <strong>${formatearPrecio(item.subtotal)}</strong>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="quitarProductoCarrito(${index})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `).join('');

    document.getElementById('totalVenta').textContent = formatearPrecio(calcularTotalCarrito());
}

// Documentacion: Actualiza actualizar cantidad carrito.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarCantidadCarrito(index, valor) {
    const cantidad = Number(valor || 0);
    if (cantidad <= 0 || cantidad > Number(carrito[index].stock_actual)) return renderizarCarrito();
    carrito[index].cantidad = cantidad;
    carrito[index].subtotal = cantidad * carrito[index].precio_unitario;
    renderizarCarrito();
}

// Documentacion: Ejecuta quitar producto carrito.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function quitarProductoCarrito(index) {
    carrito.splice(index, 1);
    renderizarCarrito();
}

// Documentacion: Calcula calcular total carrito.
// Como lo hace: Toma valores numericos de la pantalla y deriva totales, saldos o vuelto.
function calcularTotalCarrito() {
    return carrito.reduce((total, item) => total + Number(item.subtotal), 0);
}

// Documentacion: Ejecuta confirmar venta.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
async function confirmarVenta() {
    if (!usuarioActual) return alertaError('No se pudo identificar el usuario actual.');
    if (!carrito.length) return alertaError('Agregue al menos un producto.');

    const confirmacion = await Swal.fire({
        icon: 'question',
        title: 'Confirmar compra',
        html: `Total: <strong>${formatearPrecio(calcularTotalCarrito())}</strong><br>El saldo se gestionara en Pago de productos.`,
        showCancelButton: true,
        confirmButtonText: 'Confirmar compra',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#198754'
    });
    if (!confirmacion.isConfirmed) return;

    const datos = {
        paciente_id: document.getElementById('pacienteVenta').value || null,
        usuario_id: usuarioActual.id,
        observaciones: document.getElementById('observacionesVenta').value.trim() || null,
        productos: carrito.map(item => ({ producto_id: item.producto_id, cantidad: item.cantidad }))
    };

    try {
        const response = await fetch(`${API_URL}/inventario/venta`, {
            method: 'POST',
            headers: headersApi(true),
            body: JSON.stringify(datos)
        });
        const resultado = await response.json();

        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo registrar la venta');

        alertaExito('Venta registrada correctamente');
        cancelarVenta();
        await cargarProductosDisponibles();
        await cargarVentas();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Ejecuta cancelar venta.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cancelarVenta() {
    carrito = [];
    document.getElementById('pacienteVenta').value = '';
    document.getElementById('productoVenta').value = '';
    document.getElementById('cantidadVenta').value = 1;
    document.getElementById('observacionesVenta').value = '';
    renderizarCarrito();
}

// Documentacion: Carga cargar ventas.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarVentas() {
    const pacienteId = document.getElementById('filtroPacienteVenta').value;
    const estado = document.getElementById('filtroEstadoVenta').value;
    let url = `${API_URL}/inventario/ventas/listado?page=${paginaVentas}`;
    if (pacienteId) url += `&paciente_id=${pacienteId}`;
    if (estado) url += `&estado=${estado}`;

    const tabla = document.getElementById('tablaVentas');
    tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Cargando ventas...</td></tr>';

    try {
        const response = await fetch(url, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar ventas');

        const data = resultado.data;
        paginaVentas = data.current_page ?? 1;
        ultimaPaginaVentas = data.last_page ?? 1;
        renderizarVentas(data.data ?? []);
        document.getElementById('infoVentas').textContent = `Pagina ${paginaVentas} de ${ultimaPaginaVentas} | Total: ${data.total ?? 0} ventas`;
        document.getElementById('btnAnteriorVentas').disabled = paginaVentas <= 1;
        document.getElementById('btnSiguienteVentas').disabled = paginaVentas >= ultimaPaginaVentas;
    } catch (error) {
        tabla.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Error al cargar ventas</td></tr>';
    }
}

// Documentacion: Renderiza renderizar ventas.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarVentas(ventas) {
    const tabla = document.getElementById('tablaVentas');

    if (!ventas.length) {
        tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay ventas registradas</td></tr>';
        return;
    }

    tabla.innerHTML = ventas.map((venta, index) => `
        <tr>
            <td>${((paginaVentas - 1) * 15) + index + 1}</td>
            <td>${venta.paciente ? `${escaparHtml(venta.paciente.nombre)} ${escaparHtml(venta.paciente.apellido)}` : 'Consumidor final'}</td>
            <td>${formatearFechaHora(venta.created_at)}</td>
            <td>${(venta.detalles ?? []).map(d => `${escaparHtml(d.producto?.nombre ?? 'Producto')} x${d.cantidad}`).join('<br>')}</td>
            <td class="fw-bold">${formatearPrecio(venta.total)}</td>
            <td>${badgePago(venta.estado)}</td>
            <td class="fw-bold ${Number(venta.saldo_pendiente) > 0 ? 'text-danger' : 'text-success'}">${formatearPrecio(venta.saldo_pendiente)}</td>
        </tr>
    `).join('');
}

// Documentacion: Ejecuta cambiar pagina ventas.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPaginaVentas(direccion) {
    const nueva = paginaVentas + direccion;
    if (nueva < 1 || nueva > ultimaPaginaVentas) return;
    paginaVentas = nueva;
    cargarVentas();
}

// Documentacion: Genera badge para badge pago.
// Como lo hace: Mapea estados internos a clases y textos visuales.
function badgePago(estado) {
    if (estado === 'pagado') return '<span class="pay-status pay-paid"><i class="bi bi-check-circle"></i> Pagado</span>';
    return '<span class="pay-status pay-pending"><i class="bi bi-hourglass-split"></i> Pendiente</span>';
}

// Documentacion: Formatea formatear fecha hora.
// Como lo hace: Convierte valores internos en texto legible para tablas, badges o controles.
function formatearFechaHora(valor) {
    if (!valor) return 'N/A';
    const fecha = new Date(valor.replace(' ', 'T'));
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
