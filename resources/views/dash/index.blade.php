@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
    {{-- <div class="row">
        <!-- Tarjeta de ejemplo 1 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>150</h3>
                    <p>Nuevas Ordenes</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Tarjeta de ejemplo 2 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>53<sup style="font-size: 20px">%</sup></h3>
                    <p>Tasa de conversión</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Tarjeta de ejemplo 3 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>44</h3>
                    <p>Usuarios registrados</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Tarjeta de ejemplo 4 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>65</h3>
                    <p>Visitantes únicos</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div> --}}

<h4>Pendientes</h4>
<p>* Que los insumos de la entrega no se dupliquen al momento de scanear un codigo de barras</p>
<p>* Poner para agreagr el codigo de barras (al momento de crear el insumo)</p>
<p>* Ver lo de si un mismo insumo viene en diferente marca o presentacion y acomodarlo</p>
<p>* Que no salga el home y realizar pedido al admin</p>
<p>* Al realizar La entrega, tener en cuenta el stock si se escoge una misma variante en la misma entrega</p>
<p>* Lo del comprobante sus numeros y fechas</p>
<p>* Editar Perfil de usuarios</p>



















    {{-- <div class="text-center" style="margin-top: 250px">
        <img src="{{ asset('images/muñeco.jpg') }}" alt="Logo">
    </div>   --}}
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
