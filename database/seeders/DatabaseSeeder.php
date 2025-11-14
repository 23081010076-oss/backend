<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Admin Seeder FIRST (create admin users)
        $this->call([
            AdminSeeder::class,
        ]);

        // Only run AdminSeeder for now
        // TestDataSeeder can be added later
    }
}
