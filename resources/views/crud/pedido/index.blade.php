@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <div class="form-row">
        <div class="col-sm-12 d-flex align-items-center justify-content-between">
            <a href="{{ url('/pedido/create') }}" class="text-decoration-none text-white">
                <button type="submit" class="btn btn-primary">Agregar Pedido</button>
            </a>
        </div>
    </div>
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
                icon: "error",
                title: "Insumo Eliminado"
            });
        </script>
    @endif
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

            @if ($pedidos->isEmpty())
                <div class="alert alert text-center " role="alert">
                    No hay pedidos en el rango de fechas seleccionados
                </div>
            @else
                <table class="table">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($pedidos as $pedido)
                            <tr @if ($pedido->tipo === 'Pedido Especial') class="table-success" @endif>
                                <td>{{ $pedido->id }}</td>
                                <td>{{ $pedido->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($pedido->fecha_hora)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($pedido->fecha_hora)->format('H:i:s') }}</td>
                                <td>{{ $pedido->tipo }}</td>
                                <td>
                                    <a href="{{ route('pedido.show', $pedido->id) }}" class="btn btn-info">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @stop

    @section('css')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stop

    @section('js')

    @stop
