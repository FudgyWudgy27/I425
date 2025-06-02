<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: City.php
 * Description:
 */

namespace CourseProject\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model{

    protected $table = 'city';
    protected $primaryKey = 'city_id';

    public $timestamps = false;

    public static function getCities(){
        //Retrieve all cities
        $cities = self::all();
        return $cities;
    }

    //view specific city by id
    public static function getCityById(int $city_id){
        $city = self::findorFail($city_id);
        return $city;
    }
}