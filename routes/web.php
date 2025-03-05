<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

/********************** USUARIOS *************************/
// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: *');

Route::group(['middleware' => ['guestaw']], function () {

	Route::any('/', 'UserController@actionLogin');
	Route::any('/login', 'UserController@actionLogin');
	Route::any('/acceso', 'UserController@actionAcceso');

});

Route::get('/cerrarsession', 'UserController@actionCerrarSesion');

Route::group(['middleware' => ['authaw']], function () {

	Route::get('/bienvenido', 'UserController@actionBienvenido');
	Route::get('/obtenertipocambio', 'UserController@actionObtenerTipoCambio');

	//GESTION DE USUARIOS
	Route::any('/gestion-de-usuarios/{idopcion}', 'UserController@actionListarUsuarios');
	Route::any('/agregar-usuario/{idopcion}', 'UserController@actionAgregarUsuario');
	Route::any('/modificar-usuario/{idopcion}/{idusuario}', 'UserController@actionModificarUsuario');
	Route::any('/ajax-activar-perfiles', 'UserController@actionAjaxActivarPerfiles');

	//GESTION DE ROLES
	Route::any('/gestion-de-roles/{idopcion}', 'UserController@actionListarRoles');
	Route::any('/agregar-rol/{idopcion}', 'UserController@actionAgregarRol');
	Route::any('/modificar-rol/{idopcion}/{idrol}', 'UserController@actionModificarRol');

	//GESTION DE PERMISOS
	Route::any('/gestion-de-permisos/{idopcion}', 'UserController@actionListarPermisos');
	Route::any('/ajax-listado-de-opciones', 'UserController@actionAjaxListarOpciones');
	Route::any('/ajax-activar-permisos', 'UserController@actionAjaxActivarPermisos');

	//GESTION DE CLIENTES
	Route::any('/gestion-de-clientes/{idopcion}', 'ConfiguarionController@actionListarClientes');
	Route::any('/agregar-clientes/{idopcion}', 'ConfiguarionController@actionAgregarClientes');
	Route::any('/modificar-clientes/{idopcion}/{idcliente}', 'ConfiguarionController@actionModificarCliente');
	Route::any('/ajax-select-provincia', 'GeneralAjaxController@actionProvinciaAjax');
	Route::any('/ajax-select-distrito', 'GeneralAjaxController@actionDistritoAjax');
	Route::any('/ajax-cargar-ndoc-cliente-sin-documentos', 'GeneralAjaxController@actionCargarNDocClienteSinDocumento');
	Route::any('/ajax-cuenta-entidad-empresa', 'GeneralAjaxController@actionCuentasEntidad');



	//GESTION DE REQUERIMIENTOS
	Route::any('/gestion-de-produccion/{idopcion}', 'ProduccionController@actionListarProduccion');
	Route::any('/detalle-produccion/{idopcion}/{idregistro}', 'ProduccionController@actionDetalleProduccion');

	Route::any('/agregar-produccion/{idopcion}', 'ProduccionController@actionAgregarProduccion');
	Route::any('/modificar-produccion/{idopcion}/{idrequerimiento}', 'ProduccionController@actionModificarProduccion');
	Route::any('/extornar-produccion/{idopcion}/{idrequerimiento}', 'ProduccionController@actionEliminarProduccion');
	Route::any('/emitir-produccion/{idopcion}', 'ProduccionController@actionEmitirProduccion');
	Route::any('/subir-archivos-produccion/{idopcion}/{idrequerimiento}', 'ProduccionController@actionGestionArchivosProduccion');

	Route::any('/gestion-de-cotizacion/{idopcion}', 'CotizarProduccionController@actionListarCotizacionProduccion');
	Route::any('/cotizar-cotizacion/{idopcion}/{idcotizacion}', 'CotizarProduccionController@actionCotizarcotizacion');
	Route::any('/ajax-agregar-producto-analisis', 'CotizarProduccionController@actionAgregarProductoAnalisis');
	Route::any('/ajax-actualizar-tabla-cotizacion', 'CotizarProduccionController@actionActulizarTablaCotizacion');
	Route::any('/ajax-eliminar-tabla-cotizacion-analisis', 'CotizarProduccionController@actionEliminarTablaCotizacionProduccion');
	Route::any('/ajax-cargar-sub-categorias-productos-produccion', 'CotizarProduccionController@actionAjaxSubCategoriasProductoProduccion');
	Route::any('/ajax-cargar-productos-produccion', 'CotizarProduccionController@actionAjaxProductoProduccion');
	Route::any('/emitir-cotizacion/{idopcion}/{idregistro}', 'CotizarProduccionController@actionEmitirCotizacion');
	Route::any('/ajax-elimnar-linea-cotizacion', 'CotizarProduccionController@actionAjaxEliminarLineaCotizacionProduccion');
	Route::any('/ajax-registrar-producto-produccion', 'CotizarProduccionController@actionAjaxModalEmitirProduccion');
	Route::any('/ajax-modal-agregar-producto-produccion', 'CotizarProduccionController@actionAjaxAgregarProductoProduccion');
	Route::any('/agregar-producto-cotizacion/{idopcion}/{idregistro}', 'CotizarProduccionController@actionAjaxAgregarDetalleProductoProduccion');
	
	Route::any('/ajax-elimnar-linea-cotizacion-produccion', 'CotizarProduccionController@actionAjaxEliminarCotizacionProduccion');



	//GESTION DE EVALUAR REQUERIMIENTO
	// Route::any('/gestion-presupuesto/{idopcion}', 'EvaluarRequerimientoController@actionListarcotizaciones');
	// Route::any('/gestion-evaluar-requerimiento/{idopcion}', 'EvaluarRequerimientoController@actionListarCotizacionesEmititas');
	// Route::any('/ajax-modal-configuracion-evaluar-cotizacion-detalle', 'EvaluarRequerimientoController@actionConfigurarDetalle');
	// Route::any('/cotizar-evaluar-requerimiento/{idopcion}/{idcotizacion}', 'EvaluarRequerimientoController@actionEvaluarCotizacion');
	// Route::any('/ajax-modal-modificar-configuracion-evaluar-cotizacion-detalle', 'EvaluarRequerimientoController@actionAjaxModalModificarConfiguracionCotizacion');
	// Route::any('/extornar-evaluar-requerimiento/{idopcion}/{idregistro}', 'EvaluarRequerimientoController@actionExtornarCotizacionEvaluarRequerimiento');
	// Route::any('/agregar-archivos-evaluar-requerimiento/{idopcion}/{idrequerimiento}', 'EvaluarRequerimientoController@actionSubirArchivosEvaluarRequerimiento');
	// Route::any('/descargar-archivo-evaluar-requerimiento/{idopcion}/{idrequerimiento}/{idarchivo}', 'EvaluarRequerimientoController@actionDescargarArchivosEvaluarRequerimiento');

	Route::any('/evaluar-cotizacion/{idopcion}', 'EvaluarRequerimientoController@actionEmitirEvaluarCotizacion');


	


	//GESTION DE PLANEAMIENTO
	Route::any('/gestion-planeamiento/{idopcion}', 'PlaneamientoController@actionListarPlaneamiento');
	Route::any('/analizar-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionAnalizarPlaneamiento');
	Route::any('/ajax-modal-configuracion-planeamiento-detalle', 'PlaneamientoController@actionConfigurarDetallePlaneamiento');
	Route::any('/ajax-analizar-detalle-planeamiento', 'PlaneamientoController@actionAnalizarDetallePlaneamiento');
	Route::any('/ajax-agregar-producto-analisis-planeamiento', 'PlaneamientoController@actionAgregarProductoAnalisisPlaneamiento');
	Route::any('/ajax-actualizar-tabla-planeamiento', 'PlaneamientoController@actionActulizarTablaPlaneamiento');
	Route::any('/ajax-eliminar-tabla-planeamiento-analisis', 'PlaneamientoController@actionEliminarTablaPleanamientoAnalisis');
	Route::any('/ajax-elimnar-linea-planeamiento', 'PlaneamientoController@actionAjaxEliminarLineaPlaneamiento');
	Route::any('/ajax-elimnar-servicio-linea-planeamiento', 'PlaneamientoController@actionAjaxEliminarServicioLineaPlaneamiento');


	Route::any('/cotizar-planeamiento/{idopcion}/{idcotizacion}', 'PlaneamientoController@actionCotizarPlaneamiento');
	Route::any('/ajax-modal-modificar-configuracion-planeamiento-detalle', 'PlaneamientoController@actionAjaxModalModificarConfiguracionPlaneamiento');
	Route::any('/extornar-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionExtornarPlaneamiento');
	Route::any('/emitir-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionEmitirPlaneamiento');
	Route::any('/aprobar-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionAprobarPlaneamiento');
	Route::any('/detalle-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionDetallePlaneamiento');
	Route::any('/ajax-detalle-analizar-detalle-planeamiento', 'PlaneamientoController@actionDetalleAnalizarDetallePlaneamiento');
	Route::any('/extornar-emision-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionExtornarEmisionPlaneamiento');
	Route::any('/extornar-aprobacion-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionExtornarAprobacionPlaneamiento');
	Route::any('/imprimir-planeamiento/{idopcion}/{idregistro}', 'PlaneamientoController@actionImprimirPlaneamiento');
	Route::any('/agregar-archivos-planeamiento/{idopcion}/{idrequerimiento}', 'PlaneamientoController@actionSubirArchivosCotizarRequerimiento');


	//GESTION GRUPO SERVICIO
	Route::any('/gestion-de-grupo-servicio/{idopcion}', 'ConfiguarionController@actionListarGrupoServicio');
	Route::any('/agregar-grupo-servicio/{idopcion}', 'ConfiguarionController@actionAgregarGrupoServicio');
	Route::any('/modificar-grupo-servicio/{idopcion}/{idcategoria}', 'ConfiguarionController@actionModificarGrupoServicio');

	//GESTION DE UNIDAD DE MEDIDA
	Route::any('/gestion-de-unidad-medida/{idopcion}', 'ConfiguarionController@actionListarUnidadMedida');
	Route::any('/agregar-unidad-medida/{idopcion}', 'ConfiguarionController@actionAgregarUnidadMedida');
	Route::any('/modificar-unidad-medida/{idopcion}/{idcategoria}', 'ConfiguarionController@actionModificarUnidadMedida');

	//GESTION LINEA COTIZACION
	// Route::any('/ajax-elimnar-linea-cotizacion', 'CotizacionController@actionAjaxEliminarLineaCotizacion');
	Route::any('/ajax-elimnar-servicio-linea-cotizacion', 'CotizacionController@actionAjaxEliminarServicioLineaCotizacion');
	Route::any('/ajax-elimnar-igv-detalle-cotizacion', 'CotizacionController@actionAjaxEliminarIgvDetalleCotizacion');
	Route::any('/ajax-actualizar-mgadmin-detalle-cotizacion', 'CotizacionController@actionAjaxActualizarMGAdministrativoDetalleCotizacion');
	Route::any('/ajax-actualizar-mgutil-detalle-cotizacion', 'CotizacionController@actionAjaxActualizarMGUtilidadDetalleCotizacion');
	Route::any('/ajax-actualizar-precio-venta-detalle-cotizacion', 'CotizacionController@actionAjaxActualizarPrecioVentaDetalleCotizacion');
	


	///////////////////////////////////////////////////////////////////////////////////////////////////////////
 	// SECCION DE GRUPO OPCIONES
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	Route::any('/gestion-grupo-opciones/{idopcion}', 'GestionMenuController@actionListarGrupoOpciones');
	Route::any('/agregar-grupo-opcion/{idopcion}', 'GestionMenuController@actionAgregarGrupoOpcion');
	Route::any('/modificar-grupo-opcion/{idopcion}/{idregistro}', 'GestionMenuController@actionModificarGrupoOpcion');

	///////////////////////////////////////////////////////////////////////////////////////////////////////////
 	// SECCION DE OPCIONES
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	Route::any('/gestion-opciones/{idopcion}', 'GestionMenuController@actionListarOpciones');
	Route::any('/agregar-opcion/{idopcion}', 'GestionMenuController@actionAgregarOpcion');
	Route::any('/modificar-opcion/{idopcion}/{idregistro}', 'GestionMenuController@actionModificarOpcion');

	///////////////////////////////////////////////////////////////////////////////////////////////////////////
 	// SECCION DE EMPRESAS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Route::any('/gestion-cuentas-empresa/{idopcion}', 'GestionEmpresaController@actionListarEmpresa');
	Route::any('/gestion-cuentas-empresa/{idopcion}', 'CuentasEmpresaController@actionListarCuentasEmpresa');
	Route::any('/agregar-cuentas-empresa/{idopcion}', 'CuentasEmpresaController@actionAgregarCuentasEmpresa');
	Route::any('/modificar-cuentas-empresa/{idopcion}/{idregistro}', 'CuentasEmpresaController@actionModificarCuentasEmpresa');
	Route::any('/eliminar-cuentas-empresa/{idopcion}/{idregistro}', 'CuentasEmpresaController@actionEliminarCuentasEmpresa');




	Route::any('/gestion-saldo-cuentas-empresa/{idopcion}', 'CuentasSaldoEmpresaController@actionListarSaldoCuentasEmpresa');
	Route::any('/ajax-modal-detalle-saldo-cuenta', 'CuentasSaldoEmpresaController@actionAjaxModalSaldoCuenta');




	///////////////////////////////////////////////////////////////////////////////////////////////////////////

 	//GESTION DE PROVEEDORES
	Route::any('/gestion-de-proveedores/{idopcion}', 'ConfiguarionController@actionListarProveedores');
	Route::any('/agregar-proveedores/{idopcion}', 'ConfiguarionController@actionAgregarProveedores');
	Route::any('/modificar-proveedores/{idopcion}/{idproveedor}', 'ConfiguarionController@actionModificarProveedor');

	//GESTION DE PRODUCTOS
	Route::any('/gestion-de-productos/{idopcion}', 'ConfiguarionController@actionListarProductos');
	Route::any('/agregar-productos/{idopcion}', 'ConfiguarionController@actionAgregarProductos');
	Route::any('/modificar-productos/{idopcion}/{idproducto}', 'ConfiguarionController@actionModificarProducto');
	Route::any('/ajax-cargar-sub-categorias-productos', 'ConfiguarionController@actionAjaxSubCategoriasProducto');
	Route::any('/ajax-modal-producto-gema', 'ConfiguarionController@actionAjaxModalProductoGema');
	Route::any('/guardar-producto-gema/{idopcion}/{idproducto}', 'ConfiguarionController@actionAgregarProductoGema');	
	Route::any('/quitar-producto-gema/{idopcion}/{idproductogema}', 'ConfiguarionController@actionQuitarProductoGema');	
	Route::any('/ajax-gestion-de-productos-filtro', 'ConfiguarionController@actionAjaxProductoFiltro');

	//GESTION DE BIEN PRODUCIDOS
	Route::any('/gestion-de-bien-producidos/{idopcion}', 'ConfiguarionController@actionListarBienProducido');
	Route::any('/agregar-bien-producidos/{idopcion}', 'ConfiguarionController@actionAgregarBienProducido');
	Route::any('/modificar-bien-producidos/{idopcion}/{idproducto}', 'ConfiguarionController@actionModificarBienProducido');
	Route::any('/ajax-cargar-sub-categorias-bien-producidos', 'ConfiguarionController@actionAjaxSubCategoriasBienProducido');
	Route::any('/ajax-modal-bien-producido-gema', 'ConfiguarionController@actionAjaxModalBienProducidoGema');
	Route::any('/guardar-bien-producido-gema/{idopcion}/{idproducto}', 'ConfiguarionController@actionAgregarBienProducidoGema');
	Route::any('/quitar-bien-producido-gema/{idopcion}/{idproductogema}', 'ConfiguarionController@actionQuitarBienProducidoGema');
	Route::any('/ajax-gestion-de-bien-producidos-filtro', 'ConfiguarionController@actionAjaxBienProducidoFiltro');	

	//GESTION DE COMPRAS
	Route::any('/gestion-de-compras/{idopcion}', 'CompraController@actionListarCompras');
	Route::any('/ajax-gestion-de-compras-entrefechas', 'CompraController@actionAjaxListarComprasEntreFechas');
	Route::any('/agregar-compras/{idopcion}', 'CompraController@actionAgregarCompras');
	Route::any('/modificar-compras/{idopcion}/{idcompra}', 'CompraController@actionModificarCompra');
	Route::any('/ajax-modal-detalle-compra', 'CompraController@actionAjaxModalDetalleCompra');
	Route::any('/guardar-detalle-compra/{idopcion}/{idcompra}', 'CompraController@actionAgregarDetalleCompras');
	Route::any('/quitar-detalle-compras/{idopcion}/{iddetallecompra}', 'CompraController@actionQuitarDetalleCompra');	
	Route::any('/ajax-modal-emitir-compra', 'CompraController@actionAjaxModalEmitirCompra');
	Route::any('/emitir-compras/{idopcion}', 'CompraController@actionEmitirCompras');
	Route::any('/ajax-genera-nota-pedido', 'CompraController@actionGeneraNotaPedidoAjax');
	Route::any('/extornar-compras/{idopcion}/{idcompra}', 'CompraController@actionExtornarCompras');
	Route::any('/pdf-compras/{idcompra}', 'CompraController@actionPdfCompras');



	//GESTION DE VENTAS
	Route::any('/gestion-de-ventas/{idopcion}', 'VentaController@actionListarVentas');
	Route::any('/ajax-gestion-de-ventas-entrefechas', 'VentaController@actionAjaxListarVentasEntreFechas');
	Route::any('/ajax-tipo-pago-venta', 'VentaController@actionAjaxTipoPagoVentas');

	Route::any('/agregar-ventas/{idopcion}', 'VentaController@actionAgregarVentas');
	Route::any('/modificar-ventas/{idopcion}/{idventa}', 'VentaController@actionModificarVenta');
	Route::any('/ajax-modal-detalle-venta', 'VentaController@actionAjaxModalDetalleVenta');
	Route::any('/guardar-detalle-venta/{idopcion}/{idventa}', 'VentaController@actionAgregarDetalleVentas');
	Route::any('/quitar-detalle-ventas/{idopcion}/{iddetallecompra}', 'VentaController@actionQuitarDetalleVenta');	
	Route::any('/ajax-modal-emitir-venta', 'VentaController@actionAjaxModalEmitirVenta');
	Route::any('/emitir-ventas/{idopcion}', 'VentaController@actionEmitirVentas');
	Route::any('/ajax-genera-nota-pedido-ventas', 'VentaController@actionGeneraNotaPedidoAjax');
	Route::any('/extornar-ventas/{idopcion}/{idventa}', 'VentaController@actionExtornarVentas');
	Route::any('/pdf-ventas/{idventa}', 'VentaController@actionPdfVentas');




	//GESTION DE RUBROS
	Route::any('/gestion-de-rubros/{idopcion}', 'ConfiguarionController@actionListarRubros');
	Route::any('/agregar-rubros/{idopcion}', 'ConfiguarionController@actionAgregarRubros');
	Route::any('/modificar-rubros/{idopcion}/{idcategoria}', 'ConfiguarionController@actionModificarRubro');

	//GESTION DE ALMACEN
	Route::any('/gestion-de-almacen/{idopcion}', 'LogisticaController@actionListarAlmacen');	
	Route::any('/agregar-almacen/{idopcion}', 'LogisticaController@actionAgregarAlmacen');
	Route::any('/modificar-almacen/{idopcion}/{idalmacen}', 'LogisticaController@actionModificarAlmacen');
	Route::any('/quitar-almacen/{idopcion}/{idalmacen}', 'LogisticaController@actionQuitarAlmacen');

	//REPORTES DE COMPRAS
	Route::any('/reporte-de-documentos-compras/{idopcion}', 'ReporteController@actionListarCompras');
	Route::any('/ajax-reporte-de-compras-entrefechas-proveedor', 'ReporteController@actionAjaxReporteComprasEntreFechas');	

	//REPORTES DE KARDEX
	Route::any('/reporte-de-kardex/{idopcion}', 'ReporteController@actionListarKardex');
	Route::any('/ajax-reporte-de-kardex-entrefechas', 'ReporteController@actionAjaxReporteKardexEntreFechas');	


   	Route::get('buscarproducto', function (Illuminate\Http\Request  $request) {
        $term = $request->term ?: '';

        $tags = App\Modelos\Producto::where('activo','=',1)
        								->where('descripcion', 'like', '%'.$term.'%')
										->take(100)
										->pluck('descripcion','id');

        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);
    });
    
    
	// Route::any('/gestion-apafa/{idopcion}', 'GestionApafaConeiController@actionListarApafa');
	// Route::any('/agregar-requerimiento-apafa/{idopcion}', 'GestionApafaConeiController@actionAgregarApafa');
	// Route::any('/ajax-buscar-dni-ugel', 'GestionApafaConeiController@actionBuscardni');

	Route::any('/buscar-dni-01/{dni}', 'GestionApafaConeiController@actionBuscardni01');
	Route::any('/buscar-dni-02/{dni}', 'GestionApafaConeiController@actionBuscardni02');
	Route::any('/buscar-dni-03/{dni}', 'GestionApafaConeiController@actionBuscardni03');

	// Route::any('/gestion-conei/{idopcion}', 'GestionConeiController@actionListarConei');
	// Route::any('/agregar-requerimiento-conei/{idopcion}', 'GestionConeiController@actionAgregarConei');
	// Route::any('/ajax-modal-registro', 'GestionConeiController@actionModalRegistro');
	// Route::any('/ajax-modal-registro-oi', 'GestionConeiController@actionModalRegistroOI');
	// Route::any('/ajax-lista-tabla-oi', 'GestionConeiController@actionListaTablaOI');
	// Route::any('/ajax-elminar-fila-tabla-oi', 'GestionConeiController@actionEliminarFilaTablaOI');
	// Route::any('/ajax-modal-confirmar-registro', 'GestionConeiController@actionModalConfirmarRegistro');
	// Route::any('/detalle-conei/{idopcion}/{idconei}', 'GestionConeiController@actionDetalleConei');
	// Route::any('/descargar-archivo-requerimiento/{idopcion}/{idrequerimiento}/{idarchivo}', 'GestionConeiController@actionDescargarArchivosRequerimiento');

	/* SUBIR DOCENTE Y INTITUCION */
	Route::any('/gestion-de-institucion-docente/{idopcion}', 'CargarDatosInstitucionController@actionCargarDatos');
	Route::any('/formato-excel-cargar-datos-institucion-docente/{idopcion}', 'CargarDatosInstitucionController@actionDescargarFormatoCargaExcel');
	Route::any('/subir-excel-cargar-datos/{idopcion}', 'CargarDatosInstitucionController@actionCargarDato');
	// Route::any('/formato-excel-produccion-cargar-datos-produccion/{idopcion}', 'CargarDatosProduccionController@actionDescargarFormatoProduccionExcel');
	// Route::any('/formato-excel-departamentos-cargar-datos-produccion/{idopcion}', 'CargarDatosProduccionController@actionDescargarFormatoDepartamentosExcel');

	/* SUBIR CERTIFICADOS */
	Route::any('/gestion-de-registro-certificado/{idopcion}', 'GestionCertificadoController@actionListarCertificados');
	Route::any('/agregar-certificado/{idopcion}', 'GestionCertificadoController@actionAgregarCertificado');
	Route::any('/ajax-combo-periodo-xinstitucion', 'GestionCertificadoController@actionAjaxComboPeriodoxInstitucion');
	Route::any('/descargar-archivo-certificado/{idcertificado}/{idarchivo}', 'GestionCertificadoController@actionDescargarArchivosCertificado');
	Route::any('/modificar-certificado/{idopcion}/{idcertificado}', 'GestionCertificadoController@actionModificarCertificado');

	Route::any('/gestion-de-instituciones-certificado/{idopcion}', 'ReporteCertificadoController@actionListarCertificadosInstituciones');
	Route::any('/ajax-lista-instituciones-certificado', 'ReporteCertificadoController@actionAjaxListarInstitucionCertificado');



	Route::any('/gestion-cobro-venta/{idopcion}', 'GestionCajaController@actionListarCajaxCobrar');
	Route::any('/cobrar-caja-venta/{idopcion}/{idregistro}', 'GestionCajaController@actionCobrarCajaVenta');
	Route::any('/ajax-modal-detalle-cobro-venta', 'GestionCajaController@actionAjaxModalDetalleCobrarVenta');
	Route::any('/guardar-detalle-cobro-venta/{idopcion}/{idregistro}', 'GestionCajaController@actionAgregarDetalleCobroVentas');
	Route::any('/quitar-detalle-cobro-ventas/{idopcion}/{iddetalleregistro}', 'GestionCajaController@actionQuitarDetalleCobroVentas');	



	Route::any('/gestion-pago-compra/{idopcion}', 'GestionCajaController@actionListarCajaxPagar');
	Route::any('/pagar-caja-compra/{idopcion}/{idregistro}', 'GestionCajaController@actionPagarCajaCompra');
	Route::any('/ajax-modal-detalle-pago-compra', 'GestionCajaController@actionAjaxModalDetallePagoCompra');
	Route::any('/guardar-detalle-pago-compra/{idopcion}/{idregistro}', 'GestionCajaController@actionAgregarDetallePagoCompra');
	Route::any('/quitar-detalle-pago-cobro/{idopcion}/{iddetalleregistro}', 'GestionCajaController@actionQuitarDetallePagoCompra');	


	Route::any('/gestion-orden-venta/{idopcion}', 'GestionOrdenVentaController@actionListarOrdenesVenta');
	Route::any('/resumen-orden-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionResumenOrdenesVenta');
	Route::any('/agregar-orden-ventas/{idopcion}', 'GestionOrdenVentaController@actionAgregarOrdenesVenta');
	Route::any('/modificar-orden-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionModificarOrdenesVenta');
	Route::any('/ajax-modal-detalle-orden-venta', 'GestionOrdenVentaController@actionAjaxModalDetalleOrdenVenta');
	Route::any('/ajax-modal-agregar-cliente', 'GestionOrdenVentaController@actionAjaxModalAgregarCliente');
	Route::any('/guardar-detalle-orden-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionAgregarDetalleOrdenVentas');
	Route::any('/ajax-agregar-clientes/{idopcion}', 'GestionOrdenVentaController@actionAjaxAgregarCliente');
	Route::any('/ajax-gestion-de-orden-ventas-entrefechas', 'GestionOrdenVentaController@actionAjaxOrdenVentaEntreFechas');
	Route::any('/ajax-actualizar-envio-orden-venta', 'GestionOrdenVentaController@actionAjaxActualizarEnvioOrdenVenta');
	Route::any('/ajax-actualizar-descuento-orden-venta', 'GestionOrdenVentaController@actionAjaxActualizarDescuentoOrdenVenta');
	Route::any('/ajax-actualizar-seguro-orden-venta', 'GestionOrdenVentaController@actionAjaxActualizarSeguroOrdenVenta');

	Route::any('/validar-orden-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionValidarOrdenVenta');
	Route::any('/aprobar-orden-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionAprobarOrdenVenta');
	Route::any('/orden-ventas-esquema-producto/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionOrdenVentaEsquemaProductos');
	Route::any('/modificar-orden-ventas-esquema-productos/{idopcion}/{idesquemaproducto}/{idordenventa}', 'GestionOrdenVentaController@actionOrdenVentaModificarEsquemaProductos');
	


	Route::any('/orden-ventas-margen-producto/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionOrdenVentaMargenProductos');
	Route::any('/modificar-orden-ventas-margen-productos/{idopcion}/{idordenventa}', 'GestionOrdenVentaController@actionOrdenVentaModificarMargenProductos');
	Route::any('/ajax-cargar-preciounitario-producto', 'GestionOrdenVentaController@actionCargarPrecioUnitarioOrdenVentaAjax');
	Route::any('/quitar-detalle-orden-ventas/{idopcion}/{iddetalle}', 'GestionOrdenVentaController@actionQuitarDetalleOrdenVenta');	
	Route::any('/facturar-orden-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionFacturarOrdenesVenta');
	Route::any('/generar-emitir-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionGenerarEmitirVenta');
	Route::any('/comprar-orden-ventas/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionComprarOrdenesVenta');
	Route::any('/generar-emitir-compras/{idopcion}/{idregistro}', 'GestionOrdenVentaController@actionGenerarEmitirCompra');


	Route::any('/ajax-modal-detalle-gema-esquema', 'GestionOrdenVentaController@actionAjaxModalDetalleGemaEsquema');
	Route::any('/guardar-detalle-orden-gema/{idopcion}/{esquema_id}/{detalleesquema_id}/{ordenventa_id}', 'GestionOrdenVentaController@actionGuardarDetalleGemaEsquema');
	Route::any('/ajax-modal-eliminar-detalle-gema-esquema', 'GestionOrdenVentaController@actionAjaxModalEliminarDetalleGemaEsquema');
	Route::any('/eliminar-detalle-orden-gema/{idopcion}/{esquema_id}/{detalleesquema_id}/{ordenventa_id}', 'GestionOrdenVentaController@actionEliminarDetalleGemaEsquema');
	Route::any('/ajax-modal-agregar-detalle-gema-esquema', 'GestionOrdenVentaController@actionAjaxModalAgregarDetalleGemaEsquema');
	Route::any('/agregar-detalle-orden-gema/{idopcion}/{esquema_id}/{ordenventa_id}', 'GestionOrdenVentaController@actionAgregarDetalleGemaEsquema');


	// Route::any('/pagar-caja-compra/{idopcion}/{idregistro}', 'GestionCajaController@actionPagarCajaCompra');
	// Route::any('/ajax-modal-detalle-pago-compra', 'GestionCajaController@actionAjaxModalDetallePagoCompra');
	// Route::any('/guardar-detalle-pago-compra/{idopcion}/{idregistro}', 'GestionCajaController@actionAgregarDetallePagoCompra');
	// Route::any('/quitar-detalle-pago-cobro/{idopcion}/{iddetalleregistro}', 'GestionCajaController@actionQuitarDetallePagoCompra');	
	Route::any('/gestion-esquema-productos/{idopcion}', 'GestionEsquemaProductosController@actionListarProductosProducidos');
	Route::any('/agregar-esquema-productos/{idopcion}', 'GestionEsquemaProductosController@actionAgregarEsquemaProductos');




});

Route::get('/pruebaemail/{emailfrom}/{nombreusuario}', 'PruebasController@actionPruebaEmail');
