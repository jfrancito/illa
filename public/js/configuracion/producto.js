$(document).ready(function(){

	var carpeta = $("#carpeta").val();

	$(".formagregarproducto").on('change','#categoria_id', function() {
		debugger;
		// alerterrorajax('CLICK EN CATEGORIA CHANGE');
		var _token					=	$('#token').val();
		var categoriaproducto		=	$('#categoria_id').select2('data');
		var categoria_id			=	$('#categoria_id').val();
		var cadcategoriaproducto	=	'';
		if(categoriaproducto)
		{
			categoria_id   =   categoriaproducto[0].id;
			if(categoria_id=='CATP00000001'){
				$('.formagregarproducto .datosproducto').show(200);
			}
			else{
				$('.formagregarproducto .datosproducto').hide(200);
			}

			cadcategoriaproducto    =   categoriaproducto[0].text;
			
			$.ajax({
					type    :   "POST",
					url     :   carpeta+"/ajax-cargar-sub-categorias-productos",
					data    :   {
									_token  : _token,
									categoria_id : categoria_id,
								},
					success: function (data) {
						// $(".formagregarproducto #numerodocumento").val(data);
						$(".formagregarproducto .subcategoriaproducto").html(data);
					},
					error: function (data) {
						console.log('Error:', data);
					}
			});
		}
		// return false;        
	});

	$(".ajaxubigeo").on('change','#departamento_id', function() {
		debugger;
		var departamento_id = $('#departamento_id').val();
		var _token 		= $('#token').val();

		$.ajax({
			
			type	: 	"POST",
			url		: 	carpeta+"/ajax-select-provincia",
			data	: 	{
							_token	: _token,
							departamento_id : departamento_id
						},
			success: function (data) {

				$(".ajaxprovincia").html(data);
			},
			error: function (data) {

				console.log('Error:', data);
			}
		});
	});

	$(".ajaxubigeo").on('change','#provincia_id', function() {

		var provincia_id = $('#provincia_id').val();

		var _token 		= $('#token').val();

		$.ajax({

			type	: 	"POST",
			url		: 	carpeta+"/ajax-select-distrito",
			data	: 	{
							_token	: _token,
							provincia_id : provincia_id
						},
			success: function (data) {

				$(".ajaxdistrito").html(data);
			},
			error: function (data) {

				console.log('Error:', data);
			}
		});
	});


});