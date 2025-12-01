<?php

namespace App\Services;

use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

/**
 * ==========================================================================
 * SCHOLARSHIP SERVICE (Service untuk Beasiswa)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk beasiswa dan lamaran.
 * 
 * KENAPA PAKAI SERVICE?
 * - Logika upload file dokumen terpusat
 * - Validasi bisnis ada di sini
 * - Controller tetap ringkas
 */
class ScholarshipService
{
    /**
     * Ambil daftar beasiswa dengan filter
     */
    public function getScholarships(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Scholarship::with(['organization']);

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter berdasarkan lokasi
        if (!empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        // Filter berdasarkan bidang studi
        if (!empty($filters['study_field'])) {
            $query->where('study_field', 'like', '%' . $filters['study_field'] . '%');
        }

        // Pencarian
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Buat beasiswa baru
     */
    public function createScholarship(array $data): Scholarship
    {
        $scholarship = Scholarship::create($data);
        
        return $scholarship->load('organization');
    }

    /**
     * Update beasiswa
     */
    public function updateScholarship(Scholarship $scholarship, array $data): Scholarship
    {
        $scholarship->update($data);
        
        return $scholarship->fresh()->load('organization');
    }

    /**
     * Hapus beasiswa
     */
    public function deleteScholarship(Scholarship $scholarship): bool
    {
        return $scholarship->delete();
    }

    /**
     * Lamar beasiswa
     * 
     * @throws \Exception jika validasi gagal
     */
    public function applyScholarship(Scholarship $scholarship, User $user, array $files = []): ScholarshipApplication
    {
        // Validasi: beasiswa harus open
        if ($scholarship->status !== 'open') {
            throw new \Exception('Beasiswa ini tidak sedang menerima lamaran');
        }

        // Validasi: belum pernah melamar
        $existing = ScholarshipApplication::where('user_id', $user->id)
            ->where('scholarship_id', $scholarship->id)
            ->first();

        if ($existing) {
            throw new \Exception('Anda sudah pernah melamar beasiswa ini');
        }

        $data = [
            'user_id'        => $user->id,
            'scholarship_id' => $scholarship->id,
            'status'         => 'submitted',
            'submitted_at'   => now(),
        ];

        // Handle upload file dokumen
        if (!empty($files['motivation_letter'])) {
            $data['motivation_letter'] = $files['motivation_letter']->store('scholarship-docs', 'public');
        }
        if (!empty($files['cv_path'])) {
            $data['cv_path'] = $files['cv_path']->store('scholarship-docs', 'public');
        }
        if (!empty($files['transcript_path'])) {
            $data['transcript_path'] = $files['transcript_path']->store('scholarship-docs', 'public');
        }
        if (!empty($files['recommendation_path'])) {
            $data['recommendation_path'] = $files['recommendation_path']->store('scholarship-docs', 'public');
        }

        return ScholarshipApplication::create($data);
    }

    /**
     * Ambil lamaran user
     */
    public function getUserApplications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return ScholarshipApplication::with('scholarship')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Update status lamaran
     */
    public function updateApplicationStatus(ScholarshipApplication $application, string $status): ScholarshipApplication
    {
        $application->update(['status' => $status]);
        
        return $application->fresh();
    }

    /**
     * Ambil statistik beasiswa
     */
    public function getStatistics(): array
    {
        return [
            'total'       => Scholarship::count(),
            'open'        => Scholarship::where('status', 'open')->count(),
            'coming_soon' => Scholarship::where('status', 'coming_soon')->count(),
            'closed'      => Scholarship::where('status', 'closed')->count(),
            'applications' => [
                'total'    => ScholarshipApplication::count(),
                'submitted'=> ScholarshipApplication::where('status', 'submitted')->count(),
                'review'   => ScholarshipApplication::where('status', 'review')->count(),
                'accepted' => ScholarshipApplication::where('status', 'accepted')->count(),
                'rejected' => ScholarshipApplication::where('status', 'rejected')->count(),
            ],
        ];
    }
}
