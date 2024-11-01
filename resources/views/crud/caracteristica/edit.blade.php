@extends('adminlte::page')

@section('title', 'Insumo')

@section('content_header')
    <h1></h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">{{ $insumo->nombre }}</h1>
        </div>
        <div class="card-body">
            <form
                action="{{ route('caracteristica.update', ['insumoId' => $insumo->id, 'caracteristicaId' => $caracteristica->id]) }}"
                method="POST" class="row g-2">
                @csrf
                @method('PATCH')

                <div class="form-group col-md-6">
                    <label>Marca:</label>
                    <select data-live-search="true" name="id_marca" id="id_marca"
                        class="form-control selectpicker show-tick">
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}"
                                {{ isset($caracteristica->id_marca) && $caracteristica->id_marca == $marca->id ? 'selected' : '' }}>
                                {{ $marca->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>Presentación:</label>
                    <select data-live-search="true" name="id_presentacion" id="id_presentacion"
                        class="form-control selectpicker show-tick">
                        @foreach ($presentaciones as $presentacione)
                            <option value="{{ $presentacione->id }}"
                                {{ isset($caracteristica->id_presentacion) && $caracteristica->id_presentacion == $presentacione->id ? 'selected' : '' }}>
                                {{ $presentacione->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="invima">Invima:</label>
                    <input type="text" class="form-control" id="invima" name="invima"
                        value="{{ $caracteristica->invima }}">
                </div>

                <div class="form-group col-md-4">
                    <label for="lote">Lote:</label>
                    <input type="text" class="form-control" id="lote" name="lote"
                        value="{{ $caracteristica->lote }}">
                </div>

                <div class="form-group col-md-4">
                    <label for="vencimiento">Fecha de Vencimiento:</label>
                    <input type="date" class="form-control" id="vencimiento" name="vencimiento"
                        value="{{ $caracteristica->vencimiento }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="cantidad">Cantidad:</label>
                    <input type="text" id="cantidad" name="cantidad" value="{{ $caracteristica->cantidad }}"
                        class="form-control  @error('cantidad') is-invalid @enderror" readonly>
                    @error('cantidad')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="valor_unitario">Valor unitario:</label>
                    <input type="text" id="valor_unitario" name="valor_unitario" value="{{ $caracteristica->valor_unitario }}"
                        class="form-control  @error('valor_unitario') is-invalid @enderror" readonly>
                    @error('valor_unitario')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
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
