    @extends('adminlte::page')

    @section('title', 'Entrega')

    @section('content_header')

    @stop

    @section('content')

        <form id="entrega-form" action="{{ url('/entrega') }}" method="post">
            @csrf
            <div class="container-fluid mt-4">
                <div class="row gy-4">
                    <div class="col-md-8">
                        <div class="text-white bg-primary p-1 text-center">
                            Detalles de Entrega
                        </div>
                        <div class="p-3 border border-3 border-primary">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label">Insumos:</label>
                                    <select data-size="8" title="Seleccionar Insumos..." data-live-search="true"
                                        name="nombre" id="nombre" data-style="btn-white"
                                        class="form-control selectpicker show-tick">
                                        @foreach ($insumos as $item)
                                            <option value="{{ $item->id }}" data-barcode="{{ $item->codigo }}">
                                                {{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label">Variante de Insumo:</label>
                                    <select data-size="10" title="Seleccionar Variante..." data-live-search="true"
                                        name="variante" id="variante" data-style="btn-white"
                                        class="form-control selectpicker show-tick">
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">En Stock:</label>
                                    <input type="text" class="form-control" id="stock_actual" readonly>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Valor Unitario:</label>
                                    <input type="text" name="valor_unitario" id="valor_unitario" class="form-control"
                                        placeholder="0.00" readonly>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Cantidad:</label>
                                    <input type="number" name="stock" id="stock" class="form-control"
                                        placeholder="0">
                                </div>
                                <div class="col-md-12 mb-2 mt-2 text-right">
                                    <button id="btn_agregar" class="btn btn-primary" type="button">Agregar</button>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="tabla_detalle">
                                            <thead class="bg-primary text-white text-center">
                                                <tr>
                                                    {{-- <th>#</th> --}}
                                                    <th>Insumo</th>
                                                    <th>Invima</th>
                                                    <th>Lote</th>
                                                    <th>F.Venc</th>
                                                    <th>Marca</th>
                                                    <th>Presentación</th>
                                                    <th>Cantidad</th>
                                                    <th><i class="fa fa-trash"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                <th></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
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

                                <div class="col-md-12">

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
                                    <label for="" class="form-label">Entrega Para:</label>
                                    <select data-size="8" title="Entregar A:" data-live-search="true"
                                        data-style="btn-white" name="servicio_id" id="servicio_id"
                                        class="form-control selectpicker show-tick" required>
                                        @foreach ($servicios as $item)
                                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label for="comprobante_id" class="form-label">Comprobante:</label>
                                    <input type="text" id="comprobante_id" class="form-control"
                                        value="{{ $comprobanteEntrega->tipo_comprobante }}" readonly>
                                    <input type="hidden" name="comprobante_id" value="{{ $comprobanteEntrega->id }}">
                                </div>


                                <div class="col-md-12 mb-2">
                                    <label>Numero de Comprobante:</label>
                                    <input required type="text" name="numero_comprobante" id="numero_comprobante"
                                        class="form-control" value="{{ $numeroComprobante }}" readonly>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label>Fecha:</label>
                                    <input type="date" name="fecha" id="fecha" class="form-control"
                                        value="<?php echo date('Y-m-d'); ?>" >

                                    <input type="hidden" name="fecha_hora" id="fecha_hora" readonly>
                                </div>


                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                                <div class="col-md-12 mb-2 text-center">
                                    <button type="button" class="btn btn-success"
                                        onclick="confirmAndSubmit()">Guardar</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <style>
            #centrar {
                width: 160px;
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
            document.addEventListener('DOMContentLoaded', function() {
                const fechaInput = document.getElementById('fecha');
                const fechaHoraInput = document.getElementById('fecha_hora');

                function actualizarFechaHora() {
                    const fechaSeleccionada = fechaInput.value;
                    if (fechaSeleccionada) {
                        const ahora = new Date();
                        const horaActual = ahora.toTimeString().split(' ')[0]; // Hora en formato HH:mm:ss
                        fechaHoraInput.value = `${fechaSeleccionada} ${horaActual}`;
                    }
                }

                // Actualizar el campo al cambiar la fecha
                fechaInput.addEventListener('change', actualizarFechaHora);

                // Actualizar el campo al cargar la página
                actualizarFechaHora();
            });
        </script>
        <script>
            $(document).ready(function() {
                let barcode = ''; // Variable para almacenar el código de barras
                let isBarcodeProcessing = false; // Variable para controlar si se está procesando un código de barras

                // Capturar el evento de keydown en todo el documento
                $(document).keydown(function(e) {
                    // Verificar si no se está procesando un código de barras
                    if (!isBarcodeProcessing) {
                        // Verificar si el foco está en el área de entrada de cantidad, insumos, variante o número de comprobante
                        if (!$('#stock').is(':focus') && !$('#numero_comprobante').is(':focus') && !$('#nombre')
                            .is(':focus') && !$('#variante').is(':focus')) {
                            // Si la tecla presionada es 'Enter', buscar y seleccionar el insumo
                            if (e.key === 'Enter') {
                                // Desactivar temporalmente el evento keydown
                                isBarcodeProcessing = true;

                                let matchedOption = $('#nombre option').filter(function() {
                                    // Buscar el código de barras en los datos personalizados del option
                                    return $(this).data('barcode') == barcode;
                                });

                                if (matchedOption.length > 0) {
                                    // Limpiar selección previa antes de seleccionar el nuevo insumo
                                    $('#nombre').selectpicker('val', '');

                                    // Seleccionar el insumo encontrado
                                    $('#nombre').selectpicker('val', matchedOption.val());

                                    // Simular el evento de cambio en el select
                                    $('#nombre').trigger('change');

                                    // Activar automáticamente el select de variantes después de 1.0 segundos
                                    setTimeout(function() {
                                        $('#variante').selectpicker('toggle');
                                        // Reactivar el evento keydown después de 1 segundo
                                        isBarcodeProcessing = false;
                                    }, 1000);
                                } else {
                                    showModal('Código de barras no encontrado');
                                    // Reactivar el evento keydown
                                    isBarcodeProcessing = false;
                                }

                                barcode = ''; // Limpiar el código de barras después de la búsqueda
                            } else {
                                // Agregar la tecla presionada al código de barras
                                barcode += e.key;
                            }
                        }
                    }
                });

                // Resto del código...
            });

            $('#nombre').change(function() {
                let insumoId = $('#nombre').val();
                let stockInput = $('#stock_actual'); // Input para mostrar el stock

                $('#variante').selectpicker('destroy');
                $('#variante').empty();

                // Realizar la llamada AJAX para obtener las características del insumo
                $.ajax({
                    url: "{{ url('/get-caracteristicas') }}",
                    type: "GET",
                    data: {
                        insumo_id: insumoId
                    },
                    success: function(response) {
                        // Si el insumo tiene variantes disponibles
                        if (response.caracteristicas && response.caracteristicas.length > 0) {
                            // Ocultar el input de stock general
                            stockInput.prop('readonly', true);

                            // Limpiar el valor del input de stock general
                            stockInput.val('');

                            // Filtrar las características para eliminar aquellas con cantidad 0
                            let caracteristicasDisponibles = response.caracteristicas.filter(
                                function(caracteristica) {
                                    return caracteristica.cantidad > 0;
                                });

                            // Ordenar las variantes por fecha de vencimiento (más cercana a la actual primero)
                            caracteristicasDisponibles.sort((a, b) => {
                                const fechaA = new Date(a.vencimiento);
                                const fechaB = new Date(b.vencimiento);
                                return fechaA - fechaB;
                            });

                            // Mostrar la cantidad de la variante seleccionada en el input de stock
                            $('#variante').change(function() {
                                let varianteId = $(this).val();
                                let caracteristica = caracteristicasDisponibles.find(
                                    function(caracteristica) {
                                        return caracteristica.id == varianteId;
                                    });
                                if (caracteristica) {
                                    stockInput.val(caracteristica.cantidad);
                                    // Actualizar el precio unitario
                                    $('#valor_unitario').val(caracteristica.valor_unitario);
                                }
                            });

                            caracteristicasDisponibles.forEach(function(caracteristica) {
                                $('#variante').append('<option value="' + caracteristica.id +
                                    '" data-marca-id="' + (caracteristica.marca ? caracteristica
                                        .marca.id : '') +
                                    '" data-presentacion-id="' + (caracteristica.presentacion ?
                                        caracteristica.presentacion.id : '') +
                                    '" data-precio-unitario="' + caracteristica
                                    .valor_unitario + '">' +
                                    caracteristica.invima + ' - ' + caracteristica.lote +
                                    ' - ' + caracteristica.vencimiento +
                                    ' - ' + (caracteristica.marca ? caracteristica.marca
                                        .nombre : 'Sin Marca') +
                                    ' - ' + (caracteristica.presentacion ? caracteristica
                                        .presentacion.nombre : 'Sin Presentación') +
                                    '</option>');
                            });

                            $('#variante').selectpicker();
                        } else { // Si el insumo no tiene variantes
                            // Mostrar el stock general del insumo en el input de stock
                            stockInput.prop('readonly', false);


                            // Actualizar el valor del input de stock con el stock general del insumo
                            $.ajax({
                                url: "{{ url('/get-stock') }}",
                                type: "GET",
                                data: {
                                    insumo_id: insumoId
                                },
                                success: function(response) {
                                    let stock = response.stock;
                                    // Actualizar el valor del input de "En Stock"
                                    stockInput.val(stock);
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                    // Manejar el error según tu lógica
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Manejar el error según tu lógica
                    }
                }).done(function() {
                    $('#variante').selectpicker('val', '');
                });
            });

            // Función para agregar un insumo a la lista de detalles
            $('#btn_agregar').click(function() {
                agregarInsumo();
            });


            let cont = 1; // Contador para identificar las filas
            let total = 0; // Total de cantidades de insumos

            function agregarInsumo() {
                let idInsumo = $('#nombre').val();
                let nombreInsumo = $('#nombre option:selected').text();
                let cantidad = parseInt($('#stock').val());
                let variante = $('#variante').val();
                let stockActual = parseInt($('#stock_actual').val());
                let precioUnitario = parseFloat($('#valor_unitario').val()) || 0; // Valor unitario

                // Capturar las características de la variante seleccionada
                let varianteText = $('#variante option:selected').text();
                let [invima, lote, vencimiento, marcaNombre, presentacionNombre] = varianteText.split(' - ');

                // Extraer los IDs de marca y presentación desde la variante seleccionada
                let selectedOption = $('#variante option:selected');
                let marcaId = selectedOption.data('marca-id');
                let presentacionId = selectedOption.data('presentacion-id');

                if (idInsumo !== '' && nombreInsumo !== '' && !isNaN(cantidad)) {
                    if (cantidad > 0 && (cantidad % 1 === 0)) {
                        if (cantidad <= stockActual) {
                            let encontrado = false;
                            $('#tabla_detalle tbody tr').each(function() {
                                let idInsumoTabla = $(this).find('input[name="arrayidinsumo[]"]').val();
                                let varianteTabla = $(this).find('input[name="arrayvariante[]"]').val();
                                if (idInsumoTabla === idInsumo && varianteTabla === variante) {
                                    encontrado = true;
                                    let cantidadExistente = parseInt($(this).find('input[name="arraycantidad[]"]')
                                        .val());
                                    let nuevaCantidad = cantidadExistente + cantidad;
                                    if (nuevaCantidad <= stockActual) {
                                        $(this).find('input[name="arraycantidad[]"]').val(nuevaCantidad);
                                        total += cantidad;
                                        $('#total').html(total);
                                        limpiarCampos();
                                    } else {
                                        showModal('Cantidad Insuficiente');
                                    }
                                    return false;
                                }
                            });

                            if (!encontrado) {
                                agregarNuevaFila(idInsumo, nombreInsumo, variante, invima, lote, vencimiento, cantidad,
                                    stockActual, marcaId, marcaNombre, presentacionId, presentacionNombre, precioUnitario);
                                limpiarCampos();
                            }
                        } else {
                            showModal('Cantidad No Disponible');
                        }
                    } else {
                        showModal('Valores Incorrectos');
                    }
                } else {
                    showModal('Campos Obligatorios');
                }
            }

            function agregarNuevaFila(idInsumo, nombreInsumo, variante, invima, lote, vencimiento, cantidad, stockActual,
                marcaId, marcaNombre, presentacionId, presentacionNombre, precioUnitario) {
                let fila = '<tr id="fila' + cont + '" style="font-size: 14px; text-align:center">' +
                    '<td><input type="hidden" name="arrayidinsumo[]" value="' + idInsumo + '">' +
                    nombreInsumo + '</td>' +
                    '<td><input type="hidden" name="arrayvariante[]" value="' + variante + '">' +
                    '<input type="hidden" name="arrayinvima[]" value="' + invima + '">' + invima + '</td>' +
                    '<td><input type="hidden" name="arraylote[]" value="' + lote + '">' + lote + '</td>' +
                    '<td><input type="hidden" name="arrayvencimiento[]" value="' + vencimiento + '">' + vencimiento + '</td>' +
                    '<td><input type="hidden" name="arraymarca[]" value="' + marcaId + '">' + marcaNombre + '</td>' +
                    '<td><input type="hidden" name="arraypresentacion[]" value="' + presentacionId + '">' + presentacionNombre +
                    '</td>' +
                    // Oculta el valor unitario en la tabla, pero lo envía en un campo oculto
                    '<input type="hidden" name="arrayvalor[]" value="' + precioUnitario + '">' +
                    '<td id="centrar">' +
                    '<div class="input-group">' +
                    '<button class="btn btn-outline-danger btn-xs p-1" type="button" onClick="restarCantidad(' + cont +
                    ')"><i class="fa fa-minus"></i></button>' +
                    '<input type="number" name="arraycantidad[]" id="cantidad' + cont + '" value="' + cantidad +
                    '" class="form-control" readonly>' +
                    '<button class="btn btn-outline-success btn-xs p-1" type="button" onClick="sumarCantidad(' + cont + ', ' +
                    stockActual + ')"><i class="fa fa-plus"></i></button>' +
                    '</div>' +
                    '</td>' +
                    '<td><button class="btn btn-danger" type="button" onClick="eliminarInsumo(' + cont +
                    ')"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';

                $('#tabla_detalle tbody').append(fila);
                cont++;
                total += cantidad;
                $('#total').html(total);
            }



            function sumarCantidad(indice, stockActual) {
                let cantidadInput = $('#cantidad' + indice);
                let cantidad = parseInt(cantidadInput.val());
                if (cantidad < stockActual) {
                    cantidadInput.val(cantidad + 1);
                    // Actualizar el total y la cantidad
                    total++;
                    $('#total').html(total);
                } else {
                    showModal('Cantidad Insuficiente');
                }
                console.log('Cantidad sumada:', cantidadInput.val());
            }

            function restarCantidad(indice) {
                let cantidadInput = $('#cantidad' + indice);
                let cantidad = parseInt(cantidadInput.val());
                if (cantidad > 1) {
                    cantidadInput.val(cantidad - 1);
                    // Actualizar el total y la cantidad
                    total--;
                    $('#total').html(total);
                } else {
                    // Si la cantidad es 1, eliminar el insumo de la tabla
                    eliminarInsumo(indice);
                }
                console.log('Cantidad restada:', cantidadInput.val());
            }

            function eliminarInsumo(indice) {
                let cantidadEliminada = parseInt($('#fila' + indice).find('input[name="arraycantidad[]"]').val());
                total -= cantidadEliminada;
                $('#fila' + indice).remove();
                $('#total').html(total);
                console.log('Insumo eliminado:', indice);
            }

            function limpiarCampos() {
                $('#nombre').selectpicker('val', ''); // Limpiar el select de insumos
                $('#variante').selectpicker('val', ''); // Limpiar el select de variantes
                $('#stock_actual').val(''); // Limpiar el input de stock actual
                $('#stock').val(''); // Limpiar el input de cantidad
                $('#valor_unitario').val(''); // Limpiar también el precio unitario
                // Refrescar select pickers después de limpiar los valores
                $('#nombre').selectpicker('val', '');
                $('#variante').selectpicker('val', '');
            }

            function showModal(message, icon = 'error') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                Toast.fire({
                    icon: icon,
                    title: message
                });
            }

            function confirmAndSubmit() {
                const servicio = document.querySelector('#servicio_id').value;
                const comprobante = document.querySelector('#comprobante_id').value;
                const numeroComprobante = document.querySelector('#numero_comprobante').value;
                const tableBody = document.querySelector('#tabla_detalle tbody');
                const rows = tableBody.querySelectorAll('tr');

                if (!servicio || !comprobante || !numeroComprobante || rows.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se puede realizar la entrega'
                    });
                } else {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Deseas Realizar La Entrega.',
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
                                timer: 5000
                            });
                            setTimeout(() => {
                                document.querySelector('#entrega-form').submit();
                            }, 500);
                        }
                    });
                }
            }
        </script>
    @stop
