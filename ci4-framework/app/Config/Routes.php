<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Police Station routes
$routes->group('station', function($routes) {
    $routes->get('login', 'Station\AuthController::login');
    $routes->post('authenticate', 'Station\AuthController::authenticate');
    $routes->get('logout', 'Station\AuthController::logout');
    $routes->get('dashboard', 'Station\DashboardController::index', ['filter' => 'stationauth']);
    
    // Officers Management (Station-scoped)
    $routes->get('officers', 'Station\OfficerController::index', ['filter' => 'stationauth']);
    $routes->get('officers/create', 'Station\OfficerController::create', ['filter' => 'stationauth']);
    $routes->post('officers/store', 'Station\OfficerController::store', ['filter' => 'stationauth']);
    $routes->get('officers/edit/(:num)', 'Station\OfficerController::edit/$1', ['filter' => 'stationauth']);
    $routes->post('officers/update/(:num)', 'Station\OfficerController::update/$1', ['filter' => 'stationauth']);
    $routes->get('officers/delete/(:num)', 'Station\OfficerController::delete/$1', ['filter' => 'stationauth']);
    
    // Duty Allocation (Station-scoped)
    $routes->get('duties', 'Station\DutyController::index', ['filter' => 'stationauth']);
    $routes->get('duties/create', 'Station\DutyController::create', ['filter' => 'stationauth']);
    $routes->post('duties/store', 'Station\DutyController::store', ['filter' => 'stationauth']);
    $routes->get('duties/edit/(:num)', 'Station\DutyController::edit/$1', ['filter' => 'stationauth']);
    $routes->post('duties/update/(:num)', 'Station\DutyController::update/$1', ['filter' => 'stationauth']);
    $routes->get('duties/delete/(:num)', 'Station\DutyController::delete/$1', ['filter' => 'stationauth']);
    
    // Points Management (Station-scoped)
    $routes->get('points', 'Station\PointController::index', ['filter' => 'stationauth']);
    $routes->get('points/create', 'Station\PointController::create', ['filter' => 'stationauth']);
    $routes->post('points/store', 'Station\PointController::store', ['filter' => 'stationauth']);
    $routes->get('points/edit/(:num)', 'Station\PointController::edit/$1', ['filter' => 'stationauth']);
    $routes->post('points/update/(:num)', 'Station\PointController::update/$1', ['filter' => 'stationauth']);
    $routes->get('points/delete/(:num)', 'Station\PointController::delete/$1', ['filter' => 'stationauth']);
    
    // Compliance Tracking (Station-scoped)
    $routes->get('compliance', 'Station\ComplianceController::index', ['filter' => 'stationauth']);
    $routes->get('compliance/officer/(:num)', 'Station\ComplianceController::officer/$1', ['filter' => 'stationauth']);
    $routes->get('compliance/duty/(:num)', 'Station\ComplianceController::duty/$1', ['filter' => 'stationauth']);
});

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
    $routes->get('compliance/live-data', 'Admin\ComplianceController::liveData', ['filter' => 'adminauth']);
    
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

// Handle all OPTIONS requests for CORS preflight
$routes->options('api/(:any)', function() {
    $response = service('response');
    return $response->setHeader('Access-Control-Allow-Origin', '*')
                   ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                   ->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization')
                   ->setStatusCode(200);
});

// Mobile API routes for Capacitor app (with CORS support)
$routes->group('api', function($routes) {
    // Mobile Authentication
    $routes->options('auth/mobile-login', 'Api\AuthController::mobileLogin', ['filter' => 'cors']);
    $routes->post('auth/mobile-login', 'Api\AuthController::mobileLogin', ['filter' => 'cors']);
    $routes->post('auth/verify-token', 'Api\AuthController::verifyToken', ['filter' => 'cors']);
    
    // Mobile Duty Management
    $routes->get('duties/active/(:num)', 'Api\DutyController::getActiveDuty/$1', ['filter' => 'cors']);
    $routes->get('duties/officer/(:num)', 'Api\DutyController::getOfficerDuties/$1', ['filter' => 'cors']);
    $routes->post('duties/start', 'Api\DutyController::startDuty', ['filter' => 'cors']);
    $routes->post('duties/end', 'Api\DutyController::endDuty', ['filter' => 'cors']);
    
    // Mobile Location Tracking
    $routes->post('location/log', 'Api\LocationController::log', ['filter' => 'cors']);
    $routes->get('location/status/(:num)', 'Api\LocationController::status/$1', ['filter' => 'cors']);
    $routes->post('location/check-radius', 'Api\LocationController::checkRadius', ['filter' => 'cors']);
    
    // Mobile Compliance
    $routes->get('compliance/officer/(:num)', 'Api\ComplianceController::getOfficerCompliance/$1', ['filter' => 'cors']);
    $routes->get('compliance/duty/(:num)', 'Api\ComplianceController::getDutyCompliance/$1', ['filter' => 'cors']);
});

// API routes for compliance tracking
$routes->post('api/compliance/speedometer', 'Api\ComplianceController::speedometer');
$routes->post('api/compliance/check-alert', 'Api\ComplianceController::checkAlert');
$routes->post('api/compliance/record-alert', 'Api\ComplianceController::recordAlert');
$routes->get('api/compliance/admin-status', 'Api\ComplianceController::adminStatus');
$routes->post('api/compliance/recalculate', 'Api\ComplianceController::recalculate');
$routes->post('api/compliance/calculate-completed', 'Api\ComplianceController::calculateCompleted');
$routes->get('api/compliance/admin-data', 'Api\ComplianceController::adminData');
