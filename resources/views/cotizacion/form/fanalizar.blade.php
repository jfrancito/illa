<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
						<div class="form-group">
									<label class="col-sm-12 control-label labelleft negrita" >Categoria <span class="obligatorio">(*)</span> :</label>
									<div class="col-sm-12 abajocaja">
																			{!! Form::select( 'categoria_id', $combo_categoria_analisis, $select_categoria_analisis,
																												[
																													'class'       => 'select2 form-control control input-xs' ,
																													'id'          => 'categoria_id',
																													'required'    => '',
																													'data-aw'     => '1'
																												]) !!}
																				@include('error.erroresvalidate', [ 'id' => $errors->has('categoria_id')  , 
																																						'error' => $errors->first('grupoanalisis_id', ':message') , 
																																						'data' => '1'])
									</div>
						</div>
			</div>

			<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 ajaxsubcategoriaproduccion">
						<div class="form-group">
							<label class="col-sm-12 control-label labelleft negrita" >Sub Categoria <span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12 abajocaja">
								{!! Form::select( 'subcategoria_id', $combo_subcategoria, $select_subcategoria,
													[
														'class'       => 'select2 select3 form-control control input-xs' ,
														'id'          => 'subcategoria_id',
														'required'    => '',
														'data-aw'     => '2'
													]) !!}
									@include('error.erroresvalidate', [ 'id' => $errors->has('subcategoria_id')  , 
																											'error' => $errors->first('subcategoria_id', ':message') , 
																																	'data' => '2'])
							</div>
						</div>
			</div>

			<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 ajaxproductoproduccion" id='ajaxproductoproduccion'>
					<div class="form-group">
							<label class="col-sm-12 control-label text-left">Producto <span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12 abajocaja">
								<input type="hidden" class="form-control control input-sm validarmayusculas" name="descripcion" id='descripcion'  value="">
								{!! Form::select( 'producto_id', $listaproducto, '',
																	[
																		'class'       => 'select2 select3 form-control control input-xs' ,
																		'id'          => 'producto_id',
																		'required'    => '',
																		'data-aw'     => '4'
																	]) !!}
									@include('error.erroresvalidate', [ 'id' => $errors->has('producto_id')  , 
																											'error' => $errors->first('producto_id', ':message') , 
																											'data' => '4'])


							</div>
					</div>
			</div>  


			<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 ajaxbtnmodalProd">
					<div class="form-group">
						<label class="col-sm-12 control-label labelleft negrita" > &nbsp; <span class="obligatorio"></span></label>
						<div class="col-sm-12">
							<a href="#" class="tooltipcss opciones agregadetalleproduccion" id='agregadetalleproduccion'
								data_cotizacion_id = '{{$cotizacion->id}}'
								data_cotizacion_estado_id = '{{$cotizacion->estado_id}}'>
								<button class="form-control input-sm">
									<span class="tooltiptext">Agregar producto</span>
									<span class="icon mdi mdi-plus-circle-o"></span>
								</button>
							</a>
						</div>
					</div>
			</div>  


			{{-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
						<div class="form-group">
							<label class="col-sm-12 control-label labelleft negrita" >Cantidad <span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12">
									<input  type="text"
													id="cantidada" name='cantidada' 
													value="@if(isset($detalle)){{old('cantidad' ,$detalle->cantidad)}}@endif" 
													placeholder="Cantidad"
													autocomplete="off" class="form-control input-sm importe importe" data-aw="6"/>

							</div>
						</div>
			</div>

			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
						<div class="form-group">
							<label class="col-sm-12 control-label labelleft negrita" >Precio <span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12">
									<input  type="text"
													id="precio" name='precio' 
													value="@if(isset($detalle)){{old('precio' ,$detalle->precio)}}@endif" 
													placeholder="Precio"
													autocomplete="off" class="form-control input-sm importe importe" data-aw="6"/>

							</div>
						</div>
			</div> --}}


</div>

<div class="row xs-pt-15">
	<div class="col-xs-12">
		<p class="text-right">

			<span>
				<a href="{{ url('gestion-de-cotizacion/'.$idopcion) }}" class="button  btn-information opciones btnatras" 
					{{-- data_cotizacion_id = "{{$cotizacion->id}}" --}}
					data_cotizacion_id = "{{$cotizacion->id}}"
					data_detalle_cotizacion_id = "{{$cotizacion->id}}" 
				>
					<span class="icon mdi mdi-mail-reply btnatrascotizacionproduccion"></span>
				</a>
			</span>

			<button type="submit" class="btn btn-space btn-primary btnagregaranalisis"                  
									>Agregar</button>
		</p>
	</div>
</div>

<div class='col-sm-12 listajaxanalisis'>
	@include('cotizacion.ajax.alistadetalleanalizar')
</div>


@if(isset($ajax))
	<script type="text/javascript">
		$(document).ready(function(){
			App.formElements();
			$('.importe').inputmask({ 'alias': 'numeric', 
			'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
			'digitsOptional': false, 
			'prefix': '', 
			'placeholder': '0'});
		});
	</script>
@endif
