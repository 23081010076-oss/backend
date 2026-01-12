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
     * Append calculated attributes to JSON/array output
     */
    protected $appends = [
        'level',
        'is_parent',
    ];

    /**
     * Get the course that owns this curriculum item
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get parent curriculum (based on section hierarchy)
     * Section format: "1" > "1.1" > "1.1.1"
     */
    public function parent()
    {
        if (!$this->section || !str_contains($this->section, '.')) {
            return null;
        }

        // Get parent section (e.g., "1.2.3" -> "1.2")
        $parts = explode('.', $this->section);
        array_pop($parts);
        $parentSection = implode('.', $parts);

        return self::where('course_id', $this->course_id)
            ->where('section', $parentSection)
            ->first();
    }

    /**
     * Get children curriculums
     */
    public function children()
    {
        if (!$this->section) {
            return collect([]);
        }

        return self::where('course_id', $this->course_id)
            ->where('section', 'LIKE', $this->section . '.%')
            ->where('section', 'NOT LIKE', $this->section . '.%.%')
            ->orderBy('section_order')
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all descendants (recursive children)
     */
    public function descendants()
    {
        if (!$this->section) {
            return collect([]);
        }

        return self::where('course_id', $this->course_id)
            ->where('section', 'LIKE', $this->section . '.%')
            ->orderBy('section_order')
            ->orderBy('order')
            ->get();
    }

    /**
     * Get level/depth of this curriculum item
     * Level 0 = root (section: "1", "2")
     * Level 1 = first sub (section: "1.1", "1.2")
     * Level 2 = second sub (section: "1.1.1", "1.1.2")
     */
    public function getLevelAttribute(): int
    {
        if (!$this->section) {
            return 0;
        }

        return substr_count($this->section, '.');
    }

    /**
     * Check if this curriculum has children
     */
    public function getIsParentAttribute(): bool
    {
        if (!$this->section) {
            return false;
        }

        return self::where('course_id', $this->course_id)
            ->where('section', 'LIKE', $this->section . '.%')
            ->exists();
    }

    /**
     * Scope: Get root items only (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->where(function($q) {
            $q->whereNull('section')
              ->orWhere('section', 'NOT LIKE', '%.%');
        });
    }

    /**
     * Scope: Get items by level
     */
    public function scopeByLevel($query, int $level)
    {
        if ($level === 0) {
            return $query->roots();
        }

        $pattern = str_repeat('%.', $level - 1) . '%';
        return $query->where('section', 'LIKE', $pattern)
            ->where('section', 'NOT LIKE', $pattern . '.%');
    }
}


