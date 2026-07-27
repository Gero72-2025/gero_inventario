<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Usuarios::login');

$routes->get('login', 'Usuarios::login');
$routes->post('login', 'Usuarios::authenticate');
$routes->get('recuperar-password', 'Usuarios::forgotPassword');
$routes->post('recuperar-password', 'Usuarios::sendRecovery');
$routes->get('reset-password', 'Usuarios::resetPassword');
$routes->post('reset-password', 'Usuarios::updatePassword');
$routes->get('logout', 'Usuarios::logout');

$routes->get('usuarios', 'Usuarios::index');
$routes->get('usuarios/nuevo', 'Usuarios::create');
$routes->post('usuarios', 'Usuarios::store');
$routes->get('usuarios/editar/(:num)', 'Usuarios::edit/$1');
$routes->post('usuarios/actualizar/(:num)', 'Usuarios::update/$1');
$routes->post('usuarios/eliminar/(:num)', 'Usuarios::delete/$1');

$routes->get('configuraciones', 'Configuraciones::index');
$routes->get('configuraciones/nuevo', 'Configuraciones::create');
$routes->post('configuraciones', 'Configuraciones::store');
$routes->get('configuraciones/editar/(:num)', 'Configuraciones::edit/$1');
$routes->post('configuraciones/actualizar/(:num)', 'Configuraciones::update/$1');
$routes->post('configuraciones/eliminar/(:num)', 'Configuraciones::delete/$1');

$routes->get('roles', 'Roles::index');
$routes->get('roles/nuevo', 'Roles::create');
$routes->post('roles/guardar', 'Roles::store');
$routes->get('roles/editar/(:num)', 'Roles::edit/$1');
$routes->post('roles/actualizar/(:num)', 'Roles::update/$1');
$routes->post('roles/eliminar/(:num)', 'Roles::delete/$1');

$routes->get('etapas', 'Etapas::index');
$routes->get('etapas/nuevo', 'Etapas::create');
$routes->post('etapas/guardar', 'Etapas::store');
$routes->get('etapas/editar/(:num)', 'Etapas::edit/$1');
$routes->post('etapas/actualizar/(:num)', 'Etapas::update/$1');
$routes->post('etapas/eliminar/(:num)', 'Etapas::delete/$1');

$routes->get('permisos', 'Permisos::index');
$routes->post('permisos/sincronizar', 'Permisos::sincronizar');
$routes->post('permisos/eliminar/(:num)', 'Permisos::delete/$1');
$routes->get('permisos/roles', 'Permisos::gestionarPorRol');
$routes->post('permisos/roles/guardar', 'Permisos::guardarPermisosPorRol');

$routes->get('bodegas', 'Bodegas::index');
$routes->get('bodegas/nuevo', 'Bodegas::create');
$routes->post('bodegas', 'Bodegas::store');
$routes->get('bodegas/ver/(:num)', 'Bodegas::ver/$1');
$routes->get('bodegas/editar/(:num)', 'Bodegas::editar/$1');
$routes->post('bodegas/actualizar/(:num)', 'Bodegas::actualizar/$1');
$routes->post('bodegas/eliminar/(:num)', 'Bodegas::delete/$1');
$routes->get('bodegas/getById/(:num)', 'Bodegas::getById/$1');

$routes->get('renglones', 'Renglones::index');
$routes->get('renglones/nuevo', 'Renglones::create');
$routes->post('renglones', 'Renglones::store');
$routes->get('renglones/ver/(:num)', 'Renglones::ver/$1');
$routes->get('renglones/editar/(:num)', 'Renglones::editar/$1');
$routes->post('renglones/actualizar/(:num)', 'Renglones::actualizar/$1');
$routes->post('renglones/eliminar/(:num)', 'Renglones::delete/$1');
$routes->get('renglones/getById/(:num)', 'Renglones::getById/$1');

