$(document).ready(function(){

	var carpeta = $("#carpeta").val();
	var tabactive	= 'tab01';

	function calculaRetencionTotal(){
		return 100;
	}
	function limpiarcontrolesgema(){
		//CAPTURA DE DATOS
		$('#tipogema_id').val('').trigger('change');;
		$('#origen_id').val('').trigger('change');;
		$('#cantidad_gemas').val(0);
		
	}
	
	//DESARROLLO POSTERIOR PARA EVENTOS DE TABS DISTINTOS COMO BENEFICIENCIA
	// metodo de eventos (tabactive + '#idinput') //para poder diferenciarlos
	//EN CADA FUNCION ENVIAR EL TAB PARA AGREGARLOS ANTES DE LOS ID DE LOS INPUTS

	function CalcularTotalCosto() {
		debugger;
		let totalCantidad = 0;
		let totalCostoGemas = 0;
		


		$('.trfilagema').each(function() {
			debugger;
			let cantidad = $(this).find('.tdcantidad').text().trim();
			let costo = $(this).find('.tdnumbercostounitario').val();
			let cantidadNumerica = parseFloat(cantidad);
			let costoNumerico = parseFloat(costo);
			if (!isNaN(cantidadNumerica)) {
				totalCantidad += cantidadNumerica;
			}
			if (!isNaN(costoNumerico)) {
				totalCostoGemas += costoNumerico;
			}
		});
		
		let gramos			=	parseFloat($('#gramos').val());
		let precio_x_gramo	=	parseFloat($('#precio_x_gramo').val());
		let monto_gramos	=	gramos * precio_x_gramo;
		let precioengaste	=	parseFloat($('#precio_unitario_engaste').val());

		$('#cantidad_engaste').val(totalCantidad);
		let subtotalengaste = parseFloat(precioengaste * totalCantidad);
		$('#precio_total_engaste').val(subtotalengaste); //SUBTOTAL DEL ENGASTE
		debugger;
		$('#htotal_costo_gemas').val(totalCostoGemas); //TENEMOS EL COSTO TOTAL DE LAS GEMAS
		$('#tdtotal_costo_gemas').html(totalCostoGemas.toFixed(2));

		let costo_unitario		=	subtotalengaste+ monto_gramos + totalCostoGemas;
		$('#costo_unitario').val(costo_unitario.toFixed(2)); //TENEMOS EL COSTO UNITARIO
		let indigv				=	$('#indigv').val();
		let monto_igv = 0.0;
		if(indigv==1){
			monto_igv = parseFloat(costo_unitario*0.18);
		}
		else{
			monto_igv=0.0;
		}
		$('#monto_igv').val(monto_igv.toFixed(2));

		let costo_unitario_igv = parseFloat(costo_unitario + monto_igv);
		$('#costo_unitario_igv').val(costo_unitario_igv.toFixed(2));
		$('#costo_unitario_total').val(costo_unitario_igv.toFixed(2));

	}

	$('#ckindigv').on('change',function(event){
		let valor = $(this).prop('checked');
		if(valor==true){
			$('#indigv').val(1);
		}
		else{
			$('#indigv').val(0);
		}
		CalcularTotalCosto()
	});

	$('#sectionregistro').on('keyup','#gramos',function(event){
		CalcularTotalCosto();
	});

	$('#sectionregistro').on('keyup','#precio_x_gramo',function(event){
		CalcularTotalCosto();
	});

	// en ocasiones se agrega un identificador superior ya que el control aun no existe 
	$('#sectionregistro').on('keyup','.tdnumbercosto',function(event){
			debugger;
		//calcular Costo Unitario Gema
		    var fila = $(this).closest('tr');
		    var cantidad = parseFloat(fila.find('.tdcantidad').text().trim()) || 0;
		    var costo = parseFloat($(this).val()) || 0;
		    // Calcular el costo unitario (costo * cantidad)
		    var costounitario = costo * cantidad;
		    // Asignar el valor calculado al input tdnumbercostounitario
		    fila.find('.tdnumbercostounitario').val(costounitario.toFixed(2));
		    CalcularTotalCosto();
	});

	$(".btnagregargema").on("click", function(){
		debugger;

		// alert("presionaste el boton agregar");
		var alertaMensajeGlobal =   '';
		var aleatorio        = Math.floor((Math.random() * 500) + 1);
		var msj              = '';
		var vmonto           = $('#montorentencionjudicial').val();
		var vporcentaje      = $('#porcentajerentencionjudicial').val();
		var vmontoretencion  = 0;
	   
		var vcantidad     = $('#cantidad_gemas').val();
	   
		
		var vorigen  = $('#origen_id').val();
		var origen    = $('#origen_id').find('option:selected').text();
	   
		var vtipogema  = $('#tipogema_id').val();
		var tipogema    = $('#tipogema_id').find('option:selected').text();
	   

		if(vtipogema==0){
			alert("Seleccione Tipo de Gema");
			$('#tipogema_id').focus();
			return false;
		}

		if(vorigen==0){
			alert("Seleccione Tipo de Origen");
			$('#origen_id').focus();
			return false;
		}

		if(vtipogema==1){
			if(!valVacio(vporcentaje)){
				alert("Ingrese Porcentaje");
				$('#porcentajerentencionjudicial').focus();
				return false;
			}
			else{
				vmontoretencion=parseFloat(vporcentaje);
			}
		}

		if(vcantidad<=0){
			alert("Ingrese Cantidad");
			$('#cantidad_gemas').focus();
			return false;
		}
		// debugger;
		// alert(tipogema);
		/***************************************************************************/
		fila = "<tr class='trfilagema'>"+
					"<td class='tdorigen'>"+origen+"</td>"+
					"<td hidden class='tdtipogema'>"+vtipogema+"</td>"+
					"<td class='tdgema'>"+tipogema+"</td>"+
					"<td class='tdcantidad'>"+vcantidad+" </td>"+
					"<td class='tdcosto'><input type='number' class='input form-control input-xs tdnumbercosto' name='tdnumbercosto' value='0.0' min='0.0' max='99999' step='0.01'></td>"+
					"<td class='tdcosto'><input type='number' class='input form-control input-xs tdnumbercostounitario' readonly name='tdnumbercostounitario' value='0.0' min='0.0' max='99999' step='0.01'></td>"+
					"<td>"+
						"<button type='button' class='eliminargema btn btn-default btn-sm' aria-label='Left Align'>"+
							"<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>"+
						"</button>"+
					"</td>"+
				"</tr>";


		$("#listagemas").append(fila);
		CalcularTotalCosto();
		limpiarcontrolesgema();
	});

	$("#listagemas").on('click','.eliminargema', function() {
			$(this).closest('tr').remove();
			CalcularTotalCosto();
	})

	function validarValores() {
	    let esValido = true;

	    // Recorremos todas las filas tr con la clase 'trfilagema'
	    $('.trfilagema').each(function() {
	        // Obtenemos el valor del input con la clase 'tdnumbercosto'
	        let valorCosto = $(this).find('.tdnumbercosto').val().trim();

	        // Convertimos el valor a número
	        let valorNumerico = parseFloat(valorCosto);

	        // Validamos si el valor es 0 o NaN
	        if (isNaN(valorNumerico) || valorNumerico === 0) {
	            esValido = false;

	            // Opcional: Añadir una alerta visual al input que no es válido
	            $(this).find('.tdnumbercosto').addClass('input-error');
	            alert('El valor del costo no puede ser 0.0');
	        } else {
	            // Remover la clase de error si el valor es válido
	            $(this).find('.tdnumbercosto').removeClass('input-error');
	        }
	    });

	    return esValido;
	}

// // Llamar a esta función antes de guardar o enviar el formulario
// $('#guardarForm').on('click', function(e) {
//     // Si no es válido, prevenir el envío del formulario
//     if (!validarValores()) {
//         e.preventDefault();
//     }
// });


	$('#formagregaresquemaproducto').on('submit',function(event){
		debugger;
		if (!validarValores()) {
			event.preventDefault();
			return false;
		}
		event.preventDefault();
		var xmllistagemas="";
		$("#listagemas tr").each(function(){
			xorigen      	= $(this).find('.tdorigen').html();
			xgemaid        	= $(this).find('.tdtipogema').html();
			xgema        	= $(this).find('.tdgema').html();
			xcantidad    	= $(this).find('.tdcantidad').html();
			xcosto 			= parseFloat($(this).find('.tdnumbercosto').val().trim());
			xmllistagemas 	= xmllistagemas + xorigen +'***'+ xgemaid  +'***'+ xgema  +'***'+  xcantidad+'***'+ xcosto+'&&&';
		});

		$('#xmllistagemas').val(xmllistagemas);

		debugger;
		event.preventDefault(); // Prevenir el comportamiento predeterminado del formulario
		debugger;
		$.confirm({
			title: '¿Confirma Guardar Registro?',
			content: 'Guardar Registro',
			buttons: {
				confirmar: function() {
					debugger;
					abrircargando('Guardando Registro', 1);
					$('#formagregaresquemaproducto').off('submit').submit();
				},
				cancelar: function() {
					$.alert('Se canceló el registro');
				}
			}
		});

		return false; // Para prevenir el envío normal del formulario
	});

	$("#precio_unitario_engaste").on('keyup', function(event) {
		CalcularTotalCosto();
	});

	// $("#btnguardarregistroesquema").on('click', function(event) {
	// 	// debugger;
	// 	event.preventDefault(); // Prevenir el comportamiento predeterminado del formulario

	// 	var xmllistagemas="";
	// 	$("#listagemas tr").each(function(){
	// 		xorigen       = $(this).find('.tdorigen').html();
	// 		xgemaid       = $(this).find('.tdtipogema').html();
	// 		xgema         = $(this).find('.tdgema').html();
	// 		xcantidad     = $(this).find('.tdcantidad').html();
	// 		xmllistagemas = xmllistagemas + xorigen +'***'+ xgemaid  +'***'+ xgema  +'***'+ xcantidad+'&&&';
			
	// 	});

	// 	$('#xmllistagemas').val(xmllistagemas);
	// 	$.confirm({
	// 		title: '¿Confirma Guardar Registro?',
	// 		content: 'Guardar Registro',
	// 		buttons: {
	// 			confirmar: function() {
	// 				debugger;
	// 				abrircargando('Guardando Registro', 1);
	// 				$('#formagregaresquemaproducto').off('submit').submit();
	// 			},
	// 			cancelar: function() {
	// 				$.alert('Se canceló el registro');
	// 			}
	// 		}
	// 	});

	// 	return false; // Para prevenir el envío normal del formulario
	// });

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



});
