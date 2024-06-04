@extends('adminlte::page')

@section('title', 'Insumo')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Vista principal Para Agregar un insumo</h1>
        </div>
        <div class="card-body">
            <form action="{{ url('insumo/' . $insumo->id) }}" method="POST" class="row g-3">
                {{ csrf_field() }}
                {{ method_field('PATCH') }}

                <div class="col-md-4">
                    <label>Nombre:</label>
                    <input type="text" name="nombre"  value="{{ old('nombre', $insumo->nombre)}}"
                        class="form-control  @error('nombre') is-invalid @enderror">
                    @error('nombre')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label>Descripcion:</label>
                    <input type="text" name="descripcion" value="{{ old('descripcion', $insumo->descripcion) }}"
                        class="form-control  @error('descripcion') is-invalid @enderror">
                    @error('descripcion')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-2 text-center">
                    <label>Invima</label>
                    <input type="checkbox" name="requiere_lote" class="form-control" value="1"
                        {{ $insumo->requiere_lote ? 'checked' : '' }}>
                </div>

                <div class="col-md-2 text-center">
                    <label>Lote Y Fecha</label>
                    <input type="checkbox" name="requiere_invima" class="form-control" value="1"
                        {{ $insumo->requiere_invima ? 'checked' : '' }}>
                </div>

                <div class="col-md-4">
                    <label>Categoría:</label>
                    <select data-live-search="true" name="id_categoria" id="id_categoria"
                        class="form-control selectpicker show-tick">
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('id_categoria', $insumo->id_categoria) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label>Marca:</label>
                    <select data-live-search="true" name="id_marca" id="id_marca"
                        class="form-control selectpicker show-tick">
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}" 
                                {{ old('id_marca', $insumo->id_marca) == $marca->id ? 'selected' : '' }}>
                                {{ $marca->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Presentación:</label>
                    <select data-live-search="true" name="id_presentacion" id="id_presentacion"
                        class="form-control selectpicker show-tick">
                        @foreach ($presentaciones as $presentacione)
                            <option value="{{ $presentacione->id }}"
                                {{ old('id_presentacion', $insumo->id_presentacion) == $presentacione->id ? 'selected' : '' }}>
                                {{ $presentacione->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-4">
                    <label>Clasificacion de Riesgo:</label>
                    <input type="text" name="riesgo" class="form-control" value="{{ $insumo->riesgo }}">
                </div>

                <div class="col-md-4">
                    <label>Vida Util:</label>
                    <input type="text" name="vida_util" class="form-control" value="{{ $insumo->vida_util }}">
                </div>

                <div class="col-md-4">
                    <label>Cantidad:</label>
                    <input type="text" class="form-control bg-white" value="{{ $insumo->stock }}" readonly>  
                </div>

                <div class="col-12">
                    <br>
                    <button type="submit" class="btn bg-blue">{{ 'Actualizar' }}</button>
                </div>
            </form>


        </div>
    </div>
@stop

@section('css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@stop
