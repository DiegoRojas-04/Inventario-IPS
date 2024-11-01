@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
    <br>
    <div class="row">

        <style>
            .small-box {
                margin: 15px;
            }
        </style>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $usuarioCount }}</h3>
                    <p>Usuarios</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-users"></i>
                </div>
                <a href="usuario" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $preveedoresCount }}</h3>
                    <p>Proveedores</p>
                </div>
                <div class="icon">
                    <i class="far fa-user-circle"></i>
                </div>
                <a href="proveedor" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $serviciosCount }}</h3>
                    <p>Áreas</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-medkit"></i>
                </div>
                <a href="servicio" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $categoriaCount }}</h3>
                    <p>Categorías</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-list"></i>
                </div>
                <a href="categoria" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ $marcaCount }}</h3>
                    <p>Marcas</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-tags"></i>
                </div>
                <a href="marca" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <h3>{{ $presentacionCount }}</h3>
                    <p>Presentaciones</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-cubes"></i>
                </div>
                <a href="presentacion" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $insumoCount }}</h3>
                    <p>Insumos</p>
                </div>
                <div class="icon">
                    <i class="far fa fa-stethoscope"></i>
                </div>
                <a href="insumo" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>0</h3>

                    <p>New</p>
                </div>
                <div class="icon">
                    <i class="fa fa-signal"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
@stop

@section('css')
    <style></style>
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@stop
