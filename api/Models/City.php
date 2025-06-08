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

    //The table associated with this model
    protected $table = 'city';
    //The primary key of the table
    protected $primaryKey = 'city_id';
    //If created_at and updated_at columns are not used
    public $timestamps = false;

    public static function getCities(){
        //Retrieve all cities
        $cities = self::all();
        return $cities;
    }

    //view specific city by id
    public static function getCityById(int $city_id){
        $city = self::findorFail($city_id);
        $city->load('');
        return $city;
    }
    public function properties()
    {
        return $this->hasMany(\CourseProject\Models\Property::class, 'city_id');
    }

    public function realtors(){
        return $this->belongsToMany(Realtor::class, 'locations_of_realtors', 'city_id', 'realtor_id')
            ->withPivot('years');
    }

    //Retrieve the realtors in a city
    public static function getRealtorsByCity(int $realtor_id){
        return self::findOrFail($realtor_id)->realtors;
    }
}