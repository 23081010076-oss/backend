<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_achievements()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Achievement::create([
            'user_id' => $user->id,
            'title' => 'Best Coder',
            'organization' => 'Tech Corp',
            'year' => 2024,
        ]);

        $response = $this->getJson('/api/achievements', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'user_id',
                    ]
                ]
            ]);
    }

    public function test_user_can_create_achievement()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/achievements', [
            'title' => 'New Achievement',
            'organization' => 'Org',
            'year' => 2025,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('achievements', ['title' => 'New Achievement']);
    }

    public function test_user_can_update_achievement()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $achievement = Achievement::create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'organization' => 'Org',
            'year' => 2024,
        ]);

        $response = $this->putJson("/api/achievements/{$achievement->id}", [
            'title' => 'New Title',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('achievements', ['title' => 'New Title']);
    }

    public function test_user_can_delete_achievement()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $achievement = Achievement::create([
            'user_id' => $user->id,
            'title' => 'To Delete',
            'organization' => 'Org',
            'year' => 2024,
        ]);

        $response = $this->deleteJson("/api/achievements/{$achievement->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    }
}
