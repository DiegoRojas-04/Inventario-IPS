@extends('adminlte::page')

@section('title', 'Kardex')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <h3>Kardex Mes de {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} de
                        {{ $selectedYear }}</h3>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('kardex.index') }}" method="GET">
                        <label for="id_categoria" class="sr-only">Categoría</label>
                        <select name="id_categoria" id="id_categoria" class="form-control">
                            <option value="">Todas las Categorias</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ request('id_categoria') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                </div>
                <div class="col-md-2">
                    <label for="mes" class="sr-only">Mes</label>
                    <select name="mes" id="mes" class="form-control">
                        @foreach (range(1, 12) as $month)
                            <option value="{{ $month }}" @if ($month == $selectedMonth) selected @endif>
                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="anno" class="sr-only">Año</label>
                    <input type="number" name="anno" id="anno" class="form-control" placeholder="Ingresa el Año"
                        value="{{ $selectedYear }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <div class="mb-2 row">
                <div class="col-md-11">
                    <div class="">
                        <button type="button" class="btn btn-success">
                            <a href="{{ route('export.excel', ['mes' => $selectedMonth, 'anno' => $selectedYear, 'id_categoria' => request('id_categoria')]) }}"
                                style="color: white; text-decoration: none;">
                                <i class="fa fa-file-excel" aria-hidden="true"></i> Excel
                            </a>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <a href="{{ route('export.pdf', ['mes' => $selectedMonth, 'anno' => $selectedYear, 'id_categoria' => request('id_categoria')]) }}"
                                style="color: white; text-decoration: none;">
                                <i class="fa fa-file-pdf" aria-hidden="true"></i> PDF
                            </a>
                        </button>
                        <button type="button" class="btn btn-primary">
                            <a href="{{ route('exporto.pdf', ['mes' => $selectedMonth, 'anno' => $selectedYear, 'id_categoria' => request('id_categoria')]) }}"
                                style="color: white; text-decoration: none;">
                                <i class="fa fa-file-pdf" aria-hidden="true"></i> Pedido
                            </a>
                        </button>
                    </div>
                </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <select class="form-control" id="pageSize" name="page_size">
                                <option value="10">#</option>
                                <option value="5" {{ request('page_size') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request('page_size') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('page_size') == 20 ? 'selected' : '' }}>20</option>
                                <option value="30" {{ request('page_size') == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="text-center thead-dark">
                        <tr>
                            <th>Insumo</th>
                            <th>Inicio Mes</th>
                            <th>Ingresos</th>
                            <th>Egresos</th>
                            <th>Saldo</th>
                            <th>Informe</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($insumos as $insumo)
                            <tr>
                                <td>{{ $insumo->nombre }}</td>
                                <td>{{ round($insumo->cantidad_inicial_mes) }}</td>
                                <td>{{ round($insumo->ingresos_mes) }}</td>
                                <td>{{ round($insumo->egresos_mes) }}</td>
                                <td>{{ round($insumo->saldo_final_mes) }}</td>
                                <td>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#modalIngresos{{ $insumo->id }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#modalEgresos{{ $insumo->id }}">
                                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $insumos->links() }}
            </div>
        </div>
    </div>

    @foreach ($insumos as $insumo)
        <!-- Modal de ingresos -->
        <div class="modal fade" id="modalIngresos{{ $insumo->id }}" tabindex="-1"
            aria-labelledby="modalIngresosLabel{{ $insumo->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalIngresosLabel{{ $insumo->id }}">Detalles de Ingresos -
                            {{ $insumo->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>Invima</th>
                                    <th>Lote</th>
                                    <th>Fecha de Vencimiento</th>
                                    <th>Cantidad</th>
                                    <th>Compra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insumo->caracteristicas as $caracteristica)
                                    @if (\Carbon\Carbon::parse($caracteristica->created_at)->month == $selectedMonth)
                                        <tr class="text-center">
                                            <td>{{ $caracteristica->invima }}</td>
                                            <td>{{ $caracteristica->lote }}</td>
                                            <td>{{ $caracteristica->vencimiento }}</td>
                                            <td>{{ $caracteristica->cantidad_compra }}</td>
                                            <td>{{ \Carbon\Carbon::parse($caracteristica->created_at)->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <h6>Total Ingresos: <td>{{ round($insumo->ingresos_mes) }}</td>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal de egresos -->
        <div class="modal fade" id="modalEgresos{{ $insumo->id }}" tabindex="-1"
            aria-labelledby="modalEgresosLabel{{ $insumo->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEgresosLabel{{ $insumo->id }}">Detalles de Egresos -
                            {{ $insumo->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>Invima</th>
                                    <th>Lote</th>
                                    <th>Fecha de Vencimiento</th>
                                    <th>Cantidad</th>
                                    <th>Fecha de Entrega</th>
                                    <th>Servicio</th> <!-- New Column -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insumo->entregas as $entrega)
                                    <!-- Adjusted loop -->
                                    @if (\Carbon\Carbon::parse($entrega->created_at)->month == $selectedMonth)
                                        <tr class="text-center">
                                            <td>{{ $entrega->pivot->invima }}</td>
                                            <td>{{ $entrega->pivot->lote }}</td>
                                            <td>{{ $entrega->pivot->vencimiento }}</td>
                                            <td>{{ $entrega->pivot->cantidad }}</td>
                                            <td>{{ \Carbon\Carbon::parse($entrega->created_at)->format('d/m/Y') }}</td>
                                            <td>{{ $entrega->servicio->nombre }}</td> <!-- Show the service name -->
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <h6>Total Egresos: <td>{{ round($insumo->egresos_mes) }}</td>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/js.js"></script>
@stop
