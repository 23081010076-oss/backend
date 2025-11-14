<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display reviews for a scholarship
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'scholarship']);

        // Filter by scholarship
        if ($request->has('scholarship_id')) {
            $query->where('scholarship_id', $request->scholarship_id);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $reviews]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        // Check if user already reviewed this scholarship
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('scholarship_id', $validated['scholarship_id'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this scholarship'
            ], 422);
        }

        $validated['user_id'] = $request->user()->id;
        $review = Review::create($validated);

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => $review->load(['user', 'scholarship'])
        ], 201);
    }

    /**
     * Display the specified review
     */
    public function show($id)
    {
        $review = Review::with(['user', 'scholarship'])->findOrFail($id);
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
            'data' => $review->load(['user', 'scholarship'])
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
