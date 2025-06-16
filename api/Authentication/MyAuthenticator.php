<?php
/**
 * Author: Allen Fudge
 * Date: 6/16/2025
 * File: MyAuthenticator.php
 * Description:
 */

namespace CourseProject\Authentication;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use CourseProject\Models\User;

class MyAuthenticator {
    public function __invoke(Request $request, RequestHandler $handler) : Response {
        //Username and password are stored in a header called "CourseProject-Authorization".
        if(!$request->hasHeader('CourseProject-Authorization')) {
            $results = ['Status' => 'CourseProject-Authorization header not found.'];
            return AuthenticationHelper::withJson($results, 401);
        }

        //Retrieve the header.
        $auth = $request->getHeader('CourseProject-Authorization');

        $apikey = $auth[0];

        list($username, $password) = explode(':', $auth[0]);

        //Retrieve the header and then the username and password
        $auth = $request->getHeader('CourseProject-Authorization');
        list($username, $password) = explode(':', $auth[0]);

        //Validate the username and password
        if(!User::authenticateUser($username, $password)) {
            $results = ['Status' => 'Authentication failed.'];
            return AuthenticationHelper::withJson($results, 403);
        }

        //A user has been authenticated
        return $handler->handle($request);
    }
}