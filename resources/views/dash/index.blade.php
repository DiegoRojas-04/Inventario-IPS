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

        <div class="col-lg-4 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ $usuarioCount }}</h3>
                        <i class="far fa fa-users size-5"></i>
                    </div>
                    <h5>Usuarios</h5>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="usuario" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ $preveedoresCount }}</h3>
                        <i class="far fa-user-circle size-5"></i>
                    </div>
                    <h5>Proveedores</h5>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="proveedor" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ $serviciosCount }}</h3>
                        <i class="far fa fa-medkit size-5"></i>
                    </div>
                    <h5>Areas</h5>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="servicio" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box  bg-white">
                <div class="inner">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ $categoriaCount }}</h3>
                        <i class="far fa fa-list size-5"></i>
                    </div>
                    <h5>Categorias</h5>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="categoria" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ $marcaCount }}</h3>
                        <i class="far fa fa-tags size-5"></i>
                    </div>
                    <h5>Marcas</h5>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="marca" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ $presentacionCount }}</h3>
                        <i class="far fa fa-cubes size-5"></i>
                    </div>
                    <h5>Presentacion</h5>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="presentacion" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-white">
                <div class="inner">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>{{ $insumoCount }}</h3>
                        <i class="far fa fa-stethoscope size-5"></i>
                    </div>
                    <h5>Insumos</h5>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="insumo" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>


        <div class="text-center col-lg-8 col-6">
            <img src="{{ asset('images/muñeco.jpg') }}" alt="Logo">
        </div>
    </div>

    {{-- 
<h4>Pendientes</h4>
<p>
<p>* Ver lo de si un mismo insumo viene en diferente marca
o presentacion y acomodarlo</p>
<p>* Que no salga el home y realizar pedido al admin</p>
<p>* Lo del comprobante sus numeros y fechas</p> 
--}}


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
