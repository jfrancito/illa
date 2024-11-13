|<form method="POST" action="{{ url('/eliminar-detalle-orden-gema/'.$idopcion.'/'.$esquema_id.'/'.$gema_esquema_id.'/'.$ordenventa_id) }}">
			{{ csrf_field() }}
	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title"> 
			ELIMINAR GEMA			
		</h3>
	</div>
	<div class="modal-body">
		<div  class="row regla-modal">
			<span class="panel-heading"><center>¿Esta seguro de querer eliminar la siguiente Gema?</center></span>
			<span class="panel-subtitle"><center><b>{{$detesquemaproducto->tipodescripcion}}: {{$detesquemaproducto->origendescripcion}}</b></center></span>
		</div>		
	</div>

	<div class="modal-footer">
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-detalle-venta">Guardar</button>
	</div>
</form>
@if(isset($ajax))
	<script type="text/javascript">
		$(document).ready(function(){
			$('.importe').inputmask({ 'alias': 'numeric', 
			'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
			'digitsOptional': false, 
			'prefix': '', 
			'placeholder': '0'});
			var indigv= $('#indigv').val();
			if(indigv==1){
				$('.venta #porcigv').prop('disabled', false); 
			}
			else{
				$('.venta #porcigv').prop('disabled', true); 
			}
			$('.select4').select2();
		});
	</script>
@endif





