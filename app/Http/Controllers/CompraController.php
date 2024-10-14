<?php

namespace App\Http\Controllers;

use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Compra;
use App\Modelos\DetalleCompra;
use App\Modelos\Proveedor;
use App\Modelos\Categoria;
use App\Modelos\Producto;
use App\Modelos\LogExtornar;
use App\Modelos\Empresa;
use App\Modelos\Almacen;
use App\Modelos\DetalleAlmacen;
use App\Modelos\Kardex;
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

class CompraController extends Controller {

	use GeneralesTraits;
	use ConfiguracionTraits;

	public function actionListarCompras($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Compras');

	    $finicio 						= 	$this->inicio;
	    $ffin 							= 	$this->fin;

	    $select_proveedor		  		=	'';
		$combo_proveedor	 			=	$this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');

		$select_estado		  			=	'';
		$combo_estado	 				=	$this->gn_combo_estadoscompras('Seleccione estado','');;

	    $listacompra 					= 	Compra::where('fecha','>=', $finicio)
					    					->where('fecha','<=', $ffin)->orderBy('id', 'desc')->get();

		$funcion 						= 	$this;




		return View::make('compra/listacompras',
						 [
						 	'listacompra' 			=> $listacompra,
						 	'funcion' 				=> $funcion,
						 	'inicio' 				=> $finicio,
						 	'fin' 					=> $ffin,
						 	'select_proveedor'  	=> $select_proveedor,
							'combo_proveedor'   	=> $combo_proveedor,	
							'select_estado'  		=> $select_estado,
							'combo_estado'   		=> $combo_estado,	
						 	'idopcion' 				=> $idopcion,	

						 ]);
	}

	public function actionAjaxListarComprasEntreFechas(Request $request)
	{

		$idopcion 		=	$request['idopcion'];		
		$finicio 		=  	date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=  	date_format(date_create($request['ffin']), 'd-m-Y');
		$proveedor_id	=	$request['proveedor'];		
		$estado_id		=	$request['estado'];				

		$listacompra 	= 	Compra::where('fecha','>=', $finicio)
	    					->where('fecha','<=', $ffin)
	    					->CodProveedor($proveedor_id)	    
	    					->CodEstado($estado_id)
	    					->orderBy('id', 'asc')->get();

	    
		return View::make('compra/ajax/alistacompra',
						 [
							 'listacompra'   => $listacompra,
							 'idopcion' 	 => $idopcion,
							 'ajax'    		 => true,
						 ]);
	}


