@extends('adminlte::page')

@section('title', 'Proveedor')

@section('content_header')
    <h1></h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Editar Proveedor</h1>
        </div>
        <div class="card-body">
            <form action="{{ url('proveedor/' . $proveedor->id) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PATCH') }}

                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Nombre del Proveedor:</label>
                        <input type="text" name="nombre" value="{{ $proveedor->nombre }}"
                            class="form-control @error('nombre') is-invalid @enderror">
                        @error('nombre')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Descripcion del Proveedor:</label>
                        <input type="text" name="descripcion" value="{{ $proveedor->descripcion }}"
                            class="form-control @error('descripcion') is-invalid @enderror">
                        @error('descripcion')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>NIT:</label>
                        <input type="text" name="nit" value="{{ $proveedor->nit }}"
                            class="form-control @error('nit') is-invalid @enderror">
                        @error('nit')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Telefono:</label>
                        <input type="number" name="telefono" value="{{ $proveedor->telefono }}"
                            class="form-control @error('telefono') is-invalid @enderror">
                        @error('telefono')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Correo Electronico:</label>
                        <input type="email" name="email" value="{{ $proveedor->email }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label>Direccion:</label>
                        <input type="text" name="direccion" value="{{ $proveedor->direccion }}"
                            class="form-control @error('direccion') is-invalid @enderror">
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
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
