@extends('adminlte::page')

@section('title', 'Insumo')

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
                title: "Insumo Agregado"
            });
        </script>
    @endif
    <a href="{{ url('/insumo') }}" class="text-decoration-none text-white">
        <button type="submit" class="btn btn-primary ">Ver Insumos</button></a>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Agregar Insumos</h1>
        </div>
        <div class="card-body">
            <form action="{{ url('/insumo') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label id="label-nombre">Nombre:</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre') }}">
                    @error('nombre')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label id="label-descripcion">Descripcion:</label>
                    <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                        value="{{ old('descripcion') }}">
                    @error('descripcion')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="col-md-2 text-center">
                    <label>Invima</label>
                    <input type="checkbox" name="requiere_invima" class="form-control" value="1"
                        {{ old('requiere_invima') ? 'checked' : '' }}>
                </div>

                <div class="col-md-2 text-center">
                    <label>Lote Y Fecha</label>
                    <input type="checkbox" name="requiere_lote" class="form-control" value="1"
                        {{ old('requiere_lote') ? 'checked' : '' }}>
                </div>


                <div class="col-md-4">
                    <label>Categoria:</label>
                    <select data-size="10" title="Seleccionar Categoria..." data-live-search="true" name="id_categoria"
                        id="id_categoria"
                        class="form-control selectpicker show-tick  @error('id_categoria') is-invalid @enderror"
                        value="{{ old('id_categoria') }}">
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    @error('id_categoria')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label id="label-clasificacion">Riesgo:</label>
                    <input type="text" name="riesgo" class="form-control  @error('riesgo') is-invalid @enderror"
                        value="{{ old('riesgo') }}">
                    @error('riesgo')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label id="label-vida">Vida Util:</label>
                    <input type="text" name="vida_util" class="form-control   @error('vida_util') is-invalid @enderror"
                        value="{{ old('vida_util') }}">
                    @error('vida_util')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label>Cantidad:</label>
                    <input type="text" name="stock" class="form-control  @error('stock') is-invalid @enderror"
                        value="{{ old('stock') }}">
                    @error('stock')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-12">
                    <br>
                    <button type="submit" class="btn bg-blue">{{ 'Agregar' }}</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script>
        document.getElementById('id_categoria').addEventListener('change', function() {
            var categoriaId = this.value; // Obtiene el id de la categoría seleccionada

            if (categoriaId == 11) { // Si la categoría es Medicamentos (id 11)
                document.getElementById('label-nombre').textContent = 'Principio Activo:';
                document.getElementById('label-descripcion').textContent = 'Presentación Comercial:';
                document.getElementById('label-clasificacion').textContent = 'CONCENTRACIÓN:';
                document.getElementById('label-vida').textContent = 'CÓDIGO CUMS:';
            } else {
                // Restaura los títulos originales si no es la categoría Medicamentos
                document.getElementById('label-nombre').textContent = 'Nombre:';
                document.getElementById('label-descripcion').textContent = 'Descripción:';
                document.getElementById('label-clasificacion').textContent = 'Riesgo:';
                document.getElementById('label-vida').textContent = 'Vida útil:';
            }
        });
    </script>
@stop
