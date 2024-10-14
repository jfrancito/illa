<div class="col-sm-4">
  <div class="panel-heading panel-heading-divider formtext">Cliente :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{$precotizacion->cliente_nombre}}</span>
  </div>
  <div class="panel-heading panel-heading-divider formtext">Documento :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{$cliente->numerodocumento}}</span>
  </div>
  <div class="panel-heading panel-heading-divider formtext">Dirección :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{$cliente->direccion}}</span>
  </div>
  <div class="panel-heading panel-heading-divider formtext">Correo :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{$cliente->correo}}</span>
  </div>
  <div class="panel-heading panel-heading-divider formtext">Celular :
      <span class="panel-subtitle formsub" style="display: inline-block;">{{$cliente->celular}}</span>
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
      <a href="#" class="button  btn-success opciones agregalinea" 
         data_cotizacion = '{{$cotizacion->id}}'>
        <span class="icon mdi mdi-plus-circle-o btnagregarlinearequerimiento"></span>
      </a>
    </span>

 {{--    <span>
      <a href="{{ url('imprimir-cotizacion/'.$idopcion.'/'.Hashids::encode(substr($cotizacion->id,-8))) }}" class="button  btn-information opciones brnimprimircotizacion" 
        data_cotizacion_id = "{{$cotizacion->id}}"
       >
        <span class="icon mdi mdi-print btnimprimircotizacion"></span>
      </a>
    </span> --}}
    
    <span>
      <a href="#" class="button  btn-success opciones eliminalinea" 
        data_cotizacion_id = "{{$cotizacion->id}}"
       >
        <span class="icon mdi mdi-minus-circle-outline btneliminarlinearequerimiento"></span>
      </a>
    </span>

     <span>
      <a href="{{ url('gestion-evaluar-requerimiento/'.$idopcion) }}" class="button  btn-information opciones btnatras" 
        data_cotizacion_id = "{{$cotizacion->id}}"
       >
        <span class="icon mdi mdi-mail-reply btnatrascotizacion"></span>
      </a>
    </span>
    

  </div>  

</div>


<div class='col-sm-12 listajax listaajaxdetallecotizar'>
  @include('evaluarrequerimiento.ajax.alistadetallecotizacion')
</div>



