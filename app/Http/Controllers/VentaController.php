<?php

namespace App\Http\Controllers;

use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Compra;
use App\Modelos\DetalleCompra;
use App\Modelos\Venta;
use App\Modelos\DetalleVenta;
use App\Modelos\Proveedor;
use App\Modelos\Categoria;
use App\Modelos\Producto;
use App\Modelos\LogExtornar;
use App\Modelos\Empresa;
use App\Modelos\Almacen;
use App\Modelos\DetalleAlmacen;
use App\Modelos\Kardex;
use App\Modelos\Cliente;
use App\Modelos\Caja;
use App\Modelos\Moneda;

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

class VentaController extends Controller {

	use GeneralesTraits;
	use ConfiguracionTraits;

	public function actionListarVentas($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Compras');

	    $finicio 						= 	$this->inicio;
	    $ffin 							= 	$this->fin;

	    $select_cliente		  		=	'';
		$combo_cliente	 			=	$this->gn_generacion_combo('clientes','id','nombre_razonsocial','Seleccione Cliente','');

		$select_estado		  			=	'';
		$combo_estado	 				=	$this->gn_combo_estadoscompras('Seleccione estado','');;

	    $listacompra 					= 	Venta::where('fecha','>=', $finicio)
					    					->where('fecha','<=', $ffin)->orderBy('id', 'desc')->get();

		$funcion 						= 	$this;




		return View::make('venta/listaventas',
						 [
						 	'listacompra' 			=> $listacompra,
						 	'funcion' 				=> $funcion,
						 	'inicio' 				=> $finicio,
						 	'fin' 					=> $ffin,
						 	'select_cliente'  		=> $select_cliente,
							'combo_cliente'   		=> $combo_cliente,	
							'select_estado'  		=> $select_estado,
							'combo_estado'   		=> $combo_estado,	
						 	'idopcion' 				=> $idopcion,	

						 ]);
	}

	public function actionAjaxTipoPagoVentas(Request $request)
	{
		$idopcion 			=	$request['idopcion'];		
		$tipo_venta_id 		=	$request['tipo_venta_id'];		
		
		$select_tipo_pago	=	'';
		$combo_tipo_pago 	=	[''=>'SELECCIONE']+ Categoria::where('tipo_categoria','=','TIPO_PAGO')
														->where('aux01','=',$tipo_venta_id)
														->where('activo','=',1)
		        										->pluck('descripcion','id')
														->toArray();
		// $this->gn_combo_categoria('TIPO_PAGO','Seleccione tipo venta','');

		return View::make('venta/ajax/atipopagoventa',
						 [
							 'select_tipo_pago'   => $select_tipo_pago,
							 'combo_tipo_pago' 	 => $combo_tipo_pago,
							 'ajax'    		 => true,
						 ]);
	}

	public function actionAjaxListarVentassEntreFechas(Request $request)
	{

		$idopcion 		=	$request['idopcion'];		
		$finicio 		=  	date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=  	date_format(date_create($request['ffin']), 'd-m-Y');
		$cliente_id	=	$request['cliente'];		
		$estado_id		=	$request['estado'];				

		$listacompra 	= 	Venta::where('fecha','>=', $finicio)
	    					->where('fecha','<=', $ffin)
	    					->CodCliente($cliente_id)	    
	    					->CodEstado($estado_id)
	    					->orderBy('id', 'asc')->get();

	    
		return View::make('venta/ajax/alistaventa',
						 [
							 'listacompra'   => $listacompra,
							 'idopcion' 	 => $idopcion,
							 'ajax'    		 => true,
						 ]);
	}


