<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Insumo;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Proveedore;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Conteos de los diferentes modelos
        $usuarioCount = User::count();
        $preveedoresCount = Proveedore::count();
        $serviciosCount = Servicio::count();
        $categoriaCount = Categoria::count();
        $marcaCount = Marca::count();
        $presentacionCount = Presentacione::count();
        $insumoCount = Insumo::count();
        
        // Obtén insumos con características que están próximos a vencer o vencidos
        $insumos = Insumo::with('caracteristicas')
            ->whereHas('caracteristicas', function ($query) {
                $query->where('cantidad', '>', 0)
                    ->where(function ($query) {
                        $query->where('vencimiento', '<=', now()->addMonth()) // Menos de un mes
                              ->where('vencimiento', '!=', '0001-01-01'); // Excluir fecha 01-01-0001
                    });
            })
            ->get();


        // Pasar todos los datos a la vista
        return view('dash.index', compact(
            'usuarioCount',
            'preveedoresCount',
            'serviciosCount',
            'categoriaCount',
            'marcaCount',
            'presentacionCount',
            'insumoCount',
            'insumos' // Pasar los insumos a la vista
        ));
    }
}
