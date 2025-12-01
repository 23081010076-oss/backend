<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Services\ScholarshipService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Import Request Classes
use App\Http\Requests\Scholarship\StoreScholarshipRequest;
use App\Http\Requests\Scholarship\UpdateScholarshipRequest;
use App\Http\Requests\Scholarship\ApplyScholarshipRequest;

/**
 * ==========================================================================
 * SCHOLARSHIP CONTROLLER (Controller untuk Beasiswa)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola beasiswa dan lamaran beasiswa.
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/ScholarshipService.php
 * - Policy      : Authorization  → app/Policies/ScholarshipPolicy.php
 * - Request     : Validation     → app/Http/Requests/Scholarship/
 */
class ScholarshipController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected ScholarshipService $scholarshipService;

    /**
     * Constructor - Inject service
     */
    public function __construct(ScholarshipService $scholarshipService)
    {
        $this->scholarshipService = $scholarshipService;
    }

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan daftar beasiswa dengan filter
     */
    public function index(Request $request): JsonResponse
    {
        $scholarships = $this->scholarshipService->getScholarships($request->all());

        return $this->paginatedResponse($scholarships, 'Daftar beasiswa berhasil diambil');
    }

    /**
     * Tampilkan detail beasiswa
     */
    public function show(int $id): JsonResponse
    {
        $scholarship = Scholarship::with(['organization', 'applications'])->findOrFail($id);

        return $this->successResponse($scholarship, 'Detail beasiswa berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tambah beasiswa baru
     * 
     * Validasi di: app/Http/Requests/Scholarship/StoreScholarshipRequest.php
     */
    public function store(StoreScholarshipRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', Scholarship::class);

        $scholarship = $this->scholarshipService->createScholarship($request->validated());

        return $this->createdResponse(
            $scholarship->load('organization'),
            'Beasiswa berhasil ditambahkan'
        );
    }

    /**
     * Update beasiswa
     * 
     * Validasi di: app/Http/Requests/Scholarship/UpdateScholarshipRequest.php
     */
    public function update(UpdateScholarshipRequest $request, int $id): JsonResponse
    {
        $scholarship = Scholarship::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $scholarship);

        $scholarship = $this->scholarshipService->updateScholarship(
            $scholarship,
            $request->validated()
        );

        return $this->successResponse($scholarship, 'Beasiswa berhasil diupdate');
    }

    /**
     * Hapus beasiswa
     */
    public function destroy(int $id): JsonResponse
    {
        $scholarship = Scholarship::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('delete', $scholarship);

        $this->scholarshipService->deleteScholarship($scholarship);

        return $this->successResponse(null, 'Beasiswa berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | Application Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Melamar beasiswa
     * 
     * Validasi di: app/Http/Requests/Scholarship/ApplyScholarshipRequest.php
     */
    public function apply(ApplyScholarshipRequest $request, int $id): JsonResponse
    {
        $scholarship = Scholarship::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('apply', $scholarship);

        $result = $this->scholarshipService->applyScholarship(
            auth()->user(),
            $scholarship,
            $request->validated(),
            $request->allFiles()
        );

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 422);
        }

        return $this->createdResponse($result['data'], 'Lamaran beasiswa berhasil dikirim');
    }

    /**
     * Lihat lamaran beasiswa user
     */
    public function myApplications(): JsonResponse
    {
        $applications = $this->scholarshipService->getUserApplications(auth()->id());

        return $this->paginatedResponse($applications, 'Daftar lamaran berhasil diambil');
    }

    /**
     * Update status lamaran (admin only)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $application = ScholarshipApplication::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('updateApplicationStatus', $application->scholarship);

        $validated = $request->validate([
            'status' => 'required|in:submitted,review,accepted,rejected',
        ], [
            'status.required' => 'Status harus diisi',
            'status.in'       => 'Status harus salah satu dari: submitted, review, accepted, rejected',
        ]);

        $application = $this->scholarshipService->updateApplicationStatus(
            $application,
            $validated['status']
        );

        return $this->successResponse($application, 'Status lamaran berhasil diupdate');
    }
}
