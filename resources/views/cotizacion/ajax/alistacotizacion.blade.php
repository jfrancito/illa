<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
	<thead>
		<tr>
			<th>#</th>
			<th>Codigo</th>
			<th>Fecha</th>
			<th>Nombre</th>
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
				<td>{{$item->nombre}}</td>
				<td>{{number_format($item->total,2,'.',',')}}</td>
				<td>
					<span class="badge {{ $item->classcolor }}">{{$item->estado_descripcion}}</span>          
				</td>
				<td class="rigth">
					<div class="btn-group btn-hspace">
						<button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
						<ul role="menu" class="dropdown-menu pull-right">
									
								@if($item->estado_id==$idgenerado)

									<li>
										<a href="{{ url('/cotizar-cotizacion/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
											Editar
										</a>  
									</li>
										@if($item->total>0)
											<li>
												<a href="#" class="emitirproduccion" data_produccion = '{{Hashids::encode(substr($item->id, -8))}}'>
													Emitir
												</a>
											</li>
										@endif
									<li>
										<a href="#" class="eliminarproduccion" data_produccion ="{{Hashids::encode(substr($item->id, -8))}}" data_opcion="{{ $idopcion }}">
											Eliminar
										</a>
									</li>
									{{-- <a href="#" class="editarproduccion" data_produccion ="{{Hashids::encode(substr($item->id, -8))}}" data_opcion="{{ $idopcion }}"> --}}
									<li>
										<a href="{{ url('/detalle-produccion/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
											Detalle
										</a>
									</li>
								@else
									<li>
										<a href="{{ url('/detalle-produccion/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
											Detalle
										</a>
									</li>
								@endif

								



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