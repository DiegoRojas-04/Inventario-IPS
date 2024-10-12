<?php

namespace App\Http\Controllers;

use App\Models\CategoriaActivo;
use Illuminate\Http\Request;

class CategoriaActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categorias = CategoriaActivo::orderBy('nombre', 'asc')
            ->paginate($request->input('page_size', 15));

        return view('crud.activo.categoriaIndex', compact('categorias'));
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
        // Validar los datos de entrada
        $request->validate([
            'nombre' => 'required|string|unique:categoria_activos|max:255', 
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Insertar la nueva categoría en la base de datos
        $datosCategoria = $request->except('_token'); // Obtener todos los datos excepto el token
        CategoriaActivo::create($datosCategoria); // Usar create en lugar de insert

        // Redirigir a la lista de categorías con un mensaje de éxito
        return redirect('/categoriasAct')->with('Mensaje', 'Categoria Agregada');
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
    public function edit($id)
    {
        $categoria = CategoriaActivo::findOrFail($id);
        return view('crud.activo.categoriaEdit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Encontrar la categoría por ID y actualizarla
        $categoria = CategoriaActivo::findOrFail($id);
        $categoria->update($request->all());

        // Redirigir a la lista de categorías (index) después de actualizar
        return redirect()->route('categoriasAct.index')->with('Success', 'Categoria actualizada');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
