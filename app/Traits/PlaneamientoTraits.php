<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;

use App\Modelos\Cliente;
use App\Modelos\Planeamiento;
use App\Modelos\DetallePlaneamientoAnalisis;
use App\Modelos\DetallePlaneamiento;
use App\Modelos\Margenes;
use App\Modelos\Categoria;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait PlaneamientoTraits
{
	private function pla_lista_planeamientos($idestados='') 
	{
		if($idestados!==''){
			$cotizacion 	= 	Planeamiento::where('activo','=',1)->whereIn('estado_id',$idestados)->get();
		}
		else{
			$cotizacion 	= 	Planeamiento::where('activo','=',1)->get();
		}
	 	return  $cotizacion;
	}

	private function pla_generar_totales_detalle_planeamiento($cotizacion,$detallecotizacion) 
	{	
		// dd(compact('cotizacion','detallecotizacion'));
		$listadetalle 	= 	DetallePlaneamientoAnalisis::where('activo','=',1)
								->where('detalleplaneamiento_id','=',$detallecotizacion->id)
								->orderby('categoriaanalisis_id','asc')
								->get();
		$total 		=	0;
		$tcosto 	=	0;
		$tmobra 	=	0;
		$tservc 	=	0;
		$tcantanalisis= 0;
		foreach($listadetalle as $index=>$item){
			$total 		=	$total+$item->total;
			$tcantanalisis+= $item->cantidad;
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
		$detallecotizacion->totalcantidad 			=	$tcantanalisis;

		$detallecotizacion->subtotalpunitarioprev	=	($total+$impuesto01+$impuesto02);
		
		$detallecotizacion->subtotalpunitario		=	$totalpreciounitario;

		if($detallecotizacion->swactualizado==0){
			$detallecotizacion->totalpreciounitario 	= 	round($totalpreciounitario/$detallecotizacion->cantidad,2);
		}
		$detallecotizacion->totalpreciounitariocalc = 	round($totalpreciounitario/$detallecotizacion->cantidad,2);

		$detallecotizacion->precio_unitario 		= 	$detallecotizacion->totalpreciounitario;
		$detallecotizacion->total 					= 	$detallecotizacion->precio_unitario * $detallecotizacion->cantidad;
		
		
		$detallecotizacion->fecha_mod 	 			=   date('Ymd h:i:s');
		$detallecotizacion->usuario_mod 			=   Session::get('usuario')->id;
		$detallecotizacion->save();

		$listadetallecotizacion 					= 	DetallePlaneamiento::where('activo','=',1)
														->where('planeamiento_id','=',$cotizacion->id)->get();

		$total 										=	0;
		$totalcantidad 								=	0;
		$totalcantidadanalisis 						=	0;

		foreach($listadetallecotizacion as $index=>$item){
			$total 					=	$total+$item->total;
			$totalcantidad 			=	$totalcantidad+$item->cantidad;
			$totalanalisis 			=	0;
			$listadetalleanalisis 	=	DetallePlaneamientoAnalisis::where('planeamiento_id','=',$cotizacion->id)
										->where('detalleplaneamiento_id','=',$item->id)
										->where('activo','=',1)->get();
			$detalle 	=	DetallePlaneamiento::find($item->id);
			foreach ($listadetalleanalisis as $idexa => $analisis) {
				$totalanalisis 	+=	$analisis->cantidad;
			}
			$detalle->totalcantidad =	$totalanalisis;
			$detalle->fecha_mod 	=   date('Ymd h:i:s');
			$detalle->usuario_mod 	=   Session::get('usuario')->id;
			$detalle->save();
			$totalcantidadanalisis 	+=	$totalanalisis;
		}

		// $cotizacion->igv 							=	
		$cotizacion->total 							= 	$total;
		$cotizacion->totalcantidad					= 	$totalcantidad;
		$cotizacion->totalcantidadanalisis			= 	$totalcantidadanalisis;
		// $cotizacion->total_analisis 				= 	$total;
		$cotizacion->fecha_mod 	 					=   date('Ymd h:i:s');
		$cotizacion->usuario_mod 					=   Session::get('usuario')->id;
		$cotizacion->save();


	}

	private function pla_generar_totales_planeamiento($cotizacion) 
	{
		$listadetallecotizacion 					= 	DetallePlaneamiento::where('activo','=',1)
														->where('planeamiento_id','=',$cotizacion->id)->get();
		$total 										=	0;
		$totalcantidad 								=	0;
		$totalcantidad 								=	0;
		$totalcantidadanalisis 			=	0;
		foreach($listadetallecotizacion as $index=>$item){
			$total 					=	$total+$item->total;
			$totalcantidad			=	$totalcantidad+$item->cantidad;
			$detalle 				=	DetallePlaneamiento::find($item->id);
			$totalanalisis 			=	0;
			$listadetalleanalisis 	= DetallePlaneamientoAnalisis::where('planeamiento_id','=',$cotizacion->id)
											->where('detalleplaneamiento_id','=',$item->id)
											->where('activo','=',1)->get();
			foreach ($listadetalleanalisis as $idexa => $analisis) {
				$totalanalisis 	+=	$analisis->cantidad;
			}
			$detalle->totalcantidad =	$totalanalisis;
			$detalle->fecha_mod 	=   date('Ymd h:i:s');
			$detalle->usuario_mod 	=   Session::get('usuario')->id;
			$detalle->save();
			$totalcantidadanalisis 	+=	$totalanalisis;
		}

		$cotizacion->total 							= 	$total;
		$cotizacion->totalcantidad					= 	$totalcantidad;
		$cotizacion->totalcantidadanalisis			= 	$totalcantidadanalisis;

		$cotizacion->fecha_mod 	 					=   date('Ymd H:i:s');
		$cotizacion->usuario_mod 					=   Session::get('usuario')->id;
		$cotizacion->save();
	}
}