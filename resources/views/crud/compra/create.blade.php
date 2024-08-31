@extends('adminlte::page')

@section('title', 'Compra')

@section('content_header')

@stop

@section('content')


    <form id="compra-form" action="{{ url('/compra') }}" method="post">
        @csrf

        <div class="container-fluid mt-4">
            <div class="row gy-4">
                <div class="col-md-8">
                    <div class="text-white bg-primary p-1 text-center">
                        Detalles de Compra
                    </div>
                    <div class="p-3 border border-3 border-primary">
                        <div class="row">

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Insumos:</label>
                                <select data-size="8" title="Seleccionar Insumos..." data-live-search="true" name="nombre"
                                    id="nombre" data-style="btn-white" class="form-control selectpicker show-tick">
                                    @foreach ($insumos as $item)
                                        <option value="{{ $item->id }}" data-requiere-lote="{{ $item->requiere_lote }}"
                                            data-requiere-invima="{{ $item->requiere_invima }}">
                                            {{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2" id="campos_invima" style="display: none;">
                                <label for="invima" class="form-label">Invima:</label>
                                <input type="text" id="invima" name="arraycaracteristicas[0][invima]"
                                    class="form-control">
                            </div>

                            <div class="col-md-6 mb-2" id="campos_lote_fecha" style="display: none;">
                                <label for="lote">Lote:</label>
                                <input type="text" id="lote" name="arraycaracteristicas[0][lote]"
                                    class="form-control">
                            </div>
                            <div class="col-md-6 mb-2" id="campos_vencimiento" style="display: none;">
                                <label for="vencimiento">Fecha de Vencimiento:</label>
                                <input type="date" id="vencimiento" name="arraycaracteristicas[0][vencimiento]"
                                    class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Cantidad:</label>
                                <input type="number" name="stock" id="stock" class="form-control" placeholder="0">
                            </div>

                            <div class="col-md-12 mb-2 mt-2 text-right">
                                <button id="btn_agregar" class="btn btn-primary" type="button">Agregar</button>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tabla_detalle">
                                        <thead class="bg-primary text-white text-center">
                                            <tr>
                                                <th>#</th>
                                                <th>Insumo</th>
                                                <th>Invima</th>
                                                <th>Lote</th>
                                                <th>Fecha</th>
                                                <th>Cantidad</th>
                                                <th><i class="fa fa-trash"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th>Total</th>
                                                <th><span id="total">0</span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modalCancelar"
                                    data-bs-target="#exampleModal">
                                    Cancelar Compra
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-white bg-success p-1 text-center">
                        Datos Generales
                    </div>
                    <div class="p-3 border border-3 border-success">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Proveedores:</label>
                                <select data-size="5" title="Seleccionar Proveedor..." data-live-search="true"
                                    data-style="btn-white" name="proveedor_id" id="proveedor_id"
                                    class="form-control selectpicker show-tick" required>
                                    @foreach ($proveedores as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-12 mb-2">
                                <label for="comprobante_id" class="form-label">Comprobante:</label>
                                <input type="text" id="comprobante_id" class="form-control"
                                    value="{{ $comprobanteCompra->tipo_comprobante }}" readonly>
                                <input type="hidden" name="comprobante_id" value="{{ $comprobanteCompra->id }}">
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Numero de Comprobante:</label>
                                <input required type="text" name="numero_comprobante" id="numero_comprobante"
                                    class="form-control" value="{{ $numero_comprobante }}" readonly>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Fecha:</label>
                                <input readonly type="date" name="fecha" id="fecha" class="form-control"
                                    value="<?php echo date('Y-m-d'); ?>">
                                <?php
                                use Carbon\Carbon;
                                $fecha_hora = Carbon::now()->toDateTimeString();
                                ?>
                                <input type="hidden" name="fecha_hora" value="{{ $fecha_hora }}">
                            </div>

                            <div class="col-md-12 mb-2 text-center">
                                <button type="button" class="btn btn-success"
                                    onclick="confirmAndSubmit(event)">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <style>
        #centrar {
            width: 200px
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
        $(document).ready(function() {
            // Función para manejar el cambio en el select
            $('#nombre').change(function() {
                // Obtener el valor del insumo seleccionado
                let id_insumo = $(this).val();

                // Obtener si requiere lote y mostrar u ocultar los campos
                let requiere_lote = $(this).find('option:selected').data('requiere-lote');
                if (requiere_lote == 1) {
                    mostrarCamposLote();
                } else {
                    ocultarCamposLote();
                }

                // Obtener si requiere invima y mostrar u ocultar los campos
                let requiere_invima = $(this).find('option:selected').data('requiere-invima');
                if (requiere_invima == 1) {
                    mostrarCamposInvima();
                } else {
                    ocultarCamposInvima();
                }
            });
        });

        // Funciones para mostrar y ocultar campos
        function mostrarCamposLote() {
            $('#campos_lote_fecha').show();
            $('#campos_vencimiento').show();
        }

        function ocultarCamposLote() {
            $('#campos_lote_fecha').hide();
            $('#campos_vencimiento').hide();
        }

        function mostrarCamposInvima() {
            $('#campos_invima').show();
        }

        function ocultarCamposInvima() {
            $('#campos_invima').hide();
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#btn_agregar').click(function() {
                agregarinsumo();
            });
        });

        let cont = 0;
        let total = 0;

        function agregarinsumo() {
            let id_insumo = $('#nombre').val();
            let nameinsumo = $('#nombre option:selected').text();
            let cantidad = parseInt($('#stock').val());
            let lote = $('#lote').val();
            let vencimiento = $('#vencimiento').val();
            let invima = $('#invima').val();

            // Verificar si todos los campos lote, vencimiento e invima están vacíos
            // Si están vacíos, asignarles los valores 'NR' y '0001-01-01' respectivamente
            if (lote.trim() === '' && vencimiento.trim() === '' && invima.trim() === '') {
                lote = 'NR';
                vencimiento = '0001-01-01';
                invima = 'NR';
            } else {
                // Verificar y asignar valores predeterminados para cada campo individualmente
                if (lote.trim() === '') {
                    lote = 'NR';
                }

                if (vencimiento.trim() === '') {
                    vencimiento = '0001-01-01';
                }

                if (invima.trim() === '') {
                    invima = 'NR';
                }
            }

            if (id_insumo != '' && nameinsumo != '' && cantidad != '') {
                if (cantidad > 0 && (cantidad % 1 == 0)) {
                    let fila = '<tr id="fila' + cont + '">' +
                        '<th>' + (cont + 1) + '</th>' +
                        '<td><input type="hidden" name="arrayidinsumo[]" value="' + id_insumo + '">' + nameinsumo +
                        '</td>' +
                        '<td><input type="hidden" name="arraycaracteristicas[' + cont + '][invima]" value="' + invima +
                        '">' + invima + '</td>' +
                        '<td><input type="hidden" name="arraycaracteristicas[' + cont + '][lote]" value="' + lote + '">' +
                        lote + '</td>' +
                        '<td><input type="hidden" name="arraycaracteristicas[' + cont + '][vencimiento]" value="' +
                        vencimiento + '">' + vencimiento + '</td>' +
                        '<td  id="centrar">' +

                        '<div class="input-group">' +
                        '<button class="btn btn-outline-danger btn-sm" type="button" onclick="disminuirCantidad(' + cont +
                        ')"><i class="fa fa-minus"></i></button>' +
                        '<input type="number" name="arraycantidad[]" value="' + cantidad +
                        '" class="form-control text-center" readonly>' +
                        '<button class="btn btn-outline-success btn-sm" type="button" onclick="aumentarCantidad(' + cont +
                        ')"><i class="fa fa-plus"></i></button>' +
                        '</div>' +
                        '</td>' +
                        '<td><button class="btn btn-danger" type="button" onClick="eliminarInsumo(' + cont +
                        ')"><i class="fa fa-trash"></i></button></td>' +
                        '</tr>';

                    $('#tabla_detalle tbody').append(fila);
                    limpiarCampos();
                    cont++;
                    total += cantidad;
                    $('#total').html(total);
                } else {
                    showModal('Valores Incorrectos')
                }
            } else {
                showModal('Campos Obligatorios')
            }
        }

        function limpiarCampos() {
            let selectNombre = $('#nombre');
            let selectVariante = $('#variante');

            selectNombre.selectpicker('val', ''); // Limpiar select de nombre
            selectVariante.selectpicker('val', ''); // Limpiar select de variante
            $('#stock').val('');
            $('#invima').val('');
            $('#lote').val('');
            $('#vencimiento').val('');

        }

        function eliminarInsumo(indice) {
            let cantidadEliminada = parseInt($('#fila' + indice).find('input[name="arraycantidad[]"]').val());
            total -= cantidadEliminada;
            $('#fila' + indice).remove();
            $('#total').html(total);
        }

        function aumentarCantidad(indice) {
            let cantidadInput = $('#fila' + indice).find('input[name="arraycantidad[]"]');
            let cantidad = parseInt(cantidadInput.val());
            cantidad++;
            cantidadInput.val(cantidad);
            total++;
            $('#total').html(total);
        }

        function disminuirCantidad(indice) {
            let cantidadInput = $('#fila' + indice).find('input[name="arraycantidad[]"]');
            let cantidad = parseInt(cantidadInput.val());
            if (cantidad > 1) {
                cantidad--;
                cantidadInput.val(cantidad);
                total--;
                $('#total').html(total);
            } else {
                eliminarInsumo(indice);
            }
        }

        function showModal(message, icon = 'error') {
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
            })
            Toast.fire({
                icon: icon,
                title: message
            })
        }

        function confirmAndSubmit(event) {
            event.preventDefault();
            let proveedor = $('#proveedor_id').val();
            let comprobante = $('#comprobante_id').val();
            let numeroComprobante = $('#numero_comprobante').val();
            let rows = $('#tabla_detalle tbody tr').length;

            if (!proveedor || !comprobante || !numeroComprobante || rows === 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se puede realizar la compra'
                });
            } else {
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Desea realizar la compra',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#55aa38',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirmar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Acción Exitosa',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(() => {
                            $('#compra-form').submit();
                        }, 1500);
                    }
                });
            }
        }
    </script>

@stop
