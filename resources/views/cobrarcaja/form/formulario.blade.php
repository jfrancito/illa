<fieldset class="scheduler-border">
	<legend class="scheduler-border">Datos de la Venta</legend>
		<div class="control-group">

		<div class="row" style="margin-top: -20px;">
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<label class="control-label">Comprobante</label>
					<div class="col-sm-12">
						<input type="text" id='tipo_comprobante_id' name="tipo_comprobante_id" class="tipo_comprobante_id form-control input-sm" value="@if(isset($registro)) {{ $registro->tipo_comprobante_nombre }} @endif" disabled>
						@include('error.erroresvalidate', [ 'id' => $errors->has('tipo_comprobante_id')  , 
																								'error' => $errors->first('tipo_comprobante_id', ':message') , 
																								'data' => '1'])
					</div>
				</div>
			</div>
			


			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<label class="control-label">Cliente</label>
					<div class="col-sm-12">
						<input type="text" 
								id='cliente_nombre' 
								name="cliente_nombre" 
								class="cliente_nombre form-control input-sm" 
								value="@if(isset($registro)) {{ $registro->cliente_nombre }} @endif" 
								disabled
						>
						@include('error.erroresvalidate', [ 'id' => $errors->has('cliente_id')  , 
																								'error' => $errors->first('cliente_id', ':message') , 
																								'data' => '4'])
					</div>
				</div>
			</div>


			{{-- <div class="col-sm-3" style="margin-top: -5px;"> --}}
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-2" >


				<div class="form-group">
					<label class="col-xs-3 col-sm-3 col-md-6 col-lg-3 control-label">Fecha</label>
					<div class="col-xs-9 col-sm-9 col-md-12 col-lg-12">
						<input type="text" 
								id='fecha' 
								name="fecha" 
								class="fecha form-control input-sm" 
								value="@if(isset($registro)) {{ old('fecha',date_format(date_create($registro->fecha),'d-m-Y')) }} @endif" 
								disabled
						>
					</div>
				</div>

			</div>

			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-2" >

				<div class="form-group">
					<label class="col-xs-3 col-sm-3 col-md-6 col-lg-3 control-label">Total</label>
					<div class="col-xs-9 col-sm-9 col-md-12 col-lg-12">
						<input type="text" 
								id='total' 
								name="total" 
								class="total form-control input-sm" 
								value="@if(isset($registro)) {{ old('total',number_format($registro->total,2,'.',',')) }} @endif" 
								disabled
						>
					</div>
				</div>

			</div>





		</div>  


			<div class="col-sm-12">
					<table id='listadetalleventa'  class="table table-striped table-borderless" >
						<thead>
							<tr>
									<th>ID</th>
									<th>ENTIDAD</th>
									<th>Nro CTA</th>
									<th>TIPO</th>
									<th>(S/.) IMPORTE.</th>
									<th colspan="2">ACCION</th> 
							</tr>
						</thead>
						<tbody>
							@if(isset($listadetalleregistro))
								@foreach($listadetalleregistro as $index => $item)
									<tr data_detallecompra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
										<td>{{$index + 1 }}</td>
										<td>{{$item->entidad_nombre}}</td>
										<td>{{$item->nrocta}}</td>
										<td>{{$item->tipo_pago_nombre}}</td>
										<td>{{number_format($item->importe, 2)}}</td>
										<td class="text-right" colspan="2">
											<div class="btn-group btn-hspace">
												<button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle" @if($registro->estado_id != '1CIX00000003') disabled @endif>Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
												<ul role="menu" class="dropdown-menu pull-right">
													<li>
														<a href="{{ url('/quitar-detalle-cobro-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
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
							<tr >
								<td colspan="4" class="text-right"><b>PAGADO : </b></td>
								
									<td>
										<b>
										@if(isset($registro))
											{{number_format($registro->acta, 2)}}
										@endif
										</b>
									</td>
								<td  class="text-right"><b>SALDO : </b></td>
								<td>
									<b>
										@if(isset($registro))
											{{number_format($registro->saldo, 2)}}
										@else
											0.0
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
			<a href="{{ url('gestion-cobro-venta/'.$idopcion) }}">
				<button type="button" class="btn btn-space btn-danger btncancelar" >Cancelar</button>
			</a>

			@if(isset($registro))
				<button type="submit" class="btn btn-space btn-primary btnguardarventa" @if($registro->estado_id != '1CIX00000003') disabled @endif>Guardar</button>
			@else
				<button type="submit" class="btn btn-space btn-primary btnguardarventa">Guardar</button>
			@endif      
		</p>
	</div>
</div>


