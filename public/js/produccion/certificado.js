$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".certificado").on('change','#institucion_id', function() {

        event.preventDefault();
        var institucion_id       =   $('#institucion_id').val();
        var _token               =   $('#token').val();
        //validacioones
        if(institucion_id ==''){ alerterrorajax("Seleccione una institucion."); return false;}
        data            =   {
                                _token              : _token,
                                institucion_id      : institucion_id
                            };

        ajax_normal_combo(data,"/ajax-combo-periodo-xinstitucion","ajax_periodo")                    

    });




});