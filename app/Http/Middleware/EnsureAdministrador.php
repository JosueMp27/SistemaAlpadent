<?php

/**
 * Documentacion de archivo:
 * Middleware que se ejecuta dentro del pipeline HTTP para proteger o ajustar respuestas antes de continuar.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Documentacion de clase:
 * Middleware que se ejecuta dentro del pipeline HTTP para proteger o ajustar respuestas antes de continuar.
 */
class EnsureAdministrador
{
    /**
     * Documentacion: Procesa la peticion dentro del middleware.
     * Como lo hace: Evalua permisos o ajusta headers antes de entregar la peticion al siguiente paso.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (!$usuario || $usuario->rol !== 'administrador') {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para acceder a esta seccion.',
            ], 403);
        }

        return $next($request);
    }
}
