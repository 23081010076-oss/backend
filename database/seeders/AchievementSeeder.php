<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', ['student', 'mentor'])->get();

        $achievements = [
            [
                'title' => 'Dean\'s List Academic Achievement',
                'description' => 'Achieved Dean\'s List recognition for exceptional academic performance with GPA 3.85/4.0',
                'organization' => 'Universitas Indonesia',
                'year' => 2023,
            ],
            [
                'title' => 'Best Final Project Award',
                'description' => 'Won first place in Computer Science final project competition with AI-based learning platform',
                'organization' => 'Institut Teknologi Bandung',
                'year' => 2023,
            ],
            [
                'title' => 'National Programming Competition Winner',
                'description' => 'First place in National Algorithm Programming Contest (GEMASTIK)',
                'organization' => 'Kementerian Pendidikan dan Kebudayaan',
                'year' => 2022,
            ],
            [
                'title' => 'Outstanding Student Leader',
                'description' => 'Recognized as outstanding student leader for organizing successful tech conference',
                'organization' => 'Universitas Gadjah Mada',
                'year' => 2022,
            ],
            [
                'title' => 'Scholarship Recipient',
                'description' => 'Received full scholarship for academic excellence and community service contribution',
                'organization' => 'Beasiswa Indonesia Maju',
                'year' => 2021,
            ],
            [
                'title' => 'Research Publication',
                'description' => 'Published research paper on Machine Learning applications in education journal',
                'organization' => 'IEEE Computer Society',
                'year' => 2023,
            ],
            [
                'title' => 'Startup Pitch Competition Winner',
                'description' => 'Won first place in university startup pitch competition with EdTech solution',
                'organization' => 'Universitas Brawijaya',
                'year' => 2022,
            ],
            [
                'title' => 'Community Service Excellence',
                'description' => 'Recognized for outstanding community service in digital literacy program',
                'organization' => 'Dompet Dhuafa',
                'year' => 2021,
            ],
        ];

        foreach ($users as $user) {
            // Create 1-3 achievements per user
            $numAchievements = rand(1, 3);
            $selectedAchievements = collect($achievements)->random($numAchievements);

            foreach ($selectedAchievements as $achievement) {
                Achievement::create([
                    'user_id' => $user->id,
                    'title' => $achievement['title'],
                    'description' => $achievement['description'],
                    'organization' => $achievement['organization'],
                    'year' => $achievement['year'],
                ]);
            }
        }

        $this->command->info('Achievement seeder completed successfully!');
    }
}
