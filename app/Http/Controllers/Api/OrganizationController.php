<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\OrganizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        $organizations = Organization::where('user_id', Auth::id())
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

    /**
     * Upload logo untuk organisasi
     *
     * Endpoint: POST /api/organizations/{id}/logo
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function uploadLogo(Request $request, int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);
        $this->authorize('update', $organization);

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'logo.required' => 'File logo wajib diupload',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Format logo harus: jpeg, png, jpg, gif, atau svg',
            'logo.max' => 'Ukuran logo maksimal 2MB',
        ]);

        try {
            if ($request->hasFile('logo')) {
                // Hapus logo lama jika ada
                if ($organization->logo_url) {
                    // Ekstrak path dari URL jika disimpan sebagai full URL
                    $oldPath = str_replace(asset('storage/'), '', $organization->logo_url);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    // Atau jika disimpan sebagai path relatif
                    if (Storage::disk('public')->exists($organization->logo_url)) {
                        Storage::disk('public')->delete($organization->logo_url);
                    }
                }

                // Simpan logo baru
                $path = $request->file('logo')->store('organization-logos', 'public');
                $organization->logo_url = $path;
                $organization->save();

                Log::info('Organization logo uploaded successfully', [
                    'organization_id' => $organization->id,
                    'user_id' => Auth::id(),
                    'logo_path' => $path,
                ]);
            }

            return $this->successResponse([
                'id' => $organization->id,
                'name' => $organization->name,
                'logo_url' => $organization->logo_url,
                'logo_full_url' => $organization->logo_url ? asset('storage/' . $organization->logo_url) : null,
            ], 'Logo organisasi berhasil diupload');

        } catch (\Exception $e) {
            Log::error('Organization logo upload failed', [
                'organization_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal mengupload logo organisasi');
        }
    }

    /**
     * Hapus logo organisasi
     *
     * Endpoint: DELETE /api/organizations/{id}/logo
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function deleteLogo(int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);
        $this->authorize('update', $organization);

        try {
            if ($organization->logo_url) {
                // Hapus file dari storage
                if (Storage::disk('public')->exists($organization->logo_url)) {
                    Storage::disk('public')->delete($organization->logo_url);
                }

                $organization->logo_url = null;
                $organization->save();

                Log::info('Organization logo deleted successfully', [
                    'organization_id' => $organization->id,
                    'user_id' => Auth::id(),
                ]);
            }

            return $this->successResponse(null, 'Logo organisasi berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Organization logo deletion failed', [
                'organization_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal menghapus logo organisasi');
        }
    }
}
