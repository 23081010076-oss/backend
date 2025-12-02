<?php

namespace App\Services;

use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ==========================================================================
 * MENTORING SERVICE (Service untuk Sesi Mentoring)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk sesi mentoring.
 * 
 * KENAPA PAKAI SERVICE?
 * - Logika jadwal, status, feedback terpusat
 * - Controller tetap ringkas
 * - Mudah di-test
 */
class MentoringService
{
    /**
     * Ambil sesi mentoring berdasarkan user/mentor
     */
    public function getSessions(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = MentoringSession::with(['member', 'mentor']);

        // Filter berdasarkan role user
        if ($user->role === 'mentor') {
            $query->where('mentor_id', $user->id);
        } else {
            $query->where('member_id', $user->id);
        }

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('schedule', 'desc')->paginate($perPage);
    }

    /**
     * Buat sesi mentoring baru
     */
    public function createSession(array $data, User $user): MentoringSession
    {
        $data['member_id'] = $user->id;
        $data['status'] = 'pending';

        $session = MentoringSession::create($data);
        
        return $session->load(['member', 'mentor']);
    }

    /**
     * Update sesi mentoring
     */
    public function updateSession(MentoringSession $session, array $data): MentoringSession
    {
        $session->update($data);
        
        return $session->fresh()->load(['member', 'mentor']);
    }

    /**
     * Hapus sesi mentoring
     */
    public function deleteSession(MentoringSession $session): bool
    {
        return $session->delete();
    }

    /**
     * Update status sesi
     */
    public function updateStatus(MentoringSession $session, string $status): MentoringSession
    {
        $session->update(['status' => $status]);
        
        return $session->fresh()->load(['member', 'mentor']);
    }

    /**
     * Berikan feedback
     * 
     * @throws \Exception jika sesi belum selesai
     */
    public function giveFeedback(MentoringSession $session, array $feedbackData): MentoringSession
    {
        if ($session->status !== 'completed') {
            throw new \Exception('Feedback hanya bisa diberikan untuk sesi yang sudah selesai');
        }

        $session->update($feedbackData);
        
        return $session->fresh()->load(['member', 'mentor']);
    }

    /**
     * Ambil jadwal mentor
     */
    public function getMentorSchedule(int $mentorId, ?string $fromDate = null, ?string $toDate = null): Collection
    {
        $query = MentoringSession::where('mentor_id', $mentorId)
            ->where('status', '!=', 'cancelled');

        if ($fromDate) {
            $query->whereDate('schedule', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('schedule', '<=', $toDate);
        }

        return $query->orderBy('schedule', 'asc')
            ->get(['id', 'schedule', 'type', 'status']);
    }

    /**
     * Cek ketersediaan jadwal mentor
     */
    public function checkAvailability(int $mentorId, string $date, string $time): bool
    {
        $exists = MentoringSession::where('mentor_id', $mentorId)
            ->whereDate('schedule', $date)
            ->whereTime('schedule', $time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        return !$exists;
    }

    /**
     * Ambil sesi mendatang
     */
    public function getUpcomingSessions(int $userId, int $limit = 5): Collection
    {
        return MentoringSession::with(['member', 'mentor'])
            ->where(function ($query) use ($userId) {
                $query->where('member_id', $userId)
                      ->orWhere('mentor_id', $userId);
            })
            ->where('schedule', '>=', now())
            ->whereIn('status', ['scheduled', 'pending'])
            ->orderBy('schedule', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Ambil statistik mentoring
     */
    public function getStatistics(?int $userId = null): array
    {
        $query = MentoringSession::query();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('member_id', $userId)
                  ->orWhere('mentor_id', $userId);
            });
        }

        return [
            'total'     => (clone $query)->count(),
            'pending'   => (clone $query)->where('status', 'pending')->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
        ];
    }
}
