<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ScholarshipCacheTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test bahwa cache di-clear setelah create scholarship
     */
    public function test_cache_cleared_after_create_scholarship(): void
    {
        // Setup
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();

        // Clear cache dulu
        Cache::flush();

        // Step 1: Get scholarships pertama kali (akan di-cache)
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/scholarships')
            ->assertOk();

        $initialCount = Scholarship::count();

        // Step 2: Create scholarship baru
        $scholarshipData = [
            'name' => 'New Scholarship',
            'description' => 'Test scholarship',
            'organization_id' => $organization->id,
            'amount' => 5000000,
            'deadline' => now()->addMonths(2)->format('Y-m-d'),
            'status' => 'open',
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/scholarships', $scholarshipData)
            ->assertCreated();

        // Step 3: Get scholarships lagi (harus langsung muncul data baru)
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/scholarships')
            ->assertOk();

        $newCount = $response->json('meta.total');

        // Verify: Count harus bertambah 1
        $this->assertEquals($initialCount + 1, $newCount, 'Scholarship count should increase immediately after creation');

        // Verify: Data baru harus ada di response
        $scholarships = $response->json('data');
        $found = false;
        foreach ($scholarships as $scholarship) {
            if ($scholarship['name'] === 'New Scholarship') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'New scholarship should appear in the list immediately');
    }

    /**
     * Test bahwa cache di-clear setelah update scholarship
     */
    public function test_cache_cleared_after_update_scholarship(): void
    {
        // Setup
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        $scholarship = Scholarship::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Original Name',
        ]);

        // Clear cache
        Cache::flush();

        // Step 1: Get scholarships (cache it)
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/scholarships')
            ->assertOk();

        // Step 2: Update scholarship
        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/scholarships/{$scholarship->id}", [
                'name' => 'Updated Name',
                'description' => $scholarship->description,
                'organization_id' => $organization->id,
                'amount' => $scholarship->amount,
                'deadline' => $scholarship->deadline->format('Y-m-d'),
                'status' => $scholarship->status,
            ])
            ->assertOk();

        // Step 3: Get scholarships lagi (harus langsung muncul data update)
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/scholarships')
            ->assertOk();

        // Verify: Data update harus ada di response
        $scholarships = $response->json('data');
        $found = false;
        foreach ($scholarships as $item) {
            if ($item['id'] === $scholarship->id && $item['name'] === 'Updated Name') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Updated scholarship should appear in the list immediately');
    }

    /**
     * Test bahwa cache di-clear setelah delete scholarship
     */
    public function test_cache_cleared_after_delete_scholarship(): void
    {
        // Setup
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        $scholarship = Scholarship::factory()->create([
            'organization_id' => $organization->id,
        ]);

        // Clear cache
        Cache::flush();

        // Step 1: Get scholarships (cache it)
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/scholarships')
            ->assertOk();

        $initialCount = $response->json('meta.total');

        // Step 2: Delete scholarship
        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/scholarships/{$scholarship->id}")
            ->assertOk();

        // Step 3: Get scholarships lagi (harus langsung berkurang)
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/scholarships')
            ->assertOk();

        $newCount = $response->json('meta.total');

        // Verify: Count harus berkurang 1
        $this->assertEquals($initialCount - 1, $newCount, 'Scholarship count should decrease immediately after deletion');
    }
}
