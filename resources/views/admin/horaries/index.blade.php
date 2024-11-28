@extends('adminlte::page')

@section('title', 'Horario')

@section('content')
    <div class="p-3"></div>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-success float-right" id="btnNuevo"><i class="fas fa-plus-circle"></i> Agregar
                horario</button>

            <div>
                <strong>Mantenimiento:</strong> {{ $act->name }}
            </div>

        </div>
        <div class="card-body">
            <div class="row">

            </div>
            <div class="col-12 card" style="min-height: 50px">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <table class="table table-striped" id="datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>DIA</th>
                                <th>VEHICULO</th>
                                <th>TIPO</th>
                                <th>HORA INICIO</th>
                                <th>HORA FIN</th>
                                <th width=20></th>
                                <th width=20></th>
                                <th width=20></th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($horaries as $hor)
                                <tr>
                                    <td>{{ $hor->id }}</td>
                                    <td>{{ $hor->day }}</td>
                                    <td>{{ $hor->vehicle }}</td>
                                    <td>{{ $hor->type }}</td>
                                    <td>{{ $hor->hori }}</td>
                                    <td>{{ $hor->horf }}</td>
                                    <td>
                                        <a href="{{ route('admin.horaries.show', $hor->id) }}"
                                            class="btn btn-secondary btn-sm"><i class="fas fa-wrench"></i></a>
                                    </td>
                                    <td>
                                        <button class="btnEditar btn btn-primary" id="{{ $hor->id }}">
                                            <i class="fa fa-edit"></i></button>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.horaries.destroy', $hor->id) }}" method="POST"
                                            class="fmrEliminar">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Asignar horario</h5>
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
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        $('#datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            }
        });

        $('#btnNuevo').click(function() {
            $.ajax({
                url: "{{ route('admin.horaries.create') }}",
                type: "GET",
                success: function(response) {
                    $('#formModal .modal-body').html(response);
                    $('#formModal').modal('show');
                }
            })
        });

        $(".btnEditar").click(function() {
            var id = $(this).attr('id');
            $.ajax({

                url: "{{ route('admin.horaries.edit', '_id') }}".replace('_id', id),
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
                title: "Seguro de eliminar?",
                text: "Esta accion es irreversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Eliminar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
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


@stop
