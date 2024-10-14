<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\Requerimiento;
use App\Modelos\Conei;
use App\Modelos\Certificado;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait CertificadoTraits
{

	private function con_lista_certificados() {

		$listadatos 	= 	Certificado::where('activo','=','1')->orderby('id','desc')->get();
	 	return  $listadatos;

	}

	private function con_lista_certificados_xfiltro($periodo_id,$procedencia_id) {

		$listadatos 	= 	Certificado::where('activo','=','1')
							->Periodo($periodo_id)
							->Procedencia($procedencia_id)
							->orderby('fecha_crea','desc')->get();
	 	return  $listadatos;

	}

	private function con_lista_certificados_xperiodo($periodo_id,$procedencia_id) {

		$listadatos 	= 	Certificado::where('activo','=','1')
							->Periodo($periodo_id)
							->Procedencia($procedencia_id)
							->select(DB::raw('count(periodo_id) as cantidad,periodo_id'))
							->groupby('periodo_id')
							->orderby('periodo_id','desc')
							->get();
	 	return  $listadatos;

	}


	private function con_lista_certificados_xprocedencia($periodo_id,$procedencia_id) {

		$listadatos 	= 	Certificado::where('activo','=','1')
							->Periodo($periodo_id)
							->Procedencia($procedencia_id)
							->select(DB::raw('count(procedente_id) as cantidad,procedente_id'))
							->groupby('procedente_id')
							->orderby('procedente_id','desc')
							->get();
	 	return  $listadatos;

	}



}