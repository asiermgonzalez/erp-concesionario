<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleType extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the clients interested in this vehicle type.
     */
    public function interestedClients()
    {
        return $this->belongsToMany(Client::class, 'client_interests')
            ->withPivot('min_price', 'max_price', 'min_year', 'max_year')
            ->withTimestamps();
    }
}
