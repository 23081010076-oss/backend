<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_subscriptions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Subscription::create([
            'user_id' => $user->id,
            'plan' => 'basic',
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        $response = $this->getJson('/api/subscriptions', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'plan',
                        'user_id',
                    ]
                ]
            ]);
    }

    public function test_user_can_create_subscription()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/subscriptions', [
            'plan' => 'premium',
            'status' => 'active',
            'start_date' => '2025-01-01',
            'end_date' => '2025-02-01',
            'package_type' => 'all_in_one',
            'duration' => 1,
            'duration_unit' => 'months',
            'price' => 100.00,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('subscriptions', ['plan' => 'premium']);
    }

    public function test_user_can_upgrade_subscription()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $sub = Subscription::create([
            'user_id' => $user->id,
            'plan' => 'basic',
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        $response = $this->postJson("/api/subscriptions/{$sub->id}/upgrade", [
            'plan' => 'premium',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $sub->id,
            'plan' => 'premium',
        ]);
    }
}
