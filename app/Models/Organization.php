<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'description',
        'location',
        'website',
        'contact_email',
        'phone',
        'founded_year',
        'logo_url',
    ];

    /**
     * Append URL attributes to JSON
     */
    protected $appends = ['logo_full_url'];

    /**
     * Get full URL for logo
     * Handles both external URLs and storage paths
     */
    public function getLogoFullUrlAttribute(): ?string
    {
        if (!$this->logo_url) {
            return null;
        }

        // If already a full URL (http or https), return as-is
        if (str_starts_with($this->logo_url, 'http://') || str_starts_with($this->logo_url, 'https://')) {
            return $this->logo_url;
        }

        // Otherwise, generate storage URL
        return url(Storage::url($this->logo_url));
    }

    // Relationships
    public function scholarships()
    {
        return $this->hasMany(Scholarship::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
