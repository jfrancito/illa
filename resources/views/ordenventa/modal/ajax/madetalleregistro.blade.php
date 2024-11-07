<form method="POST" action="{{ url('/guardar-detalle-orden-ventas/'.$idopcion.'/'.Hashids::encode(substr($registro_id, -8))) }}">
			{{ csrf_field() }}

	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			{{$cliente_nombre}}<span>: {{$registro->fecha}}</span>
		</h3>
	</div>
	<div class="modal-body">


		<div  class="row regla-modal">
			<div class="col-sm-12">
						<div class="form-group">
							<label class=" col-sm-12 control-label labelleft negrita">Producto <span class="obligatorio">(*)</span></label>
							<div class="col-sm-11">
								{!! Form::select( 'producto_id', $combo_producto, $select_producto,
																	[
																		'class'       => 'select4 form-control control input-xs' ,
																		'id'          => 'producto_id',        
																		'data-aw'     => '1',
																		'required'		=>	''
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('producto_id')  , 
																										'error' => $errors->first('producto_id', ':message') , 
																										'data' => '1'])
								
							</div>
							<span class="input-group-btn">
								<a href="{{ url('/agregar-productos/'.$idopcionproducto) }}" target="_blank">
									<button type="button" class="btn btn-primary" style="margin-left: -15px; height: 36px">
										<i class="icon mdi mdi-collection-plus"></i>
									</button>
								</a>		          	
							</span>
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
											value="{{ 1 }}" 
											placeholder="Cantidad"
											readonly = "readonly"
											autocomplete="off" class="form-control input-sm importe" data-aw="2"/>

					</div>
				</div>
			</div>
			<div class="preciounitarioov">
				@include('ordenventa.ajax.apreciounitario')
			</div>		
		</div>
		<div class="separadordiv"></div>
		<div  class="row regla-modal">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Total : </label>
					<div class="col-sm-12">

							<input  type="text"
											id="total" name='total' 
											value="{{ 0 }}}" 
											placeholder="Total"
											disabled="disabled"
											autocomplete="off" class="form-control input-sm importe" data-aw="4"/>

					</div>
				</div>
			</div>	
					
		</div>
	</div>

	<div class="modal-footer">
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-detalle-venta">Guardar</button>
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

			var indigv= $('#indigv').val();
			if(indigv==1){
				$('.venta #porcigv').prop('disabled', false); 
			}
			else{
				$('.venta #porcigv').prop('disabled', true); 
			}
			// buscar productos 
			$('.select4').select2();
			// $('.select3').select2({

		});
	</script>
@endif





