{!! Form::model($vehicleroute, ['route'=>['admin.vehicleroutes.update', $vehicleroute],'method' => 'put']) !!}
@include('admin.vehicleroutes.template.form')
<button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Cerrar</button>
{!! Form::close() !!}