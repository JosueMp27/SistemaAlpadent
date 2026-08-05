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
class TestController extends ApiController
{
    /**
     * Documentacion: Verifica que la API responda.
     * Como lo hace: Devuelve una respuesta exitosa simple para probar conectividad.
     */
    public function test()
    {
        return $this->successResponse(['message' => 'API está funcionando correctamente'], 'Test exitoso');
    }
}
