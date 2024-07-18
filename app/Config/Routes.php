<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Main');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Default route
$routes->get('/', 'Main::index');

// Additional routes
$routes->get('create', 'Main::create');
$routes->get('list', 'Main::list');
$routes->get('edit/(:num)', 'Main::edit/$1');
$routes->post('save', 'Main::save');
$routes->get('delete/(:num)', 'Main::delete/$1');
$routes->get('view/(:num)', 'Main::view_details/$1');

// Authentication routes
$routes->get('register', 'Main::register');
$routes->post('register', 'Main::register');
$routes->get('login', 'Main::login');
$routes->post('login', 'Main::login');
$routes->get('logout', 'Main::logout');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * This file will be included after the above $routes. You may place any additional routing here.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
