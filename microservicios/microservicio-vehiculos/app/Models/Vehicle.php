<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'vin',
        'registration_number',
        'brand_id',
        'model_id',
        'year',
        'color',
        'mileage',
        'price',
        'cost',
        'condition',
        'status',
        'fuel_type',
        'transmission',
        'engine_size',
        'power',
        'doors',
        'seats',
        'description',
        'features',
        'observations',
        'purchase_date',
        'location',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'power' => 'integer',
        'doors' => 'integer',
        'seats' => 'integer',
        'features' => 'array',
        'purchase_date' => 'date',
    ];

    /**
     * Get the brand that owns the vehicle.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the model that owns the vehicle.
     */
    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    /**
     * Get the images for the vehicle.
     */
    public function images()
    {
        return $this->hasMany(VehicleImage::class);
    }

    /**
     * Get the technical specifications for the vehicle.
     */
    public function technicalSpecs()
    {
        return $this->hasOne(TechnicalSpecification::class);
    }

    /**
     * Get the main image for the vehicle.
     */
    public function getMainImageAttribute()
    {
        return $this->images()->where('is_main', true)->first();
    }

    /**
     * Get the margin of the vehicle.
     */
    public function getMarginAttribute()
    {
        if ($this->price && $this->cost) {
            return $this->price - $this->cost;
        }
        return 0;
    }

    /**
     * Get the margin percentage of the vehicle.
     */
    public function getMarginPercentageAttribute()
    {
        if ($this->price && $this->cost && $this->price > 0) {
            return round((($this->price - $this->cost) / $this->price) * 100, 2);
        }
        return 0;
    }

    /**
     * Scope a query to filter by brand.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $brandId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope a query to filter by model.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $modelId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByModel($query, $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    /**
     * Scope a query to filter by status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by price range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $min
     * @param  float  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPriceRange($query, $min, $max)
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }

        if ($max) {
            $query->where('price', '<=', $max);
        }

        return $query;
    }

    /**
     * Scope a query to filter by year range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $min
     * @param  int  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByYearRange($query, $min, $max)
    {
        if ($min) {
            $query->where('year', '>=', $min);
        }

        if ($max) {
            $query->where('year', '<=', $max);
        }

        return $query;
    }

    /**
     * Scope a query to filter by mileage range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $min
     * @param  int  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMileageRange($query, $min, $max)
    {
        if ($min) {
            $query->where('mileage', '>=', $min);
        }

        if ($max) {
            $query->where('mileage', '<=', $max);
        }

        return $query;
    }

    /**
     * Scope a query to filter by fuel type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $fuelType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Scope a query to filter by transmission.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $transmission
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTransmission($query, $transmission)
    {
        return $query->where('transmission', $transmission);
    }

    /**
     * Scope a query to only include available vehicles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
