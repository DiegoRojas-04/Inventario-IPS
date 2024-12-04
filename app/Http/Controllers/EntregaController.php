<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntregaRequest;
use App\Models\Categoria;
use App\Models\Comprobante;
use App\Models\Entrega;
use App\Models\EntregaInsumo;
use App\Models\Insumo;
use App\Models\Kardex;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Servicio;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntregaController extends Controller
{
    public function getStock(Request $request)
    {
        $insumoId = $request->input('insumo_id');
        $stock = Insumo::findOrFail($insumoId)->stock;
        return response()->json(['stock' => $stock]);
    }
    public function getCaracteristicas(Request $request)
    {
        $insumoId = $request->get('insumo_id');
        $insumo = Insumo::findOrFail($insumoId);
        // Obtener características con la marca y la presentación
        $caracteristicas = $insumo->caracteristicas()->with(['marca', 'presentacion'])->get();

        return response()->json(['caracteristicas' => $caracteristicas]);
    }



    public function index(Request $request)
    {
        $query = Entrega::with('comprobante')->where('estado', 1);

        // Filtra las entregas por el usuario autenticado
        $query->where('user_id', auth()->id());

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fechaInicio = Carbon::createFromFormat('Y-m-d', $request->input('fecha_inicio'))->startOfDay();
            $fechaFin = Carbon::createFromFormat('Y-m-d', $request->input('fecha_fin'))->endOfDay();
            $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
        }

        $entregas = $query->latest()->paginate(20);
        return view('crud.entrega.index', compact('entregas'));
    }

    public function create()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Filtrar insumos según el rol del usuario
        if ($user->roles->contains('name', 'Administrador')) {
            // Si es Administrador, obtener todos los insumos con estado 1 y stock mayor a 0
            $insumos = Insumo::where('estado', 1)
                ->where('stock', '>', 0)
                ->orderBy('nombre', 'asc')
                ->get();
        } elseif ($user->roles->contains('name', 'Laboratorio')) {
            // Si es Laboratorio, obtener solo insumos de la categoría 6
            $insumos = Insumo::where('estado', 1)
                ->where('stock', '>', 0)
                ->where('id_categoria', 12) // Filtrar por categoría 6
                ->orderBy('nombre', 'asc')
                ->get();
        } else {
            // Si es otro rol, manejar como consideres
            $insumos = collect(); // Sin insumos disponibles
        }

        $servicios = Servicio::where('estado', 1)->get();
        $categorias = Categoria::all();
        $comprobantes = Comprobante::all();
        $todasVariantes = collect();

        $numeroComprobante = Entrega::generarNumeroComprobante();
        $comprobanteEntrega = Comprobante::where('tipo_comprobante', 'Entrega')->first();

        foreach ($insumos as $insumo) {
            $todasVariantes = $todasVariantes->merge($insumo->caracteristicas);
        }

        $varianteIndex = 0;

        return view('crud.entrega.create', compact('insumos', 'servicios', 'comprobantes', 'todasVariantes', 'varianteIndex', 'categorias', 'numeroComprobante', 'comprobanteEntrega'));
    }



    // hacer que compare a cual le puede restar

    public function store(StoreEntregaRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear la fecha de la entrega desde el formulario
            $fechaEntrega = Carbon::createFromFormat('Y-m-d H:i:s', $request->get('fecha_hora'));

            // Crear la entrega
            $entrega = Entrega::create([
                ...$request->validated(),  // los datos validados del formulario
                'fecha_hora' => $fechaEntrega, // Aseguramos que 'fecha_hora' se guarde correctamente
                'created_at' => $fechaEntrega,  // Asignamos la fecha_hora seleccionada por el usuario a created_at
                'updated_at' => Carbon::now(),  // De igual forma, el updated_at se debe actualizar
            ]);

            // Variables para el cálculo
            $arrayInsumo = $request->get('arrayidinsumo');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayVariante = $request->get('arrayvariante');
            $arrayInvima = $request->get('arrayinvima');
            $arrayLote = $request->get('arraylote');
            $arrayVencimiento = $request->get('arrayvencimiento');
            $arrayMarca = $request->get('arraymarca');
            $arrayPresentacion = $request->get('arraypresentacion');
            $arrayValor = $request->get('arrayvalor');

            $totalCantidadEntregada = 0;
            $valorTotalEntrega = 0; // Variable para acumular el valor total

            // Procesamos cada insumo entregado
            foreach ($arrayInsumo as $key => $insumoId) {
                $variante = $arrayVariante[$key];
                $cantidad = $arrayCantidad[$key];
                $invima = $arrayInvima[$key];
                $lote = $arrayLote[$key];
                $vencimiento = $arrayVencimiento[$key];
                $marca = $arrayMarca[$key];
                $presentacion = $arrayPresentacion[$key];
                $valor = $arrayValor[$key];

                // Asociar insumo a la entrega con los detalles adicionales
                $entrega->insumos()->attach($insumoId, [
                    'cantidad' => $cantidad,
                    'invima' => $invima,
                    'lote' => $lote,
                    'vencimiento' => $vencimiento,
                    'id_marca' => $marca,
                    'id_presentacion' => $presentacion,
                    'valor_unitario' => $valor,
                    'created_at' => $fechaEntrega,  // Registrar la fecha_hora en created_at
                    'updated_at' => Carbon::now(),  // Registrar el updated_at
                ]);

                // Actualizar stock del insumo
                $insumo = Insumo::find($insumoId);
                $insumo->stock -= intval($cantidad); // Decrementar el stock
                $insumo->save();

                // Calcular el valor total de este insumo y acumularlo
                $valorTotalEntrega += $cantidad * $valor;

                // Obtener características del insumo
                $caracteristicas = DB::table('insumo_caracteristicas')
                    ->where('insumo_id', $insumoId)
                    ->where('invima', $invima)
                    ->where('lote', $lote)
                    ->where('vencimiento', $vencimiento)
                    ->where('id_marca', $marca)
                    ->where('id_presentacion', $presentacion)
                    ->get();

                $cantidadRestante = intval($cantidad);

                // Actualizamos las características del insumo
                foreach ($caracteristicas as $caracteristica) {
                    if ($cantidadRestante <= 0) {
                        break; // Si ya hemos actualizado toda la cantidad, salir del bucle
                    }

                    if ($caracteristica->cantidad >= $cantidadRestante) {
                        // Actualizar la cantidad en insumo_caracteristicas
                        DB::table('insumo_caracteristicas')
                            ->where('id', $caracteristica->id)
                            ->decrement('cantidad', $cantidadRestante);

                        // Actualizamos el `updated_at` para reflejar la fecha de entrega
                        DB::table('insumo_caracteristicas')
                            ->where('id', $caracteristica->id)
                            ->update(['updated_at' => $fechaEntrega]);

                        // Restar la cantidad entregada
                        $cantidadRestante = 0;
                    } else {
                        // Si la cantidad restante es mayor que la cantidad de la característica
                        $cantidadRestante -= $caracteristica->cantidad;

                        // Actualizamos la cantidad a 0
                        DB::table('insumo_caracteristicas')
                            ->where('id', $caracteristica->id)
                            ->update([
                                'cantidad' => 0,
                                'updated_at' => $fechaEntrega, // Usamos la fecha_hora de entrega
                            ]);
                    }
                }

                if ($cantidadRestante > 0) {
                    // Manejar la cantidad restante si es necesario (según lógica de negocio)
                }

                // Buscar o crear un nuevo registro en el Kardex
                $mesEntrega = $fechaEntrega->month;
                $annoEntrega = $fechaEntrega->year;

                $kardex = Kardex::firstOrNew([
                    'insumo_id' => $insumoId,
                    'mes' => $mesEntrega,
                    'anno' => $annoEntrega
                ]);

                // Actualizar el Kardex
                $kardex->egresos += intval($cantidad);
                $kardex->saldo -= intval($cantidad);
                $kardex->save();

                // Acumular la cantidad entregada
                $totalCantidadEntregada += $cantidad;
            }

            // Actualizar el valor total de la entrega
            $entrega->valor_total = $valorTotalEntrega;

            // Guardamos los cambios en la entrega
            $entrega->save();

            DB::commit();
            return redirect('entrega')->with('Mensaje', 'Entrega registrada con éxito.');
        } catch (Exception $e) {
            Log::error('Ocurrió un error al procesar la solicitud: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al procesar la solicitud.']);
        }
    }


    public function show($id)
    {
        // Obtener todos los insumos y ordenarlos de la A a la Z
        $insumo = Insumo::orderBy('nombre', 'asc')->get(); // Ordenar por nombre

        // Obtener la entrega con la relación de insumos y ordenarlos de la A a la Z
        $entrega = Entrega::with(['insumos' => function ($query) {
            $query->orderBy('nombre', 'asc'); // Ordenar los insumos por nombre
        }])->findOrFail($id);

        // Obtener los detalles de la entrega con las características (invima, lote, vencimiento, marca, presentación)
        $detalleEntrega = EntregaInsumo::with(['insumo', 'marca', 'presentacion'])
            ->where('entrega_id', $id)
            ->get();

        // Calcular el valor total de la entrega
        $totalEntrega = $entrega->insumoEntregas->sum(function ($entregaInsumo) {
            return $entregaInsumo->valor_unitario * $entregaInsumo->cantidad;
        });


        // Pasar los datos a la vista
        return view('crud.entrega.show', compact('entrega', 'insumo', 'detalleEntrega', 'totalEntrega'));
    }

    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $categoriaId = $request->get('categoria_id');

        // Obtener todas las categorías
        $categorias = Categoria::all();

        // Iniciar la consulta
        $query = Entrega::query()
            ->join('entrega_insumo', 'entregas.id', '=', 'entrega_insumo.entrega_id')
            ->join('insumos', 'entrega_insumo.insumo_id', '=', 'insumos.id')
            ->select('entrega_insumo.*', 'insumos.nombre as insumo_nombre', 'insumos.id_categoria', 'entregas.created_at')
            ->orderBy('entregas.created_at', 'desc'); // Ordenar por la fecha de entrega de la más reciente a la más antigua

        // Filtrar por fecha
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('entregas.created_at', [$fechaInicio, $fechaFin]);
        }

        // Filtrar por categoría si se proporciona
        if ($categoriaId) {
            $query->where('insumos.id_categoria', $categoriaId);
        }

        // Obtener las entregas y paginarlas
        $entregas = $query->paginate(100);

        // Calcular el valor total de entrega
        $valorTotalEntrega = $entregas->sum(function ($entrega) {
            return $entrega->valor_unitario * $entrega->cantidad;
        });

        return view('crud.entrega.estadisticas', compact('entregas', 'fechaInicio', 'fechaFin', 'categoriaId', 'categorias', 'valorTotalEntrega'));
    }

    public function edit(string $id)
    {
        // Implementar según las necesidades
    }

    public function update(Request $request, string $id)
    {
        // Implementar según las necesidades
    }

    public function destroy(string $id)
    {
        // Implementar según las necesidades
    }

    public function exportToPdf($id)
    {
        $entrega = Entrega::with('user', 'comprobante', 'servicio')->findOrFail($id);

        // Ordenar los insumos por nombre
        $insumosOrdenados = $entrega->insumos->sortBy('nombre');

        // HTML para el contenido del PDF
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid black;
                padding: 5px;
                text-align: center;
                font-size: 11px; /* Reduce the font size */
            }
            th {
                background-color: #f2f2f2;
            }
            td {
                word-wrap: break-word;
            }
        </style>
        <h1 style="text-align: center;">Detalle de Entrega</h1>
        <p><strong>Entrega realizada a:</strong> ' . $entrega->servicio->nombre . '</p>
        <p><strong>Fecha:</strong> ' . \Carbon\Carbon::parse($entrega->fecha_hora)->format('d-m-Y') . '</p>
        <p><strong>Hora:</strong> ' . \Carbon\Carbon::parse($entrega->fecha_hora)->format('H:i:s') . '</p>
        <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; text-align: center;">
             <thead>
                <tr>
                    <th style="width: 20%;">Producto</th>
                    <th style="width: 15%;">Marca</th>
                    <th style="width: 10%;">Presentación</th>
                    <th style="width: 15%;">Invima</th>
                    <th style="width: 10%;">Lote</th>
                    <th style="width: 10%;">Vencimiento</th>
                    <th style="width: 10%;">Cantidad</th>
                </tr>
            </thead>
            <tbody>';

        // Agregar los insumos y cantidades a la tabla
        foreach ($insumosOrdenados as $insumo) {
            $html .= '
            <tr>
                <td>' . $insumo->nombre . '</td>
                <td>' . ($insumo->marca ? $insumo->marca->nombre : 'Sin Marca') . '</td>
                <td>' . ($insumo->presentacion ? $insumo->presentacion->nombre : 'Sin Presentación') . '</td>
                <td>' . $insumo->pivot->invima . '</td>
                <td>' . $insumo->pivot->lote . '</td>
                <td>' . \Carbon\Carbon::parse($insumo->pivot->vencimiento)->format('d-m-Y') . '</td>
                <td>' . $insumo->pivot->cantidad . '</td>
            </tr>';
        }

        $html .= '
            </tbody>
        </table>';

        // Configurar el PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF
        return $dompdf->stream('Detalle_entrega_' . $entrega->user->name . '.pdf');
    }
}