	public function actionAgregarVentas($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Ventas');
		if($_POST)
		{
			
			$lote 	 							= 	$request['lote'];
			$serie 	 							= 	$request['serie'];
			$numero 	 						= 	$request['numero'];
			$fecha 	 							= 	$request['fecha'];
			//$montototal				 			= 	$request['montototal'];
			$tc				 					= 	0;
			$tipo_comprobante_id	 			= 	$request['tipo_comprobante_id'];
			$cliente_id	 						= 	$request['cliente_id'];
			$moneda_id	 						= 	$request['moneda_id'];
			$tipo_venta_id	 					= 	$request['tipo_venta_id'];
			$tipo_pago_id	 					= 	'TPCA00000002';
			
			$cliente	 						= 	Cliente::where('id','=',$cliente_id)->first();		
			$tipo_comprobante	 				= 	Categoria::where('id','=',$tipo_comprobante_id)->first();			
			$moneda	 							= 	Moneda::where('id','=',$moneda_id)->first();
			$tipo_venta 		 				= 	Categoria::where('id','=',$tipo_venta_id)->first();			
			$tipo_pago 		 					= 	Categoria::where('id','=',$tipo_pago_id)->first();			

			$idventa 							=   $this->funciones->getCreateIdMaestra('ventas');
			
			$cabecera            	 			=	new Venta;
			$cabecera->id 	     	 			=   $idventa;
			$cabecera->lote 					=   $lote;			
			$cabecera->serie 					=   $serie;			
			$cabecera->numero 	   				=   $numero;
			$cabecera->fecha			 	 	=   $fecha;
			$cabecera->montototal 	   			=   0;		
			$cabecera->tc 	   					=   $tc;			
			$cabecera->tipo_comprobante_id		=   $tipo_comprobante->id;
			$cabecera->tipo_comprobante_nombre 	=   $tipo_comprobante->descripcion;			
			$cabecera->cliente_id				=   $cliente->id;
			$cabecera->cliente_nombre 			=   $cliente->nombre_razonsocial;			
			$cabecera->moneda_id				=   $moneda->id;
			$cabecera->moneda_nombre 			=   $moneda->descripcion;		
			$cabecera->estado_id 	   			=  	$this->generado->id;
			$cabecera->estado_descripcion 	   	=   $this->generado->descripcion;	
			$cabecera->tipo_venta_id			=   $tipo_venta->id;
			$cabecera->tipo_venta_nombre 		=   $tipo_venta->descripcion;			

			$cabecera->tipo_pago_id				=   $tipo_pago->id;
			$cabecera->tipo_pago_nombre 		=   $tipo_pago->descripcion;			

			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
			$idventaen							= 	Hashids::encode(substr($idventa, -8));
 		 	return Redirect::to('/modificar-ventas/'.$idopcion.'/'.$idventaen)->with('bienhecho', 'Venta '.$serie.'-'.$numero.' registrado con exito');

		}else{

			$lote_venta		 			=   $this->funciones->getCreateLoteCorrelativo('ventas',10);
		    $select_cliente		  		=	'';
		    $combo_cliente	 			=	$this->gn_generacion_combo('clientes','id','nombre_razonsocial','SELECCIONE CLIENTE','');
		    $select_tipo_comprobante 	 	=	'';
		    $combo_tipo_comprobante 		=	$this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
		    $select_moneda 	 				=	$this->monedaxdefecto;
		    $combo_moneda 					=	$this->gn_combo_moneda('Seleccione moneda','');
		    
			$select_tipo_venta				=	'';
			$combo_tipo_venta 				=	$this->gn_combo_categoria('TIPO_VENTA','SELECCIONE','');

			$select_tipo_pago				=	'TPCA00000002';
			$combo_tipo_pago 				=	[''=>'SELECCIONE'];

		    $swmodificar					=	false;


			return View::make('venta/agregarventas',
						[
							'lote_venta'				=> $lote_venta,
							'select_cliente'  		=> $select_cliente,
							'combo_cliente'   		=> $combo_cliente,							
							'select_tipo_comprobante'  	=> $select_tipo_comprobante,
							'combo_tipo_comprobante'   	=> $combo_tipo_comprobante,							
							'select_moneda'  			=> $select_moneda,
							'combo_moneda'   			=> $combo_moneda,							
							'select_tipo_venta'  		=> $select_tipo_venta,
							'combo_tipo_venta'   		=> $combo_tipo_venta,	
							'select_tipo_pago'  		=> $select_tipo_pago,
							'combo_tipo_pago'   		=> $combo_tipo_pago,	
							'swmodificar'				=> $swmodificar,
							'idopcion'  			 	=> $idopcion
						]);
		}
	}
	public function actionModificarVenta($idopcion,$idventa,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idventa = $this->funciones->decodificarmaestra($idventa);
	    View::share('titulo','Modificar Compra');

		if($_POST)
		{

			$serie 	 							= 	$request['serie'];
			$numero 	 						= 	$request['numero'];
			$fecha 	 							= 	$request['fecha'];
			//$montototal				 			= 	$request['montototal'];
			$tc				 					= 	0;
			$tipo_comprobante_id	 			= 	$request['tipo_comprobante_id'];
			$cliente_id	 						= 	$request['cliente_id'];
			$moneda_id	 						= 	$request['moneda_id'];
			$tipo_venta_id	 					= 	$request['tipo_venta_id'];			
			$tipo_pago_id	 					= 	'TPCA00000002';			


			$cliente	 						= 	Cliente::where('id','=',$cliente_id)->first();		
			$tipo_comprobante	 				= 	Categoria::where('id','=',$tipo_comprobante_id)->first();			
			$moneda	 							= 	Moneda::where('id','=',$moneda_id)->first();
			$tipo_venta 		 				= 	Categoria::where('id','=',$tipo_venta_id)->first();			
			$tipo_pago 		 					= 	Categoria::where('id','=',$tipo_pago_id)->first();			

			$cabecera            	 			=	Venta::find($idventa);
			$cabecera->serie 					=   $serie;			
			$cabecera->numero 	   				=   $numero;
			$cabecera->fecha			 	 	=   $fecha;
			//$cabecera->montototal 	   			=   0.0;		
			$cabecera->tc 	   					=   $tc;			
			$cabecera->tipo_comprobante_id		=   $tipo_comprobante->id;
			$cabecera->tipo_comprobante_nombre 	=   $tipo_comprobante->descripcion;			
			$cabecera->cliente_id				=   $cliente->id;
			$cabecera->cliente_nombre 			=   $cliente->nombre_razonsocial;			
			$cabecera->moneda_id				=   $moneda->id;
			$cabecera->moneda_nombre 			=   $moneda->descripcion;				
			
			$cabecera->tipo_venta_id			=   $tipo_venta->id;
			$cabecera->tipo_venta_nombre 		=   $tipo_venta->descripcion;
			
			$cabecera->tipo_pago_id				=   $tipo_pago->id;
			$cabecera->tipo_pago_nombre 		=   $tipo_pago->descripcion;

			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-de-ventas/'.$idopcion)->with('bienhecho', 'Venta '.$serie.'-'.$numero.' modificada con exito');
			 		 	

		}else{

			$venta 						= 	Venta::where('id', $idventa)->first();
			$listadetalleventa				= 	DetalleVenta::where('activo','=',1)
												->where('venta_id','=',$idventa)
												->orderby('producto_nombre','asc')->get();

			$select_cliente		  		=	$venta->cliente_id;
		    $combo_cliente	 			=	$this->gn_generacion_combo('clientes','id','nombre_razonsocial','SELECCIONE CLIENTE','');
		    $select_tipo_comprobante 	 	=	$venta->tipo_comprobante_id;
		    $combo_tipo_comprobante 		=	$this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
		    $select_moneda 	 				=	$venta->moneda_id;
		    $combo_moneda 					=	$this->gn_combo_moneda('Seleccione moneda','');			
		    
		    $select_tipo_venta				=	$venta->tipo_venta_id;
		    $combo_tipo_venta 				=	$this->gn_combo_categoria('TIPO_VENTA','SELECCIONE','');

		    $select_tipo_pago				=	$venta->tipo_pago_id;
		    $combo_tipo_pago 				=	$this->gn_combo_categoria('TIPO_PAGO','SELECCIONE','');
		    $swmodificar					=	true;
	        return View::make('venta/modificarventa', 
	        				[
	        					'venta'  					=> $venta,
	        					'listadetalleventa'		=> $listadetalleventa,
	        					'select_cliente'  		=> $select_cliente,
		        				'combo_cliente' 			=> $combo_cliente,									
		        				'select_tipo_comprobante'  	=> $select_tipo_comprobante,
	        					'combo_tipo_comprobante'  	=> $combo_tipo_comprobante,
		        				'select_moneda' 			=> $select_moneda,		
		        				'combo_moneda' 				=> $combo_moneda,		
		        				'select_tipo_venta'  		=> $select_tipo_venta,
								'combo_tipo_venta'   		=> $combo_tipo_venta,	
								'select_tipo_pago'  		=> $select_tipo_pago,
								'combo_tipo_pago'   		=> $combo_tipo_pago,	
					  			'swmodificar'				=> $swmodificar,
					  			'idopcion' 					=> $idopcion
	        				]);
		}
	}

