@extends('template_lateral')

@section('style')
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('section')
	<div class="be-content contenido" style="height: 100vh;">
		<div class="main-content container-fluid">
				<div class='container'>
					<div class="row">

								<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">

									<div class="card">
										<div class="card-body">
											<ul class="list-group list-group-flush">
												<li class="list-group-item negrita"><h3>TIPO CAMBIO </h3> <h5>[{{ date('d-m-Y') }}]</h5></li>

											@if(isset($tipocambio))
												<li class="list-group-item"><b>Tipo de Cambio Obtenido de la Sunat</b></li>
												<li class="list-group-item dflex spbtflex"><b>Compra: </b> {{ number_format($tipocambio->compra,3,'.',',')}}</li>
												<li class="list-group-item dflex spbtflex"><b>Venta: </b> {{ number_format($tipocambio->venta,3,'.',',')}}</li>
											</ul>
											@else
												<li class="list-group-item">
													<a href="{{ url('/obtenertipocambio') }}"><button class="form-control input-sm"> Obtener Tipo Cambio</button></a>
												</li>
											@endif
										</div>
									</div>
								</div>

					</div>
			</div>
		</div>
	</div>
@stop 
@section('script')

		<script src="{{ asset('public/lib/jquery-flot/jquery.flot.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/jquery.flot.pie.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/jquery.flot.resize.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/plugins/jquery.flot.orderBars.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/plugins/curvedLines.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery.sparkline/jquery.sparkline.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/countup/countUp.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jqvmap/jquery.vmap.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jqvmap/maps/jquery.vmap.world.js') }}" type="text/javascript"></script>

		<script type="text/javascript">
			$(document).ready(function(){
				App.init();
				// App.dashboard();
			});
		</script>   

@stop
