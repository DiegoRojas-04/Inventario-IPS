@extends('adminlte::page')

@section('title', 'Editar Elemento')

@section('content_header')
    <h1>Editar Elemento</h1>
@stop

@section('content')
    <form action="{{ route('elementos.update', $elemento->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre">Nombre del Elemento</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $elemento->nombre }}" required>
            @error('nombre')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="categoria">Categoría</label>
            <select name="categoria" id="categoria" class="form-control" required>
                <option value="Equipos" {{ $elemento->categoria == 'Equipos' ? 'selected' : '' }}>Equipos</option>
                <option value="Insumos" {{ $elemento->categoria == 'Insumos' ? 'selected' : '' }}>Insumos</option>
                <option value="Pepeleria" {{ $elemento->categoria == 'Pepeleria' ? 'selected' : '' }}>Pepeleria</option>
            </select>
            @error('categoria')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="cantidad_necesaria">Cantidad Necesaria</label>
            <input type="text" class="form-control" id="cantidad_necesaria" name="cantidad_necesaria" value="{{ $elemento->cantidad_necesaria }}" required>
            @error('cantidad_necesaria')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ $elemento->descripcion }}</textarea>
            @error('descripcion')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('elementos.create') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@stop
@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    
@stop
