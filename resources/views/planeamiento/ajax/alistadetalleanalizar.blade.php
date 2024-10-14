<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Item</th>
      <th>Categoria</th>

      <th>Descripciones</th>
      <th>Unidad Medida</th>
      <th>Cantidad</th>
      {{-- <th>Precio Unitario</th> --}}
      {{-- <th>Total</th> --}}
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
        <td class="text-right">{{$item->cantidad}}</td>
        {{-- <td class="text-right">{{$item->precio_unitario}}</td> --}}
        {{-- <td class="text-right">{{$item->total}}</td> --}}
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="#" 
                  class= 'eliminaranalisis' 
                  data_planeamiento_id = "{{$item->planeamiento_id}}"
                  data_detalle_planeamiento_id = "{{$item->detalleplaneamiento_id}}" 
                  data_descpartida = "{{$item->descripcion}}" 
                  data_detalle_planeamiento_analisis_id = "{{$item->id}}">
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
        <td colspan="4" class="text-right"><b>TOTAL CANTIDAD: </b></td>
        <td class="text-right"><b>{{number_format($detallecotizacion->totalcantidad,2,'.', ',')}}</b></td>
        <td></td>
      </tr>
      


      


  </tfooter> 
</table>
