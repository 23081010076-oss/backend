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
            UserSeeder::class,           // Create sample users (students, mentors, corporate)
            OrganizationSeeder::class,   // Create organizations (depends on users)
            CourseSeeder::class,         // Create courses
            ScholarshipSeeder::class,    // Create scholarships (depends on organizations)
            ExperienceSeeder::class,     // Create experiences (depends on users)
            AchievementSeeder::class,    // Create achievements (depends on users)
            ArticleSeeder::class,        // Create articles (depends on users)
            SubscriptionSeeder::class,   // Create subscriptions (depends on users)
            EnrollmentSeeder::class,     // Create enrollments (depends on users and courses)
            MentoringSessionSeeder::class, // Create mentoring sessions (depends on users)
            ReviewSeeder::class,         // Create reviews (depends on users and courses)
            TransactionSeeder::class,    // Create transactions (depends on enrollments, subscriptions, mentoring sessions)
            CorporateContactSeeder::class, // Create corporate contacts
        ]);

        $this->command->info('All seeders completed successfully! 🎉');
    }
}
