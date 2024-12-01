@extends('adminlte::page')

@section('title', 'Programación')


@section('content')
    <div class="p-2"></div>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-success float-right" id="btnNuevo"><i class="fas fa-plus"></i> Nuevo</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Programación</h5>
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

        $('#btnNuevo').click(function() {
            $.ajax({
                url: "{{ route('admin.programming.create') }}",
                type: "GET",
                success: function(response) {
                    $("#formModal #exampleModalLabel").html("Nueva programación");
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






{{-- 
//     $(".btnEditar").click(function() {
//         var id = $(this).attr(
//             'id'
//             ); // Obtener el ID del botón (asumiendo que el botón tiene el atributo 'id' con el valor correcto)

//         $.ajax({
//             url: "{{ route('admin.programming.edit', ':id') }}".replace(':id',
//                 id), // Reemplazar :id con el valor real
//             type: "GET",
//             success: function(response) {
//                 // Insertar la respuesta (formulario de edición) en el cuerpo del modal
//                 $('#formModalNormal .modal-body').html(
//                     response); // Asegúrate de usar el ID correcto de tu modal

//                 $('#formModalNormal').modal('show'); // Mostrar el modal
//             },
//             error: function() {
//                 alert("Ocurrió un error al cargar el formulario de edición.");
//             }
//         });
//     });




//     $(".fmrEliminar").submit(function(e) {
//         e.preventDefault();
//         Swal.fire({
//             title: "Seguro de eliminar?",
//             text: "Esta accion es irreversible!",
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#3085d6",
//             cancelButtonColor: "#d33",
//             confirmButtonText: "Si, eliminar!"
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 this.submit();
//             }
//         });
//     });

//     function refreshTable() {
//         var table = $('#datatable').DataTable();
//         table.ajax.reload(null, false); // Recargar datos sin perder la paginación
//     }
// --}}
