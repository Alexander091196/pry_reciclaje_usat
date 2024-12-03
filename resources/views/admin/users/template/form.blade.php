<div class="form-group">
    {!! Form::label('dni', 'DNI') !!}
    {!! Form::text('dni', null, [
        'class' => 'form-control',
        'placeholder' => 'Ingrese el DNI del usuario',
        'required',
        'maxlength' => 8,
        'pattern' => '[0-9]{8}',
        'title' => 'Debe contener exactamente 8 dígitos numéricos',
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', null, [
        'class' => 'form-control',
        'placeholder' => 'Ingrese el nombre del usuario',
        'required',
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('email', 'Correo Electrónico') !!}
    {!! Form::email('email', null, [
        'class' => 'form-control',
        'placeholder' => 'Ingrese el correo electrónico del usuario',
        'required',
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('license', 'Licencia', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('license', null, [
        'class' => 'form-control',
        'placeholder' => 'Ingrese la licencia del usuario',
        'required' => false, // Ahora no es obligatorio
        'minlength' => '3', // Licencia mínima de 3 caracteres
        'maxlength' => '6', // Licencia máxima de 6 caracteres
        'pattern' => '^(|[A]{1}-(I|II[a-c]|III[a-c]))$', // Permite vacío o los formatos especificados
        'title' => 'La licencia debe tener el formato: A-I, A-IIa, A-IIb, A-IIIa, A-IIIb, o A-IIIc, o estar vacía',
    ]) !!}
    <small class="form-text text-muted">
        La licencia debe tener el formato <strong>A-I</strong>, <strong>A-IIa</strong>, <strong>A-IIb</strong>,
        <strong>A-IIIa</strong>, <strong>A-IIIb</strong>, o <strong>A-IIIc</strong>. También puede dejarse en blanco.
    </small>
    <div class="invalid-feedback" id="license-feedback">
        El formato de la licencia no es válido. Ejemplo: <strong>A-I</strong>, <strong>A-IIa</strong>,
        <strong>A-IIb</strong>, <strong>A-IIIa</strong>, <strong>A-IIIb</strong>, o <strong>A-IIIc</strong>.
    </div>
</div>




<div class="form-group">
    {!! Form::label('usertype_id', 'Tipo de Usuario') !!}
    {!! Form::select('usertype_id', $usertypes, null, [
        'class' => 'form-control',
        'placeholder' => 'Seleccione el tipo de usuario',
        'required',
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('password', 'Contraseña') !!}
    {!! Form::password('password', [
        'class' => 'form-control',
        'placeholder' => 'Ingrese una contraseña',
        // Solo aplica 'required' en modo creación
        'required' => Route::currentRouteName() === 'admin.users.create' ? 'required' : null,
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('password_confirmation', 'Confirmar Contraseña') !!}
    {!! Form::password('password_confirmation', [
        'class' => 'form-control',
        'placeholder' => 'Confirme la contraseña',
        // Solo aplica 'required' en modo creación
        'required' => Route::currentRouteName() === 'admin.users.create' ? 'required' : null,
    ]) !!}
</div>
