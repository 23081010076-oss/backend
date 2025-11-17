<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'completed',
        'certificate_url',
        'last_progress_report_date',
        'next_progress_report_date',
        'report_frequency',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'progress' => 'integer',
        'last_progress_report_date' => 'date',
        'next_progress_report_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class);
    }
}
