

<div class="col-sm-2 col-sm-offset-10">
  
  <div class="botonesconfiglinea">

    {{-- <span>
      <a href="{{ url('imprimir-cotizacion/'.$idopcion.'/'.Hashids::encode(substr($cotizacion->id,-8))) }}" class="button  btn-information opciones brnimprimircotizacion" 
        data_cotizacion_id = "{{$cotizacion->id}}"
       >
        <span class="icon mdi mdi-print btnimprimircotizacion"></span>
      </a>
    </span> --}}
   
     <span>
      <a href="{{ url('gestion-de-cotizacion/'.$idopcion) }}" class="button  btn-information opciones btnatras" 
        data_cotizacion_id = "{{$cotizacion->id}}"
       >
        <span class="icon mdi mdi-mail-reply btnatrascotizacion"></span>
      </a>
    </span>
    

  </div>  

</div>




