<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * ==========================================================================
 * TRANSACTION SERVICE (Service untuk Transaksi)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk transaksi pembayaran.
 * 
 * KENAPA PAKAI SERVICE?
 * - Logika transaksi kompleks (database transaction) terpusat
 * - Mudah di-test
 * - Controller tetap ringkas
 */
class TransactionService
{
    /**
     * Harga langganan berdasarkan plan
     */
    protected array $subscriptionPrices = [
        'regular' => 99000,
        'premium' => 199000,
    ];

    /**
     * Harga mentoring berdasarkan tipe
     */
    protected array $mentoringPrices = [
        'academic'  => 150000,
        'life_plan' => 200000,
    ];

    /**
     * Ambil daftar transaksi user
     */
    public function getUserTransactions(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Transaction::with(['user', 'transactionable'])
            ->where('user_id', $userId);

        // Filter berdasarkan tipe
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Buat transaksi untuk kursus
     * 
     * @throws \Exception jika sudah terdaftar
     */
    public function createCourseTransaction(Course $course, User $user, string $paymentMethod): array
    {
        // Cek apakah sudah terdaftar
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            throw new \Exception('Anda sudah terdaftar di kursus ini');
        }

        return DB::transaction(function () use ($course, $user, $paymentMethod) {
            // Buat enrollment
            $enrollment = Enrollment::create([
                'user_id'   => $user->id,
                'course_id' => $course->id,
                'progress'  => 0,
                'completed' => false,
            ]);

            // Buat transaksi
            $transaction = Transaction::create([
                'user_id'              => $user->id,
                'transaction_code'     => Transaction::generateTransactionCode(),
                'type'                 => 'course_enrollment',
                'transactionable_id'   => $enrollment->id,
                'transactionable_type' => Enrollment::class,
                'amount'               => $course->price,
                'payment_method'       => $paymentMethod,
                'status'               => 'pending',
                'expired_at'           => now()->addHours(24),
            ]);

            return [
                'transaction' => $transaction->load('transactionable'),
                'enrollment'  => $enrollment,
            ];
        });
    }

    /**
     * Buat transaksi untuk langganan
     */
    public function createSubscriptionTransaction(User $user, string $plan, string $paymentMethod): array
    {
        return DB::transaction(function () use ($user, $plan, $paymentMethod) {
            // Buat subscription
            $subscription = Subscription::create([
                'user_id'    => $user->id,
                'plan'       => $plan,
                'start_date' => now(),
                'end_date'   => now()->addYear(),
                'status'     => 'active',
            ]);

            // Buat transaksi
            $transaction = Transaction::create([
                'user_id'              => $user->id,
                'transaction_code'     => Transaction::generateTransactionCode(),
                'type'                 => 'subscription',
                'transactionable_id'   => $subscription->id,
                'transactionable_type' => Subscription::class,
                'amount'               => $this->subscriptionPrices[$plan],
                'payment_method'       => $paymentMethod,
                'status'               => 'pending',
                'expired_at'           => now()->addHours(24),
            ]);

            return [
                'transaction'  => $transaction->load('transactionable'),
                'subscription' => $subscription,
            ];
        });
    }

    /**
     * Buat transaksi untuk mentoring
     * 
     * @throws \Exception jika bukan member sesi
     */
    public function createMentoringTransaction(MentoringSession $session, User $user, string $paymentMethod): Transaction
    {
        // Verifikasi user adalah member sesi
        if ($session->member_id !== $user->id) {
            throw new \Exception('Anda tidak memiliki akses untuk membuat transaksi sesi ini');
        }

        return DB::transaction(function () use ($session, $user, $paymentMethod) {
            $transaction = Transaction::create([
                'user_id'              => $user->id,
                'transaction_code'     => Transaction::generateTransactionCode(),
                'type'                 => 'mentoring_session',
                'transactionable_id'   => $session->id,
                'transactionable_type' => MentoringSession::class,
                'amount'               => $this->mentoringPrices[$session->type] ?? 150000,
                'payment_method'       => $paymentMethod,
                'status'               => 'pending',
                'expired_at'           => now()->addHours(24),
            ]);

            return $transaction->load('transactionable');
        });
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadPaymentProof(Transaction $transaction, $file): Transaction
    {
        $path = $file->store('payment-proofs', 'public');
        $transaction->payment_proof = $path;
        $transaction->save();

        return $transaction;
    }

    /**
     * Konfirmasi pembayaran
     */
    public function confirmPayment(Transaction $transaction): Transaction
    {
        $transaction->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        // Update status model terkait jika perlu
        if ($transaction->transactionable_type === MentoringSession::class) {
            $transaction->transactionable->update(['status' => 'scheduled']);
        }

        return $transaction->fresh();
    }

    /**
     * Request refund
     */
    public function requestRefund(Transaction $transaction, string $reason): Transaction
    {
        $transaction->update([
            'status'          => 'refunded',
            'payment_details' => array_merge(
                $transaction->payment_details ?? [],
                [
                    'refund_reason' => $reason,
                    'refunded_at'   => now(),
                ]
            ),
        ]);

        // Update status model terkait jika perlu
        if ($transaction->transactionable_type === MentoringSession::class) {
            $transaction->transactionable->update(['status' => 'refunded']);
        }

        return $transaction->fresh();
    }

    /**
     * Ambil statistik transaksi
     */
    public function getStatistics(): array
    {
        return [
            'total_transactions'    => Transaction::count(),
            'total_revenue'         => Transaction::where('status', 'paid')->sum('amount'),
            'pending_transactions'  => Transaction::where('status', 'pending')->count(),
            'paid_transactions'     => Transaction::where('status', 'paid')->count(),
            'failed_transactions'   => Transaction::where('status', 'failed')->count(),
            'refunded_transactions' => Transaction::where('status', 'refunded')->count(),
            'by_type'               => Transaction::select('type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->where('status', 'paid')
                ->groupBy('type')
                ->get(),
            'by_payment_method'     => Transaction::select('payment_method', DB::raw('count(*) as count'))
                ->where('status', 'paid')
                ->groupBy('payment_method')
                ->get(),
        ];
    }
}
