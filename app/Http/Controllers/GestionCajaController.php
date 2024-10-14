<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
use App\Modelos\CajaDetalle;
use App\Modelos\Moneda;

use App\Modelos\CuentasEmpresa;
use App\Modelos\EntidadFinanciera;

use App\User;
// use Illuminate\Http\Request;
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


class GestionCajaController extends Controller
{

	use GeneralesTraits;
	use ConfiguracionTraits;
	private     $idmodal        =   'cobro-venta';
	private     $rutaview       =   'cobrarcaja';
	private     $rutaviewblade  =   'cobrarcaja';

	public function actionListarCajaxCobrar($idopcion)
	{


		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Listar Caja Venta');

		$finicio                        =   $this->inicio;
		$ffin                           =   $this->fin;

		$select_cliente             =   '';
		$combo_cliente              =   $this->gn_generacion_combo('clientes','id','nombre_razonsocial','Seleccione Cliente','');

		$select_estado                  =   '';
		// $combo_estado                   =   $this->gn_combo_estadoscompras('Seleccione estado','');;
		$combo_estado                   =   $this->gn_combo_estadoscobro('Seleccione estado','');;

		$listadatos                     =   Caja::where('fecha','>=', $finicio)
											->where('fecha','<=', $ffin)
											->where('tipo_movimiento_nombre','=','ENTRADA')
											->where('tabla_movimiento','=','ventas')
											->orderBy('id', 'desc')->get();
		$funcion                        =   $this;




		return View::make($this->rutaview.'/lista',
						 [
							'listadatos'           => $listadatos,
							'funcion'               => $funcion,
							'inicio'                => $finicio,
							'fin'                   => $ffin,
							'select_cliente'        => $select_cliente,
							'combo_cliente'         => $combo_cliente,  
							'select_estado'         => $select_estado,
							'combo_estado'          => $combo_estado,   
							'idopcion'              => $idopcion,   
							'idmodal'               =>  $this->idmodal,
							'view'                  =>  $this->rutaview,
						 ]);
	}


	public function actionListarCajaxPagar($idopcion)
	{


		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Listar Caja Compra');

		$finicio                        =   $this->inicio;
		$ffin                           =   $this->fin;

		$select_cliente             =   '';
		$combo_cliente              =   $this->gn_generacion_combo('clientes','id','nombre_razonsocial','Seleccione Cliente','');

		$select_estado                  =   '';
		// $combo_estado                   =   $this->gn_combo_estadoscompras('Seleccione estado','');;
		$combo_estado                   =   $this->gn_combo_estadoscobro('Seleccione estado','');;

		$listadatos                     =   Caja::where('fecha','>=', $finicio)
											->where('fecha','<=', $ffin)
											->where('tipo_movimiento_nombre','=','SALIDA')
											->where('tabla_movimiento','=','compras')
											->orderBy('id', 'desc')->get();
		$funcion                        =   $this;

		return View::make('pagocaja/lista',
						 [
							'listadatos'           => $listadatos,
							'funcion'               => $funcion,
							'inicio'                => $finicio,
							'fin'                   => $ffin,
							'select_cliente'        => $select_cliente,
							'combo_cliente'         => $combo_cliente,  
							'select_estado'         => $select_estado,
							'combo_estado'          => $combo_estado,   
							'idopcion'              => $idopcion,   
							'idmodal'               =>  $this->idmodal,
							'view'                  =>  $this->rutaview,
						 ]);
	}


	public function actionAjaxListarCajasEntreFechas(Request $request)
	{

		$idopcion       =   $request['idopcion'];       
		$finicio        =   date_format(date_create($request['finicio']), 'd-m-Y');
		$ffin           =   date_format(date_create($request['ffin']), 'd-m-Y');
		$cliente_id =   $request['cliente'];        
		$estado_id      =   $request['estado'];             

		$listadatos    =   Venta::where('fecha','>=', $finicio)
							->where('fecha','<=', $ffin)
							->CodCliente($cliente_id)       
							->CodEstado($estado_id)
							->orderBy('id', 'asc')->get();

		
		return View::make($this->rutaview.'/ajax/alistaventa',
						 [
							 'listadatos'   => $listadatos,
							 'idopcion'      => $idopcion,
							 'ajax'          => true,
						 ]);
	}



