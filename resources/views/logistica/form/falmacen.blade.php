<fieldset class="scheduler-border">
  <legend class="scheduler-border">Datos del Almacen</legend>
    <div class="control-group">

    <div class="row">
          
      <div class="ajaxnotapedido">


        <div class="col-sm-1">
          <div class="form-group">
            <label class="control-label">Codigo</label>
            <div class="col-sm-12">

                <input  type="text"
                        style="width: 70px;" 
                        id="codigo" name='codigo' 
                        value="@if(isset($almacen)){{old('codigo' ,$almacen->codigo)}}@else{{old('codigo' ,$cod_almacen)}}@endif"
                        value="{{ old('codigo') }}"                         
                        placeholder="Codigo"
                        readonly = "readonly"
                        required = ""
                        autocomplete="off" class="form-control input-sm" data-aw="1"/>

                @include('error.erroresvalidate', [ 'id' => $errors->has('codigo')  , 
                                                    'error' => $errors->first('codigo', ':message') , 
                                                    'data' => '1'])

            </div>
          </div>
        </div>       

        <div class="col-sm-5">
          <div class="form-group">
            <label class="control-label">Nombre</label>
            <div class="col-sm-12">
              <input  type="text"
                      id="nombre" name='nombre' 
                      value="@if(isset($almacen)){{old('nombre' ,$almacen->nombre)}}@else{{old('nombre')}}@endif"
                      value="{{ old('nombre') }}"                         
                      placeholder="Nombre"
                      required = ""
                      maxlength="500"                     
                      autocomplete="off" class="form-control input-sm" data-aw="2"/>

              @include('error.erroresvalidate', [ 'id' => $errors->has('nombre')  , 
                                            'error' => $errors->first('nombre', ':message') , 
                                            'data' => '2'])
            </div>
          </div>
        </div>



      </div>     
    </div>

    
    <div class="col-sm-12">
        <table id='listadetallealmacen'  class="table table-striped table-borderless" >
            <thead>
              <tr>
                  <th>ID</th>
                  <th>PROVEEDOR</th>
                  <th>PRODUCTO</th>
                  <th>STOCK</th>                  
              </tr>
            </thead>
            <tbody>            
            @if(isset($almacen))
              @foreach($almacen->detalle as $index => $item)
                <tr data_detallealmacen_id = "{{$item->id}}" class='activo{{$item->activo}}'>
                  <td>{{$index + 1 }}</td>
                  <td>{{$item->proveedor_nombre}}</td>
                  <td>{{$item->producto_nombre}}</td>
                  <td>{{number_format($item->stock, 2)}}</td>
                </tr>                    
              @endforeach     
            @endif                       
            </tbody>
        </table>          
    </div>


    </div>
</fieldset>



<div class="row xs-pt-15">
  <div class="col-xs-6">
      <div class="be-checkbox">

      </div>
  </div>
  <div class="col-xs-6">
    <p class="text-right">      
        <button type="submit" class="btn btn-space btn-primary btnguardarcompra">Guardar</button>      
    </p>
  </div>
</div>