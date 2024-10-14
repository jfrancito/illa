<?php

namespace App\Http\Controllers;

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
use App\Modelos\Produccion;
use App\Modelos\DetalleProduccion;
use App\Modelos\LogExtornar;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\RequerimientoTraits;
use App\Traits\ConfiguracionTraits;
use Hashids;
use SplFileInfo;

class ProduccionController extends Controller {

	use GeneralesTraits;
	use RequerimientoTraits;
	use ConfiguracionTraits;
  	
  	private   $montobase        = 0;
    private   $indajustliq      = 1;
    private   $ruta             = 'produccion';
    private   $urlprincipal     = 'gestion-de-produccion';
    private   $urlcompleto      = 'gestion-de-produccion';
    private   $urlopciones      = 'produccion';
    private   $rutaview         = 'produccion';
    private   $rutaviewblade    = 'produccion';
    private   $tregistro        = 'produccions';
    private   $tdetalle         = 'plantillaadenda';
    private   $idmodal          = 'produccion';
    private   $tipoarchivo 		= 'produccion';
    // private   $tipocontratoprueba = '97';
    //PERMISOS DEL USUARIO
    private   $opciones;

	public function actionListarProduccion($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Lista Produccion');
	    // $codempresa = Session::get('empresas')->id;
		$user_id    = Session::get('usuario')->id;
		$this->opciones = $this->getPermisosOpciones($idopcion,$user_id);

	    $listadatos 	= 	Produccion::where('activo',1)->get();
		$funcion 		= 	$this;
		$idgenerado 	=	$this->getIdEstado('GENERADO');
		$idemitido 		=	$this->getIdEstado('EMITIDO');
		return View::make($this->rutaview.'/lista',
						 [
						 	'listadatos' 		=> 	$listadatos,
						 	'funcion' 			=> 	$funcion,
						 	'idopcion' 			=> 	$idopcion,
						 	'view'            	=>  $this->rutaviewblade,
							'url'             	=>  $this->urlopciones,
							'ruta'            	=>  $this->ruta,
							'idmodal'         	=>  $this->idmodal,
							'opciones'        	=>  $this->opciones,
							'idgenerado'		=>	$idgenerado,
							'idemitido'			=>	$idemitido,
						 ]);
	}

	public function actionDetalleProduccion($idopcion,$idregistro)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
		if($validarurl <> 'true'){return $validarurl;}
		/******************************************************/
		
		$registro_id = $this->funciones->decodificarmaestra($idregistro);

		View::share('titulo','Detalle Produccion');
		// $codempresa = Session::get('empresas')->id;
		$user_id    = Session::get('usuario')->id;
		$this->opciones = $this->getPermisosOpciones($idopcion,$user_id);

		$produccion		=	Produccion::where('id',$registro_id)->first();
		$funcion		=	$this;
		$idgenerado		=	$this->getIdEstado('GENERADO');
		$idemitido		=	$this->getIdEstado('EMITIDO');
		$listadetalle   =   DetalleProduccion::where('activo','=',1)->where('produccion_id',$registro_id)->orderby('codigo','asc')->get();

