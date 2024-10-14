<div class="row xs-pt-15">
	<div class="col-xs-12">
		<p class="text-right">

			<span>
				<a href="{{ url('gestion-de-cotizacion/'.$idopcion) }}" class="button  btn-information opciones btnatras">
					<span class="icon mdi mdi-mail-reply btnatrascotizacionproduccion"></span>
				</a>
			</span>	
		</p>
	</div>
</div>

<div class='col-sm-12 listajaxanalisis'>
	@include('produccion.ajax.alistadetalleproduccion')
</div>


@if(isset($ajax))
	<script type="text/javascript">
		$(document).ready(function(){
			App.formElements();
			$('.importe').inputmask({ 'alias': 'numeric', 
			'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
			'digitsOptional': false, 
			'prefix': '', 
			'placeholder': '0'});
		});
	</script>
@endif
