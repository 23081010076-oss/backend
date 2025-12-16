<?php

namespace App\Swagger;

/**

 *
 * @OA\Schema(
 *     schema="MentoringSession",
 *     title="Mentoring Session Schema",
 *     description="Struktur data sesi mentoring",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="mentor_id", type="integer", example=7, description="ID User yang jadi mentor"),
 *     @OA\Property(property="member_id", type="integer", example=4, description="ID User yang jadi student"),
 *     @OA\Property(property="type", type="string", enum={"academic", "life_plan"}, example="academic", description="Jenis mentoring"),
 *     @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="qris"),
 *     @OA\Property(property="schedule", type="string", format="date-time", example="2025-12-20 10:00:00", description="Jadwal sesi"),
 *     @OA\Property(property="status", type="string", enum={"pending", "scheduled", "ongoing", "completed", "cancelled", "refunded"}, example="pending"),
 *     @OA\Property(property="meeting_link", type="string", nullable=true, example="https://zoom.us/j/123456789", description="Link Zoom/GMeet"),
 *     @OA\Property(property="note", type="string", nullable=true, example="Saya butuh bimbingan skripsi"),
 *     @OA\Property(property="rating", type="integer", nullable=true, minimum=1, maximum=5, example=5, description="Rating dari student"),
 *     @OA\Property(property="feedback", type="string", nullable=true, example="Mentornya sangat membantu!"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Get(
 *     path="/api/my-mentoring-sessions",
 *     summary="Lihat sesi mentoring saya",
 *     description="Student/Mentor dapat melihat semua sesi mentoring mereka (baik sebagai student maupun mentor)",
 *     operationId="getMyMentoringSessions",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="status", in="query", description="Filter by status", @OA\Schema(type="string", enum={"pending", "scheduled", "ongoing", "completed", "cancelled"})),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar sesi mentoring berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Daftar sesi mentoring saya berhasil diambil"),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions",
 *     summary="Booking sesi mentoring baru",
 *     description="Student membuat booking sesi mentoring dengan mentor. Status awal: pending",
 *     operationId="createMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"mentor_id", "type", "payment_method", "schedule"},
 *             @OA\Property(property="mentor_id", type="integer", example=7, description="ID mentor yang dipilih"),
 *             @OA\Property(property="type", type="string", enum={"academic", "life_plan"}, example="academic", description="Jenis mentoring: academic=bimbingan akademik, life_plan=perencanaan karir"),
 *             @OA\Property(property="payment_method", type="string", enum={"qris", "bank_transfer", "credit_card", "e_wallet"}, example="bank_transfer"),
 *             @OA\Property(property="schedule", type="string", format="date-time", example="2025-12-20 10:00:00", description="Jadwal yang diinginkan"),
 *             @OA\Property(property="note", type="string", example="Saya butuh bimbingan untuk skripsi tentang machine learning", description="Catatan/topik yang ingin dibahas")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Booking berhasil dibuat, menunggu konfirmasi mentor",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Sesi mentoring berhasil dibuat"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Lihat detail sesi mentoring",
 *     description="Lihat detail lengkap satu sesi mentoring termasuk meeting link",
 *     operationId="getSingleMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, description="ID Sesi Mentoring", @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Detail sesi berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions/{id}/feedback",
 *     summary="Beri feedback setelah sesi selesai",
 *     description="Student memberikan rating dan feedback setelah sesi mentoring selesai (status: completed)",
 *     operationId="submitMentoringFeedback",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"rating"},
 *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5, description="Rating 1-5 bintang"),
 *             @OA\Property(property="comment", type="string", example="Mentornya sangat membantu dan sabar menjelaskan!", description="Komentar feedback")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Feedback berhasil dikirim"),
 *     @OA\Response(response=400, description="Sesi belum selesai (harus status: completed)"),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{mentorId}/schedule",
 *     summary="Lihat jadwal mentor",
 *     description="Lihat jadwal sesi yang sudah terbooking untuk mentor tertentu (untuk menghindari bentrok)",
 *     operationId="getMentorSchedule",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="mentorId", in="path", required=true, description="ID User Mentor", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="from_date", in="query", description="Filter dari tanggal", @OA\Schema(type="string", format="date", example="2025-12-01")),
 *     @OA\Parameter(name="to_date", in="query", description="Filter sampai tanggal", @OA\Schema(type="string", format="date", example="2025-12-31")),
 *     @OA\Response(
 *         response=200,
 *         description="Jadwal mentor berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession"))
 *         )
 *     ),
 *     @OA\Response(response=404, description="Mentor tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions",
 *     summary="Lihat semua sesi mentoring",
 *     description="Mentor/Admin dapat melihat semua sesi mentoring",
 *     operationId="getAllMentoringSessions",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"pending", "scheduled", "ongoing", "completed", "cancelled"})),
 *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar sesi mentoring",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession"))
 *         )
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{id}/status",
 *     summary="Update status sesi (Mentor only)",
 *     description="Mentor mengubah status sesi mentoring. Contoh: pending ke scheduled (konfirmasi), scheduled ke completed (selesai)",
 *     operationId="updateSessionStatus",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"pending", "scheduled", "ongoing", "completed", "cancelled", "refunded"}, example="scheduled", description="Status baru"),
 *             @OA\Property(property="meeting_link", type="string", example="https://zoom.us/j/123456789", description="Link meeting (wajib jika status=scheduled)")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Status berhasil diupdate"),
 *     @OA\Response(response=403, description="Hanya mentor yang bisa update status")
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Update detail sesi",
 *     description="Update jadwal, meeting link, atau catatan sesi",
 *     operationId="updateMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="schedule", type="string", format="date-time", example="2025-12-25 14:00:00"),
 *             @OA\Property(property="meeting_link", type="string", example="https://meet.google.com/abc-defg-hij"),
 *             @OA\Property(property="note", type="string", example="Reschedule karena ada keperluan")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Sesi berhasil diupdate"),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Hapus sesi mentoring",
 *     description="Hapus atau batalkan sesi mentoring",
 *     operationId="deleteMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Sesi berhasil dihapus"),
 *     @OA\Response(response=403, description="Tidak punya akses"),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 */
class MentoringSwagger {}
