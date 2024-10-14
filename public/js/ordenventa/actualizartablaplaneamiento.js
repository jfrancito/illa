$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".ventacotizar").on('click','.eliminaranalisis', function() {
        debugger;
        var _token                              =   $('#token').val();
        var planeamiento_id                     =   $(this).attr('data_planeamiento_id');
        var detalle_planeamiento_id             =   $(this).attr('data_detalle_planeamiento_id');
        var detalle_planeamiento_analisis_id    =   $(this).attr('data_detalle_planeamiento_analisis_id');
        var descpartida                         =   $(this).attr('data_descpartida');
        var idopcion                            =   $('#idopcion').val();
        data                            =   {
                                                _token                      : _token,
                                                planeamiento_id               : planeamiento_id,
                                                detalle_planeamiento_id       : detalle_planeamiento_id,
                                                detalle_planeamiento_analisis_id  : detalle_planeamiento_analisis_id,
                                                idopcion                    : idopcion
                                            };
        $.confirm({
            title: '¿Confirma la eliminacion?',
            content: 'Eliminar Linea : '+descpartida,
            buttons: {
                confirmar: function () {
                    elimnarlineaanalisis(data,planeamiento_id,detalle_planeamiento_id,detalle_planeamiento_analisis_id,_token,idopcion);
                },
                cancelar: function () {
                    $.alert('Se cancelo la eliminacion');
                }
            }
        });

    });


    $(".ventacotizar").on('click','.btnagregaranalisis', function() {
        debugger;
        var _token                          =   $('#token').val();
        var idopcion                        =   $('#idopcion').val();
        var grupoanalisis_id                =   $('#grupoanalisis_id').val();
        var unidadmedidaa_id                =   $('#unidadmedidaa_id').val();
        var descripcion                     =   $('#descripcion').val();
        var cantidada                       =   $('#cantidada').val();
        var precio                          =   0;//$('#precio').val();
        var data_planeamiento_id              =   $(this).attr('data_planeamiento_id');
        var data_detalle_planeamiento_id      =   $(this).attr('data_detalle_planeamiento_id');

        //validacioones
        if(grupoanalisis_id ==''){ alerterrorajax("Seleccione una grupo de analisis."); return false;}
        if(unidadmedidaa_id ==''){ alerterrorajax("Seleccione una unidad de medida."); return false;}
        if(descripcion ==''){ alerterrorajax("Ingrese una descripcion."); return false;}
        if(cantidada ==''){ alerterrorajax("Ingrese un cantidad."); return false;}
        // if(precio ==''){ alerterrorajax("Ingrese un precio."); return false;}

        data                        =   {
                                            _token                  : _token,
                                            grupoanalisis_id        : grupoanalisis_id,
                                            unidadmedidaa_id        : unidadmedidaa_id,
                                            descripcion             : descripcion,
                                            cantidad                : cantidada,
                                            precio                  : precio,
                                            data_planeamiento_id      : data_planeamiento_id,
                                            data_detalle_planeamiento_id : data_detalle_planeamiento_id,
                                            idopcion                : idopcion
                                        };
        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+'/ajax-agregar-producto-analisis-planeamiento',
            data    :   data,
            success: function (data) {
                cerrarcargando();
                $('.listajaxanalisis').html(data);
                actualizar_tabla_cotizacion(data_planeamiento_id,data_detalle_planeamiento_id,_token,idopcion);
            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    });






});


function elimnarlineaanalisis(data,planeamiento_id,detalle_planeamiento_id,detalle_cotizacion_analisis_id,_token,idopcion){
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+'/ajax-eliminar-tabla-planeamiento-analisis',
        data    :   data,
        success: function (data) {
            $('.listajaxanalisis').html(data);
            actualizar_tabla_cotizacion(planeamiento_id,detalle_planeamiento_id,_token,idopcion);
            cerrarcargando();
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}


function actualizar_tabla_cotizacion(data_planeamiento_id,data_detalle_planeamiento_id,_token,idopcion){
    debugger;
    data                        =   {
                                        _token                          : _token,
                                        data_planeamiento_id            : data_planeamiento_id,
                                        data_detalle_planeamiento_id    : data_detalle_planeamiento_id,
                                        idopcion                        : idopcion
                                    };
    $.ajax({
        type    :   "POST",
        url     :   carpeta+'/ajax-actualizar-tabla-planeamiento',
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

