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

class GestionEsquemaProductosController extends Controller
{
	//
	use GeneralesTraits;
	use ConfiguracionTraits;

	public function actionListarProductosProducidos($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Listar Esquema Producto');

		$finicio                        =   $this->inicio;
		$ffin                           =   $this->fin;



		$listadatos                     =   EsquemaProducto::where('activo','=',1)
												->orderBy('id', 'desc')->get();

		$funcion                        =   $this;




		return View::make('esquemaproducto/lista',
						 [
							'listadatos'            => $listadatos,
							'funcion'               => $funcion,
							'inicio'                => $finicio,
							'fin'                   => $ffin,
							'idopcion'              => $idopcion,   
						 ]);
	}


	public function actionAgregarEsquemaProductos($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Agregar Esquema Producto');
		if($_POST)
		{
			
			try {
					DB::beginTransaction();
					/******************************/

					$listagemas 				=	explode('&&&',$request['xmllistagemas']);

					$usuario                    =   User::where('id',Session::get('usuario')->id)->first();
					$descripcion                =   $request['descripcion'];

					$producto_id                =   $request['producto_id'];
					$producto                   =   Producto::where('id','=',$producto_id)->first();
					
					$tipooro_id					=	$request['tipooro_id'];
					$tipooro                    =   Producto::where('id','=',$tipooro_id)->first();

					$gramos						=	(float)$request['gramos'];
					$precio_x_gramos			=	(float)$request['precio_x_gramos'];

					$cantidad_engaste			=	(int)$request['cantidad_engaste'];
					$precio_unitario_engaste	=	(float)$request['precio_unitario_engaste'];
					$precio_total_engaste		=	(float)$request['precio_total_engaste'];

					$moneda                     =   Moneda::where('id','=',$descripcion)->first();
					$fecha                      =   $request['fecha'];

					$idregistro                 =   $this->funciones->getCreateIdMaestra('esquemaproducto');
					// $codigo                     =   $this->funciones->generar_codigo('certificados',8);
					$cod_registro 				=   $this->funciones->getCreateCodCorrelativo('esquemaproducto',8);

					$tipocambio                 =   TipoCambio::where('fecha','<=',date('d-m-Y'))->orderby('fecha','desc')->first();
					$cabecera                   	=   new EsquemaProducto();
					$cabecera->id               	=   $idregistro;
					$cabecera->codigo           	=   $cod_registro;
					$cabecera->descripcion      	=   $descripcion;
					$cabecera->producto_id    		=   $producto_id;
					$cabecera->producto_descripcion =   $producto->descripcion;

					$cabecera->tipooro_id       	=   $tipooro_id;
					$cabecera->tipooro_descripcion  =   $tipooro->descripcion;
					
					$cabecera->gramos				=	$gramos;
					$cabecera->precio_x_gramos		=	$precio_x_gramos;
					$cabecera->costo_total_gemas	=	(float)($gramos*$precio_x_gramos);
					
					$cabecera->cantidad_total_gemas		=	$cantidad_engaste;
					$cabecera->cantidad_engaste			=	$cantidad_engaste;
					$cabecera->precio_unitario_engaste	=	$precio_unitario_engaste;
					$cabecera->precio_total_engaste		=	$precio_total_engaste;


					$cabecera->tc               		=   $tipocambio->venta;

					$cabecera->estado_id                =   '1CIX00000003';
					$cabecera->estado_descripcion       =   'GENERADO'; 
					
					$cabecera->fecha            =   $fecha;
					$cabecera->fecha_crea       =   $this->fechaactual;
					$cabecera->usuario_crea     =   Session::get('usuario')->id;
					$cabecera->save();
					$idregistroen               =   Hashids::encode(substr($idregistro, -8));

					DB::commit();
				
			} catch (Exception $ex) {
				DB::rollback();
				  $msj =$this->ge_getMensajeError($ex);
				return Redirect::to('/gestion-esquema-productos/'.$idopcion)->with('errorurl', $msj);
			}
			/******************************/

			return Redirect::to('/modificar-esquema-productos/'.$idopcion.'/'.$idregistroen)->with('bienhecho', 'Registro realizado con exito');

		}
		else
		{
			$combo_producto		=   array('' => "Seleccione Producto") + Producto::where('indproduccion','=',1)->pluck('descripcion','id')->toArray();// + $datos;
			$select_producto	=   '';
			$combo_tipo_oro		=   array('' => "Seleccione Tipo") + Producto::where('subcategoria_nombre','=','ORO')->pluck('descripcion','id')->toArray();// + $datos;
			$select_tipo_oro	=   '';
			$combo_gemas		=   array('' => "Seleccione Gema") + Producto::where('subcategoria_nombre','=','GEMAS')->pluck('descripcion','id')->toArray();// + $datos;
			$select_gemas		=   '';
			$tipocambio			=   TipoCambio::where('fecha','<=',date('d-m-Y'))->orderby('fecha','desc')->first();
			$combo_origen_gema	=   array('' => "Seleccione Origen") + Categoria::where('tipo_categoria','=','TIPO_ORIGEN_GEMA')->pluck('descripcion','id')->toArray();// + $datos;
			$select_origen_gema	=   '';
			$producto			=	Producto::where('indproduccion','=',1)->skip(1)->first();
			
			$swmodificar        =   true;

			return View::make('esquemaproducto/agregar',
						[
							'idopcion'                  =>  $idopcion,
							'combo_producto'            =>  $combo_producto,
							'select_producto'           =>  $select_producto,
							'combo_tipo_oro'            =>  $combo_tipo_oro,
							'select_tipo_oro'           =>  $select_tipo_oro,
							'combo_gemas'               =>  $combo_gemas,
							'select_gemas'              =>  $select_gemas,
							'combo_origen_gema'         =>  $combo_origen_gema,
							'select_origen_gema'        =>  $select_origen_gema,
							'producto'				=>	$producto,
							'tipocambio'				=>	$tipocambio,
							'swmodificar'               => $swmodificar,
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
			$cabecera->fecha_mod        =   $this->fechaactual;
			$cabecera->usuario_mod      =   Session::get('usuario')->id;
			$cabecera->save();

			return Redirect::to('/gestion-esquema-productos/'.$idopcion)->with('bienhecho', 'Orden Venta modificada con exito');
		}else{

			$registro           =   OrdenVenta::where('id', $registro_id)->first();

			$listadetalle       =   DetalleOrdenVenta::where('activo','=',1)
									->where('ordenventa_id','=',$registro_id)
									->orderby('id','asc')
									->orderby('producto_nombre','asc')
									->get();

			$select_cliente     =   $registro->cliente_id;
			$combo_cliente      =   [''=>'SELECCIONE']+Cliente::where('activo','=',1)->pluck('nombre_razonsocial','id')->toArray();
			$select_moneda      =   $registro->moneda_id;
			$combo_moneda       =   $this->gn_combo_moneda('Seleccione moneda','');         
			 
			return View::make('ordenventa/modificar', 
							[
								'registro'          => $registro,
								'listadetalle'      => $listadetalle,
								'select_cliente'    => $select_cliente,
								'combo_cliente'     => $combo_cliente,
								'select_moneda'     => $select_moneda,      
								'combo_moneda'      => $combo_moneda,       
								'idregistro'        =>  $idregistro,
								'idopcion'          => $idopcion,
								'swmodificar'       =>  false,
							]);
		}
	}

	
}
