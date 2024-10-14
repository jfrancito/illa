<div class="form-group">
  <label class="col-sm-3 control-label">Cliente:</label>
  <div class="col-sm-6">
  <input type="text" disabled class="form-control control input-sm" value="{{ $cotizacion->cliente_nombre }}">
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label">Lote:</label>
  <div class="col-sm-6">
  <input type="text" disabled class="form-control control input-sm" value="{{ $cotizacion->lote }}">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label">Descripcion de Extorno<span class="obligatorio">(*)</span> :</label>
  <div class="col-sm-6">

        <textarea 
        name="descripcion"
        id = "descripcion"
        class="form-control input-sm validarmayusculas"
        rows="5" 
        cols="50"
        required = ""       
        data-aw="2">@if(isset($cotizacion->extorno)){{old('descripcion' ,$cotizacion->extorno->descripcion)}}@else{{old('descripcion')}}@endif</textarea>

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
      <a href="{{ url('/gestion-de-cotizacion/'.$idopcion) }}"><button type="button" class="btn btn-space btn-danger btncancelar">Cancelar</button></a>
      <button type="submit" class="btn btn-space btn-primary btnguardarcliente">Guardar</button>
    </p>
  </div>
</div>