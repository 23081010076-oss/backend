<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class OrganizationService
 * 
 * Handles all business logic related to organizations.
 * Provides methods for creating, updating, and deleting organizations.
 * 
 * @package App\Services
 */
class OrganizationService
{
    /**
     * Create a new organization
     *
     * @param array $data Organization data
     * @param User $user User creating the organization
     * @return Organization
     */
    public function createOrganization(array $data, User $user): Organization
    {
        try {
            DB::beginTransaction();

            $data['user_id'] = $user->id;
            $organization = Organization::create($data);

            DB::commit();

            Log::info('Organization created successfully', [
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'name' => $organization->name,
            ]);

            return $organization;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Organization creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to create organization. Please try again later.');
        }
    }
    
    /**
     * Update an existing organization
     *
     * @param Organization $organization Organization to update
     * @param array $data Update data
     * @return Organization
     */
    public function updateOrganization(Organization $organization, array $data): Organization
    {
        try {
            DB::beginTransaction();

            $organization->update($data);

            DB::commit();

            Log::info('Organization updated successfully', [
                'organization_id' => $organization->id,
                'user_id' => $organization->user_id,
            ]);

            return $organization->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Organization update failed', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to update organization. Please try again later.');
        }
    }
    
    /**
     * Delete an organization
     *
     * @param Organization $organization Organization to delete
     * @return bool
     */
    public function deleteOrganization(Organization $organization): bool
    {
        try {
            DB::beginTransaction();

            $organizationId = $organization->id;
            $userId = $organization->user_id;
            $deleted = $organization->delete();

            DB::commit();

            Log::info('Organization deleted successfully', [
                'organization_id' => $organizationId,
                'user_id' => $userId,
            ]);

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Organization deletion failed', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to delete organization. Please try again later.');
        }
    }
}
