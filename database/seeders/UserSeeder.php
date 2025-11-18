<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Students
            [
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad.rizki@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'gender' => 'male',
                'birth_date' => '2001-05-15',
                'phone' => '08123456001',
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'institution' => 'Universitas Indonesia',
                'major' => 'Teknik Informatika',
                'education_level' => 'S1',
                'bio' => 'Mahasiswa semester 6 yang passionate dengan web development dan machine learning.',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sari Dewi',
                'email' => 'sari.dewi@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'gender' => 'female',
                'birth_date' => '2002-08-22',
                'phone' => '08123456002',
                'address' => 'Jl. Sudirman No. 456, Bandung',
                'institution' => 'Institut Teknologi Bandung',
                'major' => 'Sistem Informasi',
                'education_level' => 'S1',
                'bio' => 'Enthusiast in UI/UX design and frontend development.',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Budi Hartono',
                'email' => 'budi.hartono@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'gender' => 'male',
                'birth_date' => '2000-12-10',
                'phone' => '08123456003',
                'address' => 'Jl. Gatot Subroto No. 789, Yogyakarta',
                'institution' => 'Universitas Gadjah Mada',
                'major' => 'Ilmu Komputer',
                'education_level' => 'S1',
                'bio' => 'Final year student interested in data science and artificial intelligence.',
                'email_verified_at' => now(),
            ],
            // Mentors
            [
                'name' => 'Dr. Maya Sari',
                'email' => 'maya.sari@mentor.com',
                'password' => Hash::make('password123'),
                'role' => 'mentor',
                'gender' => 'female',
                'birth_date' => '1985-03-18',
                'phone' => '08123456004',
                'address' => 'Jl. HR Rasuna Said No. 101, Jakarta Selatan',
                'institution' => 'Google Indonesia',
                'major' => 'Computer Science',
                'education_level' => 'S3',
                'bio' => 'Senior Software Engineer at Google with 10+ years experience in full-stack development and team leadership.',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Prof. Agus Prasetyo',
                'email' => 'agus.prasetyo@mentor.com',
                'password' => Hash::make('password123'),
                'role' => 'mentor',
                'gender' => 'male',
                'birth_date' => '1978-11-25',
                'phone' => '08123456005',
                'address' => 'Jl. Thamrin No. 202, Jakarta Pusat',
                'institution' => 'Institut Teknologi Bandung',
                'major' => 'Teknik Informatika',
                'education_level' => 'S3',
                'bio' => 'Professor of Computer Science, researcher in machine learning and AI, mentor for 100+ students.',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rina Kartika',
                'email' => 'rina.kartika@mentor.com',
                'password' => Hash::make('password123'),
                'role' => 'mentor',
                'gender' => 'female',
                'birth_date' => '1990-07-12',
                'phone' => '08123456006',
                'address' => 'Jl. Kemang Raya No. 303, Jakarta Selatan',
                'institution' => 'Tokopedia',
                'major' => 'Desain Komunikasi Visual',
                'education_level' => 'S2',
                'bio' => 'Senior UX Designer at Tokopedia, specialized in mobile app design and user research.',
                'email_verified_at' => now(),
            ],
            // Corporate Users
            [
                'name' => 'Doni Pratama',
                'email' => 'doni.pratama@corporate.com',
                'password' => Hash::make('password123'),
                'role' => 'corporate',
                'gender' => 'male',
                'birth_date' => '1982-09-05',
                'phone' => '08123456007',
                'address' => 'Jl. Kuningan No. 404, Jakarta Selatan',
                'institution' => 'PT. Bukalapak',
                'major' => 'Business Management',
                'education_level' => 'S2',
                'bio' => 'HR Director at Bukalapak, focused on talent acquisition and employee development programs.',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('User seeder completed successfully!');
    }
}
