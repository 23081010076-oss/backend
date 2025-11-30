<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
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

        $reviews = [
            // Reviews for courses
            [
                'user_id' => $students->first()->id,
                'reviewable_id' => $courses->first()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 5,
                'comment' => 'Excellent course! The instructor explained everything clearly and the hands-on projects were very helpful. Highly recommended for beginners!',
            ],
            [
                'user_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'reviewable_id' => $courses->first()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 4,
                'comment' => 'Great content and well-structured. Would have loved more advanced topics though.',
            ],
            [
                'user_id' => $students->last()->id,
                'reviewable_id' => $courses->skip(1)->first()->id ?? $courses->first()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 5,
                'comment' => 'Best JavaScript course I\'ve taken! The examples are practical and easy to follow.',
            ],
            [
                'user_id' => $students->first()->id,
                'reviewable_id' => $courses->skip(2)->first()->id ?? $courses->first()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 4,
                'comment' => 'Very comprehensive bootcamp. The pace is good but sometimes challenging. Great for career switchers.',
            ],
            [
                'user_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'reviewable_id' => $courses->skip(3)->first()->id ?? $courses->first()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 5,
                'comment' => 'Amazing UI/UX course! The instructor is very knowledgeable and the Figma tutorials are top-notch.',
            ],
            [
                'user_id' => $students->last()->id,
                'reviewable_id' => $courses->skip(4)->first()->id ?? $courses->first()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 3,
                'comment' => 'Good course but assumes you already know the basics. Not suitable for complete beginners.',
            ],
            [
                'user_id' => $students->first()->id,
                'reviewable_id' => $courses->skip(5)->first()->id ?? $courses->first()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 5,
                'comment' => 'Incredible ML course! The theory is explained well and the practical projects are industry-relevant.',
            ],
            [
                'user_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'reviewable_id' => $courses->last()->id,
                'reviewable_type' => 'App\Models\Course',
                'rating' => 5,
                'comment' => 'Essential course for every developer. Git is now my favorite tool thanks to this course!',
            ],
        ];

        foreach ($reviews as $reviewData) {
            Review::create($reviewData);
        }

        $this->command->info('Review seeder completed successfully!');
    }
}
