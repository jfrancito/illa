$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".saldocuenta").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var data_entidad_id              =   $(this).attr('data_entidad_id');
        var data_cuenta_id              =   $(this).attr('data_cuenta_id');


        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            data_entidad_id              : data_entidad_id,
                                            data_cuenta_id              : data_cuenta_id,
                                            idopcion                : idopcion,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-saldo-cuenta",
                  "modal-detalle-reportes","modal-detalle-reportes-container");

    });
 

});
