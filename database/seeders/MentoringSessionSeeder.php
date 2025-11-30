<?php

namespace Database\Seeders;

use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class MentoringSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get mentors and students
        $mentors = User::where('role', 'mentor')->get();
        $students = User::where('role', 'student')->get();

        if ($mentors->isEmpty() || $students->isEmpty()) {
            $this->command->warn('No mentors or students found. Please run UserSeeder first.');
            return;
        }

        $sessions = [
            // Scheduled sessions
            [
                'mentor_id' => $mentors->first()->id,
                'member_id' => $students->first()->id,
                'session_id' => 'MENT-' . now()->format('Ymd') . '-001',
                'type' => 'academic',
                'schedule' => now()->addDays(3)->setTime(14, 0),
                'meeting_link' => 'https://meet.google.com/abc-defg-hij',
                'payment_method' => 'qris',
                'status' => 'scheduled',
                'need_assessment_status' => 'completed',
            ],
            [
                'mentor_id' => $mentors->skip(1)->first()->id ?? $mentors->first()->id,
                'member_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'session_id' => 'MENT-' . now()->format('Ymd') . '-002',
                'type' => 'life_plan',
                'schedule' => now()->addDays(5)->setTime(10, 0),
                'meeting_link' => 'https://zoom.us/j/123456789',
                'payment_method' => 'bank',
                'status' => 'scheduled',
                'need_assessment_status' => 'pending',
            ],
            [
                'mentor_id' => $mentors->last()->id,
                'member_id' => $students->last()->id,
                'session_id' => 'MENT-' . now()->format('Ymd') . '-003',
                'type' => 'academic',
                'schedule' => now()->addDays(7)->setTime(16, 0),
                'meeting_link' => 'https://meet.google.com/xyz-uvwx-rst',
                'payment_method' => 'va',
                'status' => 'scheduled',
                'need_assessment_status' => 'completed',
            ],
            // Completed sessions
            [
                'mentor_id' => $mentors->first()->id,
                'member_id' => $students->skip(2)->first()->id ?? $students->first()->id,
                'session_id' => 'MENT-' . now()->subDays(10)->format('Ymd') . '-001',
                'type' => 'academic',
                'schedule' => now()->subDays(10)->setTime(14, 0),
                'meeting_link' => 'https://meet.google.com/completed-session-1',
                'payment_method' => 'qris',
                'status' => 'completed',
                'need_assessment_status' => 'completed',
            ],
            [
                'mentor_id' => $mentors->skip(1)->first()->id ?? $mentors->first()->id,
                'member_id' => $students->first()->id,
                'session_id' => 'MENT-' . now()->subDays(15)->format('Ymd') . '-002',
                'type' => 'life_plan',
                'schedule' => now()->subDays(15)->setTime(11, 0),
                'meeting_link' => 'https://zoom.us/j/completed-session-2',
                'payment_method' => 'bank',
                'status' => 'completed',
                'need_assessment_status' => 'completed',
            ],
            // Pending sessions
            [
                'mentor_id' => $mentors->last()->id,
                'member_id' => $students->skip(1)->first()->id ?? $students->first()->id,
                'session_id' => 'MENT-' . now()->format('Ymd') . '-004',
                'type' => 'academic',
                'schedule' => null,
                'meeting_link' => null,
                'payment_method' => 'manual',
                'status' => 'pending',
                'need_assessment_status' => 'pending',
            ],
            [
                'mentor_id' => $mentors->first()->id,
                'member_id' => $students->last()->id,
                'session_id' => 'MENT-' . now()->format('Ymd') . '-005',
                'type' => 'life_plan',
                'schedule' => null,
                'meeting_link' => null,
                'payment_method' => null,
                'status' => 'pending',
                'need_assessment_status' => 'pending',
            ],
            // Cancelled session
            [
                'mentor_id' => $mentors->skip(1)->first()->id ?? $mentors->first()->id,
                'member_id' => $students->skip(2)->first()->id ?? $students->first()->id,
                'session_id' => 'MENT-' . now()->subDays(5)->format('Ymd') . '-003',
                'type' => 'academic',
                'schedule' => now()->subDays(5)->setTime(15, 0),
                'meeting_link' => 'https://meet.google.com/cancelled-session',
                'payment_method' => 'qris',
                'status' => 'cancelled',
                'need_assessment_status' => 'pending',
            ],
        ];

        foreach ($sessions as $sessionData) {
            MentoringSession::create($sessionData);
        }

        $this->command->info('MentoringSession seeder completed successfully!');
    }
}
