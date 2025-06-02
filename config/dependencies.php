<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: dependencies.php
 * Description:
 */

use DI\Container;
use CourseProject\Controllers\CityController;

return function(Container $container) {
    $container->set('City', function(){
        return new CityController();
    });
};