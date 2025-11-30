<?php

namespace Database\Seeders;

use App\Models\Transaction;
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

        // Course enrollment transactions
        if ($enrollments->isNotEmpty()) {
            foreach ($enrollments->take(5) as $index => $enrollment) {
                $transactions[] = [
                    'user_id' => $enrollment->user_id,
                    'transaction_code' => 'TRX-COURSE-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'type' => 'course_enrollment',
                    'transactionable_id' => $enrollment->id,
                    'transactionable_type' => 'App\Models\Enrollment',
                    'amount' => rand(500000, 2500000),
                    'payment_method' => ['qris', 'bank_transfer', 'virtual_account', 'credit_card'][array_rand(['qris', 'bank_transfer', 'virtual_account', 'credit_card'])],
                    'status' => 'paid',
                    'payment_proof' => null,
                    'payment_details' => json_encode(['snap_token' => 'snap-token-' . uniqid()]),
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
                    'payment_method' => ['qris', 'bank_transfer', 'virtual_account'][array_rand(['qris', 'bank_transfer', 'virtual_account'])],
                    'status' => 'paid',
                    'payment_proof' => null,
                    'payment_details' => json_encode(['snap_token' => 'snap-token-' . uniqid()]),
                    'paid_at' => $subscription->start_date,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Mentoring session transactions
        if ($mentoringSession->isNotEmpty()) {
            foreach ($mentoringSession->whereIn('status', ['scheduled', 'completed'])->take(4) as $index => $session) {
                $amount = $session->type === 'academic' ? 150000 : 200000;
                
                // Map mentoring session payment_method to transaction payment_method
                $paymentMethodMap = [
                    'qris' => 'qris',
                    'bank' => 'bank_transfer',
                    'va' => 'virtual_account',
                    'manual' => 'manual',
                ];
                $paymentMethod = $paymentMethodMap[$session->payment_method] ?? 'qris';
                
                $transactions[] = [
                    'user_id' => $session->member_id,
                    'transaction_code' => 'TRX-MENT-' . now()->format('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'type' => 'mentoring_session',
                    'transactionable_id' => $session->id,
                    'transactionable_type' => 'App\Models\MentoringSession',
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'status' => $session->status === 'completed' ? 'paid' : 'pending',
                    'payment_proof' => null,
                    'payment_details' => json_encode(['snap_token' => 'snap-token-' . uniqid()]),
                    'paid_at' => $session->status === 'completed' ? $session->schedule : null,
                    'expired_at' => now()->addHours(24),
                ];
            }
        }

        // Add some pending and failed transactions
        $transactions[] = [
            'user_id' => $students->first()->id,
            'transaction_code' => 'TRX-PENDING-' . now()->format('Ymd') . '-0001',
            'type' => 'course_enrollment',
            'transactionable_id' => $enrollments->first()->id ?? 1,
            'transactionable_type' => 'App\Models\Enrollment',
            'amount' => 750000,
            'payment_method' => 'manual',
            'status' => 'pending',
            'payment_proof' => 'payment-proofs/proof-' . uniqid() . '.jpg',
            'payment_details' => null,
            'paid_at' => null,
            'expired_at' => now()->addHours(24),
        ];

        $transactions[] = [
            'user_id' => $students->last()->id,
            'transaction_code' => 'TRX-FAILED-' . now()->format('Ymd') . '-0001',
            'type' => 'subscription',
            'transactionable_id' => $subscriptions->first()->id ?? 1,
            'transactionable_type' => 'App\Models\Subscription',
            'amount' => 999000,
            'payment_method' => 'credit_card',
            'status' => 'failed',
            'payment_proof' => null,
            'payment_details' => json_encode(['error' => 'Insufficient funds']),
            'paid_at' => null,
            'expired_at' => now()->subHours(1),
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        $this->command->info('Transaction seeder completed successfully!');
    }
}
