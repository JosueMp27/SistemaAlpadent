{{--
    Documentacion de archivo:
    Vista Blade del modulo configuracion; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .config-header {
        margin-bottom: 1.5rem;
    }

    .config-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: .25rem;
    }

    .config-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .config-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .config-card {
        aspect-ratio: 1 / 1;
        border-radius: 8px;
        border: 1px solid #e7edf5;
        padding: 1.1rem;
        color: #172033;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 .35rem 1.1rem rgba(22, 34, 51, .05);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }

    .config-card:hover {
        color: #172033;
        transform: translateY(-3px);
        border-color: #b8d5f5;
        box-shadow: 0 .75rem 1.5rem rgba(22, 34, 51, .09);
    }

    .config-card.odonto {
        background: #eefaf3;
    }

    .config-card.users {
        background: #eef6ff;
    }

    .config-card.treatments {
        background: #fff8e7;
    }

    .config-icon {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        background: rgba(255, 255, 255, .78);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
        margin-bottom: .8rem;
    }

    .config-card.odonto .config-icon {
        color: #198754;
    }

    .config-card.users .config-icon {
        color: #0d6efd;
    }

    .config-card.treatments .config-icon {
        color: #b7791f;
    }

    .config-name {
        font-size: 1.05rem;
        font-weight: 800;
        margin-bottom: .35rem;
    }

    .config-detail {
        color: #5d6878;
        font-size: .86rem;
        line-height: 1.35;
        margin: 0;
    }

    .config-action {
        font-weight: 800;
        color: #334155;
        font-size: .86rem;
    }

    @media (max-width: 1199px) {
        .config-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .config-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .config-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="config-header">
    <h3 class="config-title">Configuracion</h3>
    <p class="config-subtitle">Ajustes administrativos del sistema Alpadent.</p>
</div>

<div class="config-grid" id="configGrid">
    <div class="text-muted">Cargando configuraciones...</div>
</div>

<script>
const API_URL = `${window.location.origin}/api/v1`;

// Documentacion: Inicializa la pantalla cuando el HTML ya esta cargado.
// Como lo hace: registra un listener DOMContentLoaded y llama las funciones que llenan datos iniciales.
document.addEventListener('DOMContentLoaded', cargarConfiguraciones);

// Documentacion: Construye los encabezados para llamar la API.
// Como lo hace: Incluye Accept JSON y el token Bearer guardado en localStorage.
function headersApi() {
    return {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    };
}

// Documentacion: Carga cargar configuraciones.
// Como lo hace: Consulta la API o datos locales y actualiza el estado visual de la pantalla.
async function cargarConfiguraciones() {
    const grid = document.getElementById('configGrid');

    try {
        const response = await fetch(`${API_URL}/configuracion`, { headers: headersApi() });
        const resultado = await response.json();
        if (!response.ok || !resultado.success) throw new Error(resultado.message || 'No se pudieron cargar las configuraciones');

        grid.innerHTML = resultado.data.map(item => `
            <a href="${item.url}" class="config-card ${item.tono}">
                <div>
                    <span class="config-icon"><i class="bi ${item.icono}"></i></span>
                    <h4 class="config-name">${escaparHtml(item.titulo)}</h4>
                    <p class="config-detail">${escaparHtml(item.detalle)}</p>
                </div>
                <span class="config-action">Abrir <i class="bi bi-arrow-right"></i></span>
            </a>
        `).join('');
    } catch (error) {
        grid.innerHTML = `<div class="text-danger">${escaparHtml(error.message)}</div>`;
    }
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
