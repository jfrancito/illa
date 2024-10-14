<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;

use App\Modelos\Cliente;
use App\Modelos\Cotizacion;
use App\Modelos\DetalleCotizacionAnalisis;
use App\Modelos\DetalleCotizacion;
use App\Modelos\Margenes;
use App\Modelos\Categoria;
use App\Modelos\Produccion;
use App\Modelos\DetalleProduccion;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait CotizacionTraits
{

	private function cot_lista_produccion($idestados='') {
		if($idestados!==''){
			$cotizacion 	= 	Produccion::where('activo','=',1)->whereIn('estado_id',$idestados)->get();
		}
		else{
			$cotizacion 	= 	Produccion::where('activo','=',1)->get();
		}
	 	return  $cotizacion;
	}

	private function cot_lista_cotizaciones($idestados='') {
		if($idestados!==''){
			$cotizacion 	= 	Cotizacion::where('activo','=',1)->whereIn('estado_id',$idestados)->get();
		}
		else{
			$cotizacion 	= 	Cotizacion::where('activo','=',1)->get();
		}
	 	return  $cotizacion;
	}

	private function cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion) {

		$listadetalle 								= 	DetalleCotizacionAnalisis::where('activo','=',1)
														->where('detallecotizacion_id','=',$detallecotizacion->id)
														->orderby('categoriaanalisis_id','asc')
														->get();
		$total 		=	0;
		$tcosto 	=	0;
		$tmobra 	=	0;
		$tservc 	=	0;
		foreach($listadetalle as $index=>$item){
			$total 		=	$total+$item->total;
			switch ($item->categoriaanalisis_nombre) {
				case 'MATERIAL E ISNUMOS':
					$tcosto+=$item->total;
					break;
				case 'MANO DE OBRA':
					$tmobra+=$item->total;
					break;
				case 'SERVICIO':
					$tservc+=$item->total;
					break;
			}
		}
		$impuesto01 								=	$total*$detallecotizacion->mgadministrativos;
		$impuesto02 								=	$total*$detallecotizacion->mgutilidad;
		$igv 										= 	0;

		if($detallecotizacion->swigv==1){
			$totalpreciounitario 					=	($total+$impuesto01+$impuesto02)*(1+$detallecotizacion->migv);
			$igv 									=	($total+$impuesto01+$impuesto02)*($detallecotizacion->migv);
		}
		else{
			$totalpreciounitario 					=	($total+$impuesto01+$impuesto02);
		}
		
		$detallecotizacion->total_analisis 			= 	$total;
		$detallecotizacion->impuestoanalisis_01 	= 	$impuesto01;
		$detallecotizacion->impuestoanalisis_02 	= 	$impuesto02;
		$detallecotizacion->totalcosto				=	$tcosto;
		$detallecotizacion->totalmanoobra 			=	$tmobra;
		$detallecotizacion->totalservicio 			=	$tservc;
		$detallecotizacion->igv 					=	$igv;

		$detallecotizacion->subtotalpunitarioprev	=	($total+$impuesto01+$impuesto02);
		
		$detallecotizacion->subtotalpunitario		=	$totalpreciounitario;

		if($detallecotizacion->swactualizado==0){
			$detallecotizacion->totalpreciounitario 	= 	round($totalpreciounitario/$detallecotizacion->cantidad,2);
		}
		$detallecotizacion->totalpreciounitariocalc = 	round($totalpreciounitario/$detallecotizacion->cantidad,2);
		// $detallecotizacion->precio_unitario 		= 	$totalpreciounitario;
		// $detallecotizacion->total 					= 	$totalpreciounitario * $detallecotizacion->cantidad;
		$detallecotizacion->precio_unitario 		= 	$detallecotizacion->totalpreciounitario;
		
		$detallecotizacion->total 					= 	$detallecotizacion->precio_unitario * $detallecotizacion->cantidad;
		
		
		$detallecotizacion->fecha_mod 	 			=   date('Ymd h:i:s');
		$detallecotizacion->usuario_mod 			=   Session::get('usuario')->id;
		$detallecotizacion->save();

		$listadetallecotizacion 					= 	DetalleCotizacion::where('activo','=',1)
														->where('cotizacion_id','=',$cotizacion->id)->get();

		$total 										=	0;
		foreach($listadetallecotizacion as $index=>$item){
			$total 									=	$total+$item->total;
		}
		// $cotizacion->igv 							=	
		$cotizacion->total 							= 	$total;
		// $cotizacion->total_analisis 				= 	$total;
		$cotizacion->fecha_mod 	 					=   date('Ymd h:i:s');
		$cotizacion->usuario_mod 					=   Session::get('usuario')->id;
		$cotizacion->save();


	}

	private function cot_generar_totales_cotizacion($cotizacion) 
	{
		$listadetallecotizacion 					= 	DetalleCotizacion::where('activo','=',1)
														->where('cotizacion_id','=',$cotizacion->id)->get();
		$total 										=	0;
		foreach($listadetallecotizacion as $index=>$item){
			$total 									=	$total+$item->total;
		}
		$cotizacion->total 							= 	$total;
		$cotizacion->fecha_mod 	 					=   date('Ymd H:i:s');
		$cotizacion->usuario_mod 					=   Session::get('usuario')->id;
		$cotizacion->save();
	}



	private function cot_generar_totales_produccion($cotizacion) 
	{
		$listadetallecotizacion 					= 	DetalleProduccion::where('activo','=',1)
														->where('produccion_id','=',$cotizacion->id)->get();
		$total 										=	0;
		foreach($listadetallecotizacion as $index=>$item){
			$total 									=	$total+$item->total;
		}

		$cotizacion->subtotal 						= 	$total;
		$cotizacion->total 							= 	$total*$cotizacion->cantidad;
		$cotizacion->fecha_mod 	 					=   date('Ymd H:i:s');
		$cotizacion->usuario_mod 					=   Session::get('usuario')->id;
		$cotizacion->save();
	}

	


}