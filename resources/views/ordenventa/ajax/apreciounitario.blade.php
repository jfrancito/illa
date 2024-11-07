<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
  <div class="form-group">
    <label class="col-sm-12 control-label labelleft negrita" >Precio Unitario <span class="obligatorio">(*)</span> :</label>
    <div class="col-sm-12">

        <input  type="text"
                id="preciounitario" name='preciounitario' 
                value="@if(isset($producto)){{old('preciounitario' ,$producto->precio_venta)}}@else{{old('preciounitario')}}@endif"
                placeholder="Precio Unitario"
                autocomplete="off" class="form-control input-sm importe" data-aw="3"/>

    </div>
  </div>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

  // App.init();
        // App.formElements();
      $('.importe').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});
    });
  </script>
@endif