@extends('adminlte::page')

@section('title', 'Crear Activo')

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
                title: "{{ session('Mensaje') }}"
            });
        </script>
        
    @endif
    <a href="{{ url('/activo') }}" class="text-decoration-none text-white">
        <button type="submit" class="btn btn-primary ">Ver Activos</button></a>
 @stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"></h3>
        </div>
        <div class="card-body">
            <form action="{{ url('/activo') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre') }}">
                    @error('nombre')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="categoria">Categoria:</label>
                    <select name="categoria" class="form-control @error('categoria') is-invalid @enderror">
                        <option value="">Seleccionar categoria...</option>
                        <option value="PC" {{ old('categoria') == 'PC' ? 'selected' : '' }}>PC</option>
                        <option value="Impresora" {{ old('categoria') == 'Impresora' ? 'selected' : '' }}>Impresoras</option>
                        <option value="Monitores" {{ old('categoria') == 'Monitores' ? 'selected' : '' }}>Monitores</option>
                        <option value="Tablets" {{ old('categoria') == 'Tablets' ? 'selected' : '' }}>Tablets</option>
                        <option value="Televisores" {{ old('categoria') == 'Televisores' ? 'selected' : '' }}>Televisores</option>
                        <option value="Telefonos" {{ old('categoria') == 'Telefonos' ? 'selected' : '' }}>Telefonos</option>
                        <option value="Mause" {{ old('categoria') == 'Mause' ? 'selected' : '' }}>Mause</option>
                        <option value="Teclados" {{ old('categoria') == 'Teclados' ? 'selected' : '' }}>Teclados</option>
                        <option value="Mesas" {{ old('categoria') == 'Mesas' ? 'selected' : '' }}>Mesas</option>
                        <option value="Sillas" {{ old('categoria') == 'Sillas' ? 'selected' : '' }}>Sillas</option>
                    </select>
                    @error('categoria')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="modelo">Modelo:</label>
                    <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror"
                        value="{{ old('modelo') }}">
                    @error('modelo')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="serie">Número de Serie:</label>
                    <input type="text" name="serie" class="form-control @error('serie') is-invalid @enderror"
                        value="{{ old('serie') }}">
                    @error('serie')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="marca">Marca:</label>
                    <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror"
                        value="{{ old('marca') }}">
                    @error('marca')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror"
                        value="{{ old('cantidad') }}">
                    @error('cantidad')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="medida">Medida:</label>
                    <input type="text" name="medida" class="form-control @error('medida') is-invalid @enderror"
                        value="{{ old('medida') }}">
                    @error('medida')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="estado">Estado:</label>
                    <select name="estado" class="form-control @error('estado') is-invalid @enderror">
                        <option value="">Seleccionar estado...</option>
                        <option value="0" {{old('estado')  == 0 ? 'selected' : '' }}>En Uso</option>
                        <option value="1" {{old('estado')  == 1 ? 'selected' : '' }}>Disponible</option>
                        <option value="2" {{old('estado')  == 2 ? 'selected' : '' }}>Reparacion</option>
                    </select>
                    @error('estado')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label for="observacion">Observación:</label>
                    <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" rows="2">{{ old('observacion') }}</textarea>
                    @error('observacion')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-12">
                    <br>
                    <button type="submit" class="btn btn-primary">Agregar Activo</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
