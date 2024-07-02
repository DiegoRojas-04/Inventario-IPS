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
                        value="{{ $user->name }}" required>
                    @error('name')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ $user->email }}" required>
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Confirmar Contraseña:</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </form>
        </div>
    </div>
@stop
