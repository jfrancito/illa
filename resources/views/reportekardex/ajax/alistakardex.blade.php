<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Id</th>
      <th>Lote</th>
      <th>Almacen</th>
      <th>Tipo Movimiento</th>
      <th>Tipo</th>
      <th>Fecha y Hora</th>
      <th>Producto</th>
      <th>Cantidad Inicial</th>             
      <th>Cantidad Ingreso</th>             
      <th>Cantidad Salida</th>             
      <th>Cantidad Final</th>             
      <th>Motivo</th>            
    </tr>
  </thead>
  <tbody>
    @foreach($listakardex as $index => $item)
      <tr data_compra_id = "{{$item->id}}" class='activo{{$item->activo}}'>
        <td>{{$index + 1 }}</td>
        <td>{{$item->lote}}</td>
        <td>{{$item->almacen_nombre}}</td>
        <td>{{$item->tipo_movimiento_nombre}}</td>
        <td>{{$item->compraventa_nombre}}</td>
        <td>{{date_format(date_create($item->fechahora),'d-m-Y h:i A')}}</td>        
        <td>{{$item->producto_nombre}}</td>
        <td><b>{{number_format($item->cantidadinicial, 2)}}</b></td>               
        <td><b>{{number_format($item->cantidadingreso, 2)}}</b></td>               
        <td><b>{{number_format($item->cantidadsalida, 2)}}</b></td>               
        <td><b>{{number_format($item->cantidadfinal, 2)}}</b></td>      
        <td>{{$item->motivo_nombre}}</td>         
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