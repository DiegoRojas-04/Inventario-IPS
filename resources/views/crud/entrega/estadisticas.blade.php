@extends('adminlte::page')

@section('title', 'Entrega')

@section('content_header')
    <div class="container-fluid">
        <!-- Filtro de Fechas -->
        <div class="card">
            <div class="card-header">
                <form action="{{ url('/entregas/estadisticas') }}" method="get">
                    <div class="form-row">
                        <div class="col-md-4 text-center">
                            <h4>Entregas Realizadas</h4>
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
                    <h4>Valor Total de Entregas: {{ number_format($valorTotalEntrega, 2) }} </h4>
                @endif
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>Insumo</th>
                            <th>Fecha de Entrega</th>
                            <th>Valor Unitario</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if ($entregas->isEmpty())
                            <tr>
                                <td colspan="5">No hay entregas en las fechas seleccionadas.</td>
                            </tr>
                        @else
                            @foreach ($entregas as $entrega)
                                <tr>
                                    <td>{{ $entrega->insumo_nombre }}</td>
                                    <td>{{ $entrega->created_at->format('d-m-Y') }}</td>
                                    <td>{{ number_format($entrega->valor_unitario) }}</td>
                                    <td>{{ $entrega->cantidad }}</td>
                                    <td>{{ number_format($entrega->valor_unitario * $entrega->cantidad) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <!-- Paginación -->
                {{ $entregas->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin, 'categoria_id' => $categoriaId])->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package for Entregas!");
    </script>
@stop
