<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/subscriptions",
 *     summary="Get my subscriptions",
 *     description="Get list of user's subscriptions",
 *     operationId="getMySubscriptions",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Subscriptions retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="plan", type="string", example="premium"),
 *                     @OA\Property(property="package_type", type="string", example="all_in_one"),
 *                     @OA\Property(property="duration", type="integer", example=3),
 *                     @OA\Property(property="start_date", type="string", format="date"),
 *                     @OA\Property(property="end_date", type="string", format="date"),
 *                     @OA\Property(property="status", type="string", example="active")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/subscriptions",
 *     summary="Create subscription",
 *     description="Subscribe to a plan",
 *     operationId="createSubscription",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"plan","package_type","duration"},
 *             @OA\Property(property="plan", type="string", enum={"free", "premium", "pro"}, example="premium"),
 *             @OA\Property(property="package_type", type="string", enum={"single_course", "all_in_one"}, example="all_in_one"),
 *             @OA\Property(property="duration", type="integer", example=3, description="Duration in months"),
 *             @OA\Property(property="courses_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Subscription created"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions",
 *     summary="Get my transactions",
 *     description="Get list of user's transactions",
 *     operationId="getMyTransactions",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filter by status",
 *         required=false,
 *         @OA\Schema(type="string", enum={"pending", "paid", "failed", "expired", "refunded"})
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Filter by type",
 *         required=false,
 *         @OA\Schema(type="string", enum={"course_enrollment", "subscription", "mentoring_session"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transactions retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX-12345678"),
 *                     @OA\Property(property="type", type="string", example="course_enrollment"),
 *                     @OA\Property(property="amount", type="number", format="float", example=1500000),
 *                     @OA\Property(property="payment_method", type="string", example="qris"),
 *                     @OA\Property(property="status", type="string", example="paid")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions",
 *     summary="Create transaction",
 *     description="Create a new payment transaction",
 *     operationId="createTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"type","transactionable_type","transactionable_id","amount","payment_method"},
 *             @OA\Property(property="type", type="string", enum={"course_enrollment", "subscription", "mentoring_session"}),
 *             @OA\Property(property="transactionable_type", type="string", example="App\\Models\\Course"),
 *             @OA\Property(property="transactionable_id", type="integer", example=1),
 *             @OA\Property(property="amount", type="number", format="float", example=1500000),
 *             @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "virtual_account", "credit_card"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Transaction created"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions/{id}",
 *     summary="Get transaction detail",
 *     description="Get transaction detail",
 *     operationId="getTransactionById",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction detail retrieved"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/midtrans/callback",
 *     summary="Midtrans webhook callback",
 *     description="Handle Midtrans payment notification callback",
 *     operationId="midtransCallback",
 *     tags={"Transactions"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="transaction_time", type="string"),
 *             @OA\Property(property="transaction_status", type="string"),
 *             @OA\Property(property="transaction_id", type="string"),
 *             @OA\Property(property="status_message", type="string"),
 *             @OA\Property(property="status_code", type="string"),
 *             @OA\Property(property="signature_key", type="string"),
 *             @OA\Property(property="order_id", type="string"),
 *             @OA\Property(property="merchant_id", type="string"),
 *             @OA\Property(property="gross_amount", type="string"),
 *             @OA\Property(property="fraud_status", type="string"),
 *             @OA\Property(property="currency", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Callback processed"
 *     )
 * )
 */
class TransactionSwagger {}
