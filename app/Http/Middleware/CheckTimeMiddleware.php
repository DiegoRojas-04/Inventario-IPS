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

        // Verificar si es el día y la hora permitidos para realizar pedidos normales
        $currentDay = now()->dayOfWeek; // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
        $allowOrder = ($currentDay === 3 && now()->hour >= 6 && now()->hour < 18); // Jueves de 6 AM a 4 PM

        if (!$allowOrder) {
            return redirect()->route('home')->with('error', 'No es momento de realizar pedidos.');
        }

        return $next($request);
    }
}
