<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Services\ExperienceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
