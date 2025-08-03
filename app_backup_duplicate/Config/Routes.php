<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Admin routes
$routes->group('admin', function($routes) {
    $routes->get('login', 'Admin\AuthController::login');
    $routes->post('login', 'Admin\AuthController::authenticate');
    $routes->get('logout', 'Admin\AuthController::logout');
    $routes->get('dashboard', 'Admin\DashboardController::index', ['filter' => 'adminauth']);
    
    // Point Management
    $routes->get('points', 'Admin\PointController::index', ['filter' => 'adminauth']);
    $routes->get('points/create', 'Admin\PointController::create', ['filter' => 'adminauth']);
    $routes->post('points/store', 'Admin\PointController::store', ['filter' => 'adminauth']);
    $routes->get('points/edit/(:num)', 'Admin\PointController::edit/$1', ['filter' => 'adminauth']);
    $routes->post('points/update/(:num)', 'Admin\PointController::update/$1', ['filter' => 'adminauth']);
    $routes->get('points/delete/(:num)', 'Admin\PointController::delete/$1', ['filter' => 'adminauth']);
    
    // Duty Allocation
    $routes->get('duties', 'Admin\DutyController::index', ['filter' => 'adminauth']);
    $routes->get('duties/create', 'Admin\DutyController::create', ['filter' => 'adminauth']);
    $routes->post('duties/store', 'Admin\DutyController::store', ['filter' => 'adminauth']);
    $routes->get('duties/edit/(:num)', 'Admin\DutyController::edit/$1', ['filter' => 'adminauth']);
    $routes->post('duties/update/(:num)', 'Admin\DutyController::update/$1', ['filter' => 'adminauth']);
    $routes->get('duties/delete/(:num)', 'Admin\DutyController::delete/$1', ['filter' => 'adminauth']);
    
    // Officers Management
    $routes->get('officers', 'Admin\OfficerController::index', ['filter' => 'adminauth']);
    $routes->get('officers/create', 'Admin\OfficerController::create', ['filter' => 'adminauth']);
    $routes->post('officers/store', 'Admin\OfficerController::store', ['filter' => 'adminauth']);
    $routes->get('officers/edit/(:num)', 'Admin\OfficerController::edit/$1', ['filter' => 'adminauth']);
    $routes->post('officers/update/(:num)', 'Admin\OfficerController::update/$1', ['filter' => 'adminauth']);
    
    // Compliance Tracking
    $routes->get('compliance', 'Admin\ComplianceController::index', ['filter' => 'adminauth']);
    $routes->get('compliance/officer/(:num)', 'Admin\ComplianceController::officer/$1', ['filter' => 'adminauth']);
    $routes->get('compliance/duty/(:num)', 'Admin\ComplianceController::duty/$1', ['filter' => 'adminauth']);
    $routes->get('compliance/live', 'Admin\ComplianceController::live', ['filter' => 'adminauth']);
    
    // AJAX routes
    $routes->get('api/officers', 'Admin\ApiController::getOfficers', ['filter' => 'adminauth']);
    $routes->get('api/points', 'Admin\ApiController::getPoints', ['filter' => 'adminauth']);
    $routes->get('api/compliance/(:num)', 'Admin\ApiController::getCompliance/$1', ['filter' => 'adminauth']);
});

// Officer routes
$routes->group('officer', function($routes) {
    $routes->get('login', 'Officer\AuthController::login');
    $routes->post('login', 'Officer\AuthController::authenticate');
    $routes->get('logout', 'Officer\AuthController::logout');
    $routes->get('dashboard', 'Officer\DashboardController::index', ['filter' => 'officerauth']);
    $routes->get('duty/(:num)', 'Officer\DutyController::view/$1', ['filter' => 'officerauth']);
    $routes->post('location/update', 'Officer\LocationController::update', ['filter' => 'officerauth']);
    $routes->get('location/consent', 'Officer\LocationController::consent', ['filter' => 'officerauth']);
    $routes->post('location/grant-consent', 'Officer\LocationController::grantConsent', ['filter' => 'officerauth']);
});

// API routes for location tracking
$routes->post('api/location/log', 'Api\LocationController::log');
$routes->get('api/location/status/(:num)', 'Api\LocationController::status/$1');
