<?php

namespace App\Http\Controllers;

use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Cliente;
use App\Modelos\Categoria;
use App\Modelos\Proveedor;
use App\Modelos\Producto;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\ConfiguracionTraits;


class ConfiguarionController extends Controller {

	use GeneralesTraits;
	use ConfiguracionTraits;

	public function actionListarUnidadMedida($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Unidad Medida');
	    $listacategoria 	= 	$this->con_lista_categoria('UNIDAD_MEDIDA');
		$funcion 			= 	$this;

		return View::make('configuracion/listaunidadmedida',
						 [
						 	'listacategoria' 		=> $listacategoria,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,						 	
						 ]);
	}


	public function actionAgregarUnidadMedida($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Unidad Medida');
		if($_POST)
		{
			$this->validate($request, [
	            'descripcion' => 'unique:categorias',
			], [
            	'descripcion.unique' => 'Unidad de medida ya Registrado',
        	]);

			$descripcion 	 					= 	$request['descripcion'];
			$aux01 	 							= 	$request['aux01'];

			$idcategoria 						=   $this->funciones->getCreateIdMaestra('categorias');
			$cabecera            	 			=	new Categoria;
			$cabecera->id 	     	 			=   $idcategoria;
			$cabecera->descripcion				=   $descripcion;
			$cabecera->aux01					=   $aux01;
			$cabecera->tipo_categoria			=   'UNIDAD_MEDIDA';
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
 		 	return Redirect::to('/gestion-de-unidad-medida/'.$idopcion)->with('bienhecho', 'Unidad de medida '.$descripcion.' registrado con exito');

		}else{

		    $disabledescripcion  	=	false;

			return View::make('configuracion/agregarunidadmedida',
						[
							'disabledescripcion'   	=> $disabledescripcion,
						  	'idopcion'  			 => $idopcion
						]);
		}
	}


	public function actionModificarUnidadMedida($idopcion,$idcategoria,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idcategoria = $this->funciones->decodificarmaestra($idcategoria);
	    View::share('titulo','Modificar Unidad de medida');

		if($_POST)
		{
			$activo 	 		 				= 	$request['activo'];
			$aux01 	 		 					= 	$request['aux01'];
			$cabecera            	 			=	Categoria::find($idcategoria);
			$cabecera->activo 	 	 			=   $activo;
			$cabecera->aux01					=   $aux01;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();

 			return Redirect::to('/gestion-de-unidad-medida/'.$idopcion)->with('bienhecho', 'Unidad de medida '.$cabecera->descripcion.' modificado con éxito');

		}else{

			$categoria 					= 	Categoria::where('id', $idcategoria)->first();
		    $disabledescripcion  		=	true;

	        return View::make('configuracion/modificarunidadmedida', 
	        				[
	        					'categoria'  					=> $categoria,
								'disabledescripcion' 			=> $disabledescripcion,
					  			'idopcion' 						=> $idopcion
	        				]);
		}
	}