	public function actionCobrarCajaVenta($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Cobrar Venta');

		if($_POST)
		{

			/******************* validar url **********************/
			// $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
		    // if($validarurl <> 'true'){return $validarurl;}
		    /******************************************************/
		    dd('ss05');
		    // $registro_id = $this->funciones->decodificarmaestra($idregistro);		
						
			// $importe				 				= 	floatval(str_replace(",","",$request['importe']));
			// $preciounitario							= 	floatval(str_replace(",","",$request['preciounitario']));

			// $entidad_id	 							= 	$request['entidad_id'];
			// $cuenta_id	 							= 	$request['cuenta_id'];

			// $entidad 						=	EntidadFinanciera::where('id','=',$entidad_id)->first();
			// $cuenta							=	CuentasEmpresa::where('id','=',$cuenta_id)->first();
			// $moneda							=	Moneda::where('id','=',$cuenta->moneda_id)->first();

			// $iddetalle 						=   $this->funciones->getCreateIdMaestra('cajadetalle');
			
			// $detallecaja            	 			=	new CajaDetalle;
			// $detallecaja->id 	     	 			=   $iddetalle;
			// $detallecaja->entidad_id 	     	 	=   $entidad->id;
			// $detallecaja->entidad_nombre 	     	=   $entidad->entidad;

			// $detallecaja->cuenta_id 				=   $cuenta->id;			
			// $detallecaja->moneda_id 				=   $cuenta->moneda_id;			

			// $detallecaja->nrocta 	   				=   $cuenta->nrocta;
			// // $detallecaja->indproduccion			=	$producto->indproduccion;


			// $detallecaja->cantidad			 		=   $cantidad;
			// $detallecaja->preciounitario 	   		=   $preciounitario;
			
			// // $detallecaja->indigv 	   				=   $indigv;
			// // $detallecaja->igv 	   					=   $igv;
			// // $detallecaja->porcigv 	   				=   $porcigv;
			// // $detallecaja->subtotal 				=	$detallesubtotal;

			// $detallecaja->total 	   				=   $detalletotal;						
			// $detallecaja->fecha_crea 	 			=   $this->fechaactual;
			// $detallecaja->usuario_crea 			=   Session::get('usuario')->id;
			// $detallecaja->save();

			// $cabecera            	 				=	Caja::find($registro_id);
			// $cabecera->montototal 	   				=   $cabecera->montototal+$detalletotal;		
			// $cabecera->fecha_mod 	 				=   $this->fechaactual;
			// $cabecera->usuario_mod 					=   Session::get('usuario')->id;
			// $cabecera->save();			

			// $idventaen								= 	Hashids::encode(substr($idventa, -8));
		 	// return Redirect::to('/cobrar-caja-venta/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Detalle '.$registro->descripcion.' registrado con exito');

			// $serie                              =   $request['serie'];
			// $numero                             =   $request['numero'];
			// $fecha                              =   $request['fecha'];
			// //$montototal                           =   $request['montototal'];
			// $tc                                 =   0;
			// $tipo_comprobante_id                =   $request['tipo_comprobante_id'];
			// $cliente_id                         =   $request['cliente_id'];
			// $moneda_id                          =   $request['moneda_id'];
			// $tipo_venta_id                      =   $request['tipo_venta_id'];          
			// $tipo_pago_id                       =   $request['tipo_pago_id'];           


			// $cliente                            =   Cliente::where('id','=',$cliente_id)->first();      
			// $tipo_comprobante                   =   Categoria::where('id','=',$tipo_comprobante_id)->first();           
			// $moneda                             =   Categoria::where('id','=',$moneda_id)->first();
			// $tipo_venta                         =   Categoria::where('id','=',$tipo_venta_id)->first();         
			// $tipo_pago                          =   Categoria::where('id','=',$tipo_pago_id)->first();          

			// $cabecera                           =   Venta::find($idventa);
			// $cabecera->serie                    =   $serie;         
			// $cabecera->numero                   =   $numero;
			// $cabecera->fecha                    =   $fecha;
			// //$cabecera->montototal                 =   0.0;        
			// $cabecera->tc                       =   $tc;            
			// $cabecera->tipo_comprobante_id      =   $tipo_comprobante->id;
			// $cabecera->tipo_comprobante_nombre  =   $tipo_comprobante->descripcion;         
			// $cabecera->cliente_id               =   $cliente->id;
			// $cabecera->cliente_nombre           =   $cliente->nombre_razonsocial;           
			// $cabecera->moneda_id                =   $moneda->id;
			// $cabecera->moneda_nombre            =   $moneda->descripcion;               
			
			// $cabecera->tipo_venta_id            =   $tipo_venta->id;
			// $cabecera->tipo_venta_nombre        =   $tipo_venta->descripcion;
			
			// $cabecera->tipo_pago_id             =   $tipo_pago->id;
			// $cabecera->tipo_pago_nombre         =   $tipo_pago->descripcion;

			// $cabecera->fecha_mod                =   $this->fechaactual;
			// $cabecera->usuario_mod              =   Session::get('usuario')->id;
			// $cabecera->save();

			// return Redirect::to('/gestion-de-ventas/'.$idopcion)->with('bienhecho', 'Venta '.$serie.'-'.$numero.' modificada con exito');
						

		}else{

			$registro                   =   Caja::where('id', $registro_id)->first();
			$listadetalleregistro       =   CajaDetalle::where('activo','=',1)
												->where('caja_id','=',$registro_id)
												->orderby('fecha_crea','desc')->get();

			$select_cliente             =   $registro->cliente_id;
			$combo_cliente              =   $this->gn_generacion_combo('clientes','id','nombre_razonsocial','SELECCIONE CLIENTE','');
			$select_tipo_comprobante        =   $registro->tipo_comprobante_id;
			$combo_tipo_comprobante         =   $this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
			$select_moneda                  =   $registro->moneda_id;
			$combo_moneda                   =   $this->gn_combo_categoria('MONEDA','Seleccione moneda','');         
			
			$select_tipo_registro              =   $registro->tipo_registro_id;
			$combo_tipo_registro               =   $this->gn_combo_categoria('TIPO_VENTA','SELECCIONE','');

			$select_tipo_pago               =   $registro->tipo_pago_id;
			$combo_tipo_pago                =   $this->gn_combo_categoria('TIPO_PAGO','SELECCIONE','');
			
			return View::make($this->rutaview.'/cobrarcajaregistro', 
							[
								'registro'                     => $registro,
								'listadetalleregistro'     => $listadetalleregistro,
								'select_cliente'        => $select_cliente,
								'combo_cliente'             => $combo_cliente,                                  
								'select_tipo_comprobante'   => $select_tipo_comprobante,
								'combo_tipo_comprobante'    => $combo_tipo_comprobante,
								'select_moneda'             => $select_moneda,      
								'combo_moneda'              => $combo_moneda,       
								// 'select_tipo_venta'         => $select_tipo_venta,
								// 'combo_tipo_venta'          => $combo_tipo_venta,   
								'select_tipo_pago'          => $select_tipo_pago,
								'combo_tipo_pago'           => $combo_tipo_pago,    
								'idopcion'                  => $idopcion,
								'idmodal'               =>  $this->idmodal,
								'view'                  =>  $this->rutaview,
							]);
		}

	}


