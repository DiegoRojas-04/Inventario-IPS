// Función para mostrar el contador regresivo y gestionar la disponibilidad del botón
function updateCountdown() {
    var now = new Date();
    var currentDay = now.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
    var allowOrder = (currentDay === 3 && now.getHours() >= 6 && now.getHours() <
        18); // Permitir pedidos solo los jueves entre las 6 AM y las 4 PM

    if (allowOrder) {
        var deadline = new Date();
        deadline.setHours(18, 0, 0, 0); // Establecer la fecha límite a las 4 PM
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
document.getElementById("pedido-btn").onclick = function () {
    return showSweetAlert();
};

document.getElementById('btnPedidoEspecial').addEventListener('click', function () {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Quieres realizar un pedido especial?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, realizar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#55aa38',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a la ruta de crear pedido especial
            window.location.href = "pedido/create?especial=true";
        }
    });
});