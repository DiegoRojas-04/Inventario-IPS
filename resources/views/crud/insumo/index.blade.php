@extends('adminlte::page')

@section('title', 'Insumo')

@section('content_header')

    <div style="display: flex; justify-content: space-between; align-items:center;">
        <div>
            <a href="{{ url('/insumo/create') }}" class="text-decoration-none text-white">
                <button type="submit" class="btn btn-primary">Agregar Insumos</button>
            </a>
            <a href="{{ route('insumos.analisisPrecios') }}" class="btn btn-primary">
                Análisis <i class="fa fa-signal" aria-hidden="true"></i>
            </a>
        </div>
        <div>
            <button type="button" class="btn btn-danger">
                <a href="{{ route('insumos.exportToPdf', ['id_categoria' => request('id_categoria')]) }}"
                    style="color: white; text-decoration: none;">Inventario
                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                </a>
            </button>
            <button type="button" class="btn btn-success">
                <a href="{{ route('insumos.exportToExcel', ['id_categoria' => request('id_categoria')]) }}"
                    style="color: white; text-decoration: none;">Inventario
                    <i class="fa fa-file-excel" aria-hidden="true"></i>
                </a>
            </button>
            
            <a href="{{ route('generar.codigos.barras') }}" class="btn btn-info">Códigos <i class="fa fa-barcode" aria-hidden="true"></i> </a>
        </div>
    </div>

    @if (session('Mensaje'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "error",
                title: "Insumo Eliminado"
            });
        </script>
    @endif
    @if (session('Mensaje3'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Insumo Restaurado"
            });
        </script>
    @endif
    @if (session('Mensaje2'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Insumo Actualizado"
            });
        </script>
    @endif

    <br>
