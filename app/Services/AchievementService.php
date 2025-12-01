<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * ACHIEVEMENT SERVICE (Service untuk Prestasi)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk manajemen prestasi.
 * 
 * KENAPA PAKAI SERVICE?
 * - Agar controller tetap bersih dan ringkas
 * - Logika bisnis terpusat di satu tempat
 * - Mudah di-test dan di-maintain
 */
class AchievementService
{
    /**
     * Ambil daftar prestasi user dengan filter
     */
    public function getUserAchievements(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Achievement::where('user_id', $userId);

        // Filter berdasarkan tipe
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    /**
     * Buat prestasi baru
     */
    public function createAchievement(array $data, User $user): Achievement
    {
        $data['user_id'] = $user->id;
        
        return Achievement::create($data);
    }

    /**
     * Update prestasi
     */
    public function updateAchievement(Achievement $achievement, array $data): Achievement
    {
        $achievement->update($data);
        
        return $achievement->fresh();
    }

    /**
     * Hapus prestasi
     */
    public function deleteAchievement(Achievement $achievement): bool
    {
        return $achievement->delete();
    }

    /**
     * Ambil statistik prestasi user
     */
    public function getStatistics(int $userId): array
    {
        return [
            'total'        => Achievement::where('user_id', $userId)->count(),
            'certificates' => Achievement::where('user_id', $userId)->where('type', 'certificate')->count(),
            'awards'       => Achievement::where('user_id', $userId)->where('type', 'award')->count(),
            'publications' => Achievement::where('user_id', $userId)->where('type', 'publication')->count(),
            'projects'     => Achievement::where('user_id', $userId)->where('type', 'project')->count(),
            'other'        => Achievement::where('user_id', $userId)->where('type', 'other')->count(),
        ];
    }

    /**
     * Ambil prestasi terbaru user
     */
    public function getRecentAchievements(int $userId, int $limit = 5): Collection
    {
        return Achievement::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }
}
