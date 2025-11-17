<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'enrollment_id' => $this->enrollment_id,
            'report_date' => $this->report_date,
            'progress_percentage' => $this->progress_percentage,
            'notes' => $this->notes,
            'attachment_url' => $this->attachment_url,
            'next_report_date' => $this->next_report_date,
            'frequency' => $this->frequency,
            'enrollment' => $this->whenLoaded('enrollment', function () {
                return [
                    'id' => $this->enrollment?->id,
                    'user_id' => $this->enrollment?->user_id,
                    'course_id' => $this->enrollment?->course_id,
                    'progress' => $this->enrollment?->progress,
                    'completed' => $this->enrollment?->completed,
                ];
            }),
            'is_due' => $this->next_report_date && now()->format('Y-m-d') >= $this->next_report_date->format('Y-m-d'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
