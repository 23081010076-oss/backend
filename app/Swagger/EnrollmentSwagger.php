<?php

namespace App\Swagger;

/**
 * ==========================================================================
 * ENROLLMENT SWAGGER (Dokumentasi API Pendaftaran Kursus)
 * ==========================================================================
 * 
 * ENROLLMENT = Catatan pendaftaran user ke kursus
 * 
 * ALUR PENGGUNAAN BARU (Payment Confirmation Flow):
 * 1. User daftar kursus     → POST /api/courses/{id}/enroll (buat transaction)
 * 2. Upload bukti bayar     → POST /api/transactions/{id}/upload-proof
 * 3. Admin konfirmasi       → POST /api/transactions/{id}/confirm (enrollment dibuat)
 * 4. Lihat kursus saya      → GET /api/my-courses (course muncul setelah konfirmasi)
 * 5. Update progress        → PUT /api/enrollments/{id}/progress
 * 6. Admin kelola enrollment → GET/POST/PUT/DELETE /api/enrollments
 * 
 * CATATAN PENTING:
 * - Enrollment TIDAK dibuat langsung saat user enroll
 * - Enrollment hanya dibuat setelah admin konfirmasi pembayaran
 * - User tidak bisa akses course sebelum pembayaran dikonfirmasi
 * 
 * ==========================================================================

/**
 * @OA\Get(
 *     path="/api/my-courses",
 *     summary="Lihat kursus saya",
 *     description="Menampilkan daftar kursus yang sedang diikuti oleh user yang login beserta progress belajar. CATATAN: Hanya menampilkan course yang enrollmentnya sudah dibuat (setelah admin konfirmasi pembayaran). Course dengan transaksi pending tidak akan muncul di sini.",
 *     operationId="getMyCourses",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar kursus user",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Kursus Anda berhasil diambil"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example=1, description="ID Enrollment"),
 *                     @OA\Property(property="course_id", type="integer", example=5),
 *                     @OA\Property(property="progress", type="integer", example=45, description="Progress belajar (0-100%)"),
 *                     @OA\Property(property="completed", type="boolean", example=false, description="Apakah sudah selesai"),
 *                     @OA\Property(property="certificate_url", type="string", nullable=true, example="https://example.com/cert.pdf"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", description="Tanggal enrollment dibuat (setelah konfirmasi pembayaran)"),
 *                     @OA\Property(property="course", type="object",
 *                         @OA\Property(property="id", type="integer", example=5),
 *                         @OA\Property(property="title", type="string", example="Full Stack Web Development"),
 *                         @OA\Property(property="thumbnail", type="string", example="https://example.com/thumb.jpg")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized - Token tidak valid")
 * )
 *
 * @OA\Post(
 *     path="/api/courses/{courseId}/enroll",
 *     summary="Daftar ke kursus (Buat Transaction)",
 *     description="Membuat transaksi untuk pendaftaran kursus. PENTING: Enrollment TIDAK langsung dibuat. User harus upload bukti pembayaran, lalu admin konfirmasi pembayaran, baru enrollment dibuat dan user bisa akses course. Pilih metode pembayaran: 'manual', 'bank_transfer', atau 'qris'. Jika qris, akan generate QR code otomatis.",
 *     operationId="enrollCourse",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="courseId",
 *         in="path",
 *         description="ID Kursus yang ingin didaftarkan",
 *         required=true,
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="payment_method",
 *                 type="string",
 *                 enum={"manual", "bank_transfer", "qris"},
 *                 example="bank_transfer",
 *                 description="Metode pembayaran: manual (transfer manual), bank_transfer (via bank), atau qris (scan QR code)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Transaksi berhasil dibuat (Enrollment belum dibuat, menunggu konfirmasi admin)",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Transaksi berhasil dibuat. Silakan upload bukti pembayaran dan tunggu konfirmasi admin untuk mengakses kursus."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="transaction", type="object",
 *                     @OA\Property(property="id", type="integer", example=123),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX-20260101-ABC123"),
 *                     @OA\Property(property="type", type="string", example="course_enrollment"),
 *                     @OA\Property(property="amount", type="number", example=150000),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="payment_method", type="string", example="bank_transfer"),
 *                     @OA\Property(property="qr_code_url", type="string", nullable=true, example="qr-codes/TRX-20260101-ABC123.svg", description="Path QR code (hanya untuk qris)"),
 *                     @OA\Property(property="qr_string", type="string", nullable=true, example="ID.MERCHANT.TRX-20260101-ABC123.150000", description="Data QR code (hanya untuk qris)"),
 *                     @OA\Property(property="expired_at", type="string", format="date-time", example="2026-01-02T10:00:00Z", description="Batas waktu pembayaran (24 jam)")
 *                 ),
 *                 @OA\Property(property="course", type="object",
 *                     @OA\Property(property="id", type="integer", example=5),
 *                     @OA\Property(property="title", type="string", example="Full Stack Web Development"),
 *                     @OA\Property(property="price", type="number", example=150000),
 *                     @OA\Property(property="access_type", type="string", example="regular")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Tidak memiliki akses - Perlu subscription untuk course premium/regular",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=false),
 *             @OA\Property(property="pesan", type="string", example="Premium subscription required for this course")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Kursus tidak ditemukan"),
 *     @OA\Response(response=422, description="User sudah terdaftar di kursus ini")
 * )
 *
 * @OA\Put(
 *     path="/api/enrollments/{enrollmentId}/progress",
 *     summary="Update progress belajar",
 *     description="Update progress belajar user. Progress otomatis terupdate saat menandai materi selesai, tapi endpoint ini untuk manual override.",
 *     operationId="updateProgress",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="enrollmentId",
 *         in="path",
 *         description="ID Enrollment (bukan ID Course!)",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"progress"},
 *             @OA\Property(property="progress", type="integer", minimum=0, maximum=100, example=75, description="Persentase progress (0-100)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Progress berhasil diupdate",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Progress berhasil diupdate"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="progress", type="integer", example=75),
 *                 @OA\Property(property="completed", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Tidak punya akses ke enrollment ini"),
 *     @OA\Response(response=404, description="Enrollment tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/enrollments",
 *     summary="Lihat semua enrollment (Admin)",
 *     description="Admin dapat melihat seluruh data enrollment dari semua user",
 *     operationId="indexEnrollments",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="page", in="query", description="Halaman", @OA\Schema(type="integer", default=1)),
 *     @OA\Parameter(name="per_page", in="query", description="Jumlah per halaman", @OA\Schema(type="integer", default=15)),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil daftar enrollment",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="user_id", type="integer", example=3),
 *                     @OA\Property(property="course_id", type="integer", example=5),
 *                     @OA\Property(property="progress", type="integer", example=45),
 *                     @OA\Property(property="completed", type="boolean", example=false),
 *                     @OA\Property(property="user", type="object",
 *                         @OA\Property(property="id", type="integer", example=3),
 *                         @OA\Property(property="name", type="string", example="Andi Pratama")
 *                     ),
 *                     @OA\Property(property="course", type="object",
 *                         @OA\Property(property="id", type="integer", example=5),
 *                         @OA\Property(property="title", type="string", example="Web Development")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Admin only")
 * )
 *
 * @OA\Get(
 *     path="/api/enrollments/{id}",
 *     summary="Lihat detail enrollment",
 *     description="Lihat detail satu enrollment termasuk data user dan course",
 *     operationId="showEnrollment",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID Enrollment",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil mengambil detail enrollment",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=3),
 *                 @OA\Property(property="course_id", type="integer", example=5),
 *                 @OA\Property(property="progress", type="integer", example=45),
 *                 @OA\Property(property="completed", type="boolean", example=false),
 *                 @OA\Property(property="certificate_url", type="string", nullable=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="user", type="object"),
 *                 @OA\Property(property="course", type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Enrollment tidak ditemukan")
 * )
 *
 * @OA\Post(
 *     path="/api/enrollments",
 *     summary="Buat enrollment manual (Admin)",
 *     description="Admin dapat mendaftarkan user ke kursus secara manual",
 *     operationId="storeEnrollment",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "course_id"},
 *             @OA\Property(property="user_id", type="integer", example=3, description="ID User yang akan didaftarkan"),
 *             @OA\Property(property="course_id", type="integer", example=5, description="ID Kursus tujuan")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Enrollment berhasil dibuat"),
 *     @OA\Response(response=403, description="Forbidden - Admin only"),
 *     @OA\Response(response=422, description="User sudah terdaftar di kursus ini")
 * )
 *
 * @OA\Put(
 *     path="/api/enrollments/{id}",
 *     summary="Update enrollment (Admin)",
 *     description="Admin dapat mengupdate data enrollment seperti progress dan status completed",
 *     operationId="updateEnrollment",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID Enrollment",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="progress", type="integer", minimum=0, maximum=100, example=50),
 *             @OA\Property(property="completed", type="boolean", example=false),
 *             @OA\Property(property="certificate_url", type="string", example="https://example.com/cert.pdf")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Enrollment berhasil diupdate"),
 *     @OA\Response(response=403, description="Forbidden - Admin only"),
 *     @OA\Response(response=404, description="Enrollment tidak ditemukan")
 * )
 *
 * @OA\Delete(
 *     path="/api/enrollments/{id}",
 *     summary="Hapus enrollment (Admin)",
 *     description="Admin dapat menghapus enrollment (batalkan pendaftaran user dari kursus)",
 *     operationId="deleteEnrollment",
 *     tags={"Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID Enrollment yang akan dihapus",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="Enrollment berhasil dihapus"),
 *     @OA\Response(response=403, description="Forbidden - Admin only"),
 *     @OA\Response(response=404, description="Enrollment tidak ditemukan")
 * )
 */
class EnrollmentSwagger {}
