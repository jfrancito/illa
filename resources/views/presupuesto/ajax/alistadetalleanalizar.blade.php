<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Item</th>
      <th>Categoria</th>

      <th>Descripciones</th>
      <th>Unidad Medida</th>
      <th>Cantidad</th>
      <th>Precio Unitario</th>
      <th>Total</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadetalle as $index => $item)
      <tr >
        <td>{{$index + 1}}</td>
        <td><b>{{$item->categoriaanalisis_nombre}}</b></td>
        <td>{{$item->descripcion}}</td>
        <td>{{$item->unidadmedida_nombre}}</td>
        <td>{{$item->cantidad}}</td>
        <td>{{$item->precio_unitario}}</td>
        <td class="text-right">{{$item->total}}</td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="#" 
                  class= 'eliminaranalisis' 
                  data_cotizacion_id = "{{$item->cotizacion_id}}"
                  data_detalle_cotizacion_id = "{{$item->detallecotizacion_id}}" 
                  data_detalle_cotizacion_analisis_id = "{{$item->id}}">
                  Eliminar
                </a>  
              </li>
            </ul>
          </div>
        </td>
      </tr>                    
    @endforeach
  </tbody>
  <tfooter>
      <tr>
        <td colspan="6" class="text-right"><b>TOTAL COSTOS: </b></td>
        <td class="text-right"><b>{{$detallecotizacion->totalcosto}}</b></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="6" class="text-right"><b>TOTAL MANO DE OBRA: </b></td>
        <td class="text-right"><b>{{$detallecotizacion->totalmanoobra}}</b></td>
        <td></td>
      </tr>

      <tr>
        <td colspan="6" class="text-right"><b>TOTAL : </b></td>
        <td class="text-right"><b>{{$detallecotizacion->total_analisis}}</b></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="6" class="text-right"><b><span title="MARGEN GASTOS ADMINISTRATIVOS">M.G.ADMINISTRATIVOS : </span></b></td>
        <td class="text-right"><b>{{$detallecotizacion->impuestoanalisis_01}}</b></td>
        <td></td>
      </tr>  

      <tr>
        <td colspan="6" class="text-right"><b><span title="MARGEN GASTOS UTILIDAD"> M.G. UTILIDAD : </span></b></td>
        <td class="text-right"><b>{{$detallecotizacion->impuestoanalisis_02}}</b></td>
        <td></td>
      </tr>  

      <tr>
        <td colspan="6" class="text-right"><b>IGV:</b></td>
        <td class="text-right"><b>{{$detallecotizacion->igv}}</b></td>
        <td class="dflex">
            <div class="be-checkbox inline">
              <input id="check1" type="checkbox"
                @if($detallecotizacion->swigv==1) checked @endif 
              >
            </div>

          {{-- <input type="checkbox" @if($detallecotizacion->swigv==1) checked @endif class="form-group control input-sm">  --}}
          {{-- <button class="button form-control control input-sm"><span>ss</span></button> --}}
        </td>
      </tr>  

      <tr>
        <td colspan="6" class="text-right"><b>TOTAL PRECIO UNITARIO : </b></td>
        <td class="text-right"><b>{{$detallecotizacion->totalpreciounitario}}</b></td>
        <td></td>
      </tr> 
  </tfooter> 
</table>