	public function actionAgregarCompras($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Compras');
		if($_POST)
		{
			
			$lote 	 					= 	$request['lote'];
			$serie 	 					= 	$request['serie'];
			$numero 	 				= 	$request['numero'];
			$fecha 	 					= 	$request['fecha'];
			//$montototal				 	= 	$request['montototal'];
			$tc				 			= 	0;
			$tipo_comprobante_id	 	= 	$request['tipo_comprobante_id'];
			$proveedor_id	 			= 	$request['proveedor_id'];
			$moneda_id	 				= 	$request['moneda_id'];
			$tipo_compra_id	 			= 	$request['tipo_compra_id'];
			
			$proveedor	 				= 	Proveedor::where('id','=',$proveedor_id)->first();		
			$tipo_comprobante	 		= 	Categoria::where('id','=',$tipo_comprobante_id)->first();			
			$moneda	 					= 	Moneda::where('id','=',$moneda_id)->first();
			$tipo_compra 		 		= 	Categoria::where('id','=',$tipo_compra_id)->first();			

			$idcompra 							=   $this->funciones->getCreateIdMaestra('compras');
			
			$cabecera            	 			=	new Compra;
			$cabecera->id 	     	 			=   $idcompra;
			$cabecera->lote 					=   $lote;			
			$cabecera->serie 					=   $serie;			
			$cabecera->numero 	   				=   $numero;
			$cabecera->fecha			 	 	=   $fecha;
			$cabecera->montototal 	   			=   0;		
			$cabecera->tc 	   					=   $tc;			
			$cabecera->tipo_comprobante_id		=   $tipo_comprobante->id;
			$cabecera->tipo_comprobante_nombre 	=   $tipo_comprobante->descripcion;			
			$cabecera->proveedor_id				=   $proveedor->id;
			$cabecera->proveedor_nombre 		=   $proveedor->nombre_razonsocial;			
			$cabecera->moneda_id				=   $moneda->id;
			$cabecera->moneda_nombre 			=   $moneda->descripcion;		
			$cabecera->estado_id 	   			=   '1CIX00000003';
			$cabecera->estado_descripcion 	   	=   'GENERADO';	
			$cabecera->tipo_compra_id			=   $tipo_compra->id;
			$cabecera->tipo_compra_nombre 		=   $tipo_compra->descripcion;			
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
			$idcompraen							= 	Hashids::encode(substr($idcompra, -8));
 		 	return Redirect::to('/modificar-compras/'.$idopcion.'/'.$idcompraen)->with('bienhecho', 'Compra '.$serie.'-'.$numero.' registrado con exito');

		}else{

			$lote_compra		 			=   $this->funciones->getCreateLoteCorrelativo('compras',10);
		    $select_proveedor		  		=	'';
		    $combo_proveedor	 			=	$this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');
		    $select_tipo_comprobante 	 	=	'';
		    $combo_tipo_comprobante 		=	$this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
		    $select_moneda 	 				=	$this->monedaxdefecto;
		    $combo_moneda 					=	$this->gn_combo_moneda('Seleccione moneda','');
		    $select_tipo_compra				=	'';
		    $combo_tipo_compra 				=	$this->gn_combo_categoria('TIPO_COMPRA','Seleccione tipo compra','');

			return View::make('compra/agregarcompras',
						[
							'lote_compra'				=> $lote_compra,
							'select_proveedor'  		=> $select_proveedor,
							'combo_proveedor'   		=> $combo_proveedor,							
							'select_tipo_comprobante'  	=> $select_tipo_comprobante,
							'combo_tipo_comprobante'   	=> $combo_tipo_comprobante,							
							'select_moneda'  			=> $select_moneda,
							'combo_moneda'   			=> $combo_moneda,							
							'select_tipo_compra'  		=> $select_tipo_compra,
							'combo_tipo_compra'   		=> $combo_tipo_compra,	
						  	'idopcion'  			 	=> $idopcion
						]);
		}
	}
	public function actionModificarCompra($idopcion,$idcompra,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idcompra = $this->funciones->decodificarmaestra($idcompra);
	    View::share('titulo','Modificar Compra');

		if($_POST)
		{

			$serie 	 							= 	$request['serie'];
			$numero 	 						= 	$request['numero'];
			$fecha 	 							= 	$request['fecha'];
			//$montototal				 			= 	$request['montototal'];
			$tc				 					= 	0;
			$tipo_comprobante_id	 			= 	$request['tipo_comprobante_id'];
			$proveedor_id	 					= 	$request['proveedor_id'];
			$moneda_id	 						= 	$request['moneda_id'];
			$tipo_compra_id	 					= 	$request['tipo_compra_id'];			


			$proveedor	 						= 	Proveedor::where('id','=',$proveedor_id)->first();		
			$tipo_comprobante	 				= 	Categoria::where('id','=',$tipo_comprobante_id)->first();			
			$moneda	 							= 	Moneda::where('id','=',$moneda_id)->first();//Categoria::where('id','=',$moneda_id)->first();
			$tipo_compra 		 				= 	Categoria::where('id','=',$tipo_compra_id)->first();			

			$cabecera            	 			=	Compra::find($idcompra);
			$cabecera->serie 					=   $serie;			
			$cabecera->numero 	   				=   $numero;
			$cabecera->fecha			 	 	=   $fecha;
			//$cabecera->montototal 	   			=   0.0;		
			$cabecera->tc 	   					=   $tc;			
			$cabecera->tipo_comprobante_id		=   $tipo_comprobante->id;
			$cabecera->tipo_comprobante_nombre 	=   $tipo_comprobante->descripcion;			
			$cabecera->proveedor_id				=   $proveedor->id;
			$cabecera->proveedor_nombre 		=   $proveedor->nombre_razonsocial;			
			$cabecera->moneda_id				=   $moneda->id;
			$cabecera->moneda_nombre 			=   $moneda->descripcion;				
			$cabecera->tipo_compra_id			=   $tipo_compra->id;
			$cabecera->tipo_compra_nombre 		=   $tipo_compra->descripcion;			
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-de-compras/'.$idopcion)->with('bienhecho', 'Compra '.$serie.'-'.$numero.' modificada con exito');
			 		 	

		}else{

			$compra 						= 	Compra::where('id', $idcompra)->first();
			$listadetallecompra				= 	DetalleCompra::where('activo','=',1)
												->where('compra_id','=',$idcompra)
												->orderby('producto_nombre','asc')->get();

			$select_proveedor		  		=	$compra->proveedor_id;
		    $combo_proveedor	 			=	$this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');
		    $select_tipo_comprobante 	 	=	$compra->tipo_comprobante_id;
		    $combo_tipo_comprobante 		=	$this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
		    $select_moneda 	 				=	$compra->moneda_id;
		    $combo_moneda 					=	$this->gn_combo_moneda('Seleccione moneda','');			
		    $select_tipo_compra				=	$compra->tipo_compra_id;
		    $combo_tipo_compra 				=	$this->gn_combo_categoria('TIPO_COMPRA','Seleccione tipo compra','');
		    
	        return View::make('compra/modificarcompra', 
	        				[
	        					'compra'  					=> $compra,
	        					'listadetallecompra'		=> $listadetallecompra,
	        					'select_proveedor'  		=> $select_proveedor,
		        				'combo_proveedor' 			=> $combo_proveedor,									
		        				'select_tipo_comprobante'  	=> $select_tipo_comprobante,
	        					'combo_tipo_comprobante'  	=> $combo_tipo_comprobante,
		        				'select_moneda' 			=> $select_moneda,		
		        				'combo_moneda' 				=> $combo_moneda,		
		        				'select_tipo_compra'  		=> $select_tipo_compra,
								'combo_tipo_compra'   		=> $combo_tipo_compra,	
					  			'idopcion' 					=> $idopcion
	        				]);
		}
	}

