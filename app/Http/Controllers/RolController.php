<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
//  implements HasMiddleware
{
    // public static function middleware(): array
    // {
    //     return [
    //         'auth',
    //         new Middleware('can:insumo'),
    //         // new Middleware('Administrador', except: ['store']),
    //     ];
    // }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->input('nombre')]);
        Cache::flush();
        return back();
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
        $role = Role::find($id);
        $permisos = Permission::all();
        Cache::flush();
        return view('admin.rolePermiso', compact('role', 'permisos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        $role->permissions()->sync($request->input('permissions'));
        Cache::flush();
        return redirect()->back();
    }
    
    
    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
