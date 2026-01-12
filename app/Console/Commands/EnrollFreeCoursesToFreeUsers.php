<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnrollFreeCoursesToFreeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enroll:free-courses
                            {--user-id= : Specific user ID to enroll (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-enroll all users with any subscription to all free courses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting auto-enrollment for free courses...');
        $this->newLine();

        // Get all free courses
        $freeCourses = Course::where('access_type', 'free')->get();
        
        if ($freeCourses->isEmpty()) {
            $this->warn('⚠️  No free courses found.');
            return Command::SUCCESS;
        }

        $this->info("📚 Found {$freeCourses->count()} free courses:");
        foreach ($freeCourses as $course) {
            $this->line("   - {$course->title}");
        }
        $this->newLine();

        // Get users to enroll
        $usersQuery = User::query();
        
        if ($userId = $this->option('user-id')) {
            $usersQuery->where('id', $userId);
            $this->info("🎯 Targeting specific user ID: {$userId}");
        } else {
            $this->info("🎯 Targeting all users with any active subscription");
            // Get all users with active subscriptions
            $usersQuery->whereHas('subscriptions', function ($query) {
                $query->where('status', 'active');
            });
        }

        $users = $usersQuery->get();

        if ($users->isEmpty()) {
            $this->warn('⚠️  No users found matching criteria.');
            return Command::SUCCESS;
        }

        $this->info("👥 Found {$users->count()} users to process");
        $this->newLine();

        $totalEnrolled = 0;
        $totalSkipped = 0;

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            $enrolledCount = 0;
            
            foreach ($freeCourses as $course) {
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'progress' => 0,
                        'completed' => false,
                    ]
                );
                
                if ($enrollment->wasRecentlyCreated) {
                    $enrolledCount++;
                    $totalEnrolled++;
                    
                    Log::info('✅ Enrolled user to free course', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'course_id' => $course->id,
                        'course_title' => $course->title,
                    ]);
                } else {
                    $totalSkipped++;
                }
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('✅ Auto-enrollment completed!');
        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Users Processed', $users->count()],
                ['Free Courses', $freeCourses->count()],
                ['New Enrollments Created', $totalEnrolled],
                ['Already Enrolled (Skipped)', $totalSkipped],
            ]
        );

        return Command::SUCCESS;
    }
}
