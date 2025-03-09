<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API Routes
$router->group(['prefix' => 'api'], function () use ($router) {
    // Auth routes
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('register', 'AuthController@register');
        $router->post('login', 'AuthController@login');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');
        $router->get('me', 'AuthController@me');
    });
    
    // User routes
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', 'UserController@index');
        $router->post('/', 'UserController@store');
        $router->get('/{id}', 'UserController@show');
        $router->put('/{id}', 'UserController@update');
        $router->delete('/{id}', 'UserController@destroy');
    });
    
    // Role routes
    $router->group(['prefix' => 'roles'], function () use ($router) {
        $router->get('/', 'RoleController@index');
        $router->post('/', 'RoleController@store');
        $router->get('/{id}', 'RoleController@show');
        $router->put('/{id}', 'RoleController@update');
        $router->delete('/{id}', 'RoleController@destroy');
    });
});