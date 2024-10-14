<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 tlistacategorias">
  <thead>
    <tr>
      <th>#</th>
      <th>ITEM</th>
      <th>Descripciones</th>
      <th>Medida</th>
      <th>Cantidad</th>
      <th>Cant Analisis</th>
      {{-- <th>Total</th> --}}
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
          <td colspan="3">{{$item->descripcion}}</td>
          <td></td>
          <td></td>
        @else
          <td>{{$item->descripcion}}</td>
          <td>{{$item->unidadmedida_nombre}}</td>
          {{-- <td>{{$item->cantidad}}</td> --}}
          <td>{{number_format($item->cantidad,2,'.',',')}}</td>
          <td>{{number_format($item->totalcantidad,2,'.',',')}}</td>
          <td class="rigth">
            <div class="btn-group btn-hspace">
              <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
              <ul role="menu" class="dropdown-menu pull-right">

                <li>
                  <a href="#" 
                    class= 'analisiscotizacion'
                    title="aqui es" 
                    data_planeamiento_id = "{{$item->planeamiento_id}}"
                    data_detalle_planeamiento_id = "{{$item->id}}" >
                    Analisis
                  </a>  
                </li>
                <li>
                  <a href="#" 
                    class= 'eliminarcotizacion' 
                    data_planeamiento_id = "{{$item->planeamiento_id}}"
                    data_descpartida = "{{ $item->descripcion }}"
                    data_detalle_planeamiento_id = "{{$item->id}}" >

                    Eliminar
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
        <td colspan="4" class="text-right"><b>TOTAL : </b></td>
        <td>
          <b>
            {{number_format($cotizacion->totalcantidad,2,'.',',')}}
            {{-- {{$cotizacion->total}} --}}
          </b>
        </td>
        <td>
          <b>
            {{number_format($cotizacion->totalcantidadanalisis,2,'.',',')}}
            {{-- {{$cotizacion->total}} --}}
          </b>
        </td>
        <td></td>
      </tr>     
  </tfooter> 
</table>


{{-- <table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>#</th>
      <th>Orden</th>
      <th>Categoria</th>
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
      <tr data_categoria_id='{{ $item->id }}'>
        <td >  
          <div class="text-center be-checkbox be-checkbox-sm" >
            <input  type="checkbox"
                    class="{{Hashids::encode(substr($item->id, -8))}} input_check_pe_ln check{{Hashids::encode(substr($item->id, -8))}}" 
                    id="{{Hashids::encode(substr($item->id, -8))}}" 
                    @if($item->estado_id != '1CIX00000003') disabled @endif
                    >
            <label  for="{{Hashids::encode(substr($item->id, -8))}}"
                  data-atr = "ver"
                  class = "checkbox"                    
                  name="{{Hashids::encode(substr($item->id, -8))}}"
            ></label>
          </div>
        </td>

        <td>{{$index + 1}}</td>
        <td><b>{{$item->categoriaservicio_nombre}}</b></td>
        <td>{{$item->descripcion}}</td>
        <td>{{$item->unidadmedida_nombre}}</td>
        <td>{{$item->cantidad}}</td>
        <td>{{$item->precio_unitario}}</td>
        <td>{{$item->total}}</td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">

              <li>
                <a href="#" 
                  class= 'analisiscotizacion' 
                  data_planeamiento_id = "{{$item->cotizacion_id}}"
                  data_detalle_planeamiento_id = "{{$item->id}}" >
                  Analisis
                </a>  
              </li>
              <li>
                <a href="#" 
                  class= 'modificarcotizacion' 
                  data_planeamiento_id = "{{$item->cotizacion_id}}"
                  data_detalle_planeamiento_id = "{{$item->id}}" >
                  Modificar
                </a>  
              </li>
              <li>
                <a href="#" 
                  class= 'eliminarcotizacion' 
                  data_planeamiento_id = "{{$item->cotizacion_id}}"
                  data_detalle_planeamiento_id = "{{$item->id}}" >
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
      <tr >
        <td colspan="7" class="text-right"><b>TOTAL : </b></td>
        <td><b>{{$cotizacion->total}}</b></td>
        <td></td>
      </tr>     
  </tfooter> 
</table>
 --}}