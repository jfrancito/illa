<form method="POST" id='formagregaresquemaproducto' action="{{ url('/modificar-orden-ventas-esquema-productos/'.$idopcion.'/'.$item->id.'/'.$idregistro) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
      {{ csrf_field() }}
<div class="row">

	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<fieldset class="scheduler-border">
			<legend class="scheduler-border">PRODUCTO</legend>
				<div class="control-group">
						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Producto</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<input type="hidden" name="registro_id" value="{{ $item->id}}">
										<input type="hidden" name="producto_id" value="{{ $item->producto_id}}">
										<input type="text" name="producto_descripcion" readonly class="input form-control input-sm" value="{{ $item->producto_descripcion}}">
									</div>
							</div>
						</div>


						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Tipo</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<input type="hidden" name="tipooro_id" value="{{ $item->tipooro_id}}">
										<input type="text" name="producto_descripcion" readonly class="input form-control input-sm" value="{{ $item->tipooro_descripcion}}">
									</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Cantidad</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type="text" name="producto_descripcion" readonly class="input form-control input-sm" value="{{ $item->cantidad}}">
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Precio Venta</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type="text" name="producto_descripcion" readonly class="input form-control input-sm" value="{{ $item->precio_venta}}">
									</div>
							</div>
						</div>


						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Gramos</label>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input type='number' 
										class="input form-control input-sm text" 
										name='gramos' id='gramos' 
										step="0.01"
										min="0.0"
										max="999999" 
										required 
										value="@if(isset($item)){{ $item->gramos }}@endif" 
										placeholder="0.0">
										@include('error.erroresvalidate', [ 'id' => $errors->has('gramos'),'error' => $errors->first('gramos', ':message') ,'data' => '7'])
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">P. Gramos</label>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input type='number' 
										class="input form-control input-sm text" 
										name='precio_x_gramo' id='precio_x_gramo' 
										step="0.10"
										min="0.0"
										max="999999" 
										required 
										value="@if(isset($item)){{ $item->precio_x_gramo }}@endif" 
										placeholder="0.0">
										@include('error.erroresvalidate', [ 'id' => $errors->has('precio_x_gramo'),'error' => $errors->first('precio_x_gramo', ':message') ,'data' => '8'])
									</div>
							</div>
						</div>

				</div>
		</fieldset>

	</div>

	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

				<fieldset class="scheduler-border">

					<legend class="scheduler-border">GEMAS</legend>
					<div class="col-sm-12">
						<table id='listadetalleventa'  class="table table-striped table-borderless " >
							<thead>
								<tr>
									<th>ORIGEN</th>
									<th>PRODUCTO</th>
									<th>CANT</th>
									<th>PRECIO</th>
									<th>COSTO</th>
									@if($swresumen == false)
										<th>ACCION</th>
									@endif									
								</tr>
							</thead>
							
							<tbody id='listagemas'>
								@foreach($item->DetalleEsquema as $index =>$gema)
									<tr  gema_esquema_id = "{{$gema->id}}" 
										 esquema_id = "{{$item->id}}"
        								class='dobleclickpc seleccionar trfilagema'>
										<td class='tdorigen'>{{$gema->origendescripcion}}</td>
										<td hidden class='tdtipogema'>{{$gema->tipo_id}}</td>
										<td class='tdgema'>{{$gema->tipodescripcion}}</td>
										<td class='tdcantidad'>{{$gema->cantidad}} </td>
										<td class='tdcosto'>{{$gema->costo_unitario}}</td>
										<td class='tdcosto'>{{$gema->costo_total}}</td>
										@if($swresumen == false)
											<td>
												<button type='button' class='eliminargema btn btn-default btn-sm' gema_esquema_id = "{{$gema->id}}" 
											 		esquema_id = "{{$item->id}}" aria-label='Left Align'>
													<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>			
												</button>
											</td>
										@endif
									</tr>
								@endforeach
							</tbody>
							<tfooter>
								<th colspan="4" class="tdderecha">TOTAL COSTO GEMAS: </th>
								<th id='tdtotal_costo_gemas' name='tdtotal_costo_gemas'>{{$item->costo_total_gemas}}</th>
								@if($swresumen == false)
									<th>
										<button type='button' class='agregargema btn btn-success btn-sm'
												esquema_id = "{{$item->id}}" aria-label='Left Align'>
											<span class='glyphicon glyphicon-plus' aria-hidden='true'></span>			
										</button>
									</th>
								@endif
							</tfooter>
						</table>
					</div>

					<div class="row">
						<div class="form-group">
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">TOTAL ORO</label>
								</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
									<input type='number' 
									class="input form-control input-sm text" 
									name='costo_total_oro' id='costo_total_oro' 
									step="0.01"
									min="0.0"
									max="999999" 
									required 
									value="{{ $item->costo_total_oro}}" 
									placeholder="0.0" readonly>
									@include('error.erroresvalidate', [ 'id' => $errors->has('engaste'),'error' => $errors->first('engaste', ':message') ,'data' => '7'])
								</div>
						
						</div>
					</div>

				
					<div class="row">
						<div class="form-group">
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">TOTAL ENGASTE</label>
								</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
									<input type='number' 
									class="input form-control input-sm text" 
									name='engaste' id='engaste' 
									step="0.01"
									min="0.0"
									max="999999" 
									required 
									value="{{ $item->precio_total_engaste}}" 
									placeholder="0.0">
									@include('error.erroresvalidate', [ 'id' => $errors->has('engaste'),'error' => $errors->first('engaste', ':message') ,'data' => '7'])
								</div>
						
						</div>
					</div>


					<div class="row">
						<div class="form-group">
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">COSTO PRODUCCION</label>
								</div>
								<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
									<input type="text" name="costo_unitario" readonly class="input form-control input-sm" value="{{ $item->costo_unitario}}">
								</div>
						</div>
					</div>

				</fieldset>

	</div>
</div>
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
				<button id='btnguardarregistroesquema' type="submit" class="btn btn-space btn-primary btnguardarregistroesquema" @if($swresumen == true) disabled @endif>Guardar</button>
			@else
				<a href="{{ url('/gestion-orden-venta/'.$idopcion) }}">
					<button type="button" class="btn btn-space btn-danger btnatras" >Cancelar</button>
				</a>
				<button id='btnguardarregistroesquema' type="submit" class="btn btn-space btn-primary btnguardarregistroesquema" @if($swresumen == true) disabled @endif>Guardar</button>
			@endif      
		</p>
	</div>
</div>

</form>




