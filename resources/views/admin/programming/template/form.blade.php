<div class="form-row">

    <div class="form-group col-6">
        {!! Form::label('vehicle_id', 'Vehículo') !!}
        {!! Form::select('vehicle_id', $vehicles, null, ['class' => 'form-control', 'id' => 'vehicle_id', 'required']) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('route_id', 'Ruta:') !!}
        {!! Form::select('route_id', $routes, null, [
            'class' => 'form-control',
            'id' => 'route_id',
            'required',
        ]) !!}
    </div>

</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('startdate', 'Fecha de inicio') !!}
        <input type="date" name="startdate" id="startdate" class="form-control" required>
    </div>

    <div class="form-group col-6">
        {!! Form::label('lastdate', 'Fecha final') !!}
        <input type="date" name="lastdate" id="lastdate" class="form-control" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('schedule_id', 'Turno') !!}
        {!! Form::select('schedule_id', $schedules, null, [
            'class' => 'form-control',
            'id' => 'schedule_id',
            'required',
        ]) !!}
    </div>

    <div class="form-group col-6">
        {!! Form::label('status', 'Estado') !!}
        <div class="form-check">
            {!! Form::checkbox('status', 1, true, ['class' => 'form-check-input custom-checkbox', 'disabled' => 'disabled']) !!}
            {!! Form::label('status', 'Por empezar') !!}
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
