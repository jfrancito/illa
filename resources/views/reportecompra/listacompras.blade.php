@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class="be-content contenido asientomodelo">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-border-color panel-border-color-success">
                <div class="panel-heading">Lista de Documentos Compras
                  <div class="tools tooltiptop">
                    <a href="#" class="tooltipcss" id='buscarreportecompra' >
                      <span class="tooltiptext">Buscar Documentos Compras</span>
                      <span class="icon mdi mdi-search"></span>
                    </a>
                  </div>
                </div>
                <div class="panel-body">

                  <div class='filtrotabla row'>
                    <div class="col-xs-12">
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

                      <div class="col-sm-5">
                        <div class="form-group">
                          <label class="control-label" style="padding-left: 16px;">Proveedor</label>
                          <div class="col-sm-12">
                            {!! Form::select( 'proveedor_id', $combo_proveedor, $select_proveedor,
                                              [
                                                'class'       => 'form-control control input-sm select2' ,
                                                'id'          => 'proveedor_id',
                                                'required'    => '',         
                                                'data-aw'     => '3'
                                              ]) !!}

                            @include('error.erroresvalidate', [ 'id' => $errors->has('proveedor_id')  , 
                                                                'error' => $errors->first('proveedor_id', ':message') , 
                                                                'data' => '3'])
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>

                  <div class='listatablacompra listajax'>
                    @include('reportecompra.ajax.alistacompra')
                  </div>


                </div>
              </div>
            </div>
          </div>
		</div>
	</div>

@stop

@section('script')


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
  <script src="{{ asset('public/lib/datatables/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/responsive.bootstrap.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('public/js/app-tables-datatables.js') }}" type="text/javascript"></script>


    <script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.nestable/jquery.nestable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/moment.js/min/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/bootstrap-slider/js/bootstrap-slider.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-form-elements.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/parsley/parsley.js') }}" type="text/javascript"></script>



    <script type="text/javascript">
      $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.formElements();
        App.dataTables();
      });
    </script> 

 
  <script src="{{ asset('public/js/compra/reportecompra.js') }}" type="text/javascript"></script>     
@stop