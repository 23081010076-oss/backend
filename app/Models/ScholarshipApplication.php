<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ScholarshipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'motivation_letter',
        'motivation_letter_text',
        'cv_path',
        'transcript_path',
        'recommendation_path',
        'gpa',
        'has_other_scholarship',
        'parent_income',
        'university',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * Append URL attributes to JSON
     */
    protected $appends = [
        'motivation_letter_url',
        'cv_url',
        'transcript_url',
        'recommendation_url',
    ];

    // URL Accessors
    public function getMotivationLetterUrlAttribute(): ?string
    {
        return $this->motivation_letter 
            ? url(Storage::url($this->motivation_letter)) 
            : null;
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_path 
            ? url(Storage::url($this->cv_path)) 
            : null;
    }

    public function getTranscriptUrlAttribute(): ?string
    {
        return $this->transcript_path 
            ? url(Storage::url($this->transcript_path)) 
            : null;
    }

    public function getRecommendationUrlAttribute(): ?string
    {
        return $this->recommendation_path 
            ? url(Storage::url($this->recommendation_path)) 
            : null;
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
