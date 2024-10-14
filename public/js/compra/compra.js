$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".compra").on('click','.agregadetallecompra', function() {
        // debugger;
        var _token                  =   $('#token').val();
        var compra_id     			=   $(this).attr('data_compra_id');
        var compra_estado_id        =   $(this).attr('data_compra_estado_id');
        var idopcion                =   $('#idopcion').val();
        
        if(compra_estado_id !='1CIX00000003'){ alerterrorajax("No puede agregar Productos a una Compra Emitida."); return false;}

        data                        =   {
                                            _token                  : _token,
                                            compra_id     			: compra_id,
                                            idopcion                : idopcion
                                        };
                                        
        ajax_modal(data,"/ajax-modal-detalle-compra",
                  "modal-detalle-compra","modal-detalle-compra-container");

    });

    $('.emitircompra').on('click', function(){
        
        // datae = dataenviar();
        
        var _token                  =   $('#token').val();
        var idopcion                =   $('#idopcion').val();
        var idcompra                =   $(this).attr('data_idcompra');
        var idtipocompra            =   $('#idtipocompra').val();

        // if(datae.length<=0){alerterrorajax("Seleccione por lo menos una compra");return false;}
        // var compra = JSON.stringify(datae);

        data                        =   {
                                            _token                  : _token,                                            
                                            idopcion                : idopcion,
                                            idcompra                : idcompra,
                                            idtipocompra            : idtipocompra
                                        };
        
        ajax_modal(data,"/ajax-modal-emitir-compra",
              "modal-emitir-compra","modal-emitir-compra-container");        

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

    $('.compra').on('change','#ckindigv',function (e) {
        // debugger;
        var valor   =   ($(this).prop('checked'));

        if(valor==true){
            // $('.compra #porcigv').val(18);
            $('.compra #porcigv').val(18).prop('disabled', false); // Habilitar y establecer valor

            $('.compra #indigv').val(1);
        }
        else{
            $('.compra #porcigv').val(0).prop('disabled', true); // Habilitar y establecer valor;
            $('.compra #indigv').val(0);
        }
        CalcularImporteDetalleCompra();
        // notifybien('muy bien','entro al check change');
    });


    $('.compra').on('keyup','#porcigv',function (e) {
        debugger;
        // var valor   =    $(this).val();
        CalcularImporteDetalleCompra();
        // notifybien('muy bien','entro al check change');
    });

    $(".compra").on('keyup','.seriekeypress', function() {
        var valueserie              =   $(this).val().toUpperCase();
        $(this).val(valueserie);

    });

    $(".compra").on('keyup','#numero', function() {
        var valuennumero              =   $(this).val().replace(/\D/g, '');
        $(this).val(valuennumero);

    });

    $(".compra").on('focusout','#numero', function() {

        var valuennumero              =   $(this).val().toString().padStart(8,0);
        $(this).val(valuennumero);

    });


    $(".compra").on('keyup','#preciounitario , #cantidad', function() {

        // var preciounitario              =   $("#preciounitario").val().replace(",","");
        // var cantidad                    =   $("#cantidad").val().replace(",","");
        // var total                       =   preciounitario*cantidad;
        CalcularImporteDetalleCompra();
        // $('#total').val(total);

    });

    $(".compra").on('click','.btn-guardar-detalle-compra', function() {

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
        var preciounitario              =   $(".compra #preciounitario").val().replace(",","");
        var cantidad                    =   $(".compra #cantidad").val().replace(",","");
        var indigv                      =   $('.compra #ckindigv').prop('checked');
        var porcigv                     =   $('.compra #porcigv').val();
        var montoigv                    =   0;
        var subtotal                    =   preciounitario*cantidad;
        if(indigv==1){
            montoigv = (subtotal * porcigv)/100;
        }
        var total                       =   subtotal+ montoigv;

        $('.compra #igv').val(montoigv);
        $('.compra #subtotal').val(subtotal);
        $('#total').val(total);

        // var total                       =   preciounitario*cantidad;


    }

    $(".listacompra").on('click','.btn-emitir-compra', function() {

        var almacen_id                   =   $('#almacen_id').val();
        var motivo_id                    =   $('#motivo_id').val();
        var tipo_compra_id               =   $('#idtipocompra').val();



        //validacioones
        if(almacen_id =='' && tipo_compra_id != '1CIX00000036'){ alerterrorajax("Seleccione un Almacen."); return false;}
        if(motivo_id ==''){ alerterrorajax("Seleccione un Motivo."); return false;}
        
        return true;

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

    $(".listacompra").on('click','.buscarlistacompra', function() {
        
        event.preventDefault();
        var finicio     = $('#finicio').val();
        var ffin        = $('#ffin').val();
        var proveedor   = $('#proveedor_id').val();
        var estado      = $('#estado_id').val();
        var idopcion    = $('#idopcion').val();
        
        var _token      = $('#token').val();
        $(".listatablacompras").html("");
        abrircargando();
        
        $(".menu-roles li").removeClass( "active" )
        $(this).parents('li').addClass("active");

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-gestion-de-compras-entrefechas",
            data    :   {
                            _token        : _token,
                            idopcion      : idopcion,
                            finicio       : finicio,
                            ffin          : ffin,
                            proveedor     : proveedor,
                            estado        : estado
                        },

            success: function (data) {
                cerrarcargando();
                
                $(".listatablacompras").html(data);

            },
            error: function (data) {
                cerrarcargando();
                console.log('Error:', data);
            }
        });
    });



});
