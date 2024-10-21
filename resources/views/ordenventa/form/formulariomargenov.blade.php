
<div class="row">
	<div class="col-xs-12">
		<div class="form-group">
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>CODIGO : </b>{{$ordenventa->codigo}}</label>
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>CLIENTE : </b>{{$ordenventa->cliente_nombre}}</label>
				</div>

				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>MONEDA : </b>{{$ordenventa->moneda_nombre}}</label>
				</div>

				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>ENVIO : </b>{{$ordenventa->envio}}</label>
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>DESCUENTO : </b>{{$ordenventa->descuento}}</label>
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>SEGURO : </b>{{$ordenventa->seguro}}</label>
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>TOTAL VENTA: </b>{{$ordenventa->venta}}</label>
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
						<label class="control-label derecha"><b>TOTAL PRODUCCION: </b>{{$ordenventa->total_produccion}}</label>
				</div>

		</div>
	</div>
</div>



<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="tab-container">
      <ul class="nav nav-tabs">
			  <li class="active"><a href="#margen" data-toggle="tab">MARGEN</a></li> 
      </ul>
      <div class="tab-content">
			    <div id="margen" class="tab-pane active cont">
			        	@include('ordenventa.form.margenov',['swmodidicar'=>true])
					</div>

      </div>
    </div>
  </div>
</div>



