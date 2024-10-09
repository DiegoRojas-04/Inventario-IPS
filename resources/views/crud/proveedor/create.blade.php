@extends('adminlte::page')

@section('title', 'Proveedor')

@section('content_header')
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
                icon: "success",
                title: "Proveedor Agregado"
            });
        </script>
    @endif
    <a href="{{ url('/proveedor') }}" class="text-decoration-none text-white">
        <button type="submit" class="btn btn-primary ">Ver Proveedores</button></a>
@stop
{{--  --}}
@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Crear Proveedores</h1>
        </div>
        <div class="card-body">
            <form action="{{ url('/proveedor') }}" method="POST">
                {{ csrf_field() }}
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Nombre del Proveedor:</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}">
                        @error('nombre')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Descripcion del Proveedor:</label>
                        <input type="text" name="descripcion"
                            class="form-control @error('descripcion') is-invalid @enderror"
                            value="{{ old('descripcion') }}">
                        @error('descripcion')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>NIT:</label>
                        <input type="text" name="nit" class="form-control @error('nit') is-invalid @enderror"
                            value="{{ old('nit') }}">
                        @error('nit')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Telefono:</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                            value="{{ old('telefono') }}">
                        @error('telefono')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Correo Electronico:</label>
                        <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Direccion:</label>
                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                            value="{{ old('direccion') }}">
                        @error('direccion')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <br>
                        <button type="submit" class="btn bg-blue">{{ 'Agregar' }}</button>
                    </div>
                </div>
            </form>
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
