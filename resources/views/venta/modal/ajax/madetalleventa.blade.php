<form method="POST" action="{{ url('/guardar-detalle-venta/'.$idopcion.'/'.Hashids::encode(substr($venta_id, -8))) }}">
			{{ csrf_field() }}

	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			{{$tipo_comprobante_nombre}}<span>: {{$serie}} - {{$numero}}</span>
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
																		'data-aw'     => '1'
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
											value="@if(isset($detalleventa)){{old('cantidad' ,$detalleventa->cantidad)}}@endif" 
											placeholder="Cantidad"
											autocomplete="off" class="form-control input-sm importe" data-aw="2"/>

					</div>
				</div>
			</div>


			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Precio Unitario <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-12">

							<input  type="text"
											id="preciounitario" name='preciounitario' 
											value="@if(isset($detalleventa)){{old('preciounitario' ,$detalleventa->preciounitario)}}@endif" 
											placeholder="Precio Unitario"
											autocomplete="off" class="form-control input-sm importe" data-aw="3"/>

					</div>
				</div>
			</div>			
		</div>
		<div class="separadordiv"></div>
		<div  class="row regla-modal">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >IGV <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-12 dflex">

						<div class="be-checkbox has-success" style="display: block !important;">
							<input type="hidden" name="indigv" id='indigv' value="@if(isset($detalleventa)){{old('igv' ,$detalleventa->igv)}}@else {{ 0 }}@endif">
                          <input id="ckindigv" type="checkbox"  title="IGV" 
                          	@if(isset($detalleventa) && ($detalleventa->indigv==1))
                          		checked=""
                          	@endif
                          >
                          <label for="ckindigv"></label>
                        </div>


						<input  type="text"
							id="porcigv" name='porcigv' title="PORCENTAJE DE IGV" 
							value="@if(isset($detalleventa)){{old('porcigv' ,$detalleventa->porcigv)}}@else {{ 0.0 }}@endif" 
							placeholder="Porcentaje IGV"
							autocomplete="off" class="form-control input-sm importe porcigv" data-aw="2"/>

					</div>
				</div>
			</div>


			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >MONTO IGV <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-12">

							<input  type="text" readonly 
											id="igv" name='igv' 
											value="@if(isset($detalleventa)){{old('igv' ,$detalleventa->igv)}}@else {{ 0.0 }}@endif" 
											placeholder="Monto IGV"
											autocomplete="off" class="form-control input-sm importe" data-aw="3"/>

					</div>
				</div>
			</div>			
		</div>
		<div class="separadordiv"></div>

		<div  class="row regla-modal">

			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Sub Total <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-12">

							<input  type="text"  readonly 
											id="subtotal" name='subtotal' 
											value="@if(isset($detalleventa)){{old('subtotal' ,$detalleventa->subtotal)}}@endif" 
											placeholder="Sub Total"
											autocomplete="off" class="form-control input-sm importe" data-aw="3"/>

					</div>
				</div>
			</div>			
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Total : </label>
					<div class="col-sm-12">

							<input  type="text"
											id="total" name='total' 
											value="@if(isset($detalleventa)){{old('total' ,$detalleventa->total)}}@endif" 
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

			// 		// Activamos la opcion "Tags" del plugin
			// 		placeholder: 'Seleccione un producto',
			// 		language: "es",
			// 		tags: true,
			// 		tokenSeparators: [','],
			// 		ajax: {
			// 				dataType: 'json',
			// 				url: '{{ url("buscarproducto") }}',
			// 				delay: 100,
			// 				data: function(params) {              		              	
			// 						return {
			// 								term: params.term
			// 						}
			// 				},
			// 				processResults: function (data, page) {              	
			// 					return {
			// 						results: data
			// 					};
			// 				},
			// 		}
			// });


		});
	</script>
@endif





