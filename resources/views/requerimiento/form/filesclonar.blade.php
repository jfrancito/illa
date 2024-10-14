

<div class="form-group">
	<label class="col-sm-3 control-label"><h4>Lote <span class="obligatorio">(*)</span> </h4></label>
	<div class="col-sm-6">
		{!! Form::select( 'lote_id', $combolote, '',
			[
			'class'       => 'select2 form-control control input-xs' ,
			'id'          => 'lote_id',
			'required'    => '',
			'data-aw'     => '20'
			]) !!}
	  	@include('error.erroresvalidate', [ 'id' => $errors->has('lote_id')  , 
			'error' => $errors->first('lote_id', ':message') , 
			'data' => '20'])
	</div>
	<div class="col-sm-3">
		<input type="submit" value="Clonar" title="Clonar" name="btnclonar" id="btnclonar" />
	</div>
</div>


