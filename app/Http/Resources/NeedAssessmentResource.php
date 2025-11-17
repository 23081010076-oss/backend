<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NeedAssessmentResource extends JsonResource
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
            'form_data' => $this->form_data,
            'completed_at' => $this->completed_at,
            'is_completed' => $this->isCompleted(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
