@extends('adminlte::page')

@section('title', 'Crear Elemento')

@section('content_header')
    <h1>Nuevo Elemento</h1>
@stop

@section('content')
    <div class="row">
        <!-- Formulario de creación de elementos -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Agregar Elemento</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('elementos.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                            @error('nombre')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría</label>
                            <select name="categoria" id="categoria" class="form-control" required>
                                <option value="">Selecciona una categoría</option>
                                <option value="Equipos">Equipos</option>
                                <option value="Insumos">Insumos</option>
                                <option value="Papeleria">Papeleria</option>
                            </select>
                            @error('categoria')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="cantidad_necesaria">Cantidad Necesaria</label>
                            <input type="number" name="cantidad_necesaria" id="cantidad_necesaria" class="form-control" required>
                            @error('cantidad_necesaria')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control">
                            @error('descripcion')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                        <a href="{{ route('elementos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de elementos -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Elementos</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('elementos.create') }}" class="mb-3">
                        <div class="form-row ">
                            <div class="col-md-10">
                                <select name="categoria" class="form-control">
                                    <option value="">Selecciona categoría</option>
                                    <option value="Equipos"
                                        {{ isset($categoriaSeleccionada) && $categoriaSeleccionada === 'Equipos' ? 'selected' : '' }}>
                                        Equipos</option>
                                    <option value="Insumos"
                                        {{ isset($categoriaSeleccionada) && $categoriaSeleccionada === 'Insumos' ? 'selected' : '' }}>
                                        Insumos</option>
                                    <option value="Papeleria"
                                        {{ isset($categoriaSeleccionada) && $categoriaSeleccionada === 'Papeleria' ? 'selected' : '' }}>
                                        Papeleria</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </div>
                        </div>
                    </form>
                    @if ($elementos->count() > 0)
                        <table class="table table-striped">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($elementos as $elemento)
                                    <tr>
                                        <td>{{ $elemento->nombre }}</td>
                                        <td>{{ $elemento->cantidad_necesaria }}</td>
                                        <td>{{ $elemento->categoria }}</td>
                                        <td>{{ $elemento->descripcion }}</td>
                                        <td>
                                            <a href="{{ route('elementos.edit', $elemento->id) }}"
                                                class="btn btn-warning"><i class="fa fa-file" aria-hidden="true"></i></a>
                                            <div class="btn-group" role="group">
                                                @if ($elemento->estado == 1)
                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#eliminar-{{ $elemento->id }}"><i class="fa fa-trash"
                                                            aria-hidden="true"></i></button>
                                                @else
                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                        data-target="#eliminar-{{ $elemento->id }}"><i class="fa fa-share"
                                                            aria-hidden="true"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="eliminar-{{ $elemento->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                        {{ $elemento->estado == 1 ? 'Eliminar Elemento' : 'Restaurar Elemento' }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ $elemento->estado == 1 ? '¿Estás seguro que quieres eliminar este Elemento?' : '¿Estás seguro que quieres restaurar este Elemento?' }}
                                                    <br>
                                                    <h5>{{ $elemento->nombre }}</h5>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cerrar</button>
                                                    <form action="{{ url('/elementos/' . $elemento->id) }}"
                                                        method="POST">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button type="submit" class="btn btn-primary">Confirmar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No hay elementos disponibles.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script>
        @if (session('Mensaje'))
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
                title: "{{ session('Mensaje') }}"
            });
        @endif
    </script>

    <script>
        @if (session('Success'))
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
                title: "{{ session('Success') }}"
            });
        @endif
    </script>
@stop
