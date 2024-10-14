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

use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\RequerimientoTraits;
use App\Traits\ConfiguracionTraits;
use App\Modelos\EntidadFinanciera;
use App\Modelos\Moneda;


use Hashids;
use SplFileInfo;

class CuentasEmpresaController extends Controller
{
	use GeneralesTraits;
	use RequerimientoTraits;
	use ConfiguracionTraits;

	private   $montobase        = 0;
	private   $indajustliq      = 1;
	private   $ruta             = 'cuentasempresa';
	private   $urlprincipal     = 'gestion-cuentas-empresa';
	private   $urlcompleto      = 'gestion-cuentas-empresa';
	private   $urlopciones      = 'cuentas-empresa';
	private   $rutaview         = 'cuentasempresa';
	private   $rutaviewblade    = 'cuentasempresa';
	private   $tregistro        = 'cuentasempresa';
	private   $tdetalle         = 'plantillaadenda';
	private   $idmodal          = 'cuentasempresa';
	private   $tipoarchivo      = 'cuentasempresa';
	// private   $tipocontratoprueba = '97';
	//PERMISOS DEL USUARIO
	private   $opciones;

	public function actionListarCuentasEmpresa($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		View::share('titulo','Lista Cuentas de la Empresa');
		// $codempresa = Session::get('empresas')->id;
		$user_id        = Session::get('usuario')->id;
		$this->opciones = $this->getPermisosOpciones($idopcion,$user_id);
		
		// dd(Session::get('empresas')->id);

		$empresa        =   Empresa::where('id',Session::get('empresas')->id)->first();

		$listadatos     =   CuentasEmpresa::from($this->tregistro.' as E')->where('E.empresa_id',$empresa->id)->where('E.activo',1)->get();
		// dd($listadatos->Entidad);
		$funcion        =   $this;

		return View::make($this->rutaview.'/lista',
						 [
							'listadatos'        => $listadatos,
							'funcion'           => $funcion,
							'idopcion'          => $idopcion,
							'view'              =>  $this->rutaviewblade,
							'url'               =>  $this->urlopciones,
							'ruta'              =>  $this->ruta,
							'idmodal'           =>  $this->idmodal,
							'opciones'          =>  $this->opciones,
						 ]);
	}

	public function actionAgregarCuentasEmpresa($idopcion,Request $request)
	{
		// dd($this->rutaview);
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		$codempresa = Session::get('empresas')->id;
		$usuario_id = Session::get('usuario')->id;
		View::share('titulo','Agregar Cuentas Empresa');
		$titulo     = 'Agregar Cuenta de Empresa';
		if($_POST)
		{
			/******************************/
			$moneda_id                  =   $request['moneda_id'];
			$entidad_id                 =   $request['entidad_id'];
			$nrocta                 	=   $request['nrocta'];
			$nroctacci                 	=   $request['nroctacci'];

			
			$entidad 					=	EntidadFinanciera::where('id',$entidad_id)->first();
			$moneda 					=	Moneda::where('id',$moneda_id)->first();

			$registro 					=	CuentasEmpresa::where('empresa_id',$codempresa)
											->where('entidad_id',$entidad_id)
											->where('moneda_id',$moneda_id)
											->where('activo',1)
											->first();
			if(count($registro)){
				return Redirect::to('/agregar-'.$this->urlopciones.'/'.$idopcion)->with('errorbd', 'Cuenta de la Entidad '.$entidad->entidad.' con moneda en : '.$moneda->descripcionabreviada.' ya registrada')->withInput();
			}

			// CuentasEmpresa::where('empresa_id',$codempresa)
			// 				->where('entidad_id',$entidad_id)
			// 				->where('moneda_id',$moneda_id)
			// 				->where('activo',1)
			// 				->update(
			// 					[
			// 						'activo'		=>	0,
			// 						'fecha_mod'		=>	$this->fechaactual,
			// 						'usuario_mod'	=>	$usuario_id
			// 					]
			// 				);
			$idnuevo                 	=   $this->funciones->getCreateIdMaestra('cuentasempresa');
			$cabecera                	=  	new CuentasEmpresa;
			$cabecera->id            	=	$idnuevo;
			$cabecera->empresa_id    	=	$codempresa;
			$cabecera->entidad_id    	=	$entidad_id;
			$cabecera->moneda_id     	=	$moneda_id;
			$cabecera->nrocta     		=	$nrocta;
			$cabecera->nroctacci    	=	is_null($request['nroctacci'])? '':$request['nroctacci'];
			$cabecera->fecha_crea    	=	$this->fechaactual;
			$cabecera->usuario_crea  	=	$usuario_id;
			$cabecera->activo        	=	1;
			$cabecera->save();



			return Redirect::to('/gestion-'.$this->urlopciones.'/'.$idopcion)->with('bienhecho', 'Registro '.$request['entidad'].' registrado con exito');

		}else{
			$combo_entidad = $this->con_combo_entidades_financieras();
			$select_entidad = '';
			$combo_moneda               =   $this->con_combo_monedas();
			$select_moneda              =   '';


			return View::make('/'.$this->rutaview.'/agregar',
						[
							'idopcion'          => $idopcion,
							'combo_entidad'     => $combo_entidad,
							'select_entidad'    =>  $select_entidad,
							'combo_moneda'      => $combo_moneda,
							'select_moneda'     => $select_moneda,

							'view'              =>  $this->rutaviewblade,
							'url'               =>  $this->urlopciones,
							'ruta'              =>  $this->ruta,
							'idmodal'           =>  $this->idmodal,
							'opciones'          =>  $this->opciones,
						]);
		}
	}

