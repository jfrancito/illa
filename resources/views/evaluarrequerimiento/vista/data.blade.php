<div class="row">	
	<div class="col-sm-12">
		<div class="col-sm-6">
			<div class="panel-heading panel-heading-divider" style="border:0px;">Cliente
				<span class="panel-subtitle">{{$precotizacion->cliente_nombre}}</span>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="panel-heading panel-heading-divider" style="border:0px;">Descripcion
				<span class="panel-subtitle">{{$precotizacion->descripcion}}</span>
			</div>
		</div>
	</div>
</div>

{{-- <div class="row" style="padding-bottom:300px;">
	<div class="be">
		@include('requerimiento.ajax.alistaarchivos')
	</div>	
</div> --}}

 <div class="row" style="padding-bottom:300px;">
 	@if($registro->estado_id<>'1CIX00000004')
		<div class="row">
			<div class="col-sm-12 col-lg-6">
				<div id="accordionfiles" class="panel-group accordion">
					<div class="panel panel-full-default">
						<div class="panel-heading">
							<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordionfiles" href="#subirArchivos" aria-expanded="false" 
								class="collapsed"><i class="icon mdi mdi-chevron-down"></i> <b>Subir Archivos</b></a></h4>
						</div>
						<div id="subirArchivos" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
							<div class="panel-body">
									@include('requerimiento.files')
							</div>
						</div>
					</div>
					
				</div>
			</div>
			<div class="col-sm-12 col-lg-6">
				<div id="accordionfilesclonar" class="panel-group accordion">
					<div class="panel panel-full-default">
						<div class="panel-heading">
							<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordionfilesclonar" href="#clonarArchivos" aria-expanded="false" 
								class="collapsed"><i class="icon mdi mdi-chevron-down"></i> <b>Clonar Archivos</b></a></h4>
						</div>
						<div id="clonarArchivos" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
							<div class="panel-body">
									@include('requerimiento.filesclonar')
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	@endif
	<div class="panel-body">
		<div class='filtrotabla row'>
			<div class="col-xs-12">
				<input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
			</div>
		</div>
		<div class='listajax'>
			@include('evaluarrequerimiento.ajax.alistaarchivos')
		</div>
	</div>
 </div>