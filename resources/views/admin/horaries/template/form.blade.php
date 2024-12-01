

<div class="form-row">


    <div class="form-group col-6">
        {!! Form::label('day', 'Dia:') !!}
        <select name="day" id="day" class="form-control">
            <option value="LUNES">Lunes</option>
            <option value="MARTES">Martes</option>
            <option value="MIERCOLES">Miercoles</option>
            <option value="JUEVES">Jueves</option>
            <option value="VIERNES">Viernes</option>
            <option value="SABADO">Sábado</option>
        </select>
    </div>

    <div class="form-group col-6">
        {!! Form::label('starttime', 'Hora inicio:') !!}
        {!! Form::time('starttime', null, [
            'class' => 'form-control',
            'required',
        ]) !!}
    </div>

    
</div>


<div class="form-row">
<div class="form-group col-6">
        {!! Form::label('vehicle_id', 'Vehiculo') !!}
        {!! Form::select('vehicle_id', $vehicles, null, ['class' => 'form-control', 'id' => 'vehicle_id', 'required']) !!}
    </div>
    
    <div class="form-group col-6">
        {!! Form::label('lasttime', 'Hora fin:') !!}
        {!! Form::time('lasttime', null, [
            'class' => 'form-control',
            'required',
        ]) !!}
    </div>
    

</div>

<div class="form-row">
<div class="form-group col-6">
        {!! Form::label('typemantenimiento_id', 'Tipo de mantenimiento:') !!}
        {!! Form::select('typemantenimiento_id', $types, null, [
            'class' => 'form-control',
            'id' => 'activitie_id',
            'required',
        ]) !!}
    </div>


</div>

