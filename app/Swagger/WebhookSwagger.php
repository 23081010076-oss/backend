<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/midtrans/webhook",
 *     summary="Midtrans Webhook Notification",
 *     description="Handle payment notification from Midtrans payment gateway",
 *     operationId="midtransWebhook",
 *     tags={"Webhooks"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="transaction_time", type="string", example="2024-01-15 10:30:00"),
 *             @OA\Property(property="transaction_status", type="string", example="settlement"),
 *             @OA\Property(property="transaction_id", type="string", example="abc123-def456"),
 *             @OA\Property(property="status_message", type="string", example="midtrans payment notification"),
 *             @OA\Property(property="status_code", type="string", example="200"),
 *             @OA\Property(property="signature_key", type="string", example="signature_hash_here"),
 *             @OA\Property(property="order_id", type="string", example="TRX-12345678"),
 *             @OA\Property(property="merchant_id", type="string", example="G123456789"),
 *             @OA\Property(property="gross_amount", type="string", example="150000.00"),
 *             @OA\Property(property="fraud_status", type="string", example="accept"),
 *             @OA\Property(property="currency", type="string", example="IDR"),
 *             @OA\Property(property="payment_type", type="string", example="qris")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Webhook processed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Payment notification processed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid signature or request"
 *     )
 * )
 */
class WebhookSwagger {}
