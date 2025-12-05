<?php

namespace App\Swagger;

/**
 * @OA\Tag(
 *     name="Curriculum",
 *     description="Endpoints untuk mengelola kurikulum/materi pembelajaran course"
 * )
 *
 * @OA\Get(
 *     path="/api/courses/{courseId}/curriculums",
 *     summary="List semua kurikulum course",
 *     description="Menampilkan daftar materi pembelajaran untuk course tertentu, diurutkan berdasarkan section dan order",
 *     operationId="getCurriculums",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="courseId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar kurikulum berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Daftar kurikulum berhasil diambil"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="course_id", type="integer", example=1),
 *                     @OA\Property(property="section", type="string", example="Bab 1: Dasar-dasar Web"),
 *                     @OA\Property(property="section_order", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Pengenalan Web Development"),
 *                     @OA\Property(property="description", type="string", example="Memahami dasar-dasar web"),
 *                     @OA\Property(property="order", type="integer", example=1),
 *                     @OA\Property(property="duration", type="string", example="2 jam"),
 *                     @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/pengenalan-web")
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/courses/{courseId}/curriculums",
 *     summary="Tambah kurikulum baru",
 *     description="Menambahkan satu materi pembelajaran baru ke course (Admin only)",
 *     operationId="createCurriculum",
 *     tags={"Curriculum"},
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
 *             required={"title"},
 *             @OA\Property(property="section", type="string", example="Bab 1: Pengenalan"),
 *             @OA\Property(property="section_order", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Materi Baru"),
 *             @OA\Property(property="description", type="string", example="Deskripsi materi"),
 *             @OA\Property(property="duration", type="string", example="30 menit"),
 *             @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/materi-baru"),
 *             @OA\Property(property="order", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=201, description="Kurikulum berhasil ditambahkan"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Admin only")
 * )
 *
 * @OA\Post(
 *     path="/api/courses/{courseId}/curriculums/bulk",
 *     summary="Tambah banyak kurikulum sekaligus",
 *     description="Bulk create: Menambahkan beberapa materi pembelajaran sekaligus (Admin only)",
 *     operationId="bulkCreateCurriculum",
 *     tags={"Curriculum"},
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
 *             required={"curriculums"},
 *             @OA\Property(property="curriculums", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="section", type="string", example="Bab 1: Pengenalan"),
 *                     @OA\Property(property="section_order", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Materi 1"),
 *                     @OA\Property(property="description", type="string", example="Deskripsi materi"),
 *                     @OA\Property(property="duration", type="string", example="2 jam"),
 *                     @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/materi-1")
 *                 ),
 *                 example={
 *                     {"section": "Bab 1: Pengenalan", "title": "Materi 1", "duration": "1 jam", "video_url": "https://youtube.com/embed/materi-1"},
 *                     {"section": "Bab 1: Pengenalan", "title": "Materi 2", "duration": "2 jam", "video_url": "https://youtube.com/embed/materi-2"},
 *                     {"section": "Bab 2: Lanjutan", "title": "Materi 3", "duration": "1 jam", "video_url": "https://youtube.com/embed/materi-3"}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Kurikulum berhasil ditambahkan",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="3 kurikulum berhasil ditambahkan")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Forbidden - Admin only")
 * )
 *
 * @OA\Put(
 *     path="/api/courses/{courseId}/curriculums/{id}",
 *     summary="Update kurikulum",
 *     description="Mengubah materi pembelajaran (Admin only)",
 *     operationId="updateCurriculum",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="courseId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="section", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="duration", type="string"),
 *             @OA\Property(property="video_url", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Kurikulum berhasil diupdate"),
 *     @OA\Response(response=404, description="Kurikulum tidak ditemukan")
 * )
 *
 * @OA\Delete(
 *     path="/api/courses/{courseId}/curriculums/{id}",
 *     summary="Hapus kurikulum",
 *     description="Menghapus materi pembelajaran (Admin only)",
 *     operationId="deleteCurriculum",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="courseId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Kurikulum berhasil dihapus")
 * )
 *
 * @OA\Put(
 *     path="/api/courses/{courseId}/curriculums/reorder",
 *     summary="Ubah urutan kurikulum",
 *     description="Mengubah urutan tampilan materi pembelajaran (Admin only)",
 *     operationId="reorderCurriculum",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="courseId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"ordered_ids"},
 *             @OA\Property(property="ordered_ids", type="array",
 *                 @OA\Items(type="integer"),
 *                 example={3, 1, 2, 4}
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Urutan kurikulum berhasil diupdate")
 * )
 *
 * @OA\Post(
 *     path="/api/enrollments/{enrollmentId}/curriculums/{curriculumId}/complete",
 *     summary="Tandai materi selesai",
 *     description="Menandai materi sebagai selesai dipelajari. Progress akan otomatis dihitung berdasarkan jumlah materi yang selesai.",
 *     operationId="markCurriculumComplete",
 *     tags={"Curriculum", "Enrollment"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="enrollmentId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="curriculumId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Materi berhasil ditandai selesai",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="pesan", type="string", example="Materi berhasil ditandai selesai"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="curriculum_progress", type="object",
 *                     @OA\Property(property="curriculum_id", type="integer", example=1),
 *                     @OA\Property(property="completed", type="boolean", example=true),
 *                     @OA\Property(property="completed_at", type="string", example="2025-12-05T23:00:00")
 *                 ),
 *                 @OA\Property(property="enrollment", type="object",
 *                     @OA\Property(property="progress", type="integer", example=50),
 *                     @OA\Property(property="calculated_progress", type="integer", example=50),
 *                     @OA\Property(property="completed_materials", type="integer", example=3),
 *                     @OA\Property(property="total_materials", type="integer", example=6),
 *                     @OA\Property(property="completed", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Tidak memiliki akses ke enrollment ini"),
 *     @OA\Response(response=404, description="Materi tidak ditemukan dalam kursus ini")
 * )
 */
class CurriculumSwagger {}
