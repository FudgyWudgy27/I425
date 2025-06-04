<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: CityController.php
 * Description:
 */

namespace CourseProject\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use CourseProject\Models\City;
use CourseProject\Controllers\ControllerHelper as Helper;

class CityController {

    public function index(Request $request, Response $response, array $args) : Response {
        $results = City::getCities($request);
        return Helper::withJson($response, $results, 200);
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $city = City::with('properties')->findOrFail($id);
        $city = City::findOrFail($id);
        return Helper::withJson($response, $city, 200);
    }
}