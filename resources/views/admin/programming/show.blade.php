@extends('adminlte::page')

@section('title', 'Programación')


@section('content')
    <div class="p-2"></div>
    <div class="card">
        <div class="card-header">

            {{-- <button class="btn btn-success float-right"id="btnEditarRango" data-id="{{ $programming->id }}"> <i
                    class="fas fa-plus"></i> Editar por Rango</button> --}}

            <h3>Lista de programación</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped" id="datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>FECHA</th>
                        <th>VEHICULO</th>
                        <th>RUTA</th>
                        <th>ESTADO</th>
                        <th>TURNO</th>
                        <th width=5></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para editar por rango -->
    <div class="modal fade" id="rangeEditModal" tabindex="-1" role="dialog" aria-labelledby="rangeEditModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rangeEditModalLabel">Editar programación por rango</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- El formulario se cargará aquí dinámicamente -->
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                "ajax": "{{ route('admin.programming.show', $programming->id) }}", // Ruta que devuelve los datos en formato JSON
                "columns": [{
                        "data": "id",
                    },
                    {
                        "data": "fecha",
                    },
                    {
                        "data": "vehiculo",
                    },
                    {
                        "data": "ruta",
                    },
                    {
                        "data": "estado",
                    },
                    {
                        "data": "turno",
                    },
                    {
                        "data": "actions",
                    },

                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
        });


        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr("id");

            $.ajax({
                url: "{{ route('admin.vehicleroutes.edit', 'id') }}".replace('id', id),
                type: "GET",
                success: function(response) {
                    $("#formModal #exampleModalLabel").html("Modificar programación");
                    $("#formModal .modal-body").html(response);
                    $("#formModal").modal("show");

                    $("#formModal form").on("submit", function(e) {
                        e.preventDefault();

                        var form = $(this);
                        var formData = new FormData(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $("#formModal").modal("hide");
                                refreshTable();
                                Swal.fire('Proceso existoso', response.message,
                                    'success');
                            },
                            error: function(xhr) {
                                var response = xhr.responseJSON;
                                Swal.fire('Error', response.message, 'error');
                            }
                        })

                    })
                }
            });
        })

        $(document).on('click', '#btnEditarRango', function() {
            var id = $(this).data("id");

            // Verificar la URL generada
            var url = "{{ route('admin.vehicleroutes.massUpdate', ':id') }}".replace(':id', id);
            console.log("URL Generada: " + url); // Agregar esto para depurar

            // Abrir el modal para la edición masiva
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    console.log(response); // Verifica si la respuesta contiene el formulario

                    // Cargar el formulario dentro del modal de rango
                    $("#rangeEditModal .modal-body").html(response);
                    $("#rangeEditModal").modal("show"); // Mostrar el modal

                    // Adjuntar el evento de envío del formulario en el modal
                    $("#rangeEditModal form").on("submit", function(e) {
                        e.preventDefault();

                        var form = $(this);
                        var formData = new FormData(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $("#rangeEditModal").modal("hide");
                                refreshTable();
                                Swal.fire('Proceso exitoso', response.message,
                                    'success');
                            },
                            error: function(xhr) {
                                var response = xhr.responseJSON;
                                Swal.fire('Error', response.message, 'error');
                            }
                        })
                    })
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo cargar el formulario de edición masiva.',
                        icon: 'error'
                    });
                }
            });
        });



        function refreshTable() {
            var table = $('#datatable').DataTable();
            table.ajax.reload(null, false); // Recargar datos sin perder la paginación
        }
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

@stop
