<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Traits\GeneralesTraits;
use App\Traits\ReporteTraits;
use App\Traits\CertificadoTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;


class ReporteCertificadoController extends Controller
{

	use GeneralesTraits;
	use ReporteTraits;
    use CertificadoTraits;

	public function actionListarCertificadosInstituciones($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Reporte de certificados de instituciones');
	    $sel_periodo 			=	'';
	    $anio  					=   $this->anio;
        $comboperiodo       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','TODO','APAFA_CONEI_PERIODO');
        $selectperiodo      	=   'TODO';
        $comboprocedencia   	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','TODO','APAFA_CONEI');
        $selectprocedencia  	=   'TODO';
        $listadatos     		=   $this->con_lista_certificados_xfiltro($selectperiodo,$selectprocedencia);
        $listacertificadoperiod =   $this->con_lista_certificados_xperiodo($selectperiodo,$selectprocedencia);
        $listacertificadoproced =   $this->con_lista_certificados_xprocedencia($selectperiodo,$selectprocedencia);

        //dd($listacertificadoperiod);
		$funcion 				= 	$this;
		
		return View::make('reportes/listacertificadoinstitucion',
						 [
						 	'listadatos' 			=> $listadatos,
						 	'listacertificadoperiod'=> $listacertificadoperiod,
						 	'listacertificadoproced'=> $listacertificadoproced,
						 	'comboperiodo'			=> $comboperiodo,
						 	'selectperiodo'			=> $selectperiodo,
						 	'comboprocedencia'		=> $comboprocedencia,
						 	'selectprocedencia'	 	=> $selectprocedencia,

						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarInstitucionCertificado(Request $request)
	{

		$periodo_id 			=   $request['periodo_id'];
		$procedencia_id 		=   $request['procedencia_id'];
		$idopcion 				=   $request['idopcion'];


        $comboperiodo       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','TODO','APAFA_CONEI_PERIODO');
        $selectperiodo      	=   $periodo_id;
        $comboprocedencia   	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','TODO','APAFA_CONEI');
        $selectprocedencia  	=   $procedencia_id;


        $listadatos     		=   $this->con_lista_certificados_xfiltro($selectperiodo,$selectprocedencia);

        $listacertificadoperiod =   $this->con_lista_certificados_xperiodo($selectperiodo,$selectprocedencia);
        $listacertificadoproced =   $this->con_lista_certificados_xprocedencia($selectperiodo,$selectprocedencia);

        //dd($listacertificadoperiod);
		$funcion 				= 	$this;
		
		return View::make('reportes/ajax/alistacertificadoinstitucion',
						 [

						 	'listadatos' 			=> $listadatos,
						 	'listacertificadoperiod'=> $listacertificadoperiod,
						 	'listacertificadoproced'=> $listacertificadoproced,
						 	'funcion'				=> $funcion,	
						 	'idopcion'				=> $idopcion,		 	
						 	'ajax' 					=> true,						 	
						 ]);
	}

}
