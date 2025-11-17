<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoachingFileResource extends JsonResource
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
            'mentoring_session_id' => $this->mentoring_session_id,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_url' => $this->getFileUrlAttribute(),
            'file_type' => $this->file_type,
            'uploaded_by' => $this->uploaded_by,
            'uploaded_by_user' => $this->whenLoaded('uploadedByUser', function () {
                return [
                    'id' => $this->uploadedByUser?->id,
                    'name' => $this->uploadedByUser?->name,
                    'email' => $this->uploadedByUser?->email,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
