<div class="form-row">
    <div class="form-group col-md-8">
        {!! Form::hidden('vehicle_id', $vehicle->id, ['class'=>'form-control', 'id'=>'vehicle_id']) !!}
        {!! Form::label('user_id', 'Buscar por DNI', ['class' => 'form-label']) !!}
        <div class="input-group">
            {!! Form::text('user_id', null, ['class'=>'form-control me-2', 'id'=>'user_dni', 'placeholder'=>'Ingrese el DNI', 'required']) !!}
            <button type="button" class="btn btn-primary" id="search_user_btn">Buscar</button>
        </div>
    </div>
</div>
 
<div class="form-row">
    <div class="form-group col-6">
        <label for="user_id">Persona:</label>
        <input type="text" class="form-control" id="user_id" readonly>
    </div>
    {{-- <div class="form-group col-6">
        <label for="usertype_id">Tipo de persona:</label>
        <input type="text" class="form-control" id="usertype_id" readonly>
    </div>
     --}}
</div>

<div class="form-row">
    <div class="form-group col-6"> 
        {!! Form::label('fecha', 'Fecha de asignación') !!} 
        {!! Form::date('fecha', date('Y-m-d'), 
        ['class' => 'form-control', 
        'placeholder' => 'Seleccione una fecha', 
        'required',] ) !!} 
        </div>

    
</div> 

    <script>
        $('#search_user_btn').click(function() {
        var dni = $('#user_dni').val();

        $.ajax({
            url: "{{ route('admin.searchbydni', '_id') }}".replace("_id", dni),
            type: "GET",
            dataType: "JSON",
            contentType: "application/json",
            success: function(response) {
                if (response.usertype_id) {
                    $('#usertype_id').val(response.usertype_id);
                } else {
                    $('#usertype_id').val('');
                }
                $('#user_id').val(response.name + " " + response.lastname);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error al buscar usuario:', textStatus, errorThrown);
                alert('Ocurrió un error al buscar al usuario. Intente nuevamente.');
            }
        });
        });
    </script>

