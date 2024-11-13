$(document).ready(function(){

	var carpeta = $("#carpeta").val();

	function aprobarOrdenVenta(data,registro_id,_token,idopcion)
	{
	    debugger;
	    abrircargando();
	    $.ajax({
	        type    :   "POST",
	        url     :   carpeta+'/ajax-aprobar-orden-venta',
	        data    :   data,
	        success: function (data) {
	            $('.listaventa').html(data);
	            cerrarcargando();
	        },
	        error: function (data) {
	            cerrarcargando();
	            error500(data);
	        }
	    });
	}
	
	$(".listaventa").on('click', '.validarordenventa', function(event) {
	    event.preventDefault(); // Evita la navegación automática del enlace
	    debugger;
	    var href = $(this).attr('href'); // Obtiene la URL del enlace
	    var codigo = $(this).attr('data_codigo'); // Obtiene el código de la orden de venta
	    
	    $.confirm({
	        title: '¿Confirma la Validación?',
	        content: 'Confirmar Valicación de Orden de Venta ' + codigo,
	        buttons: {
	            confirmar: function () {
	                abrircargando('VALIDANDO ORDEN DE VENTA'); // Muestra el cargando
	                window.location.href = href; // Redirige a la URL original
	            },
	            cancelar: function () {
	                $.alert('Se canceló la validación'); // Muestra un mensaje de cancelación
	            }
	        }
	    });
	    
	    return false; // Evita la navegación inmediata del enlace
	});

	$(".listaventa").on('click', '.aprobarordenventa', function(event) {
	    event.preventDefault(); // Evita la navegación automática del enlace
	    debugger;
	    var href = $(this).attr('href'); // Obtiene la URL del enlace
	    var codigo = $(this).attr('data_codigo'); // Obtiene el código de la orden de venta
	    
	    $.confirm({
	        title: '¿Confirma la Aprobación?',
	        content: 'Confirmar Aprobación de Orden de Venta ' + codigo,
	        buttons: {
	            confirmar: function () {
	                abrircargando('APROBANDO ORDEN DE VENTA'); // Muestra el cargando
	                window.location.href = href; // Redirige a la URL original
	            },
	            cancelar: function () {
	                $.alert('Se canceló la aprobación'); // Muestra un mensaje de cancelación
	            }
	        }
	    });
	    
	    return false; // Evita la navegación inmediata del enlace
	});


	$(".venta").on('click','.agregadetalleregistro', function() {
		debugger;
		var _token                  =   $('#token').val();
		var registro_id     			=   $(this).attr('data_registro_id');
		var registro_estado_id        =   $(this).attr('data_registro_estado_id');
		var idopcion                =   $('#idopcion').val();
		
		if(registro_estado_id !='1CIX00000003'){ alerterrorajax("No puede agregar Productos a un Registro Emitido."); return false;}

		data                        =   {
											_token                  : _token,
											registro_id     		: registro_id,
											idopcion                : idopcion
										};
										
		ajax_modal(data,"/ajax-modal-detalle-orden-venta",
				  "modal-detalle-registro","modal-detalle-registro-container");

	});

	$('.listaventa').on('click','.emitirventa', function(){
		
		// datae = dataenviar();
		debugger;
		var _token                  =   $('#token').val();
		var idopcion                =   $('#idopcion').val();
		var idventa                =   $(this).attr('data_idventa');
	
		// if(datae.length<=0){alerterrorajax("Seleccione por lo menos una compra");return false;}
		// var compra = JSON.stringify(datae);

		data                        =   {
											_token        : _token,                                            
											idopcion      : idopcion,
											idventa       : idventa,
										};
		
		ajax_modal(data,"/ajax-modal-emitir-venta",
			  "modal-emitir-venta","modal-emitir-venta-container");        

	});

	function dataenviar(){
			var data = [];
			$(".listatabla tr").each(function(){                
				check   = $(this).find('input');
				nombre  = $(this).find('input').attr('id');
				if(nombre != 'todo'){
					if($(check).is(':checked')){                        
						data.push({id: $(check).attr("id")});
					}               
				}
			});
			return data;
	}

	$('.venta').on('change','#ckindigv',function (e) {
		// debugger;
		var valor   =   ($(this).prop('checked'));

		if(valor==true){
			// $('.venta #porcigv').val(18);
			$('.venta #porcigv').val(18).prop('disabled', false); // Habilitar y establecer valor

			$('.venta #indigv').val(1);
		}
		else{
			$('.venta #porcigv').val(0).prop('disabled', true); // Habilitar y establecer valor;
			$('.venta #indigv').val(0);
		}
		CalcularImporteDetalleCompra();
		// notifybien('muy bien','entro al check change');
	});


	$('.venta').on('keyup','#porcigv',function (e) {
		debugger;
		// var valor   =    $(this).val();
		CalcularImporteDetalleCompra();
		// notifybien('muy bien','entro al check change');
	});

	$(".venta").on('keyup','.seriekeypress', function() {
		var valueserie              =   $(this).val().toUpperCase();
		$(this).val(valueserie);

	});

	$(".venta").on('keyup','#numero', function() {
		var valuennumero              =   $(this).val().replace(/\D/g, '');
		$(this).val(valuennumero);

	});

	$(".venta").on('focusout','#numero', function() {

		var valuennumero              =   $(this).val().toString().padStart(8,0);
		$(this).val(valuennumero);

	});


	$(".venta").on('keyup','#preciounitario , #cantidad', function() {

		// var preciounitario              =   $("#preciounitario").val().replace(",","");
		// var cantidad                    =   $("#cantidad").val().replace(",","");
		// var total                       =   preciounitario*cantidad;
		CalcularImporteDetalleCompra();
		// $('#total').val(total);

	});

	$(".venta").on('click','.btn-guardar-detalle-compra', function() {

		var producto_id                  =   $('#producto_id').val();
		var preciounitario               =   $('#preciounitario').val();
		var cantidad                     =   $('#cantidad').val();

		//validacioones
		if(producto_id ==''){ alerterrorajax("Seleccione un Producto."); return false;}
		if(preciounitario ==''){ alerterrorajax("Ingrese un precio unitario."); return false;}
		if(cantidad ==''){ alerterrorajax("Ingrese una cantidad."); return false;}
		if(preciounitario =='0.00'){ alerterrorajax("Ingrese un precio unitario mayor a 0."); return false;}
		if(cantidad =='0.00'){ alerterrorajax("Ingrese una cantidad mayor a 0."); return false;}


		return true;

	});

	function CalcularImporteDetalleCompra()
	{
		// debugger;
		var preciounitario              =   $(".venta #preciounitario").val().replace(",","");
		var cantidad                    =   $(".venta #cantidad").val().replace(",","");
		var indigv                      =   $('.venta #ckindigv').prop('checked');
		var porcigv                     =   $('.venta #porcigv').val();
		var montoigv                    =   0;
		var subtotal                    =   preciounitario*cantidad;
		if(indigv==1){
			montoigv = (subtotal * porcigv)/100;
		}
		var total                       =   subtotal+ montoigv;

		$('.venta #igv').val(montoigv);
		$('.venta #subtotal').val(subtotal);
		$('#total').val(total);

		// var total                       =   preciounitario*cantidad;


	}
	$('.venta').on('change','.tipo_venta_id',function(event){
		debugger;
		
		var tipo_venta_id = $('#tipo_venta_id').val();
		var _token        = $('#token').val();
		// if(tipo_venta_id!='1CIX00000018'){return false;}

		$.ajax({
			
			type    :   "POST",
			url     :   carpeta+"/ajax-tipo-pago-venta",
			data    :   {
							_token  : _token,
							tipo_venta_id : tipo_venta_id
						},
			success: function (data) {
				$(".ajaxtipopagoventa").html(data);
			},
			error: function (data) {

				console.log('Error:', data);
			}
		});
	});

	$(".listaventa").on('click','.btn-emitir-venta', function() {

		var almacen_id                   =   $('#almacen_id').val();
		var motivo_id                    =   $('#motivo_id').val();
		var tipo_venta_id               =   $('#idtipoventa').val();



		//validacioones
		if(almacen_id =='' && tipo_venta_id != '1CIX00000036'){ alerterrorajax("Seleccione un Almacen."); return false;}
		if(motivo_id ==''){ alerterrorajax("Seleccione un Motivo."); return false;}
		
		return true;

	});

	

	$(".venta").on('change','#tipo_comprobante_id', function() {
		
		var tipo_comprobante_id = $('#tipo_comprobante_id').val();
		var _token              = $('#token').val();

		// if(tipo_comprobante_id!='1CIX00000018'){return false;}

		$.ajax({
			
			type    :   "POST",
			url     :   carpeta+"/ajax-genera-nota-pedido",
			data    :   {
							_token  : _token,
							tipo_comprobante_id : tipo_comprobante_id
						},
			success: function (data) {

				$(".ajaxnotapedido").html(data);
			},
			error: function (data) {

				console.log('Error:', data);
			}
		});
	});

	$(".compra").on('change','#tipo_comprobante_id', function() {
        
        var tipo_comprobante_id = $('#tipo_comprobante_id').val();
        var _token              = $('#token').val();

        // if(tipo_comprobante_id!='1CIX00000018'){return false;}

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-genera-nota-pedido",
            data    :   {
                            _token  : _token,
                            tipo_comprobante_id : tipo_comprobante_id
                        },
            success: function (data) {

                $(".ajaxnotapedido").html(data);
            },
            error: function (data) {

                console.log('Error:', data);
            }
        });
    });

	$(".listaventa").on('click','.buscarlistaventa', function() {
		
		event.preventDefault();
		var finicio     = $('#finicio').val();
		var ffin        = $('#ffin').val();
		var cliente   = $('#cliente_id').val();
		var estado      = $('#estado_id').val();
		var idopcion    = $('#idopcion').val();
		
		var _token      = $('#token').val();
		$(".listatablaventas").html("");
		abrircargando();
		
		$(".menu-roles li").removeClass( "active" )
		$(this).parents('li').addClass("active");

		$.ajax({
			
			type    :   "POST",
			url     :   carpeta+"/ajax-gestion-de-orden-ventas-entrefechas",
			data    :   {
							_token        : _token,
							idopcion      : idopcion,
							finicio       : finicio,
							ffin          : ffin,
							cliente       : cliente,
							estado        : estado
						},

			success: function (data) {
				cerrarcargando();
				
				$(".listatablaventas").html(data);

			},
			error: function (data) {
				cerrarcargando();
				console.log('Error:', data);
			}
		});
	});

	$(".contenedor_ov_detalle").on('click','.btnupdenvio', function(e) {

        debugger;
        var envio         					=   $(".contenedor_ov_detalle .envio").val();
        var envioorg      					=   $(this).attr('data_monto_envio');
        var accion          				=   'Desea Actualizar el Monto de Envio de : ' + envioorg + ' a '+envio+ ' ?';
        var _token                      	=   $('#token').val();
        var idopcion                    	=   $('#idopcion').val();        
        var data_orden_venta_id          	=   $(this).attr('data_orden_venta_id');
        var ov_estado_id      				=   $(this).attr('data_orden_venta_estado_id');		
		
		if(ov_estado_id !='1CIX00000003'){ alerterrorajax("No puede modificar una Orden de Venta en estado Emitido."); return false;}	
     
        data    =   {
                        _token                      : _token,
                        data_orden_venta_id         : data_orden_venta_id,                        
                        idopcion                    : idopcion,
                        envio                       : envio,
                    };

        $.confirm({
            title: '¿Confirma Actualizacion del Monto de Envio?',
            content: accion,
            buttons: {
                confirmar: function () {
                    ModificarEnvioOV(data,data_orden_venta_id,_token,idopcion);
                },
                cancelar: function () {
                    debugger;
                    $.alert('Se cancelo la Actualizacion del Monto de Envio');
                    $('.contenedor_ov_detalle .envio').val(envioorg);
                }
            }
        });

    });

    function ModificarEnvioOV(data,data_orden_venta_id,_token,idopcion)
	{
	    abrircargando();
	    $.ajax({
	        type    :   "POST",
	        url     :   carpeta+'/ajax-actualizar-envio-orden-venta',
	        data    :   data,
	        success: function (data) {
	            cerrarcargando();
	            $('.contenedor_ov_detalle').html(data);	            
	        },
	        error: function (data) {
	            cerrarcargando();
	            error500(data);
	        }
	    });   
	}

	$(".contenedor_ov_detalle").on('click','.btnupddescuento', function(e) {

        debugger;
        var descuento         				=   $(".contenedor_ov_detalle .descuento").val();
        var descuentoorg      				=   $(this).attr('data_monto_descuento');
        var accion          				=   'Desea Actualizar el Monto de Descuento de : ' +descuentoorg + ' a '+descuento+ ' ?';
        var _token                      	=   $('#token').val();
        var idopcion                    	=   $('#idopcion').val();        
        var data_orden_venta_id          	=   $(this).attr('data_orden_venta_id');        
        var ov_estado_id      				=   $(this).attr('data_orden_venta_estado_id');		
		
		if(ov_estado_id !='1CIX00000003'){ alerterrorajax("No puede modificar una Orden de Venta en estado Emitido."); return false;}	
     
        data    =   {
                        _token                      : _token,
                        data_orden_venta_id         : data_orden_venta_id,                        
                        idopcion                    : idopcion,
                        descuento                   : descuento,
                    };

        $.confirm({
            title: '¿Confirma Actualizacion del Monto de Descuento?',
            content: accion,
            buttons: {
                confirmar: function () {
                    ModificarDescuentoOV(data,data_orden_venta_id,_token,idopcion);
                },
                cancelar: function () {
                    debugger;
                    $.alert('Se cancelo la Actualizacion del Monto de Descuento');
                    $('.contenedor_ov_detalle .descuento').val(descuentoorg);
                }
            }
        });

    });

    function ModificarDescuentoOV(data,data_orden_venta_id,_token,idopcion)
	{
	    abrircargando();
	    $.ajax({
	        type    :   "POST",
	        url     :   carpeta+'/ajax-actualizar-descuento-orden-venta',
	        data    :   data,
	        success: function (data) {
	            cerrarcargando();
	            $('.contenedor_ov_detalle').html(data);	            
	        },
	        error: function (data) {
	            cerrarcargando();
	            error500(data);
	        }
	    });   
	}

	$(".contenedor_ov_detalle").on('click','.btnupdseguro', function(e) {

        debugger;
        var seguro         					=   $(".contenedor_ov_detalle .seguro").val();
        var seguroorg      					=   $(this).attr('data_monto_seguro');
        var accion          				=   'Desea Actualizar el Monto de Seguro de : ' + seguroorg + ' a '+seguro+ ' ?';
        var _token                      	=   $('#token').val();
        var idopcion                    	=   $('#idopcion').val();        
        var data_orden_venta_id          	=   $(this).attr('data_orden_venta_id');        
        var ov_estado_id      				=   $(this).attr('data_orden_venta_estado_id');		
		
		if(ov_estado_id !='1CIX00000003'){ alerterrorajax("No puede modificar una Orden de Venta en estado Emitido."); return false;}	
     
        data    =   {
                        _token                      : _token,
                        data_orden_venta_id         : data_orden_venta_id,                        
                        idopcion                    : idopcion,
                        seguro                       : seguro,
                    };

        $.confirm({
            title: '¿Confirma Actualizacion del Monto de Seguro?',
            content: accion,
            buttons: {
                confirmar: function () {
                    ModificarSeguroOV(data,data_orden_venta_id,_token,idopcion);
                },
                cancelar: function () {
                    debugger;
                    $.alert('Se cancelo la Actualizacion del Monto de Seguro');
                    $('.contenedor_ov_detalle .seguro').val(seguroorg);
                }
            }
        });

    });

    function ModificarSeguroOV(data,data_orden_venta_id,_token,idopcion)
	{
	    abrircargando();
	    $.ajax({
	        type    :   "POST",
	        url     :   carpeta+'/ajax-actualizar-seguro-orden-venta',
	        data    :   data,
	        success: function (data) {
	            cerrarcargando();
	            $('.contenedor_ov_detalle').html(data);	            
	        },
	        error: function (data) {
	            cerrarcargando();
	            error500(data);
	        }
	    });   
	}

	$('.ordenventa').on('change','#producto_id',function(event){
		debugger;

		var producto_id	=	$('#producto_id').val();
		var _token      =	$('#token').val();		

		$.ajax({
			
			type    :   "POST",
			url     :   carpeta+"/ajax-cargar-preciounitario-producto",
			data    :   {
							_token  : _token,
							producto_id : producto_id
						},
			success: function (data) {
				$(".ordenventa .preciounitarioov").html(data);
				CalcularImporteDetalleCompra()
			},
			error: function (data) {

				console.log('Error:', data);
			}
		});
	});


});
