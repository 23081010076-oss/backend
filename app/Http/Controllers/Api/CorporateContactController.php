<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CorporateContact;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CorporateContactController
 * 
 * Handles corporate contact/inquiry submissions.
 * 
 * Database columns: id, org_id, name, email, message, timestamps
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

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by organization
        if ($request->has('org_id')) {
            $query->where('org_id', $request->org_id);
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
            'org_id'  => 'nullable|exists:organizations,id',
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string',
        ]);

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
            'org_id'  => 'nullable|exists:organizations,id',
            'name'    => 'sometimes|string|max:255',
            'email'   => 'sometimes|email|max:255',
            'message' => 'sometimes|string',
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
            'total' => CorporateContact::count(),
        ];

        return $this->successResponse($stats, 'Corporate contact statistics retrieved successfully');
    }
}