// Catálogo de Insumos
$routes->get('cat_insumos', 'CatInsumos::index');
$routes->get('cat_insumos/nuevo', 'CatInsumos::create');
$routes->post('cat_insumos', 'CatInsumos::store');
$routes->get('cat_insumos/ver/(:num)', 'CatInsumos::ver/$1');
$routes->get('cat_insumos/editar/(:num)', 'CatInsumos::editar/$1');
$routes->post('cat_insumos/actualizar/(:num)', 'CatInsumos::actualizar/$1');
$routes->post('cat_insumos/eliminar/(:num)', 'CatInsumos::delete/$1');
$routes->get('cat_insumos/getById/(:num)', 'CatInsumos::getById/$1');

// Rutas alternativas sin guiones/underscore
$routes->get('catinsumos', 'CatInsumos::index');
$routes->get('catinsumos/nuevo', 'CatInsumos::create');
$routes->post('catinsumos', 'CatInsumos::store');
$routes->get('catinsumos/ver/(:num)', 'CatInsumos::ver/$1');
$routes->get('catinsumos/editar/(:num)', 'CatInsumos::editar/$1');
$routes->post('catinsumos/actualizar/(:num)', 'CatInsumos::actualizar/$1');
$routes->post('catinsumos/eliminar/(:num)', 'CatInsumos::delete/$1');
$routes->get('catinsumos/getById/(:num)', 'CatInsumos::getById/$1');

$routes->get('divisiones', 'Divisiones::index');
$routes->get('divisiones/nuevo', 'Divisiones::create');
$routes->post('divisiones/guardar', 'Divisiones::store');
$routes->get('divisiones/editar/(:num)', 'Divisiones::edit/$1');
$routes->post('divisiones/actualizar/(:num)', 'Divisiones::update/$1');
$routes->post('divisiones/eliminar/(:num)', 'Divisiones::delete/$1');

$routes->get('ejercicios-fiscales', 'EjerciciosFiscales::index');
$routes->get('ejercicios-fiscales/nuevo', 'EjerciciosFiscales::create');
$routes->post('ejercicios-fiscales/guardar', 'EjerciciosFiscales::store');
$routes->get('ejercicios-fiscales/editar/(:num)', 'EjerciciosFiscales::edit/$1');
$routes->post('ejercicios-fiscales/actualizar/(:num)', 'EjerciciosFiscales::update/$1');
$routes->post('ejercicios-fiscales/eliminar/(:num)', 'EjerciciosFiscales::delete/$1');

// Rutas alternativas sin guiones
$routes->get('ejerciciosfiscales', 'EjerciciosFiscales::index');
$routes->get('ejerciciosfiscales/nuevo', 'EjerciciosFiscales::create');
$routes->post('ejerciciosfiscales/guardar', 'EjerciciosFiscales::store');
$routes->get('ejerciciosfiscales/editar/(:num)', 'EjerciciosFiscales::edit/$1');
$routes->post('ejerciciosfiscales/actualizar/(:num)', 'EjerciciosFiscales::update/$1');
$routes->post('ejerciciosfiscales/eliminar/(:num)', 'EjerciciosFiscales::delete/$1');

$routes->get('proveedores', 'Proveedores::index');
$routes->get('proveedores/nuevo', 'Proveedores::create');
$routes->post('proveedores/guardar', 'Proveedores::store');
$routes->get('proveedores/editar/(:num)', 'Proveedores::edit/$1');
$routes->post('proveedores/actualizar/(:num)', 'Proveedores::update/$1');
$routes->post('proveedores/eliminar/(:num)', 'Proveedores::delete/$1');

$routes->get('agregado-ejercicios-fiscales', 'AgregadoEjerciciosFiscales::index');
$routes->get('agregado-ejercicios-fiscales/nuevo', 'AgregadoEjerciciosFiscales::create');
$routes->post('agregado-ejercicios-fiscales/guardar', 'AgregadoEjerciciosFiscales::store');
$routes->get('agregado-ejercicios-fiscales/editar/(:num)', 'AgregadoEjerciciosFiscales::edit/$1');
$routes->post('agregado-ejercicios-fiscales/actualizar/(:num)', 'AgregadoEjerciciosFiscales::update/$1');
$routes->post('agregado-ejercicios-fiscales/eliminar/(:num)', 'AgregadoEjerciciosFiscales::delete/$1');

