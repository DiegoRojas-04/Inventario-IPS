@extends('adminlte::page')

@section('title', 'Elementos')

@section('content_header')
    <h1>Elementos</h1>
@stop

@section('content')
    <form action="{{ route('elementos.index') }}" method="GET" class="mb-3">
        <div class="form-row align-items-center">
            <div class="col-auto">
                <select data-size="10" title="Seleccionar Consultorio..." data-live-search="true"
                class="form-control selectpicker show-tick"  id="consultorio_id" name="consultorio_id">
                    @foreach ($consultorios as $consultorio)
                        <option value="{{ $consultorio->id }}"
                            {{ request('consultorio_id') == $consultorio->id ? 'selected' : '' }}>
                            {{ $consultorio->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    <div class="card">
        @if (request('consultorio_id')) {{-- Solo muestra si hay un consultorio seleccionado --}}
            @php $consultorio = $consultorios->find(request('consultorio_id')); @endphp
            <div class="text-center p-1">
                <h4>{{ $consultorio->nombre }}</h4>
            </div>
            <table class="table table-striped">
                <thead class="thead-dark text-center">
                    <tr class="text-center">
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Observación</th>
                        <th>Actualizar</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consultorio->elementos as $elemento)
                        <tr class="text-center">
                            <td>{{ $elemento->nombre }}</td>
                            <td class="center">
                                <form action="{{ route('elementos.update.cantidad', $elemento->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="consultorio_id" value="{{ $consultorio->id }}">
                                    <div class="input-group">
                                        <button class="btn btn-outline-danger btn-sm" type="button"
                                            onclick="disminuirCantidad({{ $loop->index }})">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input type="number" id="cantidad_{{ $loop->index }}" name="cantidad"
                                            value="{{ $elemento->pivot->cantidad }}" min="0" required
                                            class="form-control text-center" style="width: 60px;">
                                        <button class="btn btn-outline-success btn-sm" type="button"
                                            onclick="aumentarCantidad({{ $loop->index }})">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                            </td>
                            <td>
                                <input type="text" name="observacion" class="form-control"
                                    value="{{ $elemento->pivot->observacion }}" placeholder="Escribe una observación">
                            </td>
                            

                            <td>
                                <button type="submit" class="btn btn-primary btn-sm"
                                    style="margin-left: 5px;">Actualizar</button>
                                </form>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $elemento->pivot->estado === 'bueno' ? 'badge-success' : 'badge-danger' }}"
                                    style="padding: 10px;">
                                    {{ ucfirst($elemento->pivot->estado) }}
                                </span>
                                <div class="btn-group" role="group">
                                    @if ($elemento->pivot->estado === 'bueno')
                                        <button type="button" class="btn btn-danger" data-toggle="modal"
                                            data-target="#cambiarEstado-{{ $elemento->id }}">
                                            <i class="fa fa-share" aria-hidden="true"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success" data-toggle="modal"
                                            data-target="#cambiarEstado-{{ $elemento->id }}">
                                            <i class="fa fa-share" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <div class="modal fade" id="cambiarEstado-{{ $elemento->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ $elemento->pivot->estado === 'bueno' ? 'Cambiar Estado a Malo' : 'Cambiar Estado a Bueno' }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{ $elemento->pivot->estado === 'bueno' ? '¿Estás seguro que quieres cambiar el estado a malo?' : '¿Estás seguro que quieres cambiar el estado a bueno?' }}
                                        <br>
                                        <h5>{{ $elemento->nombre }}</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                        <form action="{{ route('elementos.update.estado', $elemento->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="consultorio_id" value="{{ $consultorio->id }}">
                                            <input type="hidden" name="nuevo_estado"
                                                value="{{ $elemento->pivot->estado === 'bueno' ? 'malo' : 'bueno' }}">
                                            <button type="submit" class="btn btn-primary">Confirmar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </td>
                    @endforeach
                </tbody>
            </table>
        @else
        @endif
    </div>

    {{-- Tabla de elementos con cantidades totales --}}
    <div class="card mt-3">
        <div class="text-center p-1">
            <h4>Cantidad Total de Elementos</h4>
        </div>
        <table class="table table-striped">
            <thead class="thead-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Cantidad Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($todosLosElementos as $elemento)
                    <tr class="text-center">
                        <td>{{ $elemento->nombre }}</td>
                        <td>{{ $elemento->consultorios->sum('pivot.cantidad') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop

<style>
    .center {
        width: 160px;
    }
</style>

@section('css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <script>
        function aumentarCantidad(index) {
            var cantidadInput = document.getElementById('cantidad_' + index);
            cantidadInput.value = parseInt(cantidadInput.value) + 1;
        }

        function disminuirCantidad(index) {
            var cantidadInput = document.getElementById('cantidad_' + index);
            if (parseInt(cantidadInput.value) > 0) {
                cantidadInput.value = parseInt(cantidadInput.value) - 1;
            }
        }
    </script>

    <script>
        @if (session('Success'))
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
                title: "{{ session('Success') }}"
            });
        @endif
    </script>
@stop
