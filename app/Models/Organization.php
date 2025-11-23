<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
