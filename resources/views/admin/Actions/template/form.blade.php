<div class="form-group">
    {!! Form::label('date', 'Fecha') !!}
    {!! Form::text('date', null, [
        'class' => 'form-control',
        'id' => 'dateInput', // Necesario para que Flatpickr lo reconozca
        'placeholder' => 'Seleccione una fecha',
        'required',
    ]) !!}
</div>

{!! Form::hidden('horarie_id', session('horarie_id')) !!}
<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, [
        'class' => 'form-control',
        'style' => 'height:100px',
        'placeholder' => 'Ingrese la descripción',
        'required',
    ]) !!}
</div>

<div class="form-row">
    <div class="form-group col-3">
        {!! Form::file('image', [
            'class' => 'form-control-file d-none', // Oculta el input
            'accept' => 'image/*',
            'id' => 'imageInput',
        ]) !!}
        <button type="button" class="btn btn-primary" id="imageButton"><i class="fas fa-image"></i> Imagen</button>
    </div>
    <div class="form-group col-9">
        <img id="imagePreview" src="#" alt="Vista previa de la imagen"
            style="max-width: 100%; height: auto; display: none;">
    </div>
</div>

<script>
    $('#imageInput').change(function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    $('#imageButton').click(function() {
        $('#imageInput').click();
    });

</script>
<script>
   document.addEventListener('DOMContentLoaded', function () {
    const allowedDay = "LUNES"; // Cambia dinámicamente este valor según el horario
    const dayMapping = {
        "DOMINGO": 0,
        "LUNES": 1,
        "MARTES": 2,
        "MIÉRCOLES": 3,
        "JUEVES": 4,
        "VIERNES": 5,
        "SÁBADO": 6,
    };

    const dayOfWeek = dayMapping[allowedDay];

    flatpickr("#dateInput", {
        dateFormat: "Y-m-d",
        enable: [
            function (date) {
                // Habilita solo los días que coincidan con el día permitido
                return date.getDay() === dayOfWeek;
            }
        ],
        locale: "es", // Configura el idioma
    });
});
</script>