	public function actionPagarCajaCompra($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		View::share('titulo','Pagar Compra');

		if($_POST)
		{

		    dd('ss05');

					
		}else{

			$registro                   =   Caja::where('id', $registro_id)->first();
			$listadetalleregistro       =   CajaDetalle::where('activo','=',1)
												->where('caja_id','=',$registro_id)
												->orderby('fecha_crea','desc')->get();

			$select_cliente             =   $registro->cliente_id;
			$combo_cliente              =   $this->gn_generacion_combo('clientes','id','nombre_razonsocial','SELECCIONE CLIENTE','');
			$select_tipo_comprobante        =   $registro->tipo_comprobante_id;
			$combo_tipo_comprobante         =   $this->gn_combo_categoria('TIPO_COMPROBANTE','Seleccione tipo comprobante','');
			$select_moneda                  =   $registro->moneda_id;
			$combo_moneda                   =   $this->gn_combo_categoria('MONEDA','Seleccione moneda','');         
			
			$select_tipo_registro              =   $registro->tipo_registro_id;
			$combo_tipo_registro               =   $this->gn_combo_categoria('TIPO_VENTA','SELECCIONE','');

			$select_tipo_pago               =   $registro->tipo_pago_id;
			$combo_tipo_pago                =   $this->gn_combo_categoria('TIPO_PAGO','SELECCIONE','');
			
			return View::make('pagocaja/pagarcajaregistro', 
							[
								'registro'                     => $registro,
								'listadetalleregistro'     => $listadetalleregistro,
								'select_cliente'        => $select_cliente,
								'combo_cliente'             => $combo_cliente,                                  
								'select_tipo_comprobante'   => $select_tipo_comprobante,
								'combo_tipo_comprobante'    => $combo_tipo_comprobante,
								'select_moneda'             => $select_moneda,      
								'combo_moneda'              => $combo_moneda,       
								// 'select_tipo_venta'         => $select_tipo_venta,
								// 'combo_tipo_venta'          => $combo_tipo_venta,   
								'select_tipo_pago'          => $select_tipo_pago,
								'combo_tipo_pago'           => $combo_tipo_pago,    
								'idopcion'                  => $idopcion,
								'idmodal'               =>  'pago-compra',
								'view'                  =>  'pagocaja',
							]);
		}

	}


