@extends('adminlte::page')

@section('title', 'Entrega')

@section('content_header')
    <div class="form-row">
        <div class="col-sm-12 d-flex align-items-center justify-content-between">
            <a href="{{ url('/entrega') }}" class="text-decoration-none text-white">
                <button type="submit" class="btn btn-primary">Ver Entregas</button>
            </a>
        </div>
    @stop

    @section('content')
        <div class="container-fluid w-100 border border-3 rounded p-4 mt-3">
            <div class="row mb-2">
                <div class="col-sm-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-user-md"></i></span>
                        <input disabled type="text" class="form-control" value="Entrega realizada Por:">
                    </div>
                </div>
                <div class="col-sm-8">
                    <input disabled type="text" class="form-control" value="{{ $entrega->user->name }}">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-users"></i></span>
                        <input disabled type="text" class="form-control" value="Entrega realizada a:">
                    </div>
                </div>
                <div class="col-sm-8">
                    <input disabled type="text" class="form-control" value="{{ $entrega->servicio->nombre }}">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-file"></i></span>
                        <input disabled type="text" class="form-control" value="Tipo de Comprobante:">
                    </div>
                </div>
                <div class="col-sm-8">
                    <input disabled type="text" class="form-control"
                        value="{{ $entrega->comprobante->tipo_comprobante }}">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-list-ol"></i></span>
                        <input disabled type="text" class="form-control" value="Numero de Comprobante:">
                    </div>
                </div>
                <div class="col-sm-8">
                    <input disabled type="text" class="form-control" value="{{ $entrega->numero_comprobante }}">
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
                        value="{{ \Carbon\Carbon::parse($entrega->fecha_hora)->format('d-m-Y') }}">
                </div>
                <div class="col-sm-4">
                    <input disabled type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($entrega->fecha_hora)->format('H:i:s') }}">
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header text-center">
                    <h4>Detalle de Entrega</h4>
                </div>
                <div class="card-body table-responsive">
                    <div class="mb-3">
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-file-excel" aria-hidden="true"></i>
                        </button>
                        <form method="GET" action="{{ route('export.entrega.pdf', ['id' => $entrega->id]) }}"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger" style="color: white; text-decoration: none;">
                                <i class="fa fa-file-pdf" aria-hidden="true"></i> PDF
                            </button>
                        </form>
                    </div>
                    <table class="table table-striped text-center">
                        <thead class="bg-primary text-white">
                            <tr class="text-center">
                                <th>Producto</th>
                                <th>Marca</th>
                                <th>Presentación</th>
                                <th>Invima</th>
                                <th>Lote</th>
                                <th>Vencimiento</th>
                                <th>Valor</th>
                                <th>Cantidad</th>
                                <th>SubTotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entrega->insumoEntregas as $entregaInsumo)
                                <tr>
                                    <td>{{ $entregaInsumo->insumo->nombre }}</td>
                                    <td>{{ $entregaInsumo->marca->nombre ?? 'N/A' }}</td>
                                    <td>{{ $entregaInsumo->presentacion->nombre ?? 'N/A' }}</td>
                                    <td>{{ $entregaInsumo->invima }}</td>
                                    <td>{{ $entregaInsumo->lote }}</td>
                                    <td>{{ $entregaInsumo->vencimiento }}</td>
                                    <td>{{ number_format($entregaInsumo->valor_unitario) }}</td>
                                    <td>{{ $entregaInsumo->cantidad }}</td>
                                    <td>{{ number_format($entregaInsumo->valor_unitario * $entregaInsumo->cantidad) }}

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                            <h5>Total de la Entrega: <span class="text">{{ number_format($totalEntrega) }}</span></h5>
                    </div>
                </div>
            </div>
        </div>
    @stop

    @section('css')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stop

    @section('js')
        <script>
            console.log("Hi, I'm using the Laravel-AdminLTE package!");
        </script>
    @stop
