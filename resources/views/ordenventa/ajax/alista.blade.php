<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Id</th>
      {{-- <th>Lote</th> --}}
      {{-- <th>Tipo Comprobante</th> --}}
      {{-- <th>Serie</th> --}}
      <th>Codigo Shopify</th>
      <th>Codigo</th>
      <th>Cliente</th>
      <th>Fecha</th>
      {{-- <th>Tipo</th>   --}}
      <th>Moneda</th>
      <th>Productos</th>             
      <th>Estado</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_compra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
        <td>{{$index + 1 }}</td>
        {{-- <td>{{$item->lote}}</td> --}}
        {{-- <td>{{$item->tipo_comprobante_nombre}}</td> --}}
        {{-- <td>{{$item->serie}}</td> --}}
        <td>{{$item->codigo_shopify}}</td>
        <td>{{$item->codigo}}</td>
        <td>{{$item->cliente_nombre}}</td>
        <td>{{date_format(date_create($item->fecha),'d-m-Y')}}</td>        
        {{-- <td>{{$item->tipo_venta_nombre}}</td> --}}
        <td>{{$item->moneda_nombre}}</td>
        <td>
            <span class="obligatorio">{{number_format(count($item->Detalle()), 2)}}</span>
            {{-- <b>{{number_format(count($item->detalle()), 2)}}</b>   --}}
        </td>        
        <td>
          @if($item->estado_id=='1CIX00000003')
            <span class="badge badge-light">{{$item->estado_descripcion}}</span>
          @else
            @if($item->estado_id=='1CIX00000004')
              <span class="badge badge-success">{{$item->estado_descripcion}}</span><br>
            @else
              @if($item->estado_id=='1CIX00000014')
                <span class="badge badge-danger">{{$item->estado_descripcion}}</span><br>
              @endif
            @endif
          @endif
        </td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li @if($item->estado_id == '1CIX00000014') hidden @endif>
                <a href="{{ url('/modificar-orden-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Modificar
                </a>  
              </li>
              <li @if($item->estado_id != '1CIX00000003') hidden @endif>
                <a href="{{ url('/aprobar-orden-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}" class="emitirordenventa" data_codigo = '{{ $item->codigo }}'>
                  Aprobar
                </a>  
              </li>

              <li @if($item->estado_id == '1CIX00000003') hidden @endif>
                <a href="{{ url('/orden-ventas-esquema-producto/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Produccion
                </a>  
              </li>
              <li @if($item->estado_id == '1CIX00000003') hidden @endif>
                <a href="{{ url('/orden-ventas-margen-producto/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Margen
                </a>  
              </li>

              {{-- <li @if($item->estado_id == '1CIX00000014') hidden @endif>
                <a href="{{ url('/extornar-orden-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Extornar
                </a>  
              </li> --}}
{{--               <li>                
                <a href="{{ url('/pdf-orden-ventas/'.Hashids::encode(substr($item->id, -8))) }}" target="_blank">
                  PDF
                </a>  
              </li> --}}
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
      $(".select3").select2();
    });
  </script> 
@endif