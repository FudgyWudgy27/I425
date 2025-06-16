<?php
/**
 * Author: Allen Fudge
 * Date: 6/16/2025
 * File: AuthenticationHelper.php
 * Description:
 */

namespace CourseProject\Authentication;

use Slim\Psr7\Response;

class AuthenticationHelper {
    public static function withJson($data, int $code) : Response {
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($code);
    }
}