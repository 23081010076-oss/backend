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
            'qr_code_url' => $this->qr_code_url ? asset('storage/' . $this->qr_code_url) : null,
            'qr_string' => $this->qr_string, // String untuk generate QR di frontend
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

            // Item name and details (flattened from transactionable)
            'item_name' => $this->getItemName(),
            'item_details' => $this->getItemDetails(),
        ];
    }

    /**
     * Get the item name based on transactionable type
     */
    private function getItemName(): ?string
    {
        if (!$this->transactionable) {
            return null;
        }

        return match($this->transactionable_type) {
            'App\Models\Course' => $this->transactionable->title,
            'App\Models\Enrollment' => $this->transactionable->course?->title,
            'App\Models\Subscription' => ucfirst($this->transactionable->plan) . ' Subscription',
            'App\Models\MentoringSession' => 'Mentoring: ' . ucfirst(str_replace('_', ' ', $this->transactionable->type)),
            default => null,
        };
    }

    /**
     * Get the item details based on transactionable type
     */
    private function getItemDetails(): ?array
    {
        if (!$this->transactionable) {
            return null;
        }

        return match($this->transactionable_type) {
            'App\Models\Course' => [
                'id' => $this->transactionable->id,
                'title' => $this->transactionable->title,
                'image' => $this->transactionable->image 
                    ? (str_starts_with($this->transactionable->image, 'http') 
                        ? $this->transactionable->image 
                        : asset('storage/' . $this->transactionable->image))
                    : null,
                'instructor' => $this->transactionable->instructor,
                'level' => $this->transactionable->level,
                'duration' => $this->transactionable->duration,
            ],
            'App\Models\Enrollment' => $this->formatEnrollmentDetails(),
            'App\Models\Subscription' => [
                'id' => $this->transactionable->id,
                'plan' => $this->transactionable->plan,
                'plan_label' => $this->getSubscriptionPlanLabel($this->transactionable->plan),
                'image' => $this->getSubscriptionImage($this->transactionable->plan),
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
                'mentor' => $this->transactionable->relationLoaded('mentor') && $this->transactionable->mentor
                    ? [
                        'id' => $this->transactionable->mentor->id,
                        'name' => $this->transactionable->mentor->name,
                    ]
                    : null,
            ],
            default => null,
        };
    }

    /**
     * Format enrollment details with course information
     */
    private function formatEnrollmentDetails(): array
    {
        $enrollment = $this->transactionable;
        $course = $enrollment->course;

        return [
            'enrollment_id' => $enrollment->id,
            'course_id' => $course?->id,
            'title' => $course?->title,
            'image' => $course?->image 
                ? (str_starts_with($course->image, 'http') 
                    ? $course->image 
                    : asset('storage/' . $course->image))
                : null,
            'instructor' => $course?->instructor,
            'level' => $course?->level,
            'duration' => $course?->duration,
            'progress' => $enrollment->progress,
            'completed' => $enrollment->completed,
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
     * Get subscription image based on plan
     * 
     * @param string $plan
     * @return string
     */
    private function getSubscriptionImage(string $plan): ?string
    {
        return match($plan) {
            'premium' => 'https://img.freepik.com/free-vector/purple-diamond-sticker-isolated_1308-88426.jpg?semt=ais_hybrid&w=740&q=80',
            'regular' => 'https://img.freepik.com/free-psd/pile-gleaming-gold-bullions_191095-83967.jpg?semt=ais_hybrid&w=740&q=80',
            'free' => 'https://img.freepik.com/free-vector/golden-star-3d-icon_1308-169203.jpg?semt=ais_hybrid&w=740&q=80',
            default => null,
        };
    }

    /**
     * Get subscription plan label in Indonesian
     * 
     * @param string $plan
     * @return string
     */
    private function getSubscriptionPlanLabel(string $plan): string
    {
        return match($plan) {
            'premium' => 'Paket Premium',
            'regular' => 'Paket Reguler',
            'free' => 'Paket Gratis',
            default => ucfirst($plan),
        };
    }
}
