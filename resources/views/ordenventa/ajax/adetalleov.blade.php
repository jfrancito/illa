					<table id='listadetalleventa'  class="table table-striped table-borderless" >
							<thead>
								<tr>
										<th>ID</th>
										<th>PRODUCTO</th>
										<th>TIPO ORO</th>
										<th>CANT. ORO</th>
										<th>CANTIDAD</th>	
										<th>PRECIO UNITARIO</th>
										<th>P. TOTAL</th>										
										<th>ACCION</th> 
								</tr>
							</thead>
							<tbody>
							@if(isset($listadetalle))								
								@foreach($listadetalle as $index => $item)									
									<tr data_detallecompra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
										<td>{{$index + 1 }}</td>
										<td>{{$item->producto_descripcion}}</td>
										<td>{{$item->tipo_oro_nombre}}</td>
										<td class="text-right">{{number_format($item->cantidad_oro, 2)}}</td>
										<td class="text-right">{{number_format($item->cantidad, 2)}}</td>
										<td class="text-right">{{number_format($item->preciounitario, 2)}}</td>
										<td class="text-right">{{number_format($item->total, 2)}}</td>	
										<td class="rigth">
											<div class="btn-group btn-hspace">
												<button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle" @if($registro->estado_id != '1CIX00000003') disabled @endif>Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
												<ul role="menu" class="dropdown-menu pull-right">
													<li>
														<a href="{{ url('/quitar-detalle-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
															Quitar
														</a>  
													</li>
												</ul>
											</div>
										</td>
									</tr>                    
								@endforeach                
							@endif
							</tbody>
					<tfooter>
						<tr>
			                <td colspan="6" class="text-right"><b><span title="SUBTOTAL">SUBTOTAL : </span></b></td>
			                  <td class="text-right">
			                    <b>		
			                    @if(isset($registro))	                    
			                      {{number_format($registro->venta, 2)}}	
			                    @else
			                    	{{number_format(0, 2)}}
			                    @endif		                    
			                    </b>
			                  </td>
			            	<td></td>
			            </tr>			            
			            <tr>
					        <td colspan="6" class="text-right"><b><span title="ENVIO">ENVIO : </span></b></td>       
					        <td class="text-right">
					        	<b>					        		
					        	@if(isset($registro))	                    
			                      {{ number_format($registro->envio,2,'.', ',') }}
			                    @else
			                    	{{number_format(0, 2)}}
			                    @endif
					        	</b>
					        </td>
					        <td class="dflex">
					            <input 
					              type="number" 
					              name="envio" 
					              id='envio' 
					              class="envio" 
					              step='1.00' min=0 max=99999
					              value="{{ number_format(0,2,'.', ',') }}"
					            >
					            @if(isset($registro))	    
					            <button type="button" name="btnupdenvio" id='btnupdenvio' class="btnupdenvio" 
					                data_orden_venta_id = "{{$registro->id}}"				                
					                data_monto_envio 	= "{{number_format($registro->envio,2,'.',',')}}"
					            ><span class="mdi mdi-replay"></span></button>	
					            @endif				            
					        </td>
					    </tr>
					    <tr>
					        <td colspan="6" class="text-right"><b><span title="DESCUENTO">DESCUENTO : </span></b></td>
					        <td class="text-right">
					        	<b>
					        	@if(isset($registro))	                    
			                      {{ number_format($registro->descuento,2,'.', ',') }}
			                    @else
			                    	{{number_format(0, 2)}}
			                    @endif					        		
					        	</b>
					        </td>
					        <td class="dflex">
					            <input 
					              type="number" 
					              name="descuento" 
					              id='descuento' 
					              class="descuento" 
					              step='1.00' min=0 max=99999
					              value="{{ number_format(0,2,'.', ',') }}"
					            >
					            @if(isset($registro))	  
					            <button type="button" name="btnupddescuento" id='btnupddescuento' class="btnupddescuento" 
					                data_orden_venta_id = "{{$registro->id}}"				                
					                data_monto_envio 	= "{{number_format($registro->descuento,2,'.',',')}}"
					            ><span class="mdi mdi-replay"></span></button>			
					            @endif		            
					        </td>
					    </tr>
					    <tr>
					        <td colspan="6" class="text-right"><b><span title="SEGURO">SEGURO : </span></b></td>
					        <td class="text-right">
					        	<b>
					        	@if(isset($registro))	                    
			                      {{ number_format($registro->seguro,2,'.', ',') }}
			                    @else
			                    	{{number_format(0, 2)}}
			                    @endif					        		
					        	</b>
					        </td>
					        <td class="dflex">
					            <input 
					              type="number" 
					              name="seguro" 
					              id='seguro' 
					              class="seguro" 
					              step='1.00' min=0 max=99999
					              value="{{ number_format(0,2,'.', ',') }}"
					            >
					            @if(isset($registro))	
					            <button type="button" name="btnupdseguro" id='btnupdseguro' class="btnupdseguro" 
					                data_orden_venta_id = "{{$registro->id}}"				                
					                data_monto_envio 	= "{{number_format($registro->seguro,2,'.',',')}}"
					            ><span class="mdi mdi-replay"></span></button>		
					            @endif			            
					        </td>
					    </tr>
					    <tr>
			                <td colspan="6" class="text-right"><b><span title="TOTAL">TOTAL : </span></b></td>
			                  <td class="text-right">
			                    <b>
			                    @if(isset($registro))
				                    {{number_format(
				                    	($registro->venta+$registro->envio+$registro->seguro)-$registro->descuento
				                    , 2)}}
				                @else
				                	{{number_format(0, 2)}}
				                @endif			
			                    </b>
			                  </td>
			            	<td></td>
			            </tr>					    
					</tfooter>
					</table>  

