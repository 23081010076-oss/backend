<?php

namespace App\Services;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * EXPERIENCE SERVICE (Service untuk Pengalaman)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk manajemen pengalaman.
 * 
 * KENAPA PAKAI SERVICE?
 * - Logika seperti handle is_current ada di sini
 * - Controller tetap bersih
 * - Mudah dipakai ulang
 */
class ExperienceService
{
    /**
     * Ambil daftar pengalaman user dengan filter
     */
    public function getUserExperiences(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Experience::where('user_id', $userId);

        // Filter berdasarkan tipe
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }

    /**
     * Buat pengalaman baru
     */
    public function createExperience(array $data, User $user): Experience
    {
        $data['user_id'] = $user->id;

        // Jika is_current true, hapus end_date
        if (!empty($data['is_current']) && $data['is_current']) {
            $data['end_date'] = null;
        }

        return Experience::create($data);
    }

    /**
     * Update pengalaman
     */
    public function updateExperience(Experience $experience, array $data): Experience
    {
        // Jika is_current true, hapus end_date
        if (!empty($data['is_current']) && $data['is_current']) {
            $data['end_date'] = null;
        }

        $experience->update($data);

        return $experience->fresh();
    }

    /**
     * Hapus pengalaman
     */
    public function deleteExperience(Experience $experience): bool
    {
        return $experience->delete();
    }

    /**
     * Ambil pengalaman kerja user
     */
    public function getWorkExperiences(int $userId): Collection
    {
        return Experience::where('user_id', $userId)
            ->where('type', 'work')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Ambil pengalaman pendidikan user
     */
    public function getEducationExperiences(int $userId): Collection
    {
        return Experience::where('user_id', $userId)
            ->where('type', 'education')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Ambil statistik pengalaman user
     */
    public function getStatistics(int $userId): array
    {
        return [
            'total'      => Experience::where('user_id', $userId)->count(),
            'work'       => Experience::where('user_id', $userId)->where('type', 'work')->count(),
            'education'  => Experience::where('user_id', $userId)->where('type', 'education')->count(),
            'volunteer'  => Experience::where('user_id', $userId)->where('type', 'volunteer')->count(),
            'internship' => Experience::where('user_id', $userId)->where('type', 'internship')->count(),
            'current'    => Experience::where('user_id', $userId)->where('is_current', true)->count(),
        ];
    }

    /**
     * Ambil pengalaman saat ini (yang sedang berjalan)
     */
    public function getCurrentExperiences(int $userId): Collection
    {
        return Experience::where('user_id', $userId)
            ->where('is_current', true)
            ->orderBy('start_date', 'desc')
            ->get();
    }
}
