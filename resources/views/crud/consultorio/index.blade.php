@extends('adminlte::page')

@section('title', 'Consultorios')

@section('content_header')
    <h1>Consultorios</h1>
@stop

@section('content')
    <div class="row">
        <!-- Formulario de creación de consultorios -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Agregar Consultorio</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('consultorios.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            @error('nombre')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion">
                            @error('descripcion')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de consultorios -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Consultorios</h3>
                </div>
                <div class="card-body">
                    @if ($consultorios->count() > 0)
                        <table class="table table-striped">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($consultorios as $consultorio)
                                    <tr>
                                        <td>{{ $consultorio->nombre }}</td>
                                        <td>{{ $consultorio->descripcion }}</td>
                                        <td>
                                            <a href="{{ route('consultorios.edit', $consultorio->id) }}"
                                                class="btn btn-warning"><i class="fa fa-file" aria-hidden="true"></i></a>
                                            <div class="btn-group" role="group">
                                                @if ($consultorio->estado == 1)
                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#eliminar-{{ $consultorio->id }}"><i
                                                            class="fa fa-trash" aria-hidden="true"></i></button>
                                                @else
                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                        data-target="#eliminar-{{ $consultorio->id }}"><i
                                                            class="fa fa-share" aria-hidden="true"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Modal de confirmación -->
                                    <div class="modal fade" id="eliminar-{{ $consultorio->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                        {{ $consultorio->estado == 1 ? 'Eliminar Consultorio' : 'Restaurar Consultorio' }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ $consultorio->estado == 1 ? '¿Estás seguro que quieres eliminar este consultorio?' : '¿Estás seguro que quieres restaurar este consultorio?' }}
                                                    <br>
                                                    <h5>{{ $consultorio->nombre }}</h5>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cerrar</button>
                                                    <form action="{{ url('/consultorios/' . $consultorio->id) }}"
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
                        {{ $consultorios->links() }} <!-- Paginación -->
                    @else
                        <p>No hay consultorios disponibles.</p>
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
