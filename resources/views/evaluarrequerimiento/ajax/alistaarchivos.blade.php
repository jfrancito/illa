{{-- @if($registro->estado_id<>'1CIX00000001') --}}
                    <div class="row">
                      <div class="col-sm-12 col-lg-12">
                        <div id="accordionfiles" class="panel-group accordion">
                          <div class="panel panel-full-default">
                            <div class="panel-heading">
                              <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordionfiles" href="#subirArchivos" aria-expanded="false" 
                                class="collapsed"><i class="icon mdi mdi-chevron-down"></i> <b>Subir Archivos</b></a></h4>
                            </div>
                            <div id="subirArchivos" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                              <div class="panel-body">
                                  @include('requerimiento.files')
                              </div>
                            </div>
                          </div>
                          
                        </div>
                      </div>
                     
                    </div>
                  {{-- @endif --}}
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
                <a href="{{ url('/descargar-archivo-evaluar-requerimiento/'.$idopcion.'/'.Hashids::encode(substr($registro->id, -8)).'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Descargar
                </a>  
              </li>
              @if($registro->estado_id<>'1CIX00000004')
                <li>
                  <a href="{{ url('/eliminar-archivo-evaluar-requerimiento/'.$idopcion.'/'.Hashids::encode(substr($registro->id, -8)).'/'.Hashids::encode(substr($item->id, -8))) }}">
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