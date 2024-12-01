<div class="form-group">
    {!! Form::label('name', 'Nombre del horario') !!}
    {!! Form::text('name', null, [
        'class' => 'form-control',
        'placeholder' => 'Nombre del horario',
        'required',
        'id' => 'name',
    ]) !!}
    <small class="form-text text-muted">Ejemplo: Lunes, Martes, Miércoles, etc.</small>
    <div id="error-message" style="color: red; font-size: 0.9em; display: none;">Por favor, introduce un día válido de la
        semana.</div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('time_start', 'Hora inicio') !!}
        {!! Form::time('time_start', null, [
            'class' => 'form-control',
            'id' => 'time_start',
            'required',
        ]) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('time_end', 'Hora fin') !!}
        {!! Form::time('time_end', null, [
            'class' => 'form-control',
            'id' => 'time_end',
            'required',
        ]) !!}
    </div>
</div>


<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, [
        'class' => 'form-control',
        'placeholder' => 'Descripción del sector (opcional)',
    ]) !!}
    <small class="form-text text-muted">Opcional: Caracteristicas del sector.</small>
</div>



<script>
    document.getElementById('name').addEventListener('blur', function() {
        // Lista de días válidos con normalización
        const diasSemana = {
            'lunes': 'Lunes',
            'martes': 'Martes',
            'miercoles': 'Miércoles',
            'miércoles': 'Miércoles',
            'jueves': 'Jueves',
            'viernes': 'Viernes',
            'sabado': 'Sábado',
            'sábado': 'Sábado',
            'domingo': 'Domingo'
        };

        const inputField = this;
        const valor = inputField.value.trim().toLowerCase()
            .normalize("NFD") // Elimina acentos
            .replace(/[\u0300-\u036f]/g, ""); // Quita marcas diacríticas

        const errorMessage = document.getElementById('error-message');

        if (diasSemana[valor]) {
            // Si el valor es válido, corrige el texto y lo muestra
            inputField.value = diasSemana[valor];
            errorMessage.style.display = 'none';
            inputField.setCustomValidity('');
        } else if (valor === '') {
            // Si el campo está vacío, limpia los errores
            errorMessage.style.display = 'none';
            inputField.setCustomValidity('');
        } else {
            // Si el valor no es válido, muestra el mensaje de error
            errorMessage.style.display = 'block';
            inputField.setCustomValidity(
                'El campo debe contener un día válido de la semana.');
        }
    });
</script>
