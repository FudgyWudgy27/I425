<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: routes.php
 * Description:
 */

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {
    $app->group('/api/v1', function(RouteCollectorProxy $group) {
        //Route group for cities
        $group->group('/cities', function (RouteCollectorProxy $group) {
            //Call the index method defined in the CityController class
            $group->get('', 'City:index');
            //Call the view method defined in the CityController class
            $group->get('/{id}', 'City:view');
            $group->get('/{id}/properties', 'City:getProperties');
            $group->get('/{id}/realtors', 'City:viewRealtorsByCity');
        });

        //Route group for housetypes
        $group->group('/housetypes', function (RouteCollectorProxy $group) {
            //Call the index method defined in the HouseTypeController class
            $group->get('', 'HouseType:index');
            //Call the view method defined in the HouseTypeController class
            $group->get('/{id}', 'HouseType:view');
        });

        //Route group for status
        $group->group('/status', function (RouteCollectorProxy $group) {
            //Call the index method defined in the HouseTypeController class
            $group->get('', 'Status:index');
            //Call the view method defined in the HouseTypeController class
            $group->get('/{id}', 'Status:view');
        });

        //Route group for properties
        $group->group('/properties', function (RouteCollectorProxy $group) {
            $group->get('', 'Property:index');
            $group->get('/increasing', 'Property:increasing');
            $group->get('/analytics', 'Property:analytics');
            $group->get('/{id}', 'Property:view');
            $group->post('', 'Property:create');
            $group->put('/{id}', 'Property:update');
            $group->delete('/{id}', 'Property:delete');
        });

        //Route group for realtors
        $group->group('/realtors', function (RouteCollectorProxy $group) {
            //Call the index method defined in the HouseTypeController class
            $group->get('', 'Realtor:index');
            //Call the view method defined in the HouseTypeController class
            $group->get('/{id}', 'Realtor:view');
            $group->get('/{id}/cities', 'Realtor:viewRealtorCities');
        });
    });

    // Handle invalid routes
    $app->any('{route:.*}', function(Request $request, Response $response) {
        $response->getBody()->write("Page Not Found");
        return $response->withStatus(404);
    });

};
