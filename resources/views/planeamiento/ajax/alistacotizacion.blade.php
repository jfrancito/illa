<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
	<thead>
		<tr>
			<th>#</th>
			<th>Lote</th>
			<th>Fecha</th>
			<th>Cliente</th>
			<th>Total</th>
			<th>Estado</th>
			<th>Opciones</th>
		</tr>
	</thead>
	<tbody>
		@foreach($listacotizaciones as $index => $item)
			<tr data_precotizacion_id = "{{$item->id}}">
				<td >  
					{{ $index+1 }}
				</td>
				<td>{{$item->lote}}</td>
				<td>{{date_format(date_create($item->fecha_crea), 'd-m-Y H:i')}}</td>
				<td>{{$item->cliente_nombre}}</td>
				<td>{{number_format($item->totalcantidad,2,'.',',')}}</td>
				<td>
					<span class="badge {{ $item->classcolor }}">{{$item->estado_descripcion}}</span>          
				</td>
				<td class="rigth">
					<div class="btn-group btn-hspace">
						<button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
						<ul role="menu" class="dropdown-menu pull-right">
								<li>
									<a href="{{ url('/analizar-planeamiento/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
										ANALIZAR
									</a>  
								</li>
								<li>
									<a href="{{ url('/imprimir-planeamiento/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}" target="_blank">
										IMPRIMIR
									</a>  
								</li>
								<li>
									<a href="{{ url('/cronograma-planeamiento/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}" >
										CRONOGRAMA
									</a>
								</li>
{{-- 								
								<li>
									<a href="{{ url('/extornar-aprobacion-planeamiento/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
										EXTORNAR
									</a>  
								</li>
 --}}

						</ul>
					</div>
				</td>
			</tr>                    
		@endforeach
	</tbody>
</table>

@if(isset($ajax))
	<script type="text/javascript">
		$(document).ready(function(){
			 App.dataTables();
		});
	</script> 
@endif