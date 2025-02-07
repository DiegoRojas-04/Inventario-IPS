<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Definir las asociaciones entre role_id, model_type y model_id
        $modelRoles = [
            ['role_id' => 1, 'model_type' => 'App\Models\User', 'model_id' => 1],
        ];

        // Insertar las asociaciones en la tabla model_has_roles
        DB::table('model_has_roles')->insert($modelRoles);
    }
}
