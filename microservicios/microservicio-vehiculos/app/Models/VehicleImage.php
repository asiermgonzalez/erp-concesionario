<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleImage extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id',
        'file_name',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'is_main',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_main' => 'boolean',
        'order' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the vehicle that owns the image.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the full URL to the image.
     */
    public function getUrlAttribute()
    {
        return env('STORAGE_URL') . '/' . $this->file_path;
    }
}