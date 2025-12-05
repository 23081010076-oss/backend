<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumProgress extends Model
{
    protected $table = 'curriculum_progress';

    protected $fillable = [
        'enrollment_id',
        'curriculum_id',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the enrollment
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the curriculum item
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(CourseCurriculum::class, 'curriculum_id');
    }
}
