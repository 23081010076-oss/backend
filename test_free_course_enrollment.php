<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST: USER BARU ENROLL KE FREE COURSE ===\n\n";

// Create new user without subscription
$newUser = App\Models\User::create([
    'name' => 'Test Student New',
    'email' => 'test.new.student@example.com',
    'password' => bcrypt('password123'),
    'role' => 'student',
]);

echo "✅ Created new user: {$newUser->name} (ID: {$newUser->id})\n";

// Check subscription
$subscription = $newUser->subscriptions()->where('status', 'active')->first();
echo "   Has subscription: " . ($subscription ? 'YES' : 'NO') . "\n\n";

// Get free course
$freeCourse = App\Models\Course::where('access_type', 'free')->first();
echo "📚 Free Course: {$freeCourse->title} (ID: {$freeCourse->id})\n";
echo "   Access Type: {$freeCourse->access_type}\n\n";

// Try to enroll
$service = app(App\Services\EnrollmentService::class);

try {
    $result = $service->enrollUserToCourse($newUser, $freeCourse);
    
    echo "🎉 SUCCESS!\n";
    echo "   Enrolled to: {$result['course']->title}\n";
    if (isset($result['enrollment'])) {
        echo "   Enrollment ID: {$result['enrollment']->id}\n";
        echo "   Progress: {$result['enrollment']->progress}%\n";
    }
    echo "   Is Free: " . ($result['is_free'] ? 'YES' : 'NO') . "\n";
    
    // Verify in database
    $enrollment = App\Models\Enrollment::where('user_id', $newUser->id)
        ->where('course_id', $freeCourse->id)
        ->first();
    
    if ($enrollment) {
        echo "\n✅ Verified in database: Enrollment exists!\n";
    } else {
        echo "\n❌ ERROR: Enrollment not found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ FAILED!\n";
    echo "   Error: {$e->getMessage()}\n";
}

// Cleanup
$newUser->delete();
echo "\n🧹 Cleaned up test user\n";
