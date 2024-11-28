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

<div class="form-row">
    <div class="form-group col-3">
        {!! Form::file('logo', [
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
