@extends('adminlte::page')

@section('title', 'Alertas de Elementos y Activos')

@section('content_header')
@stop

@section('content')
    <div class="row" style="font-size: 14px">
        <!-- Tabla de insumos con vencimiento próximo o vencidos -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">
                    <h6>Insumos Alerta</h6>
                </div>
                <div class="card-body text-center">
                    @if ($insumosVencidos->isEmpty())
                        <p>No hay insumos vencidos</p>
                    @else
                        <table class="table table-striped text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Insumo</th>
                                    <th>Vencimiento</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insumosVencidos as $insumo)
                                    <tr>
                                        <td>{{ $insumo->insumo }}</td>
                                        <td>{{ \Carbon\Carbon::parse($insumo->vencimiento)->format('d-m-Y') }}</td>
                                        <!-- Formato de la fecha -->
                                        <td>{{ $insumo->cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabla de elementos en mal estado (consultorio_elemento) -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">
                    <h6>Elementos en Mal Estado</h6>
                </div>
                <div class="card-body  text-center">
                    @if ($elementosMalEstado->isEmpty())
                        <p>No hay elementos en mal estado</p>
                    @else
                        <table class="table table-striped text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Consultorio</th>
                                    <th>Elemento</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($elementosMalEstado as $elemento)
                                    <tr>
                                        <td>{{ $elemento->consultorio }}</td>
                                        <td>{{ $elemento->elemento }}</td>
                                        <td>{{ $elemento->cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabla de activos en reparación (activos) -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">
                    <h6>Activos en Reparación</h6>
                </div>
                <div class="card-body  text-center">
                    @if ($activosReparacion->isEmpty())
                        <p>No hay activos en reparación</p>
                    @else
                        <table class="table table-striped text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activosReparacion as $activo)
                                    <tr>
                                        <td>{{ $activo->codigo }}</td>
                                        <td>{{ $activo->nombre }}</td>
                                        <td>{{ $activo->cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop