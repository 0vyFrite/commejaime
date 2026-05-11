<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DashboardController;
use App\Controllers\RegimesController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//Auth routes
$routes->get('/index','AuthController::loginForm');
$routes->get('/login','AuthController::loginForm');
$routes->post('/login','AuthController::login');
$routes->get('/logout','AuthController::logout');
 

//Inscription
$routes->get('/inscription','AuthController::inscriptionEtape1');
$routes->post('/inscription','AuthController::inscriptionEtape1Post');
$routes->get('/inscription/etape2','AuthController::inscriptionEtape2');
$routes->post('/inscription/etape2','AuthController::inscriptionEtape2Post');
 
//Dashboard
$routes->get('/dashboard','DashboardController::index');

//Regimes
$routes->get('/regimes/export/(:num)/(:num)','RegimesController::exportRegimePdf/$1/$2');
