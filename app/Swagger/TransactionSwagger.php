<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/subscriptions",
 *     summary="Get my subscriptions",
 *     description="Retrieve all subscriptions for the authenticated user",
 *     operationId="getMySubscriptions",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Subscriptions retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="plan", type="string", example="premium"),
 *                     @OA\Property(property="package_type", type="string", example="all_in_one"),
 *                     @OA\Property(property="duration", type="integer", example=12),
 *                     @OA\Property(property="duration_unit", type="string", example="months"),
 *                     @OA\Property(property="start_date", type="string", format="date", example="2025-12-01"),
 *                     @OA\Property(property="end_date", type="string", format="date", example="2026-12-01"),
 *                     @OA\Property(property="status", type="string", example="active"),
 *                     @OA\Property(property="price", type="number", example=500000),
 *                     @OA\Property(property="auto_renew", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/subscriptions",
 *     summary="Create a subscription",
 *     description="Subscribe to a chosen plan",
 *     operationId="createSubscription",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"plan", "package_type", "duration", "duration_unit", "price"},
 *             @OA\Property(property="plan", type="string", enum={"basic", "premium", "pro"}, example="premium"),
 *             @OA\Property(property="package_type", type="string", enum={"single_course", "all_in_one"}, example="all_in_one"),
 *             @OA\Property(property="duration", type="integer", example=12),
 *             @OA\Property(property="duration_unit", type="string", enum={"days", "weeks", "months", "years"}, example="months"),
 *             @OA\Property(property="courses_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
 *             @OA\Property(property="price", type="number", example=500000),
 *             @OA\Property(property="auto_renew", type="boolean", example=false),
 *             @OA\Property(property="start_date", type="string", format="date", example="2025-12-03")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Subscription created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Subscription created successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="plan", type="string", example="premium"),
 *                 @OA\Property(property="status", type="string", example="active")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/subscriptions/{id}",
 *     summary="Get subscription details",
 *     description="Get details of a specific subscription",
 *     operationId="getSubscriptionById",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription details retrieved successfully"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/subscriptions/{id}",
 *     summary="Update subscription",
 *     description="Update subscription settings",
 *     operationId="updateSubscription",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="auto_renew", type="boolean", example=false)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription updated successfully"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/subscriptions/{id}/upgrade",
 *     summary="Upgrade subscription",
 *     description="Upgrade to a higher tier subscription",
 *     operationId="upgradeSubscription",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"new_plan", "payment_method"},
 *             @OA\Property(property="new_plan", type="string", enum={"basic", "premium", "pro"}, example="premium"),
 *             @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="qris")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription upgrade initiated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Subscription upgrade initiated"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="transaction", type="object"),
 *                 @OA\Property(property="snap_token", type="string"),
 *                 @OA\Property(property="redirect_url", type="string")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/subscriptions/{id}",
 *     summary="Cancel subscription",
 *     description="Cancel an active subscription",
 *     operationId="deleteSubscription",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription cancelled successfully"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions",
 *     summary="Get my transactions",
 *     description="Retrieve all transactions for the authenticated user",
 *     operationId="getMyTransactions",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filter by transaction status",
 *         @OA\Schema(type="string", enum={"pending", "paid", "failed", "expired", "refunded"})
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Filter by transaction type",
 *         @OA\Schema(type="string", enum={"course_enrollment", "subscription", "mentoring_session"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transactions retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=10),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX-20251203-001"),
 *                     @OA\Property(property="type", type="string", example="subscription"),
 *                     @OA\Property(property="amount", type="number", example=500000),
 *                     @OA\Property(property="payment_method", type="string", example="qris"),
 *                     @OA\Property(property="status", type="string", example="paid"),
 *                     @OA\Property(property="created_at", type="string", format="datetime"),
 *                     @OA\Property(property="transactionable_type", type="string", example="App\\Models\\Subscription"),
 *                     @OA\Property(property="transactionable_id", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions/{id}",
 *     summary="Get transaction details",
 *     description="Retrieve details of a single transaction",
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
 *         description="Transaction details retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="transaction_code", type="string", example="TRX-20251203-001"),
 *                 @OA\Property(property="type", type="string", example="subscription"),
 *                 @OA\Property(property="amount", type="number", example=500000),
 *                 @OA\Property(property="payment_method", type="string", example="qris"),
 *                 @OA\Property(property="status", type="string", example="paid"),
 *                 @OA\Property(property="midtrans_order_id", type="string", example="ORDER-123456"),
 *                 @OA\Property(property="snap_token", type="string", nullable=true),
 *                 @OA\Property(property="payment_url", type="string", nullable=true),
 *                 @OA\Property(property="expired_at", type="string", format="datetime"),
 *                 @OA\Property(property="paid_at", type="string", format="datetime", nullable=true),
 *                 @OA\Property(property="transactionable", type="object")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/courses/{courseId}",
 *     summary="Create course transaction",
 *     description="Create payment transaction for course enrollment",
 *     operationId="createCourseTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="courseId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"payment_method"},
 *             @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="qris")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Course transaction created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Transaction created successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="transaction", type="object",
 *                     @OA\Property(property="id", type="integer", example=15),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX-20251203-002"),
 *                     @OA\Property(property="amount", type="number", example=2500000)
 *                 ),
 *                 @OA\Property(property="snap_token", type="string", example="abc123def456"),
 *                 @OA\Property(property="redirect_url", type="string", example="https://app.midtrans.com/snap/v2/vtweb/abc123")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/subscriptions",
 *     summary="Create subscription transaction",
 *     description="Create payment transaction for subscription",
 *     operationId="createSubscriptionTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"plan", "package_type", "duration", "duration_unit", "payment_method"},
 *             @OA\Property(property="plan", type="string", enum={"basic", "premium", "pro"}, example="premium"),
 *             @OA\Property(property="package_type", type="string", enum={"single_course", "all_in_one"}, example="all_in_one"),
 *             @OA\Property(property="duration", type="integer", example=12),
 *             @OA\Property(property="duration_unit", type="string", enum={"days", "weeks", "months", "years"}, example="months"),
 *             @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="qris"),
 *             @OA\Property(property="courses_ids", type="array", @OA\Items(type="integer"), example={1, 2})
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Subscription transaction created successfully"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/mentoring-sessions/{sessionId}",
 *     summary="Create mentoring transaction",
 *     description="Create payment transaction for mentoring session",
 *     operationId="createMentoringTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"payment_method"},
 *             @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="qris")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Mentoring transaction created successfully"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/{id}/payment-proof",
 *     summary="Upload payment proof",
 *     description="Upload payment proof for manual verification",
 *     operationId="uploadPaymentProof",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"payment_proof"},
 *                 @OA\Property(property="payment_proof", type="string", format="binary", description="Payment proof image/document")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment proof uploaded successfully"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/{id}/refund",
 *     summary="Request refund",
 *     description="Submit refund request for a transaction",
 *     operationId="requestRefund",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reason"},
 *             @OA\Property(property="reason", type="string", example="Course tidak sesuai dengan ekspektasi")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Refund request submitted successfully"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/{id}/confirm",
 *     summary="Confirm payment (Admin only)",
 *     description="Manually confirm payment for a transaction",
 *     operationId="confirmPayment",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"paid", "failed"}, example="paid")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment confirmed successfully"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions/statistics",
 *     summary="Get transaction statistics (Admin only)",
 *     description="Get transaction statistics and analytics",
 *     operationId="getTransactionStatistics",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Statistics retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="total_revenue", type="number", example=50000000),
 *                 @OA\Property(property="total_transactions", type="integer", example=150),
 *                 @OA\Property(property="pending_transactions", type="integer", example=10),
 *                 @OA\Property(property="completed_transactions", type="integer", example=130),
 *                 @OA\Property(property="failed_transactions", type="integer", example=10),
 *                 @OA\Property(property="revenue_by_type", type="object",
 *                     @OA\Property(property="course", type="number", example=20000000),
 *                     @OA\Property(property="subscription", type="number", example=25000000),
 *                     @OA\Property(property="mentoring", type="number", example=5000000)
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class TransactionSwagger {}
