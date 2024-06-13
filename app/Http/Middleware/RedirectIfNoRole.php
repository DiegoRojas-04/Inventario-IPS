<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNoRole
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Verificar si el usuario está autenticado
        if ($user) {
            // Verificar si el usuario tiene roles asignados
            if ($user->roles->count() > 0) {
                // Si el usuario tiene roles, permitir acceso a todas las rutas protegidas por roles
                return $next($request);
            } else {
                // Si el usuario no tiene roles asignados, verificar si la solicitud es para crear o almacenar un pedido
                if ($request->routeIs('pedido.create') || $request->routeIs('pedido.store')) {
                    // Permitir acceso a la creación y almacenamiento de pedidos
                    return $next($request);
                } else {
                    // Redirigir a la página de inicio si no tiene roles y no está intentando crear o almacenar un pedido
                    return redirect('/home')->with('error', 'No tienes permiso para acceder a esta página.');
                }
            }
        }

        // Si el usuario no está autenticado, redirigirlo a la página de inicio de sesión
        return redirect('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
    }
}
