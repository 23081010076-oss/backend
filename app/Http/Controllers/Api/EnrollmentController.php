<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// Import Request Classes
use App\Http\Requests\Enrollment\UpdateEnrollmentRequest;
use App\Http\Requests\Enrollment\UpdateProgressRequest;

/**
 * ==========================================================================
 * ENROLLMENT CONTROLLER (Controller untuk Enrollment/Pendaftaran Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola pendaftaran kursus.
 * 
 * VALIDASI:
 * - UpdateEnrollmentRequest = untuk update enrollment
 * - UpdateProgressRequest   = untuk update progress
 * 
 * Lihat file validasi di: app/Http/Requests/Enrollment/
 */
class EnrollmentController extends Controller
{
    use ApiResponse;

    /**
     * @var EnrollmentService
     */
    protected EnrollmentService $enrollmentService;

    /**
     * Constructor
     */
    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Tampilkan daftar enrollment
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Enrollment::class);

        $enrollments = Enrollment::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($enrollments, 'Daftar enrollment berhasil diambil');
    }

    /**
     * Daftar ke kursus
     */
    public function enroll(int $courseId): JsonResponse
    {
        $this->authorize('create', Enrollment::class);

        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        try {
            $enrollment = $this->enrollmentService->enrollUserToCourse($user, $course);

            return $this->createdResponse(
                $enrollment->load('course'),
                'Berhasil mendaftar ke kursus'
            );
        } catch (\InvalidArgumentException $e) {
            $statusCode = str_contains($e->getMessage(), 'already enrolled') ? 422 : 403;
            return $this->errorResponse($e->getMessage(), $statusCode);
        } catch (\Exception $e) {
            Log::error('Enrollment failed in controller', [
                'user_id' => $user->id,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal mendaftar ke kursus');
        }
    }

    /**
     * Lihat kursus yang diikuti user
     */
    public function myCourses(): JsonResponse
    {
        $user = Auth::user();
        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginatedResponse($enrollments, 'Kursus Anda berhasil diambil');
    }

    /**
     * Update progress enrollment
     * 
     * Validasi di: app/Http/Requests/Enrollment/UpdateProgressRequest.php
     */
    public function updateProgress(UpdateProgressRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $enrollment = Enrollment::findOrFail($id);
        $this->authorize('updateProgress', $enrollment);

        try {
            $enrollment = $this->enrollmentService->updateProgress(
                $enrollment,
                $validated['progress']
            );

            return $this->successResponse($enrollment, 'Progress berhasil diupdate');
        } catch (\Exception $e) {
            Log::error('Progress update failed in controller', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal mengupdate progress');
        }
    }

    /**
     * Tampilkan detail enrollment
     */
    public function show(int $id): JsonResponse
    {
        $enrollment = Enrollment::with(['user', 'course'])->findOrFail($id);
        $this->authorize('view', $enrollment);

        return $this->successResponse($enrollment, 'Detail enrollment berhasil diambil');
    }

    /**
     * Update enrollment
     * 
     * Validasi di: app/Http/Requests/Enrollment/UpdateEnrollmentRequest.php
     */
    public function update(UpdateEnrollmentRequest $request, int $id): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $this->authorize('update', $enrollment);

        $validated = $request->validated();

        try {
            $enrollment->update($validated);

            return $this->successResponse($enrollment->fresh(), 'Enrollment berhasil diupdate');
        } catch (\Exception $e) {
            Log::error('Enrollment update failed in controller', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal mengupdate enrollment');
        }
    }

    /**
     * Hapus enrollment
     */
    public function destroy(int $id): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $this->authorize('delete', $enrollment);

        try {
            $enrollment->delete();
            return $this->successResponse(null, 'Enrollment berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Enrollment deletion failed in controller', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal menghapus enrollment');
        }
    }

    /**
     * Mark a curriculum item as completed
     * Progress will be auto-calculated
     */
    public function markCurriculumCompleted(int $enrollmentId, int $curriculumId): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        
        // Ensure user owns this enrollment
        if ($enrollment->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Anda tidak memiliki akses ke enrollment ini');
        }

        // Verify curriculum belongs to the course
        $course = $enrollment->course;
        $curriculumExists = $course->curriculums()->where('id', $curriculumId)->exists();
        
        if (!$curriculumExists) {
            return $this->notFoundResponse('Materi tidak ditemukan dalam kursus ini');
        }

        try {
            $progress = $enrollment->markCurriculumCompleted($curriculumId);

            return $this->successResponse([
                'curriculum_progress' => $progress,
                'enrollment' => $enrollment->fresh(),
            ], 'Materi berhasil ditandai selesai');
        } catch (\Exception $e) {
            Log::error('Mark curriculum completed failed', [
                'enrollment_id' => $enrollmentId,
                'curriculum_id' => $curriculumId,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal menandai materi selesai');
        }
    }
}
