<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsumoCaracteristicaController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PresentacionController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Restricción de acceso a la creación de pedidos mediante URL
    Route::get('pedido/create', [PedidoController::class, 'create'])
        ->middleware(['check.time'])
        ->name('pedido.create');

    // Aplica el middleware 'role' a las rutas protegidas por roles
    Route::middleware(['role'])->group(function () {
        Route::resource('pedido', PedidoController::class)->except(['create']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::resource('categoria', CategoriaController::class);
        Route::resource('servicio', ServicioController::class);
        Route::resource('insumo', InsumoController::class);
        Route::resource('marca', MarcaController::class);
        Route::resource('presentacion', PresentacionController::class);
        Route::resource('entrega', EntregaController::class);
        Route::resource('proveedor', ProveedorController::class);
        Route::resource('compra', CompraController::class);
        Route::resource('profile', PerfilController::class);
        Route::resource('usuario', UsuarioController::class);
        Route::resource('rol', RolController::class);
        Route::resource('permiso', PermisoController::class);
        Route::resource('kardex', KardexController::class);
        Route::get('/pedido', [PedidoController::class, 'index'])->name('pedido');
    });
});

Route::get('/export/compra/pdf/{id}', [CompraController::class, 'exportToPdf'])->name('export.compra.pdf');
// Route::get('/export-order-pdf', [KardexController::class, 'exportOrderToPdf'])->name('generate.order');
Route::get('/export/entrega/pdf/{id}', [EntregaController::class, 'exportToPdf'])->name('export.entrega.pdf');

Route::get('/export/pedido/pdf/{id}', [PedidoController::class, 'exportToPdf'])->name('export.pedido.pdf');


Route::get('/export/excel', [KardexController::class, 'exportToExcel'])->name('export.excel');
Route::get('/export/pdf', [KardexController::class, 'exportToPdf'])->name('export.pdf');
Route::get('/exporto/pdf', [KardexController::class, 'exportOrderToPdf'])->name('exporto.pdf');

// Rutas adicionales fuera del grupo middleware
Route::get('/obtener-detalles-pedido/{idPedido}', [PedidoController::class, 'obtenerDetallesPedido']);
Route::get('/insumo/search', [InsumoController::class, 'search'])->name('insumo.search');
Route::get('/get-stock', [EntregaController::class, 'getStock'])->name('get-stock');
Route::get('/get-caracteristicas', [EntregaController::class, 'getCaracteristicas']);
Route::get('/insumo/{insumoId}/caracteristica/{caracteristicaId}/edit', [InsumoCaracteristicaController::class, 'edit']);
Route::patch('/insumo/{insumoId}/caracteristica/{caracteristicaId}', [InsumoCaracteristicaController::class, 'update'])->name('caracteristica.update');
