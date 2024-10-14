@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.magnific-popup/magnific-popup.css') }} "/>

@stop
@section('section')

<div class="be-content ventacotizar">
  <div class="main-content container-fluid">

    <!--Basic forms-->
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default panel-border-color panel-border-color-primary">
          <div class="panel-heading panel-heading-divider">Detalle Cotizacion

            <div class="tools tooltiptop">
             
            </div>

            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <span class="panel-subtitle">Cotizacion : {{$cotizacion->lote}}</span>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <span class="panel-subtitle">Estado &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{$cotizacion->estado_descripcion}}</span>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <span class="panel-subtitle">Moneda &nbsp;&nbsp;&nbsp;&nbsp;: {{$cotizacion->moneda->descripcionabreviada}}</span>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <span class="panel-subtitle">T.Cambio &nbsp;&nbsp;: {{number_format($cotizacion->tcambio,3,'.',',')}}</span>
              </div>
            </div>

            
            <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
          </div>
          <div class="panel-body">
                <input type="hidden" id='idcategoria' name='idcategoria' >
                <div class="tab-container">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#cotizar" data-toggle="tab">Cotizar</a></li>
                    <li><a href="#precotizar" data-toggle="tab">Requerimiento</a></li>
                    <li class="disabled"><a href="#analizar" data-toggle="tab">Analizar</a></li>
                  </ul>
                  <div class="tab-content">
                    
                    <div id="cotizar" class="tab-pane active cont">
                        @include('cotizacion.form.fdetalle')
                    </div>
                    
                    <div id="precotizar" class="tab-pane cont">
                        @include('precotizacion.vista.data')
                    </div>
                    
                    <div id="analizar" class="tab-pane cont analizar">

                    </div>
                  </div>
                </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- @include('cotizacion.modal.mcotizacion') --}}

</div>  

@stop

@section('script')

    <script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>


    <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/jszipoo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/pdfmake.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/vfs_fonts.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.html5.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.flash.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.print.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.colVis.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-tables-datatables.js?v='.$version) }}" type="text/javascript"></script>

    <script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.nestable/jquery.nestable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/moment.js/min/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/bootstrap-slider/js/bootstrap-slider.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-form-elements.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/parsley/parsley.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.niftymodals/dist/jquery.niftymodals.js') }}" type="text/javascript"></script>

    <script src="{{ asset('public/lib/jquery.magnific-popup/jquery.magnific-popup.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/masonry/masonry.pkgd.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-page-gallery.js') }}" type="text/javascript"></script>


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
      });

      $(window).on('load',function(){
        App.pageGallery();
      });


    </script> 

    <script src="{{ asset('public/js/venta/detallecotizacion.js?v='.$version) }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/venta/actualizartabla.js?v='.$version) }}" type="text/javascript"></script>

@stop