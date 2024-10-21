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
use App\Modelos\Cliente;
use App\Modelos\Moneda;
use App\Modelos\TipoCambio;

use App\Modelos\OrdenVenta;
use App\Modelos\DetalleOrdenVenta;
use App\Modelos\EsquemaProducto;
use App\Modelos\DetalleEsquemaProducto;
use App\Modelos\ProductoGema;
use App\Modelos\DetalleOrdenVenta_ProductoGema;
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
use App\Traits\OrdenVentaTraits;


class GestionOrdenVentaController extends Controller
{
	use GeneralesTraits;
	use ConfiguracionTraits;
	use OrdenVentaTraits;

	
	public function actionAjaxModalDetalleGemaEsquema(Request $request)
	{


		$gema_esquema_id 	 		= 	$request['gema_esquema_id'];
		$idopcion 	 				= 	$request['idopcion'];
		$esquema_id 	 			= 	$request['esquema_id'];
		$ordenventa_id 	 			= 	$request['ordenventa_id'];



		$esquemaproducto 			=	EsquemaProducto::where('id','=',$esquema_id)->first();
		$detesquemaproducto 		=	DetalleEsquemaProducto::where('id','=',$gema_esquema_id)->where('activo','=',1)->first();
		$combo_origen_gema			=   array('' => "Seleccione Origen") + Categoria::where('tipo_categoria','=','TIPO_ORIGEN_GEMA')->pluck('descripcion','id')->toArray();// + $datos;
		$select_origen_gema			=   $detesquemaproducto->origen_id;

		//dd($idopcion);

		
		return View::make('ordenventa/modal/ajax/madetallegema',
						 [		 	
						 	'gema_esquema_id' 		=> $gema_esquema_id,
						 	'esquema_id' 			=> $esquema_id,
						 	'ordenventa_id' 		=> $ordenventa_id,

						 	'esquemaproducto' 		=> $esquemaproducto,
						 	'detesquemaproducto' 	=> $detesquemaproducto,
						 	'combo_origen_gema' 	=> $combo_origen_gema,
						 	'select_origen_gema' 	=> $select_origen_gema,
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}



	public function actionGuardaretalleGemaEsquema($idopcion,$esquema_id,$detalleesquema_id,$idordenventa,Request $request)
	{

		if($_POST)
		{
			
			try {
					DB::beginTransaction();
					/******************************/

					$origen_id						=	$request['origen_id'];
					$cantidad						=	(float)$request['cantidad'];
					$precio							=	(float)$request['precio'];
					$costo_total 					=	$cantidad * $precio;

					$origen 						=	Categoria::where('id','=',$origen_id)->first();
					DetalleEsquemaProducto::where('id','=',$detalleesquema_id)
										->update(
											[
												'cantidad'=>$cantidad,
												'costo_unitario'=>$precio,
												'costo_total'=>$costo_total,
												'origen_id'=>$origen->id,
												'origendescripcion'=>$origen->descripcion,
												'fecha_mod'=>$this->fechaactual,
												'usuario_mod'=>Session::get('usuario')->id,
											]
										);

					$esquema 					   =    EsquemaProducto::where('id','=',$esquema_id)->first();

					$this->ov_calculo_total_gema($esquema_id);
					$this->ov_calculo_total_orden_venta($esquema->ordenventa_id);

					DB::commit();
				
			} catch (Exception $ex) {
				DB::rollback();
				$msj =$this->ge_getMensajeError($ex);
				return Redirect::to('orden-ventas-esquema-producto/'.$idopcion.'/'.$idordenventa)->with('errorurl', $msj);
			}
			/******************************/

            Session::flash('tab', $esquema_id);
			return Redirect::to('/orden-ventas-esquema-producto/'.$idopcion.'/'.$idordenventa)->with('bienhecho', 'Registro guardado realizado con exito');
		}

	}


	public function actionListarOrdenesVenta($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Listar Ordenes de Venta');

		$finicio                        =   $this->inicio;
		$ffin                           =   $this->fin;

		$select_proveedor               =   '';
		$combo_proveedor                =   $this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');
			$combo_cliente          =[''=>'SELECCIONE']+Cliente::where('activo','=',1)->pluck('nombre_razonsocial','id')->toArray();

		$select_estado                  =   '';
		$select_cliente                  =   '';
		$combo_estado                   =   $this->gn_combo_estadoscompras('Seleccione estado','');;

		$listadatos                     =   OrdenVenta::where('fecha','>=', $finicio)
											->where('fecha','<=', $ffin)->orderBy('id', 'desc')->get();

		$funcion                        =   $this;




		return View::make('ordenventa/lista',
						 [
							'listadatos'            => $listadatos,
							'funcion'               => $funcion,
							'inicio'                => $finicio,
							'fin'                   => $ffin,
							'select_proveedor'      => $select_proveedor,
							'combo_cliente'         => $combo_cliente,    
							'combo_proveedor'       => $combo_proveedor,    
							'select_estado'         => $select_estado,
							'select_cliente'         => $select_cliente,
							'combo_estado'          => $combo_estado,   
							'idopcion'              => $idopcion,   

						 ]);
	}

	public function actionAgregarOrdenesVenta($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Agregar Ordenes de Venta');

		if($_POST)
		{

			try {
					DB::beginTransaction();
					/******************************/

					$usuario                                    =   User::where('id',Session::get('usuario')->id)->first();

					$moneda_id             		=   $request['moneda_id'];
					$moneda						=	Moneda::where('id','=',$moneda_id)->first();
					$cliente_id                 =   $request['cliente_id'];
					$cliente             		=   Cliente::where('id','=',$cliente_id)->first();
					$fecha                 		=   $request['fecha'];
					$idregistro              	=   $this->funciones->getCreateIdMaestra('ordenventas');
					$codigo                     =   $this->funciones->getCreateCodCorrelativo('ordenventas',8);
					$codigo_shopify             =   $request['codigo_shopify'];

					$tipocambio					=	$this->ge_tipo_cambio();


					$cabecera                   =   new OrdenVenta();
					$cabecera->id               =   $idregistro;
					$cabecera->codigo           =   $codigo;
					$cabecera->codigo_shopify   =   $codigo_shopify;
					$cabecera->moneda_id   		=   $moneda_id;
					$cabecera->moneda_nombre   	=   $moneda->descripcion;
					$cabecera->cliente_id       =   $cliente_id;
					$cabecera->cliente_nombre   =   $cliente->nombre_razonsocial;
					$cabecera->tc				=	$tipocambio;

					$cabecera->estado_id 	   			=   '1CIX00000003';
					$cabecera->estado_descripcion 	   	=   'GENERADO';	
					
					$cabecera->fecha    		=   $fecha;
					$cabecera->fecha_crea       =   $this->fechaactual;
					$cabecera->usuario_crea     =   Session::get('usuario')->id;
					$cabecera->save();
					$idregistroen				= 	Hashids::encode(substr($idregistro, -8));

					

					DB::commit();
				
			} catch (Exception $ex) {
				DB::rollback();
				 $msj =$this->ge_getMensajeError($ex);
				return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('errorurl', $msj);
			}
			/******************************/

			return Redirect::to('/modificar-orden-ventas/'.$idopcion.'/'.$idregistroen)->with('bienhecho', 'Registro realizado con exito');

		}else{

			// $datos              =   DB::table('ordenventas')->where('activo','=',1)
			//                         ->where('id','<>','1CIX00000001')
			//                         ->pluck('nombre','id')->toArray();
			$combopro =   array('' => "Seleccione Categoria");// + $datos;
			$select_tipo_comprobante =   '';
			// $comboperiodo       =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','','APAFA_CONEI_PERIODO');
			$selectperiodo      =   '';
			// $comboprocedencia   =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','','APAFA_CONEI');
			$selectprocedencia  =   '';
			$combo_producto     =   [''=>'SELECCIONE PRODUCTO'] + Producto::where('activo','=',1)->where('indproduccion','=',1)->pluck('descripcion','id')->toArray();
			$select_cliente     =   '';
			$select_moneda      =   $this->monedaxdefecto;
			$combo_tipo_comprobante =[''=>'SELECCIONE'];
			$combo_cliente          =[''=>'SELECCIONE']+Cliente::where('activo','=',1)->pluck('nombre_razonsocial','id')->toArray();
			$combo_moneda           =   $this->gn_combo_moneda('SELECCIONE MONEDA','');
			$swmodificar			=	true;
			return View::make('ordenventa/agregar',
						[
							'idopcion'                  =>  $idopcion,
							'combo_producto'            =>  $combo_producto,
							'combo_tipo_comprobante'    =>  $combo_tipo_comprobante,
							'select_tipo_comprobante'   =>  $select_tipo_comprobante,
							'select_cliente'            =>  $select_cliente,
							'combo_cliente'             =>  $combo_cliente, 
							'combo_moneda'              =>  $combo_moneda, 
							'select_moneda'             =>  $select_moneda,
							// 'swmodificar'               => $swmodificar,
							'swmodificar'               => $swmodificar,
							// 'comboperiodo'          =>  $comboperiodo, 
							// 'selectprocedencia'     =>  $selectprocedencia
						]);
		}

	}


	public function actionModificarOrdenesVenta($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idregistro);
	    View::share('titulo','Modificar Orden Venta');

		if($_POST)
		{

			$cabecera                   =   OrdenVenta::find($registro_id);
			$cabecera->fecha_mod 	 	=   $this->fechaactual;
			$cabecera->usuario_mod 		=   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('bienhecho', 'Orden Venta modificada con exito');
		}else{

			$registro 			=	OrdenVenta::where('id', $registro_id)->first();

			$listadetalle		=	DetalleOrdenVenta::where('activo','=',1)
									->where('ordenventa_id','=',$registro_id)
									->orderby('id','asc')
									->orderby('producto_descripcion','asc')
									->get();

			$select_cliente		=	$registro->cliente_id;
			$combo_cliente      =	[''=>'SELECCIONE']+Cliente::where('activo','=',1)->pluck('nombre_razonsocial','id')->toArray();
		    $select_moneda 	 	=	$registro->moneda_id;
		    $combo_moneda 		=	$this->gn_combo_moneda('Seleccione moneda','');			
		     
	        return View::make('ordenventa/modificar', 
	        				[
	        					'registro'  		=> $registro,
	        					'listadetalle'		=> $listadetalle,
	        					'select_cliente'  	=> $select_cliente,
		        				'combo_cliente' 	=> $combo_cliente,
		        				'select_moneda' 	=> $select_moneda,		
		        				'combo_moneda' 		=> $combo_moneda,		
		        				'idregistro'		=>	$idregistro,
					  			'idopcion' 			=> $idopcion,
					  			'swmodificar'		=>	false,
	        				]);
		}
	}

	public function actionAjaxModalDetalleOrdenVenta(Request $request)
	{
		$registro_id 	 = 	$request['registro_id'];
		$idopcion 	 = 	$request['idopcion'];

		$registro					= 	OrdenVenta::where('id', $registro_id)->first();
		// dd($registro);
		// $tipo_comprobante_nombre 	=	$registro->tipo_comprobante_nombre;
		$cliente_nombre 	 		= 	$registro->cliente_nombre;
		
		$opcionproducto 			=	Opcion::where('nombre','=','Productos')->where('activo','=',1)->first();
		$idopcionproducto 			=	($opcionproducto) ? Hashids::encode(substr($opcionproducto->id,-8)) : '';

		$select_producto			=	'';
		$combo_producto	 			=	[''=>'SELECCIONE PRODUCTO']+Producto::where('indproduccion','=',1)->where('activo','=',1)->pluck('descripcion','id')->toArray();		
				
		return View::make('ordenventa/modal/ajax/madetalleregistro',
						 [		 	
						 	'registro' 				=> $registro,
						 	'registro_id' 			=> $registro_id,
						 	'cliente_nombre' 		=> $cliente_nombre,
						 	'select_producto' 		=> $select_producto,
						 	'combo_producto' 		=> $combo_producto,						 	
						 	'idopcion' 				=> $idopcion,
						 	'idopcionproducto'		=> $idopcionproducto,
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionAgregarDetalleOrdenVentas($idopcion,$idregistro,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idregistro);	

	    try {
					DB::beginTransaction();

				    $producto_id		=	$request['producto_id'];	
				    					
					$cantidad				 				= 	floatval(str_replace(",","",$request['cantidad']));
					$producto	 							= 	Producto::where('id','=',$producto_id)->first();				
					$total	 								= 	$cantidad*$producto->precio_venta;		

					$cabecera						=	OrdenVenta::find($registro_id);
					
					$iddetalle 						=   $this->funciones->getCreateIdMaestra('detalleordenventas');
					
					$detalle						=	new DetalleOrdenVenta;
					$detalle->id					=	$iddetalle;
					$detalle->ordenventa_id			=	$registro_id;
					$detalle->producto_id			=	$producto->id;
					$detalle->producto_descripcion	=	$producto->descripcion;
					$detalle->indproduccion			=	$producto->indproduccion;		
					$detalle->cantidad				=	$cantidad;
					$detalle->preciounitario		=	$producto->precio_venta;
					$detalle->total					=	$total;
					$detalle->tc					=	$cabecera->tc;
					$detalle->total_mn				=	$total*$cabecera->tc;
					$detalle->tipo_oro_id			=	$producto->tipo_oro_id;
					$detalle->tipo_oro_nombre		=	$producto->tipo_oro_nombre;
					$detalle->cantidad_oro			=	$producto->cantidad_oro;
					$detalle->fecha_crea 	 		=	$this->fechaactual;
					$detalle->usuario_crea 			=	Session::get('usuario')->id;
					$detalle->save();


					$listaprodgemas 				= 	ProductoGema::where('producto_id','=',$producto_id)
														->where('activo','=',1)->get();

					foreach($listaprodgemas as $index => $gema){						
						$iddov_gemas						=	$this->funciones->getCreateIdMaestra('detalleordenventas_productogemas');
												
						$ddov_gemas							=	new DetalleOrdenVenta_ProductoGema();
						$ddov_gemas->id						=	$iddov_gemas;						
						$ddov_gemas->ordenventa_id			=	$registro_id;
						$ddov_gemas->detalleordenventa_id	=	$iddetalle;
						$ddov_gemas->producto_id 	     	=   $gema->producto_id;
						$ddov_gemas->producto_nombre 		=   $gema->producto_nombre;			
						$ddov_gemas->gema_id 	   			=   $gema->gema_id;
						$ddov_gemas->gema_nombre			=   $gema->gema_nombre;								
						$ddov_gemas->cantidad				=   $gema->cantidad;								
						$ddov_gemas->fecha_crea       		=   $this->fechaactual;
						$ddov_gemas->usuario_crea     		=   Session::get('usuario')->id;
						$ddov_gemas->save();
					}

					$cabecera->venta 	   			=   $cabecera->venta+$total;	
					$cabecera->venta_mn 	   		=   $cabecera->venta_mn+($total*$cabecera->tc);	
					$cabecera->fecha_mod			=	$this->fechaactual;
					$cabecera->usuario_mod			=	Session::get('usuario')->id;
					$cabecera->save();	

					DB::commit();
				
		} catch (Exception $ex) {
			DB::rollback();
			$msj =$this->ge_getMensajeError($ex);
			return Redirect::to('/modificar-orden-ventas/'.$idopcion.'/'.$idregistro)->with('errorurl', $msj);
		}		

		// $idregistroen								= 	Hashids::encode(substr($idventa, -8));
		return Redirect::to('/modificar-orden-ventas/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Producto '.$producto->descripcion.' registrado con exito');
		
	}


	public function actionAjaxOrdenVentaEntreFechas(Request $request)
	{

		$finicio 		=  	date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin 			=  	date_format(date_create($request['ffin']), 'd-m-Y');
		$estado_id		=	$request['estado'];
		$cliente_id		=	$request['cliente'];
		$idopcion		=	$request['idopcion'];
		$listadatos 	= 	OrdenVenta::where('activo','=',1)	    					
	    					->where('fecha','>=', $finicio)
	    					->where('fecha','<=', $ffin)
	    					->CodEstado($estado_id)	    
	    					->CodCliente($cliente_id)
	    					->orderBy('id', 'asc')->get();
	    
		return View::make('ordenventa/ajax/alista',
						 [
							 'listadatos'   => $listadatos,
							 'idopcion'		=>	$idopcion,
							 'ajax'    		 => true,
						 ]);
	}


	public function actionAprobarOrdenVenta($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Listar Orden Venta');
		try {
					
					DB::beginTransaction();
					/******************************/
					$usuario                    	=   User::where('id',Session::get('usuario')->id)->first();


					$cabecera						=	OrdenVenta::find($registro_id);
					$cabecera->estado_id 	   		=   '1CIX00000004';
					$cabecera->estado_descripcion	=   'EMITIDO';	
					$cabecera->descuento_shopify	=   $cabecera->venta*2/100;	
					$cabecera->fecha_mod       		=   $this->fechaactual;
					$cabecera->usuario_mod     		=   Session::get('usuario')->id;
					$cabecera->save();



					$listadetalle 					= 	DetalleOrdenVenta::where('ordenventa_id','=',$registro_id)->where('activo','=','1')->get();

					foreach($listadetalle as $index => $detalle){
						//crear un esquema de producto x cada detalle
						$idregistro						=	$this->funciones->getCreateIdMaestra('esquemaproducto');
						$cod_registro 					=	$this->funciones->getCreateCodCorrelativo('esquemaproducto',8);
						$producto						=	Producto::where('id','=',$detalle->producto_id)->first();
						$tipooro						=	Producto::where('id','=',$detalle->tipo_oro_id)->first();
						$cantidad_total_gemas			=	0;
						$costo_total_gemas 				=	0;


						$precio_x_gramo 				=	$this->ov_precio_gramo_ultima_ov($detalle->producto_id);
						$costo_total_oro 				=	$precio_x_gramo*$detalle->cantidad_oro;
						$costo_total_engaste 			=	$this->ov_total_engaste_ultima_ov($detalle->producto_id);

						$esquema						=	new EsquemaProducto();
						$esquema->id					=	$idregistro;
						$esquema->codigo				=	$cod_registro;
						$esquema->ordenventa_id			=	$registro_id;
						$esquema->detalleordenventa_id	=	$detalle->id;
						$esquema->producto_id			=	$producto->id;
						$esquema->producto_descripcion	=	$producto->descripcion;
						$esquema->tipooro_id			=	$tipooro->id;
						$esquema->tipooro_descripcion	=	$tipooro->descripcion;
						$esquema->gramos				=	$detalle->cantidad_oro;
						$esquema->precio_x_gramo		=	$precio_x_gramo;
						$esquema->costo_total_oro		=	$costo_total_oro;
						$esquema->precio_total_engaste	=	$costo_total_engaste;
						$esquema->cantidad				=	$detalle->cantidad;
						$esquema->precio_venta			=	$detalle->preciounitario;
						$esquema->fecha_crea       		=   $this->fechaactual;
						$esquema->usuario_crea     		=   Session::get('usuario')->id;
						$esquema->save();

						$gemasproducto 					=	DetalleOrdenVenta_ProductoGema::where('producto_id','=',$detalle->producto_id)
															->where('ordenventa_id','=',$registro_id)->get();


						//dd($gemasproducto);

						foreach($gemasproducto as $index2 => $detalle2){



							$costo_unitario 				=	$this->ov_costo_unitario_gemas_ov($detalle2->gema_id);
							$costo_total 					=	$detalle2->cantidad*$costo_unitario;
							$cantidad_total_gemas 			=	$cantidad_total_gemas + $detalle2->cantidad;
							$costo_total_gemas 				=	$costo_total_gemas + $costo_total;

							$idregistrod					=	$this->funciones->getCreateIdMaestra('detalleesquemaproducto');
							$detalle						=	New DetalleEsquemaProducto();
							$detalle->id            		=   $idregistrod;
							$detalle->ordenventa_id			=	$registro_id;

							$detalle->esquemaproducto_id	=	$idregistro;
							$detalle->origen_id				=	'TOGE00000001';
							$detalle->origendescripcion		=	'PROVEEDOR';
							$detalle->tipo_id				=	$detalle2->gema_id;
							$detalle->tipodescripcion		=	$detalle2->gema_nombre;
							$detalle->cantidad				=	(float)$detalle2->cantidad;
							$detalle->costo_unitario		=	(float)$costo_unitario;
							$detalle->costo_total			=	(float)$costo_total;
							$detalle->usuario_crea     		=   Session::get('usuario')->id;
							$detalle->fecha            		=   date('d-m-Y');
							$detalle->fecha_mod       		=   $this->fechaactual;
							$detalle->usuario_mod     		=   Session::get('usuario')->id;
							$detalle->save();


						}

						$costo_unitario 					=	$costo_total_oro + $costo_total_gemas + $costo_total_engaste;
						EsquemaProducto::where('id','=',$idregistro)
											->update(
												[
													'cantidad_total_gemas'=>$cantidad_total_gemas,
													'costo_total_gemas'=>$costo_total_gemas,
													'costo_unitario'=>$costo_unitario,
													'costo_unitario_igv'=>$costo_unitario,
												]
											);
						$this->ov_calculo_total_orden_venta($registro_id);

					}

					DB::commit();
				
		} catch (Exception $ex) {
			DB::rollback();
			$msj =$this->ge_getMensajeError($ex);
			return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('errorurl', $msj);
		}
		/******************************/
		return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('bienhecho', 'Registro ['.$cabecera->codigo.'] EMITIDO con exito');

	}

	public function actionOrdenVentaEsquemaProductos($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Modificar Orden Venta');


		/******************************/
		$usuario			=	User::where('id',Session::get('usuario')->id)->first();
		$ordenventa			=	OrdenVenta::find($registro_id);
		$listadetalle		=	DetalleOrdenVenta::where('activo','=',1)
								->where('ordenventa_id','=',$registro_id)
								->orderby('id','asc')
								->orderby('producto_descripcion','asc')
								->get();

		$lregistro 			=	EsquemaProducto::where('ordenventa_id','=',$registro_id)->where('activo','=',1)->get();
		$activo_esquema 	=	EsquemaProducto::where('ordenventa_id','=',$registro_id)->where('activo','=',1)->first();
        
		$tab 				=	$activo_esquema->id;
        if(Session::has('tab')){
            $tab           =   Session::get('tab');
        }


		$combo_gemas		=   array('' => "Seleccione Gema") + Producto::where('subcategoria_nombre','=','GEMAS')->pluck('descripcion','id')->toArray();// + $datos;
		$select_gemas		=   '';
		$tipocambio			=   TipoCambio::where('fecha','<=',date('d-m-Y'))->orderby('fecha','desc')->first();
		$combo_origen_gema	=   array('' => "Seleccione Origen") + Categoria::where('tipo_categoria','=','TIPO_ORIGEN_GEMA')->pluck('descripcion','id')->toArray();// + $datos;
		$select_origen_gema	=   '';
		$producto			=	Producto::where('indproduccion','=',1)->skip(1)->first();

		$swmodificar        =   true;
		$listagemas 		=	DetalleEsquemaProducto::where('ordenventa_id','=',$ordenventa->id)->where('activo','=',1)->get();

		// dd($listagemas);
		return View::make('ordenventa/modificaresquema',
					[
						'idopcion'                  =>  $idopcion,
						'idregistro'				=>	$idregistro,
						'tab'						=>	$tab,

						'lregistro'           		=>  $lregistro,
						'ordenventa'           		=>  $ordenventa,
						'listadetalle'           	=>  $listadetalle,
						'listagemas'       			=>  $listagemas,
						'combo_gemas'               =>  $combo_gemas,
						'select_gemas'              =>  $select_gemas,
						'combo_origen_gema'         =>  $combo_origen_gema,
						'select_origen_gema'        =>  $select_origen_gema,
						'tipocambio'				=>	$tipocambio,
						'swmodificar'               => $swmodificar,
					]);
	}


	public function actionOrdenVentaMargenProductos($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Margen Orden Venta');


		/******************************/
		$usuario			=	User::where('id',Session::get('usuario')->id)->first();
		$ordenventa			=	OrdenVenta::find($registro_id);
		$listadetalle		=	DetalleOrdenVenta::where('activo','=',1)
								->where('ordenventa_id','=',$registro_id)
								->orderby('id','asc')
								->orderby('producto_descripcion','asc')
								->get();
		$lregistro 			=	EsquemaProducto::where('ordenventa_id','=',$registro_id)->where('activo','=',1)->get();
		$activo_esquema 	=	EsquemaProducto::where('ordenventa_id','=',$registro_id)->where('activo','=',1)->first();
        
		$tab 				=	$activo_esquema->id;
        if(Session::has('tab')){
            $tab           =   Session::get('tab');
        }
		$combo_gemas		=   array('' => "Seleccione Gema") + Producto::where('subcategoria_nombre','=','GEMAS')->pluck('descripcion','id')->toArray();// + $datos;
		$select_gemas		=   '';
		$tipocambio			=   TipoCambio::where('fecha','<=',date('d-m-Y'))->orderby('fecha','desc')->first();
		$combo_origen_gema	=   array('' => "Seleccione Origen") + Categoria::where('tipo_categoria','=','TIPO_ORIGEN_GEMA')->pluck('descripcion','id')->toArray();// + $datos;
		$select_origen_gema	=   '';
		$producto			=	Producto::where('indproduccion','=',1)->skip(1)->first();
		$swmodificar        =   true;
		$listagemas 		=	DetalleEsquemaProducto::where('ordenventa_id','=',$ordenventa->id)->where('activo','=',1)->get();

		return View::make('ordenventa/margenordenventa',
					[
						'idopcion'                  =>  $idopcion,
						'idregistro'				=>	$idregistro,
						'tab'						=>	$tab,
						'lregistro'           		=>  $lregistro,
						'ordenventa'           		=>  $ordenventa,
						'listadetalle'           	=>  $listadetalle,
						'listagemas'       			=>  $listagemas,
						'combo_gemas'               =>  $combo_gemas,
						'select_gemas'              =>  $select_gemas,
						'combo_origen_gema'         =>  $combo_origen_gema,
						'select_origen_gema'        =>  $select_origen_gema,
						'tipocambio'				=>	$tipocambio,
						'swmodificar'               => $swmodificar,
					]);
	}


	public function actionOrdenVentaModificarMargenProductos($idopcion,$idordenventa,Request $request)
	{
		View::share('titulo','Agregar Margen Orden de Venta');
		if($_POST)
		{
			
			try {
					DB::beginTransaction();
					/******************************/
					$registro_id = $this->funciones->decodificarmaestra($idordenventa);

					$descuento_shopify				=	(float)$request['descuento_shopify'];
					$checkout						=	(float)$request['checkout'];
					$shipping						=	(float)$request['shipping'];
					$papeleria						=	(float)$request['papeleria'];
					$cabecera                   	=   OrdenVenta::find($registro_id);
					$cabecera->descuento_shopify	=	$descuento_shopify;
					$cabecera->checkout				=	$checkout;
					$cabecera->shipping				=	$shipping;
					$cabecera->papeleria			=	$papeleria;
					$cabecera->fecha_mod      		=   $this->fechaactual;
					$cabecera->usuario_mod     		=   Session::get('usuario')->id;
					$cabecera->save();
					$this->ov_calculo_total_orden_venta($registro_id);
					DB::commit();
			} catch (Exception $ex) {
				DB::rollback();
				$msj =$this->ge_getMensajeError($ex);
				return Redirect::to('orden-ventas-margen-producto/'.$idopcion.'/'.$idordenventa)->with('errorurl', $msj);
			}
			/******************************/

			return Redirect::to('/orden-ventas-margen-producto/'.$idopcion.'/'.$idordenventa)->with('bienhecho', 'Registro guardado realizado con exito');
		}

	}


	public function actionOrdenVentaModificarEsquemaProductos($idopcion,$idesquemaproducto,$idordenventa,Request $request)
	{
		View::share('titulo','Agregar Esquema Producto');
		if($_POST)
		{
			
			try {
					DB::beginTransaction();
					/******************************/

					$gramos							=	(float)$request['gramos'];
					$precio_x_gramo					=	(float)$request['precio_x_gramo'];
					$engaste						=	(int)$request['engaste'];
					$costo_total_oro 				=	(float)($gramos*$precio_x_gramo);
					$costo_unitario 				=	$engaste + $costo_total_oro;
					$cabecera                   		=   EsquemaProducto::find($idesquemaproducto);
					$cabecera->gramos					=	$gramos;
					$cabecera->precio_x_gramo			=	$precio_x_gramo;
					$cabecera->costo_total_oro			=	$costo_total_oro;
					$cabecera->precio_total_engaste		=	$engaste;
					$cabecera->costo_unitario			=	$costo_unitario + $cabecera->costo_total_gemas;
					$cabecera->costo_unitario_igv		=	$costo_unitario + $cabecera->costo_total_gemas;
					$cabecera->fecha            		=   date('d-m-Y');
					$cabecera->fecha_mod       			=   $this->fechaactual;
					$cabecera->usuario_mod     			=   Session::get('usuario')->id;
					$cabecera->save();

					DB::commit();
				
			} catch (Exception $ex) {
				DB::rollback();
				$msj =$this->ge_getMensajeError($ex);
				return Redirect::to('orden-ventas-esquema-producto/'.$idopcion.'/'.$idordenventa)->with('errorurl', $msj);
			}
			/******************************/



            Session::flash('tab', $idesquemaproducto);
			return Redirect::to('/orden-ventas-esquema-producto/'.$idopcion.'/'.$idordenventa)->with('bienhecho', 'Registro guardado realizado con exito');
		}

	}

	public function actionAjaxActualizarEnvioOrdenVenta(Request $request)
    {
        $orden_venta_id             =   $request['data_orden_venta_id'];
        $idopcion                   =   $request['idopcion'];
        $envio                   	=   $request['envio'];
        
        $cabecera                    =   OrdenVenta::where('id','=',$orden_venta_id)->first();
        $cabecera->envio =   $envio;
        $cabecera->fecha_mod         =   $this->fechaactual;
        $cabecera->usuario_mod       =   Session::get('usuario')->id;
        $cabecera->save();

        $funcion                            =   $this;

        $registro                 =   OrdenVenta::where('id', $orden_venta_id)->first();
        $listadetalle             =   DetalleOrdenVenta::where('activo','=',1)
                                                ->where('ordenventa_id','=',$orden_venta_id)
                                                ->orderby('producto_descripcion','asc')->get();

        $funcion                    =   $this;
        return View::make('ordenventa/ajax/adetalleov',
                         [
                            'registro'         			=> $registro,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }

    public function actionAjaxActualizarDescuentoOrdenVenta(Request $request)
    {
        $orden_venta_id             =   $request['data_orden_venta_id'];
        $idopcion                   =   $request['idopcion'];
        $descuento                   	=   $request['descuento'];
        
        $cabecera                    =   OrdenVenta::where('id','=',$orden_venta_id)->first();
        $cabecera->descuento =   $descuento;
        $cabecera->fecha_mod         =   $this->fechaactual;
        $cabecera->usuario_mod       =   Session::get('usuario')->id;
        $cabecera->save();

        $funcion                            =   $this;

        $registro                 =   OrdenVenta::where('id', $orden_venta_id)->first();
        $listadetalle             =   DetalleOrdenVenta::where('activo','=',1)
                                                ->where('ordenventa_id','=',$orden_venta_id)
                                                ->orderby('producto_descripcion','asc')->get();

        $funcion                    =   $this;
        return View::make('ordenventa/ajax/adetalleov',
                         [
                            'registro'         			=> $registro,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }

    public function actionAjaxActualizarSeguroOrdenVenta(Request $request)
    {
        $orden_venta_id             =   $request['data_orden_venta_id'];
        $idopcion                   =   $request['idopcion'];
        $seguro                   	=   $request['seguro'];
        
        $cabecera                    =   OrdenVenta::where('id','=',$orden_venta_id)->first();
        $cabecera->seguro =   $seguro;
        $cabecera->fecha_mod         =   $this->fechaactual;
        $cabecera->usuario_mod       =   Session::get('usuario')->id;
        $cabecera->save();

        $funcion                            =   $this;

        $registro                 =   OrdenVenta::where('id', $orden_venta_id)->first();
        $listadetalle             =   DetalleOrdenVenta::where('activo','=',1)
                                                ->where('ordenventa_id','=',$orden_venta_id)
                                                ->orderby('producto_descripcion','asc')->get();

        $funcion                    =   $this;
        return View::make('ordenventa/ajax/adetalleov',
                         [
                            'registro'         			=> $registro,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }
}
