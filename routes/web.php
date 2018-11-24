<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/products', 'ProductsController@index');
$router->post('/products', 'ProductsController@create');
$router->patch('/products/{id}', 'ProductsController@update');
$router->delete('/products/{id}', 'ProductsController@delete');
$router->patch('/products/{id}/discount/{amount}/fixed', 'ProductsController@fixedDiscount');
$router->patch('/products/{id}/discount/{percent}', 'ProductsController@percentDiscount');

$router->post('/bundles', 'BundlesController@create');
$router->post('/orders', 'OrdersController@create');
