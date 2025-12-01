<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

/**
 * Class MidtransService
 * 
 * Handles all interactions with Midtrans payment gateway.
 * Provides methods for creating transactions, checking status, and verifying webhooks.
 * 
 * @package App\Services
 */
class MidtransService
{
    /**
     * Initialize Midtrans configuration
     */
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Create Snap transaction token
     *
     * @param array $params Transaction parameters
     * @return array Response with snap_token or error
     */
    public function createTransaction(array $params): array
    {
        try {
            $snapToken = Snap::getSnapToken($params);

            Log::info('Midtrans transaction created successfully', [
                'order_id' => $params['transaction_details']['order_id'] ?? 'unknown',
                'amount' => $params['transaction_details']['gross_amount'] ?? 0,
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => config('services.midtrans.is_production')
                    ? "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}"
                    : "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}"
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans transaction creation failed', [
                'order_id' => $params['transaction_details']['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction status from Midtrans
     *
     * @param string $orderId Transaction order ID
     * @return array Response with transaction status or error
     */
    public function getTransactionStatus(string $orderId): array
    {
        try {
            $status = MidtransTransaction::status($orderId);

            Log::info('Midtrans transaction status retrieved', [
                'order_id' => $orderId,
                'transaction_status' => $status->transaction_status ?? 'unknown',
            ]);

            return [
                'success' => true,
                'data' => $status
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Midtrans transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify notification signature from Midtrans webhook
     *
     * @param array $notification Notification data from webhook
     * @return bool True if signature is valid
     */
    public function verifySignature(array $notification): bool
    {
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        $serverKey = config('services.midtrans.server_key');
        $signatureKey = $notification['signature_key'] ?? '';

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $isValid = $signatureKey === $expectedSignature;

        if (!$isValid) {
            Log::warning('Midtrans webhook signature verification failed', [
                'order_id' => $orderId,
                'provided_signature' => $signatureKey,
                'expected_signature' => $expectedSignature,
            ]);
        } else {
            Log::info('Midtrans webhook signature verified', [
                'order_id' => $orderId,
            ]);
        }

        return $isValid;
    }

    /**
     * Map Midtrans transaction status to our system status
     *
     * @param string $transactionStatus Midtrans transaction status
     * @param string|null $fraudStatus Midtrans fraud status
     * @return string Our system status
     */
    public function mapTransactionStatus(string $transactionStatus, ?string $fraudStatus = null): string
    {
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                return 'paid';
            }
            return 'pending';
        }

        $statusMap = [
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny' => 'failed',
            'expire' => 'expired',
            'cancel' => 'failed',
        ];

        return $statusMap[$transactionStatus] ?? 'pending';
    }

    /**
     * Build transaction parameters for Midtrans
     *
     * @param string $orderId Unique order ID
     * @param int $amount Transaction amount
     * @param array $itemDetails Item details
     * @param array $customerDetails Customer details
     * @return array Transaction parameters
     */
    public function buildTransactionParams(
        string $orderId,
        int $amount,
        array $itemDetails,
        array $customerDetails
    ): array {
        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            'enabled_payments' => [
                'credit_card',
                'bca_va',
                'bni_va',
                'bri_va',
                'echannel', // Mandiri Bill
                'gopay',
                'shopeepay',
                'qris',
            ],
        ];
    }
}
