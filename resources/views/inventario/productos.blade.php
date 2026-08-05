{{--
    Documentacion de archivo:
    Vista Blade del modulo inventario; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .inventory-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 22px;
    }

    .inventory-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: 4px;
    }

    .inventory-card {
        border: 0;
        border-radius: 8px;
    }

    .product-toolbar {
        border-radius: 8px;
        border: 1px solid #e6eef8;
        background: #f7fafe;
        padding: 16px;
    }

    .product-table thead th {
        background: #dfeaff;
        color: #174a7c;
        font-size: .84rem;
        white-space: nowrap;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 7px;
        padding: 6px 9px;
        font-weight: 800;
        font-size: .78rem;
    }

    .stock-ok {
        background: #dcf8e8;
        color: #11733d;
    }

    .stock-low {
        background: #fff3cd;
        color: #9a6700;
    }

    .stock-empty {
        background: #ffe4e8;
        color: #b42331;
    }
</style>

<div class="inventory-header">
    <div>
        <h3 class="inventory-title">Productos</h3>
        <p class="text-muted mb-0">CRUD de productos odontologicos y control rapido de stock</p>
    </div>

    <button type="button" class="btn btn-primary" onclick="abrirModalProducto()">
        <i class="bi bi-plus-circle me-1"></i> Nuevo producto
    </button>
</div>

<div class="product-toolbar mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-7">
            <label class="form-label">Buscar producto</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="busquedaProducto" class="form-control" placeholder="Nombre, marca o descripcion">
            </div>
        </div>

        <div class="col-md-5 d-flex gap-2">
            <button type="button" class="btn btn-primary flex-fill" onclick="buscarProductos()">
                <i class="bi bi-search me-1"></i> Buscar
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="limpiarBusquedaProductos()">
                Limpiar
            </button>
        </div>
    </div>
</div>

<div class="card inventory-card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-box-seam text-primary me-2"></i> Listado de productos
            </h5>
            <small class="text-muted" id="infoProductos">Sin datos</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle product-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Marca</th>
                        <th>Descripcion</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Minimo</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaProductos">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Cargando productos...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <button class="btn btn-outline-secondary btn-sm" id="btnAnteriorProductos" onclick="cambiarPaginaProductos(-1)" disabled>Anterior</button>
            <button class="btn btn-outline-secondary btn-sm" id="btnSiguienteProductos" onclick="cambiarPaginaProductos(1)" disabled>Siguiente</button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalProductoLabel">Nuevo producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formProducto">
                    <input type="hidden" id="productoId">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="productoNombre" class="form-control" maxlength="150">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca</label>
                            <input type="text" id="productoMarca" class="form-control" maxlength="100">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripcion</label>
                            <textarea id="productoDescripcion" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Precio de venta</label>
                            <input type="number" id="productoPrecio" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock actual</label>
                            <input type="number" id="productoStock" class="form-control" min="0" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock minimo</label>
                            <input type="number" id="productoStockMinimo" class="form-control" min="0" step="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarProducto()">
                    <i class="bi bi-floppy me-1"></i> Guardar producto
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;
let paginaProductos = 1;
let ultimaPaginaProductos = 1;
let modalProducto = null;
let productosCache = [];

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', () => {
    cargarProductos();
    document.getElementById('busquedaProducto').addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            buscarProductos();
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

// Documentacion: Busca buscar productos.
// Como lo hace: Actualiza filtros de la pantalla y vuelve a consultar la API.
function buscarProductos() {
    paginaProductos = 1;
    cargarProductos();
}

// Documentacion: Limpia limpiar busqueda productos.
// Como lo hace: Reinicia campos o filtros y vuelve al estado base de la pantalla.
function limpiarBusquedaProductos() {
    document.getElementById('busquedaProducto').value = '';
    paginaProductos = 1;
    cargarProductos();
}

