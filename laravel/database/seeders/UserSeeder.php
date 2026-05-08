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
            'name' => 'Администратор',
            'email' => 'admin@mail.by',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Бухгалтер',
            'email' => 'accountant@mail.by',
            'password' => Hash::make('password'),
            'role' => 'accountant',
        ]);

        User::create([
            'name' => 'Кадровик',
            'email' => 'hr@mail.by',
            'password' => Hash::make('password'),
            'role' => 'hr',
        ]);

        User::create([
            'name' => 'Начальник отдела',
            'email' => 'manager@mail.by',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);
    }
}
