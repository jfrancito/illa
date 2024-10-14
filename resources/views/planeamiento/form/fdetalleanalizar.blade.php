<div class="row">
      <div class="col-xs-12">
            <div class="panel-heading panel-heading-divider formtext">{{$detallecotizacion->descripcion}}</div>
      </div>
      



</div>



<div class='col-sm-12 listajaxanalisis'>
  @include('cotizacion.ajax.alistadetalleanalizardetalle')
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
