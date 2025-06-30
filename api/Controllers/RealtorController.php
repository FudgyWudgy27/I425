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

class RealtorController {
    // List all realtors or search by query
    public function index(Request $request, Response $response, array $args) : Response {
        $params = $request->getQueryParams();
        $term = array_key_exists('q', $params) ? $params['q'] : "";

        $results = ($term) ? Realtor::searchRealtors($term) : Realtor::getRealtors();

        return Helper::withJson($response, $results, 200);
    }

    // View a specific realtor by id
    public function view(Request $request, Response $response, array $args) : Response {
        $results = Realtor::getRealtorById($args['id']);
        return Helper::withJson($response, $results, 200);
    }

    // View all cities related to a realtor
    public function viewRealtorCities(Request $request, Response $response, array $args) : Response {
        $id = $args['id'];
        $results = Realtor::getCityByRealtor($id);
        return Helper::withJson($response, $results, 200);
    }

    // Create a new realtor
    public function create(Request $request, Response $response, array $args) : Response {
        $data = $request->getParsedBody();

        // Validate required fields
        if (empty($data['realtor_name']) || empty($data['phone']) || empty($data['email']) || empty($data['state'])) {
            $error = ['error' => 'Missing required fields'];
            return Helper::withJson($response, $error, 400);
        }

        $realtor = new Realtor();
        $realtor->realtor_name = $data['realtor_name'];
        $realtor->phone = $data['phone'];
        $realtor->email = $data['email'];
        $realtor->state = $data['state'];

        try {
            $realtor->save(); // Auto-generates realtor_id
        } catch (\Exception $e) {
            $error = ['error' => 'Failed to create realtor: ' . $e->getMessage()];
            return Helper::withJson($response, $error, 500);
        }

        return Helper::withJson($response, $realtor, 201);
    }

    // Update an existing realtor
    public function update(Request $request, Response $response, array $args) : Response {
        $id = $args['id'];
        $data = $request->getParsedBody();

        $realtor = Realtor::find($id);

        if (!$realtor) {
            return Helper::withJson($response, ['error' => 'Realtor not found'], 404);
        }

        // Update fields if provided
        if (!empty($data['realtor_name'])) {
            $realtor->realtor_name = $data['realtor_name'];
        }
        if (!empty($data['phone'])) {
            $realtor->phone = $data['phone'];
        }
        if (!empty($data['email'])) {
            $realtor->email = $data['email'];
        }
        if (!empty($data['state'])) {
            $realtor->state = $data['state'];
        }

        try {
            $realtor->save();
        } catch (\Exception $e) {
            return Helper::withJson($response, ['error' => 'Failed to update realtor: ' . $e->getMessage()], 500);
        }

        return Helper::withJson($response, $realtor, 200);
    }

    // Delete a realtor
    public function delete(Request $request, Response $response, array $args) : Response {
        $id = $args['id'];

        $realtor = Realtor::find($id);

        if (!$realtor) {
            return Helper::withJson($response, ['error' => 'Realtor not found'], 404);
        }

        try {
            $realtor->delete();
        } catch (\Exception $e) {
            return Helper::withJson($response, ['error' => 'Failed to delete realtor: ' . $e->getMessage()], 500);
        }

        return Helper::withJson($response, ['message' => 'Realtor deleted'], 200);
    }
}
