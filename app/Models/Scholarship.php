<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'study_field',
        'funding_amount',
        'requirements',
    ];

    protected $casts = [
        'deadline' => 'date',
        'funding_amount' => 'decimal:2',
    ];

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
