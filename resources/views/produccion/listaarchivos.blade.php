@extends('template_lateral')
@section('style')
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/css/archivos.css?v='.$version) }} "/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

@stop
@section('section')

	<div class="be-content contenido asientomodelo">
		<div class="main-content container-fluid">
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default panel-border-color panel-border-color-success">
								<div class="panel-heading panel-heading-divider">
									{{ $idmodal }}
									<span class="panel-subtitle">{{ $titulo }} : {{$registro->lote}}</span>
									<span class="panel-subtitle">Cliente : {{$registro->cliente_nombre}}</span>
									<span class="panel-subtitle">Descripcion : {{$registro->descripcion}}</span>
								</div>
									<div class="panel-heading">Lista Archivos : ( {{ $tmusados }} / {{ $tmlimite }} ) MB | {{ count($listaarchivos) }} Archivos
										<div class="tools tooltiptop">
											 <a href="{{ url('/gestion-de-requerimiento/'.$idopcion) }}" class="tooltipcss">
												<span class="tooltiptext">Atras</span>
												<span class="icon mdi mdi-mail-reply"></span>
											</a>
										</div>
									</div>
									@if($registro->estado_id<>'1CIX00000004')
										<div class="row">
											<div class="col-sm-12 col-lg-6">
												<div id="accordionfiles" class="panel-group accordion">
													<div class="panel panel-full-default">
														<div class="panel-heading">
															<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordionfiles" href="#subirArchivos" aria-expanded="false" 
																class="collapsed"><i class="icon mdi mdi-chevron-down"></i> <b>Subir Archivos</b></a></h4>
														</div>
														<div id="subirArchivos" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
															<div class="panel-body">
																	@include('requerimiento.files')
															</div>
														</div>
													</div>
													
												</div>
											</div>
											<div class="col-sm-12 col-lg-6">
												<div id="accordionfilesclonar" class="panel-group accordion">
													<div class="panel panel-full-default">
														<div class="panel-heading">
															<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordionfilesclonar" href="#clonarArchivos" aria-expanded="false" 
																class="collapsed"><i class="icon mdi mdi-chevron-down"></i> <b>Clonar Archivos</b></a></h4>
														</div>
														<div id="clonarArchivos" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
															<div class="panel-body">
																	@include('requerimiento.filesclonar')
															</div>
														</div>
													</div>
													
												</div>
											</div>
										</div>
									@endif
									<div class="panel-body">
										<div class='filtrotabla row'>
											<div class="col-xs-12">
												<input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
											</div>
										</div>
										<div class='listajax'>
											@include($view.'.ajax.alistaarchivos')
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

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
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
	<script src="{{ asset('public/js/venta/'.$ruta.'.js?v='.$version) }}" type="text/javascript"></script>
 <script src="{{ asset('public/js/archivos.js?v='.$version) }}" type="text/javascript"></script>
		<script src="{{ asset('public/js/venta/precotizacion.js?v='.$version) }}" type="text/javascript"></script>

@stop