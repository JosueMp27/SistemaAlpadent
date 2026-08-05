<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Controllers\Api;

/**
 * Documentacion de clase:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 */
class ConfiguracionController extends ApiController
{
    /**
     * Documentacion: Lista registros del recurso principal.
     * Como lo hace: Construye una consulta, aplica filtros de la peticion y devuelve una coleccion paginada.
     */
    public function index()
    {
        return $this->successResponse([
            [
                'titulo' => 'Configuracion de odontograma',
                'detalle' => 'Administre colores, nombres y significados de las marcas dentales.',
                'icono' => 'bi-palette',
                'url' => '/configuracion/odontograma',
                'tono' => 'odonto',
            ],
            [
                'titulo' => 'Usuarios del sistema',
                'detalle' => 'Cree, edite, desactive y reactive administradores o secretarias/asistentes.',
                'icono' => 'bi-person-gear',
                'url' => '/configuracion/usuarios',
                'tono' => 'users',
            ],
            [
                'titulo' => 'Tratamientos y precios',
                'detalle' => 'Configure tratamientos, categorias, precios y disponibilidad para citas y pagos.',
                'icono' => 'bi-clipboard2-pulse',
                'url' => '/configuracion/tratamientos',
                'tono' => 'treatments',
            ],
        ], 'Configuraciones obtenidas correctamente');
    }
}