// Documentacion: Carga cargar productos.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarProductos() {
    const tabla = document.getElementById('tablaProductos');
    const search = document.getElementById('busquedaProducto').value.trim();
    tabla.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Cargando productos...</td></tr>';

    let url = `${API_URL}/inventario/productos?page=${paginaProductos}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    try {
        const response = await fetch(url, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar los productos');

        const data = resultado.data;
        paginaProductos = data.current_page ?? 1;
        ultimaPaginaProductos = data.last_page ?? 1;
        renderizarProductos(data.data ?? []);
        actualizarPaginacion(data.total ?? 0);
    } catch (error) {
        tabla.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar productos</td></tr>';
        alertaError(error.message);
    }
}

// Documentacion: Renderiza renderizar productos.
// Como lo hace: Construye HTML dinamico a partir de arreglos de datos y lo inyecta en el contenedor correspondiente.
function renderizarProductos(productos) {
    const tabla = document.getElementById('tablaProductos');
    productosCache = productos;

    if (!productos.length) {
        tabla.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay productos registrados</td></tr>';
        return;
    }

    tabla.innerHTML = productos.map((producto, index) => `
        <tr>
            <td>${((paginaProductos - 1) * 20) + index + 1}</td>
            <td class="fw-bold">${escaparHtml(producto.nombre)}</td>
            <td>${escaparHtml(producto.marca ?? 'N/A')}</td>
            <td>${escaparHtml(producto.descripcion ?? '')}</td>
            <td class="fw-bold">${formatearPrecio(producto.precio_venta)}</td>
            <td>${producto.stock_actual}</td>
            <td>${producto.stock_minimo}</td>
            <td>${badgeStock(producto)}</td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editarProducto(${producto.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="eliminarProducto(${producto.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Documentacion: Actualiza actualizar paginacion.
// Como lo hace: Sincroniza controles, calculos o etiquetas segun el estado actual de la interfaz.
function actualizarPaginacion(total) {
    document.getElementById('infoProductos').textContent = `Pagina ${paginaProductos} de ${ultimaPaginaProductos} | Total: ${total} productos`;
    document.getElementById('btnAnteriorProductos').disabled = paginaProductos <= 1;
    document.getElementById('btnSiguienteProductos').disabled = paginaProductos >= ultimaPaginaProductos;
}

// Documentacion: Ejecuta cambiar pagina productos.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function cambiarPaginaProductos(direccion) {
    const nueva = paginaProductos + direccion;
    if (nueva < 1 || nueva > ultimaPaginaProductos) return;
    paginaProductos = nueva;
    cargarProductos();
}

// Documentacion: Abre abrir modal producto.
// Como lo hace: Prepara campos, estado o datos y muestra el modal o panel solicitado por el usuario.
function abrirModalProducto() {
    document.getElementById('formProducto').reset();
    document.getElementById('productoId').value = '';
    document.getElementById('modalProductoLabel').textContent = 'Nuevo producto';
    modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
    modalProducto.show();
}

// Documentacion: Ejecuta editar producto.
// Como lo hace: Usa el estado local de esta vista y operaciones DOM/fetch para completar la accion del modulo.
function editarProducto(productoId) {
    const producto = productosCache.find(item => Number(item.id) === Number(productoId));
    if (!producto) return alertaError('Producto no encontrado.');

    document.getElementById('productoId').value = producto.id;
    document.getElementById('productoNombre').value = producto.nombre ?? '';
    document.getElementById('productoMarca').value = producto.marca ?? '';
    document.getElementById('productoDescripcion').value = producto.descripcion ?? '';
    document.getElementById('productoPrecio').value = producto.precio_venta ?? 0;
    document.getElementById('productoStock').value = producto.stock_actual ?? 0;
    document.getElementById('productoStockMinimo').value = producto.stock_minimo ?? 0;
    document.getElementById('modalProductoLabel').textContent = 'Editar producto';
    modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
    modalProducto.show();
}

// Documentacion: Guarda guardar producto.
// Como lo hace: Lee el formulario, valida datos minimos, envia fetch a la API y refresca la vista al terminar.
async function guardarProducto() {
    const id = document.getElementById('productoId').value;
    const datos = {
        nombre: document.getElementById('productoNombre').value.trim(),
        marca: document.getElementById('productoMarca').value.trim() || null,
        descripcion: document.getElementById('productoDescripcion').value.trim() || null,
        precio_venta: Number(document.getElementById('productoPrecio').value || 0),
        stock_actual: Number(document.getElementById('productoStock').value || 0),
        stock_minimo: Number(document.getElementById('productoStockMinimo').value || 0),
    };

    if (datos.nombre.length < 2) return alertaError('El nombre debe tener al menos 2 caracteres.');
    if (datos.precio_venta < 0) return alertaError('El precio no puede ser negativo.');

    try {
        const response = await fetch(id ? `${API_URL}/inventario/productos/${id}` : `${API_URL}/inventario/productos`, {
            method: id ? 'PUT' : 'POST',
            headers: headersApi(true),
            body: JSON.stringify(datos)
        });
        const resultado = await response.json();

        if (!response.ok || !resultado.success) {
            let mensaje = resultado.message || 'No se pudo guardar el producto';
            if (resultado.errors) mensaje = Object.values(resultado.errors).flat().join('<br>');
            Swal.fire({ icon: 'error', title: 'Error', html: mensaje });
            return;
        }

        modalProducto.hide();
        alertaExito(resultado.message);
        cargarProductos();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Elimina eliminar producto.
// Como lo hace: Confirma la accion, llama la API y actualiza el listado para reflejar el cambio.
async function eliminarProducto(id) {
    const confirmacion = await Swal.fire({
        icon: 'warning',
        title: 'Eliminar producto',
        text: 'El producto se desactivara del inventario.',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const response = await fetch(`${API_URL}/inventario/productos/${id}`, {
            method: 'DELETE',
            headers: headersApi()
        });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudo eliminar');

        alertaExito(resultado.message);
        cargarProductos();
    } catch (error) {
        alertaError(error.message);
    }
}

// Documentacion: Genera badge para badge stock.
// Como lo hace: Mapea estados internos a clases y textos visuales.
function badgeStock(producto) {
    if (Number(producto.stock_actual) <= 0) {
        return '<span class="stock-badge stock-empty"><i class="bi bi-x-circle"></i> Sin stock</span>';
    }

    if (Number(producto.stock_actual) <= Number(producto.stock_minimo)) {
        return '<span class="stock-badge stock-low"><i class="bi bi-exclamation-triangle"></i> Stock bajo</span>';
    }

    return '<span class="stock-badge stock-ok"><i class="bi bi-check-circle"></i> Disponible</span>';
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
