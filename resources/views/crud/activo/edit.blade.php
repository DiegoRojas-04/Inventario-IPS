@extends('adminlte::page')

@section('title', 'Editar Activo')

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
        <button type="submit" class="btn btn-primary">Ver Activos</button>
    </a>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Activo {{ $activo->nombre }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ url('/activo/' . $activo->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT') <!-- Se usa PUT para la actualización -->

                
                <div class="col-md-4">
                    <label for="categoria_id">Categoría</label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria_id', $activo->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="ubicacion_general">Ubicación:</label>
                    <select name="ubicacion_general" class="form-control @error('ubicacion_general') is-invalid @enderror">
                        <option value="">Seleccionar</option>
                        <option value="1" {{ old('ubicacion_general', $activo->ubicacion_general) == '1' ? 'selected' : '' }}>La Dorada</option>
                        <option value="2" {{ old('ubicacion_general', $activo->ubicacion_general) == '2' ? 'selected' : '' }}>Manizales</option>
                    </select>
                    @error('ubicacion_general')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                


                <div class="col-md-4">
                    <label for="modelo">Modelo:</label>
                    <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror"
                        value="{{ old('modelo', $activo->modelo) }}">
                    @error('modelo')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="serie">Número de Serie:</label>
                    <input type="text" name="serie" class="form-control @error('serie') is-invalid @enderror"
                        value="{{ old('serie', $activo->serie) }}">
                    @error('serie')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="marca">Marca:</label>
                    <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror"
                        value="{{ old('marca', $activo->marca) }}">
                    @error('marca')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror"
                        value="{{ old('cantidad', $activo->cantidad) }}" readonly>
                    @error('cantidad')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="medida">Medida:</label>
                    <input type="text" name="medida" class="form-control @error('medida') is-invalid @enderror"
                        value="{{ old('medida', $activo->medida) }}">
                    @error('medida')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="estado">Estado:</label>
                    <select name="estado" class="form-control @error('estado') is-invalid @enderror">
                        <option value="0" {{ old('estado', $activo->estado) == 0 ? 'selected' : '' }}>En Uso</option>
                        <option value="1" {{ old('estado', $activo->estado) == 1 ? 'selected' : '' }}>Disponible
                        </option>
                        <option value="2" {{ old('estado', $activo->estado) == 2 ? 'selected' : '' }}>Reparación
                        </option>
                    </select>
                    @error('estado')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="ubicacion_id">Ubicación Especifica:</label>
                    <select name="ubicacion_id" id="ubicacion_id"
                        class="form-control @error('ubicacion_id') is-invalid @enderror">
                        @foreach ($ubicaciones as $ubicacion)
                            <option value="{{ $ubicacion->id }}"
                                {{ old('ubicacion_id', $activo->ubicacion_id) == $ubicacion->id ? 'selected' : '' }}>
                                {{ $ubicacion->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('ubicacion_id')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="codigo">Código:</label>
                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                        value="{{ old('codigo', $activo->codigo) }}" readonly>
                    @error('codigo')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="observacion">Observación:</label>
                    <input type="text" name="observacion" class="form-control @error('observacion') is-invalid @enderror"
                        value="{{ old('observacion', $activo->observacion) }}">
                    @error('observacion')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>


                <div class="col-12">
                    <br>
                    <button type="submit" class="btn btn-primary">Actualizar Activo</button>
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
