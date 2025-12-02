<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::limit(3)->pluck('id')->toArray();

        if ($students->isEmpty()) {
            $this->command->warn('No students found. Please run UserSeeder first.');
            return;
        }

        $subscriptions = [
            // Active premium subscription
            [
                'user_id' => $students->first()->id,
                'plan' => 'premium',
                'package_type' => 'all_in_one',
                'courses_ids' => null,
                'duration' => 12,
                'duration_unit' => 'months',
                'price' => 1999000.00,
                'auto_renew' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
                'status' => 'active',
            ],
            // Active regular subscription
            [
                'user_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'plan' => 'regular',
                'package_type' => 'all_in_one',
                'courses_ids' => null,
                'duration' => 6,
                'duration_unit' => 'months',
                'price' => 999000.00,
                'auto_renew' => false,
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(5),
                'status' => 'active',
            ],
            // Expired subscription with dynamic course IDs
            [
                'user_id' => $students->last()->id,
                'plan' => 'regular',
                'package_type' => 'single_course',
                'courses_ids' => !empty($courses) ? json_encode($courses) : null,
                'duration' => 3,
                'duration_unit' => 'months',
                'price' => 499000.00,
                'auto_renew' => false,
                'start_date' => now()->subMonths(6),
                'end_date' => now()->subMonths(3),
                'status' => 'expired',
            ],
            // Active free subscription
            [
                'user_id' => $students->skip(2)->first()->id ?? $students->first()->id,
                'plan' => 'free',
                'package_type' => 'all_in_one',
                'courses_ids' => null,
                'duration' => 12,
                'duration_unit' => 'months',
                'price' => 0.00,
                'auto_renew' => false,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => 'active',
            ],
            // Cancelled subscription
            [
                'user_id' => $students->first()->id,
                'plan' => 'regular',
                'package_type' => 'all_in_one',
                'courses_ids' => null,
                'duration' => 6,
                'duration_unit' => 'months',
                'price' => 999000.00,
                'auto_renew' => false,
                'start_date' => now()->subYear(),
                'end_date' => now()->subMonths(6),
                'status' => 'cancelled',
            ],
        ];

        foreach ($subscriptions as $subscriptionData) {
            Subscription::create($subscriptionData);
        }

        $this->command->info('Subscription seeder completed successfully!');
    }
}
