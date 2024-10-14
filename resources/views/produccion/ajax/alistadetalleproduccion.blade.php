<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Item</th>
      <th>Categoria</th>
      <th>SubCategoria</th>
      <th>Producto</th>
      <th>Unidad Medida</th>
      <th>Cantidad</th>
      <th>Precio Unitario</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadetalle as $index => $item)
      <tr >
        <td>{{$index + 1}}</td>
        <td><b>{{$item->categoria_nombre}}</b></td>
        <td>{{$item->subcategoria_nombre}}</td>
        <td><b>{{$item->producto_nombre}}</b></td>
        <td>{{$item->unidadmedida_nombre}}</td>
        <td class="text-right">{{number_format($item->cantidad,0,'',',')}}</td>
        <td class="text-right">{{number_format($item->precio_unitario,2,'.',',')}}</td>
        <td class="text-right">{{number_format($item->total,2,'.',',')}}</td>
      </tr>                    
    @endforeach
  </tbody>
  <tfooter>
      <tr>
        <td colspan="7" class="text-right"><b>TOTAL : </b></td>
        <td class="text-right"><b>{{number_format($produccion->total,2,'.', ',')}}</b></td>
      </tr>
  </tfooter> 
</table>
