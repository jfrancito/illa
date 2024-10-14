

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
							<label class=" col-sm-12 control-label labelleft negrita">Entidad <span class="obligatorio">(*)</span></label>
							<div class="col-sm-11">
								{!! Form::select( 'entidad_id', $combo_entidades, '',
																	[
																		'class'       => 'select4 form-control control input-xs' ,
																		'id'          => 'entidad_id',        
																		'data-aw'     => '1'
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('entidad_id')  , 
																										'error' => $errors->first('entidad_id', ':message') , 
																										'data' => '1'])
								
							</div>
							<span class="input-group-btn">
								<a href="{{ url('/agregar-cuentas-empresa/'.$idopcionproducto) }}" target="_blank">
									<button type="button" class="btn btn-primary" style="margin-left: -15px; height: 36px">
										<i class="icon mdi mdi-collection-plus"></i>
									</button>
								</a>		          	
							</span>
						</div>
					</div>   
		</div>


		<div  class="row regla-modal ajaxcuentasentidad">
			<div class="col-sm-12">
						<div class="form-group">
							<label class=" col-sm-12 control-label labelleft negrita">Cuenta <span class="obligatorio">(*)</span></label>
							<div class="col-sm-12">
								{!! Form::select( 'cuenta_id', $combo_cuentas, $select_cuenta,
																	[
																		'class'       => 'select4 form-control control input-xs' ,
																		'id'          => 'cuenta_id',        
																		'required'	=>'',
																		'data-aw'     => '1'
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('cuenta_id')  , 
																										'error' => $errors->first('cuenta_id', ':message') , 
																										'data' => '1'])
								
							</div>
						</div>
					</div>   
		</div>

		<div class="separadordiv"></div>


		<div class="separadordiv"></div>

		<div  class="row regla-modal">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">

					<label class=" col-sm-12 control-label labelleft negrita">Tipo Pago <span class="obligatorio">(*)</span></label>
					<div class="col-sm-12">
						{!! Form::select( 'tipo_pago_id', $combo_tipo_pago, $select_tipo_pago,
															[
																'class'       => 'form-control control input-sm select2 select3' ,
																'id'          => 'tipo_pago_id',
																'required'    => '',        
																'data-aw'     => '8',
																'disabled'    => 'disabled'
															]) !!}

						@include('error.erroresvalidate', [ 'id' => $errors->has('tipo_pago_id')  , 
																								'error' => $errors->first('tipo_pago_id', ':message') , 
																								'data' => '9'])
					</div>
				</div>
			</div>

			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Importe <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-12">

							<input  type="text"
											id="importe" name='importe' 
											required 
											value="@if(isset($detalle)){{old('importe' ,$detalle->importe)}}@endif" 
											placeholder="Cantidad"
											autocomplete="off" class="form-control input-sm importe" data-aw="2"/>

					</div>
				</div>
			</div>


					
		</div>

		<div class="separadordiv"></div>

		<div  class="row regla-modal">

			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Saldo <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-12">

							<input  type="text"  readonly 
											id="saldo" name='saldo' 
											value="@if(isset($registro)){{old('saldo' ,$registro->saldo)}}@else{{ '0.0' }}@endif" 
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
											value="@if(isset($registro)){{old('total' ,$registro->total)}}@else{{'0.0'  }}@endif" 
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





