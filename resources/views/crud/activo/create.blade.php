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
                    <label for="categoria_id">Categoría:</label>
                    <select name="categoria_id" class="form-control @error('categoria_id') is-invalid @enderror">
                        <option value="">Seleccionar categoría...</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <span class="invalid-feedback">{{ $message }}</span>
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
                    <label for="ubicacion_id">Ubicacion:</label>
                    <select name="ubicacion_id" class="form-control @error('ubicacion_id') is-invalid @enderror">
                        @foreach ($ubicaciones as $ubicacion)
                            <option value="{{ $ubicacion->id }}"
                                {{ old('ubicacion_id') == $ubicacion->id ? 'selected' : '' }}>{{ $ubicacion->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('ubicacion_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="estado">Estado:</label>
                    <select name="estado" class="form-control @error('estado') is-invalid @enderror">
                        <option value="0" {{ old('estado') == 0 ? 'selected' : '' }}>En Uso</option>
                        <option value="1" {{ old('estado') == 1 ? 'selected' : '' }}>Disponible</option>
                        <option value="2" {{ old('estado') == 2 ? 'selected' : '' }}>Reparacion</option>
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
