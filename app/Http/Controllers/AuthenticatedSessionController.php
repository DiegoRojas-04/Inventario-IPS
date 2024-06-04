<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthenticatedSessionController extends Controller
{
    // Otros métodos del controlador...

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {

        if ($user->hasRole('Administrador')) {
            return redirect('/dashboard')->intended('/dashboard');
        } elseif ($user->hasRole('Usuario')) {
            return redirect('/kardex')->intended('/kardex');
        } else {
            // Si el usuario no tiene un rol específico, puedes redirigirlo a una página por defecto
            return redirect()->intended('/');
        }
    }
}
