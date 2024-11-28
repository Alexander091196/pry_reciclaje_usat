

<div class="form-row">

    <div class="form-group col-4">
        {!! Form::label('date', 'Fecha') !!}
        {!! Form::date('date', null, [
            'class' => 'form-control',
            'placeholder' => 'Seleccione una fecha',
            'required',
        ]) !!}
    </div>

</div>
<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, 
    ['class'=>'form-control', 
    'style' =>'height:100px',
    'placeholder'=>'Ingrese la descripción ',
    'required',
    ]) !!}
</div>