	public function actionModificarCuentasEmpresa($idopcion,$idregistro,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $registro_id = $this->funciones->decodificarmaestra($idregistro);

        View::share('titulo','Modificar Cuenta Empresa');
		$codempresa = Session::get('empresas')->id;
		$usuario_id = Session::get('usuario')->id;

        if($_POST)
        {

            $cabecera               =	CuentasEmpresa::find($registro_id);
            // $cabecera->entidad_id   =	$request['entidad_id'];
			// $cabecera->moneda_id    =	$request['moneda_id'];
			$cabecera->nrocta     	=	$request['nrocta'];
			$cabecera->nroctacci    =	is_null($request['nroctacci'])? '':$request['nroctacci'];
            $cabecera->fecha_mod	=	$this->fechaactual;
			$cabecera->usuario_mod	=	$usuario_id;
            $cabecera->save();


            return Redirect::to('/'.$this->urlcompleto.'/'.$idopcion)->with('bienhecho', 'CuentasEmpresa modificado con exito');

        }else{

                $registro 			= 	CuentasEmpresa::where('id', $registro_id)->first();
                $combo_entidad 		= 	$this->con_combo_entidades_financieras();
				$select_entidad 	= 	$registro->entidad_id;
				$combo_moneda       =   $this->con_combo_monedas();
				$select_moneda      =   $registro->moneda_id;

                return View::make('/'.$this->rutaview.'/modificar',
                                [
                                    'registro'  		=> $registro,
                                    'idopcion' 			=> $idopcion,

                                    'combo_entidad'     => $combo_entidad,
									'select_entidad'    =>  $select_entidad,
									'combo_moneda'      => $combo_moneda,
									'select_moneda'     => $select_moneda,

									'view'              =>  $this->rutaviewblade,
									'url'               =>  $this->urlopciones,
									'ruta'              =>  $this->ruta,
									'idmodal'           =>  $this->idmodal,
									'opciones'          =>  $this->opciones,
									'modificar'			=>	true,
                                ]);
        }

    }

    public function actionEliminarCuentasEmpresa($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
	    if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $sregistro_id = $idregistro;
        $registro_id = $this->funciones->decodificarmaestra($idregistro);
        $titulo 	=	'Eliminar  Cuenta Empresa';
        View::share('titulo','Eliminar  Cuenta Empresa');
        $user_id =Session::get('usuario')->id;

      
        $registro 				= 	CuentasEmpresa::where('id','=',$registro_id)->first();

        if($registro->activo==0){
            return Redirect::to('/'.$this->urlcompleto.'/'.$idopcion)->with('errorbd','LA CUENTA YA SE ENCUENTRA ELIMINADA');
        }
        $registro->activo      	=	0;

        $registro->usuario_mod  =	$user_id;
        $registro->fecha_mod    =	$this->fechaactual;
		$registro->save();	
        return Redirect::to('/'.$this->urlcompleto.'/'.$idopcion)->with('bienhecho', 'Cuenta Empresa ELIMINADA con EXITO');
	}



}
