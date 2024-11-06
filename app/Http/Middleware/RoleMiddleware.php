<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado. Debes iniciar sesión.'], 401);
        }

        // Verificar si el usuario tiene el rol adecuado
        if (!Auth::user()->role || Auth::user()->role->role_name !== $role) {
            return response()->json(['message' => 'Acceso no autorizado. No tienes el rol adecuado.'], 403);
        }

        return $next($request);
    }
}
