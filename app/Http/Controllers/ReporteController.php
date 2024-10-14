<?php

namespace App\Http\Controllers;

use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Compra;
use App\Modelos\Proveedor;
use App\Modelos\Kardex;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use Hashids;
use NumeroALetras;
use PDF;
use App\Traits\GeneralesTraits;
use App\Traits\ConfiguracionTraits;


class ReporteController extends Controller{

	use GeneralesTraits;
	use ConfiguracionTraits;

	public function actionListarCompras($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    View::share('titulo','Listar Compras Reporte');


	    $finicio 						= $this->inicio;
	    $ffin 							= $this->fin;

	    $select_proveedor		  		=	'';
		$combo_proveedor	 			=	$this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');

	    $listadocumento = 	Compra::where('estado_id','=','1CIX00000004')	    					
	    					->where('fecha','>=', $finicio)
	    					->where('fecha','<=', $ffin)	    					
	    					->where('activo','=',1)->orderBy('id', 'asc')->get();


		return View::make('reportecompra/listacompras',
						 [
						 	'listadocumento' 	=> $listadocumento,
						 	'inicio' 			=> $finicio,
						 	'fin' 				=> $ffin,
						 	'select_proveedor'  => $select_proveedor,
							'combo_proveedor'   => $combo_proveedor,	
						 	'idopcion' 			=> $idopcion,
						 ]);



	}

	public function actionAjaxReporteComprasEntreFechas(Request $request)
	{

		$finicio 		=  	date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=  	date_format(date_create($request['ffin']), 'd-m-Y');
		$proveedor_id	=	$request['proveedor'];		

		$idestado 	=	$this->getIdEstado('EMITIDO');

		$listadocumento = 	Compra::where('estado_id','=',$idestado)	    					
	    					->where('fecha','>=', $finicio)
	    					->where('fecha','<=', $ffin)	    
	    					->CodProveedor($proveedor_id)	    					
	    					->where('activo','=',1)->orderBy('id', 'asc')->get();

	    
		return View::make('reportecompra/ajax/alistacompra',
						 [
							 'listadocumento'   => $listadocumento,
							 'ajax'    		 => true,
						 ]);

	}

	public function actionListarKardex($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    View::share('titulo','Listar Kardex Reporte');

	    $finicio 						= $this->inicio;
	    $ffin 							= $this->fin;

	    $select_almacen			  		=	'';
		$combo_almacen	 				=	$this->gn_generacion_combo('almacen','id','nombre','Seleccione almacen','');

		$select_producto			  	=	'';
		$combo_producto	 				=	$this->gn_generacion_combo('productos','id','descripcion','Seleccione producto','');

	    $listakardex 					= 	Kardex::where('activo','=',1)	    					
	    									->where('fecha','>=', $finicio)
	    									->where('fecha','<=', $ffin)->orderBy('id', 'asc')->get();


		return View::make('reportekardex/listakardex',
						 [
						 	'listakardex' 		=> $listakardex,
						 	'inicio' 			=> $finicio,
						 	'fin' 				=> $ffin,
						 	'select_almacen'  	=> $select_almacen,
							'combo_almacen'   	=> $combo_almacen,	
							'select_producto'  	=> $select_producto,
							'combo_producto'   	=> $combo_producto,	
						 	'idopcion' 			=> $idopcion,
						 ]);



	}

	public function actionAjaxReporteKardexEntreFechas(Request $request)
	{

		$finicio 		=  	date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=  	date_format(date_create($request['ffin']), 'd-m-Y');
		$almacen_id		=	$request['almacen'];		
		$producto_id	=	$request['producto'];				

		$listakardex 	= 	Kardex::where('activo','=',1)	    					
	    					->where('fecha','>=', $finicio)
	    					->where('fecha','<=', $ffin)
	    					->CodAlmacen($almacen_id)	    
	    					->CodProducto($producto_id)
	    					->orderBy('id', 'asc')->get();
	    
		return View::make('reportekardex/ajax/alistakardex',
						 [
							 'listakardex'   => $listakardex,
							 'ajax'    		 => true,
						 ]);
	}

}
