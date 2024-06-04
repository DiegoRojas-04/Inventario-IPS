@extends('adminlte::page')

@section('title', 'Insumo')

@section('content_header')
    <h1></h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Vista principal Para Agregar un insumo</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('caracteristica.update', ['insumoId' => $insumo->id, 'caracteristicaId' => $caracteristica->id]) }}" method="POST" class="row g-2">
                @csrf
                @method('PATCH')
                <div class="form-group col-md-6">
                    <label for="cantidad">Cantidad:</label>
                    <input type="text" id="cantidad" name="cantidad"
                        value="{{ $caracteristica->cantidad }}" class="form-control  @error('cantidad') is-invalid @enderror">
                        @error('cantidad')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="invima">Invima:</label>
                    <input type="text" class="form-control" id="invima" name="invima"
                        value="{{ $caracteristica->invima }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="lote">Lote:</label>
                    <input type="text" class="form-control" id="lote" name="lote"
                        value="{{ $caracteristica->lote }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="vencimiento">Fecha de Vencimiento:</label>
                    <input type="date" class="form-control" id="vencimiento" name="vencimiento"
                        value="{{ $caracteristica->vencimiento }}">
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
