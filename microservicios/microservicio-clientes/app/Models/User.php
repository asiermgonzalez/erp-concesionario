<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'id',
        'name',
        'email',
    ];

    // Esta es una clase de referencia para relaciones, los datos reales
    // se manejan en el microservicio de usuarios
}