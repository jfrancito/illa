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
use App\Modelos\Requerimiento;
// use App\Modelos\Modelo as ModeloCotizacion;
use App\Modelos\Margenes;

use App\Modelos\CategoriaServicio;
use App\Modelos\LogExtornar;
use App\Modelos\LogEmision;
use App\Modelos\LogAprobacion;

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
use SplFileInfo;

class EvaluarRequerimientoController extends Controller
{
    //
    use GeneralesTraits;
    use CotizacionTraits;
    use ConfiguracionTraits;
    private   $tipoarchivo      = 'requerimiento';

    public function actionListarCotizacionesEmititas($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Lista de Cotizaciones para Evaluar');
        $idestados          =   Categoria::where('tipo_categoria','ESTADO_GENERAL')->whereIn('DESCRIPCION',['GENERADO'])->pluck('id')->toArray();
        $listacotizaciones  =   $this->cot_lista_cotizaciones($idestados);
        $funcion            =   $this;

        return View::make('evaluarrequerimiento/listacotizaciones',
                         [
                            'listacotizaciones'     => $listacotizaciones,
                            'funcion'               => $funcion,
                            'idopcion'              => $idopcion,                           
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
        $detallecot        =   DetalleCotizacion::where('cotizacion_id',$cotizacion_id)->first();

        $descripcion    =   (count($detallecot))? $detallecot->descripcion:'';
        $cotizacion                         =   Cotizacion::where('id', $cotizacion_id)->first();
        $combo_unidad_medida                =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
        $select_unidad_medida               =   '';
        $combo_categoria_servicio           =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
        $combo_tipocategoria                =   $this->gn_combo_tipocategoria();

        $select_categoria_servicio          =   '';
        $cotizaciondetalle_id               =   '';
        
        // dd($grupomodelo1);// $grupomodelo1                       =   ['id'=>'ssss'];

        return View::make('evaluarrequerimiento/modal/ajax/mconfiguracioncotizacion',
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
                            'descripcion'               =>  $descripcion,
                            'swmodificar'               =>  true,
                         ]);
    }

    public function actionEvaluarCotizacion($idopcion,$idcotizacion,Request $request)
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
                    return Redirect::to('/cotizar-evaluar-requerimiento/'.$idopcion.'/'.$sidcotizacion)->with('errorbd', 'Ya existe un Servicio con Codigo :'.$codigo);
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
                    return Redirect::to('/cotizar-evaluar-requerimiento/'.$idopcion.'/'.$sidcotizacion)->with('errorbd', 'Ya existe un Servicio con Codigo : ['.$codigo.'] ');
                }

                $detallecotizacion                          =   DetalleCotizacion::where('id', $cotizaciondetalle_id)->first();
                $detallecotizacion->descripcion             =   $servicio;
                