	public function actionAjaxModalDetalleVenta(Request $request)
	{
		$venta_id 	 = 	$request['venta_id'];
		$idopcion 	 = 	$request['idopcion'];

		$venta 						= 	Venta::where('id', $venta_id)->first();
		$tipo_comprobante_nombre 	=	$venta->tipo_comprobante_nombre;
		$serie 	 					= 	$venta->serie;
		$numero 	 				= 	$venta->numero;
		$opcionproducto 			=	Opcion::where('nombre','=','Productos')->where('activo','=',1)->first();
		$idopcionproducto 			=	($opcionproducto) ? Hashids::encode(substr($opcionproducto->id,-8)) : '';
		$select_producto			=	'';
		$combo_producto	 			=	$this->gn_generacion_combo('productos','id','descripcion','Seleccione producto','');
		
		
		return View::make('venta/modal/ajax/madetalleventa',
						 [		 	
						 	'venta_id' 				=> $venta_id,
						 	'tipo_comprobante_nombre' 	=> $tipo_comprobante_nombre,
						 	'serie' 					=> $serie,
						 	'numero' 					=> $numero,
						 	'select_producto' 			=> $select_producto,
						 	'combo_producto' 			=> $combo_producto,
						 	'idopcion' 					=> $idopcion,
						 	'idopcionproducto'			=> $idopcionproducto,
						 	'ajax' 						=> true,						 	
						 ]);
	}

