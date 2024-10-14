<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Id</th>
      <th>Tipo Comprobante</th>
      <th>Serie</th>
      <th>Numero</th>
      <th>Cliente</th>
      <th>Fecha</th>
      <th>Moneda</th>
      <th>Pagado</th>
      <th>Saldo</th>
      <th>Total</th>
      <th>Estado</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_compra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
        <td>{{$index + 1 }}</td>
        {{-- <td>{{$item->lote}}</td> --}}
        <td>{{$item->tipo_comprobante_nombre}}</td>
        <td>{{$item->serie}}</td>
        <td>{{$item->numero}}</td>
        <td>{{$item->cliente_nombre}}</td>
        <td>{{date_format(date_create($item->fecha),'d-m-Y')}}</td>        
        {{-- <td>{{$item->tipo_venta_nombre}}</td> --}}
        <td>{{$item->moneda_nombre}}</td>
        <td>{{number_format($item->acta, 2)}}</td>
        <td>{{number_format($item->saldo, 2)}}</td>
        <td>
            @if($item->total == 0)
              <span class="obligatorio">{{number_format($item->total, 2)}}</span>
            @else
              <b>{{number_format($item->total, 2)}}</b>  
            @endif
        </td>        
        <td>
          @if($item->estado_id=='1CIX00000003')
            <span class="badge badge-light">{{$item->estado_descripcion}}</span>
          @else
            @if($item->estado_id=='1CIX00000045')
              <span class="badge badge-success">{{$item->estado_descripcion}}</span><br>
            @else
              @if($item->estado_id=='1CIX00000044')
                <span class="badge badge-danger">{{$item->estado_descripcion}}</span><br>
              @endif
            @endif
          @endif
        </td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li @if($item->estado_id == '1CIX00000045') hidden @endif>
                <a href="{{ url('/cobrar-caja-venta/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Cobrar
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