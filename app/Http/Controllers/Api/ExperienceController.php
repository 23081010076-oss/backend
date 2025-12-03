<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Services\ExperienceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Import Request Classes
use App\Http\Requests\Experience\StoreExperienceRequest;
use App\Http\Requests\Experience\UpdateExperienceRequest;

/**
 * ==========================================================================
 * EXPERIENCE CONTROLLER (Controller untuk Pengalaman)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola pengalaman kerja/pendidikan user.
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/ExperienceService.php
 * - Policy      : Authorization  → app/Policies/ExperiencePolicy.php
 * - Request     : Validation     → app/Http/Requests/Experience/
 */
class ExperienceController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected ExperienceService $experienceService;

    /**
     * Constructor - Inject service
     */
    public function __construct(ExperienceService $experienceService)
    {
        $this->experienceService = $experienceService;
    }

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan daftar pengalaman user yang login
     */
    public function index(Request $request): JsonResponse
    {
        $experiences = $this->experienceService->getUserExperiences(
            Auth::id(),
            $request->all()
        );

        return $this->paginatedResponse($experiences, 'Daftar pengalaman berhasil diambil');
    }

    /**
     * Tampilkan detail pengalaman
     */
    public function show(int $id): JsonResponse
    {
        $experience = Experience::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('view', $experience);

        return $this->successResponse($experience, 'Detail pengalaman berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tambah pengalaman baru
     * 
     * Validasi di: app/Http/Requests/Experience/StoreExperienceRequest.php
     */
    public function store(StoreExperienceRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', Experience::class);

        $experience = $this->experienceService->createExperience(
            $request->validated(),
            Auth::user()
        );

        return $this->createdResponse($experience, 'Pengalaman berhasil ditambahkan');
    }

    /**
     * Update pengalaman
     * 
     * Validasi di: app/Http/Requests/Experience/UpdateExperienceRequest.php
     */
    public function update(UpdateExperienceRequest $request, int $id): JsonResponse
    {
        $experience = Experience::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $experience);

        $experience = $this->experienceService->updateExperience(
            $experience,
            $request->validated()
        );

        return $this->successResponse($experience, 'Pengalaman berhasil diupdate');
    }

    /**
     * Hapus pengalaman
     */
    public function destroy(int $id): JsonResponse
    {
        $experience = Experience::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('delete', $experience);

        $this->experienceService->deleteExperience($experience);

        return $this->successResponse(null, 'Pengalaman berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Lihat pengalaman user tertentu (profil publik)
     */
    public function userExperiences(int $userId, Request $request): JsonResponse
    {
        $experiences = $this->experienceService->getUserExperiences(
            $userId,
            $request->all()
        );

        return $this->paginatedResponse($experiences, 'Daftar pengalaman user berhasil diambil');
    }

    /**
     * Pengalaman kerja user yang login
     */
    public function workExperiences(): JsonResponse
    {
        $experiences = $this->experienceService->getWorkExperiences(Auth::id());

        return $this->successResponse($experiences, 'Pengalaman kerja berhasil diambil');
    }

    /**
     * Pengalaman pendidikan user yang login
     */
    public function educationExperiences(): JsonResponse
    {
        $experiences = $this->experienceService->getEducationExperiences(Auth::id());

        return $this->successResponse($experiences, 'Pengalaman pendidikan berhasil diambil');
    }

    /**
     * Statistik pengalaman user yang login
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->experienceService->getStatistics(Auth::id());

        return $this->successResponse($stats, 'Statistik pengalaman berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Certificate Upload Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Upload sertifikat untuk pengalaman
     *
     * Endpoint: POST|PUT /api/experiences/{id}/certificate
     */
    public function uploadCertificate(Request $request, int $id): JsonResponse
    {
        $experience = Experience::findOrFail($id);
        $this->authorize('update', $experience);

        $request->validate([
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'certificate.required' => 'File sertifikat wajib diupload',
            'certificate.file' => 'File tidak valid',
            'certificate.mimes' => 'Format sertifikat harus: pdf, jpg, jpeg, atau png',
            'certificate.max' => 'Ukuran sertifikat maksimal 5MB',
        ]);

        try {
            if ($request->hasFile('certificate')) {
                // Hapus sertifikat lama jika ada
                if ($experience->certificate_url && Storage::disk('public')->exists($experience->certificate_url)) {
                    Storage::disk('public')->delete($experience->certificate_url);
                }

                // Simpan sertifikat baru
                $path = $request->file('certificate')->store('certificates/experiences', 'public');
                $experience->certificate_url = $path;
                $experience->save();

                Log::info('Experience certificate uploaded', [
                    'experience_id' => $experience->id,
                    'user_id' => Auth::id(),
                    'path' => $path,
                ]);
            }

            return $this->successResponse([
                'id' => $experience->id,
                'title' => $experience->title,
                'certificate_url' => $experience->certificate_url,
                'certificate_full_url' => $experience->certificate_url ? asset('storage/' . $experience->certificate_url) : null,
            ], 'Sertifikat pengalaman berhasil diupload');

        } catch (\Exception $e) {
            Log::error('Experience certificate upload failed', [
                'experience_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal mengupload sertifikat');
        }
    }

    /**
     * Hapus sertifikat pengalaman
     *
     * Endpoint: DELETE /api/experiences/{id}/certificate
     */
    public function deleteCertificate(int $id): JsonResponse
    {
        $experience = Experience::findOrFail($id);
        $this->authorize('update', $experience);

        try {
            if ($experience->certificate_url) {
                if (Storage::disk('public')->exists($experience->certificate_url)) {
                    Storage::disk('public')->delete($experience->certificate_url);
                }

                $experience->certificate_url = null;
                $experience->save();

                Log::info('Experience certificate deleted', [
                    'experience_id' => $experience->id,
                    'user_id' => Auth::id(),
                ]);
            }

            return $this->successResponse(null, 'Sertifikat pengalaman berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Experience certificate deletion failed', [
                'experience_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal menghapus sertifikat');
        }
    }
}
