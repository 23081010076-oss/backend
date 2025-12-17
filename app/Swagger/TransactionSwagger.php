<?php

namespace App\Swagger;

/**
 * ==========================================================================
 * TRANSACTION SWAGGER DOCUMENTATION
 * ==========================================================================
 * 
 * Dokumentasi API untuk sistem transaksi pembayaran.
 * 
 * TIPE TRANSAKSI:
 * - course_enrollment : Pembelian kursus
 * - subscription      : Langganan paket
 * - mentoring_session : Sesi mentoring
 * 
 * ALUR TRANSAKSI:
 * 1. User buat transaksi → status: pending
 * 2. User upload bukti bayar
 * 3. Admin konfirmasi → status: paid
 * 4. User ter-enroll/langganan aktif
 */

/**
 * ==========================================================================
 * GET /api/transactions - Daftar Transaksi User
 * ==========================================================================
 * 
 * @OA\Get(
 *     path="/api/transactions",
 *     summary="Get my transactions",
 *     description="Retrieve all transactions for the authenticated user. Supports filtering by status and type.",
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
 *                     @OA\Property(property="id", type="integer", example=18),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX20251217DC9865"),
 *                     @OA\Property(property="type", type="string", example="course_enrollment"),
 *                     @OA\Property(property="type_label", type="string", example="Pendaftaran Kursus"),
 *                     @OA\Property(property="amount", type="string", example="3500000.00"),
 *                     @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                     @OA\Property(property="status", type="string", example="paid"),
 *                     @OA\Property(property="status_label", type="string", example="Lunas"),
 *                     @OA\Property(property="payment_proof", type="string", nullable=true, example="http://127.0.0.1:8000/storage/payment-proofs/xxx.png"),
 *                     @OA\Property(property="payment_details", type="object", nullable=true),
 *                     @OA\Property(property="paid_at", type="string", format="datetime", nullable=true, example="2025-12-17T06:26:14.000000Z"),
 *                     @OA\Property(property="expired_at", type="string", format="datetime", example="2025-12-18T06:21:01.000000Z"),
 *                     @OA\Property(property="created_at", type="string", format="datetime", example="2025-12-17T06:21:01.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="datetime"),
 *                     @OA\Property(property="user", type="object",
 *                         @OA\Property(property="id", type="integer", example=3),
 *                         @OA\Property(property="name", type="string", example="Test Admin"),
 *                         @OA\Property(property="email", type="string", example="test.admin@learningplatform.com")
 *                     ),
 *                     @OA\Property(property="item_name", type="string", example="Machine Learning with Python"),
 *                     @OA\Property(property="item_details", type="object",
 *                         @OA\Property(property="enrollment_id", type="integer", example=10),
 *                         @OA\Property(property="course_id", type="integer", example=5),
 *                         @OA\Property(property="title", type="string", example="Machine Learning with Python"),
 *                         @OA\Property(property="image", type="string", example="https://images.unsplash.com/photo-xxx"),
 *                         @OA\Property(property="instructor", type="string", example="Dr. Ravi Kumar"),
 *                         @OA\Property(property="level", type="string", example="intermediate"),
 *                         @OA\Property(property="duration", type="string", example="16 weeks"),
 *                         @OA\Property(property="progress", type="integer", example=0),
 *                         @OA\Property(property="completed", type="boolean", example=false)
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=1),
 *                 @OA\Property(property="per_page", type="integer", example=20),
 *                 @OA\Property(property="total", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * ==========================================================================
 * GET /api/transactions/{id} - Detail Transaksi
 * ==========================================================================
 * 
 * @OA\Get(
 *     path="/api/transactions/{id}",
 *     summary="Get transaction details",
 *     description="Retrieve details of a single transaction including related item information",
 *     operationId="getTransactionById",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction details retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Detail transaksi berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=18),
 *                 @OA\Property(property="transaction_code", type="string", example="TRX20251217DC9865"),
 *                 @OA\Property(property="type", type="string", example="course_enrollment"),
 *                 @OA\Property(property="type_label", type="string", example="Pendaftaran Kursus"),
 *                 @OA\Property(property="amount", type="string", example="3500000.00"),
 *                 @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                 @OA\Property(property="status", type="string", example="paid"),
 *                 @OA\Property(property="status_label", type="string", example="Lunas"),
 *                 @OA\Property(property="payment_proof", type="string", nullable=true),
 *                 @OA\Property(property="paid_at", type="string", format="datetime", nullable=true),
 *                 @OA\Property(property="expired_at", type="string", format="datetime"),
 *                 @OA\Property(property="item_name", type="string", example="Machine Learning with Python"),
 *                 @OA\Property(property="item_details", type="object",
 *                     description="Structure varies based on transaction type (Course/Enrollment/Subscription/MentoringSession)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Transaction not found"),
 *     @OA\Response(response=403, description="Forbidden - Not your transaction")
 * )
 *
 * ==========================================================================
 * POST /api/transactions/courses/{courseId} - Buat Transaksi Kursus
 * ==========================================================================
 * 
 * @OA\Post(
 *     path="/api/transactions/courses/{courseId}",
 *     summary="Create course transaction",
 *     description="Create a payment transaction for course enrollment. After payment is confirmed, user will be enrolled in the course.",
 *     operationId="createCourseTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="courseId",
 *         in="path",
 *         required=true,
 *         description="Course ID to purchase",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"payment_method"},
 *             @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, example="bank_transfer", description="Payment method - manual requires payment proof upload")
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
 *                     @OA\Property(property="id", type="integer", example=18),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX20251217DC9865"),
 *                     @OA\Property(property="type", type="string", example="course_enrollment"),
 *                     @OA\Property(property="type_label", type="string", example="Pendaftaran Kursus"),
 *                     @OA\Property(property="amount", type="string", example="3500000.00"),
 *                     @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="status_label", type="string", example="Menunggu Pembayaran"),
 *                     @OA\Property(property="expired_at", type="string", format="datetime", example="2025-12-18T06:21:01.000000Z"),
 *                     @OA\Property(property="item_name", type="string", example="Machine Learning with Python"),
 *                     @OA\Property(property="item_details", type="object",
 *                         @OA\Property(property="id", type="integer", example=5),
 *                         @OA\Property(property="title", type="string", example="Machine Learning with Python"),
 *                         @OA\Property(property="image", type="string", example="https://images.unsplash.com/photo-xxx"),
 *                         @OA\Property(property="instructor", type="string", example="Dr. Ravi Kumar"),
 *                         @OA\Property(property="level", type="string", example="intermediate"),
 *                         @OA\Property(property="duration", type="string", example="16 weeks")
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
 *     @OA\Response(response=422, description="Already enrolled in this course"),
 *     @OA\Response(response=404, description="Course not found")
 * )
 *
 * ==========================================================================
 * POST /api/transactions/subscriptions - Buat Transaksi Langganan
 * ==========================================================================
 * 
 * @OA\Post(
 *     path="/api/transactions/subscriptions",
 *     summary="Create subscription transaction",
 *     description="Create a payment transaction for subscription plan",
 *     operationId="createSubscriptionTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"plan", "payment_method"},
 *             @OA\Property(property="plan", type="string", enum={"regular", "premium"}, example="premium", description="Subscription plan type"),
 *             @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, example="bank_transfer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Subscription transaction created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Transaksi langganan berhasil dibuat"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="transaction", type="object",
 *                     @OA\Property(property="id", type="integer", example=20),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX20251217AB1234"),
 *                     @OA\Property(property="type", type="string", example="subscription"),
 *                     @OA\Property(property="type_label", type="string", example="Langganan"),
 *                     @OA\Property(property="amount", type="string", example="199000.00"),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="status_label", type="string", example="Menunggu Pembayaran"),
 *                     @OA\Property(property="item_name", type="string", example="premium - null"),
 *                     @OA\Property(property="item_details", type="object",
 *                         @OA\Property(property="id", type="integer", example=5),
 *                         @OA\Property(property="plan", type="string", example="premium"),
 *                         @OA\Property(property="package_type", type="string", nullable=true),
 *                         @OA\Property(property="start_date", type="string", format="date"),
 *                         @OA\Property(property="end_date", type="string", format="date"),
 *                         @OA\Property(property="status", type="string", example="pending")
 *                     )
 *                 ),
 *                 @OA\Property(property="subscription", type="object",
 *                     @OA\Property(property="id", type="integer", example=5),
 *                     @OA\Property(property="plan", type="string", example="premium"),
 *                     @OA\Property(property="status", type="string", example="pending")
 *                 ),
 *                 @OA\Property(property="instructions", type="object",
 *                     @OA\Property(property="bank_name", type="string", example="BCA"),
 *                     @OA\Property(property="account_number", type="string", example="1234567890"),
 *                     @OA\Property(property="account_holder", type="string", example="PT Edukasi Masa Depan")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * ==========================================================================
 * POST /api/transactions/mentoring-sessions/{sessionId} - Buat Transaksi Mentoring
 * ==========================================================================
 * 
 * @OA\Post(
 *     path="/api/transactions/mentoring-sessions/{sessionId}",
 *     summary="Create mentoring transaction",
 *     description="Create a payment transaction for a mentoring session. Only the session member can create this transaction.",
 *     operationId="createMentoringTransaction",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         description="Mentoring Session ID",
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
 *         description="Mentoring transaction created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Transaksi mentoring berhasil dibuat"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="transaction", type="object",
 *                     @OA\Property(property="id", type="integer", example=21),
 *                     @OA\Property(property="type", type="string", example="mentoring_session"),
 *                     @OA\Property(property="type_label", type="string", example="Sesi Mentoring"),
 *                     @OA\Property(property="amount", type="string", example="150000.00"),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="item_name", type="string", example="Mentoring Akademik"),
 *                     @OA\Property(property="item_details", type="object",
 *                         @OA\Property(property="id", type="integer", example=3),
 *                         @OA\Property(property="type", type="string", example="academic"),
 *                         @OA\Property(property="schedule", type="string", format="datetime"),
 *                         @OA\Property(property="status", type="string", example="pending")
 *                     )
 *                 ),
 *                 @OA\Property(property="instructions", type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="You are not the member of this session"),
 *     @OA\Response(response=404, description="Mentoring session not found")
 * )
 *
 * ==========================================================================
 * POST /api/transactions/{id}/payment-proof - Upload Bukti Pembayaran
 * ==========================================================================
 * 
 * @OA\Post(
 *     path="/api/transactions/{id}/payment-proof",
 *     summary="Upload payment proof",
 *     description="Upload payment proof (image or PDF) for manual payment verification. Max file size: 5MB. Allowed formats: jpeg, png, jpg, pdf.",
 *     operationId="uploadPaymentProof",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"payment_proof"},
 *                 @OA\Property(property="payment_proof", type="string", format="binary", description="Payment proof image or PDF (max 5MB)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment proof uploaded successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Bukti pembayaran berhasil diupload"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=18),
 *                 @OA\Property(property="transaction_code", type="string", example="TRX20251217DC9865"),
 *                 @OA\Property(property="status", type="string", example="pending"),
 *                 @OA\Property(property="status_label", type="string", example="Menunggu Pembayaran"),
 *                 @OA\Property(property="payment_proof", type="string", example="http://127.0.0.1:8000/storage/payment-proofs/xxx.png")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error - Invalid file format or size"),
 *     @OA\Response(response=403, description="Forbidden - Not your transaction")
 * )
 *
 * ==========================================================================
 * POST /api/transactions/{id}/confirm - Konfirmasi Pembayaran (Admin)
 * ==========================================================================
 * 
 * @OA\Post(
 *     path="/api/transactions/{id}/confirm",
 *     summary="Confirm payment (Admin only)",
 *     description="Manually confirm payment for a transaction. This will activate the subscription/enrollment.",
 *     operationId="confirmPayment",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID to confirm",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment confirmed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Pembayaran berhasil dikonfirmasi"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=18),
 *                 @OA\Property(property="status", type="string", example="paid"),
 *                 @OA\Property(property="status_label", type="string", example="Lunas"),
 *                 @OA\Property(property="paid_at", type="string", format="datetime", example="2025-12-17T06:26:14.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Admin only"),
 *     @OA\Response(response=404, description="Transaction not found")
 * )
 *
 * ==========================================================================
 * POST /api/transactions/{id}/refund - Request Refund
 * ==========================================================================
 * 
 * @OA\Post(
 *     path="/api/transactions/{id}/refund",
 *     summary="Request refund",
 *     description="Submit a refund request for a paid transaction",
 *     operationId="requestRefund",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID to refund",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reason"},
 *             @OA\Property(property="reason", type="string", example="Kursus tidak sesuai dengan ekspektasi", description="Reason for refund request")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Refund request submitted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Pengembalian dana berhasil diajukan"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=18),
 *                 @OA\Property(property="status", type="string", example="refunded"),
 *                 @OA\Property(property="status_label", type="string", example="Dikembalikan"),
 *                 @OA\Property(property="payment_details", type="object",
 *                     @OA\Property(property="refund_reason", type="string", example="Kursus tidak sesuai dengan ekspektasi"),
 *                     @OA\Property(property="refunded_at", type="string", format="datetime")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Not your transaction")
 * )
 *
 * ==========================================================================
 * GET /api/transactions/statistics - Statistik Transaksi (Admin)
 * ==========================================================================
 * 
 * @OA\Get(
 *     path="/api/transactions/statistics",
 *     summary="Get transaction statistics (Admin only)",
 *     description="Get transaction statistics and analytics including revenue breakdown",
 *     operationId="getTransactionStatistics",
 *     tags={"Transactions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Statistics retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Statistik transaksi berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="total_transactions", type="integer", example=150),
 *                 @OA\Property(property="total_revenue", type="number", example=50000000),
 *                 @OA\Property(property="pending_transactions", type="integer", example=10),
 *                 @OA\Property(property="paid_transactions", type="integer", example=130),
 *                 @OA\Property(property="failed_transactions", type="integer", example=5),
 *                 @OA\Property(property="refunded_transactions", type="integer", example=5),
 *                 @OA\Property(property="by_type", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="type", type="string", example="course_enrollment"),
 *                         @OA\Property(property="count", type="integer", example=80),
 *                         @OA\Property(property="total", type="number", example=28000000)
 *                     )
 *                 ),
 *                 @OA\Property(property="by_payment_method", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                         @OA\Property(property="count", type="integer", example=100)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Admin only")
 * )
 */
class TransactionSwagger {}
