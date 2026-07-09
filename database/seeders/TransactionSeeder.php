<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $enrollments = Enrollment::all();
        $subscriptions = Subscription::all();
        $mentoringSession = MentoringSession::all();

        if ($students->isEmpty()) {
            $this->command->warn('No students found. Please run UserSeeder first.');
            return;
        }

        $transactions = [];

        // Course enrollment transactions - PAID transactions link to Enrollment
        if ($enrollments->isNotEmpty()) {
            foreach ($enrollments->take(5) as $index => $enrollment) {
                $transactions[] = [
                    'user_id' => $enrollment->user_id,
                    'transaction_code' => 'TRX-COURSE-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'type' => 'course_enrollment',
                    'transactionable_id' => $enrollment->id, // Link to Enrollment (paid)
                    'transactionable_type' => 'App\Models\Enrollment',
                    'amount' => $enrollment->course->price ?? rand(500000, 2500000),
                    'payment_method' => ['manual', 'bank_transfer'][array_rand(['manual', 'bank_transfer'])],
                    'status' => 'paid',
                    'payment_proof' => 'payment-proofs/sample-proof-' . ($index + 1) . '.jpg',
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => now()->subDays(rand(1, 30)),
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Subscription transactions
        if ($subscriptions->isNotEmpty()) {
            foreach ($subscriptions->where('status', 'active')->take(3) as $index => $subscription) {
                $transactions[] = [
                    'user_id' => $subscription->user_id,
                    'transaction_code' => 'TRX-SUB-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'type' => 'subscription',
                    'transactionable_id' => $subscription->id,
                    'transactionable_type' => 'App\Models\Subscription',
                    'amount' => $subscription->price,
                    'payment_method' => ['manual', 'bank_transfer'][array_rand(['manual', 'bank_transfer'])],
                    'status' => 'paid',
                    'payment_proof' => 'payment-proofs/sample-subscription-' . ($index + 1) . '.jpg',
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => $subscription->start_date,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Mentoring session transactions
        if ($mentoringSession->isNotEmpty()) {
            foreach ($mentoringSession->whereIn('status', ['scheduled', 'completed'])->take(4) as $index => $session) {
                $amount = $session->type === 'academic' ? 150000 : 200000;

                $transactions[] = [
                    'user_id' => $session->member_id,
                    'transaction_code' => 'TRX-MENT-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'type' => 'mentoring_session',
                    'transactionable_id' => $session->id,
                    'transactionable_type' => 'App\Models\MentoringSession',
                    'amount' => $amount,
                    'payment_method' => ['manual', 'bank_transfer'][array_rand(['manual', 'bank_transfer'])],
                    'status' => $session->status === 'completed' ? 'paid' : 'pending',
                    'payment_proof' => $session->status === 'completed' ? 'payment-proofs/mentoring-' . ($index + 1) . '.jpg' : null,
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => $session->status === 'completed' ? $session->schedule : null,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Add sample mentoring transactions for first 3 students
        if ($mentoringSession->isNotEmpty() && $students->count() >= 3) {
            $firstSession = $mentoringSession->first();
            foreach ($students->take(3) as $index => $student) {
                $transactions[] = [
                    'user_id' => $student->id,
                    'transaction_code' => 'TRX-MENT-SAMPLE-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'type' => 'mentoring_session',
                    'transactionable_id' => $firstSession->id,
                    'transactionable_type' => 'App\Models\MentoringSession',
                    'amount' => 150000,
                    'payment_method' => 'bank_transfer',
                    'status' => $index === 0 ? 'paid' : 'pending',
                    'payment_proof' => $index === 0 ? 'payment-proofs/mentoring-sample.jpg' : null,
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => $index === 0 ? now()->subDays(5) : null,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Add sample mentoring transactions for user ID 3 (for testing)
        if ($mentoringSession->isNotEmpty()) {
            $user3 = User::find(3);
            if ($user3) {
                $firstSession = $mentoringSession->first();
                $transactions[] = [
                    'user_id' => 3,
                    'transaction_code' => 'TRX-MENT-USER3-' . now()->format('Ymd') . '-0001',
                    'type' => 'mentoring_session',
                    'transactionable_id' => $firstSession->id,
                    'transactionable_type' => 'App\Models\MentoringSession',
                    'amount' => 150000,
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'payment_proof' => 'payment-proofs/mentoring-user3.jpg',
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => now()->subDays(3),
                    'expired_at' => now()->addHours(24),
                ];

                // Add one pending
                $transactions[] = [
                    'user_id' => 3,
                    'transaction_code' => 'TRX-MENT-USER3-' . now()->format('Ymd') . '-0002',
                    'type' => 'mentoring_session',
                    'transactionable_id' => $firstSession->id,
                    'transactionable_type' => 'App\Models\MentoringSession',
                    'amount' => 200000,
                    'payment_method' => 'manual',
                    'status' => 'pending',
                    'payment_proof' => null,
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => null,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Add sample course transactions for user ID 3
        if ($enrollments->isNotEmpty()) {
            $user3 = User::find(3);
            if ($user3) {
                $firstEnrollment = $enrollments->first();
                // Paid course transaction
                $transactions[] = [
                    'user_id' => 3,
                    'transaction_code' => 'TRX-COURSE-USER3-' . now()->format('Ymd') . '-0001',
                    'type' => 'course_enrollment',
                    'transactionable_id' => $firstEnrollment->id,
                    'transactionable_type' => 'App\Models\Enrollment',
                    'amount' => $firstEnrollment->course->price ?? 500000,
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'payment_proof' => 'payment-proofs/course-user3.jpg',
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => now()->subDays(7),
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Add pending course transaction for user ID 3 (links to Course, not Enrollment)
        $courses = Course::all();
        if ($courses->isNotEmpty()) {
            $user3 = User::find(3);
            if ($user3) {
                $transactions[] = [
                    'user_id' => 3,
                    'transaction_code' => 'TRX-COURSE-USER3-' . now()->format('Ymd') . '-0002',
                    'type' => 'course_enrollment',
                    'transactionable_id' => $courses->random()->id,
                    'transactionable_type' => 'App\Models\Course',
                    'amount' => 750000,
                    'payment_method' => 'manual',
                    'status' => 'pending',
                    'payment_proof' => null,
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => null,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Add sample subscription transactions for user ID 3
        if ($subscriptions->isNotEmpty()) {
            $user3 = User::find(3);
            if ($user3) {
                $firstSub = $subscriptions->first();
                // Paid subscription
                $transactions[] = [
                    'user_id' => 3,
                    'transaction_code' => 'TRX-SUB-USER3-' . now()->format('Ymd') . '-0001',
                    'type' => 'subscription',
                    'transactionable_id' => $firstSub->id,
                    'transactionable_type' => 'App\Models\Subscription',
                    'amount' => $firstSub->price ?? 199000,
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'payment_proof' => 'payment-proofs/sub-user3.jpg',
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => now()->subDays(10),
                    'expired_at' => now()->addHours(24),
                ];

                // Pending subscription
                $transactions[] = [
                    'user_id' => 3,
                    'transaction_code' => 'TRX-SUB-USER3-' . now()->format('Ymd') . '-0002',
                    'type' => 'subscription',
                    'transactionable_id' => $firstSub->id,
                    'transactionable_type' => 'App\Models\Subscription',
                    'amount' => 199000,
                    'payment_method' => 'manual',
                    'status' => 'pending',
                    'payment_proof' => null,
                    'payment_details' => json_encode([
                        'bank_name' => 'BCA',
                        'account_number' => '1234567890',
                        'account_holder' => 'PT Edukasi Masa Depan',
                    ]),
                    'paid_at' => null,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Add pending transaction (links to Course since not yet paid)
        $courses = Course::all();
        if ($courses->isNotEmpty()) {
            $transactions[] = [
                'user_id' => $students->first()->id,
                'transaction_code' => 'TRX-PENDING-' . now()->format('Ymd') . '-0001',
                'type' => 'course_enrollment',
                'transactionable_id' => $courses->random()->id, // Link to Course (pending)
                'transactionable_type' => 'App\Models\Course',
                'amount' => 750000,
                'payment_method' => 'bank_transfer',
                'status' => 'pending',
                'payment_proof' => null,
                'payment_details' => null,
                'paid_at' => null,
                'expired_at' => now()->addHours(24),
            ];
        }

        // Add failed subscription transaction
        if ($subscriptions->isNotEmpty()) {
            $transactions[] = [
                'user_id' => $students->last()->id,
                'transaction_code' => 'TRX-FAILED-' . now()->format('Ymd') . '-0001',
                'type' => 'subscription',
                'transactionable_id' => $subscriptions->first()->id,
                'transactionable_type' => 'App\Models\Subscription',
                'amount' => 999000,
                'payment_method' => 'bank_transfer',
                'status' => 'failed',
                'payment_proof' => null,
                'payment_details' => json_encode(['error' => 'Insufficient funds']),
                'paid_at' => null,
                'expired_at' => now()->subHours(1),
            ];
        }

        foreach ($transactions as $transactionData) {
            Transaction::firstOrCreate(
                ['transaction_code' => $transactionData['transaction_code']],
                $transactionData
            );
        }

        $this->command->info('Transaction seeder completed successfully!');
    }
}
