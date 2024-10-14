

<div class="col-sm-4">
  <div class="panel-heading panel-heading-divider formtext">Nombre :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{$cotizacion->nombre}}</span>
  </div>

</div>


<div class="col-sm-4">
  <div class="panel-heading panel-heading-divider formtext">Lote :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{$cotizacion->lote}}</span>
  </div>
  <div class="panel-heading panel-heading-divider formtext">Fecha :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{date_format(date_create($cotizacion->fecha), 'd-m-Y')}}</span>
  </div>
</div>

<div class="col-sm-2 col-sm-offset-10">
  
  <div class="botonesconfiglinea">


     <span>
      <a href="{{ url('gestion-de-cotizacion/'.$idopcion) }}" class="button  btn-information opciones btnatras" 
        data_cotizacion_id = "{{$cotizacion->id}}"
       >
        <span class="icon mdi mdi-mail-reply btnatrascotizacion"></span>
      </a>
    </span>
    
  </div>  

</div>


<div class='col-sm-12 listajax listaajaxdetallecotizar'>
  @include('cotizacion.ajax.alistadetallecotizaciondetalle')
</div>



