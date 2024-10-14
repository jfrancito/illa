  <form action="{{ url('/clonar-archivos-'.$url.'/'.$idopcion.'/'.$idregistro) }}" method="POST" name='formclonararchivos' id='formclonararchivos'>
    

  <div class="row">
    <div class="col-md-12">
        <div class="panel-body">
          <form id='formagregararchivos' name="formagregararchivos" method="POST" action="{{ url('/agregar-archivos-'.$url.'/'.$idopcion.'/'.Hashids::encode(substr($registro->id, -8))) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed" enctype="multipart/form-data">
                {{ csrf_field() }}
            @include($view.'.form.filesclonar')
          </form>
        </div>
    </div>
  </div>

  </form>