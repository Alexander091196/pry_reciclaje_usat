@extends('adminlte::page')

@section('title', 'Inicio | ReciclaUSAT')

@section('content_header')
    <h1></h1>
@stop
@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="/css/admin_custom.css">

    <style>
        /* Estilo personalizado limitado al contenedor principal */
        .main-content-container .jumbotron {
            background-color: #e8f5e9 !important;
            color: #1b5e20 !important;
            border-radius: 10px !important;
        }

        .main-content-container .jumbotron h2 {
            font-size: 2.5rem !important;
        }

        .main-content-container .jumbotron .btn-primary {
            background-color: #1b5e20 !important;
            border-color: #1b5e20 !important;
        }

        .main-content-container .jumbotron .btn-primary:hover {
            background-color: #43a047 !important;
            border-color: #43a047 !important;
        }

        /* Priorizar estilos para imágenes dentro del contenedor */
        .main-content-container img {
            max-width: 100% !important;
            height: auto !important;
        }

        /* Estilo de listas */
        .main-content-container ul {
            list-style-type: disc !important;
            margin-left: 20px !important;
        }

        /* Otros elementos del contenido */
        .main-content-container h4 {
            font-weight: bold !important;
            margin-top: 20px !important;
        }

        .custom-image {
            width: 80%;
            /* Ancho del 100% del contenedor */
            max-width: 300px;
            /* Máximo ancho de la imagen */
            height: auto;
            /* Ajuste proporcional de altura */
            display: block;
            margin: 0 auto;
            /* Centrar la imagen dentro del contenedor */
        }
    </style>
@stop

@section('content')
    <div class="container mt-4 main-content-container">
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron text-center">
                    <div class="col-md-12 d-flex justify-content-center align-items-center">
                        <img src="/vendor/adminlte/dist/img/logo1.png" class="img-fluid"
                            style="max-width: 150px !important; height: 150 !important;">
                    </div>
                    <h2 class="display-4">¡Bienvenidos a ReciclaUSAT!</h2>
                    <p class="lead">
                        Un proyecto comprometido con el medio ambiente desarrollado por los estudiantes del curso de
                        <b style="font-weight: 700;">Tópico Avanzado en Desarrollado de Software</b> del <b style="font-weight: 700;">Grupo 2</b> de la USAT.
                    </p>
                    <hr class="my-4">
                    <p>
                        Este sistema está diseñado para optimizar la gestión de la recolección de residuos, fomentando el
                        reciclaje y la sostenibilidad en nuestra comunidad.
                    </p>
                    <a class="btn btn-primary btn-lg" href="#objetivos" role="button"><i class="fas fa-leaf"></i> Conocer
                        más</a>
                </div>
            </div>
        </div>

        <div class="row" id="objetivos">
            <div class="col-md-12">
                <h4 class="text-center">Objetivos del Proyecto</h4>
                <p>
                <h4>Este sistema busca:</h4>
                </p>
                <ul>
                    <li>Promover la cultura del reciclaje en la comunidad universitaria.</li>
                    <li>Facilitar el registro y seguimiento de actividades de recolección de residuos.</li>
                    <li>Optimizar la logística de recolección mediante una gestión eficiente de recursos.</li>
                    <li>Involucrar a estudiantes y docentes en iniciativas de sostenibilidad ambiental.</li>
                </ul>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h4>¿Cómo funciona?</h4>
                <p>
                    ReciclaUSAT permite:
                </p>
                <ul>
                    <li>Registrar actividades de recolección.</li>
                    <li>Monitorear el estado de los residuos recolectados.</li>
                    <li>Generar reportes para la toma de decisiones.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <img src="/vendor/adminlte/dist/img/recycle.jpg" class="img-fluid custom-image"
                    alt="Recolección de Residuos">
            </div>

        </div>

        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <h4>Comprometidos con el futuro</h4>
                <p>
                    Este proyecto no solo busca resultados inmediatos, sino también fomentar una conciencia ambiental que
                    inspire a futuras generaciones.
                </p>
            </div>
        </div>
    </div><br>

@stop

@section('js')
    <script>
        console.log('Página de inicio cargada correctamente.');
    </script>
@stop
