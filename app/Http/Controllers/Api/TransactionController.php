<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Course;
use App\Models\MentoringSession;
use App\Services\TransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Import Request Classes
use App\Http\Requests\Transaction\CreateCourseTransactionRequest;
use App\Http\Requests\Transaction\CreateSubscriptionTransactionRequest;
use App\Http\Requests\Transaction\UploadPaymentProofRequest;

/**
 * ==========================================================================
 * TRANSACTION CONTROLLER (Controller untuk Transaksi)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola transaksi pembayaran (kursus, langganan, mentoring).
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/TransactionService.php
 * - Policy      : Authorization  → app/Policies/TransactionPolicy.php
 * - Request     : Validation     → app/Http/Requests/Transaction/
 */
class TransactionController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected TransactionService $transactionService;

    /**
     * Constructor - Inject service
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan daftar transaksi user
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = $this->transactionService->getUserTransactions(
            $request->user()->id,
            $request->all()
        );

        return $this->paginatedResponse($transactions, 'Daftar transaksi berhasil diambil');
    }

    /**
     * Tampilkan detail transaksi
     */
    public function show(int $id): JsonResponse
    {
        $transaction = Transaction::with(['user', 'transactionable'])->findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('view', $transaction);

        return $this->successResponse($transaction, 'Detail transaksi berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Course Transaction Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Buat transaksi untuk pendaftaran kursus
     * 
     * Validasi di: app/Http/Requests/Transaction/CreateCourseTransactionRequest.php
     */
    public function createCourseTransaction(CreateCourseTransactionRequest $request, int $courseId): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', Transaction::class);

        try {
            $course = Course::findOrFail($courseId);
            $validated = $request->validated();
            
            $result = $this->transactionService->createCourseTransaction(
                $course,
                $request->user(),
                $validated['payment_method']
            );

            return $this->createdResponse($result, 'Transaksi berhasil dibuat');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Subscription Transaction Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Buat transaksi untuk langganan
     * 
     * Validasi di: app/Http/Requests/Transaction/CreateSubscriptionTransactionRequest.php
     */
    public function createSubscriptionTransaction(CreateSubscriptionTransactionRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', Transaction::class);

        try {
            $validated = $request->validated();
            
            $result = $this->transactionService->createSubscriptionTransaction(
                $request->user(),
                $validated['plan'],
                $validated['payment_method']
            );

            return $this->createdResponse($result, 'Transaksi langganan berhasil dibuat');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Mentoring Transaction Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Buat transaksi untuk sesi mentoring
     */
    public function createMentoringTransaction(Request $request, int $sessionId): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:qris,bank_transfer,virtual_account,credit_card,manual',
        ], [
            'payment_method.required' => 'Metode pembayaran harus diisi',
            'payment_method.in'       => 'Metode pembayaran tidak valid',
        ]);

        // Cek akses dengan Policy
        $this->authorize('create', Transaction::class);

        try {
            $session = MentoringSession::findOrFail($sessionId);
            
            $result = $this->transactionService->createMentoringTransaction(
                $session,
                $request->user(),
                $validated['payment_method']
            );

            return $this->createdResponse($result, 'Transaksi mentoring berhasil dibuat');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Management Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Upload bukti pembayaran untuk pembayaran manual
     * 
     * Validasi di: app/Http/Requests/Transaction/UploadPaymentProofRequest.php
     */
    public function uploadPaymentProof(UploadPaymentProofRequest $request, int $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('uploadProof', $transaction);

        $transaction = $this->transactionService->uploadPaymentProof(
            $transaction,
            $request->file('payment_proof')
        );

        return $this->successResponse($transaction, 'Bukti pembayaran berhasil diupload');
    }

    /**
     * Konfirmasi pembayaran (Admin only)
     */
    public function confirmPayment(Request $request, int $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('confirmPayment', $transaction);

        $transaction = $this->transactionService->confirmPayment($transaction);

        return $this->successResponse($transaction, 'Pembayaran berhasil dikonfirmasi');
    }

    /**
     * Minta pengembalian dana
     */
    public function requestRefund(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ], [
            'reason.required' => 'Alasan harus diisi',
            'reason.string'   => 'Alasan harus berupa teks',
        ]);

        $transaction = Transaction::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('requestRefund', $transaction);

        $transaction = $this->transactionService->requestRefund(
            $transaction,
            $validated['reason']
        );

        return $this->successResponse($transaction, 'Pengembalian dana berhasil diajukan');
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics Methods (Admin only)
    |--------------------------------------------------------------------------
    */

    /**
     * Statistik transaksi
     */
    public function statistics(): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('viewStatistics', Transaction::class);

        $stats = $this->transactionService->getStatistics();

        return $this->successResponse($stats, 'Statistik transaksi berhasil diambil');
    }
}
