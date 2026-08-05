<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Documentacion de clase:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 */
class ApiController extends Controller
{
    /**
     * Respuesta exitosa
     */
    /**
     * Documentacion: Ejecuta la operacion success response.
     * Como lo hace: Valida o lee la peticion, usa servicios/modelos y devuelve successResponse/errorResponse.
     */
    public function successResponse($data, $message = 'Operación exitosa', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Respuesta con error
     */
    /**
     * Documentacion: Ejecuta la operacion error response.
     * Como lo hace: Valida o lee la peticion, usa servicios/modelos y devuelve successResponse/errorResponse.
     */
    public function errorResponse($message = 'Error en la operación', $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta no encontrado
     */
    /**
     * Documentacion: Ejecuta la operacion not found response.
     * Como lo hace: Valida o lee la peticion, usa servicios/modelos y devuelve successResponse/errorResponse.
     */
    public function notFoundResponse($message = 'Recurso no encontrado')
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Respuesta no autorizado
     */
    /**
     * Documentacion: Ejecuta la operacion unauthorized response.
     * Como lo hace: Valida o lee la peticion, usa servicios/modelos y devuelve successResponse/errorResponse.
     */
    public function unauthorizedResponse($message = 'No autorizado')
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Respuesta validación fallida
     */
    /**
     * Documentacion: Ejecuta la operacion validation failed response.
     * Como lo hace: Valida o lee la peticion, usa servicios/modelos y devuelve successResponse/errorResponse.
     */
    public function validationFailedResponse($errors)
    {
        return $this->errorResponse('Validación fallida', 422, $errors);
    }
}
