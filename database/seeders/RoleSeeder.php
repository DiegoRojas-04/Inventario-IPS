<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'Administrador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Usuario', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'Consultorio', 'guard_name' => 'web'],
            ['id' => 4, 'name' => 'Compras', 'guard_name' => 'web'],
            ['id' => 5, 'name' => 'Activos', 'guard_name' => 'web'],
            ['id' => 6, 'name' => 'Laboratorio', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
