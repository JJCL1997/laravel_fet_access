<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'identificacion',
        'telefono',
        'motivo_visita',
    ];

    // Relación de uno a muchos con AccessLog
    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class, 'visitor_id');
    }

    public function latestAccessLog()
    {
        return $this->hasOne(AccessLog::class, 'visitor_id')->orderBy('log_id', 'desc');
    }

}
