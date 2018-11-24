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

use App\Models\User;

$router->group(
    ['middleware' => 'jwt.auth:' . User::ROLE_ADMIN],
    function () use ($router) {
        $router->post('/products', 'ProductsController@create');
        $router->patch('/products/{id}', 'ProductsController@update');
        $router->delete('/products/{id}', 'ProductsController@delete');

        $router->post('/bundles', 'BundlesController@create');

        $router->patch('/products/{id}/discount/{amount}/fixed', 'ProductsController@fixedDiscount');
        $router->patch('/products/{id}/discount/{percent}', 'ProductsController@percentDiscount');
    }
);

$router->group(
    ['middleware' => 'jwt.auth:' . User::ROLE_ADMIN . ',' . User::ROLE_CUSTOMER],
    function () use ($router) {
        $router->get('/products', 'ProductsController@index');
        $router->post('/orders', 'OrdersController@create');
    }
);

$router->post('/users/authenticate', 'UsersController@authenticate');
