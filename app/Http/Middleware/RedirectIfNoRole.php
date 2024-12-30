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
                // Permitir acceso a la creación y almacenamiento de pedidos si tiene cualquier rol
                if ($request->routeIs('pedido.create') || $request->routeIs('pedido.store')) {
                    return $next($request);
                }

                // Comprobar si tiene el rol de 'Administrador'
                if ($user->roles->contains('name', 'Administrador')) {
                    // Si tiene el rol de Administrador, permitir acceso
                    return $next($request);
                }

                // Comprobar si tiene el rol de 'Laboratorio' y si la ruta es permitida
                if ($user->roles->contains('name', 'Laboratorio') && $this->isLaboratorioRoute($request)) {
                    // Si es una ruta permitida para Laboratorio, permitir acceso
                    return $next($request);
                }

                // Si no tiene el rol de Administrador ni Laboratorio, redirigir a la página de inicio
                return redirect('/home')->with('error', 'No tienes permiso para acceder a esta página.');
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

    // Método para verificar si la ruta es accesible para el rol Laboratorio
    private function isLaboratorioRoute($request)
    {
        // Especifica las rutas permitidas para el rol 'Laboratorio'
        $allowedRoutes = [
            'compra.*',
            'entrega.*',
            'kardex.*',
            'insumo.*',
        ];

        // Verifica si la ruta actual coincide con alguna de las rutas permitidas
        foreach ($allowedRoutes as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }
}
