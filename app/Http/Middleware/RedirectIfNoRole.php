<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNoRole
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Verifica si el usuario está autenticado y tiene roles
        if ($user && $user->roles->count() > 0) {
            return $next($request);
        }

        // Redirige a la página de inicio si no tiene roles
        return redirect('/home');
    }
}

