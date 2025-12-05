<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Full Stack Web Development Bootcamp',
                'description' => 'Comprehensive bootcamp covering HTML, CSS, JavaScript, React, Node.js, and database management. Perfect for beginners who want to become full-stack developers.',
                'category' => 'Web Development',
                'type' => 'bootcamp',
                'level' => 'beginner',
                'instructor' => 'Dr. Ahmad Syafiq',
                'duration' => '12 weeks',
                'price' => 2500000.00,
                'access_type' => 'premium',
                'video_url' => 'https://youtube.com/embed/fullstack-intro',
                'video_duration' => '02:30:45',
                'total_videos' => 45,
                'certificate_url' => 'https://example.com/certificates/fullstack-template.pdf',
            ],
            [
                'title' => 'JavaScript Fundamentals',
                'description' => 'Learn JavaScript from basics to advanced concepts including ES6+, DOM manipulation, and asynchronous programming.',
                'category' => 'Web Development',
                'type' => 'course',
                'level' => 'beginner',
                'instructor' => 'Sari Wijayanti, M.Kom',
                'duration' => '6 weeks',
                'price' => 750000.00,
                'access_type' => 'regular',
                'video_url' => 'https://youtube.com/embed/javascript-basics',
                'video_duration' => '01:45:20',
                'total_videos' => 28,
                'certificate_url' => 'https://example.com/certificates/javascript-template.pdf',
            ],
            [
                'title' => 'Introduction to Programming',
                'description' => 'Perfect first course for complete beginners. Learn programming concepts using Python with hands-on exercises.',
                'category' => 'Programming',
                'type' => 'course',
                'level' => 'beginner',
                'instructor' => 'Budi Santoso, S.Kom',
                'duration' => '4 weeks',
                'price' => 0.00,
                'access_type' => 'free',
                'video_url' => 'https://youtube.com/embed/programming-intro',
                'video_duration' => '01:15:30',
                'total_videos' => 20,
                'certificate_url' => 'https://example.com/certificates/programming-template.pdf',
            ],
            [
                'title' => 'React Advanced Patterns',
                'description' => 'Advanced React concepts including hooks, context, performance optimization, and testing strategies.',
                'category' => 'Web Development',
                'type' => 'course',
                'level' => 'advanced',
                'instructor' => 'Prof. Indira Sari',
                'duration' => '8 weeks',
                'price' => 1200000.00,
                'access_type' => 'premium',
                'video_url' => 'https://youtube.com/embed/react-advanced',
                'video_duration' => '03:20:15',
                'total_videos' => 52,
                'certificate_url' => 'https://example.com/certificates/react-advanced-template.pdf',
            ],
            [
                'title' => 'Machine Learning with Python',
                'description' => 'Learn machine learning algorithms, data preprocessing, and model evaluation using scikit-learn and TensorFlow.',
                'category' => 'Data Science',
                'type' => 'bootcamp',
                'level' => 'intermediate',
                'instructor' => 'Dr. Ravi Kumar',
                'duration' => '16 weeks',
                'price' => 3500000.00,
                'access_type' => 'premium',
                'video_url' => 'https://youtube.com/embed/ml-python',
                'video_duration' => '04:45:30',
                'total_videos' => 72,
                'certificate_url' => 'https://example.com/certificates/ml-template.pdf',
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'description' => 'Build cross-platform mobile applications using Flutter and Dart programming language.',
                'category' => 'Mobile Development',
                'type' => 'course',
                'level' => 'intermediate',
                'instructor' => 'Maya Sari, M.T',
                'duration' => '10 weeks',
                'price' => 1800000.00,
                'access_type' => 'regular',
                'video_url' => 'https://youtube.com/embed/flutter-mobile',
                'video_duration' => '02:55:40',
                'total_videos' => 38,
                'certificate_url' => 'https://example.com/certificates/flutter-template.pdf',
            ],
            [
                'title' => 'Database Design and SQL',
                'description' => 'Master database design principles, SQL queries, and database optimization techniques.',
                'category' => 'Database',
                'type' => 'course',
                'level' => 'intermediate',
                'instructor' => 'Agus Prasetyo, S.T',
                'duration' => '6 weeks',
                'price' => 900000.00,
                'access_type' => 'regular',
                'video_url' => 'https://youtube.com/embed/database-sql',
                'video_duration' => '01:50:25',
                'total_videos' => 32,
                'certificate_url' => 'https://example.com/certificates/database-template.pdf',
            ],
            [
                'title' => 'UI/UX Design Fundamentals',
                'description' => 'Learn user interface and user experience design principles using Figma and design thinking methodology.',
                'category' => 'Design',
                'type' => 'course',
                'level' => 'beginner',
                'instructor' => 'Rina Kartika, S.Des',
                'duration' => '8 weeks',
                'price' => 1100000.00,
                'access_type' => 'regular',
                'video_url' => 'https://youtube.com/embed/uiux-design',
                'video_duration' => '02:15:10',
                'total_videos' => 35,
                'certificate_url' => 'https://example.com/certificates/uiux-template.pdf',
            ],
            [
                'title' => 'Cloud Computing with AWS',
                'description' => 'Learn Amazon Web Services including EC2, S3, Lambda, and deployment strategies for scalable applications.',
                'category' => 'Cloud Computing',
                'type' => 'bootcamp',
                'level' => 'advanced',
                'instructor' => 'Dr. Farid Wajdi',
                'duration' => '14 weeks',
                'price' => 4000000.00,
                'access_type' => 'premium',
                'video_url' => 'https://youtube.com/embed/aws-cloud',
                'video_duration' => '05:30:20',
                'total_videos' => 68,
                'certificate_url' => 'https://example.com/certificates/aws-template.pdf',
            ],
            [
                'title' => 'Git and Version Control',
                'description' => 'Essential skills for developers: master Git version control, branching strategies, and collaborative development.',
                'category' => 'DevOps',
                'type' => 'course',
                'level' => 'beginner',
                'instructor' => 'Doni Pratama, S.Kom',
                'duration' => '3 weeks',
                'price' => 0.00,
                'access_type' => 'free',
                'video_url' => 'https://youtube.com/embed/git-version-control',
                'video_duration' => '00:45:15',
                'total_videos' => 15,
                'certificate_url' => 'https://example.com/certificates/git-template.pdf',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        $this->command->info('Course seeder completed successfully!');
    }
}
