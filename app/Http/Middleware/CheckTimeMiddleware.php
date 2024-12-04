<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTimeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si es un pedido especial
        if ($request->has('especial') && $request->get('especial') == 'true') {
            return $next($request); // Permitir el pedido especial sin restricciones
        }

        // Obtener el día y la hora actuales
        $currentDay = now()->dayOfWeek; // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
        $currentHour = now()->hour;
        $currentMinute = now()->minute;

        // Permitir pedidos desde el miércoles (día 3) a las 12 PM hasta el jueves (día 4) a las 4 PM
        $allowOrder = (
            ($currentDay === 3 && $currentHour >= 8) || // Miércoles desde las 12 PM
            ($currentDay === 4 && $currentHour < 16) // Jueves hasta las 4 PM
        );

        if (!$allowOrder) {
            return redirect()->route('home')->with('error', 'No es momento de realizar pedidos.');
        }

        return $next($request);
    }
}
