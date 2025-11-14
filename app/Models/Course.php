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
        'type',
        'level',
        'duration',
        'price',
        'access_type',
        'certificate_url',
        'video_url',
        'video_duration',
        'total_videos',
    ];

    protected $casts = [
        'price' => 'decimal:2',
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
}
