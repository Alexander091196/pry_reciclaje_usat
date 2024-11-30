@extends('adminlte::page')

@section('title', 'Perímetro de zona')

@section('content')
    <div class="p-3"></div>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-success float-right" id="btnNuevo" data-id="{{ $route->id }}">
                <i class="fas fa-plus"></i> Agregar Zona a Ruta
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Información de la ruta -->
                <div class="col-3 card" style="min-height: 50px">
                    <br>
                    <div class="card-body">
                        <label for="">Nombre de la Ruta:</label><br>
                        {{ $route->name }} <br><br>
                        <label for="">Latitud y Longitud Inicial:</label><br>
                        ({{ $route->latitude_start }}, {{ $route->longitude_start }})<br><br>
                        <label for="">Latitud y Longitud Final:</label><br>
                        ({{ $route->latitude_end }}, {{ $route->longitude_end }})<br><br>
                    </div>
                </div>

                <!-- Tabla de zonas -->
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped" id="datatable">
                                <thead>
                                    <tr>
                                        <th>ZONAS</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <a href="{{ route('admin.routes.index') }}" class="btn btn-danger float-right">
                <i class="fas fa-chevron-left"></i> Retornar
            </a>
        </div>
    </div>

    <!-- Mapa -->
    <div class="card">
        <div class="card-header">Mapa de la ruta</div>
        <div class="card-body">
            <div id="map" style="height:400px"></div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de Zonas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se cargará el formulario -->
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="/css/admin_custom.css">
    <!-- Estilos adicionales -->
@stop

@section('js')
    <script>
        // Inicializar DataTable
        var table = $('#datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "ajax": "{{ route('admin.routezones.show', $route->id) }}",
            "columns": [{
                    "data": "zone_name",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "actions",
                    "orderable": false,
                    "searchable": false
                }
            ]
        });

        $('#btnNuevo').click(function() {
            var routeId = $(this).attr('data-id');
            $.ajax({
                url: "{{ route('admin.routezones.create', '_id') }}".replace('_id', routeId),
                type: "GET",
                success: function(response) {
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
                                table.ajax.reload(null,
                                    false
                                ); // Recargar datos sin perder la paginación
                                Swal.fire('Proceso exitoso', response.message,
                                    'success');
                            },
                            error: function(xhr) {
                                var response = xhr.responseJSON;
                                Swal.fire('Error', response.message, 'error');
                            }
                        });
                    });
                }
            });
        });

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

        // Variables con datos desde el backend
        var route = {
            start: {
                lat: {{ $route->latitude_start }},
                lng: {{ $route->longitude_start }}
            },
            end: {
                lat: {{ $route->latitude_end }},
                lng: {{ $route->longitude_end }}
            }
        };

        // Zona(s) con sus coordenadas (pasadas desde el backend)
        var zones = @json($perimeter);

        // Inicializar el mapa
        function initMap() {
            var mapOptions = {
                center: {
                    lat: route.start.lat,
                    lng: route.start.lng
                },
                zoom: 15
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Dibujar la ruta
            var routePath = new google.maps.Polyline({
                path: [route.start, route.end],
                geodesic: true,
                strokeColor: '#0000FF',
                strokeOpacity: 1.0,
                strokeWeight: 2,
                map: map
            });

            // Marcadores de inicio y fin de la ruta
            new google.maps.Marker({
                position: route.start,
                map: map,
                title: 'Inicio de la Ruta'
            });

            new google.maps.Marker({
                position: route.end,
                map: map,
                title: 'Final de la Ruta'
            });

            // Dibujar el perímetro de las zonas
            zones.forEach(function(zone) {
                var zonePolygon = new google.maps.Polygon({
                    paths: zone.coords, // Coordenadas de la zona
                    strokeColor: '#FF0000', // Color del perímetro
                    strokeOpacity: 0.8, // Opacidad del perímetro
                    strokeWeight: 2, // Grosor del perímetro
                    fillColor: '#FF0000', // Color de relleno
                    fillOpacity: 0.35, // Opacidad de relleno
                    map: map
                });

                // Opcional: agregar un marcador en el centro de la zona
                var center = getPolygonCenter(zone.coords);
                new google.maps.Marker({
                    position: center,
                    map: map,
                    title: zone.name
                });
            });
        }

        // Función para calcular el centro del polígono (opcional)
        function getPolygonCenter(coords) {
            var latSum = 0;
            var lngSum = 0;

            coords.forEach(function(coord) {
                latSum += coord.lat;
                lngSum += coord.lng;
            });

            var latCenter = latSum / coords.length;
            var lngCenter = lngSum / coords.length;

            return {
                lat: latCenter,
                lng: lngCenter
            };
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
@stop
