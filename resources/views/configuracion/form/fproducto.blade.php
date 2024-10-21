<fieldset class="scheduler-border">
	<legend class="scheduler-border">Datos del Producto</legend>
		<div class="control-group">
			
			<div class="form-group">
				<label class="col-sm-3 control-label">Codigo <span class="obligatorio">(*)</span> :</label>
				<div class="col-sm-6">

						<input  type="text"
										id="codigo" name='codigo' 
										value="@if(isset($producto)){{old('codigo' ,$producto->codigo)}}@else{{old('codigo' ,$cod_producto)}}@endif"
										value="{{ old('codigo') }}"                         
										placeholder="Codigo"
										readonly = "readonly"
										required = ""
										autocomplete="off" class="form-control input-sm" data-aw="3"/>

						@include('error.erroresvalidate', [ 'id' => $errors->has('codigo')  , 
																								'error' => $errors->first('codigo', ':message') , 
																								'data' => '3'])

				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label">Categoria <span class="obligatorio">(*)</span> :</label>
				<input type="hidden" name="ocategoria_id" id='ocategoria_id' value='{{$select_categoria}}'>
				<div class="col-sm-6">
						{!! Form::select( 'categoria_id', $combo_categoria, $select_categoria,
														[
															'class'       => 'select2 form-control control input-xs' ,
															'id'          => 'categoria_id',
															'required'    => '',                        
															'disabled'    => $disabled,
															'data-aw'     => '1'		
														]) !!}
						@include('error.erroresvalidate', [ 'id' => $errors->has('categoria_id')  , 
																								'error' => $errors->first('categoria_id', ':message') , 
																								'data' => '1'])
				</div>
			</div>

			<div class="subcategoriaproducto">
				@include('configuracion.ajax.asubcategoriaproducto')
			</div>


			<div class="form-group">
				<label class="col-sm-3 control-label">Descripcion <span class="obligatorio">(*)</span> :</label>
				<div class="col-sm-6">

						<input  type="text"
										id="descripcion" name='descripcion' 
										value="@if(isset($producto)){{old('descripcion' ,$producto->descripcion)}}@else{{old('descripcion')}}@endif"
										value="{{ old('descripcion') }}" 
										placeholder="Descripcion"
										required = ""
										autocomplete="off" class="form-control input-sm" data-aw="4"
										@if(isset($producto))
										readonly = "readonly"
										@endif
										/>

						@include('error.erroresvalidate', [ 'id' => $errors->has('descripcion')  , 
																								'error' => $errors->first('descripcion', ':message') , 
																								'data' => '4'])

				</div>
			</div>

			<div class="datosproducto" id='datosproducto'>
				
				<div class="form-group" hidden>
					<label class="col-sm-3 control-label" >Peso <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-6">
							<input  type="text"
											id="peso" name='peso' 
											value="@if(isset($producto)){{old('peso' ,$producto->peso)}}@endif" 
											placeholder="Peso"
											autocomplete="off" class="form-control input-sm importe" data-aw="3"/>

					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-3 control-label">Unidad Medida <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-6">
						{!! Form::select( 'unidad_medida_id', $combo_unidad_medida, $select_unidad_medida,
															[
																'class'       => 'select2 form-control control input-xs' ,
																'id'          => 'unidad_medida_id',
																'disabled'    => $disabled,
																'data-aw'     => '5'
															]) !!}

							@include('error.erroresvalidate', [ 'id' => $errors->has('unidad_medida_id')  , 
																									'error' => $errors->first('unidad_medida_id', ':message') , 
																									'data' => '5'])

					</div>
				</div>
			</div>

			<div class="datosbienesproducidos" id='datosbienesproducidos'>
				
				<div class="form-group">
					<label class="col-sm-3 control-label" >Precio Venta <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-6">
							<input  type="text"
											id="precio_venta" name='precio_venta' 
											value="@if(isset($producto)){{old('precio_venta' ,$producto->precio_venta)}}@endif" 
											placeholder="Precio Venta"
											autocomplete="off" class="form-control input-sm importe" data-aw="3"/>

					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-3 control-label">Tipo Oro <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-6">
						{!! Form::select( 'tipo_oro_id', $combo_tipo_oro, $select_tipo_oro,
															[
																'class'       => 'select2 form-control control input-xs' ,
																'id'          => 'tipo_oro_id',
																'data-aw'     => '5'
															]) !!}

							@include('error.erroresvalidate', [ 'id' => $errors->has('tipo_oro_id')  , 
																									'error' => $errors->first('tipo_oro_id', ':message') , 
																									'data' => '5'])

					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" >Cantidad Oro <span class="obligatorio">(*)</span> :</label>
					<div class="col-sm-6">
							<input  type="text"
											id="cantidad_oro" name='cantidad_oro' 
											value="@if(isset($producto)){{old('cantidad_oro' ,$producto->cantidad_oro)}}@endif" 
											placeholder="Cantidad Oro"
											autocomplete="off" class="form-control input-sm importe" data-aw="6"/>

					</div>
				</div>
			</div>
		
			<div class="col-xs-12 col-sm-12 col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 datosproductogemas" id='datosproductogemas' > 
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Gemas del Producto</legend>		
					<div class="col-sm-12">
						<table id='listaproductogema'  class="table table-striped table-borderless " >
							<thead>
								<tr>
									<th>ID</th>																	
									<th>GEMA</th>								
									<th>CANTIDAD</th>								
									<th>ACCION</th>
								</tr>
							</thead>							
							<tbody>
							@if(isset($listaproductogema))
				                @foreach($listaproductogema as $index => $item)
				                  <tr data_detallecompra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
				                    <td>{{$index + 1 }}</td>
				                    <td>{{$item->gema_nombre}}</td>				                    
				                    <td>{{number_format($item->cantidad, 2)}}</td>
				                    <td class="rigth">
				                      <div class="btn-group btn-hspace">
				                        <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
				                        <ul role="menu" class="dropdown-menu pull-right">
				                          <li>
				                            <a href="{{ url('/quitar-producto-gema/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
				                              Quitar
				                            </a>  
				                          </li>
				                        </ul>
				                      </div>
				                    </td>
				                  </tr>                    
				                @endforeach                
				              @endif
							</tbody>						
						</table>
					</div>
				</fieldset>
			</div>

			@if(isset($producto))
			<div class="form-group">
				<label class="col-sm-3 control-label">Activo</label>
				<div class="col-sm-6">
					<div class="be-radio has-success inline">
						<input type="radio" value='1' name="activo" id="rad6" @if($producto->activo == 1) checked @endif>
						<label for="rad6">Activado</label>
					</div>
					<div class="be-radio has-danger inline">
						<input type="radio" value='0' name="activo" id="rad8" @if($producto->activo == 0) checked @endif >
						<label for="rad8">Desactivado</label>
					</div>
				</div>
			</div> 
			@endif
		</div>
</fieldset>

<div class="row xs-pt-15">
	<div class="col-xs-6">
		<div class="be-checkbox">

		</div>
	</div>
	<div class="col-xs-6">
		<p class="text-right">
			<a href="{{ url('gestion-de-productos/'.$idopcion) }}">
				<button type="button" class="btn btn-space btn-danger btncancelarproducto">Cancelar</button>
			</a>
			<button type="submit" class="btn btn-space btn-primary btnguardarproducto">Guardar</button>
		</p>
	</div>
</div>


