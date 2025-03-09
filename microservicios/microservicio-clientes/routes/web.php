<?php

/** @var \Laravel\Lumen\Routing\Router $router */


// API Routes
$router->group(['prefix' => 'api'], function () use ($router) {
    // Clients routes
    $router->group(['prefix' => 'clients'], function () use ($router) {
        $router->get('/', 'ClientController@index');
        $router->post('/', 'ClientController@store');
        $router->get('/{id}', 'ClientController@show');
        $router->put('/{id}', 'ClientController@update');
        $router->delete('/{id}', 'ClientController@destroy');
        $router->get('/{id}/purchases', 'ClientController@purchaseHistory');
        $router->get('/{id}/interactions', 'ClientController@interactionHistory');

        // Client interests routes
        $router->get('/{id}/interests', 'ClientInterestController@getInterests');
        $router->post('/{id}/interests', 'ClientInterestController@addInterest');
        $router->put('/{clientId}/interests/{typeId}', 'ClientInterestController@updateInterest');
        $router->delete('/{clientId}/interests/{typeId}', 'ClientInterestController@removeInterest');
    });

    // Interactions routes
    $router->group(['prefix' => 'interactions'], function () use ($router) {
        $router->get('/', 'InteractionController@index');
        $router->post('/', 'InteractionController@store');
        $router->get('/{id}', 'InteractionController@show');
        $router->put('/{id}', 'InteractionController@update');
        $router->put('/{id}/complete', 'InteractionController@complete');
        $router->delete('/{id}', 'InteractionController@destroy');
    });

    // Vehicle types routes
    $router->group(['prefix' => 'vehicle-types'], function () use ($router) {
        $router->get('/', 'VehicleTypeController@index');
        $router->post('/', 'VehicleTypeController@store');
        $router->get('/{id}', 'VehicleTypeController@show');
        $router->put('/{id}', 'VehicleTypeController@update');
        $router->delete('/{id}', 'VehicleTypeController@destroy');
        $router->get('/{id}/interested-clients', 'VehicleTypeController@interestedClients');
    });

    // Search and statistics routes
    $router->group(['prefix' => 'search'], function () use ($router) {
        $router->get('/clients', 'SearchController@searchClients');
        $router->get('/advanced', 'SearchController@advancedSearch');
    });

    $router->group(['prefix' => 'statistics'], function () use ($router) {
        $router->get('/clients-by-source', 'StatisticsController@clientsBySource');
        $router->get('/interactions-by-type', 'StatisticsController@interactionsByType');
        $router->get('/interests-by-type', 'StatisticsController@interestsByType');
        $router->get('/clients-acquisition', 'StatisticsController@clientsAcquisition');
    });

    // Utility routes
    $router->get('/lead-sources', 'UtilityController@getLeadSources');
    $router->get('/interaction-types', 'UtilityController@getInteractionTypes');
    $router->get('/interaction-statuses', 'UtilityController@getInteractionStatuses');

    // Health check
    $router->get('/health', function () {
        return response()->json(['status' => 'ok', 'service' => 'clients']);
    });
});

// Ruta de documentación
$router->get('/api/docs', function () {
    return view('api_docs');
});

// Ruta raíz
$router->get('/', function () use ($router) {
    return response()->json([
        'service' => 'Clients Service',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});
