<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCurriculum;
use App\Services\CourseCurriculumService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Curriculum\StoreCurriculumRequest;
use App\Http\Requests\Curriculum\UpdateCurriculumRequest;

/**
 * ==========================================================================
 * COURSE CURRICULUM CONTROLLER
 * ==========================================================================
 * 
 * FUNGSI: Mengelola kurikulum/materi pembelajaran untuk kursus.
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Handle request/response (file ini)
 * - Service     : Business logic → app/Services/CourseCurriculumService.php
 */
class CourseCurriculumController extends Controller
{
    use ApiResponse;

    protected CourseCurriculumService $curriculumService;

    public function __construct(CourseCurriculumService $curriculumService)
    {
        $this->curriculumService = $curriculumService;
    }

    /**
     * Tampilkan semua kurikulum untuk course tertentu
     * 
     * Query params:
     * - nested=true : Return nested structure (with children)
     * - flat=true : Return flat list (default)
     */
    public function index(Request $request, int $courseId): JsonResponse
    {
        // Pastikan course ada
        Course::findOrFail($courseId);

        $nested = $request->boolean('nested', false);
        $curriculums = $this->curriculumService->getCurriculumsByCourse($courseId, $nested);

        $message = $nested 
            ? 'Kurikulum dengan struktur nested berhasil diambil'
            : 'Daftar kurikulum berhasil diambil';

        return $this->successResponse($curriculums, $message);
    }

    /**
     * Tambah kurikulum baru
     * 
     * Request body dapat include:
     * - parent_section: "1" atau "1.1" untuk membuat sub-bab
     * - section: Custom section number (optional, auto-generated if not provided)
     */
    public function store(StoreCurriculumRequest $request, int $courseId): JsonResponse
    {
        // Pastikan course ada
        $course = Course::findOrFail($courseId);

        // Cek akses dengan Policy
        $this->authorize('update', $course);

        $data = $request->validated();

        // Handle parent_section untuk auto-generate section
        if ($request->has('parent_section') && !isset($data['section'])) {
            $parentSection = $request->input('parent_section');
            $data['section'] = $this->curriculumService->generateNextSection($courseId, $parentSection);
        }

        $curriculum = $this->curriculumService->createCurriculum(
            $courseId,
            $data
        );

        // Load level and is_parent attributes
        $curriculum = $curriculum->fresh();

        return $this->createdResponse($curriculum, 'Kurikulum berhasil ditambahkan');
    }

    /**
     * Tambah banyak kurikulum sekaligus (Bulk Create)
     */
    public function bulkStore(Request $request, int $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('update', $course);

        $request->validate([
            'curriculums' => 'required|array|min:1',
            'curriculums.*.title' => 'required|string|max:255',
            'curriculums.*.section' => 'nullable|string|max:255',
            'curriculums.*.section_order' => 'nullable|integer|min:0',
            'curriculums.*.description' => 'nullable|string',
            'curriculums.*.duration' => 'nullable|string|max:100',
            'curriculums.*.order' => 'nullable|integer|min:0',
        ]);

        $created = [];
        foreach ($request->curriculums as $index => $data) {
            // Set order otomatis jika tidak diberikan
            if (!isset($data['order'])) {
                $data['order'] = $index + 1;
            }
            $created[] = $this->curriculumService->createCurriculum($courseId, $data);
        }

        return $this->createdResponse($created, count($created) . ' kurikulum berhasil ditambahkan');
    }

    /**
     * Tampilkan detail kurikulum
     */
    public function show(int $courseId, int $id): JsonResponse
    {
        Course::findOrFail($courseId);
        
        $curriculum = CourseCurriculum::where('course_id', $courseId)
            ->findOrFail($id);

        return $this->successResponse($curriculum, 'Detail kurikulum berhasil diambil');
    }

    /**
     * Update kurikulum
     */
    public function update(UpdateCurriculumRequest $request, int $courseId, int $id): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        // Cek akses dengan Policy
        $this->authorize('update', $course);

        $curriculum = CourseCurriculum::where('course_id', $courseId)
            ->findOrFail($id);

        $curriculum = $this->curriculumService->updateCurriculum(
            $curriculum,
            $request->validated()
        );

        return $this->successResponse($curriculum, 'Kurikulum berhasil diupdate');
    }

    /**
     * Hapus kurikulum
     */
    public function destroy(int $courseId, int $id): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        // Cek akses dengan Policy
        $this->authorize('delete', $course);

        $curriculum = CourseCurriculum::where('course_id', $courseId)
            ->findOrFail($id);

        $this->curriculumService->deleteCurriculum($curriculum);

        return $this->successResponse(null, 'Kurikulum berhasil dihapus');
    }

    /**
     * Reorder kurikulum
     */
    public function reorder(Request $request, int $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        // Cek akses dengan Policy
        $this->authorize('update', $course);

        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:course_curriculums,id',
        ]);

        $this->curriculumService->reorderCurriculums(
            $courseId,
            $request->ordered_ids
        );

        return $this->successResponse(null, 'Urutan kurikulum berhasil diupdate');
    }
}
