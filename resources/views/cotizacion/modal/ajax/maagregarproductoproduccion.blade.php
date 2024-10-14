
<form method="POST" action="{{ url('/agregar-producto-cotizacion/'.$idopcion.'/'.Hashids::encode(substr($cotizacion->id, -8))) }}" name='frmagregarproductoproduccion' id='frmagregarproductoproduccion'>
	{{ csrf_field() }}
	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			{{$cotizacion->nombre}} <span>({{$cotizacion->lote}})</span>
		</h3>
			<span>
				<h4>{{ $oeProducto->descripcion }}</h4>
			</span>
		<input type="hidden" name="productos" id="productos">
		<input type="hidden" name="idopcion" id="idopcion" value='{{$idopcion}}'>
		<input type="hidden" name="data_cotizacion_id" id="data_cotizacion_id" value='{{$cotizacion->id}}'>
		<input type="hidden" name="idproducto" id="idproducto" value='{{$oeProducto->id}}'>
	</div>
	<div class="modal-body">
		<div  class="row regla-modal">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group">
						<table id='nso' class="table1 table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
							<thead>
								<tr>
									<th>#</th>
									<th>Prov</th> 
									{{-- AGREGAR CODIGO --}}
									<th>Cant</th>
									<th>Disp</th>
									<th>Cons</th>
									<th>Prec</th>
									<th>STot</th>
									<th>Accn</th>
								</tr>
							</thead>
							<tbody class="tbodyproductos" id='tdbodyproductos'>
								@foreach($olProductos as $index => $item)
									<tr data_detallecompra_id = "{{$item->id}}">
										<td >  
											{{ $index+1 }}
										</td>
										<td>{{$item->proveedor_nombre}}</td>
										<td>{{number_format($item->cantidad,0,'.',',')}}</td>
										<td>{{number_format($item->disponible,0,'.',',')}}</td>
										<td>{{number_format($item->consumido,0,'.',',')}}</td>
										<td>{{number_format($item->preciounitario,2,'.',',')}}</td>
										<td>{{number_format(0.0,2,'.',',')}}</td>
										<td class="rigth">
											<input 
												type="number" 
												name="cantidadprod" 
												id='cantidadprod'{{ $item->id }} 
												class="cantidadprod" 
												detallecompra_id="{{ $item->id}}"
												producto_id="{{ $item->producto_id}}"
												compra_id="{{ $item->compra_id}}"
												step='0.01' min=0 max={{ $item->disponible }} 
												value="0"
											>
										</td>
									</tr>                    
								@endforeach
							</tbody>
						</table>


					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Guardar</button>
	</div>
</form>

@if(isset($ajax))
	<script type="text/javascript">
		$(document).ready(function(){
			// App.dataTables();
			App.formElements();
			$('.importe').inputmask({ 'alias': 'numeric', 
			'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
			'digitsOptional': false, 
			'prefix': '', 
			'placeholder': '0'});
		});
	</script>
@endif


