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
                    class="form-control selectpicker show-tick" id="consultorio_id" name="consultorio_id">
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
            <form action="{{ route('elementos.actualizar.cantidades', $consultorio->id) }}" method="POST">
                @csrf
                <table class="table table-striped">
                    <thead class="thead-dark text-center">
                        <tr class="text-center">
                            <th>Nombre</th>
                            <th>Necesario</th>
                            <th>Cantidad</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consultorio->elementos->sortBy('nombre') as $elemento)
                                <tr class="text-center">
                                <td>{{ $elemento->nombre }}</td>
                                <td>{{ $elemento->cantidad_necesaria }}</td>
                                <td class="center">
                                    <div class="input-group">
                                        <button class="btn btn-outline-danger btn-sm" type="button"
                                            onclick="disminuirCantidad({{ $loop->index }})">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input type="number" id="cantidad_{{ $loop->index }}" name="cantidades[{{ $elemento->id }}]"
                                            value="{{ $elemento->pivot->cantidad }}" min="0" required
                                            class="form-control text-center" style="width: 60px;">
                                        <button class="btn btn-outline-success btn-sm" type="button"
                                            onclick="aumentarCantidad({{ $loop->index }})">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <select name="observaciones[{{ $elemento->id }}]" class="form-control">
                                        <option value="1" {{ $elemento->pivot->observacion == 1 ? 'selected' : '' }}>Buen Estado</option>
                                        <option value="2" {{ $elemento->pivot->observacion == 2 ? 'selected' : '' }}>Mal estado</option>
                                        <option value="3" {{ $elemento->pivot->observacion == 3 ? 'selected' : '' }}>Incompleto</option>
                                        <option value="4" {{ $elemento->pivot->observacion == 4 ? 'selected' : '' }}>No hay</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Actualizar Cantidades</button>
                </div>
            </form>
        @else
            {{-- Aquí puedes manejar el caso en el que no hay consultorio seleccionado --}}
        @endif
    </div>

    {{-- Tabla de elementos con cantidades totales --}}
    <div class="card mt-3">
        <div class="text-center p-1">
            <h4>Cantidad Necesaria Total de Elementos</h4>
        </div>
        <table class="table table-striped">
            <thead class="thead-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Cantidad Necesaria</th>
                    <th>Cantidad Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($todosLosElementos as $elemento)
                    <tr class="text-center">
                        <td>{{ $elemento->nombre }}</td>
                        <td>{{ $elemento->cantidad_necesaria * $elemento->consultorios->count() }}</td>
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
