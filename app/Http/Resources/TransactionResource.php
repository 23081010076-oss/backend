<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'transaction_code' => $this->transaction_code,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'payment_proof' => $this->payment_proof ? asset('storage/' . $this->payment_proof) : null,
            'payment_details' => $this->payment_details,
            'paid_at' => $this->paid_at,
            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // User information
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            // Transactionable information (Course, Subscription, MentoringSession)
            'item_name' => $this->getItemName(),
            'item_details' => $this->getItemDetails(),
        ];
    }

    /**
     * Get human-readable type label
     */
    private function getTypeLabel(): string
    {
        return match($this->type) {
            'course_enrollment' => 'Pendaftaran Kursus',
            'subscription' => 'Langganan',
            'mentoring_session' => 'Sesi Mentoring',
            'scholarship_application' => 'Beasiswa',
            default => $this->type,
        };
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            'refunded' => 'Dikembalikan',
            default => $this->status,
        };
    }

    /**
     * Get item name based on transactionable type
     */
    private function getItemName(): ?string
    {
        if (!$this->relationLoaded('transactionable') || !$this->transactionable) {
            return null;
        }

        return match($this->transactionable_type) {
            'App\Models\Course' => $this->transactionable->title ?? null,
            'App\Models\Enrollment' => $this->transactionable->course->title ?? null,
            'App\Models\Subscription' => $this->transactionable->plan . ' - ' . $this->transactionable->package_type ?? null,
            'App\Models\MentoringSession' => 'Mentoring ' . ($this->transactionable->type === 'academic' ? 'Akademik' : 'Life Plan') ?? null,
            default => null,
        };
    }

    /**
     * Get detailed item information based on transactionable type
     */
    private function getItemDetails(): ?array
    {
        if (!$this->relationLoaded('transactionable') || !$this->transactionable) {
            return null;
        }

        return match($this->transactionable_type) {
            'App\Models\Course' => [
                'id' => $this->transactionable->id,
                'title' => $this->transactionable->title,
                'image' => $this->transactionable->image,
                'instructor' => $this->transactionable->instructor,
                'level' => $this->transactionable->level,
                'duration' => $this->transactionable->duration,
            ],
            'App\Models\Enrollment' => [
                'enrollment_id' => $this->transactionable->id,
                'course_id' => $this->transactionable->course->id ?? null,
                'title' => $this->transactionable->course->title ?? null,
                'image' => $this->transactionable->course->image ?? null,
                'instructor' => $this->transactionable->course->instructor ?? null,
                'level' => $this->transactionable->course->level ?? null,
                'duration' => $this->transactionable->course->duration ?? null,
                'progress' => $this->transactionable->progress,
                'completed' => $this->transactionable->completed,
            ],
            'App\Models\Subscription' => [
                'id' => $this->transactionable->id,
                'plan' => $this->transactionable->plan,
                'package_type' => $this->transactionable->package_type,
                'duration' => $this->transactionable->duration . ' ' . $this->transactionable->duration_unit,
                'start_date' => $this->transactionable->start_date,
                'end_date' => $this->transactionable->end_date,
                'status' => $this->transactionable->status,
            ],
            'App\Models\MentoringSession' => [
                'id' => $this->transactionable->id,
                'session_id' => $this->transactionable->session_id,
                'type' => $this->transactionable->type,
                'schedule' => $this->transactionable->schedule,
                'meeting_link' => $this->transactionable->meeting_link,
                'status' => $this->transactionable->status,
            ],
            default => null,
        };
    }
}
