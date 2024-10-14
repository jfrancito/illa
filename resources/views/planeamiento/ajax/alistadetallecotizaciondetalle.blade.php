<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 tlistacategorias">
  <thead>
    <tr>
      <th>#</th>
      <th>ITEM</th>
      {{-- <th>Categoria</th> --}}
      <th>Descripciones</th>
      <th>Medida</th>
      <th>Cantidad</th>
      <th>Precio Unitario</th>
      <th>Total</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadetalle as $index => $item)
      <tr>
        <td >  
          <div class="text-center be-checkbox be-checkbox-sm" >
            <input  type="checkbox"
                    class="{{Hashids::encode(substr($item->id, -8))}} input_check_cat_ln check{{Hashids::encode(substr($item->id, -8))}}" 
                    id="{{Hashids::encode(substr($item->id, -8))}}" 
                    data_id ="{{ $item->id }}"
                    @if($item->ispadre ==0) disabled @endif
                    >
            <label  for="{{Hashids::encode(substr($item->id, -8))}}"
                  data-atr = "ver"
                  class = "checkbox"                    
                  name="{{Hashids::encode(substr($item->id, -8))}}"
            ></label>
          </div>
        </td>

        <td>{{$item->codigo}}</td>
        @if($item->ispadre==1)
          <td colspan="4">{{$item->descripcion}}</td>
          <td></td>
          <td></td>
        @else
          <td>{{$item->descripcion}}</td>
          <td>{{$item->unidadmedida_nombre}}</td>
          <td>{{$item->cantidad}}</td>
          <td>{{number_format($item->precio_unitario,2,'.',',')}}</td>
          <td>{{number_format($item->total,2,'.',',')}}</td>
          <td class="rigth">
            <div class="btn-group btn-hspace">
              <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
              <ul role="menu" class="dropdown-menu pull-right">

                <li>
                  <a href="#" 
                    class= 'analisiscotizacion' 
                    data_cotizacion_id = "{{$item->cotizacion_id}}"
                    data_detalle_cotizacion_id = "{{$item->id}}" >
                    VER DETALLE
                  </a>  
                </li>
              </ul>
            </div>
          </td>

        @endif
      </tr>                    
    @endforeach
  </tbody>
  <tfooter>
      <tr >
        <td colspan="6" class="text-right"><b>TOTAL : </b></td>
        <td><b>{{$cotizacion->total}}</b></td>
        <td></td>
      </tr>     
  </tfooter> 
</table>