// Rutas alternativas sin guiones
$routes->get('agregadoejerciciosfiscales', 'AgregadoEjerciciosFiscales::index');
$routes->get('agregadoejerciciosfiscales/nuevo', 'AgregadoEjerciciosFiscales::create');
$routes->post('agregadoejerciciosfiscales/guardar', 'AgregadoEjerciciosFiscales::store');
$routes->get('agregadoejerciciosfiscales/editar/(:num)', 'AgregadoEjerciciosFiscales::edit/$1');
$routes->post('agregadoejerciciosfiscales/actualizar/(:num)', 'AgregadoEjerciciosFiscales::update/$1');
$routes->post('agregadoejerciciosfiscales/eliminar/(:num)', 'AgregadoEjerciciosFiscales::delete/$1');

$routes->get('presupuestos-divisiones', 'PresupuestosDivisiones::index');
$routes->get('presupuestos-divisiones/nuevo', 'PresupuestosDivisiones::create');
$routes->post('presupuestos-divisiones/guardar', 'PresupuestosDivisiones::store');
$routes->get('presupuestos-divisiones/editar/(:num)', 'PresupuestosDivisiones::edit/$1');
$routes->post('presupuestos-divisiones/actualizar/(:num)', 'PresupuestosDivisiones::update/$1');
$routes->post('presupuestos-divisiones/eliminar/(:num)', 'PresupuestosDivisiones::delete/$1');

// Rutas alternativas sin guiones
$routes->get('presupuestosdivisiones', 'PresupuestosDivisiones::index');
$routes->get('presupuestosdivisiones/nuevo', 'PresupuestosDivisiones::create');
$routes->post('presupuestosdivisiones/guardar', 'PresupuestosDivisiones::store');
$routes->get('presupuestosdivisiones/editar/(:num)', 'PresupuestosDivisiones::edit/$1');
$routes->post('presupuestosdivisiones/actualizar/(:num)', 'PresupuestosDivisiones::update/$1');
$routes->post('presupuestosdivisiones/eliminar/(:num)', 'PresupuestosDivisiones::delete/$1');

$routes->get('presupuestos-renglones', 'PresupuestosRenglones::index');
$routes->get('presupuestos-renglones/nuevo', 'PresupuestosRenglones::create');
$routes->post('presupuestos-renglones/guardar', 'PresupuestosRenglones::store');
$routes->get('presupuestos-renglones/ver/(:num)', 'PresupuestosRenglones::ver/$1');
$routes->get('presupuestos-renglones/editar/(:num)', 'PresupuestosRenglones::editar/$1');
$routes->post('presupuestos-renglones/actualizar/(:num)', 'PresupuestosRenglones::actualizar/$1');
$routes->post('presupuestos-renglones/eliminar/(:num)', 'PresupuestosRenglones::delete/$1');
$routes->get('presupuestos-renglones/getById/(:num)', 'PresupuestosRenglones::getById/$1');
$routes->get('presupuestos-renglones/obtener-disponible/(:num)', 'PresupuestosRenglones::obtenerDisponible/$1');

// Rutas alternativas sin guiones
$routes->get('presupuestosrenglones', 'PresupuestosRenglones::index');
$routes->get('presupuestosrenglones/nuevo', 'PresupuestosRenglones::create');
$routes->post('presupuestosrenglones/guardar', 'PresupuestosRenglones::store');
$routes->get('presupuestosrenglones/ver/(:num)', 'PresupuestosRenglones::ver/$1');
$routes->get('presupuestosrenglones/editar/(:num)', 'PresupuestosRenglones::editar/$1');
$routes->post('presupuestosrenglones/actualizar/(:num)', 'PresupuestosRenglones::actualizar/$1');
$routes->post('presupuestosrenglones/eliminar/(:num)', 'PresupuestosRenglones::delete/$1');
$routes->get('presupuestosrenglones/getById/(:num)', 'PresupuestosRenglones::getById/$1');
$routes->get('presupuestosrenglones/obtener-disponible/(:num)', 'PresupuestosRenglones::obtenerDisponible/$1');