	public function actionListarGrupoServicio($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Grupo de Servicio');
	    $listacategoria 	= 	$this->con_lista_categoria('CATEGORIA_SERVICIO');
		$funcion 			= 	$this;

		return View::make('configuracion/listagruposervicio',
						 [
						 	'listacategoria' 		=> $listacategoria,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,						 	
						 ]);
	}
	public function actionAgregarGrupoServicio($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Grupo de Servicio');
		if($_POST)
		{
			$this->validate($request, [
	            'descripcion' => 'unique:categorias',
			], [
            	'descripcion.unique' => 'Grupo de servicio ya Registrado',
        	]);

			$descripcion 	 					= 	$request['descripcion'];
			$idcategoria 						=   $this->funciones->getCreateIdMaestra('categorias');
			$cabecera            	 			=	new Categoria;
			$cabecera->id 	     	 			=   $idcategoria;
			$cabecera->descripcion				=   $descripcion;
			$cabecera->tipo_categoria			=   'CATEGORIA_SERVICIO';
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
 		 	return Redirect::to('/gestion-de-grupo-servicio/'.$idopcion)->with('bienhecho', 'Grupo de servicio '.$descripcion.' registrado con exito');

		}else{

		    $disabledescripcion  	=	false;
			return View::make('configuracion/agregargruposervicio',
						[
							'disabledescripcion'   	=> $disabledescripcion,
						  	'idopcion'  			 => $idopcion
						]);
		}
	}
	public function actionModificarGrupoServicio($idopcion,$idcategoria,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idcategoria = $this->funciones->decodificarmaestra($idcategoria);
	    View::share('titulo','Modificar Grupo Servicio');

		if($_POST)
		{
			$activo 	 		 				= 	$request['activo'];
			$cabecera            	 			=	Categoria::find($idcategoria);
			$cabecera->activo 	 	 			=   $activo;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();

 			return Redirect::to('/gestion-de-grupo-servicio/'.$idopcion)->with('bienhecho', 'Grupo de servicio '.$cabecera->descripcion.' modificado con éxito');

		}else{

			$categoria 					= 	Categoria::where('id', $idcategoria)->first();
		    $disabledescripcion  		=	true;

	        return View::make('configuracion/modificargruposervicio', 
	        				[
	        					'categoria'  					=> $categoria,
								'disabledescripcion' 			=> $disabledescripcion,
					  			'idopcion' 						=> $idopcion
	        				]);
		}
	}
	public function actionListarClientes($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Clientes');

	    $listacliente 	= 	$this->con_lista_clientes();
	    //dd($listacliente);
		$funcion 		= 	$this;


		return View::make('configuracion/listaclientes',
						 [
						 	'listacliente' 			=> $listacliente,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,						 	
						 ]);
	}
	public function actionAgregarClientes($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Clientes');
		if($_POST)
		{
			$this->validate($request, [
	            'numerodocumento' => 'unique:clientes',
			], [
            	'numerodocumento.unique' => 'Cliente ya Registrado',
        	]);

			$tipo_documento_id 	 		= 	$request['tipo_documento_id'];
			$numerodocumento 	 		= 	$request['numerodocumento'];
			$nombre_razonsocial 	 	= 	$request['nombre_razonsocial'];
			$direccion 	 		 		= 	$request['direccion'];
			$correo 	 		 		= 	$request['correo'];
			$celular 	 		 		= 	$request['celular'];
			$departamento_id 	 		= 	$request['departamento_id'];
			$provincia_id 	 		 	= 	$request['provincia_id'];
			$distrito_id 	 		 	= 	$request['distrito_id'];
			$sindocumento 	 		 	= 	$request['sindocumento'];


			$tipo_documento 			= 	Categoria::where('id','=',$tipo_documento_id)->first();

			$idcliente 					=   $this->funciones->getCreateIdMaestra('clientes');
			
			$cabecera            	 			=	new Cliente;
			$cabecera->id 	     	 			=   $idcliente;
			$cabecera->tipo_documento_id		=   $tipo_documento->id;
			$cabecera->tipo_documento_nombre 	=   $tipo_documento->descripcion;
			$cabecera->numerodocumento 			=   $numerodocumento;
			$cabecera->sindocumento 			=   $sindocumento;
			$cabecera->nombre_razonsocial 	   	=   $nombre_razonsocial;
			$cabecera->departamento_id 			=	$departamento_id;
			$cabecera->provincia_id 			=	$provincia_id;
			$cabecera->distrito_id 				=	$distrito_id;
			$cabecera->direccion 	   			=   $direccion;
			$cabecera->correo 					=   $correo;
			$cabecera->celular 					=   $celular;
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
 		 	return Redirect::to('/gestion-de-clientes/'.$idopcion)->with('bienhecho', 'Cliente '.$nombre_razonsocial.' registrado con exito');

		}else{

		    $select_tipo_documento  =	'';
		    $combo_tipo_documento 	=	$this->gn_combo_categoria('TIPO_DOCUMENTO','Seleccione tipo documento','');

			$select_departamento	=	'';
		    $combo_departamentos 	=	$this->gn_combo_departamentos();

		    $select_provincia 		=	'';
		    $combo_provincias 		=	[];
		    
		    $select_distrito		=	'';
		    $combo_distritos 		=	[];

		    $disabletipodocumento  	=	false;
		    $disablenumerodocumento =	false;

			return View::make('configuracion/agregarclientes',
						[
							'select_tipo_documento'  	=>  $select_tipo_documento,
							'combo_tipo_documento'   	=>  $combo_tipo_documento,
							'disabletipodocumento'   	=>  $disabletipodocumento,
							'disablenumerodocumento' 	=>  $disablenumerodocumento,
							
							'combo_departamentos'		=>	$combo_departamentos,
							'select_departamento'  	=>  $select_departamento,

							'combo_provincias'			=>	$combo_provincias,
							'select_provincia'  	=>  $select_provincia,
							
							'combo_distritos'			=>	$combo_distritos,
							'select_distrito'  	=>  $select_distrito,
						  	
						  	'idopcion'  			 	=>	$idopcion
						]);
		}
	}
	public function actionModificarCliente($idopcion,$idcliente,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idcliente = $this->funciones->decodificarmaestra($idcliente);
	    View::share('titulo','Modificar Cliente');

		if($_POST)
		{


			$nombre_razonsocial 	 			= 	$request['nombre_razonsocial'];
			$direccion 	 		 				= 	$request['direccion'];
			$correo 	 		 				= 	$request['correo'];
			$celular 	 		 				= 	$request['celular'];
			$activo 	 		 				= 	$request['activo'];
			
			$departamento_id 	 		 		= 	$request['departamento_id'];
			$provincia_id 	 		 			= 	$request['provincia_id'];
			$distrito_id 	 		 			= 	$request['distrito_id'];


			$cabecera            	 			=	Cliente::find($idcliente);
			$cabecera->nombre_razonsocial 	   	=   $nombre_razonsocial;
			$cabecera->direccion 	   			=   $direccion;
			$cabecera->correo 					=   $correo;
			$cabecera->celular 					=   $celular;
			$cabecera->departamento_id 			=	$departamento_id;
			$cabecera->provincia_id 			=	$provincia_id;
			$cabecera->distrito_id 				=	$distrito_id;
			$cabecera->activo 	 	 			=   $activo;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();


 			return Redirect::to('/gestion-de-clientes/'.$idopcion)->with('bienhecho', 'Cliente '.$nombre_razonsocial.' modificado con éxito');

		}else{

		    $combo_tipo_documento 		=	$this->gn_combo_categoria('TIPO_DOCUMENTO','Seleccione tipo documento','');
			$cliente 					= 	Cliente::where('id', $idcliente)->first();
			$select_tipo_documento 		= 	$cliente->tipo_documento_id;
		    $disabletipodocumento  		=	true;
		    $disablenumerodocumento 	=	true;

		    $combo_departamentos 		=	$this->gn_combo_departamentos();
		    $combo_provincias 			=	$this->gn_combo_provincias($cliente->departamento_id);
		    $combo_distritos 			=	$this->gn_combo_distritos($cliente->provincia_id);

			$select_departamento 		= 	$cliente->departamento_id;
			$select_provincia 			= 	$cliente->provincia_id;
			$select_distrito 			= 	$cliente->distrito_id;


	        return View::make('configuracion/modificarcliente', 
	        				[
	        					'combo_tipo_documento'  	=> $combo_tipo_documento,
	        					'cliente'  					=> $cliente,
		        				'select_tipo_documento' 	=> $select_tipo_documento,	
								'disabletipodocumento'   	=> $disabletipodocumento,
								'disablenumerodocumento' 	=> $disablenumerodocumento,

								'combo_departamentos'		=>	$combo_departamentos,
								'combo_provincias'			=>	$combo_provincias,
								'combo_distritos'			=>	$combo_distritos,

								'select_departamento'		=>	$select_departamento,
								'select_provincia'			=>	$select_provincia,
								'select_distrito'			=>	$select_distrito,

					  			'idopcion' 					=> $idopcion
	        				]);
		}
	}
	public function actionListarProveedores($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Proveedores');

	    $listaproveedor 	= 	$this->con_lista_proveedores();	    
		$funcion 		= 	$this;


		return View::make('configuracion/listaproveedores',
						 [
						 	'listaproveedor' 		=> $listaproveedor,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,						 	
						 ]);
	}
	public function actionAgregarProveedores($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Proveedores');
		if($_POST)
		{
			$this->validate($request, [
	            'numerodocumento' => 'unique:proveedores',
			], [
            	'numerodocumento.unique' => 'Proveedor ya Registrado',
        	]);

			$tipo_documento_id 	 		= 	$request['tipo_documento_id'];
			$numerodocumento 	 		= 	$request['numerodocumento'];
			$nombre_razonsocial 	 	= 	$request['nombre_razonsocial'];
			// $rubro_id				 	= 	$request['rubro_id'];
			$direccion 	 		 		= 	$request['direccion'];
			$correo 	 		 		= 	$request['correo'];
			$celular 	 		 		= 	$request['celular'];
		
			$tipo_documento 			= 	Categoria::where('id','=',$tipo_documento_id)->first();
			// $rubro 			 			= 	Categoria::where('id','=',$rubro_id)->first();

			$idproveedor 				=   $this->funciones->getCreateIdMaestra('proveedores');
			
			$cabecera            	 			=	new Proveedor;
			$cabecera->id 	     	 			=   $idproveedor;
			$cabecera->tipo_documento_id		=   $tipo_documento->id;
			$cabecera->tipo_documento_nombre 	=   $tipo_documento->descripcion;
			$cabecera->numerodocumento 			=   $numerodocumento;			
			$cabecera->nombre_razonsocial 	   	=   $nombre_razonsocial;
			// $cabecera->rubro_id			 	   	=   $rubro->id;
			// $cabecera->rubro_nombre		 	   	=   $rubro->descripcion;
			$cabecera->direccion 	   			=   $direccion;
			$cabecera->correo 					=   $correo;
			$cabecera->celular 					=   $celular;
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
 		 	return Redirect::to('/gestion-de-proveedores/'.$idopcion)->with('bienhecho', 'Proveedor '.$nombre_razonsocial.' registrado con exito');

		}else{

		    $select_tipo_documento  =	'';
		    $combo_tipo_documento 	=	$this->gn_combo_categoria('TIPO_DOCUMENTO','Seleccione tipo documento','');
		    $select_rubro			=	'';
		    // $combo_rubro		 	=	$this->gn_combo_categoria('RUBRO','Seleccione rubro','');

		    $disabletipodocumento  	=	false;
		    $disablenumerodocumento =	false;
		    
			return View::make('configuracion/agregarproveedores',
						[
							'select_tipo_documento'  => $select_tipo_documento,
							'combo_tipo_documento'   => $combo_tipo_documento,
							'disabletipodocumento'   => $disabletipodocumento,
							'disablenumerodocumento' => $disablenumerodocumento,
							'select_rubro'  		 => $select_rubro,
							// 'combo_rubro'   		 => $combo_rubro,
							'idopcion'  			 => $idopcion
						]);
		}
	}
	public function actionModificarProveedor($idopcion,$idproveedor,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idproveedor = $this->funciones->decodificarmaestra($idproveedor);
	    View::share('titulo','Modificar Proveedor');

		if($_POST)
		{


			$nombre_razonsocial 	 			= 	$request['nombre_razonsocial'];
			$rubro_id				 			= 	$request['rubro_id'];
			$direccion 	 		 				= 	$request['direccion'];
			$correo 	 		 				= 	$request['correo'];
			$celular 	 		 				= 	$request['celular'];
			$activo 	 		 				= 	$request['activo'];
			
			$rubro 				 				= 	Categoria::where('id','=',$rubro_id)->first();	

			$cabecera            	 			=	Proveedor::find($idproveedor);
			$cabecera->nombre_razonsocial 	   	=   $nombre_razonsocial;
			$cabecera->rubro_id	   				=   $rubro->id;
			$cabecera->rubro_nombre				=   $rubro->descripcion;
			$cabecera->direccion 	   			=   $direccion;
			$cabecera->correo 					=   $correo;
			$cabecera->celular 					=   $celular;
			$cabecera->activo 	 	 			=   $activo;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();


 			return Redirect::to('/gestion-de-proveedores/'.$idopcion)->with('bienhecho', 'Proveedor '.$nombre_razonsocial.' modificado con éxito');

		}else{

			$proveedor 					= 	Proveedor::where('id', $idproveedor)->first();
		    $combo_tipo_documento 		=	$this->gn_combo_categoria('TIPO_DOCUMENTO','Seleccione tipo documento','');			
			$select_tipo_documento 		= 	$proveedor->tipo_documento_id;
			$combo_rubro 				=	$this->gn_combo_categoria('RUBRO','Seleccione rubro','');
			$select_rubro		 		= 	$proveedor->rubro_id;
		    $disabletipodocumento  		=	true;
		    $disablenumerodocumento 	=	true;



	        return View::make('configuracion/modificarproveedor', 
	        				[
	        					'proveedor'  				=> $proveedor,
	        					'combo_tipo_documento'  	=> $combo_tipo_documento,
	        					'select_tipo_documento' 	=> $select_tipo_documento,	
	        					'combo_rubro'  				=> $combo_rubro,
	        					'select_rubro' 				=> $select_rubro,	
								'disabletipodocumento'   	=> $disabletipodocumento,
								'disablenumerodocumento' 	=> $disablenumerodocumento,
					  			'idopcion' 					=> $idopcion
	        				]);
		}
	}
	public function actionListarProductos($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Productos');

	    $listaproducto 	= 	$this->con_lista_productos();	    
		$funcion 		= 	$this;


		return View::make('configuracion/listaproductos',
						 [
						 	'listaproducto' 		=> $listaproducto,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,						 	
						 ]);
	}
	public function actionAgregarProductos($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Agregar Productos');
		if($_POST)
		{
			$this->validate($request, [
	            'descripcion' => 'unique:productos',
			], [
            	'descripcion.unique' => 'Producto ya Registrado',
        	]);

			$codigo 	 				= 	$request['codigo'];
			$descripcion 	 			= 	$request['descripcion'];
			$peso 	 					= 	$request['peso'];
			$unidad_medida_id	 		= 	$request['unidad_medida_id'];
			$categoria_id	 			= 	$request['categoria_id'];
			$subcategoria_id	 		= 	$request['subcategoria_id'];
					
			$unidad_medida	 			= 	Categoria::where('id','=',$unidad_medida_id)->first();
			$categoria_prod	 			= 	Categoria::where('id','=',$categoria_id)->first();
			$subcategoria_prod	 		= 	Categoria::where('id','=',$subcategoria_id)->first();

			$idproducto 				=   $this->funciones->getCreateIdMaestra('productos');
			
			$cabecera            	 			=	new Producto;
			$cabecera->id 	     	 			=   $idproducto;
			$cabecera->codigo 					=   $codigo;			
			$cabecera->descripcion 	   			=   $descripcion;

			
			$cabecera->categoria_id				=   $categoria_prod->id;
			$cabecera->categoria_nombre			=   $categoria_prod->descripcion;
			
			$cabecera->subcategoria_id			=   $subcategoria_prod->id;
			$cabecera->subcategoria_nombre		=   $subcategoria_prod->descripcion;
			// dd($categoria_id);
			if($categoria_id=='CATP00000003'){
				$cabecera->indservicio			=	1;
			}
			elseif($categoria_id=='CATP00000007'){
			
				$cabecera->indproduccion		=	1;
				$unidad_medida	 					= 	Categoria::where('tipo_categoria','=','UNIDAD_MEDIDA')->where('aux01','=','UND')->first();
				$cabecera->unidad_medida_id			=   $unidad_medida->id;
				$cabecera->unidad_medida_nombre 	=   $unidad_medida->descripcion;	
			}
			else
			{
				$unidad_medida	 					= 	Categoria::where('id','=',$unidad_medida_id)->first();
				$cabecera->unidad_medida_id			=   $unidad_medida->id;
				$cabecera->unidad_medida_nombre 	=   $unidad_medida->descripcion;	
			}

			$cabecera->peso			 	 	  	=   (float)$peso;
			// $cabecera->unidad_medida_id			=   $unidad_medida->id;
			// $cabecera->unidad_medida_nombre 	=   $unidad_medida->descripcion;			

			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
 		 	return Redirect::to('/gestion-de-productos/'.$idopcion)->with('bienhecho', 'Producto '.$descripcion.' registrado con exito');

		}else{

		    $select_unidad_medida  	=	'';
		    $combo_unidad_medida 	=	$this->gn_combo_categoria('UNIDAD_MEDIDA','Seleccione unidad medida','');
		    $cod_producto 			=   $this->funciones->getCreateCodCorrelativo('productos',7);
		    $select_categoria  		=	'';
		    $combo_categoria 		=	$this->gn_combo_categoria('CAT_PRODUCTO','Seleccione Categoria','');

			$select_subcategoria  	=	'';
			$combo_subcategoria		=	[];
		    // $combo_subcategoria 	=	$this->gn_combo_subcategoria('CAT_PRODUCTO','Seleccione Categoria','');
		    // $combo_subcategoria 	=	$this->gn_combo_subcategoria('CAT_PRODUCTO','Seleccione Categoria','');

			return View::make('configuracion/agregarproductos',
						[
							'select_unidad_medida'  	=> $select_unidad_medida,
							'combo_unidad_medida'   	=> $combo_unidad_medida,	
							'cod_producto'				=> $cod_producto,						
							'select_categoria'  		=> $select_categoria,
							'combo_categoria'   		=> $combo_categoria,	
							'select_subcategoria'  		=> $select_subcategoria,
							'combo_subcategoria'   		=> $combo_subcategoria,	
						  	'idopcion'  			 	=> $idopcion
						]);
		}
	}

