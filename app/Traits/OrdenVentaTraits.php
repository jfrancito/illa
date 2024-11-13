<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\Categoria;
use App\Modelos\Estado;
use App\Modelos\Conei;

use App\Modelos\OrdenVenta;
use App\Modelos\DetalleOrdenVenta;

use App\Modelos\EsquemaProducto;
use App\Modelos\DetalleEsquemaProducto;
use App\Modelos\Requerimiento;
use App\Modelos\Archivo;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait OrdenVentaTraits
{

	public function ov_calculo_total_orden_venta($ordenventa_id)
	{

		$tcosto_unitario 	  = (float)EsquemaProducto::where('ordenventa_id','=',$ordenventa_id)->sum('costo_unitario');

		$orden_venta 	  	  = OrdenVenta::where('id','=',$ordenventa_id)->first();
		$total_margen 		  = $tcosto_unitario + $orden_venta->descuento_shopify + $orden_venta->checkout + $orden_venta->shipping +$orden_venta->papeleria;
		$utilidad 		  	  = $orden_venta->venta - $total_margen;
		$margen 		  	  = ($utilidad / $orden_venta->venta)*100;


		OrdenVenta::where('id','=',$ordenventa_id)
							->update(
								[
									'total_produccion'=>$tcosto_unitario,
									'total_margen'=>$total_margen,
									'utilidad'=>$utilidad,
									'margen'=>$margen,
									'fecha_mod'=>$this->fechaactual,
									'usuario_mod'=>Session::get('usuario')->id,
								]
							);

	}


	public function ov_precio_gramo_ultima_ov($producto_id)
	{
		$valor 				  =	0;
		$esquemaproducto 	  = EsquemaProducto::where('producto_id','=',$producto_id)->where('precio_x_gramo','>',0)->where('activo','=','1')->orderby('fecha_crea','desc')->first();
		if(count($esquemaproducto)>0){
					$valor 				  =	$esquemaproducto->precio_x_gramo;
		}
		return $valor;
	}

	public function ov_total_engaste_ultima_ov($producto_id)
	{
		$valor 				  =	0;
		$esquemaproducto 	  = EsquemaProducto::where('producto_id','=',$producto_id)->where('precio_total_engaste','>',0)->where('activo','=','1')->orderby('fecha_crea','desc')->first();
		if(count($esquemaproducto)>0){
					$valor 				  =	$esquemaproducto->precio_total_engaste;
		}
		return $valor;
	}

	public function ov_costo_unitario_gemas_ov($gema_id)
	{
		$valor 				  =	0;
		$esquemaproducto 	  = DetalleEsquemaProducto::where('tipo_id','=',$gema_id)->where('costo_unitario','>',0)->where('activo','=','1')->orderby('fecha_crea','desc')->first();
		if(count($esquemaproducto)>0){
					$valor 				  =	$esquemaproducto->costo_unitario;
		}
		return $valor;
	}


	public function ov_calculo_total_gema($esquema_id)
	{
		$esquemaproducto 	  = EsquemaProducto::where('id','=',$esquema_id)->first();
		$ctdetesquemaproducto = DetalleEsquemaProducto::where('esquemaproducto_id','=',$esquema_id)->where('activo','=','1')->sum('costo_total');

		$candetesquemaproducto = DetalleEsquemaProducto::where('esquemaproducto_id','=',$esquema_id)->where('activo','=','1')->sum('cantidad');

		$costo_unitario  	  =	$esquemaproducto->costo_total_oro + $ctdetesquemaproducto + $esquemaproducto->precio_total_engaste;

		EsquemaProducto::where('id','=',$esquema_id)
							->update(
								[
									'costo_total_gemas'=>$ctdetesquemaproducto,
									'cantidad_total_gemas'=>$candetesquemaproducto,
									'costo_unitario'=>$costo_unitario,
									'costo_unitario_igv'=>$costo_unitario,
									'fecha_mod'=>$this->fechaactual,
									'usuario_mod'=>Session::get('usuario')->id,
								]
							);

	}
	
}