$routes->get('empleados', 'Empleados::index');
$routes->get('empleados/nuevo', 'Empleados::create');
$routes->post('empleados/guardar', 'Empleados::store');
$routes->get('empleados/editar/(:num)', 'Empleados::edit/$1');
$routes->post('empleados/actualizar/(:num)', 'Empleados::update/$1');
$routes->post('empleados/eliminar/(:num)', 'Empleados::delete/$1');

// Rutas alternativas sin guiones
$routes->get('empleados', 'Empleados::index');
$routes->get('empleados/nuevo', 'Empleados::create');
$routes->post('empleados/guardar', 'Empleados::store');
$routes->get('empleados/editar/(:num)', 'Empleados::edit/$1');
$routes->post('empleados/actualizar/(:num)', 'Empleados::update/$1');
$routes->post('empleados/eliminar/(:num)', 'Empleados::delete/$1');

$routes->get('contratos-empleados', 'ContratosEmpleados::index');
$routes->get('contratos-empleados/nuevo', 'ContratosEmpleados::create');
$routes->post('contratos-empleados/guardar', 'ContratosEmpleados::store');
$routes->get('contratos-empleados/editar/(:num)', 'ContratosEmpleados::edit/$1');
$routes->post('contratos-empleados/actualizar/(:num)', 'ContratosEmpleados::update/$1');
$routes->post('contratos-empleados/eliminar/(:num)', 'ContratosEmpleados::delete/$1');
$routes->get('contratos-empleados/descargar-pdf/(:num)', 'ContratosEmpleados::descargarPdf/$1');

// Rutas alternativas sin guiones
$routes->get('contratosempleados', 'ContratosEmpleados::index');
$routes->get('contratosempleados/nuevo', 'ContratosEmpleados::create');
$routes->post('contratosempleados/guardar', 'ContratosEmpleados::store');
$routes->get('contratosempleados/editar/(:num)', 'ContratosEmpleados::edit/$1');
$routes->post('contratosempleados/actualizar/(:num)', 'ContratosEmpleados::update/$1');
$routes->post('contratosempleados/eliminar/(:num)', 'ContratosEmpleados::delete/$1');
$routes->get('contratosempleados/descargar-pdf/(:num)', 'ContratosEmpleados::descargarPdf/$1');

$routes->get('solicitudes-encabezado', 'SolicitudesEncabezado::index');
$routes->get('solicitudes-encabezado/nuevo', 'SolicitudesEncabezado::create');
$routes->post('solicitudes-encabezado', 'SolicitudesEncabezado::store');
$routes->get('solicitudes-encabezado/ver/(:num)', 'SolicitudesEncabezado::ver/$1');
$routes->get('solicitudes-encabezado/editar/(:num)', 'SolicitudesEncabezado::editar/$1');
$routes->post('solicitudes-encabezado/actualizar/(:num)', 'SolicitudesEncabezado::actualizar/$1');
$routes->post('solicitudes-encabezado/eliminar/(:num)', 'SolicitudesEncabezado::delete/$1');
$routes->get('solicitudes-encabezado/descargar-pdf/(:num)', 'SolicitudesEncabezado::descargarPdf/$1');

// Rutas para solicitudes_detalle
$routes->get('solicitudes-detalle', 'SolicitudesDetalle::index');
$routes->get('solicitudes-detalle/nuevo', 'SolicitudesDetalle::create');
$routes->post('solicitudes-detalle', 'SolicitudesDetalle::store');
$routes->get('solicitudes-detalle/editar/(:num)', 'SolicitudesDetalle::edit/$1');
$routes->post('solicitudes-detalle/actualizar/(:num)', 'SolicitudesDetalle::update/$1');
$routes->post('solicitudes-detalle/eliminar/(:num)', 'SolicitudesDetalle::delete/$1');

// Rutas alternativas sin guiones
$routes->get('solicitudesdetalle', 'SolicitudesDetalle::index');
$routes->get('solicitudesdetalle/nuevo', 'SolicitudesDetalle::create');
$routes->post('solicitudesdetalle', 'SolicitudesDetalle::store');
$routes->get('solicitudesdetalle/editar/(:num)', 'SolicitudesDetalle::edit/$1');
$routes->post('solicitudesdetalle/actualizar/(:num)', 'SolicitudesDetalle::update/$1');
$routes->post('solicitudesdetalle/eliminar/(:num)', 'SolicitudesDetalle::delete/$1');

