@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ url('/usuario/' . $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ $user->name }}" required placeholder="Nombre del Usuario">
                    @error('name')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ $user->email }}" required placeholder="Correo Electrónico">
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Contraseña">
                    @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Confirmar Contraseña:</label>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Confirmar Contraseña">
                </div>

                <div class="form-group">
                    <label>Area:</label>
                    <select name="servicio_id" class="form-control @error('servicio_id') is-invalid @enderror" required>
                        <option value="">Seleccionar Area</option>
                        @foreach ($servicios as $servicio)
                            <option value="{{ $servicio->id }}" {{ $user->servicio_id == $servicio->id ? 'selected' : '' }}>
                                {{ $servicio->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('servicio_id')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </form>
        </div>
    </div>
@stop
