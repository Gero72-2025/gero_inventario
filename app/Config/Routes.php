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
