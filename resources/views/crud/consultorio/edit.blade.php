@extends('adminlte::page')

@section('title', 'Editar Consultorio')

@section('content_header')
    <h1>Editar Consultorio</h1>
@stop

@section('content')
    <form action="{{ route('consultorios.update', $consultorio->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre">Nombre del Consultorio</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $consultorio->nombre }}" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $consultorio->descripcion }}">
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('consultorios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@stop
