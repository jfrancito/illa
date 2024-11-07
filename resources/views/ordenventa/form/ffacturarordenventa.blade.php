<fieldset class="scheduler-border">
	<legend class="scheduler-border">Datos de la Venta</legend>
		<div class="control-group">

		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label">Comprobante</label>
					<div class="col-sm-12">
						{!! Form::select( 'tipo_comprobante_id', $combo_tipo_comprobante, $select_tipo_comprobante,
															[
																'class'       => 'form-control control input-sm select2' ,
																'id'          => 'tipo_comprobante_id',
																'required'    => '',         
																'data-aw'     => '1'
															]) !!}

						@include('error.erroresvalidate', [ 'id' => $errors->has('tipo_comprobante_id')  , 
																								'error' => $errors->first('tipo_comprobante_id', ':message') , 
																								'data' => '1'])
					</div>
				</div>
			</div>
			
			<div class="ajaxnotapedido">
				<div class="col-sm-1">
					<div class="form-group">
						<label class="control-label">Serie</label>
						<div class="col-sm-12">
							<input  type="text"
											style="width: 70px;" 
											id="serie" name='serie' 											
											value="{{ old('serie') }}"                         
											placeholder="Serie"
											required = ""
											maxlength="4" 
											autocomplete="off" class="form-control input-sm seriekeypress" data-aw="2"/>

							@include('error.erroresvalidate', [ 'id' => $errors->has('serie')  , 
																						'error' => $errors->first('serie', ':message') , 
																						'data' => '2'])
						</div>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<label class="control-label">Numero</label>
						<div class="col-sm-12">
							<input  type="text"
											id="numero" name='numero' 											
											value="{{ old('numero') }}"                         
											placeholder="Numero"
											required = ""
											maxlength="8"                     
											autocomplete="off" class="form-control input-sm numero" data-aw="3"/>

							@include('error.erroresvalidate', [ 'id' => $errors->has('numero')  , 
																						'error' => $errors->first('numero', ':message') , 
																						'data' => '3'])
						</div>
					</div>
				</div>
			</div>

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
		</div>

		<div class="row">

			<div class="col-sm-3" style="margin-top: -5px;">

				<div class="form-group">
						<label class="control-label">Fecha
						</label> 
						<div class="col-sm-12"> 
							<div data-min-view="2" data-date-format="dd-mm-yyyy"  class="input-group date datetimepicker">
												<input size="16" type="text"  placeholder="Fecha"
												id='fecha' name='fecha' 
												value="@if(isset($ordenventa)){{old('fecha' ,date_format(date_create($ordenventa->fecha),'d-m-Y'))}}@else{{old('fecha')}}@endif"
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

			<div class="col-sm-2">
				<div class="form-group">
					<label class="control-label">Lote</label>
					<div class="col-sm-12">

							<input  type="text"
											id="lote" name='lote' 
											value="{{old('lote' ,$lote_venta)}}"
											value="{{ old('lote') }}"                         
											placeholder="Lote"
											readonly = "readonly"
											required = ""
											autocomplete="off" class="form-control input-sm" data-aw="7"/>

							@include('error.erroresvalidate', [ 'id' => $errors->has('lote')  , 
																									'error' => $errors->first('lote', ':message') , 
																									'data' => '7'])

					</div>
				</div>
			</div>

			<div class="col-sm-2">
				<div class="form-group">
					<label class="control-label">Tipo Venta</label>
					<div class="col-sm-12">
						{!! Form::select( 'tipo_venta_id', $combo_tipo_venta, $select_tipo_venta,
															[
																'class'       => 'form-control control input-sm tipo_venta_id select2 select3' ,
																'id'          => 'tipo_venta_id',
																'required'    => '',        
																'data-aw'     => '8'
															]) !!}

						@include('error.erroresvalidate', [ 'id' => $errors->has('tipo_venta_id')  , 
																								'error' => $errors->first('tipo_venta_id', ':message') , 
																								'data' => '8'])
					</div>
				</div>
			</div>






		</div>  


			<div class="col-sm-12">
					<table id='listadetalleventa'  class="table table-striped table-borderless" >
							<thead>
								<tr>
										<th>ID</th>
										<th>PRODUCTO</th>
										<th>CANTIDAD</th>
										<th>P.UNIT.</th>
										<th>IGV</th>
										<th>IGV</th>
										<th>SUBTOTAL</th>										
								</tr>
							</thead>
							<tbody>
							@if(isset($listadetalle))
								@foreach($listadetalle as $index => $item)
									<tr data_detallecompra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
										<td>{{$index + 1 }}</td>
										<td>{{$item->producto_descripcion}}</td>
										<td>{{number_format($item->cantidad, 2)}}</td>
										<td>{{number_format($item->preciounitario, 2)}}</td>	
										<td><p>&#10006;</p></td>										
										<td>{{number_format(0, 2)}}</td>
										<td><b>{{number_format($item->total, 2)}}</b></td>									
									</tr>                    
								@endforeach                
							@endif
							</tbody>
					<tfooter>
							<tr >
								<td colspan="6" class="text-right"><b>TOTAL : </b></td>
								
									<td>
										<b>
										@if(isset($ordenventa))
											{{number_format($ordenventa->venta, 2)}}
										@endif
										</b>
									</td>								
							</tr>     
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
			<button type="submit" class="btn btn-space btn-primary btnguardarventa">Guardar</button>			
		</p>
	</div>
</div>

