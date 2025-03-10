<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicalSpecification extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id',
        'engine_type',
        'displacement',
        'cylinders',
        'valves',
        'power',
        'torque',
        'acceleration',
        'max_speed',
        'fuel_consumption_urban',
        'fuel_consumption_extra',
        'fuel_consumption_combined',
        'co2_emissions',
        'emission_standard',
        'weight',
        'length',
        'width',
        'height',
        'wheelbase',
        'trunk_capacity',
        'tank_capacity',
        'tires',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'displacement' => 'integer',
        'cylinders' => 'integer',
        'valves' => 'integer',
        'power' => 'integer',
        'torque' => 'integer',
        'acceleration' => 'float',
        'max_speed' => 'integer',
        'fuel_consumption_urban' => 'float',
        'fuel_consumption_extra' => 'float',
        'fuel_consumption_combined' => 'float',
        'co2_emissions' => 'integer',
        'weight' => 'integer',
        'length' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'wheelbase' => 'integer',
        'trunk_capacity' => 'integer',
        'tank_capacity' => 'integer',
    ];

    /**
     * Get the vehicle that owns the technical specification.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}