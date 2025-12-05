<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseCurriculum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * ==========================================================================
 * COURSE CURRICULUM SERVICE
 * ==========================================================================
 * 
 * FUNGSI: Menangani logika bisnis untuk manajemen kurikulum/materi kursus.
 */
class CourseCurriculumService
{
    /**
     * Ambil semua kurikulum berdasarkan course
     */
    public function getCurriculumsByCourse(int $courseId): Collection
    {
        return CourseCurriculum::where('course_id', $courseId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Buat kurikulum baru
     */
    public function createCurriculum(int $courseId, array $data): CourseCurriculum
    {
        // Set order otomatis jika tidak diberikan
        if (!isset($data['order'])) {
            $maxOrder = CourseCurriculum::where('course_id', $courseId)->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $data['course_id'] = $courseId;
        
        $curriculum = CourseCurriculum::create($data);
        
        $this->clearCache($courseId);
        
        return $curriculum;
    }

    /**
     * Update kurikulum
     */
    public function updateCurriculum(CourseCurriculum $curriculum, array $data): CourseCurriculum
    {
        $curriculum->update($data);
        
        $this->clearCache($curriculum->course_id);
        
        return $curriculum->fresh();
    }

    /**
     * Hapus kurikulum
     */
    public function deleteCurriculum(CourseCurriculum $curriculum): bool
    {
        $courseId = $curriculum->course_id;
        $result = $curriculum->delete();
        
        $this->clearCache($courseId);
        
        return $result;
    }

    /**
     * Reorder kurikulum
     * 
     * @param array $orderedIds Array of curriculum IDs in new order
     */
    public function reorderCurriculums(int $courseId, array $orderedIds): bool
    {
        foreach ($orderedIds as $order => $id) {
            CourseCurriculum::where('id', $id)
                ->where('course_id', $courseId)
                ->update(['order' => $order + 1]);
        }

        $this->clearCache($courseId);

        return true;
    }

    /**
     * Clear cache untuk course tertentu
     */
    private function clearCache(int $courseId): void
    {
        Cache::forget("course:{$courseId}:curriculums");
    }
}
