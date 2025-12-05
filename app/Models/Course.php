<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'type',
        'level',
        'duration',
        'price',
        'access_type',
        'certificate_url',
        'instructor',
        'video_url',
        'video_duration',
        'total_videos',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Append calculated attributes to JSON/array output
     */
    protected $appends = [
        'total_materials',
        'total_curriculum_duration',
    ];

    // Relationships
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('progress', 'completed', 'certificate_url')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * Get the curriculums/materials for this course (grouped by section)
     */
    public function curriculums()
    {
        return $this->hasMany(CourseCurriculum::class)->orderBy('section_order')->orderBy('order');
    }

    // ==========================================================================
    // CALCULATED ATTRIBUTES (Auto-kalkulasi dari kurikulum)
    // ==========================================================================

    /**
     * Get total number of materials/curriculums
     * Auto-calculated from curriculum count
     */
    public function getTotalMaterialsAttribute(): int
    {
        return $this->curriculums()->count();
    }

    /**
     * Get total duration from all curriculum items
     * Parses duration strings like "2 jam", "30 menit" and sums them
     */
    public function getTotalCurriculumDurationAttribute(): string
    {
        $curriculums = $this->curriculums()->pluck('duration');
        
        $totalMinutes = 0;
        
        foreach ($curriculums as $duration) {
            if (!$duration) continue;
            
            // Parse "X jam" format
            if (preg_match('/(\d+)\s*jam/i', $duration, $matches)) {
                $totalMinutes += (int)$matches[1] * 60;
            }
            
            // Parse "X menit" format
            if (preg_match('/(\d+)\s*menit/i', $duration, $matches)) {
                $totalMinutes += (int)$matches[1];
            }
        }
        
        if ($totalMinutes === 0) {
            return '0 menit';
        }
        
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        } else {
            return "{$minutes} menit";
        }
    }
}

