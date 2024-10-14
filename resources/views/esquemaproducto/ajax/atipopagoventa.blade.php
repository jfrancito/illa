<div class="form-group">
          <label class="control-label">Tipo Pago</label>
          <div class="col-sm-12">
            {!! Form::select( 'tipo_pago_id', $combo_tipo_pago, $select_tipo_pago,
                              [
                                'class'       => 'form-control control input-sm tipo_pago_id select2 select3' ,
                                'id'          => 'tipo_pago_id',
                                'required'    => '',        
                                'data-aw'     => '8'
                              ]) !!}

            @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_pago_id')  , 
                                                'error' => $errors->first('tipo_venta_id', ':message') , 
                                                'data' => '9'])
          </div>
        </div>

  @if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
    App.formElements();
      $(".select3").select2();
    });
        
  </script> 
  @endif