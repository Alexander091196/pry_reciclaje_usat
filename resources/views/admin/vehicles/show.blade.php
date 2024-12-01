@extends('adminlte::page')

@section('title', 'Asignacion de personas')


@section('content')
    <div class="p-3"></div>
    <div class="card">
        <div class="card-header">
            <button class="btnEditar btn-success float-right" id={{ $vehicle->id }}><i class="fas fa-plus-circle"></i>
                Agregar ocupante</button>

            <div>
                <strong>Nombre del vehículo:</strong> {{ $vehicle->name }} |
                <strong>Placa:</strong> {{ $vehicle->plate }}
                <div>
                    <label for="">Máximo de ocupantes:</label>
                    {{ $vehicle->occupant_capacity }} <br>
                </div>
            </div>


        </div>
        <div class="card-body">
            <div class="row">

            </div>
            <div class="col-12 card" style="min-height: 50px">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOMBRE</th>
                                <th>TIPO</th>
                                <th>FECHA ASIGNADA</th>
                                <th>DAR DE BAJA</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($occupants as $occ)
                                <tr>
                                    <td>{{ $occ->id }}</td>
                                    <td>{{ $occ->usernames }}</td>
                                    <td>{{ $occ->usertype }}</td>
                                    <td>{{ $occ->date }}</td>
                                    <td>
                                        <form action="{{ route('admin.occupant.update', $occ->id) }}" method="POST"
                                            class="fmrEliminar">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>

            </div>
            <div class="row">
                <div class="card" style="min-height: 50px">
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer"></div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Asignar ocupante</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="/css/admin_custom.css">
    {{-- Add here extra stylesheets --}}
    {{--  --}}
@stop

@section('js')
    <script>
        $('#datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            }
        });

        $(".btnEditar").click(function() {
            var id = $(this).attr('id');
            $.ajax({

                url: "{{ route('admin.occupant.edit', '_id') }}".replace('_id', id),
                type: "GET",
                success: function(response) {
                    $('#formModal .modal-body').html(response);
                    $('#formModal').modal('show');
                }
            });

        });

        $(".fmrEliminar").submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Seguro de dar de baja?",
                text: "Esta accion es irreversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Dar de baja!"
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                    /*Swal.fire({
                    title: "Marca eliminada!",
                    text: "Porceso exitoso.",
                    icon: "Satisfactorio"
                    });*/
                }
            });
        });
    </script>

    @if (session('success') !== null)
        <script>
            Swal.fire({
                title: "Proceso Exitoso",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>
    @endif

    @if (session('error') !== null)
        <script>
            Swal.fire({
                title: "Error de proceso",
                text: "{{ session('error') }}",
                icon: "error"
            });
        </script>
    @endif
    // En tu vista de Blade donde tienes el script de SweetAlert


    @if (session('warning') !== null)
        <script>
            Swal.fire({
                title: "Asignación Activa",
                text: "{{ session('warning') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, Actualizar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí puedes realizar la acción de enviar el formulario
                    $.ajax({
                        url: "{{ route('admin.occupants.confirm-assignment') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            user_id: '{{ session('user_id') }}',
                            vehicle_id: '{{ session('vehicle_id') }}',
                            assignment_date: '{{ session('assignment_date') }}',
                            usertype_id: '{{ session('usertype_id') }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Asignado!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al asignar el usuario al vehículo.',
                                'error'
                            );
                        }
                    });
                }
            });
        </script>
    @endif




@stop


<div class="row">
    @foreach ($images as $image)
        <div class="col-3">
            <div class="card">
                <form action="{{ route('admin.vehicleimages.destroy', $image->id) }}" method="POST"
                    class="imgEliminar">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm close-button"><i class="fas fa-minus-circle"></i></button>
                    <img src={{ asset($image->image) }} alt="" style="width: 100%;height:100%">
                </form>
                <button class="btn btn-sm btn-success btnimageprofile" id='{{ $image->id }}'
                    data-id="{{ $image->vehicle_id }}"><i class="fas fa-image"></i> Perfil</button>
            </div>

        </div>
    @endforeach
</div>
<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-arrow-alt-circle-left"></i>
    Cerrar</button>

<style>
    /* Clase para asegurar la posición en la esquina superior derecha */
    .close-button {
        position: absolute;
        top: 5px;
        right: 5px;
        color: red;
        cursor: pointer;
    }

    .close-button:hover {
        color: orange;
    }
</style>
