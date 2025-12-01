<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::where('user_id', request()->user()->id)->paginate(15);
        return response()->json($achievements);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'organization' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
        ]);

        $validated['user_id'] = $request->user()->id;
        $achievement = Achievement::create($validated);

        return response()->json([
            'message' => 'Achievement created successfully',
            'data' => $achievement
        ], 201);
    }

    public function show($id)
    {
        $achievement = Achievement::where('user_id', request()->user()->id)->findOrFail($id);
        return response()->json(['data' => $achievement]);
    }

    public function update(Request $request, $id)
    {
        $achievement = Achievement::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'organization' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
        ]);

        $achievement->update($validated);

        return response()->json([
            'message' => 'Achievement updated successfully',
            'data' => $achievement
        ]);
    }

    public function destroy($id)
    {
        $achievement = Achievement::where('user_id', request()->user()->id)->findOrFail($id);
        $achievement->delete();

        return response()->json(['message' => 'Achievement deleted successfully']);
    }
}
