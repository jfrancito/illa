@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>

    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.magnific-popup/magnific-popup.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/archivos.css?v='.$version) }} "/>

@stop
@section('section')

<div class="be-content">
  <div class="main-content container-fluid">

    <!--Basic forms-->
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default panel-border-color panel-border-color-primary">
          <div class="panel-heading panel-heading-divider">{{ $idmodal }}<span class="panel-subtitle">{{ $titulo }} : {{$registro->lote}}</span></div>
          <div class="panel-body">
            <form id='formagregararchivos' name="formagregararchivos" method="POST" action="{{ url('/agregar-archivos-'.$url.'/'.$idopcion.'/'.Hashids::encode(substr($registro->id, -8))) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed" enctype="multipart/form-data">
                  {{ csrf_field() }}
              @include($view.'.form.archivos')
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>  
@stop
@section('script')
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
    <script src="{{ asset('public/js/archivos.js?v='.$version) }}" type="text/javascript"></script>

    <script type="text/javascript">
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

  <script src="{{ asset('public/js/venta/precotizacion.js?v='.$version) }}" type="text/javascript"></script>

@stop