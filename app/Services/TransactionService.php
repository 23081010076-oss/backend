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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

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
        $query = Transaction::with([
                'user', 
                'transactionable' => function ($morphTo) {
                    // Eager load course relationship for Enrollment
                    $morphTo->morphWith([
                        Enrollment::class => ['course'],
                        MentoringSession::class => ['mentor'],
                    ]);
                }
            ])
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
     * Ambil semua transaksi (Admin only)
     */
    public function getAllTransactions(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Transaction::with([
                'user', 
                'transactionable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Enrollment::class => ['course'],
                        MentoringSession::class => ['mentor'],
                    ]);
                }
            ]);

        // Filter berdasarkan tipe
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter berdasarkan payment method
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Ambil transaksi yang perlu diverifikasi (Admin only)
     * Transaksi dengan status pending dan sudah upload bukti pembayaran
     */
    public function getPendingVerificationTransactions(int $perPage = 20): LengthAwarePaginator
    {
        return Transaction::with([
                'user', 
                'transactionable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Enrollment::class => ['course'],
                        MentoringSession::class => ['mentor'],
                    ]);
                }
            ])
            ->where('status', 'pending')
            ->whereNotNull('payment_proof') // Sudah upload bukti
            ->orderBy('created_at', 'asc') // Yang paling lama duluan
            ->paginate($perPage);
    }

    /**
     * Buat transaksi untuk kursus
     * Note: Enrollment check sudah dilakukan di EnrollmentService
     */
    public function createCourseTransaction(Course $course, User $user, string $paymentMethod): Transaction
    {
        return DB::transaction(function () use ($course, $user, $paymentMethod) {
            // Buat transaksi (link ke Course dulu karena belum enrolled)
            $transaction = Transaction::create([
                'user_id'              => $user->id,
                'transaction_code'     => Transaction::generateTransactionCode(),
                'type'                 => 'course_enrollment',
                'transactionable_id'   => $course->id,
                'transactionable_type' => Course::class,
                'amount'               => $course->price,
                'payment_method'       => $paymentMethod,
                'status'               => 'pending',
                'expired_at'           => now()->addHours(24),
            ]);

            // Generate QR code jika payment method adalah QRIS
            if ($paymentMethod === 'qris') {
                $this->generateQRCode($transaction);
            }

            return $transaction->fresh()->load('transactionable');
        });
    }

    /**
     * Buat transaksi untuk langganan
     * Note: Subscription sudah dibuat di SubscriptionService
     */
    public function createSubscriptionTransaction(User $user, string $plan, string $paymentMethod): array
    {
        return DB::transaction(function () use ($user, $plan, $paymentMethod) {
            // Cari subscription pending terakhir user
            $subscription = Subscription::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$subscription) {
                throw new \Exception('Subscription not found');
            }

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

            // Generate QR code jika payment method adalah QRIS
            if ($paymentMethod === 'qris') {
                $this->generateQRCode($transaction);
            }

            return [
                'transaction' => $transaction->fresh()->load('transactionable'),
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

            // Generate QR code jika payment method adalah QRIS
            if ($paymentMethod === 'qris') {
                $this->generateQRCode($transaction);
            }

            return $transaction->fresh()->load('transactionable');
        });
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadPaymentProof(Transaction $transaction, $file): Transaction
    {
        // Validasi: Hanya untuk metode pembayaran manual
        if (!in_array($transaction->payment_method, ['manual', 'bank_transfer'])) {
            throw new \Exception('Upload bukti pembayaran hanya untuk pembayaran manual');
        }

        // Validasi: Status harus pending
        if (!in_array($transaction->status, ['pending'])) {
            throw new \Exception('Tidak dapat upload bukti pembayaran untuk transaksi dengan status: ' . $transaction->status);
        }

        // Hapus file lama jika ada
        if ($transaction->payment_proof && Storage::disk('public')->exists($transaction->payment_proof)) {
            Storage::disk('public')->delete($transaction->payment_proof);
        }

        // Upload file baru
        $path = $file->store('payment-proofs', 'public');
        
        // Update transaksi
        $transaction->update([
            'payment_proof' => $path,
            // Status tetap pending, nanti diubah admin saat konfirmasi
        ]);

        return $transaction;
    }

    /**
     * Konfirmasi pembayaran
     */
    public function confirmPayment(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            // Handle Course Enrollment
            // FLOW: User enroll → transaction created → upload bukti → admin confirm → enrollment created
            if ($transaction->transactionable_type === Course::class) {
                $courseId = $transaction->transactionable_id;
                
                // ✅ ENROLLMENT DIBUAT DI SINI - Setelah admin konfirmasi pembayaran
                $enrollment = Enrollment::create([
                    'user_id'   => $transaction->user_id,
                    'course_id' => $courseId,
                    'progress'  => 0,
                    'completed' => false,
                ]);

                // Update transaction to point to enrollment (good for tracking history)
                $transaction->update([
                    'transactionable_id'   => $enrollment->id,
                    'transactionable_type' => Enrollment::class,
                ]);
                
                Log::info('Course enrollment created after payment confirmation', [
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $transaction->user_id,
                    'course_id' => $courseId,
                    'transaction_id' => $transaction->id,
                ]);
            }
            // Handle Subscription Activation
            // FLOW: User subscribe → upload bukti → admin confirm → status active → auto-enroll
            elseif ($transaction->transactionable_type === Subscription::class) {
                $subscription = $transaction->transactionable;
                $subscription->update(['status' => 'active']);
                
                // ✅ AUTO-ENROLL: Hanya terjadi SETELAH admin konfirmasi pembayaran
                // Jika premium subscription, daftarkan user ke semua kursus premium
                if ($subscription->plan === 'premium') {
                    $this->autoEnrollPremiumCourses($transaction->user_id);
                }
                // Jika regular subscription, daftarkan user ke semua kursus regular
                elseif ($subscription->plan === 'regular') {
                    $this->autoEnrollRegularCourses($transaction->user_id);
                }
            }
            // Handle Mentoring Session
            elseif ($transaction->transactionable_type === MentoringSession::class) {
                $transaction->transactionable->update(['status' => 'scheduled']);
            }

            return $transaction->fresh();
        });
    }

    /**
     * Generate QR code untuk pembayaran QRIS
     * Generate langsung di backend dalam format SVG (tidak perlu Imagick/GD)
     */
    private function generateQRCode(Transaction $transaction): void
    {
        // Format QRIS string (simplified - dalam produksi gunakan format QRIS resmi)
        // Format: ID.MERCHANTCODE.TRANSACTION_CODE.AMOUNT
        $qrString = sprintf(
            "ID.MERCHANT.%s.%s",
            $transaction->transaction_code,
            number_format($transaction->amount, 0, '', '')
        );
        
        // Generate QR code dalam format SVG (tidak butuh extension tambahan)
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrString);
        
        // Simpan QR code SVG ke storage
        $qrCodePath = 'qr-codes/' . $transaction->transaction_code . '.svg';
        Storage::disk('public')->put($qrCodePath, $qrCodeSvg);
        
        // Update transaction dengan QR info
        $transaction->update([
            'qr_code_url' => $qrCodePath,
            'qr_string' => $qrString,
        ]);
    }

    /**
     * Auto-enroll user ke semua kursus premium saat berlangganan premium
     */
    private function autoEnrollPremiumCourses(int $userId): void
    {
        // Ambil semua kursus premium
        $premiumCourses = Course::where('access_type', 'premium')->get();
        
        foreach ($premiumCourses as $course) {
            // Cek apakah user sudah enrolled
            $alreadyEnrolled = Enrollment::where('user_id', $userId)
                ->where('course_id', $course->id)
                ->exists();
            
            // Jika belum enrolled, buat enrollment baru
            if (!$alreadyEnrolled) {
                Enrollment::create([
                    'user_id'   => $userId,
                    'course_id' => $course->id,
                    'progress'  => 0,
                    'completed' => false,
                ]);
            }
        }
        
        \Log::info('Auto-enrolled user to premium courses', [
            'user_id' => $userId,
            'total_courses' => $premiumCourses->count(),
        ]);
    }

    /**
     * Auto-enroll user ke semua kursus regular (non-premium) saat berlangganan regular
     */
    private function autoEnrollRegularCourses(int $userId): void
    {
        // Ambil semua kursus regular (tidak premium)
        $regularCourses = Course::where('access_type', 'regular')
            ->orWhere('access_type', 'free')
            ->get();
        
        foreach ($regularCourses as $course) {
            // Cek apakah user sudah enrolled
            $alreadyEnrolled = Enrollment::where('user_id', $userId)
                ->where('course_id', $course->id)
                ->exists();
            
            // Jika belum enrolled, buat enrollment baru
            if (!$alreadyEnrolled) {
                Enrollment::create([
                    'user_id'   => $userId,
                    'course_id' => $course->id,
                    'progress'  => 0,
                    'completed' => false,
                ]);
            }
        }
        
        \Log::info('Auto-enrolled user to regular courses', [
            'user_id' => $userId,
            'total_courses' => $regularCourses->count(),
        ]);
    }

    /**
     * Get payment instructions
     */
    private function getPaymentInstructions(): array
    {
        return [
            'bank_name'      => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'PT Edukasi Masa Depan',
            'instructions'   => [
                'Transfer sesuai nominal yang tertera.',
                'Simpan bukti transfer.',
                'Upload bukti transfer melalui menu "Riwayat Transaksi".',
                'Tunggu verifikasi admin (maksimal 1x24 jam).'
            ]
        ];
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
