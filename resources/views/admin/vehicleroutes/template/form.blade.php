<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('date_route', 'Fecha de ruta') !!}
        {!! Form::date('date_route', null, [
            'class' => 'form-control',
            'required',
            'disabled' => 'disabled'
        ]) !!}
    </div>
    <div class="form-group col-6">
        {!! Form::label('routestatus_id', 'Estado') !!}
        {!! Form::select('routestatus_id', $routestatus, null, [
            'class' => 'form-control',
            'id' => 'routestatus_id',
            'required',
        ]) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('vehicle_id', 'Vehículo') !!}
        {!! Form::select('vehicle_id', $vehicles, null, ['class' => 'form-control', 'id' => 'vehicle_id', 'required']) !!}
    </div>
    <div class="form-group col-6">
        {!! Form::label('route_id', 'Ruta') !!}
        {!! Form::select('route_id', $routes, null, [
            'class' => 'form-control',
            'id' => 'route_id',
            'required',
        ]) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('schedule_id', 'Turno') !!}
        {!! Form::select('schedule_id', $schedules, null, [
            'id' => 'schedule_id',
            'class' => 'form-control',
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
    <small class="form-text text-muted">Opcional: Características del sector.</small>
</div>
