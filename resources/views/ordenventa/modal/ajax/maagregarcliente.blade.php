<form method="POST" action="{{ url('/ajax-agregar-clientes/'.$idopcion) }}">
			{{ csrf_field() }}

	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			Crear un nuevo cliente
		</h3>
	</div>
	<div class="modal-body">


		<div  class="row regla-modal">
			<div class="col-sm-12">
						<div class="form-group">
					    <label class="col-sm-12 control-label labelleft negrita">Tipo documento <span class="obligatorio">(*)</span> :</label>
					    <div class="col-sm-12">
					      <input type="hidden" name="sindocumento" id='sindocumento' value="@if(isset($cliente)){{ $cliente->sindocumento}}@endif">
					      {!! Form::select( 'tipo_documento_id', $combo_tipo_documento, $select_tipo_documento,
					                        [
					                          'class'       => 'select5 form-control control input-xs' ,
					                          'id'          => 'tipo_documento_id',
					                          'required'    => '',
					                          'disabled'    => $disabletipodocumento,
					                          'data-aw'     => '1'
					                        ]) !!}

					        @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_documento_id')  , 
					                                            'error' => $errors->first('tipo_documento_id', ':message') , 
					                                            'data' => '1'])

					    </div>
					  </div>
					</div>   
		</div>

		<div class="separadordiv"></div>

		<div  class="row regla-modal">
			<div class="col-sm-12">
				<div class="form-group">
			    <label class="col-sm-12 control-label labelleft negrita">Nombre <span class="obligatorio">(*)</span> :</label>
			    <div class="col-sm-12">

			        <input  type="text"
			                id="nombre_razonsocial" name='nombre_razonsocial' 
			                value="@if(isset($cliente)){{old('nombre_razonsocial' ,$cliente->nombre_razonsocial)}}@else{{old('nombre_razonsocial')}}@endif"
			                value="{{ old('nombre_razonsocial') }}" 
			                placeholder="Nombre"
			                required = ""
			                autocomplete="off" class="form-control input-sm" data-aw="3"/>

			        @include('error.erroresvalidate', [ 'id' => $errors->has('nombre_razonsocial')  , 
			                                            'error' => $errors->first('nombre_razonsocial', ':message') , 
			                                            'data' => '3'])

			    </div>
			  </div>
			</div>			
		</div>

		<div class="separadordiv"></div>

		<div  class="row regla-modal">
			<div class="col-sm-12">
				<div class="form-group">
			    <label class="col-sm-12 control-label labelleft negrita">Pais <span class="obligatorio">(*)</span> :</label>
			    <div class="col-sm-12">
			      {!! Form::select( 'pais_id', $combo_paises, $select_pais,
			                        [
			                          'class'       => 'select5 form-control control input-xs' ,
			                          'id'          => 'pais_id',
			                          'required'    => '',
			                          'disabled'    => false,
			                          'data-aw'     => '4'
			                        ]) !!}

			        @include('error.erroresvalidate', [ 'id' => $errors->has('pais_id')  , 
			                                            'error' => $errors->first('pais_id', ':message') , 
			                                            'data' => '4'])

			    </div>
			  </div>				
			</div>						
		</div>

		<div class="separadordiv"></div>

		<div  class="row regla-modal">
			<div class="col-sm-12">
				<div class="form-group">
			    <label class="col-sm-12 control-label labelleft negrita">Direccion :</label>
			    <div class="col-sm-12">

			        <input  type="text"
			                id="direccion" name='direccion' 
			                value="@if(isset($cliente)){{old('direccion' ,$cliente->direccion)}}@else{{old('direccion')}}@endif"
			                value="{{ old('direccion') }}" 
			                placeholder="Direccion"                
			                autocomplete="off" class="form-control input-sm" data-aw="4"/>

			        @include('error.erroresvalidate', [ 'id' => $errors->has('direccion')  , 
			                                            'error' => $errors->first('direccion', ':message') , 
			                                            'data' => '7'])

			    </div>
			  </div>				
			</div>						
		</div>

	</div>

	<div class="modal-footer">
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-cliente">Guardar</button>
	</div>
</form>
@if(isset($ajax))
	<script type="text/javascript">
		$(document).ready(function(){			
			$('.select5').select2();
		});
	</script>
@endif





