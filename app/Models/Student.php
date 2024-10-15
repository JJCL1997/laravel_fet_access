<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'codigo_estudiante',
        'telefono',
        'password',
        'status',
    ];
}
    