	public function actionAjaxModalDetalleCompra(Request $request)
	{
		$compra_id 	 = 	$request['compra_id'];
		$idopcion 	 = 	$request['idopcion'];

		$compra 							= 	Compra::where('id', $compra_id)->first();
		$tipo_comprobante_nombre 	 		=	$compra->tipo_comprobante_nombre;
		$serie 	 							= 	$compra->serie;
		$numero 	 						= 	$compra->numero;
		$opcionproducto 					=	Opcion::where('nombre','=','Productos')->where('activo','=',1)->first();
		$idopcionproducto 					=	($opcionproducto) ? Hashids::encode(substr($opcionproducto->id,-8)) : '';
		$select_producto			  		=	'';
		$combo_producto	 					=	$this->gn_generacion_combo('productos','id','descripcion','Seleccione producto','');
		
		//dd($combo_producto);
		
		return View::make('compra/modal/ajax/madetallecompra',
						 [		 	
						 	'compra_id' 				=> $compra_id,
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

	public function actionAgregarDetalleCompras($idopcion,$idcompra,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idcompra = $this->funciones->decodificarmaestra($idcompra);		

		$cabecera            	 				=	Compra::find($idcompra);
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


		$iddetallecompra 						=   $this->funciones->getCreateIdMaestra('detallecompras');
		
		$detallecompra            	 			=	new DetalleCompra;
		$detallecompra->id 	     	 			=   $iddetallecompra;
		$detallecompra->compra_id 	     	 	=   $idcompra;
		$detallecompra->producto_id 			=   $producto->id;			
		$detallecompra->producto_nombre 	   	=   $producto->descripcion;
		$detallecompra->cantidad			 	=   $cantidad;
		$detallecompra->disponible				=	($producto->categoria_id=='CATP00000003')?$preciounitario:$cantidad;
		$detallecompra->proveedor_id			=	$cabecera->proveedor_id;
		$detallecompra->proveedor_nombre		=	$cabecera->proveedor_nombre;
		$detallecompra->preciounitario 	   		=   $preciounitario;
		
		$detallecompra->indigv 	   				=   $indigv;
		$detallecompra->igv 	   				=   $igv;
		$detallecompra->porcigv 	   			=   $porcigv;
		$detallecompra->subtotal 				=	$detallesubtotal;

		$detallecompra->total 	   				=   $detalletotal;						
		$detallecompra->fecha_crea 	 			=   $this->fechaactual;
		$detallecompra->usuario_crea 			=   Session::get('usuario')->id;
		$detallecompra->save();

		$cabecera->montototal 	   				=   $cabecera->montototal+$detalletotal;		
		$cabecera->fecha_mod 	 				=   $this->fechaactual;
		$cabecera->usuario_mod 					=   Session::get('usuario')->id;
		$cabecera->save();			

		$idcompraen								= 	Hashids::encode(substr($idcompra, -8));
		 	return Redirect::to('/modificar-compras/'.$idopcion.'/'.$idcompraen)->with('bienhecho', 'Detalle compra '.$producto->descripcion.' registrado con exito');
		
	}

	public function actionQuitarDetalleCompra($idopcion,$iddetallecompra,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $iddetallecompra = $this->funciones->decodificarmaestra($iddetallecompra);		
					
		$activo			 						= 	0;

		$detallecompra            	 			=	DetalleCompra::find($iddetallecompra);
		$detallecompra->activo 					=   $activo;					
		$detallecompra->fecha_mod 	 			=   $this->fechaactual;
		$detallecompra->usuario_mod 			=   Session::get('usuario')->id;
		$detallecompra->save();

		$cabecera            	 				=	Compra::find($detallecompra->compra_id);
		$cabecera->montototal 	   				=   $cabecera->montototal-$detallecompra->total;		
		$cabecera->fecha_mod 	 				=   $this->fechaactual;
		$cabecera->usuario_mod 					=   Session::get('usuario')->id;
		$cabecera->save();					

		$idcompraen								= 	Hashids::encode(substr($detallecompra->compra_id, -8));
		 	return Redirect::to('/modificar-compras/'.$idopcion.'/'.$idcompraen)->with('bienhecho', 'Detalle compra '.$detallecompra->producto_nombre.' quitado con exito');
		
	}

	public function actionAjaxModalEmitirCompra(Request $request)
	{
		

		$idopcion 	 						= 	$request['idopcion'];
		$idcompra 	 						= 	$request['idcompra'];
		

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    $idcompra 							= 	$this->funciones->decodificarmaestra($idcompra);
		$compra 							= 	Compra::where('id','=',$idcompra)->first();


	    $idmotivo							=	$this->getIdMotivoDocumento('COMPRA');

	    $array 								= 	DB::table('categorias')
		        								->where('id','=',$idmotivo)
		        								->pluck('descripcion','id')
												->toArray();

		$select_almacen				  		=	'';
		$combo_almacen	 					=	$this->gn_generacion_combo('almacen','id','nombre','Seleccione almacen','');
		$select_motivo				  		=	$idmotivo;
		$combo_motivo	 					=	array('' => 'Seleccione motivo documento') + $array;
		//$combo_motivo	 					=	$this->gn_combo_categoria('MOTIVO_DOCUMENTO','Seleccione motivo documento','');
		
		return View::make('compra/modal/ajax/maemitircompra',
						 [		 	
						 	'compra' 					=> $compra,						 	
						 	'select_almacen' 			=> $select_almacen,
						 	'combo_almacen' 			=> $combo_almacen,
						 	'select_motivo' 			=> $select_motivo,
						 	'combo_motivo' 				=> $combo_motivo,
						 	'idopcion' 					=> $idopcion,
						 	'ajax' 						=> true,						 	
						 ]);
	}

	public function actionEmitirCompras($idopcion,Request $request)
	{

		if($_POST)
		{
			
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;			
    		
			$idcompra 					= $request['idcompra'];
			$compra 					= 	Compra::where('id','=',$idcompra)->first();

			if($compra->estado_id == $this->generado->id){ 

				$almacen_id 					= $request['almacen_id'];
				$almacen 						= Almacen::where('id','=',$almacen_id)->first();
				$motivo_id 						= $request['motivo_id'];
				$motivo 						= Categoria::where('id','=',$motivo_id)->first();

				// if ($compra->tipo_compra_id == '1CIX00000035') {
				if ($compra->tipo_compra_id == $this->ge_getIdCategoria('MATERIAL','TIPO_COMPRA')) {
					foreach($compra->detalle as $item){
						

						$detallealmacen				= 	DetalleAlmacen::where('activo','=',1)
														->where('almacen_id','=',$almacen_id)
														->where('proveedor_id','=',$compra->proveedor_id)
														->where('producto_id','=',$item->producto_id)->first();

														

						$cantidadinicial 	= 0;
						$cantidadingreso 	= 0;
						$cantidadfinal		= 0;


						if (count($detallealmacen) <= 0) {

							$iddetallealmacen 						=   $this->funciones->getCreateIdMaestra('detallealmacen');

							$cantidadinicial 	= 0;
							$cantidadingreso 	= $item->cantidad;	
							$cantidadfinal		= $cantidadinicial + $cantidadingreso;	
		
							$detallealmacen            	 			=	new DetalleAlmacen;
							$detallealmacen->id 	     	 		=   $iddetallealmacen;
							$detallealmacen->almacen_id 	     	=   $almacen_id;
							$detallealmacen->almacen_nombre 	   	=   $almacen->nombre;
							$detallealmacen->proveedor_id 			=   $compra->proveedor_id;			
							$detallealmacen->proveedor_nombre 	   	=   $compra->proveedor_nombre;
							$detallealmacen->producto_id 			=   $item->producto_id;			
							$detallealmacen->producto_nombre 	   	=   $item->producto_nombre;
							$detallealmacen->stock			 		=   $cantidadfinal;							
							$detallealmacen->fecha_crea 	 		=   $this->fechaactual;
							$detallealmacen->usuario_crea 			=   Session::get('usuario')->id;
							$detallealmacen->save();

						}else{

							$cantidadinicial 	= $detallealmacen->stock;
							$cantidadingreso 	= $item->cantidad;
							$cantidadfinal		= $cantidadinicial + $cantidadingreso;	

							$detallealmacen->stock 					=   $cantidadfinal;					
							$detallealmacen->fecha_mod 	 			=   $this->fechaactual;
							$detallealmacen->usuario_mod 			=   Session::get('usuario')->id;
							$detallealmacen->save();

						}

						$idkardex 						=   $this->funciones->getCreateIdMaestra('kardex');
						$idtipomovimiento 				=	$this->getIdTipoMovimiento('ENTRADA');
						$idcompraventa 					=	$this->getIdCompraVenta('COMPRA');
		
						$kardex            	 			=	new Kardex;
						$kardex->id 	     	 		=   $idkardex;
						$kardex->lote 	     	 		=   $compra->lote;			
						$kardex->almacen_id 	     	=   $almacen_id;
						$kardex->almacen_nombre 	   	=   $almacen->nombre;
						$kardex->tipo_movimiento_id 	=   $idtipomovimiento;
						$kardex->tipo_movimiento_nombre =   'ENTRADA';
						$kardex->compraventa_id 		=   $idcompraventa;
						$kardex->compraventa_nombre 	=   'COMPRA';
						$kardex->compraventa_tabla		=	'compras';
						$kardex->registroaux_id			=	$compra->id;
						$kardex->fecha 				 	=   date_format(date_create($compra->fecha_emision), 'd/m/Y');						
						$kardex->fechahora			 	=   date_format(date_create($compra->fecha_emision), 'd/m/Y H:i:s');						
						$kardex->producto_id 			=   $item->producto_id;			
						$kardex->producto_nombre 	   	=   $item->producto_nombre;

						$kardex->cantidadinicial		=   $cantidadinicial;	
						$kardex->cantidadingreso		=   $cantidadingreso;

						$kardex->cantidadsalida			=   0;	

						$kardex->cantidadfinal			=   $cantidadfinal;
						$kardex->motivo_id 	     		=   $motivo_id;
						$kardex->motivo_nombre			=   $motivo->descripcion;
						$kardex->fecha_crea 	 		=   $this->fechaactual;
						$kardex->usuario_crea 			=   Session::get('usuario')->id;
						$kardex->save();
					}
				}
				
				DetalleCompra::where('compra_id','=', $compra->id)
							->where('activo','=',1)
							->update([
										'estado_id'			=> $this->emitido->id,
										'estado_descripcion'=> $this->emitido->descripcion,
										'fecha_mod'			=> $this->fechaactual,
										'usuario_mod'		=> Session::get('usuario')->id
								  ]);


				$compra->motivo_id 				 		= 	$motivo_id;
			    $compra->motivo_nombre 					= 	$motivo->descripcion;
				$compra->estado_id 				 		= 	$this->emitido->id;//'1CIX00000004';
			    $compra->estado_descripcion 			= 	$this->emitido->descripcion;//'EMITIDO';
				$compra->fecha_emision 	 				=   $this->fechaactual;
				$compra->usuario_emision 				=   Session::get('usuario')->id;
				$compra->save();
				$idcaja 						=   $this->funciones->getCreateIdMaestra('caja');

				$tipo_movimiento				=	Categoria::where('tipo_categoria','=','TIPO_MOVIMIENTO')->where('descripcion','=','SALIDA')->first();

				$caja            	 			=	new Caja;
				$caja->id						=	$idcaja;
				$caja->tipo_movimiento_id		=	$tipo_movimiento->id;
				$caja->tipo_movimiento_nombre	=	$tipo_movimiento->descripcion;
				$caja->tipo_movimiento			=	(int)$tipo_movimiento->aux01;

				$caja->movimiento_id			=	$compra->id;
				$caja->tabla_movimiento			=	'compras';

				$caja->ind_comprobante			=	1;
				$caja->tipo_comprobante_id		=	$compra->tipo_comprobante_id;
				$caja->tipo_comprobante_nombre	=	$compra->tipo_comprobante_nombre;
				
				$caja->serie					=	$compra->serie;
				$caja->numero					=	$compra->numero;
				$caja->cliente_id				=	$compra->proveedor_id;
				$caja->cliente_nombre			=	$compra->proveedor_nombre;

				$caja->fecha					=	date('d-m-Y',strtotime($compra->fecha));
				$caja->moneda_id				=	$compra->moneda_id;
				$caja->moneda_nombre			=	$compra->moneda_nombre;

				$caja->tc						=	$compra->tc;
			
				$caja->saldo					=	$compra->montototal;
				$caja->montototal				=	$compra->montototal;
				$caja->total					=	$compra->montototal;

				$caja->estado_id 	   			=  	$this->generado->id;
				$caja->estado_descripcion 	   	=   $this->generado->descripcion;	
				$caja->fecha_crea 	 			=   $this->fechaactual;
				$caja->usuario_crea 			=   Session::get('usuario')->id;
				$caja->save();






		    	$msjarray[] 							= 	array(	"data_0" => $compra->serie.'-'.ltrim($compra->numero, '0'), 
		    														"data_1" => 'Compra Emitida', 
		    														"tipo" => 'S');

				$conts 									= 	$conts + 1;
				$codigo 								= 	$compra->serie.'-'.ltrim($compra->numero, '0');

		    }else{
				/**** ERROR DE PROGRMACION O SINTAXIS ****/
				$msjarray[] = array("data_0" => $compra->serie.'-'.ltrim($compra->numero, '0'), 
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


			return Redirect::to('/gestion-de-compras/'.$idopcion)->with('xmlmsj', $msjjson);

		
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
		
		return View::make('compra/ajax/textserienumero',
						 [
						 	'serie' 	=> $serie,
						 	'numero'	=> $numero,
							'ajax'		=> true,
						 ]);
	}	

	public function actionExtornarCompras($idopcion,$idcompra,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idcompra 	=	$this->funciones->decodificarmaestra($idcompra);
		View::share('titulo','Extornar Compra');
		if($_POST)
		{

			$idemitido 							=	$this->getIdEstado('EMITIDO');
			$idcompraventa 						=	$this->getIdCompraVenta('COMPRA');
			$idmotivo							=	$this->getIdMotivoDocumento('EXTORNO');
			$idtipocompra 						=	$this->getIdTipoCompra('MATERIAL');

			$compra 							=	Compra::where('id','=',$idcompra)->first();
			
			if($compra->estado_id == $idemitido && $compra->tipo_compra_id == $idtipocompra){ 

				foreach($compra->detalle as $item){

					$kardex							= 	Kardex::where('activo','=',1)
														->where('lote','=',$compra->lote)
														->where('compraventa_id','=',$idcompraventa)
														->where('producto_id','=',$item->producto_id)->first();

					$almacen_id 					= 	$kardex->almacen_id;
					$almacen 						= 	Almacen::where('id','=',$almacen_id)->first();
					
					$detallealmacen					= 	DetalleAlmacen::where('activo','=',1)
														->where('almacen_id','=',$almacen_id)
														->where('proveedor_id','=',$compra->proveedor_id)
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
					$kardex->lote 	     	 		=   $compra->lote;			
					$kardex->almacen_id 	     	=   $almacen_id;
					$kardex->almacen_nombre 	   	=   $almacen->nombre;
					$kardex->tipo_movimiento_id 	=   $idtipomovimiento;
					$kardex->tipo_movimiento_nombre =   'SALIDA';
					$kardex->compraventa_id 		=   $idcompraventa;
					$kardex->compraventa_nombre 	=   'COMPRA';
					$kardex->fecha 				 	=   date_format(date_create($compra->fecha_extornar), 'd/m/Y');						
					$kardex->fechahora			 	=   date_format(date_create($compra->fecha_extornar), 'd/m/Y H:i:s');
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

			$compra->fecha_extornar 			=	$this->fechaactual;
			$compra->usuario_extornar			=	Session::get('usuario')->id;
			$compra->estado_id 					=	$this->extornado->id;//'1CIX00000014';
			$compra->estado_descripcion 		= 	$this->extornado->descripcion;//'EXTORNADO';
			$compra->activo 					=	0;
			$compra->save();

			$descripcion 	 					= 	$request['descripcion'];
			$cabecera            	 			=	new LogExtornar;
			$cabecera->idtabla 					=   $compra->id;
			$cabecera->descripcion 				=	$descripcion;
			$cabecera->tabla					=   'compras';
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
			
 		 	return Redirect::to('/gestion-de-compras/'.$idopcion)->with('bienhecho', 'Compra '.$compra->serie.'-'.$compra->numero.' extornada con exito');

		}else{

		    $compra 							=	Compra::where('id','=',$idcompra)->first();

		    $idemitido 							=	$this->getIdEstado('EMITIDO');
		    $idcompraventa 						=	$this->getIdCompraVenta('COMPRA');
		    $idtipocompra 						=	$this->getIdTipoCompra('MATERIAL');
			
			//validamos si hay stock
			if($compra->estado_id == $idemitido && $compra->tipo_compra_id == $idtipocompra){ 
				
				foreach($compra->detalle as $item){

					$kardex							= 	Kardex::where('activo','=',1)
														->where('lote','=',$compra->lote)
														->where('compraventa_id','=',$idcompraventa)
														->where('producto_id','=',$item->producto_id)->first();

					$almacen_id 					= 	$kardex->almacen_id;
					$almacen 						= 	Almacen::where('id','=',$almacen_id)->first();
					
					$detallealmacen					= 	DetalleAlmacen::where('activo','=',1)
														->where('almacen_id','=',$almacen_id)
														->where('proveedor_id','=',$compra->proveedor_id)
														->where('producto_id','=',$item->producto_id)->first();					

					$cantidadinicial 				= 	$detallealmacen->stock;
					$cantidadsalida 				= 	$item->cantidad;

					if ($cantidadinicial < $cantidadsalida){
						return Redirect::to('/gestion-de-compras/'.$idopcion)->with('errorbd', 'No se pudo extornar, producto '.$item->producto_nombre.' con stock insuficiente ('.$cantidadinicial.') en almacen '.$almacen->nombre);
					}					
				}
			}

			return View::make('compra.extornar',
						[
							'compra'  			=> $compra,
							'idopcion'  	  	=> $idopcion,
						]);
		}
	}

	public function actionPdfCompras($idcompra)
	{

		$id 			= 	$this->funciones->decodificarmaestra($idcompra);
		$doc            =   Compra::where('id', $id)->first();

				
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
		
		$igv 			=	(float)DetalleCompra::where('compra_id','=',$id)->where('activo',1)->sum('igv');
		$subtotal 		=	(float)DetalleCompra::where('compra_id','=',$id)->where('activo',1)->sum('subtotal');

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
