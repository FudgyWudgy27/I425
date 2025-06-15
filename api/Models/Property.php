<?php
/**
 * Author: your name
 * Date: 6/3/2025
 * File: Property.php
 * Description:
 */
namespace CourseProject\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'properties';
    protected $primaryKey = 'property_id';
    public $timestamps = false;

    // Cast numeric values
    protected $casts = [
        'feb_value' => 'integer',
        'mar_value' => 'integer',
        'apr_value' => 'integer'
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function houseType()
    {
        return $this->belongsTo(HouseType::class, 'house_type_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    // Accessors for calculated fields
    public function getQuarterlyTrendAttribute()
    {
        return $this->apr_value - $this->feb_value;
    }

    public function getMonthlyChangesAttribute()
    {
        return [
            'feb_to_mar' => $this->mar_value - $this->feb_value,
            'mar_to_apr' => $this->apr_value - $this->mar_value
        ];
    }

    public function getFormattedValuesAttribute()
    {
        return [
            'february' => number_format($this->feb_value),
            'march' => number_format($this->mar_value),
            'april' => number_format($this->apr_value)
        ];
    }

    // Scope for filtering
    public function scopeWithValueIncrease($query)
    {
        return $query->whereRaw('apr_value > feb_value');
    }

    public function scopeWithValueDecrease($query)
    {
        return $query->whereRaw('apr_value < feb_value');

    }
    //Search for Property
    public static function searchProperty($term) {
        if(is_numeric($term)) {
            $query = self::where('Value', '>=', $term);
        } else {
            $query = self::where('id', 'like', "%$term%")
                ->orWhere('Status', 'like', "%$term%")
                ->orWhere('Value', 'like', "%$term%")
                ->orWhere('City', 'like', "%$term%")
                ->orWhere('house Type', 'like', "%$term%");
        }
        return $query->get();
    }
}