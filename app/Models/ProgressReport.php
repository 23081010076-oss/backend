<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'report_date',
        'progress_percentage',
        'notes',
        'attachment_url',
        'next_report_date',
        'frequency',
    ];

    protected $casts = [
        'report_date' => 'date',
        'next_report_date' => 'date',
    ];

    /**
     * Get the enrollment for this report
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get all reports due for generation
     */
    public static function getDueReports()
    {
        return self::whereHas('enrollment', function ($query) {
            $query->where('status', 'active');
        })->where('next_report_date', '<=', now()->toDateString())->get();
    }

    /**
     * Calculate and set next report date
     */
    public function setNextReportDate(): void
    {
        $this->next_report_date = now()->addDays($this->frequency);
        $this->save();
    }
}
