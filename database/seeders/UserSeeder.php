<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Usuario normal
        User::create([
            'name' => 'Usuari',
            'email' => 'usuari@email.com',
            'password' => Hash::make('usuari123'),
            'role' => 'user',
        ]);
    }
}
