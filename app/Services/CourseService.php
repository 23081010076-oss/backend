<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

/**
 * ==========================================================================
 * COURSE SERVICE (Service untuk Kursus)
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk manajemen kursus.
 * 
 * KENAPA PAKAI SERVICE?
 * - Controller jadi bersih dan ringkas
 * - Logika upload video, filter, dll terpusat di sini
 * - Mudah di-test secara terpisah
 */
class CourseService
{
    /**
     * Ambil daftar kursus dengan filter
     * 
     * CACHING: Data di-cache selama 10 menit untuk performa lebih baik
     */
    public function getCourses(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        // Generate cache key berdasarkan filter
        $cacheKey = 'courses:' . md5(json_encode($filters) . $perPage . request('page', 1));
        
        // Cache selama 10 menit (600 detik)
        return Cache::remember($cacheKey, 600, function () use ($filters, $perPage) {
            $query = Course::query();

            // Filter berdasarkan tipe
            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            // Filter berdasarkan kategori
            if (!empty($filters['category'])) {
                $query->where('category', $filters['category']);
            }

            // Filter berdasarkan level
            if (!empty($filters['level'])) {
                $query->where('level', $filters['level']);
            }

            // Filter berdasarkan tipe akses
            if (!empty($filters['access_type'])) {
                $query->where('access_type', $filters['access_type']);
            }

            // Pencarian berdasarkan judul atau deskripsi
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            return $query->paginate($perPage);
        });
    }

    /**
     * Ambil detail kursus dengan relasi
     */
    public function getCourseWithDetails(int $id): Course
    {
        return Course::with(['enrollments', 'reviews', 'curriculums'])->findOrFail($id);
    }

    /**
     * Buat kursus baru
     */
    public function createCourse(array $data, $videoFile = null): Course
    {
        // Handle upload video
        if ($videoFile) {
            $data['video_url'] = $videoFile->store('course-videos', 'public');
        }

        $course = Course::create($data);
        
        // Clear cache setelah create
        $this->clearCache();

        return $course;
    }

    /**
     * Update kursus
     */
    public function updateCourse(Course $course, array $data, $videoFile = null): Course
    {
        // Handle upload video baru
        if ($videoFile) {
            // Hapus video lama
            $this->deleteVideo($course->video_url);
            
            // Upload video baru
            $data['video_url'] = $videoFile->store('course-videos', 'public');
        }

        $course->update($data);
        
        // Clear cache setelah update
        $this->clearCache();

        return $course->fresh();
    }

    /**
     * Hapus kursus
     */
    public function deleteCourse(Course $course): bool
    {
        // Hapus video jika ada
        $this->deleteVideo($course->video_url);

        $result = $course->delete();
        
        // Clear cache setelah delete
        $this->clearCache();

        return $result;
    }

    /**
     * Ambil kursus gratis
     */
    public function getFreeCourses(int $limit = 10): Collection
    {
        return Course::where('access_type', 'free')
            ->limit($limit)
            ->get();
    }

    /**
     * Ambil kursus premium
     */
    public function getPremiumCourses(int $limit = 10): Collection
    {
        return Course::where('access_type', 'premium')
            ->limit($limit)
            ->get();
    }

    /**
     * Ambil kursus berdasarkan level
     */
    public function getByLevel(string $level, int $perPage = 15): LengthAwarePaginator
    {
        return Course::where('level', $level)->paginate($perPage);
    }

    /**
     * Cek apakah kursus dapat diakses oleh user
     */
    public function canUserAccess(Course $course, User $user): bool
    {
        // Kursus gratis bisa diakses semua orang
        if ($course->access_type === 'free') {
            return true;
        }

        // Cek subscription user
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if (!$subscription) {
            return false;
        }

        // Premium subscription bisa akses semua
        if ($subscription->plan === 'premium') {
            return true;
        }

        // Regular subscription hanya bisa akses regular dan free
        if ($subscription->plan === 'regular' && $course->access_type !== 'premium') {
            return true;
        }

        return false;
    }

    /**
     * Hapus file video
     */
    private function deleteVideo(?string $videoPath): void
    {
        if ($videoPath && Storage::disk('public')->exists($videoPath)) {
            Storage::disk('public')->delete($videoPath);
        }
    }

    /**
     * Ambil statistik kursus (cached 30 menit)
     */
    public function getStatistics(): array
    {
        return Cache::remember('courses:statistics', 1800, function () {
            return [
                'total'    => Course::count(),
                'bootcamp' => Course::where('type', 'bootcamp')->count(),
                'course'   => Course::where('type', 'course')->count(),
                'free'     => Course::where('access_type', 'free')->count(),
                'regular'  => Course::where('access_type', 'regular')->count(),
                'premium'  => Course::where('access_type', 'premium')->count(),
                'beginner'     => Course::where('level', 'beginner')->count(),
                'intermediate' => Course::where('level', 'intermediate')->count(),
                'advanced'     => Course::where('level', 'advanced')->count(),
            ];
        });
    }

    /**
     * Clear semua cache courses
     */
    public function clearCache(): void
    {
        // Clear cache dengan pattern 'courses:*'
        // Untuk production, gunakan Redis dengan tags
        Cache::forget('courses:statistics');
        
        // Clear cache list (simplified - di production gunakan cache tags)
        // Cache::tags(['courses'])->flush();
    }
}
