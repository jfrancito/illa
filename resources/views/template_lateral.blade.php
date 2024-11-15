<!doctype html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="Sistemas de Ventas">
		<meta name="author" content="Jorge Francelli Saldaña Reyes">
		<link rel="icon" href="{{ asset('public/img/icono/illa2.ico') }}">
		<title>ILLA - {{$titulo}}</title>

		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.gritter/css/jquery.gritter.css?v='.$version) }}">

		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/perfect-scrollbar/css/perfect-scrollbar.min.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/material-design-icons/css/material-design-iconic-font.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/css/font-awesome.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/scroll/css/scroll.css') }} "/>

		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->


		@yield('style')
		<link rel="stylesheet" type="text/css" href="{{ asset('public/css/jquery-confirm.min.css') }} "/>

		<link rel="stylesheet" type="text/css" href="{{ asset('public/css/style.css?v='.$version) }} "/>

		
		<link rel="stylesheet" type="text/css" href="{{ asset('public/css/meta.css?v='.$version) }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/css/mainapp.css?v='.$version) }} " />
	</head>
	<body class='fuente-muktabold'>


		<div class="be-wrapper be-fixed-sidebar">

				@include('success.ajax-alert')
				@include('success.bienhecho', ['bien' => Session::get('bienhecho')])
				@include('error.erroresurl', ['error' => Session::get('errorurl')])
				@include('error.erroresbd', ['error' => Session::get('errorbd')])

				@include('menu.nav-top')
				@include('menu.nav-left')

				@include('success.xml', ['xml' => Session::get('xmlmsj')])

				@yield('section')

				 <input type='hidden' id='carpeta' value="{{$capeta}}"/>
				 <input type="hidden" id="token" name="_token"  value="{{ csrf_token() }}"> 
		</div>


		<script src="{{ asset('public/lib/jquery/jquery-2.1.3.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/js/main.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/scroll/js/jquery.mousewheel.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/scroll/js/jquery-scrollpanel-0.7.0.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/scroll/js/scroll.js') }}" type="text/javascript"></script>   
		<script src="{{ asset('public/js/general/general.js?v='.$version) }}" type="text/javascript"></script>
		<script src="{{ asset('public/js/general/gmeta.js?v='.$version) }}" type="text/javascript"></script>
		<script src="{{ asset('public/js/general/jquery-confirm.min.js?v='.$version) }}" type="text/javascript"></script>

		<script src="{{ asset('public/lib/jquery.gritter/js/jquery.gritter.js?v='.$version) }}" type="text/javascript"></script>
		<script src="{{ asset('public/js/app-ui-notifications.js?v='.$version) }}" type="text/javascript"></script>

	<script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
	<script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>


		@yield('script')
		
		<script type="text/javascript">
	      $(document).ready(function(){
	        //initialize the javascript
	        
	        $('.importe').inputmask({ 'alias': 'numeric', 
	          'groupSeparator': '', 'autoGroup': true, 'digits': 2, 
	          'digitsOptional': false, 
	          'prefix': '', 
	          'placeholder': '0'});

	        $('.unidad').inputmask({ 'alias': 'numeric', 
	          'groupSeparator': '', 'autoGroup': true, 'digits': 0, 
	          'digitsOptional': false, 
	          'prefix': '', 
	          'placeholder': '0'});
	        

	      });
	    </script>

	</body>
</html>