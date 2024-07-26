@extends('adminlte::page')

@section('title', 'Realizar Pedido')

@section('content_header')
    <div class="card-header text-center">
        <h1 class="card-title">Realizar Pedido</h1>
    </div>
@stop

@section('content')
    <div class="container w-100 border border-3 rounded p-4 mt-3">
        <div class="row">
            <div class="col-md-5">
                <form id="pedido-form" action="{{ route('pedido.store') }}" method="post">
                    @csrf
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fa fa-users"></i></span>
                                <input disabled type="text" class="form-control" value="Usuario:">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="usuario" class="form-control" value="{{ auth()->user()->name }}"
                                readonly>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <label for="insumo">Seleccionar Insumo:</label>
                            <select data-size="7" title="Seleccionar Insumos..." data-live-search="true" id="insumos"
                                data-style="btn-white" class="form-control selectpicker show-tick">
                                @foreach ($insumos as $insumo)
                                    <option value="{{ $insumo->id }}">{{ $insumo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-12">
                            <label for="restante">Restante:</label>
                            <input type="number" id="restantes" class="form-control" min="1" required>
                        </div>

                        <div class="col-sm-12">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" id="cantidades" class="form-control" min="1" required>
                        </div>

                        <div class="col-sm-12">
                            <label for="observacion">Observacion:</label>
                            <input type="text" id="observacion" name="observacion" class="form-control" required>
                        </div>

                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary mt-3 mb-3" onclick="agregarInsumo()">Agregar
                            Insumo</button>
                    </div>
                </form>
            </div>
            <div class="col-md-7">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-center thead-dark">
                            <tr>
                                <th>Insumo</th>
                                <th>Restante</th>
                                <th>Cantidad</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-insumos" class="text-center">
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-success mt-3 mb-3" onclick="confirmAndSubmit()">Realizar
                        Pedido</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        #centrar {
            width: 180px;
        }
    </style>
@stop

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <script>
        function agregarInsumo() {
            var insumoId = $('#insumos').val();
            var insumoNombre = $('#insumos option:selected').text();
            var cantidad = $('#cantidades').val();
            var restante = $('#restantes').val();
            if (insumoId && cantidad && restante) {
                var fila = '<tr>' +
                    '<td>' + insumoNombre + '<input type="hidden" name="arrayinsumos[]" value="' + insumoId + '"></td>' +
                    '<td>' + restante + '<input type="hidden" name="arrayrestantes[]" value="' + restante + '"></td>' +
                    '<td id="centrar">' +
                    '<div class="input-group">' +
                    '<button type="button" class="btn btn-outline-danger" onclick="disminuirCantidad(this)"><i class="fa fa-minus"></i></button>' +
                    '<input type="number" name="arraycantidades[]" value="' + cantidad +
                    '" class="form-control text-center" readonly>' +
                    '<button type="button" class="btn btn-outline-success" onclick="aumentarCantidad(this)"><i class="fa fa-plus"></i></button>' +
                    '</div>' +
                    '</td>' +
                    '<td><button type="button" class="btn btn-danger" onclick="eliminarFila(this)"><i class="fa fa-trash"></i> </button></td>' +
                    '</tr>';
                $('#tabla-insumos').append(fila);
                limpiarCampos();
            } else {
                showModal('Completa los campos Obligatorios.', 'error');
            }
        }

        function eliminarFila(btn) {
            $(btn).closest('tr').remove();
        }

        function confirmAndSubmit() {
            var tablaInsumos = $('#tabla-insumos tr').length;
            if (tablaInsumos == 0) {
                showModal('Debes agregar Insumos.', 'error');
                return;
            }
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Deseas Realizar El Pedido.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#55aa38',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var insumos = JSON.stringify(getInsumos());
                    var cantidades = JSON.stringify(getCantidades());
                    var restantes = JSON.stringify(getRestantes());
                    $('#pedido-form').append('<input type="hidden" name="insumos" value=\'' + insumos + '\'>');
                    $('#pedido-form').append('<input type="hidden" name="cantidades" value=\'' + cantidades +
                        '\'>');
                    $('#pedido-form').append('<input type="hidden" name="restantes" value=\'' + restantes + '\'>');
                    $('#pedido-form').submit();
                }
            });
        }

        function getRestantes() {
            var restantes = [];
            $('input[name="arrayrestantes[]"]').each(function() {
                restantes.push($(this).val());
            });
            return restantes;
        }

        function limpiarCampos() {
            $('#insumos').val('');
            $('#cantidades').val('');
            $('#restantes').val('');
        }

        function getInsumos() {
            var insumos = [];
            $('input[name="arrayinsumos[]"]').each(function() {
                insumos.push($(this).val());
            });
            return insumos;
        }

        function getCantidades() {
            var cantidades = [];
            $('input[name="arraycantidades[]"]').each(function() {
                cantidades.push($(this).val());
            });
            return cantidades;
        }

        function aumentarCantidad(btn) {
            var inputCantidad = $(btn).closest('.input-group').find('input[name="arraycantidades[]"]');
            var cantidad = parseInt(inputCantidad.val());
            cantidad++;
            inputCantidad.val(cantidad);
        }

        function disminuirCantidad(btn) {
            var inputCantidad = $(btn).closest('.input-group').find('input[name="arraycantidades[]"]');
            var cantidad = parseInt(inputCantidad.val());
            if (cantidad > 1) {
                cantidad--;
                inputCantidad.val(cantidad);
            } else {
                $(btn).closest('tr').remove();
            }
        }
    </script>

@stop
