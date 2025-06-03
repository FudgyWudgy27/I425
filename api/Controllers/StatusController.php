<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: StatusController.php
 * Description:
 */

namespace CourseProject\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use CourseProject\Models\Status;
use CourseProject\Controllers\ControllerHelper as Helper;

class StatusController {
    //Retrieve all status types
    public function index(Request $request, Response $response, $args) {
        $results = Status::getStatus();
        return Helper::withJson($response, $results, 200);
    }

    //Retrieve a specific status type by ID
    public function view(Request $request, Response $response, $args) {
        $id = $args['id'];
        $results = Status::getStatusById($id);
        return Helper::withJson($response, $results, 200);
    }
}