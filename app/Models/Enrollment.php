<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'completed',
        'certificate_url',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'progress' => 'integer',
    ];

    /**
     * Append calculated attributes
     */
    protected $appends = [
        'calculated_progress',
        'completed_materials',
        'total_materials',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * Get curriculum progress records for this enrollment
     */
    public function curriculumProgress()
    {
        return $this->hasMany(CurriculumProgress::class);
    }

    // ==========================================================================
    // AUTO-CALCULATED PROGRESS ATTRIBUTES
    // ==========================================================================

    /**
     * Get total materials in this course
     */
    public function getTotalMaterialsAttribute(): int
    {
        return $this->course->curriculums()->count();
    }

    /**
     * Get number of completed materials
     */
    public function getCompletedMaterialsAttribute(): int
    {
        return $this->curriculumProgress()->where('completed', true)->count();
    }

    /**
     * Calculate progress percentage automatically from completed materials
     */
    public function getCalculatedProgressAttribute(): int
    {
        $total = $this->total_materials;
        
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->completed_materials / $total) * 100);
    }

    /**
     * Mark a curriculum item as completed
     */
    public function markCurriculumCompleted(int $curriculumId): CurriculumProgress
    {
        $progress = CurriculumProgress::updateOrCreate(
            [
                'enrollment_id' => $this->id,
                'curriculum_id' => $curriculumId,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );

        // Auto-update the progress field
        $this->update(['progress' => $this->calculated_progress]);

        // Check if all materials completed
        if ($this->calculated_progress >= 100) {
            $this->update(['completed' => true]);
        }

        return $progress;
    }
}