	public function actionModificarProducto($idopcion,$idproducto,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idproducto = $this->funciones->decodificarmaestra($idproducto);
	    View::share('titulo','Modificar Producto');

		if($_POST)
		{


			$codigo 	 						= 	$request['codigo'];
			$descripcion 	 					= 	$request['descripcion'];
			$peso 	 							= 	$request['peso'];
			$unidad_medida_id	 				= 	$request['unidad_medida_id'];
			$categoria_id	 					= 	$request['categoria_id'];
			$subcategoria_id	 				= 	$request['subcategoria_id'];
			$activo 	 		 				= 	$request['activo'];

			$categoria_prod	 					= 	Categoria::where('id','=',$categoria_id)->first();
			$subcategoria_prod	 				= 	Categoria::where('id','=',$subcategoria_id)->first();

			$cabecera            	 			=	Producto::find($idproducto);
			$cabecera->codigo 					=   $codigo;			
			$cabecera->descripcion 	   			=   $descripcion;
			$cabecera->peso			 	 	  	=   (float)$peso;
			$cabecera->categoria_id				=   $categoria_prod->id;
			$cabecera->categoria_nombre			=   $categoria_prod->descripcion;
			
			$cabecera->subcategoria_id			=   $subcategoria_prod->id;
			$cabecera->subcategoria_nombre		=   $subcategoria_prod->descripcion;
			if($categoria_id=='CATP00000003'){
				$cabecera->indservicio			=	1;
				$cabecera->unidad_medida_id			=   NULL;
				$cabecera->unidad_medida_nombre 	=   NULL;
			}
			else
			{
				$unidad_medida	 					= 	Categoria::where('id','=',$unidad_medida_id)->first();
				$cabecera->unidad_medida_id			=   $unidad_medida->id;
				$cabecera->unidad_medida_nombre 	=   $unidad_medida->descripcion;	
			}

			$cabecera->activo 	 	 			=   $activo;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();


 			return Redirect::to('/gestion-de-productos/'.$idopcion)->with('bienhecho', 'Producto '.$descripcion.' modificado con éxito');

		}else{

		    $combo_unidad_medida 		=	$this->gn_combo_categoria('UNIDAD_MEDIDA','Seleccione unidad medida','');
			$producto 					= 	Producto::where('id', $idproducto)->first();
			$select_unidad_medida 		= 	$producto->unidad_medida_id;
		    
  			$select_categoria  			=	$producto->categoria_id;
		    $combo_categoria 			=	$this->gn_combo_categoria('CAT_PRODUCTO','Seleccione Categoria','');
			
			$select_subcategoria  		=	$producto->subcategoria_id;
		    $combo_subcategoria 		=	Categoria::where('id',$producto->subcategoria_id)->pluck('descripcion','id')->toArray()
											+
											Categoria::where('tipo_categoria','SUBCAT_PRODUCTO')
												->where('aux01','=',$producto->categoria_id)
												->where('activo','=',1)
												->whereNotIn('id',[$producto->subcategoria_id])
												->pluck('descripcion','id')->toArray();

	        return View::make('configuracion/modificarproducto', 
	        				[
	        					'combo_unidad_medida'  		=> $combo_unidad_medida,
	        					'producto'  				=> $producto,
		        				'select_unidad_medida' 		=> $select_unidad_medida,
		        				'select_categoria'			=> $select_categoria,
		        				'combo_categoria'			=> $combo_categoria,
		        				'select_subcategoria'			=> $select_subcategoria,
		        				'combo_subcategoria'			=> $combo_subcategoria,
					  			'idopcion' 					=> $idopcion
	        				]);
		}
	}

