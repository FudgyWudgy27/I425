<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: dependencies.php
 * Description:
 */

use DI\Container;
use CourseProject\Controllers\CityController;
use CourseProject\Controllers\HouseTypeController;
use CourseProject\Controllers\StatusController;
use CourseProject\Controllers\PropertyController;
use CourseProject\Controllers\RealtorController;
use Respect\Validation\Validator as v;
use CourseProject\Controllers\UserController;



return function(Container $container) {
    //Set a dependency called City
    $container->set('City', function() {
        return new CityController();
    });

    //Set a dependency called HouseType
    $container->set('HouseType', function(){
        return new HouseTypeController();
    });

    //Set a dependency called Status
    $container->set('Status', function(){
        return new StatusController();
    });

    $container->set('Property', function() {
        return new \CourseProject\Controllers\PropertyController();
    });

    //Set a dependency called Realtor
    $container->set('Realtor', function(){
        return new RealtorController();
    });

    $container->set('validator', function() {
        return new \CourseProject\Validation\Validator();
    });
    // Set a dependency called "User"
    $container->set('User', function() {
        return new UserController();
    });
};