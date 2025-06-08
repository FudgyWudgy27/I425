<?php
/**
 * Author: your name
 * Date: 6/3/2025
 * File: PropertyController.php
 * Description:
 */
namespace CourseProject\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use CourseProject\Models\Property;
use CourseProject\Controllers\ControllerHelper;
use Illuminate\Support\Facades\DB;
use CourseProject\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;


class PropertyController
{
    // Get all properties with enhanced data
    public function index(Request $request, Response $response): Response
    {
        $properties = Property::with(['city', 'houseType', 'status'])
            ->get()
            ->map(function ($property) {
                return [
                    'property_id' => $property->property_id,
                    'address' => "{$property->city->city_name}, {$property->city->state}",
                    'type' => $property->houseType->house_type,
                    'status' => $property->status->status_name,
                    'values' => $property->formatted_values,
                    'trend' => $property->quarterly_trend,
                    'monthly_changes' => $property->monthly_changes
                ];
            });

        return ControllerHelper::withJson($response, $properties, 200);
    }

    // Get properties with value increases
    public function increasing(Request $request, Response $response): Response
    {
        $properties = Property::withValueIncrease()
            ->with(['city', 'houseType'])
            ->get()
            ->map(function ($property) {
                return [
                    'property_id' => $property->property_id,
                    'location' => $property->city->city_name,
                    'type' => $property->houseType->house_type,
                    'value_increase' => $property->quarterly_trend
                ];
            });

        return ControllerHelper::withJson($response, $properties, 200);
    }

    // Get property analytics
    public function analytics(Request $request, Response $response): Response
    {
        $totalProperties = Property::count();
        $increasing = Property::withValueIncrease()->count();
        $decreasing = Property::withValueDecrease()->count();
        $stable = $totalProperties - $increasing - $decreasing;

        return ControllerHelper::withJson($response, [
            'total_properties' => $totalProperties,
            'value_trends' => [
                'increasing' => $increasing,
                'decreasing' => $decreasing,
                'stable' => $stable,
                'percentage_increasing' => round(($increasing / $totalProperties) * 100, 2)
            ],
            'average_increase' => Property::withValueIncrease()
                ->avg(DB::raw('apr_value - feb_value')),
            'average_decrease' => Property::withValueDecrease()
                ->avg(DB::raw('feb_value - apr_value'))
        ], 200);
    }

    // Get single property with full details
    public function view(Request $request, Response $response, array $args): Response
    {
        $property = Property::with(['city', 'houseType', 'status'])
            ->findOrFail($args['id']);

        $responseData = [
            'property_id' => $property->property_id,
            'location' => [
                'city' => $property->city->city_name,
                'state' => $property->city->state,
                'population' => $property->city->population
            ],
            'specs' => [
                'type' => $property->houseType->house_type,
                'recommended_residents' => $property->houseType->recommended_residents,
                'description' => $property->houseType->description
            ],
            'status' => [
                'name' => $property->status->status_name,
                'category' => $property->status->status_category
            ],
            'valuation' => [
                'february' => $property->feb_value,
                'march' => $property->mar_value,
                'april' => $property->apr_value,
                'quarterly_trend' => $property->quarterly_trend,
                'formatted_values' => $property->formatted_values
            ]
        ];

        return ControllerHelper::withJson($response, $responseData, 200);
    }
    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            // Validate input
            $validator = new Validator();
            $validator->validatePropertyData($data);

            // Create new property
            $property = new Property();
            $property->city_id = $data['city_id'];
            $property->house_type_id = $data['house_type_id'];
            $property->status_id = $data['status_id'];
            $property->feb_value = $data['feb_value'];
            $property->mar_value = $data['mar_value'];
            $property->apr_value = $data['apr_value'];
            $property->save();

            // Return the newly created property with 201 status
            return $this->view($request, $response, ['id' => $property->property_id])
                ->withStatus(201);

        } catch (NestedValidationException $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Validation failed',
                'messages' => $e->getMessages()
            ], 400);
        } catch (\Exception $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

// Update an existing property
    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        try {
            // Validate ID and input data
            $validator = new Validator();
            $validator->validateId($args['id']);
            $validator->validatePropertyData($data, false); // false for update (optional fields)

            // Find and update property
            $property = Property::findOrFail($args['id']);
            $property->city_id = $data['city_id'] ?? $property->city_id;
            $property->house_type_id = $data['house_type_id'] ?? $property->house_type_id;
            $property->status_id = $data['status_id'] ?? $property->status_id;
            $property->feb_value = $data['feb_value'] ?? $property->feb_value;
            $property->mar_value = $data['mar_value'] ?? $property->mar_value;
            $property->apr_value = $data['apr_value'] ?? $property->apr_value;
            $property->save();

            // Return the updated property
            return $this->view($request, $response, ['id' => $property->property_id]);

        } catch (ModelNotFoundException $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Property not found',
                'message' => 'No property found with ID ' . $args['id']
            ], 404);
        } catch (NestedValidationException $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Validation failed',
                'messages' => $e->getMessages()
            ], 400);
        } catch (\Exception $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

// Delete a property
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            // Validate ID
            $validator = new Validator();
            $validator->validateId($args['id']);

            // Find and delete property
            $property = Property::findOrFail($args['id']);
            $property->delete();

            return ControllerHelper::withJson($response, [
                'message' => 'Property deleted successfully'
            ], 200); // Explicitly set 200 status code

        } catch (ModelNotFoundException $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Property not found',
                'message' => 'No property found with ID ' . $args['id']
            ], 404);
        } catch (\InvalidArgumentException $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Invalid ID',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return ControllerHelper::withJson($response, [
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}