                $detallecotizacion->cantidad                  =   $cantidad;
                $detallecotizacion->precio_unitario         =   0;
                $detallecotizacion->total                   =   0;
                $detallecotizacion->fecha_mod               =   $this->fechaactual;
                $detallecotizacion->usuario_mod             =   Session::get('usuario')->id;
                $detallecotizacion->save();
            }

            return Redirect::to('/cotizar-evaluar-requerimiento/'.$idopcion.'/'.$sidcotizacion)->with('bienhecho', 'Servicio '.$servicio.' agregada con éxito');

        }else{
            // dd('ss');
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

            return View::make('evaluarrequerimiento/ventacotizar', 
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
                                'url'                       =>  'evaluar-requerimiento',
                                'view'                      =>  'requerimiento',
                                // 'swmodificar'               =>  false,

                                'ajax'                       =>true,
                            ]);
        }
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

        return View::make('evaluarrequerimiento/modal/ajax/mconfiguracioncotizacion',
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
                            'swmodificar'               =>  false,
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

        $funcion                    =   $this;
        return View::make('evaluarrequerimiento/form/fanalizar',
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

    public function actionAgregarEvProductoAnalisis(Request $request)
    {

        $grupoanalisis_id                           =   $request['grupoanalisis_id'];
        $unidadmedidaa_id                           =   $request['unidadmedidaa_id'];
        $descripcion                                =   $request['descripcion'];
        $cantidad                                   =   $request['cantidad'];
        $precio                                     =   $request['precio'];
        $data_cotizacion_id                         =   $request['data_cotizacion_id'];
        $data_detalle_cotizacion_id                 =   $request['data_detalle_cotizacion_id'];

        $idopcion                                   =   $request['idopcion'];
        $detallecotizacion                          =   DetalleCotizacion::where('id', $data_detalle_cotizacion_id)->first();
        $cotizacion                                 =   Cotizacion::where('id', $data_cotizacion_id)->first();

        $grupoanalisis                              =   Categoria::where('id', $grupoanalisis_id)->first();
        $unidadmedida                               =   Categoria::where('id', $unidadmedidaa_id)->first();

        $iddetallecotizacionanalisis                =   $this->funciones->getCreateIdMaestra('detallecotizacionanalisis');
        $cabecera                                   =   new DetalleCotizacionAnalisis;
        $cabecera->id                               =   $iddetallecotizacionanalisis;
        $cabecera->cotizacion_id                    =   $data_cotizacion_id;
        $cabecera->detallecotizacion_id             =   $data_detalle_cotizacion_id;
        $cabecera->descripcion                      =   $descripcion;
        $cabecera->categoriaanalisis_id             =   $grupoanalisis->id;
        $cabecera->categoriaanalisis_nombre         =   $grupoanalisis->descripcion;
        $cabecera->unidadmedida_id                  =   $unidadmedida->id;
        $cabecera->unidadmedida_nombre              =   $unidadmedida->descripcion;
        $cabecera->cantidad                         =   floatval($cantidad);
        $cabecera->precio_unitario                  =   floatval($precio);
        $cabecera->total                            =   floatval($cantidad)*floatval($precio);
        $cabecera->fecha_crea                       =   $this->fechaactual;
        $cabecera->usuario_crea                     =   Session::get('usuario')->id;
        $cabecera->save();



        //generar el precio y totales   
        $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);


        $funcion                                    =   $this;

        $listadetalle                               =   DetalleCotizacionAnalisis::where('activo','=',1)
                                                        ->where('detallecotizacion_id','=',$detallecotizacion->id)
                                                        ->orderby('categoriaanalisis_id','asc')->get();


        return View::make('evaluarrequerimiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'cotizacion'                => $cotizacion,
                            'funcion'                   => $funcion,
                            'idopcion'                  => $idopcion,
                            'listadetalle'              => $listadetalle,
                            'ajax'                      => true,                            
                         ]);
    }

    public function actionActulizarEvTablaCotizacion(Request $request)
    {

        $cotizacion_id              =   $request['data_cotizacion_id'];
        $detalle_cotizacion_id      =   $request['data_detalle_cotizacion_id'];
        $idopcion                   =   $request['idopcion'];
        $cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
        $listadetalle               =   DetalleCotizacion::where('activo','=',1)->where('cotizacion_id','=',$cotizacion_id)
                                        ->orderby('codigo','asc')->get();
        $funcion                    =   $this;
        return View::make('evaluarrequerimiento/ajax/alistadetallecotizacion',
                         [
                            'cotizacion'                => $cotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);
    }

    public function actionEliminarTablaCotizacionAnalisis(Request $request)
    {

        $cotizacion_id                      =   $request['cotizacion_id'];
        $detalle_cotizacion_id              =   $request['detalle_cotizacion_id'];
        $detalle_cotizacion_analisis_id     =   $request['detalle_cotizacion_analisis_id'];
        $idopcion                           =   $request['idopcion'];
        $cotizacion                         =   Cotizacion::where('id', $cotizacion_id)->first();
        $detallecotizacion                  =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();

        $detalle                            =   DetalleCotizacionAnalisis::where('id','=',$detalle_cotizacion_analisis_id)->first();
        $detalle->activo                    =   0;
        $detalle->fecha_mod                 =   $this->fechaactual;
        $detalle->usuario_mod               =   Session::get('usuario')->id;
        $detalle->save();
        $funcion                            =   $this;

        //generar el precio y totales   
        $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
        $listadetalle                       =   DetalleCotizacionAnalisis::where('activo','=',1)
                                                ->where('detallecotizacion_id','=',$detallecotizacion->id)
                                                ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('evaluarrequerimiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
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
        return View::make('evaluarrequerimiento/ajax/alistadetalleanalizar',
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
        return View::make('evaluarrequerimiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }
    
    // public function actionAjaxActualizarMGUtilidadDetalleCotizacion(Request $request)
    // {
    //     $cotizacion_id              =   $request['data_cotizacion_id'];
    //     $detalle_cotizacion_id      =   $request['data_detalle_cotizacion_id'];
    //     $idopcion                   =   $request['idopcion'];
    //     $mgutil                         =   $request['mgutil'];
        
    //     $detalle                    =   DetalleCotizacion::where('id','=',$detalle_cotizacion_id)->first();
    //     $detalle->mgutilidad        =   $mgutil;
    //     $detalle->fecha_mod         =   $this->fechaactual;
    //     $detalle->usuario_mod       =   Session::get('usuario')->id;
    //     $detalle->save();
    //     $funcion                            =   $this;

    //     $cotizacion                 =   Cotizacion::where('id', $cotizacion_id)->first();
    //     $detallecotizacion          =   DetalleCotizacion::where('id', $detalle_cotizacion_id)->first();
    //     //generar el precio y totales   
    //     $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
    //     $listadetalle                       =   DetalleCotizacionAnalisis::where('activo','=',1)
    //                                             ->where('detallecotizacion_id','=',$detallecotizacion->id)
    //                                             ->orderby('categoriaanalisis_id','asc')->get();

    //     $funcion                    =   $this;
    //     return View::make('evaluarrequerimiento/ajax/alistadetalleanalizar',
    //                      [
    //                         'detallecotizacion'         => $detallecotizacion,
    //                         'listadetalle'              => $listadetalle,
    //                         'idopcion'                  => $idopcion,
    //                         'ajax'                      => true,                            
    //                      ]);

    // }

    public function actionAjaxEliminarServicioLineaCotizacion(Request $request)
    {
        $cotizacion_id              =   $request['cotizacion_id'];
        $detalle_cotizacion_id      =   $request['detalle_cotizacion_id'];
        $idopcion                   =   $request['idopcion'];
        $this->EliminarServiciosDetalle($detalle_cotizacion_id);
        $cotizacion         =   Cotizacion::find($cotizacion_id);
        $this->cot_generar_totales_cotizacion($cotizacion);
    }

    public function actionEmitirEvaluarCotizacion($idopcion,Request $request)
    {

        if($_POST)
        {
            $msjarray           = array();
            $respuesta          = json_decode($request['pedido'], true);
            $conts              = 0;
            $contw              = 0;
            $contd              = 0;
        
            foreach($respuesta as $obj){
                $pedido_id          = $this->funciones->decodificarmaestra($obj['id']);
                $pedido             =   Cotizacion::where('id','=',$pedido_id)->first();
                $estado             =   Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('DESCRIPCION','EVALUADO')->first();
                if($pedido->estado_id == '1CIX00000003'){ 


                    $pedido->estado_id                      =   $estado->id;
                    $pedido->estado_descripcion             =   $estado->descripcion;

                    LogEmision::where('tabla','cotizaciones')
                                ->where('accion','evaluacion')
                                ->where('idtabla',$pedido_id)
                                ->where('activo',1)
                                ->update(
                                    [
                                        'activo'=>0,
                                        'fecha_mod'=>$this->fechaactual,
                                        'usuario_mod'=>Session::get('usuario')->id
                                    ]
                                ); 
                    $cabecera                       =   new LogEmision;
                    $cabecera->idtabla              =   $estado->id;
                    $cabecera->accion              =   'evaluacion';
                    $cabecera->tabla                =   'cotizaciones';

                    $cabecera->fecha_crea           =   $this->fechaactual;
                    $cabecera->usuario_crea         =   Session::get('usuario')->id;
                    $cabecera->save();                   

                    // $pedido->fecha_emision                  =   $this->fechaactual;
                    // $pedido->usuario_emision                =   Session::get('usuario')->id;
                    
                    $pedido->save();
                    $msjarray[]                             =   array(  "data_0" => $pedido->lote, 
                                                                        "data_1" => 'Requerimiento Emitido', 
                                                                        "tipo" => 'S');
                    $conts                                  =   $conts + 1;
                    $codigo                                 =   $pedido->lote;

                }else{
                    /**** ERROR DE PROGRMACION O SINTAXIS ****/
                    $msjarray[] = array("data_0" => $pedido->lote, 
                                        "data_1" => 'este pedido esta autorizado', 
                                        "tipo" => 'D');
                    $contd      =   $contd + 1;

                }

            }


            /************** MENSAJES DEL DETALLE PEDIDO  ******************/
            $msjarray[] = array("data_0" => $conts, 
                                "data_1" => 'Cotizacion Evaluada', 
                                "tipo" => 'TS');

            $msjarray[] = array("data_0" => $contw, 
                                "data_1" => 'Cotizacion', 
                                "tipo" => 'TW');     

            $msjarray[] = array("data_0" => $contd, 
                                "data_1" => 'Cotizaciones errados', 
                                "tipo" => 'TD');

            $msjjson = json_encode($msjarray);


            return Redirect::to('/gestion-evaluar-requerimiento/'.$idopcion)->with('xmlmsj', $msjjson);

        
        }
    }

 


     public function actionExtornarCotizacionEvaluarRequerimiento($idopcion,$idregistro,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $sregistro_id = $idregistro;
        $registro_id = $this->funciones->decodificarmaestra($idregistro);
        View::share('titulo','Extornar  Cotizacion');


        if($_POST)
        {
            
            $cotizacion = Cotizacion::where('id','=',$registro_id)->first();
            $idgenerado = $this->getIdEstado('GENERADO');
            $generado   = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','GENERADO')->first();
            $extornado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EXTORNADO')->first();
            $idextornado = $this->getIdEstado('EXTORNADO');
            if($cotizacion->estado_id!==$generado->id){
                return Redirect::to('/extornar-evaluar-requerimiento/'.$idopcion.'/'.$idregistro)->with('errorbd','La Cotizacion debe estar en estado GENERADO para poder EXTORNARLA');

            }

            $requerimiento                      =   Requerimiento::where('lote','=',$cotizacion->lote)->where('activo',1)->first();
            $requerimiento->estado_id           =   $generado->id;
            $requerimiento->estado_descripcion  =   $generado->descripcion;
            $requerimiento->usuario_mod         =   Session::get('usuario')->id;
            $requerimiento->fecha_mod           =   date('Ymd H:i:s');
            $requerimiento->save();

            $descripcion                        =   $request['descripcion'];
            $cotizacion->activo                 =   0;
            $cotizacion->estado_id              =   $extornado->id;
            $cotizacion->estado_descripcion     =   $extornado->descripcion;
            
            LogExtornar::where('tabla','cotizaciones')
                                ->where('accion','evaluacion')
                                ->where('idtabla',$registro_id)
                                ->where('activo',1)
                                ->update(
                                    [
                                        'activo'=>0,
                                        'fecha_mod'=>$this->fechaactual,
                                        'usuario_mod'=>Session::get('usuario')->id
                                    ]
                                );   

            $cabecera                       =   new LogExtornar;
            $cabecera->idtabla              =   $registro_id;
            $cabecera->descripcion          =   $descripcion;
            $cabecera->accion               =   'evaluacion';
            $cabecera->tabla                =   'cotizaciones';
            $cabecera->fecha_crea           =   $this->fechaactual;
            $cabecera->usuario_crea         =   Session::get('usuario')->id;
            $cabecera->save();          

            // $cotizacion->fecha_extornarevaluacion        =  date('Y-m-d H:i:s');
            // $cotizacion->descripcion_extornarevaluacion  =  $descripcion;
            // $cotizacion->usuario_extornarevaluacion      =  Session::get('usuario')->id;

            $cotizacion->save();

            return Redirect::to('/gestion-evaluar-requerimiento/'.$idopcion)->with('bienhecho', 'Cotizacion Lote: '.$cotizacion->lote.' EXTORNADA con EXITO');
        
        }
        else{


            $cotizacion             =   Cotizacion::where('id', $registro_id)->first();
           
            // dd($listaarchivos);                      
            return View::make('evaluarrequerimiento/extornar', 
                            [
                                'cotizacion'          =>  $cotizacion,
                                'idopcion'          =>  $idopcion,
                                'idregistro'            =>  $idregistro,
                            ]);
            // return Redirect::to('/extornar-evaluar-requerimiento/'.$idopcion.'/'.$idregistro)->with('xmlmsj', $msjjson);

        }
    }

    public function actionSubirArchivosEvaluarRequerimiento($idopcion,$idregistro,Request $request)
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
                return Redirect::to('/cotizar-evaluar-requerimiento/'.$idopcion.'/'.$idregistro)
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
                return Redirect::to('/cotizar-evaluar-requerimiento/'.$idopcion.'/'.$idregistro)->with('errorbd', $mensaje);

            }

            return Redirect::to('/cotizar-evaluar-requerimiento/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Archivos '.$registro->nombre_razonsocial.' registrados con éxito');

        }
        // }else{

        //     $registro           =   Requerimiento::where('id', $registro_id)->first();
        //     $listaarchivos      =   Archivo::where('referencia_id','=',$registro->id)
        //                                 ->where('tipo_archivo','=',$this->tipoarchivo)
        //                                 ->where('activo','=','1')->get();
        //     // dd($listaarchivos);                      
        //     return View::make($this->rutaview.'/archivos', 
        //                     [
        //                         'registro'          =>  $registro,
        //                         'listaarchivos'     =>  $listaarchivos,
        //                         'idopcion'          =>  $idopcion,
        //                         'idregistro'            =>  $idregistro,
        //                         'view'              =>  $this->rutaviewblade,
        //                         'url'               =>  'evaluar-requerimiento',
        //                         'ruta'              =>  'evaluarrequerimiento',
        //                         'idmodal'           =>  'mevaluarrequerimiento',
        //                         // 'opciones'          =>  $this->opciones,
        //                         'unidad'            =>  $this->unidadmb,
        //                     ]);
        // }
    }

    public function actionDescargarArchivosEvaluarRequerimiento($idopcion,$idregistro,$idarchivo)
        {

            /******************* validar url **********************/
            $validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
            if($validarurl <> 'true'){return $validarurl;}
            /******************************************************/
            $registro_id = $this->funciones->decodificarmaestra($idarchivo);
            $user_id    = Session::get('usuario')->id;

            // View::share('titulo','Eliminar Archivos del Requerimiento');

            

            try{
                // DB::beginTransaction();
                $archivo                =   Archivo::where('id','=',$registro_id)->first();
                $storagePath            = storage_path('app\\'.$this->pathFiles.$archivo->lote.'\\'.$archivo->nombre_archivo);
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
}
