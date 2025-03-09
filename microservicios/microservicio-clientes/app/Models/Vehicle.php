<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'id',
        'make',
        'model',
        'year',
        'price',
    ];

    // Esta es una clase de referencia para relaciones, los datos reales
    // se manejan en el microservicio de vehículos
}