<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar un solo usuario con la id = 1
        User::create([
            'id' => 1,
            'name' => 'Administrador',
            'email' => 'Administrador@gmail.com',
            'password' => Hash::make('administrador'),
            'remember_token' => Str::random(10),
        ]);

    }
}
