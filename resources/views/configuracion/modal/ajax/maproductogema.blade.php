<form method="POST" action="{{ url('/guardar-producto-gema/'.$idopcion.'/'.Hashids::encode(substr($producto_id, -8))) }}">
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
					</div>   
		</div>
	

		<div  class="row regla-modal">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Cantidad <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-12">

							<input  type="text"
											id="cantidad" name='cantidad' 
											value="@if(isset($detalleventa)){{old('cantidad' ,$detalleventa->cantidad)}}@else{{old('cantidad')}}@endif" 
											placeholder="Cantidad"														
											autocomplete="off" class="form-control input-sm importe" data-aw="2"/>

					</div>
				</div>
			</div>
		</div>	
	</div>

	<div class="modal-footer">
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-producto-gema">Guardar</button>
	</div>
</form>

@if(isset($ajax))
	<script type="text/javascript">
		$(document).ready(function(){

  // App.init();
        // App.formElements();
			$('.importe').inputmask({ 'alias': 'numeric', 
			'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
			'digitsOptional': false, 
			'prefix': '', 
			'placeholder': '0'});
			
			// buscar productos 
			$('.select4').select2();
			// $('.select3').select2({

		});
	</script>
@endif





