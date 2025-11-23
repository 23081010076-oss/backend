<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of user's subscriptions
     */
    public function index()
    {
        $subscriptions = Subscription::where('user_id', request()->user()->id)->get();
        return response()->json(['data' => $subscriptions]);
    }

    /**
     * Store a newly created subscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:free,regular,premium',
            'package_type' => 'required|in:single_course,all_in_one',
            'courses_ids' => 'nullable|array',
            'courses_ids.*' => 'exists:courses,id',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:months,years',
            'price' => 'required|numeric|min:0',
            'auto_renew' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
        ]);

        $validated['user_id'] = $request->user()->id;

        // If package_type is single_course, ensure courses_ids is present
        if ($validated['package_type'] === 'single_course' && empty($validated['courses_ids'])) {
             return response()->json(['message' => 'Course selection is required for single course package'], 422);
        }

        $subscription = Subscription::create($validated);

        return response()->json([
            'message' => 'Subscription created successfully',
            'data' => $subscription
        ], 201);
    }

    /**
     * Display the specified subscription
     */
    public function show($id)
    {
        $subscription = Subscription::where('user_id', request()->user()->id)->findOrFail($id);
        return response()->json(['data' => $subscription]);
    }

    /**
     * Update the specified subscription
     */
    public function update(Request $request, $id)
    {
        $subscription = Subscription::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'plan' => 'sometimes|in:free,regular,premium',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'sometimes|in:active,expired,cancelled',
        ]);

        $subscription->update($validated);

        return response()->json([
            'message' => 'Subscription updated successfully',
            'data' => $subscription
        ]);
    }

    /**
     * Upgrade subscription
     */
    public function upgrade(Request $request, $id)
    {
        $subscription = Subscription::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'plan' => 'required|in:regular,premium',
        ]);

        // Validate upgrade path
        if ($subscription->plan === 'premium' && $validated['plan'] !== 'premium') {
            return response()->json([
                'message' => 'Cannot downgrade from premium'
            ], 422);
        }

        $subscription->update([
            'plan' => $validated['plan'],
            'status' => 'active',
            'end_date' => now()->addYear(),
        ]);

        return response()->json([
            'message' => 'Subscription upgraded successfully',
            'data' => $subscription
        ]);
    }

    /**
     * Remove the specified subscription
     */
    public function destroy($id)
    {
        $subscription = Subscription::where('user_id', request()->user()->id)->findOrFail($id);
        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully']);
    }
}
