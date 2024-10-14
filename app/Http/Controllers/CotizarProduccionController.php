<?php

namespace App\Http\Controllers;

use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Cliente;
use App\Modelos\Categoria;
use App\Modelos\Precotizacion;
use App\Modelos\Archivo;

use App\Modelos\Cotizacion;
use App\Modelos\DetalleCotizacion;
use App\Modelos\DetalleCotizacionAnalisis;

use App\Modelos\Planeamiento;
use App\Modelos\DetallePlaneamiento;
use App\Modelos\DetallePlaneamientoAnalisis;

use App\Modelos\Requerimiento;
use App\Modelos\LogAprobacion;
use App\Modelos\LogEmision;
use App\Modelos\Empresa;
use App\Modelos\CuentasEmpresa;
// use App\Modelos\Modelo as ModeloCotizacion;
use App\Modelos\Margenes;

use App\Modelos\CategoriaServicio;
use App\Modelos\LogExtornar;
use App\Modelos\Producto;
use App\Modelos\Produccion;
use App\Modelos\DetalleProduccion;
use App\Modelos\DetalleProduccionDet;
use App\Modelos\Kardex;
use App\Modelos\Almacen;
use App\Modelos\Proveedor;

use App\Modelos\DetalleAlmacen;
use App\Modelos\DetalleCompra;



use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\CotizacionTraits;
use App\Traits\ConfiguracionTraits;

use PDF;
use ZipArchive;
use Hashids;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use TPDF;
use Storage;
use SplFileInfo;


class CotizarProduccionController extends Controller {
	
	use GeneralesTraits;
	use CotizacionTraits;
	use ConfiguracionTraits;
	private   $tipoarchivo      = 'requerimiento';

	public function actionListarCotizacionProduccion($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Listar Cotizacion');
		// $estado      =   Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('DESCRIPCION','EVALUADO')->first();
		$idestados          =   Categoria::where('tipo_categoria','ESTADO_GENERAL')->whereIn('DESCRIPCION',['GENERADO','EMITIDO'])->pluck('id')->toArray();
		
		$idgenerado     = $this->getIdEstado('GENERADO');
		$idemitido      = $this->getIdEstado('EMITIDO');
		$idaprobado     = $this->getIdEstado('APROBADO');
		// $idextornado    = $this->getIdEstado('GENERADO');

		$colores    =   [
							$idgenerado  =>  ['color'=>'badge-light','nivel'=>0],
							$idemitido   =>  ['color'=>'badge-primary','nivel'=>1],
							$idaprobado  =>  ['color'=>'badge-success','nivel'=>2],
						];
		
		$listacotizaciones  =   Produccion::where('activo',1)
									->whereIn('estado_id',$idestados)
									->select('*')
									->selectRaw(" '' as classcolor, 0 as nivel")
									->get();
		foreach ($listacotizaciones as $index => $cotizacion) {
			$cotizacion->classcolor = $colores[$cotizacion->estado_id]['color'];
			$cotizacion->nivel = $colores[$cotizacion->estado_id]['nivel'];
		}
		// dd($listacotizaciones);
		$funcion            =   $this;
		
		$idgenerado         =   $this->getIdEstado('GENERADO');
		$idemitido          =   $this->getIdEstado('EMITIDO');
		
		$finicio 			=	$this->inicio;
		$ffin 				=	$this->fin;

		return View::make('cotizacion/listacotizaciones',
						 [
							'listacotizaciones'     =>  $listacotizaciones,
							'funcion'               =>  $funcion,
							'idopcion'              =>  $idopcion,                           
							'idgenerado'            =>  $idgenerado,
							'idemitido'             =>  $idemitido,
							'idaprobado'            =>  $idaprobado,
							'inicio' 				=> $finicio,
							'fin' 					=> $ffin,
						 ]);
	}

