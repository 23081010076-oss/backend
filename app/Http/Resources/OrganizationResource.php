<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'location' => $this->location,
            'website' => $this->website,
            'contact_email' => $this->contact_email,
            'phone' => $this->phone,
            'founded_year' => $this->founded_year,
            'logo_url' => $this->logo_url,
            'logo_full_url' => $this->logo_url ? asset('storage/' . $this->logo_url) : null,
            'scholarship_count' => $this->scholarships_count ?? 0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
