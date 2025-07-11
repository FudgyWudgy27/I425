<?php
/**
 * Author: Allen Fudge
 * Date: 6/8/2025
 * File: Realtor.php
 * Description:
 */

namespace CourseProject\Models;

use Illuminate\Database\Eloquent\Model;

class Realtor extends Model {
    protected $table = 'realtor';
    protected $primaryKey = 'realtor_id';
    public $timestamps = false;

    //Retrieve all students
    public static function getRealtors(){
        //If there are issues with getRealtors(), then add $request to inside the (), uncomment the code, and comment out the last two lines

////get the total number of row count
//        $count = self::count();
//
//        //Get querystring variables from url
//        $params = $request->getQueryParams();
//
//        //do limit and offset exist?
//        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10;   //items per page
//        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0;  //offset of the first item
//
//        //pagination
//        $links = self::getLinks($request, $limit, $offset);
//
//        //build query
//        $query = self::with('cities');  //build the query to get all courses
//        $query = $query->skip($offset)->take($limit);  //limit the rows
//
//        //code for sorting
//        $sort_key_array = self::getSortKeys($request);
//
//        //soft the output by one or more columns
//        foreach($sort_key_array as $column => $direction){
//            $query->orderBy($column, $direction);
//        }
//
//        //retrieve the courses
//        $realtors = $query->get();  //Finally, run the query and get the results
//
//        //construct the data for response
//        $results = [
//            'totalCount' => $count,
//            'limit' => $limit,
//            'offset' => $offset,
//            'links' => $links,
//            'sort' => $sort_key_array,
//            'data' => $realtors
//        ];
//
//        return $results;

        $realtors = self::all();
        return $realtors;
    }

    //View a specific student
    public static function getRealtorById(string $realtor_id){
        $student = self::findOrFail($realtor_id);
        return $student;
    }

    public function cities(){
        return $this->belongsToMany(City::class, 'locations_of_realtors', 'realtor_id', 'city_id')
            ->withPivot('years');
    }

    public static function getCityByRealtor(string $city_id){
        return self::findOrFail($city_id)->cities;
    }

    // Return an array of links for pagination. The array includes links for the current, first, next, and last pages.
    private static function getLinks($request, $limit, $offset) {
        $count = self::count();

        // Get request uri and parts
        $uri = $request->getUri();
        if($port = $uri->getPort()) {
            $port = ':' . $port;
        }
        $base_url = $uri->getScheme() . "://" . $uri->getHost() . $port . $uri->getPath();

        // Construct links for pagination
        $links = [];
        $links[] = ['rel' => 'self', 'href' => "$base_url?limit=$limit&offset=$offset"];
        $links[] = ['rel' => 'first', 'href' => "$base_url?limit=$limit&offset=0"];
        if ($offset - $limit >= 0) {
            $links[] = ['rel' => 'prev', 'href' => "$base_url?limit=$limit&offset=" . $offset - $limit];
        }
        if ($offset + $limit < $count) {
            $links[] = ['rel' => 'next', 'href' => "$base_url?limit=$limit&offset=" . $offset + $limit];
        }
        $links[] = ['rel' => 'last', 'href' => "$base_url?limit=$limit&offset=" . $limit * (ceil($count / $limit) - 1)];

        return $links;
    }

    private static function getSortKeys($request) {
        $sort_key_array = [];

        // Get querystring variables from url
        $params = $request->getQueryParams();

        if (array_key_exists('sort', $params)) {
            $sort = preg_replace('/^\[|]$|\s+/', '', $params['sort']);  // remove white spaces, [, and ]
            $sort_keys = explode(',', $sort); //get all the key:direction pairs
            foreach ($sort_keys as $sort_key) {
                $direction = 'asc';
                $column = $sort_key;
                if (strpos($sort_key, ':')) {
                    list($column, $direction) = explode(':', $sort_key);
                }
                $sort_key_array[$column] = $direction;
            }
        }

        return $sort_key_array;
    }

    public static function searchRealtors($term){
        $query = self::where('realtor_name', 'LIKE', "%$term%")
            ->orWhere('phone', 'LIKE', "%$term%")
            ->orWhere('email', 'LIKE', "%$term%")
            ->orWhere('state', 'LIKE', "%$term%");
        return $query->get();
    }
}