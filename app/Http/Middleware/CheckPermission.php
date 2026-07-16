<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // El Administrador siempre pasa sin importar sus permisos asignados
        if ($user->role && $user->role->name === 'Administrador') {
            return $next($request);
        }

        // Operadores u otros roles evalúan sus permisos asignados
        if ($user->hasPermissionTo($permission)) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'No cuentas con los permisos requeridos para acceder.');
    }
}