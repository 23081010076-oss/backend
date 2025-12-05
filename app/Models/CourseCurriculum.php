<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCurriculum extends Model
{
    use HasFactory;

    protected $table = 'course_curriculums';

    protected $fillable = [
        'course_id',
        'section',
        'section_order',
        'title',
        'description',
        'order',
        'duration',
        'video_url',
    ];

    protected $casts = [
        'order' => 'integer',
        'section_order' => 'integer',
    ];

    /**
     * Get the course that owns this curriculum item
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}


