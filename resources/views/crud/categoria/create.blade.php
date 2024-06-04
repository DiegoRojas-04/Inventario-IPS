@extends('adminlte::page')

@section('title', 'Categoria')

@section('content_header')
    <a href="{{ url('/categoria') }}" class="text-decoration-none text-white">
        <button type="submit" class="btn btn-primary ">Categorias</button></a>
@stop

@section('content')

    @if (session('Mensaje'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: "Categoria Agregada"
        });
    </script>
    @endif


    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Crear Categoria</h1>
        </div>
        <div class="card-body">
            <form action="{{ url('/categoria') }}" method="POST">
                {{ csrf_field() }}

                <label>Nombre de Categoria:</label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                    value="{{ old('nombre') }}">
                @error('nombre')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
                <br>
                <label>Descripcion de Categoria:</label>
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