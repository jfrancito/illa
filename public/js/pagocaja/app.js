$(document).ready(function(){
    debugger;
    var carpeta = $("#carpeta").val();
    var rutamodal = $("#idmodal").val();


    $(".compra").on('click','.agregadetalle', function() {
        debugger;
        var _token                  =   $('#token').val();
        var venta_id     			=   $(this).attr('data_venta_id');
        var compra_estado_id        =   $(this).attr('data_venta_estado_id');
        var idopcion                =   $('#idopcion').val();

        if(compra_estado_id !='1CIX00000003'){ alerterrorajax("No puede agregar Productos a una Venta Emitida."); return false;}

        data                        =   {
                                            _token                  : _token,
                                            venta_id     			: venta_id,
                                            idopcion                : idopcion
                                        };
                                        

        debugger;

        ajax_modal(data,"/ajax-modal-detalle-"+rutamodal,
                  "modal-detalle","modal-detalle-container");

    });


    $('#modal-detalle').on('change','#entidad_id',function(event){
        debugger;
        
        var entidad_id = $('#entidad_id').val();
        var _token        = $('#token').val();
        // if(entidad_id!='1CIX00000018'){return false;}
        abrircargando('Registrando Informacion',1);

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-cuenta-entidad-empresa",
            data    :   {
                            _token  : _token,
                            entidad_id : entidad_id
                        },
            success: function (data) {
                $(".ajaxcuentasentidad").html(data);
            },
            error: function (data) {

                console.log('Error:', data);
            }
        });
        cerrarcargando();

    });


    $('#formmodalagregar').submit(function(event) {
        debugger;
        event.preventDefault();
    
        $.confirm({
            title: '¿Confirma Guardar Registro?',
            content: 'Guardar Registro',
            buttons: {
                confirmar: function () {
                    abrircargando('Registrando Informacion',1);
                    $('#formmodalagregar').off('submit').submit();
                },
                cancelar: function () {
                    $.alert('Se cancelo Registro');
                }
            }
        });
        return false;
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

    // $('.venta').on('change','#ckindigv',function (e) {
    //     // debugger;
    //     var valor   =   ($(this).prop('checked'));

    //     if(valor==true){
    //         // $('.venta #porcigv').val(18);
    //         $('.venta #porcigv').val(18).prop('disabled', false); // Habilitar y establecer valor

    //         $('.venta #indigv').val(1);
    //     }
    //     else{
    //         $('.venta #porcigv').val(0).prop('disabled', true); // Habilitar y establecer valor;
    //         $('.venta #indigv').val(0);
    //     }
    //     CalcularImporteDetalleCompra();
    //     // notifybien('muy bien','entro al check change');
    // });


    // $('.venta').on('keyup','#porcigv',function (e) {
    //     debugger;
    //     // var valor   =    $(this).val();
    //     CalcularImporteDetalleCompra();
    //     // notifybien('muy bien','entro al check change');
    // });

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


    $(".compra").on('keyup','#preciounitario , #importe', function() {

        // var preciounitario              =   $("#preciounitario").val().replace(",","");
        // var cantidad                    =   $("#cantidad").val().replace(",","");
        // var total                       =   preciounitario*cantidad;
        CalcularSaldo();
        // CalcularImporteDetalleCompra();
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

    function CalcularSaldo()
    {
        debugger;
        // var preciounitario              =   $(".venta #preciounitario").val().replace(",","");
        var importe                    =   $(".compra #importe").val().replace(",","");
        var saldo                       =   $('.compra #saldo').val();
        var total                       =   $('.compra #total').val();
        
        
        if(importe>saldo){
            alerterrorajax("Importe debe ser menor o igual al saldo.");
            $('.compra #importe').val(saldo);
            return false;
        }
        // saldo = saldo - importe;
        // $('.venta #saldo').val(saldo);
        // $('.venta #total').val(subtotal);
        // $('#total').val(total);
        // var total                       =   preciounitario*cantidad;
    }

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
    $('.compra').on('change','.tipo_venta_id',function(event){
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
        var proveedor   = $('#proveedor_id').val();
        var estado      = $('#estado_id').val();
        var idopcion    = $('#idopcion').val();
        
        var _token      = $('#token').val();
        $(".listatablaventas").html("");
        abrircargando();
        
        $(".menu-roles li").removeClass( "active" )
        $(this).parents('li').addClass("active");

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-gestion-de-ventas-entrefechas",
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
                
                $(".listatablaventas").html(data);

            },
            error: function (data) {
                cerrarcargando();
                console.log('Error:', data);
            }
        });
    });



});
