<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    /**
     * Display a listing of scholarships
     */
    public function index(Request $request)
    {
        $query = Scholarship::with(['organization']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by study_field
        if ($request->has('study_field')) {
            $query->where('study_field', 'like', '%' . $request->study_field . '%');
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $scholarships = $query->paginate(15);

        return response()->json($scholarships);
    }

    /**
     * Store a newly created scholarship
     */
    public function store(Request $request)
    {
        // Only organizations can create scholarships
        $user = auth()->user();
        if (!$user->hasRole('corporate')) {
            return response()->json([
                'message' => 'Only corporate partners can create scholarships'
            ], 403);
        }

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'benefit' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'required|in:open,coming_soon,closed',
            'deadline' => 'nullable|date',
            'study_field' => 'nullable|string|max:255',
            'funding_amount' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|string',
        ]);

        $scholarship = Scholarship::create($validated);

        return response()->json([
            'message' => 'Scholarship created successfully',
            'data' => $scholarship->load('organization')
        ], 201);
    }

    /**
     * Display the specified scholarship
     */
    public function show($id)
    {
        $scholarship = Scholarship::with(['organization', 'applications'])->findOrFail($id);
        
        return response()->json([
            'data' => $scholarship
        ]);
    }

    /**
     * Apply to a scholarship
     */
    public function apply(Request $request, $id)
    {
        $scholarship = Scholarship::findOrFail($id);

        if ($scholarship->status !== 'open') {
            return response()->json([
                'message' => 'This scholarship is not currently accepting applications'
            ], 422);
        }

        // Check if already applied
        $existing = ScholarshipApplication::where('user_id', auth()->id())
            ->where('scholarship_id', $id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You have already applied to this scholarship'
            ], 422);
        }

        $validated = $request->validate([
            'motivation_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'cv_path' => 'nullable|file|mimes:pdf|max:2048',
            'transcript_path' => 'nullable|file|mimes:pdf|max:2048',
            'recommendation_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'scholarship_id' => $id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ];

        // Handle file uploads
        if ($request->hasFile('motivation_letter')) {
            $data['motivation_letter'] = $request->file('motivation_letter')->store('scholarship-docs', 'public');
        }
        if ($request->hasFile('cv_path')) {
            $data['cv_path'] = $request->file('cv_path')->store('scholarship-docs', 'public');
        }
        if ($request->hasFile('transcript_path')) {
            $data['transcript_path'] = $request->file('transcript_path')->store('scholarship-docs', 'public');
        }
        if ($request->hasFile('recommendation_path')) {
            $data['recommendation_path'] = $request->file('recommendation_path')->store('scholarship-docs', 'public');
        }

        $application = ScholarshipApplication::create($data);

        return response()->json([
            'message' => 'Application submitted successfully',
            'data' => $application
        ], 201);
    }

    /**
     * Get user's scholarship applications
     */
    public function myApplications()
    {
        $applications = ScholarshipApplication::with('scholarship')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'data' => $applications
        ]);
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,review,accepted,rejected',
        ]);

        $application = ScholarshipApplication::findOrFail($id);
        $application->update($validated);

        return response()->json([
            'message' => 'Application status updated successfully',
            'data' => $application
        ]);
    }

    /**
     * Update the specified scholarship
     */
    public function update(Request $request, $id)
    {
        $scholarship = Scholarship::findOrFail($id);

        $validated = $request->validate([
            'organization_id' => 'sometimes|nullable|exists:organizations,id',
            'provider_id' => 'nullable|string',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'benefit' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'sometimes|in:open,coming_soon,closed',
            'deadline' => 'nullable|date',
        ]);

        $scholarship->update($validated);

        return response()->json([
            'message' => 'Scholarship updated successfully',
            'data' => $scholarship
        ]);
    }

    /**
     * Remove the specified scholarship
     */
    public function destroy($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        $scholarship->delete();

        return response()->json([
            'message' => 'Scholarship deleted successfully'
        ]);
    }
}
