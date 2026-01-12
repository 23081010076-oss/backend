<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Class ReviewService
 * 
 * Handles all business logic related to reviews.
 * Provides methods for creating, updating, and managing reviews.
 * 
 * @package App\Services
 */
class ReviewService
{
    /**
     * Create a new review
     *
     * @param array $data Review data
     * @param User $user User creating the review
     * @return Review
     * @throws InvalidArgumentException
     */
    public function createReview(array $data, User $user): Review
    {
        try {
            DB::beginTransaction();

            // Check if user already reviewed this item
            if ($this->checkDuplicateReview($user, $data['reviewable_id'], $data['reviewable_type'])) {
                throw new InvalidArgumentException('You have already reviewed this item');
            }

            // ✅ FIX: Validate user has access to review (enrolled or subscribed)
            if ($data['reviewable_type'] === 'App\\Models\\Course') {
                $this->validateCourseReviewAccess($user, $data['reviewable_id']);
            }
            
            $data['user_id'] = $user->id;
            $review = Review::create($data);
            
            $review->load(['user', 'reviewable']);

            DB::commit();

            Log::info('Review created successfully', [
                'review_id' => $review->id,
                'user_id' => $user->id,
                'reviewable_type' => $data['reviewable_type'],
                'reviewable_id' => $data['reviewable_id'],
                'rating' => $data['rating'],
            ]);

            return $review;
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            Log::warning('Review creation failed: validation error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review creation failed: unexpected error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to create review. Please try again later.');
        }
    }
    
    /**
     * Update an existing review
     *
     * @param Review $review Review to update
     * @param array $data Update data
     * @return Review
     */
    public function updateReview(Review $review, array $data): Review
    {
        try {
            DB::beginTransaction();

            $review->update($data);
            $review->load(['user', 'reviewable']);

            DB::commit();

            Log::info('Review updated successfully', [
                'review_id' => $review->id,
                'user_id' => $review->user_id,
            ]);

            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Review update failed', [
                'review_id' => $review->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to update review. Please try again later.');
        }
    }
    
    /**
     * Check if user has already reviewed this item
     *
     * @param User $user User to check
     * @param int $reviewableId ID of the reviewable item
     * @param string $reviewableType Type of the reviewable item
     * @return bool
     */
    public function checkDuplicateReview(User $user, int $reviewableId, string $reviewableType): bool
    {
        return Review::where('user_id', $user->id)
            ->where('reviewable_id', $reviewableId)
            ->where('reviewable_type', $reviewableType)
            ->exists();
    }

    /**
     * Calculate average rating for a reviewable item
     *
     * @param int $reviewableId ID of the reviewable item
     * @param string $reviewableType Type of the reviewable item
     * @return float Average rating
     */
    public function calculateAverageRating(int $reviewableId, string $reviewableType): float
    {
        return Review::where('reviewable_id', $reviewableId)
            ->where('reviewable_type', $reviewableType)
            ->avg('rating') ?? 0.0;
    }

    /**
     * Get review statistics for a reviewable item
     *
     * @param int $reviewableId ID of the reviewable item
     * @param string $reviewableType Type of the reviewable item
     * @return array Statistics including average, count, and rating distribution
     */
    public function getReviewStatistics(int $reviewableId, string $reviewableType): array
    {
        $reviews = Review::where('reviewable_id', $reviewableId)
            ->where('reviewable_type', $reviewableType)
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating') ?? 0.0;

        // Rating distribution (1-5 stars)
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $reviews->where('rating', $i)->count();
        }

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 2),
            'rating_distribution' => $distribution,
        ];
    }

    /**
     * ✅ NEW: Validate user has access to review course
     * User must either:
     * 1. Be enrolled in the course (bought it)
     * 2. Have active subscription that covers this course
     *
     * @param User $user
     * @param int $courseId
     * @throws InvalidArgumentException
     */
    private function validateCourseReviewAccess(User $user, int $courseId): void
    {
        // Check if user is enrolled in this course
        $isEnrolled = $user->enrollments()
            ->where('course_id', $courseId)
            ->exists();

        if ($isEnrolled) {
            return; // User has access via enrollment
        }

        // Check if user has active subscription
        $course = \App\Models\Course::findOrFail($courseId);
        
        // Free courses can be reviewed by anyone
        if ($course->access_type === 'free') {
            return;
        }

        // Check subscription access
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if (!$subscription) {
            throw new InvalidArgumentException('You must enroll in this course or have an active subscription to leave a review');
        }

        // Premium subscription can review all courses
        if ($subscription->plan === 'premium') {
            return;
        }

        // Regular subscription can review regular and free courses
        if ($subscription->plan === 'regular' && $course->access_type !== 'premium') {
            return;
        }

        throw new InvalidArgumentException('Your subscription does not cover this course. Please enroll or upgrade your subscription');
    }
}
