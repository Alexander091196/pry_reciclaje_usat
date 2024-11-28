<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', null, [
        'class' => 'form-control',
        'placeholder' => 'Ingrese el nombre de la actividad',
        'required',
    ]) !!}
</div>
<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('startdate', 'Fecha inicial') !!}
        {!! Form::date('startdate', null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione una fecha',
            'required',
        ]) !!}
    </div>
    <div class="form-group col-6">
        {!! Form::label('lastdate', 'Fecha final') !!}
        {!! Form::date('lastdate', null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione una fecha',
            'required',
        ]) !!}
    </div>
</div>
