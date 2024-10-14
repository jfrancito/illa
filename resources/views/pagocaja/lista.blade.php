@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class="be-content contenido listaventa">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-border-color panel-border-color-success">
                <div class="panel-heading">Lista de Pagos 
                  <input type="hidden" name="idmodal" id='idmodal' class="idmodal" value="{{ $idmodal }}" >
                  <div class="tools tooltiptop">

<!--                     <a href="{{ url('/agregar-ventas/'.$idopcion) }}" class="tooltipcss">
                      <span class="tooltiptext">Crear Pago</span>
                      <span class="icon mdi mdi-plus-circle-o"></span>
                    </a>     -->                
                    <a href="#" class="tooltipcss buscarlistaventa">
                      <span class="tooltiptext">Buscar</span>
                      <span class="icon mdi mdi-search"></span>
                    </a>
                  </div>
                </div>

                <div class="panel-body">
                  <div class='filtrotabla row'>

                    <div class="col-xs-12">
                      <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>

                      <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
                          <div class="form-group">
                            <label class="col-sm-12 control-label">
                              Fecha Inicio
                            </label>
                            <div class="col-sm-12">
                              <div data-min-view="2" data-date-format="dd-mm-yyyy" class="input-group date datetimepicker">
                                        <input size="16" type="text" value="{{$inicio}}" id='finicio' name='finicio' class="form-control input-sm">
                                        <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                              </div>
                            </div>
                          </div>
                      </div>

                      <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
                        <div class="form-group">
                          <label class="col-sm-12 control-label">
                            Fecha Fin
                          </label>
                          <div class="col-sm-12">
                            <div data-min-view="2" data-date-format="dd-mm-yyyy"  class="input-group date datetimepicker">
                                      <input size="16" type="text" value="{{$fin}}" id='ffin' name='ffin' class="form-control input-sm">
                                      <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
                        <div class="form-group">
                          <label class="control-label" style="padding-left: 16px;">Cliente</label>
                          <div class="col-sm-12">
                            {!! Form::select( 'cliente_id', $combo_cliente, $select_cliente,
                                              [
                                                'class'       => 'form-control control input-sm select2' ,
                                                'id'          => 'cliente_id',
                                                'required'    => '',         
                                                'data-aw'     => '3'
                                              ]) !!}

                            @include('error.erroresvalidate', [ 'id' => $errors->has('cliente_id')  , 
                                                                'error' => $errors->first('cliente_id', ':message') , 
                                                                'data' => '3'])
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
                        <div class="form-group">
                          <label class="control-label" style="padding-left: 16px;">Estado</label>
                          <div class="col-sm-12">
                            {!! Form::select( 'estado_id', $combo_estado, $select_estado,
                                              [
                                                'class'       => 'form-control control input-sm select2' ,
                                                'id'          => 'estado_id',
                                                'required'    => '',         
                                                'data-aw'     => '4'
                                              ]) !!}

                            @include('error.erroresvalidate', [ 'id' => $errors->has('estado_id')  , 
                                                                'error' => $errors->first('estado_id', ':message') , 
                                                                'data' => '4'])
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- <div class="col-xs-12 widget-chart-container">
                      <div class="widget-chart-info">
                        <ul class="chart-legend-horizontal">
                          <li><span class="main-chart-color1"></span> Activo</li>
                          <li><span class="main-chart-color2"></span> Inactivo</li>
                        </ul>
                      </div>
                    </div> -->
                  </div>
                  <div class='listatablaventas listajax'>
                    @include('pagocaja.ajax.alista')
                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
    {{-- @include('cobrarcaja.modal.memitirventa') --}}
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
      App.dataTables();
      $('[data-toggle="tooltip"]').tooltip();
      $('form').parsley();

    });

  </script>
  <script src="{{ asset('public/js/cobrarcaja/app.js?v='.$version) }}" type="text/javascript"></script>


  

@stop