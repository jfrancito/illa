  <div class="col-sm-1">
    <div class="form-group">
      <label class="control-label">Serie</label>
      <div class="col-sm-12">
        <input  type="text"
                style="width: 70px;" 
                id="serie" name='serie' 
                value="@if(isset($serie)){{old('serie' ,$serie)}}@else{{old('serie')}}@endif"
                value="{{ old('serie') }}"                         
                placeholder="Serie"
                required = ""
                maxlength="4" 
                autocomplete="off" class="form-control input-sm seriekeypress" data-aw="2"/>

        @include('error.erroresvalidate', [ 'id' => $errors->has('serie')  , 
                                      'error' => $errors->first('serie', ':message') , 
                                      'data' => '2'])
      </div>
    </div>
  </div>

  <div class="col-sm-2">
    <div class="form-group">
      <label class="control-label">Numero</label>
      <div class="col-sm-12">
        <input  type="text"
                id="numero" name='numero' 
                value="@if(isset($numero)){{old('numero' ,$numero)}}@else{{old('numero')}}@endif"
                value="{{ old('numero') }}"                         
                placeholder="Numero"
                required = ""
                maxlength="8"                     
                autocomplete="off" class="form-control input-sm numero" data-aw="3"/>

        @include('error.erroresvalidate', [ 'id' => $errors->has('numero')  , 
                                      'error' => $errors->first('numero', ':message') , 
                                      'data' => '3'])
      </div>
    </div>
  </div>

  @if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
    App.formElements();
    });
        
  </script> 
  @endif