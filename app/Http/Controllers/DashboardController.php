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
        $usuarioCount = User::count();
        $preveedoresCount = Proveedore::count();
        $serviciosCount = Servicio::count();
        $categoriaCount = Categoria::count();
        $marcaCount = Marca::count();
        $presentacionCount = Presentacione::count();
        $insumoCount = Insumo::count();

        // $pedidoCount = Pedido::count();

        return view('dash.index', compact('usuarioCount', 'preveedoresCount', 'serviciosCount', 'categoriaCount', 'marcaCount', 'presentacionCount', 'insumoCount'));
    }
}
