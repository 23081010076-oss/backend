<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CorporateContact;
use Illuminate\Http\Request;

class CorporateContactController extends Controller
{
    /**
     * Display a listing of corporate contacts (Admin only)
     */
    public function index(Request $request)
    {
        $query = CorporateContact::with('user');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($contacts);
    }

    /**
     * Store a new corporate contact request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // 'status' is not in migration, removing it.
        // 'org_id' is in migration, but not used here.

        $contact = CorporateContact::create($validated);

        return response()->json([
            'message' => 'Corporate contact request submitted successfully',
            'data' => $contact
        ], 201);
    }

    /**
     * Display the specified corporate contact
     */
    public function show($id)
    {
        $contact = CorporateContact::with('user')->findOrFail($id);
        return response()->json(['data' => $contact]);
    }

    /**
     * Update the status of corporate contact (Admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        $contact = CorporateContact::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed,rejected',
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Contact status updated successfully',
            'data' => $contact
        ]);
    }

    /**
     * Remove the specified corporate contact
     */
    public function destroy($id)
    {
        $contact = CorporateContact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Corporate contact deleted successfully']);
    }
}
