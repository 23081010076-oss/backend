<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\EnrollmentService::class);
$user = \App\Models\User::first();

// Ensure we find a course with curriculums
$course = \App\Models\Course::with('curriculums')->has('curriculums')->first();

if (!$course) {
    echo "No course with curriculums found.\n";
    exit(1);
}

echo "Testing with Course: {$course->title} (ID: {$course->id})\n";
echo "User: {$user->email} (ID: {$user->id})\n";

// Create or clean enrollment
$enrollment = \App\Models\Enrollment::updateOrCreate(
    ['user_id' => $user->id, 'course_id' => $course->id], 
    ['progress' => 0, 'completed' => false, 'certificate_url' => null]
);
\App\Models\CurriculumProgress::where('enrollment_id', $enrollment->id)->delete();

$curriculums = $course->curriculums;
$total = $curriculums->count();
echo "Total Curriculums: $total\n";

foreach ($curriculums as $index => $curriculum) {
    echo "Marking curriculum {$curriculum->id} as completed (" . ($index + 1) . "/$total)...\n";
    $service->markCurriculumCompleted($enrollment, $curriculum->id);
}

$enrollment->refresh();
echo "Final Progress: {$enrollment->progress}%\n";
echo "Certificate URL: " . ($enrollment->certificate_url ?? 'None') . "\n";
