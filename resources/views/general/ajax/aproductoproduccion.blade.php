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


@if(isset($ajax))
	<script type="text/javascript">
	$(document).ready(function(){
		$('.select3').select2();
	});
	</script>
@endif