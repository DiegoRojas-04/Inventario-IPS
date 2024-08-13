@extends('adminlte::page')

@section('title', 'Home')

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
                title: "Pedido Realizado"
            });
        </script>
    @endif
    <div class="card-header">
        <div class="absolute inset-x-0 top-0 flex justify-center -mt-20 text-center" style="padding-bottom: 15px;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                class="rounded-full border-4 border-white shadow-lg object-cover"
                style="border-radius: 50%; height: 160px; width: 160px;">
        </div>

        <div class="text-center">
            <h5><i class="fa fa-user" style="padding-right: 10px;"></i> Bienvenido!! {{ Auth::user()->name }}</h5>
        </div>
    </div>

    <!-- Botón de carrito de compras -->
    <div class="text-center" style="margin-top: 20px;">
        <a href="{{ route('pedido.create') }}" id="pedido-btn" class="btn btn-primary">
            <i class="fa fa-shopping-cart"></i> Realizar Pedido
        </a>
    </div>
    <!-- Contador regresivo -->
    <div class="text-center" style="margin-top: 20px;">
        <div id="countdown" class="text-danger"></div>
    </div>
@stop

@section('content')

    <div class="text-center">
        <img src="{{ asset('images/muñeco.jpg') }}" alt="Logo">
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Asegúrate de incluir Font Awesome si aún no está incluido -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/home.js') }}"></script>
@stop
