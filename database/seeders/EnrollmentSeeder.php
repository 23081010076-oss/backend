<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('No students or courses found. Please run UserSeeder and CourseSeeder first.');
            return;
        }

        $enrollments = [
            // Student 1 enrollments
            [
                'user_id' => $students->first()->id,
                'course_id' => $courses->where('title', 'Introduction to Programming')->first()->id ?? $courses->first()->id,
                'progress' => 100,
                'completed' => true,
                'certificate_url' => 'https://example.com/certificates/student1-intro-programming.pdf',
            ],
            [
                'user_id' => $students->first()->id,
                'course_id' => $courses->where('title', 'JavaScript Fundamentals')->first()->id ?? $courses->skip(1)->first()->id,
                'progress' => 75,
                'completed' => false,
                'certificate_url' => null,
            ],
            [
                'user_id' => $students->first()->id,
                'course_id' => $courses->where('title', 'Full Stack Web Development Bootcamp')->first()->id ?? $courses->skip(2)->first()->id,
                'progress' => 45,
                'completed' => false,
                'certificate_url' => null,
            ],
            // Student 2 enrollments
            [
                'user_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'course_id' => $courses->where('title', 'UI/UX Design Fundamentals')->first()->id ?? $courses->skip(3)->first()->id,
                'progress' => 100,
                'completed' => true,
                'certificate_url' => 'https://example.com/certificates/student2-uiux-design.pdf',
            ],
            [
                'user_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'course_id' => $courses->where('title', 'React Advanced Patterns')->first()->id ?? $courses->skip(4)->first()->id,
                'progress' => 60,
                'completed' => false,
                'certificate_url' => null,
            ],
            [
                'user_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'course_id' => $courses->where('title', 'Git and Version Control')->first()->id ?? $courses->last()->id,
                'progress' => 100,
                'completed' => true,
                'certificate_url' => 'https://example.com/certificates/student2-git.pdf',
            ],
            // Student 3 enrollments
            [
                'user_id' => $students->last()->id,
                'course_id' => $courses->where('title', 'Machine Learning with Python')->first()->id ?? $courses->skip(5)->first()->id,
                'progress' => 30,
                'completed' => false,
                'certificate_url' => null,
            ],
            [
                'user_id' => $students->last()->id,
                'course_id' => $courses->where('title', 'Database Design and SQL')->first()->id ?? $courses->skip(6)->first()->id,
                'progress' => 85,
                'completed' => false,
                'certificate_url' => null,
            ],
            [
                'user_id' => $students->last()->id,
                'course_id' => $courses->where('title', 'Introduction to Programming')->first()->id ?? $courses->first()->id,
                'progress' => 100,
                'completed' => true,
                'certificate_url' => 'https://example.com/certificates/student3-intro-programming.pdf',
            ],
        ];

        foreach ($enrollments as $enrollmentData) {
            Enrollment::create($enrollmentData);
        }

        $this->command->info('Enrollment seeder completed successfully!');
    }
}
