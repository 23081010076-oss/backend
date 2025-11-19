<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', ['student', 'mentor'])->get();

        $experiences = [
            [
                'title' => 'Software Engineering Intern',
                'description' => 'Developed web applications using Laravel and Vue.js, collaborated with senior developers on feature implementation',
                'type' => 'internship',
                'level' => 'Junior',
                'company' => 'PT. Tokopedia',
                'start_date' => '2023-06-01',
                'end_date' => '2023-08-31',
                'certificate_url' => null,
            ],
            [
                'title' => 'Full Stack Developer',
                'description' => 'Built and maintained e-commerce platform, implemented REST APIs and database optimization',
                'type' => 'work',
                'level' => 'Mid-level',
                'company' => 'PT. Bukalapak',
                'start_date' => '2022-09-01',
                'end_date' => '2023-05-31',
                'certificate_url' => 'https://example.com/certificates/fullstack-developer.pdf',
            ],
            [
                'title' => 'Mobile App Developer',
                'description' => 'Developed React Native applications for iOS and Android platforms',
                'type' => 'work',
                'level' => 'Senior',
                'company' => 'PT. Gojek Indonesia',
                'start_date' => '2021-01-01',
                'end_date' => '2022-08-31',
                'certificate_url' => null,
            ],
            [
                'title' => 'Teaching Assistant',
                'description' => 'Assisted professors in Data Structures and Algorithm courses, mentored junior students',
                'type' => 'volunteer',
                'level' => 'Assistant',
                'company' => 'Institut Teknologi Bandung',
                'start_date' => '2022-02-01',
                'end_date' => '2022-12-31',
                'certificate_url' => 'https://example.com/certificates/teaching-assistant.pdf',
            ],
            [
                'title' => 'Frontend Developer Intern',
                'description' => 'Created responsive web interfaces using React.js and implemented UI/UX designs',
                'type' => 'internship',
                'level' => 'Junior',
                'company' => 'PT. Traveloka',
                'start_date' => '2022-01-01',
                'end_date' => '2022-03-31',
                'certificate_url' => null,
            ],
            [
                'title' => 'Data Analyst',
                'description' => 'Analyzed user behavior data and created dashboards for business intelligence',
                'type' => 'work',
                'level' => 'Mid-level',
                'company' => 'PT. Shopee Indonesia',
                'start_date' => '2021-06-01',
                'end_date' => '2022-05-31',
                'certificate_url' => 'https://example.com/certificates/data-analyst.pdf',
            ],
            [
                'title' => 'IT Support Volunteer',
                'description' => 'Provided technical support for community events and digital literacy programs',
                'type' => 'volunteer',
                'level' => 'Support',
                'company' => 'Yayasan Pendidikan Indonesia',
                'start_date' => '2020-09-01',
                'end_date' => '2021-08-31',
                'certificate_url' => null,
            ],
            [
                'title' => 'Backend Developer',
                'description' => 'Designed and implemented microservices architecture using Node.js and Docker',
                'type' => 'work',
                'level' => 'Senior',
                'company' => 'PT. OVO (Visionet)',
                'start_date' => '2020-01-01',
                'end_date' => '2021-05-31',
                'certificate_url' => 'https://example.com/certificates/backend-developer.pdf',
            ],
        ];

        foreach ($users as $user) {
            // Create 2-4 experiences per user
            $numExperiences = rand(2, 4);
            $selectedExperiences = collect($experiences)->random($numExperiences);

            foreach ($selectedExperiences as $exp) {
                Experience::create([
                    'user_id' => $user->id,
                    'title' => $exp['title'],
                    'description' => $exp['description'],
                    'type' => $exp['type'],
                    'level' => $exp['level'],
                    'company' => $exp['company'],
                    'start_date' => $exp['start_date'],
                    'end_date' => $exp['end_date'],
                    'certificate_url' => $exp['certificate_url'],
                ]);
            }
        }

        $this->command->info('Experience seeder completed successfully!');
    }
}
