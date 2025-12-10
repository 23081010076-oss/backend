<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'rating'  => (int) $this->rating,
            'comment' => $this->comment,
            
            // User info untuk ditampilkan di review (hanya data yang diperlukan)
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id'            => $this->user->id,
                    'name'          => $this->user->name,
                    'profile_photo' => $this->user->profile_photo 
                        ? asset('storage/' . $this->user->profile_photo) 
                        : null,
                    'bio'           => $this->user->bio,
                ];
            }),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
