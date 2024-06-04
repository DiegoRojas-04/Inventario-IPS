@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Usuarios y Roles</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="form-group">
                <input type="text" name="name" id="name" value="{{ $user->name }}" class="form-control" readonly>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ url('usuario/' . $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label>Lista de Roles</label>
                    <div class="row">
                        <div class="col-md-12">
                            @foreach ($roles as $role)
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        class="form-check-input" id="perm_{{ $role->id }}"
                                        {{ $user->hasAnyRole($role->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
