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
        $caracteristicas = $insumo->caracteristicas;
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

        $entregas = $query->latest()->paginate(5);
        return view('crud.entrega.index', compact('entregas'));
    }

    public function create()
    {
        $insumos = Insumo::where('estado', 1)->orderBy('nombre', 'asc')->where('stock', '>', 0)->get();
        $servicios = Servicio::where('estado', 1)->get();
        $categorias = Categoria::all();
        $comprobantes = Comprobante::all();
        $todasVariantes = collect();

        foreach ($insumos as $insumo) {
            $todasVariantes = $todasVariantes->merge($insumo->caracteristicas);
        }

        $varianteIndex = 0;
        return view('crud.entrega.create', compact('insumos', 'servicios', 'comprobantes', 'todasVariantes', 'varianteIndex', 'categorias'));
    }

    public function store(StoreEntregaRequest $request)
    {
        try {
            DB::beginTransaction();
            $entrega = Entrega::create($request->validated());
            $arrayInsumo = $request->get('arrayidinsumo');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayVariante = $request->get('arrayvariante');
            $arrayInvima = $request->get('arrayinvima');
            $arrayLote = $request->get('arraylote');
            $arrayVencimiento = $request->get('arrayvencimiento');
            $totalCantidadEntregada = 0;

            foreach ($arrayInsumo as $key => $insumoId) {
                $variante = $arrayVariante[$key];
                $cantidad = $arrayCantidad[$key];
                $invima = $arrayInvima[$key];
                $lote = $arrayLote[$key];
                $vencimiento = $arrayVencimiento[$key];

                $entrega->insumos()->attach([
                    $insumoId => [
                        'cantidad' => $cantidad,
                        'invima' => $invima,
                        'lote' => $lote,
                        'vencimiento' => $vencimiento,
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
            $query->select('insumo_id', 'invima', 'lote', 'vencimiento');
        }])->get();

        return view('crud.entrega.show', compact('entrega', 'insumo', 'detalleEntrega'));
    }


    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function exportToPdf($id)
    {
        $entrega = Entrega::with('insumos.marca', 'insumos.presentacione', 'user', 'comprobante', 'servicio')->findOrFail($id);

        // Ordenar los insumos por nombre
        $insumosOrdenados = $entrega->insumos->sortBy('nombre');

        // HTML para el contenido del PDF
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
            }
        </style>
        <h1 style="text-align: center;">Detalle de Entrega</h1>
        <p><strong>Entrega realizada a:</strong> ' . $entrega->servicio->nombre . '</p>
        <p><strong>Fecha:</strong> ' . \Carbon\Carbon::parse($entrega->fecha_hora)->format('d-m-Y') . '</p>
        <p><strong>Hora:</strong> ' . \Carbon\Carbon::parse($entrega->fecha_hora)->format('H:i:s') . '</p>
        <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Presentacion</th>
                    <th>Invima</th>
                    <th>Lote</th>
                    <th>Vencimiento</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>';

        // Agregar los insumos y cantidades a la tabla
        foreach ($insumosOrdenados as $insumo) {
            $html .= '
            <tr>
                <td>' . $insumo->nombre . '</td>
                <td>' . $insumo->marca->nombre . '</td>
                <td>' . $insumo->presentacione->nombre . '</td>
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
