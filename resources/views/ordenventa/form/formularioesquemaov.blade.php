
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
		@foreach($lregistro as $index => $item)
			@if($index == 0)
			    <li class="@if($tab == $item->id) active @endif"><a href="#{{$item->id}}" data-toggle="tab">({{$index + 1}}) {{$item->producto_descripcion}}</a></li>
			@else
				<li class="@if($tab == $item->id) active @endif"><a href="#{{$item->id}}" data-toggle="tab">({{$index + 1}}) {{$item->producto_descripcion}}</a></li>
			@endif


		@endforeach  
      </ul>
      <div class="tab-content">
		@foreach($lregistro as $index => $item)
				@if($index == 0)
			        <div id="{{$item->id}}" class="tab-pane @if($tab == $item->id) active @endif cont">
			        	@include('ordenventa.form.esquemaov',['swmodidicar'=>true])
					</div>
				@else
			        <div id="{{$item->id}}" class="tab-pane @if($tab == $item->id) active @endif cont">
			        	@include('ordenventa.form.esquemaov',['swmodidicar'=>true]) 
			        </div>
				@endif
		@endforeach
      </div>
    </div>
  </div>
</div>