	public function actionAjaxModalDetalleCobrarVenta(Request $request)
	{
		$registro_id    =  $request['venta_id'];
		$idopcion    =  $request['idopcion'];

		$registro                      =   Caja::where('id', $registro_id)->first();
		$tipo_comprobante_nombre    =   $registro->tipo_comprobante_nombre;
		$serie                      =   $registro->serie;
		$numero                     =   $registro->numero;
		
		$opcionproducto             =   Opcion::where('pagina','=','gestion-cuentas-empresa')->where('activo','=',1)->first();
		// dd($opcionproducto);
		$idopcionproducto           =   ($opcionproducto) ? Hashids::encode(substr($opcionproducto->id,-8)) : '';
		$select_producto            =   '';

		$idcuentasEmpresa 			=	CuentasEmpresa::where('activo','=',1)->selectRaw("DISTINCT entidad_id")->pluck('entidad_id')->toArray();
		
		$combo_entidades			=	[''=>'SELECCIONE OPCION']+EntidadFinanciera::whereIn('id',$idcuentasEmpresa)->pluck('entidad','id')->toArray();
		$combo_cuentas				=	[];
		// $combo_				=	[];

		$combo_producto             =   $this->gn_generacion_combo('productos','id','descripcion','Seleccione producto','');
		$select_tipo_pago			=	'TPCA00000002';
		$select_cuenta				=	'';
		$combo_tipo_pago 			=	$this->gn_combo_categoria('TIPO_PAGO','SELECCIONE','');
		
		return View::make($this->rutaview.'/modal/ajax/madetalle',
						 [          
							'venta_id'              	=> $registro_id,
							'tipo_comprobante_nombre'	=> $tipo_comprobante_nombre,
							'serie'						=> $serie,
							'numero'                    => $numero,
							'select_producto'           => $select_producto,
							'combo_producto'            => $combo_producto,
							'combo_entidades'           => $combo_entidades,
							'combo_cuentas'            	=> $combo_cuentas,
							'idopcion'                  => $idopcion,
							'idopcionproducto'          => $idopcionproducto,
							'select_cuenta'				=> $select_cuenta,
							'select_tipo_pago'			=> $select_tipo_pago,
							'combo_tipo_pago'			=>	$combo_tipo_pago,
							'ajax'                      => true,         
							'registro'					=>	$registro,                   
						 ]);
	}


