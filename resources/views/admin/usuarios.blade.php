@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')

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
                title: "Usuario Actualizado"
            });
        </script>
    @endif

    <div class="form-row">
        <div class="col-sm-12 d-flex align-items-center justify-content-between">
            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#agregar">Agregar Usuario</button>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row g-3">

                <div class="col-md-1">
                    <select class="form-control " id="pageSize">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>


                <div class="col-md-6">

                </div>


                <div class="col-md-5 input-group">
                    <input type="text" class="form-control" placeholder="Buscar" id="search">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-body">

            <table class="table">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Correo Electronico</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>

                                <div class="btn-group" role="group">
                                    <a href="{{ url('/usuario/' . $user->id . '/edit') }}" class="btn btn-warning"><i
                                            class="fa fa-edit"></i></a>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-danger" data-toggle="modal"
                                        data-target="#eliminar-{{ $user->id }}"><i class="fa fa-trash"
                                            aria-hidden="true"></i></button>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Modal -->
            <div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title" id="exampleModalLabel">
                                <h1 class="card-title">Crear Usuario</h1>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ url('/usuario') }}" method="POST">
                                        @csrf

                                        <div class="form-group">
                                            <label>Nombre:</label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" required placeholder="Nombre del Usuario">
                                            @error('name')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Correo Electrónico:</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required placeholder="Correo Electrónico">
                                            @error('email')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Contraseña:</label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" required
                                                placeholder="Contraseña">
                                            @error('password')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Confirmar Contraseña:</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                required placeholder="Confirmar Contraseña">
                                        </div>

                                        <div class="form-group">
                                            <label>Area:</label>
                                            <select name="servicio_id"
                                                class="form-control @error('servicio_id') is-invalid @enderror" required>
                                                <option value="">Seleccionar Area</option>
                                                @foreach ($servicios as $servicio)
                                                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('servicio_id')
                                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
