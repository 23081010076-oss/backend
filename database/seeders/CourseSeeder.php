<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCurriculum;
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
                'total_videos' => 45,
                'certificate_url' => 'https://example.com/certificates/fullstack-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Dasar-dasar Web', 'title' => 'Pengenalan Web Development', 'description' => 'Memahami dasar-dasar web dan cara kerja internet', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/pengenalan-web'],
                    ['section' => 'Bab 1: Dasar-dasar Web', 'title' => 'HTML & CSS Fundamentals', 'description' => 'Belajar struktur dan styling halaman web', 'duration' => '8 jam', 'video_url' => 'https://youtube.com/embed/html-css-fundamentals'],
                    ['section' => 'Bab 2: JavaScript', 'title' => 'JavaScript Basics', 'description' => 'Dasar pemrograman JavaScript untuk interaktivitas', 'duration' => '10 jam', 'video_url' => 'https://youtube.com/embed/javascript-basics'],
                    ['section' => 'Bab 3: React.js', 'title' => 'React.js Framework', 'description' => 'Membangun aplikasi dengan React.js', 'duration' => '15 jam', 'video_url' => 'https://youtube.com/embed/reactjs-framework'],
                    ['section' => 'Bab 4: Backend', 'title' => 'Node.js & Express', 'description' => 'Backend development dengan Node.js', 'duration' => '12 jam', 'video_url' => 'https://youtube.com/embed/nodejs-express'],
                    ['section' => 'Bab 4: Backend', 'title' => 'Database & MongoDB', 'description' => 'Manajemen data dengan MongoDB', 'duration' => '8 jam', 'video_url' => 'https://youtube.com/embed/database-mongodb'],
                    ['section' => 'Bab 5: Deployment', 'title' => 'Deployment & DevOps', 'description' => 'Deploy aplikasi ke production', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/deployment-devops'],
                ],
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
                'total_videos' => 28,
                'certificate_url' => 'https://example.com/certificates/javascript-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Dasar JavaScript', 'title' => 'Variables & Data Types', 'description' => 'Pengenalan variabel dan tipe data di JavaScript', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/js-variables'],
                    ['section' => 'Bab 1: Dasar JavaScript', 'title' => 'Functions & Scope', 'description' => 'Memahami fungsi dan scope', 'duration' => '3 jam', 'video_url' => 'https://youtube.com/embed/js-functions'],
                    ['section' => 'Bab 2: Struktur Data', 'title' => 'Arrays & Objects', 'description' => 'Bekerja dengan array dan object', 'duration' => '4 jam', 'video_url' => 'https://youtube.com/embed/js-arrays'],
                    ['section' => 'Bab 3: DOM', 'title' => 'DOM Manipulation', 'description' => 'Mengubah halaman web dengan JavaScript', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/js-dom'],
                    ['section' => 'Bab 4: ES6+', 'title' => 'ES6+ Features', 'description' => 'Fitur modern JavaScript', 'duration' => '4 jam', 'video_url' => 'https://youtube.com/embed/js-es6'],
                    ['section' => 'Bab 5: Async', 'title' => 'Async Programming', 'description' => 'Promise, async/await, dan fetch API', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/js-async'],
                ],
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
                'total_videos' => 20,
                'certificate_url' => 'https://example.com/certificates/programming-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Pengenalan', 'title' => 'Apa itu Programming?', 'description' => 'Pengenalan konsep pemrograman', 'duration' => '1 jam', 'video_url' => 'https://youtube.com/embed/intro-programming'],
                    ['section' => 'Bab 1: Pengenalan', 'title' => 'Instalasi Python', 'description' => 'Setup environment development', 'duration' => '30 menit', 'video_url' => 'https://youtube.com/embed/python-install'],
                    ['section' => 'Bab 2: Dasar Python', 'title' => 'Variabel dan Operasi', 'description' => 'Dasar-dasar variabel dan operasi matematika', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/python-variables'],
                    ['section' => 'Bab 2: Dasar Python', 'title' => 'Kondisional (If/Else)', 'description' => 'Pengambilan keputusan dalam program', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/python-conditional'],
                    ['section' => 'Bab 3: Loops & Functions', 'title' => 'Perulangan (Loops)', 'description' => 'For loop dan while loop', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/python-loops'],
                    ['section' => 'Bab 3: Loops & Functions', 'title' => 'Fungsi', 'description' => 'Membuat dan menggunakan fungsi', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/python-functions'],
                ],
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
                'total_videos' => 52,
                'certificate_url' => 'https://example.com/certificates/react-advanced-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Hooks', 'title' => 'React Hooks Deep Dive', 'description' => 'useState, useEffect, useRef, dan custom hooks', 'duration' => '6 jam', 'video_url' => 'https://youtube.com/embed/react-hooks'],
                    ['section' => 'Bab 2: State Management', 'title' => 'Context & State Management', 'description' => 'Global state dengan Context API dan Redux', 'duration' => '8 jam', 'video_url' => 'https://youtube.com/embed/react-state'],
                    ['section' => 'Bab 3: Performance', 'title' => 'Performance Optimization', 'description' => 'Memoization, lazy loading, dan profiling', 'duration' => '6 jam', 'video_url' => 'https://youtube.com/embed/react-performance'],
                    ['section' => 'Bab 4: Patterns', 'title' => 'Advanced Patterns', 'description' => 'HOC, Render Props, Compound Components', 'duration' => '8 jam', 'video_url' => 'https://youtube.com/embed/react-patterns'],
                    ['section' => 'Bab 5: Testing', 'title' => 'Testing React Apps', 'description' => 'Unit testing dan integration testing', 'duration' => '6 jam', 'video_url' => 'https://youtube.com/embed/react-testing'],
                ],
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
                'total_videos' => 72,
                'certificate_url' => 'https://example.com/certificates/ml-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Python Data Science', 'title' => 'Python untuk Data Science', 'description' => 'NumPy, Pandas, dan Matplotlib', 'duration' => '10 jam', 'video_url' => 'https://youtube.com/embed/python-datascience'],
                    ['section' => 'Bab 2: Supervised Learning', 'title' => 'Supervised Learning', 'description' => 'Regresi dan klasifikasi', 'duration' => '15 jam', 'video_url' => 'https://youtube.com/embed/ml-supervised'],
                    ['section' => 'Bab 3: Unsupervised Learning', 'title' => 'Unsupervised Learning', 'description' => 'Clustering dan dimensionality reduction', 'duration' => '12 jam', 'video_url' => 'https://youtube.com/embed/ml-unsupervised'],
                    ['section' => 'Bab 4: Deep Learning', 'title' => 'Deep Learning', 'description' => 'Neural networks dengan TensorFlow', 'duration' => '20 jam', 'video_url' => 'https://youtube.com/embed/ml-deeplearning'],
                    ['section' => 'Bab 5: Deployment', 'title' => 'Model Deployment', 'description' => 'Deploy ML model ke production', 'duration' => '8 jam', 'video_url' => 'https://youtube.com/embed/ml-deployment'],
                ],
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
                'total_videos' => 38,
                'certificate_url' => 'https://example.com/certificates/flutter-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Dart', 'title' => 'Dart Programming', 'description' => 'Dasar bahasa pemrograman Dart', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/dart-basics'],
                    ['section' => 'Bab 2: Widgets', 'title' => 'Flutter Widgets', 'description' => 'Memahami sistem widget Flutter', 'duration' => '8 jam', 'video_url' => 'https://youtube.com/embed/flutter-widgets'],
                    ['section' => 'Bab 3: State', 'title' => 'State Management', 'description' => 'Provider dan Riverpod', 'duration' => '6 jam', 'video_url' => 'https://youtube.com/embed/flutter-state'],
                    ['section' => 'Bab 4: API', 'title' => 'API Integration', 'description' => 'Menghubungkan app dengan backend', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/flutter-api'],
                    ['section' => 'Bab 5: Publishing', 'title' => 'Publishing Apps', 'description' => 'Publish ke Play Store dan App Store', 'duration' => '4 jam', 'video_url' => 'https://youtube.com/embed/flutter-publish'],
                ],
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
                'total_videos' => 32,
                'certificate_url' => 'https://example.com/certificates/database-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Dasar Database', 'title' => 'Database Concepts', 'description' => 'Konsep dasar database relasional', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/db-concepts'],
                    ['section' => 'Bab 2: SQL Dasar', 'title' => 'SQL Basics', 'description' => 'SELECT, INSERT, UPDATE, DELETE', 'duration' => '4 jam', 'video_url' => 'https://youtube.com/embed/sql-basics'],
                    ['section' => 'Bab 3: SQL Lanjutan', 'title' => 'Advanced SQL', 'description' => 'JOIN, subquery, dan aggregate functions', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/sql-advanced'],
                    ['section' => 'Bab 4: Design', 'title' => 'Database Design', 'description' => 'Normalisasi dan ERD', 'duration' => '4 jam', 'video_url' => 'https://youtube.com/embed/db-design'],
                    ['section' => 'Bab 5: Performance', 'title' => 'Performance Tuning', 'description' => 'Indexing dan query optimization', 'duration' => '3 jam', 'video_url' => 'https://youtube.com/embed/db-performance'],
                ],
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
                'total_videos' => 35,
                'certificate_url' => 'https://example.com/certificates/uiux-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Design Thinking', 'title' => 'Design Thinking', 'description' => 'Metodologi design thinking', 'duration' => '3 jam', 'video_url' => 'https://youtube.com/embed/design-thinking'],
                    ['section' => 'Bab 2: Research', 'title' => 'User Research', 'description' => 'Memahami kebutuhan pengguna', 'duration' => '4 jam', 'video_url' => 'https://youtube.com/embed/user-research'],
                    ['section' => 'Bab 3: Wireframe', 'title' => 'Wireframing', 'description' => 'Membuat wireframe dengan Figma', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/wireframing'],
                    ['section' => 'Bab 4: Visual', 'title' => 'Visual Design', 'description' => 'Warna, tipografi, dan layout', 'duration' => '6 jam', 'video_url' => 'https://youtube.com/embed/visual-design'],
                    ['section' => 'Bab 5: Prototype', 'title' => 'Prototyping', 'description' => 'Membuat prototype interaktif', 'duration' => '5 jam', 'video_url' => 'https://youtube.com/embed/prototyping'],
                ],
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
                'total_videos' => 68,
                'certificate_url' => 'https://example.com/certificates/aws-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: AWS Fundamentals', 'title' => 'AWS Fundamentals', 'description' => 'Pengenalan cloud computing dan AWS', 'duration' => '4 jam', 'video_url' => 'https://youtube.com/embed/aws-fundamentals'],
                    ['section' => 'Bab 2: EC2', 'title' => 'EC2 & Networking', 'description' => 'Virtual servers dan VPC', 'duration' => '10 jam', 'video_url' => 'https://youtube.com/embed/aws-ec2'],
                    ['section' => 'Bab 3: Storage', 'title' => 'S3 & Storage', 'description' => 'Object storage dan CDN', 'duration' => '6 jam', 'video_url' => 'https://youtube.com/embed/aws-s3'],
                    ['section' => 'Bab 4: Serverless', 'title' => 'Lambda & Serverless', 'description' => 'Serverless computing', 'duration' => '8 jam', 'video_url' => 'https://youtube.com/embed/aws-lambda'],
                    ['section' => 'Bab 5: DevOps', 'title' => 'DevOps on AWS', 'description' => 'CI/CD dan infrastructure as code', 'duration' => '12 jam', 'video_url' => 'https://youtube.com/embed/aws-devops'],
                ],
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
                'total_videos' => 15,
                'certificate_url' => 'https://example.com/certificates/git-template.pdf',
                'curriculums' => [
                    ['section' => 'Bab 1: Git Basics', 'title' => 'Git Basics', 'description' => 'Init, add, commit, dan push', 'duration' => '1 jam', 'video_url' => 'https://youtube.com/embed/git-basics'],
                    ['section' => 'Bab 2: Branching', 'title' => 'Branching & Merging', 'description' => 'Bekerja dengan branch', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/git-branching'],
                    ['section' => 'Bab 3: Collaboration', 'title' => 'Collaboration', 'description' => 'Pull request dan code review', 'duration' => '2 jam', 'video_url' => 'https://youtube.com/embed/git-collaboration'],
                    ['section' => 'Bab 4: Workflow', 'title' => 'Git Workflow', 'description' => 'Gitflow dan trunk-based development', 'duration' => '1 jam', 'video_url' => 'https://youtube.com/embed/git-workflow'],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $curriculums = $courseData['curriculums'] ?? [];
            unset($courseData['curriculums']);

            $course = Course::create($courseData);

            $sectionOrder = 0;
            $currentSection = null;
            
            foreach ($curriculums as $order => $curriculum) {
                $section = $curriculum['section'] ?? null;
                if ($section !== $currentSection) {
                    $sectionOrder++;
                    $currentSection = $section;
                }

                CourseCurriculum::create([
                    'course_id' => $course->id,
                    'section' => $section,
                    'section_order' => $section ? $sectionOrder : 0,
                    'title' => $curriculum['title'],
                    'description' => $curriculum['description'] ?? null,
                    'duration' => $curriculum['duration'] ?? null,
                    'video_url' => $curriculum['video_url'] ?? null,
                    'order' => $order + 1,
                ]);
            }
        }

        $this->command->info('Course seeder with curriculums completed successfully!');
    }
}
