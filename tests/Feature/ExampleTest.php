<?php

/**
 * Documentacion de archivo:
 * Prueba automatizada; verifica comportamiento basico de la aplicacion durante ejecuciones de PHPUnit.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Documentacion de clase:
 * Prueba automatizada; verifica comportamiento basico de la aplicacion durante ejecuciones de PHPUnit.
 */
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    /**
     * Documentacion: Verifica que la API responda.
     * Como lo hace: Devuelve una respuesta exitosa simple para probar conectividad.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
