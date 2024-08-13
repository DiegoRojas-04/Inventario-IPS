function confirmDelete(insumoId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción se puede revertir.',
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "##3085d6",
        confirmButtonText: "Confirmar"
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Accion Exitosa",
                icon: "success"
            });
            document.getElementById('delete-form-' + insumoId).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const selectCategoria = document.getElementById('id_categoria');

    // Escuchar cambios en el select de categoría
    selectCategoria.addEventListener('change', function() {
        document.getElementById('filterForm')
            .submit(); // Enviar el formulario al cambiar la categoría
    });

    const selectPageSize = document.getElementById('pageSize');

    // Obtener el tamaño de página de la URL actual
    const urlParams = new URLSearchParams(window.location.search);
    const pageSizeFromUrl = urlParams.get('page_size');

    // Establecer el valor del tamaño de página en el select
    if (pageSizeFromUrl) {
        selectPageSize.value = pageSizeFromUrl;
    }

    // Escuchar cambios en el select de tamaño de página
    selectPageSize.addEventListener('change', function() {
        const pageSize = this.value;
        document.getElementById('pageSizeHidden').value = pageSize; // Actualizar el campo oculto
        document.getElementById('filterForm').submit(); // Enviar el formulario
    });
});

//buscador automatic
$(document).ready(function() {

    $("#search").keyup(function() {

        _this = this;

        $.each($("#datos tbody tr"), function() {

            if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)

                $(this).hide();

            else

                $(this).show();
        });
    });
});