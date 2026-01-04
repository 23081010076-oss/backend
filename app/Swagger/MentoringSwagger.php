<?php

namespace App\Swagger;

/**
 * ==========================================================================
 * MENTORING SESSION SWAGGER DOCUMENTATION
 * ==========================================================================
 *
 * ALUR MENTORING:
 * 1. Student memilih mentor → GET /api/mentors
 * 2. Student melihat jadwal mentor → GET /api/mentors/{id}/schedule
 * 3. Student booking sesi → POST /api/mentoring-sessions
 * 4. Student upload bukti bayar → POST /api/transactions/{id}/payment-proof
 * 5. Admin konfirmasi pembayaran → POST /api/transactions/{id}/confirm
 * 6. Status sesi berubah: pending → scheduled
 * 7. Mentor update status setelah selesai → PUT /api/mentoring-sessions/{id}/status
 * 8. Student beri feedback → POST /api/mentoring-sessions/{id}/feedback
 *
 * @OA\Schema(
 *     schema="MentoringSession",
 *     title="Mentoring Session Schema",
 *     description="Struktur data sesi mentoring",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="mentor_id", type="integer", example=7, description="ID User mentor"),
 *     @OA\Property(property="member_id", type="integer", example=4, description="ID User student/member yang booking"),
 *     @OA\Property(property="session_id", type="string", nullable=true, example="MENT-20251231-001", description="Kode unik sesi"),
 *     @OA\Property(property="type", type="string", enum={"academic", "life_plan"}, example="academic",
 *         description="Jenis mentoring: academic=bimbingan akademik (Rp150.000), life_plan=perencanaan karir (Rp200.000)"),
 *     @OA\Property(property="schedule", type="string", format="date-time", nullable=true, example="2026-01-05 10:00:00", description="Jadwal sesi"),
 *     @OA\Property(property="meeting_link", type="string", nullable=true, example="https://zoom.us/j/123456789", description="Link meeting Zoom/GMeet"),
 *     @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, nullable=true, example="manual",
 *         description="Metode pembayaran"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Saya butuh bimbingan skripsi", description="Catatan dari student/mentor"),
 *     @OA\Property(property="status", type="string", enum={"pending", "scheduled", "completed", "cancelled", "refunded"}, example="pending",
 *         description="Status sesi: pending (menunggu bayar), scheduled (terjadwal), completed (selesai), cancelled, refunded"),
 *     @OA\Property(property="need_assessment_status", type="string", enum={"pending", "completed"}, example="pending",
 *         description="Status need assessment form"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="mentor", type="object", description="Data mentor",
 *         @OA\Property(property="id", type="integer", example=7),
 *         @OA\Property(property="name", type="string", example="Dr. Budi Santoso"),
 *         @OA\Property(property="email", type="string", example="mentor@example.com"),
 *         @OA\Property(property="role", type="string", example="mentor")
 *     ),
 *     @OA\Property(property="member", type="object", description="Data student/member",
 *         @OA\Property(property="id", type="integer", example=4),
 *         @OA\Property(property="name", type="string", example="Andi Student"),
 *         @OA\Property(property="email", type="string", example="student@example.com"),
 *         @OA\Property(property="role", type="string", example="student")
 *     )
 * )
 *
 * ==========================================================================
 * MENTOR ENDPOINTS (Untuk Student memilih mentor)
 * ==========================================================================
 *
 * @OA\Get(
 *     path="/api/mentors",
 *     summary="Lihat daftar mentor (Public)",
 *     description="Endpoint publik untuk melihat daftar mentor di landing page. Tidak memerlukan autentikasi.",
 *     operationId="listMentors",
 *     tags={"Mentoring"},
 *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
 *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
 *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Cari berdasarkan nama mentor"),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar mentor berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Daftar mentor berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=7),
 *                         @OA\Property(property="name", type="string", example="Dr. Budi Santoso"),
 *                         @OA\Property(property="email", type="string", example="mentor@example.com"),
 *                         @OA\Property(property="role", type="string", example="mentor"),
 *                         @OA\Property(property="phone", type="string", nullable=true, example="081234567890"),
 *                         @OA\Property(property="bio", type="string", nullable=true, example="Expert in Machine Learning"),
 *                         @OA\Property(property="profile_photo", type="string", nullable=true)
 *                     )
 *                 ),
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=2),
 *                 @OA\Property(property="per_page", type="integer", example=15),
 *                 @OA\Property(property="total", type="integer", example=25)
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/mentors/{id}",
 *     summary="Lihat detail mentor (Public)",
 *     description="Endpoint publik untuk melihat detail profil mentor di landing page. Tidak memerlukan autentikasi.",
 *     operationId="showMentor",
 *     tags={"Mentoring"},
 *     @OA\Parameter(name="id", in="path", required=true, description="ID Mentor", @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Detail mentor berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Detail mentor berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=7),
 *                 @OA\Property(property="name", type="string", example="Dr. Budi Santoso"),
 *                 @OA\Property(property="email", type="string", example="mentor@example.com"),
 *                 @OA\Property(property="role", type="string", example="mentor"),
 *                 @OA\Property(property="phone", type="string", nullable=true, example="081234567890"),
 *                 @OA\Property(property="bio", type="string", nullable=true, example="Expert in Machine Learning with 10 years of experience"),
 *                 @OA\Property(property="profile_photo", type="string", nullable=true),
 *                 @OA\Property(property="achievements", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="experiences", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="organizations", type="array", @OA\Items(type="object"))
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Mentor tidak ditemukan (user bukan mentor)")
 * )
 *
 * ==========================================================================
 * LIST & RETRIEVE ENDPOINTS
 * ==========================================================================
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions",
 *     summary="Lihat semua sesi mentoring",
 *     description="Menampilkan semua sesi mentoring. Dapat difilter berdasarkan status dan type.",
 *     operationId="getAllMentoringSessions",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="status", in="query", description="Filter by status",
 *         @OA\Schema(type="string", enum={"pending", "scheduled", "completed", "cancelled", "refunded"})),
 *     @OA\Parameter(name="type", in="query", description="Filter by type",
 *         @OA\Schema(type="string", enum={"academic", "life_plan"})),
 *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar sesi mentoring berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Daftar sesi mentoring berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession")),
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=3),
 *                 @OA\Property(property="per_page", type="integer", example=15),
 *                 @OA\Property(property="total", type="integer", example=42)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 *
 * @OA\Get(
 *     path="/api/my-mentoring-sessions",
 *     summary="Lihat sesi mentoring saya",
 *     description="Student melihat sesi dimana dia adalah member. Mentor melihat sesi dimana dia adalah mentor.",
 *     operationId="getMyMentoringSessions",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="status", in="query", description="Filter by status",
 *         @OA\Schema(type="string", enum={"pending", "scheduled", "completed", "cancelled", "refunded"})),
 *     @OA\Parameter(name="type", in="query", description="Filter by type",
 *         @OA\Schema(type="string", enum={"academic", "life_plan"})),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar sesi mentoring saya berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Daftar sesi mentoring saya berhasil diambil"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentoringSession")),
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=1),
 *                 @OA\Property(property="per_page", type="integer", example=15),
 *                 @OA\Property(property="total", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 *
 * @OA\Get(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Lihat detail sesi mentoring",
 *     description="Lihat detail lengkap satu sesi mentoring. Hanya bisa diakses oleh member, mentor yang terlibat, atau admin.",
 *     operationId="getSingleMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, description="ID Sesi Mentoring", @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Detail sesi berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Detail sesi mentoring berhasil diambil"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Bukan peserta sesi ini"),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/mentors/{id}/schedule",
 *     summary="Lihat jadwal mentor",
 *     description="Melihat jadwal sesi mentoring yang sudah terbooking untuk mentor tertentu. Berguna untuk student menghindari jadwal yang bentrok.",
 *     operationId="getMentorSchedule",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, description="ID User Mentor", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="from_date", in="query", description="Filter dari tanggal",
 *         @OA\Schema(type="string", format="date", example="2026-01-01")),
 *     @OA\Parameter(name="to_date", in="query", description="Filter sampai tanggal",
 *         @OA\Schema(type="string", format="date", example="2026-01-31")),
 *     @OA\Response(
 *         response=200,
 *         description="Jadwal mentor berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Jadwal mentor berhasil diambil"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="schedule", type="string", format="date-time", example="2026-01-05 10:00:00"),
 *                     @OA\Property(property="type", type="string", example="academic"),
 *                     @OA\Property(property="status", type="string", example="scheduled")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Mentor tidak ditemukan")
 * )
 *
 * ==========================================================================
 * CREATE & UPDATE ENDPOINTS
 * ==========================================================================
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions",
 *     summary="Booking sesi mentoring baru (Student)",
 *     description="Student membuat booking sesi mentoring dengan mentor yang dipilih. Otomatis membuat transaksi pembayaran. Status awal: pending. Harga: academic=Rp150.000, life_plan=Rp200.000",
 *     operationId="createMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"mentor_id", "type"},
 *             @OA\Property(property="mentor_id", type="integer", example=7,
 *                 description="ID mentor yang dipilih (harus user dengan role mentor)"),
 *             @OA\Property(property="type", type="string", enum={"academic", "life_plan"}, example="academic",
 *                 description="Jenis mentoring: academic (Rp150.000) atau life_plan (Rp200.000)"),
 *             @OA\Property(property="schedule", type="string", format="date-time", example="2026-01-05 10:00:00",
 *                 description="Jadwal yang diinginkan (opsional, harus di masa depan)"),
 *             @OA\Property(property="meeting_link", type="string", example="https://zoom.us/j/123456789",
 *                 description="Link meeting (opsional, biasanya diisi mentor)"),
 *             @OA\Property(property="payment_method", type="string", enum={"manual", "bank_transfer"}, example="manual",
 *                 description="Metode pembayaran"),
 *             @OA\Property(property="notes", type="string", example="Saya butuh bimbingan untuk skripsi tentang machine learning",
 *                 description="Catatan/topik yang ingin dibahas (opsional)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Booking berhasil dibuat",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Sesi mentoring berhasil dibuat"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="session", ref="#/components/schemas/MentoringSession"),
 *                 @OA\Property(property="transaction", type="object",
 *                     @OA\Property(property="id", type="integer", example=456),
 *                     @OA\Property(property="transaction_code", type="string", example="TRX-20260101-ABC123"),
 *                     @OA\Property(property="type", type="string", example="mentoring_session"),
 *                     @OA\Property(property="amount", type="number", example=150000),
 *                     @OA\Property(property="status", type="string", example="pending"),
 *                     @OA\Property(property="payment_method", type="string", example="manual"),
 *                     @OA\Property(property="expired_at", type="string", format="date-time", example="2026-01-02 20:00:00")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error - mentor_id tidak valid atau type tidak sesuai")
 * )
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Update detail sesi",
 *     description="Update jadwal, meeting link, atau catatan sesi. Member hanya bisa update sesinya sendiri. Mentor bisa update notes untuk sesinya.",
 *     operationId="updateMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="schedule", type="string", format="date-time", example="2026-01-10 14:00:00"),
 *             @OA\Property(property="meeting_link", type="string", example="https://meet.google.com/abc-defg-hij"),
 *             @OA\Property(property="notes", type="string", example="Reschedule karena ada keperluan")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Sesi berhasil diupdate",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Sesi mentoring berhasil diupdate"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Bukan peserta sesi ini"),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 *
 * @OA\Delete(
 *     path="/api/mentoring-sessions/{id}",
 *     summary="Hapus/Batalkan sesi mentoring",
 *     description="Hapus atau batalkan sesi mentoring. Hanya bisa dilakukan oleh member yang membuat sesi atau admin.",
 *     operationId="deleteMentoringSession",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Sesi berhasil dihapus",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Sesi mentoring berhasil dihapus"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Hanya member atau admin yang bisa hapus"),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 *
 * ==========================================================================
 * STATUS & FEEDBACK ENDPOINTS
 * ==========================================================================
 *
 * @OA\Put(
 *     path="/api/mentoring-sessions/{id}/status",
 *     summary="Update status sesi (Mentor/Admin only)",
 *     description="Mentor atau admin mengubah status sesi mentoring. Transisi normal: pending → scheduled (setelah bayar dikonfirmasi) → completed (setelah sesi selesai).",
 *     operationId="updateSessionStatus",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string",
 *                 enum={"pending", "scheduled", "completed", "cancelled", "refunded"},
 *                 example="completed",
 *                 description="Status baru sesi")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status berhasil diupdate",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Status sesi berhasil diupdate"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Hanya mentor atau admin"),
 *     @OA\Response(response=422, description="Validation error - status tidak valid")
 * )
 *
 * @OA\Post(
 *     path="/api/mentoring-sessions/{id}/feedback",
 *     summary="Beri feedback setelah sesi selesai",
 *     description="Member atau mentor memberikan rating dan feedback setelah sesi mentoring selesai. Sesi harus berstatus 'completed'.",
 *     operationId="submitMentoringFeedback",
 *     tags={"Mentoring"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"rating"},
 *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5,
 *                 description="Rating 1-5 bintang"),
 *             @OA\Property(property="feedback", type="string", example="Mentornya sangat membantu dan sabar menjelaskan!",
 *                 description="Komentar feedback (opsional)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Feedback berhasil dikirim",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Feedback berhasil dikirim"),
 *             @OA\Property(property="data", ref="#/components/schemas/MentoringSession")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad Request - Sesi belum berstatus completed"),
 *     @OA\Response(response=403, description="Forbidden - Bukan peserta sesi ini"),
 *     @OA\Response(response=404, description="Sesi tidak ditemukan")
 * )
 */
class MentoringSwagger {}
