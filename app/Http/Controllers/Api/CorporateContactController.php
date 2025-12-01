<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CorporateContact;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class CorporateContactController
 * 
 * Handles corporate contact/inquiry submissions.
 * Allows companies to submit partnership or training requests.
 * 
 * @package App\Http\Controllers\Api
 */
class CorporateContactController extends Controller
{
    use ApiResponse;

    /*
    |--------------------------------------------------------------------------
    | List & Retrieve Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Display a listing of corporate contacts (admin only)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = CorporateContact::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Search by company name or contact name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $contacts = $query->latest()->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($contacts, 'Corporate contacts retrieved successfully');
    }

    /**
     * Display the specified corporate contact
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $contact = CorporateContact::findOrFail($id);

        return $this->successResponse($contact, 'Corporate contact retrieved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Create & Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Store a newly created corporate contact (public endpoint)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name'     => 'required|string|max:255',
            'contact_name'     => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'type'             => 'required|in:partnership,training,recruitment,other',
            'message'          => 'required|string',
            'company_size'     => 'nullable|string|max:50',
            'industry'         => 'nullable|string|max:100',
            'website'          => 'nullable|url',
            'preferred_date'   => 'nullable|date',
            'budget_range'     => 'nullable|string|max:100',
        ]);

        $validated['status'] = 'pending';

        // Associate with authenticated user if logged in
        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
        }

        $contact = CorporateContact::create($validated);

        return $this->createdResponse($contact, 'Corporate contact submitted successfully. We will get back to you soon.');
    }

    /**
     * Update the specified corporate contact (admin only)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $contact = CorporateContact::findOrFail($id);

        $validated = $request->validate([
            'company_name'   => 'sometimes|string|max:255',
            'contact_name'   => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|max:255',
            'phone'          => 'nullable|string|max:20',
            'type'           => 'sometimes|in:partnership,training,recruitment,other',
            'message'        => 'sometimes|string',
            'status'         => 'sometimes|in:pending,contacted,in_progress,completed,rejected',
            'admin_notes'    => 'nullable|string',
            'company_size'   => 'nullable|string|max:50',
            'industry'       => 'nullable|string|max:100',
            'website'        => 'nullable|url',
            'preferred_date' => 'nullable|date',
            'budget_range'   => 'nullable|string|max:100',
        ]);

        $contact->update($validated);

        return $this->successResponse($contact, 'Corporate contact updated successfully');
    }

    /**
     * Remove the specified corporate contact (admin only)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $contact = CorporateContact::findOrFail($id);

        $contact->delete();

        return $this->successResponse(null, 'Corporate contact deleted successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Status Management Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Update contact status (admin only)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $contact = CorporateContact::findOrFail($id);

        $validated = $request->validate([
            'status'      => 'required|in:pending,contacted,in_progress,completed,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $contact->update($validated);

        return $this->successResponse($contact, 'Contact status updated successfully');
    }

    /**
     * Mark contact as contacted
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function markContacted(Request $request, int $id): JsonResponse
    {
        $contact = CorporateContact::findOrFail($id);

        $contact->update([
            'status'       => 'contacted',
            'contacted_at' => now(),
            'admin_notes'  => $request->get('admin_notes', $contact->admin_notes),
        ]);

        return $this->successResponse($contact, 'Contact marked as contacted');
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get corporate contact statistics (admin only)
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total'       => CorporateContact::count(),
            'pending'     => CorporateContact::where('status', 'pending')->count(),
            'contacted'   => CorporateContact::where('status', 'contacted')->count(),
            'in_progress' => CorporateContact::where('status', 'in_progress')->count(),
            'completed'   => CorporateContact::where('status', 'completed')->count(),
            'rejected'    => CorporateContact::where('status', 'rejected')->count(),
            'by_type'     => [
                'partnership' => CorporateContact::where('type', 'partnership')->count(),
                'training'    => CorporateContact::where('type', 'training')->count(),
                'recruitment' => CorporateContact::where('type', 'recruitment')->count(),
                'other'       => CorporateContact::where('type', 'other')->count(),
            ],
        ];

        return $this->successResponse($stats, 'Corporate contact statistics retrieved successfully');
    }
}
