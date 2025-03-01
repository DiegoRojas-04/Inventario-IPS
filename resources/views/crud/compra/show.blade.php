@extends('adminlte::page')

@section('title', 'Compra')

@section('content_header')
    <div class="form-row">
        <div class="col-sm-12 d-flex align-items-center justify-content-between">
            <a href="{{ url('/compra') }}" class="text-decoration-none text-white">
                <button type="submit" class="btn btn-primary">Ver Compras</button>
            </a>
        </div>
    @stop

    @section('content')
        <div class="container-fluid w-100 border border-3 rounded p-4 mt-3">
            <div class="row mb-2">
                <div class="col-sm-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-users"></i></span>
                        <input disabled type="text" class="form-control" value="Proveedor:">
                    </div>
                </div>
                <div class="col-sm-8">
                    <input disabled type="text" class="form-control" value="{{ $compra->proveedor->nombre }}">
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-folder"></i></span>
                        <input disabled type="text" class="form-control" value="Tipo de Comprobante:">
                    </div>
                </div>
                <div class="col-sm-8">
                    <input disabled type="text" class="form-control"
                        value="{{ $compra->comprobante->tipo_comprobante }}">
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
                    <input disabled type="text" class="form-control" value="{{ $compra->numero_comprobante }}">
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
                        value="{{ \Carbon\Carbon::parse($compra->fecha_hora)->format('d-m-Y') }}">
                </div>
                <div class="col-sm-4">
                    <input disabled type="text" class="form-control"
                        value="{{ \Carbon\Carbon::parse($compra->fecha_hora)->format('H:i:s') }}">
                </div>
            </div>

            <div class="card mb-4">

                <div class="card-header text-center">
                    <h4>Detalle de Compra</h4>
                </div>
                <div class="card-body table-responsive" style="font-size: 15px">
                    <div class="mb-3">
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-file-excel" aria-hidden="true"></i>
                        </button>
                        <form method="GET" action="{{ route('export.compra.pdf', ['id' => $compra->id]) }}"
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
                            @foreach ($insumosConCaracteristicas as $insumo)
                                @foreach ($insumo->caracteristicasCompra as $caracteristica)
                                    <tr>
                                        <td>{{ $insumo->nombre }}</td>
                                        <td>{{ $caracteristica->marca->nombre }}</td>
                                        <td>{{ $caracteristica->presentacion->nombre }}</td>
                                        <td>{{ $caracteristica->invima }}</td>
                                        <td>{{ $caracteristica->lote }}</td>
                                        <td>{{ $caracteristica->vencimiento }}</td>
                                        <td>{{ number_format($caracteristica->valor_unitario) }}</td>
                                        <td>{{ $caracteristica->cantidad_compra }}</td>
                                        <td>{{ number_format($caracteristica->valor_unitario * $caracteristica->cantidad_compra) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        <h5>Total de la compra: <span class="text">{{ number_format($totalCompra) }}</span></h5>
                    </div>
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
