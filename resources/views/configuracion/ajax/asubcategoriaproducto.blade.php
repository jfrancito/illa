<div class="form-group">
	<label class="col-sm-3 control-label">Sub Categoria <span class="obligatorio">(*)</span> :</label>
	<div class="col-sm-6">
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

@if(isset($ajax))
	<script type="text/javascript">
	$(document).ready(function(){
		$('.select3').select2();
	});
	</script>
@endif