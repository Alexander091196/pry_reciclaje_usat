<div class="form-group">
    {!! Form::label('name', 'Nombre del horario') !!}
    {!! Form::text('name', null, [
        'class' => 'form-control',
        'placeholder' => 'Nombre del horario',
        'required',
        'id' => 'name',
    ]) !!}
    <small class="form-text text-muted">Ejemplo: Mañana, Tarde, Noche, Madrugadas.</small>
    <div id="error-message" style="color: red; font-size: 0.9em; display: none;">Por favor, introduce un turno válido.</div>
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
