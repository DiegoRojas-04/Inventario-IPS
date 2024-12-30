@extends('adminlte::page')

@section('title', 'Detalle del Pedido')

@section('content_header')
    <div class="form-row">
        <div class="col-sm-12 d-flex align-items-center justify-content-between">
            <a href="{{ url('/pedido') }}" class="text-decoration-none text-white">
                <button type="submit" class="btn btn-primary">Ver Pedidos</button>
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container w-100 border border-3 rounded p-4 mt-3">
        <div class="row mb-2">
            <div class="col-sm-12">
                <input disabled type="text" class="form-control text-center bg-blue" value="{{ $pedido->tipo }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-4">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-users"></i></span>
                    <input disabled type="text" class="form-control" value="Usuario:">
                </div>
            </div>
            <div class="col-sm-8">
                <input disabled type="text" class="form-control" value="{{ $pedido->user->name }}">
            </div>
        </div>

        <!-- Nueva sección para mostrar el nombre del servicio -->
        <div class="row mb-2">
            <div class="col-sm-4">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-cogs"></i></span>
                    <input disabled type="text" class="form-control" value="Área:">
                </div>
            </div>
            <div class="col-sm-8">
                <input disabled type="text" class="form-control" value="{{ $pedido->user->servicio->nombre }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-sm-4">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    <input disabled type="text" class="form-control" value="Fecha y Hora:">
                </div>
            </div>
            <div class="col-sm-4">
                <input disabled type="text" class="form-control"
                    value="{{ \Carbon\Carbon::parse($pedido->fecha_hora)->format('d-m-Y') }}">
            </div>
            <div class="col-sm-4">
                <input disabled type="text" class="form-control"
                    value="{{ \Carbon\Carbon::parse($pedido->fecha_hora)->format('H:i:s') }}">
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header text-center">
                <h4>Detalle de Pedido</h4>
            </div>
            <div class="card-body table-responsive">
                <div class="mb-3">
                    <form method="GET" action="{{ route('export.pedido.pdf', ['id' => $pedido->id]) }}"
                        style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="color: white; text-decoration: none;">
                            <i class="fa fa-file-pdf" aria-hidden="true"></i> PDF
                        </button>
                    </form>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-2">
                        <div class="input-group mb-3">
                            <input disabled type="text" class="form-control" value="Observacion:">
                        </div>
                    </div>
                    <div class="col-sm-10">
                        <input disabled type="text" class="form-control" id="observacion" value="{{ $observacion }}">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped text-center">
                        <thead class="bg-primary text-white">
                            <tr class="text-center">
                                <th>Insumo</th>
                                <th>Última Entrega</th>
                                <th>Restante</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($insumosConUltimaEntrega as $insumo)
                                <tr>
                                    <td>{{ $insumo->nombre }}</td>
                                    <td>
                                        @if ($insumo->ultima_entrega)
                                            {{ $insumo->ultima_entrega->pivot->cantidad }}
                                        @else
                                            Sin pedido
                                        @endif
                                    </td>
                                    <td>{{ $insumo->pivot->restante }}</td>
                                    <td>{{ $insumo->pivot->cantidad }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@stop
