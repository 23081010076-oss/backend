<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentoringSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mentor_id' => $this->mentor_id,
            'member_id' => $this->member_id,
            'session_id' => $this->session_id,
            'type' => $this->type,
            'schedule' => $this->schedule?->format('Y-m-d H:i:s'),
            'meeting_link' => $this->meeting_link,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
