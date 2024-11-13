<form method="POST" id='formagregaresquemaproducto' action="{{ url('/modificar-orden-ventas-margen-productos/'.$idopcion.'/'.$idregistro) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
      {{ csrf_field() }}
<div class="row">

	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<fieldset class="scheduler-border">
			<legend class="scheduler-border">{{$ordenventa->codigo_shopify}}</legend>
				<div class="control-group">
						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Total Produccion</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type="text" name="total_produccion" readonly class="input form-control input-sm" value="{{ $ordenventa->total_produccion}}">
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Shopify </label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type='number'  name="descuento_shopify"  class="input form-control input-sm" 
										step="0.01"
										min="0.0"
										max="999999"  value="{{ $ordenventa->descuento_shopify}}">
									</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">2Checkout</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type='number'  name="checkout"  class="input form-control input-sm" 
										step="0.01"
										min="0.0"
										max="999999" 
										 
										value="{{ $ordenventa->checkout}}">
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Shipping </label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type='number'  name="shipping"  class="input form-control input-sm" 
										step="0.01"
										min="0.0"
										max="999999" 
										  value="{{ $ordenventa->shipping}}">
									</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Papeleria</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type='number'  name="papeleria"  class="input form-control input-sm" 
										step="0.01"
										min="0.0"
										max="999999" value="{{ $ordenventa->papeleria}}">
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Total Margen </label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type="text" name="total_margen" readonly class="input form-control input-sm" value="{{ $ordenventa->total_margen}}">
									</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Utilidad</label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type="text" name="utilidad" readonly class="input form-control input-sm" value="{{ $ordenventa->utilidad}}">
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label class="control-label derecha">Margen </label>
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-3">
										<input type="text" name="margen" readonly class="input form-control input-sm" value="{{ $ordenventa->margen}} %">
									</div>
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