	public function actionAjaxModalDetallePagoCompra(Request $request)
	{
		$registro_id    =  $request['venta_id'];
		$idopcion    =  $request['idopcion'];

		$registro                      =   Caja::where('id', $registro_id)->first();
		$tipo_comprobante_nombre    =   $registro->tipo_comprobante_nombre;
		$serie                      =   $registro->serie;
		$numero                     =   $registro->numero;
		
		$opcionproducto             =   Opcion::where('pagina','=','gestion-cuentas-empresa')->where('activo','=',1)->first();
		// dd($opcionproducto);
		$idopcionproducto           =   ($opcionproducto) ? Hashids::encode(substr($opcionproducto->id,-8)) : '';
		$select_producto            =   '';

		$idcuentasEmpresa 			=	CuentasEmpresa::where('activo','=',1)->selectRaw("DISTINCT entidad_id")->pluck('entidad_id')->toArray();
		
		$combo_entidades			=	[''=>'SELECCIONE OPCION']+EntidadFinanciera::whereIn('id',$idcuentasEmpresa)->pluck('entidad','id')->toArray();
		$combo_cuentas				=	[];
		// $combo_				=	[];

		$combo_producto             =   $this->gn_generacion_combo('productos','id','descripcion','Seleccione producto','');
		$select_tipo_pago			=	'TPCA00000002';
		$select_cuenta				=	'';
		$combo_tipo_pago 			=	$this->gn_combo_categoria('TIPO_PAGO','SELECCIONE','');
		
		return View::make('pagocaja/modal/ajax/madetalle',
						 [          
							'venta_id'              	=> $registro_id,
							'tipo_comprobante_nombre'	=> $tipo_comprobante_nombre,
							'serie'						=> $serie,
							'numero'                    => $numero,
							'select_producto'           => $select_producto,
							'combo_producto'            => $combo_producto,
							'combo_entidades'           => $combo_entidades,
							'combo_cuentas'            	=> $combo_cuentas,
							'idopcion'                  => $idopcion,
							'idopcionproducto'          => $idopcionproducto,
							'select_cuenta'				=> $select_cuenta,
							'select_tipo_pago'			=> $select_tipo_pago,
							'combo_tipo_pago'			=>	$combo_tipo_pago,
							'ajax'                      => true,         
							'registro'					=>	$registro,                   
						 ]);
	}


