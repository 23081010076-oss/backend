<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_organizations()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Organization::create([
            'user_id' => $user->id,
            'name' => 'Tech Corp',
            'type' => 'company',
        ]);

        $response = $this->getJson('/api/organizations', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'user_id',
                    ]
                ]
            ]);
    }

    public function test_user_can_create_organization()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/organizations', [
            'name' => 'New Org',
            'type' => 'non-profit',
            'description' => 'A non-profit org',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('organizations', ['name' => 'New Org']);
    }

    public function test_user_can_update_organization()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $org = Organization::create([
            'user_id' => $user->id,
            'name' => 'Old Org',
            'type' => 'company',
        ]);

        $response = $this->putJson("/api/organizations/{$org->id}", [
            'name' => 'Updated Org',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('organizations', ['name' => 'Updated Org']);
    }

    public function test_user_can_delete_organization()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $org = Organization::create([
            'user_id' => $user->id,
            'name' => 'To Delete',
            'type' => 'company',
        ]);

        $response = $this->deleteJson("/api/organizations/{$org->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('organizations', ['id' => $org->id]);
    }
}
