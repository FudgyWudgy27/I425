<?php
/**
 * Author: your name
 * Date: 6/15/2025
 * File: User.php
 * Description: Define the User model class
 */

namespace CourseProject\Models;

use Illuminate\Database\Eloquent\Model;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User extends Model {
    //JWT secret
    const JWT_KEY = 'MyCollegeAPI-api-v1$';
    //The lifetime of the JWT token: seconds
    const JWT_EXPIRE = 3600;

    //The table associated with this model. "users" is the default name.
    protected $table = 'users';
    //The primary key of the table. "id" is the default name.
    protected $primaryKey = 'id';
    //Is the PK an incrementing integer value? "True" is the default value.
    public $incrementing = true;
    //The data type of the PK. "int" is the default value.
    protected $keyType = 'int';
    //Do the created_at and updated_at columns exist in the table? "True" is the default value.
    public $timestamps = true;

    //List all users
    public static function getUsers() {
        $users = self::all();
        return $users;
    }


    // View a specific user by id
    public static function getUserById(string $id)
    {
        $user = self::findOrFail($id);
        return $user;
    }

    // Create a new user
    public static function createUser($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        // Create a new User instance
        $user = new User();

        // Set the user's attributes
        foreach ($params as $field => $value) {
            $user->$field = ($field == "password") ? password_hash($value, PASSWORD_DEFAULT) : $value;
        }

        // Insert the user into the database
        $user->save();
        return $user;
    }

    // Update a user
    public static function updateUser($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        //Retrieve the user's id from url
        $id = $request->getAttribute('id');
        $user = self::findOrFail($id);

        if(!$user) {
            return false;
        }

        //update attributes of the user
        foreach($params as $field => $value) {
            $user->$field =  ($field == "password") ? password_hash($value, PASSWORD_DEFAULT) : $value;
        }

        // Update the user
        $user->save();
        return $user;
    }

    // Delete a user
    public static function deleteUser($request)
    {
        $user = self::findOrFail($request->getAttribute('id'));
        return ($user ? $user->delete() : $user);
    }

    /************* User Authentication and Authorization Methods ************************/
    //Authenticate a user by username and password
    public static function authenticateUser($username, $password) {
        //Retrieve the first record from the database table that matches the username
        $user =self::where('username', $username)->first();
        if(!$user) {
            return false;
        }
        //Verify password.
        return password_verify($password, $user->password) ? $user : false;
    }

    /****************** JWT Authentication ************************************/
    /*
     * Generate a JWT token.
     * The signature secret rule: the secret must be at least 12 characters in length;
     * contain numbers; upper and lowercase letters; and one of the following special characters *&!@%^#$.
     * For more details, please visit https://github.com/RobDWaller/ReallySimpleJWT
     */
    public static function generateJWT($id) {
        //Data for payload
        $user = self::find($id);

        if(!$user) {
            return false;
        }

        $key = self::JWT_KEY;
        $expiration = time() + self::JWT_EXPIRE;
        $issuer = 'mycollege-api.com';

        $payload = [
            'iss' => $issuer,
            'exp' => $expiration,
            'isa' => time(),
            'data' => [
                'uid' => $id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ];

        //Generate and return the token
        return JWT::encode(
            $payload,  //data to be encoded in the JWT
            $key,  //the signing key
            'HS256' //algorithm used to sign the token; defaults to HS256
        );
    }

    //Validate a JWT token
    public static function validateJWT($jwt) {
        $decoded = JWT::decode($jwt, new Key(self::JWT_KEY, 'HS256'));
        return $decoded;
    }
}