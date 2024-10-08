<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicios = Servicio::all(); // Obtener todos los servicios
        $users = User::with('servicio')->get(); // Eager load 'servicio'
        return view('admin.usuarios', compact('users', 'servicios'));
    }


    public function asignarRol($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Obtener todos los roles disponibles
        return view('admin.userRol', compact('user', 'roles'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $servicios = Servicio::all(); // Obtener todos los servicios
        return view('admin.usuarios', compact('servicios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'servicio_id' => 'nullable|exists:servicios,id', // Validar que el servicio exista
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'servicio_id' => $request->servicio_id, // Asociar servicio
        ]);

        return redirect()->back()->with('success', 'Usuario creado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $servicios = Servicio::all(); // Obtener todos los servicios
        Cache::flush();

        // Comprobar si el usuario autenticado es el mismo que el que se está editando
        $isEditingOwnProfile = auth()->user()->id === $user->id;

        return view('admin.editUsuario', compact('user', 'roles', 'servicios', 'isEditingOwnProfile'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'servicio_id' => 'nullable|exists:servicios,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Actualizar información del usuario
        $user->name = $request->name;
        $user->email = $request->email;
        $user->servicio_id = $request->servicio_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Comparar los IDs para determinar si es el mismo usuario
        if (auth()->user()->id !== $user->id) {
            // Obtener nombres de los roles a partir de los IDs o asignar "Usuario" si no se seleccionó ninguno
            $roleNames = $request->roles ? Role::whereIn('id', $request->roles)->pluck('name')->toArray() : ['Usuario'];
            $user->syncRoles($roleNames); // Sincroniza roles usando los nombres
        }

        return redirect('usuario')->with('Mensaje2', 'Usuario actualizado y roles sincronizados');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