	public function actionAgregarDetalleVentas($idopcion,$idventa,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idventa = $this->funciones->decodificarmaestra($idventa);		
					
		$cantidad				 				= 	floatval(str_replace(",","",$request['cantidad']));
		$preciounitario							= 	floatval(str_replace(",","",$request['preciounitario']));

		$producto_id	 						= 	$request['producto_id'];
		$igv	 								= 	floatval(str_replace(",","",$request['igv']));
		$porcigv	 							= 	floatval(str_replace(",","",$request['porcigv']));
		$indigv	 								= 	$request['indigv'];
		
		// $detalletotal				 			= 	$cantidad*$preciounitario;
		$detallesubtotal				 		= 	$cantidad*$preciounitario;
		$detalletotal				 			= 	$detallesubtotal+ $igv;

		$producto	 							= 	Producto::where('id','=',$producto_id)->first();				

		$iddetalleventas 						=   $this->funciones->getCreateIdMaestra('detalleventas');
		
		$detalleventa            	 			=	new DetalleVenta;
		$detalleventa->id 	     	 			=   $iddetalleventas;
		$detalleventa->venta_id 	     	 	=   $idventa;
		$detalleventa->producto_id 				=   $producto->id;			
		$detalleventa->producto_nombre 	   		=   $producto->descripcion;
		$detalleventa->indproduccion			=	$producto->indproduccion;


		$detalleventa->cantidad			 		=   $cantidad;
		$detalleventa->preciounitario 	   		=   $preciounitario;
		
		$detalleventa->indigv 	   				=   $indigv;
		$detalleventa->igv 	   					=   $igv;
		$detalleventa->porcigv 	   				=   $porcigv;
		$detalleventa->subtotal 				=	$detallesubtotal;

		$detalleventa->total 	   				=   $detalletotal;						
		$detalleventa->fecha_crea 	 			=   $this->fechaactual;
		$detalleventa->usuario_crea 			=   Session::get('usuario')->id;
		$detalleventa->save();

		$cabecera            	 				=	Venta::find($idventa);
		$cabecera->montototal 	   				=   $cabecera->montototal+$detalletotal;		
		$cabecera->fecha_mod 	 				=   $this->fechaactual;
		$cabecera->usuario_mod 					=   Session::get('usuario')->id;
		$cabecera->save();			

		$idventaen								= 	Hashids::encode(substr($idventa, -8));
		 	return Redirect::to('/modificar-ventas/'.$idopcion.'/'.$idventaen)->with('bienhecho', 'Detalle Venta '.$producto->descripcion.' registrado con exito');
		
	}

	public function actionQuitarDetalleVenta($idopcion,$iddetalleventas,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $iddetalleventas = $this->funciones->decodificarmaestra($iddetalleventas);		
					
		$activo			 						= 	0;

		$detalleventa            	 			=	DetalleVenta::find($iddetalleventas);
		$detalleventa->activo 					=   $activo;					
		$detalleventa->fecha_mod 	 			=   $this->fechaactual;
		$detalleventa->usuario_mod 				=   Session::get('usuario')->id;
		$detalleventa->save();

		$cabecera            	 				=	Venta::find($detalleventa->venta_id);
		$cabecera->montototal 	   				=   $cabecera->montototal-$detalleventa->total;		
		$cabecera->fecha_mod 	 				=   $this->fechaactual;
		$cabecera->usuario_mod 					=   Session::get('usuario')->id;
		$cabecera->save();					

		$idventaen								= 	Hashids::encode(substr($detalleventa->venta_id, -8));
		 	return Redirect::to('/modificar-ventas/'.$idopcion.'/'.$idventaen)->with('bienhecho', 'Detalle Venta '.$detalleventa->producto_nombre.' quitado con exito');
		
	}

