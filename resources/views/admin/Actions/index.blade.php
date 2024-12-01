@extends('adminlte::page')

@section('title', 'Horario')

@section('content')
    <div class="p-3"></div>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-success float-right" id="btnNuevo"><i class="fas fa-plus-circle"></i> Agregar
                actividad</button>

            <div>
                <strong>Dia del horario:</strong> {{ $hor->day }}
            </div>

        </div>
        <div class="card-body">
            <div class="row">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="col-12 card" style="min-height: 50px">
                <div class="card-body">
                    <table class="table table-striped" id="datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>IMAGEN</th>
                                <th>FECHA</th>
                                <th>DESCRIPCION</th>
                                <th width=20>EDIT</th>
                                <th width=20>DEL</th>

                            </tr>
                        </thead>

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
                    <h5 class="modal-title" id="exampleModalLabel">Registro de actividad</h5>
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
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
@stop




@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                "ajax": "{{ route('admin.actions.index') }}", // La ruta que llama al controlador vía AJAX
                "columns": [{
                        "data": "id",
                    },
                    {
                        "data": "image",
                    },
                    {
                        "data": "date",
                    },
                    {
                        "data": "description",
                    },
                    {
                        "data": "edit",
                    },
                    {
                        "data": "delete",
                    }

                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
        });

        $('#btnNuevo').click(function() {
    $.ajax({
        url: "{{ route('admin.actions.create') }}",
        type: "GET",
        success: function(response) {
            // Carga el contenido del modal
            $("#formModal #exampleModalLabel").html("Nueva acción");
            $("#formModal .modal-body").html(response);
            $("#formModal").modal("show");

            // Inicializa Flatpickr dentro del modal
            flatpickr("#dateInput", {
                dateFormat: "Y-m-d",
                enable: [
                    function (date) {
                        // Configuración para habilitar solo los lunes
                        const allowedDay = "{{ $hor->day }}"; // Pasa el día dinámicamente desde el backend
                        const dayMapping = {
                            "DOMINGO": 0,
                            "LUNES": 1,
                            "MARTES": 2,
                            "MIERCOLES": 3,
                            "JUEVES": 4,
                            "VIERNES": 5,
                            "SABADO": 6,
                        };

                        const dayOfWeek = dayMapping[allowedDay];
                        return date.getDay() === dayOfWeek;
                    }
                ],
                locale: "es", // Configura el idioma
            });

            // Manejo del formulario dentro del modal
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
                        Swal.fire('Proceso exitoso', response.message, 'success');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON); // Agrega este log
                        if (xhr.status === 422) {
                            Swal.fire('Error', xhr.responseJSON.message, 'error');
                        } else if (xhr.status === 500) {
                            Swal.fire('Error', 'Error interno del servidor: ' + xhr.responseJSON.message, 'error');
                        } else {
                            Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
                        }
                    }
                });
            });
        }
    });
});

        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr("id");

            $.ajax({
                url: "{{ route('admin.actions.edit', 'id') }}".replace('id', id),
                type: "GET",
                success: function(response) {
                    $("#formModal #exampleModalLabel").html("Modificar acción");
                    $("#formModal .modal-body").html(response);
                    $("#formModal").modal("show");

                    // Inicializa Flatpickr dentro del modal
            flatpickr("#dateInput", {
                dateFormat: "Y-m-d",
                enable: [
                    function (date) {
                        // Configuración para habilitar solo los lunes
                        const allowedDay = "{{ $hor->day }}"; // Pasa el día dinámicamente desde el backend
                        const dayMapping = {
                            "DOMINGO": 0,
                            "LUNES": 1,
                            "MARTES": 2,
                            "MIERCOLES": 3,
                            "JUEVES": 4,
                            "VIERNES": 5,
                            "SABADO": 6,
                        };

                        const dayOfWeek = dayMapping[allowedDay];
                        return date.getDay() === dayOfWeek;
                    }
                ],
                locale: "es", // Configura el idioma
            });

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

        $(document).on('submit', '.frmEliminar', function(e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "Está seguro de eliminar?",
                text: "Está acción no se puede revertir!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            refreshTable();
                            Swal.fire('Proceso existoso', response.message, 'success');
                        },
                        error: function(xhr) {
                            var response = xhr.responseJSON;
                            Swal.fire('Error', response.message, 'error');
                        }
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
