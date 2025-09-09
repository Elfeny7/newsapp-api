<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        User::create(
            [
                'name' => 'Ikmal',
                'email' => 'ikmal@email.com',
                'password' => Hash::make('ikmal123'),
                'role' => 'superadmin'
            ],
        );
        User::create(
            [
                'name' => 'Faris',
                'email' => 'faris@email.com',
                'password' => Hash::make('faris123'),
                'role' => 'journalist'
            ],
        );
        User::create(
            [
                'name' => 'Musyaffa',
                'email' => 'musyaffa@email.com',
                'password' => Hash::make('musyaffa123'),
                'role' => 'viewer'
            ],
        );
    }
}
