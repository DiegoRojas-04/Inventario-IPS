<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntregaRequest;
use App\Models\Categoria;
use App\Models\Comprobante;
use App\Models\Entrega;
use App\Models\Insumo;
use App\Models\Kardex;
use Carbon\Carbon;
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

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fechaInicio = Carbon::createFromFormat('Y-m-d', $request->input('fecha_inicio'))->startOfDay();
            $fechaFin = Carbon::createFromFormat('Y-m-d', $request->input('fecha_fin'))->endOfDay();
            $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
        }

        $entregas = $query->latest()->paginate(10);
        return view('crud.entrega.index', compact('entregas'));
    }

    public function create()
    {
        $insumos = Insumo::where('estado', 1)->orderBy('nombre', 'asc')->where('stock', '>', 0)->get();
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

    public function store(StoreEntregaRequest $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();
            $entrega = Entrega::create($request->validated());
            $arrayInsumo = $request->get('arrayidinsumo');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayVariante = $request->get('arrayvariante');
            $arrayInvima = $request->get('arrayinvima');
            $arrayLote = $request->get('arraylote');
            $arrayVencimiento = $request->get('arrayvencimiento');
            $arrayMarca = $request->get('arraymarca');
            $arrayPresentacion = $request->get('arraypresentacion');
            $totalCantidadEntregada = 0;

            foreach ($arrayInsumo as $key => $insumoId) {
                $variante = $arrayVariante[$key];
                $cantidad = $arrayCantidad[$key];
                $invima = $arrayInvima[$key];
                $lote = $arrayLote[$key];
                $vencimiento = $arrayVencimiento[$key];
                $marca = $arrayMarca[$key];
                $presentacion = $arrayPresentacion[$key];

                $entrega->insumos()->attach([
                    $insumoId => [
                        'cantidad' => $cantidad,
                        'invima' => $invima,
                        'lote' => $lote,
                        'vencimiento' => $vencimiento,
                        'id_marca' => $marca,
                        'id_presentacion' => $presentacion,
                    ]
                ]);

                $insumo = Insumo::find($insumoId);
                $insumo->stock -= intval($cantidad);
                $insumo->save();

                $caracteristica = DB::table('insumo_caracteristicas')
                    ->where('insumo_id', $insumoId)
                    ->where('invima', $invima)
                    ->where('lote', $lote)
                    ->where('vencimiento', $vencimiento)
                    ->where('id_marca', $marca)
                    ->where('id_presentacion', $presentacion)
                    ->first();

                if ($caracteristica) {
                    DB::table('insumo_caracteristicas')
                        ->where('id', $caracteristica->id)
                        ->decrement('cantidad', intval($cantidad));
                }

                $fechaEntrega = Carbon::createFromFormat('Y-m-d H:i:s', $entrega->fecha_hora);
                $mesEntrega = $fechaEntrega->month;
                $annoEntrega = $fechaEntrega->year;

                $kardex = Kardex::firstOrNew([
                    'insumo_id' => $insumoId,
                    'mes' => $mesEntrega,
                    'anno' => $annoEntrega
                ]);

                $kardex->egresos += intval($cantidad);
                $kardex->saldo -= intval($cantidad);
                $kardex->save();

                $totalCantidadEntregada += $cantidad;
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error('Ocurrió un error al procesar la solicitud: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al procesar la solicitud.']);
        }

        return redirect('entrega')->with('Mensaje', 'Entrega registrada con éxito.');
    }

    public function show($id)
    {
        $insumo = Insumo::all();
        $entrega = Entrega::with(['insumos' => function ($query) {
            $query->orderBy('nombre', 'asc'); // Ordenar por nombre
        }])->findOrFail($id);

        $detalleEntrega = $entrega->insumos()->with(['caracteristicas' => function ($query) {
            $query->select('insumo_id', 'invima', 'lote', 'vencimiento', 'id_marca', 'id_presentacion')
                ->with(['marca', 'presentacion']);
        }])->get();

        return view('crud.entrega.show', compact('entrega', 'insumo', 'detalleEntrega'));
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
