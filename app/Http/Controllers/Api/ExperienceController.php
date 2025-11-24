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
            'certificate_url' => 'nullable|string|url',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $validated;
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('certificate')) {
            $data['certificate_url'] = $request->file('certificate')->store('certificates', 'public');
        }
        // If certificate_url is provided in request, it stays in $data. 
        // If file is provided, it overwrites certificate_url in $data.

        $experience = Experience::create($data);

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
            'certificate_url' => 'nullable|string|url',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('certificate')) {
            $data['certificate_url'] = $request->file('certificate')->store('certificates', 'public');
        }

        $experience->update($data);

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
