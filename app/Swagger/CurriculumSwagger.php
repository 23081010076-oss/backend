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
 *     description="Menampilkan daftar materi pembelajaran untuk course tertentu. **Supports nested sub-bab structure**: Use `?nested=true` to get hierarchical tree structure with unlimited depth (Bab > Sub Bab > Sub Sub Bab). Section numbering: '1' (root), '1.1' (sub), '1.1.1' (sub-sub), etc. Default returns flat list sorted by section_order.",
 *     operationId="getCurriculums",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="courseId",
 *         in="path",
 *         required=true,
 *         description="ID of the course",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="nested",
 *         in="query",
 *         required=false,
 *         description="Return nested tree structure with children. Default is flat list.",
 *         @OA\Schema(type="boolean", example=false, default=false)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Daftar kurikulum berhasil diambil (Flat or Nested structure)",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Daftar kurikulum berhasil diambil"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="course_id", type="integer", example=1),
 *                     @OA\Property(property="section", type="string", example="1", description="Section number: '1' (root), '1.1' (sub), '1.1.1' (sub-sub)"),
 *                     @OA\Property(property="section_order", type="integer", example=1000000, description="Auto-calculated for sorting"),
 *                     @OA\Property(property="title", type="string", example="Pengenalan Web Development"),
 *                     @OA\Property(property="description", type="string", example="Bab 1: Memahami dasar-dasar web"),
 *                     @OA\Property(property="order", type="integer", example=1, description="Order within same section"),
 *                     @OA\Property(property="duration", type="string", example="30 menit"),
 *                     @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/video-id", nullable=true),
 *                     @OA\Property(property="level", type="integer", example=0, description="Depth level: 0=root, 1=sub, 2=sub-sub"),
 *                     @OA\Property(property="is_parent", type="boolean", example=true, description="Has children or not"),
 *                     @OA\Property(property="children", type="array", description="Only present if nested=true",
 *                         @OA\Items(type="object", description="Same structure, recursive")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Course not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/courses/{courseId}/curriculums",
 *     summary="Tambah kurikulum baru (Support Nested Sub-bab)",
 *     description="Menambahkan materi pembelajaran baru ke course dengan support **nested structure**. **Auto-generate section number** if not provided. Use `parent_section` to create sub-bab: null/empty = root level ('1','2','3'), '1' = sub of 1 ('1.1','1.2'), '1.1' = sub of 1.1 ('1.1.1','1.1.2'), etc. Unlimited depth supported. (Admin only)",
 *     operationId="createCurriculum",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="courseId",
 *         in="path",
 *         required=true,
 *         description="ID of the course",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title"},
 *             @OA\Property(property="title", type="string", example="Pengenalan HTML", description="Title of curriculum item (required)"),
 *             @OA\Property(property="description", type="string", example="Mempelajari dasar-dasar HTML", nullable=true),
 *             @OA\Property(property="duration", type="string", example="30 menit", nullable=true),
 *             @OA\Property(property="video_url", type="string", example="https://youtube.com/embed/video-id", nullable=true, description="Video URL (optional)"),
 *             @OA\Property(property="parent_section", type="string", example="1", nullable=true, description="Parent section number for nested structure. null=root, '1'=sub of 1, '1.1'=sub of 1.1. Section will be auto-generated."),
 *             @OA\Property(property="section", type="string", example="1.2", nullable=true, description="Custom section number (optional, auto-generated if not provided)"),
 *             @OA\Property(property="section_order", type="integer", example=1002000, nullable=true, description="Auto-calculated if not provided"),
 *             @OA\Property(property="order", type="integer", example=1, nullable=true, description="Order within same section (auto-generated if not provided)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Kurikulum berhasil ditambahkan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Kurikulum berhasil ditambahkan"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="section", type="string", example="1.1", description="Auto-generated section number"),
 *                 @OA\Property(property="section_order", type="integer", example=1001000),
 *                 @OA\Property(property="title", type="string", example="Pengenalan HTML"),
 *                 @OA\Property(property="level", type="integer", example=1, description="Depth: 0=root, 1=sub, 2=sub-sub"),
 *                 @OA\Property(property="is_parent", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=403, description="Forbidden - Admin only"),
 *     @OA\Response(response=422, description="Validation error")
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
 * @OA\Get(
 *     path="/api/courses/{courseId}/progress",
 *     summary="Get curriculum progress",
 *     description="Get progress tracking for all curriculums in a course for the authenticated user",
 *     operationId="getCurriculumProgress",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="courseId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Progress berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="sukses", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="course_id", type="integer", example=1),
 *                 @OA\Property(property="total_materials", type="integer", example=10),
 *                 @OA\Property(property="completed_materials", type="integer", example=5),
 *                 @OA\Property(property="progress_percentage", type="integer", example=50),
 *                 @OA\Property(property="curriculum_progress", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="curriculum_id", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="Pengenalan Web Development"),
 *                         @OA\Property(property="completed", type="boolean", example=true),
 *                         @OA\Property(property="completed_at", type="string", format="datetime", nullable=true)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Course tidak ditemukan")
 * )
 *
 * @OA\Post(
 *     path="/api/curriculums/{curriculumId}/complete",
 *     summary="Tandai materi selesai",
 *     description="Menandai materi sebagai selesai dipelajari. Progress akan otomatis dihitung berdasarkan jumlah materi yang selesai.",
 *     operationId="markCurriculumComplete",
 *     tags={"Curriculum"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="curriculumId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"completed"},
 *             @OA\Property(property="completed", type="boolean", description="Status penyelesaian (true = selesai, false = belum selesai)", example=true)
 *         )
 *     ),
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
 *     @OA\Response(response=403, description="Tidak memiliki akses"),
 *     @OA\Response(response=404, description="Materi tidak ditemukan")
 * )
 */
class CurriculumSwagger {}
