<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('shop.owner_email', 'owner@rhsheritage.example')],
            [
                'name' => 'Owner RHS HERITAGE',
                'password' => Hash::make(config('shop.owner_initial_password', 'password')),
                'is_owner' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin RHS HERITAGE',
                'password' => Hash::make('12345678'),
                'is_owner' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
