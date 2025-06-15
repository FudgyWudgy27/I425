<?php
/**
 * Author: your name
 * Date: 6/8/2025
 * File: Validator.php
 * Description: Handles validation for different models
 */
namespace CourseProject\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    // Store validation errors
    protected static $errors = [];

    // Validate a numeric ID
    public function validateId($id)
    {
        try {
            v::intVal()->positive()->assert($id);
            return true;
        } catch (NestedValidationException $e) {
            throw new \InvalidArgumentException($e->getFullMessage());
        }
    }

    // Validate property data (for Property model)
    public function validatePropertyData(array $data, bool $isCreate = true)
    {
        $rules = v::key('city_id', v::intVal()->positive())
            ->key('house_type_id', v::intVal()->positive())
            ->key('status_id', v::intVal()->positive())
            ->key('feb_value', v::intVal()->min(0))
            ->key('mar_value', v::intVal()->min(0))
            ->key('apr_value', v::intVal()->min(0));

        if ($isCreate) {
            $rules = $rules->key('city_id', v::notEmpty())
                ->key('house_type_id', v::notEmpty())
                ->key('status_id', v::notEmpty())
                ->key('feb_value', v::notEmpty())
                ->key('mar_value', v::notEmpty())
                ->key('apr_value', v::notEmpty());
        }

        try {
            $rules->assert($data);
        } catch (NestedValidationException $e) {
            throw $e;
        }
    }

    // Validate user attributes from the request body
    public static function validateUser($request): bool
    {
        $rules = [
            'name' => v::alnum(' '),
            'email' => v::email(),
            'username' => v::notEmpty(),
            'password' => v::notEmpty(),
            'role' => v::number()->between(1, 4)
        ];

        return self::validate($request, $rules);
    }

    // Generic validation function for request body and given rules
    public static function validate($request, $rules): bool
    {
        $params = $request->getParsedBody();
        self::$errors = []; // Reset errors

        foreach ($rules as $field => $rule) {
            try {
                $rule->assert($params[$field] ?? null);
            } catch (NestedValidationException $e) {
                self::$errors[$field] = $e->getMessages();
            }
        }

        return empty(self::$errors);
    }

    // Get validation errors
    public static function getErrors()
    {
        return self::$errors;
    }
}