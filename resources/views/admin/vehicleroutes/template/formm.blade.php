

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('start_date', 'Fecha Inicio') !!}
        {!! Form::date('start_date', old('start_date', $programming->start_date), [
            'class' => 'form-control',
            'required',
            'readonly' => true, // solo visualización
        ]) !!}
    </div>
    <div class="form-group col-6">
        {!! Form::label('end_date', 'Fecha Fin') !!}
        {!! Form::date('end_date', old('end_date', $programming->end_date), [
            'class' => 'form-control',
            'required',
            'readonly' => true, // solo visualización
        ]) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('routestatus_id', 'Estado') !!}
        {!! Form::select('routestatus_id', $routestatus, old('routestatus_id'), ['class' => 'form-control', 'required']) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('vehicle_id', 'Vehículo') !!}
        {!! Form::select('vehicle_id', $vehicles, old('vehicle_id'), ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="form-group col-6">
        {!! Form::label('route_id', 'Ruta') !!}
        {!! Form::select('route_id', $routes, old('route_id'), ['class' => 'form-control', 'required']) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('schedule_id', 'Turno') !!}
        {!! Form::select('schedule_id', $schedules, old('schedule_id'), ['class' => 'form-control', 'required']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
</div>


