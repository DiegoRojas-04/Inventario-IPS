<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivoRequest;
use App\Http\Requests\StoreActivoRequest;
use App\Models\Activo;
use App\Models\CategoriaActivo;
use App\Models\Ubicacion;
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

        // Filtrado por categoría usando el ID
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        // Búsqueda
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Paginación
        $pageSize = $request->input('pageSize', 15);
        $activos = $query->paginate($pageSize);

        // Obtener todas las categorías para mostrarlas en el filtro
        $ubicaciones = Ubicacion::all(); // Obtén todas las ubicaciones
        $categorias = CategoriaActivo::all();

        return view('crud.activo.index', compact('activos', 'categorias', 'ubicaciones'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ubicaciones = Ubicacion::all();
        $categorias = CategoriaActivo::all();
        return view('crud.activo.create', compact('categorias', 'ubicaciones'));
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

        // Verificar si el estado es "Disponible"
        if ($datosActivos['estado'] === '1') { // Estado "Disponible"
            // Asignar automáticamente la ubicación a "Bodega" (id 2)
            $datosActivos['ubicacion_id'] = 2;
        }

        // Verificar si la ubicación es "Bodega" (id 2)
        if ($datosActivos['ubicacion_id'] == 2) {
            // Asignar automáticamente el estado a "Disponible"
            $datosActivos['estado'] = '1'; // "Disponible"
        }

        // Verificar si el estado es "Reparación"
        if ($datosActivos['estado'] === '2') { // Estado "Reparación"
            // Asignar automáticamente la ubicación a "En Reparación" (id 3)
            $datosActivos['ubicacion_id'] = 3;
        }

        // Insertar los datos en la base de datos
        Activo::insert($datosActivos);

        return redirect('activo/create')->with('Mensaje', 'Activo creado con éxito');
    }

    public function update(StoreActivoRequest $request, $id)
    {
        $datosActivos = $request->all(); // Obtiene los datos validados
        $activo = Activo::findOrFail($id); // Encuentra el activo por ID

        // Verificar si el estado es "Disponible"
        if ($datosActivos['estado'] === '1') { // Estado "Disponible"
            // Asignar automáticamente la ubicación a "Bodega" (id 2)
            $datosActivos['ubicacion_id'] = 2;
        }

        // Verificar si la ubicación es "Bodega" (id 2)
        if ($datosActivos['ubicacion_id'] == 2) {
            // Asignar automáticamente el estado a "Disponible"
            $datosActivos['estado'] = '1'; // "Disponible"
        }

        // Verificar si el estado es "Reparación"
        if ($datosActivos['estado'] === '2') { // Estado "Reparación"
            // Asignar automáticamente la ubicación a "En Reparación" (id 3)
            $datosActivos['ubicacion_id'] = 3;
        }

        // Actualizar el registro en la base de datos
        $activo->update($datosActivos);

        return redirect('activo')->with('Mensaje2', 'Activo actualizado con éxito');
    }


    public function updateEstado(Request $request, $id)
    {
        $activo = Activo::findOrFail($id);

        // Actualizar el estado
        $activo->estado = $request->input('estado');

        // Si el estado es "Disponible", cambiar la ubicación a "Bodega" (id 2)
        if ($activo->estado === '1') { // Estado "Disponible"
            $activo->ubicacion_id = 2; // Cambiar a "Bodega"
        } elseif ($activo->estado === '2') { // Estado "En Reparación"
            $activo->ubicacion_id = 3; // Cambiar a "En Reparación"
        }

        $activo->save();

        return redirect()->back()->with('Mensaje2', 'Estado actualizado correctamente');
    }

    public function updateUbicacion(Request $request, $id)
    {
        // Validación para asegurar que se proporciona una ubicación válida
        $request->validate([
            'ubicacion_id' => 'required|exists:ubicacions,id', // Asegúrate de que la ubicación exista en la tabla 'ubicaciones'
        ]);

        // Encuentra el activo por su ID
        $activo = Activo::findOrFail($id);

        // Actualizar la ubicación
        $activo->ubicacion_id = $request->ubicacion_id;

        // Si la ubicación es "Bodega" (id 2), cambiar el estado a "Disponible"
        if ($activo->ubicacion_id == 2) {
            $activo->estado = '1'; // Estado "Disponible"
        } elseif ($activo->ubicacion_id == 3) {
            $activo->estado = '2';  // Estado "Reparacion"
        }

        $activo->save();

        return redirect()->back()->with('Mensaje2', 'Ubicación actualizada correctamente');
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
        $ubicaciones = Ubicacion::orderBy('nombre', 'asc')->get();
        $categorias = CategoriaActivo::all();
        $activo = Activo::findOrFail($id); // Encuentra el activo por ID o lanza un error 404
        return view('crud.activo.edit', compact('activo', 'categorias', 'ubicaciones')); // Pasa el activo a la vista
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
        $nombreActivo = $activo->nombre; // Obtener el nombre del activo

        // Genera el código de barras
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

        // Crea una nueva instancia de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true); // Permitir imágenes remotas
        $options->set('isHtml5ParserEnabled', true); // Habilitar el analizador HTML5

        // Configurar márgenes a 0
        $options->set('marginTop', 0);
        $options->set('marginBottom', 0);
        $options->set('marginLeft', 0);
        $options->set('marginRight', 0);

        $dompdf = new Dompdf($options);

        // HTML del PDF
        $html = '
        <style>
            .sticker {
                width: 130mm;
                height: 150mm;
                display: flex;
                justify-content: center;
                text-align: center;
                margin: 0; /* Elimina el margen */
                padding: 0; /* Elimina el padding */
            }
            .logo {
                text-align: center;
                width: 100%;
                height: auto; 
                font-size: 24px;
                margin: 0; /* Elimina el margen */
                padding: 0; /* Elimina el padding */
                margin-bottom: 20px;
                font-weight: bold; 
            }
            .barcode {
                width: 100%;
                height: 30%; /* Cambiar a auto para mantener la proporción */
                margin: 0; /* Elimina el margen */
                padding: 0; /* Elimina el padding */
            }
            .codigo {
                font-size: 32px;
                font-weight: bold;
                letter-spacing: 35px; /* Reduce el espaciado */
                margin: 5px 0; /* Ajusta el margen superior e inferior */
                font-weight: bold; 
            }
        </style>
    
        <div class="sticker">
            <p class="logo">' . $nombreActivo . '</p> <!-- Cambiar a nombre del activo -->
            <img class="barcode" src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Código de Barras">
            <p class="codigo">' . $codigo . '</p>
        </div>
        ';

        // Cargar el HTML
        $dompdf->loadHtml($html);

        // Configura el tamaño del papel A6 y la orientación a horizontal
        $dompdf->setPaper('A6', 'landscape'); // Cambiar a landscape para orientación horizontal

        // Renderiza el PDF
        $dompdf->render();

        // Envía el PDF al navegador para descargar
        return $dompdf->stream('codigo_barras.pdf', [
            'Attachment' => true // Esto hace que el PDF se descargue en lugar de mostrarse en el navegador
        ]);
    }


    public function generarCodigosBarrasActivosPDF()
    {
        // Obtener todos los activos y ordenarlos alfabéticamente por nombre
        $activos = Activo::orderBy('nombre')->get();

        // Parámetros de tamaño de cada código de barras (en milímetros)
        $barcodeWidth = 45; // Ancho del código de barras
        $barcodeHeight = 25; // Alto del código de barras

        // Definir la cantidad de códigos por fila y por página
        $barcodesPerRow = 3; // Número de columnas
        $barcodesPerPage = 5 * $barcodesPerRow; // Total de códigos por página (filas * columnas)

        // Inicializar Dompdf
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        // Iniciar el HTML con los estilos
        $html = '<style>
              .page { 
                  page-break-after: always;
                  margin-top: 10mm;
                  margin-bottom: 0mm;
                  display: flex;
                  justify-content: center;
                  text-align: center;
              }
              .barcode-container {
                  display: inline-block;
                  width: ' . $barcodeWidth . 'mm;
                  height: ' . $barcodeHeight . 'mm;
                  text-align: center;
                  margin: 7mm 10mm 12mm 5mm;
              }
              .barcode {
                  width: 100%;
                  height: 70%;
              }
              .nombre-activo {
                  text-align: center;
                  font-size: 8px;
                  margin-top: 2px;
                  font-weight: bold;
              }
              .codigo-activo {
                  font-size: 12px;
                  letter-spacing: 15px;
                  font-weight: bold;
                  text-align: center;
              }
              .letra {
                  font-size: 30px;
                  font-weight: bold;
                  text-align: center;
                  margin-bottom: 30px;
              }
          </style>';

        $currentLetter = ''; // Para rastrear el cambio de letra
        $count = 0; // Contador de códigos de barras por página
        $mostrarLetra = true; // Bandera para mostrar la letra solo en la primera página del grupo

        foreach ($activos as $activo) {
            $codigo = $activo->codigo;
            $nombreActivo = $activo->nombre;
            $letraActual = strtoupper(substr($nombreActivo, 0, 1)); // Obtener la primera letra del nombre

            if ($letraActual !== $currentLetter) {
                if ($count > 0) {
                    $html .= '</div>'; // Cerrar la página anterior
                }
                $html .= '<div class="page">';
                $html .= '<h1 class="letra"> ' . $letraActual . '</h1>'; // Mostrar la letra
                $currentLetter = $letraActual;
                $count = 0;
            }

            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

            $html .= '
            <div class="barcode-container">
                <p class="nombre-activo">' . $nombreActivo . '</p>
                <img class="barcode" src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Código de Barras">
                <p class="codigo-activo">' . $codigo . '</p>
            </div>';

            $count++;

            if ($count % $barcodesPerRow == 0) {
                $html .= '<br>';
            }

            if ($count % $barcodesPerPage == 0) {
                $html .= '</div><div class="page">';
            }
        }

        $html .= '</div>'; // Cerrar la última página

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('codigos_barras_activos.pdf', ['Attachment' => true]);
    }


    public function generarCodigoBarrasPorActivoPDF($id)
    {
        // Obtener el activo por ID
        $activo = Activo::findOrFail($id);

        // Parámetros de tamaño de cada código de barras (en milímetros)
        $barcodeWidth = 40;
        $barcodeHeight = 20;
        $barcodesPerRow = 3;
        $barcodesPerPage = 18;

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        $html = '<style>
                .page { 
                    page-break-after: always;
                    margin-top: 10mm;
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    text-align: center;
                }
                .barcode-container {
                    display: inline-block;
                    width: ' . $barcodeWidth . 'mm;
                    height: ' . $barcodeHeight . 'mm;
                    text-align: center;
                    margin: 7mm 10mm 12mm 5mm;
                }
                .barcode {
                    width: 100%;
                    height: 70%;
                }
                .nombre-activo {
                    text-align: center;
                    font-size: 7px;
                    margin-top: 2px;
                    font-weight: bold;
                }
                .codigo-activo {
                    font-size: 11px;
                    letter-spacing: 15px;
                    font-weight: bold;
                    text-align: center;
                }
            </style>';

        $codigo = $activo->codigo;
        $nombreActivo = $activo->nombre;

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

        $html .= '<div class="page">';

        for ($i = 0; $i < $barcodesPerPage; $i++) {
            $html .= '
            <div class="barcode-container">
                <p class="nombre-activo">' . $nombreActivo . '</p>
                <img class="barcode" src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Código de Barras">
                <p class="codigo-activo">' . $codigo . '</p>
            </div>';
        }

        $html .= '</div>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('codigo_barras_activo_' . $codigo . '.pdf', ['Attachment' => true]);
    }
}
