<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@miapp.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Luis Tasayco',
            'email' => 'dw@fiestatoursperu.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Usuario Uno',
            'email' => 'usuario@miapp.com',
            'password' => Hash::make('usuario123'),
            'role' => 'usuario',
        ]);
    }
}
