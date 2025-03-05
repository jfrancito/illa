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
use App\Modelos\Venta;
use App\Modelos\DetalleVenta;
use App\Modelos\Kardex;
use App\Modelos\Caja;
use App\Modelos\ReferenciaDocumento;


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



	public function actionGuardarDetalleGemaEsquema($idopcion,$esquema_id,$detalleesquema_id,$idordenventa,Request $request)
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

	public function actionAjaxModalEliminarDetalleGemaEsquema(Request $request)
	{


		$gema_esquema_id 	 		= 	$request['gema_esquema_id'];
		$idopcion 	 				= 	$request['idopcion'];
		$esquema_id 	 			= 	$request['esquema_id'];
		$ordenventa_id 	 			= 	$request['ordenventa_id'];



		$detesquemaproducto 		=	DetalleEsquemaProducto::where('id','=',$gema_esquema_id)->where('activo','=',1)->first();
		
		return View::make('ordenventa/modal/ajax/maeliminardetallegema',
						 [		 	
						 	'gema_esquema_id' 		=> $gema_esquema_id,
						 	'esquema_id' 			=> $esquema_id,
						 	'ordenventa_id' 		=> $ordenventa_id,						 	
						 	'detesquemaproducto' 	=> $detesquemaproducto,						 	
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionEliminarDetalleGemaEsquema($idopcion,$esquema_id,$detalleesquema_id,$idordenventa,Request $request)
	{

		if($_POST)
		{
			
			try {
					DB::beginTransaction();
					/******************************/

					$cabecera            	 		=	DetalleEsquemaProducto::find($detalleesquema_id);
					$cabecera->fecha_mod 	 		= 	$this->fechaactual;
					$cabecera->usuario_mod 			=   Session::get('usuario')->id;
					$cabecera->activo 	     		=  	0;	
					$cabecera->save();			

					$esquema 					   	=    EsquemaProducto::where('id','=',$esquema_id)->first();

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
			return Redirect::to('/orden-ventas-esquema-producto/'.$idopcion.'/'.$idordenventa)->with('bienhecho', 'Gema eliminada con exito');
		}

	}

	public function actionAjaxModalAgregarDetalleGemaEsquema(Request $request)
	{


		$idopcion 	 				= 	$request['idopcion'];
		$esquema_id 	 			= 	$request['esquema_id'];
		$ordenventa_id 	 			= 	$request['ordenventa_id'];



		$esquemaproducto 			=	EsquemaProducto::where('id','=',$esquema_id)->first();
		
		$combo_origen_gema			=   array('' => "Seleccione Origen") + Categoria::where('tipo_categoria','=','TIPO_ORIGEN_GEMA')->pluck('descripcion','id')->toArray();// + $datos;
		$select_origen_gema			=   '';

		$combo_gemas			=   array('' => "Seleccione Gema") + Producto::where('subcategoria_nombre','=','GEMAS')->pluck('descripcion','id')->toArray();// + $datos;
		$select_gemas			=   '';

		//dd($idopcion);

		
		return View::make('ordenventa/modal/ajax/maagregardetallegema',
						 [		 	
						 	'esquema_id' 			=> $esquema_id,
						 	'ordenventa_id' 		=> $ordenventa_id,
						 	'esquemaproducto' 		=> $esquemaproducto,
						 	'combo_origen_gema' 	=> $combo_origen_gema,
						 	'select_origen_gema' 	=> $select_origen_gema,
						 	'combo_gemas' 			=> $combo_gemas,
						 	'select_gemas' 			=> $select_gemas,
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionAgregarDetalleGemaEsquema($idopcion,$esquema_id,$idordenventa,Request $request)
	{

		if($_POST)
		{
			
			try {
					DB::beginTransaction();
					/******************************/

					$ordenventa_id = $this->funciones->decodificarmaestra($idordenventa);

					$tipogema_id					=	$request['tipogema_id'];
					$origen_id						=	$request['origen_id'];
					$cantidad						=	(float)$request['cantidad'];
					$precio							=	(float)$request['precio'];
					$costo_total 					=	$cantidad * $precio;

					$origen 						=	Categoria::where('id','=',$origen_id)->first();
					$tipogema	 					= 	Producto::where('id','=',$tipogema_id)->first();				

					$iddetalle						=	$this->funciones->getCreateIdMaestra('detalleesquemaproducto');
					$detalle						=	New DetalleEsquemaProducto();
					$detalle->id            		=   $iddetalle;
					$detalle->ordenventa_id			=	$ordenventa_id;

					$detalle->esquemaproducto_id	=	$esquema_id;
					$detalle->origen_id				=	$origen_id;
					$detalle->origendescripcion		=	$origen->descripcion;
					$detalle->tipo_id				=	$tipogema->id;
					$detalle->tipodescripcion		=	$tipogema->descripcion;
					$detalle->cantidad				=	$cantidad;
					$detalle->costo_unitario		=	$precio;
					$detalle->costo_total			=	$costo_total;
					$detalle->usuario_crea     		=   Session::get('usuario')->id;
					$detalle->fecha            		=   date('d-m-Y');
					$detalle->fecha_mod       		=   $this->fechaactual;
					$detalle->usuario_mod     		=   Session::get('usuario')->id;
					$detalle->save();

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
		View::share('titulo','Listar Pedido Shopify');

		$finicio                        =   $this->inicio;
		$ffin                           =   $this->fin;

		$select_proveedor               =   '';
		$combo_proveedor                =   $this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');
			$combo_cliente          =[''=>'SELECCIONE']+Cliente::where('activo','=',1)->pluck('nombre_razonsocial','id')->toArray();

		$select_estado                  =   '';
		$select_cliente                 =   '';
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

	public function actionResumenOrdenesVenta($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idregistro);
	    View::share('titulo','Resumen Pedido Shopify');		

	    $refdoc_compra	    			=	ReferenciaDocumento::where('referencia_id','=',$registro_id)
	    									->where('tipo_referencia','=','OC')
	    									->where('activo','=',1)
	    									->first();

	    $refdoc_venta	    			=	ReferenciaDocumento::where('referencia_id','=',$registro_id)
	    									->where('tipo_referencia','=','OV')
	    									->where('activo','=',1)
	    									->first();

	    $select_moneda 	 				=	'';
	    $combo_moneda 					=	$this->gn_combo_moneda('Seleccione moneda','');	

	    $select_tipo_comprobante 	 	=	'';
		$combo_tipo_comprobante 		=	$this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');	    		    

	    /*******TAB MODIFICAR INICIO*******/
		$registro 			=	OrdenVenta::where('id', $registro_id)->first();

		$listadetalle		=	DetalleOrdenVenta::where('activo','=',1)
								->where('ordenventa_id','=',$registro_id)
								->orderby('id','asc')
								->orderby('producto_descripcion','asc')
								->get();

		$select_cliente		=	$registro->cliente_id;
		$combo_cliente      =	[''=>'SELECCIONE']+Cliente::where('activo','=',1)->pluck('nombre_razonsocial','id')->toArray();
	    
	    /*******TAB MODIFICAR FIN**********/

	    /*******TAB PRODUCCION Y MARGEN INICIO*******/
	    $ordenventa			=	OrdenVenta::find($registro_id);
		
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

		$listagemas 		=	DetalleEsquemaProducto::where('ordenventa_id','=',$ordenventa->id)->where('activo','=',1)->get();
	    /*******TAB PRODUCCION Y MARGEN FIN**********/

	    /*******TAB COMPRA INICIO*******/	    
	    if(isset($refdoc_compra)){
	    	$idcompra 						=	$refdoc_compra->documento_id;

		    $compra 						= 	Compra::where('id', $idcompra)->first();
			$listadetallecompra				= 	DetalleCompra::where('activo','=',1)
												->where('compra_id','=',$idcompra)
												->orderby('producto_nombre','asc')->get();

			$select_proveedor		  		=	$compra->proveedor_id;
			
		    $combo_proveedor	 			=	$this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');    
		    $select_tipo_compra				=	$compra->tipo_compra_id;
		    $combo_tipo_compra 				=	$this->gn_combo_categoria('TIPO_COMPRA','Seleccione tipo compra','');
	    }else{
	    	$idcompra 						=	'';

		    $compra 						= 	Compra::where('id', $idcompra)->first();
			$listadetallecompra				= 	DetalleCompra::where('activo','=',1)
												->where('compra_id','=',$idcompra)
												->orderby('producto_nombre','asc')->get();

			$select_proveedor		  		=	'';
			
		    $combo_proveedor	 			=	$this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');    
		    $select_tipo_compra				=	'';
		    $combo_tipo_compra 				=	$this->gn_combo_categoria('TIPO_COMPRA','Seleccione tipo compra','');
	    }
		
	    /*******TAB COMPRA FIN**********/

	    /*******TAB VENTA INICIO*******/
	    if(isset($refdoc_venta)){
	    	$idventa 						=	$refdoc_venta->documento_id;

		    $venta 							= 	Venta::where('id', $idventa)->first();
			$listadetalleventa				= 	DetalleVenta::where('activo','=',1)
												->where('venta_id','=',$idventa)
												->orderby('producto_nombre','asc')->get();

			$select_cliente		  			=	$venta->cliente_id;
		    $combo_cliente	 				=	$this->gn_generacion_combo('clientes','id','nombre_razonsocial','SELECCIONE CLIENTE','');	    
		    
		    $select_tipo_venta				=	$venta->tipo_venta_id;
		    $combo_tipo_venta 				=	$this->gn_combo_categoria('TIPO_VENTA','SELECCIONE','');

		    $select_tipo_pago				=	$venta->tipo_pago_id;
		    $combo_tipo_pago 				=	$this->gn_combo_categoria('TIPO_PAGO','SELECCIONE','');		   
	    }else{
	    	$idventa 						=	'';

		    $venta 							= 	Venta::where('id', $idventa)->first();
			$listadetalleventa				= 	DetalleVenta::where('activo','=',1)
												->where('venta_id','=',$idventa)
												->orderby('producto_nombre','asc')->get();

			$select_cliente		  			=	'';
		    $combo_cliente	 				=	$this->gn_generacion_combo('clientes','id','nombre_razonsocial','SELECCIONE CLIENTE','');    
		    
		    $select_tipo_venta				=	'';
		    $combo_tipo_venta 				=	$this->gn_combo_categoria('TIPO_VENTA','SELECCIONE','');

		    $select_tipo_pago				=	'';
		    $combo_tipo_pago 				=	$this->gn_combo_categoria('TIPO_PAGO','SELECCIONE','');		  
	    }
	     
	    /*******TAB VENTA FIN**********/

	    
	    	     
        return View::make('ordenventa/resumen', 
        				[
        					'registro'  				=> 	$registro,
        					'listadetalle'				=> 	$listadetalle,
        					'select_cliente'  			=> 	$select_cliente,
	        				'combo_cliente' 			=> 	$combo_cliente,
	        				'select_moneda' 			=> 	$select_moneda,		
	        				'combo_moneda' 				=> 	$combo_moneda,		
	        				'idregistro'				=>	$idregistro,
				  			'idopcion' 					=>	$idopcion,
				  			'swmodificar'				=>	false,				  			
							
							'tab'						=>	$tab,
							'lregistro'           		=>  $lregistro,
							'ordenventa'           		=>  $ordenventa,							
							'listagemas'       			=>  $listagemas,
							'combo_gemas'               =>  $combo_gemas,
							'select_gemas'              =>  $select_gemas,
							'combo_origen_gema'         =>  $combo_origen_gema,
							'select_origen_gema'        =>  $select_origen_gema,
							'tipocambio'				=>	$tipocambio,	

							'compra'  					=> 	$compra,
        					'listadetallecompra'		=> 	$listadetallecompra,
        					'select_proveedor'  		=> 	$select_proveedor,
	        				'combo_proveedor' 			=> 	$combo_proveedor,									
	        				'select_tipo_comprobante'  	=> 	$select_tipo_comprobante,
        					'combo_tipo_comprobante'  	=> 	$combo_tipo_comprobante,	        				
	        				'select_tipo_compra'  		=> 	$select_tipo_compra,
							'combo_tipo_compra'   		=> 	$combo_tipo_compra,					  			

				  			'venta'  					=> 	$venta,
        					'listadetalleventa'			=> 	$listadetalleventa,
        					'select_cliente'  			=> 	$select_cliente,
	        				'combo_cliente' 			=> 	$combo_cliente,					  				
	        				'select_tipo_venta'  		=> 	$select_tipo_venta,
							'combo_tipo_venta'   		=> 	$combo_tipo_venta,	
							'select_tipo_pago'  		=> 	$select_tipo_pago,
							'combo_tipo_pago'   		=>	$combo_tipo_pago,	
							'swresumen'					=>	true
        				]);		
	}

	public function actionAgregarOrdenesVenta($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Agregar Pedido Shopify');

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
	    View::share('titulo','Modificar Pedido Shopify');

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
					  			'swresumen'			=>	false
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

	public function actionAjaxModalAgregarCliente(Request $request)
	{		
		$idopcion 	 = 	$request['idopcion'];

		$select_tipo_documento  =	'1CIX00000033';
	    $combo_tipo_documento 	=	$this->gn_combo_categoria('TIPO_DOCUMENTO','Seleccione tipo documento','');

	    $select_pais	=	'';
	    $combo_paises 	=	$this->gn_combo_paises();		

	    $disabletipodocumento  	=	false;
	    $disablenumerodocumento =	false;
				
		return View::make('ordenventa/modal/ajax/maagregarcliente',
						 [							 	
						 	'idopcion' 					=> $idopcion,
						 	'select_tipo_documento'  	=>  $select_tipo_documento,
							'combo_tipo_documento'   	=>  $combo_tipo_documento,
							'disabletipodocumento'   	=>  $disabletipodocumento,
							'disablenumerodocumento' 	=>  $disablenumerodocumento,
							'combo_paises'				=>	$combo_paises,
							'select_pais'  				=>  $select_pais,
						 	'ajax' 						=> true,						 	
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

				    $producto_id					=	$request['producto_id'];	
				    $preciounitario					=	floatval(str_replace(",","",$request['preciounitario']));
				    
					$cantidad				 		= 	floatval(str_replace(",","",$request['cantidad']));
					$producto	 					= 	Producto::where('id','=',$producto_id)->first();				
					$total	 						= 	$cantidad*$preciounitario;		

					$cabecera						=	OrdenVenta::find($registro_id);
					
					$iddetalle 						=   $this->funciones->getCreateIdMaestra('detalleordenventas');
					
					$detalle						=	new DetalleOrdenVenta;
					$detalle->id					=	$iddetalle;
					$detalle->ordenventa_id			=	$registro_id;
					$detalle->producto_id			=	$producto->id;
					$detalle->producto_descripcion	=	$producto->descripcion;
					$detalle->indproduccion			=	$producto->indproduccion;		
					$detalle->cantidad				=	$cantidad;
					$detalle->preciounitario		=	$preciounitario;
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


	public function actionValidarOrdenVenta($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Listar Pedido Shopify');
		try {
					
					DB::beginTransaction();
					/******************************/
					$usuario                    	=   User::where('id',Session::get('usuario')->id)->first();


					$cabecera						=	OrdenVenta::find($registro_id);
					$cabecera->estado_id 	   		=   '1CIX00000046';
					$cabecera->estado_descripcion	=   'VALIDADO';	
					$cabecera->descuento_shopify	=   $cabecera->venta*2/100;	
					$cabecera->fecha_valida       	=   $this->fechaactual;
					$cabecera->usuario_valida     	=   Session::get('usuario')->id;
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
		return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('bienhecho', 'Registro ['.$cabecera->codigo.'] VALIDADO con exito');

	}

	public function actionAprobarOrdenVenta($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Listar Pedido Shopify');
		try {
					
					DB::beginTransaction();
					
					$cabecera						=	OrdenVenta::find($registro_id);
					$cabecera->estado_id 	   		=   '1CIX00000034';
					$cabecera->estado_descripcion	=   'APROBADO';						
					$cabecera->fecha_aprobar       	=   $this->fechaactual;
					$cabecera->usuario_aprobar     	=   Session::get('usuario')->id;
					$cabecera->save();

					DB::commit();
				
		} catch (Exception $ex) {
			DB::rollback();
			$msj =$this->ge_getMensajeError($ex);
			return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('errorurl', $msj);
		}
		/******************************/
		return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('bienhecho', 'Registro ['.$cabecera->codigo.'] APROBADO con exito');

	}


	public function actionOrdenVentaEsquemaProductos($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Modificar Pedido Shopify');


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
						'swresumen'					=>	false
					]);
	}


	public function actionOrdenVentaMargenProductos($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Margen Pedido Shopify');


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
						'swresumen'					=>	false
					]);
	}


	public function actionOrdenVentaModificarMargenProductos($idopcion,$idordenventa,Request $request)
	{
		View::share('titulo','Agregar Margen Pedido Shopify');
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
                            'swresumen' 				=> false
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
                            'swresumen' 				=> false                       
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
                            'swresumen' 				=> false
                         ]);

    }

    public function actionCargarPrecioUnitarioOrdenVentaAjax(Request $request)
	{
		$producto_id   			= $request['producto_id'];
		$producto				= 	Producto::where('id','=',$producto_id)->first();

		
		return View::make('ordenventa/ajax/apreciounitario',
						 [
						 	'producto' 	=> $producto,						 	
							'ajax'		=> true,
						 ]);
	}

	public function actionQuitarDetalleOrdenVenta($idopcion,$iddetalle,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $iddetalle = $this->funciones->decodificarmaestra($iddetalle);			    

	    try {
					DB::beginTransaction();
					/******************************/
					
					$activo			 					= 	0;

					$detalle            	 			=	DetalleOrdenVenta::find($iddetalle);
					$detalle->activo 					=   $activo;					
					$detalle->fecha_mod 	 			=   $this->fechaactual;
					$detalle->usuario_mod 				=   Session::get('usuario')->id;
					$detalle->save();

					$cabecera            	 			=	OrdenVenta::find($detalle->ordenventa_id);
					$cabecera->venta 	   				=   $cabecera->venta-$detalle->total;		
					$cabecera->venta_mn	   				=   $cabecera->venta_mn-$detalle->total_mn;
					$cabecera->fecha_mod 	 			=   $this->fechaactual;
					$cabecera->usuario_mod 				=   Session::get('usuario')->id;
					$cabecera->save();			

					DB::commit();
				
		} catch (Exception $ex) {
			DB::rollback();
			 $msj =$this->ge_getMensajeError($ex);
			 $idordenventaen							= 	Hashids::encode(substr($detalle->ordenventa_id, -8));
			return Redirect::to('/modificar-orden-ventas/'.$idopcion.'/'.$idordenventaen)->with('errorurl', $msj);
		}
		/******************************/		

		$idordenventaen							= 	Hashids::encode(substr($detalle->ordenventa_id, -8));
	 	return Redirect::to('/modificar-orden-ventas/'.$idopcion.'/'.$idordenventaen)->with('bienhecho', 'Producto '.$detalle->producto_descripcion.' quitado con exito');
		
	}

	public function actionFacturarOrdenesVenta($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Facturar Pedido Shopify');

		/******************************/	

		$lote_venta		 			=   $this->funciones->getCreateLoteCorrelativo('ventas',10);

		$ordenventa			=	OrdenVenta::find($registro_id);
		$listadetalle		=	DetalleOrdenVenta::where('activo','=',1)
								->where('ordenventa_id','=',$registro_id)
								->orderby('id','asc')
								->orderby('producto_descripcion','asc')
								->get();
		
		$select_cliente		  			=	$ordenventa->cliente_id;
	    $combo_cliente	 				=	$this->gn_generacion_combo('clientes','id','nombre_razonsocial','SELECCIONE CLIENTE','');
	    $select_tipo_comprobante 	 	=	'';
	    $combo_tipo_comprobante 		=	$this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
	    $select_moneda 	 				=	$ordenventa->moneda_id;
	    $combo_moneda 					=	$this->gn_combo_moneda('Seleccione moneda','');			
	    
	    $select_tipo_venta				=	'';
	    $combo_tipo_venta 				=	$this->gn_combo_categoria('TIPO_VENTA','SELECCIONE','');
	    
	    $swmodificar					=	true;

		return View::make('ordenventa/facturarordenventa',
					[
						'idopcion'                  =>  $idopcion,
						'lote_venta'				=> 	$lote_venta,						
						'ordenventa'           		=>  $ordenventa,
						'listadetalle'           	=>  $listadetalle,
						'select_cliente'       		=>  $select_cliente,
						'combo_cliente'             =>  $combo_cliente,
						'select_tipo_comprobante'   =>  $select_tipo_comprobante,
						'combo_tipo_comprobante'    =>  $combo_tipo_comprobante,
						'select_moneda'        		=>  $select_moneda,
						'combo_moneda'				=>	$combo_moneda,
						'select_tipo_venta'        	=>  $select_tipo_venta,
						'combo_tipo_venta'			=>	$combo_tipo_venta,
						'swmodificar'               => $swmodificar,
					]);
	}	

	public function actionGenerarEmitirVenta($idopcion,$idregistro,Request $request)
	{
		View::share('titulo','Agregar Ventas');
		if($_POST)
		{			
			try {
					DB::beginTransaction();
					/******************************/

					$registro_id = $this->funciones->decodificarmaestra($idregistro);

					$lote 	 							= 	$request['lote'];
					$serie 	 							= 	$request['serie'];
					$numero 	 						= 	$request['numero'];
					$fecha 	 							= 	$request['fecha'];					
					$montototal 						=	(float)DetalleOrdenVenta::where('ordenventa_id','=',$registro_id)
															->where('activo',1)->sum('total');
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

					$almacen_id 						= '1CIX00000001';
					$almacen 							= Almacen::where('id','=',$almacen_id)->first();

					$motivo_id 							= 	$this->getIdMotivoDocumento('VENTA');
					$motivo 							= 	Categoria::where('id','=',$motivo_id)->first();

					$listadet_ordenventa				=	DetalleOrdenVenta::where('activo','=',1)
															->where('ordenventa_id','=',$registro_id)
															->orderby('id','asc')
															->orderby('producto_descripcion','asc')
															->get();

					$ordenventa                   		=   OrdenVenta::find($registro_id);
					$ordenventa->indventa 	 			=   1;
					$ordenventa->estado_id 	   			=  	'1CIX00000048';
					$ordenventa->estado_descripcion 	=   'TERMINADO';
					$ordenventa->fecha_terminado 	 	=   $this->fechaactual;
					$ordenventa->usuario_terminado 		=   Session::get('usuario')->id;
					$ordenventa->save();

					$idventa 							=   $this->funciones->getCreateIdMaestra('ventas');
					
					$cabecera            	 			=	new Venta;
					$cabecera->id 	     	 			=   $idventa;
					$cabecera->lote 					=   $lote;			
					$cabecera->serie 					=   $serie;			
					$cabecera->numero 	   				=   $numero;
					$cabecera->fecha			 	 	=   $fecha;
					$cabecera->montototal 	   			=   $montototal;		
					$cabecera->tc 	   					=   $tc;			
					$cabecera->tipo_comprobante_id		=   $tipo_comprobante->id;
					$cabecera->tipo_comprobante_nombre 	=   $tipo_comprobante->descripcion;			
					$cabecera->cliente_id				=   $cliente->id;
					$cabecera->cliente_nombre 			=   $cliente->nombre_razonsocial;			
					$cabecera->moneda_id				=   $moneda->id;
					$cabecera->moneda_nombre 			=   $moneda->descripcion;		
					$cabecera->estado_id 	   			=  	$this->emitido->id;
					$cabecera->estado_descripcion 	   	=   $this->emitido->descripcion;
					$cabecera->fecha_emision 	 		=   $this->fechaactual;
					$cabecera->usuario_emision 			=   Session::get('usuario')->id;
					$cabecera->tipo_venta_id			=   $tipo_venta->id;
					$cabecera->tipo_venta_nombre 		=   $tipo_venta->descripcion;			
					$cabecera->tipo_pago_id				=   $tipo_pago->id;
					$cabecera->tipo_pago_nombre 		=   $tipo_pago->descripcion;			
					$cabecera->motivo_id 				= 	$motivo_id;
				    $cabecera->motivo_nombre 			= 	$motivo->descripcion;
					$cabecera->fecha_crea 	 			=   $this->fechaactual;
					$cabecera->usuario_crea 			=   Session::get('usuario')->id;					
					$cabecera->save();

					$refdoc 							=	new ReferenciaDocumento;
					$refdoc->documento_id 				= 	$idventa;
					$refdoc->referencia_id 				= 	$registro_id;
				    $refdoc->tipo_referencia 			= 	'OV';
				    $refdoc->fecha_crea 	 			=   $this->fechaactual;
					$refdoc->usuario_crea 				=   Session::get('usuario')->id;					
					$refdoc->save();

					foreach($listadet_ordenventa as $item){
						$cantidad				 				= 	(float)$item->cantidad;
						$preciounitario							= 	(float)$item->preciounitario;

						$producto_id	 						= 	$item->producto_id;
						$igv	 								= 	0;
						$porcigv	 							= 	0;
						$indigv	 								= 	0;
						
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

						$detallealmacen							= 	DetalleAlmacen::where('activo','=',1)
																	->where('almacen_id','=',$almacen_id)	
																	->where('producto_id','=',$item->producto_id)->first();

						$cantidadinicial 	= 0;
						$cantidadingreso 	= 0;
						$cantidadfinal		= 0;


						if (count($detallealmacen) <= 0) {
							return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('errorbd', 'NO TIENE STOCK SUFICIENTE DE : '.$producto->descripcion.' STOCK: 0');							
						}else{

							$cantidadinicial 	= $detallealmacen->stock;
							$cantidadsalida 	= $item->cantidad;
							$cantidadfinal		= $cantidadinicial - $cantidadsalida;	
							if($cantidadfinal<0){
								return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('errorbd', 'NO TIENE STOCK SUFICIENTE DE : '.$producto->descripcion .' STOCK: '.$cantidadinicial);
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
						$kardex->lote 	     	 		=   $lote;			
						$kardex->almacen_id 	     	=   $almacen_id;
						$kardex->almacen_nombre 	   	=   $almacen->nombre;
						$kardex->tipo_movimiento_id 	=   $idtipomovimiento;
						$kardex->tipo_movimiento_nombre =   'SALIDA';
						$kardex->compraventa_id 		=   $idventaventa;
						$kardex->compraventa_nombre 	=   'VENTA';
						$kardex->fecha 				 	=   date_format(date_create($cabecera->fecha_emision), 'd/m/Y');						
						$kardex->fechahora			 	=   date_format(date_create($cabecera->fecha_emision), 'd/m/Y H:i:s');						
						$kardex->producto_id 			=   $item->producto_id;			
						$kardex->producto_nombre 	   	=   $item->producto_descripcion;
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

					$idcaja 						=   $this->funciones->getCreateIdMaestra('caja');

					$tipo_movimiento				=	Categoria::where('tipo_categoria','=','TIPO_MOVIMIENTO')->where('descripcion','=','ENTRADA')->first();

					$caja            	 			=	new Caja;
					$caja->id						=	$idcaja;
					$caja->tipo_movimiento_id		=	$tipo_movimiento->id;
					$caja->tipo_movimiento_nombre	=	$tipo_movimiento->descripcion;
					$caja->tipo_movimiento			=	(int)$tipo_movimiento->aux01;

					$caja->movimiento_id			=	$idventa;
					$caja->tabla_movimiento			=	'ventas';

					$caja->ind_comprobante			=	1;
					$caja->tipo_comprobante_id		=	$tipo_comprobante->id;
					$caja->tipo_comprobante_nombre	=	$tipo_comprobante->descripcion;
					
					$caja->serie					=	$serie;
					$caja->numero					=	$numero;
					$caja->cliente_id				=	$cliente->id;
					$caja->cliente_nombre			=	$cliente->nombre_razonsocial;

					$caja->fecha					=	date('d-m-Y',strtotime($fecha));
					$caja->moneda_id				=	$moneda->id;
					$caja->moneda_nombre			=	$moneda->descripcion;

					$caja->tc						=	$tc;
				
					$caja->saldo					=	$montototal;
					$caja->montototal				=	$montototal;
					$caja->total					=	$montototal;

					$caja->estado_id 	   			=  	$this->generado->id;
					$caja->estado_descripcion 	   	=   $this->generado->descripcion;	
					$caja->fecha_crea 	 			=   $this->fechaactual;
					$caja->usuario_crea 			=   Session::get('usuario')->id;
					$caja->save();
			    	
					$codigo 						= 	$serie.'-'.ltrim($numero, '0');

					DB::commit();

			} catch (Exception $ex) {
				DB::rollback();
				$msj =$this->ge_getMensajeError($ex);
				return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('errorurl', $msj);
			}
			/******************************/
			$opcionventa 		=	Opcion::where('nombre','=','Ventas')->where('activo','=',1)->first();
			$idopcionventa		=	($opcionventa) ? Hashids::encode(substr($opcionventa->id,-8)) : '';
			return Redirect::to('/gestion-de-ventas/'.$idopcionventa)->with('bienhecho', 'Venta '.$codigo.' generada y emitida con exito');
		}
	}

	public function actionComprarOrdenesVenta($idopcion,$idregistro,Request $request)
	{

			/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Comprar Pedido Shopify');

		/******************************/	

		$lote_compra		 			=   $this->funciones->getCreateLoteCorrelativo('compras',10);

		$ordenventa						=	OrdenVenta::find($registro_id);
		$listadetalle					=	EsquemaProducto::where('activo','=',1)
											->where('ordenventa_id','=',$registro_id)
											->orderby('id','asc')
											->orderby('producto_descripcion','asc')
											->get();
		
		$select_proveedor		  		=	'1CIX00000001';
		$combo_proveedor	 			=	$this->gn_generacion_combo('proveedores','id','nombre_razonsocial','Seleccione proveedor','');
	    $select_tipo_comprobante 	 	=	'';
	    $combo_tipo_comprobante 		=	$this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
	    $select_moneda 	 				=	$ordenventa->moneda_id;
	    $combo_moneda 					=	$this->gn_combo_moneda('Seleccione moneda','');			
	    
	    $select_tipo_compra				=	'';
		$combo_tipo_compra 				=	$this->gn_combo_categoria('TIPO_COMPRA','Seleccione tipo compra','');
	    
	    $swmodificar					=	true;

		return View::make('ordenventa/comprarordenventa',
					[
						'idopcion'                  =>  $idopcion,
						'lote_compra'				=> 	$lote_compra,						
						'ordenventa'           		=>  $ordenventa,
						'listadetalle'           	=>  $listadetalle,
						'select_proveedor'       	=>  $select_proveedor,
						'combo_proveedor'           =>  $combo_proveedor,
						'select_tipo_comprobante'   =>  $select_tipo_comprobante,
						'combo_tipo_comprobante'    =>  $combo_tipo_comprobante,
						'select_moneda'        		=>  $select_moneda,
						'combo_moneda'				=>	$combo_moneda,
						'select_tipo_compra'        =>  $select_tipo_compra,
						'combo_tipo_compra'			=>	$combo_tipo_compra,
						'swmodificar'               => $swmodificar,
					]);
	}

	public function actionGenerarEmitirCompra($idopcion,$idregistro,Request $request)
	{
		View::share('titulo','Agregar Compras');
		if($_POST)
		{			
			try {
					DB::beginTransaction();
					/******************************/

					$registro_id = $this->funciones->decodificarmaestra($idregistro);

					$lote 	 							= 	$request['lote'];
					$serie 	 							= 	$request['serie'];
					$numero 	 						= 	$request['numero'];
					$fecha 	 							= 	$request['fecha'];					
					$montototal 						=	(float)$request['data_total'];		
					
					$tc				 					= 	0;
					$tipo_comprobante_id	 			= 	$request['tipo_comprobante_id'];
					$proveedor_id	 					= 	$request['proveedor_id'];
					$moneda_id	 						= 	$request['moneda_id'];
					$tipo_compra_id	 					= 	$request['tipo_compra_id'];					
					
					$proveedor	 						= 	Proveedor::where('id','=',$proveedor_id)->first();		
					$tipo_comprobante	 				= 	Categoria::where('id','=',$tipo_comprobante_id)->first();			
					$moneda	 							= 	Moneda::where('id','=',$moneda_id)->first();
					$tipo_compra 		 				= 	Categoria::where('id','=',$tipo_compra_id)->first();			

					$almacen_id 						= '1CIX00000001';
					$almacen 							= Almacen::where('id','=',$almacen_id)->first();

					$motivo_id 							= 	$this->getIdMotivoDocumento('COMPRA');
					$motivo 							= 	Categoria::where('id','=',$motivo_id)->first();

					$listadet_esquemaproducto			=	EsquemaProducto::where('activo','=',1)
															->where('ordenventa_id','=',$registro_id)
															->orderby('id','asc')
															->orderby('producto_descripcion','asc')
															->get();

					$ordenventa                   		=   OrdenVenta::find($registro_id);
					$ordenventa->indcompra 	 			=   1;
					$ordenventa->estado_id 	   			=  	'1CIX00000047';
					$ordenventa->estado_descripcion 	=   'ATENDIDO PARCIALMENTE';
					$ordenventa->fecha_atendido 	 	=   $this->fechaactual;
					$ordenventa->usuario_atendido 		=   Session::get('usuario')->id;
					$ordenventa->save();

					$idcompra 							=   $this->funciones->getCreateIdMaestra('compras');
					
					$cabecera            	 			=	new Compra;
					$cabecera->id 	     	 			=   $idcompra;
					$cabecera->lote 					=   $lote;			
					$cabecera->serie 					=   $serie;			
					$cabecera->numero 	   				=   $numero;
					$cabecera->fecha			 	 	=   $fecha;
					$cabecera->montototal 	   			=   $montototal;		
					$cabecera->tc 	   					=   $tc;			
					$cabecera->tipo_comprobante_id		=   $tipo_comprobante->id;
					$cabecera->tipo_comprobante_nombre 	=   $tipo_comprobante->descripcion;			
					$cabecera->proveedor_id				=   $proveedor->id;
					$cabecera->proveedor_nombre 		=   $proveedor->nombre_razonsocial;			
					$cabecera->moneda_id				=   $moneda->id;
					$cabecera->moneda_nombre 			=   $moneda->descripcion;		
					$cabecera->estado_id 	   			=  	$this->emitido->id;
					$cabecera->estado_descripcion 	   	=   $this->emitido->descripcion;
					$cabecera->fecha_emision 	 		=   $this->fechaactual;
					$cabecera->usuario_emision 			=   Session::get('usuario')->id;
					$cabecera->tipo_compra_id			=   $tipo_compra->id;
					$cabecera->tipo_compra_nombre 		=   $tipo_compra->descripcion;								
					$cabecera->motivo_id 				= 	$motivo_id;
				    $cabecera->motivo_nombre 			= 	$motivo->descripcion;
					$cabecera->fecha_crea 	 			=   $this->fechaactual;
					$cabecera->usuario_crea 			=   Session::get('usuario')->id;					
					$cabecera->save();

					$refdoc 							=	new ReferenciaDocumento;
					$refdoc->documento_id 				= 	$idcompra;
					$refdoc->referencia_id 				= 	$registro_id;
				    $refdoc->tipo_referencia 			= 	'OC';
				    $refdoc->fecha_crea 	 			=   $this->fechaactual;
					$refdoc->usuario_crea 				=   Session::get('usuario')->id;					
					$refdoc->save();

					foreach($listadet_esquemaproducto as $item){
						$cantidad				 				= 	(float)$item->cantidad;
						$preciounitario							= 	(float)$item->costo_unitario;

						$producto_id	 						= 	$item->producto_id;
						$igv	 								= 	0;
						$porcigv	 							= 	0;
						$indigv	 								= 	0;
						
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
						$detallecompra->estado_id 	   			=   $this->emitido->id;
						$detallecompra->estado_descripcion 	   	=	$this->emitido->descripcion;
						$detallecompra->fecha_crea 	 			=   $this->fechaactual;
						$detallecompra->usuario_crea 			=   Session::get('usuario')->id;
						$detallecompra->save();

						$detallealmacen				= 	DetalleAlmacen::where('activo','=',1)
														->where('almacen_id','=',$almacen_id)
														->where('proveedor_id','=',$cabecera->proveedor_id)
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
							$detallealmacen->proveedor_id 			=   $cabecera->proveedor_id;			
							$detallealmacen->proveedor_nombre 	   	=   $cabecera->proveedor_nombre;
							$detallealmacen->producto_id 			=   $item->producto_id;			
							$detallealmacen->producto_nombre 	   	=   $producto->descripcion;
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
						$kardex->lote 	     	 		=   $lote;			
						$kardex->almacen_id 	     	=   $almacen_id;
						$kardex->almacen_nombre 	   	=   $almacen->nombre;
						$kardex->tipo_movimiento_id 	=   $idtipomovimiento;
						$kardex->tipo_movimiento_nombre =   'ENTRADA';
						$kardex->compraventa_id 		=   $idcompraventa;
						$kardex->compraventa_nombre 	=   'COMPRA';
						$kardex->compraventa_tabla		=	'compras';
						$kardex->registroaux_id			=	$idcompra;
						$kardex->fecha 				 	=   date_format(date_create($cabecera->fecha_emision), 'd/m/Y');						
						$kardex->fechahora			 	=   date_format(date_create($cabecera->fecha_emision), 'd/m/Y H:i:s');						
						$kardex->producto_id 			=   $item->producto_id;			
						$kardex->producto_nombre 	   	=   $item->producto_descripcion;
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

					$idcaja 						=   $this->funciones->getCreateIdMaestra('caja');

					$tipo_movimiento				=	Categoria::where('tipo_categoria','=','TIPO_MOVIMIENTO')->where('descripcion','=','SALIDA')->first();

					$caja            	 			=	new Caja;
					$caja->id						=	$idcaja;
					$caja->tipo_movimiento_id		=	$tipo_movimiento->id;
					$caja->tipo_movimiento_nombre	=	$tipo_movimiento->descripcion;
					$caja->tipo_movimiento			=	(int)$tipo_movimiento->aux01;

					$caja->movimiento_id			=	$idcompra;
					$caja->tabla_movimiento			=	'compras';

					$caja->ind_comprobante			=	1;
					$caja->tipo_comprobante_id		=	$tipo_comprobante->id;
					$caja->tipo_comprobante_nombre	=	$tipo_comprobante->descripcion;
					
					$caja->serie					=	$serie;
					$caja->numero					=	$numero;
					$caja->cliente_id				=	$proveedor->id;
					$caja->cliente_nombre			=	$proveedor->nombre_razonsocial;

					$caja->fecha					=	date('d-m-Y',strtotime($fecha));
					$caja->moneda_id				=	$moneda->id;
					$caja->moneda_nombre			=	$moneda->descripcion;

					$caja->tc						=	$tc;
				
					$caja->saldo					=	$montototal;
					$caja->montototal				=	$montototal;
					$caja->total					=	$montototal;

					$caja->estado_id 	   			=  	$this->generado->id;
					$caja->estado_descripcion 	   	=   $this->generado->descripcion;	
					$caja->fecha_crea 	 			=   $this->fechaactual;
					$caja->usuario_crea 			=   Session::get('usuario')->id;
					$caja->save();
			    	
					$codigo 						= 	$serie.'-'.ltrim($numero, '0');

					DB::commit();

			} catch (Exception $ex) {
				DB::rollback();
				$msj =$this->ge_getMensajeError($ex);
				return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('errorurl', $msj);
			}
			/******************************/
			$opcioncompra 		=	Opcion::where('nombre','=','Compras')->where('activo','=',1)->first();
			$idopcioncompra		=	($opcioncompra) ? Hashids::encode(substr($opcioncompra->id,-8)) : '';
			return Redirect::to('/gestion-de-compras/'.$idopcioncompra)->with('bienhecho', 'Compra '.$codigo.' generada y emitida con exito');
		}
	}

	public function actionAjaxAgregarCliente($idopcion,Request $request)
	{
		$tipo_documento_id 	 		= 	$request['tipo_documento_id'];			
		if($tipo_documento_id == '1CIX00000033'){
			$sindocumento 	 		= 	1;
		}else{
			$sindocumento 	 		= 	0;
		}		
		
		$nombre_razonsocial 	 	= 	$request['nombre_razonsocial'];
		$direccion 	 		 		= 	$request['direccion'];		
		$pais_id 	 				= 	$request['pais_id'];

		$tipo_documento 			= 	Categoria::where('id','=',$tipo_documento_id)->first();

		$idcliente 					=   $this->funciones->getCreateIdMaestra('clientes');
		
		$cabecera            	 			=	new Cliente;
		$cabecera->id 	     	 			=   $idcliente;
		$cabecera->tipo_documento_id		=   $tipo_documento->id;
		$cabecera->tipo_documento_nombre 	=   $tipo_documento->descripcion;		
		$cabecera->sindocumento 			=   $sindocumento;
		$cabecera->nombre_razonsocial 	   	=   $nombre_razonsocial;
		$cabecera->pais_id 					=	$pais_id;		
		$cabecera->direccion 	   			=   $direccion;		
		$cabecera->fecha_crea 	 			=   $this->fechaactual;
		$cabecera->usuario_crea 			=   Session::get('usuario')->id;
		$cabecera->save();

 		return Redirect::to('/agregar-orden-ventas/'.$idopcion)->with('bienhecho', 'Cliente '.$nombre_razonsocial.' registrado con exito');
	}
}
