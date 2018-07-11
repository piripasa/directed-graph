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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api', 'namespace' => 'Api'], function () use ($router) {
    $router->post('nodes/connect', 'NodeController@connect');
    $router->get('nodes/paths', 'NodeController@shortestPath');

    $router->post('nodes', 'NodeController@store');

//    $router->resource('nodes', 'NodeController', ['only' => [
//        'index', 'store', 'show', 'update', 'destroy'
//    ]]);
});