$routes->get('solicitudes-historial-etapas', 'SolicitudesHistorialEtapas::index');
$routes->get('solicitudes-historial-etapas/nuevo', 'SolicitudesHistorialEtapas::create');
$routes->post('solicitudes-historial-etapas', 'SolicitudesHistorialEtapas::store');
$routes->get('solicitudes-historial-etapas/ver/(:num)', 'SolicitudesHistorialEtapas::ver/$1');
$routes->get('solicitudes-historial-etapas/editar/(:num)', 'SolicitudesHistorialEtapas::editar/$1');
$routes->post('solicitudes-historial-etapas/actualizar/(:num)', 'SolicitudesHistorialEtapas::actualizar/$1');
$routes->post('solicitudes-historial-etapas/eliminar/(:num)', 'SolicitudesHistorialEtapas::delete/$1');

// Rutas alternativas sin guiones
$routes->get('solicitudesencabezado', 'SolicitudesEncabezado::index');
$routes->get('solicitudesencabezado/nuevo', 'SolicitudesEncabezado::create');
$routes->post('solicitudesencabezado', 'SolicitudesEncabezado::store');
$routes->get('solicitudesencabezado/ver/(:num)', 'SolicitudesEncabezado::ver/$1');
$routes->get('solicitudesencabezado/editar/(:num)', 'SolicitudesEncabezado::editar/$1');
$routes->post('solicitudesencabezado/actualizar/(:num)', 'SolicitudesEncabezado::actualizar/$1');
$routes->post('solicitudesencabezado/eliminar/(:num)', 'SolicitudesEncabezado::delete/$1');
$routes->get('solicitudesencabezado/descargar-pdf/(:num)', 'SolicitudesEncabezado::descargarPdf/$1');

$routes->get('solicitudeshistorialetapas', 'SolicitudesHistorialEtapas::index');
$routes->get('solicitudeshistorialetapas/nuevo', 'SolicitudesHistorialEtapas::create');
$routes->post('solicitudeshistorialetapas', 'SolicitudesHistorialEtapas::store');
$routes->get('solicitudeshistorialetapas/ver/(:num)', 'SolicitudesHistorialEtapas::ver/$1');
$routes->get('solicitudeshistorialetapas/editar/(:num)', 'SolicitudesHistorialEtapas::editar/$1');
$routes->post('solicitudeshistorialetapas/actualizar/(:num)', 'SolicitudesHistorialEtapas::actualizar/$1');
$routes->post('solicitudeshistorialetapas/eliminar/(:num)', 'SolicitudesHistorialEtapas::delete/$1');

$routes->get('facturas-liquidacion', 'FacturasLiquidacion::index');
$routes->get('facturas-liquidacion/nuevo', 'FacturasLiquidacion::create');
$routes->post('facturas-liquidacion/guardar', 'FacturasLiquidacion::store');
$routes->get('facturas-liquidacion/editar/(:num)', 'FacturasLiquidacion::edit/$1');
$routes->post('facturas-liquidacion/actualizar/(:num)', 'FacturasLiquidacion::update/$1');
$routes->post('facturas-liquidacion/eliminar/(:num)', 'FacturasLiquidacion::delete/$1');

// Rutas alternativas sin guiones
$routes->get('facturasliquidacion', 'FacturasLiquidacion::index');
$routes->get('facturasliquidacion/nuevo', 'FacturasLiquidacion::create');
$routes->post('facturasliquidacion/guardar', 'FacturasLiquidacion::store');
$routes->get('facturasliquidacion/editar/(:num)', 'FacturasLiquidacion::edit/$1');
$routes->post('facturasliquidacion/actualizar/(:num)', 'FacturasLiquidacion::update/$1');
$routes->post('facturasliquidacion/eliminar/(:num)', 'FacturasLiquidacion::delete/$1');
