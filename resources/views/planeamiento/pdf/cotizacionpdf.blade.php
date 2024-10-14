<!DOCTYPE html>
<html lang="es">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/alfasweb.css?v='.$version) }} "/>
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/alfasweb.css?v='.$version) }} "/>
  <link rel="icon" type="image/x-icon" href="{{ asset('public/favicon.ico') }}"> 
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/pdfcotizacion.css?v='.$version) }} "/>
</head>

<body>

@php
  $montoNeto=0;
@endphp
@php 
    $raiz = $_SERVER["DOCUMENT_ROOT"].$capeta.'/';
@endphp


<div class="row">
  <div class="page" style="align: center !important;">


    <div class="col-md-10 col-md-offset-1">
      <div class="row">
        <div class="col-md-10 col-md-offset-1 centro" >
            <img  height="80"  width="320" src="{{$raiz}}public/img/empresa/logo-grande.jpg" align="center">
            <br>
            <span class="subtituloempresa">
            {{ $empresa->domiciliofiscal1 }}
            </span>
            <hr class="separadorempresa">
        </div>
      </div>
      <div class="row">
            <table class="tdatoscliente" >
                <tr>
                  <td width="80%" class="sinpadding">
                    <table class="tdatos" > 
                      <tr>
                        <td class="sinpadding" width="10%">CLIENTE</td>
                        <td class="sinpadding" width="5%">:</td>
                        <td class="sinpadding" width="85%">{{ $cliente->nombre_razonsocial }}</td>
                      </tr>
                       <tr>
                        <td class="sinpadding" width="10%">N°DOC.</td>
                        <td class="sinpadding" width="5%">:</td>
                        <td class="sinpadding" width="85%">{{ $cliente->numerodocumento }}</td>
                      </tr>
                      <tr>
                        <td class="sinpadding" width="10%">DIRECCIÓN</td>
                        <td class="sinpadding" width="5%">:</td>
                        <td class="sinpadding" width="85%">{{ $cliente->direccion }}</td>
                      </tr>

                      <tr>
                        <td class="sinpadding" width="10%">CORREO</td>
                        <td class="sinpadding" width="5%">:</td>
                        <td class="sinpadding" width="85%">{{ $cliente->correo }}</td>
                      </tr>

                      <tr>
                        <td class="sinpadding" width="10%">CELULAR</td>
                        <td class="sinpadding" width="5%">:</td>
                        <td class="sinpadding" width="85%">{{ $cliente->celular }}</td>
                      </tr>
                     
                    </table>
                  </td>
                  <td width="20%" class="sinpadding">

                  <table class="tdatos" > 
                      <tr>
                        <td class="sinpadding negrita" >COTIZACION</td>
                      </tr>
                      <tr>
                        <td class="sinpadding" >NRO: {{ $cotizacion->lote }}</td>
                      </tr>
                      
                       <tr>
                        <td class="sinpadding"></td>
                       </tr>
                      <tr>
                         <td class="sinpadding negrita" >FECHA</td>
                        </tr>
                      <tr>
                        <td class="sinpadding" >{{ $cotizacion->fecha }}</td>
                      </tr>
                      


                     
                    </table>
                  </td>
                </tr>
            </table>
          </div>
        <div class="row mt10">
          <div class="col-md-10 col-md-offset-1" >
              <span class="titulotabla">
                Por medio de la presente ante ud. nuestra cotizacion de servicio requerido.
              </span>
          </div>
        </div>

         <div class="row">
          <div class="col-md-10 col-md-offset-1" >
              <table class="tservicios" width="70%" border="1">
                <thead>
                    <tr>
                      <td>N°</td>
                      <td>DESCIPCION</td>
                      <td>UNIDAD</td>
                      <td>CANTIDAD</td>
                      <td>P.UNITARIO S/.</td>
                      <td>SUB.TOTAL S/.</td>
                    </tr>
                </thead>
                <tbody>
                      @foreach($detallecotizacion as $index => $detalle)
                        <tr>
                          <td>{{ $detalle->codigo }}</td>
                          <td>{{ $detalle->descripcion }}</td>
                          <td>{{ $detalle->Unidad->aux01 }}</td>

                          <td>{{ $detalle->cantidad }}</td>
                          <td class="monto">{{ number_format($detalle->precio_unitario, 2, '.', ',') }}</td>
                          <td class="monto">{{ number_format($detalle->total, 2, '.', ',') }}</td>

                        </tr>
                      @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">TOTAL</td>
                        <td class="monto">{{ number_format($cotizacion->total, 2, '.', ',') }}</td>
                    </tr>
                </tfoot>
              </table>
          </div>
        </div>
        {{-- </div> --}}
      {{-- </div> --}}

          <div class="row">
            <div class="col-md-10 col-md-offset-1 divnotas" >
                <span class="negrita">NOTA:</span>
                <span>
                  <pre>{{ $cotizacion->notas }}</pre>
                </span>
            </div>
          </div>
          <div class="row">
            <div class="col-md-10 col-md-offset-1 divcondiciones" >
                <span class="negrita">CONDICIONES:</span>
                <span>
                  <pre>{{ $cotizacion->condiciones }}</pre>
                </span>
            </div>
          </div>

         <div class="row">
          <div class="col-md-10 col-md-offset-1" >
              <table class="tcuentas" width="70%" border="1">
                <thead>
                    <tr>
                      <td colspan="4" class="ttserv">DATOS BANCARIOS</td>
                    </tr>
                </thead>
                <thead>
                    <tr>
                      <td>Nombre de la Entidad Financiera</td>
                      <td>N° de Cuenta Corriente</td>
                      <td>N° del CCI (20 digitos)</td>
                      <td>Tipo de Cuenta (Soles y/o Dolares)</td>
                    </tr>
                </thead>
                <tbody>
                      @foreach($cuentas as $i => $cuenta)
                        <tr>
                          <td>{{ $cuenta->Entidad->entidad }}</td>
                          <td>{{ $cuenta->nrocta }}</td>
                          <td>{{ $cuenta->nroctacci }}</td>
                          <td>{{ $cuenta->moneda->descripcionabreviada }}</td>
                        </tr>
                      @endforeach
                      <tr>
                        <td colspan="4" class="ttserv">N° de la Cuenta de Detracción</td>
                      </tr>
                      <tr>
                        <td colspan="4" >BANDO DE LA NACIÓN : {{ $empresa->ctadetraccion }}</td>
                      </tr>
                </tbody>
                

              </table>
          </div>
        </div>

    </div>

    <div class="footer">
        <img src="{{ public_path('/img/empresa/footer.jpg') }}" alt="Pie de página">
    </div>


  </div>
</div>



</body>
</html>