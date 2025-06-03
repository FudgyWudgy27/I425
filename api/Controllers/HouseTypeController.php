<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: HouseTypeController.php
 * Description:
 */

namespace CourseProject\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use CourseProject\Models\HouseType;
use CourseProject\Controllers\ControllerHelper as Helper;

class HouseTypeController {
    //Retrieve all house types
    public function index(Request $request, Response $response, $args) {
        $results = HouseType::getHouseType();
        return Helper::withJson($response, $results, 200);
    }

    //Retrieve a specific house type by ID
    public function view(Request $request, Response $response, $args) {
        $id = $args['id'];
        $results = HouseType::getHouseTypeById($id);
        return Helper::withJson($response, $results, 200);
    }
}