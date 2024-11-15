<div class="form-group">
	<label class="col-sm-3 control-label">Nombre Producción <span class="obligatorio">(*)</span> :</label>
	<div class="col-sm-6">
			<input type="text" name="nombre" id='nombre' class="form-control control input-sm">

			@include('error.erroresvalidate', [ 'id' => $errors->has('nombre')  , 
																					'error' => $errors->first('nombre', ':message') , 
																					'data' => '1'])

	</div>
</div>

<div class="form-group">
	<label class="col-sm-3 control-label">Cantidad a producir <span class="obligatorio">(*)</span> :</label>
	<div class="col-sm-6">
			<input type="text" name="cantidad" id='cantidad' class="form-control control input-sm unidad" value="1" readonly>

			@include('error.erroresvalidate', [ 'id' => $errors->has('cantidad')  , 
																					'error' => $errors->first('nombre', ':message') , 
																					'data' => '1'])

	</div>
</div>


<div class="form-group">
	<label class="col-sm-3 control-label">Moneda <span class="obligatorio">(*)</span> :</label>
	<div class="col-sm-6">
		{!! Form::select( 'moneda_id', $combo_moneda, $select_moneda,
											[
												'class'       => 'select2 form-control control input-xs' ,
												'id'          => 'moneda_id',
												'required'    => '',
												'data-aw'     => '1'
											]) !!}

			@include('error.erroresvalidate', [ 'id' => $errors->has('moneda_id')  , 
																					'error' => $errors->first('moneda_id', ':message') , 
																					'data' => '1'])

	</div>
</div>

<div class="form-group">
	<label class="col-sm-3 control-label">Descripcion <span class="obligatorio">(*)</span> :</label>
	<div class="col-sm-6">

				<textarea 
				name="descripcion"
				id = "descripcion"
				class="form-control input-sm validarmayusculas"
				rows="5" 
				cols="50"
				required = ""       
				data-aw="2">@if(isset($registro)){{old('descripcion' ,$registro->descripcion)}}@else{{old('descripcion')}}@endif</textarea>

				@include('error.erroresvalidate', [ 'id' => $errors->has('descripcion')  , 
																						'error' => $errors->first('descripcion', ':message') , 
																						'data' => '2'])

	</div>
</div>

<div class="row xs-pt-15">
	<div class="col-xs-6">
			<div class="be-checkbox">

			</div>
	</div>
	<div class="col-xs-6">
		<p class="text-right">
			<a href="{{ url('gestion-de-cotizacion'.'/'.$idopcion) }}"><button type="button" class="btn btn-space btn-danger btncancelar">Cancelar</button></a>
			<button type="submit" class="btn btn-space btn-primary btnguardarcliente">Guardar</button>
		</p>
	</div>
</div>