	public function actionAjaxSubCategoriasProducto(Request $request)
	{
		$categoria_id			=	$request['categoria_id'];
		$select_subcategoria  	=	'';
		$combo_subcategoria		=	Categoria::where('tipo_categoria','=','SUBCAT_PRODUCTO')->where('activo','=',1)->where('aux01','=',$categoria_id)->pluck('descripcion','id')->toArray();
		
		return View::make('configuracion/ajax/asubcategoriaproducto',
						[
							'select_subcategoria'  		=> $select_subcategoria,
							'combo_subcategoria'   		=> $combo_subcategoria,	
						  	'ajax'						=>	true,
						]);
	}

	public function actionListarRubros($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listar Rubros');
	    $listacategoria 	= 	$this->con_lista_categoria('RUBRO');
		$funcion 			= 	$this;

		return View::make('configuracion/listarubros',
						 [
						 	'listacategoria' 		=> $listacategoria,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,						 	
						 ]);
	}


	public function actionAgregarRubros($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Rubros');
		if($_POST)
		{
			$this->validate($request, [
	            'descripcion' => 'unique:categorias',
			], [
            	'descripcion.unique' => 'Rubro ya Registrado',
        	]);

			$descripcion 	 					= 	$request['descripcion'];

			$idcategoria 						=   $this->funciones->getCreateIdMaestra('categorias');
			$cabecera            	 			=	new Categoria;
			$cabecera->id 	     	 			=   $idcategoria;
			$cabecera->descripcion				=   $descripcion;
			$cabecera->tipo_categoria			=   'RUBRO';
			$cabecera->fecha_crea 	 			=   $this->fechaactual;
			$cabecera->usuario_crea 			=   Session::get('usuario')->id;
			$cabecera->save();
 
 		 	return Redirect::to('/gestion-de-rubros/'.$idopcion)->with('bienhecho', 'Rubro '.$descripcion.' registrado con exito');

		}else{

		    $disabledescripcion  	=	false;

			return View::make('configuracion/agregarrubros',
						[
							'disabledescripcion'   	=> $disabledescripcion,
						  	'idopcion'  			 => $idopcion
						]);
		}
	}


	public function actionModificarRubro($idopcion,$idcategoria,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idcategoria = $this->funciones->decodificarmaestra($idcategoria);
	    View::share('titulo','Modificar Rubro');

		if($_POST)
		{
			$activo 	 		 				= 	$request['activo'];
			$cabecera            	 			=	Categoria::find($idcategoria);
			$cabecera->activo 	 	 			=   $activo;
			$cabecera->fecha_mod 	 			=   $this->fechaactual;
			$cabecera->usuario_mod 				=   Session::get('usuario')->id;
			$cabecera->save();

 			return Redirect::to('/gestion-de-rubros/'.$idopcion)->with('bienhecho', 'Rubro '.$cabecera->descripcion.' modificado con éxito');

		}else{

			$categoria 					= 	Categoria::where('id', $idcategoria)->first();
		    $disabledescripcion  		=	true;

	        return View::make('configuracion/modificarrubro', 
	        				[
	        					'categoria'  					=> $categoria,
								'disabledescripcion' 			=> $disabledescripcion,
					  			'idopcion' 						=> $idopcion
	        				]);
		}
	}

}