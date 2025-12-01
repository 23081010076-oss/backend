<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/**
 * Class EnrollmentService
 * 
 * Handles all business logic related to course enrollments.
 * Provides methods for enrolling users, updating progress, and generating certificates.
 * 
 * @package App\Services
 */
class EnrollmentService
{
    /**
     * Enroll a user to a course
     *
     * @param User $user User to enroll
     * @param Course $course Course to enroll in
     * @return Enrollment
     * @throws InvalidArgumentException
     */
    public function enrollUserToCourse(User $user, Course $course): Enrollment
    {
        try {
            DB::beginTransaction();

            // Check if already enrolled
            if ($this->isUserEnrolled($user, $course)) {
                throw new InvalidArgumentException('You are already enrolled in this course');
            }
            
            // Check access permission based on subscription
            if (!$this->checkEnrollmentAccess($user, $course)) {
                $requiredPlan = $course->access_type === 'premium' ? 'Premium' : 'Regular or Premium';
                throw new InvalidArgumentException("{$requiredPlan} subscription required for this course");
            }
            
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'progress' => 0,
                'completed' => false,
            ]);

            DB::commit();

            Log::info('User enrolled in course successfully', [
                'enrollment_id' => $enrollment->id,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_title' => $course->title,
            ]);

            return $enrollment;
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            Log::warning('Enrollment failed: validation error', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Enrollment failed: unexpected error', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to enroll in course. Please try again later.');
        }
    }
    
    /**
     * Update enrollment progress
     *
     * @param Enrollment $enrollment Enrollment to update
     * @param int $progress Progress percentage (0-100)
     * @return Enrollment
     */
    public function updateProgress(Enrollment $enrollment, int $progress): Enrollment
    {
        try {
            DB::beginTransaction();

            $enrollment->progress = $progress;
            
            // Auto-complete if progress is 100%
            if ($progress >= 100) {
                $enrollment->completed = true;
                
                // Generate Certificate
                $certificateUrl = $this->generateCertificate($enrollment);
                if ($certificateUrl) {
                    $enrollment->certificate_url = $certificateUrl;
                }

                Log::info('Course completed and certificate generated', [
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                    'certificate_url' => $certificateUrl,
                ]);
            }
            
            $enrollment->save();

            DB::commit();

            Log::info('Enrollment progress updated', [
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'progress' => $progress,
                'completed' => $enrollment->completed,
            ]);

            return $enrollment->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update enrollment progress', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to update progress. Please try again later.');
        }
    }
    
    /**
     * Check if user has access to enroll in course
     *
     * @param User $user User to check
     * @param Course $course Course to check access for
     * @return bool
     */
    public function checkEnrollmentAccess(User $user, Course $course): bool
    {
        // Free courses are accessible to everyone
        if ($course->access_type === 'free') {
            return true;
        }
        
        // Get user's active subscription
        $subscription = $user->subscriptions()->where('status', 'active')->latest()->first();
        
        if (!$subscription) {
            return false;
        }
        
        // Check access based on course type
        if ($course->access_type === 'premium' && $subscription->plan !== 'premium') {
            return false;
        }
        
        if ($course->access_type === 'regular' && $subscription->plan === 'free') {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if user is already enrolled in course
     *
     * @param User $user User to check
     * @param Course $course Course to check enrollment for
     * @return bool
     */
    protected function isUserEnrolled(User $user, Course $course): bool
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }
    
    /**
     * Generate certificate for completed enrollment
     *
     * @param Enrollment $enrollment Enrollment to generate certificate for
     * @return string|null Certificate URL or null if generation failed
     */
    public function generateCertificate(Enrollment $enrollment): ?string
    {
        try {
            $pdf = Pdf::loadView('certificates.course_completion', [
                'user' => $enrollment->user,
                'course' => $enrollment->course,
                'date' => now()->format('F d, Y'),
            ]);

            $fileName = 'certificates/' . $enrollment->id . '-' . time() . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());
            
            // Generate full URL
            $url = url(Storage::url($fileName));

            Log::info('Certificate generated successfully', [
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'file_name' => $fileName,
            ]);

            return $url;
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Certificate generation failed', [
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }
}
