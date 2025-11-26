<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@learningplatform.com'],
            [
                'name' => 'Admin Platform',
                'email' => 'admin@learningplatform.com',
                'password' => 'AdminPassword123!',
                'role' => 'admin',
                'phone' => '08123456789',
                'gender' => 'male',
                'email_verified_at' => now(),
            ]
        );

        // Create Additional Admin Users (Optional)
        User::firstOrCreate(
            ['email' => 'superadmin@learningplatform.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@learningplatform.com',
                'password' => 'SuperAdminPass123!',
                'role' => 'admin',
                'phone' => '08234567890',
                'gender' => 'female',
                'email_verified_at' => now(),
            ]
        );

        // Create Test Admin User
        User::firstOrCreate(
            ['email' => 'test.admin@learningplatform.com'],
            [
                'name' => 'Test Admin',
                'email' => 'test.admin@learningplatform.com',
                'password' => Hash::make('TestAdminPass123!'),
                'role' => 'admin',
                'phone' => '08345678901',
                'gender' => 'other',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin users created successfully!');
    }
}
