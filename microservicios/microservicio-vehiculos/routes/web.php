<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'api'], function () use ($router) {
    // Vehicles routes
    $router->group(['prefix' => 'vehicles'], function () use ($router) {
        $router->get('/', 'VehicleController@index');
        $router->post('/', 'VehicleController@store');
        $router->get('/{id}', 'VehicleController@show');
        $router->put('/{id}', 'VehicleController@update');
        $router->put('/{id}/status', 'VehicleController@updateStatus');
        $router->delete('/{id}', 'VehicleController@destroy');

        // Vehicle images routes
        $router->get('/{id}/images', 'VehicleImageController@getByVehicle');
        $router->post('/{id}/images', 'VehicleImageController@upload');
        $router->get('/{id}/images/main', 'VehicleImageController@getMainImage');
        $router->put('/{id}/images/{imageId}/main', 'VehicleImageController@setMainImage');
        $router->put('/{id}/images/{imageId}', 'VehicleImageController@updateMetadata');
        $router->delete('/{id}/images/{imageId}', 'VehicleImageController@destroy');
        $router->put('/{id}/images/reorder', 'VehicleImageController@reorder');

        // Technical specifications routes
        $router->get('/{id}/specs', 'TechnicalSpecificationController@getByVehicle');
        $router->post('/{id}/specs', 'TechnicalSpecificationController@store');
        $router->put('/{id}/specs', 'TechnicalSpecificationController@update');
        $router->delete('/{id}/specs', 'TechnicalSpecificationController@destroy');
    });

    // Compare vehicles technical specifications
    $router->get('/vehicles/compare/{vehicleId1}/{vehicleId2}', 'TechnicalSpecificationController@compare');

    // Brands routes
    $router->group(['prefix' => 'brands'], function () use ($router) {
        $router->get('/', 'BrandController@index');
        $router->post('/', 'BrandController@store');
        $router->get('/{id}', 'BrandController@show');
        $router->put('/{id}', 'BrandController@update');
        $router->delete('/{id}', 'BrandController@destroy');
        $router->get('/{id}/models', 'BrandController@getModels');
        $router->post('/{id}/logo', 'BrandController@uploadLogo');
    });

    // Vehicle models routes
    $router->group(['prefix' => 'models'], function () use ($router) {
        $router->get('/', 'VehicleModelController@index');
        $router->post('/', 'VehicleModelController@store');
        $router->get('/{id}', 'VehicleModelController@show');
        $router->put('/{id}', 'VehicleModelController@update');
        $router->delete('/{id}', 'VehicleModelController@destroy');
        $router->get('/{id}/vehicles', 'VehicleModelController@getVehicles');
        $router->get('/types', 'VehicleModelController@getTypes');
    });

    // Health check
    $router->get('/health', function () {
        return response()->json(['status' => 'ok', 'service' => 'vehicles']);
    });
});

// Ruta de documentación
$router->get('/api/docs', function () {
    return view('api_docs');
});

// Ruta raíz
$router->get('/', function () use ($router) {
    return response()->json([
        'service' => 'Vehicles Service',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});
