<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class MidtransService
{
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
     * @return object Snap token response
     */
    public function createTransaction(array $params)
    {
        try {
            $snapToken = Snap::getSnapToken($params);
            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => config('services.midtrans.is_production')
                    ? "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}"
                    : "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}"
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction status
     *
     * @param string $orderId Transaction order ID
     * @return object Transaction status
     */
    public function getTransactionStatus(string $orderId)
    {
        try {
            $status = MidtransTransaction::status($orderId);
            return [
                'success' => true,
                'data' => $status
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify notification signature from Midtrans webhook
     *
     * @param array $notification Notification data
     * @return bool Is signature valid
     */
    public function verifySignature(array $notification): bool
    {
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        $serverKey = config('services.midtrans.server_key');
        $signatureKey = $notification['signature_key'] ?? '';

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return $signatureKey === $expectedSignature;
    }

    /**
     * Map Midtrans transaction status to our system status
     *
     * @param string $transactionStatus Midtrans transaction status
     * @param string $fraudStatus Midtrans fraud status
     * @return string Our system status
     */
    public function mapTransactionStatus(string $transactionStatus, string $fraudStatus = null): string
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
