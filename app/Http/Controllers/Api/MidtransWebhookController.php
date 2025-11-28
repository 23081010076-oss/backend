<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\MentoringSession;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle Midtrans notification webhook
     */
    public function handleNotification(Request $request)
    {
        try {
            $notification = $request->all();

            Log::info('Midtrans Notification Received', $notification);

            // Verify signature
            if (!$this->midtransService->verifySignature($notification)) {
                Log::warning('Invalid Midtrans signature', $notification);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $transactionCode = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

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
                        'updated_at' => now()->toDateTimeString()
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
                    'new_status' => $newStatus
                ]);

                return response()->json(['message' => 'Notification processed successfully']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to update transaction', [
                    'transaction_code' => $transactionCode,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute actions after successful payment
     */
    protected function executePostPaymentActions(Transaction $transaction)
    {
        $type = $transaction->transactionable_type;

        // Update related entities based on transaction type
        if ($type === Enrollment::class) {
            // Course enrollment is already created, just log
            Log::info('Course enrollment payment confirmed', ['transaction_id' => $transaction->id]);
        } elseif ($type === Subscription::class) {
            // Activate subscription
            $transaction->transactionable->update(['status' => 'active']);
            Log::info('Subscription activated', ['subscription_id' => $transaction->transactionable_id]);
        } elseif ($type === MentoringSession::class) {
            // Update mentoring session status to scheduled
            $transaction->transactionable->update(['status' => 'scheduled']);
            Log::info('Mentoring session confirmed', ['session_id' => $transaction->transactionable_id]);
        }
    }
}
