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

    </tr>
  </thead>
  <tbody>
    @foreach($listadetalle as $index => $item)
      <tr >
        <td>{{$index + 1}}</td>
        <td><b>{{$item->categoriaanalisis_nombre}}</b></td>
        <td>{{$item->descripcion}}</td>
        <td>{{$item->unidadmedida_nombre}}</td>
        <td class="text-right">{{$item->cantidad}}</td>
        <td class="text-right">{{$item->precio_unitario}}</td>
        <td class="text-right">{{$item->total}}</td>


      </tr>                    
    @endforeach
  </tbody>
  <tfooter>
      <tr>
        <td colspan="6" class="text-right"><b>TOTAL MATERIAL E INSUMOS: </b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->totalcosto,2,'.', ',')}}</b></td>
      </tr>
      <tr>
        <td colspan="6" class="text-right"><b>TOTAL MANO DE OBRA: </b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->totalmanoobra,2,'.', ',')}}</b></td>
      </tr>
      <tr>
        <td colspan="6" class="text-right"><b>TOTAL SERVICIO: </b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->totalservicio,2,'.', ',')}}</b></td>
      </tr>

      <tr>
        <td colspan="6" class="text-right"><b>TOTAL : </b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->total_analisis,2,'.', ',')}}</b></td>
      </tr>
      <tr>
        <td colspan="6" class="text-right"><b><span title="MARGEN GASTOS ADMINISTRATIVOS">M.G.ADMINISTRATIVOS : </span></b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->impuestoanalisis_01,2,'.', ',')}}</b></td>
      
      </tr>  

      <tr>
        <td colspan="6" class="text-right"><b><span title="MARGEN GASTOS UTILIDAD"> M.G. UTILIDAD : </span></b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->impuestoanalisis_02,2,'.', ',')}}</b></td>
      </tr>  

      <tr>
        <td colspan="6" class="text-right"><b><span title="SUBTOTAL PRECIO UNITARIO">PREC.UNIT PREVIO: </span></b></td>
        <td class="text-right"><b><span>{{number_format($detallecotizacion->subtotalpunitario,2,'.', ',')}}</span></b></td>
      </tr>  


      <tr>
        <td colspan="6" class="text-right"><b>IGV:</b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->igv,2,'.', ',')}}</b></td>
        
      </tr>  


      <tr>
        <td colspan="6" class="text-right"><b><span title="SUBTOTAL PRECIO UNITARIO"> SUBTOTAL PRECIO UNIT: </span></b></td>
        <td class="text-right"><b><span>{{number_format($detallecotizacion->subtotalpunitario,2,'.', ',')}}</span></b></td>
      </tr>  
      
      <tr>
        <td colspan="6" class="text-right"><b><span title="SUBTOTAL PRECIO UNITARIO"> CANTIDAD: </span></b></td>
        <td class="text-right"><b><span>{{number_format($detallecotizacion->cantidad,0,'.', ',')}}</span></b></td>
      </tr> 

      <tr>
        <td colspan="6" class="text-right"><b><span title="TOTAL PRECIO UNITARIO">TOTAL PRECIO UNITARIO  : </span></b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->totalpreciounitario,2,'.', ',')}}</b></td>
      </tr> 


  </tfooter> 
</table>
