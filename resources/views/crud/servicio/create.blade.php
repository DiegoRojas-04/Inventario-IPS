@extends('adminlte::page')

@section('title', 'Area')

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
                title: "Area Agregada"
            });
        </script>
    @endif
    <a href="{{ url('/servicio') }}" class="text-decoration-none text-white">
        <button type="submit" class="btn btn-primary ">Ver Areas</button></a>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Crear Area</h1>
        </div>
        <div class="card-body">
            <form action="{{ url('/servicio') }}" method="POST">
                {{ csrf_field() }}
                <label>Nombre del Area:</label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                    value="{{ old('nombre') }}">
                @error('nombre')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
                <br>
                <label>Descripcion del Area:</label>
                <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                    value="{{ old('descripcion') }}">
                @error('descripcion')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
                <br>
                <button type="submit" class="btn bg-blue">{{ 'Agregar' }}</button>
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
