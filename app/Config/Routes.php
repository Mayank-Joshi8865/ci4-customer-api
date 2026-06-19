<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Web\DashboardController::index');
$routes->get('login', 'Web\AuthController::showLogin');
$routes->post('login', 'Web\AuthController::login');
$routes->get('logout', 'Web\AuthController::logout');
$routes->get('dashboard', 'Web\DashboardController::index');
$routes->get('customers', 'Web\CustomerController::index');
$routes->get('customers/(:num)', 'Web\CustomerController::show/$1');
$routes->post('customers/(:num)/payment-status', 'Web\CustomerController::updatePaymentStatus/$1');
$routes->post('customers/(:num)/send-notification', 'Web\CustomerController::sendNotification/$1');
$routes->get('admin/upload-csv', 'Web\CustomerController::uploadForm');
$routes->post('admin/upload-csv', 'Web\CustomerController::uploadCsv');
$routes->get('reports', 'Web\ReportController::index');

$routes->group('api', static function (RouteCollection $routes): void {
    $routes->post('login', 'Api\AuthController::login');

    $routes->get('customers', 'Api\CustomerController::index', ['filter' => 'authToken']);
    $routes->get('reports/summary', 'Api\ReportController::summary', ['filter' => 'authToken']);

    $routes->post('admin/upload-csv', 'Api\CustomerController::uploadCsv', ['filter' => 'role:admin']);
    $routes->put('customer/(:num)/payment-status', 'Api\CustomerController::updatePaymentStatus/$1', ['filter' => 'role:user']);
    $routes->post('customer/(:num)/send-notification', 'Api\CustomerController::sendNotification/$1', ['filter' => 'role:user']);
});
