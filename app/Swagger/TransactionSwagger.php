<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/subscription-status",
 *     summary="Check subscription status",
 *     description="Check user's current subscription status and available upgrade options. Returns information about active subscription and whether user can upgrade. Response will indicate if user has active subscription (with plan details) or no subscription (can subscribe to any plan).",
 *     operationId="checkSubscriptionStatus",
 *     tags={"Subscriptions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Subscription status retrieved successfully. has_active_subscription=true means user has active plan, false means user can subscribe.",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     description="User has active subscription",
 *                     @OA\Property(property="sukses", type="boolean", example=true),
 *                     @OA\Property(property="pesan", type="string", example="Subscription status retrieved successfully"),
 *                     @OA\Property(property="data", type="object",
 *                         @OA\Property(property="has_active_subscription", type="boolean", example=true),
 *                         @OA\Property(property="current_plan", type="string", example="premium"),
 *                         @OA\Property(property="subscription", type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="plan", type="string", example="premium"),
 *                             @OA\Property(property="status", type="string", example="active"),
 *                             @OA\Property(property="start_date", type="string", format="date", example="2025-11-01"),
 *                             @OA\Property(property="end_date", type="string", format="date", example="2026-11-01"),
 *                             @OA\Property(property="package_type", type="string", example="all_in_one")
 *                         ),
 *                         @OA\Property(property="can_upgrade", type="boolean", example=false, description="false jika sudah premium"),
 *                         @OA\Property(property="available_plans", type="array", @OA\Items(type="string"), example={}, description="Empty array jika sudah premium"),
 *                         @OA\Property(property="message", type="string", example="Anda sudah berlangganan paket Premium (paket tertinggi).")
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     description="User has no active subscription",
 *                     @OA\Property(property="sukses", type="boolean", example=true),
 *                     @OA\Property(property="data", type="object",
 *                         @OA\Property(property="has_active_subscription", type="boolean", example=false),
 *                         @OA\Property(property="current_plan", type="string", nullable=true, example=null),
 *                         @OA\Property(property="can_upgrade", type="boolean", example=true),
 *                         @OA\Property(property="available_plans", type="array", @OA\Items(type="string"), example={"regular", "premium"}),
 *                         @OA\Property(property="message", type="string", example="Tidak ada paket aktif. Silakan pilih paket langganan.")
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 *
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
 *     description="Subscribe to a chosen plan with payment method selection (manual, bank_transfer, atau qris). Jika qris, akan generate QR code otomatis.",
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
 *             @OA\Property(property="start_date", type="string", format="date", example="2025-12-03"),
 *             @OA\Property(
 *                 property="payment_method",
 *                 type="string",
 *                 enum={"manual", "bank_transfer", "qris"},
 *                 example="qris",
 *                 description="Metode pembayaran: manual (transfer manual), bank_transfer (via bank), atau qris (scan QR code)"
 *             )
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
 *                 @OA\Property(property="status", type="string", example="active"),
 *                 @OA\Property(property="transaction", type="object",
 *                     @OA\Property(property="id", type="integer", example=123),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX-20251224-XYZ789"),
 *                     @OA\Property(property="amount", type="number", example=500000),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="payment_method", type="string", example="qris"),
 *                     @OA\Property(property="qr_code_url", type="string", nullable=true, example="qr-codes/TRX-20251224-XYZ789.svg", description="Path QR code (hanya untuk qris)"),
 *                     @OA\Property(property="qr_string", type="string", nullable=true, example="ID.MERCHANT.TRX-20251224-XYZ789.500000", description="Data QR code (hanya untuk qris)")
 *                 )
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
 *             @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, example="manual")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription upgrade initiated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Subscription upgrade initiated"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="transaction", type="object")
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
 *     description="Retrieve all transactions for the authenticated user. Returns item_name and item_details based on transaction type and status.",
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
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Daftar transaksi berhasil diambil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=16),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX202512174488C7"),
 *                     @OA\Property(property="type", type="string", example="course_enrollment"),
 *                     @OA\Property(property="type_label", type="string", example="Pendaftaran Kursus"),
 *                     @OA\Property(property="amount", type="string", example="900000.00"),
 *                     @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                     @OA\Property(property="status", type="string", example="paid"),
 *                     @OA\Property(property="status_label", type="string", example="Lunas"),
 *                     @OA\Property(property="payment_proof", type="string", nullable=true, example="http://127.0.0.1:8000/storage/payment-proofs/proof.jpg"),
 *                     @OA\Property(property="payment_details", type="object", nullable=true),
 *                     @OA\Property(property="paid_at", type="string", format="datetime", nullable=true, example="2025-12-17T12:28:16.000000Z"),
 *                     @OA\Property(property="expired_at", type="string", format="datetime", example="2025-12-18T12:27:32.000000Z"),
 *                     @OA\Property(property="created_at", type="string", format="datetime", example="2025-12-17T12:27:32.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2025-12-17T12:28:16.000000Z"),
 *                     @OA\Property(property="user", type="object",
 *                         @OA\Property(property="id", type="integer", example=3),
 *                         @OA\Property(property="name", type="string", example="Test Admin"),
 *                         @OA\Property(property="email", type="string", example="test.admin@learningplatform.com")
 *                     ),
 *                     @OA\Property(property="item_name", type="string", example="Database Design and SQL", description="Name of the purchased item"),
 *                     @OA\Property(property="item_details", type="object", description="Details vary by transaction type. For paid course: enrollment_id, course_id, title, image, instructor, level, duration, progress, completed. For pending course: id, title, image, instructor, level, duration. For subscription: id, plan, package_type, duration, start_date, end_date, status. For mentoring: id, session_id, type, schedule, meeting_link, status, mentor.",
 *                         @OA\Property(property="enrollment_id", type="integer", example=10, description="Only for paid course transactions"),
 *                         @OA\Property(property="course_id", type="integer", example=7),
 *                         @OA\Property(property="title", type="string", example="Database Design and SQL"),
 *                         @OA\Property(property="image", type="string", example="https://images.unsplash.com/photo-example?w=800"),
 *                         @OA\Property(property="instructor", type="string", example="Agus Prasetyo, S.T"),
 *                         @OA\Property(property="level", type="string", example="intermediate"),
 *                         @OA\Property(property="duration", type="string", example="6 weeks"),
 *                         @OA\Property(property="progress", type="integer", example=0, description="Only for paid course transactions"),
 *                         @OA\Property(property="completed", type="boolean", example=false, description="Only for paid course transactions")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="total", type="integer", example=2),
 *                 @OA\Property(property="per_halaman", type="integer", example=20),
 *                 @OA\Property(property="halaman_sekarang", type="integer", example=1),
 *                 @OA\Property(property="halaman_terakhir", type="integer", example=1),
 *                 @OA\Property(property="dari", type="integer", example=1),
 *                 @OA\Property(property="sampai", type="integer", example=2)
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions/{id}",
 *     summary="Get transaction details",
 *     description="Retrieve details of a single transaction with item information",
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
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Detail transaksi berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=16),
 *                 @OA\Property(property="transaction_code", type="string", example="TRX202512174488C7"),
 *                 @OA\Property(property="type", type="string", example="course_enrollment"),
 *                 @OA\Property(property="type_label", type="string", example="Pendaftaran Kursus"),
 *                 @OA\Property(property="amount", type="string", example="900000.00"),
 *                 @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                 @OA\Property(property="status", type="string", example="paid"),
 *                 @OA\Property(property="status_label", type="string", example="Lunas"),
 *                 @OA\Property(property="payment_proof", type="string", nullable=true),
 *                 @OA\Property(property="payment_details", type="object", nullable=true),
 *                 @OA\Property(property="paid_at", type="string", format="datetime", nullable=true),
 *                 @OA\Property(property="expired_at", type="string", format="datetime"),
 *                 @OA\Property(property="created_at", type="string", format="datetime"),
 *                 @OA\Property(property="updated_at", type="string", format="datetime"),
 *                 @OA\Property(property="user", type="object",
 *                     @OA\Property(property="id", type="integer", example=3),
 *                     @OA\Property(property="name", type="string", example="Test User"),
 *                     @OA\Property(property="email", type="string", example="user@example.com")
 *                 ),
 *                 @OA\Property(property="item_name", type="string", example="Database Design and SQL"),
 *                 @OA\Property(property="item_details", type="object")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/courses/{courseId}",
 *     summary="Create course transaction",
 *     description="Create payment transaction for course enrollment. Returns transaction details and payment instructions.",
 *     operationId="createCourseTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="courseId",
 *         in="path",
 *         required=true,
 *         description="ID of the course to enroll in",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"payment_method"},
 *             @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, example="bank_transfer", description="Payment method to use")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Course transaction created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Transaksi berhasil dibuat"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="transaction", type="object",
 *                     @OA\Property(property="id", type="integer", example=17),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX20251217ABC123"),
 *                     @OA\Property(property="type", type="string", example="course_enrollment"),
 *                     @OA\Property(property="type_label", type="string", example="Pendaftaran Kursus"),
 *                     @OA\Property(property="amount", type="string", example="900000.00"),
 *                     @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="status_label", type="string", example="Menunggu Pembayaran"),
 *                     @OA\Property(property="expired_at", type="string", format="datetime"),
 *                     @OA\Property(property="item_name", type="string", example="Database Design and SQL"),
 *                     @OA\Property(property="item_details", type="object",
 *                         @OA\Property(property="id", type="integer", example=7),
 *                         @OA\Property(property="title", type="string", example="Database Design and SQL"),
 *                         @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
 *                         @OA\Property(property="instructor", type="string", example="John Doe"),
 *                         @OA\Property(property="level", type="string", example="intermediate"),
 *                         @OA\Property(property="duration", type="string", example="6 weeks")
 *                     )
 *                 ),
 *                 @OA\Property(property="instructions", type="object",
 *                     @OA\Property(property="bank_name", type="string", example="BCA"),
 *                     @OA\Property(property="account_number", type="string", example="1234567890"),
 *                     @OA\Property(property="account_holder", type="string", example="PT Edukasi Masa Depan"),
 *                     @OA\Property(property="instructions", type="array", @OA\Items(type="string"), example={"Transfer sesuai nominal yang tertera.", "Simpan bukti transfer.", "Upload bukti transfer melalui menu Riwayat Transaksi.", "Tunggu verifikasi admin (maksimal 1x24 jam)."})
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error or already enrolled",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=false),
 *             @OA\Property(property="pesan", type="string", example="Anda sudah terdaftar di kursus ini")
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
 *             required={"plan", "payment_method"},
 *             @OA\Property(property="plan", type="string", enum={"basic", "premium", "pro"}, example="premium"),
 *             @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, example="bank_transfer"),
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
 *             @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, example="manual")
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
 *     description="Upload bukti pembayaran untuk verifikasi manual oleh admin. Setelah upload, menunggu admin konfirmasi via POST /api/transactions/{id}/confirm. File yang diupload: JPG, PNG, PDF (max 2MB).",
 *     operationId="uploadPaymentProof",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID Transaction yang akan diupload bukti pembayarannya",
 *         required=true,
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"payment_proof"},
 *                 @OA\Property(
 *                     property="payment_proof",
 *                     type="string",
 *                     format="binary",
 *                     description="File bukti pembayaran (JPG, PNG, PDF - max 2MB)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment proof uploaded successfully - Waiting for admin confirmation",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Bukti pembayaran berhasil diupload"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=123),
 *                 @OA\Property(property="transaction_code", type="string", example="TRX-20260101-ABC123"),
 *                 @OA\Property(property="status", type="string", example="pending", description="Status masih pending, menunggu konfirmasi admin"),
 *                 @OA\Property(property="payment_proof", type="string", example="payment-proofs/user_3/proof_123.jpg", description="Path file bukti pembayaran yang diupload")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - User can only upload proof for their own transaction"
 *     ),
 *     @OA\Response(response=404, description="Transaction not found"),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error - Invalid file type or size"
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
 *     description="Konfirmasi pembayaran manual oleh admin. PENTING: Endpoint ini akan membuat enrollment untuk course atau mengaktifkan subscription setelah pembayaran dikonfirmasi. Flow: 1) Course - Membuat enrollment baru setelah konfirmasi, 2) Subscription - Mengaktifkan subscription dan auto-enroll ke semua kursus sesuai plan.",
 *     operationId="confirmPayment",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID Transaction yang akan dikonfirmasi",
 *         required=true,
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment confirmed successfully - Enrollment created (for course) or Subscription activated (for subscription)",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Pembayaran berhasil dikonfirmasi"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=123),
 *                 @OA\Property(property="transaction_code", type="string", example="TRX-20260101-ABC123"),
 *                 @OA\Property(property="type", type="string", example="course_enrollment", description="Type: course_enrollment, subscription, mentoring_session"),
 *                 @OA\Property(property="status", type="string", example="paid"),
 *                 @OA\Property(property="paid_at", type="string", format="date-time", example="2026-01-01T10:30:00Z"),
 *                 @OA\Property(property="transactionable", type="object", description="Enrollment (for course) or Subscription (for subscription)",
 *                     @OA\Property(property="id", type="integer", example=45),
 *                     @OA\Property(property="user_id", type="integer", example=3),
 *                     @OA\Property(property="course_id", type="integer", example=5, description="Only for course enrollment"),
 *                     @OA\Property(property="plan", type="string", example="premium", description="Only for subscription"),
 *                     @OA\Property(property="status", type="string", example="active", description="Only for subscription")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin only",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=false),
 *             @OA\Property(property="pesan", type="string", example="Unauthorized - Admin access required")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Transaction not found")
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
