<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'logo',
        'country',
        'description',
    ];

    /**
     * Get the models for the brand.
     */
    public function models()
    {
        return $this->hasMany(VehicleModel::class);
    }

    /**
     * Get the vehicles for the brand.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}