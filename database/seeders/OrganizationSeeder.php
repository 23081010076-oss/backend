<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', ['student', 'mentor', 'corporate'])->get();

        $organizations = [
            [
                'name' => 'Google Developer Student Club',
                'role' => 'President',
                'description' => 'Leading university tech community focused on Google technologies and developer skills',
                'location' => 'Jakarta, Indonesia',
                'contact_email' => 'gdsc.ui@gmail.com',
                'phone' => '+62 21 7863333',
                'website' => 'https://gdsc.community.dev/university-of-indonesia/',
                'start_date' => '2022-08-01',
                'end_date' => '2023-07-31',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Informatika',
                'role' => 'Secretary',
                'description' => 'Student association for Computer Science majors promoting academic and professional development',
                'location' => 'Bandung, Indonesia',
                'contact_email' => 'hmti@itb.ac.id',
                'phone' => '+62 22 2511568',
                'website' => 'https://hmti.itb.ac.id',
                'start_date' => '2021-01-01',
                'end_date' => '2022-12-31',
            ],
            [
                'name' => 'Indonesia Student Startup Community',
                'role' => 'Vice President',
                'description' => 'National community connecting student entrepreneurs and startup enthusiasts',
                'location' => 'Indonesia',
                'contact_email' => 'info@issc.id',
                'phone' => '+62 811 9999 888',
                'website' => 'https://issc.id',
                'start_date' => '2021-06-01',
                'end_date' => '2022-05-31',
            ],
            [
                'name' => 'IEEE Computer Society',
                'role' => 'Technical Committee Member',
                'description' => 'International professional organization for computer science and engineering',
                'location' => 'Global',
                'contact_email' => 'ieee.cs@ieee.org',
                'phone' => '+1 732 981 0060',
                'website' => 'https://www.computer.org',
                'start_date' => '2020-09-01',
                'end_date' => null,
            ],
            [
                'name' => 'Komunitas Programmer Indonesia',
                'role' => 'Event Coordinator',
                'description' => 'Largest programming community in Indonesia focusing on knowledge sharing',
                'location' => 'Indonesia',
                'contact_email' => 'admin@kpi.org',
                'phone' => '+62 21 5555 4444',
                'website' => 'https://kpi.org',
                'start_date' => '2021-03-01',
                'end_date' => '2023-02-28',
            ],
            [
                'name' => 'Techno Creative Association',
                'role' => 'Project Manager',
                'description' => 'Creative technology organization promoting innovation in digital arts',
                'location' => 'Yogyakarta, Indonesia',
                'contact_email' => 'hello@technocreative.org',
                'phone' => '+62 274 123456',
                'website' => 'https://technocreative.org',
                'start_date' => '2020-01-01',
                'end_date' => '2021-12-31',
            ],
        ];

        foreach ($users as $user) {
            // Create 1-2 organizations per user
            $numOrganizations = rand(1, 2);
            $selectedOrgs = collect($organizations)->random($numOrganizations);

            foreach ($selectedOrgs as $org) {
                Organization::create([
                    'user_id' => $user->id,
                    'name' => $org['name'],
                    'role' => $org['role'],
                    'description' => $org['description'],
                    'location' => $org['location'],
                    'contact_email' => $org['contact_email'],
                    'phone' => $org['phone'],
                    'website' => $org['website'],
                    'start_date' => $org['start_date'],
                    'end_date' => $org['end_date'],
                ]);
            }
        }

        $this->command->info('Organization seeder completed successfully!');
    }
}
