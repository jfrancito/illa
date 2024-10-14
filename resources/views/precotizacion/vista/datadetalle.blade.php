<div class="panel-heading panel-heading-divider" style="border:0px;">Cliente
		<span class="panel-subtitle">{{$precotizacion->cliente_nombre}}</span>
</div>

<div class="panel-heading panel-heading-divider" style="border:0px;">Descripcion
		<span class="panel-subtitle">{{$precotizacion->descripcion}}</span>
</div>

<div class="row" style="padding-bottom:300px;">
<div class="be">
	@include('evaluarrequerimiento.ajax.alistaarchivosdetalle')
</div>	
</div>