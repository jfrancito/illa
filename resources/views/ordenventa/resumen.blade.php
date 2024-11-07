@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>

@stop
@section('section')

<div class="be-content venta ordenventa">
  <div class="main-content container-fluid">

    <!--Basic forms-->
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default panel-border-color panel-border-color-primary">
          <div class="panel-heading panel-heading-divider">Orden Venta
          <div class="tools tooltiptop">
            <a href="#" class="tooltipcss opciones agregadetalleregistro"
              data_registro_id = '{{$registro->id}}'
              data_registro_estado_id = '{{$registro->estado_id}}'>
              <span class="tooltiptext">Agregar producto</span>
              <span class="icon mdi mdi-plus-circle-o"></span>              
            </a>
          </div>
          <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
          <span class="panel-subtitle">Resumen de la Orden Venta</span></div>
          <div class="panel-body">
          <div class="row">
            <!--Default Tabs-->
            <div class="col-sm-12">
              <div class="panel panel-default">
                <div class="tab-container">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#modificar" data-toggle="tab">OV</a></li>
                    <li><a href="#produccion" data-toggle="tab">Produccion</a></li>
                    <li><a href="#margen" data-toggle="tab">Margenes</a></li>
                    <li><a href="#referencia" data-toggle="tab">Referencia</a></li>
                  </ul>
                  <div class="tab-content">
                    <div id="modificar" class="tab-pane active cont">
                        <form style="border-radius: 0px;" class="form-horizontal group-border-dashed">                              
                          @include('ordenventa.form.formulario',
                                  [
                                    'swmodidicar'=>false,
                                    'select_moneda' => $registro->moneda_id
                                  ]
                                  )
                        </form>
                    </div>
                    <div id="produccion" class="tab-pane cont">
                      <div class='formconsulta'>
                        @include('ordenventa.form.formularioesquemaov',['swmodidicar'=>true])                        
                      </div>
                    </div>
                    <div id="margen" class="tab-pane">
                      <div class='formconsulta'>
                        @include('ordenventa.form.formulariomargenov',['swmodidicar'=>true])                        
                      </div> 
                    </div>
                    <div id="referencia" class="tab-pane">
                      <div class="row">
                        <!--Default Tabs-->
                        <div class="col-sm-12">
                          <div class="panel panel-default">
                            <div class="tab-container">
                              <ul class="nav nav-tabs">
                                <li class="active"><a href="#compra" data-toggle="tab">Compra</a></li>
                                <li><a href="#venta" data-toggle="tab">Venta</a></li>          
                              </ul>
                              <div class="tab-content">
                                <div id="compra" class="tab-pane active cont">

                                    @if(count($compra)>0)
                                      <form style="border-radius: 0px;" class="form-horizontal group-border-dashed">
                                        @include('compra.form.fcompra', 
                                                [
                                                  'select_moneda' => $compra->moneda_id,
                                                  'select_tipo_comprobante' => $compra->tipo_comprobante_id
                                                ]
                                                )
                                      </form>                                      
                                    @else
                                      No hay compra
                                    @endif

                                    
                                </div>
                                <div id="venta" class="tab-pane cont">
                                    @if(count($venta)>0)
                                      <form style="border-radius: 0px;" class="form-horizontal group-border-dashed">
                                        @include('venta.form.fventa',
                                                [
                                                  'select_moneda' => $venta->moneda_id,
                                                  'select_tipo_comprobante' => $venta->tipo_comprobante_id
                                                ]
                                                )
                                      </form>
                                    @else
                                      No hay venta
                                    @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('ordenventa.modal.mdetalleregistro')
</div>  



@stop

@section('script')

    <script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>

    <script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.nestable/jquery.nestable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/moment.js/min/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/bootstrap-slider/js/bootstrap-slider.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-form-elements.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/parsley/parsley.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.niftymodals/dist/jquery.niftymodals.js') }}" type="text/javascript"></script>

    <script type="text/javascript">

      $.fn.niftyModal('setDefaults',{
        overlaySelector: '.modal-overlay',
        closeSelector: '.modal-close',
        classAddAfterOpen: 'modal-show',
      });


      $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.formElements();
        $('form').parsley();
        
        $('.importe').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'});       


      });
    </script> 

    <script src="{{ asset('public/js/ordenventa/ordenventa.js?v='.$version) }}" type="text/javascript"></script>    
@stop