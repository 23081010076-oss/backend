<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Import Request Classes
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;

/**
 * ==========================================================================
 * COURSE CONTROLLER (Controller untuk Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola kursus dan bootcamp.
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/CourseService.php
 * - Policy      : Authorization  → app/Policies/CoursePolicy.php
 * - Request     : Validation     → app/Http/Requests/Course/
 */
class CourseController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected CourseService $courseService;

    /**
     * Constructor - Inject service
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Tampilkan daftar kursus dengan filter
     */
    public function index(Request $request): JsonResponse
    {
        $courses = $this->courseService->getCourses($request->all());

        return $this->paginatedResponse($courses, 'Daftar kursus berhasil diambil');
    }

    /**
     * Tambah kursus baru
     * 
     * Validasi di: app/Http/Requests/Course/StoreCourseRequest.php
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', Course::class);

        $course = $this->courseService->createCourse($request->validated());

        return $this->createdResponse($course, 'Kursus berhasil ditambahkan');
    }

    /**
     * Tampilkan detail kursus
     */
    public function show(int $id): JsonResponse
    {
        $course = $this->courseService->getCourseWithDetails($id);

        return $this->successResponse($course, 'Detail kursus berhasil diambil');
    }

    /**
     * Update kursus
     * 
     * Validasi di: app/Http/Requests/Course/UpdateCourseRequest.php
     */
    public function update(UpdateCourseRequest $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $course);

        $course = $this->courseService->updateCourse(
            $course,
            $request->validated(),
            $request->file('video_file')
        );

        return $this->successResponse($course, 'Kursus berhasil diupdate');
    }

    /**
     * Hapus kursus
     */
    public function destroy(int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('delete', $course);

        $this->courseService->deleteCourse($course);

        return $this->successResponse(null, 'Kursus berhasil dihapus');
    }
}
