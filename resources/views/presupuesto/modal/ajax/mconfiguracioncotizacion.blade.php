{{-- <style type="text/css">
	label {
		display: inline-block;
		width: 150px; /* Ajusta el ancho según tus necesidades */
		text-align: right; /* Alinea el texto a la derecha */
		padding-right: 10px; /* Espacio entre la etiqueta y el campo de entrada */
	}

	input[type="checkbox"] {
		display: inline-block;
		vertical-align: middle; /* Alinea el checkbox verticalmente con el texto de la etiqueta */
	}
</style> --}}
@php
	$disable ='';
	$vardisable = false;
	if(isset($swmodificar)){
		$disable = 'disabled';
		$vardisable = true;
	}
@endphp
<form method="POST" action="{{ url('/cotizar-cotizacion/'.$idopcion.'/'.Hashids::encode(substr($cotizacion->id, -8))) }}" name='frmcotizarrequerimiento' id='frmcotizarrequerimiento'>
	{{ csrf_field() }}
	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			{{$cotizacion->cliente_nombre}} <span>({{$cotizacion->lote}})</span>
		</h3>
		<input type="hidden" name="cotizaciondetalle_id" id="cotizaciondetalle_id" value='{{$cotizaciondetalle_id}}'>
		<input type="hidden" name="idpadre" id="idpadre" value='{{$idpadre}}'>
		<input type="hidden" name="iddatocategoria" id="iddatocategoria" value='{{$iddatocategoria}}'>
	</div>
	<div class="modal-body">
		<div  class="row regla-modal">
			<div class="row">

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-12 control-label labelleft negrita" >Tipo<span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12 abajocaja">
								{!! Form::select( 'tipocategoria', $combo_tipocategoria, '',
													[
														'class'       => 'select2 form-control control input-xs' ,
														'id'          => 'tipocategoria',
														'required'    => '',
														'data-aw'     => '1'
													]) !!}
									@include(	'error.erroresvalidate', 
												[ 
													'id' => $errors->has('tipocategoria')  , 
													'error' => $errors->first('tipocategoria', ':message') , 
													'data' => '1'
												]
											)
							</div>
						</div>
					</div>

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="form-group">
							<label class="col-sm-12 control-label labelleft negrita" >Codigo<span class="obligatorio">(*)</span> :</label>
							<div class="col-sm-12 abajocaja">
								<input type="text" name="codigo" id='codigo' class="form-control control input-sm codigo SOLONUMEROS" required value="@if(isset($codigo)) {{ $codigo }} @else {{ '00' }} @endif" maxlength="20"> 
									@include(	'error.erroresvalidate', 
												[ 
													'id' => $errors->has('codigo')  , 
													'error' => $errors->first('codigo', ':message') , 
													'data' => '2'
												]
											)
							</div>
						</div>
					</div>
					

				
			</div>
			<div class="row">

				<div class="form-group">
					<div class="col-xs-12 col-sm-6 col-md-12 col-lg-12">
						<div class="form-group">
								<label class="col-sm-12 control-label text-left"><b>Descripcion <span class="obligatorio">(*)</span> </b>:</label>
								<div class="col-sm-12">
									<input list="servicios" name="servicio" id="servicio" class="form-control control input-sm">
									<datalist id="servicios">
										@foreach($ldescripcion as $index => $descripcion)
									  		<option value="{{ $descripcion }}">
									  	@endforeach
									</datalist>
								</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group">


						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label class="col-sm-12 control-label labelleft negrita" >Unidad Medida <span class="obligatorio">(*)</span> :</label>
								<div class="col-sm-12 abajocaja" >
										{!! Form::select( 'unidadmedida_id', $combo_unidad_medida, $select_unidad_medida,
																			[
																				'class'       => 'select2 form-control control input-xs' ,
																				'id'          => 'unidadmedida_id',
																				'required'    => '',
																				'data-aw'     => '1',
																				'disabled' 	  => 'true',
																			]) !!}
											@include('error.erroresvalidate', [ 'id' => $errors->has('unidadmedida_id')  , 
																													'error' => $errors->first('unidadmedida_id', ':message') , 
																													'data' => '2'])
								</div>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label class="col-sm-12 control-label labelleft negrita" >Cantidad <span class="obligatorio">(*)</span> :</label>
								<div class="col-sm-12">

										<input  type="text"
														id="cantidad" name='cantidad' 
														value="@if(isset($detalle)){{old('cantidad' ,$detalle->cantidad)}}@endif" 
														placeholder="Cantidad"
														disabled 
														autocomplete="off" class="form-control input-sm importe" data-aw="1"/>

								</div>
							</div>
						</div>


				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
				</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Guardar</button>
	</div>
</form>
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
