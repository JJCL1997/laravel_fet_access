<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['role_name' => 'admin'],
            ['role_name' => 'student'],
            ['role_name' => 'visitor'],
            ['role_name' => 'vigilant']
        ]);
    }
}
