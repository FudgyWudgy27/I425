<?php
/**
 * Author: your name
 * Date: 6/8/2025
 * File: Validator.php
 * Description:
 */
namespace CourseProject\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    public function validateId($id)
    {
        try {
            v::intVal()->positive()->assert($id);
            return true;
        } catch (NestedValidationException $e) {
            throw new \InvalidArgumentException($e->getFullMessage());
        }
    }

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
}