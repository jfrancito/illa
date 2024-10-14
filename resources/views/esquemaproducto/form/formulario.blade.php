<fieldset class="scheduler-border">
	<legend class="scheduler-border">Datos del Esquema Producto</legend>
		<div class="control-group">

			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

				<div class="row">
					<div class="form-group">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
									<label class="control-label derecha">Producto</label>
							</div>
							<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
								<input type="hidden" name="producto_id" value="{{ $producto->id}}">
								<input type="text" name="producto_descripcion" readonly class="input form-control input-sm" value="{{ $producto->descripcion}}">
						
							</div>
					</div>
				</div>

				<div class="row">
					<div class="form-group">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
									<label class="control-label derecha">Tipo</label>
							</div>
							<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
								{!! Form::select( 'tipooro_id', $combo_tipo_oro, $select_tipo_oro,
										[
											'class'       => 'form-control control input-sm select2' ,
											'id'          => 'tipooro_id',
											'required'    => '',   
											'data-aw'     => '4'
										]) !!}
								@include('error.erroresvalidate', [ 'id' => $errors->has('tipooro_id'),'error' => $errors->first('tipooro_id', ':message') ,'data' => '6'])
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
								value="@if(isset($registro)){{ $registro->gramos }}@endif" 
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
								value="@if(isset($registro)){{ $registro->precio_x_gramo }}@endif" 
								placeholder="0.0">
								@include('error.erroresvalidate', [ 'id' => $errors->has('precio_x_gramo'),'error' => $errors->first('precio_x_gramo', ':message') ,'data' => '8'])
							</div>
					</div>
				</div>

				
				<fieldset class="scheduler-border">

					<legend class="scheduler-border">Engaste</legend>
					<div class="row">
						<div class="form-group">
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">Precio Eng</label>
								</div>
								<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
									<input type='number' 
									class="input form-control input-sm text" 
									name='precio_unitario_engaste' id='precio_unitario_engaste' 
									step="0.01"
									min="0.00"
									max="999999.99"
									value="@if(isset($registro)){{ $registro->precio_unitario_engaste }}@endif" 
									placeholder="0.0">
						
								</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
								<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">Cantidad</label>
								</div>
								<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
									<input type='number' 
									class="input form-control input-sm text sololectura" 
									name='cantidad_engaste' id='cantidad_engaste' 
									step="1"
									readonly 
									min="0.0"
									max="999999"
									value="@if(isset($registro)){{ $registro->cantidad_engaste }}@endif" 
									placeholder="0.0">
								</div>
						
								<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">SubTotal</label>
								</div>
								<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
									<input type='number' 
									class="input form-control input-sm text sololectura" 
									{{-- readonly  --}}
									name='precio_total_engaste' id='precio_total_engaste' 
									step="1"
									min="0.0"
									readonly 
									max="999999"

									value="@if(isset($registro)){{ $registro->precio_total_engaste }}@endif" 
									placeholder="0.0">
						
								</div>
						</div>
					</div>

				</fieldset>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Gemas</legend>
					<input type="hidden" name="xmllistagemas" id="xmllistagemas">

					<div class="row">
						<div class="form-group">
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">Tipo</label>
								</div>
								<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
									{!! Form::select( 'tipogema_id', $combo_gemas, $select_gemas,
											[
												'class'       => 'form-control control input-sm select2' ,
												'id'          => 'tipogema_id',
												'data-aw'     => '4'
											]) !!}
								</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<label class="control-label derecha">Origen</label>
								</div>
								<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
									{!! Form::select( 'origen_id', $combo_origen_gema, $select_origen_gema,
											[
												'class'       => 'form-control control input-sm select2' ,
												'id'          => 'origen_id',
												'data-aw'     => '4'
											]) !!}
								</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
								<label class="control-label derecha">Cantidad</label>
							</div>
							<div class="col-xs-5 col-sm-4 col-md-4 col-lg-4">
								<input type='number' 
								class="input form-control input-sm text" 
								name='cantidad_gemas' id='cantidad_gemas' 
								step="0.01"
								min="0.0"
								max="999999" 
								required 
								value="0.0" 
								placeholder="0.0">
							</div>
							<div class="col-xs-3 col-sm-offset-2  col-sm-3 col-md-offset-2  col-md-3 col-lg-offset-2  col-lg-3">
								<button class="col-xs-12 btnagregargema btn btn-success btn-add" type="button">
									<span class="glyphicon glyphicon-plus"></span>
								</button>
							</div>
						</div>
					</div>
			

					<div class="col-sm-12">
						<table id='listadetalleventa'  class="table table-striped table-borderless " >
							<thead>
								<tr>
									<th>ORIGEN</th>
									<th>PRODUCTO</th>
									<th>CANT</th>
									<th>PRECIO</th>
									<th>COSTO</th>
									<th>ACCION</th>
								</tr>
							</thead>
							
							<tbody id='listagemas'>
							</tbody>
							<tfooter>
								<th colspan="4" class="tdderecha">TOTAL COSTO GEMAS: </th>
								<th id='tdtotal_costo_gemas' name='tdtotal_costo_gemas'>0.0</th>
								<th></th>
							</tfooter>
						</table>
					</div>
				</fieldset>
			</div>
			
			<div class="col-xs-12">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">Costo Total</legend>
					<input type="hidden" name="htotal_costo_gemas" id="htotal_costo_gemas" value="0">

					<div class="row">
						<div class="form-group">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<label class="control-label derecha">COSTO</label>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<input  type="text"
											id="costo_unitario" name='costo_unitario' title="MONTO DE IGV" 
											value="0.0" 
											readonly 
											placeholder="MONTO IGV"
											autocomplete="off" class="form-control input-sm importe costo_unitario" data-aw="2"/>
									</div>
								</div>
								
								<div class="col-xs-2 col-sm-3 col-md-3 col-lg-1">
									
									<div class="be-checkbox has-success" style="display: block !important;">
										<input type="hidden" name="indigv" id='indigv' value="0">
										<input id="ckindigv" type="checkbox"  title="IGV">
										<label for="ckindigv">IGV</label>
									</div>
								</div>
								<div class="col-xs-10 col-sm-3 col-md-3 col-lg-2">

									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<input  type="text"
											id="monto_igv" name='monto_igv' title="MONTO DE IGV" 
											value="0.0" 
											readonly
											placeholder="MONTO IGV"
											autocomplete="off" class="form-control input-sm importe monto_igv" data-aw="2"/>
									</div>
								</div>
								
								<div class="col-xs-10 col-sm-3 col-md-3 col-lg-3">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<label class="control-label derecha">CT + IGV</label>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<input  type="text"
											id="costo_unitario_igv" name='costo_unitario_igv' title="MONTO DE IGV" 
											value="0.0" 
											readonly 
											placeholder="MONTO IGV"
											autocomplete="off" class="form-control input-sm importe costo_unitario_igv" data-aw="2"/>
									</div>
								</div>

								<div class="col-xs-10 col-sm-3 col-md-3 col-lg-3">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<label class="control-label derecha">COSTO TOTAL</label>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<input  type="text"
											id="costo_unitario_total" name='costo_unitario_total' title="MONTO DE IGV" 
											value="0.0" 
											readonly 
											placeholder="MONTO IGV"
											autocomplete="off" class="form-control input-sm importe costo_unitario_total" data-aw="2"/>
									</div>
								</div>
						</div>
					</div>

				</fieldset>
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
				<a href="{{ url('/gestion-esquema-productos/'.$idopcion) }}">
					<button type="button" class="btn btn-space btn-danger btnatras" >Cancelar</button>
				</a>
				<button id='btnguardarregistroesquema' type="submit" class="btn btn-space btn-primary btnguardarregistroesquema" @if($registro->estado_id != '1CIX00000003') disabled @endif>Guardar</button>
			@else
				<a href="{{ url('/gestion-esquema-productos/'.$idopcion) }}">
					<button type="button" class="btn btn-space btn-danger btnatras" >Cancelar</button>
				</a>
				<button id='btnguardarregistroesquema' type="submit" class="btn btn-space btn-primary btnguardarregistroesquema">Guardar</button>
			@endif      
		</p>
	</div>
</div>


