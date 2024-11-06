<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nombres' => 'admin',
            'apellidos' => 'uno',
            'email' => 'admin@e.com',
            'codigo' => '11111111',
            'telefono' => '1234567890',
            'password' => Hash::make('password123'), // Cambia esto a una contraseña segura
            'role_id' => 1, // ID del rol admin en la tabla roles
        ]);
    }
}
