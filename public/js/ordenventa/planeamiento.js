$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $('#evaluarcotizacion').on('click', function(event){
        event.preventDefault();
        data = dataenviar();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos una Cotizacion");return false;}
        var datastring = JSON.stringify(data);
        $('#pedido').val(datastring);

        $.confirm({
            title: '¿Confirma Terminar la Evaluacion?',
            content: 'Terminar de Evaluar la Cotizacion',
            buttons: {
                confirmar: function () {
                    abrircargando();
                    $( "#formpedido" ).submit();
                },
                cancelar: function () {
                    $.alert('Se cancelo la Evaluacion');
                }
            }
        });

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


    $('#enviarcotizacion').on('click', function(event){
        event.preventDefault();
        data = dataenviar();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos una Cotizacion");return false;}
        var datastring = JSON.stringify(data);
        $('#pedido').val(datastring);

        $.confirm({
            title: '¿Confirma Emitir la Evaluacion?',
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
        debugger;
        var _token                      =   $('#token').val();
        var data_planeamiento_id        =   $(this).attr('data_planeamiento_id');
        var detalle_planeamiento_id     =   $(this).attr('data_detalle_planeamiento_id');
        var idopcion                    =   $('#idopcion').val();

        data                            =   {
                                                _token                      : _token,
                                                data_planeamiento_id               : data_planeamiento_id,
                                                detalle_planeamiento_id       : detalle_planeamiento_id,
                                                idopcion                    : idopcion
                                            };

        var section                     =   'analizar';
        $('.nav-tabs a[href="#analizar"]').tab('show');

        ajax_normal_section(data,"/ajax-analizar-detalle-planeamiento",section);                                    

    });



    function desmarcarchecks(nombrecheck){
            // debugger;
            $(".tlistacategorias tr").each(function(){
                // debugger;
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
        var planeamiento_id           =   $(this).attr('data_cotizacion');
        var idopcion                =   $('#idopcion').val();
        data                        =   {
                                            _token                  : _token,
                                            planeamiento_id           : planeamiento_id,
                                            idopcion                : idopcion,
                                            idcategoria             : idcategoria,
                                        };
        ajax_modal(data,"/ajax-modal-configuracion-planeamiento-detalle",
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
        var detalle_planeamiento_id       =   $(this).attr('data_detalle_planeamiento_id');
        var idopcion                    =   $('#idopcion').val();

        data                            =   {
                                                _token                      : _token,
                                                cotizacion_id               : cotizacion_id,
                                                detalle_planeamiento_id       : detalle_planeamiento_id,
                                                idopcion                    : idopcion
                                            };

        ajax_modal(data,"/ajax-modal-modificar-configuracion-cotizacion-detalle",
                  "modal-configuracion-cotizacion-modelo-detalle","modal-configuracion-cotizacion-modelo-detalle-container");

    });


    $(".ventacotizar").on('click','.eliminarcotizacion', function() {
        debugger;
        var _token                      =   $('#token').val();
        var data_planeamiento_id             =   $(this).attr('data_planeamiento_id');
        var detalle_planeamiento_id     =   $(this).attr('data_detalle_planeamiento_id');
        var descpartida                 =   $(this).attr('data_descpartida');
        var idopcion                    =   $('#idopcion').val();

        data                            =   {
                                                _token                      : _token,
                                                data_planeamiento_id               : data_planeamiento_id,
                                                detalle_planeamiento_id       : detalle_planeamiento_id,
                                                idopcion                    : idopcion
                                            };
        $.confirm({
            title: '¿Confirma la eliminacion?',
            content: 'Eliminar Linea : '+ descpartida,
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
        var data_planeamiento_id        =   $(this).attr('data_cotizacion_id');
        var detalle_planeamiento_id     =   $('#idcategoria').val();
        
        if(detalle_planeamiento_id==''){
            alerterrorajax('DEBE SELECCIONAR UNA FILA');
            return false;
        }

        data                            =   {
                                                _token                      : _token,
                                                data_planeamiento_id        : data_planeamiento_id,
                                                detalle_planeamiento_id     : detalle_planeamiento_id,
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



    function elimnarservicio(data){
        ajax_normal_cargar(data,"/ajax-elimnar-servicio-linea-planeamiento");
    }

    function elimnarlinea(data){
        ajax_normal_cargar(data,"/ajax-elimnar-linea-planeamiento");
    }

});