		return View::make($this->rutaview.'/detalle',
						 [
						 	'produccion' 		=> 	$produccion,
						 	'funcion' 			=> 	$funcion,
						 	'idopcion' 			=> 	$idopcion,
						 	'listadetalle'		=>	$listadetalle,
						 	'view'            	=>  $this->rutaviewblade,
							'url'             	=>  $this->urlopciones,
							'ruta'            	=>  $this->ruta,
							'idmodal'         	=>  $this->idmodal,
							'opciones'        	=>  $this->opciones,
							'idgenerado'		=>	$idgenerado,
							'idemitido'			=>	$idemitido,
						 ]);
	}

	public function actionAgregarProduccion($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Produccion');
		if($_POST)
		{

			$nombre 	 						= 	$request['nombre'];
			$descripcion 	 					= 	$request['descripcion'];
			$moneda_id 	 						= 	$request['moneda_id'];
			$cantidad 	 						= 	$request['cantidad'];

			// $cliente 							= 	Cliente::where('id','=',$cliente_id)->first();

			$codigo 							= 	$this->funciones->generar_codigo('produccions',10);
			$idprecotizacion 					=   $this->funciones->getCreateIdMaestra('produccions');
			// dd($codigo);
			$cabecera            	 			=	new Produccion;
			$cabecera->id 	     	 			=   $idprecotizacion;
			$cabecera->lote						=   $codigo;
			$cabecera->codigo					=   $codigo;
			$cabecera->nombre 					=   $nombre;
			$cabecera->cantidad 				=   $cantidad;

			$cabecera->fecha 					=   $this->fecha_sin_hora;
			$cabecera->descripcion 				=   $descripcion;
			$cabecera->moneda_id 	   			=   $moneda_id;
			$cabecera->estado_id 	   			=   $this->generado->id;
			$cabecera->estado_descripcion 	   	=   $this->generado->descripcion;

			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();

 		 	return Redirect::to('/cotizar-cotizacion/'.$idopcion.'/'.Hashids::encode(substr($idprecotizacion, -8)))->with('bienhecho', 'Ingrese el detalle de producción');

		}else{

		    // $select_cliente  		=	'';
		    $select_moneda 			=	'1CIX00000017';
		    // $combo_cliente 			=	$this->con_combo_clientes();
		    $combo_moneda 			=	$this->con_combo_monedas();


			return View::make($this->rutaview.'.agregar',
						[
							// 'select_cliente'  	=> 	$select_cliente,
							// 'combo_cliente'   	=> 	$combo_cliente,
							'select_moneda'  	=> 	$select_moneda,
							'combo_moneda'		=>	$combo_moneda,
						  	'idopcion'  	  	=> 	$idopcion,
						  	'view'            	=>  $this->rutaviewblade,
							'url'             	=>  $this->urlopciones,
							'ruta'            	=>  $this->ruta,
							'idmodal'         	=>  $this->idmodal,
							'opciones'        	=>  $this->opciones,
						]);
		}
	}

	public function actionGestionArchivosProduccion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idregistro);
	    View::share('titulo','Subir Archivos al Requerimiento');

		$registro 			= 	Requerimiento::where('id', $registro_id)->first();
		$combolote 			=	[''=>'SELECCIONE LOTE']+Archivo::where('referencia_id','<>',$registro_id)->where('activo',1)->selectRaw("DISTINCT referencia_id as id,lote")->pluck('lote','id')->toArray();
									// +Requerimiento::where('id','<>',$registro_id)->where('activo',1)->pluck('lote','id')->toArray();
		$listaarchivos 		= 	Archivo::where('referencia_id','=',$registro->id)
									->where('tipo_archivo','=',$this->tipoarchivo)
									->where('activo','=','1')->get();
		$tmusados 	= (float)$listaarchivos->sum('size');
		$tmlimite 	= round(($this->maxsize/(pow(1024,$this->unidadmb))),2);
		$tmusados 	= round(($tmusados/(pow(1024,$this->unidadmb))),2);

	    return View::make($this->rutaview.'/listaarchivos', 
	    				[
	    					'registro'  		=> 	$registro,
	    					'listaarchivos'  	=> 	$listaarchivos,
				  			'idopcion' 			=> 	$idopcion,
				  			'idregistro' 		=> 	$idregistro,
				  			'view'            	=>  $this->rutaviewblade,
							'url'             	=>  $this->urlopciones,
							'ruta'            	=>  $this->ruta,
							'idmodal'         	=>  $this->idmodal,
							'opciones'        	=>  $this->opciones,
							'unidad'			=> 	$this->unidadmb,
							'tmusados'			=>	$tmusados,
							'tmlimite'			=>	$tmlimite,
							'combolote'			=>	$combolote,
							''
	    				]);
	}


	public function actionClonarArchivosProduccion($idopcion,$idregistro,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id 		= 	$this->funciones->decodificarmaestra($idregistro);
		$registro 			= 	Requerimiento::where('id', $registro_id)->first();
	    View::share('titulo','Subir Archivos al Requerimiento');
		
		DB::beginTransaction();
		try {
		
			$lote_id 		= 	$request['lote_id'];
			$reqorigen 		= 	Requerimiento::where('id','=',$lote_id)->first();

			$larchivosorig  = 	Archivo::where('referencia_id','=',$lote_id)->where('activo',1)->get();
			$larchivosdst 	= 	Archivo::where('referencia_id','=',$registro_id)->get();

			$context 		=	0;
			$contnoext 		=	0;
			
			$rutafile 					=	storage_path('app/').$this->pathFiles.$registro->lote.'/';
			$rutadondeguardar 			=	$this->pathFiles.$registro->lote.'/';
			$valor 						=	$this->ge_crearCarpetaSiNoExiste($rutafile);

			foreach ($larchivosorig as $index => $fileorg) {

				$numero 	= count($larchivosdst)+$index+1;
				if($this->ge_validarArchivoDuplicado($fileorg->nombre_archivo,$registro_id))
				{
					$contnoext++;
					$nombre 					=	$fileorg->nombre_archivo;

					$rutaoriginal 				=	storage_path('app/').$this->pathFiles.$fileorg->lote.'/'.$fileorg->nombre_archivo;
					copy($rutaoriginal,$rutafile.$nombre);
					$urlmedio = 'app/'.$rutadondeguardar.$nombre;
					$idarchivo 					=	$this->funciones->getCreateIdMaestra('archivos');
					$dcontrol 					=	new Archivo;
					$dcontrol->id 				=	$idarchivo;
					$dcontrol->size 			=	$fileorg->size;
					$dcontrol->extension 		=	$fileorg->extension;
					$dcontrol->lote 			=	$registro->lote;
					$dcontrol->referencia_id 	=	$registro->id;
					$dcontrol->nombre_archivo 	=	$nombre;
					$dcontrol->url_archivo 		=	$urlmedio;
					$dcontrol->tipo_archivo 	=	$this->tipoarchivo;
					$dcontrol->area_id 			=	$fileorg->area_id;
					$dcontrol->area_nombre 		=	$fileorg->area_nombre;
					$dcontrol->usuario_nombre 	=	$fileorg->usuario_nombre;
					$dcontrol->fecha_crea 		=	$this->fechaactual;
					$dcontrol->usuario_crea		=	Session::get('usuario')->id;
					$dcontrol->save();
					$index 				= 	$index + 1;
				}
				else{
					$context++;
				}
			}				
		
		} catch (Exception $ex) {	
			DB::rollback();
			$mensaje = $this->ge_getMensajeError($ex);
			return Redirect::to('/subir-archivos-'.$this->urlopciones.'/'.$idopcion.'/'.$idregistro)->with('errorbd', $mensaje);
		}
		DB::commit();

		return Redirect::to('/subir-archivos-'.$this->urlopciones.'/'.$idopcion.'/'.$idregistro)->with('bienhecho', 
						' Archivos CLONADOS  : '.$contnoext.
						' Archivos Existentes: '.$context
						);
		
	}

	public function actionSubirArchivosProduccion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idregistro);
	    View::share('titulo','Subir Archivos al Requerimiento');

		if($_POST)
		{
			$registro 			= 	Requerimiento::where('id', $registro_id)->first();
			$files 				= 	$request['upload'];
			$arr_archivos 		=	explode(',',$request['archivos']);
			$usuario 			=	User::where('id',Session::get('usuario')->id)->first();
			$listadetalledoc 	= 	Archivo::where('referencia_id','=',$registro->id)
									->where('tipo_archivo','=',$this->tipoarchivo)
									->get();
			$index 				= 	0;

			$datossize 		= $this->ge_validarSizeArchivos($files,$arr_archivos,$registro->lote,$this->maxsize,$this->unidadmb);
			if((boolean)$datossize['sw']){
                return Redirect::to('/subir-archivos-requerimiento/'.$idopcion.'/'.$idregistro)
                	->with('errorbd', 
                		'LIMITE '. round($datossize['limitesize'],2) 
                		.' MB superado, EL LOTE: '.$registro->lote
                		.' YA TIENE : '. round($datossize['sizeusado'],2)
                		.' INTENTAS SUBIR : '.round($datossize['sizefiles'],2)
                	);
			}
		  	try{
                DB::beginTransaction();
				$files 				= 	$request['upload'];
				if(!is_null($files)){
					// dd($files[0]);
					foreach($files as $file){
						// dd($file);
						// dd('555');
						$numero 					= count($listadetalledoc)+$index+1;
						$nombreoriginal 			= $file->getClientOriginalName();
						if(in_array($nombreoriginal,$arr_archivos)){
							
							$info 						= new SplFileInfo($nombreoriginal);
							$extension 					= $info->getExtension();

							$nombre 					= $registro->lote.'-'.$numero.'-'.$file->getClientOriginalName();
							
                            $rutafile 			=	storage_path('app/').$this->pathFiles.$registro->lote.'/';
						    $valor = $this->ge_crearCarpetaSiNoExiste($rutafile);
							$rutadondeguardar 	= 	$this->pathFiles.$registro->lote.'/';
							// $file->getRealPath()
							$rutaoriginal = $file->getRealPath();
                            // copy($rutaoriginal,$rutafile.$nombre);
                            copy($file->getRealPath(),$rutafile.$nombre);
                            $urlmedio = 'app/'.$rutadondeguardar.$nombre;

							// \Storage::disk('local')->put($nombre,  \File::get($file));
							$idarchivo 					= $this->funciones->getCreateIdMaestra('archivos');
							$dcontrol 					= new Archivo;
							$dcontrol->id 				= $idarchivo;
							$dcontrol->size 			= filesize($file);
							$dcontrol->extension 		= $extension;
							$dcontrol->lote 			= $registro->lote;
							$dcontrol->referencia_id 	= $registro->id;
							$dcontrol->nombre_archivo 	= $nombre;
							$dcontrol->url_archivo 		= $urlmedio;
							$dcontrol->area_id 			= $usuario->trabajador->area_id;
							$dcontrol->area_nombre 		= $usuario->trabajador->area->descripcion;
							$dcontrol->usuario_nombre 	= $usuario->nombre;
							$dcontrol->tipo_archivo 	= $this->tipoarchivo;
							$dcontrol->fecha_crea 		= $this->fechaactual;
							$dcontrol->usuario_crea		= Session::get('usuario')->id;
							$dcontrol->save();

							$index 				= 	$index + 1;
						}
					}	
				}

                DB::commit();
            }catch(\Exception $ex){
                DB::rollback(); 
                $sw =   1;
                $mensaje  = $this->ge_getMensajeError($ex);
                return Redirect::to('/subir-archivos-requerimiento/'.$idopcion.'/'.$idregistro)->with('errorbd', $mensaje);

            }

 			return Redirect::to('/subir-archivos-requerimiento/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Archivos '.$registro->nombre_razonsocial.' registrados con éxito');


		}else{

			$registro 			= 	Requerimiento::where('id', $registro_id)->first();
			$listaarchivos 		= 	Archivo::where('referencia_id','=',$registro->id)
										->where('tipo_archivo','=',$this->tipoarchivo)
										->where('activo','=','1')->get();
			// dd($listaarchivos);						
	        return View::make($this->rutaview.'/archivos', 
	        				[
	        					'registro'  		=> 	$registro,
	        					'listaarchivos'  	=> 	$listaarchivos,
					  			'idopcion' 			=> 	$idopcion,
					  			'idregistro' 			=> 	$idregistro,
					  			'view'            	=>  $this->rutaviewblade,
								'url'             	=>  $this->urlopciones,
								'ruta'            	=>  $this->ruta,
								'idmodal'         	=>  $this->idmodal,
								'opciones'        	=>  $this->opciones,
								'unidad'			=> 	$this->unidadmb,
	        				]);
		}
	}

	public function actionEliminarArchivosProduccion($idopcion,$idregistro,$idarchivo)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idarchivo);
		$user_id    = Session::get('usuario')->id;

	    View::share('titulo','Eliminar Archivos del Requerimiento');

		

	  	try{
            DB::beginTransaction();
            $archivo 				= 	Archivo::where('id','=',$registro_id)->first();
            $archivo->activo 		=	0;
            $archivo->usuario_mod 	=	$user_id;
            $archivo->fecha_mod 	=	$this->fechaactual;
            $storagePath  			= storage_path('app\\'.$this->pathFiles.$archivo->lote.'\\'.$archivo->nombre_archivo);
			if(is_file($storagePath))
			{		
				unlink($storagePath);
			}

            $archivo->save();

			

            DB::commit();
        }catch(\Exception $ex){
            DB::rollback(); 
            $sw =   1;
            $mensaje  = $this->ge_getMensajeError($ex);
            return Redirect::to('/subir-archivos-requerimiento/'.$idopcion.'/'.$idregistro)->with('errorbd', $mensaje);

        }
		return Redirect::to('/subir-archivos-requerimiento/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Archivo '.$archivo->nombre_archivo.' eliminado con éxito');

	}

	public function actionDescargarArchivosProduccion($idopcion,$idregistro,$idarchivo)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idarchivo);
		$user_id    = Session::get('usuario')->id;

	    View::share('titulo','Eliminar Archivos del Requerimiento');

		

	  	try{
            // DB::beginTransaction();
            $archivo 				= 	Archivo::where('id','=',$registro_id)->first();
            $storagePath  			= storage_path('app\\'.$this->pathFiles.$archivo->lote.'\\'.$archivo->nombre_archivo);
			if(is_file($storagePath))
			{		
					// return Response::download($rutaArchivo);
					return response()->download($storagePath);
			}
 			
            // DB::commit();
        }catch(\Exception $ex){
            // DB::rollback(); 
            $sw =   1;
            $mensaje  = $this->ge_getMensajeError($ex);
        	dd('archivo no encontrado');

        }
		
	}

	public function actionModificarProduccion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $registro_id = $this->funciones->decodificarmaestra($idregistro);
	    View::share('titulo','Modificar Requerimiento');

		if($_POST)
		{
			$requerimiento = 	Requerimiento::where('id', $registro_id)->first();

            if($requerimiento->estado_id=='1CIX00000004'){
                    return Redirect::back()->withInput()->with('errorbd', 'No se puede modificar porque ya se encuentra en estado emitido');
            }    

			$cliente_id 	 					= 	$request['cliente_id'];
			$descripcion 	 					= 	$request['descripcion'];
			$moneda_id 	 						= 	$request['moneda_id'];
			$cliente 							= 	Cliente::where('id','=',$cliente_id)->first();
			$cabecera            	 			=	Requerimiento::find($registro_id);
			$cabecera->cliente_id 				=   $cliente->id;
			$cabecera->moneda_id 				=   $moneda_id;
			$cabecera->cliente_nombre 			=   $cliente->nombre_razonsocial;
			$cabecera->descripcion 				=   $descripcion;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();
 			return Redirect::to('/'.$this->urlcompleto.'/'.$idopcion)->with('bienhecho', 'Requerimiento '.$cliente->nombre_razonsocial.' modificado con éxito');

		}else{

			$requerimiento 				= 	Requerimiento::where('id', $registro_id)->first();
		    $combo_cliente 				=	$this->con_combo_clientes();
		    $select_cliente  			=	$requerimiento->cliente_id;
			$combo_moneda 				=	$this->con_combo_monedas();
		    $select_moneda  			=	$requerimiento->moneda_id;

	        return View::make($this->rutaview.'/modificar', 
	        				[
	        					'registro'  			=> $requerimiento,
	        					'combo_cliente'  		=> $combo_cliente,
		        				'select_cliente' 		=> $select_cliente,
		        				'combo_moneda'  		=> $combo_moneda,
		        				'select_moneda' 		=> $select_moneda,
					  			'idopcion' 				=> $idopcion,
						  		'view'            		=>  $this->rutaviewblade,
								'url'             		=>  $this->urlopciones,
								'ruta'            		=>  $this->ruta,
								'idmodal'         		=>  $this->idmodal,
								'opciones'        		=>  $this->opciones,
	        				]);
		}
	}

	public function actionEliminarProduccion($idopcion,$idregistro,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
	    if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $sregistro_id = $idregistro;
        $registro_id = $this->funciones->decodificarmaestra($idregistro);
        $titulo 	=	'Extornar  Produccion';
        View::share('titulo','Extornar  Produccion');


        if($_POST)
        {
            

            $requerimiento = Produccion::where('id','=',$registro_id)->first();
            $idgenerado = $this->getIdEstado('GENERADO');
            $idextornado = $this->getIdEstado('EXTORNADO');
            $generado   = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','GENERADO')->first();
            $extornado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EXTORNADO')->first();
            

            if($requerimiento->estado_id!==$generado->id){
                return Redirect::to('/extornar-produccion/'.$idopcion.'/'.$idregistro)->with('errorbd','La produccion debe estar en estado GENERADO para poder EXTORNARLO');
            }

            $descripcion                	= $request['descripcion'];
            $requerimiento->activo         	=  0;
            $requerimiento->estado_id      	=  $idextornado;
            $requerimiento->usuario_mod     =  Session::get('usuario')->id;
            $requerimiento->fecha_mod      	=  date('Y-m-d H:i:s');
			
            LogExtornar::where('tabla','produccions')
            			->where('activo',1)
            			->where('idtabla',$registro_id)
            			->update(
            				[
            					'activo'		=>	0,
            					'usuario_mod'	=>	Session::get('usuario')->id,
            					'fecha_mod'		=>	$this->fechaactual
            				]
            			);

			$cabecera            	 		=	new LogExtornar;
			$cabecera->idtabla 				=   $registro_id;
			$cabecera->descripcion 			=	$descripcion;
			$cabecera->tabla				=   'produccions';
			$cabecera->fecha_crea 	 		=   $this->fechaactual;
			$cabecera->usuario_crea 		=   Session::get('usuario')->id;
			$cabecera->save();
            $requerimiento->save();

            return Redirect::to('/'.$this->urlcompleto.'/'.$idopcion)->with('bienhecho', 'Produccion Lote: '.$requerimiento->lote.' EXTORNADA con EXITO');
        
        }
        else{
            $cotizacion             =   Produccion::where('id', $registro_id)->first();
            return View::make('produccion/extornar', 
                            [
                                'cotizacion'   	=>  $cotizacion,
                                'idopcion'     	=>  $idopcion,
                                'idregistro'   	=>  $idregistro,
                                'titulo' 		=>	$titulo,
                            ]);
        }
	}

	public function actionEmitirProduccion($idopcion,Request $request)
	{

		if($_POST)
		{
			$msjarray  			= array();
			$respuesta 			= json_decode($request['pedido'], true);
	        $conts   			= 0;
	        $contw				= 0;
			$contd				= 0;

			foreach($respuesta as $obj){
	    		$pedido_id 					= $this->funciones->decodificarmaestra($obj['id']);
				$pedido 					=   Produccion::where('id','=',$pedido_id)->first();
	    		// dd($pedido_id);

			    if($pedido->estado_id == $this->generado->id){ 

				    $pedido->estado_id 				 		= 	$this->emitido->id;
				    $pedido->estado_descripcion 			= 	$this->emitido->descripcion;
					$pedido->fecha_emision 	 				=   $this->fechaactual;
					$pedido->usuario_emision 				=   Session::get('usuario')->id;
   					$pedido->save();


			    	$msjarray[] 							= 	array(	"data_0" => $pedido->lote, 
			    														"data_1" => 'Produccion Emitido', 
			    														"tipo" => 'S');
					$conts 									= 	$conts + 1;
					$codigo 								= 	$pedido->lote;

			    }else{
					/**** ERROR DE PROGRMACION O SINTAXIS ****/
					$msjarray[] = array("data_0" => $pedido->lote, 
										"data_1" => 'este pedido esta autorizado', 
										"tipo" => 'D');
					$contd 		= 	$contd + 1;

			    }

			}


			/************** MENSAJES DEL DETALLE PEDIDO  ******************/
	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'Requerimiento Emitido', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'Requerimiento', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'Requerimiento errados', 
	    						"tipo" => 'TD');

			$msjjson = json_encode($msjarray);


			return Redirect::to('/'.$this->urlcompleto.'/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}
	}


}
