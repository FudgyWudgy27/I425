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
use CourseProject\Controllers\ControllerHelper as Helper;

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

        return Helper::withJson($response, $properties, 200);

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

        return Helper::withJson($response, $properties);
    }

    // Get property analytics
    public function analytics(Request $request, Response $response): Response
    {
        $totalProperties = Property::count();
        $increasing = Property::withValueIncrease()->count();
        $decreasing = Property::withValueDecrease()->count();
        $stable = $totalProperties - $increasing - $decreasing;

        return Helper::withJson($response, [
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
        ]);
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

        return Helper::withJson($response, $responseData);
    }
}