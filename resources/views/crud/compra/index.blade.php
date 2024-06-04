@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @if (session('Mensaje'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Compra Exitosa"
            });
        </script>
    @endif
    <div class="form-row">
        <div class="col-sm-12 d-flex align-items-center justify-content-between">
            <a href="{{ url('/compra/create') }}" class="text-decoration-none text-white">
                <button type="submit" class="btn btn-primary">Agregar Compra</button>
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ url('/compra') }}" method="get">
                <div class="form-row">
                    <div class="col-md-7">

                    </div>
                    <div class="col-md-2">
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">

            @if ($compras->isEmpty())
                <div class="alert alert text-center " role="alert">
                    No hay compras en el rango de fechas seleccionadas
                </div>
            @else
                <table class="table">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>Proveedor</th>
                            <th>N°Comprobante</th>
                            <th>Comprobante</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($compras as $item)
                            <tr>
                                <td>{{ $item->proveedor->nombre }}</td>
                                <td>{{ $item->numero_comprobante }}</td>
                                <td>{{ $item->comprobante->tipo_comprobante }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->fecha_hora)->format('H:i:s') }}</td>
                                <td>

                                    <div class="btn-group" role="group">
                                        <form action="{{ route('compra.show', ['compra' => $item]) }}" method="get">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-eye"
                                                    aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $compras->appends(request()->query())->links() }}
            @endif
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
