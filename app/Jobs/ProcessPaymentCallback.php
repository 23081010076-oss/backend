<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika gagal
     */
    public int $tries = 5;

    /**
     * Timeout dalam detik
     */
    public int $timeout = 30;

    /**
     * Delay antar percobaan (dalam detik)
     */
    public int $backoff = 10;

    /**
     * Data callback dari payment gateway
     */
    public array $payload;

    /**
     * Buat instance job baru
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->onQueue('payments');
    }

    /**
     * Eksekusi job
     */
    public function handle(): void
    {
        try {
            $orderId     = $this->payload['order_id'] ?? null;
            $status      = $this->payload['transaction_status'] ?? null;
            $fraudStatus = $this->payload['fraud_status'] ?? null;

            if (!$orderId) {
                Log::warning('Payment callback missing order_id', $this->payload);
                return;
            }

            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning('Transaction not found for payment callback', [
                    'order_id' => $orderId,
                ]);
                return;
            }

            // Determine status berdasarkan Midtrans response
            $newStatus = $this->determineStatus($status, $fraudStatus);

            // Update transaction
            $transaction->update([
                'status'         => $newStatus,
                'payment_status' => $status,
                'paid_at'        => $newStatus === 'paid' ? now() : null,
            ]);

            Log::info('Payment callback processed', [
                'order_id'   => $orderId,
                'old_status' => $transaction->getOriginal('status'),
                'new_status' => $newStatus,
            ]);

            // Dispatch follow-up jobs jika payment sukses
            if ($newStatus === 'paid') {
                $this->handleSuccessfulPayment($transaction);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process payment callback', [
                'payload' => $this->payload,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Tentukan status berdasarkan response Midtrans
     */
    private function determineStatus(string $status, ?string $fraudStatus): string
    {
        if ($status === 'capture') {
            return $fraudStatus === 'accept' ? 'paid' : 'pending';
        }

        return match ($status) {
            'settlement' => 'paid',
            'pending'    => 'pending',
            'deny', 'cancel', 'expire' => 'cancelled',
            default      => 'pending',
        };
    }

    /**
     * Handle aksi setelah payment sukses
     */
    private function handleSuccessfulPayment(Transaction $transaction): void
    {
        // Kirim email konfirmasi pembayaran
        if ($transaction->user) {
            SendNotificationEmail::dispatch(
                $transaction->user,
                'Pembayaran Berhasil - Learning Platform',
                "Pembayaran Anda untuk pesanan #{$transaction->order_id} telah berhasil. Terima kasih!",
                'payment_success'
            );
        }

        Log::info('Successful payment follow-up completed', [
            'transaction_id' => $transaction->id,
        ]);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Payment callback job permanently failed', [
            'payload' => $this->payload,
            'error'   => $exception->getMessage(),
        ]);
    }
}
