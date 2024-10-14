<!DOCTYPE html>

<html lang="es">

<head>
	<title>Factura ({{$doc->serie}}-{{$doc->numero}}) </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/x-icon" href="{{ asset('public/favicon.ico') }}"> 
	{{-- <link rel="stylesheet" type="text/css" href="{{ asset('public/css/pdf.css') }} "/> --}}

	<style type="text/css">
		
		.izquierda{
			text-align: right;
		}

		.menu{
		    overflow:hidden;
		    width 	: 730px;
		    display : table;
		    /*border 	: 1px solid black;*/
		}

		.menu .left{
		    width	: 	50%
		    float	:	left;
		    display : 	table-cell; 
		    text-align: center;     
		}


		.menu .right{
		    width	: 	50%
		    float	:	left;
		    border  :	1px solid black; 
		    display : 	table-cell; 
		    text-align: center; 
		    border-radius: 4px ;    
		}

		.menu .left h1{
			font-size:  1.2em;
			/*border   :  1px solid red;*/
		}
		.menu .left h3{
			font-size:  0.8em;
			font-weight: normal;
			/*border   :  1px solid red;*/
		}
		.menu .left h4{
			font-size:  0.8em;
			font-weight: normal;	
			/*border: 1px solid blue;*/
		}

		.top .det1{
			width: 718px;
			font-size: 0.8em;
			margin-top: 5px;
			border: 1px solid #000;
			border-radius: 4px;
			padding: 5px;

		}
		.top .det1 p{
			margin-top: 1px;
			margin-bottom: 3px;
		}

		.det2{
			margin-top: 5px;
		    overflow:hidden;
		    width 	: 730px;
		    display : table;
			border: 1px solid #000;
			border-radius: 4px;
		    font-size: 0.8em;
		    padding: 5px;
		}

		.det2 .d1,.det2 .d2,.det2 .d3{
		    width	: 	32%
		    float	:	left;
		    display : 	table-cell;     
		}

		table {
		    border-collapse: collapse;
/*		    width 	: 100% !important;*/
		    width 	: 730px !important;
/*		    background-color: red !important;*/
			margin-top: 15px;
		    font-size: 0.7em;    
		}

		th, td {
		    padding: 8px;
		    text-align: left;
		    border-bottom: 1px solid #ddd;
		}

		.titulo{
			text-align: center;
		}
		.codigo{
			width: 40px;
		}
		.descripcion{
			width: 250px;
		}
		.unidad{
			width: 20px;
		}
		.cantidad{
			width: 20px;
		}
		.precio{
			width: 40px;
		}
		.importe{
			width: 55px;
		}


		.totales{
			margin-top: 10px;
		    overflow:hidden;
		    width 	: 730px;
		    display : table;
		    /*border 	: 1px solid black;*/
		}

		.totales .left{
		    width	: 	65%
		    float	:	left;
		    display : 	table-cell;  
		   	/*border      : 1px solid red;  */ 
		}


		.totales .right{
		    width	: 	35%
		    float	:	left;
		    /*border  :	1px solid black; */
		    display : 	table-cell; 
		      
		}

		.totales .right p{
			font-size 	: 0.75em;
			margin-top	: 0px;
			margin-bottom 	: 1px;	

		}

		.totales .right .descripcion{
			display 	: inline-block;
			width 		: 55%;

		}
		.totales .right .monto{
			display 	: inline-block;
			width 		: 40%;

		}

		.totales .left .uno{
		    display     : inline-block;
		    width       : 25%;
		}
		.totales .left .dos{
		    /*border: 1px solid blue;   */ 
		    display     : inline-block;
		    width       : 70%;
		    font-size   : 0.75em;

		}
		.totales .left .dos p{
		    margin-top: 5px;
		    margin-bottom: 5px;
		}
		.totales .left .derecha{
		    margin-top: 55px;
		}
		.totales .left .uno img{
		    /*border: 1px solid red;*/
		    width: 100px;
		    position: absolute;
		    top: -87px;

		}
		footer .observacion{
		    border-top: 1px solid #000;
		    border-bottom:  1px solid #000;
		}
		footer .observacion h3 {
		    /*border: 1px solid red;*/
		    margin-top: 2px;
		    margin-bottom: 2px;
		    font-size: 0.9em;
		}
		footer .observacion p {
		    /*border: 1px solid red;*/
		    margin-top: 0px;
		    margin-bottom: 2px;    
		    font-size: 0.8em;
		}


	</style>

