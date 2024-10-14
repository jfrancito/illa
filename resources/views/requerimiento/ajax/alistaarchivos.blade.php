<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Nro</th>
      <th>Descripcion</th>      
      <th>Extension</th>      
      <th>Tamaño(MB)</th>      
     

      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listaarchivos as $index => $item)
      <tr data_precotizacion_id = "{{$item->id}}">
        <td>{{ $index+1 }}</td>
        <td class="cell-detail" >
          <span><b>Lote : </b> {{$item->lote}}</span>
          <span><b>Fecha Subida: </b> {{date_format(date_create($item->fecha_crea), 'd-m-Y H:i')}} </span>
          <span><b>Nombre Archivo : </b> {{$item->nombre_archivo}} </span>
          <span><b>Area : </b> {{$item->area_nombre}} </span>
          <span><b>Usuario : </b> {{$item->usuario_nombre}} </span>
        </td>
        <td>
          <img src="{{ asset('/public/img/icono/'.$item->extension.'.png')}}" width="40px" height="50px" alt="{{ $item->extension }}">
        </td>
        <td>
          {{ round($item->size/pow(1024,$unidad),2) }}
        </td>


        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="{{ url('/descargar-archivo-requerimiento/'.$idopcion.'/'.Hashids::encode(substr($registro->id, -8)).'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Descargar
                </a>  
              </li>
              @if($registro->estado_id<>'1CIX00000004')
                <li>
                  <a href="{{ url('/eliminar-archivo-requerimiento/'.$idopcion.'/'.Hashids::encode(substr($registro->id, -8)).'/'.Hashids::encode(substr($item->id, -8))) }}">
                    Eliminar
                  </a>  
                </li>
              @endif
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