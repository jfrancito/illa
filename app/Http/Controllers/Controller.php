<?php

namespace App\Http\Controllers;
use App\Biblioteca\Funcion;
use App\Modelos\Margenes;
use App\Modelos\Categoria;
use App\Modelos\Moneda;

use DateTime;
use Hashids;
use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public $funciones;


	public $anio;
	public $mes;
	public $dia;


	public $inicio;
	public $fin;
	public $hoy;
	public $prefijomaestro;
	public $fechaactual;
	public $fecha_sin_hora;
	public $maxsize;
	public $unidadmb;
	public $igv;
	public $mgadmin;
	public $mgutil;
	public $generado;
	public $apronado;
	public $emitido;
	public $evaluado;
	public $extornado;
	public $pathFiles='requerimiento/';
	public $pathFilesCer='certificado_conei/';
	public $monedaxdefecto='';

	public function __construct() {
		$this->funciones 		= new Funcion();
		$this->unidadmb 		= 2;
		$anio = date("Y");
		$mes = date("n");
		$dia = date("d");
		$this->anio 			= $anio;
		$this->mes 				= $mes;
		$this->dia 				= $dia;
		$this->maxsize 			= pow(1024,$this->unidadmb)*20;
		$fecha 					= new DateTime();
		$fecha->modify('first day of this month');
		$this->inicio 			= date_format(date_create($fecha->format('Y-m-d')), 'd-m-Y');
		$this->fin 				= date_format(date_create(date('Y-m-d')), 'd-m-Y');

		$this->prefijomaestro 	= $this->funciones->prefijomaestra();
		$this->fechaactual 		= date('Ymd H:i:s');
		$this->hoy 				= date_format(date_create(date('Ymd h:i:s')), 'Ymd h:i:s');
		$this->fecha_sin_hora 	= date('d-m-Y');

		$this->igv 		= 	Margenes::where('codigo','=','IGV')->where('activo','=',1)->first()->importe;
		$this->mgadmin 	=	Margenes::where('codigo','=','MGADMIN')->where('activo','=',1)->first()->importe;
		$this->mgutil 	=	Margenes::where('codigo','=','MGUTIL')->where('activo','=',1)->first()->importe;
		$this->generado =	Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','GENERADO')->first();
		$this->aprobado =	Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','APROBADO')->first();
		$this->emitido 	=	Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EMITIDO')->first();
		$this->evaluado	=	Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EVALUADO')->first();
		$this->extornado=	Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EXTORNADO')->first();
		$moneda_consulta		=	Moneda::where('codigo','=','USD')->first();
		$this->monedaxdefecto	=	($moneda_consulta)?$moneda_consulta->id:null;

	}



	public function getPermisosOpciones($idopcion,$idusuario)
	{

		//decodificar variable
	  	$decidopcion = Hashids::decode($idopcion);
	  	
	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($decidopcion[0], 8, "0", STR_PAD_LEFT); 
	  	//concatenar prefijo

	  	$idopcioncompleta = $this->funciones->prefijomaestra().$idopcioncompleta;

	  	// ver si la opcion existe
	  	$opcion =  DB::table('rolopciones as RO')
	  					->join('rols as R','RO.rol_id','=','R.id')
	  					->join('users as U','U.rol_id','=','R.id')
	  					->where('U.id','=',$idusuario)
	  					->where('RO.opcion_id','=',$idopcioncompleta)
	  					->select(
	  						'RO.ver',
	  						'RO.anadir',
	  						'RO.modificar',
	  						'RO.eliminar',
	  						'RO.todas',
	  						'RO.*'
	  					)
	  					->first();
	  	// dd($opcion);
	  	if((count($opcion)>0) && !empty($opcion))
	  	{
	  		$permisosopciones['ver'] 		= $opcion->ver;
	  		$permisosopciones['anadir'] 	= $opcion->anadir;
	  		$permisosopciones['modificar'] 	= $opcion->modificar;
	  		$permisosopciones['eliminar'] 	= $opcion->eliminar;
	  		$permisosopciones['todas'] 		= $opcion->todas;
	  	}
		// $opciones= P
		return $permisosopciones;
	}



}
