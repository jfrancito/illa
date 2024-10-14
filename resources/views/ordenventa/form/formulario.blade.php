<fieldset class="scheduler-border">
	<legend class="scheduler-border">Datos de la Orden de Venta</legend>
		<div class="control-group">

			@if($swmodificar==true)
				<div class="row">
					

					<div class="col-sm-5">


						<div class="form-group">
							<label class="control-label">Cliente</label>
							<div class="col-sm-11">
								{!! Form::select( 'cliente_id', $combo_cliente, $select_cliente,
																	[
																		'class'       => 'form-control control input-sm select2' ,
																		'id'          => 'cliente_id',
																		'required'    => '',   
																		'data-aw'     => '4'
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('cliente_id')  , 
																										'error' => $errors->first('cliente_id', ':message') , 
																										'data' => '4'])
							</div>

							<span class="input-group-btn">
								<a href="{{ url('/agregar-clientes/oj') }}" target="_blank">
									<button type="button" class="btn btn-primary" style="margin-left: -15px; height: 36px">
										<i class="icon mdi mdi-collection-plus"></i>
									</button>
								</a>
							</span>
						</div>
					</div>

					<div class="col-sm-3" style="margin-top: -5px;">

						<div class="form-group">
								<label class="control-label">Fecha
								</label> 
								<div class="col-sm-12"> 
									<div data-min-view="2" data-date-format="dd-mm-yyyy"  class="input-group date datetimepicker">
														<input size="16" type="text"  placeholder="Fecha"
														id='fecha' name='fecha' 
														value="@if(isset($registro)){{old('fecha' ,date_format(date_create($registro->fecha),'d-m-Y'))}}@else{{old('fecha')}}@endif"
														required = "" 
														class="form-control input-sm" data-aw="5">
														<span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>             
									</div>
								</div>
						</div>

					</div>

					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Moneda</label>
							<div class="col-sm-12">
								{!! Form::select( 'moneda_id', $combo_moneda, $select_moneda,
																	[
																		'class'       => 'form-control control input-sm select2 select3' ,
																		'id'          => 'moneda_id',
																		'required'    => '',        
																		'data-aw'     => '6'
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('moneda_id')  , 
																										'error' => $errors->first('moneda_id', ':message') , 
																										'data' => '6'])
							</div>
						</div>
					</div>
				</div>
			@else
				<div class="row">
					

					<div class="col-sm-5">


						<div class="form-group">
							<label class="control-label">Cliente</label>
							<div class="col-sm-11">
								{!! Form::select( 'cliente_id', $combo_cliente, $select_cliente,
																	[
																		'class'       => 'form-control control input-sm select2' ,
																		'id'          => 'cliente_id',
																		'required'    => '',   
																		'disabled'    => 'true',         
																		'data-aw'     => '4'
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('cliente_id')  , 
																										'error' => $errors->first('cliente_id', ':message') , 
																										'data' => '4'])
							</div>

							<span class="input-group-btn">
								<a href="{{ url('/agregar-clientes/oj') }}" target="_blank">
									<button type="button" class="btn btn-primary" style="margin-left: -15px; height: 36px">
										<i class="icon mdi mdi-collection-plus"></i>
									</button>
								</a>
							</span>
						</div>
					</div>

					<div class="col-sm-3" style="margin-top: -5px;">

						<div class="form-group">
								<label class="control-label">Fecha
								</label> 
								<div class="col-sm-12"> 
									<div data-min-view="2" data-date-format="dd-mm-yyyy"  class="input-group date datetimepicker">
														<input size="16" type="text"  placeholder="Fecha"
														id='fecha' name='fecha' 
														value="@if(isset($registro)){{old('fecha' ,date_format(date_create($registro->fecha),'d-m-Y'))}}@else{{old('fecha')}}@endif"
														required = "" disabled
														class="form-control input-sm" data-aw="5">
														<span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>             
									</div>
								</div>
						</div>

					</div>

					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Moneda</label>
							<div class="col-sm-12">
								{!! Form::select( 'moneda_id', $combo_moneda, $select_moneda,
																	[
																		'class'       => 'form-control control input-sm select2 select3' ,
																		'id'          => 'moneda_id',
																		'required'    => '',        
																		'disabled'	  =>'',
																		'data-aw'     => '6'
																	]) !!}

								@include('error.erroresvalidate', [ 'id' => $errors->has('moneda_id')  , 
																										'error' => $errors->first('moneda_id', ':message') , 
																										'data' => '6'])
							</div>
						</div>
					</div>
				</div>
			@endif



			<div class="col-sm-12">
					<table id='listadetalleventa'  class="table table-striped table-borderless" >
							<thead>
								<tr>
										<th>ID</th>
										<th>PRODUCTO</th>
										<th>TIPO ORO</th>
										<th>CANTIDAD</th>
										
										<th>ACCION</th> 
								</tr>
							</thead>
							<tbody>
							@if(isset($listadetalle))
								@foreach($listadetalle as $index => $item)
									<tr data_detallecompra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
										<td>{{$index + 1 }}</td>
										<td>{{$item->producto_descripcion}}</td>
										<td>{{$item->tipooro_descripcion}}</td>
										<td>{{number_format($item->cantidad, 2)}}</td>
										
										<td class="rigth">
											<div class="btn-group btn-hspace">
												<button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle" @if($registro->estado_id != '1CIX00000003') disabled @endif>Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
												<ul role="menu" class="dropdown-menu pull-right">
													<li>
														<a href="{{ url('/quitar-detalle-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
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
					<tfooter>
							
					</tfooter>
					</table>          
			</div>


		</div>
</fieldset>



<div class="row xs-pt-15">
	<div class="col-xs-6">
			<div class="be-checkbox">

			</div>
	</div>
	<div class="col-xs-6">
		<p class="text-right">
			@if(isset($registro))
				<a href="{{ url('/gestion-orden-venta/'.$idopcion) }}">
					<button type="button" class="btn btn-space btn-danger btnatras" >Cancelar</button>
				</a>
				<button type="submit" class="btn btn-space btn-primary btnguardarventa" @if($registro->estado_id != '1CIX00000003') disabled @endif>Guardar</button>
			@else
				<a href="{{ url('/gestion-orden-venta/'.$idopcion) }}">
					<button type="button" class="btn btn-space btn-danger btnatras" >Cancelar</button>
				</a>
				<button type="submit" class="btn btn-space btn-primary btnguardarventa">Guardar</button>
			@endif      
		</p>
	</div>
</div>


