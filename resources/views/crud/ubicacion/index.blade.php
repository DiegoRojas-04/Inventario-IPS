@extends('adminlte::page')

@section('title', 'Consultorios')

@section('content_header')
    <h1>Ubicacion</h1>
@stop

@section('content')
    <div class="row">
        <!-- Formulario de creación de consultorios -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Agregar Ubicacion</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('ubicaciones.store') }}" method="POST">
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
                    <h3 class="card-title">Lista de Ubicaciones</h3>
                </div>
                <div class="card-body">
                    @if ($ubicaciones->count() > 0)
                        <table class="table table-striped">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($ubicaciones as $ubicacion)
                                    <tr>
                                        <td>{{ $ubicacion->nombre }}</td>
                                        <td>{{ $ubicacion->descripcion }}</td>
                                        <td>
                                            <a href="{{ route('ubicaciones.edit', $ubicacion->id) }}"
                                                class="btn btn-warning"><i class="fa fa-file" aria-hidden="true"></i></a>
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- {{ $ubicacion->links() }} --}}
                    @else
                        <p>No hay ubicaciones disponibles.</p>
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
