
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$entidad->entidad}}
		</h5>
	</div>
</div>
<div class="modal-body">

	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 

	<table class="table table-condensed table-striped">
	    <thead>
	      <tr>
	      	<th>LINEA</th>
	      	<th>TIPO MOVIMIENTO</th>
	      	<th>OPERACION</th>
	        <th>ENTIDAD</th>
	        <th>CUENTA</th>
	        <th>DOCUMENTO</th>
	        <th>CLIENTE</th>
	        <th>FECHA</th>
	        <th>SALDO</th>
	      </tr>
	    </thead>
	    <tbody>
	    @foreach($listadatos as $index => $item)
	      	<tr>
	      	   <td>{{$item->index + 1}}</td>
	      	   <td>{{$item->tipo_movimiento_nombre}}</td>
	      	   <td>{{$item->tabla_movimiento}}</td>
		       <td>{{$item->entidad_nombre}}</td>
		       <td>{{$item->nrocta}}</td>
		       <td>{{$item->serie}}-{{$item->numero}}</td>
		       <td>{{$item->cliente_nombre}}</td>
		       <td>{{$item->fecha_crea}}</td>
		       <td>{{$item->tipo_movimiento * $item->total}}</td>
	      	</tr>                  
	    @endforeach
	    </tbody>
	    <tfoot>
	      <tr>
	      	<th colspan="8">Totales</th>
	      	<th>{{number_format($listadatos->sum("tt"), 2, '.', ',')}}</th>
	      </tr>
	    </tfoot>
	</table>
	</div>
</div>

<div class="modal-footer">

	<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
</div>