	public function actionAgregarDetalleCobroVentas($idopcion,$idregistro,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		try {
			DB::beginTransaction();
			$registro_id 					=	$this->funciones->decodificarmaestra($idregistro);		
			$registro						=	Caja::where('id','=',$registro_id)->first();
			$importe				 		=	floatval(str_replace(",","",$request['importe']));
			$entidad_id						=	$request['entidad_id'];
			$cuenta_id						=	$request['cuenta_id'];
			$tipo_pago_id					=	'TPCA00000002';
			
			$tipopago						=	Categoria::where('id','=',$tipo_pago_id)->first();
			$entidad 						=	EntidadFinanciera::where('id','=',$entidad_id)->first();
			$cuenta							=	CuentasEmpresa::where('id','=',$cuenta_id)->first();
			$moneda							=	Moneda::where('id','=',$cuenta->moneda_id)->first();

			$iddetalle 						=	$this->funciones->getCreateIdMaestra('cajadetalle');
			
			$detallecaja					=	new CajaDetalle;
			$detallecaja->id				=	$iddetalle;
			$detallecaja->caja_id			=	$registro->id;
			$detallecaja->entidad_id		=	$entidad->id;
			$detallecaja->entidad_nombre 	=	$entidad->entidad;

			$detallecaja->cuenta_id 		=	$cuenta->id;
			$detallecaja->moneda_id 		=	$cuenta->moneda_id;
			$detallecaja->moneda_nombre		=	$moneda->descripcionabreviada;

			$detallecaja->nrocta			=	$cuenta->nrocta;
			// $detallecaja->indproduccion	=	$producto->indproduccion;
			$detallecaja->serie 			=	$registro->serie;
			$detallecaja->numero 			=	$registro->numero;
			$detallecaja->cliente_id 		=	$registro->cliente_id;
			$detallecaja->cliente_nombre 	=	$registro->cliente_nombre;
			
			$detallecaja->fecha 			=	$registro->fecha;			
			$detallecaja->importe 			=	$importe;
			$detallecaja->total 			=	$importe;
			
			$detallecaja->tipo_pago_id		=   $tipopago->id;
			$detallecaja->tipo_pago_nombre 	=   $tipopago->descripcion;

			$detallecaja->fecha_crea		=	$this->fechaactual;
			$detallecaja->usuario_crea		=	Session::get('usuario')->id;

			$detallecaja->estado_id 		=	$this->generado->id;
			$detallecaja->estado_descripcion=	$this->generado->descripcion;

			$detallecaja->save();

			$cabecera            	 		=	Caja::find($registro_id);
			$cabecera->acta 	   			=   $cabecera->acta+$importe;		
			$cabecera->saldo 	   			=   $cabecera->total - $cabecera->acta;		
			if($cabecera->saldo==0){
				$detallecaja->estado_id 		=	'1CIX00000045';
				$detallecaja->estado_descripcion=	'COBRADO';
			}
			elseif($cabecera->total==$cabecera->saldo){
				$detallecaja->estado_id 		=	$this->generado->id;
				$detallecaja->estado_descripcion=	$this->generado->descripcion;
			}	
			else{
				$detallecaja->estado_id 		=	'1CIX00000044';
				$detallecaja->estado_descripcion=	'PENDIENTE PAGO';
			}

			$cabecera->fecha_mod 	 		=   $this->fechaactual;
			$cabecera->usuario_mod 			=   Session::get('usuario')->id;
			$cabecera->save();			

		
			DB::commit();
		} catch (Exception $ex) {
			DB::rollback();
			return Redirect::to('/cobrar-caja-venta/'.$idopcion.'/'.$idregistro)->with('errorbd', 'Ocurrio un error Inesperado. '.$ex);
		}

		return Redirect::to('/cobrar-caja-venta/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Detalle registrado con exito');

	}

	public function actionAgregarDetallePagoCompra($idopcion,$idregistro,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		try {
			DB::beginTransaction();
			$registro_id 					=	$this->funciones->decodificarmaestra($idregistro);		
			$registro						=	Caja::where('id','=',$registro_id)->first();
			$importe				 		=	floatval(str_replace(",","",$request['importe']));
			$entidad_id						=	$request['entidad_id'];
			$cuenta_id						=	$request['cuenta_id'];
			$tipo_pago_id					=	'TPCA00000002';
			
			$tipopago						=	Categoria::where('id','=',$tipo_pago_id)->first();
			$entidad 						=	EntidadFinanciera::where('id','=',$entidad_id)->first();
			$cuenta							=	CuentasEmpresa::where('id','=',$cuenta_id)->first();
			$moneda							=	Moneda::where('id','=',$cuenta->moneda_id)->first();

			$iddetalle 						=	$this->funciones->getCreateIdMaestra('cajadetalle');
			
			$detallecaja					=	new CajaDetalle;
			$detallecaja->id				=	$iddetalle;
			$detallecaja->caja_id			=	$registro->id;
			$detallecaja->entidad_id		=	$entidad->id;
			$detallecaja->entidad_nombre 	=	$entidad->entidad;

			$detallecaja->cuenta_id 		=	$cuenta->id;
			$detallecaja->moneda_id 		=	$cuenta->moneda_id;
			$detallecaja->moneda_nombre		=	$moneda->descripcionabreviada;

			$detallecaja->nrocta			=	$cuenta->nrocta;
			// $detallecaja->indproduccion	=	$producto->indproduccion;
			$detallecaja->serie 			=	$registro->serie;
			$detallecaja->numero 			=	$registro->numero;
			$detallecaja->cliente_id 		=	$registro->cliente_id;
			$detallecaja->cliente_nombre 	=	$registro->cliente_nombre;
			
			$detallecaja->fecha 			=	$registro->fecha;			
			$detallecaja->importe 			=	$importe;
			$detallecaja->total 			=	$importe;
			
			$detallecaja->tipo_pago_id		=   $tipopago->id;
			$detallecaja->tipo_pago_nombre 	=   $tipopago->descripcion;

			$detallecaja->fecha_crea		=	$this->fechaactual;
			$detallecaja->usuario_crea		=	Session::get('usuario')->id;

			$detallecaja->estado_id 		=	$this->generado->id;
			$detallecaja->estado_descripcion=	$this->generado->descripcion;

			$detallecaja->save();

			$cabecera            	 		=	Caja::find($registro_id);
			$cabecera->acta 	   			=   $cabecera->acta+$importe;		
			$cabecera->saldo 	   			=   $cabecera->total - $cabecera->acta;		
			if($cabecera->saldo==0){
				$detallecaja->estado_id 		=	'1CIX00000045';
				$detallecaja->estado_descripcion=	'COBRADO';
			}
			elseif($cabecera->total==$cabecera->saldo){
				$detallecaja->estado_id 		=	$this->generado->id;
				$detallecaja->estado_descripcion=	$this->generado->descripcion;
			}	
			else{
				$detallecaja->estado_id 		=	'1CIX00000044';
				$detallecaja->estado_descripcion=	'PENDIENTE PAGO';
			}

			$cabecera->fecha_mod 	 		=   $this->fechaactual;
			$cabecera->usuario_mod 			=   Session::get('usuario')->id;
			$cabecera->save();			

		
			DB::commit();
		} catch (Exception $ex) {
			DB::rollback();
			return Redirect::to('/pagar-caja-compra/'.$idopcion.'/'.$idregistro)->with('errorbd', 'Ocurrio un error Inesperado. '.$ex);
		}

		return Redirect::to('/pagar-caja-compra/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Detalle registrado con exito');

	}