	public function actionConfigurarDetalle(Request $request)
	{

		$cotizacion_id              =   $request['cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
		$iddatocategoria            =   $request['idcategoria'];
		$nivel                      =   1;
		$codigo                     =   '01';

		$idpadre                    =   '';
		$ldescripcion               =   [];

		if(isset($iddatocategoria) && !is_null($iddatocategoria)) 
		{
			// $registrocategoria_id = $this->funciones->decodificarmaestra($iddatocategoria);
			$registrocategoria_id = $iddatocategoria;
			$registro   =   DetalleCotizacion::where('id','=',$registrocategoria_id)->first();
			$nivel      =   (int)($registro->nivel)+1;

			// $numero  =   ((int)DetalleCotizacion::where('idpadre','=',$registrocategoria_id)->where('nivel','=',$nivel)->where('activo','=',1)->max('codigo')) + 1;
			$numero     =   ((int) substr(DetalleCotizacion::where('idpadre','=',$registrocategoria_id)->where('nivel','=',$nivel)->where('activo','=',1)->max('codigo'),-2)) + 1;

			// dd(DetalleCotizacion::where('idpadre','=',$registrocategoria_id)->where('nivel','=',$nivel)->where('activo','=',1)->max('codigo'));
			$codigosig  =   str_pad($numero, 2, "0", STR_PAD_LEFT);
			// dd($numero);
			$codigo     =   $registro->codigo.'.'.$codigosig;
			$idpadre    =   $registro->id;
			$nivel      =   (int)$registro->nivel+1;
		}
		else{
			$numero     =   ((int)DetalleCotizacion::where('cotizacion_id','=',$cotizacion_id)->where('activo','=',1)->max('codigo')) + 1;
			$codigo     =   str_pad($numero, 2, "0", STR_PAD_LEFT);
		}

		$ldescripcion   =   DetalleCotizacion::where('activo','=',1)->pluck('descripcion')->toArray();

		$cotizacion                         =   Cotizacion::where('id', $cotizacion_id)->first();
		$combo_unidad_medida                =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
		$select_unidad_medida               =   '';
		$combo_categoria_servicio           =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
		// $combo_tipocategoria                =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
		$combo_tipocategoria                =   $this->gn_combo_tipocategoria();
			// $combo_tipocategoria              =   $this->gn_combo_categoria('TIPO_COMPRA','Seleccione Tipo','');

		$select_categoria_servicio          =   '';
		$cotizaciondetalle_id               =   '';
		
		// dd($grupomodelo1);// $grupomodelo1                       =   ['id'=>'ssss'];

		return View::make('cotizacion/modal/ajax/mconfiguracioncotizacion',
						 [          
							'idopcion'                  =>  $idopcion,
							'cotizacion'                =>  $cotizacion,
							'combo_unidad_medida'       =>  $combo_unidad_medida,
							'select_unidad_medida'      =>  $select_unidad_medida,
							'combo_categoria_servicio'  =>  $combo_categoria_servicio,
							'select_categoria_servicio' =>  $select_categoria_servicio,
							'cotizaciondetalle_id'      =>  $cotizaciondetalle_id,
							'ajax'                      =>  true,       
							'combo_tipocategoria'       =>  $combo_tipocategoria,   
							'nivel'                     =>  $nivel,
							'codigo'                    =>  $codigo,     
							'idpadre'                   =>  $idpadre,   
							'iddatocategoria'           =>  $iddatocategoria,
							'ldescripcion'              =>  $ldescripcion,
							'swmodificar'               =>  false,
						 ]);
	}

	public function actionCotizarcotizacion($idopcion,$idcotizacion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$sidcotizacion = $idcotizacion;
		$idcotizacion = $this->funciones->decodificarmaestra($idcotizacion);
		View::share('titulo','Venta Cotizar');

		if($_POST)
		{
			$gruposervicio_id                               =   $request['gruposervicio_id'];
			$unidadmedida_id                                =   $request['unidadmedida_id'];
			$servicio                                       =   $request['servicio'];
			$cotizaciondetalle_id                           =   $request['cotizaciondetalle_id'];
			$cantidad                                       =   $request['cantidad'];

			$ispadre                                        =   $request['tipocategoria'];
			$idpadre                                        =   $request['iddatocategoria'];
			$codigo                                         =   $request['codigo'];
		
			$cotizacion                                     =   Cotizacion::where('id', $idcotizacion)->first();
			$gruposervicio                                  =   Categoria::where('id', $gruposervicio_id)->first();

			//agregar cuenta contable
			if(trim($cotizaciondetalle_id)==''){
				
				$validarcodigog = DetalleCotizacion::where('cotizacion_id','=',$idcotizacion)->where('activo','=',1)->where('codigo','=',$codigo)->first();
				if(!empty($validarcodigog) && count($validarcodigog)>0){
					return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.$sidcotizacion)->with('errorbd', 'Ya existe un Servicio con Codigo :'.$codigo);
				}


				$iddetallecotizacion                        =   $this->funciones->getCreateIdMaestra('detallecotizaciones');
				$cabecera                                   =   new DetalleCotizacion;
				$cabecera->id                               =   $iddetallecotizacion;
				$cabecera->cotizacion_id                    =   $idcotizacion;
				$cabecera->descripcion                      =   $servicio;

				$cabecera->ispadre                          =   $ispadre;
				$cabecera->idpadre                          =   $idpadre;
				$nivel = 1;

				if(!is_null($idpadre)){
					$nivel  = (int) Detallecotizacion::where('id','=',$idpadre)->first()->nivel+1;
				}
				$cabecera->nivel=$nivel;
				if($ispadre==0){
					
					$unidadmedida                                   =   Categoria::where('id', $unidadmedida_id)->first();
					$cabecera->unidadmedida_id                  =   $unidadmedida->id;
					$cabecera->unidadmedida_nombre              =   $unidadmedida->descripcion;
					$cabecera->cantidad                         =   $cantidad;
					$cabecera->precio_unitario                  =   0;
					$cabecera->total                            =   0;
					$cabecera->mgadministrativos                =   $this->mgadmin;
					$cabecera->mgutilidad                       =   $this->mgutil;
					$cabecera->swigv                            =   1;
					$cabecera->migv                             =   $this->igv;
					$cabecera->igv                              =   0;

					$cabecera->total_analisis                   =   0;
					$cabecera->impuestoanalisis_01              =   0;
					$cabecera->impuestoanalisis_02              =   0;
					$cabecera->totalpreciounitario              =   0;
				}
				$cabecera->codigo                           =   $codigo;
				$cabecera->fecha_crea                       =   $this->fechaactual;
				$cabecera->usuario_crea                     =   Session::get('usuario')->id;
				$cabecera->save();
			}
			else{
				//modificar cuenta contable
				$validarcodigog = DetalleCotizacion::where('cotizacion_id','=',$idcotizacion)->where('activo','=',1)->where('codigo','=',$codigo)->where('id','<>',$cotizaciondetalle_id)->first();
				if(!empty($validarcodigog) && count($validarcodigog)>0){
					return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.$sidcotizacion)->with('errorbd', 'Ya existe un Servicio con Codigo : ['.$codigo.'] ');
				}

				$detallecotizacion                          =   DetalleCotizacion::where('id', $cotizaciondetalle_id)->first();
				$detallecotizacion->descripcion             =   $servicio;
				
				$detallecotizacion->codigo                  =   $codigo;
				$detallecotizacion->precio_unitario         =   0;
				$detallecotizacion->total                   =   0;
				$detallecotizacion->fecha_mod               =   $this->fechaactual;
				$detallecotizacion->usuario_mod             =   Session::get('usuario')->id;
				$detallecotizacion->save();
			}
			return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.$sidcotizacion)->with('bienhecho', 'Servicio '.$servicio.' agregada con éxito');

		}else{

			$cotizacion                 = Produccion::where('id', $idcotizacion)->first();
			// dd($cotizacion);
			$precotizacion              =   Produccion::where('lote', $cotizacion->lote)->first();
			$listadetalle               =   DetalleProduccion::where('activo','=',1)->where('produccion_id',$idcotizacion)->orderby('codigo','asc')->get();
			// $listadetalle                       =   DetalleCotizacionAnalisis::where('activo','=',1)->where('cotizacion_id',$idcotizacion)->orderby('id','asc')->get();
			// $combo_categoria_analisis   =   $this->con_generacion_combo('CAT_PRODUCTO','Seleccione Categoria Producto','');
			$valoresmenos 				= 	['CATP00000007'];
			$combo_categoria_analisis   =   $this->con_generacion_combo_menos('CAT_PRODUCTO','Seleccione Categoria Producto','',$valoresmenos);
			$select_categoria_analisis  =   '';
			$combo_unidad_medida_a      =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
			$select_unidad_medida_a     =   '';
			$listaproducto              = [''=>'SELECCIONE PRODUCTO'];//  [''=>'SELECCIONE PRODUCTO']+ Producto::where('activo','=',1)->pluck('descripcion','id')->toArray();
			$combo_subcategoria			=	[];
			$select_subcategoria		=	[];

			return View::make('cotizacion/ventacotizar', 
							[
								'precotizacion'             => [],
								'cotizacion'                => $cotizacion,
								'cliente'                   => [],
								'listadetalle'              => $listadetalle,
								'idopcion'                  => $idopcion,
								'unidad'                    =>  $this->unidadmb,

								'registro'                  =>  $precotizacion,
								'url'                       =>  'cotizacion',
								'view'                      =>  'requerimiento',
								
								'combo_categoria_analisis'  => $combo_categoria_analisis,
								'select_categoria_analisis' => $select_categoria_analisis,

								'combo_subcategoria'		=>	$combo_subcategoria,
								'select_subcategoria'		=>	$select_subcategoria,

								'combo_unidad_medida_a'     => $combo_unidad_medida_a,
								'select_unidad_medida_a'    => $select_unidad_medida_a,
								'listaproducto'				=> $listaproducto,
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
		$detallecompra->preciounitario 	   		=   $preciounitario;
		
		$detallecompra->indigv 	   				=   $indigv;
		$detallecompra->igv 	   				=   $igv;
		$detallecompra->porcigv 	   			=   $porcigv;
		$detallecompra->subtotal 				=	$detallesubtotal;

		$detallecompra->total 	   				=   $detalletotal;						
		$detallecompra->fecha_crea 	 			=   $this->fechaactual;
		$detallecompra->usuario_crea 			=   Session::get('usuario')->id;
		$detallecompra->save();

		$cabecera            	 				=	Compra::find($idcompra);
		$cabecera->montototal 	   				=   $cabecera->montototal+$detalletotal;		
		$cabecera->fecha_mod 	 				=   $this->fechaactual;
		$cabecera->usuario_mod 					=   Session::get('usuario')->id;
		$cabecera->save();			

		$idcompraen								= 	Hashids::encode(substr($idcompra, -8));
		 	return Redirect::to('/modificar-compras/'.$idopcion.'/'.$idcompraen)->with('bienhecho', 'Detalle compra '.$producto->descripcion.' registrado con exito');
		
	}
	
	public function actionAjaxModalModificarConfiguracionCotizacion(Request $request)
	{
		$cotizacion_id              =   $request['cotizacion_id'];
		$detalle_cotizacion_id      =   $request['detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
	
		$iddatocategoria            =   $request['idcategoria'];

		// $iddatocategoria             =   $request['idcategoria'];
		$idpadre                    =   $request['iddatocategoria'];

		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$detalle                    =   DetalleCotizacion::where('id','=',$detalle_cotizacion_id)->first();

		$combo_unidad_medida        =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
		$select_unidad_medida       =   $detalle->unidadmedida_id;
		$combo_categoria_servicio   =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
		$select_categoria_servicio  =   $detalle->categoriaservicio_id;
		$cotizaciondetalle_id       =   $detalle_cotizacion_id;
		$codigo                     =   $detalle->codigo;
		$ccategoriaserv             =   $this->gn_combo_tipocategoria();
		$combo_tipocategoria        =   [$detalle->ispadre =>  $ccategoriaserv[$detalle->ispadre]];

		return View::make('cotizacion/modal/ajax/mconfiguracioncotizacion',
						 [          
							'idopcion'                  => $idopcion,
							'cotizacion'                => $cotizacion,
							'detalle'                   => $detalle,
							'combo_unidad_medida'       => $combo_unidad_medida,
							'select_unidad_medida'      => $select_unidad_medida,
							'combo_categoria_servicio'  => $combo_categoria_servicio,
							'select_categoria_servicio' => $select_categoria_servicio,
							'cotizaciondetalle_id'      => $cotizaciondetalle_id,
							'ajax'                      => true,                            
							'idpadre'                   =>  $idpadre,
							'iddatocategoria'           =>  $iddatocategoria,
							'swmodificar'               =>  true,
							'combo_tipocategoria'       =>  $combo_tipocategoria,
							'codigo'                    =>  $codigo,
						 ]);
	}

	public function actionAnalizarDetalleCotizacion(Request $request)
	{

		$cotizacion_id              =   $request['cotizacion_id'];
		$detalle_cotizacion_id      =   $request['detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];

		$detallecotizacion          =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();
		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$combo_categoria_analisis   =   $this->con_generacion_combo('CATEGORIA_ANALISIS','Seleccione Categoria Analisis','');
		$select_categoria_analisis  =   '';
		$combo_unidad_medida_a      =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
		$select_unidad_medida_a     =   '';

		$listadetalle               =   DetalleCotizacionAnalisis::where('activo','=',1)
										->where('detallecotizacion_id','=',$detallecotizacion->id)
										->orderby('categoriaanalisis_id','asc')->get();
		$listaproducto               =   [''=>'SELECCIONE PRODUCTO']+ Producto::where('activo','=',1)->pluck('descripcion','id')->toArray();

		$funcion                    =   $this;
		return View::make('cotizacion/form/fanalizar',
						 [
							'detallecotizacion'         => $detallecotizacion,
							'cotizacion'                => $cotizacion,
							'combo_categoria_analisis'  => $combo_categoria_analisis,
							'select_categoria_analisis' => $select_categoria_analisis,
							'combo_unidad_medida_a'     => $combo_unidad_medida_a,
							'select_unidad_medida_a'    => $select_unidad_medida_a,
							'funcion'                   => $funcion,
							'idopcion'                  => $idopcion,
							'listadetalle'              => $listadetalle,
							'listaproducto'              => $listaproducto,
							'ajax'                      => true,                            
						 ]);
	}

	public function actionAgregarProductoAnalisis(Request $request)
	{

		$categoria_id                           	=   $request['categoria_id'];
		$subcategoria_id                           	=   $request['subcategoria_id'];
		//$unidadmedidaa_id                           =   $request['unidadmedidaa_id'];
		$descripcion                                =   $request['descripcion'];
		$data_producto_id                           =   $request['producto_id'];//nuevo
		$cantidad                                   =   $request['cantidad'];
		$precio                                     =   $request['precio'];
		$data_cotizacion_id                         =   $request['data_cotizacion_id'];
		// $data_detalle_cotizacion_id                 =   $request['data_detalle_cotizacion_id'];

		// $detallecotizacion                          =   DetalleProduccion::where('id', $data_detalle_cotizacion_id)->first();
		// $detallecotizacion->swactualizado           =   0;
		// $detallecotizacion->save();

		$cotizacion                                 =   Produccion::where('id', $data_cotizacion_id)->first();
		// dd($cotizacion);
		$categoria                              	=   Categoria::where('id', $categoria_id)->first();
		$subcategoria                              	=   Categoria::where('id', $subcategoria_id)->first();
		$producto                                 	=   Producto::where('id', $data_producto_id)->first();
		// $unidadmedida                               =   Categoria::where('id', $producto->unidad_medida_id)->first();

		$idopcion                                   =   $request['idopcion'];
		if($categoria_id=='CATP00000003'){
			$unidadmedida = Categoria::wheredescripcion('UNIDAD')->first();
			// dd($unidadmedida);
		}
		else
		{
			$unidadmedida                               =   Categoria::where('id', $producto->unidad_medida_id)->first();
		}

		$iddetallecotizacionanalisis                =   $this->funciones->getCreateIdMaestra('detalleproduccions');
		$cabecera                                   =   new DetalleProduccion;
		$cabecera->id                               =   $iddetallecotizacionanalisis;
		$cabecera->produccion_id                    =   $data_cotizacion_id;
		$cabecera->producto_id                      =   $data_producto_id;
		$cabecera->producto_nombre                  =   $producto->descripcion;
		$cabecera->codigo                      		=   $cotizacion->codigo;
		// $cabecera->codigo                      		=   $cotizacion->codigo;
		// $cabecera->detallecotizacion_id             =   $data_detalle_cotizacion_id;
		// $cabecera->descripcion                      =   $descripcion;
		$cabecera->categoria_id             		=   $categoria->id;
		$cabecera->categoria_nombre         		=   $categoria->descripcion;

		$cabecera->subcategoria_id             		=   $subcategoria->id;
		$cabecera->subcategoria_nombre         		=   $subcategoria->descripcion;


		$cabecera->unidadmedida_id                  =   $unidadmedida->id;
		$cabecera->unidadmedida_nombre              =   $unidadmedida->descripcion;
		$cabecera->cantidad                         =   floatval($cantidad);
		$cabecera->precio_unitario                  =   floatval($precio);
		$cabecera->total                            =   floatval($cantidad)*floatval($precio);
		$cabecera->fecha_crea                       =   $this->fechaactual;
		$cabecera->usuario_crea                     =   Session::get('usuario')->id;
		$cabecera->save();



		//generar el precio y totales   
		$this->cot_generar_totales_produccion($cotizacion);


		$funcion                                    =   $this;

		$listadetalle                               =   DetalleProduccion::where('activo','=',1)
														->where('produccion_id','=',$cotizacion->id)
														->orderby('categoria_id','asc')
														->orderby('subcategoria_id','asc')
														->get();


		return View::make('cotizacion/ajax/alistadetalleanalizar',
						 [
							// 'detallecotizacion'         => $detallecotizacion,
							'cotizacion'                => $cotizacion,
							'funcion'                   => $funcion,
							'idopcion'                  => $idopcion,
							'listadetalle'              => $listadetalle,
							'ajax'                      => true,                            
						 ]);
	}

	public function actionActulizarTablaCotizacion(Request $request)
	{
		$cotizacion_id              =   $request['data_cotizacion_id'];
		dd($cotizacion_id);
		$detalle_cotizacion_id      =   $request['data_detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$listadetalle               =   DetalleProduccion::where('activo','=',1)->where('produccion_id','=',$cotizacion_id)
										->orderby('codigo','asc')->get();
		$funcion                    =   $this;
		return View::make('cotizacion/ajax/alistadetallecotizacion',
						 [
							'cotizacion'                => $cotizacion,
							'listadetalle'              => $listadetalle,
							'idopcion'                  => $idopcion,
							'ajax'                      => true,                            
						 ]);
	}


	public function actionAjaxSubCategoriasProductoProduccion(Request $request)
	{
		$categoria_id			=	$request['categoria_id'];
		$select_subcategoria  	=	'';
		$combo_subcategoria		=	[''=>'SELECCIONE SUB CATEGORIA']  + Categoria::where('tipo_categoria','=','SUBCAT_PRODUCTO')->where('activo','=',1)->where('aux01','=',$categoria_id)->pluck('descripcion','id')->toArray();
		
		return View::make('general/ajax/asubcategoriaproducto',
						[
							'select_subcategoria'  		=> $select_subcategoria,
							'combo_subcategoria'   		=> $combo_subcategoria,	
						  	'ajax'						=>	true,
						]);
	}

	public function actionAjaxProductoProduccion(Request $request)
	{
		$categoria_id			=	$request['categoria_id'];
		$subcategoria_id		=	$request['subcategoria_id'];
		
		$listaproducto		=	[''=>'SELECCIONE PRODUCTO'] + Producto::where('categoria_id','=',$categoria_id)->where('subcategoria_id','=',$subcategoria_id)->where('activo','=',1)->pluck('descripcion','id')->toArray();
		
		return View::make('general/ajax/aproductoproduccion',
						[
							'listaproducto'  		=> $listaproducto,
							'ajax'						=>	true,
						]);
	}

	public function actionEliminarTablaCotizacionProduccion(Request $request)
	{
		$cotizacion_id            =   $request['cotizacion_id'];
		$detalle_cotizacion_id    =   $request['detalle_cotizacion_id'];
		// $detalle_cotizacion_analisis_id     =   $request['detalle_cotizacion_analisis_id'];
		$idopcion                 =   $request['idopcion'];
		$cotizacion               =   Produccion::where('id', $cotizacion_id)->first();
		
		$detalle                  =   DetalleProduccion::where('id', $detalle_cotizacion_id)->first();
		$detalle->activo          =   0;
		$detalle->fecha_mod       =   $this->fechaactual;
		$detalle->usuario_mod     =   Session::get('usuario')->id;
		$detalle->save();
		
		$funcion                  =   $this;

		//generar el precio y totales   
		// $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
		$this->cot_generar_totales_produccion($cotizacion);
		
		$listadetalle                       =   DetalleProduccion::where('activo','=',1)
												->where('produccion_id','=',$cotizacion_id)
												->orderby('categoria_id','asc')
												->orderby('subcategoria_id','asc')
												->get();

		$funcion                    =   $this;
		return View::make('cotizacion/ajax/alistadetalleanalizar',
						 [
							'cotizacion'         => $cotizacion,
							'listadetalle'              => $listadetalle,
							'idopcion'                  => $idopcion,
							'ajax'                      => true,                            
						 ]);
	}


	public function actionAjaxEliminarLineaCotizacion(Request $request)
	{
		$cotizacion_id              =   $request['cotizacion_id'];
		$detalle_cotizacion_id      =   $request['detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
		$detalle                    =   DetalleCotizacion::where('id','=',$detalle_cotizacion_id)->first();
		$detalle->activo            =   0;
		$detalle->fecha_mod         =   $this->fechaactual;
		$detalle->usuario_mod       =   Session::get('usuario')->id;
		$detalle->save();
	}

	public function EliminarServiciosDetalle($idservicio)
	{
		$registro   = DetalleCotizacion::find($idservicio);
		$hijos      = DetalleCotizacion::where('idpadre','=',$idservicio)->where('activo','=',1)->get();
		foreach($hijos as $index=> $hijo){
			$this->EliminarServiciosDetalle($hijo->id);
		}
		$registro->activo           =   0;
		$registro->swactualizado    =   0;
		$registro->fecha_mod        =   $this->fechaactual;
		$registro->usuario_mod      =   Session::get('usuario')->id;
		$registro->save();
	}

	public function actionAjaxEliminarIgvDetalleCotizacion(Request $request)
	{
		$cotizacion_id              =   $request['data_cotizacion_id'];
		$detalle_cotizacion_id      =   $request['data_detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
		$swigv                      =   $request['swigv'];
		
		$detalle                    =   DetalleCotizacion::where('id','=',$detalle_cotizacion_id)->first();
		$detalle->swigv             =   $swigv;
		$detalle->swactualizado     =   0;
		// $detalle->igv                =   0;
		$detalle->fecha_mod         =   $this->fechaactual;
		$detalle->usuario_mod       =   Session::get('usuario')->id;
		$detalle->save();

		$funcion                            =   $this;

		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$detallecotizacion          =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();
		//generar el precio y totales   
		$this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
		$listadetalle                       =   DetalleCotizacionAnalisis::where('activo','=',1)
												->where('detallecotizacion_id','=',$detallecotizacion->id)
												->orderby('categoriaanalisis_id','asc')->get();

		$funcion                    =   $this;
		return View::make('cotizacion/ajax/alistadetalleanalizar',
						 [
							'detallecotizacion'         => $detallecotizacion,
							'listadetalle'              => $listadetalle,
							'idopcion'                  => $idopcion,
							'ajax'                      => true,                            
						 ]);

	}

	public function actionAjaxActualizarMGAdministrativoDetalleCotizacion(Request $request)
	{
		$cotizacion_id              =   $request['data_cotizacion_id'];
		$detalle_cotizacion_id      =   $request['data_detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
		$mgadmin                        =   $request['mgadmin'];
		
		$detalle                    =   DetalleCotizacion::where('id','=',$detalle_cotizacion_id)->first();
		$detalle->mgadministrativos =   $mgadmin;
		$detalle->swactualizado     =   0;
		$detalle->fecha_mod         =   $this->fechaactual;
		$detalle->usuario_mod       =   Session::get('usuario')->id;
		$detalle->save();

		$funcion                            =   $this;

		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$detallecotizacion          =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();
		//generar el precio y totales   
		$this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
		$listadetalle                       =   DetalleCotizacionAnalisis::where('activo','=',1)
												->where('detallecotizacion_id','=',$detallecotizacion->id)
												->orderby('categoriaanalisis_id','asc')->get();

		$funcion                    =   $this;
		return View::make('cotizacion/ajax/alistadetalleanalizar',
						 [
							'detallecotizacion'         => $detallecotizacion,
							'listadetalle'              => $listadetalle,
							'idopcion'                  => $idopcion,
							'ajax'                      => true,                            
						 ]);

	}
	
	public function actionAjaxActualizarMGUtilidadDetalleCotizacion(Request $request)
	{
		$cotizacion_id              =   $request['data_cotizacion_id'];
		$detalle_cotizacion_id      =   $request['data_detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
		$mgutil                         =   $request['mgutil'];
		
		$detalle                    =   DetalleCotizacion::where('id','=',$detalle_cotizacion_id)->first();
		$detalle->mgutilidad        =   $mgutil;
		$detalle->swactualizado     =   0;
		$detalle->fecha_mod         =   $this->fechaactual;
		$detalle->usuario_mod       =   Session::get('usuario')->id;
		$detalle->save();
		$funcion                            =   $this;

		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$detallecotizacion          =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();
		//generar el precio y totales   
		$this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
		$listadetalle                       =   DetalleCotizacionAnalisis::where('activo','=',1)
												->where('detallecotizacion_id','=',$detallecotizacion->id)
												->orderby('categoriaanalisis_id','asc')->get();

		$funcion                    =   $this;
		return View::make('cotizacion/ajax/alistadetalleanalizar',
						 [
							'detallecotizacion'         => $detallecotizacion,
							'listadetalle'              => $listadetalle,
							'idopcion'                  => $idopcion,
							'ajax'                      => true,                            
						 ]);

	}

	public function actionAjaxActualizarPrecioVentaDetalleCotizacion(Request $request)
	{
		$cotizacion_id                  =   $request['data_cotizacion_id'];
		$detalle_cotizacion_id          =   $request['data_detalle_cotizacion_id'];
		$idopcion                       =   $request['idopcion'];
		$totalpv                        =   $request['totalpv'];
		
		$detalle                        =   DetalleCotizacion::where('id','=',$detalle_cotizacion_id)->first();
		// $detalle->totalpreciounitario   =   $totalpv;
		$detalle->totalpreciounitario     =   $totalpv;
		$detalle->swactualizado         =   1;
		$detalle->fecha_mod             =   $this->fechaactual;
		$detalle->usuario_mod           =   Session::get('usuario')->id;
		$detalle->save();
		$funcion                            =   $this;

		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$detallecotizacion          =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();
		//generar el precio y totales   
		$this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
		$listadetalle                       =   DetalleCotizacionAnalisis::where('activo','=',1)
												->where('detallecotizacion_id','=',$detallecotizacion->id)
												->orderby('categoriaanalisis_id','asc')->get();

		$funcion                    =   $this;
		return View::make('cotizacion/ajax/alistadetalleanalizar',
						 [
							'detallecotizacion'         => $detallecotizacion,
							'listadetalle'              => $listadetalle,
							'idopcion'                  => $idopcion,
							'ajax'                      => true,                            
						 ]);

	}
	
	public function actionAjaxEliminarServicioLineaCotizacion(Request $request)
	{
		$cotizacion_id              =   $request['cotizacion_id'];
		$detalle_cotizacion_id      =   $request['detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];
		$this->EliminarServiciosDetalle($detalle_cotizacion_id);
		$cotizacion         =   Cotizacion::find($cotizacion_id);
		$this->cot_generar_totales_cotizacion($cotizacion);
	}


	public function actionExtornarCotizacion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$sregistro_id = $idregistro;
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		$titulo     =   'Extornar  Cotizacion';
		$subtitulo     =   'Extorno de una Cotizacion';
		View::share('titulo','Extornar  Cotizacion');
		$url    =   'extornar-cotizacion';

		if($_POST)
		{
			
			$cotizacion = Cotizacion::where('id','=',$registro_id)->first();
			$idgenerado = $this->getIdEstado('EVALUADO');
			$generado   = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EVALUADO')->first();
			$extornado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','GENERADO')->first();
			// $evaluado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EVALUADO')->first();
			$idextornado = $this->getIdEstado('GENERADO');
			// $idevaluado  = $this->getIdEstado('EVALUADO');

			if($cotizacion->estado_id!==$generado->id){
				return Redirect::to('/'.$url.'/'.$idopcion.'/'.$idregistro)->with('errorbd','La Cotizacion debe estar en estado '.$generado->descripcion.' para poder EXTORNARLA');
			}

			$descripcion                = $request['descripcion'];
			// $cotizacion->activo         =  1;
			$cotizacion->estado_id      =  $idextornado;
			$cotizacion->estado_descripcion      =  $extornado->descripcion;

			// $cotizacion->fecha_extornarevaluacion        =  $this->fechaactual;;
			// $cotizacion->descripcion_extornarevaluacion  =  $descripcion;
			// $cotizacion->usuario_extornarevaluacion      =  Session::get('usuario')->id;
			$cotizacion->save();

			LogExtornar::where('tabla','cotizaciones')
						->where('accion','cotizacion-evaluada')
						->where('activo',1)
						->where('idtabla',$registro_id)
						->update(
							[
								'activo'        =>  0,
								'usuario_mod'   =>  Session::get('usuario')->id,
								'fecha_mod'     =>  $this->fechaactual
							]
						);
			
			$cabecera                       =   new LogExtornar;
			$cabecera->idtabla              =   $registro_id;
			$cabecera->accion              =   'cotizacion-evaluada';
			$cabecera->tabla                =   'cotizaciones';
			$cabecera->descripcion          =   $descripcion;
			$cabecera->fecha_crea           =   $this->fechaactual;
			$cabecera->usuario_crea         =   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-de-cotizacion/'.$idopcion)->with('bienhecho', 'Cotizacion Lote: '.$cotizacion->lote.' EXTORNADA con EXITO');
		
		}
		else{
			$cotizacion             =   Cotizacion::where('id', $registro_id)->first();
			return View::make('cotizacion/extornar', 
							[
								'cotizacion'    =>  $cotizacion,
								'idopcion'      =>  $idopcion,
								'idregistro'    =>  $idregistro,
								'titulo'        =>  $titulo,
								'subtitulo'     =>  $subtitulo,
								'url'           =>  $url,
							]);
		}
	}

	public function actionAjaxModalEmitirProduccion(Request $request)
	{
		$idproduccion					=	$request['idproduccion'];
		$produccion_id					=	$this->funciones->decodificarmaestra($idproduccion);

		$idopcion						=	$request['idopcion'];
		$cabecera						=	Produccion::where('id','=',$produccion_id)->first();
		// $detalles						=	DetalleProduccion::where('produccion_id','=',$produccion_id)->where('activo','=',1)->get();
		$emitido						=	Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EMITIDO ')->first();
		// $cabecera->activo            =   0;


		//AGREGAR PRODUCTO

		$idproducto 						=   $this->funciones->getCreateIdMaestra('productos');
		$cod_producto 						=   $this->funciones->getCreateCodCorrelativo('productos',7);
		$producto            	 			=	new Producto;
		$producto->id 	     	 			=   $idproducto;
		$producto->codigo 					=   $cod_producto;			
		$producto->descripcion 	   			=   $cabecera->descripcion;
		$producto->indproduccion 	   		=   1;
		$categoria_prod						=	Categoria::where('descripcion','=','BIENES PRODUCIDOS')->where('tipo_categoria','=','CAT_PRODUCTO')->first();
		$subcategoria_prod					=	Categoria::where('aux01','=',$categoria_prod->id)->where('tipo_categoria','=','SUBCAT_PRODUCTO')->first();
		$producto->categoria_id				=   $categoria_prod->id;
		$producto->categoria_nombre			=   $categoria_prod->descripcion;
		$producto->subcategoria_id			=   $subcategoria_prod->id;
		$producto->subcategoria_nombre		=   $subcategoria_prod->descripcion;
		$unidad_medida	 					= 	Categoria::where('descripcion','=','UNIDAD')
													->where('tipo_categoria','=','UNIDAD_MEDIDA')
													->first();
		$producto->unidad_medida_id			=   $unidad_medida->id;
		$producto->unidad_medida_nombre 	=   $unidad_medida->descripcion;
		$producto->peso			 	 	  	=   0;
		$producto->fecha_crea 	 			=   $this->fechaactual;
		$producto->usuario_crea 			=   Session::get('usuario')->id;
		$producto->save();

		//MODIFICAR PRODUCCION
		$cabecera->producto_id				=	$idproducto;
		$cabecera->estado_id				=	$emitido->id;
		$cabecera->estado_descripcion		=	$emitido->descripcion;
		$cabecera->fecha_mod         		=   $this->fechaactual;
		$cabecera->usuario_mod       		=   Session::get('usuario')->id;
		$cabecera->save();


		//ENTRADA DE PRODUCTOS DE ALMACEN

		$idkardex 							=   $this->funciones->getCreateIdMaestra('kardex');
		$idtipomovimiento 					=	$this->getIdTipoMovimiento('ENTRADA');
		$idcompraventa 						=	$this->getIdCompraVenta('PRODUCCION');

		$cantidadinicial 					= 	0;
		$cantidadingreso 					= 	0;
		$cantidadfinal						= 	0;
		//$kardexproducto 					=   Kardex::where('producto_id','=',$idproducto)->first();

		$producto							=	Producto::where('id','=',$idproducto)->first();

		$kardex            	 				=	new Kardex;
		$kardex->id 	     	 			=   $idkardex;
		$kardex->lote 	     	 			=   $cabecera->lote;			
		$kardex->almacen_id 	     		=   '1CIX00000001';
		$kardex->almacen_nombre 	   		=   'ALMACEN A';
		$kardex->tipo_movimiento_id 		=   $idtipomovimiento;
		$kardex->tipo_movimiento_nombre 	=   'ENTRADA';
		$kardex->compraventa_id 			=   $idcompraventa;
		$kardex->compraventa_nombre 		=   'PRODUCCION';
		$kardex->fecha 				 		=   $this->fecha_sin_hora;						
		$kardex->fechahora			 		=   $this->fechaactual;						
		$kardex->producto_id 				=   $idproducto;			
		$kardex->producto_nombre 	   		=   $producto->descripcion;
		$kardex->cantidadinicial			=   0;	
		$kardex->cantidadingreso			=   $cabecera->cantidad;	
		$kardex->cantidadsalida				=   0;
		$kardex->cantidadfinal				=   $cabecera->cantidad;
		$kardex->motivo_id 	     			=   '1CIX00000043';
		$kardex->motivo_nombre				=   'PRODUCCION';
		$kardex->fecha_crea 	 			=   $this->fechaactual;
		$kardex->usuario_crea 				=   Session::get('usuario')->id;
		$kardex->save();


		//SALIDAS DE ALMACEN 
		$DetalleProduccion  				=   DetalleProduccion::where('produccion_id','=',$produccion_id)
												->where('activo','=','1')
												->get();

		foreach($DetalleProduccion as $index=>$item){

			$idkardex 							=   $this->funciones->getCreateIdMaestra('kardex');
			$idtipomovimiento 					=	$this->getIdTipoMovimiento('SALIDA');
			$idcompraventa 						=	$this->getIdCompraVenta('PRODUCCION');
			$cantidadinicial 					= 	0;
			$cantidadingreso 					= 	0;
			$cantidadfinal						= 	0;

			$Productodet 						=	Producto::where('id','=',$item->producto_id)->first();
			$kardexproducto 					=   Kardex::where('producto_id','=',$item->producto_id)->orderby('fechahora','desc')->first();

			if($Productodet->categoria_id != 'CATP00000003'){

				$cantidadinicial 					= 	0;
				$cantidadingreso 					= 	0;
				$cantidadsalida 					= 	0;
				$cantidadfinal						= 	0;

				if(count($kardexproducto)>0){
					$cantidadinicial					= 	$kardexproducto->cantidadfinal;
					$cantidadsalida						= 	$item->cantidad;
					$cantidadfinal						= 	$cantidadinicial-$cantidadsalida;
				}

				$kardex            	 				=	new Kardex;
				$kardex->id 	     	 			=   $idkardex;
				$kardex->lote 	     	 			=   $cabecera->lote;			
				$kardex->almacen_id 	     		=   '1CIX00000001';
				$kardex->almacen_nombre 	   		=   'ALMACEN A';
				$kardex->tipo_movimiento_id 		=   $idtipomovimiento;
				$kardex->tipo_movimiento_nombre 	=   'SALIDA';
				$kardex->compraventa_id 			=   $idcompraventa;
				$kardex->compraventa_nombre 		=   'PRODUCCION';
				$kardex->fecha 				 		=   $this->fecha_sin_hora;					
				$kardex->fechahora			 		=   $this->fechaactual;						
				$kardex->producto_id 				=   $item->producto_id;			
				$kardex->producto_nombre 	   		=   $Productodet->descripcion;

				$kardex->cantidadinicial			=   $cantidadinicial;	
				$kardex->cantidadingreso			=   $cantidadingreso;	
				$kardex->cantidadsalida				=   $cantidadsalida;
				$kardex->cantidadfinal				=   $cantidadfinal;

				$kardex->motivo_id 	     			=   '1CIX00000043';
				$kardex->motivo_nombre				=   'PRODUCCION';
				$kardex->fecha_crea 	 			=   $this->fechaactual;
				$kardex->usuario_crea 				=   Session::get('usuario')->id;
				$kardex->save();

			}




		}


		$almacen_id           		=   '1CIX00000001';
		$proveedor_id           	=   '1CIX00000001';
		$almacen 					= 	Almacen::where('id','=',$almacen_id)->first();
		$proveedor 					= 	Proveedor::where('id','=',$proveedor_id)->first();
		//ingresar por almacen
		$detallealmacen				= 	DetalleAlmacen::where('activo','=',1)
										->where('almacen_id','=',$almacen_id)
										->where('proveedor_id','=','1CIX00000001')
										->where('producto_id','=',$idproducto)->first();

		$cantidadinicial 	= 0;
		$cantidadingreso 	= 0;
		$cantidadfinal		= 0;
		$producto					=	Producto::where('id','=',$idproducto)->first();
		if (count($detallealmacen) <= 0) {

			$iddetallealmacen 						=   $this->funciones->getCreateIdMaestra('detallealmacen');
			$cantidadinicial 	= 0;
			$cantidadingreso 	= $cabecera->cantidad;	
			$cantidadfinal		= $cantidadinicial + $cantidadingreso;	

			$detallealmacen            	 			=	new DetalleAlmacen;
			$detallealmacen->id 	     	 		=   $iddetallealmacen;
			$detallealmacen->almacen_id 	     	=   $almacen_id;
			$detallealmacen->almacen_nombre 	   	=   $almacen->nombre;
			$detallealmacen->proveedor_id 			=   $proveedor->id;			
			$detallealmacen->proveedor_nombre 	   	=   $proveedor->nombre_razonsocial;

			$detallealmacen->producto_id 			=   $producto->id;			
			$detallealmacen->producto_nombre 	   	=   $producto->descripcion;
			$detallealmacen->stock			 		=   $cantidadfinal;							
			$detallealmacen->fecha_crea 	 		=   $this->fechaactual;
			$detallealmacen->usuario_crea 			=   Session::get('usuario')->id;
			$detallealmacen->save();

		}else{

			$cantidadinicial 	= $detallealmacen->stock;
			$cantidadingreso 	= $cabecera->cantidad;
			$cantidadfinal		= $cantidadinicial + $cantidadingreso;	

			$detallealmacen->stock 					=   $cantidadfinal;					
			$detallealmacen->fecha_mod 	 			=   $this->fechaactual;
			$detallealmacen->usuario_mod 			=   Session::get('usuario')->id;
			$detallealmacen->save();

		}

	}


	public function actionAjaxAgregarDetalleProductoProduccion($idopcion,$idregistro,Request $request)
	{
		// dd('ssssse45');
		$data_producto_id                           =   $request['idproducto'];//nuevo
		$oeProducto 								=	Producto::where('id','=',$data_producto_id)->first();
		$descripcion                                =   $oeProducto->descripcion;
		$categoria_id                           	=   $oeProducto->categoria_id;
		$subcategoria_id                           	=   $oeProducto->subcategoria_id;
		//$unidadmedidaa_id                           =   $request['unidadmedidaa_id'];

		// $idopcion						=	$request['idopcion'];
		$produccion_id					=	$this->funciones->decodificarmaestra($idregistro);

		$productos		=	json_decode($request['productos'],true);
		$cantidad		=	0;
		$precio			=	0;
		$subtotal		=	0;
		foreach ($productos as $key => $producto) {
			$detallecompra = DetalleCompra::where('id','=',$producto['detallecompra_id'])->first();
			if($oeProducto->categoria_id=='CATP00000003'){
				$monto = (float)($producto['cantidad']);
				$cantidad=1;
			}
			else{
				$cantidad += $producto['cantidad'];
				$monto = (float)($producto['cantidad']*$detallecompra->preciounitario);
			}
			$subtotal += $monto;
		}
		
		$precio = round(($subtotal / $cantidad),2);
		// dd(compact('subtotal','cantidad','precio'));
		// dd($request['idproducto']);


		



		$cotizacion                                 =   Produccion::where('id', $produccion_id)->first();
		// dd($cotizacion);
		$categoria                              	=   Categoria::where('id', $categoria_id)->first();
		$subcategoria                              	=   Categoria::where('id', $subcategoria_id)->first();
		$producto                                 	=   Producto::where('id', $data_producto_id)->first();
		// $unidadmedida                               =   Categoria::where('id', $producto->unidad_medida_id)->first();

		$idopcion                                   =   $request['idopcion'];
		if($categoria_id=='CATP00000003'){
			$unidadmedida = Categoria::wheredescripcion('UNIDAD')->first();
			// dd($unidadmedida);
		}
		else
		{
			$unidadmedida                               =   Categoria::where('id', $producto->unidad_medida_id)->first();
		}

		$iddetallecotizacionanalisis                =   $this->funciones->getCreateIdMaestra('detalleproduccions');
		$cabecera                                   =   new DetalleProduccion;
		$cabecera->id                               =   $iddetallecotizacionanalisis;
		$cabecera->produccion_id                    =   $produccion_id;
		$cabecera->producto_id                      =   $data_producto_id;
		$cabecera->producto_nombre                  =   $producto->descripcion;
		$cabecera->codigo                      		=   $cotizacion->codigo;
	

		$cabecera->categoria_id             		=   $categoria->id;
		$cabecera->categoria_nombre         		=   $categoria->descripcion;

		$cabecera->subcategoria_id             		=   $subcategoria->id;
		$cabecera->subcategoria_nombre         		=   $subcategoria->descripcion;


		$cabecera->unidadmedida_id                  =   $unidadmedida->id;
		$cabecera->unidadmedida_nombre              =   $unidadmedida->descripcion;
		$cabecera->cantidad                         =   floatval($cantidad);
		$cabecera->precio_unitario                  =   floatval($precio);
		$cabecera->total                            =   $subtotal;//floatval($cantidad)*floatval($precio);
		$cabecera->fecha_crea                       =   $this->fechaactual;
		$cabecera->usuario_crea                     =   Session::get('usuario')->id;
		$cabecera->save();


		foreach ($productos as $key => $oeproducto) {

			// $cantidad 					+= 	$oeproducto['cantidad'];
			$detallecompra 				= 	DetalleCompra::where('id','=',$oeproducto['detallecompra_id'])->first();
			$detallecompra->consumido 	+= 	$oeproducto['cantidad'];
			$detallecompra->disponible 	-= 	$oeproducto['cantidad'];
			if($detallecompra->disponible<0){
				$detallecompra->disponible=0;
			}
			
			if($producto->categoria_id=='CATP00000003'){ 
				//servicio
				$monto = (float)($oeproducto['cantidad']);
				$cantidaddetalle=1;
			}
			else{
				//productos
				$monto = (float)((int)($oeproducto['cantidad'])*$detallecompra->preciounitario);
				$cantidaddetalle=$oeproducto['cantidad'];	
			}
			

			$detallecompra->save();



			$iddetalleproddet                	=   $this->funciones->getCreateIdMaestra('detalleproduccionsdetalle');
			$detalleProd                        =   new DetalleProduccionDet;
			
			$detalleProd->id					=	$iddetalleproddet;
			$detalleProd->produccion_id			=	$produccion_id;
			$detalleProd->detalleproduccion_id	=	$iddetallecotizacionanalisis;
			$detalleProd->compra_id				=	$oeproducto['compra_id'];;
			$detalleProd->detallecompra_id		=	$oeproducto['detallecompra_id'];
			$detalleProd->producto_id			=	$data_producto_id;
			$detalleProd->producto_nombre		=	$producto->descripcion;
			
			$detalleProd->cantidad				=	$cantidaddetalle;
			$detalleProd->preciounitario		=	$monto;
			$detalleProd->total					=	$monto;

			$detalleProd->subtotal				=	$monto;
			$detalleProd->disponible			=	$detallecompra->disponible;
			$detalleProd->consumido				=	$detallecompra->consumido;
			$detalleProd->fecha_crea            =   $this->fechaactual;
			$detalleProd->usuario_crea          =   Session::get('usuario')->id;
			$detalleProd->save();

		}
		//generar el precio y totales   
		$this->cot_generar_totales_produccion($cotizacion);
		return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.$idregistro)->with('bienhecho','Detalle Agregado Correctamente ');
	}

	public function actionAjaxAgregarProductoProduccion(Request $request)
	{
		// dd('ssssse45');

		$idopcion						=	$request['idopcion'];
		$cotizacion_id					=	$request['cotizacion_id'];
		$producto_id					=	$request['producto_id'];
		$oeProducto						=	Producto::where('id','=',$producto_id)->first();
		$olProductos					=	DetalleCompra::where('producto_id','=',$producto_id)
												->where('disponible','>',0)
												->where('activo','=',1)
												->where('estado_descripcion','=','EMITIDO')
												->get();

		$ldescripcion   =   DetalleCotizacion::where('activo','=',1)->pluck('descripcion')->toArray();

		$cotizacion                         =   Produccion::where('id', $cotizacion_id)->first();
		$combo_unidad_medida                =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
		$select_unidad_medida               =   '';
		$combo_categoria_servicio           =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
		// $combo_tipocategoria                =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
		$combo_tipocategoria                =   $this->gn_combo_tipocategoria();
			// $combo_tipocategoria              =   $this->gn_combo_categoria('TIPO_COMPRA','Seleccione Tipo','');

		$select_categoria_servicio          =   '';
		$cotizaciondetalle_id               =   '';
		
		// dd($cotizacion);// $grupomodelo1                       =   ['id'=>'ssss'];

		return View::make('cotizacion/modal/ajax/maagregarproductoproduccion',
						 [          
							'idopcion'                  =>  $idopcion,
							'cotizacion'                =>  $cotizacion,
							'combo_unidad_medida'       =>  $combo_unidad_medida,
							'select_unidad_medida'      =>  $select_unidad_medida,
							'combo_categoria_servicio'  =>  $combo_categoria_servicio,
							'select_categoria_servicio' =>  $select_categoria_servicio,
							'cotizaciondetalle_id'      =>  $cotizaciondetalle_id,
							'ajax'                      =>  true,       
							'combo_tipocategoria'       =>  $combo_tipocategoria,   
							'ldescripcion'              =>  $ldescripcion,
							'swmodificar'               =>  false,
							'olProductos'				=>	$olProductos,
							'oeProducto'				=>	$oeProducto,
						 ]);


	}

	public function actionAjaxEliminarLineaCotizacionProduccion(Request $request)
	{
		$idproduccion              =   $request['produccion_id'];
		$produccion_id				=	$this->funciones->decodificarmaestra($idproduccion);

		$idopcion                   =   $request['idopcion'];
		$cabecera                    =   Produccion::where('id','=',$produccion_id)->first();
		$detalles 					=	DetalleProduccion::where('produccion_id','=',$produccion_id)->where('activo','=',1)->get();
		foreach ($detalles as $index => $detalle) {
			$detalle->activo            =   0;
			$detalle->fecha_mod         =   $this->fechaactual;
			$detalle->usuario_mod       =   Session::get('usuario')->id;
			$detalle->save();

			$ldetdetalleprod		=		DetalleProduccionDet::where('produccion_id','=',$produccion_id)
															->where('detalleproduccion_id','=',$detalle->id)
															->where('activo','=',1)
															->get();
			foreach ($ldetdetalleprod as $index => $oedetProd)
			{
				$detallecompra 				=	DetalleCompra::where('id','=',$oedetProd->detallecompra_id)->first();
				$detallecompra->disponible	+=	$oedetProd->cantidad;
				$detallecompra->consumido	-=	$oedetProd->cantidad;
				$detallecompra->fecha_mod	=	$this->fechaactual;
				$detallecompra->usuario_mod	=	Session::get('usuario')->id;
				$detallecompra->save();

				$oedetProd->activo			=	0;
				$oedetProd->fecha_mod		=	$this->fechaactual;
				$oedetProd->usuario_mod		=	Session::get('usuario')->id;
				$oedetProd->save();
			}
		}

		$cabecera->activo            =   0;
		$cabecera->fecha_mod         =   $this->fechaactual;
		$cabecera->usuario_mod       =   Session::get('usuario')->id;
		$cabecera->save();
	}

	public function actionAjaxEliminarCotizacionProduccion(Request $request)
	{
		// dd('ssasd');
		// dd($request);

		$idproduccion              =   $request['produccion_id'];
		$produccion_id				=	$this->funciones->decodificarmaestra($idproduccion);

		// $idopcion                   =   $request['idopcion'];
		$cabecera                    =   Produccion::where('id','=',$produccion_id)->first();
		$detalles 					=	DetalleProduccion::where('produccion_id','=',$produccion_id)->where('activo','=',1)->get();
		foreach ($detalles as $index => $detalle) {
			$detalle->activo            =   0;
			$detalle->fecha_mod         =   $this->fechaactual;
			$detalle->usuario_mod       =   Session::get('usuario')->id;
			$detalle->save();
			$ldetdetalleprod		=		DetalleProduccionDet::where('produccion_id','=',$produccion_id)
															->where('detalleproduccion_id','=',$detalle->id)
															->where('activo','=',1)
															->get();
			foreach ($ldetdetalleprod as $index => $oedetProd)
			{
				$detallecompra 				=	DetalleCompra::where('id','=',$oedetProd->detallecompra_id)->first();
				$detallecompra->disponible	+=	$oedetProd->cantidad;
				$detallecompra->consumido	-=	$oedetProd->cantidad;
				$detallecompra->fecha_mod	=	$this->fechaactual;
				$detallecompra->usuario_mod	=	Session::get('usuario')->id;
				$detallecompra->save();

				$oedetProd->activo			=	0;
				$oedetProd->fecha_mod		=	$this->fechaactual;
				$oedetProd->usuario_mod		=	Session::get('usuario')->id;
				$oedetProd->save();
			}
		}

		$cabecera->activo            =   0;
		$cabecera->fecha_mod         =   $this->fechaactual;
		$cabecera->usuario_mod       =   Session::get('usuario')->id;
		$cabecera->save();
	}

	public function actionEmitirCotizacion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$sregistro_id = $idregistro;
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		$titulo     =   'Emitir  Cotizacion';
		$subtitulo     =   'Emicion de Cotizacion';
		View::share('titulo','Emitir  Cotizacion');
		$url = 'extornar-cotizacion';

		if($_POST)
		{
			
			$cotizacion = Cotizacion::where('id','=',$registro_id)->first();

			$generado   = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EVALUADO')->first();
			$emitido    = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EMITIDO ')->first();


			if($cotizacion->estado_id!==$generado->id){
				return Redirect::to('/'.$url.'/'.$idopcion.'/'.$idregistro)->with('errorbd','La Cotizacion debe estar en estado '.$generado->descripcion.' para poder EXTORNARLA');
			}


			$descripcion                      = $request['descripcion'];
			$notas                            = $request['notas'];
			$condiciones                      = $request['condiciones'];

			$cotizacion->notas                =   $notas;
			$cotizacion->condiciones          =   $condiciones;
			$cotizacion->estado_id              =  $emitido->id;
			$cotizacion->estado_descripcion     =  $emitido->descripcion;

			$cotizacion->save();

			LogEmision::where('tabla','cotizaciones')
						->where('activo',1)
						->where('idtabla',$registro_id)
						->update(
							[
								'activo'        =>  0,
								'usuario_mod'   =>  Session::get('usuario')->id,
								'fecha_mod'     =>  $this->fechaactual
							]
						);
			
			$cabecera                       =   new LogEmision;
			$cabecera->idtabla              =   $registro_id;
			$cabecera->descripcion          =   $descripcion;
			$cabecera->tabla                =   'cotizaciones';
			$cabecera->fecha_crea           =   $this->fechaactual;
			$cabecera->usuario_crea         =   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-de-cotizacion/'.$idopcion)->with('bienhecho', 'Cotizacion Lote: '.$cotizacion->lote.' EXTORNADA con EXITO');
		
		}
		else{
			$cotizacion             =   Cotizacion::where('id', $registro_id)->first();
			return View::make('cotizacion/emitir', 
							[
								'cotizacion'    =>  $cotizacion,
								'idopcion'      =>  $idopcion,
								'idregistro'    =>  $idregistro,
								'titulo'        =>  $titulo,
								'subtitulo'     =>  $subtitulo,
								'url'           =>  $url,
							]);
		}
	}

	public function actionAprobarCotizacion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$sregistro_id = $idregistro;
		$registro_id = $this->funciones->decodificarmaestra($idregistro);
		$titulo     =   'Aprobar  Cotizacion';
		View::share('titulo','Aprobar  Cotizacion');

		if($_POST)
		{
			
			$cotizacion     = Cotizacion::where('id','=',$registro_id)->first();
			$idgenerado     = $this->getIdEstado('EVALUADO');
			$aprobado       = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','APROBADO')->first();
			$extornado      = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','GENERADO')->first();
			$emitido        = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EMITIDO ')->first();
			$idextornado    = $this->getIdEstado('GENERADO');
			$idemitido      = $this->getIdEstado('EMITIDO');
			// $idevaluado  = $this->getIdEstado('EVALUADO');

			if($cotizacion->estado_id!==$emitido->id){
				return Redirect::to('/aprobar-cotizacion/'.$idopcion.'/'.$idregistro)->with('errorbd','La Cotizacion debe estar en estado '.$emitido->descripcion.' para poder EXTORNARLA');
			}

			$msj = $this->GenerarPlaneamiento($registro_id);
			if($msj!=='0'){
				return Redirect::to('/aprobar-cotizacion/'.$idopcion.'/'.$idregistro)->with('errorbd','Ocurrio un error inesperado '.$msj);
			}

			try {
				DB::beginTransaction();

				$descripcion                    =  $request['descripcion'];
				$nombreproyecto                 =  $request['nombre_proyecto'];

				$cotizacion->estado_id          =  $aprobado->id;
				$cotizacion->estado_descripcion =  $aprobado->descripcion;
				$cotizacion->nombreproyecto     =  $nombreproyecto;
				$cotizacion->save();

				LogAprobacion::where('tabla','cotizaciones')
							->where('accion','aprobar')
							->where('activo',1)
							->where('idtabla',$registro_id)
							->update(
								[
									'activo'        =>  0,
									'usuario_mod'   =>  Session::get('usuario')->id,
									'fecha_mod'     =>  $this->fechaactual
								]
							);
				
				$cabecera                       =   new LogAprobacion;
				$cabecera->idtabla              =   $registro_id;
				$cabecera->descripcion          =   $descripcion;
				$cabecera->accion               =   'aprobar';
				$cabecera->tabla                =   'cotizaciones';
				$cabecera->fecha_crea           =   $this->fechaactual;
				$cabecera->usuario_crea         =   Session::get('usuario')->id;
				$cabecera->save();

				DB::commit();
			} catch (Exception $ex) {
				DB::rollback();
				return Redirect::to('/aprobar-cotizacion/'.$idopcion.'/'.$idregistro)->with('errorbd','Ocurrio un error inesperado ');
			}
			return Redirect::to('/gestion-de-cotizacion/'.$idopcion)->with('bienhecho', 'Cotizacion Lote: '.$cotizacion->lote.' APROBADA con EXITO');
			
		}
		else{
			$cotizacion             =   Cotizacion::where('id', $registro_id)->first();
			return View::make('cotizacion/aprobar', 
							[
								'cotizacion'    =>  $cotizacion,
								'idopcion'      =>  $idopcion,
								'idregistro'    =>  $idregistro,
								'titulo'        =>  $titulo,
							]);
		}
	}


	public function actionDetalleCotizacion($idopcion,$idcotizacion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$sidcotizacion = $idcotizacion;
		$idcotizacion = $this->funciones->decodificarmaestra($idcotizacion);
		View::share('titulo','Detalle Cotizar');

		$cotizacion                         =   Cotizacion::where('id', $idcotizacion)->first();
		$cliente                            =   Cliente::where('id', $cotizacion->cliente_id)->first();
		$precotizacion                      =   Requerimiento::where('lote', $cotizacion->lote)->first();
		$listaimagenes                      =   Archivo::where('referencia_id','=',$precotizacion->id)
												->where('tipo_archivo','=','precotizacion')->where('activo','=','1')->get();

		$listadetalle                       =   DetalleCotizacion::where('activo','=',1)->where('cotizacion_id',$idcotizacion)->orderby('codigo','asc')->get();

		$listaarchivos                      =   Archivo::where('activo','=',1)->where('lote',$cotizacion->lote)->get();
		$tmusados   = (float)$listaarchivos->sum('size');
		$tmlimite   = round(($this->maxsize/(pow(1024,$this->unidadmb))),2);
		$tmusados   = round(($tmusados/(pow(1024,$this->unidadmb))),2);

		return View::make('cotizacion/detallecotizar', 
						[
							'precotizacion'             => $precotizacion,
							'cotizacion'                => $cotizacion,
							'listaimagenes'             => $listaimagenes,
							'cliente'                   => $cliente,
							'listadetalle'              => $listadetalle,
							'idopcion'                  => $idopcion,
							'listaarchivos'             => $listaarchivos,
							'unidad'                    =>  $this->unidadmb,
							'tmusados'                  =>  $tmusados,
							'tmlimite'                  =>  $tmlimite,
							'registro'                  =>  $precotizacion,
							'url'                       =>  'cotizacion',
							'view'                      =>  'requerimiento',
							// 'ajax'                       =>true,
						]);
	}

	 public function actionDetalleAnalizarDetalleCotizacion(Request $request)
	{

		$cotizacion_id              =   $request['cotizacion_id'];
		$detalle_cotizacion_id      =   $request['detalle_cotizacion_id'];
		$idopcion                   =   $request['idopcion'];

		$detallecotizacion          =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();
		$cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
		$combo_categoria_analisis   =   $this->con_generacion_combo('CATEGORIA_ANALISIS','Seleccione Categoria Analisis','');
		$select_categoria_analisis  =   '';
		$combo_unidad_medida_a      =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
		$select_unidad_medida_a     =   '';

		$listadetalle               =   DetalleCotizacionAnalisis::where('activo','=',1)
										->where('detallecotizacion_id','=',$detallecotizacion->id)
										->orderby('categoriaanalisis_id','asc')->get();

		$funcion                    =   $this;
		return View::make('cotizacion/form/fdetalleanalizar',
						 [
							'detallecotizacion'         => $detallecotizacion,
							'cotizacion'                => $cotizacion,
							'combo_categoria_analisis'  => $combo_categoria_analisis,
							'select_categoria_analisis' => $select_categoria_analisis,
							'combo_unidad_medida_a'     => $combo_unidad_medida_a,
							'select_unidad_medida_a'    => $select_unidad_medida_a,
							'funcion'                   => $funcion,
							'idopcion'                  => $idopcion,
							'listadetalle'              => $listadetalle,
							'ajax'                      => true,                            
						 ]);
	}

	public function actionExtornarEmisionCotizacion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$sregistro_id   =   $idregistro;
		$registro_id    =   $this->funciones->decodificarmaestra($idregistro);
		$titulo         =   'Extornar Emision Cotizacion';
		$subtitulo      =   'Extornar Emision de Cotizacion';
		$url            =   'extornar-emision-cotizacion';
		View::share('titulo','Extornar Emision  Cotizacion');


		if($_POST)
		{
			
			$cotizacion = Cotizacion::where('id','=',$registro_id)->first();
			$idgenerado = $this->getIdEstado('EMITIDO');
			$generado   = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EMITIDO')->first();
			$extornado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EVALUADO')->first();
			// $evaluado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EVALUADO')->first();
			$idextornado = $this->getIdEstado('EVALUADO');
			// $idevaluado  = $this->getIdEstado('EVALUADO');

			if($cotizacion->estado_id!==$generado->id){
				return Redirect::to('/extornar-emision-cotizacion/'.$idopcion.'/'.$idregistro)->with('errorbd','La Cotizacion debe estar en estado '.$generado->descripcion.' para poder EXTORNARLA');
			}

			$descripcion                = $request['descripcion'];
			// $cotizacion->activo         =  1;
			$cotizacion->estado_id      =  $idextornado;
			$cotizacion->estado_descripcion      =  $extornado->descripcion;

			// $cotizacion->fecha_extornarevaluacion        =  $this->fechaactual;;
			// $cotizacion->descripcion_extornarevaluacion  =  $descripcion;
			// $cotizacion->usuario_extornarevaluacion      =  Session::get('usuario')->id;
			$cotizacion->save();

			LogExtornar::where('tabla','cotizaciones')
						->where('accion','cotizacion-emitida')
						->where('activo',1)
						->where('idtabla',$registro_id)
						->update(
							[
								'activo'        =>  0,
								'usuario_mod'   =>  Session::get('usuario')->id,
								'fecha_mod'     =>  $this->fechaactual
							]
						);
			
			$cabecera                       =   new LogExtornar;
			$cabecera->idtabla              =   $registro_id;
			$cabecera->accion              =   'cotizacion-emitida';
			$cabecera->tabla                =   'cotizaciones';
			$cabecera->descripcion          =   $descripcion;
			$cabecera->fecha_crea           =   $this->fechaactual;
			$cabecera->usuario_crea         =   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-de-cotizacion/'.$idopcion)->with('bienhecho', 'Cotizacion Lote: '.$cotizacion->lote.' EXTORNADA con EXITO');
		
		}
		else{
			$cotizacion             =   Cotizacion::where('id', $registro_id)->first();
			return View::make('cotizacion/extornar', 
							[
								'subtitulo'     =>  $subtitulo,
								'url'           =>  $url,
								'cotizacion'    =>  $cotizacion,
								'idopcion'      =>  $idopcion,
								'idregistro'    =>  $idregistro,
								'titulo'        =>  $titulo,
							]);
		}
	}

	public function actionExtornarAprobacionCotizacion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$sregistro_id   =   $idregistro;
		$registro_id    =   $this->funciones->decodificarmaestra($idregistro);
		$titulo         =   'Extornar Aprobacion Cotizacion';
		$subtitulo      =   'Extornar Aprobacion de Cotizacion';
		$url            =   'extornar-aprobacion-cotizacion';
		View::share('titulo','Extornar Aprobacion  Cotizacion');


		if($_POST)
		{
			
			$cotizacion = Cotizacion::where('id','=',$registro_id)->first();
			$idgenerado = $this->getIdEstado('APROBADO');
			$generado   = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','APROBADO')->first();
			$extornado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EMITIDO')->first();
			$idextornado = $this->getIdEstado('EMITIDO');

			if($cotizacion->estado_id!==$generado->id){
				return Redirect::to('/'.$url.'/'.$idopcion.'/'.$idregistro)->with('errorbd','La Cotizacion debe estar en estado '.$generado->descripcion.' para poder EXTORNARLA');
			}

			$descripcion                = $request['descripcion'];
			// $cotizacion->activo         =  1;
			$cotizacion->estado_id      =  $idextornado;
			$cotizacion->estado_descripcion      =  $extornado->descripcion;

			// $cotizacion->fecha_extornarevaluacion        =  $this->fechaactual;;
			// $cotizacion->descripcion_extornarevaluacion  =  $descripcion;
			// $cotizacion->usuario_extornarevaluacion      =  Session::get('usuario')->id;
			$cotizacion->save();

			LogExtornar::where('tabla','cotizaciones')
						->where('accion','cotizacion-aprobada')
						->where('activo',1)
						->where('idtabla',$registro_id)
						->update(
							[
								'activo'        =>  0,
								'usuario_mod'   =>  Session::get('usuario')->id,
								'fecha_mod'     =>  $this->fechaactual
							]
						);
			
			$cabecera                       =   new LogExtornar;
			$cabecera->idtabla              =   $registro_id;
			$cabecera->accion              =   'cotizacion-aprobada';
			$cabecera->tabla                =   'cotizaciones';
			$cabecera->descripcion          =   $descripcion;
			$cabecera->fecha_crea           =   $this->fechaactual;
			$cabecera->usuario_crea         =   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-de-cotizacion/'.$idopcion)->with('bienhecho', 'Cotizacion Lote: '.$cotizacion->lote.' EXTORNADA con EXITO');
		
		}
		else{
			$cotizacion             =   Cotizacion::where('id', $registro_id)->first();
			return View::make('cotizacion/extornar', 
							[
								'subtitulo'     =>  $subtitulo,
								'url'           =>  $url,
								'cotizacion'    =>  $cotizacion,
								'idopcion'      =>  $idopcion,
								'idregistro'    =>  $idregistro,
								'titulo'        =>  $titulo,
							]);
		}
	}


	public function actionImprimirCotizacion($idopcion,$idregistro)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/

		set_time_limit(0);
		$registro_id        =   $this->funciones->decodificarmaestra($idregistro);
		$empresa_id         =   Session::get('empresas')->id;
		$empresa            =   Empresa::where('id',$empresa_id)->first();
		$cotizacion         =   Cotizacion::where('id',$registro_id)->first();
		$cliente            =   Cliente::where('id',$cotizacion->cliente_id)->first();
		$archivos           =   Archivo::where('referencia_id',$registro_id)->where('activo',1)->get();
		$detallecotizacion  =   DetalleCotizacion::where('cotizacion_id',$registro_id)->where('ispadre',0)->orderby('codigo','asc')->get();
		$cuentas            =   CuentasEmpresa::from('cuentasempresa as C')
									->join('entidadfinancieras as E','C.entidad_id','=','E.id')
									->where('C.empresa_id','=',$empresa->id)
									->get();

		// $customPaper        =   array(0,0,700.00,700.80);
		$customPaper        =   array(0,0,500,1000);
		$titulopdf          =   'Costo Produccion N°('.$cotizacion->lote.').pdf';
		$titulo             =   "Costo Produccion (".$cotizacion->lote.")";
		$pdf                =   PDF::loadView('cotizacion.pdf.cotizacionpdf', 
											[                                               
												'titulo'            =>  $titulo,
												'idopcion'          =>  $idopcion,
												'cliente'           =>  $cliente,
												'empresa'           =>  $empresa,
												'cotizacion'        =>  $cotizacion,
												'detallecotizacion' =>  $detallecotizacion,
												'cuentas'           =>  $cuentas,
											])
							// ->setPaper($customPaper,'landscape');
							->setPaper('a4','portrait');

		return $pdf->stream($titulopdf);
	}

	public function actionSubirArchivosCotizarRequerimiento($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		// $registro_id = $this->funciones->decodificarmaestra($idregistro);
		$registrocot_id = $this->funciones->decodificarmaestra($idregistro);
		// View::share('titulo','Subir Archivos a la Cotizacion');

		if($_POST)
		{
			$cotizacion         =   Cotizacion::where('id',$registrocot_id)->first();
			$lote               =   $cotizacion->lote;

			$registro           =   Requerimiento::where('lote', $lote)->where('activo',1)->first();
			$registro_id        =   $registro->id;
			$files              =   $request['upload'];
			$arr_archivos       =   explode(',',$request['archivos']);
			$usuario            =   User::where('id',Session::get('usuario')->id)->first();
			$listadetalledoc    =   Archivo::where('referencia_id','=',$registro->id)
									->where('tipo_archivo','=',$this->tipoarchivo)
									->get();
			$index              =   0;

			$datossize      = $this->ge_validarSizeArchivos($files,$arr_archivos,$registro->lote,$this->maxsize,$this->unidadmb);
			if((boolean)$datossize['sw']){
				return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.$idregistro)
					->with('errorbd', 
						'LIMITE '. round($datossize['limitesize'],2) 
						.' MB superado, EL LOTE: '.$registro->lote
						.' YA TIENE : '. round($datossize['sizeusado'],2)
						.' INTENTAS SUBIR : '.round($datossize['sizefiles'],2)
					);
			}
			try{
				DB::beginTransaction();
				$files              =   $request['upload'];
				if(!is_null($files)){
					// dd($files[0]);
					foreach($files as $file){
						// dd($file);
						// dd('555');
						$numero                     = count($listadetalledoc)+$index+1;
						$nombreoriginal             = $file->getClientOriginalName();
						if(in_array($nombreoriginal,$arr_archivos)){
							
							$info                       = new SplFileInfo($nombreoriginal);
							$extension                  = $info->getExtension();

							$nombre                     = $registro->lote.'-'.$numero.'-'.$file->getClientOriginalName();
							
							$rutafile           =   storage_path('app/').$this->pathFiles.$registro->lote.'/';
							$valor = $this->ge_crearCarpetaSiNoExiste($rutafile);
							$rutadondeguardar   =   $this->pathFiles.$registro->lote.'/';
							// $file->getRealPath()
							$rutaoriginal = $file->getRealPath();
							// copy($rutaoriginal,$rutafile.$nombre);
							copy($file->getRealPath(),$rutafile.$nombre);
							$urlmedio = 'app/'.$rutadondeguardar.$nombre;

							// \Storage::disk('local')->put($nombre,  \File::get($file));
							$idarchivo                  = $this->funciones->getCreateIdMaestra('archivos');
							$dcontrol                   = new Archivo;
							$dcontrol->id               = $idarchivo;
							$dcontrol->size             = filesize($file);
							$dcontrol->extension        = $extension;
							$dcontrol->lote             = $registro->lote;
							$dcontrol->referencia_id    = $registro->id;
							$dcontrol->nombre_archivo   = $nombre;
							$dcontrol->url_archivo      = $urlmedio;
							$dcontrol->area_id          = $usuario->trabajador->area_id;
							$dcontrol->area_nombre      = $usuario->trabajador->area->descripcion;
							$dcontrol->usuario_nombre   = $usuario->nombre;
							$dcontrol->tipo_archivo     = $this->tipoarchivo;
							$dcontrol->fecha_crea       = $this->fechaactual;
							$dcontrol->usuario_crea     = Session::get('usuario')->id;
							$dcontrol->save();

							$index              =   $index + 1;
						}
					}   
				}

				DB::commit();
			}catch(\Exception $ex){
				DB::rollback(); 
				$sw =   1;
				$mensaje  = $this->ge_getMensajeError($ex);
				return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.$idregistro)->with('errorbd', $mensaje);

			}

			return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Archivos '.$registro->nombre_razonsocial.' registrados con éxito');

		}

	}


	public function GenerarPlaneamiento($idcotizacion)
	{
		$rpta ='0';
		try {
				DB::beginTransaction();

				$cotizacion                     =   Cotizacion::where('id','=',$idcotizacion)->first();
				$cabecera                       =   new Planeamiento;
				$idplaneamiento                 =   $this->funciones->getCreateIdMaestra('planeamientos');
				$cabecera->id                   =   $idplaneamiento;
				$cabecera->cotizacion_id        =   $cotizacion->id;
				$cabecera->lote                 =   $cotizacion->lote;
				$cabecera->cliente_id           =   $cotizacion->cliente_id;
				$cabecera->cliente_nombre       =   $cotizacion->cliente_nombre;
				$cabecera->nombreproyecto       =   $cotizacion->nombreproyecto;
				$cabecera->notas                =   $cotizacion->notas;
				$cabecera->condiciones          =   $cotizacion->condiciones;
				$cabecera->total                =   $cotizacion->total;
				$cabecera->moneda_id            =   $cotizacion->moneda_id;
				$cabecera->totalmn              =   $cotizacion->totalmn;
				$cabecera->totalme              =   $cotizacion->totalme;
				$cabecera->tcambio              =   $cotizacion->tcambio;
				$cabecera->fecha                =   $cotizacion->fecha;
				$cabecera->estado_id            =   $this->generado->id;
				$cabecera->estado_descripcion   =   $this->generado->descripcion;
				$cabecera->fecha_crea           =   $this->fechaactual;
				$cabecera->usuario_crea         =   Session::get('usuario')->id;
				$totalcantidad =0;

				$detallecotizacion              =   DetalleCotizacion::where('cotizacion_id','=',$idcotizacion)->where('activo','=',1)->get();
				foreach ($detallecotizacion as $idet => $detalle) {
						$registro                           =   new DetallePlaneamiento;
						$iddetalle                          =   $this->funciones->getCreateIdMaestra('detalleplaneamientos');

						$registro->id                       =   $iddetalle;
						$registro->planeamiento_id          =   $idplaneamiento;
						$registro->descripcion              =   $detalle->descripcion;
						$registro->codigo                   =   $detalle->codigo;
						$registro->nivel                    =   $detalle->nivel;
						$registro->idpadre                  =   $detalle->idpadre;
						$registro->ispadre                  =   $detalle->ispadre;
						$registro->unidadmedida_id          =   $detalle->unidadmedida_id;
						$registro->unidadmedida_nombre      =   $detalle->unidadmedida_nombre;
						$registro->cantidad                 =   $detalle->cantidad;
						$totalcantidad                      +=  $detalle->cantidad;
						$registro->precio_unitario          =   $detalle->precio_unitario;
						$registro->mgadministrativos        =   $detalle->mgadministrativos;
						$registro->mgutilidad               =   $detalle->mgutilidad;
						$registro->totalcosto               =   $detalle->totalcosto;
						$registro->totalmanoobra            =   $detalle->totalmanoobra;
						$registro->totalservicio            =   $detalle->totalservicio;
						$registro->swigv                    =   $detalle->swigv;
						$registro->migv                     =   $detalle->migv;
						$registro->igv                      =   $detalle->igv;
						$registro->subtotalpunitarioprev    =   $detalle->subtotalpunitarioprev;
						$registro->subtotalpunitario        =   $detalle->subtotalpunitario;
						$registro->total                    =   $detalle->total;
						$registro->total_analisis           =   $detalle->total_analisis;
						$registro->impuestoanalisis_01      =   $detalle->impuestoanalisis_01;
						$registro->impuestoanalisis_02      =   $detalle->impuestoanalisis_02;
						$registro->swactualizado            =   $detalle->swactualizado;
						$registro->totalpreciounitariocalc  =   $detalle->totalpreciounitariocalc;
						$registro->totalpreciounitario      =   $detalle->totalpreciounitario;
					   
						$registro->fecha_crea               =   $this->fechaactual;
						$registro->usuario_crea             =   Session::get('usuario')->id;
						$registro->save();

						$detalleanalisis    =   DetalleCotizacionAnalisis::where('cotizacion_id','=',$idcotizacion)->where('detallecotizacion_id','=',$detalle->id)->get();
						foreach ($detalleanalisis as $idetana => $analisis) {
							$detanal                            =   new DetallePlaneamientoAnalisis;
							$iddetanalisis                      =   $this->funciones->getCreateIdMaestra('detalleplaneamientoanalisis');
							$detanal->id                        =   $iddetanalisis;
							$detanal->planeamiento_id           =   $idplaneamiento;
							$detanal->detalleplaneamiento_id    =   $iddetalle;
							$detanal->descripcion               =   $analisis->descripcion;
							$detanal->categoriaanalisis_id      =   $analisis->categoriaanalisis_id;
							$detanal->categoriaanalisis_nombre  =   $analisis->categoriaanalisis_nombre;
							$detanal->unidadmedida_id           =   $analisis->unidadmedida_id;
							$detanal->unidadmedida_nombre       =   $analisis->unidadmedida_nombre;
							$detanal->cantidad                  =   $analisis->cantidad;
							$detanal->precio_unitario           =   $analisis->precio_unitario;
							$detanal->total                     =   $analisis->total;
							  
							$detanal->fecha_crea               =   $this->fechaactual;
							$detanal->usuario_crea             =   Session::get('usuario')->id;
							$detanal->save();
						}
				}

				$cabecera->totalcantidad    =   $totalcantidad;
				$cabecera->save();
				
			DB::commit();
		} catch (Exception $ex) {
			$rpta = $this->ge_getMensajeError($ex);
			DB::rollback();

		}
		return $rpta;
	}
}
