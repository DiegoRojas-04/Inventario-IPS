@extends('adminlte::page')

@section('title', 'Activos')

@section('content_header')

    <div style="display: flex; justify-content: space-between; align-items:center;">

        <a href="{{ url('/activo/create') }}" class="text-decoration-none text-white">
            <button type="submit" class="btn btn-primary">Agregar Activo</button>
        </a>

        <a href="{{ route('activo.generarCodigosBarras') }}" class="btn btn-primary">Códigos de Barras</a>

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
                title: "Activo Eliminado"
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
                title: "Activo Actualizado"
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
                title: "Activo Restaurado"
            });
        </script>
    @endif

@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form id="filterForm" action="{{ url('/activo') }}" method="GET">
                <div class="row g-3">

                    <div class="col-md-1">
                        <select class="form-control" id="pageSize" name="page_size">
                            <option value="15" {{ request('page_size') == 15 ? 'selected' : '' }}>15</option>
                            <option value="10" {{ request('page_size') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('page_size') == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select data-size="5" title="Seleccionar Categoría" data-live-search="true" name="categoria"
                            id="categoria" class="form-control selectpicker show-tick" onchange="this.form.submit()">
                            <option value="">Seleccionar Categoría</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select data-size="5" title="Seleccionar Ubicación" data-live-search="true" name="ubicacion"
                            id="ubicacion" class="form-control selectpicker show-tick" onchange="this.form.submit()">
                            <option value="">Seleccionar Ubicación</option>
                            @foreach ($ubicaciones as $ubicacion)
                                <option value="{{ $ubicacion->id }}"
                                    {{ request('ubicacion') == $ubicacion->id ? 'selected' : '' }}>
                                    {{ $ubicacion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5 input-group">
                        <input type="text" class="form-control" placeholder="Buscar Codigo" id="search" name="search"
                            value="{{ request('search') }}">
                        <div class="input-group-prepend">
                            <button type="submit" class="btn" aria-disabled="true" style="pointer-events: none;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="card">
            <table class="table">
                <thead class="thead-dark">
                    <tr class="text-center">
                        {{-- <th scope="col">Codigo</th> --}}
                        <th scope="col">Categoria</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Serie</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Medida</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Ubicación</th>
                        <th scope="col">Accion</th>

                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($activos as $activo)
                        <tr>
                            <td>{{ $activo->categoria->nombre }}</td>
                            <td>{{ $activo->modelo }}</td>
                            <td>{{ $activo->serie }}</td>
                            <td>{{ $activo->marca }}</td>
                            <td>{{ $activo->medida }}</td>
                            <td>{{ $activo->cantidad }}</td>
                            <td>
                                <form action="{{ url('/activo/' . $activo->id . '/update-estado') }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="estado" class="form-control" onchange="this.form.submit()">
                                        <option value="0" {{ $activo->estado == 0 ? 'selected' : '' }}>EN USO</option>
                                        <option value="1" {{ $activo->estado == 1 ? 'selected' : '' }}>DISPONIBLE
                                        </option>
                                        <option value="2" {{ $activo->estado == 2 ? 'selected' : '' }}>REPARACION
                                        </option>
                                    </select>
                                </form>
                            </td>

                            <td>
                                <form action="{{ url('/activo/' . $activo->id . '/update-ubicacion') }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="ubicacion_id" class="form-control" onchange="this.form.submit()">
                                        @foreach ($ubicaciones as $ubicacion)
                                            <option value="{{ $ubicacion->id }}"
                                                {{ $activo->ubicacion_id == $ubicacion->id ? 'selected' : '' }}>
                                                {{ $ubicacion->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group" style="gap: 5px;">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#ver-{{ $activo->id }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>


                                    <a href="{{ url('/activo/' . $activo->id . '/edit') }}"
                                        class="text-decoration-none text-white">
                                        <button type="submit" class="btn btn-warning "><i class="fa fa-file"
                                                aria-hidden="true"></i></button></a>


                                    @if ($activo->condicion == 1)
                                        <button type="submit" class="btn btn-danger" data-toggle="modal"
                                            data-target="#eliminar-{{ $activo->id }}"><i class="fa fa-trash"
                                                aria-hidden="true"></i></button>
                                    @else
                                        <button type="submit" class="btn btn-success" data-toggle="modal"
                                            data-target="#eliminar-{{ $activo->id }}"><i class="fa fa-share"
                                                aria-hidden="true"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <!-- Modal -->
                        <div class="modal fade" id="eliminar-{{ $activo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ $activo->condicion == 1 ? 'Eliminar Activo' : 'Restaurar Activo' }} <br>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{ $activo->condicion == 1 ? ' ¿Estas seguro que quieres Eliminar esta Activo?' : '¿Estas seguro que quieres Restaurar esta Marca?' }}
                                        <br>
                                        <h5>{{ $activo->nombre }}</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                        <form action="{{ url('/activo/' . $activo->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-primary">Confirmar</i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="ver-{{ $activo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="modalLabel-{{ $activo->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel-{{ $activo->id }}">
                                            {{ $activo->nombre }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <div
                                            style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
                                            <a href="{{ route('activo.codigoBarras.pdf', $activo->id) }}"
                                                class="btn btn-primary" style="margin-right: 15px;">
                                                Código de Barras <i class="fa fa-barcode" aria-hidden="true"></i></a>

                                            <a href="{{ route('activo.generarCodigoBarrasPorActivo', $activo->id) }}"
                                                class="btn btn-primary" style="margin-right: 15px;">
                                                Varios <i class="fa fa-barcode" aria-hidden="true"></i>
                                            </a>
                                            <h4 style="margin: 0;">{{ $activo->codigo }}</h4>
                                        </div>
                                        <form action="{{ route('elementos.update.observacion', $activo->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group">
                                                <label for="observacion">Observación:</label>
                                                <input type="text" name="observacion" class="form-control"
                                                    value="{{ $activo->observacion }}"
                                                    placeholder="Escribe una observación">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
            {{ $activos->appends(request()->input())->links() }}
        </div>
    </div>
@stop

@section('css')

    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script src="{{ asset('js/insumos.js') }}"></script>
    <script>
        $(document).ready(function() {
            let barcode = ''; // Variable para almacenar el código de barras
            let isBarcodeProcessing = false; // Controla si se está procesando un código de barras

            // Capturar el evento de keydown en todo el documento
            $(document).keydown(function(e) {
                // Verificar si no se está procesando un código de barras
                if (!isBarcodeProcessing) {
                    // Verificar si el foco no está en un campo de entrada o textarea
                    if (!$('input, textarea').is(':focus')) {
                        if (e.key === 'Enter') {
                            // Cuando se presiona Enter, procesar el código de barras
                            isBarcodeProcessing = true;

                            // Colocar el código capturado en el campo de búsqueda
                            $('#search').val(barcode);

                            // Enviar el formulario automáticamente
                            $('#search').closest('form').submit();

                            // Limpiar la variable y reactivar el evento
                            barcode = '';
                            isBarcodeProcessing = false;
                        } else {
                            // Agregar la tecla presionada al código de barras
                            barcode += e.key;
                        }
                    }
                }
            });
        });
    </script>
@stop
