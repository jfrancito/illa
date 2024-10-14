<div class="col-sm-12">
	<div class="form-group">
		<label class=" col-sm-12 control-label labelleft negrita">Cuenta *<span class="obligatorio">(*)</span></label>
		<div class="col-sm-12">
			{!! Form::select( 'cuenta_id', $combo_cuentas, $select_cuenta,
												[
													'class'       => 'select4 form-control control input-xs' ,
													'id'          => 'cuenta_id',
													'required'    => '',
													'data-aw'     => '1'
												]) !!}

			@include('error.erroresvalidate', [ 'id' => $errors->has('cuenta_id')  , 
																					'error' => $errors->first('cuenta_id', ':message') , 
																					'data' => '1'])
			
		</div>
	</div>
</div>

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
			// buscar productos 
			$('.select4').select2();
		});
	</script>
@endif





