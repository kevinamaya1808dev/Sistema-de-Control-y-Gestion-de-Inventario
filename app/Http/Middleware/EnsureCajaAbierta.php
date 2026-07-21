<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCajaAbierta
{
    /**
     * Maneja una solicitud entrante.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si no está autenticado, pasa a la siguiente capa
        if (! $user) {
            return $next($request);
        }

        // El Administrador (ID 1 o por método isAdmin) tiene acceso maestro
        if ($user->id === 1 || $user->isAdmin()) {
            return $next($request);
        }

        // Para cualquier otro usuario, verificar que tenga una caja activa
        if (! $user->cajaActiva()) {
            return redirect()->route('caja.index')->with('error', 'Debes abrir el turno de caja para realizar movimientos u operaciones.');
        }

        return $next($request);
    }
}
