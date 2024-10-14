<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Id</th>
      {{-- <th>Codigo</th> --}}
      <th>Producto</th>             
      <th>Fecha</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_compra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
        <td>{{$index + 1 }}</td>
        <td>{{$item->codigo}}</td>
        <td>{{$item->descripcion}}</td>
        
        <td>{{date_format(date_create($item->fecha_crea),'d-m-Y')}}</td>        
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li @if($item->estado_id == '1CIX00000014') hidden @endif>
                <a href="{{ url('/modificar-orden-ventas/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Esquemas
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