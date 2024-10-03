<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivoRequest;
use App\Http\Requests\StoreActivoRequest;
use App\Models\Activo;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Activo::query();

        // Filtrado por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Búsqueda
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Paginación
        $pageSize = $request->input('pageSize', 15);
        $activos = $query->paginate($pageSize);

        return view('crud.activo.index', compact('activos'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crud.activo.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreActivoRequest $request)
    {
        // Generar un código único de 6 dígitos
        $codigo = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Obtener todos los datos excepto el token y añadir el código generado
        $datosActivos = $request->except('_token');
        $datosActivos['codigo'] = $codigo;

        // Insertar los datos en la base de datos
        Activo::insert($datosActivos);

        return redirect('activo/create')->with('Mensaje', 'Activo creado con éxito');
    }

    public function updateEstado(Request $request, $id)
    {
        $activo = Activo::findOrFail($id);
        $activo->estado = $request->input('estado');
        $activo->save();

        return redirect()->back()->with('Mensaje2', 'Estado actualizado correctamente');
    }

    // En App\Http\Controllers\ActivoController
    public function updateObservacion(Request $request, $id)
    {
        $request->validate([
            'observacion' => 'nullable|string|max:255', // Validar la observación
        ]);

        $activo = Activo::findOrFail($id); // Encuentra el activo por ID
        $activo->observacion = $request->input('observacion'); // Actualiza la observación
        $activo->save(); // Guarda los cambios

        return redirect()->back()->with('Mensaje2', 'Observación actualizada con éxito'); // Redirige con un mensaje
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
        $activo = Activo::findOrFail($id); // Encuentra el activo por ID o lanza un error 404
        return view('crud.activo.edit', compact('activo')); // Pasa el activo a la vista
    }

    public function update(StoreActivoRequest $request, $id)
    {
        $datosActivos = $request->all(); // Obtiene los datos validados
        $activo = Activo::findOrFail($id); // Encuentra el activo por ID
        $activo->update($datosActivos);    // Actualiza el registro en la base de datos
        return redirect('activo')->with('Mensaje2', 'Activo actualizado con éxito');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $activo = Activo::findOrFail($id);

        // Cambia el estado del activo
        if ($activo->condicion == 1) {
            $activo->condicion = 0; // Establece como eliminado
            $mensaje = 'Activo Eliminado';
            $sessionMessage = 'Mensaje'; // Usar el mensaje para eliminar
        } else {
            $activo->condicion = 1; // Restaurar
            $mensaje = 'Activo Restaurado';
            $sessionMessage = 'Mensaje3'; // Usar el mensaje para restaurar
        }

        $activo->save();

        return redirect()->back()->with($sessionMessage, $mensaje);
    }

    public function generarCodigoBarrasPDF($id)
    {
        $activo = Activo::findOrFail($id);
        $codigo = $activo->codigo;

        // Genera el código de barras
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

        // Crea una nueva instancia de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true); // Permitir imágenes remotas
        $dompdf = new Dompdf($options);

        // Ruta del logo
        $logoPath = public_path('images/logoCodigo.png');

        // HTML del PDF
        $html = '
        <style>
            .sticker {
                width: 90mm;
                height: 85mm;
                text-align: center;
                position: relative;
            }
            .logo {
                text-align: center;
                width: 100%;
                height: auto; 
                position: relative;
                margin-bottom: 3mm;
                font-size: 25px;

            }
            .barcode {
                width: 100%;
                height: auto;

            }
            .codigo {
                font-size: 15px;
                font-weight: bold;
                letter-spacing: 5px;
            }
        </style>
        <div class="sticker">
            <p class="logo">MEDICARE IPS</p>
            <img class="barcode" src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Código de Barras">
            <p class="codigo">' . $codigo . '</p>
        </div>
        ';

        // Cargar el HTML
        $dompdf->loadHtml($html);

        // Configura el tamaño del papel A8 y la orientación a horizontal
        $dompdf->setPaper('A7', 'landscape'); // Cambiar a landscape para orientación horizontal

        // Renderiza el PDF
        $dompdf->render();

        // Envía el PDF al navegador para descargar
        return $dompdf->stream('codigo_barras.pdf', [
            'Attachment' => true // Esto hace que el PDF se descargue en lugar de mostrarse en el navegador
        ]);
    }
}
