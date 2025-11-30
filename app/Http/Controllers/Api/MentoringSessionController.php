<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentoringSession;
use Illuminate\Http\Request;

class MentoringSessionController extends Controller
{
    /**
     * Display a listing of mentoring sessions
     */
    public function index(Request $request)
    {
        $query = MentoringSession::with(['mentor', 'member']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $sessions = $query->orderBy('schedule', 'desc')->paginate(15);
        return response()->json($sessions);
    }

    /**
     * Schedule a new mentoring session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mentor_id' => 'required|exists:users,id',
            'member_id' => 'required|exists:users,id',
            'session_id' => 'nullable|string|max:255',
            'type' => 'required|in:academic,life_plan',
            'schedule' => 'required|date|after:now',
            'meeting_link' => 'nullable|url',
            'payment_method' => 'nullable|in:qris,bank,va,manual',
            'status' => 'required|in:pending,scheduled,completed,cancelled,refunded',
        ]);

        $session = MentoringSession::create($validated);

        return response()->json([
            'message' => 'Mentoring session scheduled successfully',
            'data' => $session
        ], 201);
    }

    /**
     * Display the specified mentoring session
     */
    public function show($id)
    {
        $session = MentoringSession::with(['mentor', 'member'])->findOrFail($id);
        return response()->json(['data' => $session]);
    }

    /**
     * Update the specified mentoring session
     */
    public function update(Request $request, $id)
    {
        $session = MentoringSession::findOrFail($id);

        $validated = $request->validate([
            'session_id' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:academic,life_plan',
            'schedule' => 'sometimes|date|after:now',
            'meeting_link' => 'nullable|url',
            'payment_method' => 'nullable|in:qris,bank,va,manual',
            'status' => 'sometimes|in:pending,scheduled,completed,cancelled,refunded',
        ]);

        $session->update($validated);

        return response()->json([
            'message' => 'Mentoring session updated successfully',
            'data' => $session
        ]);
    }

    /**
     * Get sessions for authenticated user (as mentor or student)
     */
    public function mySessions()
    {
        $userId = request()->user()->id;
        
        $sessions = MentoringSession::with(['mentor', 'member'])
            ->where(function($query) use ($userId) {
                $query->where('mentor_id', $userId)
                      ->orWhere('member_id', $userId);
            })
            ->orderBy('schedule', 'desc')
            ->paginate(15);

        return response()->json($sessions);
    }

    /**
     * Schedule/Reschedule a mentoring session
     */
    public function schedule(Request $request, $id)
    {
        $session = MentoringSession::findOrFail($id);

        $validated = $request->validate([
            'schedule' => 'required|date|after:now',
            'meeting_link' => 'nullable|url',
        ]);

        $session->update([
            'schedule' => $validated['schedule'],
            'meeting_link' => $validated['meeting_link'] ?? $session->meeting_link,
            'status' => 'scheduled',
        ]);

        return response()->json([
            'message' => 'Mentoring session scheduled successfully',
            'data' => $session->load(['mentor', 'member'])
        ]);
    }

    /**
     * Update session status
     */
    public function updateStatus(Request $request, $id)
    {
        $session = MentoringSession::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,scheduled,completed,cancelled,refunded',
        ]);

        $session->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Session status updated successfully',
            'data' => $session
        ]);
    }

    /**
     * Remove the specified mentoring session
     */
    public function destroy($id)
    {
        $session = MentoringSession::findOrFail($id);
        $session->delete();

        return response()->json(['message' => 'Mentoring session deleted successfully']);
    }
    
}
