<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Constantes para los roles
    const ROLE_ADMIN = 'admin';
    const ROLE_STUDENT = 'student';
    const ROLE_VIGILANT = 'vigilant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'password',
        'codigo',   // Añadir 'codigo' como atributo rellenable
        'telefono', // Añadir 'telefono' como atributo rellenable
        'role',     // Añadir 'role' como atributo rellenable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Métodos para verificar el rol del usuario
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStudent()
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isVigilant()
    {
        return $this->role === self::ROLE_VIGILANT;
    }
}
