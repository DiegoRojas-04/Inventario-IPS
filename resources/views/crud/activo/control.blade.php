@extends('adminlte::page')

@section('title', 'ControlActivos')

@section('content')
    <div class="card">
        <div class="row">
            @foreach ($categorias as $categoria)
                <div class="col-lg-2 col-1">
                    <a href="{{ route('control.filtrar', $categoria->id) }}" class="text-decoration-none">
                        <div class="small-box bg-primary categoria-card">
                            <div class="inner">
                                <h3>{{ $categoria->activos_count }}</h3>
                                <p>{{ $categoria->nombre }}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
            
        <div class="card-body">

            <!-- Tabla de activos si hay una categoría seleccionada -->
            @if (isset($categoriaSeleccionada))
                <h2 class="mt-4 text-center">{{ $categoriaSeleccionada->nombre }}</h2>

                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>Ubicación</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($activos as $activo)
                            <tr>
                                <td>{{ $activo->ubicacion_nombre }}</td>
                                <td>{{ $activo->total_cantidad }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No hay activos en esta categoría.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop
