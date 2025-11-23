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
            UserSeeder::class,      // Create sample users (students, mentors, corporate)
            OrganizationSeeder::class, // Create organizations (depends on users)
            CourseSeeder::class,    // Create courses
            ScholarshipSeeder::class, // Create scholarships (depends on organizations)
            ExperienceSeeder::class,   // Create experiences (depends on users)
            AchievementSeeder::class,  // Create achievements (depends on users)
            ArticleSeeder::class,      // Create articles (depends on users)
        ]);

        $this->command->info('All seeders completed successfully! 🎉');
    }
}
