{!! Form::model($vehicleroute, ['route'=>['admin.vehicleroutes.massUpdate', $vehicleroute],'method' => 'put']) !!}
@include('admin.vehicleroutes.template.formm')
<button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Cerrar</button>
{!! Form::close() !!}