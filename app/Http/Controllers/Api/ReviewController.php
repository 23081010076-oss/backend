<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display reviews
     */
    public function index(Request $request)
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
        return response()->json($reviews);
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reviewable_id' => 'required|integer',
            'reviewable_type' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('reviewable_id', $validated['reviewable_id'])
            ->where('reviewable_type', $validated['reviewable_type'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this item'
            ], 422);
        }

        $validated['user_id'] = $request->user()->id;
        $review = Review::create($validated);

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => $review->load(['user', 'reviewable'])
        ], 201);
    }

    /**
     * Display the specified review
     */
    public function show($id)
    {
        $review = Review::with(['user', 'reviewable'])->findOrFail($id);
        return response()->json(['data' => $review]);
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, $id)
    {
        $review = Review::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Review updated successfully',
            'data' => $review->load(['user', 'reviewable'])
        ]);
    }

    /**
     * Remove the specified review
     */
    public function destroy($id)
    {
        $review = Review::where('user_id', request()->user()->id)->findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
