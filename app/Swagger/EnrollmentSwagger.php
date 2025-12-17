<?php

namespace App\Swagger;

/**
 * ==========================================================================
 * ENROLLMENT SWAGGER (Dokumentasi API Pendaftaran Kursus)
 * ==========================================================================
 * 
 * ENROLLMENT = Catatan pendaftaran user ke kursus
 * 
 * ALUR PENGGUNAAN:
 * 1. User daftar kursus     → POST /api/courses/{id}/enroll
 * 2. Lihat kursus saya      → GET /api/my-courses
 * 3. Update progress        → PUT /api/enrollments/{id}/progress
 * 4. Admin kelola enrollment → GET/POST/PUT/DELETE /api/enrollments
 * 
 * ==========================================================================
 */

/**
 * @OA\Get(
 *     path="/api/my-courses",
 *     summary="Lihat kursus saya",
 *     description="Menampilkan daftar kursus yang sedang diikuti oleh user yang login beserta progress belajar",
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
 *                     @OA\Property(property="created_at", type="string", format="date-time", description="Tanggal daftar"),
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
 *     summary="Daftar ke kursus",
 *     description="Mendaftarkan user yang login ke kursus tertentu. Tidak perlu request body, hanya perlu ID kursus di URL.",
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
 *     @OA\Response(
 *         response=201,
 *         description="Berhasil mendaftar ke kursus",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Berhasil mendaftar ke kursus"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=3),
 *                 @OA\Property(property="course_id", type="integer", example=5),
 *                 @OA\Property(property="progress", type="integer", example=0),
 *                 @OA\Property(property="completed", type="boolean", example=false)
 *             )
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
