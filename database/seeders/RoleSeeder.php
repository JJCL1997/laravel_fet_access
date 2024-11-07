<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crear los roles si no existen
        Role::firstOrCreate(['id' => 1, 'role_name' => 'admin']);
        Role::firstOrCreate(['id' => 2, 'role_name' => 'student']);
        Role::firstOrCreate(['id' => 4, 'role_name' => 'vigilant']);
        Role::firstOrCreate(['id' => 3, 'role_name' => 'visitor']);

    }
}
