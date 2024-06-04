@extends('adminlte::page')

@section('title', 'Servicio')

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
                icon: "error",
                title: "Proveedor Eliminado"
            });
        </script>
    @endif

    @if (session('Mensaje2'))
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
                title: "Proveedor Actualizado"
            });
        </script>
    @endif

    @if (session('Mensaje3'))
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
        title: "Proveedor Restaurado"
    });
</script>
@endif
    <a href="{{ url('/proveedor/create') }}" class="text-decoration-none text-white">
        <button type="submit" class="btn btn-primary ">Agregar Proveedor</button></a>
    <br>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row g-3">

                <div class="col-md-1">
                    <select class="form-control " id="pageSize">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>


                <div class="col-md-6">

                </div>




                <div class="col-md-5 input-group">
                    <input type="text" class="form-control" placeholder="Buscar" id="search">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-body">

            <table class="table">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Descripcion</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($proveedores as $proveedor)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $proveedor->nombre }}</td>
                            <td>{{ $proveedor->descripcion }}</td>
                            <td>
                                @if ($proveedor->estado == 1)
                                    <span class="fw-bolder rounded bg-success text-white p-1">Activo</span>
                                @else
                                    <span class="fw-bolder rounded bg-danger text-white p-1">Eliminado</span>
                                @endif
                            </td>
            
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ url('/proveedor/' . $proveedor->id . '/edit') }}"
                                        class="text-decoration-none text-white">
                                        <button type="submit" class="btn btn-warning "><i class="fa fa-file"
                                                aria-hidden="true"></i></button></a>
                                </div>
                                <div class="btn-group" role="group">
                                    @if ($proveedor->estado == 1)
                                        <button type="submit" class="btn btn-danger" data-toggle="modal"
                                            data-target="#eliminar-{{ $proveedor->id }}"><i class="fa fa-trash"
                                                aria-hidden="true"></i></button>
                                    @else
                                        <button type="submit" class="btn btn-success" data-toggle="modal"
                                            data-target="#eliminar-{{ $proveedor->id }}"><i class="fa fa-share"
                                                aria-hidden="true"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr> 
                        <div class="modal fade" id="eliminar-{{ $proveedor->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ $proveedor->estado == 1 ? 'Eliminar proveedor' : 'Restaurar proveedor' }} <br>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{ $proveedor->estado == 1 ? ' ¿Estas seguro que quieres Eliminar esta proveedor?' : '¿Estas seguro que quieres Restaurar esta proveedor?' }}
                                        <br>
                                        <h5>{{ $proveedor->nombre }}</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cerrar</button>
                                        <form action="{{ url('/proveedor/' . $proveedor->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-primary">Confirmar</i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
            {{ $proveedores->links() }}

        </div>
    </div>
@stop

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
