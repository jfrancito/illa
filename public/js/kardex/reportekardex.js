$(document).ready(function(){


	var carpeta = $("#carpeta").val();

    $('#buscarreportekardex').on('click', function(event){

    	event.preventDefault();
    	var finicio 	= $('#finicio').val();
    	var ffin 		= $('#ffin').val();
        var almacen     = $('#almacen_id').val();
        var producto    = $('#producto_id').val();
        debugger;
    	var _token 		= $('#token').val();
		$(".listatablakardex").html("");
		abrircargando();
		
		$(".menu-roles li").removeClass( "active" )
		$(this).parents('li').addClass("active");

        $.ajax({
            
            type	: 	"POST",
            url		: 	carpeta+"/ajax-reporte-de-kardex-entrefechas",
            data	: 	{
            				_token	      : _token,
            				finicio       : finicio,
            				ffin 	      : ffin,
                            almacen       : almacen,
                            producto      : producto
            	 		},

            success: function (data) {
            	cerrarcargando();
                
            	$(".listatablakardex").html(data);

            },
            error: function (data) {
            	cerrarcargando();
                console.log('Error:', data);
            }
        });

    });	




});