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

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\CotizacionTraits;
use App\Traits\PlaneamientoTraits;
use App\Traits\ConfiguracionTraits;



use PDF;
use ZipArchive;
use Hashids;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use TPDF;
use Storage;
use SplFileInfo;


class PlaneamientoController extends Controller {

    use GeneralesTraits;
    use CotizacionTraits;
    use PlaneamientoTraits;
    use ConfiguracionTraits;
    
    private   $tipoarchivo      = 'requerimiento';

    public function actionListarPlaneamiento($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Listar Planeamiento');
        // $estado      =   Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('DESCRIPCION','EVALUADO')->first();
        $idestados          =   Categoria::where('tipo_categoria','ESTADO_GENERAL')->whereIn('DESCRIPCION',['GENERADO','EVALUADO','EMITIDO','APROBADO'])->pluck('id')->toArray();
        
        $idgenerado     = $this->getIdEstado('GENERADO');
        $idemitido      = $this->getIdEstado('EMITIDO');
        // $idextornado    = $this->getIdEstado('GENERADO');
        $idaprobado     = $this->getIdEstado('APROBADO');

        $colores    =   [
                            $idgenerado  =>  ['color'=>'badge-light','nivel'=>0],
                            $idemitido   =>  ['color'=>'badge-primary','nivel'=>1],
                            $idaprobado  =>  ['color'=>'badge-success','nivel'=>2],
                        ];

        // $listacotizaciones  =   $this->cot_lista_cotizaciones($idestados);
        
        $listacotizaciones  =   Planeamiento::where('activo',1)
                                    ->whereIn('estado_id',$idestados)
                                    ->select('*')
                                    ->selectRaw(" '' as classcolor, 0 as nivel")
                                    ->get();
        foreach ($listacotizaciones as $index => $cotizacion) {
            $cotizacion->classcolor = $colores[$cotizacion->estado_id]['color'];
            $cotizacion->nivel = $colores[$cotizacion->estado_id]['nivel'];
        }

        $funcion            =   $this;
        
        $idgenerado         =   $this->getIdEstado('GENERADO');
        $idemitido          =   $this->getIdEstado('EMITIDO');

        return View::make('planeamiento/listacotizaciones',
                         [
                            'listacotizaciones'     =>  $listacotizaciones,
                            'funcion'               =>  $funcion,
                            'idopcion'              =>  $idopcion,                           
                            'idgenerado'            =>  $idgenerado,
                            'idemitido'             =>  $idemitido,
                            'idaprobado'            =>  $idaprobado,
                            'titulo'                =>  'Planeamientos',
                         ]);
    }

    public function actionConfigurarDetallePlaneamiento(Request $request)
    {

        $planeamiento_id            =   $request['planeamiento_id'];
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
            $registro   =   DetallePlaneamiento::where('id','=',$registrocategoria_id)->first();
            $nivel      =   (int)($registro->nivel)+1;

            // $numero  =   ((int)DetallePlaneamiento::where('idpadre','=',$registrocategoria_id)->where('nivel','=',$nivel)->where('activo','=',1)->max('codigo')) + 1;
            $numero     =   ((int) substr(DetallePlaneamiento::where('idpadre','=',$registrocategoria_id)->where('nivel','=',$nivel)->where('activo','=',1)->max('codigo'),-2)) + 1;

            // dd(DetallePlaneamiento::where('idpadre','=',$registrocategoria_id)->where('nivel','=',$nivel)->where('activo','=',1)->max('codigo'));
            $codigosig  =   str_pad($numero, 2, "0", STR_PAD_LEFT);
            // dd($numero);
            $codigo     =   $registro->codigo.'.'.$codigosig;
            $idpadre    =   $registro->id;
            $nivel      =   (int)$registro->nivel+1;
        }
        else{
            $numero     =   ((int)DetallePlaneamiento::where('planeamiento_id','=',$planeamiento_id)->where('activo','=',1)->max('codigo')) + 1;
            $codigo     =   str_pad($numero, 2, "0", STR_PAD_LEFT);
        }

