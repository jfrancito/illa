<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Id</th>
      {{-- <th>Lote</th> --}}
      <th>Tipo Comprobante</th>
      <th>Serie</th>
      <th>Numero</th>
      <th>Cliente</th>
      <th>Fecha</th>
      <th>Tipo</th>  
      <th>Moneda</th>
      <th>Total</th>             
      <th>Estado</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listacompra as $index => $item)
      <tr data_compra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
        <td>{{$index + 1 }}</td>
        {{-- <td>{{$item->lote}}</td> --}}
        <td>{{$item->tipo_comprobante_nombre}}</td>
        <td>{{$item->serie}}</td>
        <td>{{$item->numero}}</td>
        <td>{{$item->cliente_nombre}}</td>
        <td>{{date_format(date_create($item->fecha),'d-m-Y')}}</td>        
        <td>{{$item->tipo_venta_nombre}}</td>
        <td>{{$item->moneda_nombre}}</td>
        <td>
            @if($item->montototal == 0)
              <span class="obligatorio">{{number_format($item->montototal, 2)}}</span>
            @else
              <b>{{number_format($item->montototal, 2)}}</b>  
            @endiF
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
              @endiF
            @endiF
          @endiF
        </td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li @if($item->estado_id == '1CIX00000014') hidden @endif>
                <a href="{{ url('/modificar-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Modificar
                </a>  
              </li>
              <li @if($item->estado_id != '1CIX00000003' or $item->montototal == 0) hidden @endif>
                <a href="#" class="emitirventa" data_idventa = '{{Hashids::encode(substr($item->id, -8))}}'>
                  Emitir
                </a>  
              </li>
              <li @if($item->estado_id == '1CIX00000014') hidden @endif>
                <a href="{{ url('/extornar-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Extornar
                </a>  
              </li>
              <li>                
                <a href="{{ url('/pdf-ventas/'.Hashids::encode(substr($item->id, -8))) }}" target="_blank">
                  PDF
                </a>  
              </li>
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