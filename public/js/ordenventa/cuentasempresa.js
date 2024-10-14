
$(document).ready(function(){
	var carpeta = $("#carpeta").val();

  
	$('.btnguardarregistro').on('click',function(e){
		debugger;
		let cuenta = $('#nrocta').val();
		let numerosEncontrados = cuenta.match(/\d/g); // Encuentra todos los dígitos en el cuenta
		if (numerosEncontrados !== null && numerosEncontrados.length === 0) {
			alerterrorajax('EL NUMERO DE CTA DEBE TENER NUMEROS');
			$('#nrocta').focus();
			return false;
		}
		debugger;

		let cuentacci = $('#nroctacci').val();
		if(cuentacci.length>0){
			let numerosEncontradoscci = cuentacci.match(/\d/g); // Encuentra todos los dígitos en el cuenta
			if (!(numerosEncontradoscci !== null && numerosEncontradoscci.length === 20)) {
				alerterrorajax('EL NUMERO DE CTA CCI DEBE TENER 20 DIGITOS COMO MINIMO');
				$('#nroctacci').focus();
				return false;
			}
		}
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

    // //INPUT NUMERO DE CTA
    // $(".validarnrocta").keydown(function(event){
    //     // 48-57 son los códigos ASCII para los números del 0 al 9
    //     // 189 es el código ASCII para el guion "-"
    //     // 8 es el código ASCII para la tecla de retroceso (backspace)
    //     if((event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode === 189 || event.keyCode === 8){
    //         return true;
    //     } else {
    //         return false;
    //     }
    // });


});