</head>

<body>
    <header>
	<div class="menu">
	    <div class="left">
	    		<h1>{{$razonsocial}}</h1> 
	    		<h3>{{$direccion}} {{$departamento}} - {{$provincia}} - {{$distrito}}</h3>
	    		<h4>Teléfono : {{$telefono}}</h4>    
	    </div>
	    <div class="right">
	    		<h3>R.U.C. {{$ruc}}</h3> 
	    		<h3>{{$titulo}}</h3>
	    		<h3>{{$doc->serie}}-{{$doc->numero}}</h3> 

	    </div>
	</div>
    </header>
    <section>
        <article>

			<div class="top">
			    <div class="det1">
	   				<p>
	   					<strong>Señor (es) :</strong> {{$doc->proveedor_nombre}}
	   				</p>  		    		   					   				
	   				<p>
	   					<strong>RUC :</strong> {{$doc->proveedor->numerodocumento}}	   					
	   				</p>
	   				<p>
	   					<strong>Dirección :</strong> {{$doc->proveedor->direccion}}
	   				</p>					
			    </div>

			    <div class="det2">

	   				<p class="d1">
	   					<strong>Fecha de Emisión :</strong> {{date_format(date_create($doc->fecha), 'd/m/Y')}}
	   				</p>  		    	
	   				<p class="d2">
	   					<strong>Fecha de Vencimiento :</strong> {{date_format(date_create($doc->fecha), 'd/m/Y')}}
	   				</p>
	   				<p class="d3">
	   					<strong>Condición de Pago  :</strong> Contado
	   				</p>
	


			    </div>
			</div>
        </article>
        <article>

		  <table class="">
		    <tr>
		      <th class='titulo codigo'>CODIGO</th>
		      <th class='descripcion'>DESCRIPCIÓN</th>
		      <th class='titulo unidad'>UNIDAD</th>
		      <th class='titulo cantidad'>CANTIDAD</th>
		      <th class='titulo precio'>PRECIO</th>
		      <th class='titulo precio'>SUBTOTAL</th>
		      <th class='titulo precio'>IGV</th>
		      <th class='titulo importe'>IMPORTE</th>
		    </tr>


		    @foreach($doc->detalle as $item)
			    <tr>			    	
			      <td class='titulo'>{{$item->producto->codigo}}</td>
			      <td>{{$item->producto_nombre}}</td>
			      <td class='titulo'>{{$item->producto->unidad_medida_nombre}}</td>
			      <td class='titulo'>{{number_format(round($item->cantidad,2),2,'.','')}}</td>
			      <td class='titulo'>{{number_format(round($item->preciounitario,2),2,'.','')}}</td>
			      <td class='titulo'>{{number_format(round($item->subtotal,2),2,'.','')}}</td>
			      <td class='titulo'>{{number_format(round($item->igv,2),2,'.','')}}</td>
			      <td class='izquierda'>{{number_format(round($item->total,2),2,'.',',')}}</td>
			    </tr>
		    @endforeach		    

		    <tr>
		      <td  colspan="8">SON : {{$letras}}</td>
		    </tr>

		  </table>

        </article>

        <article>
			<div class="totales">
				<div class="left">			    	
			    </div>
			    <div class="right">
			    		<p class='descripcion izquierda'>
			    			SUB TOTAL S/
			    		</p>
			    		<p class='monto izquierda'>
			    			{{number_format(round($subtotal,2),2,'.',',')}}
			    		</p>
			    		<br>			    		
			    		<p class='descripcion izquierda'>
			    			IGV S/
			    		</p>
			    		<p class='monto izquierda'>
			    			{{number_format(round($igv,2),2,'.',',')}}
			    		</p>
			    		<br>	
			    		<p class='descripcion izquierda'>
			    			IMPORTE TOTAL  S/
			    		</p>
			    		<p class='monto izquierda'>
			    			{{number_format(round($total,2),2,'.',',')}}
			    		</p>
			    </div>
			</div>
        </article>
    </section>    
</body>
</html>