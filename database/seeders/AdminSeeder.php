<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the admin account.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@kosmetik.example.com'],
            [
                'name'              => 'Administrator',
                'email'             => 'admin@kosmetik.example.com',
                'password'          => Hash::make('Admin@12345'),
                'role'              => 'admin',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin account seeded: admin@kosmetik.example.com / Admin@12345');
    }
}