		public function actionQuitarDetallePagoCompra($idopcion,$iddetalleregistro,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$detalle_id = $this->funciones->decodificarmaestra($iddetalleregistro);
		$activo							=	0;
		$detalleregistro				=	CajaDetalle::find($detalle_id);
		$detalleregistro->activo 		=	$activo;
		$detalleregistro->fecha_mod 	=	$this->fechaactual;
		$detalleregistro->usuario_mod 	=	Session::get('usuario')->id;

		$cabecera						=	Caja::find($detalleregistro->caja_id);
		$cabecera->acta					=	$cabecera->acta-$detalleregistro->total;
		$cabecera->saldo				=	$cabecera->total-$cabecera->acta;
		$cabecera->fecha_mod			=	$this->fechaactual;
		$cabecera->usuario_mod			=	Session::get('usuario')->id;
		
		$detalleregistro->save();
		$cabecera->save();					
		
		$idregistro						= 	Hashids::encode(substr($cabecera->id, -8));

		return Redirect::to('/pagar-caja-compra/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Detalle Registro quitado con exito');
		
	}

	public function actionQuitarDetalleCobroVentas($idopcion,$iddetalleregistro,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$detalle_id = $this->funciones->decodificarmaestra($iddetalleregistro);
		$activo							=	0;
		$detalleregistro				=	CajaDetalle::find($detalle_id);
		$detalleregistro->activo 		=	$activo;
		$detalleregistro->fecha_mod 	=	$this->fechaactual;
		$detalleregistro->usuario_mod 	=	Session::get('usuario')->id;

		$cabecera						=	Caja::find($detalleregistro->caja_id);
		$cabecera->acta					=	$cabecera->acta-$detalleregistro->total;
		$cabecera->saldo				=	$cabecera->total-$cabecera->acta;
		$cabecera->fecha_mod			=	$this->fechaactual;
		$cabecera->usuario_mod			=	Session::get('usuario')->id;
		
		$detalleregistro->save();
		$cabecera->save();					
		
		$idregistro						= 	Hashids::encode(substr($cabecera->id, -8));

		return Redirect::to('/cobrar-caja-venta/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Detalle Registro quitado con exito');
		
	}

}
