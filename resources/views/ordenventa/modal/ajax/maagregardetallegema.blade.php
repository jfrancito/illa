|<form method="POST" action="{{ url('/agregar-detalle-orden-gema/'.$idopcion.'/'.$esquema_id.'/'.$ordenventa_id) }}">
			{{ csrf_field() }}


	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title"> 
			AGREGAR GEMA
		</h3>
	</div>
	<div class="modal-body">


		<div  class="row regla-modal">
			<div class="col-sm-12">


						<div class="form-group">
							<label class=" col-sm-12 control-label labelleft negrita">Tipo <span class="obligatorio">(*)</span></label>
							<div class="col-sm-12">
								{!! Form::select( 'tipogema_id', $combo_gemas, $select_gemas,
																	[
																		'class'       => 'select4 form-control control input-xs' ,
																		'id'          => 'tipogema_id',        
																		'data-aw'     => '1'
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('producto_id')  , 
																										'error' => $errors->first('tipogema_id', ':message') , 
																										'data' => '1'])
							</div>							
						</div>


						<div class="form-group">
							<label class=" col-sm-12 control-label labelleft negrita">Origen <span class="obligatorio">(*)</span></label>
							<div class="col-sm-12">
								{!! Form::select( 'origen_id', $combo_origen_gema, $select_origen_gema,
																	[
																		'class'       => 'select4 form-control control input-xs' ,
																		'id'          => 'origen_id',        
																		'data-aw'     => '1',
																		'required'		=>	''
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('origen_id')  , 
																										'error' => $errors->first('origen_id', ':message') , 
																										'data' => '1'])
								
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-12 control-label labelleft negrita" >Cantidad <span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12">

									<input  type="text"
													id="cantidad" name='cantidad' 
													value="{{0}}" 
													placeholder="Cantidad"
													autocomplete="off" class="form-control input-sm importe" data-aw="2"/>

							</div>
						</div>

						
						<div class="form-group">
							<label class="col-sm-12 control-label labelleft negrita" >Precio <span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12">

									<input  type="text"
													id="precio" name='precio' 
													value="{{0}}" 
													placeholder="Precio"
													autocomplete="off" class="form-control input-sm importe" data-aw="2"/>

							</div>
						</div>

					</div>   
		</div>
	</div>

	<div class="modal-footer">
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-agregar-detalle-gema">Guardar</button>
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





