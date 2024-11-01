@extends('adminlte::page')

@section('title', 'Editar Ubicación')

@section('content_header')
    <h1>Editar Ubicación</h1>
@stop

@section('content')
    <form action="{{ route('ubicaciones.update', $ubicacion->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre de Ubicación</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $ubicacion->nombre) }}" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ old('descripcion', $ubicacion->descripcion) }}">
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('ubicaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@stop

