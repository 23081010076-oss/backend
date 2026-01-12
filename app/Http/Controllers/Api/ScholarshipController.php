<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\Organization;
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
    public function show($id): JsonResponse
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

        $data = $request->validated();
        
        // Jika user adalah corporate dan tidak ada organization_id, set otomatis
        if (Auth::user()->role === 'corporate' && empty($data['organization_id'])) {
            // Ambil organization pertama milik user (corporate pasti punya organization dari register)
            $organization = Organization::where('user_id', Auth::id())->first();
            
            if (!$organization) {
                return $this->errorResponse('Corporate user harus memiliki organization terlebih dahulu', 400);
            }
            
            $data['organization_id'] = $organization->id;
        }

        $scholarship = $this->scholarshipService->createScholarship($data);

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
     * Get user's application for a specific scholarship
     * 
     * Endpoint: GET /api/scholarships/{id}/my-application
     * 
     * Digunakan untuk:
     * - Mengecek apakah user sudah memiliki application untuk scholarship tertentu
     * - Menentukan step mana yang harus ditampilkan di frontend
     * 
     * Response includes currentStep:
     * - 2: Draft tanpa assessment data → redirect ke Step 2 (Pre-Assessment)
     * - 3: Draft dengan assessment data → redirect ke Step 3 (Review)
     * - 4: Submitted → redirect ke Step 4 (Success)
     */
    public function myApplicationForScholarship(int $id): JsonResponse
    {
        $scholarship = Scholarship::find($id);
        
        if (!$scholarship) {
            return $this->errorResponse('Beasiswa tidak ditemukan', 404);
        }

        $application = $this->scholarshipService->getUserApplicationForScholarship($id, Auth::id());

        if (!$application) {
            return $this->successResponse(null, 'Belum ada lamaran untuk beasiswa ini');
        }

        // Determine current step based on application status and data
        $currentStep = 2; // Default: Step 2 (Pre-Assessment)
        
        if ($application->status === 'submitted' || 
            $application->status === 'review' || 
            $application->status === 'accepted' || 
            $application->status === 'rejected') {
            $currentStep = 4; // Step 4 (Success/Status)
        } elseif ($application->status === 'draft') {
            // Check if assessment data exists
            $hasAssessmentData = $application->gpa !== null || 
                                 $application->has_other_scholarship !== null || 
                                 $application->parent_income !== null || 
                                 $application->university !== null;
            
            $currentStep = $hasAssessmentData ? 3 : 2; // Step 3 (Review) or Step 2 (Pre-Assessment)
        }

        return $this->successResponse([
            'application' => $application,
            'currentStep' => $currentStep,
        ], 'Data lamaran berhasil diambil');
    }

    /**
     * Lihat semua lamaran beasiswa (admin/corporate only)
     */
    public function allApplications(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Corporate hanya bisa melihat aplikasi dari scholarship milik mereka
        $filters = $request->all();
        if ($user->role === 'corporate') {
            // Ambil ID organization milik corporate user
            $organizationIds = Organization::where('user_id', $user->id)->pluck('id')->toArray();
            
            // Ambil ID scholarship yang terkait dengan organization tersebut
            $scholarshipIds = Scholarship::whereIn('organization_id', $organizationIds)->pluck('id')->toArray();
            $filters['scholarship_ids'] = $scholarshipIds;
        }

        $applications = $this->scholarshipService->getAllApplications($filters);

        return $this->paginatedResponse($applications, 'Daftar semua lamaran berhasil diambil');
    }

    /**
     * Lihat detail lamaran beasiswa by ID (admin/corporate only)
     */
    public function showApplicationDetail(int $id): JsonResponse
    {
        $user = Auth::user();
        $application = $this->scholarshipService->getApplicationById($id);

        if (!$application) {
            return $this->errorResponse('Lamaran tidak ditemukan', 404);
        }

        // Corporate hanya bisa melihat aplikasi dari scholarship milik mereka
        if ($user->role === 'corporate') {
            // Ambil ID organization milik corporate user
            $organizationIds = Organization::where('user_id', $user->id)->pluck('id')->toArray();
            
            // Cek apakah scholarship terkait dengan organization milik corporate
            $scholarship = Scholarship::where('id', $application->scholarship_id)
                ->whereIn('organization_id', $organizationIds)
                ->first();
            
            if (!$scholarship) {
                return $this->errorResponse('Anda tidak memiliki akses ke lamaran ini', 403);
            }
        }

        return $this->successResponse($application, 'Detail lamaran berhasil diambil');
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

    /**
     * Beasiswa milik user yang login (untuk corporate melihat beasiswa sendiri)
     */
    public function myScholarships(Request $request): JsonResponse
    {
        // Hanya corporate yang bisa membuat beasiswa
        if (Auth::user()->role !== 'corporate') {
            return $this->errorResponse('Hanya corporate yang memiliki beasiswa', 403);
        }

        // Ambil ID organization milik corporate user
        $organizationIds = Organization::where('user_id', Auth::id())->pluck('id')->toArray();
        
        $scholarships = Scholarship::whereIn('organization_id', $organizationIds)
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($scholarships, 'Beasiswa Anda berhasil diambil');
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
                $request->only(['motivation_letter_text', 'cv_from_profile'])
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
                $request->only(['motivation_letter_text', 'cv_from_profile']),
                Auth::user()
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

    /**
     * Rekomendasi beasiswa berdasarkan specialization user dan yang terbaru
     * 
     * Endpoint: GET /api/scholarships/recommendations
     * 
     * Algoritma:
     * 1. Beasiswa yang masih open (status = 'open')
     * 2. Beasiswa yang terbaru (berdasarkan created_at DESC)
     * 3. Relevansi dengan specialization user (study_field match)
     * 4. Exclude beasiswa yang sudah dilamar
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->input('limit', 5);
        
        $recommendations = $this->scholarshipService->getRecommendations($user, $limit);
        
        return $this->successResponse($recommendations, 'Rekomendasi beasiswa berhasil diambil');
    }
}
