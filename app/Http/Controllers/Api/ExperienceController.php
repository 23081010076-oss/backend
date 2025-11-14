<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display a listing of user's experiences
     */
    public function index()
    {
        $experiences = Experience::where('user_id', request()->user()->id)->get();
        return response()->json(['data' => $experiences]);
    }

    /**
     * Store a newly created experience
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:work,internship,volunteer',
            'level' => 'nullable|string',
            'company' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'certificate_url' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $experience = Experience::create($validated);

        return response()->json([
            'message' => 'Experience created successfully',
            'data' => $experience
        ], 201);
    }

    /**
     * Display the specified experience
     */
    public function show($id)
    {
        $experience = Experience::where('user_id', request()->user()->id)->findOrFail($id);
        return response()->json(['data' => $experience]);
    }

    /**
     * Update the specified experience
     */
    public function update(Request $request, $id)
    {
        $experience = Experience::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:work,internship,volunteer',
            'level' => 'nullable|string',
            'company' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'certificate_url' => 'nullable|string',
        ]);

        $experience->update($validated);

        return response()->json([
            'message' => 'Experience updated successfully',
            'data' => $experience
        ]);
    }

    /**
     * Remove the specified experience
     */
    public function destroy($id)
    {
        $experience = Experience::where('user_id', request()->user()->id)->findOrFail($id);
        $experience->delete();

        return response()->json(['message' => 'Experience deleted successfully']);
    }
}
