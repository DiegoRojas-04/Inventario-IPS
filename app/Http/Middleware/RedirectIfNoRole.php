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
            if ($user->roles->isNotEmpty()) {
                // Comprobar si tiene el rol de 'Administrador'
                if ($user->roles->contains('name', 'Administrador')) {
                    // Si tiene el rol de Administrador, permitir acceso
                    return $next($request);
                } else {
                    // Si no tiene el rol de Administrador, redirigir a la página de inicio
                    return redirect('/home')->with('error', 'No tienes permiso para acceder a esta página.');
                }
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
