<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\OrganizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * ==========================================================================
 * ORGANIZATION CONTROLLER (Controller untuk Organisasi)
 * ==========================================================================
 * 
 * FUNGSI: Menangani operasi CRUD untuk organisasi
 * - Lihat daftar organisasi user
 * - Tambah organisasi baru
 * - Update organisasi
 * - Hapus organisasi
 * 
 * @package App\Http\Controllers\Api
 */
class OrganizationController extends Controller
{
    use ApiResponse;

    /**
     * @var OrganizationService
     */
    protected OrganizationService $organizationService;

    /**
     * Create a new controller instance
     *
     * @param OrganizationService $organizationService
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Display user's organizations
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Organization::class);

        $organizations = Organization::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return $this->paginatedResponse($organizations, 'Organizations retrieved successfully');
    }

    /**
     * Store a new organization
     *
     * @param StoreOrganizationRequest $request
     * @return JsonResponse
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $this->authorize('create', Organization::class);

        try {
            $organization = $this->organizationService->createOrganization(
                $request->validated(),
                $request->user()
            );

            return $this->createdResponse($organization, 'Organization added successfully');
        } catch (\Exception $e) {
            Log::error('Organization creation failed in controller', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to create organization');
        }
    }

    /**
     * Display the specified organization
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);
        $this->authorize('view', $organization);
        
        return $this->successResponse($organization, 'Organization retrieved successfully');
    }

    /**
     * Update the specified organization
     *
     * @param UpdateOrganizationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateOrganizationRequest $request, int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);
        $this->authorize('update', $organization);

        try {
            $organization = $this->organizationService->updateOrganization(
                $organization,
                $request->validated()
            );

            return $this->successResponse($organization, 'Organization updated successfully');
        } catch (\Exception $e) {
            Log::error('Organization update failed in controller', [
                'organization_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to update organization');
        }
    }

    /**
     * Remove the specified organization
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);
        $this->authorize('delete', $organization);
        
        try {
            $this->organizationService->deleteOrganization($organization);
            return $this->successResponse(null, 'Organization deleted successfully');
        } catch (\Exception $e) {
            Log::error('Organization deletion failed in controller', [
                'organization_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Failed to delete organization');
        }
    }
}
