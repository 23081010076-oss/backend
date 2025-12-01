<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\MentoringSession;
use App\Services\MidtransService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class MidtransWebhookController
 * 
 * Handles payment notifications from Midtrans payment gateway.
 * Processes webhooks for successful/failed payments.
 * 
 * @package App\Http\Controllers\Api
 */
class MidtransWebhookController extends Controller
{
    use ApiResponse;

    /**
     * @var MidtransService
     */
    protected MidtransService $midtransService;

    /**
     * MidtransWebhookController constructor
     *
     * @param MidtransService $midtransService
     */
    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /*
    |--------------------------------------------------------------------------
    | Webhook Handler
    |--------------------------------------------------------------------------
    */

    /**
     * Handle Midtrans notification webhook
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleNotification(Request $request): JsonResponse
    {
        try {
            $notification = $request->all();

            Log::info('Midtrans Notification Received', $notification);

            // Verify signature
            if (!$this->midtransService->verifySignature($notification)) {
                Log::warning('Invalid Midtrans signature', $notification);
                return $this->forbiddenResponse('Invalid signature');
            }

            $transactionCode   = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus       = $notification['fraud_status'] ?? null;

            // Find transaction
            $transaction = Transaction::where('transaction_code', $transactionCode)->firstOrFail();

            // Map Midtrans status to our system status
            $newStatus = $this->midtransService->mapTransactionStatus($transactionStatus, $fraudStatus);

            DB::beginTransaction();
            try {
                // Update transaction status
                $transaction->status = $newStatus;
                $transaction->payment_details = array_merge(
                    $transaction->payment_details ?? [],
                    [
                        'midtrans_response' => $notification,
                        'updated_at'        => now()->toDateTimeString(),
                    ]
                );

                if ($newStatus === 'paid') {
                    $transaction->paid_at = now();
                }

                $transaction->save();

                // Execute post-payment actions if paid
                if ($newStatus === 'paid') {
                    $this->executePostPaymentActions($transaction);
                }

                DB::commit();

                Log::info('Transaction updated successfully', [
                    'transaction_code' => $transactionCode,
                    'new_status'       => $newStatus,
                ]);

                return $this->successResponse(null, 'Notification processed successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to update transaction', [
                    'transaction_code' => $transactionCode,
                    'error'            => $e->getMessage(),
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse('Webhook processing failed: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Post-Payment Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Execute actions after successful payment
     *
     * @param Transaction $transaction
     * @return void
     */
    protected function executePostPaymentActions(Transaction $transaction): void
    {
        $type = $transaction->transactionable_type;

        // Update related entities based on transaction type
        if ($type === Enrollment::class) {
            Log::info('Course enrollment payment confirmed', [
                'transaction_id' => $transaction->id,
            ]);

        } elseif ($type === Subscription::class) {
            $transaction->transactionable->update(['status' => 'active']);
            Log::info('Subscription activated', [
                'subscription_id' => $transaction->transactionable_id,
            ]);

        } elseif ($type === MentoringSession::class) {
            $transaction->transactionable->update(['status' => 'scheduled']);
            Log::info('Mentoring session confirmed', [
                'session_id' => $transaction->transactionable_id,
            ]);
        }
    }
}
