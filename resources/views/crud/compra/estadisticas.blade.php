@extends('adminlte::page')

@section('title', 'Compra')

@section('content_header')
    <div class="container-fluid">
        <!-- Filtro de Fechas -->
        <div class="card">
            <div class="card-header">
                <form action="{{ url('/compras/estadisticas') }}" method="get">
                    <div class="form-row">
                        <div class="col-md-4 text-center">
                            <h4>Compras Realizadas</h4>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                                class="form-control">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}"
                                class="form-control">
                        </div>
                        <div class="col-md-3">
                            <select name="categoria_id" class="form-control">
                                <option value="">Seleccione Categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ $categoria->id == $categoriaId ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body text-center">
                @if ($fechaInicio && $fechaFin)
                    <h4>Valor de Compras: {{ number_format($valorTotalCompra, 2) }} </h4>
                @endif
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>Insumo</th>
                            <th>Fecha de Compra</th>
                            <th>Valor Unitario</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if ($insumos->isEmpty())
                            <tr>
                                <td colspan="5">No hay compras en las fechas seleccionadas.</td>
                            </tr>
                        @else
                            @foreach ($insumos as $insumo)
                                <tr>
                                    <td>{{ $insumo->insumo_nombre }}</td>
                                    <td>{{ $insumo->created_at->format('d-m-Y') }}</td>
                                    <td>{{ number_format($insumo->valor_unitario) }}</td>
                                    <td>{{ $insumo->cantidad_compra }}</td>
                                    <td>{{ number_format($insumo->valor_unitario * $insumo->cantidad_compra) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <!-- Paginación -->
                {{ $insumos->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin, 'categoria_id' => $categoriaId])->links() }}
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
