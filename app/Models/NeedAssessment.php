<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NeedAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentoring_session_id',
        'form_data',
        'completed_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the mentoring session for this assessment
     */
    public function mentoringSession(): BelongsTo
    {
        return $this->belongsTo(MentoringSession::class);
    }

    /**
     * Check if assessment is completed
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Mark assessment as completed
     */
    public function markCompleted(): void
    {
        $this->update(['completed_at' => now()]);
    }
}
