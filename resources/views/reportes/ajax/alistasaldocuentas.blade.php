<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>#</th>
      <th>BANCO</th>
      <th>NRO CTA</th>
      <th>MONEDA</th>

      <th>SALDO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_entidad_id = "{{$item->entidad_id}}"
        data_cuenta_id = "{{$item->cuenta_id}}" 
        class='dobleclickpc seleccionar'
        style="cursor: pointer;">
        <td >  
          {{$index+1}}
        </td>
        <td>{{$item->entidad_nombre}}</td>
        <td>{{$item->nrocta}}</td>
        <td>{{$item->moneda_nombre}}</td>

        <td><b>{{$item->total}}</b></td>
      </tr>                    
    @endforeach
  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif