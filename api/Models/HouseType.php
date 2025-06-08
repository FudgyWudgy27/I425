<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: HouseType.php
 * Description:
 */

namespace CourseProject\Models;

use Illuminate\Database\Eloquent\Model;

class HouseType extends Model {
    //The table associated with this model
    protected $table = 'housetype';
    //The primary key of the table
    protected $primaryKey = 'house_type_id';
    //If created_at and updated_at columns are not used
    public $timestamps = false;

    public static function getHouseType(){
        $housetype = self::all();
        return $housetype;
    }

    public static function getHouseTypeById(string $house_type_id){
        $housetype = self::findOrFail($house_type_id);
        return $housetype;
    }
    public function statuses() {
        return $this->belongsToMany(Status::class, 'house_type_status', 'house_type_id', 'status_id');
    }
}