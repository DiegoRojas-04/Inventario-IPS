@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Roles y Permisos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="form-group">
                <input type="text" name="name" id="name" value="{{ $role->name }}" class="form-control" readonly>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ url('rol/' . $role->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label>Lista de Permisos</label>
                    <div class="row">
                        @foreach ($permisos->chunk(ceil($permisos->count() / 4)) as $chunk)
                            <div class="col-md-3">
                                @foreach ($chunk as $permiso)
                                    @php
                                        $isAdminPermission = $permiso->name === 'Administrador'; // Verificar si el permiso es Administrador
                                    @endphp

                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="permissions[]" value="{{ $permiso->id }}"
                                            class="form-check-input" id="perm_{{ $permiso->id }}"
                                            {{ $role->hasPermissionTo($permiso->name) ? 'checked' : '' }}
                                            {{ $isAdminPermission && $role->name === 'Administrador' ? 'disabled' : '' }}>
                                        <!-- Desactivar checkbox solo para el permiso Administrador -->

                                        <label class="form-check-label" for="perm_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>

                                    @if ($isAdminPermission && $role->name === 'Administrador')
                                        <!-- Campo oculto para asegurar que se envíe el permiso de Administrador -->
                                        <input type="hidden" name="permissions[]" value="{{ $permiso->id }}">
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>

        </div>
    </div>
@stop

@section('css')
    {{-- Agregar aquí hojas de estilo adicionales --}}
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
