<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use App\Services\ReviewService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class ReviewController
 * 
 * Handles HTTP requests related to reviews.
 * Uses ReviewService for business logic and ReviewPolicy for authorization.
 * 
 * @package App\Http\Controllers\Api
 */
class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * @var ReviewService
     */
    protected ReviewService $reviewService;

    /**
     * Create a new controller instance
     *
     * @param ReviewService $reviewService
     */
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display reviews with optional filtering
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user', 'reviewable']);

        // Filter by reviewable
        if ($request->has('reviewable_id') && $request->has('reviewable_type')) {
            $query->where('reviewable_id', $request->reviewable_id)
                  ->where('reviewable_type', $request->reviewable_type);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return $this->paginatedResponse($reviews, 'Reviews retrieved successfully');
    }

    /**
     * Store a new review
     *
     * @param StoreReviewRequest $request
     * @return JsonResponse
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $this->authorize('create', Review::class);

        try {
            $review = $this->reviewService->createReview(
                $request->validated(),
                $request->user()
            );

            return $this->createdResponse($review, 'Review submitted successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->validationErrorResponse(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Review creation failed in controller', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to create review');
        }
    }

    /**
     * Display the specified review
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $review = Review::with(['user', 'reviewable'])->findOrFail($id);
        
        return $this->successResponse($review, 'Review retrieved successfully');
    }

    /**
     * Update the specified review
     *
     * @param UpdateReviewRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $this->authorize('update', $review);

        try {
            $review = $this->reviewService->updateReview(
                $review,
                $request->validated()
            );

            return $this->successResponse($review, 'Review updated successfully');
        } catch (\Exception $e) {
            Log::error('Review update failed in controller', [
                'review_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to update review');
        }
    }

    /**
     * Remove the specified review
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $this->authorize('delete', $review);
        
        try {
            $review->delete();
            return $this->successResponse(null, 'Review deleted successfully');
        } catch (\Exception $e) {
            Log::error('Review deletion failed in controller', [
                'review_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to delete review');
        }
    }
}
