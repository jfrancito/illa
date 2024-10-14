$(document).ready(function(){


	var carpeta = $("#carpeta").val();

    $('#buscarreportecompra').on('click', function(event){

    	event.preventDefault();
    	var finicio 	= $('#finicio').val();
    	var ffin 		= $('#ffin').val();
        var proveedor   = $('#proveedor_id').val();
        debugger;
    	var _token 		= $('#token').val();
		$(".listatablacompra").html("");
		abrircargando();
		
		$(".menu-roles li").removeClass( "active" )
		$(this).parents('li').addClass("active");

        $.ajax({
            
            type	: 	"POST",
            url		: 	carpeta+"/ajax-reporte-de-compras-entrefechas-proveedor",
            data	: 	{
            				_token	      : _token,
            				finicio       : finicio,
            				ffin 	      : ffin,
                            proveedor     : proveedor
            	 		},
            success: function (data) {
            	cerrarcargando();
            	$(".listatablacompra").html(data);

            },
            error: function (data) {
            	cerrarcargando();
                console.log('Error:', data);
            }
        });

    });	




});