<div class="form-group">
  <label class="col-sm-3 control-label">Cliente <span class="obligatorio">(*)</span> :</label>
  <div class="col-sm-6">
    {!! Form::select( 'cliente_id', $combo_cliente, $select_cliente,
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'cliente_id',
                        'required'    => '',
                        'data-aw'     => '1'
                      ]) !!}

      @include('error.erroresvalidate', [ 'id' => $errors->has('cliente_id')  , 
                                          'error' => $errors->first('cliente_id', ':message') , 
                                          'data' => '1'])

  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label">Moneda <span class="obligatorio">(*)</span> :</label>
  <div class="col-sm-6">
    {!! Form::select( 'moneda_id', $combo_moneda, $select_moneda,
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'moneda_id',
                        'required'    => '',
                        'data-aw'     => '1'
                      ]) !!}

      @include('error.erroresvalidate', [ 'id' => $errors->has('moneda_id')  , 
                                          'error' => $errors->first('moneda_id', ':message') , 
                                          'data' => '1'])

  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label">Descripcion <span class="obligatorio">(*)</span> :</label>
  <div class="col-sm-6">

        <textarea 
        name="descripcion"
        id = "descripcion"
        class="form-control input-sm"
        rows="5" 
        cols="50"
        required = ""       
        data-aw="2">@if(isset($precotizacion)){{old('descripcion' ,$precotizacion->descripcion)}}@else{{old('descripcion')}}@endif</textarea>

        @include('error.erroresvalidate', [ 'id' => $errors->has('descripcion')  , 
                                            'error' => $errors->first('descripcion', ':message') , 
                                            'data' => '2'])

  </div>
</div>

<div class="row xs-pt-15">
  <div class="col-xs-6">
      <div class="be-checkbox">

      </div>
  </div>
  <div class="col-xs-6">
    <p class="text-right">
      <button type="submit" class="btn btn-space btn-primary btnguardarcliente">Guardar</button>
    </p>
  </div>
</div>