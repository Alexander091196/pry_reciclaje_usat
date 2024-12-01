{!! Form::model($vr, ['route'=>['admin.programming.update', $vr],'method' => 'put']) !!}
@include('admin.programming.template.form')
<button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Cerrar</button>
{!! Form::close() !!}