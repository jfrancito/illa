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


class GestionOrdenVentaController extends Controller
{
	use GeneralesTraits;
	use ConfiguracionTraits;

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

					$tipocambio					=	TipoCambio::whereDate('fecha','<=',$fecha)->orderby('fecha','desc')->first();
					$cabecera                   =   new OrdenVenta();
					$cabecera->id               =   $idregistro;
					$cabecera->codigo           =   $codigo;
					$cabecera->moneda_id   		=   $moneda_id;
					$cabecera->moneda_nombre   	=   $moneda->descripcion;
					$cabecera->cliente_id       =   $cliente_id;
					$cabecera->cliente_nombre   =   $cliente->nombre_razonsocial;
					$cabecera->tc				=	$tipocambio->venta;

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
		
		$select_tipo_oro			=	'';
		$combo_tipo_oro	 			=	[''=>'SELECCIONE TIPO ORO']+Producto::where('subcategoria_nombre','=','ORO')->where('activo','=',1)->pluck('descripcion','id')->toArray();
		
		return View::make('ordenventa/modal/ajax/madetalleregistro',
						 [		 	
						 	'registro' 				=> $registro,
						 	'registro_id' 			=> $registro_id,
						 	'cliente_nombre' 		=> $cliente_nombre,
						 	'select_producto' 		=> $select_producto,
						 	'combo_producto' 		=> $combo_producto,
						 	'select_tipo_oro' 		=> $select_tipo_oro,
						 	'combo_tipo_oro' 		=> $combo_tipo_oro,
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
	    $producto_id		=	$request['producto_id'];	
	    $tipooro_id			=	$request['tipooro_id'];	
					
		$cantidad				 				= 	floatval(str_replace(",","",$request['cantidad']));
		$producto	 							= 	Producto::where('id','=',$producto_id)->first();				
		$tipooro	 							= 	Producto::where('id','=',$tipooro_id)->first();				

		$iddetalle 						=   $this->funciones->getCreateIdMaestra('detalleordenventas');
		
		$detalle						=	new DetalleOrdenVenta;
		$detalle->id					=	$iddetalle;
		$detalle->ordenventa_id			=	$registro_id;
		$detalle->producto_id			=	$producto->id;
		$detalle->producto_descripcion	=	$producto->descripcion;
		$detalle->indproduccion			=	$producto->indproduccion;
		
		$detalle->tipooro_id			=	$tipooro->id;
		$detalle->tipooro_descripcion	=	$tipooro->descripcion;

		$detalle->cantidad				=	$cantidad;
		$detalle->fecha_crea 	 		=	$this->fechaactual;
		$detalle->usuario_crea 			=	Session::get('usuario')->id;
		$detalle->save();

		$cabecera						=	OrdenVenta::find($registro_id);
		$cabecera->fecha_mod			=	$this->fechaactual;
		$cabecera->usuario_mod			=	Session::get('usuario')->id;
		$cabecera->save();			

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
					$cabecera->fecha_mod       		=   $this->fechaactual;
					$cabecera->usuario_mod     		=   Session::get('usuario')->id;
					$cabecera->save();
					$listadetalle = DetalleOrdenVenta::where('ordenventa_id','=',$registro_id)->get();

					foreach($listadetalle as $index => $detalle){
						//crear un esquema de producto x cada detalle
						$idregistro						=	$this->funciones->getCreateIdMaestra('esquemaproducto');
						$cod_registro 					=	$this->funciones->getCreateCodCorrelativo('esquemaproducto',8);
						$producto						=	Producto::where('id','=',$detalle->producto_id)->first();
						$tipooro						=	Producto::where('id','=',$detalle->tipooro_id)->where('activo','=',1)->first();
						$esquema						=	new EsquemaProducto();
						$esquema->id					=	$idregistro;
						$esquema->codigo				=	$cod_registro;
						$esquema->ordenventa_id			=	$registro_id;
						$esquema->producto_id			=	$producto->id;
						$esquema->producto_descripcion	=	$producto->descripcion;
						$esquema->tipooro_id			=	$tipooro->id;
						$esquema->tipooro_descripcion	=	$tipooro->descripcion;
						$esquema->fecha_crea       		=   $this->fechaactual;
						$esquema->usuario_crea     		=   Session::get('usuario')->id;
						$esquema->save();
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
		$esquema 			=	
		// $listaesquemas 		=	EsquemaProducto::where('ordenventa_id','=',$registro_id)->where('activo','=',1)->get();
		$registro 			=	EsquemaProducto::where('ordenventa_id','=',$registro_id)->where('activo','=',1)->first();

		
		$combo_gemas		=   array('' => "Seleccione Gema") + Producto::where('subcategoria_nombre','=','GEMAS')->pluck('descripcion','id')->toArray();// + $datos;
		$select_gemas		=   '';
		$tipocambio			=   TipoCambio::where('fecha','<=',date('d-m-Y'))->orderby('fecha','desc')->first();
		$combo_origen_gema	=   array('' => "Seleccione Origen") + Categoria::where('tipo_categoria','=','TIPO_ORIGEN_GEMA')->pluck('descripcion','id')->toArray();// + $datos;
		$select_origen_gema	=   '';
		$producto			=	Producto::where('indproduccion','=',1)->skip(1)->first();

		$swmodificar        =   true;
		$listagemas 		=	DetalleEsquemaProducto::where('esquemaproducto_id','=',$registro->id)->where('activo','=',1)->get();
		// dd($listagemas);
		return View::make('ordenventa/modificaresquema',
					[
						'idopcion'                  =>  $idopcion,
						'idregistro'				=>	$idregistro,
						'registro'           		=>  $registro,
						'ordenventa'           		=>  $ordenventa,
						'listagemas'       		=>  $listagemas,
						'combo_gemas'               =>  $combo_gemas,
						'select_gemas'              =>  $select_gemas,
						'combo_origen_gema'         =>  $combo_origen_gema,
						'select_origen_gema'        =>  $select_origen_gema,
						'tipocambio'				=>	$tipocambio,
						'swmodificar'               => $swmodificar,
					]);
	}


	public function actionOrdenVentaModificarEsquemaProductos($idopcion,$idregistro,Request $request)
	{
		View::share('titulo','Agregar Esquema Producto');
		if($_POST)
		{
			
			try {
					DB::beginTransaction();
					/******************************/
					// $registro_id				=	$this->funciones->decodificarmaestra($idregistro);
					$registro_id				=	$request['registro_id'];
					$listagemas 				=	explode('&&&',$request['xmllistagemas']);
					// dd($listagemas);
					$usuario                    =   User::where('id',Session::get('usuario')->id)->first();
					// $descripcion                =   $request['descripcion'];

					$producto_id                =   $request['producto_id'];
					$producto                   =   Producto::where('id','=',$producto_id)->first();
					
					$tipooro_id					=	$request['tipooro_id'];
					$tipooro                    =   Producto::where('id','=',$tipooro_id)->first();

					$gramos						=	(float)$request['gramos'];
					$precio_x_gramo				=	(float)$request['precio_x_gramo'];

					$cantidad_engaste			=	(int)$request['cantidad_engaste'];
					$precio_unitario_engaste	=	(float)$request['precio_unitario_engaste'];
					$precio_total_engaste		=	(float)$request['precio_total_engaste'];
					$precio_total_engaste		=	(float)$request['precio_total_engaste'];
					// dd($this->monedaxdefecto);
					$moneda                     =   Moneda::where('id','=',$this->monedaxdefecto)->first();
					$fecha                      =   date('Y-m-d');

					

					$tipocambio                 	=   TipoCambio::where('fecha','<=',date('d-m-Y'))->orderby('fecha','desc')->first();
					$cabecera                   	=   EsquemaProducto::find($registro_id);
			
					$cabecera->producto_id    		=   $producto_id;
					$cabecera->producto_descripcion =   $producto->descripcion;

					$cabecera->tipooro_id       	=   $tipooro_id;
					$cabecera->tipooro_descripcion  =   $tipooro->descripcion;
					
					$cabecera->gramos				=	$gramos;
					$cabecera->precio_x_gramo		=	$precio_x_gramo;
					$cabecera->costo_total_oro		=	(float)($gramos*$precio_x_gramo);
					
					
					$cabecera->cantidad_total_gemas		=	$cantidad_engaste;
					$cabecera->cantidad_engaste			=	$cantidad_engaste;
					$cabecera->precio_unitario_engaste	=	$precio_unitario_engaste;
					$cabecera->precio_total_engaste		=	$precio_total_engaste;

					$cabecera->ind_igv					=	$request['indigv'];
					$cabecera->monto_igv				=	(float)$request['monto_igv'];
					$cabecera->costo_unitario			=	(float)$request['costo_unitario'];
					$cabecera->costo_unitario_igv			=	(float)$request['costo_unitario_igv'];
					$cabecera->costo_unitario_igv			=	(float)$request['costo_unitario_igv'];
					$cabecera->costo_unitario_total			=	(float)$request['costo_unitario_total'];

					$cabecera->tc               		=   $tipocambio->venta;
					$cabecera->costo_unitario_total_mn	=	((float)$request['costo_unitario_total'])*$tipocambio->venta;

					// $cabecera->estado_id                =   '1CIX00000003';
					// $cabecera->estado_descripcion       =   'GENERADO'; 
					$cabecera->costo_total_gemas		=	(float)($request['htotal_costo_gemas']);
					
					$cabecera->fecha            =   date('d-m-Y');
					$cabecera->fecha_mod       =   $this->fechaactual;
					$cabecera->usuario_mod     =   Session::get('usuario')->id;
					$cabecera->save();
					// dd(count($listagemas));
					$cantidad = count($listagemas) -1;
					// dd($listagemas);
					DetalleEsquemaProducto::where('esquemaproducto_id','=',$registro_id)
										->update(
											[
												'fecha_mod'=>date('d-m-Y'),
												'usuario_mod'=>Session::get('usuario')->id,
												'activo'=>0,
											]
										);
					foreach ($listagemas as $index => $gema) {
						$datos = explode('***', $gema);
						if($index<$cantidad){

							$categoria_origen = Categoria::where('tipo_categoria','=','TIPO_ORIGEN_GEMA')->where('descripcion','=',$datos[0])->first();
							// dd($categoria_origen);
							$idregistro              	=   $this->funciones->getCreateIdMaestra('detalleesquemaproducto');
							// $codigo                     =   $this->funciones->getCreateCodCorrelativo('ordenventas',8);

							$detalle					=	New DetalleEsquemaProducto();
							$detalle->id            	=   $idregistro;
							$detalle->esquemaproducto_id	=	$registro_id;
							$detalle->origen_id			=	$categoria_origen->id;
							$detalle->origendescripcion	=	$categoria_origen->descripcion;

							$detalle->tipo_id			=	$datos[1];
							$detalle->tipodescripcion	=	$datos[2];
							$detalle->cantidad			=	$datos[3];
							$detalle->costo_unitario	=	(float)$datos[4];
							$detalle->costo_total		=	((float)($datos[4]))*((float)($datos[3]));

							$detalle->usuario_crea     =   Session::get('usuario')->id;
							$detalle->fecha            	=   date('d-m-Y');
							$detalle->fecha_mod       	=   $this->fechaactual;
							$detalle->usuario_mod     	=   Session::get('usuario')->id;
							$detalle->save();
						}

					}



					$idregistroen               =   Hashids::encode(substr($idregistro, -8));

					DB::commit();
				
			} catch (Exception $ex) {
				DB::rollback();
				$msj =$this->ge_getMensajeError($ex);
				return Redirect::to('/orden-ventas-esquema-producto/'.$idopcion.'/'.$idregistro)->with('errorurl', $msj);
			}
			/******************************/

			return Redirect::to('/gestion-orden-venta/'.$idopcion)->with('bienhecho', 'Registro realizado con exito');
		}

	}
}
