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
     * Return sebagai flat list atau nested structure
     */
    public function getCurriculumsByCourse(int $courseId, bool $nested = false): Collection|array
    {
        $curriculums = CourseCurriculum::where('course_id', $courseId)
            ->orderBy('section_order')
            ->orderBy('order')
            ->get();

        if ($nested) {
            return $this->buildNestedStructure($curriculums);
        }

        return $curriculums;
    }

    /**
     * Build nested structure dari flat curriculums
     * Menggunakan section format: "1", "1.1", "1.1.1"
     */
    private function buildNestedStructure(Collection $curriculums): array
    {
        $nested = [];
        $indexed = [];

        // Index by section for quick lookup
        foreach ($curriculums as $curriculum) {
            $section = $curriculum->section ?? '';
            $indexed[$section] = [
                'id' => $curriculum->id,
                'course_id' => $curriculum->course_id,
                'section' => $curriculum->section,
                'section_order' => $curriculum->section_order,
                'title' => $curriculum->title,
                'description' => $curriculum->description,
                'order' => $curriculum->order,
                'duration' => $curriculum->duration,
                'video_url' => $curriculum->video_url,
                'level' => $curriculum->level,
                'is_parent' => $curriculum->is_parent,
                'children' => [],
            ];
        }

        // Build tree structure
        foreach ($indexed as $section => $item) {
            if (empty($section) || !str_contains($section, '.')) {
                // Root level
                $nested[] = &$indexed[$section];
            } else {
                // Child level - find parent
                $parts = explode('.', $section);
                array_pop($parts);
                $parentSection = implode('.', $parts);

                if (isset($indexed[$parentSection])) {
                    $indexed[$parentSection]['children'][] = &$indexed[$section];
                }
            }
        }

        return $nested;
    }

    /**
     * Buat kurikulum baru
     * Supports nested structure via section field
     */
    public function createCurriculum(int $courseId, array $data): CourseCurriculum
    {
        // Auto-generate section jika tidak ada
        if (!isset($data['section']) || empty($data['section'])) {
            $data['section'] = $this->generateNextSection($courseId, null);
        }

        // Set section_order based on section
        if (!isset($data['section_order'])) {
            $data['section_order'] = $this->calculateSectionOrder($data['section']);
        }

        // Set order otomatis jika tidak diberikan
        if (!isset($data['order'])) {
            $maxOrder = CourseCurriculum::where('course_id', $courseId)
                ->where('section', $data['section'])
                ->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $data['course_id'] = $courseId;
        
        $curriculum = CourseCurriculum::create($data);
        
        $this->clearCache($courseId);
        
        return $curriculum;
    }

    /**
     * Generate next section number
     * parent_section null = root level ("1", "2", "3")
     * parent_section "1" = child of 1 ("1.1", "1.2")
     * parent_section "1.1" = child of 1.1 ("1.1.1", "1.1.2")
     */
    public function generateNextSection(int $courseId, ?string $parentSection = null): string
    {
        if ($parentSection === null) {
            // Root level
            $maxSection = CourseCurriculum::where('course_id', $courseId)
                ->where(function($q) {
                    $q->whereNull('section')
                      ->orWhere('section', 'NOT LIKE', '%.%');
                })
                ->selectRaw('MAX(CAST(section AS UNSIGNED)) as max_section')
                ->value('max_section');

            return (string)($maxSection ? $maxSection + 1 : 1);
        }

        // Child level
        $pattern = $parentSection . '.%';
        $children = CourseCurriculum::where('course_id', $courseId)
            ->where('section', 'LIKE', $pattern)
            ->where('section', 'NOT LIKE', $pattern . '.%')
            ->get();

        if ($children->isEmpty()) {
            return $parentSection . '.1';
        }

        // Get max child number
        $maxChild = 0;
        foreach ($children as $child) {
            $parts = explode('.', $child->section);
            $childNum = (int)end($parts);
            if ($childNum > $maxChild) {
                $maxChild = $childNum;
            }
        }

        return $parentSection . '.' . ($maxChild + 1);
    }

    /**
     * Calculate section_order from section string
     * "1" -> 1000000
     * "1.1" -> 1001000
     * "1.1.1" -> 1001001
     */
    private function calculateSectionOrder(string $section): int
    {
        $parts = explode('.', $section);
        $order = 0;
        $multiplier = 1000000;

        foreach ($parts as $part) {
            $order += (int)$part * $multiplier;
            $multiplier = (int)($multiplier / 1000);
        }

        return $order;
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
