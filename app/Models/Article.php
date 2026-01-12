<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Article Model
 * 
 * Database columns: id, author_id, title, content, category, author (string), timestamps
 */
class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'content',
        'category',
        'author', // author name as string
        'image',
    ];

    protected $appends = ['image_url'];

    // Accessor untuk image URL
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // Jika sudah URL lengkap, return as is
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Generate URL dari storage
        return asset('storage/' . $this->image);
    }

    // Relationships
    public function authorUser()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
