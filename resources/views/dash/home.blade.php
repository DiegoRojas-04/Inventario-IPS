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
    <script>
        // Función para mostrar el contador regresivo y gestionar la disponibilidad del botón
        function updateCountdown() {
            var now = new Date();
            var currentDay = now.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
            var allowOrder = (currentDay === 3 && now.getHours() >= 6 && now.getHours() <
                16); // Permitir pedidos solo los jueves entre las 6 AM y las 4 PM

            if (allowOrder) {
                var deadline = new Date();
                deadline.setHours(16, 0, 0, 0); // Establecer la fecha límite a las 4 PM
                var diff = deadline - now; 

                if (diff > 0) {
                    var hours = Math.floor(diff / (1000 * 60 * 60));
                    var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    document.getElementById("countdown").innerHTML = "Tiempo restante: " + hours + "h " + minutes + "m " +
                        seconds + "s";
                    document.getElementById("pedido-btn").disabled =
                        false; // Habilitar el botón cuando está dentro del horario permitido
                } else {
                    document.getElementById("countdown").innerHTML = "Tiempo de pedido finalizado";
                    document.getElementById("pedido-btn").disabled =
                        true; // Deshabilitar el botón cuando se haya pasado el horario permitido
                }
            } else {
                document.getElementById("countdown").innerHTML = "No es momento de realizar pedidos";
                document.getElementById("pedido-btn").disabled =
                    true; // Deshabilitar el botón cuando no esté dentro del horario permitido
            }
        }

        // Actualizar el contador cada segundo
        setInterval(updateCountdown, 1000);

        // Función para mostrar una alerta de SweetAlert y verificar su estado antes de realizar una acción
        function showSweetAlert() {
            if (document.getElementById("pedido-btn").disabled) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'El tiempo de Realizar Pedido ha finalizado.',
                });
                return false;
            }
            return true;
        }

        // Agregar el evento onclick al botón y mostrar SweetAlert antes de realizar una acción
        document.getElementById("pedido-btn").onclick = function() {
            return showSweetAlert();
        };
    </script>
@stop
