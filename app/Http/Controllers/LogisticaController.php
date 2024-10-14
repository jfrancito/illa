<?php

namespace App\Http\Controllers;

use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Compra;
use App\Modelos\DetalleCompra;
use App\Modelos\Almacen;
use App\Modelos\DetalleAlmacen;
use App\Modelos\Proveedor;
use App\Modelos\Categoria;
use App\Modelos\Producto;

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


class LogisticaController extends Controller {

	use GeneralesTraits;
	use ConfiguracionTraits;

	public function actionListarAlmacen($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Almacen');

	    $listaalmacen 	= 	Almacen::where('activo','=',1)->get();
		$funcion 		= 	$this;


		return View::make('logistica/listaalmacen',
						 [
						 	'listaalmacen' 			=> $listaalmacen,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,						 	
						 ]);
	}
	public function actionAgregarAlmacen($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Almacen');
		if($_POST)
		{
			
			$codigo 	 						= 	$request['codigo'];
			$nombre 	 						= 	$request['nombre'];
			
			$idalmacen 							=   $this->funciones->getCreateIdMaestra('almacen');
			
			$cabecera            	 			=	new Almacen;
			$cabecera->id 	     	 			=   $idalmacen;
			$cabecera->codigo 					=   $codigo;			
			$cabecera->nombre 					=   $nombre;			
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
			return Redirect::to('/gestion-de-almacen/'.$idopcion)->with('bienhecho', 'Almacen '.$cabecera->nombre.' registrado con exito');

		}else{

			$cod_almacen		 			=   $this->funciones->getCreateCodCorrelativo('almacen',3);
		    
			return View::make('logistica/agregaralmacen',
						[
							'cod_almacen'   			=> $cod_almacen,							
						  	'idopcion'  			 	=> $idopcion
						]);
		}
	}
	public function actionModificarAlmacen($idopcion,$idalmacen,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idalmacen = $this->funciones->decodificarmaestra($idalmacen);
	    View::share('titulo','Modificar Almacen');

		if($_POST)
		{

			$codigo 	 						= 	$request['codigo'];
			$nombre 	 						= 	$request['nombre'];
			
			$cabecera            	 			=	Almacen::find($idalmacen);
			$cabecera->codigo 					=   $codigo;			
			$cabecera->nombre 					=   $nombre;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-de-almacen/'.$idopcion)->with('bienhecho', 'Almacen '.$cabecera->nombre.' modificado con exito');					 	

		}else{

			$almacen 							= 	Almacen::where('id', $idalmacen)->first();
			
			return View::make('logistica/modificaralmacen', 
	        				[
	        					'almacen'  					=> $almacen,
	        					'idopcion' 					=> $idopcion
	        				]);
		}
	}

	public function actionQuitarAlmacen($idopcion,$idalmacen,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idalmacen 							= $this->funciones->decodificarmaestra($idalmacen);		
					
		$activo			 					= 	0;

		$cabecera            	 			=	Almacen::find($idalmacen);
		$cabecera->activo 					=   $activo;					
		$cabecera->fecha_mod 	 			=   $this->fechaactual;
		$cabecera->usuario_mod 				=   Session::get('usuario')->id;
		$cabecera->save();

		return Redirect::to('/gestion-de-almacen/'.$idopcion)->with('bienhecho', 'Almacen '.$cabecera->nombre.' quitado con exito');				
		
	}

}
