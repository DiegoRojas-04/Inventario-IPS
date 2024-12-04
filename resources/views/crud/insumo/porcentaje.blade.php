@extends('adminlte::page')

@section('title', 'Insumo')

@section('content_header')
    <div style="display: flex; justify-content: space-between; align-items:center;">
        <div>
            <a href="{{ url('/insumo/create') }}" class="text-decoration-none text-white">
                <button type="submit" class="btn btn-primary">Agregar</button>
            </a>
            <a href="{{ url('insumo') }}" class="btn btn-primary">Insumos</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card container-fluid">
        <div class="card-header row g-3">
            <div class="col-md-2">
                <form id="filterForm" method="GET" action="{{ route('insumos.analisisPrecios') }}">
                    <select class="form-control" name="price_change" id="price_change">
                        <option value="">Filtrar Cambios</option>
                        <option value="up" {{ request('price_change') == 'up' ? 'selected' : '' }}>Subida</option>
                        <option value="down" {{ request('price_change') == 'down' ? 'selected' : '' }}>Bajada</option>
                        <option value="equal" {{ request('price_change') == 'equal' ? 'selected' : '' }}>Estables</option>
                    </select>
                </form>
            </div>

            <div class="col-md-5">
            </div>
            <div class="col-md-5 input-group">
                <input type="text" class="form-control" placeholder="Buscar" id="search" name="search"
                    value="{{ request('search') }}">
                <div class="input-group-prepend">
                    <button type="submit" class="btn" aria-disabled="true" style="pointer-events: none;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped text-center align-middle" style="font-size: 14px" id="datos">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th rowspan="2">Nombre</th>
                        <th colspan="3">Última Compra</th>
                        <th colspan="3">Nueva Compra</th>
                        <th rowspan="2">Porcentaje</th>
                    </tr>
                    <tr class="text-center">
                        <th>Proveedor</th>
                        <th>Fecha Compra</th>
                        <th>Valor</th>
                        <th>Proveedor</th>
                        <th>Fecha Compra</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($insumos as $insumo)
                        <tr>
                            <td class="nombre-ajustable align-middle">{{ $insumo['nombre'] }}</td>
                            <td class="align-middle">{{ $insumo['proveedor_penultima'] ?? 'NR' }}</td>
                            <td class="align-middle">
                                {{ isset($insumo['fecha_penultima']) ? \Carbon\Carbon::parse($insumo['fecha_penultima'])->format('d-m-Y') : 'NR' }}
                            </td>
                            <td class="align-middle">
                                {{ isset($insumo['valor_penultima']) ? number_format($insumo['valor_penultima'], 0, ',', '.') : 'NR' }}
                            </td>
                            <td class="align-middle">{{ $insumo['proveedor_ultima'] ?? 'NR' }}</td>
                            <td class="align-middle">
                                {{ isset($insumo['fecha_ultima']) ? \Carbon\Carbon::parse($insumo['fecha_ultima'])->format('d-m-Y') : 'NR' }}
                            </td>
                            <td class="align-middle">
                                {{ isset($insumo['valor_ultima']) ? number_format($insumo['valor_ultima'], 0, ',', '.') : 'NR' }}
                            </td>
                            <td
                                class="align-middle
                                @if ($insumo['diferencia_porcentaje'] > 0) bg-danger text-white;
                                @elseif ($insumo['diferencia_porcentaje'] < 0) bg-success text-white @endif">
                                @if ($insumo['diferencia_porcentaje'] !== null)
                                    {{ number_format($insumo['diferencia_porcentaje'], 2) }}%
                                    @if ($insumo['diferencia_porcentaje'] > 0)
                                        <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                    @elseif ($insumo['diferencia_porcentaje'] < 0)
                                        <i class="fa fa-arrow-down" aria-hidden="true"></i>
                                    @endif
                                @else
                                    NR
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <style>
        .nombre-ajustable {
            white-space: normal;
            word-wrap: break-word;
            max-width: 200px;
            text-align: center;
            vertical-align: middle;
        }

        .table td,
        .table th {
            vertical-align: middle;
            /* Centra verticalmente */
        }
    </style>
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#price_change').on('change', function() {
                $('#filterForm').submit();
            });
        });

        // Buscador automático
        $(document).ready(function() {
            $("#search").keyup(function() {
                _this = this;
                $.each($("#datos tbody tr"), function() {
                    if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
                        $(this).hide();
                    else
                        $(this).show();
                });
            });
        });
    </script>
@stop
