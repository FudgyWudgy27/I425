<?php
/**
 * Author: Allen Fudge
 * Date: 6/2/2025
 * File: Status.php
 * Description:
 */

namespace CourseProject\Models;

use Illuminate\Database\Eloquent\Model;

Class Status extends Model {
    //The table associated with this model
    protected $table = 'status';
    //The primary key of the table
    protected $primaryKey = 'status_id';
    //If created_at and updated_at columns are not used
    public $timestamps = false;

    public static function getStatus(){
        //Retrieve all status
        $status = self::all();
        return $status;
    }

    //view specific status by id
    public static function getStatusById(int $status_id){
        $status = self::findorFail($status_id);
        return $status;
    }
    public function houseTypes() {
        return $this->belongsToMany(HouseType::class, 'house_type_status', 'status_id', 'house_type_id');
    }
}