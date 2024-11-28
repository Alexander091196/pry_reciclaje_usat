{!! Form::model($hor, ['route'=>['admin.horaries.update', $hor],'method' => 'put']) !!}
@include('admin.horaries.template.form')
<button type="submit" class="btn btn-success"><i class="fas fa-save"> Actualizar</i></button>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}