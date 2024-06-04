@extends('adminlte::page')

@section('title', 'Insumo')

@section('content_header')
    <a href="{{ url('/insumo/create') }}" class="text-decoration-none text-white">
        <button type="submit" class="btn btn-primary">Agregar Insumos</button>
    </a>
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
        <div class="card-body">
            <table class="table table-striped" id="datos">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col">Nombre</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Presentacion</th>
                        <th scope="col">Vida Util</th>
                        <th scope="col">Clasif.Riesgo</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($insumos as $insumo)
                        <tr>
                            <td>{{ $insumo->nombre }}</td>
                            <td>{{ $insumo->marca->nombre }}</td>
                            <td>{{ $insumo->presentacione->nombre }}</td>
                            <td>{{ $insumo->vida_util }}</td>
                            <td>{{ $insumo->riesgo }}</td>
                            <td>{{ $insumo->stock }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalInsumo-{{ $insumo->id }}"><i class="fa fa-eye"
                                            aria-hidden="true"></i>
                                    </button>
                                </div>
                               
                                    <div class="btn-group" role="group">
                                        <a href="{{ url('/insumo/' . $insumo->id . '/edit') }}"
                                            class="text-decoration-none text-white">
                                            <button type="submit" class="btn btn-warning"><i class="fa fa-file"
                                                    aria-hidden="true"></i></button></a>
                                    </div>
                            
                                <div class="btn-group" role="group">
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title font-bold" id="exampleModalLabel"></h4>
                    </div>
                    <div class="modal-body text-center">
                        <label class="text-center font-bold">
                            <h4>{{ $insumo->nombre }}</h4>
                        </label>
                        <div class="mb-3 border-b pb-3">
                            <label class="block">Descripción:</label>
                            <span class="block">{{ $insumo->descripcion }}</span>
                        </div>
                        <div class="mb-3">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                        <th>Invima</th>
                                        <th>Lote</th>
                                        <th>Fecha de Vencimiento</th>
                                        <th>Cantidad</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($insumo->caracteristicas as $caracteristica)
                                        @if ($caracteristica->cantidad > 0)
                                            <tr>
                                                <td>{{ $caracteristica->invima }}</td>
                                                <td>{{ $caracteristica->lote }}</td>
                                                <td>{{ \Carbon\Carbon::parse($caracteristica->vencimiento)->format('d-m-Y') }}
                                                </td>
                                                <td>{{ $caracteristica->cantidad }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ url('/insumo/' . $insumo->id . '/caracteristica/' . $caracteristica->id . '/edit') }}"
                                                            class="text-decoration-none text-white">
                                                            <button type="submit" class="btn btn-warning"><i
                                                                    class="fa fa-file"
                                                                    aria-hidden="true"></i></button></a>
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
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/js.js"></script>
    <script>
        function confirmDelete(insumoId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción se puede revertir.',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "##3085d6",
                confirmButtonText: "Confirmar"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Accion Exitosa",
                        icon: "success"
                    });
                    document.getElementById('delete-form-' + insumoId).submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectCategoria = document.getElementById('id_categoria');

            // Escuchar cambios en el select de categoría
            selectCategoria.addEventListener('change', function() {
                document.getElementById('filterForm')
                    .submit(); // Enviar el formulario al cambiar la categoría
            });

            const selectPageSize = document.getElementById('pageSize');

            // Obtener el tamaño de página de la URL actual
            const urlParams = new URLSearchParams(window.location.search);
            const pageSizeFromUrl = urlParams.get('page_size');

            // Establecer el valor del tamaño de página en el select
            if (pageSizeFromUrl) {
                selectPageSize.value = pageSizeFromUrl;
            }

            // Escuchar cambios en el select de tamaño de página
            selectPageSize.addEventListener('change', function() {
                const pageSize = this.value;
                document.getElementById('pageSizeHidden').value = pageSize; // Actualizar el campo oculto
                document.getElementById('filterForm').submit(); // Enviar el formulario
            });
        });

        //buscador automatic
        $(document).ready(function() {

            $("#search").keyup(function() {

                _this = this;

                $.each($("#datos tbody tr"), function() {

                    if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)

                        $(this).hide();

                    else

                        $(this).show();
                });
            });
        });
    </script>
@stop
