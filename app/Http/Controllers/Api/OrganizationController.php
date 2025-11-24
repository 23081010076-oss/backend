<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display user's organizations
     */
    public function index()
    {
        $organizations = Organization::where('user_id', request()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json(['data' => $organizations]);
    }

    /**
     * Store a new organization
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'phone' => 'nullable|string',
            'founded_year' => 'nullable|integer',
        ]);

        $validated['user_id'] = $request->user()->id;
        $organization = Organization::create($validated);

        return response()->json([
            'message' => 'Organization added successfully',
            'data' => $organization
        ], 201);
    }

    /**
     * Display the specified organization
     */
    public function show($id)
    {
        $organization = Organization::where('user_id', request()->user()->id)
            ->findOrFail($id);
        
        return response()->json(['data' => $organization]);
    }

    /**
     * Update the specified organization
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $organization->update($validated);

        return response()->json([
            'message' => 'Organization updated successfully',
            'data' => $organization
        ]);
    }

    /**
     * Remove the specified organization
     */
    public function destroy($id)
    {
        $organization = Organization::where('user_id', request()->user()->id)
            ->findOrFail($id);
        
        $organization->delete();

        return response()->json(['message' => 'Organization deleted successfully']);
    }
}
