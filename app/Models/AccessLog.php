<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';
    protected $fillable = [
        'visitor_id',
        'user_id',
        'role_id',
        'user_name',
        'user_email',
        'access_time',
        'vehicle_type',
        'vehicle_plate',
        'token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
}
