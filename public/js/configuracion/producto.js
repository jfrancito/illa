$(document).ready(function(){

	var carpeta = $("#carpeta").val();

	$(".formagregarproducto").on('change','#categoria_id', function() {

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

			if(categoria_id=='CATP00000007'){
				$('.formagregarproducto .datosbienesproducidos').show(200);
				$('.formagregarproducto .datosproductogemas').show(200);				
			}
			else{
				$('.formagregarproducto .datosbienesproducidos').hide(200);				
				$('.formagregarproducto .datosproductogemas').hide(200);
			}

			cadcategoriaproducto    =   categoriaproducto[0].text;
			debugger;
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

	$(".producto").on('click','.agregaproductogema', function() {

        var _token                  =   $('#token').val();
        var producto_id     				=   $(this).attr('data_producto_id');        
        var categoria_id						=		$('#categoria_id').val();
        var idopcion                =   $('#idopcion').val();

        if(categoria_id !='CATP00000007'){ alerterrorajax("No puede agregar Gemas a este tipo de Producto."); return false;}

        data                        =   {
                                            _token                  : _token,
                                            producto_id     				: producto_id,
                                            idopcion                : idopcion
                                        };
                                        
        ajax_modal(data,"/ajax-modal-producto-gema",
                  "modal-producto-gema","modal-producto-gema-container");

  });

  $(".listaproducto").on('click','.buscarlistaproducto', function() {
		
		event.preventDefault();		
		debugger;
		var activo      = $('#activo').val();
		var idopcion    = $('#idopcion').val();
		
		var _token      = $('#token').val();
		$(".listatablaproductos").html("");
		abrircargando();
		
		$(".menu-roles li").removeClass( "active" )
		$(this).parents('li').addClass("active");

		$.ajax({
			
			type    :   "POST",
			url     :   carpeta+"/ajax-gestion-de-productos-filtro",
			data    :   {
							_token        : _token,
							idopcion      : idopcion,
							activo       	: activo							
						},

			success: function (data) {
				cerrarcargando();
				
				$(".listatablaproductos").html(data);

			},
			error: function (data) {
				cerrarcargando();
				console.log('Error:', data);
			}
		});
	});
	
  click_boton_categoria();
  function click_boton_categoria(){
  		var categoriaproducto		=	$('#categoria_id').select2('data');
    	categoria_id   =   categoriaproducto[0].id;
			if(categoria_id=='CATP00000001'){
				$('.formagregarproducto .datosproducto').show(200);
			}
			else{
				$('.formagregarproducto .datosproducto').hide(200);				
			}

			if(categoria_id=='CATP00000007'){
				$('.formagregarproducto .datosbienesproducidos').show(200);
				$('.formagregarproducto .datosproductogemas').show(200);				
			}
			else{
				$('.formagregarproducto .datosbienesproducidos').hide(200);				
				$('.formagregarproducto .datosproductogemas').hide(200);
			}
  }

  $(".producto").on('click','.btn-guardar-producto-gema', function() {

      var tipogema_id                  =   $('#tipogema_id').val();      
      var cantidad                     =   $('#cantidad').val();

      //validacioones
      if(tipogema_id ==''){ alerterrorajax("Seleccione una Gema."); return false;}      
      if(cantidad =='' || cantidad ==0){ alerterrorajax("Ingrese una cantidad."); return false;}

      return true;

  });

  $(".producto").on('click','.btnguardarproducto', function() {
        event.preventDefault()
       
        var unidad_medida_id     		=   $('#unidad_medida_id').val();        
        var categoria_id     				=   $('#categoria_id').val();        
        var formclass               =   'formagregarproducto';
debugger;
        if(unidad_medida_id =='' && categoria_id == 'CATP00000001'){ alerterrorajax("Ingrese Unidad de Medida."); return false;}

        $('.'+formclass).submit();
        return true;

    });
});