<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home → leitet auf Arbeitsseite um (Login oder Admin)
$routes->get('/', 'Home::index');

// Auth Routes (öffentlich)
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('register', 'Auth::register', ['filter' => 'auth:admin']);
$routes->post('register', 'Auth::doRegister', ['filter' => 'auth:admin']);

// Admin Routes (nur für Admins)
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('/', 'Admin::index');
    
    // Klassen
    $routes->get('klassen', 'Admin::klassen');
    $routes->post('klassen/create', 'Admin::createKlasse');
    $routes->delete('klassen/(:num)', 'Admin::deleteKlasse/$1');
    $routes->get('klassen/(:num)', 'Admin::showKlasse/$1');
    
    // Schüler
    $routes->post('schueler/create', 'Admin::createSchueler');
    $routes->delete('schueler/(:num)', 'Admin::deleteSchueler/$1');
    
    // AGs
    $routes->get('clubs', 'Admin::clubs');
    $routes->post('clubs/create', 'Admin::createClub');
    $routes->delete('clubs/(:num)', 'Admin::deleteClub/$1');
});

// Klassen Routes (für Lehrer & Admins)
$routes->group('klassen', ['filter' => 'auth:teacher,admin'], function($routes) {
    $routes->get('/', 'Klassen::select');
    $routes->get('(:num)', 'Klassen::show/$1');
    $routes->post('(:num)/choices', 'Klassen::saveChoices/$1');
    $routes->get('(:num)/check', 'Klassen::checkCompletion/$1');
});

// Allocation Routes (für Koordinatoren & Admins)
$routes->group('allocation', ['filter' => 'auth:coordinator,admin'], function($routes) {
    $routes->get('/', 'Allocation::index');
    $routes->post('run', 'Allocation::run');
    $routes->get('results', 'Allocation::results');
    $routes->get('swaps', 'Allocation::swaps');
    $routes->post('swap', 'Allocation::performSwap');
    $routes->get('statistics', 'Allocation::statistics');
    $routes->get('export/(:alpha)', 'Allocation::export/$1');
});
