<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@rppgenerator.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Demo Guru User
        User::create([
            'name' => 'Guru Demo',
            'email' => 'guru@rppgenerator.test',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);
    }
}
