<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'progress' => $this->progress,
            'completed' => $this->completed,
            'certificate_url' => $this->certificate_url,
            'enrolled_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
