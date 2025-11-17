<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentoring_session_id',
        'file_name',
        'file_path',
        'file_type',
        'uploaded_by',
    ];

    /**
     * Get the mentoring session for this file
     */
    public function mentoringSession(): BelongsTo
    {
        return $this->belongsTo(MentoringSession::class);
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get file download name
     */
    public function getDownloadNameAttribute(): string
    {
        return $this->file_name . '.' . $this->file_type;
    }
}
