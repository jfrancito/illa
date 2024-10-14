$(document).ready(function(){

	var carpeta = $("#carpeta").val();

	$('#agregadetalleproduccion').on('click',function(event){
		debugger;
		var _token					=	$('#token').val();
		var cotizacion_id			=	$(this).attr('data_cotizacion_id');
		var detalle_cotizacion_id	=	$(this).attr('data_detalle_cotizacion_id');
		var producto_id				=	'';
		var cadproducto				=	'';
		var producto				=	$('#ajaxproductoproduccion #producto_id').select2('data');
		if(producto){
			producto_id		=	producto[0].id;
			cadproducto		=	producto[0].text;
			if(producto_id!==''){
				var idopcion		=	$('#idopcion').val();
				data				=	{
											_token            : _token,
											cotizacion_id     : cotizacion_id,
											producto_id       : producto_id,
											idopcion          : idopcion
										};
				ajax_modal(data,"/ajax-modal-agregar-producto-produccion",
						  "modal-agregar-producto-produccion","modal-agregar-producto-produccion-container");
			}
			else{
				alerterrorajax('SELECCINE PRODUCTO');
				return false;
			}
		}
		else{
			alerterrorajax('SELECCINE PRODUCTO');
			return false;
		}
	});

	// $('#frmagregarproductoproduccion').on('submit',function(event){
	$(".modal-agregar-producto-produccion-container").on('submit','#frmagregarproductoproduccion', function(event) {
		debugger;
		event.preventDefault();
		// debugger;
		// alerterrorajax('entro al submit');
		// debugger;
		data = dataenviarProductos();
		if (data) { // Si la validación es exitosa
			var data_string = JSON.stringify(data);
			$('#productos').val(data_string); // Asignar la cadena JSON al input oculto
			abrircargando();
			// Enviar el formulario manualmente después de la validación
			this.submit();
		}
		return true;

	});


	$('.emitirproduccion').on('click', function(event){
		// datae = dataenviar();
		debugger;
		event.preventDefault();

		var _token				=	$('#token').val();
		var idopcion			=	$('#idopcion').val();
		var idproduccion		=	$(this).attr('data_produccion');
		data					=	{
										_token           : _token,                                            
										idopcion         : idopcion,
										idproduccion     : idproduccion,
									};
		// data = dataenviar();
		// if(data.length<=0){alerterrorajax("Seleccione por lo menos una Cotizacion");return false;}
		// var datastring = JSON.stringify(data);
		// $('#pedido').val(datastring);
		$.confirm({
			title: '¿Confirma Emitir la Produccion?',
			content: 'Terminar la Produccion',
			buttons: {
				confirmar: function () {
					abrircargando();
					// $( "#formpedido" ).submit();
					registrarproduccion(data)
				},
				cancelar: function () {
					$.alert('Se cancelo la Emision de Produccion');
				}
			}
		});

	});

	function dataenviarProductos(){
		var data = [];
		$(".listatabla tr").each(function(){
			var inputcantidad = $(this).find('input');
			var idcantidad = inputcantidad.attr('id');
			var cantidadproducto = inputcantidad.val();
			
			// Convertir a entero y validar si es mayor que 0
			if(parseInt(cantidadproducto, 10) > 0){
				data.push({
					producto_id      : inputcantidad.attr("producto_id"),
					detallecompra_id : inputcantidad.attr("detallecompra_id"),
					compra_id 		: inputcantidad.attr("compra_id"),
					cantidad         : parseFloat(cantidadproducto)
				});
			}
		});
		return data;
	}


	function dataenviar(){
		debugger;
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


	$('#enviarcotizacion').on('click', function(event){
		event.preventDefault();
		data = dataenviar();
		if(data.length<=0){alerterrorajax("Seleccione por lo menos una Cotizacion");return false;}
		var datastring = JSON.stringify(data);
		$('#pedido').val(datastring);

		$.confirm({
			title: '¿Confirma Emitir la Cotizacion?',
			content: 'Finalizar la Cotizacion ya no podra editarla y pasara a un estado final.',
			buttons: {
				confirmar: function () {
					abrircargando();
					$( "#formpedido" ).submit();
				},
				cancelar: function () {
					$.alert('Se cancelo la Emision');
				}
			}
		});

	});


	$('#modal-configuracion-cotizacion-modelo-detalle').on('change','#tipocategoria',function(e){
		debugger;
		var tipo = $(this).val();
		// alerterrorajax(tipo);
		if(tipo==1){
			//Categoria
			$('#unidadmedida_id').attr('required',false);
			$('#cantidad').attr('required',false);
			$('#unidadmedida_id').prop('disabled',true);
			$('#cantidad').prop('disabled',true);
		}
		else{
			//Servicio 0: no es padre
			 $('#unidadmedida_id').attr('required',true);
			$('#cantidad').attr('required',true);
			$('#unidadmedida_id').prop('disabled',false);
			$('#cantidad').prop('disabled',false);
		}
	});

  
	$(".ventacotizar").on('click','.analisiscotizacion', function() {

		var _token                      =   $('#token').val();
		var cotizacion_id               =   $(this).attr('data_cotizacion_id');
		var detalle_cotizacion_id       =   $(this).attr('data_detalle_cotizacion_id');
		var idopcion                    =   $('#idopcion').val();

		data                            =   {
												_token                      : _token,
												cotizacion_id               : cotizacion_id,
												detalle_cotizacion_id       : detalle_cotizacion_id,
												idopcion                    : idopcion
											};

		var section                     =   'analizar';
		$('.nav-tabs a[href="#analizar"]').tab('show');

		ajax_normal_section(data,"/ajax-analizar-detalle-cotizacion",section);                                    

	});



	function desmarcarchecks(nombrecheck){
			debugger;
			$(".tlistacategorias tr").each(function(){
				debugger;
				var checkbox = $(this).find('input[type="checkbox"]');
				  // Verifica si el checkbox está marcado
				if(checkbox.attr('id')!==nombrecheck){
					  if (checkbox.prop("checked")) {
						// Desmarca el checkbox si está marcado
						checkbox.prop("checked", false);
					  }
				}
			});
	}

	$(".ventacotizar").on('click','.input_check_cat_ln', function(e) {
		debugger;
		var idcategoria = $(this).attr('data_id');
		var valor = Boolean($(this).prop('checked'));
		if(valor==true){
			var nombre = $(this).attr('id');
			$('#idcategoria').val(idcategoria);
			desmarcarchecks(nombre);
		}
		else{
			$('#idcategoria').val('');
		}

	});

	$(".ventacotizar").on('click','.agregalinea', function() {

		var idcategoria             =   $('#idcategoria').val();
		var _token                  =   $('#token').val();
		var cotizacion_id           =   $(this).attr('data_cotizacion');
		var idopcion                =   $('#idopcion').val();
		data                        =   {
											_token                  : _token,
											cotizacion_id           : cotizacion_id,
											idopcion                : idopcion,
											idcategoria             : idcategoria,
										};
		ajax_modal(data,"/ajax-modal-configuracion-cotizacion-detalle",
				  "modal-configuracion-cotizacion-modelo-detalle","modal-configuracion-cotizacion-modelo-detalle-container");
	});


	$(".ventacotizar").on('click','.btn-guardar-configuracion', function() {

		var gruposervicio_id                =   $('#gruposervicio_id').val();
		var unidadmedida_id                 =   $('#unidadmedida_id').val();
		var servicio                        =   $('#servicio').val();
		var cantidad                        =   $('#cantidad').val();
		//validacioones
		// if(gruposervicio_id ==''){ alerterrorajax("Seleccione una grupo de servicio."); return false;}
		// if(unidadmedida_id ==''){ alerterrorajax("Seleccione una unidad de medida."); return false;}
		if(servicio ==''){ alerterrorajax("Ingrese Descripcion."); return false;}
		// if(cantidad ==''){ alerterrorajax("Ingrese un cantidad."); return false;}

		return true;

	});


	$(".ventacotizar").on('click','.modificarcotizacion', function() {

		var _token                      =   $('#token').val();
		var cotizacion_id               =   $(this).attr('data_cotizacion_id');
		var detalle_cotizacion_id       =   $(this).attr('data_detalle_cotizacion_id');
		var idopcion                    =   $('#idopcion').val();

		data                            =   {
												_token                      : _token,
												cotizacion_id               : cotizacion_id,
												detalle_cotizacion_id       : detalle_cotizacion_id,
												idopcion                    : idopcion
											};

		ajax_modal(data,"/ajax-modal-modificar-configuracion-cotizacion-detalle",
				  "modal-configuracion-cotizacion-modelo-detalle","modal-configuracion-cotizacion-modelo-detalle-container");

	});


	$(".cotizacion").on('click','.eliminarproduccion', function() 
	{
		debugger;
		var _token         =   $('#token').val();
		var produccion_id  =   $(this).attr('data_produccion');
		var idopcion       =   $(this).attr('data_opcion');//$('#idopcion').val();
		// var detalle_cotizacion_id       =   $(this).attr('data_detalle_cotizacion_id');

												// detalle_cotizacion_id       : detalle_cotizacion_id,
		data                            =   {
												_token                      : _token,
												produccion_id               : produccion_id,
												idopcion                    : idopcion
											};
		$.confirm({
			title: '¿Confirma la eliminacion?',
			content: 'Eliminar Linea',
			buttons: {
				confirmar: function () {
					elimnarlinea(data);
				},
				cancelar: function () {
					$.alert('Se cancelo la eliminacion');
				}
			}
		});

	});


	$(".ventacotizar").on('click','.eliminalinea', function() {

		debugger;
		var _token                      =   $('#token').val();
		var idopcion                    =   $('#idopcion').val();
		var idcategoria                 =   $('#idcategoria').val();
		var cotizacion_id               =   $(this).attr('data_cotizacion_id');
		var detalle_cotizacion_id       =   $('#idcategoria').val();
		
		if(detalle_cotizacion_id==''){
			alerterrorajax('DEBE SELECCIONAR UNA FILA');
			return false;
		}
		// alerterrorajax(detalle_cotizacion_id);
		data                            =   {
												_token                      : _token,
												cotizacion_id               : cotizacion_id,
												detalle_cotizacion_id       : detalle_cotizacion_id,
												idopcion                    : idopcion
											};
		$.confirm({
			title: '¿Confirma la Eliminacion?',
			content: 'Eliminar Linea y todos los Detalles que Contiene?',
			buttons: {
				confirmar: function () {
					elimnarservicio(data);
				},
				cancelar: function () {
					$.alert('Se cancelo la Eliminacion');
				}
			}
		});

	});

	// function elimnarservicio(data){
	// 	ajax_normal_cargar(data,"/ajax-elimnar-servicio-linea-cotizacion");
	// }

	function registrarproduccion(data){
		ajax_normal_cargar(data,"/ajax-registrar-producto-produccion");
	}

	function elimnarlinea(data){
		ajax_normal_cargar(data,"/ajax-elimnar-linea-cotizacion-produccion");
	}

});
