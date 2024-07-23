<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckTimeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si es el día y la hora permitidos para realizar pedidos
        // Por ejemplo, permitir los pedidos solo los jueves entre las 6 AM y las 4 PM
        $currentDay = now()->dayOfWeek; // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
        $allowOrder = ($currentDay === 2 && now()->hour >= 6 && now()->hour < 16);

        if (!$allowOrder) {
            return redirect()->route('home')->with('error', 'No es momento de realizar pedidos.');
        }

        return $next($request);
    }
}