        $ldescripcion   =   DetallePlaneamiento::where('activo','=',1)->pluck('descripcion')->toArray();

        $cotizacion                         =   Planeamiento::where('id', $planeamiento_id)->first();
        $combo_unidad_medida                =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
        $select_unidad_medida               =   '';
        $combo_categoria_servicio           =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
        $combo_tipocategoria                =   $this->gn_combo_tipocategoria();

        $select_categoria_servicio          =   '';
        $cotizaciondetalle_id               =   '';
        
        return View::make('planeamiento/modal/ajax/mconfiguracioncotizacion',
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

    public function actionAnalizarPlaneamiento($idopcion,$idcotizacion,Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $sidcotizacion = $idcotizacion;
        $idcotizacion = $this->funciones->decodificarmaestra($idcotizacion);
        View::share('titulo','Analisis Planeamiento');

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

            $cotizacion                                     =   Planeamiento::where('id', $idcotizacion)->first();
            $gruposervicio                                  =   Categoria::where('id', $gruposervicio_id)->first();

            if(trim($cotizaciondetalle_id)==''){
                
                $validarcodigog = DetallePlaneamiento::where('planeamiento_id','=',$idcotizacion)->where('activo','=',1)->where('codigo','=',$codigo)->first();
                if(!empty($validarcodigog) && count($validarcodigog)>0){
                    return Redirect::to('/analizar-planeamiento/'.$idopcion.'/'.$sidcotizacion)->with('errorbd', 'Ya existe un Servicio con Codigo :'.$codigo);
                }

                $iddetallecotizacion                        =   $this->funciones->getCreateIdMaestra('detalleplaneamientos');
                $cabecera                                   =   new DetallePlaneamiento;
                $cabecera->id                               =   $iddetallecotizacion;
                $cabecera->planeamiento_id                  =   $idcotizacion;
                $cabecera->descripcion                      =   $servicio;

                $cabecera->ispadre                          =   $ispadre;
                $cabecera->idpadre                          =   $idpadre;
                $nivel = 1;

                if(!is_null($idpadre)){
                    $nivel  = (int) DetallePlaneamiento::where('id','=',$idpadre)->first()->nivel+1;
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
                $validarcodigog = DetallePlaneamiento::where('planeamiento_id','=',$idcotizacion)->where('activo','=',1)->where('codigo','=',$codigo)->where('id','<>',$cotizaciondetalle_id)->first();
                if(!empty($validarcodigog) && count($validarcodigog)>0){
                    return Redirect::to('/analizar-planeamiento/'.$idopcion.'/'.$sidcotizacion)->with('errorbd', 'Ya existe un Servicio con Codigo : ['.$codigo.'] ');
                }

                $detallecotizacion                          =   DetallePlaneamiento::where('id', $cotizaciondetalle_id)->first();
                $detallecotizacion->descripcion             =   $servicio;
                
                $detallecotizacion->codigo                  =   $codigo;
                $detallecotizacion->precio_unitario         =   0;
                $detallecotizacion->total                   =   0;
                $detallecotizacion->totalcantidad           =   0;
                $detallecotizacion->fecha_mod               =   $this->fechaactual;
                $detallecotizacion->usuario_mod             =   Session::get('usuario')->id;
                $detallecotizacion->save();
            }

            $this->pla_generar_totales_planeamiento($cotizacion);
            return Redirect::to('/analizar-planeamiento/'.$idopcion.'/'.$sidcotizacion)->with('bienhecho', 'Servicio '.$servicio.' agregada con éxito');

        }else{

            $cotizacion                         =   Planeamiento::where('id', $idcotizacion)->first();
            $cliente                            =   Cliente::where('id', $cotizacion->cliente_id)->first();
            $precotizacion                      =   Requerimiento::where('lote', $cotizacion->lote)->first();
            $listaimagenes                      =   Archivo::where('referencia_id','=',$precotizacion->id)
                                                    ->where('tipo_archivo','=','precotizacion')->where('activo','=','1')->get();
            // $listadetalle                        =   DetallePlaneamiento::where('activo','=',1)->where('planeamiento_id',$idcotizacion)->orderby('categoriaservicio_id','asc')->get();
            $listadetalle                       =   DetallePlaneamiento::where('activo','=',1)->where('planeamiento_id',$idcotizacion)->orderby('codigo','asc')->get();

            $listaarchivos                      =   Archivo::where('activo','=',1)->where('lote',$cotizacion->lote)->get();
            $tmusados   = (float)$listaarchivos->sum('size');
            $tmlimite   = round(($this->maxsize/(pow(1024,$this->unidadmb))),2);
            $tmusados   = round(($tmusados/(pow(1024,$this->unidadmb))),2);

            return View::make('planeamiento/ventacotizar', 
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
    }

    public function actionAjaxModalModificarConfiguracionCotizacion(Request $request)
    {
        $planeamiento_id              =   $request['planeamiento_id'];
        $detalle_planeamiento_id      =   $request['detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];
    
        $iddatocategoria            =   $request['idcategoria'];

        // $iddatocategoria             =   $request['idcategoria'];
        $idpadre                    =   $request['iddatocategoria'];

        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();
        $detalle                    =   DetallePlaneamiento::where('id','=',$detalle_planeamiento_id)->first();

        $combo_unidad_medida        =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
        $select_unidad_medida       =   $detalle->unidadmedida_id;
        $combo_categoria_servicio   =   $this->con_generacion_combo('CATEGORIA_SERVICIO','Seleccione Categoria Servicio','');
        $select_categoria_servicio  =   $detalle->categoriaservicio_id;
        $cotizaciondetalle_id       =   $detalle_planeamiento_id;
        $codigo                     =   $detalle->codigo;
        $ccategoriaserv             =   $this->gn_combo_tipocategoria();
        $combo_tipocategoria        =   [$detalle->ispadre =>  $ccategoriaserv[$detalle->ispadre]];

        return View::make('planeamiento/modal/ajax/mconfiguracioncotizacion',
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

    public function actionAnalizarDetallePlaneamiento(Request $request)
    {

        $planeamiento_id            =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id    =   $request['detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];

        $detallecotizacion          =   DetallePlaneamiento::where('id', $detalle_planeamiento_id)->first();
        // dd($detallecotizacion);
        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();

        $combo_categoria_analisis   =   $this->con_generacion_combo('CATEGORIA_ANALISIS','Seleccione Categoria Analisis','');
        $select_categoria_analisis  =   '';
        $combo_unidad_medida_a      =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
        $select_unidad_medida_a     =   '';

        $listadetalle               =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                        ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                        ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('planeamiento/form/fanalizar',
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

    public function actionAgregarProductoAnalisisPlaneamiento(Request $request)
    {

        $grupoanalisis_id                           =   $request['grupoanalisis_id'];
        $unidadmedidaa_id                           =   $request['unidadmedidaa_id'];
        $descripcion                                =   $request['descripcion'];
        $cantidad                                   =   $request['cantidad'];
        $precio                                     =   $request['precio'];
        $data_planeamiento_id                       =   $request['data_planeamiento_id'];
        $data_detalle_planeamiento_id               =   $request['data_detalle_planeamiento_id'];
        $idopcion                                   =   $request['idopcion'];
        
        $detallecotizacion                          =   DetallePlaneamiento::where('id', $data_detalle_planeamiento_id)->first();
        $detallecotizacion->totalcantidad           =   $detallecotizacion->totalcantidad+$cantidad;
        $detallecotizacion->swactualizado           =   0;
        $detallecotizacion->save();

        $cotizacion                                 =   Planeamiento::where('id', $data_planeamiento_id)->first();

        $grupoanalisis                              =   Categoria::where('id', $grupoanalisis_id)->first();
        $unidadmedida                               =   Categoria::where('id', $unidadmedidaa_id)->first();

        $iddetallecotizacionanalisis                =   $this->funciones->getCreateIdMaestra('detalleplaneamientoanalisis');
        $cabecera                                   =   new DetallePlaneamientoAnalisis;
        $cabecera->id                               =   $iddetallecotizacionanalisis;
        $cabecera->planeamiento_id                  =   $data_planeamiento_id;
        $cabecera->detalleplaneamiento_id           =   $data_detalle_planeamiento_id;
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
        // $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
        $this->pla_generar_totales_detalle_planeamiento($cotizacion,$detallecotizacion);


        $funcion                                    =   $this;

        $listadetalle                               =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                                        ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                                        ->orderby('categoriaanalisis_id','asc')->get();


        return View::make('planeamiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'cotizacion'                => $cotizacion,
                            'funcion'                   => $funcion,
                            'idopcion'                  => $idopcion,
                            'listadetalle'              => $listadetalle,
                            'ajax'                      => true,                            
                         ]);
    }

    public function actionActulizarTablaPlaneamiento(Request $request)
    {

        $planeamiento_id              =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id      =   $request['data_detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];
        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();
        $listadetalle               =   DetallePlaneamiento::where('activo','=',1)->where('planeamiento_id','=',$planeamiento_id)
                                        ->orderby('codigo','asc')->get();
        $funcion                    =   $this;
        return View::make('planeamiento/ajax/alistadetallecotizacion',
                         [
                            'cotizacion'                => $cotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);
    }

    public function actionEliminarTablaPleanamientoAnalisis(Request $request)
    {

        $planeamiento_id                      =   $request['planeamiento_id'];
        $detalle_planeamiento_id              =   $request['detalle_planeamiento_id'];
        $detalle_planeamiento_analisis_id     =   $request['detalle_planeamiento_analisis_id'];
        $idopcion                           =   $request['idopcion'];
        $cotizacion                         =   Planeamiento::where('id', $planeamiento_id)->first();
        $detallecotizacion                  =   DetallePlaneamiento::where('id', $detalle_planeamiento_id)->first();
        $detallecotizacion->swactualizado   =   0;

        $detalle                            =   DetallePlaneamientoAnalisis::where('id','=',$detalle_planeamiento_analisis_id)->first();
        $detalle->activo                    =   0;
        $detalle->fecha_mod                 =   $this->fechaactual;
        $detalle->usuario_mod               =   Session::get('usuario')->id;
        $detalle->save();
        $funcion                            =   $this;

        //generar el precio y totales   
        $this->pla_generar_totales_detalle_planeamiento($cotizacion,$detallecotizacion);
        $listadetalle                       =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                                ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                                ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('planeamiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);
    }


    public function actionAjaxEliminarLineaPlaneamiento(Request $request)
    {
        $planeamiento_id            =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id    =   $request['detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];
        $detalle                    =   DetallePlaneamiento::where('id','=',$detalle_planeamiento_id)->first();
        $detalle->activo            =   0;
        $detalle->fecha_mod         =   $this->fechaactual;
        $detalle->usuario_mod       =   Session::get('usuario')->id;
        $detalle->save();
        $cotizacion                 =   Planeamiento::where('id','=',$planeamiento_id)->first();
        $this->pla_generar_totales_planeamiento($cotizacion);

    }

    public function EliminarServiciosDetallePlaneamiento($idservicio)
    {
        $registro   = DetallePlaneamiento::find($idservicio);
        $hijos      = DetallePlaneamiento::where('idpadre','=',$idservicio)->where('activo','=',1)->get();
        foreach($hijos as $index=> $hijo){
            $this->EliminarServiciosDetallePlaneamiento($hijo->id);
        }
        $registro->activo           =   0;
        $registro->swactualizado    =   0;
        $registro->fecha_mod        =   $this->fechaactual;
        $registro->usuario_mod      =   Session::get('usuario')->id;
        $registro->save();
    }

    public function actionAjaxEliminarIgvDetalleCotizacion(Request $request)
    {
        $planeamiento_id              =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id      =   $request['data_detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];
        $swigv                      =   $request['swigv'];
        
        $detalle                    =   DetallePlaneamiento::where('id','=',$detalle_planeamiento_id)->first();
        $detalle->swigv             =   $swigv;
        $detalle->swactualizado     =   0;
        // $detalle->igv                =   0;
        $detalle->fecha_mod         =   $this->fechaactual;
        $detalle->usuario_mod       =   Session::get('usuario')->id;
        $detalle->save();

        $funcion                            =   $this;

        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();
        $detallecotizacion          =   DetallePlaneamiento::where('id', $planeamiento_id)->first();
        //generar el precio y totales   
        $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
        $listadetalle                       =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                                ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                                ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('planeamiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }

    public function actionAjaxActualizarMGAdministrativoDetalleCotizacion(Request $request)
    {
        $planeamiento_id              =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id      =   $request['data_detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];
        $mgadmin                        =   $request['mgadmin'];
        
        $detalle                    =   DetallePlaneamiento::where('id','=',$detalle_planeamiento_id)->first();
        $detalle->mgadministrativos =   $mgadmin;
        $detalle->swactualizado     =   0;
        $detalle->fecha_mod         =   $this->fechaactual;
        $detalle->usuario_mod       =   Session::get('usuario')->id;
        $detalle->save();

        $funcion                            =   $this;

        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();
        $detallecotizacion          =   DetallePlaneamiento::where('id', $planeamiento_id)->first();
        //generar el precio y totales   
        $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
        $listadetalle                       =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                                ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                                ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('planeamiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }
    
    public function actionAjaxActualizarMGUtilidadDetalleCotizacion(Request $request)
    {
        $planeamiento_id              =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id      =   $request['data_detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];
        $mgutil                         =   $request['mgutil'];
        
        $detalle                    =   DetallePlaneamiento::where('id','=',$detalle_planeamiento_id)->first();
        $detalle->mgutilidad        =   $mgutil;
        $detalle->swactualizado     =   0;
        $detalle->fecha_mod         =   $this->fechaactual;
        $detalle->usuario_mod       =   Session::get('usuario')->id;
        $detalle->save();
        $funcion                            =   $this;

        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();
        $detallecotizacion          =   DetallePlaneamiento::where('id', $planeamiento_id)->first();
        //generar el precio y totales   
        $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
        $listadetalle                       =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                                ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                                ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('planeamiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }

    public function actionAjaxActualizarPrecioVentaDetalleCotizacion(Request $request)
    {
        $planeamiento_id                  =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id          =   $request['data_detalle_planeamiento_id'];
        $idopcion                       =   $request['idopcion'];
        $totalpv                        =   $request['totalpv'];
        
        $detalle                        =   DetallePlaneamiento::where('id','=',$detalle_planeamiento_id)->first();
        // $detalle->totalpreciounitario   =   $totalpv;
        $detalle->totalpreciounitario     =   $totalpv;
        $detalle->swactualizado         =   1;
        $detalle->fecha_mod             =   $this->fechaactual;
        $detalle->usuario_mod           =   Session::get('usuario')->id;
        $detalle->save();
        $funcion                            =   $this;

        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();
        $detallecotizacion          =   DetallePlaneamiento::where('id', $planeamiento_id)->first();
        //generar el precio y totales   
        $this->cot_generar_totales_detalle_cotizacion($cotizacion,$detallecotizacion);
        $listadetalle                       =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                                ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                                ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('planeamiento/ajax/alistadetalleanalizar',
                         [
                            'detallecotizacion'         => $detallecotizacion,
                            'listadetalle'              => $listadetalle,
                            'idopcion'                  => $idopcion,
                            'ajax'                      => true,                            
                         ]);

    }
    
    public function actionAjaxEliminarServicioLineaPlaneamiento(Request $request)
    {
        $planeamiento_id                =   $request['data_planeamiento_id'];
        $detalle_planeamiento_id        =   $request['detalle_planeamiento_id'];
        $idopcion                       =   $request['idopcion'];
        $this->EliminarServiciosDetallePlaneamiento($detalle_planeamiento_id);
     // $this->EliminarServiciosDetallePlaneamiento($detalle_cotizacion_id);
        $cotizacion         =   Planeamiento::find($planeamiento_id);
        $this->pla_generar_totales_planeamiento($cotizacion);

        // $cotizacion                     =   Planeamiento::find($planeamiento_id);
        // $this->pla_generar_totales_planeamiento($cotizacion);
    }


    public function actionExtornarPlaneamiento($idopcion,$idregistro,Request $request)
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
            
            $cotizacion = Planeamiento::where('id','=',$registro_id)->first();
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
            $cotizacion             =   Planeamiento::where('id', $registro_id)->first();
            return View::make('planeamiento/extornar', 
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



    public function actionEmitirPlaneamiento($idopcion,$idregistro,Request $request)
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
            
            $cotizacion = Planeamiento::where('id','=',$registro_id)->first();

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
            $cotizacion             =   Planeamiento::where('id', $registro_id)->first();
            return View::make('planeamiento/emitir', 
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

    public function actionAprobarPlaneamiento($idopcion,$idregistro,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Eliminar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $sregistro_id = $idregistro;
        $registro_id = $this->funciones->decodificarmaestra($idregistro);
        $titulo     =   'Emitir  Cotizacion';
        View::share('titulo','Emitir  Cotizacion');


        if($_POST)
        {
            
            $cotizacion = Planeamiento::where('id','=',$registro_id)->first();
            $idgenerado = $this->getIdEstado('EVALUADO');
            $aprobado   = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','APROBADO')->first();
            $extornado  = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','GENERADO')->first();
            $emitido    = Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion','EMITIDO ')->first();
            $idextornado = $this->getIdEstado('GENERADO');
            $idemitido   = $this->getIdEstado('EMITIDO');
            // $idevaluado  = $this->getIdEstado('EVALUADO');

            if($cotizacion->estado_id!==$emitido->id){
                return Redirect::to('/aprobar-cotizacion/'.$idopcion.'/'.$idregistro)->with('errorbd','La Cotizacion debe estar en estado '.$emitido->descripcion.' para poder EXTORNARLA');
            }


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

            return Redirect::to('/gestion-de-cotizacion/'.$idopcion)->with('bienhecho', 'Cotizacion Lote: '.$cotizacion->lote.' APROBADA con EXITO');
        
        }
        else{
            $cotizacion             =   Planeamiento::where('id', $registro_id)->first();
            return View::make('planeamiento/aprobar', 
                            [
                                'cotizacion'    =>  $cotizacion,
                                'idopcion'      =>  $idopcion,
                                'idregistro'    =>  $idregistro,
                                'titulo'        =>  $titulo,
                            ]);
        }
    }


    public function actionDetallePlaneamiento($idopcion,$idcotizacion,Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $sidcotizacion = $idcotizacion;
        $idcotizacion = $this->funciones->decodificarmaestra($idcotizacion);
        View::share('titulo','Detalle Cotizar');

        $cotizacion                         =   Planeamiento::where('id', $idcotizacion)->first();
        $cliente                            =   Cliente::where('id', $cotizacion->cliente_id)->first();
        $precotizacion                      =   Requerimiento::where('lote', $cotizacion->lote)->first();
        $listaimagenes                      =   Archivo::where('referencia_id','=',$precotizacion->id)
                                                ->where('tipo_archivo','=','precotizacion')->where('activo','=','1')->get();

        $listadetalle                       =   DetallePlaneamiento::where('activo','=',1)->where('planeamiento_id',$idcotizacion)->orderby('codigo','asc')->get();

        $listaarchivos                      =   Archivo::where('activo','=',1)->where('lote',$cotizacion->lote)->get();
        $tmusados   = (float)$listaarchivos->sum('size');
        $tmlimite   = round(($this->maxsize/(pow(1024,$this->unidadmb))),2);
        $tmusados   = round(($tmusados/(pow(1024,$this->unidadmb))),2);

        return View::make('planeamiento/detallecotizar', 
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

                            // 'ajax'                       =>true,
                        ]);
    }

     public function actionDetalleAnalizarDetalleCotizacion(Request $request)
    {

        $planeamiento_id              =   $request['planeamiento_id'];
        $detalle_planeamiento_id      =   $request['detalle_planeamiento_id'];
        $idopcion                   =   $request['idopcion'];

        $detallecotizacion          =   DetallePlaneamiento::where('id', $planeamiento_id)->first();
        $cotizacion                 =   Planeamiento::where('id', $planeamiento_id)->first();
        $combo_categoria_analisis   =   $this->con_generacion_combo('CATEGORIA_ANALISIS','Seleccione Categoria Analisis','');
        $select_categoria_analisis  =   '';
        $combo_unidad_medida_a      =   $this->con_generacion_combo('UNIDAD_MEDIDA','Seleccione Unidad Medida','');
        $select_unidad_medida_a     =   '';

        $listadetalle               =   DetallePlaneamientoAnalisis::where('activo','=',1)
                                        ->where('detalleplaneamiento_id','=',$detallecotizacion->id)
                                        ->orderby('categoriaanalisis_id','asc')->get();

        $funcion                    =   $this;
        return View::make('planeamiento/form/fdetalleanalizar',
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

    public function actionExtornarEmisionPlaneamiento($idopcion,$idregistro,Request $request)
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
            
            $cotizacion = Planeamiento::where('id','=',$registro_id)->first();
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
            $cotizacion             =   Planeamiento::where('id', $registro_id)->first();
            return View::make('planeamiento/extornar', 
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

    public function actionExtornarAprobacionPlaneamiento($idopcion,$idregistro,Request $request)
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
            
            $cotizacion = Planeamiento::where('id','=',$registro_id)->first();
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
            $cotizacion             =   Planeamiento::where('id', $registro_id)->first();
            return View::make('planeamiento/extornar', 
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


    public function actionImprimirPlaneamiento($idopcion,$idregistro)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/

        set_time_limit(0);
        $registro_id        =   $this->funciones->decodificarmaestra($idregistro);
        $empresa_id         =   Session::get('empresas')->id;
        $empresa            =   Empresa::where('id',$empresa_id)->first();
        $cotizacion         =   Planeamiento::where('id',$registro_id)->first();
        $cliente            =   Cliente::where('id',$cotizacion->cliente_id)->first();
        $archivos           =   Archivo::where('referencia_id',$registro_id)->where('activo',1)->get();
        $detallecotizacion  =   DetallePlaneamiento::where('planeamiento_id',$registro_id)->where('ispadre',0)->orderby('codigo','asc')->get();
        $cuentas            =   CuentasEmpresa::from('cuentasempresa as C')
                                    ->join('entidadfinancieras as E','C.entidad_id','=','E.id')
                                    ->where('C.empresa_id','=',$empresa->id)
                                    ->get();

        // $customPaper        =   array(0,0,700.00,700.80);
        $customPaper        =   array(0,0,500,1000);
        $titulopdf          =   'Cotizacion N°('.$cotizacion->lote.').pdf';
        $titulo             =   "Cotizacion (".$cotizacion->lote.")";
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
            $cotizacion         =   Planeamiento::where('id',$registrocot_id)->first();
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
                return Redirect::to('/gestion-planeamiento/'.$idopcion.'/'.$idregistro)
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
                return Redirect::to('/gestion-planeamiento/'.$idopcion.'/'.$idregistro)->with('errorbd', $mensaje);

            }

            return Redirect::to('/gestion-planeamiento/'.$idopcion.'/'.$idregistro)->with('bienhecho', 'Archivos '.$registro->nombre_razonsocial.' registrados con éxito');

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
}
