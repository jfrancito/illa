<form method="POST" action="{{ url('/emitir-ventas/'.$idopcion) }}">
      {{ csrf_field() }}
	<input type="hidden" id='idventa' name='idventa' value='{{$venta->id}}'>	
	<input type="hidden" id='idtipoventa' name='idtipoventa' value='{{$venta->tipo_venta_id}}'>	
	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			EMITIR VENTAS
		</h3>
	</div>
	<div class="modal-body">


		<div  class="row regla-modal" @if($venta->tipo_venta_id == '1CIX00000035') hidden @endif>
			<span class="panel-heading"><center>¿Esta seguro de querer emitir la Siguiente Venta?</center></span>
			<span class="panel-subtitle"><center>Emitiendo Compra : <b>{{$venta->serie}} - {{$venta->numero}}</b></center></span>
		</div>

		<div  class="row regla-modal" @if($venta->tipo_venta_id == '1CIX00000036') hidden @endif>
			<div class="col-sm-12">
				<div class="form-group">
				  <label class="col-sm-12 control-label labelleft negrita" >Almacen <span class="obligatorio">(*)</span> :</label>
				  <div class="col-sm-12">

				      {!! Form::select( 'almacen_id', $combo_almacen, $select_almacen,
		                              [
		                                'class'       => 'select3 form-control control input-xs' ,
		                                'id'          => 'almacen_id',        
		                                'data-aw'     => '1'
		                              ]) !!}

		            @include('error.erroresvalidate', [ 'id' => $errors->has('almacen_id')  , 
		                                                'error' => $errors->first('almacen_id', ':message') , 
		                                                'data' => '1'])

				  </div>
				</div>
			</div>
		</div>

		<div  class="row regla-modal" hidden>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
				  <label class="col-sm-12 control-label labelleft negrita" >Motivo <span class="obligatorio">(*)</span> :</label>
				  <div class="col-sm-12">

				      {!! Form::select( 'motivo_id', $combo_motivo, $select_motivo,
		                              [
		                                'class'       => 'select3 form-control control input-xs' ,
		                                'id'          => 'motivo_id',        
		                                'data-aw'     => '2'
		                              ]) !!}

		            @include('error.erroresvalidate', [ 'id' => $errors->has('motivo_id')  , 
		                                                'error' => $errors->first('motivo_id', ':message') , 
		                                                'data' => '2'])

				  </div>
				</div>
			</div>			
		</div>
		
	</div>

	<div class="modal-footer">
	  <button type="submit" data-dismiss="modal" class="btn btn-success btn-emitir-venta">Emitir</button>
	</div>
</form>

@if(isset($ajax))
  <script type="text/javascript">
  $(document).ready(function(){


      $('.importe').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

      $('.select3').select2();

  });
  </script>
@endif