<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnrollmentService;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\CurriculumProgress;

class VerifyCertificate extends Command
{
    protected $signature = 'app:verify-certificate';
    protected $description = 'Verify certificate generation logic';

    public function handle()
    {
        $service = app(EnrollmentService::class);
        $user = User::first();
        $course = Course::with('curriculums')->has('curriculums')->first();

        if (!$course) {
            $this->error("No course with curriculums found.");
            return 1;
        }

        $this->info("Testing with Course: {$course->title} (ID: {$course->id})");
        $this->info("User: {$user->email} (ID: {$user->id})");

        // Clean slate
        $enrollment = Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id], 
            ['progress' => 0, 'completed' => false, 'certificate_url' => null]
        );
        CurriculumProgress::where('enrollment_id', $enrollment->id)->delete();
        $enrollment->update(['progress' => 0, 'completed' => false, 'certificate_url' => null]);

        $curriculums = $course->curriculums;
        $total = $curriculums->count();
        $this->info("Total Curriculums: $total");

        foreach ($curriculums as $index => $curriculum) {
            $this->line("Marking curriculum {$curriculum->id} as completed (" . ($index + 1) . "/$total)...");
            $service->markCurriculumCompleted($enrollment, $curriculum->id);
        }

        $enrollment->refresh();
        $this->info("Final Progress: {$enrollment->progress}%");
        
        if ($enrollment->certificate_url) {
            $this->info("Certificate URL: " . $enrollment->certificate_url);
            return 0;
        } else {
            $this->error("Certificate URL: None");
            return 1;
        }
    }
}
