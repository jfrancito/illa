<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Cliente;
use App\Modelos\Categoria;
use App\Modelos\Precotizacion;
use App\Modelos\Requerimiento;
use App\Modelos\Archivo;
use App\Modelos\Cotizacion;
use App\Modelos\Empresa;
use App\Modelos\CuentasEmpresa;
use App\Modelos\EntidadFinanciera;


use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\RequerimientoTraits;
use App\Traits\ConfiguracionTraits;

use App\Modelos\Moneda;
use App\Modelos\CajaDetalle;
use App\Modelos\Caja;



use Hashids;
use SplFileInfo;

class CuentasSaldoEmpresaController extends Controller
{
	use GeneralesTraits;
	use RequerimientoTraits;
	use ConfiguracionTraits;


	private   $opciones;

	public function actionListarSaldoCuentasEmpresa($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Lista Saldo Cuentas de la Empresa');
		// $codempresa = Session::get('empresas')->id;
		$user_id        = Session::get('usuario')->id;
		$this->opciones = $this->getPermisosOpciones($idopcion,$user_id);
		
		// dd(Session::get('empresas')->id);

		$empresa        =   Empresa::where('id',Session::get('empresas')->id)->first();

		$listadatos     =   CajaDetalle::join('caja','caja.id','=','cajadetalle.caja_id')
							->where('cajadetalle.activo','=','1')
							->select(DB::raw('entidad_id,entidad_nombre,cajadetalle.cuenta_id,nrocta,cajadetalle.moneda_nombre,sum(cajadetalle.importe * caja.tipo_movimiento) as total'))
							->groupBy('entidad_id')
							->groupBy('entidad_nombre')
							->groupBy('cajadetalle.cuenta_id')
							->groupBy('nrocta')
							->groupBy('cajadetalle.moneda_nombre')
							->get();

		$funcion        =   $this;

		return View::make('reportes/listasaldocuentas',
						 [
							'listadatos'        => $listadatos,
							'funcion'           => $funcion,
							'idopcion'          => $idopcion
						 ]);
	}


	public function actionAjaxModalSaldoCuenta(Request $request)
	{

		$idopcion 	 						= 	$request['idopcion'];
		$data_entidad_id 	 				= 	$request['data_entidad_id'];
		$data_cuenta_id 	 				= 	$request['data_cuenta_id'];


		$entidad 							=	EntidadFinanciera::where('id','=',$data_entidad_id)->first();
		$listadatos     					=   CajaDetalle::join('caja','caja.id','=','cajadetalle.caja_id')
												->where('cajadetalle.activo','=','1')
												->where('cajadetalle.entidad_id','=',$data_entidad_id)
												->where('cajadetalle.cuenta_id','=',$data_cuenta_id)
												->select(DB::raw(
														'cajadetalle.*,caja.tipo_movimiento_nombre,caja.tabla_movimiento,caja.tipo_movimiento,caja.tipo_movimiento*cajadetalle.total as tt'

														))
												->orderby('cajadetalle.fecha_crea','desc')
												->get();

		//dd($data_cuenta_id);

		return View::make('reportes/modal/ajax/mdetallesaldo',
						 [		 	
						 	'listadatos' 				=> $listadatos,
						 	'entidad' 				=> $entidad,					 	
						 	'ajax' 						=> true,						 	
						 ]);
	}




}