@stop
@section('content')
    <div class="card">
        <div class="card-header">
            <form id="filterForm" method="GET" action="{{ url('/insumo') }}">
                <div class="row g-3">
                    <div class="col-md-1">
                        <select class="form-control" id="pageSize" name="page_size">
                            <option value="10">#</option>
                            <option value="5" {{ request('page_size') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('page_size') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('page_size') == 20 ? 'selected' : '' }}>20</option>
                            <option value="30" {{ request('page_size') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50</option>
                            <option value="70" {{ request('page_size') == 70 ? 'selected' : '' }}>70</option>
                            <option value="100" {{ request('page_size') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select data-size="5" title="Seleccionar Categoria" data-live-search="true" name="id_categoria"
                                id="id_categoria" class="form-control selectpicker show-tick">
                            <option value="">Seleccionar Categoría</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                        {{ request('id_categoria') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                    </div>

                    <div class="col-md-5 input-group">
                        <input type="text" class="form-control" placeholder="Buscar" id="search" name="search"
                            value="{{ request('search') }}">
                        <div class="input-group-prepend">
                            <button type="submit" class="btn" aria-disabled="true" style="pointer-events: none;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="page_size" id="pageSizeHidden">
            </form>
        </div>
        <style>
            .table-danger {
                background-color: #f8d7da !important;
                color: #721c24 !important;
            }

            .table-danger td,
            .table-danger th {
                border-color: #f5c6cb !important;
            }
        </style>

        <div class="card-body">
            <table class="table table-striped" id="datos">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($insumos as $insumo)
                        @php
                            // Variables de estado
                            $estadoInsumo = 'status-green'; // Asumimos verde por defecto

                            // Recorrer las características del insumo
                            foreach ($insumo->caracteristicas as $caracteristica) {
                                // Ignorar si la cantidad es 0
                                if ($caracteristica->cantidad == 0) {
                                    continue;
                                }

                                $fechaVencimiento = \Carbon\Carbon::parse($caracteristica->vencimiento);
                                $hoy = \Carbon\Carbon::now();
                                $diferenciaMeses = $hoy->diffInMonths($fechaVencimiento);

                                // Verificar estado según la fecha de vencimiento
                                if ($fechaVencimiento->format('d-m-Y') === '01-01-0001') {
                                    continue; // Ignorar si la fecha no es válida
                                } elseif ($fechaVencimiento->lessThanOrEqualTo($hoy->addMonth())) {
                                    $estadoInsumo = 'status-red'; // Si hay algún rojo, prevalece
                                    break; // Ya no es necesario seguir revisando
                                } elseif ($diferenciaMeses <= 3 && $estadoInsumo !== 'status-red') {
                                    $estadoInsumo = 'status-yellow'; // Si hay algún amarillo y no hay rojo
                                }
                            }
                        @endphp
                        <tr class="{{ $insumo->estado == 0 ? 'table-eliminado' : $insumo->alertClass ?? '' }}">
                            <td>
                                <div class="status-circle {{ $estadoInsumo }}"></div>
                            </td>
                            <td>{{ $insumo->nombre }}</td>
                            <td>{{ $insumo->stock }}</td>
                            <td>
                                <div class="btn-group" role="group" style="gap: 5px;">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalInsumo-{{ $insumo->id }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                    <a href="{{ url('/insumo/' . $insumo->id . '/edit') }}"
                                        class="text-decoration-none text-white">
                                        <button type="submit" class="btn btn-warning"><i class="fa fa-file"
                                                aria-hidden="true"></i></button>
                                    </a>
                                    @if ($insumo->estado == 1)
                                        <form id="delete-form-{{ $insumo->id }}"
                                            action="{{ url('/insumo/' . $insumo->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete({{ $insumo->id }})">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form id="delete-form-{{ $insumo->id }}"
                                            action="{{ url('/insumo/' . $insumo->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="button" class="btn btn-success"
                                                onclick="confirmDelete({{ $insumo->id }})">
                                                <i class="fa fa-share" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $insumos->appends(request()->query())->links() }}
        </div>

    </div>
    @foreach ($insumos as $insumo)
        <div class="modal fade bd-example-modal-lg" id="modalInsumo-{{ $insumo->id }}" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title font-bold" id="exampleModalLabel"></h4>
                    </div>
                    <div class="modal-body text-center">
                        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
                            <a href="{{ route('insumo.generarCodigoBarrasPDF', $insumo->id) }}" class="btn btn-primary"
                                style="margin-right: 15px;">
                                Código de Barras <i class="fa fa-barcode" aria-hidden="true"></i>
                            </a>

                            <a href="{{ route('insumo.generarCodigoBarrasPorInsumoPDF', $insumo->id) }}"
                                class="btn btn-primary" style="margin-right: 15px;">
                                Varios <i class="fa fa-barcode" aria-hidden="true"></i>
                            </a>
                            <h4 style="margin: 0;">{{ $insumo->codigo }}</h4>
                        </div>

                        <label class="text-center font-bold">
                            <h4>{{ $insumo->nombre }}</h4>
                        </label>

                        <div class="mb-3 border-b pb-3"
                        style="display: flex; justify-content: space-between; align-items: stretch; border: 1px solid #ccc; padding: 10px;">
                        
                        <div style="flex: 0 0 55%; border-right: 1px solid #ccc; padding-right: 10px;">
                            <label class="block font-bold" for="descripcion">Descripción:</label>
                            <span id="descripcion" style="word-wrap: break-word; text-align: left;">
                                {{ $insumo->descripcion }}
                            </span>
                        </div>
                        
                        <div style="flex: 0 0 15%; border-right: 1px solid #ccc; padding-right: 10px; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <label class="block font-bold" for="vida_util" style="text-align: center;">Vida Útil:</label>
                                <span id="vida_util" style="text-align: center;">
                                    {{ $insumo->vida_util }}
                                </span>
                            </div>
                        </div>
                        
                        <div style="flex: 0 0 15%; border-right: 1px solid #ccc; padding-right: 10px; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <label class="block font-bold" for="riesgo" style="text-align: center;">Riesgo:</label>
                                <span id="riesgo" style="text-align: center;">
                                    {{ $insumo->riesgo }}
                                </span>
                            </div>
                        </div>
                    
                        <div style="flex: 0 0 15%; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <label class="block font-bold" for="ubicacion" style="text-align: center;">Ubicación:</label>
                                <span id="ubicacion" style="text-align: center;">
                                    @if ($insumo->ubicacion == 1)
                                        Insumos
                                    @elseif ($insumo->ubicacion == 2)
                                        Bodega Principal
                                    @elseif ($insumo->ubicacion == 3)
                                        Laboratorio
                                    @else
                                        Desconocido
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                        <div class="mb-3">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                        <th>Marca</th>
                                        <th>Presentacion</th>
                                        <th>Invima</th>
                                        <th>Lote</th>
                                        <th>Vencimiento</th>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($insumo->caracteristicas->sortBy('vencimiento') as $caracteristica)
                                        @if ($caracteristica->cantidad > 0)
                                            @php
                                                // Parse la fecha de vencimiento
                                                $fechaVencimiento = \Carbon\Carbon::parse($caracteristica->vencimiento);
                                                $hoy = \Carbon\Carbon::now();
                                                $diferenciaMeses = $hoy->diffInMonths($fechaVencimiento);
                                                $estado = '';

                                                // Verifica si la fecha de vencimiento es '01-01-0001'
                                                if ($fechaVencimiento->format('d-m-Y') === '01-01-0001') {
                                                    $estado = 'status-blue'; // Fecha no válida
                                                } elseif ($fechaVencimiento->lessThanOrEqualTo($hoy->addMonth())) {
                                                    $estado = 'status-red'; // Menos de un mes
                                                } elseif ($diferenciaMeses <= 3) {
                                                    $estado = 'status-yellow'; // Menos de 3 meses
                                                } else {
                                                    $estado = 'status-green'; // Más de 4 meses
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $caracteristica->marca->nombre }}</td>
                                                <td>{{ $caracteristica->presentacion->nombre }}</td>
                                                <td>{{ $caracteristica->invima }}</td>
                                                <td>{{ $caracteristica->lote }}</td>
                                                <td>{{ \Carbon\Carbon::parse($caracteristica->vencimiento)->format('d-m-Y') }}
                                                </td>
                                                <td>{{ $caracteristica->cantidad }}</td>
                                                <td>
                                                    <div class="status-circle {{ $estado }}"></div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ url('/insumo/' . $insumo->id . '/caracteristica/' . $caracteristica->id . '/edit') }}"
                                                            class="text-decoration-none text-white">
                                                            <button type="submit" class="btn btn-warning"><i
                                                                    class="fa fa-file" aria-hidden="true"></i></button>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer justify-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@stop

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/insumos.js') }}"></script>
@stop