	public function actionAjaxModalEmitirVenta(Request $request)
	{

		$idopcion 	 						= 	$request['idopcion'];
		$idventa 	 						= 	$request['idventa'];

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $idventa 							= 	$this->funciones->decodificarmaestra($idventa);
		$venta 								= 	Venta::where('id','=',$idventa)->first();

	    $idmotivo							=	$this->getIdMotivoDocumento('VENTA');

	    $array 								= 	DB::table('categorias')
		        								->where('id','=',$idmotivo)
		        								->pluck('descripcion','id')
												->toArray();

		$select_almacen				  		=	'';
		$combo_almacen	 					=	$this->gn_generacion_combo('almacen','id','nombre','Seleccione almacen','');
		$select_motivo				  		=	$idmotivo;
		$combo_motivo	 					=	array('' => 'Seleccione motivo documento') + $array;
		//$combo_motivo	 					=	$this->gn_combo_categoria('MOTIVO_DOCUMENTO','Seleccione motivo documento','');
		
		return View::make('venta/modal/ajax/maemitirventa',
						 [		 	
						 	'venta' 					=> $venta,						 	
						 	'select_almacen' 			=> $select_almacen,
						 	'combo_almacen' 			=> $combo_almacen,
						 	'select_motivo' 			=> $select_motivo,
						 	'combo_motivo' 				=> $combo_motivo,
						 	'idopcion' 					=> $idopcion,
						 	'ajax' 						=> true,						 	
						 ]);
	}

