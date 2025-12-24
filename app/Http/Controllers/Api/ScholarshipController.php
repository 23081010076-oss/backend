<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Services\ScholarshipService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $scholarship = Scholarship::with(['organization'])
            ->withCount('applications')
            ->findOrFail($id);

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

        try {
            $application = $this->scholarshipService->applyScholarship(
                $scholarship,
                Auth::user(),
                $request->allFiles()
            );

            return $this->createdResponse($application, 'Lamaran beasiswa berhasil dikirim');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Lihat lamaran beasiswa user
     */
    public function myApplications(): JsonResponse
    {
        $applications = $this->scholarshipService->getUserApplications(Auth::id());

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
            'status' => 'required|in:draft,submitted,review,accepted,rejected',
        ], [
            'status.required' => 'Status harus diisi',
            'status.in'       => 'Status harus salah satu dari: draft, submitted, review, accepted, rejected',
        ]);

        $application = $this->scholarshipService->updateApplicationStatus(
            $application,
            $validated['status']
        );

        return $this->successResponse($application, 'Status lamaran berhasil diupdate');
    }

    /*
    |--------------------------------------------------------------------------
    | Scholarship Application Flow Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Step 1: Simpan draft lamaran dengan dokumen
     */
    public function saveDraft(ApplyScholarshipRequest $request, int $id): JsonResponse
    {
        $scholarship = Scholarship::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('apply', $scholarship);

        try {
            $application = $this->scholarshipService->saveDraft(
                $scholarship,
                Auth::user(),
                $request->allFiles(),
                $request->only(['motivation_letter_text'])
            );

            return $this->createdResponse($application, 'Draft lamaran berhasil disimpan');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Step 2: Update pre-assessment data
     */
    public function updateAssessment(Request $request, int $id): JsonResponse
    {
        $application = ScholarshipApplication::findOrFail($id);

        // Pastikan user adalah pemilik aplikasi
        if ($application->user_id !== Auth::id()) {
            return $this->errorResponse('Anda tidak memiliki akses ke lamaran ini', 403);
        }

        $validated = $request->validate([
            'gpa'                  => 'nullable|numeric|min:0|max:4',
            'has_other_scholarship'=> 'nullable|boolean',
            'parent_income'        => 'nullable|integer|min:0',
            'university'           => 'nullable|string|max:255',
        ], [
            'gpa.numeric'   => 'GPA harus berupa angka',
            'gpa.min'       => 'GPA minimal 0',
            'gpa.max'       => 'GPA maksimal 4',
            'parent_income.integer' => 'Penghasilan orang tua harus berupa angka',
            'parent_income.min'     => 'Penghasilan orang tua tidak boleh negatif',
        ]);

        try {
            $application = $this->scholarshipService->updateAssessment($application, $validated);
            return $this->successResponse($application, 'Data pre-assessment berhasil disimpan');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Step 3: Get application detail untuk review
     */
    public function getApplication(int $id): JsonResponse
    {
        $application = $this->scholarshipService->getApplication($id, Auth::id());

        if (!$application) {
            return $this->errorResponse('Lamaran tidak ditemukan', 404);
        }

        return $this->successResponse($application, 'Detail lamaran berhasil diambil');
    }

    /**
     * Step 3b: Update draft lamaran
     */
    public function updateDraft(ApplyScholarshipRequest $request, int $id): JsonResponse
    {
        $application = ScholarshipApplication::findOrFail($id);

        // Pastikan user adalah pemilik aplikasi
        if ($application->user_id !== Auth::id()) {
            return $this->errorResponse('Anda tidak memiliki akses ke lamaran ini', 403);
        }

        try {
            $application = $this->scholarshipService->updateDraft(
                $application,
                $request->allFiles(),
                $request->only(['motivation_letter_text'])
            );

            return $this->successResponse($application, 'Draft lamaran berhasil diupdate');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Step 4: Submit draft menjadi lamaran resmi
     */
    public function submitApplication(int $id): JsonResponse
    {
        $application = ScholarshipApplication::findOrFail($id);

        // Pastikan user adalah pemilik aplikasi
        if ($application->user_id !== Auth::id()) {
            return $this->errorResponse('Anda tidak memiliki akses ke lamaran ini', 403);
        }

        try {
            $application = $this->scholarshipService->submitApplication($application);
            return $this->successResponse($application, 'Lamaran berhasil dikirim');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
