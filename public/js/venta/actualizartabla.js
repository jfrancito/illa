$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".ventacotizar").on('click','.eliminaranalisis', function() {
        debugger;
        var _token                      =   $('#token').val();
        var cotizacion_id               =   $(this).attr('data_cotizacion_id');
        var detalle_cotizacion_id       =   $(this).attr('data_detalle_cotizacion_id');
        var detalle_cotizacion_analisis_id  =   $(this).attr('data_detalle_cotizacion_analisis_id');
        var idopcion                    =   $('#idopcion').val();
        data                            =   {
                                                _token                      : _token,
                                                cotizacion_id               : cotizacion_id,
                                                detalle_cotizacion_id       : detalle_cotizacion_id,
                                                detalle_cotizacion_analisis_id  : detalle_cotizacion_analisis_id,
                                                idopcion                    : idopcion
                                            };
        $.confirm({
            title: '¿Confirma la eliminacion?',
            content: 'Eliminar Linea',
            buttons: {
                confirmar: function () {
                    elimnarlineaanalisis(data,cotizacion_id,detalle_cotizacion_id,detalle_cotizacion_analisis_id,_token,idopcion);
                },
                cancelar: function () {
                    $.alert('Se cancelo la eliminacion');
                }
            }
        });

    });
    
    $(".ventacotizar").on('change','#categoria_id',function()
    {

        // alerterrorajax('CLICK EN CATEGORIA CHANGE');
        var _token                  =   $('#token').val();
        var categoriaproducto       =   $('#categoria_id').select2('data');
        var categoria_id            =   $('#categoria_id').val();
        var cadcategoriaproducto    =   '';
        if(categoriaproducto)
        {
            categoria_id   =   categoriaproducto[0].id;
            cadcategoriaproducto    =   categoriaproducto[0].text;
            
            $.ajax({
                    type    :   "POST",
                    url     :   carpeta+"/ajax-cargar-sub-categorias-productos-produccion",
                    data    :   {
                                    _token  : _token,
                                    categoria_id : categoria_id,
                                },
                    success: function (data) {
                        // $(".formagregarproducto #numerodocumento").val(data);
                        $(".divventacotizar .ajaxsubcategoriaproduccion").html(data);
                        $(".ventacotizar .subcategoria_id").change();
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        }
    });

    $(".ventacotizar").on('change','#subcategoria_id',function()
    {

        // alerterrorajax('CLICK EN CATEGORIA CHANGE');
        var _token                  =   $('#token').val();
        var categoriaproducto       =   $('.ventacotizar #categoria_id').select2('data');
        var subcategoriaproducto    =   $('.ventacotizar #subcategoria_id').select2('data');
        var categoria_id            =   $('.ventacotizar #categoria_id').val();
        var subcategoria_id         =   $('.ventacotizar #subcategoria_id').val();
        var cadcategoriaproducto    =   '';
        
        if(categoriaproducto)
        {
            if(subcategoriaproducto){
                categoria_id   =   categoriaproducto[0].id;
                cadcategoriaproducto    =   categoriaproducto[0].text;
                
                $.ajax({
                        type    :   "POST",
                        url     :   carpeta+"/ajax-cargar-productos-produccion",
                        data    :   {
                                        _token  : _token,
                                        categoria_id : categoria_id,
                                        subcategoria_id : subcategoria_id,
                                    },
                        success: function (data) {
                            // $(".formagregarproducto #numerodocumento").val(data);
                            $(".divventacotizar .ajaxproductoproduccion").html(data);
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                });
            }
        }
    });

    $(".ventacotizar").on('change','#producto_id',function(){
        // var producto_desc = $(this).select2
        var producto_desc = $(this).find("option:selected").text();
        $('.ventacotizar #descripcion').val(producto_desc);
    });

    $(".ventacotizar").on('click','.btnagregaranalisis', function() {
        debugger;
        var _token                          =   $('#token').val();
        var idopcion                        =   $('#idopcion').val();
        var categoria_id                    =   $('#categoria_id').val();
        var subcategoria_id                 =   $('#subcategoria_id').val();
        // var grupoanalisis_id                =   $('#grupoanalisis_id').val();
        //var unidadmedidaa_id                =   $('#unidadmedidaa_id').val();
        var descripcion                     =   $('#descripcion').val();
        var producto_id                     =   $('#producto_id').val();
        var cantidada                       =   $('#cantidada').val();
        var precio                          =   $('#precio').val();
        var data_cotizacion_id              =   $(this).attr('data_cotizacion_id');
        var data_detalle_cotizacion_id      =   $(this).attr('data_detalle_cotizacion_id');
        debugger;

        //validacioones
        if(categoria_id ==''){ alerterrorajax("Seleccione una categoria."); return false;}
        if(subcategoria_id ==''){ alerterrorajax("Seleccione una sub-categoria."); return false;}
        //if(unidadmedidaa_id ==''){ alerterrorajax("Seleccione una unidad de medida."); return false;}
        if(producto_id ==''){ alerterrorajax("Seleccione un Producto."); return false;}
        if(descripcion ==''){ alerterrorajax("Ingrese una descripcion."); return false;}
        if(cantidada ==''){ alerterrorajax("Ingrese un cantidad."); return false;}
        if(precio ==''){ alerterrorajax("Ingrese un precio."); return false;}

        data                        =   {
                                            _token                  : _token,
                                            categoria_id        : categoria_id,
                                            subcategoria_id        : subcategoria_id,
                                            //unidadmedidaa_id        : unidadmedidaa_id,
                                            descripcion             : descripcion,
                                            producto_id             : producto_id,
                                            cantidad                : cantidada,
                                            precio                  : precio,
                                            data_cotizacion_id      : data_cotizacion_id,
                                            data_detalle_cotizacion_id : data_detalle_cotizacion_id,
                                            idopcion                : idopcion
                                        };
        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+'/ajax-agregar-producto-analisis',
            data    :   data,
            success: function (data) {
                cerrarcargando();
                $('.listajaxanalisis').html(data);
                $(input).val('');
                // actualizar_tabla_cotizacion(data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });
        // debugger;
    });


    $("#analizar").on('click','.swigv', function(e) {

        debugger;
        var vswigv      =   ($(this).prop('checked'));
        var vswigvant   =   !($(this).prop('checked'));
        var swigv       =   0;
        var accion      =   'Eliminar';
        if(vswigv===true){
            swigv=1;
            accion='Agregar';
        }
        else{
            swigv=0;
            accion='Eliminar';
        }
     
        var _token                      =   $('#token').val();
        var idopcion                    =   $('#idopcion').val();
        var idcategoria                 =   $('#idcategoria').val();
        var data_cotizacion_id               =   $(this).attr('data_cotizacion_id');
        var data_detalle_cotizacion_id       =   $(this).attr('data_detalle_cotizacion_id');
     
        data    =   {
                        _token                      : _token,
                        data_cotizacion_id          : data_cotizacion_id,
                        data_detalle_cotizacion_id  : data_detalle_cotizacion_id,
                        idopcion                    : idopcion,
                        swigv                       : swigv,
                    };

        $.confirm({
            title: '¿Confirma '+accion+' IGV?',
            content: 'Desea '+accion+' el Calculo de IGV para este Detalle Analisis?',
            buttons: {
                confirmar: function () {
                    ModificarIgvLineaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
                },
                cancelar: function () {
                    debugger;
                    $.alert('Se cancelo el '+accion+' el IGV');
                    $('#analizar .swigv').prop('checked',vswigvant)
                }
            }
        });

    });


    $("#analizar").on('click','.btnupdmgadmin', function(e) {

        debugger;
        var mgadmin         =   $("#analizar .mgadmin").val();
        var mgadminorg      =   $(this).attr('data_monto_margen');
        var accion          =   'Desea Actualizar el Margen de Ganancia Administrativo de : ' + mgadminorg + ' a '+mgadmin+ ' ?';
        var _token                      =   $('#token').val();
        var idopcion                    =   $('#idopcion').val();
        var idcategoria                 =   $('#idcategoria').val();
        var data_cotizacion_id          =   $(this).attr('data_cotizacion_id');
        var data_detalle_cotizacion_id  =   $(this).attr('data_detalle_cotizacion_id');
     
        data    =   {
                        _token                      : _token,
                        data_cotizacion_id          : data_cotizacion_id,
                        data_detalle_cotizacion_id  : data_detalle_cotizacion_id,
                        idopcion                    : idopcion,
                        mgadmin                       : mgadmin,
                    };

        $.confirm({
            title: '¿Confirma Actualizacion del Margen de Ganancia?',
            content: accion,
            buttons: {
                confirmar: function () {
                    ModificarMGAdminLineaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
                },
                cancelar: function () {
                    debugger;
                    $.alert('Se cancelo la Actualizacion del Margen de Ganancia');
                    $('#analizar .mgadmin').val(mgadminorg);
                }
            }
        });

    });

     $("#analizar").on('click','.btnupdmgutil', function(e) {

        debugger;
        var mgutil         =   $("#analizar .mgutil").val();
        var mgutilorg      =   $(this).attr('data_monto_margen');
        var accion         =   'Desea Actualizar el Margen de Ganancia Utilidades de : ' + mgutilorg + ' a '+mgutil+ ' ?';
        var _token                      =   $('#token').val();
        var idopcion                    =   $('#idopcion').val();
        var idcategoria                 =   $('#idcategoria').val();
        var data_cotizacion_id          =   $(this).attr('data_cotizacion_id');
        var data_detalle_cotizacion_id  =   $(this).attr('data_detalle_cotizacion_id');
     
        data    =   {
                        _token                      : _token,
                        data_cotizacion_id          : data_cotizacion_id,
                        data_detalle_cotizacion_id  : data_detalle_cotizacion_id,
                        idopcion                    : idopcion,
                        mgutil                       : mgutil,
                    };

        $.confirm({
            title: '¿Confirma Actualizacion del Margen de Ganancia?',
            content: accion,
            buttons: {
                confirmar: function () {
                    ModificarMGUtilLineaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
                },
                cancelar: function () {
                    debugger;
                    $.alert('Se cancelo la Actualizacion del Margen de Ganancia');
                    $('#analizar .mgutil').val(mgutilorg);
                }
            }
        });

    });

    $("#analizar").on('click','.btnupdtotprecunit', function(e) {

        debugger;
        // var mgutil         =   $("#analizar .mgutil").val();
        var totalpv        =   $("#analizar .totprecunit").val();
        var totalpvcalc    =   $(this).attr('data_monto_margen');
        var accion         =   'Desea Actualizar el Precio de Venta de : ' + totalpvcalc + ' a '+totalpv+ ' ?';
        var _token                      =   $('#token').val();
        var idopcion                    =   $('#idopcion').val();
        var idcategoria                 =   $('#idcategoria').val();
        var data_cotizacion_id          =   $(this).attr('data_cotizacion_id');
        var data_detalle_cotizacion_id  =   $(this).attr('data_detalle_cotizacion_id');
     
        data    =   {
                        _token                      : _token,
                        data_cotizacion_id          : data_cotizacion_id,
                        data_detalle_cotizacion_id  : data_detalle_cotizacion_id,
                        idopcion                    : idopcion,
                        totalpv                     : totalpv,
                    };

        $.confirm({
            title: '¿Confirma Actualizacion del Precio de Venta?',
            content: accion,
            buttons: {
                confirmar: function () {
                    ModificarPrecioVentaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
                },
                cancelar: function () {
                    debugger;
                    $.alert('Se cancelo la Actualizacion del Precio de Venta');
                    $('#analizar .totprecunit').val(totalpvcalc);
                }
            }
        });

    });

});

function ModificarMGAdminLineaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion)
{
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+'/ajax-actualizar-mgadmin-detalle-cotizacion',
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $('.listajaxanalisis').html(data);
            actualizar_tabla_cotizacion(data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });   
}

function ModificarMGUtilLineaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion)
{
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+'/ajax-actualizar-mgutil-detalle-cotizacion',
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $('.listajaxanalisis').html(data);
            actualizar_tabla_cotizacion(data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });   
}


function ModificarPrecioVentaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion)
{
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+'/ajax-actualizar-precio-venta-detalle-cotizacion',
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $('.listajaxanalisis').html(data);
            actualizar_tabla_cotizacion(data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });   
}

function ModificarIgvLineaAnalisis(data,data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion)
{
     abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+'/ajax-elimnar-igv-detalle-cotizacion',
            data    :   data,
            success: function (data) {
                cerrarcargando();
                $('.listajaxanalisis').html(data);
                actualizar_tabla_cotizacion(data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion);
            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });
}

function elimnarlineaanalisis(data,cotizacion_id,detalle_cotizacion_id,detalle_cotizacion_analisis_id,_token,idopcion){
    debugger;
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+'/ajax-eliminar-tabla-cotizacion-analisis',
        data    :   data,
        success: function (data) {
            $('.listajaxanalisis').html(data);
            actualizar_tabla_cotizacion(cotizacion_id,detalle_cotizacion_id,_token,idopcion);
            cerrarcargando();
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}


function actualizar_tabla_cotizacion(data_cotizacion_id,data_detalle_cotizacion_id,_token,idopcion){
    debugger;
    data                        =   {
                                        _token                  : _token,
                                        data_cotizacion_id      : data_cotizacion_id,
                                        data_detalle_cotizacion_id : data_detalle_cotizacion_id,
                                        idopcion                : idopcion
                                    };
    $.ajax({
        type    :   "POST",
        url     :   carpeta+'/ajax-actualizar-tabla-cotizacion',
        data    :   data,
        success: function (data) {
            $('.listaajaxdetallecotizar').html(data);
            debugger;
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });

}

