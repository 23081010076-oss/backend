<?php

namespace App\Services;

use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

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
     * Ambil daftar beasiswa dengan filter (cached 10 menit)
     */
    public function getScholarships(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        // Generate cache key berdasarkan filter
        $cacheKey = 'scholarships:' . md5(json_encode($filters) . $perPage . request('page', 1));

        // Track cache key untuk bisa di-clear nanti
        $cacheKeys = Cache::get('scholarships:cache_keys', []);
        if (!in_array($cacheKey, $cacheKeys)) {
            $cacheKeys[] = $cacheKey;
            Cache::put('scholarships:cache_keys', $cacheKeys, 86400); // 24 jam
        }

        return Cache::remember($cacheKey, 600, function () use ($filters, $perPage) {
            $query = Scholarship::with(['organization'])
                ->withCount('applications'); // Hitung jumlah aplikasi untuk popularity

            // Filter berdasarkan status
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

             // Filter recommended
            if (!empty($filters['is_recommended'])) {
                $query->where('is_recommended', $filters['is_recommended'] === 'true' || $filters['is_recommended'] === '1');
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

            // Sorting berdasarkan parameter
            $sort = $filters['sort'] ?? 'latest';
            match ($sort) {
                'popular'  => $query->orderByDesc('applications_count'),
                'deadline' => $query->orderBy('deadline', 'asc'),
                default    => $query->latest(),
            };

            return $query->paginate($perPage);
        });
    }

    /**
     * Buat beasiswa baru
     */
    public function createScholarship(array $data): Scholarship
    {
        $scholarship = Scholarship::create($data);

        // Clear cache setelah create
        $this->clearCache();

        return $scholarship->load('organization');
    }

    /**
     * Update beasiswa
     */
    public function updateScholarship(Scholarship $scholarship, array $data): Scholarship
    {
        $scholarship->update($data);

        // Clear cache setelah update
        $this->clearCache();

        return $scholarship->fresh()->load('organization');
    }

    /**
     * Hapus beasiswa
     */
    public function deleteScholarship(Scholarship $scholarship): bool
    {
        $result = $scholarship->delete();

        // Clear cache setelah delete
        $this->clearCache();

        return $result;
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
     * Ambil semua lamaran beasiswa (untuk admin/corporate)
     */
    public function getAllApplications(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ScholarshipApplication::with(['scholarship', 'user']);

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter berdasarkan scholarship_id
        if (!empty($filters['scholarship_id'])) {
            $query->where('scholarship_id', $filters['scholarship_id']);
        }

        // Filter berdasarkan user_id
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter berdasarkan array scholarship_ids (untuk corporate)
        if (!empty($filters['scholarship_ids'])) {
            $query->whereIn('scholarship_id', $filters['scholarship_ids']);
        }

        // Pencarian berdasarkan nama user
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Ambil detail lamaran by ID (untuk admin/corporate)
     */
    public function getApplicationById(int $applicationId): ?ScholarshipApplication
    {
        return ScholarshipApplication::with(['scholarship', 'user'])->find($applicationId);
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
     * Ambil statistik beasiswa (cached 30 menit)
     */
    public function getStatistics(): array
    {
        return Cache::remember('scholarships:statistics', 1800, function () {
            return [
                'total'       => Scholarship::count(),
                'open'        => Scholarship::where('status', 'open')->count(),
                'coming_soon' => Scholarship::where('status', 'coming_soon')->count(),
                'closed'      => Scholarship::where('status', 'closed')->count(),
                'applications' => [
                    'total'    => ScholarshipApplication::count(),
                    'draft'    => ScholarshipApplication::where('status', 'draft')->count(),
                    'submitted'=> ScholarshipApplication::where('status', 'submitted')->count(),
                    'review'   => ScholarshipApplication::where('status', 'review')->count(),
                    'accepted' => ScholarshipApplication::where('status', 'accepted')->count(),
                    'rejected' => ScholarshipApplication::where('status', 'rejected')->count(),
                ],
            ];
        });
    }

    /*
     Scholarship Application Flow Methods
    /**
     * Step 1: Simpan draft lamaran dengan dokumen
     *
     * @throws \Exception jika sudah ada lamaran
     */
    public function saveDraft(Scholarship $scholarship, User $user, array $files = [], array $data = []): ScholarshipApplication
    {
        // Validasi: beasiswa harus open
        if ($scholarship->status !== 'open') {
            throw new \Exception('Beasiswa ini tidak sedang menerima lamaran');
        }

        // Cek apakah sudah ada draft atau lamaran
        $existing = ScholarshipApplication::where('user_id', $user->id)
            ->where('scholarship_id', $scholarship->id)
            ->first();

        if ($existing) {
            throw new \Exception('Anda sudah memiliki lamaran untuk beasiswa ini');
        }

        $applicationData = [
            'user_id'        => $user->id,
            'scholarship_id' => $scholarship->id,
            'status'         => 'draft',
        ];

        // Handle CV from profile
        if (!empty($data['cv_from_profile']) && $data['cv_from_profile'] === true || $data['cv_from_profile'] === 'true' || $data['cv_from_profile'] === '1') {
            if (!empty($user->cv_path)) {
                $applicationData['cv_path'] = $user->cv_path;
            } else {
                throw new \Exception('CV belum tersedia di profil Anda. Silakan upload CV di profil terlebih dahulu.');
            }
        } elseif (!empty($files['cv_path'])) {
            $applicationData['cv_path'] = $files['cv_path']->store('scholarship-docs', 'public');
        }

        // Handle file uploads
        if (!empty($files['transcript_path'])) {
            $applicationData['transcript_path'] = $files['transcript_path']->store('scholarship-docs', 'public');
        }
        if (!empty($files['recommendation_path'])) {
            $applicationData['recommendation_path'] = $files['recommendation_path']->store('scholarship-docs', 'public');
        }
        if (!empty($files['motivation_letter'])) {
            $applicationData['motivation_letter'] = $files['motivation_letter']->store('scholarship-docs', 'public');
        }

        // Handle motivation letter text
        if (!empty($data['motivation_letter_text'])) {
            $applicationData['motivation_letter_text'] = $data['motivation_letter_text'];
        }

        return ScholarshipApplication::create($applicationData);
    }

    /**
     * Step 1b: Update draft dengan dokumen baru
     *
     * @throws \Exception jika bukan draft
     */
    public function updateDraft(ScholarshipApplication $application, array $files = [], array $data = [], ?User $user = null): ScholarshipApplication
    {
        if ($application->status !== 'draft') {
            throw new \Exception('Hanya draft yang bisa diupdate');
        }

        $updateData = [];

        // Handle CV from profile
        if (!empty($data['cv_from_profile']) && ($data['cv_from_profile'] === true || $data['cv_from_profile'] === 'true' || $data['cv_from_profile'] === '1')) {
            $user = $user ?? User::find($application->user_id);
            if (!empty($user->cv_path)) {
                // Don't delete old file if it's from profile (shared file)
                $updateData['cv_path'] = $user->cv_path;
            } else {
                throw new \Exception('CV belum tersedia di profil Anda. Silakan upload CV di profil terlebih dahulu.');
            }
        } elseif (!empty($files['cv_path'])) {
            // Delete old file if exists and it's not a profile CV
            if ($application->cv_path && !$this->isProfileCv($application)) {
                Storage::disk('public')->delete($application->cv_path);
            }
            $updateData['cv_path'] = $files['cv_path']->store('scholarship-docs', 'public');
        }

        // Handle file uploads (replace old files)
        if (!empty($files['transcript_path'])) {
            if ($application->transcript_path) {
                Storage::disk('public')->delete($application->transcript_path);
            }
            $updateData['transcript_path'] = $files['transcript_path']->store('scholarship-docs', 'public');
        }
        if (!empty($files['recommendation_path'])) {
            if ($application->recommendation_path) {
                Storage::disk('public')->delete($application->recommendation_path);
            }
            $updateData['recommendation_path'] = $files['recommendation_path']->store('scholarship-docs', 'public');
        }
        if (!empty($files['motivation_letter'])) {
            if ($application->motivation_letter) {
                Storage::disk('public')->delete($application->motivation_letter);
            }
            $updateData['motivation_letter'] = $files['motivation_letter']->store('scholarship-docs', 'public');
        }

        // Handle motivation letter text
        if (array_key_exists('motivation_letter_text', $data)) {
            $updateData['motivation_letter_text'] = $data['motivation_letter_text'];
        }

        if (!empty($updateData)) {
            $application->update($updateData);
        }

        return $application->fresh();
    }

    /**
     * Check if application CV is from user profile
     */
    private function isProfileCv(ScholarshipApplication $application): bool
    {
        $user = User::find($application->user_id);
        return $user && $user->cv_path === $application->cv_path;
    }

    /**
     * Step 2: Update pre-assessment data
     *
     * @throws \Exception jika bukan draft
     */
    public function updateAssessment(ScholarshipApplication $application, array $data): ScholarshipApplication
    {
        if ($application->status !== 'draft') {
            throw new \Exception('Hanya draft yang bisa diupdate');
        }

        $assessmentData = [];

        if (array_key_exists('gpa', $data)) {
            $assessmentData['gpa'] = $data['gpa'];
        }
        if (array_key_exists('has_other_scholarship', $data)) {
            $assessmentData['has_other_scholarship'] = $data['has_other_scholarship'];
        }
        if (array_key_exists('parent_income', $data)) {
            $assessmentData['parent_income'] = $data['parent_income'];
        }
        if (array_key_exists('university', $data)) {
            $assessmentData['university'] = $data['university'];
        }

        if (!empty($assessmentData)) {
            $application->update($assessmentData);
        }

        return $application->fresh();
    }

    /**
     * Step 3: Get application detail untuk review
     */
    public function getApplication(int $applicationId, int $userId): ?ScholarshipApplication
    {
        return ScholarshipApplication::with('scholarship')
            ->where('id', $applicationId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Step 4: Submit draft menjadi lamaran resmi
     *
     * @throws \Exception jika bukan draft atau dokumen belum lengkap
     */
    public function submitApplication(ScholarshipApplication $application): ScholarshipApplication
    {
        if ($application->status !== 'draft') {
            throw new \Exception('Hanya draft yang bisa disubmit');
        }

        // Validasi minimal dokumen (CV wajib)
        if (empty($application->cv_path)) {
            throw new \Exception('CV harus diupload sebelum submit');
        }

        $application->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return $application->fresh();
    }

    /**
     * Clear semua cache scholarships
     */
    public function clearCache(): void
    {
        // Clear statistics cache
        Cache::forget('scholarships:statistics');
        
        // Clear all scholarship list caches (with different filters and pages)
        // Karena cache key menggunakan md5 hash dari filters, kita perlu clear semua
        // yang dimulai dengan 'scholarships:'
        $cacheKeys = Cache::get('scholarships:cache_keys', []);
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget('scholarships:cache_keys');
    }
}
