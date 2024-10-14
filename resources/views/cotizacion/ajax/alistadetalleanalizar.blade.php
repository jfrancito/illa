<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Item</th>
      <th>Categoria</th>
      <th>SubCategoria</th>
      <th>Producto</th>
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
        <td><b>{{$item->categoria_nombre}}</b></td>
        <td>{{$item->subcategoria_nombre}}</td>
        <td>{{$item->producto_nombre}}</td>
        <td>{{$item->unidadmedida_nombre}}</td>
        <td class="text-right">{{number_format($item->cantidad,0,'.',',')}}</td>
        <td class="text-right">{{number_format($item->precio_unitario,2,'.',',')}}</td>
        <td class="text-right">{{number_format($item->total,2,'.',',')}}</td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="#" 
                  class= 'eliminaranalisis' 
                  data_cotizacion_id = "{{$item->produccion_id}}"
                  data_detalle_cotizacion_id = "{{$item->id}}" 
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
        <td colspan="7" class="text-right"><b>TOTAL : </b></td>
        <td class="text-right"><b>{{number_format($cotizacion->total,2,'.', ',')}}</b></td>
        <td></td>
      </tr>

  </tfooter> 
</table>
