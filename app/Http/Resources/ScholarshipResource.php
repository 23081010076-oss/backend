<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'name' => $this->name,
            'description' => $this->description,
            'benefit' => $this->benefit,
            'location' => $this->location,
            'status' => $this->status,
            'deadline' => $this->deadline?->format('Y-m-d'),
            'study_field' => $this->study_field,
            'funding_amount' => (float) $this->funding_amount,
            'requirements' => $this->requirements,
            'application_count' => $this->applications_count ?? 0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
