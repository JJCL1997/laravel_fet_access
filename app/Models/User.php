<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'password',
        'codigo',
        'telefono',
        'role_id',
        'last_qr_token',
        'profile_photo' // Nuevo campo para la foto de perfil
    ];

    /**
     * Los atributos que deben estar ocultos para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relación con la tabla de roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Método para verificar si el usuario es administrador.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role && $this->role->role_name === 'admin';
    }

    /**
     * Método para verificar si el usuario es estudiante.
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->role && $this->role->role_name === 'student';
    }

    /**
     * Método para verificar si el usuario es vigilante.
     *
     * @return bool
     */
    public function isVigilant()
    {
        return $this->role && $this->role->role_name === 'vigilant';
    }
}
