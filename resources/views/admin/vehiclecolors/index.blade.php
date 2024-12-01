@extends('adminlte::page')

@section('title', 'Colores')

@section('content')
    <div class="p-2"></div>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-success float-right" id="btnNuevo"><i class="fas fa-plus-circle"></i> Registrar</button>
            <h4><b>Listado de colores</b></h4>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped" id="datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>COLOR</th>
                        <th>CÓDIGO RGB</th>
                        <th width="10"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

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
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="/css/admin_custom.css">
    @vite(['resources/css/app.css'])
    @livewireStyles
@stop

@section('js')
    @livewireScripts
    @vite(['resources/js/app.js'])
    <script>
        $(document).ready(function() {
            // Configurar DataTable
            var table = $('#datatable').DataTable({
                "ajax": "{{ route('admin.vehiclecolors.index') }}",
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `<div style="width: 20px; height: 20px; background-color: rgb(${row.red}, ${row.green}, ${row.blue}); border-radius: 50%;"></div>`;
                        },
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `rgb(${row.red}, ${row.green}, ${row.blue})`;
                        }
                    },
                    {
                        "data": "actions",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });

            // Registrar un nuevo color
            $('#btnNuevo').click(function() {
                $.ajax({
                    url: "{{ route('admin.vehiclecolors.create') }}",
                    type: "GET",
                    success: function(response) {
                        $("#formModal #exampleModalLabel").html("Registrar nuevo color");
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
                                    table.ajax.reload(null, false);
                                    Swal.fire('Proceso exitoso', response
                                        .message, 'success');
                                },
                                error: function(xhr) {
                                    Swal.fire('Error',
                                        'No se pudo registrar el color.',
                                        'error');
                                }
                            });
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo cargar el formulario.', 'error');
                    }
                });
            });

            // Editar un color
            $(document).on('click', '.btnEditar', function() {
                var id = $(this).attr('id');

                $.ajax({
                    url: "{{ route('admin.vehiclecolors.edit', ':id') }}".replace(':id', id),
                    type: "GET",
                    success: function(response) {
                        $("#formModal #exampleModalLabel").html("Editar color");
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
                                    table.ajax.reload(null, false);
                                    Swal.fire('Proceso exitoso', response
                                        .message, 'success');
                                },
                                error: function(xhr) {
                                    Swal.fire('Error',
                                        'No se pudo actualizar el color.',
                                        'error');
                                }
                            });
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo cargar el formulario.', 'error');
                    }
                });
            });

            // Eliminar un color
            $(document).on('submit', '.frmEliminar', function(e) {
                e.preventDefault();
                var form = $(this);

                Swal.fire({
                    title: "¿Estás seguro de eliminar?",
                    text: "¡Esta acción no se puede deshacer!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            success: function(response) {
                                table.ajax.reload(null, false);
                                Swal.fire('Eliminado', response.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'No se pudo eliminar el color.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
