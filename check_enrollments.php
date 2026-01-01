<?php

use App\Models\User;

// Get users with active subscriptions
$users = User::whereHas('subscriptions', function ($q) {
    $q->where('status', 'active');
})->get();

echo "=== USERS WITH ACTIVE SUBSCRIPTIONS ===\n\n";

foreach ($users as $user) {
    $subscription = $user->activeSubscription();
    $enrollments = $user->enrollments()->with('course')->get();
    
    echo "👤 {$user->name} ({$user->email})\n";
    echo "   📋 Plan: " . ($subscription ? $subscription->plan : 'none') . "\n";
    echo "   📚 Total Enrollments: {$enrollments->count()}\n";
    
    if ($enrollments->isNotEmpty()) {
        echo "   📖 Courses:\n";
        foreach ($enrollments as $enrollment) {
            echo "      - {$enrollment->course->title} ({$enrollment->course->access_type})\n";
        }
    }
    
    echo "\n";
}
