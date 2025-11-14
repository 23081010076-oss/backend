<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'level' => $this->level,
            'duration' => $this->duration,
            'price' => (float) $this->price,
            'access_type' => $this->access_type,
            'certificate_url' => $this->certificate_url,
            'video_url' => $this->video_url,
            'video_duration' => $this->video_duration,
            'total_videos' => $this->total_videos,
            'enrollment_count' => $this->enrollments_count ?? 0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
