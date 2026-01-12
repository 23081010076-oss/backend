<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'provider_id',
        'name',
        'description',
        'benefit',
        'location',
        'status',
        'deadline',
        'is_recommended',
        'image',
        'study_field',
        'funding_amount',
        'requirements',
    ];

    protected $casts = [
        'deadline' => 'date',
        'funding_amount' => 'decimal:2',
    ];

    /**
     * Append URL attributes to JSON
     */
    protected $appends = ['image_url'];

    /**
     * Get full URL for image
     * Handles both external URLs and storage paths
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If already a full URL (http or https), return as-is
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        // Otherwise, generate storage URL
        return url(Storage::url($this->image));
    }

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function applications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
