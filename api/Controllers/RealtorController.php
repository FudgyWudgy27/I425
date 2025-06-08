<?php
/**
 * Author: Allen Fudge
 * Date: 6/8/2025
 * File: RealtorController.php
 * Description:
 */

namespace CourseProject\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use CourseProject\Models\Realtor;
use CourseProject\Controllers\ControllerHelper as Helper;

class RealtorController{
    //list all realtors
    public function index(Request $request, Response $response, array $args) : Response {
        $results = Realtor::getRealtors($request);
        return Helper::withJson($response, $results, 200);
    }

    //view a specific realtor
    public function view(Request $request, Response $response, array $args) : Response {
        $results = Realtor::getRealtorById($args['id']);
        return Helper::withJson($response, $results, 200);
    }

    //View all cities of a realtor
    public function viewRealtorCities(Request $request, Response $response, array $args) : Response {
        $id = $args['id'];
        $results = Realtor::getCityByRealtor($id);
        return Helper::withJson($response, $results, 200);
    }
}