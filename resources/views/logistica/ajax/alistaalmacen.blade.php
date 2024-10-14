<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Id</th>
      <th>Codigo</th>
      <th>Nombre</th>
      <th>Fecha Creacion</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listaalmacen as $index => $item)
      <tr data_almacen_id = "{{$item->id}}" class='activo{{$item->activo}}'>
        <td>{{$index + 1 }}</td>
        <td>{{$item->codigo}}</td>
        <td>{{$item->nombre}}</td>
        <td>{{date_format(date_create($item->fecha_crea),'d-m-Y')}}</td>        
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="{{ url('/modificar-almacen/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Modificar
                </a>  
              </li>
              <li @if($item->detalle->count() > 0) hidden @endif>
                <a href="{{ url('/quitar-almacen/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Quitar
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
    });
  </script> 
@endif