	public function actionEmitirVentas($idopcion,Request $request)
	{

		if($_POST)
		{
			
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;			
    		
			$idventa 					= $request['idventa'];
			$venta 					= 	Venta::where('id','=',$idventa)->first();

			if($venta->estado_id == $this->generado->id){ 

				$almacen_id 					= $request['almacen_id'];
				$almacen 						= Almacen::where('id','=',$almacen_id)->first();
				$motivo_id 						= $request['motivo_id'];
				$motivo 						= Categoria::where('id','=',$motivo_id)->first();

				// if ($venta->tipo_venta_id == '1CIX00000035') {
				// if ($venta->tipo_venta_id == $this->ge_getIdCategoria('MATERIAL','TIPO_COMPRA')) {
					foreach($venta->detalle as $item){
						
						$producto			=	Producto::find($item->producto_id);
						$detallealmacen				= 	DetalleAlmacen::where('activo','=',1)
														->where('almacen_id','=',$almacen_id)
														// ->where('cliente_id','=',$venta->cliente_id)
														->where('producto_id','=',$item->producto_id)->first();

						$cantidadinicial 	= 0;
						$cantidadingreso 	= 0;
						$cantidadfinal		= 0;


						if (count($detallealmacen) <= 0) {
							return Redirect::to('/gestion-de-ventas/'.$idopcion)->with('errorbd', 'NO TIENE STOCK SUFICIENTE DE : '.$producto->descripcion.' STOCK: 0');
						}else{

							$cantidadinicial 	= $detallealmacen->stock;
							$cantidadsalida 	= $item->cantidad;
							$cantidadfinal		= $cantidadinicial - $cantidadsalida;	
							if($cantidadfinal<0){
								return Redirect::to('/gestion-de-ventas/'.$idopcion)->with('errorbd', 'NO TIENE STOCK SUFICIENTE DE : '.$producto->descripcion .' STOCK: '.$cantidadinicial);
							}
							$detallealmacen->stock 					=   $cantidadfinal;					
							$detallealmacen->fecha_mod 	 			=   $this->fechaactual;
							$detallealmacen->usuario_mod 			=   Session::get('usuario')->id;
							$detallealmacen->save();

						}

						$idkardex 						=   $this->funciones->getCreateIdMaestra('kardex');
						$idtipomovimiento 				=	$this->getIdTipoMovimiento('SALIDA');
						$idventaventa 					=	$this->getIdCompraVenta('VENTA');
		
						$kardex            	 			=	new Kardex;
						$kardex->id 	     	 		=   $idkardex;
						$kardex->lote 	     	 		=   $venta->lote;			
						$kardex->almacen_id 	     	=   $almacen_id;
						$kardex->almacen_nombre 	   	=   $almacen->nombre;
						$kardex->tipo_movimiento_id 	=   $idtipomovimiento;
						$kardex->tipo_movimiento_nombre =   'SALIDA';
						$kardex->compraventa_id 		=   $idventaventa;
						$kardex->compraventa_nombre 	=   'VENTA';
						$kardex->fecha 				 	=   date_format(date_create($venta->fecha_emision), 'd/m/Y');						
						$kardex->fechahora			 	=   date_format(date_create($venta->fecha_emision), 'd/m/Y H:i:s');						
						$kardex->producto_id 			=   $item->producto_id;			
						$kardex->producto_nombre 	   	=   $item->producto_nombre;
						$kardex->cantidadinicial		=   $cantidadinicial;	
						$kardex->cantidadingreso		=   0;//$cantidadingreso;	
						$kardex->cantidadsalida			=   $cantidadsalida;	
						$kardex->cantidadfinal			=   $cantidadfinal;
						$kardex->motivo_id 	     		=   $motivo_id;
						$kardex->motivo_nombre			=   $motivo->descripcion;
						$kardex->fecha_crea 	 		=   $this->fechaactual;
						$kardex->usuario_crea 			=   Session::get('usuario')->id;
						$kardex->save();
					}
				// }
				
				$venta->motivo_id 				 		= 	$motivo_id;
			    $venta->motivo_nombre 					= 	$motivo->descripcion;
				$venta->estado_id 				 		= 	$this->emitido->id;//'1CIX00000004';
			    $venta->estado_descripcion 				= 	$this->emitido->descripcion;//'EMITIDO';
				$venta->fecha_emision 	 				=   $this->fechaactual;
				$venta->usuario_emision 				=   Session::get('usuario')->id;
				$venta->save();


				$idcaja 						=   $this->funciones->getCreateIdMaestra('caja');

				$tipo_movimiento				=	Categoria::where('tipo_categoria','=','TIPO_MOVIMIENTO')->where('descripcion','=','ENTRADA')->first();

				$caja            	 			=	new Caja;
				$caja->id						=	$idcaja;
				$caja->tipo_movimiento_id		=	$tipo_movimiento->id;
				$caja->tipo_movimiento_nombre	=	$tipo_movimiento->descripcion;
				$caja->tipo_movimiento			=	(int)$tipo_movimiento->aux01;

				$caja->movimiento_id			=	$venta->id;
				$caja->tabla_movimiento			=	'ventas';

				$caja->ind_comprobante			=	1;
				$caja->tipo_comprobante_id		=	$venta->tipo_comprobante_id;
				$caja->tipo_comprobante_nombre	=	$venta->tipo_comprobante_nombre;
				
				$caja->serie					=	$venta->serie;
				$caja->numero					=	$venta->numero;
				$caja->cliente_id				=	$venta->cliente_id;
				$caja->cliente_nombre			=	$venta->cliente_nombre;

				$caja->fecha					=	date('d-m-Y',strtotime($venta->fecha));
				$caja->moneda_id				=	$venta->moneda_id;
				$caja->moneda_nombre			=	$venta->moneda_nombre;

				$caja->tc						=	$venta->tc;
			
				$caja->saldo					=	$venta->montototal;
				$caja->montototal				=	$venta->montototal;
				$caja->total					=	$venta->montototal;

				$caja->estado_id 	   			=  	$this->generado->id;
				$caja->estado_descripcion 	   	=   $this->generado->descripcion;	
				$caja->fecha_crea 	 			=   $this->fechaactual;
				$caja->usuario_crea 			=   Session::get('usuario')->id;
				$caja->save();

		    	$msjarray[] 							= 	array(	"data_0" => $venta->serie.'-'.ltrim($venta->numero, '0'), 
		    														"data_1" => 'Compra Emitida', 
		    														"tipo" => 'S');

				$conts 									= 	$conts + 1;
				$codigo 								= 	$venta->serie.'-'.ltrim($venta->numero, '0');

		    }else{
				/**** ERROR DE PROGRMACION O SINTAXIS ****/
				$msjarray[] = array("data_0" => $venta->serie.'-'.ltrim($venta->numero, '0'), 
									"data_1" => 'esta compra ya esta emitida', 
									"tipo" => 'D');
				$contd 		= 	$contd + 1;

		    }

			
		    

			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'Compra Emitida', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'Compra', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'Compras erradas', 
	    						"tipo" => 'TD');

			$msjjson = json_encode($msjarray);


			return Redirect::to('/gestion-de-ventas/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}
	}

	public function actionGeneraNotaPedidoAjax(Request $request)
	{
		$tipo_comprobante_id   	= $request['tipo_comprobante_id'];
		$serie 					= 'NT01';
		$num 					= DB::table('compras')
								  ->select(DB::raw('max(numero) as numero'))
								  ->where('tipo_comprobante_id','=',$tipo_comprobante_id)
								  ->first();			
		
		$numsuma 				= (int) $num->numero + 1;

		$numero 				= str_pad($numsuma, 8, "0", STR_PAD_LEFT);

		if($tipo_comprobante_id != '1CIX00000018')
			{
				$serie 			= '';
				$numero 		= '';
			}
		
		return View::make('venta/ajax/textserienumero',
						 [
						 	'serie' 	=> $serie,
						 	'numero'	=> $numero,
							'ajax'		=> true,
						 ]);
	}	

	public function actionExtornarVentas($idopcion,$idventa,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idventa 	=	$this->funciones->decodificarmaestra($idventa);
		View::share('titulo','Extornar Compra');
		if($_POST)
		{

			$idemitido 							=	$this->getIdEstado('EMITIDO');
			$idventaventa 						=	$this->getIdCompraVenta('COMPRA');
			$idmotivo							=	$this->getIdMotivoDocumento('EXTORNO');
			$idtipocompra 						=	$this->getIdTipoCompra('MATERIAL');

			$venta 							=	Venta::where('id','=',$idventa)->first();
			
			if($venta->estado_id == $idemitido && $venta->tipo_venta_id == $idtipocompra){ 

				foreach($venta->detalle as $item){

					$kardex							= 	Kardex::where('activo','=',1)
														->where('lote','=',$venta->lote)
														->where('compraventa_id','=',$idventaventa)
														->where('producto_id','=',$item->producto_id)->first();

					$almacen_id 					= 	$kardex->almacen_id;
					$almacen 						= 	Almacen::where('id','=',$almacen_id)->first();
					
					$detallealmacen					= 	DetalleAlmacen::where('activo','=',1)
														->where('almacen_id','=',$almacen_id)
														->where('cliente_id','=',$venta->cliente_id)
														->where('producto_id','=',$item->producto_id)->first();					

					$cantidadinicial 				= 	$detallealmacen->stock;
					$cantidadsalida 				= 	$item->cantidad;
					$cantidadfinal					= 	$cantidadinicial - $cantidadsalida;	

					$detallealmacen->stock 			=   $cantidadfinal;					
					$detallealmacen->fecha_mod 	 	=   $this->fechaactual;
					$detallealmacen->usuario_mod 	=   Session::get('usuario')->id;
					$detallealmacen->save();					

					$idkardex 						=   $this->funciones->getCreateIdMaestra('kardex');
					$idtipomovimiento 				=	$this->getIdTipoMovimiento('SALIDA');
					
					$kardex            	 			=	new Kardex;
					$kardex->id 	     	 		=   $idkardex;
					$kardex->lote 	     	 		=   $venta->lote;			
					$kardex->almacen_id 	     	=   $almacen_id;
					$kardex->almacen_nombre 	   	=   $almacen->nombre;
					$kardex->tipo_movimiento_id 	=   $idtipomovimiento;
					$kardex->tipo_movimiento_nombre =   'SALIDA';
					$kardex->compraventa_id 		=   $idventaventa;
					$kardex->compraventa_nombre 	=   'COMPRA';
					$kardex->fecha 				 	=   date_format(date_create($venta->fecha_extornar), 'd/m/Y');						
					$kardex->fechahora			 	=   date_format(date_create($venta->fecha_extornar), 'd/m/Y H:i:s');
					$kardex->producto_id 			=   $item->producto_id;			
					$kardex->producto_nombre 	   	=   $item->producto_nombre;
					$kardex->cantidadinicial		=   $cantidadinicial;	
					$kardex->cantidadingreso		=   0;	
					$kardex->cantidadsalida			=   $cantidadsalida;	
					$kardex->cantidadfinal			=   $cantidadfinal;
					$kardex->motivo_id 	     		=   $idmotivo;
					$kardex->motivo_nombre			=   'EXTORNO';
					$kardex->fecha_crea 	 		=   $this->fechaactual;
					$kardex->usuario_crea 			=   Session::get('usuario')->id;
					$kardex->save();
				}

			}

			$venta->fecha_extornar 			=	$this->fechaactual;
			$venta->usuario_extornar			=	Session::get('usuario')->id;
			$venta->estado_id 					=	$this->extornado->id;//'1CIX00000014';
			$venta->estado_descripcion 		= 	$this->extornado->descripcion;//'EXTORNADO';
			$venta->activo 					=	0;
			$venta->save();

			$descripcion 	 					= 	$request['descripcion'];
			$cabecera            	 			=	new LogExtornar;
			$cabecera->idtabla 					=   $venta->id;
			$cabecera->descripcion 				=	$descripcion;
			$cabecera->tabla					=   'compras';
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
			
 		 	return Redirect::to('/gestion-de-ventas/'.$idopcion)->with('bienhecho', 'Venta '.$venta->serie.'-'.$venta->numero.' extornada con exito');

		}else{

		    $venta 							=	Venta::where('id','=',$idventa)->first();

		    $idemitido 							=	$this->getIdEstado('EMITIDO');
		    $idventaventa 						=	$this->getIdCompraVenta('COMPRA');
		    $idtipocompra 						=	$this->getIdTipoCompra('MATERIAL');
			
			//validamos si hay stock
			if($venta->estado_id == $idemitido && $venta->tipo_venta_id == $idtipocompra){ 
				
				foreach($venta->detalle as $item){

					$kardex							= 	Kardex::where('activo','=',1)
														->where('lote','=',$venta->lote)
														->where('compraventa_id','=',$idventaventa)
														->where('producto_id','=',$item->producto_id)->first();

					$almacen_id 					= 	$kardex->almacen_id;
					$almacen 						= 	Almacen::where('id','=',$almacen_id)->first();
					
					$detallealmacen					= 	DetalleAlmacen::where('activo','=',1)
														->where('almacen_id','=',$almacen_id)
														->where('cliente_id','=',$venta->cliente_id)
														->where('producto_id','=',$item->producto_id)->first();					

					$cantidadinicial 				= 	$detallealmacen->stock;
					$cantidadsalida 				= 	$item->cantidad;

					if ($cantidadinicial < $cantidadsalida){
						return Redirect::to('/gestion-de-ventas/'.$idopcion)->with('errorbd', 'No se pudo extornar, producto '.$item->producto_nombre.' con stock insuficiente ('.$cantidadinicial.') en almacen '.$almacen->nombre);
					}					
				}
			}

			return View::make('compra.extornar',
						[
							'venta'  			=> $venta,
							'idopcion'  	  	=> $idopcion,
						]);
		}
	}

	public function actionPdfVentas($idventa)
	{

		$id 			= 	$this->funciones->decodificarmaestra($idventa);
		$doc            =   Venta::where('id', $id)->first();

				
		$titulo = $doc->tipo_comprobante_nombre; 
				
		$empresa 		= Empresa::where('activo','=',1)->first();


	    $razonsocial    = $empresa->descripcion;
	    $ruc            = $empresa->ruc;
	    $direccion      = $empresa->domiciliofiscal1;
		$telefono      	= $empresa->telefono;

	    $departamento   = $empresa->departamento_nombre;
	    $provincia      = $empresa->provincia_nombre;
	    $distrito       = $empresa->distrito_nombre;
		
		
	    /************** IMPORTE DE COMPRA NUMERO Y LETRAS **************/
        $importecompra  = 0.00;
		$importecompra 	= $doc->montototal; $tmoneda = 'Soles'; $smoneda = 'S/.';
		
		$igv 			=	(float)DetalleVenta::where('venta_id','=',$id)->where('activo',1)->sum('igv');
		$subtotal 		=	(float)DetalleVenta::where('venta_id','=',$id)->where('activo',1)->sum('subtotal');

        $monto      	=   $importecompra;
        $entero     	=   intval($monto);        
        $decimal    	=   substr(number_format($monto, 2, '.', ''),-2);
        $letras     	=   trim(NumeroALetras::convertir($entero));
        $letras     	=   strtoupper($letras.' CON '.$decimal.'/100 '.$tmoneda);

        /*************************************************************/

		// $subtotal      	=   $importecompra;
		
		$total 			=	$importecompra;



		$pdf = PDF::loadView('pdffa.factura', [ 
												'doc' 		  	=> $doc, 
												'ruc' 		  	=> $ruc,
												'razonsocial' 	=> $razonsocial,
												'direccion'   	=> $direccion,
												'telefono'    	=> $telefono,
												'departamento' 	=> $departamento,													
												'provincia'   	=> $provincia,
												'distrito'    	=> $distrito,

												'importecompra' => $importecompra,														
												'letras'   	  	=> $letras,												
												'smoneda'     	=> $smoneda,	
												
												'subtotal'    	=> $subtotal,
												'igv'    		=> $igv,
												'total'    		=> $total,

												'titulo'    	=> $titulo,												
									
											  ]);

		return $pdf->stream('download.pdf');



	}



}
