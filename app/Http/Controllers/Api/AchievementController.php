<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Services\AchievementService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Import Request Classes
use App\Http\Requests\Achievement\StoreAchievementRequest;
use App\Http\Requests\Achievement\UpdateAchievementRequest;

/**
 * ==========================================================================
 * ACHIEVEMENT CONTROLLER (Controller untuk Prestasi)
 * ==========================================================================
 * 
 * FUNGSI: Mengelola data prestasi pengguna.
 * 
 * STRUKTUR CLEAN CODE:
 * - Controller  : Hanya handle request/response (file ini)
 * - Service     : Business logic → app/Services/AchievementService.php
 * - Policy      : Authorization  → app/Policies/AchievementPolicy.php
 * - Request     : Validation     → app/Http/Requests/Achievement/
 */
class AchievementController extends Controller
{
    use ApiResponse;

    /**
     * Service untuk business logic
     */
    protected AchievementService $achievementService;

    /**
     * Constructor - Inject service
     */
    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tampilkan daftar prestasi user yang login
     */
    public function index(Request $request): JsonResponse
    {
        $achievements = $this->achievementService->getUserAchievements(
            Auth::id(),
            $request->all()
        );

        return $this->paginatedResponse($achievements, 'Daftar prestasi berhasil diambil');
    }

    /**
     * Tampilkan detail prestasi
     */
    public function show(int $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('view', $achievement);

        return $this->successResponse($achievement, 'Detail prestasi berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tambah prestasi baru
     * 
     * Validasi di: app/Http/Requests/Achievement/StoreAchievementRequest.php
     */
    public function store(StoreAchievementRequest $request): JsonResponse
    {
        // Cek akses dengan Policy
        $this->authorize('create', Achievement::class);

        $achievement = $this->achievementService->createAchievement(
            $request->validated(),
            Auth::user()
        );

        return $this->createdResponse($achievement, 'Prestasi berhasil ditambahkan');
    }

    /**
     * Update prestasi
     * 
     * Validasi di: app/Http/Requests/Achievement/UpdateAchievementRequest.php
     */
    public function update(UpdateAchievementRequest $request, int $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('update', $achievement);

        $achievement = $this->achievementService->updateAchievement(
            $achievement,
            $request->validated()
        );

        return $this->successResponse($achievement, 'Prestasi berhasil diupdate');
    }

    /**
     * Hapus prestasi
     */
    public function destroy(int $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);

        // Cek akses dengan Policy
        $this->authorize('delete', $achievement);

        $this->achievementService->deleteAchievement($achievement);

        return $this->successResponse(null, 'Prestasi berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Lihat prestasi user lain (untuk profil publik)
     */
    public function userAchievements(int $userId, Request $request): JsonResponse
    {
        $achievements = $this->achievementService->getUserAchievements(
            $userId,
            $request->all()
        );

        return $this->paginatedResponse($achievements, 'Prestasi user berhasil diambil');
    }

    /**
     * Statistik prestasi user yang login
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->achievementService->getStatistics(Auth::id());

        return $this->successResponse($stats, 'Statistik prestasi berhasil diambil');
    }

    /*
    |--------------------------------------------------------------------------
    | Certificate Upload Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Upload sertifikat untuk prestasi
     *
     * Endpoint: POST|PUT /api/achievements/{id}/certificate
     */
    public function uploadCertificate(Request $request, int $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);
        $this->authorize('update', $achievement);

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
                if ($achievement->certificate_url && Storage::disk('public')->exists($achievement->certificate_url)) {
                    Storage::disk('public')->delete($achievement->certificate_url);
                }

                // Simpan sertifikat baru
                $path = $request->file('certificate')->store('certificates/achievements', 'public');
                $achievement->certificate_url = $path;
                $achievement->save();

                Log::info('Achievement certificate uploaded', [
                    'achievement_id' => $achievement->id,
                    'user_id' => Auth::id(),
                    'path' => $path,
                ]);
            }

            return $this->successResponse([
                'id' => $achievement->id,
                'title' => $achievement->title,
                'certificate_url' => $achievement->certificate_url,
                'certificate_full_url' => $achievement->certificate_url ? asset('storage/' . $achievement->certificate_url) : null,
            ], 'Sertifikat prestasi berhasil diupload');

        } catch (\Exception $e) {
            Log::error('Achievement certificate upload failed', [
                'achievement_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal mengupload sertifikat');
        }
    }

    /**
     * Hapus sertifikat prestasi
     *
     * Endpoint: DELETE /api/achievements/{id}/certificate
     */
    public function deleteCertificate(int $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);
        $this->authorize('update', $achievement);

        try {
            if ($achievement->certificate_url) {
                if (Storage::disk('public')->exists($achievement->certificate_url)) {
                    Storage::disk('public')->delete($achievement->certificate_url);
                }

                $achievement->certificate_url = null;
                $achievement->save();

                Log::info('Achievement certificate deleted', [
                    'achievement_id' => $achievement->id,
                    'user_id' => Auth::id(),
                ]);
            }

            return $this->successResponse(null, 'Sertifikat prestasi berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Achievement certificate deletion failed', [
                'achievement_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal menghapus sertifikat');
        }
    }
}
