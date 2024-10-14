<div class="container">
  <div class="main-content container-fluid">
          <div class="row">
            <div class="col-xs-12 col-md-4">
              <div class="panel panel-default" style="background-color: #eeefff;">
                <div class="panel-heading panel-heading-divider xs-pb-15">Certificados por Periodos</div>
                <div class="panel-body xs-pt-25">
                  @foreach($listacertificadoperiod as $index => $item)
                    <div class="row user-progress user-progress-small" style="margin-bottom: 15px !important;">
                      <div class="col-md-5"><span class="title"><b>{{$item->periodo->nombre}}</b></span></div>
                      <div class="col-md-7"><span class="badge badge-primary">{{$item->cantidad}}</span>
                          
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-md-4">
              <div class="widget be-loading" style="background-color: #eeefff;">
                <div class="panel-heading panel-heading-divider xs-pb-15" style="padding-top: 0px;">Certificados por Periodos</div>

                <div class="widget-chart-container" >
                  <div id="top-sales" style="height: 100px;"></div>
                  <div class="chart-pie-counter">
                        {{count($listadatos)}}<br>
                  </div>
                </div>
                <div class="chart-legend">
                  <table>
                    <tr>
                      <td class="chart-legend-color"><span data-color="top-sales-color1" style="background: #f7d292;"></span></td>
                      <td>TOTAL</td>
                      <td class="chart-legend-value"> <span class="badge badge-primary">{{count($listadatos)}}</span></td>
                    </tr>

                    @foreach($listacertificadoproced as $index => $item)
                      <tr>
                        <td class="chart-legend-color"><span data-color="top-sales-color2 primary" style="background: #f7d292;"></span></td>
                        <td>{{$item->procedencia->nombre}} </td>
                        <td class="chart-legend-value"><span class="badge badge-primary">{{$item->cantidad}}</span></td>
                      </tr>
                    @endforeach
                  </table>
                </div>
                <div class="be-spinner">
                  <svg width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30" class="circle"></circle>
                  </svg>
                </div>
              </div>
            </div>



          </div>


  </div>
</div>


<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>ITEM</th>
      <th>CODIGO.</th>
      <th>INSTITUCION</th>
      <th>PERIODO</th>
      <th>PROCEDENCIA</th>
      <th>FECHA CREA</th>
      <th>OPCION</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_certificado_id = "{{$item->id}}">
        <td>{{$index +1}}</td>
        <td>{{$item->codigo}}</td>
        <td>{{$item->institucion->nombre}}</td>
        <td>{{$item->periodo->nombre}}</td>
        <td>{{$item->procedencia->nombre}}</td>
        <td>{{$item->fecha_crea}}</td>

        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="{{ url('/descargar-archivo-certificado/'.Hashids::encode(substr($item->id, -8)).'/'.Hashids::encode(substr($item->archivo_id, -8))) }}">
                  Descargar
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